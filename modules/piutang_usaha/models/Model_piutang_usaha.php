<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_piutang_usaha extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'piutang_usaha';
	private $field_search 	= ['piutang_usaha.nomor_piutang', 'pelanggan.nama_pelanggan', 'piutang_usaha.status'];

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
		$this->db->select('piutang_usaha.*, pelanggan.nama_pelanggan, pelanggan.kode_pelanggan, sales_order.nomor_so, invoice.nomor_invoice, invoice.tanggal_invoice');
		$this->db->join('pelanggan', 'pelanggan.id = piutang_usaha.pelanggan_id', 'LEFT');
		$this->db->join('sales_order', 'sales_order.id = piutang_usaha.so_id', 'LEFT');
		$this->db->join('invoice', 'invoice.id = piutang_usaha.invoice_id', 'LEFT');
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
		$this->db->order_by('piutang_usaha.'.$this->primary_key, 'DESC');
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

		if ($this->input->get('pelanggan_id')) {
			$where .= ($where ? ' AND ' : '') . "piutang_usaha.pelanggan_id = " . intval($this->input->get('pelanggan_id'));
		}
		if ($this->input->get('status')) {
			$where .= ($where ? ' AND ' : '') . "piutang_usaha.status = " . $this->db->escape($this->input->get('status'));
		}

		return $where;
	}

	public function get_stats()
	{
		$total_hak = $this->db->select_sum('total')->get('piutang_usaha')->row()->total ?: 0;
		$total_terima = $this->db->select_sum('terbayar')->get('piutang_usaha')->row()->terbayar ?: 0;
		$sisa = $this->db->select_sum('sisa')->get('piutang_usaha')->row()->sisa ?: 0;

		$today = date('Y-m-d');
		$overdue_res = $this->db->select('SUM(sisa) as nominal, COUNT(*) as count')
			->where('tanggal_jatuh_tempo <', $today)
			->where('status !=', 'lunas')
			->get('piutang_usaha')->row();

		return [
			'total_hak'       => abs($total_hak),
			'total_terima'    => abs($total_terima),
			'sisa_piutang'    => abs($sisa),
			'overdue_nominal' => abs($overdue_res->nominal ?: 0),
			'overdue_count'   => $overdue_res->count ?: 0,
			'total_faktur'    => $this->db->count_all('piutang_usaha'),
		];
	}

	public function generate_bkm()
	{
		$prefix = 'BKM-' . date('Ym') . '-';
		$last = $this->db->like('nomor_bkm', $prefix, 'after')
			->order_by('id', 'DESC')->limit(1)->get('pembayaran_piutang')->row();
		$num = $last ? ((int) substr($last->nomor_bkm, -4)) + 1 : 1;
		return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
	}
}

/* End of file Model_piutang_usaha.php */
/* Location: ./modules/piutang_usaha/models/Model_piutang_usaha.php */
