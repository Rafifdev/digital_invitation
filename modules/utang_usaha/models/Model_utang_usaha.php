<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_utang_usaha extends MY_Model {

	public $primary_key 	= 'id';
	public $table_name 	= 'utang_usaha';
	public $field_search 	= ['utang_usaha.nomor_utang', 'supplier.nama_supplier', 'utang_usaha.status'];

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
		$this->db->select('utang_usaha.*, supplier.nama_supplier, supplier.kode_supplier, penerimaan_barang.nomor_penerimaan, penerimaan_barang.nomor_surat_jalan, purchase_order.nomor_po');
		$this->db->join('supplier', 'supplier.id = utang_usaha.supplier_id', 'LEFT');
		$this->db->join('penerimaan_barang', 'penerimaan_barang.id = utang_usaha.penerimaan_id', 'LEFT');
		$this->db->join('purchase_order', 'purchase_order.id = penerimaan_barang.po_id', 'LEFT');
		return $this;
	}

	public function count_all($q = '', $field = '', $supplier_id = null, $status = null)
	{
		$this->join_available();
		$this->apply_filters($q, $field, $supplier_id, $status);
		return $this->db->get($this->table_name)->num_rows();
	}

	public function get($q = '', $field = '', $limit = 0, $offset = 0, $supplier_id = null, $status = null)
	{
		$this->join_available();
		$this->apply_filters($q, $field, $supplier_id, $status);
        $this->db->limit($limit, $offset);
        $this->db->order_by('utang_usaha.'.$this->primary_key, 'DESC');
		return $this->db->get($this->table_name)->result();
	}

	private function apply_filters($q, $field, $supplier_id, $status)
	{
		if (!empty($supplier_id)) {
			$this->db->where('utang_usaha.supplier_id', $supplier_id);
		}
		if (!empty($status)) {
			$this->db->where('utang_usaha.status', $status);
		}

		if (!empty($q)) {
			$this->db->group_start();
			$this->db->like('utang_usaha.nomor_utang', $q);
			$this->db->or_like('supplier.nama_supplier', $q);
			$this->db->or_like('penerimaan_barang.nomor_penerimaan', $q);
			$this->db->group_end();
		}
	}

	public function get_stats()
	{
		$total_kewajiban = $this->db->select_sum('total')->get('utang_usaha')->row()->total ?: 0;
		$total_dibayar   = $this->db->select_sum('terbayar')->get('utang_usaha')->row()->terbayar ?: 0;
		$sisa_tagihan    = $this->db->select_sum('sisa')->get('utang_usaha')->row()->sisa ?: 0;
		
		$today = date('Y-m-d');
		$overdue_res     = $this->db->select_sum('sisa')->select('COUNT(*) as cnt')->where('tanggal_jatuh_tempo <', $today)->where('status !=', 'lunas')->get('utang_usaha')->row();
		$overdue_nominal = $overdue_res->sisa ?: 0;
		$overdue_count   = $overdue_res->cnt ?: 0;

		return [
			'total_kewajiban' => $total_kewajiban,
			'total_dibayar'   => $total_dibayar,
			'sisa_tagihan'    => $sisa_tagihan,
			'overdue_nominal' => $overdue_nominal,
			'overdue_count'   => $overdue_count,
			'total_faktur'    => $this->db->count_all('utang_usaha'),
		];
	}

	public function get_kas_keluar($supplier_id = null, $metode = null, $q = '', $limit = 10, $offset = 0)
	{
		$this->db->select('pembayaran_utang.*, supplier.nama_supplier, supplier.kode_supplier, utang_usaha.nomor_utang')
			->join('supplier', 'supplier.id = pembayaran_utang.supplier_id', 'LEFT')
			->join('utang_usaha', 'utang_usaha.id = pembayaran_utang.utang_id', 'LEFT');

		if ($supplier_id) {
			$this->db->where('pembayaran_utang.supplier_id', $supplier_id);
		}
		if ($metode) {
			$this->db->where('pembayaran_utang.metode_pembayaran', $metode);
		}
		if ($q) {
			$this->db->group_start();
			$this->db->like('pembayaran_utang.nomor_bkk', $q);
			$this->db->or_like('supplier.nama_supplier', $q);
			$this->db->or_like('utang_usaha.nomor_utang', $q);
			$this->db->group_end();
		}

		$this->db->limit($limit, $offset);
		$this->db->order_by('pembayaran_utang.id', 'DESC');
		return $this->db->get('pembayaran_utang')->result();
	}

	public function count_kas_keluar($supplier_id = null, $metode = null, $q = '')
	{
		$this->db->join('supplier', 'supplier.id = pembayaran_utang.supplier_id', 'LEFT')
			->join('utang_usaha', 'utang_usaha.id = pembayaran_utang.utang_id', 'LEFT');

		if ($supplier_id) { $this->db->where('pembayaran_utang.supplier_id', $supplier_id); }
		if ($metode) { $this->db->where('pembayaran_utang.metode_pembayaran', $metode); }
		if ($q) {
			$this->db->group_start();
			$this->db->like('pembayaran_utang.nomor_bkk', $q);
			$this->db->or_like('supplier.nama_supplier', $q);
			$this->db->or_like('utang_usaha.nomor_utang', $q);
			$this->db->group_end();
		}

		return $this->db->get('pembayaran_utang')->num_rows();
	}

	public function generate_bkk_nomor()
	{
		$prefix = 'BKK-' . date('Ym') . '-';
		$last = $this->db->like('nomor_bkk', $prefix, 'after')
			->order_by('id', 'DESC')
			->limit(1)
			->get('pembayaran_utang')->row();
		$num = $last ? ((int) substr($last->nomor_bkk, -4)) + 1 : 1;
		return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
	}
}
