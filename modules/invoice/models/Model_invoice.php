<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Model_invoice extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'invoice';
	private $field_search 	= ['invoice.nomor_invoice', 'pelanggan.nama_pelanggan', 'invoice.status', 'invoice.status_kirim'];

	public function __construct()
	{
		$config = array(
			'primary_key' 	=> $this->primary_key,
		 	'table_name' 	=> $this->table_name,
		 	'field_search' 	=> $this->field_search,
		 );

		parent::__construct($config);
	}

	public function join_available()
	{
		$this->db->select('invoice.*, pelanggan.nama_pelanggan, pelanggan.kode_pelanggan, pelanggan.alamat as alamat_pelanggan, pelanggan.telepon as telp_pelanggan, gudang.nama_gudang, sales_order.nomor_so');
		$this->db->join('pelanggan', 'pelanggan.id = invoice.pelanggan_id', 'LEFT');
		$this->db->join('gudang', 'gudang.id = invoice.gudang_id', 'LEFT');
		$this->db->join('sales_order', 'sales_order.id = invoice.so_id', 'LEFT');
		return $this;
	}

	public function count_all($q = '', $field = '')
	{
		$this->join_available();
		$where = $this->build_where($q, $field);
		if (!empty($where)) {
			$this->db->where($where);
		}
		return $this->db->get($this->table_name)->num_rows();
	}

	public function get($q = '', $field = '', $limit = 0, $offset = 0)
	{
		$this->join_available();
		$where = $this->build_where($q, $field);
		if (!empty($where)) {
			$this->db->where($where);
		}
		$this->db->limit($limit, $offset);
		$this->db->order_by('invoice.'.$this->primary_key, 'DESC');
		return $this->db->get($this->table_name)->result();
	}

	private function build_where($q = '', $field = '')
	{
		$iterasi = 1;
		$num = count($this->field_search);
		$where = NULL;
		$q = $this->scurity($q);
		$field = $this->scurity($field);

		if (!empty($q)) {
			if (empty($field)) {
				foreach ($this->field_search as $f) {
					if ($iterasi == 1) {
						$where .= "(" . $f . " LIKE '%" . $q . "%' ";
					} else if ($iterasi == $num) {
						$where .= "OR " . $f . " LIKE '%" . $q . "%') ";
					} else {
						$where .= "OR " . $f . " LIKE '%" . $q . "%' ";
					}
					$iterasi++;
				}
			} else {
				$where .= "(" . $field . " LIKE '%" . $q . "%' )";
			}
		}

		$pelanggan_id = $this->input->get('pelanggan_id');
		if (!empty($pelanggan_id)) {
			$where .= ($where ? ' AND ' : '') . "invoice.pelanggan_id = " . intval($pelanggan_id);
		}

		$gudang_id = $this->input->get('gudang_id');
		if (!empty($gudang_id)) {
			$where .= ($where ? ' AND ' : '') . "invoice.gudang_id = " . intval($gudang_id);
		}

		$status = $this->input->get('status');
		if (!empty($status)) {
			$where .= ($where ? ' AND ' : '') . "invoice.status = " . $this->db->escape($status);
		}

		return $where;
	}

	public function generate_nomor()
	{
		$prefix = 'INV-' . date('Ym') . '-';
		$last = $this->db->like('nomor_invoice', $prefix, 'after')
			->order_by('id', 'DESC')
			->limit(1)
			->get($this->table_name)->row();
		$num = $last ? ((int) substr($last->nomor_invoice, -4)) + 1 : 1;
		return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
	}

	public function get_items($invoice_id)
	{
		return $this->db->select('invoice_item.*, produk.nama_produk, produk.kode_produk, satuan.singkatan')
			->join('produk', 'produk.id = invoice_item.produk_id')
			->join('satuan', 'satuan.id = produk.satuan_id', 'LEFT')
			->get_where('invoice_item', ['invoice_id' => $invoice_id])->result();
	}

	/**
	 * Buat piutang usaha otomatis saat invoice dibuat
	 */
	public function buat_piutang($invoice_id)
	{
		$invoice = $this->find($invoice_id);
		if (!$invoice) return false;

		// Cek apakah piutang sudah ada
		$existing = $this->db->get_where('piutang_usaha', ['invoice_id' => $invoice_id])->row();
		if ($existing) {
			if ($existing->total != $invoice->total) {
				$new_sisa = max(0, $invoice->total - $existing->terbayar);
				$this->db->update('piutang_usaha', [
					'total'      => $invoice->total,
					'sisa'       => $new_sisa,
					'updated_at' => date('Y-m-d H:i:s'),
				], ['id' => $existing->id]);
			}
			return $existing->id;
		}

		$prefix = 'PIU-' . date('Ym') . '-';
		$last = $this->db->like('nomor_piutang', $prefix, 'after')->order_by('id', 'DESC')->limit(1)->get('piutang_usaha')->row();
		$num = $last ? ((int) substr($last->nomor_piutang, -4)) + 1 : 1;
		$nomor = $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);

		$this->db->insert('piutang_usaha', [
			'nomor_piutang' => $nomor,
			'pelanggan_id' => $invoice->pelanggan_id,
			'invoice_id' => $invoice_id,
			'total' => $invoice->total,
			'terbayar' => 0,
			'sisa' => $invoice->total,
			'tanggal_jatuh_tempo' => (!empty($invoice->tanggal_jatuh_tempo) && $invoice->tanggal_jatuh_tempo != '0000-00-00') ? $invoice->tanggal_jatuh_tempo : date('Y-m-d', strtotime('+30 days')),
			'status' => 'belum_bayar',
			'created_at' => date('Y-m-d H:i:s'),
		]);

		return $this->db->insert_id();
	}

	/**
	 * Kurangi stok saat invoice dikonfirmasi dengan logika FIFO / FEFO
	 */
	public function kurangi_stok($invoice_id, $user)
	{
		$invoice = $this->find($invoice_id);
		$items   = $this->get_items($invoice_id);

		$gudang_id = !empty($invoice->gudang_id) ? $invoice->gudang_id : null;
		if (empty($gudang_id) && !empty($invoice->so_id)) {
			$so_rec = $this->db->get_where('sales_order', ['id' => $invoice->so_id])->row();
			if ($so_rec && !empty($so_rec->gudang_id)) {
				$gudang_id = $so_rec->gudang_id;
				$this->db->update('invoice', ['gudang_id' => $gudang_id], ['id' => $invoice_id]);
			}
		}
		if (empty($gudang_id)) {
			$first_g = $this->db->get('gudang')->row();
			$gudang_id = $first_g ? $first_g->id : 1;
			$this->db->update('invoice', ['gudang_id' => $gudang_id], ['id' => $invoice_id]);
		}

		foreach ($items as $item) {
			$produk = $this->db->get_where('produk', ['id' => $item->produk_id])->row();
			$metode = ($produk && !empty($produk->metode_stok)) ? strtoupper($produk->metode_stok) : 'FIFO';

			// Tentukan urutan pengambilan berdasarkan FIFO atau FEFO
			$order_by = ($metode == 'FEFO') ? 'expired_date ASC, created_at ASC' : 'created_at ASC';

			// Update stok total di gudang
			$stok = $this->db->get_where('stok', ['produk_id' => $item->produk_id, 'gudang_id' => $gudang_id])->row();
			if ($stok) {
				$new_qty = max(0, $stok->qty - $item->qty);
				$this->db->update('stok', ['qty' => $new_qty, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $stok->id]);
			}

			// Ambil batch teratas sesuai alur FIFO / FEFO dari log penerimaan
			$batch = $this->db->where('produk_id', $item->produk_id)
				->where('gudang_id', $gudang_id)
				->where('jenis', 'masuk')
				->order_by($order_by)
				->limit(1)
				->get('stok_log')->row();

			$batch_no     = $batch ? $batch->batch_no : null;
			$expired_date = $batch ? $batch->expired_date : null;

			// Catat log pengeluaran stok dengan informasi metode & batch
			$this->db->insert('stok_log', [
				'produk_id'      => $item->produk_id,
				'gudang_id'      => $gudang_id,
				'batch_no'       => $batch_no,
				'expired_date'   => $expired_date,
				'jenis'          => 'keluar',
				'qty'            => $item->qty,
				'harga_satuan'   => $item->harga_satuan,
				'referensi_type' => 'invoice',
				'referensi_id'   => $invoice_id,
				'keterangan'     => "Penjualan Invoice {$invoice->nomor_invoice} [Metode {$metode}]",
				'created_by'     => $user,
				'created_at'     => date('Y-m-d H:i:s'),
			]);
		}

		return true;
	}

}

/* End of file Model_invoice.php */
/* Location: ./modules/invoice/models/Model_invoice.php */
