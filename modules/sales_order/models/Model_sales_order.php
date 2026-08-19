<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Model_sales_order extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'sales_order';
	private $field_search 	= [
		'sales_order.nomor_so', 
		'sales_order.no_pesanan', 
		'sales_order.nomor_resi', 
		'sales_order.username_pembeli', 
		'sales_order.nama_penerima', 
		'pelanggan.nama_pelanggan', 
		'sales_order.status'
	];

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
		$this->db->select('sales_order.*, pelanggan.nama_pelanggan, pelanggan.kode_pelanggan, pelanggan.tipe_pelanggan, pelanggan.alamat as alamat_pelanggan, pelanggan.no_telp as telp_pelanggan, gudang.nama_gudang, (SELECT COALESCE(SUM(qty), 1) FROM sales_order_item WHERE sales_order_item.so_id = sales_order.id) as total_qty');
		$this->db->join('pelanggan', 'pelanggan.id = sales_order.pelanggan_id', 'LEFT');
		$this->db->join('gudang', 'gudang.id = sales_order.gudang_id', 'LEFT');
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
		$this->db->order_by('sales_order.'.$this->primary_key, 'DESC');
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
			$where .= ($where ? ' AND ' : '') . "sales_order.pelanggan_id = " . intval($pelanggan_id);
		}

		$gudang_id = $this->input->get('gudang_id');
		if (!empty($gudang_id)) {
			$where .= ($where ? ' AND ' : '') . "sales_order.gudang_id = " . intval($gudang_id);
		}

		$status = $this->input->get('status');
		if (!empty($status)) {
			$where .= ($where ? ' AND ' : '') . "sales_order.status = " . $this->db->escape($status);
		}

		// Strictly filter for Offline Sales Orders
		$where .= ($where ? ' AND ' : '') . "(sales_order.source = 'manual' OR sales_order.source IS NULL OR sales_order.source = '')";

		return $where;
	}

	public function find_with_detail($id)
	{
		$this->join_available();
		return $this->db->get_where($this->table_name, ['sales_order.id' => $id])->row();
	}

	public function get_items($so_id)
	{
		$so = $this->db->get_where('sales_order', ['id' => $so_id])->row();
		$gudang_id = $so ? $so->gudang_id : 0;
		return $this->db->select('sales_order_item.*, produk.nama_produk, produk.kode_produk, satuan.singkatan, MIN(rak.kode_rak) as kode_rak, MIN(rak.baris) as baris')
			->join('produk', 'produk.id = sales_order_item.produk_id', 'LEFT')
			->join('satuan', 'satuan.id = produk.satuan_id', 'LEFT')
			->join('rak', "rak.id = produk.default_rak_id OR rak.gudang_id = '{$gudang_id}'", 'LEFT')
			->group_by('sales_order_item.id')
			->get_where('sales_order_item', ['so_id' => $so_id])->result();
	}

	public function generate_nomor()
	{
		$prefix = 'SO-' . date('Ym') . '-';
		$last = $this->db->like('nomor_so', $prefix, 'after')
			->order_by('id', 'DESC')
			->limit(1)
			->get($this->table_name)->row();
		$num = $last ? ((int) substr($last->nomor_so, -4)) + 1 : 1;
		return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
	}

	public function cek_stok($so_id)
	{
		$items = $this->get_items($so_id);
		$so = $this->find($so_id);
		$errors = [];
		foreach ($items as $item) {
			$stok = $this->db->where('produk_id', $item->produk_id)
				->where('gudang_id', $so->gudang_id)
				->get('stok')->row();
			$stok_tersedia = $stok ? $stok->qty : 0;
			if ($stok_tersedia < $item->qty) {
				$errors[] = "Stok {$item->nama_produk} tidak cukup. Tersedia: {$stok_tersedia}, Dibutuhkan: {$item->qty}";
			}
		}
		return $errors;
	}

	public function update_total($so_id)
	{
		$items = $this->db->get_where('sales_order_item', ['so_id' => $so_id])->result();
		$subtotal = 0;
		foreach ($items as $item) {
			$subtotal += $item->subtotal;
		}
		$so = $this->find($so_id);
		$pajak_nominal = $subtotal * ($so->pajak_persen / 100);
		$total = $subtotal - $so->diskon_total + $pajak_nominal;
		$this->db->update($this->table_name, [
			'subtotal' => $subtotal,
			'pajak_nominal' => $pajak_nominal,
			'total' => $total,
		], ['id' => $so_id]);
	}

	public function get_stats()
	{
		$start_month = date('Y-m-01');
		$end_month   = date('Y-m-t');

		$penjualan_bulan_ini = $this->db->select_sum('total')
			->where('DATE(tanggal_so) >=', $start_month)
			->where('DATE(tanggal_so) <=', $end_month)
			->get('sales_order')->row()->total ?: 0;

		$terbayar_lunas = $this->db->select_sum('sales_order.total')
			->join('invoice', 'invoice.so_id = sales_order.id')
			->where('invoice.status', 'paid')
			->where('DATE(sales_order.tanggal_so) >=', $start_month)
			->where('DATE(sales_order.tanggal_so) <=', $end_month)
			->get('sales_order')->row()->total ?: 0;

		$piutang_unpaid = max(0, $penjualan_bulan_ini - $terbayar_lunas);

		$pesanan_aktif = $this->db->where_in('status', ['draft', 'submitted'])
			->count_all_results('sales_order');

		$proses_gudang = $this->db->where_in('status', ['confirmed', 'packing'])
			->count_all_results('sales_order');

		$siap_kirim = $this->db->where_in('status', ['ready_to_ship', 'shipped'])
			->count_all_results('sales_order');

		$pesanan_terkirim = $this->db->where_in('status', ['delivered', 'completed', 'paid'])
			->count_all_results('sales_order');

		return [
			'penjualan_bulan_ini' => $penjualan_bulan_ini,
			'terbayar_lunas'      => $terbayar_lunas,
			'piutang_unpaid'      => $piutang_unpaid,
			'pesanan_aktif'       => $pesanan_aktif,
			'proses_gudang'       => $proses_gudang,
			'siap_kirim'          => $siap_kirim,
			'pesanan_terkirim'    => $pesanan_terkirim,
		];
	}

}

/* End of file Model_sales_order.php */
/* Location: ./modules/sales_order/models/Model_sales_order.php */
