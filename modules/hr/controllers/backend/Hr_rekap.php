<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_rekap extends Admin
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->is_allowed('hr_rekap_list');
        $periode = $this->input->get('bulan') ?: date('Y-m');
        $tahun = substr($periode, 0, 4);
        $bulan = substr($periode, 5, 2);
        
        $jumlah_hari_sebulan = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        // Ambil data hari libur dari tabel hr_hari_libur
        $hari_libur_db = $this->db->get_where('hr_hari_libur', "DATE_FORMAT(tanggal, '%Y-%m') = '$periode'")->result();
        $libur_array = [];
        foreach($hari_libur_db as $ldb) {
            $libur_array[] = $ldb->tanggal;
        }

        $this->db->select('k.id, u.username, k.nama_lengkap, k.tanggal_bergabung');
        $this->db->from('hr_karyawan k');
        $this->db->join('aauth_users u', 'u.id = k.user_id', 'left');
        
        if (!$this->aauth->is_admin()) {
            $this->db->where('k.user_id', $this->aauth->get_user_id());
        }
        $this->db->order_by('k.nama_lengkap', 'ASC');
		$karyawans = $this->db->get()->result();

        // Buat matriks
        $matrix = [];
        foreach ($karyawans as $k) {
            $row = [
                'nama_lengkap' => $k->nama_lengkap ?: $k->username,
                'days' => []
            ];

            // Ambil semua absen di bulan ini untuk karyawan ini
            $absensi = $this->db->get_where('hr_absensi', ['karyawan_id' => $k->id, 'tanggal LIKE' => "$periode%"])->result();
            $absen_map = [];
            foreach($absensi as $ab) {
                $absen_map[$ab->tanggal] = $ab->status_kehadiran;
            }

            for ($i = 1; $i <= $jumlah_hari_sebulan; $i++) {
                $tgl = sprintf("%04d-%02d-%02d", $tahun, $bulan, $i);
                $day_of_week = date('N', strtotime($tgl)); // 1=Mon, 7=Sun
                
                $status = '';
                
                // Aturan 1: Belum bergabung
                if ($k->tanggal_bergabung && $tgl < $k->tanggal_bergabung) {
                    $status = 'Belum Bergabung';
                } else {
                    // Aturan 2: Cek apakah ada absen
                    if (isset($absen_map[$tgl])) {
                        $st = $absen_map[$tgl];
                        if (in_array($st, ['Hadir', 'Tepat Waktu'])) $status = 'Hadir';
                        else if (in_array($st, ['Telat', 'Terlambat'])) $status = 'Terlambat';
                        else if (in_array($st, ['Izin', 'Izin Khusus'])) $status = 'Izin';
                        else if (strpos($st, 'Sakit') !== false) $status = 'Sakit';
                        else if ($st == 'Cuti') $status = 'Cuti';
                    } else {
                        // Aturan 3: Jika tidak absen, cek apakah hari libur atau weekend
                        if ($day_of_week >= 6 || in_array($tgl, $libur_array)) {
                            $status = 'Libur';
                        } else {
                            // Jika bukan libur dan belum hari esok, maka Alfa
                            if ($tgl <= date('Y-m-d')) {
                                $status = 'Alfa';
                            } else {
                                $status = '-'; // Hari belum terjadi
                            }
                        }
                    }
                }
                
                $row['days'][$i] = $status;
            }
            $matrix[] = $row;
        }

        $this->data['jumlah_hari'] = $jumlah_hari_sebulan;
        $this->data['bulan_aktif'] = $periode;
        $this->data['matrix'] = $matrix;
		
		$this->template->title('Laporan Matriks Absensi');
		$this->render('backend/standart/administrator/hr_rekap/hr_rekap_list', $this->data);
	}
}

