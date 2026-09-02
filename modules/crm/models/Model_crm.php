<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_crm extends CI_Model
{
	/**
	 * Get consolidated customer list from sales_order + penjualan_marketplace
	 * Grouped by phone number (primary key for customer identity)
	 */
	public function get_customers($filter = '', $limit = 25, $offset = 0)
	{
		$sql = "SELECT 
			customer_phone,
			customer_nama,
			COUNT(DISTINCT CONCAT(order_table, '-', order_id)) as total_orders,
			GROUP_CONCAT(DISTINCT order_source SEPARATOR ', ') as sumber_penjualan,
			MAX(tanggal_pengiriman) as last_order_date,
			SUM(CASE WHEN status_label = 'Pending' THEN 1 ELSE 0 END) as pending_followups
		FROM crm_transaction_followup_labels
		WHERE customer_phone IS NOT NULL AND customer_phone != ''";

		if (!empty($filter)) {
			$filter = $this->db->escape_like_str($filter);
			$sql .= " AND (customer_nama LIKE '%{$filter}%' OR customer_phone LIKE '%{$filter}%')";
		}

		$sql .= " GROUP BY customer_phone, customer_nama ORDER BY last_order_date DESC";
		
		$count_sql = "SELECT COUNT(*) as cnt FROM ({$sql}) as sub";
		$count = $this->db->query($count_sql)->row()->cnt;

		$sql .= " LIMIT {$limit} OFFSET {$offset}";
		$rows = $this->db->query($sql)->result();

		return ['rows' => $rows, 'total' => $count];
	}

	/**
	 * Get customer summary stats (FA-04)
	 */
	public function get_customer_summary($phone)
	{
		$phone = $this->db->escape_str($phone);

		// Get total spend from SO
		$so_spend = $this->db->query("SELECT COALESCE(SUM(total),0) as t FROM sales_order WHERE no_telepon = '{$phone}'")->row()->t;
		
		// Get total spend from marketplace
		$mp_spend = $this->db->query("SELECT COALESCE(SUM(total),0) as t FROM penjualan_marketplace WHERE no_telepon = '{$phone}'")->row()->t;

		// Get order counts
		$so_count = $this->db->query("SELECT COUNT(*) as c FROM sales_order WHERE no_telepon = '{$phone}'")->row()->c;
		$mp_count = $this->db->query("SELECT COUNT(*) as c FROM penjualan_marketplace WHERE no_telepon = '{$phone}'")->row()->c;

		// Get sources breakdown
		$sources = $this->db->query("SELECT DISTINCT order_source FROM crm_transaction_followup_labels WHERE customer_phone = '{$phone}'")->result();
		$source_list = array_map(function($s) { return $s->order_source; }, $sources);

		// Get first and last order dates
		$dates = $this->db->query("SELECT 
			MIN(tanggal_pengiriman) as first_order, 
			MAX(tanggal_pengiriman) as last_order 
			FROM crm_transaction_followup_labels WHERE customer_phone = '{$phone}'")->row();

		// Customer name
		$name_row = $this->db->query("SELECT customer_nama FROM crm_transaction_followup_labels WHERE customer_phone = '{$phone}' ORDER BY id DESC LIMIT 1")->row();

		return [
			'nama' => $name_row ? $name_row->customer_nama : '-',
			'phone' => $phone,
			'total_spend_toko' => (float)$so_spend,
			'total_spend_online' => (float)$mp_spend,
			'total_spend' => (float)$so_spend + (float)$mp_spend,
			'order_count_toko' => (int)$so_count,
			'order_count_online' => (int)$mp_count,
			'total_orders' => (int)$so_count + (int)$mp_count,
			'sources' => $source_list,
			'first_order' => $dates ? $dates->first_order : null,
			'last_order' => $dates ? $dates->last_order : null,
		];
	}

	/**
	 * Get transaction history for a customer (FA-01, FA-02)
	 * UNION of sales_order + penjualan_marketplace
	 */
	public function get_customer_transactions($phone)
	{
		$phone = $this->db->escape_str($phone);

		$sql = "
			(SELECT 
				'sales_order' as tbl, so.id, so.nomor_so, so.no_pesanan, so.tanggal_so as tanggal,
				COALESCE(so.tanggal_kirim, so.tanggal_so) as tanggal_kirim,
				'Toko' as sumber_penjualan,
				so.status, so.status_pesanan, so.nomor_resi, so.kurir_ekspedisi,
				so.nama_penerima, so.no_telepon, so.alamat_pengiriman,
				so.total, so.nama_produk_marketplace as produk_ringkasan
			FROM sales_order so 
			WHERE so.no_telepon = '{$phone}')
			UNION ALL
			(SELECT 
				'penjualan_marketplace' as tbl, pm.id, pm.nomor_so, pm.no_pesanan, pm.tanggal_so as tanggal,
				pm.tanggal_so as tanggal_kirim,
				CASE 
					WHEN LOWER(pm.source) LIKE '%tokopedia%' THEN 'Tokopedia'
					WHEN LOWER(pm.source) LIKE '%tiktok%' THEN 'TikTok Shop'
					ELSE 'Shopee'
				END as sumber_penjualan,
				pm.status, pm.status_pesanan, pm.nomor_resi, pm.kurir_ekspedisi,
				pm.nama_penerima, pm.no_telepon, pm.alamat_pengiriman,
				pm.total, pm.nama_produk_marketplace as produk_ringkasan
			FROM penjualan_marketplace pm 
			WHERE pm.no_telepon = '{$phone}')
			ORDER BY tanggal DESC";

		return $this->db->query($sql)->result();
	}

	/**
	 * Get transaction items (FA-03)
	 */
	public function get_transaction_items($table, $order_id)
	{
		if ($table === 'sales_order') {
			return $this->db->query("SELECT soi.*, p.nama_produk, p.kode_produk 
				FROM sales_order_item soi 
				LEFT JOIN produk p ON soi.produk_id = p.id 
				WHERE soi.so_id = ?", [$order_id])->result();
		} else {
			return $this->db->query("SELECT pmi.*, p.nama_produk, p.kode_produk 
				FROM penjualan_marketplace_item pmi 
				LEFT JOIN produk p ON pmi.produk_id = p.id 
				WHERE pmi.penjualan_marketplace_id = ?", [$order_id])->result();
		}
	}

	/**
	 * Get follow-up labels for a specific transaction (FA-05 timeline)
	 */
	public function get_transaction_followup_timeline($table, $order_id)
	{
		return $this->db->query("
			SELECT tfl.*, fc.nama_kategori, fc.kode_kategori, fc.jumlah_hari,
				(SELECT GROUP_CONCAT(fn.catatan SEPARATOR ' | ') FROM crm_followup_notes fn WHERE fn.followup_label_id = tfl.id) as notes,
				(SELECT COUNT(*) FROM crm_broadcast_logs bl WHERE bl.followup_label_id = tfl.id) as broadcast_count
			FROM crm_transaction_followup_labels tfl
			JOIN crm_followup_categories fc ON tfl.followup_category_id = fc.id
			WHERE tfl.order_table = ? AND tfl.order_id = ?
			ORDER BY fc.jumlah_hari ASC", [$table, $order_id])->result();
	}

	// ===== JOURNAL FOLLOW-UP (Modul B) =====

	/**
	 * Get follow-up journal entries with filters (FB-03)
	 */
	public function get_journal_entries($filters = [], $limit = 25, $offset = 0)
	{
		$this->db->select('tfl.*, fc.nama_kategori, fc.kode_kategori, fc.jumlah_hari');
		$this->db->from('crm_transaction_followup_labels tfl');
		$this->db->join('crm_followup_categories fc', 'tfl.followup_category_id = fc.id');

		if (!empty($filters['category_id'])) {
			$this->db->where('tfl.followup_category_id', $filters['category_id']);
		}
		if (!empty($filters['order_source'])) {
			$this->db->where('tfl.order_source', $filters['order_source']);
		}
		if (!empty($filters['status_label'])) {
			$this->db->where('tfl.status_label', $filters['status_label']);
		}
		if (!empty($filters['date_from'])) {
			$this->db->where('tfl.tanggal_pengiriman >=', $filters['date_from']);
		}
		if (!empty($filters['date_to'])) {
			$this->db->where('tfl.tanggal_pengiriman <=', $filters['date_to']);
		}
		if (!empty($filters['search'])) {
			$this->db->group_start();
			$this->db->like('tfl.customer_nama', $filters['search']);
			$this->db->or_like('tfl.customer_phone', $filters['search']);
			$this->db->or_like('tfl.no_pesanan', $filters['search']);
			$this->db->group_end();
		}
		// Only show labels whose target date has arrived
		if (empty($filters['show_future'])) {
			$this->db->where('tfl.tanggal_label_muncul <=', date('Y-m-d'));
		}

		$count = $this->db->count_all_results('', false);

		$this->db->order_by('tfl.tanggal_label_muncul', 'DESC');
		$this->db->limit($limit, $offset);
		$rows = $this->db->get()->result();

		return ['rows' => $rows, 'total' => $count];
	}

	/**
	 * Get all active follow-up categories
	 */
	public function get_categories($active_only = true)
	{
		$this->db->order_by('urutan', 'ASC');
		if ($active_only) $this->db->where('is_active', 1);
		return $this->db->get('crm_followup_categories')->result();
	}

	public function get_category($id)
	{
		return $this->db->get_where('crm_followup_categories', ['id' => $id])->row();
	}

	public function save_category($data, $id = null)
	{
		if ($id) {
			$data['updated_at'] = date('Y-m-d H:i:s');
			return $this->db->where('id', $id)->update('crm_followup_categories', $data);
		} else {
			$data['created_at'] = date('Y-m-d H:i:s');
			return $this->db->insert('crm_followup_categories', $data);
		}
	}

	public function delete_category($id)
	{
		// Check if labels exist using this category
		$count = $this->db->where('followup_category_id', $id)->count_all_results('crm_transaction_followup_labels');
		if ($count > 0) {
			return false; // Cannot delete if labels exist
		}
		return $this->db->delete('crm_followup_categories', ['id' => $id]);
	}

	/**
	 * Update follow-up label status (FB-07)
	 */
	public function update_label_status($label_id, $status, $handled_by = null)
	{
		$data = [
			'status_label' => $status,
			'updated_at' => date('Y-m-d H:i:s'),
		];
		if ($handled_by) $data['handled_by'] = $handled_by;
		return $this->db->where('id', $label_id)->update('crm_transaction_followup_labels', $data);
	}

	/**
	 * Add follow-up note (FB-07)
	 */
	public function add_note($label_id, $catatan, $status_manual = null)
	{
		return $this->db->insert('crm_followup_notes', [
			'followup_label_id' => $label_id,
			'username' => get_user_data('username') ?: 'system',
			'catatan' => $catatan,
			'status_manual' => $status_manual,
			'created_at' => date('Y-m-d H:i:s'),
		]);
	}

	public function get_notes($label_id)
	{
		return $this->db->where('followup_label_id', $label_id)->order_by('created_at', 'DESC')->get('crm_followup_notes')->result();
	}

	/**
	 * Render WA template with variables (FB-05)
	 */
	public function render_template($template, $label)
	{
		// Get product info
		$produk = '-';
		if ($label->order_table === 'sales_order') {
			$items = $this->db->query("SELECT COALESCE(p.nama_produk, soi.catatan) as nama FROM sales_order_item soi LEFT JOIN produk p ON soi.produk_id = p.id WHERE soi.so_id = ? LIMIT 3", [$label->order_id])->result();
		} else {
			$items = $this->db->query("SELECT COALESCE(p.nama_produk, pmi.catatan) as nama FROM penjualan_marketplace_item pmi LEFT JOIN produk p ON pmi.produk_id = p.id WHERE pmi.penjualan_marketplace_id = ? LIMIT 3", [$label->order_id])->result();
		}
		if (!empty($items)) {
			$produk = implode(', ', array_map(function($i) { return $i->nama; }, $items));
		}

		// Get kurir info
		$kurir = '-';
		if ($label->order_table === 'sales_order') {
			$order = $this->db->get_where('sales_order', ['id' => $label->order_id])->row();
		} else {
			$order = $this->db->get_where('penjualan_marketplace', ['id' => $label->order_id])->row();
		}
		if ($order) {
			$kurir = $order->kurir_ekspedisi ?: ($order->opsi_pengiriman ?? '-');
		}

		$vars = [
			'{{nama_customer}}' => $label->customer_nama ?: '-',
			'{{no_pesanan}}' => $label->no_pesanan ?: '-',
			'{{nomor_resi}}' => $label->nomor_resi ?: '-',
			'{{sumber_penjualan}}' => $label->order_source ?: '-',
			'{{produk}}' => $produk,
			'{{tanggal_kirim}}' => $label->tanggal_pengiriman ? date('d/m/Y', strtotime($label->tanggal_pengiriman)) : '-',
			'{{kurir}}' => $kurir,
		];

		return str_replace(array_keys($vars), array_values($vars), $template);
	}

	/**
	 * Generate WA direct link
	 */
	public function generate_wa_link($phone, $message)
	{
		// Normalize phone: 08xx → 628xx
		$phone = preg_replace('/^0/', '62', $phone);
		$phone = preg_replace('/[^0-9]/', '', $phone);
		if (substr($phone, 0, 2) !== '62') $phone = '62' . $phone;
		return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
	}

	/**
	 * Log broadcast (FB-06)
	 */
	public function log_broadcast($label_id, $pesan_final, $metode = 'wa_direct_link')
	{
		$this->db->insert('crm_broadcast_logs', [
			'followup_label_id' => $label_id,
			'dikirim_oleh' => get_user_data('username') ?: 'system',
			'isi_pesan_final' => $pesan_final,
			'waktu_kirim' => date('Y-m-d H:i:s'),
			'status_kirim' => 'Terkirim',
			'metode_kirim' => $metode,
		]);
		// Mark as broadcast sent (FB-10: prevent duplicate)
		$this->db->where('id', $label_id)->update('crm_transaction_followup_labels', [
			'is_broadcast_sent' => 1,
			'status_label' => 'Sudah Dikirim',
			'handled_by' => get_user_data('username') ?: 'system',
		]);
	}

	public function get_broadcast_logs($filters = [], $limit = 25, $offset = 0)
	{
		$this->db->select('bl.*, tfl.customer_nama, tfl.customer_phone, tfl.no_pesanan, tfl.order_source, fc.nama_kategori');
		$this->db->from('crm_broadcast_logs bl');
		$this->db->join('crm_transaction_followup_labels tfl', 'bl.followup_label_id = tfl.id');
		$this->db->join('crm_followup_categories fc', 'tfl.followup_category_id = fc.id');
		$this->db->order_by('bl.waktu_kirim', 'DESC');
		$this->db->limit($limit, $offset);
		return $this->db->get()->result();
	}

	/**
	 * CRON: Generate follow-up labels for new shipped orders (FB-01)
	 */
	public function generate_labels_for_new_orders()
	{
		$categories = $this->get_categories(true);
		$generated = 0;

		// Process sales_order
		$so_rows = $this->db->query("SELECT so.*, p.nama_pelanggan, p.telepon, p.no_telp 
			FROM sales_order so LEFT JOIN pelanggan p ON so.pelanggan_id = p.id 
			WHERE so.status IN ('shipped','delivered','completed','processing')
			OR so.status_pesanan IN ('Siap Kirim','Dikirim','Selesai')")->result();

		foreach ($so_rows as $so) {
			$ship_date = $so->tanggal_kirim ?: $so->tanggal_so ?: substr($so->created_at, 0, 10);
			$phone = $so->no_telepon ?: $so->telepon ?: $so->no_telp ?: '';
			$nama = $so->nama_penerima ?: $so->nama_pelanggan ?: '-';

			foreach ($categories as $cat) {
				$target = date('Y-m-d', strtotime($ship_date . ' + ' . $cat->jumlah_hari . ' days'));
				$exists = $this->db->where(['order_table' => 'sales_order', 'order_id' => $so->id, 'followup_category_id' => $cat->id])
					->count_all_results('crm_transaction_followup_labels');
				if ($exists == 0) {
					$this->db->insert('crm_transaction_followup_labels', [
						'order_source' => 'Toko',
						'order_id' => $so->id,
						'order_table' => 'sales_order',
						'customer_nama' => $nama,
						'customer_phone' => $phone,
						'customer_pelanggan_id' => $so->pelanggan_id,
						'no_pesanan' => $so->no_pesanan ?: $so->nomor_so,
						'nomor_resi' => $so->nomor_resi,
						'tanggal_pengiriman' => $ship_date,
						'followup_category_id' => $cat->id,
						'tanggal_label_muncul' => $target,
					]);
					$generated++;
				}
			}
		}

		// Process penjualan_marketplace
		$mp_rows = $this->db->query("SELECT * FROM penjualan_marketplace 
			WHERE status IN ('shipped','delivered','completed','processing','confirmed','packing')
			OR status_pesanan IN ('Siap Kirim','Dikirim','Selesai')")->result();

		foreach ($mp_rows as $mp) {
			$ship_date = $mp->tanggal_so ?: substr($mp->created_at, 0, 10);
			$phone = $mp->no_telepon ?: '';
			$nama = $mp->nama_penerima ?: '-';
			$src = strtolower($mp->source ?: '');
			$source = 'Shopee';
			if (strpos($src, 'tokopedia') !== false) $source = 'Tokopedia';
			elseif (strpos($src, 'tiktok') !== false) $source = 'TikTok Shop';

			foreach ($categories as $cat) {
				$target = date('Y-m-d', strtotime($ship_date . ' + ' . $cat->jumlah_hari . ' days'));
				$exists = $this->db->where(['order_table' => 'penjualan_marketplace', 'order_id' => $mp->id, 'followup_category_id' => $cat->id])
					->count_all_results('crm_transaction_followup_labels');
				if ($exists == 0) {
					$this->db->insert('crm_transaction_followup_labels', [
						'order_source' => $source,
						'order_id' => $mp->id,
						'order_table' => 'penjualan_marketplace',
						'customer_nama' => $nama,
						'customer_phone' => $phone,
						'customer_pelanggan_id' => $mp->pelanggan_id,
						'no_pesanan' => $mp->no_pesanan ?: $mp->nomor_so,
						'nomor_resi' => $mp->nomor_resi,
						'tanggal_pengiriman' => $ship_date,
						'followup_category_id' => $cat->id,
						'tanggal_label_muncul' => $target,
					]);
					$generated++;
				}
			}
		}

		return $generated;
	}

	/**
	 * Get journal stats for badges
	 */
	public function get_journal_stats()
	{
		$today = date('Y-m-d');
		return [
			'total_pending' => $this->db->where(['status_label' => 'Pending', 'tanggal_label_muncul <=' => $today])->count_all_results('crm_transaction_followup_labels'),
			'overdue' => $this->db->where(['status_label' => 'Pending', 'tanggal_label_muncul <' => $today])->count_all_results('crm_transaction_followup_labels'),
			'today' => $this->db->where(['status_label' => 'Pending', 'tanggal_label_muncul' => $today])->count_all_results('crm_transaction_followup_labels'),
		];
	}

	public function get_label($id)
	{
		return $this->db->select('tfl.*, fc.nama_kategori, fc.kode_kategori, fc.template_pesan_default, fc.jumlah_hari')
			->from('crm_transaction_followup_labels tfl')
			->join('crm_followup_categories fc', 'tfl.followup_category_id = fc.id')
			->where('tfl.id', $id)
			->get()->row();
	}

	// ===== DASHBOARD CRM ANALYTICS (Fase 5) =====

	public function get_dashboard_kpis()
	{
		$today = date('Y-m-d');
		
		// Total customers
		$total_cust = $this->db->query("SELECT COUNT(DISTINCT customer_phone) as cnt FROM crm_transaction_followup_labels WHERE customer_phone IS NOT NULL AND customer_phone != ''")->row()->cnt;
		
		// Total spend
		$so_spend = $this->db->query("SELECT COALESCE(SUM(total),0) as t FROM sales_order")->row()->t;
		$mp_spend = $this->db->query("SELECT COALESCE(SUM(total),0) as t FROM penjualan_marketplace")->row()->t;
		$total_spend = (float)$so_spend + (float)$mp_spend;

		// Repeat order customers (more than 1 order)
		$repeat_cust = $this->db->query("SELECT COUNT(*) as cnt FROM (
			SELECT customer_phone FROM crm_transaction_followup_labels 
			WHERE customer_phone IS NOT NULL AND customer_phone != '' 
			GROUP BY customer_phone HAVING COUNT(DISTINCT CONCAT(order_table, '-', order_id)) > 1
		) sub")->row()->cnt;
		
		$repeat_rate = $total_cust > 0 ? round(($repeat_cust / $total_cust) * 100, 1) : 0;

		// Pending today & overdue
		$pending_today = $this->db->query("SELECT COUNT(*) as cnt FROM crm_transaction_followup_labels WHERE status_label = 'Pending' AND tanggal_label_muncul = '$today'")->row()->cnt;
		$overdue = $this->db->query("SELECT COUNT(*) as cnt FROM crm_transaction_followup_labels WHERE status_label = 'Pending' AND tanggal_label_muncul < '$today'")->row()->cnt;

		// Broadcast sent count
		$total_broadcast = $this->db->query("SELECT COUNT(*) as cnt FROM crm_broadcast_logs")->row()->cnt;

		return [
			'total_customers' => (int)$total_cust,
			'total_spend' => $total_spend,
			'repeat_customers' => (int)$repeat_cust,
			'repeat_rate' => $repeat_rate,
			'pending_today' => (int)$pending_today,
			'overdue' => (int)$overdue,
			'total_broadcast' => (int)$total_broadcast,
		];
	}

	public function get_channel_distribution()
	{
		$rows = $this->db->query("SELECT order_source, COUNT(DISTINCT CONCAT(order_table, '-', order_id)) as cnt 
			FROM crm_transaction_followup_labels 
			GROUP BY order_source")->result();

		$data = ['Toko' => 0, 'Shopee' => 0, 'Tokopedia' => 0, 'TikTok Shop' => 0];
		foreach ($rows as $r) {
			if (isset($data[$r->order_source])) {
				$data[$r->order_source] = (int)$r->cnt;
			}
		}
		return $data;
	}

	public function get_followup_trend_30days()
	{
		$dates = [];
		$broadcast_counts = [];
		$labels_counts = [];

		for ($i = 14; $i >= 0; $i--) {
			$d = date('Y-m-d', strtotime("-$i days"));
			$dates[] = date('d M', strtotime($d));
			
			$bc = $this->db->query("SELECT COUNT(*) as cnt FROM crm_broadcast_logs WHERE DATE(waktu_kirim) = '$d'")->row()->cnt;
			$lbl = $this->db->query("SELECT COUNT(*) as cnt FROM crm_transaction_followup_labels WHERE tanggal_label_muncul = '$d'")->row()->cnt;
			
			$broadcast_counts[] = (int)$bc;
			$labels_counts[] = (int)$lbl;
		}

		return [
			'dates' => $dates,
			'broadcast' => $broadcast_counts,
			'labels' => $labels_counts,
		];
	}

	public function get_category_status_breakdown()
	{
		$cats = $this->get_categories(true);
		$result = [];

		foreach ($cats as $c) {
			$pending = $this->db->query("SELECT COUNT(*) as cnt FROM crm_transaction_followup_labels WHERE followup_category_id = {$c->id} AND status_label = 'Pending'")->row()->cnt;
			$dikirim = $this->db->query("SELECT COUNT(*) as cnt FROM crm_transaction_followup_labels WHERE followup_category_id = {$c->id} AND status_label IN ('Sudah Dikirim','Direspon','Sudah Konfirmasi Diterima')")->row()->cnt;
			$selesai = $this->db->query("SELECT COUNT(*) as cnt FROM crm_transaction_followup_labels WHERE followup_category_id = {$c->id} AND status_label IN ('Selesai','Tawarkan Repeat Order')")->row()->cnt;

			$result[] = [
				'kategori' => $c->nama_kategori,
				'pending' => (int)$pending,
				'dikirim' => (int)$dikirim,
				'selesai' => (int)$selesai,
			];
		}
		return $result;
	}

	public function get_top_repeat_customers($limit = 5)
	{
		$sql = "SELECT 
			customer_nama,
			customer_phone,
			COUNT(DISTINCT CONCAT(order_table, '-', order_id)) as total_orders,
			GROUP_CONCAT(DISTINCT order_source SEPARATOR ', ') as sources,
			MAX(tanggal_pengiriman) as last_order
		FROM crm_transaction_followup_labels 
		WHERE customer_phone IS NOT NULL AND customer_phone != ''
		GROUP BY customer_phone, customer_nama
		ORDER BY total_orders DESC, last_order DESC
		LIMIT $limit";

		return $this->db->query($sql)->result();
	}

	public function get_urgent_alerts()
	{
		$today = date('Y-m-d');
		$sql = "SELECT tfl.*, fc.nama_kategori 
			FROM crm_transaction_followup_labels tfl
			JOIN crm_followup_categories fc ON tfl.followup_category_id = fc.id
			WHERE tfl.status_label = 'Komplain' 
			OR (tfl.status_label = 'Pending' AND tfl.tanggal_label_muncul <= DATE_SUB('$today', INTERVAL 3 DAY))
			ORDER BY tfl.tanggal_label_muncul ASC
			LIMIT 10";

		return $this->db->query($sql)->result();
	}

	public function get_recent_activities($limit = 10)
	{
		$sql = "SELECT 
			bl.waktu_kirim as waktu,
			bl.dikirim_oleh as user,
			tfl.customer_nama,
			tfl.customer_phone,
			fc.nama_kategori,
			'broadcast' as type,
			bl.isi_pesan_final as detail
		FROM crm_broadcast_logs bl
		JOIN crm_transaction_followup_labels tfl ON bl.followup_label_id = tfl.id
		JOIN crm_followup_categories fc ON tfl.followup_category_id = fc.id
		UNION ALL
		SELECT 
			fn.created_at as waktu,
			fn.username as user,
			tfl.customer_nama,
			tfl.customer_phone,
			fc.nama_kategori,
			'note' as type,
			fn.catatan as detail
		FROM crm_followup_notes fn
		JOIN crm_transaction_followup_labels tfl ON fn.followup_label_id = tfl.id
		JOIN crm_followup_categories fc ON tfl.followup_category_id = fc.id
		ORDER BY waktu DESC
		LIMIT $limit";

		return $this->db->query($sql)->result();
	}
}
