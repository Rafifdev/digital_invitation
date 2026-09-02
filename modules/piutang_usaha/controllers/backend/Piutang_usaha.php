<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Piutang_usaha extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_piutang_usaha');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('piutang_usaha_list');

		// Auto-sanitize any negative values in piutang_usaha database table from previous test entries
		$this->db->query("UPDATE piutang_usaha SET total = ABS(total), sisa = ABS(sisa) WHERE total < 0 OR sisa < 0");

		$filter       = trim((string)$this->input->get('q'));
		$field        = $this->input->get('f');

		$this->data['piutang_list'] = $this->model_piutang_usaha->get($filter, $field, $this->limit_page, $offset);
		$this->data['piutang_counts'] = $this->model_piutang_usaha->count_all($filter, $field);
		$this->data['stats']          = $this->model_piutang_usaha->get_stats();
		$this->data['pelanggans']     = $this->db->get('pelanggan')->result();

		$config = [
			'base_url'    => 'administrator/piutang_usaha/index/',
			'total_rows'  => $this->data['piutang_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Piutang Usaha (Accounts Receivable)');
		$this->render('backend/standart/administrator/piutang_usaha/piutang_usaha_list', $this->data);
	}

	public function kas_masuk($offset = 0)
	{
		$this->is_allowed('piutang_usaha_list');

		$q            = trim((string)$this->input->get('q'));
		$pelanggan_id = $this->input->get('pelanggan_id');

		$this->db->select('pembayaran_piutang.*, pelanggan.nama_pelanggan, pelanggan.kode_pelanggan, piutang_usaha.nomor_piutang');
		$this->db->join('pelanggan', 'pelanggan.id = pembayaran_piutang.pelanggan_id', 'LEFT');
		$this->db->join('piutang_usaha', 'piutang_usaha.id = pembayaran_piutang.piutang_id', 'LEFT');

		if ($q) {
			$this->db->group_start();
			$this->db->like('pembayaran_piutang.nomor_bkm', $q);
			$this->db->or_like('pelanggan.nama_pelanggan', $q);
			$this->db->or_like('piutang_usaha.nomor_piutang', $q);
			$this->db->group_end();
		}

		$this->db->limit($this->limit_page, $offset);
		$this->db->order_by('pembayaran_piutang.id', 'DESC');
		$this->data['bkm_list'] = $this->db->get('pembayaran_piutang')->result();

		$this->data['pelanggans'] = $this->db->get('pelanggan')->result();

		$this->template->title('Penerimaan Kas Masuk (BKM)');
		$this->render('backend/standart/administrator/piutang_usaha/kas_masuk_list', $this->data);
	}

	public function bayar_save()
	{
		if (!$this->is_allowed('piutang_usaha_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('piutang_id', 'Piutang', 'trim|required');
		$this->form_validation->set_rules('nominal', 'Nominal Bayar', 'trim|required');

		if ($this->form_validation->run()) {
			$piutang_id = $this->input->post('piutang_id');
			$nominal    = (float) str_replace(['.', ','], '', $this->input->post('nominal'));
			$piutang    = $this->db->get_where('piutang_usaha', ['id' => $piutang_id])->row();

			if (!$piutang) {
				$this->response(['success' => false, 'message' => 'Data Piutang tidak ditemukan']); return;
			}

			if ($nominal <= 0 || ($nominal - $piutang->sisa) > 1.0) {
				$this->response(['success' => false, 'message' => 'Nominal bayar tidak boleh melebihi sisa piutang (Rp ' . number_format($piutang->sisa, 0, ',', '.') . ')']); return;
			}
			if (abs($nominal - $piutang->sisa) <= 1.0) {
				$nominal = (float) $piutang->sisa;
			}

			$nomor_bkm = $this->model_piutang_usaha->generate_bkm();
			$bkm_data = [
				'nomor_bkm'         => $nomor_bkm,
				'piutang_id'        => $piutang_id,
				'pelanggan_id'      => $piutang->pelanggan_id,
				'tanggal_bayar'     => $this->input->post('tanggal_bayar') ?: date('Y-m-d'),
				'akun_kas_bank'     => $this->input->post('akun_kas_bank') ?: 'Bank BCA',
				'metode_pembayaran' => $this->input->post('metode_pembayaran') ?: 'bank_transfer',
				'nomor_referensi'   => $this->input->post('nomor_referensi'),
				'nominal'           => $nominal,
				'catatan'           => $this->input->post('catatan'),
				'created_by'        => get_user_data('username'),
				'created_at'        => date('Y-m-d H:i:s'),
			];
			$this->db->insert('pembayaran_piutang', $bkm_data);

			// Update piutang_usaha terbayar, sisa, status by summing directly from DB to avoid out-of-sync
			$total_terbayar = $this->db->select_sum('nominal')->get_where('pembayaran_piutang', ['piutang_id' => $piutang_id])->row()->nominal ?: 0;
			$sisa_new     = max(0, $piutang->total - $total_terbayar);
			$status_new   = ($sisa_new <= 0) ? 'lunas' : 'sebagian';

			$this->db->update('piutang_usaha', [
				'terbayar'   => $total_terbayar,
				'sisa'       => $sisa_new,
				'status'     => $status_new,
				'updated_at' => date('Y-m-d H:i:s'),
			], ['id' => $piutang_id]);

			// Also update Invoice and Sales Order status if associated!
			if ($piutang->so_id) {
				$this->db->update('sales_order', ['status' => ($status_new == 'lunas' ? 'completed' : 'confirmed')], ['id' => $piutang->so_id]);
			}
			if ($piutang->invoice_id) {
				$this->db->update('invoice', ['status' => ($status_new == 'lunas' ? 'paid' : 'partial')], ['id' => $piutang->invoice_id]);
			}

			set_message('Pembayaran Piutang berhasil disimpan dengan Nomor ' . $nomor_bkm, 'success');
			$this->data['success']  = true;
			$this->data['redirect'] = base_url('administrator/piutang_usaha');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function cetak_bkm($id)
	{
		$this->is_allowed('piutang_usaha_list');
		$this->data['bkm'] = $this->db->select('pembayaran_piutang.*, pelanggan.nama_pelanggan, pelanggan.kode_pelanggan, pelanggan.alamat, piutang_usaha.nomor_piutang')
			->join('pelanggan', 'pelanggan.id = pembayaran_piutang.pelanggan_id', 'LEFT')
			->join('piutang_usaha', 'piutang_usaha.id = pembayaran_piutang.piutang_id', 'LEFT')
			->get_where('pembayaran_piutang', ['pembayaran_piutang.id' => $id])->row();

		if (!$this->data['bkm']) {
			show_404(); return;
		}

		$this->load->library('HtmlPdf');
		$this->htmlpdf->initialize(['orientation' => 'P', 'format' => 'A4', 'marges' => [10, 10, 10, 10]]);
		$content = $this->load->view('backend/standart/administrator/piutang_usaha/bkm_pdf', $this->data, true);
		
		$this->htmlpdf->writeHTML($content);
		$doc_num = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->data['bkm']->nomor_bkm ?? $id);
		$filename = 'bkm_' . $doc_num . '.pdf';
		if (ob_get_length()) { ob_clean(); }
		$this->htmlpdf->Output($filename, 'D');
	}

	public function export()
	{
		$this->is_allowed('piutang_usaha_export');
		$this->model_piutang_usaha->export('piutang_usaha', 'piutang_usaha');
	}

	public function export_pdf()
	{
		$this->is_allowed('piutang_usaha_export');
		$this->model_piutang_usaha->pdf('piutang_usaha', 'piutang_usaha');
	}

}
