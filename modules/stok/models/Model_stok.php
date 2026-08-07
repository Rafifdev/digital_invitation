<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Model_stok extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'stok';
	private $field_search 	= ['produk.nama_produk', 'produk.kode_produk', 'gudang.nama_gudang'];

	public function __construct()
	{
		$config = array(
			'primary_key' 	=> $this->primary_key,
		 	'table_name' 	=> $this->table_name,
		 	'field_search' 	=> $this->field_search,
		 );

		parent::__construct($config);
	}

	public function count_all($q = '', $field = '', $gudang_id = null)
	{
		$this->join_available();
		$where = $this->build_where($q, $field);
        $this->db->where($where);
		if ($gudang_id) $this->db->where('stok.gudang_id', $gudang_id);
		return $this->db->get($this->table_name)->num_rows();
	}

	public function get($q = '', $field = '', $limit = 0, $offset = 0, $gudang_id = null)
	{
		$this->join_available();
		$where = $this->build_where($q, $field);
        $this->db->where($where);
		if ($gudang_id) $this->db->where('stok.gudang_id', $gudang_id);
        $this->db->limit($limit, $offset);
        $this->db->order_by('produk.nama_produk', 'ASC');
		return $this->db->get($this->table_name)->result();
	}

	public function join_available()
	{
		$this->db->select('stok.*, produk.nama_produk, produk.kode_produk, produk.stok_minimum, produk.harga_beli, produk.harga_jual, satuan.singkatan, gudang.nama_gudang, kategori_produk.nama_kategori, rak.kode_rak, rak.baris');
		$this->db->join('produk', 'produk.id = stok.produk_id');
		$this->db->join('satuan', 'satuan.id = produk.satuan_id', 'LEFT');
		$this->db->join('gudang', 'gudang.id = stok.gudang_id');
		$this->db->join('kategori_produk', 'kategori_produk.id = produk.kategori_id', 'LEFT');
		$this->db->join('rak', 'rak.id = IFNULL(stok.rak_id, produk.default_rak_id)', 'LEFT');
		return $this;
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

	public function get_stok_menipis($gudang_id = null)
	{
		$this->db->select('stok.*, produk.nama_produk, produk.kode_produk, produk.stok_minimum, satuan.singkatan, gudang.nama_gudang');
		$this->db->join('produk', 'produk.id = stok.produk_id');
		$this->db->join('satuan', 'satuan.id = produk.satuan_id', 'LEFT');
		$this->db->join('gudang', 'gudang.id = stok.gudang_id');
		$this->db->where('stok.qty <= produk.stok_minimum', null, false);
		if ($gudang_id) $this->db->where('stok.gudang_id', $gudang_id);
		return $this->db->get('stok')->result();
	}

	public function get_kartu_stok($produk_id, $gudang_id = null, $date_start = null, $date_end = null)
	{
		$this->db->select('stok_log.*, produk.nama_produk, produk.kode_produk, gudang.nama_gudang');
		$this->db->join('produk', 'produk.id = stok_log.produk_id');
		$this->db->join('gudang', 'gudang.id = stok_log.gudang_id');
		$this->db->where('stok_log.produk_id', $produk_id);
		if ($gudang_id) $this->db->where('stok_log.gudang_id', $gudang_id);
		if ($date_start) $this->db->where('DATE(stok_log.created_at) >=', $date_start);
		if ($date_end) $this->db->where('DATE(stok_log.created_at) <=', $date_end);
		$this->db->order_by('stok_log.created_at', 'ASC');
		return $this->db->get('stok_log')->result();
	}

	public function get_summary_dashboard($gudang_id = null)
	{
		// Total nilai stok, total produk, & total keseluruhan stok
		$q = $this->db->select('SUM(stok.qty * produk.harga_beli) AS total_nilai, COUNT(DISTINCT stok.produk_id) AS total_produk, SUM(stok.qty + stok.qty_rusak) AS total_keseluruhan_stok');
		$q->join('produk', 'produk.id = stok.produk_id');
		if ($gudang_id) $q->where('stok.gudang_id', $gudang_id);
		$summary = $q->get('stok')->row();

		// Produk stok menipis (0 < qty <= stok_minimum)
		$q_menipis = $this->db->where('stok.qty > 0', null, false)
			->where('stok.qty <= produk.stok_minimum', null, false)
			->join('produk', 'produk.id = stok.produk_id');
		if ($gudang_id) $q_menipis->where('stok.gudang_id', $gudang_id);
		$menipis_count = $q_menipis->count_all_results('stok');

		// Produk stok habis (qty == 0)
		$q_habis = $this->db->where('stok.qty', 0)
			->join('produk', 'produk.id = stok.produk_id');
		if ($gudang_id) $q_habis->where('stok.gudang_id', $gudang_id);
		$habis_count = $q_habis->count_all_results('stok');

		return (object) [
			'total_nilai'     => $summary ? (float) $summary->total_nilai : 0,
			'total_produk'    => $summary ? (int) $summary->total_produk : 0,
			'total_stok'      => $summary ? (float) $summary->total_keseluruhan_stok : 0,
			'stok_menipis'    => $menipis_count,
			'stok_habis'      => $habis_count,
		];
	}

}

/* End of file Model_stok.php */
/* Location: ./modules/stok/models/Model_stok.php */
