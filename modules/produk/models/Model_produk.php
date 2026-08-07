<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Model_produk extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'produk';
	private $field_search 	= ['kode_produk', 'barcode', 'nama_produk', 'deskripsi'];

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
	                $where .= "(produk." . $f . " LIKE '%" . $q . "%' ";
	            } else if ($iterasi == $num) {
	                $where .= "OR produk." . $f . " LIKE '%" . $q . "%') ";
	            } else {
	                $where .= "OR produk." . $f . " LIKE '%" . $q . "%' ";
	            }
	            $iterasi++;
	        }
        } else {
        	$where .= "(produk." . $field . " LIKE '%" . $q . "%' )";
        }

        $this->join_available();
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
	                $where .= "(produk." . $f . " LIKE '%" . $q . "%' ";
	            } else if ($iterasi == $num) {
	                $where .= "OR produk." . $f . " LIKE '%" . $q . "%') ";
	            } else {
	                $where .= "OR produk." . $f . " LIKE '%" . $q . "%' ";
	            }
	            $iterasi++;
	        }
        } else {
        	$where .= "(produk." . $field . " LIKE '%" . $q . "%' )";
        }

        $this->join_available();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('produk.id', "ASC");
		$query = $this->db->get($this->table_name);

		return $query->result();
	}

	public function join_available()
	{
		$this->db->select('produk.*, kategori_produk.nama_kategori, satuan.nama_satuan, satuan.singkatan');
		$this->db->join('kategori_produk', 'kategori_produk.id = produk.kategori_id', 'LEFT');
		$this->db->join('satuan', 'satuan.id = produk.satuan_id', 'LEFT');
		return $this;
	}

	public function get_stok($produk_id, $gudang_id = null)
	{
		$this->db->where('produk_id', $produk_id);
		if ($gudang_id) {
			$this->db->where('gudang_id', $gudang_id);
		}
		$result = $this->db->get('stok')->result();
		$total = 0;
		foreach ($result as $r) {
			$total += $r->qty;
		}
		return $total;
	}

	public function generate_kode()
	{
		$last = $this->db->order_by('id', 'DESC')->limit(1)->get($this->table_name)->row();
		$num = $last ? ((int) substr($last->kode_produk, -4)) + 1 : 1;
		return 'PRD-' . str_pad($num, 4, '0', STR_PAD_LEFT);
	}

	public function generate_barcode()
	{
		$last = $this->db->order_by('id', 'DESC')->limit(1)->get($this->table_name)->row();
		$num = $last ? $last->id + 1 : 1;
		return '899300' . str_pad($num, 7, '0', STR_PAD_LEFT);
	}

	public function get_for_select()
	{
		return $this->db->where('status', 'aktif')->get($this->table_name)->result();
	}

	public function get_riwayat_harga($produk_id)
	{
		$this->db->select('riwayat_harga_beli.*, supplier.nama_supplier, purchase_order.mata_uang, purchase_order.kurs');
		$this->db->join('supplier', 'supplier.id = riwayat_harga_beli.supplier_id', 'LEFT');
		$this->db->join('purchase_order', 'purchase_order.nomor_po = riwayat_harga_beli.nomor_po', 'LEFT');
		$this->db->where('riwayat_harga_beli.produk_id', $produk_id);
		$this->db->order_by('riwayat_harga_beli.tanggal', 'DESC');
		$this->db->order_by('riwayat_harga_beli.id', 'DESC');
		return $this->db->get('riwayat_harga_beli')->result();
	}

	public function remove($id = NULL)
	{
		if ($id) {
			// Cascade delete: Hapus data stok dan log stok yang berkaitan dengan produk ini
			$this->db->delete('stok', ['produk_id' => $id]);
			$this->db->delete('stok_log', ['produk_id' => $id]);
		}
		
		return parent::remove($id);
	}

}

/* End of file Model_produk.php */
/* Location: ./modules/produk/models/Model_produk.php */
