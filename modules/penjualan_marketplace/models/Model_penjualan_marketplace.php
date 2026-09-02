<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_penjualan_marketplace extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'penjualan_marketplace';
	private $field_search 	= [
		'penjualan_marketplace.nomor_so', 
		'penjualan_marketplace.no_pesanan', 
		'penjualan_marketplace.nomor_resi', 
		'penjualan_marketplace.username_pembeli', 
		'penjualan_marketplace.nama_penerima', 
		'pelanggan.nama_pelanggan', 
		'penjualan_marketplace.status'
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
		$this->db->select('penjualan_marketplace.*, pelanggan.nama_pelanggan, pelanggan.kode_pelanggan, pelanggan.tipe_pelanggan, pelanggan.alamat as alamat_pelanggan, pelanggan.no_telp as telp_pelanggan, gudang.nama_gudang, (SELECT COALESCE(SUM(qty), 1) FROM penjualan_marketplace_item WHERE penjualan_marketplace_item.penjualan_marketplace_id = penjualan_marketplace.id) as total_qty');
		$this->db->join('pelanggan', 'pelanggan.id = penjualan_marketplace.pelanggan_id', 'LEFT');
		$this->db->join('gudang', 'gudang.id = penjualan_marketplace.gudang_id', 'LEFT');
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
		$this->db->order_by('penjualan_marketplace.'.$this->primary_key, 'DESC');
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
			$where .= ($where ? ' AND ' : '') . "penjualan_marketplace.pelanggan_id = " . intval($pelanggan_id);
		}

		$gudang_id = $this->input->get('gudang_id');
		if (!empty($gudang_id)) {
			$where .= ($where ? ' AND ' : '') . "penjualan_marketplace.gudang_id = " . intval($gudang_id);
		}

		$status = $this->input->get('status');
		if (!empty($status)) {
			$where .= ($where ? ' AND ' : '') . "penjualan_marketplace.status = " . $this->db->escape($status);
		}

		$source = $this->input->get('source');
		if (!empty($source)) {
			$where .= ($where ? ' AND ' : '') . "penjualan_marketplace.source = " . $this->db->escape($source);
		}

		return $where;
	}

	public function find_with_detail($id)
	{
		$this->join_available();
		return $this->db->get_where($this->table_name, ['penjualan_marketplace.id' => $id])->row();
	}

	public function get_items($pm_id)
	{
		$pm = $this->db->get_where('penjualan_marketplace', ['id' => $pm_id])->row();
		$gudang_id = $pm ? $pm->gudang_id : 0;
		return $this->db->select('penjualan_marketplace_item.*, produk.nama_produk, produk.kode_produk, satuan.singkatan, MIN(rak.kode_rak) as kode_rak, MIN(rak.baris) as baris')
			->join('produk', 'produk.id = penjualan_marketplace_item.produk_id', 'LEFT')
			->join('satuan', 'satuan.id = produk.satuan_id', 'LEFT')
			->join('rak', "rak.id = produk.default_rak_id OR rak.gudang_id = '{$gudang_id}'", 'LEFT')
			->group_by('penjualan_marketplace_item.id')
			->get_where('penjualan_marketplace_item', ['penjualan_marketplace_id' => $pm_id])->result();
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

	public function get_stats()
	{
		$start_month = date('Y-m-01');
		$end_month   = date('Y-m-t');

		$penjualan_bulan_ini = $this->db->select_sum('total')
			->where('DATE(tanggal_so) >=', $start_month)
			->where('DATE(tanggal_so) <=', $end_month)
			->get('penjualan_marketplace')->row()->total ?: 0;

		$terbayar_lunas = $this->db->select_sum('penjualan_marketplace.total')
			->join('invoice', 'invoice.so_id = penjualan_marketplace.id')
			->where('invoice.status', 'paid')
			->where('DATE(penjualan_marketplace.tanggal_so) >=', $start_month)
			->where('DATE(penjualan_marketplace.tanggal_so) <=', $end_month)
			->get('penjualan_marketplace')->row()->total ?: 0;

		$piutang_unpaid = max(0, $penjualan_bulan_ini - $terbayar_lunas);

		$pesanan_aktif = $this->db->where_in('status', ['draft', 'submitted'])
			->count_all_results('penjualan_marketplace');

		$proses_gudang = $this->db->where_in('status', ['confirmed', 'packing'])
			->count_all_results('penjualan_marketplace');

		$siap_kirim = $this->db->where_in('status', ['ready_to_ship', 'shipped'])
			->count_all_results('penjualan_marketplace');

		$pesanan_terkirim = $this->db->where_in('status', ['delivered', 'completed', 'paid'])
			->count_all_results('penjualan_marketplace');

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

/* End of file Model_penjualan_marketplace.php */
/* Location: ./modules/penjualan_marketplace/models/Model_penjualan_marketplace.php */
