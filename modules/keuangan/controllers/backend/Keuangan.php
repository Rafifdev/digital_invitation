<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keuangan extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_keuangan');
	}

	// 1. Ringkasan Financial Dashboard
	public function index()
	{
		$this->is_allowed('utang_usaha_list');
		$this->data['summary']       = $this->model_keuangan->get_dashboard_summary();
		$this->data['stats_utang']   = $this->model_keuangan->get_stats_utang();
		$this->data['stats_piutang'] = $this->model_keuangan->get_stats_piutang();
		
		// Recent transactions BKK & BKM
		$this->db->select('pembayaran_utang.*, supplier.nama_supplier');
		$this->db->join('supplier', 'supplier.id = pembayaran_utang.supplier_id', 'LEFT');
		$this->db->limit(5)->order_by('id', 'DESC');
		$this->data['bkk_recent'] = $this->db->get('pembayaran_utang')->result();

		$this->db->select('pembayaran_piutang.*, pelanggan.nama_pelanggan');
		$this->db->join('pelanggan', 'pelanggan.id = pembayaran_piutang.pelanggan_id', 'LEFT');
		$this->db->limit(5)->order_by('id', 'DESC');
		$this->data['bkm_recent'] = $this->db->get('pembayaran_piutang')->result();

		$this->template->title('Dashboard Ringkasan Keuangan');
		$this->render('backend/standart/administrator/keuangan/keuangan_dashboard', $this->data);
	}

	// 2. Ledger Utang Usaha (AP)
	public function utang($offset = 0)
	{
		$this->is_allowed('utang_usaha_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['debts']        = $this->model_keuangan->get_utang($filter, $field, $this->limit_page, $offset);
		$this->data['debt_counts']   = $this->model_keuangan->count_utang($filter);
		$this->data['stats']         = $this->model_keuangan->get_stats_utang();
		$this->data['suppliers']     = $this->db->get('supplier')->result();

		$config = [
			'base_url'    => 'administrator/keuangan/utang/',
			'total_rows'  => $this->data['debt_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Ledger Utang Usaha (Accounts Payable)');
		$this->render('backend/standart/administrator/keuangan/utang_list', $this->data);
	}

	// 3. Pengeluaran Kas (BKK)
	public function kas_keluar($offset = 0)
	{
		$this->is_allowed('utang_usaha_list');

		$this->db->select('pembayaran_utang.*, supplier.nama_supplier, supplier.kode_supplier, utang_usaha.nomor_utang');
		$this->db->join('supplier', 'supplier.id = pembayaran_utang.supplier_id', 'LEFT');
		$this->db->join('utang_usaha', 'utang_usaha.id = pembayaran_utang.utang_id', 'LEFT');

		if ($this->input->get('supplier_id')) {
			$this->db->where('pembayaran_utang.supplier_id', $this->input->get('supplier_id'));
		}
		if ($this->input->get('q')) {
			$q = $this->input->get('q');
			$this->db->group_start();
			$this->db->like('pembayaran_utang.nomor_bkk', $q);
			$this->db->or_like('supplier.nama_supplier', $q);
			$this->db->or_like('utang_usaha.nomor_utang', $q);
			$this->db->group_end();
		}

		$this->db->limit($this->limit_page, $offset);
		$this->db->order_by('pembayaran_utang.id', 'DESC');
		$this->data['bkk_list'] = $this->db->get('pembayaran_utang')->result();
		$this->data['suppliers'] = $this->db->get('supplier')->result();

		$this->template->title('Pengeluaran Kas Keluar (BKK)');
		$this->render('backend/standart/administrator/keuangan/kas_keluar_list', $this->data);
	}

	// 4. Ledger Piutang Usaha (AR)
	public function piutang($offset = 0)
	{
		$this->is_allowed('utang_usaha_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['piutang_list']   = $this->model_keuangan->get_piutang($filter, $field, $this->limit_page, $offset);
		$this->data['piutang_counts'] = $this->model_keuangan->count_piutang($filter);
		$this->data['stats']          = $this->model_keuangan->get_stats_piutang();
		$this->data['pelanggans']     = $this->db->get('pelanggan')->result();

		$config = [
			'base_url'    => 'administrator/keuangan/piutang/',
			'total_rows'  => $this->data['piutang_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Ledger Piutang Usaha (Accounts Receivable)');
		$this->render('backend/standart/administrator/keuangan/piutang_list', $this->data);
	}

	// 5. Penerimaan Kas (BKM)
	public function kas_masuk($offset = 0)
	{
		$this->is_allowed('utang_usaha_list');

		$this->db->select('pembayaran_piutang.*, pelanggan.nama_pelanggan, pelanggan.kode_pelanggan, piutang_usaha.nomor_piutang');
		$this->db->join('pelanggan', 'pelanggan.id = pembayaran_piutang.pelanggan_id', 'LEFT');
		$this->db->join('piutang_usaha', 'piutang_usaha.id = pembayaran_piutang.piutang_id', 'LEFT');

		if ($this->input->get('pelanggan_id')) {
			$this->db->where('pembayaran_piutang.pelanggan_id', $this->input->get('pelanggan_id'));
		}
		if ($this->input->get('q')) {
			$q = $this->input->get('q');
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
		$this->render('backend/standart/administrator/keuangan/kas_masuk_list', $this->data);
	}

	// Save Bayar Utang (Kas Keluar BKK)
	public function bayar_utang_save()
	{
		if (!$this->is_allowed('utang_usaha_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('utang_id', 'Utang', 'trim|required');
		$this->form_validation->set_rules('nominal', 'Nominal Bayar', 'trim|required');

		if ($this->form_validation->run()) {
			$utang_id = $this->input->post('utang_id');
			$nominal  = (float) str_replace(['.', ','], '', $this->input->post('nominal'));
			$utang    = $this->db->get_where('utang_usaha', ['id' => $utang_id])->row();

			if (!$utang) {
				$this->response(['success' => false, 'message' => 'Data Utang tidak ditemukan']); return;
			}

			if ($nominal <= 0 || $nominal > $utang->sisa) {
				$this->response(['success' => false, 'message' => 'Nominal bayar tidak boleh melebihi sisa utang']); return;
			}

			$nomor_bkk = $this->model_keuangan->generate_bkk();
			$this->db->insert('pembayaran_utang', [
				'nomor_bkk'         => $nomor_bkk,
				'utang_id'          => $utang_id,
				'supplier_id'       => $utang->supplier_id,
				'tanggal_bayar'     => $this->input->post('tanggal_bayar') ?: date('Y-m-d'),
				'akun_kas_bank'     => $this->input->post('akun_kas_bank') ?: 'Bank Mandiri',
				'metode_pembayaran' => $this->input->post('metode_pembayaran') ?: 'bank_transfer',
				'nomor_referensi'   => $this->input->post('nomor_referensi'),
				'nominal'           => $nominal,
				'catatan'           => $this->input->post('catatan'),
				'created_by'        => get_user_data('username'),
				'created_at'        => date('Y-m-d H:i:s'),
			]);

			// Calculate accurate total terbayar from DB to avoid out-of-sync
			$total_terbayar = $this->db->select_sum('nominal')->get_where('pembayaran_utang', ['utang_id' => $utang_id])->row()->nominal ?: 0;
			$sisa_new     = max(0, $utang->total - $total_terbayar);
			$status_new   = ($sisa_new <= 0) ? 'lunas' : 'sebagian';

			$this->db->update('utang_usaha', [
				'terbayar'   => $total_terbayar,
				'sisa'       => $sisa_new,
				'status'     => $status_new,
				'updated_at' => date('Y-m-d H:i:s'),
			], ['id' => $utang_id]);

			set_message('Pembayaran Utang berhasil disimpan dengan Voucher ' . $nomor_bkk, 'success');
			$this->data['success']  = true;
			$this->data['redirect'] = base_url('administrator/keuangan/utang');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	// Save Terima Piutang (Kas Masuk BKM)
	public function bayar_piutang_save()
	{
		if (!$this->is_allowed('utang_usaha_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('piutang_id', 'Piutang', 'trim|required');
		$this->form_validation->set_rules('nominal', 'Nominal Diterima', 'trim|required');

		if ($this->form_validation->run()) {
			$piutang_id = $this->input->post('piutang_id');
			$nominal    = (float) str_replace(['.', ','], '', $this->input->post('nominal'));
			$piutang    = $this->db->get_where('piutang_usaha', ['id' => $piutang_id])->row();

			if (!$piutang) {
				$this->response(['success' => false, 'message' => 'Data Piutang tidak ditemukan']); return;
			}

			if ($nominal <= 0 || $nominal > $piutang->sisa) {
				$this->response(['success' => false, 'message' => 'Nominal diterima tidak boleh melebihi sisa piutang']); return;
			}

			$nomor_bkm = $this->model_keuangan->generate_bkm();
			$this->db->insert('pembayaran_piutang', [
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
			]);

			// Calculate accurate total terbayar from DB to avoid out-of-sync
			$total_terbayar = $this->db->select_sum('nominal')->get_where('pembayaran_piutang', ['piutang_id' => $piutang_id])->row()->nominal ?: 0;
			$sisa_new     = max(0, $piutang->total - $total_terbayar);
			$status_new   = ($sisa_new <= 0) ? 'lunas' : 'sebagian';

			$this->db->update('piutang_usaha', [
				'terbayar'   => $total_terbayar,
				'sisa'       => $sisa_new,
				'status'     => $status_new,
				'updated_at' => date('Y-m-d H:i:s'),
			], ['id' => $piutang_id]);

			if ($piutang->so_id) {
				$this->db->update('sales_order', ['status' => ($status_new == 'lunas' ? 'completed' : 'confirmed')], ['id' => $piutang->so_id]);
			}
			if ($piutang->invoice_id) {
				$this->db->update('invoice', ['status' => ($status_new == 'lunas' ? 'paid' : 'partial')], ['id' => $piutang->invoice_id]);
			}

			set_message('Penerimaan Kas berhasil disimpan dengan Voucher ' . $nomor_bkm, 'success');
			$this->data['success']  = true;
			$this->data['redirect'] = base_url('administrator/keuangan/piutang');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}
}

/* End of file Keuangan.php */
/* Location: ./modules/keuangan/controllers/backend/Keuangan.php */
