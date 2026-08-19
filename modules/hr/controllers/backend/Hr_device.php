<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_device extends Admin
{
    public function __construct()
    {
        parent::__construct();
    }

    // Halaman daftar semua perangkat (Admin only)
    public function index()
    {
        $this->is_allowed('hr_laporan_list');

        $q = $this->input->get('q');
        $status_filter = $this->input->get('status') ?: 'semua';

        $this->db->select('d.*, u.username, u.email,
            (SELECT COUNT(*) FROM hr_karyawan k WHERE k.user_id = d.user_id LIMIT 1) as karyawan_ada,
            (SELECT COUNT(*) FROM hr_absensi a
                INNER JOIN hr_karyawan k2 ON k2.id = a.karyawan_id
                WHERE k2.user_id = d.user_id
                AND a.waktu_masuk IS NOT NULL
            ) as total_absen_real');
        $this->db->from('hr_device_fingerprint d');
        $this->db->join('aauth_users u', 'u.id = d.user_id', 'left');

        if ($q) {
            $this->db->group_start();
            $this->db->like('d.full_name', $q);
            $this->db->or_like('u.username', $q);
            $this->db->or_like('d.device_id', $q);
            $this->db->or_like('d.gpu_info', $q);
            $this->db->or_like('d.os_platform', $q);
            $this->db->group_end();
        }

        if ($status_filter !== 'semua') {
            $this->db->where('d.status', $status_filter);
        }

        $this->db->order_by('d.last_seen', 'DESC');
        $this->data['devices']        = $this->db->get()->result();
        $this->data['q']              = $q;
        $this->data['status_filter']  = $status_filter;

        // Statistik ringkasan
        $this->data['total_device']   = $this->db->count_all('hr_device_fingerprint');
        $this->data['total_aktif']    = $this->db->get_where('hr_device_fingerprint', ['status' => 'aktif'])->num_rows();
        $this->data['total_dicabut']  = $this->db->get_where('hr_device_fingerprint', ['status' => 'dicabut'])->num_rows();

        $this->db->select('COUNT(DISTINCT user_id) as total');
        $this->data['total_user_unik'] = $this->db->get('hr_device_fingerprint')->row()->total;

        $this->template->title('Manajemen Perangkat Karyawan');
        $this->render('backend/standart/administrator/hr_device/hr_device_list', $this->data);
    }

    // Cabut akses perangkat
    public function revoke($id)
    {
        $this->is_allowed('hr_laporan_list');
        $this->db->update('hr_device_fingerprint', ['status' => 'dicabut'], ['id' => $id]);
        set_message('Akses perangkat berhasil dicabut.', 'success');
        redirect('administrator/hr_device');
    }

    // Aktifkan kembali perangkat
    public function activate($id)
    {
        $this->is_allowed('hr_laporan_list');
        $this->db->update('hr_device_fingerprint', ['status' => 'aktif'], ['id' => $id]);
        set_message('Perangkat berhasil diaktifkan kembali.', 'success');
        redirect('administrator/hr_device');
    }

    // Hapus data perangkat
    public function delete($id)
    {
        $this->is_allowed('hr_laporan_list');
        $this->db->delete('hr_device_fingerprint', ['id' => $id]);
        set_message('Data perangkat berhasil dihapus.', 'success');
        redirect('administrator/hr_device');
    }

    // API: simpan/update fingerprint dari halaman absen
    public function save_fingerprint()
    {
        // Pastikan hanya bisa diakses via AJAX POST
        if ($this->input->method() !== 'post') {
            echo json_encode(['success' => false, 'message' => 'Method tidak valid.']);
            return;
        }

        $user_id   = $this->aauth->get_user_id();
        $user_data = get_user_data();

        if (!$user_id || !$user_data) {
            echo json_encode(['success' => false, 'message' => 'Sesi tidak valid.']);
            return;
        }

        $username  = $user_data->username ?? '';
        $full_name = $user_data->full_name ?? $username;

        $device_id    = $this->input->post('device_id');
        $os_platform  = substr($this->input->post('os_platform') ?? '', 0, 200);
        $screen_info  = substr($this->input->post('screen_info') ?? '', 0, 100);
        $gpu_info     = substr($this->input->post('gpu_info') ?? '', 0, 300);
        $cpu_cores    = (int) ($this->input->post('cpu_cores') ?? 0);
        $ram_gb       = (float) ($this->input->post('ram_gb') ?? 0);
        $timezone     = substr($this->input->post('timezone') ?? '', 0, 100);
        $is_touch     = $this->input->post('is_touchscreen') ? 1 : 0;
        $browser_info = substr($this->input->post('browser_info') ?? '', 0, 300);
        $ip_address   = $this->input->ip_address();
        $lat          = $this->input->post('lat');
        $lng          = $this->input->post('lng');

        if (!$device_id) {
            echo json_encode(['success' => false, 'message' => 'Device ID tidak ditemukan.']);
            return;
        }

        $now = date('Y-m-d H:i:s');

        // Cek apakah device ini sudah pernah disimpan untuk user ini
        $existing = $this->db->get_where('hr_device_fingerprint', [
            'user_id'   => $user_id,
            'device_id' => $device_id
        ])->row();

        if ($existing) {
            // Update last_seen dan absen_count
            $update_data = [
                'last_seen'    => $now,
                'absen_count'  => $existing->absen_count + 1,
                'gpu_info'     => $gpu_info ?: $existing->gpu_info,
                'os_platform'  => $os_platform ?: $existing->os_platform,
                'screen_info'  => $screen_info ?: $existing->screen_info,
                'ip_address'   => $ip_address,
                'full_name'    => $full_name,
            ];
            
            if (!empty($lat) && !empty($lng)) {
                $update_data['last_lat'] = $lat;
                $update_data['last_lng'] = $lng;
            }

            $this->db->update('hr_device_fingerprint', $update_data, ['id' => $existing->id]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Fingerprint diperbarui.', 'status' => $existing->status]);
        } else {
            // Hitung label perangkat (Perangkat ke-n milik user ini)
            $count = $this->db->get_where('hr_device_fingerprint', ['user_id' => $user_id])->num_rows();
            $label = 'Perangkat #' . ($count + 1);

            $this->db->insert('hr_device_fingerprint', [
                'user_id'        => $user_id,
                'username'       => $username,
                'full_name'      => $full_name,
                'device_id'      => $device_id,
                'device_label'   => $label,
                'os_platform'    => $os_platform,
                'screen_info'    => $screen_info,
                'gpu_info'       => $gpu_info,
                'cpu_cores'      => $cpu_cores,
                'ram_gb'         => $ram_gb,
                'timezone'       => $timezone,
                'is_touchscreen' => $is_touch,
                'browser_info'   => $browser_info,
                'ip_address'     => $ip_address,
                'first_seen'     => $now,
                'last_seen'      => $now,
                'absen_count'    => 1,
                'status'         => 'aktif',
                'last_lat'       => $lat,
                'last_lng'       => $lng,
            ]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Perangkat baru terdaftar (' . $label . ').', 'status' => 'aktif']);
        }
    }
}
