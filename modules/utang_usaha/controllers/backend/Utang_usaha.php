<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utang_usaha extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_utang_usaha');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('utang_usaha_list');

		// Auto-round any trailing decimals in utang_usaha table to clean integer currency
		$this->db->query("UPDATE utang_usaha SET total = ROUND(total), terbayar = ROUND(terbayar), sisa = ROUND(sisa) WHERE total != ROUND(total) OR sisa != ROUND(sisa)");

		$filter      = trim((string)$this->input->get('q'));
		$field       = $this->input->get('f');
		$supplier_id = $this->input->get('supplier_id');
		$status      = $this->input->get('status');

		$this->data['utangs']       = $this->model_utang_usaha->get($filter, $field, $this->limit_page, $offset, $supplier_id, $status);
		$this->data['utang_counts'] = $this->model_utang_usaha->count_all($filter, $field, $supplier_id, $status);
		$this->data['stats']        = $this->model_utang_usaha->get_stats();
		$this->data['suppliers']    = $this->db->get('supplier')->result();

		$config = [
			'base_url'    => 'administrator/utang_usaha/index/',
			'total_rows'  => $this->data['utang_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Utang Usaha (AP)');
		$this->render('backend/standart/administrator/utang_usaha/utang_usaha_list', $this->data);
	}

	public function bayar_save()
	{
		if (!$this->is_allowed('utang_usaha_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$utang_id          = $this->input->post('utang_id');
		$nominal           = (float) str_replace(['.', ','], '', $this->input->post('nominal'));
		$tanggal_bayar     = $this->input->post('tanggal_bayar') ?: date('Y-m-d');
		$akun_kas_bank     = $this->input->post('akun_kas_bank') ?: 'Bank BCA';
		$metode_pembayaran = $this->input->post('metode_pembayaran') ?: 'bank_transfer';
		$nomor_referensi   = $this->input->post('nomor_referensi');
		$catatan           = $this->input->post('catatan');

		$utang = $this->model_utang_usaha->find($utang_id);
		if (!$utang) {
			$this->response(['success' => false, 'message' => 'Data tagihan utang tidak ditemukan']); return;
		}

		if ($nominal <= 0 || $nominal > $utang->sisa) {
			$this->response(['success' => false, 'message' => 'Nominal pembayaran tidak valid! (Maksimal Rp ' . number_format($utang->sisa, 0) . ')']); return;
		}

		$nomor_bkk = $this->model_utang_usaha->generate_bkk_nomor();
		$this->db->insert('pembayaran_utang', [
			'nomor_bkk'         => $nomor_bkk,
			'utang_id'          => $utang_id,
			'supplier_id'       => $utang->supplier_id,
			'tanggal_bayar'     => $tanggal_bayar,
			'akun_kas_bank'     => $akun_kas_bank,
			'metode_pembayaran' => $metode_pembayaran,
			'nomor_referensi'   => $nomor_referensi,
			'nominal'           => $nominal,
			'catatan'           => $catatan,
			'created_by'        => get_user_data('username'),
			'created_at'        => date('Y-m-d H:i:s'),
		]);

		// Calculate accurate total terbayar from DB to avoid out-of-sync
		$total_terbayar = $this->db->select_sum('nominal')->get_where('pembayaran_utang', ['utang_id' => $utang_id])->row()->nominal ?: 0;
		$new_sisa     = max(0, $utang->total - $total_terbayar);
		$new_status   = ($new_sisa <= 0) ? 'lunas' : 'sebagian';

		$this->db->update('utang_usaha', [
			'terbayar'   => $total_terbayar,
			'sisa'       => $new_sisa,
			'status'     => $new_status,
			'updated_at' => date('Y-m-d H:i:s'),
		], ['id' => $utang_id]);

		set_message('Pembayaran utang berhasil dicatat dengan Bukti Kas Keluar (' . $nomor_bkk . ').', 'success');
		$this->response(['success' => true, 'redirect' => site_url('administrator/utang_usaha/kas_keluar')]);
	}

	public function kas_keluar($offset = 0)
	{
		$this->is_allowed('utang_usaha_list');

		$supplier_id = $this->input->get('supplier_id');
		$metode      = $this->input->get('metode');
		$q           = trim((string)$this->input->get('q'));

		$this->data['kas_keluars']      = $this->model_utang_usaha->get_kas_keluar($supplier_id, $metode, $q, $this->limit_page, $offset);
		$this->data['kas_keluar_count'] = $this->model_utang_usaha->count_kas_keluar($supplier_id, $metode, $q);
		$this->data['suppliers']        = $this->db->get('supplier')->result();

		$config = [
			'base_url'    => 'administrator/utang_usaha/kas_keluar/',
			'total_rows'  => $this->data['kas_keluar_count'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pengeluaran Kas Keluar (BKK)');
		$this->render('backend/standart/administrator/utang_usaha/kas_keluar_list', $this->data);
	}

	public function cetak_bkk($id)
	{
		$this->is_allowed('utang_usaha_list');
		$this->data['bkk'] = $this->db->select('pembayaran_utang.*, supplier.nama_supplier, supplier.kode_supplier, supplier.alamat, utang_usaha.nomor_utang, utang_usaha.total as total_utang')
			->join('supplier', 'supplier.id = pembayaran_utang.supplier_id', 'LEFT')
			->join('utang_usaha', 'utang_usaha.id = pembayaran_utang.utang_id', 'LEFT')
			->get_where('pembayaran_utang', ['pembayaran_utang.id' => $id])->row();

		if (!$this->data['bkk']) {
			show_404();
		}

		$this->load->library('HtmlPdf');
		$this->htmlpdf->initialize(['orientation' => 'P', 'format' => 'A4', 'marges' => [5, 5, 5, 5]]);
		$content = $this->load->view('backend/standart/administrator/utang_usaha/bkk_pdf', $this->data, true);

		$this->htmlpdf->writeHTML($content);
		$filename = 'Voucher_BKK_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->data['bkk']->nomor_bkk) . '.pdf';
		if (ob_get_length()) { ob_clean(); }
		$this->htmlpdf->Output($filename, 'D');
	}

	public function export()
	{
		$this->is_allowed('utang_usaha_export');
		$this->model_utang_usaha->export('utang_usaha', 'utang_usaha');
	}

	public function export_pdf()
	{
		$this->is_allowed('utang_usaha_export');
		$this->model_utang_usaha->pdf('utang_usaha', 'utang_usaha');
	}

}
