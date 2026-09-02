<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crm extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_crm');
	}

	// ===== MODUL A: Customer Directory & Detail =====

	public function index($offset = 0)
	{
		$filter = $this->input->get('q');
		$result = $this->model_crm->get_customers($filter, $this->limit_page, $offset);

		$this->data['customers'] = $result['rows'];
		$this->data['customer_counts'] = $result['total'];
		$this->data['filter'] = $filter;

		$config = [
			'base_url'    => 'administrator/crm/index/',
			'total_rows'  => $result['total'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('CRM — Customer Directory');
		$this->render('backend/standart/administrator/crm/crm_customer_list', $this->data);
	}

	public function customer_detail()
	{
		$phone = $this->input->get('phone');
		if (empty($phone)) {
			set_message('Nomor telepon customer tidak valid.', 'error');
			redirect('administrator/crm');
		}

		$this->data['summary'] = $this->model_crm->get_customer_summary($phone);
		$this->data['transactions'] = $this->model_crm->get_customer_transactions($phone);
		$this->data['phone'] = $phone;

		$this->template->title('CRM — Detail Customer');
		$this->render('backend/standart/administrator/crm/crm_customer_detail', $this->data);
	}

	public function transaction_detail_ajax()
	{
		$table = $this->input->get('table');
		$order_id = $this->input->get('order_id');
		if (!$table || !$order_id) {
			echo json_encode(['success' => false]); return;
		}
		$items = $this->model_crm->get_transaction_items($table, $order_id);
		$timeline = $this->model_crm->get_transaction_followup_timeline($table, $order_id);
		echo json_encode(['success' => true, 'items' => $items, 'timeline' => $timeline]);
	}

	// ===== MODUL B: Journal Follow-Up =====

	public function journal_followup($offset = 0)
	{
		$filters = [
			'category_id' => $this->input->get('category_id'),
			'order_source' => $this->input->get('order_source'),
			'status_label' => $this->input->get('status_label'),
			'date_from' => $this->input->get('date_from'),
			'date_to' => $this->input->get('date_to'),
			'search' => $this->input->get('q'),
		];

		$result = $this->model_crm->get_journal_entries($filters, $this->limit_page, $offset);
		$this->data['entries'] = $result['rows'];
		$this->data['entry_counts'] = $result['total'];
		$this->data['categories'] = $this->model_crm->get_categories();
		$this->data['stats'] = $this->model_crm->get_journal_stats();
		$this->data['filters'] = $filters;

		$config = [
			'base_url'    => 'administrator/crm/journal_followup/',
			'total_rows'  => $result['total'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('CRM — Journal Follow-Up');
		$this->render('backend/standart/administrator/crm/crm_journal_followup', $this->data);
	}

	public function update_status_ajax()
	{
		$label_id = $this->input->post('label_id');
		$status = $this->input->post('status');
		$catatan = $this->input->post('catatan');

		if (!$label_id || !$status) {
			echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']); return;
		}

		$this->model_crm->update_label_status($label_id, $status, get_user_data('username'));
		if (!empty($catatan)) {
			$this->model_crm->add_note($label_id, $catatan, $status);
		}

		echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui']);
	}

	public function get_wa_link_ajax()
	{
		$label_id = $this->input->get('label_id');
		$label = $this->model_crm->get_label($label_id);
		if (!$label) {
			echo json_encode(['success' => false]); return;
		}

		$message = $this->model_crm->render_template($label->template_pesan_default, $label);
		$wa_link = $this->model_crm->generate_wa_link($label->customer_phone, $message);

		echo json_encode([
			'success' => true,
			'wa_link' => $wa_link,
			'message_preview' => $message,
			'phone' => $label->customer_phone,
			'nama' => $label->customer_nama,
		]);
	}

	public function send_broadcast_ajax()
	{
		$label_ids = $this->input->post('label_ids');
		if (empty($label_ids) || !is_array($label_ids)) {
			echo json_encode(['success' => false, 'message' => 'Pilih minimal 1 customer']); return;
		}

		$wa_links = [];
		foreach ($label_ids as $lid) {
			$label = $this->model_crm->get_label($lid);
			if (!$label) continue;
			// FB-10: Check duplicate
			if ($label->is_broadcast_sent) continue;

			$message = $this->model_crm->render_template($label->template_pesan_default, $label);
			$wa_link = $this->model_crm->generate_wa_link($label->customer_phone, $message);
			$this->model_crm->log_broadcast($lid, $message);
			$wa_links[] = ['link' => $wa_link, 'nama' => $label->customer_nama, 'phone' => $label->customer_phone];
		}

		echo json_encode(['success' => true, 'wa_links' => $wa_links, 'count' => count($wa_links)]);
	}

	// ===== Follow-Up Settings =====

	public function followup_setting()
	{
		$this->data['categories'] = $this->model_crm->get_categories(false);
		$this->template->title('CRM — Pengaturan Kelompok Follow-Up');
		$this->render('backend/standart/administrator/crm/crm_followup_setting', $this->data);
	}

	public function save_category_ajax()
	{
		$id = $this->input->post('id');
		$data = [
			'nama_kategori' => $this->input->post('nama_kategori'),
			'kode_kategori' => $this->input->post('kode_kategori'),
			'jumlah_hari' => (int)$this->input->post('jumlah_hari'),
			'template_pesan_default' => $this->input->post('template_pesan_default'),
			'is_active' => $this->input->post('is_active') ? 1 : 0,
			'urutan' => (int)$this->input->post('urutan'),
			'created_by' => get_user_data('username'),
		];

		$result = $this->model_crm->save_category($data, $id ?: null);
		echo json_encode(['success' => (bool)$result, 'message' => $result ? 'Berhasil disimpan' : 'Gagal menyimpan']);
	}

	public function delete_category_ajax()
	{
		$id = $this->input->post('id');
		$result = $this->model_crm->delete_category($id);
		echo json_encode(['success' => (bool)$result, 'message' => $result ? 'Berhasil dihapus' : 'Gagal: masih ada label follow-up aktif']);
	}

	// ===== Broadcast =====

	public function broadcast($offset = 0)
	{
		$this->data['categories'] = $this->model_crm->get_categories();
		$this->data['logs'] = $this->model_crm->get_broadcast_logs([], 50, 0);
		$this->data['stats'] = $this->model_crm->get_journal_stats();

		$this->template->title('CRM — Broadcast WhatsApp');
		$this->render('backend/standart/administrator/crm/crm_broadcast', $this->data);
	}

	public function get_broadcast_recipients_ajax()
	{
		$category_id = $this->input->get('category_id');
		$order_source = $this->input->get('order_source');

		$filters = [
			'category_id' => $category_id,
			'order_source' => $order_source,
			'status_label' => 'Pending',
		];
		$result = $this->model_crm->get_journal_entries($filters, 200, 0);

		// Filter only non-broadcast-sent (FB-10)
		$recipients = [];
		foreach ($result['rows'] as $r) {
			if (!$r->is_broadcast_sent && !empty($r->customer_phone)) {
				$recipients[] = $r;
			}
		}

		echo json_encode(['success' => true, 'recipients' => $recipients, 'count' => count($recipients)]);
	}

	// ===== CRON Endpoint =====

	public function generate_labels_cron()
	{
		$count = $this->model_crm->generate_labels_for_new_orders();
		echo json_encode(['success' => true, 'generated' => $count]);
	}

	// ===== DASHBOARD CRM ANALYTICS (Fase 5) =====

	public function dashboard()
	{
		$this->data['kpis'] = $this->model_crm->get_dashboard_kpis();
		$this->data['channels'] = $this->model_crm->get_channel_distribution();
		$this->data['trend'] = $this->model_crm->get_followup_trend_30days();
		$this->data['category_status'] = $this->model_crm->get_category_status_breakdown();
		$this->data['top_repeat'] = $this->model_crm->get_top_repeat_customers(5);
		$this->data['urgent_alerts'] = $this->model_crm->get_urgent_alerts();
		$this->data['recent_activities'] = $this->model_crm->get_recent_activities(10);

		$this->template->title('CRM — Dashboard Analytics');
		$this->render('backend/standart/administrator/crm/crm_dashboard', $this->data);
	}

	// ===== EXPORT & MANUAL CRON SYNC =====

	public function export_customers()
	{
		$filter = $this->input->get('q');
		$result = $this->model_crm->get_customers($filter, 10000, 0);

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=CRM_Customer_Directory_' . date('Ymd_His') . '.csv');
		$output = fopen('php://output', 'w');
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
		fputcsv($output, ['No', 'Nama Customer', 'No. HP', 'Sumber Penjualan', 'Total Order', 'Order Terakhir', 'Pending Followup']);

		$no = 1;
		foreach ($result['rows'] as $c) {
			fputcsv($output, [
				$no++,
				$c->customer_nama,
				$c->customer_phone,
				$c->sumber_penjualan,
				$c->total_orders,
				$c->last_order_date,
				$c->pending_followups
			]);
		}
		fclose($output);
		exit;
	}

	public function export_journal()
	{
		$filters = [
			'category_id' => $this->input->get('category_id'),
			'order_source' => $this->input->get('order_source'),
			'status_label' => $this->input->get('status_label'),
			'date_from' => $this->input->get('date_from'),
			'date_to' => $this->input->get('date_to'),
			'search' => $this->input->get('q'),
			'show_future' => 1
		];

		$result = $this->model_crm->get_journal_entries($filters, 10000, 0);

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=CRM_Journal_FollowUp_' . date('Ymd_His') . '.csv');
		$output = fopen('php://output', 'w');
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
		fputcsv($output, ['No', 'Customer', 'No. HP', 'No. Pesanan', 'Sumber', 'Kategori H+', 'Tgl Kirim', 'Target Label', 'Status', 'Broadcast Terkirim']);

		$no = 1;
		foreach ($result['rows'] as $e) {
			fputcsv($output, [
				$no++,
				$e->customer_nama,
				$e->customer_phone,
				$e->no_pesanan,
				$e->order_source,
				$e->nama_kategori,
				$e->tanggal_pengiriman,
				$e->tanggal_label_muncul,
				$e->status_label,
				$e->is_broadcast_sent ? 'Ya' : 'Belum'
			]);
		}
		fclose($output);
		exit;
	}

	public function trigger_cron_manual()
	{
		$count = $this->model_crm->generate_labels_for_new_orders();
		echo json_encode(['success' => true, 'message' => "Sinkronisasi selesai! $count label follow-up baru berhasil dibuat."]);
	}

	public function get_notes_ajax()
	{
		$label_id = $this->input->get('label_id');
		$notes = $this->model_crm->get_notes($label_id);
		echo json_encode(['success' => true, 'notes' => $notes]);
	}
}
