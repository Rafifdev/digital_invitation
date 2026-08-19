<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_absensi extends Admin
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
        // View for Karyawan to do Absensi (Live Camera)
        $this->is_allowed('hr_absensi_list'); // Pastikan login & punya akses

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
            
            // Hitung titik tengah shift sebagai batas akhir checkout
            $time_masuk = strtotime($ag->tanggal . ' ' . $jam_masuk_shift);
            $time_pulang = strtotime($ag->tanggal . ' ' . $jam_pulang_shift);
            if ($jam_masuk_shift > $jam_pulang_shift) {
                // Lintas hari (shift malam), pulangnya besok
                $time_pulang = strtotime($ag->tanggal . ' ' . $jam_pulang_shift . ' +1 day');
            }
            
            $diff = $time_pulang - $time_masuk;
            if ($diff < 0) $diff = 8 * 3600; // fallback jika aneh
            
            // Titik tengah jeda istirahat (misal pulang 06:00, masuk lagi 22:00 -> titik tengah 14:00)
            // Jadi batas telat pulang adalah setengah dari sisa waktu sampai shift berikutnya
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

        $user_id = get_user_data('id');
        $this->data['karyawan'] = $this->db->get_where('hr_karyawan', ['user_id' => $user_id])->row();
        
        if (!$this->data['karyawan']) {
            set_message('Akun Anda belum dipetakan sebagai Karyawan. Hubungi HRD.', 'error');
            redirect('administrator/dashboard');
        }

        $this->data['pengaturan'] = $this->db->get('hr_pengaturan')->row();
        
        // Ambil info Shift Karyawan
        $shift_info = $this->db->get_where('hr_shift', ['id' => $this->data['karyawan']->shift_id])->row();
        $this->data['shift_info'] = $shift_info;

        // Ambil info Kantor Karyawan
        $kantor_info = $this->db->get_where('hr_kantor', ['id' => $this->data['karyawan']->kantor_id])->row();
        $this->data['kantor_info'] = $kantor_info;
        
        // Cek absensi hari ini
        $hari_ini = date('Y-m-d');
        $this->data['absen_hari_ini'] = $this->db->get_where('hr_absensi', [
            'karyawan_id' => $this->data['karyawan']->id,
            'tanggal' => $hari_ini
        ])->row();

        // Cek apakah user ini Admin (boleh melihat detail identitas perangkat)
        $this->data['is_admin'] = $this->aauth->is_admin();

		$this->template->title('Live Absensi Karyawan');
		$this->render('backend/standart/administrator/hr_absensi/hr_absensi_live', $this->data);
	}

    private function get_distance($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371000; // in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));
        return $earth_radius * $c;
    }

	public function process_absen()
	{
        $this->is_allowed('hr_absensi_add');
        $karyawan_id = get_user_data('id');

        $karyawan = $this->db->get_where('hr_karyawan', ['user_id' => $karyawan_id])->row();
        if (!$karyawan) {
            echo json_encode(['success' => false, 'message' => 'Data Karyawan Anda tidak ditemukan di sistem.']); return;
        }

        $jenis_absen = $this->input->post('jenis');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $foto_base64 = $this->input->post('foto');
        $device_id_client = $this->input->post('device_id');
        $alasan_terlambat = $this->input->post('alasan_terlambat') ?: NULL;

        // 1. Device Lock Validation
        // A. Cek status di tabel hr_device_fingerprint (Apakah perangkat ini diblokir?)
        $device_status = $this->db->get_where('hr_device_fingerprint', [
            'user_id' => $karyawan->user_id,
            'device_id' => $device_id_client
        ])->row();
        
        if ($device_status && $device_status->status === 'dicabut') {
            echo json_encode(['success' => false, 'message' => 'Perangkat ini telah DIBLOKIR oleh Admin. Silakan gunakan perangkat lain atau hubungi HRD.']); return;
        }

        // B. Lock Karyawan ke 1 Perangkat Utama
        if (empty($karyawan->device_id)) {
            // First time login/absen, lock to this device
            $this->db->update('hr_karyawan', ['device_id' => $device_id_client], ['id' => $karyawan->id]);
        } else {
            if ($karyawan->device_id !== $device_id_client) {
                echo json_encode(['success' => false, 'message' => 'Device tidak dikenali! Dilarang absen menggunakan perangkat orang lain. Hubungi HRD untuk reset device.']); return;
            }
        }

        $pengaturan = $this->db->get('hr_pengaturan')->row();
        $kantor = $this->db->get_where('hr_kantor', ['id' => $karyawan->kantor_id])->row();
        
        // Fallback jika karyawan belum di-assign ke cabang spesifik
        if (!$kantor) {
            $default_kantor_id = $pengaturan->default_kantor_id ?: 1;
            $kantor = $this->db->get_where('hr_kantor', ['id' => $default_kantor_id])->row();
        }

        // Gunakan titik pusat cabang
        $lat_kantor = $kantor ? $kantor->latitude : '-6.2088';
        $lng_kantor = $kantor ? $kantor->longitude : '106.8456';
        $radius_toleransi = $kantor ? $kantor->radius_meter : 50;

        $jarak = 0;

        // 2. Geofencing Validation (hanya berlaku untuk WFO, WFH/WFA bebas lokasi)
        if ($karyawan->tipe_kerja == 'WFO') {
            if (!$lat || !$lng) {
                echo json_encode(['success' => false, 'message' => 'Lokasi GPS tidak terbaca. Pastikan izin lokasi aktif.']); return;
            }
            $jarak = $this->get_distance($lat_kantor, $lng_kantor, $lat, $lng);
            if ($jarak > $radius_toleransi) {
                echo json_encode(['success' => false, 'message' => 'Anda berada di luar radius cabang! Jarak Anda: ' . round($jarak) . 'm. Radius maksimal: ' . $radius_toleransi . 'm dari ' . ($kantor ? $kantor->nama_kantor : 'Kantor Pusat')]); return;
            }
        }
        // WFH dan WFA: tidak ada pembatasan lokasi, langsung lanjut

        // 3. Save Image
        $upload_path = FCPATH . 'uploads/absensi/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
            chmod($upload_path, 0777);
            file_put_contents($upload_path . 'index.html', '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>');
        }

        $image_parts = explode(";base64,", $foto_base64);
        if(count($image_parts) != 2) {
             echo json_encode(['success' => false, 'message' => 'Format foto tidak valid.']); return;
        }
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $filename = uniqid() . '_' . $karyawan->id . '.' . $image_type;
        $file_dir = $upload_path . $filename;
        file_put_contents($file_dir, $image_base64);

        $hari_ini = date('Y-m-d');
        $sekarang = date('Y-m-d H:i:s');
        $jam_sekarang = date('H:i:s');

        $absen_hari_ini = $this->db->get_where('hr_absensi', ['karyawan_id' => $karyawan->id, 'tanggal' => $hari_ini])->row();

        if ($jenis_absen == 'masuk') {
            if ($absen_hari_ini && $absen_hari_ini->waktu_masuk) {
                echo json_encode(['success' => false, 'message' => 'Anda sudah absen masuk hari ini.']); return;
            }
            if ($absen_hari_ini && in_array($absen_hari_ini->status_kehadiran, ['Izin', 'Izin Khusus', 'Sakit (Dengan Surat)', 'Sakit (Tanpa Surat)', 'Cuti'])) {
                echo json_encode(['success' => false, 'message' => 'Anda sudah mengajukan ' . $absen_hari_ini->status_kehadiran . ' hari ini. Tidak bisa absen masuk.']); return;
            }

            // Dapatkan Jam Masuk Karyawan dari Shift-nya atau standar karyawan
            $shift = $this->db->get_where('hr_shift', ['id' => $karyawan->shift_id])->row();
            // Prioritas: 1. Shift, 2. Karyawan (jam_masuk_standar), 3. Pengaturan global
            $jam_masuk_shift = $shift ? $shift->jam_masuk : (!empty($karyawan->jam_masuk_standar) ? $karyawan->jam_masuk_standar : $pengaturan->jam_masuk);
            $jam_pulang_shift = $shift ? $shift->jam_pulang : $pengaturan->jam_pulang;

            $is_cross_day = $jam_masuk_shift > $jam_pulang_shift;
            
            if (!$is_cross_day) {
                if ($jam_sekarang > $jam_pulang_shift) {
                    echo json_encode(['success' => false, 'message' => 'Sudah melewati jam pulang shift (' . date('H:i', strtotime($jam_pulang_shift)) . '). Tidak bisa melakukan Absen Masuk.']); return;
                }
            } else {
                // Shift malam (lintas hari). Batas tutup absen dihitung dinamis (titik tengah antara jam pulang dan jam masuk berikutnya)
                // Misal masuk 22:00, pulang 06:00. Jeda 16 jam. Titik tengah = 14:00.
                // Dilarang absen masuk jika jam sekarang berada di antara 06:00 dan 14:00.
                $time_masuk = strtotime($jam_masuk_shift);
                $time_pulang = strtotime($jam_pulang_shift);
                
                $diff = $time_masuk - $time_pulang;
                $midpoint_time = $time_pulang + ($diff / 2);
                $jam_midpoint = date('H:i:s', $midpoint_time);

                if ($jam_sekarang > $jam_pulang_shift && $jam_sekarang < $jam_midpoint) {
                    echo json_encode(['success' => false, 'message' => 'Sudah melewati jam pulang shift. Anda baru bisa absen lagi setelah jam ' . date('H:i', $midpoint_time) . ' untuk shift berikutnya.']); return;
                }
            }

            // Hitung Keterlambatan
            $waktu_batas_masuk = date('H:i:s', strtotime($jam_masuk_shift . ' + ' . $pengaturan->toleransi_terlambat_menit . ' minutes'));
            $waktu_telat_parah = date('H:i:s', strtotime($jam_masuk_shift . ' + ' . ($pengaturan->menit_batas_denda_telat ?? 15) . ' minutes'));
            $status = ($jam_sekarang > $waktu_batas_masuk) ? 'Telat' : 'Hadir'; // 'Telat' dan 'Hadir' sesuai ENUM baru

            // Jika telat melebihi batas denda, wajib isi alasan
            if ($jam_sekarang > $waktu_telat_parah && empty($alasan_terlambat)) {
                echo json_encode(['success' => false, 'message' => 'Anda terlambat lebih dari ' . ($pengaturan->menit_batas_denda_telat ?? 15) . ' menit! Wajib mengisi kolom Alasan Terlambat.']); return;
            }

            // Hitung menit telat
            $menit_telat = 0;
            if ($status == 'Telat') {
                // Selisih antara jam masuk shift (bukan batas masuk) dengan jam sekarang
                $t_shift = strtotime($hari_ini . ' ' . $jam_masuk_shift);
                $t_sekarang = strtotime($sekarang);
                $selisih_detik = $t_sekarang - $t_shift;
                if ($selisih_detik > 0) {
                    $menit_telat = floor($selisih_detik / 60);
                }
            }

            $data_masuk = [
                'karyawan_id' => $karyawan->id,
                'tanggal' => $hari_ini,
                'waktu_masuk' => $sekarang,
                'foto_masuk' => 'uploads/absensi/' . $filename,
                'lat_masuk' => $lat,
                'lng_masuk' => $lng,
                'jarak_masuk_meter' => round($jarak),
                'status_kehadiran' => $status,
                'alasan_terlambat' => $alasan_terlambat,
                'terlambat_menit' => $menit_telat
            ];
            
            if ($absen_hari_ini) {
                $this->db->update('hr_absensi', $data_masuk, ['id' => $absen_hari_ini->id]);
            } else {
                $this->db->insert('hr_absensi', $data_masuk);
            }
            
            echo json_encode(['success' => true, 'message' => 'Absen Masuk Berhasil! Status: ' . $status]); return;

        } else if ($jenis_absen == 'pulang') {
            if (!$absen_hari_ini || !$absen_hari_ini->waktu_masuk) {
                echo json_encode(['success' => false, 'message' => 'Anda belum absen masuk hari ini.']); return;
            }
            if ($absen_hari_ini->waktu_pulang) {
                echo json_encode(['success' => false, 'message' => 'Anda sudah absen pulang hari ini.']); return;
            }

            // Dapatkan Jam Pulang Karyawan dari Shift-nya
            $shift = $this->db->get_where('hr_shift', ['id' => $karyawan->shift_id])->row();
            $jam_pulang_shift = $shift ? $shift->jam_pulang : $pengaturan->jam_pulang;

            // Hitung Toleransi Pulang Awal
            $waktu_boleh_pulang = date('H:i:s', strtotime($jam_pulang_shift . ' - ' . $pengaturan->toleransi_pulang_awal_menit . ' minutes'));
            if ($jam_sekarang < $waktu_boleh_pulang) {
                echo json_encode(['success' => false, 'message' => 'Belum waktunya pulang! Anda baru boleh absen pulang pukul ' . $waktu_boleh_pulang]); return;
            }

            // Validasi Batas Akhir Pulang
            $batas_jam_pulang = $pengaturan->batas_jam_pulang ?? '23:59:00';
            if ($jam_sekarang > $batas_jam_pulang) {
                echo json_encode(['success' => false, 'message' => 'Sudah melewati batas akhir absen pulang (Batas: ' . date('H:i', strtotime($batas_jam_pulang)) . '). Absen pulang ditolak.']); return;
            }

            $data_pulang = [
                'waktu_pulang' => $sekarang,
                'foto_pulang' => 'uploads/absensi/' . $filename,
                'lat_pulang' => $lat,
                'lng_pulang' => $lng,
                'jarak_pulang_meter' => round($jarak)
            ];
            $this->db->update('hr_absensi', $data_pulang, ['id' => $absen_hari_ini->id]);
            echo json_encode(['success' => true, 'message' => 'Absen Pulang Berhasil!']); return;
        }

        echo json_encode(['success' => false, 'message' => 'Invalid Action']);
	}

    public function process_izin()
    {
        $this->is_allowed('hr_absensi_add');
        $karyawan_id = get_user_data('id');

        $karyawan = $this->db->get_where('hr_karyawan', ['user_id' => $karyawan_id])->row();
        if (!$karyawan) {
            echo json_encode(['success' => false, 'message' => 'Data Karyawan Anda tidak ditemukan.']); return;
        }

        $jenis_izin = $this->input->post('jenis_izin'); // Izin / Sakit
        $alasan = $this->input->post('alasan');

        $allowed_izin = ['Izin', 'Izin Khusus', 'Sakit (Dengan Surat)', 'Sakit (Tanpa Surat)', 'Cuti'];
        if (!in_array($jenis_izin, $allowed_izin)) {
            echo json_encode(['success' => false, 'message' => 'Jenis izin tidak valid.']); return;
        }
        if (empty($alasan)) {
            echo json_encode(['success' => false, 'message' => 'Alasan wajib diisi!']); return;
        }

        $hari_ini = date('Y-m-d');
        $absen_hari_ini = $this->db->get_where('hr_absensi', ['karyawan_id' => $karyawan->id, 'tanggal' => $hari_ini])->row();

        if ($absen_hari_ini && $absen_hari_ini->waktu_masuk) {
            echo json_encode(['success' => false, 'message' => 'Anda sudah absen masuk hari ini. Tidak bisa mengajukan izin.']); return;
        }
        if ($absen_hari_ini && in_array($absen_hari_ini->status_kehadiran, $allowed_izin)) {
            echo json_encode(['success' => false, 'message' => 'Anda sudah mengajukan ' . $absen_hari_ini->status_kehadiran . ' hari ini. Data tidak bisa diubah lagi.']); return;
        }

        $filename_surat = NULL;
        if ($jenis_izin == 'Sakit (Dengan Surat)') {
            if (empty($_FILES['file_surat']['name'])) {
                echo json_encode(['success' => false, 'message' => 'Surat dokter wajib diupload untuk pilihan ini!']); return;
            }
            
            $config['upload_path'] = './uploads/absensi/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = 5048;
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('file_surat')) {
                echo json_encode(['success' => false, 'message' => strip_tags($this->upload->display_errors())]); return;
            }
            $filename_surat = 'uploads/absensi/' . $this->upload->data('file_name');
        }

        $data_izin = [
            'karyawan_id' => $karyawan->id,
            'tanggal' => $hari_ini,
            'status_kehadiran' => $jenis_izin,
            'alasan_terlambat' => $alasan, // Digunakan multi-purpose untuk alasan izin
            'file_surat' => $filename_surat
        ];

        if ($absen_hari_ini) {
            $this->db->update('hr_absensi', $data_izin, ['id' => $absen_hari_ini->id]);
        } else {
            $this->db->insert('hr_absensi', $data_izin);
        }

        echo json_encode(['success' => true, 'message' => 'Pengajuan ' . $jenis_izin . ' berhasil disubmit untuk hari ini!']);
    }
}
