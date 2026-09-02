<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_laporan extends Admin
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->is_allowed('hr_laporan_list');
        $bulan = $this->input->get('bulan') ?: date('Y-m');

        // Auto Checkout Absen Gantung Dinamis (Berdasarkan Titik Tengah Shift)
        $pengaturan = $this->db->get('hr_pengaturan')->row();
        
        $absen_gantung = $this->db->query("
            SELECT a.id, a.tanggal, a.status_kehadiran, k.shift_id, k.jam_masuk_standar
            FROM hr_absensi a
            JOIN hr_karyawan k ON a.karyawan_id = k.id
            WHERE a.waktu_pulang IS NULL AND a.waktu_masuk IS NOT NULL
        ")->result();

        $sekarang_time = time();
        foreach ($absen_gantung as $ag) {
            $shift = $this->db->get_where('hr_shift', ['id' => $ag->shift_id])->row();
            $jam_masuk_shift = $shift ? $shift->jam_masuk : (!empty($ag->jam_masuk_standar) ? $ag->jam_masuk_standar : $pengaturan->jam_masuk);
            $jam_pulang_shift = $shift ? $shift->jam_pulang : $pengaturan->jam_pulang;
            
            $time_masuk = strtotime($ag->tanggal . ' ' . $jam_masuk_shift);
            $time_pulang = strtotime($ag->tanggal . ' ' . $jam_pulang_shift);
            if ($jam_masuk_shift > $jam_pulang_shift) {
                $time_pulang = strtotime($ag->tanggal . ' ' . $jam_pulang_shift . ' +1 day');
            }
            
            $diff = $time_pulang - $time_masuk;
            if ($diff < 0) $diff = 8 * 3600;
            
            $sisa_waktu = (24 * 3600) - $diff;
            $batas_auto_checkout = $time_pulang + ($sisa_waktu / 2);
            
            if ($sekarang_time > $batas_auto_checkout) {
                $status_baru = $ag->status_kehadiran;
                if (strpos($status_baru, '(Lupa Absen') === false) {
                    $status_baru = $status_baru . ' (Lupa Absen Pulang)';
                }
                $this->db->update('hr_absensi', [
                    'waktu_pulang' => date('Y-m-d H:i:s', $time_pulang),
                    'status_kehadiran' => $status_baru
                ], ['id' => $ag->id]);
            }
        }

        $this->db->select('a.*, k.nik, k.tipe_kerja, u.username, u.email, kt.nama_kantor, kt.latitude as kantor_lat, kt.longitude as kantor_lng, kt.radius_meter as kantor_radius');
        $this->db->from('hr_absensi a');
        $this->db->join('hr_karyawan k', 'k.id = a.karyawan_id');
        $this->db->join('aauth_users u', 'u.id = k.user_id');
        $this->db->join('hr_kantor kt', 'kt.id = k.kantor_id', 'left');
        $this->db->like('a.tanggal', $bulan, 'after');
        
        if (!$this->aauth->is_admin()) {
            $this->db->where('k.user_id', $this->aauth->get_user_id());
        }

        $this->db->order_by('a.tanggal', 'DESC');
        $this->db->order_by('a.waktu_masuk', 'DESC');

		$this->data['laporan'] = $this->db->get()->result();
        
        // --- 2. QUERY REKAP BULANAN PER KARYAWAN ---
        $this->db->select('
            k.id, k.nik, k.nama_lengkap, kt.nama_kantor, 
            COUNT(a.id) as total_kehadiran,
            SUM(CASE WHEN a.status_kehadiran IN ("Hadir", "Tepat Waktu", "Telat", "Terlambat") THEN 1 ELSE 0 END) as total_hadir,
            SUM(CASE WHEN a.status_kehadiran IN ("Telat", "Terlambat") THEN 1 ELSE 0 END) as total_terlambat,
            SUM(CASE WHEN a.status_kehadiran IN ("Izin", "Izin Khusus") THEN 1 ELSE 0 END) as total_izin,
            SUM(CASE WHEN a.status_kehadiran IN ("Sakit", "Sakit (Dengan Surat)", "Sakit (Tanpa Surat)") THEN 1 ELSE 0 END) as total_sakit,
            SUM(CASE WHEN a.status_kehadiran = "Cuti" THEN 1 ELSE 0 END) as total_cuti,
            SUM(CASE WHEN a.status_kehadiran = "Alpha" THEN 1 ELSE 0 END) as total_alpha,
            SUM(a.terlambat_menit) as total_menit_telat
        ', FALSE);
        $this->db->from('hr_karyawan k');
        $this->db->join('hr_absensi a', "a.karyawan_id = k.id AND a.tanggal LIKE '$bulan%'", 'left');
        $this->db->join('hr_kantor kt', 'kt.id = k.kantor_id', 'left');
        if (!$this->aauth->is_admin()) {
            $this->db->where('k.user_id', $this->aauth->get_user_id());
        }
        $this->db->group_by('k.id');
        $this->data['rekap'] = $this->db->get()->result();

        $this->data['bulan_aktif'] = $bulan;
		
		$this->template->title('Laporan Absensi Karyawan');
		$this->render('backend/standart/administrator/hr_laporan/hr_laporan_list', $this->data);
	}

    public function view($id)
    {
        $this->is_allowed('hr_laporan_view');
        
        $this->db->select('a.*, k.nik, k.tipe_kerja, u.username, u.email, kt.nama_kantor');
        $this->db->from('hr_absensi a');
        $this->db->join('hr_karyawan k', 'k.id = a.karyawan_id');
        $this->db->join('aauth_users u', 'u.id = k.user_id');
        $this->db->join('hr_kantor kt', 'kt.id = k.kantor_id', 'left');
        $this->db->where('a.id', $id);

        if (!$this->aauth->is_admin()) {
            $this->db->where('k.user_id', $this->aauth->get_user_id());
        }
        
        $laporan = $this->db->get()->row();
        
        if (!$laporan) {
            set_message('Data absen tidak ditemukan.', 'error');
            redirect('administrator/hr_laporan');
        }
        
        $this->data['laporan'] = $laporan;
        $this->template->title('Detail Absensi Karyawan');
        $this->render('backend/standart/administrator/hr_laporan/hr_laporan_view', $this->data);
    }
}
