<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Dashboard extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_dashboard');
	}

	public function index()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			$this->render('backend/standart/blank', []);
			return;
		}

        $is_karyawan = $this->db->get_where('hr_karyawan', ['user_id' => get_user_data('id')])->row();
        if ($is_karyawan && !$this->aauth->is_admin()) {
            redirect('administrator/hr_profile', 'refresh');
        }

		$data['low_stocks']    = $this->model_dashboard->get_low_stocks(10);
		$data['recent_returs'] = $this->model_dashboard->get_recent_returs(5);
		$summary  = $this->model_dashboard->get_summary();
		$realtime = $this->model_dashboard->get_realtime_stats();
		
		$data = array_merge($data, $summary, $realtime);

		$this->render('backend/standart/dashboard', $data);
	}

	public function chart()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/', 'refresh');
		}

		$data = [];
		$this->render('backend/standart/chart', $data);
	}

	public function get_notifications()
	{
		$user_id = get_user_data('id');
		$is_karyawan_only = $this->db->get_where('hr_karyawan', ['user_id' => $user_id])->row();
		$is_manager = $this->aauth->is_admin() 
			|| $this->aauth->is_allowed('purchase_order_approve', false) 
			|| $this->aauth->is_allowed('hr_absensi_approve', false) 
			|| $this->aauth->is_allowed('transfer_stok_approve', false);

		$items = [];
		$total_count = 0;

		// Jika karyawan biasa: Tampilkan HANYA Notifikasi Pribadi miliknya sendiri
		if ($is_karyawan_only && !$is_manager) {
			$karyawan_id = $is_karyawan_only->id;

			if ($this->db->table_exists('hr_absensi')) {
				$my_absensi = $this->db->select('id, tanggal, waktu_masuk, status_kehadiran, alasan_terlambat, terlambat_menit, file_surat')
					->from('hr_absensi')
					->where('karyawan_id', $karyawan_id)
					->order_by('id', 'DESC')
					->limit(5)
					->get()->result();

				foreach ($my_absensi as $ab) {
					$st = $ab->status_kehadiran;
					$icon = 'fa-clock-o';
					$bg = 'text-blue';
					if (in_array($st, ['Telat', 'Telat (Lupa Absen)', 'Telat (Lupa Absen Pulang)', 'Hadir (Lupa Absen Pulang)', 'Lupa Absen Pulang'])) {
						$icon = 'fa-warning';
						$bg = 'text-yellow';
					} elseif (in_array($st, ['Izin', 'Sakit', 'Cuti', 'Izin Khusus', 'Sakit (Dengan Surat)', 'Sakit (Tanpa Surat)'])) {
						$icon = 'fa-file-text-o';
						$bg = 'text-purple';
					} elseif ($st == 'Alpha') {
						$icon = 'fa-times-circle';
						$bg = 'text-red';
					}

					$sub = 'Status: ' . $st;
					if ($ab->terlambat_menit > 0) {
						$sub .= ' (' . $ab->terlambat_menit . ' menit)';
					} elseif (!empty($ab->alasan_terlambat)) {
						$sub .= ' (' . $ab->alasan_terlambat . ')';
					}

					$items[] = [
						'type' => 'personal_absensi',
						'title' => 'Presensi Anda: ' . date('d/m/Y', strtotime($ab->tanggal)),
						'subtitle' => $sub,
						'url' => site_url('administrator/hr_profile'),
						'icon' => $icon,
						'icon_bg' => $bg,
						'date' => date('d/m', strtotime($ab->tanggal))
					];
				}
				$total_count = count($items);
			}

			return $this->response([
				'success' => true,
				'header_title' => 'Notifikasi Saya',
				'footer_url' => site_url('administrator/hr_profile'),
				'footer_label' => 'Lihat Profile & Presensi',
				'total_count' => $total_count,
				'items' => $items
			]);
		}

		// 1. Purchase Order Pending Approval (status = 'submitted')
		if ($this->db->table_exists('purchase_order')) {
			$threshold = (float) get_option('po_approval_threshold', 50000000);

			$pos = $this->db->select('po.id, po.nomor_po, po.created_at, po.total, po.kurs, s.nama_supplier')
				->from('purchase_order po')
				->join('supplier s', 's.id = po.supplier_id', 'left')
				->where('po.status', 'submitted')
				->order_by('po.id', 'DESC')
				->limit(10)
				->get()->result();

			foreach ($pos as $po) {
				$total_idr = (float)$po->total * (float)($po->kurs ?: 1);
				$is_high_value = ($total_idr >= $threshold);

				$items[] = [
					'type' => 'po',
					'title' => ($is_high_value ? '⚠️ PO High-Value: ' : 'PO Pending Approval: ') . $po->nomor_po,
					'subtitle' => ($po->nama_supplier ? $po->nama_supplier . ' - ' : '') . 'Rp ' . number_format($total_idr, 0, ',', '.') . ($is_high_value ? ' (Butuh Approval Manajer)' : ''),
					'url' => site_url('administrator/purchase_order/view/' . $po->id),
					'icon' => $is_high_value ? 'fa-shield' : 'fa-shopping-cart',
					'icon_bg' => $is_high_value ? 'text-red' : 'text-aqua',
					'date' => date('d/m H:i', strtotime($po->created_at))
				];
			}
			$total_count = $this->db->where('status', 'submitted')->count_all_results('purchase_order');
		}

		return $this->response([
			'success' => true,
			'header_title' => 'Approval PO Pending',
			'footer_url' => site_url('administrator/purchase_order'),
			'footer_label' => 'Lihat Kelola Purchase Order',
			'total_count' => $total_count,
			'items' => $items
		]);
	}
}

/* End of file Dashboard.php */
/* Location: ./modules/dashboard/controllers/backend/Dashboard.php */