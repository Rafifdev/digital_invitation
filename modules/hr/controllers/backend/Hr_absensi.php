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
        $user_id = get_user_data('id');
        $this->data['karyawan'] = $this->db->get_where('hr_karyawan', ['user_id' => $user_id])->row();
        
        if (!$this->data['karyawan']) {
            set_message('Akun Anda belum dipetakan sebagai Karyawan. Hubungi HRD.', 'error');
            redirect('administrator/dashboard');
        }

        $this->data['pengaturan'] = $this->db->get('hr_pengaturan')->row();
        
        // Cek absensi hari ini
        $hari_ini = date('Y-m-d');
        $this->data['absen_hari_ini'] = $this->db->get_where('hr_absensi', [
            'karyawan_id' => $this->data['karyawan']->id,
            'tanggal' => $hari_ini
        ])->row();

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
        $user_id = get_user_data('id');
        $karyawan = $this->db->get_where('hr_karyawan', ['user_id' => $user_id])->row();
        if (!$karyawan) {
            echo json_encode(['success' => false, 'message' => 'Karyawan tidak valid']); return;
        }

        $jenis_absen = $this->input->post('jenis_absen'); // masuk / pulang
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $foto_base64 = $this->input->post('foto');
        $device_id_client = $this->input->post('device_id');

        // 1. Device Lock Validation
        if (empty($karyawan->device_id)) {
            // First time login/absen, lock to this device
            $this->db->update('hr_karyawan', ['device_id' => $device_id_client], ['id' => $karyawan->id]);
        } else {
            if ($karyawan->device_id !== $device_id_client) {
                echo json_encode(['success' => false, 'message' => 'Device tidak dikenali! Dilarang absen menggunakan perangkat orang lain. Hubungi HRD untuk reset device.']); return;
            }
        }

        $pengaturan = $this->db->get('hr_pengaturan')->row();
        $jarak = 0;

        // 2. Geofencing Validation (Only for WFO)
        if ($karyawan->tipe_kerja == 'WFO') {
            if (!$lat || !$lng) {
                echo json_encode(['success' => false, 'message' => 'Lokasi GPS tidak terbaca. Pastikan izin lokasi aktif.']); return;
            }
            $jarak = $this->get_distance($pengaturan->latitude_kantor, $pengaturan->longitude_kantor, $lat, $lng);
            if ($jarak > $pengaturan->radius_toleransi_meter) {
                echo json_encode(['success' => false, 'message' => 'Anda berada di luar radius kantor! Jarak Anda: ' . round($jarak) . 'm. Radius maksimal: ' . $pengaturan->radius_toleransi_meter . 'm']); return;
            }
        }

        // 3. Save Image
        $image_parts = explode(";base64,", $foto_base64);
        if(count($image_parts) != 2) {
             echo json_encode(['success' => false, 'message' => 'Format foto tidak valid.']); return;
        }
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $filename = uniqid() . '_' . $karyawan->id . '.' . $image_type;
        $file_dir = FCPATH . 'uploads/absensi/' . $filename;
        file_put_contents($file_dir, $image_base64);

        $hari_ini = date('Y-m-d');
        $sekarang = date('Y-m-d H:i:s');
        $jam_sekarang = date('H:i:s');

        $absen_hari_ini = $this->db->get_where('hr_absensi', ['karyawan_id' => $karyawan->id, 'tanggal' => $hari_ini])->row();

        if ($jenis_absen == 'masuk') {
            if ($absen_hari_ini && $absen_hari_ini->waktu_masuk) {
                echo json_encode(['success' => false, 'message' => 'Anda sudah absen masuk hari ini.']); return;
            }

            // Hitung Keterlambatan
            $waktu_batas_masuk = date('H:i:s', strtotime($pengaturan->jam_masuk . ' + ' . $pengaturan->toleransi_terlambat_menit . ' minutes'));
            $status = ($jam_sekarang > $waktu_batas_masuk) ? 'Terlambat' : 'Tepat Waktu';

            $data_masuk = [
                'karyawan_id' => $karyawan->id,
                'tanggal' => $hari_ini,
                'waktu_masuk' => $sekarang,
                'foto_masuk' => 'uploads/absensi/' . $filename,
                'lat_masuk' => $lat,
                'lng_masuk' => $lng,
                'jarak_masuk_meter' => round($jarak),
                'status_kehadiran' => $status
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

            // Hitung Toleransi Pulang Awal
            $waktu_boleh_pulang = date('H:i:s', strtotime($pengaturan->jam_pulang . ' - ' . $pengaturan->toleransi_pulang_awal_menit . ' minutes'));
            if ($jam_sekarang < $waktu_boleh_pulang) {
                echo json_encode(['success' => false, 'message' => 'Belum waktunya pulang! Anda baru boleh absen pulang pukul ' . $waktu_boleh_pulang]); return;
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
}
