<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Model_pelanggan extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'pelanggan';
	private $field_search 	= ['kode_pelanggan', 'nama_pelanggan', 'kontak_person', 'telepon', 'email', 'kota', 'tipe_pelanggan'];

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
		$num = $last ? ((int) substr($last->kode_pelanggan, -4)) + 1 : 1;
		return 'CUS-' . str_pad($num, 4, '0', STR_PAD_LEFT);
	}

	public function get_harga($produk_id, $tipe, $qty = 1)
	{
		// Cek apakah ada override harga volume/tier khusus di tabel harga_pelanggan
		$harga_override = $this->db->where('produk_id', $produk_id)
			->where('tipe_pelanggan', $tipe)
			->where('min_qty <=', $qty)
			->order_by('min_qty', 'DESC')
			->limit(1)
			->get('harga_pelanggan')->row();

		if ($harga_override) {
			return $harga_override->harga;
		}

		// Jika tidak ada override, gunakan harga default produk sesuai tipe pelanggan
		$produk = $this->db->get_where('produk', ['id' => $produk_id])->row();
		if (!$produk) return 0;
		
		$tipe = strtolower(trim($tipe));
		if ($tipe == 'grosir' && $produk->harga_jual_grosir > 0) return $produk->harga_jual_grosir;
		if ($tipe == 'distributor' && $produk->harga_jual_distributor > 0) return $produk->harga_jual_distributor;
		if ($tipe == 'vip' && $produk->harga_jual_vip > 0) return $produk->harga_jual_vip;
		
		return $produk->harga_jual;
	}

	/**
	 * Get laporan ringkasan transaksi per pelanggan
	 */
	public function get_laporan_transaksi()
	{
		return $this->db->query("
			SELECT p.id, p.kode_pelanggan, p.nama_pelanggan, p.tipe_pelanggan,
			       COUNT(DISTINCT so.id) AS total_so,
			       COUNT(DISTINCT inv.id) AS total_inv,
			       COALESCE(SUM(inv.total), 0) AS total_belanja,
			       COALESCE(SUM(piu.sisa), 0) AS sisa_piutang
			FROM pelanggan p
			LEFT JOIN sales_order so ON so.pelanggan_id = p.id
			LEFT JOIN invoice inv ON inv.pelanggan_id = p.id
			LEFT JOIN piutang_usaha piu ON piu.pelanggan_id = p.id AND piu.status != 'lunas'
			WHERE p.status = 'aktif'
			GROUP BY p.id, p.kode_pelanggan, p.nama_pelanggan, p.tipe_pelanggan
			ORDER BY total_belanja DESC
		")->result();
	}

}

/* End of file Model_pelanggan.php */
/* Location: ./modules/pelanggan/models/Model_pelanggan.php */
