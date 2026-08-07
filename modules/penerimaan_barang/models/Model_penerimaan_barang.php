<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Model_penerimaan_barang extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'penerimaan_barang';
	private $field_search 	= ['penerimaan_barang.nomor_penerimaan', 'supplier.nama_supplier', 'penerimaan_barang.status'];

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
		$this->db->select('penerimaan_barang.*, supplier.nama_supplier, gudang.nama_gudang, purchase_order.nomor_po, purchase_order.mata_uang, purchase_order.kurs');
		$this->db->join('supplier', 'supplier.id = penerimaan_barang.supplier_id', 'LEFT');
		$this->db->join('gudang', 'gudang.id = penerimaan_barang.gudang_id', 'LEFT');
		$this->db->join('purchase_order', 'purchase_order.id = penerimaan_barang.po_id', 'LEFT');
		return $this;
	}

	public function count_all($q = '', $field = '')
	{
		$this->join_available();
		$where = $this->build_where($q, $field);
        $this->db->where($where);
		return $this->db->get($this->table_name)->num_rows();
	}

	public function get($q = '', $field = '', $limit = 0, $offset = 0)
	{
		$this->join_available();
		$where = $this->build_where($q, $field);
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('penerimaan_barang.'.$this->primary_key, 'DESC');
		return $this->db->get($this->table_name)->result();
	}

	private function build_where($q, $field)
	{
		$iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
		$field = $this->scurity($field);

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
		return $where;
	}

	public function generate_nomor()
	{
		$prefix = 'PNR-' . date('Ym') . '-';
		$last = $this->db->like('nomor_penerimaan', $prefix, 'after')
			->order_by('id', 'DESC')
			->limit(1)
			->get($this->table_name)->row();
		$num = $last ? ((int) substr($last->nomor_penerimaan, -4)) + 1 : 1;
		return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
	}

	public function get_items($penerimaan_id)
	{
		return $this->db->select('penerimaan_barang_item.*, produk.nama_produk, produk.kode_produk, rak.kode_rak, rak.baris')
			->join('produk', 'produk.id = penerimaan_barang_item.produk_id')
			->join('rak', 'rak.id = penerimaan_barang_item.rak_id', 'LEFT')
			->get_where('penerimaan_barang_item', ['penerimaan_id' => $penerimaan_id])->result();
	}

	/**
	 * Tambah stok dan buat utang usaha otomatis saat penerimaan dikonfirmasi
	 */
	public function konfirmasi($penerimaan_id, $user)
	{
		$penerimaan = $this->find($penerimaan_id);
		$items = $this->get_items($penerimaan_id);
		$total_nilai = 0;

		$po = $penerimaan->po_id ? $this->db->get_where('purchase_order', ['id' => $penerimaan->po_id])->row() : null;
		$mata_uang = $po ? ($po->mata_uang ?: 'IDR') : 'IDR';
		$kurs = ($po && (float)$po->kurs > 0) ? (float)$po->kurs : 1;

		foreach ($items as $item) {
			// Tambah stok barang baik
			$stok = $this->db->get_where('stok', ['produk_id' => $item->produk_id, 'gudang_id' => $penerimaan->gudang_id])->row();
			if ($stok) {
				$update_stok = [
					'qty' => $stok->qty + $item->qty_baik,
					'qty_rusak' => $stok->qty_rusak + $item->qty_rusak,
					'updated_at' => date('Y-m-d H:i:s'),
				];
				if (!empty($item->rak_id)) {
					$update_stok['rak_id'] = $item->rak_id;
				}
				$this->db->update('stok', $update_stok, ['id' => $stok->id]);
			} else {
				$this->db->insert('stok', [
					'produk_id' => $item->produk_id,
					'gudang_id' => $penerimaan->gudang_id,
					'rak_id'    => $item->rak_id ?: null,
					'qty' => $item->qty_baik,
					'qty_rusak' => $item->qty_rusak,
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}

			// Log stok masuk (harga disesuaikan ke IDR untuk akuntansi stok)
			$harga_log_idr = ($mata_uang !== 'IDR') ? ($item->harga_satuan * $kurs) : $item->harga_satuan;
			$this->db->insert('stok_log', [
				'produk_id' => $item->produk_id,
				'gudang_id' => $penerimaan->gudang_id,
				'batch_no' => $item->batch_no,
				'expired_date' => $item->expired_date,
				'jenis' => 'masuk',
				'qty' => $item->qty_baik,
				'harga_satuan' => $harga_log_idr,
				'referensi_type' => 'penerimaan_barang',
				'referensi_id' => $penerimaan_id,
				'keterangan' => 'Penerimaan Barang ' . $penerimaan->nomor_penerimaan,
				'created_by' => $user,
				'created_at' => date('Y-m-d H:i:s'),
			]);

			$total_nilai += $item->qty_baik * $item->harga_satuan;

			// Update qty_terima di PO item
			if ($item->po_item_id) {
				$po_item = $this->db->get_where('purchase_order_item', ['id' => $item->po_item_id])->row();
				if ($po_item) {
					$this->db->update('purchase_order_item', [
						'qty_terima' => $po_item->qty_terima + $item->qty_terima,
					], ['id' => $item->po_item_id]);
				}
			}
		}

		// Buat utang usaha otomatis (dikonversi ke IDR jika transaksi menggunakan valas)
		if ($total_nilai > 0) {
			$total_utang_idr = ($mata_uang !== 'IDR') ? ($total_nilai * $kurs) : $total_nilai;
			$prefix = 'UTG-' . date('Ym') . '-';
			$last = $this->db->like('nomor_utang', $prefix, 'after')->order_by('id', 'DESC')->limit(1)->get('utang_usaha')->row();
			$num = $last ? ((int) substr($last->nomor_utang, -4)) + 1 : 1;

			$catatan_utang = $mata_uang !== 'IDR'
				? 'Penerimaan GRN #' . $penerimaan->nomor_penerimaan . ' (' . $mata_uang . ' ' . number_format($total_nilai, 2, '.', ',') . ' @ Kurs Rp ' . number_format($kurs, 0, ',', '.') . ')'
				: 'Penerimaan GRN #' . $penerimaan->nomor_penerimaan;

			$this->db->insert('utang_usaha', [
				'nomor_utang' => $prefix . str_pad($num, 4, '0', STR_PAD_LEFT),
				'supplier_id' => $penerimaan->supplier_id,
				'penerimaan_id' => $penerimaan_id,
				'po_id'         => $penerimaan->po_id ?: null,
				'total' => $total_utang_idr,
				'terbayar' => 0,
				'sisa' => $total_utang_idr,
				'subtotal' => $total_utang_idr,
				'status' => 'belum_bayar',
				'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+30 days')), // default 30 hari
				'catatan' => $catatan_utang,
				'created_at' => date('Y-m-d H:i:s'),
			]);
		}

		// Update status PO jika ada
		if ($penerimaan->po_id) {
			$po_items = $this->db->get_where('purchase_order_item', ['po_id' => $penerimaan->po_id])->result();
			$semua_terima = true;
			foreach ($po_items as $poi) {
				if ($poi->qty_terima < $poi->qty_pesan) {
					$semua_terima = false;
					break;
				}
			}
			$po_status = $semua_terima ? 'received' : 'partial';
			$this->db->update('purchase_order', ['status' => $po_status], ['id' => $penerimaan->po_id]);
		}

		// Update status & total penerimaan
		$this->db->update($this->table_name, ['status' => 'confirmed', 'total' => $total_nilai, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $penerimaan_id]);

		return true;
	}

}

/* End of file Model_penerimaan_barang.php */
/* Location: ./modules/penerimaan_barang/models/Model_penerimaan_barang.php */
