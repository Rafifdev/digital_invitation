<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Transfer_stok extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_transfer_stok');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('transfer_stok_list');
		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['transfers']      = $this->model_transfer_stok->get($filter, $field, $this->limit_page, $offset);
		$this->data['transfer_counts'] = $this->model_transfer_stok->count_all($filter, $field);

		$config = ['base_url' => 'administrator/transfer_stok/index/', 'total_rows' => $this->data['transfer_counts'], 'per_page' => $this->limit_page, 'uri_segment' => 4];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Transfer Stok');
		$this->render('backend/standart/administrator/transfer_stok/transfer_stok_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('transfer_stok_add');
		$this->data['nomor_baru'] = $this->model_transfer_stok->generate_nomor();
		$this->data['gudangs']    = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->data['produks']    = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->template->title('Buat Transfer Stok');
		$this->render('backend/standart/administrator/transfer_stok/transfer_stok_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('transfer_stok_add', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('gudang_asal_id', 'Gudang Asal', 'trim|required');
		$this->form_validation->set_rules('gudang_tujuan_id', 'Gudang Tujuan', 'trim|required');
		$this->form_validation->set_rules('tanggal_transfer', 'Tanggal Transfer', 'trim|required');

		if ($this->form_validation->run()) {
			// Backend validation: Ensure qty does not exceed available stock in origin warehouse
			$gudang_asal_id = $this->input->post('gudang_asal_id');
			$items = $this->input->post('items');
			if (!empty($items)) {
				$this->load->model('produk/model_produk');
				foreach ($items as $item) {
					if (empty($item['produk_id'])) continue;
					$produk_id = $item['produk_id'];
					$qty = (float) $item['qty'];
					$stok_tersedia = (float) $this->model_produk->get_stok($produk_id, $gudang_asal_id);
					if ($qty > $stok_tersedia) {
						$produk = $this->db->get_where('produk', ['id' => $produk_id])->row();
						$nama_prod = $produk ? $produk->nama_produk : 'Produk';
						$this->response([
							'success' => false,
							'message' => "Qty transfer untuk {$nama_prod} ({$qty}) melebihi stok yang tersedia ({$stok_tersedia}) di Gudang Asal!"
						]);
						return;
					}
				}
			}

			$data = [
				'nomor_transfer'       => $this->model_transfer_stok->generate_nomor(),
				'gudang_asal_id'       => $this->input->post('gudang_asal_id'),
				'gudang_tujuan_id'     => $this->input->post('gudang_tujuan_id'),
				'tanggal_transfer'     => $this->input->post('tanggal_transfer'),
				'tanggal_estimasi_tiba'=> $this->input->post('tanggal_estimasi_tiba') ?: null,
				'nomor_bukti_kirim'    => $this->input->post('nomor_bukti_kirim') ?: null,
				'status'               => 'draft',
				'catatan'              => $this->input->post('catatan'),
				'created_by'           => get_user_data('username'),
				'created_at'           => date('Y-m-d H:i:s'),
			];

			$transfer_id = $this->model_transfer_stok->store($data);

			if ($transfer_id && $this->input->post('items')) {
				foreach ($this->input->post('items') as $item) {
					if (empty($item['produk_id'])) continue;
					$this->db->insert('transfer_stok_item', [
						'transfer_id' => $transfer_id,
						'produk_id'   => $item['produk_id'],
						'qty'         => (float) $item['qty'],
						'batch_no'    => $item['batch_no'],
						'catatan'     => $item['catatan'],
					]);
				}
			}

			set_message('Transfer stok berhasil dibuat.', 'success');
			$this->data['success']  = true;
			$this->data['redirect'] = base_url('administrator/transfer_stok');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function view($id)
	{
		$this->is_allowed('transfer_stok_view');
		$this->data['transfer'] = $this->model_transfer_stok->join_available()->find($id);
		$this->data['items']    = $this->model_transfer_stok->get_items($id);
		$this->template->title('Detail Transfer Stok');
		$this->render('backend/standart/administrator/transfer_stok/transfer_stok_view', $this->data);
	}

	public function approve($id)
	{
		$this->is_allowed('transfer_stok_approve');
		$t = $this->model_transfer_stok->find($id);
		if ($t && $t->status == 'submitted') {
			$this->db->update('transfer_stok', [
				'status'      => 'approved',
				'approved_by' => get_user_data('username'),
				'approved_at' => date('Y-m-d H:i:s'),
			], ['id' => $id]);

			$this->db->insert('approval_log', ['referensi_type' => 'transfer_stok', 'referensi_id' => $id, 'level' => 1, 'action' => 'approved', 'created_by' => get_user_data('username'), 'created_at' => date('Y-m-d H:i:s')]);
			set_message('Transfer stok diapprove.', 'success');
		}
		redirect('administrator/transfer_stok/view/' . $id);
	}

	public function submit($id)
	{
		$this->is_allowed('transfer_stok_update');
		$this->db->update('transfer_stok', ['status' => 'submitted'], ['id' => $id, 'status' => 'draft']);
		set_message('Transfer stok disubmit untuk approval.', 'success');
		redirect('administrator/transfer_stok/view/' . $id);
	}

	public function kirim($id)
	{
		$this->is_allowed('transfer_stok_update');
		$t = $this->model_transfer_stok->find($id);
		if ($t && in_array($t->status, ['approved', 'draft', 'submitted'])) {
			$bukti = $this->input->post('nomor_bukti_kirim') ?: $this->input->get('bukti_kirim');
			if (empty($bukti)) {
				$bukti = 'BK-' . date('Ymd-His');
			}
			$this->db->update('transfer_stok', [
				'status'            => 'shipped',
				'nomor_bukti_kirim' => $bukti,
				'updated_at'        => date('Y-m-d H:i:s'),
			], ['id' => $id]);
			set_message('Transfer stok berhasil dikirim dengan Bukti Pengiriman (' . $bukti . ').', 'success');
		} else {
			set_message('Transfer stok harus berstatus Approved untuk dikirim.', 'warning');
		}
		redirect('administrator/transfer_stok/view/' . $id);
	}

	public function terima($id)
	{
		$this->is_allowed('transfer_stok_update');
		$t = $this->model_transfer_stok->find($id);
		if ($t && $t->status == 'shipped') {
			$this->model_transfer_stok->proses_terima($id, get_user_data('username'));
			set_message('Transfer stok diterima. Stok kedua gudang telah diperbarui.', 'success');
		}
		redirect('administrator/transfer_stok/view/' . $id);
	}

	public function get_produk_by_gudang()
	{
		$gudang_id = $this->input->get('gudang_id');
		if (!$gudang_id) {
			$this->response(['success' => false, 'data' => []]);
			return;
		}
		
		$produks = $this->db->select('produk.id, produk.kode_produk, produk.nama_produk, stok.qty')
			->from('stok')
			->join('produk', 'produk.id = stok.produk_id')
			->where('stok.gudang_id', $gudang_id)
			->where('stok.qty >', 0)
			->get()->result();
			
		$this->response(['success' => true, 'data' => $produks]);
	}

	public function edit($id)
	{
		$this->is_allowed('transfer_stok_update');
		$this->data['transfer'] = $this->model_transfer_stok->find($id);
		if (!$this->data['transfer'] || $this->data['transfer']->status != 'draft') {
			set_message('Hanya data berstatus Draft yang bisa diedit.', 'error');
			redirect('administrator/transfer_stok');
		}
		$this->data['gudangs']    = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->data['produks']    = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->data['items']      = $this->model_transfer_stok->get_items($id);
		$this->template->title('Edit Transfer Stok');
		$this->render('backend/standart/administrator/transfer_stok/transfer_stok_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('transfer_stok_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$existing = $this->model_transfer_stok->find($id);
		if (!$existing || $existing->status != 'draft') {
			$this->response(['success' => false, 'message' => 'Hanya data berstatus Draft yang bisa diedit.']); return;
		}

		$data = [
			'catatan'           => $this->input->post('catatan'),
			'updated_at'        => date('Y-m-d H:i:s'),
			'updated_by'        => get_user_data('username'),
			'gudang_asal_id'    => $this->input->post('gudang_asal_id'),
			'gudang_tujuan_id'  => $this->input->post('gudang_tujuan_id'),
			'tanggal_transfer'  => $this->input->post('tanggal_transfer'),
			'tanggal_estimasi_tiba' => $this->input->post('tanggal_estimasi_tiba') ?: null,
		];

		$this->model_transfer_stok->change($id, $data);

		// Update items: delete old and insert new
		$this->db->delete('transfer_stok_item', ['transfer_id' => $id]);
		if ($this->input->post('items')) {
			foreach ($this->input->post('items') as $item) {
				if (empty($item['produk_id'])) continue;
				$this->db->insert('transfer_stok_item', [
					'transfer_id' => $id,
					'produk_id'   => $item['produk_id'],
					'qty'         => max(1, (int) round($item['qty'])),
					'batch_no'    => $item['batch_no'] ?? '',
					'catatan'     => $item['catatan'] ?? '',
				]);
			}
		}

		set_message('Transfer Stok berhasil diupdate.', 'success');
		$this->data['success']  = true;
		$this->data['redirect'] = base_url('administrator/transfer_stok');
		$this->response($this->data);
	}

	public function delete($id = null)
	{
		$this->is_allowed('transfer_stok_delete');
		$t = $this->model_transfer_stok->find($id);
		if ($t) {
			// If already received, reverse the stock movement before deleting
			if ($t->status == 'received') {
				$items = $this->model_transfer_stok->get_items($id);
				foreach ($items as $item) {
					// Restore stock to origin warehouse
					$stok_asal = $this->db->get_where('stok', ['produk_id' => $item->produk_id, 'gudang_id' => $t->gudang_asal_id])->row();
					if ($stok_asal) {
						$this->db->update('stok', ['qty' => $stok_asal->qty + $item->qty, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $stok_asal->id]);
					} else {
						$this->db->insert('stok', ['produk_id' => $item->produk_id, 'gudang_id' => $t->gudang_asal_id, 'qty' => $item->qty, 'qty_rusak' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
					}
					// Deduct stock from destination warehouse
					$stok_tujuan = $this->db->get_where('stok', ['produk_id' => $item->produk_id, 'gudang_id' => $t->gudang_tujuan_id])->row();
					if ($stok_tujuan) {
						$this->db->update('stok', ['qty' => max(0, $stok_tujuan->qty - $item->qty), 'updated_at' => date('Y-m-d H:i:s')], ['id' => $stok_tujuan->id]);
					}

					// Log rollback entries
					$user = get_user_data('username');
					$this->db->insert('stok_log', ['produk_id' => $item->produk_id, 'gudang_id' => $t->gudang_asal_id, 'jenis' => 'transfer_masuk', 'qty' => $item->qty, 'referensi_type' => 'transfer_stok', 'referensi_id' => $id, 'keterangan' => 'Pembatalan/Hapus Transfer Stok ' . $t->nomor_transfer, 'created_by' => $user, 'created_at' => date('Y-m-d H:i:s')]);
				}
			}

			$this->db->delete('transfer_stok_item', ['transfer_id' => $id]);
			$this->model_transfer_stok->remove($id);
			set_message('Transfer stok berhasil dihapus.', 'success');
		} else {
			set_message('Data transfer stok tidak ditemukan.', 'error');
		}
		redirect('administrator/transfer_stok');
	}

	

	

	public function cetak($id)
	{
		$this->is_allowed('transfer_stok_view');
		$this->data['transfer'] = $this->model_transfer_stok->join_available()->find($id);
		$this->data['items'] = $this->model_transfer_stok->get_items($id);
		
		$this->load->library('HtmlPdf');
		$this->htmlpdf->initialize(['orientation' => 'P', 'format' => 'A4', 'marges' => [5, 5, 5, 5]]);
		$content = $this->load->view('backend/standart/administrator/transfer_stok/transfer_stok_pdf', $this->data, true);
		
		$this->htmlpdf->writeHTML($content);
		$transfer = $this->data['transfer'];
		$doc_num = preg_replace('/[^a-zA-Z0-9_-]/', '_', $transfer->nomor_transfer ?? $id);
		$filename = 'transfer_stok_' . $doc_num . '.pdf';
		if (ob_get_length()) { ob_clean(); }
		$this->htmlpdf->Output($filename, 'D');
	}

	public function export()
	{
		$this->is_allowed('transfer_stok_export');
		$this->model_transfer_stok->export('transfer_stok', 'transfer_stok');
	}

	public function export_pdf()
	{
		$this->is_allowed('transfer_stok_export');
		$this->model_transfer_stok->pdf('transfer_stok', 'transfer_stok');
	}

}
