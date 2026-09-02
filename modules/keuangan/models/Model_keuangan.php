<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_keuangan extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'utang_usaha';
	private $field_search 	= ['utang_usaha.nomor_utang', 'supplier.nama_supplier'];

	public function __construct()
	{
		$config = array(
			'primary_key' 	=> $this->primary_key,
		 	'table_name' 	=> $this->table_name,
		 	'field_search' 	=> $this->field_search,
		 );
		parent::__construct($config);
	}

	// ------------------------------------------------------------------------
	// UTANG USAHA (AP)
	// ------------------------------------------------------------------------
	public function get_utang($filter = '', $field = '', $limit = 0, $offset = 0)
	{
		$this->db->select('utang_usaha.*, supplier.nama_supplier, supplier.kode_supplier, penerimaan_barang.nomor_penerimaan, purchase_order.nomor_po');
		$this->db->join('supplier', 'supplier.id = utang_usaha.supplier_id', 'LEFT');
		$this->db->join('penerimaan_barang', 'penerimaan_barang.id = utang_usaha.penerimaan_id', 'LEFT');
		$this->db->join('purchase_order', 'purchase_order.id = utang_usaha.po_id', 'LEFT');

		if ($this->input->get('supplier_id')) {
			$this->db->where('utang_usaha.supplier_id', $this->input->get('supplier_id'));
		}
		if ($this->input->get('status')) {
			$this->db->where('utang_usaha.status', $this->input->get('status'));
		}
		if ($filter) {
			$this->db->group_start();
			$this->db->like('utang_usaha.nomor_utang', $filter);
			$this->db->or_like('supplier.nama_supplier', $filter);
			$this->db->group_end();
		}

		$this->db->limit($limit, $offset);
		$this->db->order_by('utang_usaha.id', 'DESC');
		return $this->db->get('utang_usaha')->result();
	}

	public function count_utang($filter = '')
	{
		if ($this->input->get('supplier_id')) {
			$this->db->where('utang_usaha.supplier_id', $this->input->get('supplier_id'));
		}
		if ($this->input->get('status')) {
			$this->db->where('utang_usaha.status', $this->input->get('status'));
		}
		if ($filter) {
			$this->db->group_start();
			$this->db->like('utang_usaha.nomor_utang', $filter);
			$this->db->or_like('supplier.nama_supplier', $filter);
			$this->db->group_end();
		}
		return $this->db->count_all_results('utang_usaha');
	}

	public function get_stats_utang()
	{
		$total_kewajiban = $this->db->select_sum('total')->get('utang_usaha')->row()->total ?: 0;
		$total_dibayar   = $this->db->select_sum('terbayar')->get('utang_usaha')->row()->terbayar ?: 0;
		$sisa_utang      = $this->db->select_sum('sisa')->get('utang_usaha')->row()->sisa ?: 0;

		$today = date('Y-m-d');
		$overdue_res = $this->db->select('SUM(sisa) as nominal, COUNT(*) as count')
			->where('tanggal_jatuh_tempo <', $today)
			->where('status !=', 'lunas')
			->get('utang_usaha')->row();

		return [
			'total_kewajiban' => $total_kewajiban,
			'total_dibayar'   => $total_dibayar,
			'sisa_utang'      => $sisa_utang,
			'overdue_nominal' => $overdue_res->nominal ?: 0,
			'overdue_count'   => $overdue_res->count ?: 0,
			'total_faktur'    => $this->db->count_all('utang_usaha'),
		];
	}

	// ------------------------------------------------------------------------
	// PIUTANG USAHA (AR)
	// ------------------------------------------------------------------------
	public function get_piutang($filter = '', $field = '', $limit = 0, $offset = 0)
	{
		$this->db->select('piutang_usaha.*, pelanggan.nama_pelanggan, pelanggan.kode_pelanggan, sales_order.nomor_so, invoice.nomor_invoice');
		$this->db->join('pelanggan', 'pelanggan.id = piutang_usaha.pelanggan_id', 'LEFT');
		$this->db->join('sales_order', 'sales_order.id = piutang_usaha.so_id', 'LEFT');
		$this->db->join('invoice', 'invoice.id = piutang_usaha.invoice_id', 'LEFT');

		if ($this->input->get('pelanggan_id')) {
			$this->db->where('piutang_usaha.pelanggan_id', $this->input->get('pelanggan_id'));
		}
		if ($this->input->get('status')) {
			$this->db->where('piutang_usaha.status', $this->input->get('status'));
		}
		if ($filter) {
			$this->db->group_start();
			$this->db->like('piutang_usaha.nomor_piutang', $filter);
			$this->db->or_like('pelanggan.nama_pelanggan', $filter);
			$this->db->group_end();
		}

		$this->db->limit($limit, $offset);
		$this->db->order_by('piutang_usaha.id', 'DESC');
		return $this->db->get('piutang_usaha')->result();
	}

	public function count_piutang($filter = '')
	{
		if ($this->input->get('pelanggan_id')) {
			$this->db->where('piutang_usaha.pelanggan_id', $this->input->get('pelanggan_id'));
		}
		if ($this->input->get('status')) {
			$this->db->where('piutang_usaha.status', $this->input->get('status'));
		}
		if ($filter) {
			$this->db->group_start();
			$this->db->like('piutang_usaha.nomor_piutang', $filter);
			$this->db->or_like('pelanggan.nama_pelanggan', $filter);
			$this->db->group_end();
		}
		return $this->db->count_all_results('piutang_usaha');
	}

	public function get_stats_piutang()
	{
		$total_hak    = $this->db->select_sum('total')->get('piutang_usaha')->row()->total ?: 0;
		$total_terima = $this->db->select_sum('terbayar')->get('piutang_usaha')->row()->terbayar ?: 0;
		$sisa         = $this->db->select_sum('sisa')->get('piutang_usaha')->row()->sisa ?: 0;

		$today = date('Y-m-d');
		$overdue_res = $this->db->select('SUM(sisa) as nominal, COUNT(*) as count')
			->where('tanggal_jatuh_tempo <', $today)
			->where('status !=', 'lunas')
			->get('piutang_usaha')->row();

		return [
			'total_hak'       => $total_hak,
			'total_terima'    => $total_terima,
			'sisa_piutang'    => $sisa,
			'overdue_nominal' => $overdue_res->nominal ?: 0,
			'overdue_count'   => $overdue_res->count ?: 0,
			'total_faktur'    => $this->db->count_all('piutang_usaha'),
		];
	}

	// ------------------------------------------------------------------------
	// DASHBOARD FINANCIAL OVERVIEW
	// ------------------------------------------------------------------------
	public function get_dashboard_summary()
	{
		$kas_masuk  = $this->db->select_sum('nominal')->get('pembayaran_piutang')->row()->nominal ?: 0;
		$kas_keluar = $this->db->select_sum('nominal')->get('pembayaran_utang')->row()->nominal ?: 0;
		$saldo_kas  = $kas_masuk - $kas_keluar;

		$utang_aktif   = $this->db->select_sum('sisa')->where('status !=', 'lunas')->get('utang_usaha')->row()->sisa ?: 0;
		$piutang_aktif = $this->db->select_sum('sisa')->where('status !=', 'lunas')->get('piutang_usaha')->row()->sisa ?: 0;

		return [
			'kas_masuk'     => $kas_masuk,
			'kas_keluar'    => $kas_keluar,
			'saldo_kas'     => $saldo_kas,
			'utang_aktif'   => $utang_aktif,
			'piutang_aktif' => $piutang_aktif,
		];
	}

	public function generate_bkk()
	{
		$prefix = 'BKK-' . date('Ym') . '-';
		$last = $this->db->like('nomor_bkk', $prefix, 'after')
			->order_by('id', 'DESC')->limit(1)->get('pembayaran_utang')->row();
		$num = $last ? ((int) substr($last->nomor_bkk, -4)) + 1 : 1;
		return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
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

/* End of file Model_keuangan.php */
/* Location: ./modules/keuangan/models/Model_keuangan.php */
