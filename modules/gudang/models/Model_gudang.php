<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Model_gudang extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'gudang';
	private $field_search 	= ['kode_gudang', 'nama_gudang', 'alamat', 'pic_name'];

	public function __construct()
	{
		$config = array(
			'primary_key' 	=> $this->primary_key,
		 	'table_name' 	=> $this->table_name,
		 	'field_search' 	=> $this->field_search,
		 );

		parent::__construct($config);
	}

	public function count_all($q = '', $field = '')
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

        $this->db->where($where);
		$query = $this->db->get($this->table_name);

		return $query->num_rows();
	}

	public function get($q = '', $field = '', $limit = 0, $offset = 0)
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

        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by($this->primary_key, "DESC");
		$query = $this->db->get($this->table_name);

		return $query->result();
	}

	public function generate_kode()
	{
		$last = $this->db->order_by('id', 'DESC')->limit(1)->get($this->table_name)->row();
		$num = $last ? ((int) substr($last->kode_gudang, -3)) + 1 : 1;
		return 'GDG-' . str_pad($num, 3, '0', STR_PAD_LEFT);
	}

	public function get_rak_by_gudang($gudang_id)
	{
		return $this->db->get_where('rak', ['gudang_id' => $gudang_id])->result();
	}

	public function get_stok_per_produk($gudang_id)
	{
		return $this->db->select('stok.*, produk.nama_produk, produk.kode_produk, satuan.singkatan, produk.stok_minimum')
			->join('produk', 'produk.id = stok.produk_id')
			->join('satuan', 'satuan.id = produk.satuan_id', 'LEFT')
			->where('stok.gudang_id', $gudang_id)
			->get('stok')->result();
	}

}

/* End of file Model_gudang.php */
/* Location: ./modules/gudang/models/Model_gudang.php */
