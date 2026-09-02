<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Model_supplier extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'supplier';
	private $field_search 	= ['kode_supplier', 'nama_supplier', 'kontak_person', 'telepon', 'email', 'kota'];

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
		$num = $last ? ((int) substr($last->kode_supplier, -4)) + 1 : 1;
		return 'SUP-' . str_pad($num, 4, '0', STR_PAD_LEFT);
	}

	/**
	 * Get riwayat harga beli per supplier/produk
	 */
	public function get_riwayat_harga($supplier_id = null, $produk_id = null)
	{
		$this->db->select('purchase_order_item.*, purchase_order.nomor_po, purchase_order.tanggal_po, supplier.nama_supplier, produk.nama_produk, produk.kode_produk')
			->from('purchase_order_item')
			->join('purchase_order', 'purchase_order.id = purchase_order_item.po_id')
			->join('supplier', 'supplier.id = purchase_order.supplier_id')
			->join('produk', 'produk.id = purchase_order_item.produk_id')
			->order_by('purchase_order.tanggal_po', 'DESC');

		if ($supplier_id) { $this->db->where('purchase_order.supplier_id', $supplier_id); }
		if ($produk_id) { $this->db->where('purchase_order_item.produk_id', $produk_id); }

		return $this->db->get()->result();
	}

}

/* End of file Model_supplier.php */
/* Location: ./modules/supplier/models/Model_supplier.php */
