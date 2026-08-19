<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_profile extends Admin
{
	public function __construct()
	{
		parent::__construct();
		// Ensure user is logged in
		if (!$this->aauth->is_loggedin()) {
			redirect('administrator/login', 'refresh');
		}
	}

	public function index()
	{
        // Get User Data
        $user_id = get_user_data('id');
        $this->db->select('hr_karyawan.*, hr_shift.nama_shift, hr_shift.jam_masuk, hr_shift.jam_pulang, aauth_groups.name as group_name');
        $this->db->from('hr_karyawan');
        $this->db->join('hr_shift', 'hr_shift.id = hr_karyawan.shift_id', 'left');
        $this->db->join('aauth_user_to_group', 'aauth_user_to_group.user_id = hr_karyawan.user_id', 'left');
        $this->db->join('aauth_groups', 'aauth_groups.id = aauth_user_to_group.group_id', 'left');
        $this->db->where('hr_karyawan.user_id', $user_id);
        $karyawan = $this->db->get()->row();

        if (!$karyawan) {
            // Jika bukan karyawan (misal super admin), redirect ke dashboard utama
            if ($this->aauth->is_admin()) {
                redirect('administrator/dashboard', 'refresh');
            } else {
                set_message('Profil karyawan tidak ditemukan.', 'error');
                redirect('administrator/login', 'refresh');
            }
        }

        $this->data['karyawan'] = $karyawan;

        // Ambil data payroll terakhir (Bulan Ini atau Bulan Terakhir)
        $periode_sekarang = date('Y-m');
        $this->db->where('karyawan_id', $karyawan->id);
        $this->db->order_by('periode_bulan', 'DESC');
        $this->db->limit(1);
        $payroll = $this->db->get('hr_payroll')->row();
        
        $this->data['payroll_terakhir'] = $payroll;

        $this->data['absensi'] = null;
        $this->data['log_harian'] = [];
        
        if ($payroll) {
            // Ambil rincian log harian
            $tahun = substr($payroll->periode_bulan, 0, 4);
            $bulan = substr($payroll->periode_bulan, 5, 2);
            
            $pengaturan = $this->db->get('hr_pengaturan')->row();
            $tgl_mulai = $pengaturan->tgl_cutoff_mulai ?? 21;
            $tgl_akhir = $pengaturan->tgl_cutoff_akhir ?? 20;

            if ($tgl_mulai > $tgl_akhir) {
                $start_date_obj = new DateTime("$tahun-$bulan-01");
                $start_date_obj->modify('-1 month');
                $prev_year = $start_date_obj->format('Y');
                $prev_month = $start_date_obj->format('m');
                
                $max_days_prev = cal_days_in_month(CAL_GREGORIAN, $prev_month, $prev_year);
                $actual_mulai = min($tgl_mulai, $max_days_prev);
                $start_date = sprintf("%04d-%02d-%02d", $prev_year, $prev_month, $actual_mulai);
                
                $max_days_curr = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
                $actual_akhir = min($tgl_akhir, $max_days_curr);
                $end_date = sprintf("%04d-%02d-%02d", $tahun, $bulan, $actual_akhir);
            } else {
                $max_days_curr = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
                $actual_mulai = min($tgl_mulai, $max_days_curr);
                $actual_akhir = min($tgl_akhir, $max_days_curr);
                $start_date = sprintf("%04d-%02d-%02d", $tahun, $bulan, $actual_mulai);
                $end_date = sprintf("%04d-%02d-%02d", $tahun, $bulan, $actual_akhir);
            }

            $begin = new DateTime($start_date);
            $end_date_obj = new DateTime($end_date);
            $end_date_obj = $end_date_obj->modify('+1 day');
            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($begin, $interval, $end_date_obj);

            $hari_libur = [];
            $this->db->where("tanggal >=", $start_date);
            $this->db->where("tanggal <=", $end_date);
            $libur_db = $this->db->get('hr_hari_libur')->result();
            foreach($libur_db as $ldb) {
                $hari_libur[] = $ldb->tanggal;
            }

            $hari_kerja_per_minggu = $pengaturan->hari_kerja_per_minggu ?? 'senin_jumat';

            $this->db->where('karyawan_id', $karyawan->id);
            $this->db->where("tanggal >=", $start_date);
            $this->db->where("tanggal <=", $end_date);
            $absensi_db = $this->db->get('hr_absensi')->result();
            $absen_map = [];
            $total_menit_telat = 0;
            
            // LIVE counters - hitung langsung dari database hr_absensi
            $jumlah_hadir = 0;
            $jumlah_telat = 0;
            $jumlah_sakit = 0;
            $jumlah_izin = 0;
            $jumlah_cuti = 0;
            $jumlah_alfa_record = 0; // Alpha yang ada recordnya di db
            
            foreach($absensi_db as $a) {
                $absen_map[$a->tanggal] = $a;
                $total_menit_telat += (int)$a->terlambat_menit;
                $s = $a->status_kehadiran;
                
                // Match semua varian status kehadiran
                if (in_array($s, ['Hadir', 'Tepat Waktu'])) {
                    $jumlah_hadir++;
                } else if (in_array($s, ['Telat', 'Terlambat'])) {
                    $jumlah_hadir++; // Telat tetap dihitung hadir
                    $jumlah_telat++;
                } else if (in_array($s, ['Izin', 'Izin Khusus'])) {
                    $jumlah_izin++;
                } else if (in_array($s, ['Sakit', 'Sakit (Dengan Surat)', 'Sakit (Tanpa Surat)'])) {
                    $jumlah_sakit++;
                } else if ($s == 'Cuti') {
                    $jumlah_cuti++;
                } else if ($s == 'Alpha') {
                    $jumlah_alfa_record++;
                }
            }

            // Hitung total hari kerja dalam periode cutoff
            $real_today = date('Y-m-d');
            $total_hari_kerja = 0;
            foreach($daterange as $dt) {
                $date_str = $dt->format('Y-m-d');
                $day_of_week = $dt->format('N');
                $is_weekend = false;
                if ($hari_kerja_per_minggu == 'senin_jumat' && $day_of_week > 5) $is_weekend = true;
                if ($hari_kerja_per_minggu == 'senin_sabtu' && $day_of_week > 6) $is_weekend = true;
                
                if (!$is_weekend && !in_array($date_str, $hari_libur)) {
                    $total_hari_kerja++;
                }
            }

            $this->data['absensi'] = [
                'hadir' => $jumlah_hadir,
                'alfa' => $jumlah_alfa_record,
                'sakit' => $jumlah_sakit,
                'izin' => $jumlah_izin,
                'telat' => $jumlah_telat,
                'cuti' => $jumlah_cuti,
                'total_hari_kerja' => $total_hari_kerja,
                'total_menit_telat' => $total_menit_telat
            ];

            $gaji_harian = $payroll->gaji_harian_pokok ?? 0;
            $tunj_tetap_harian = $payroll->tunj_tetap_harian ?? 0;
            $tunj_kehadiran_harian = $payroll->tunj_kehadiran_harian ?? 0;

            $tier1_menit = $pengaturan->denda_tier1_menit ?? 15;
            $tier1_persen = ($pengaturan->denda_tier1_persen ?? 0) / 100;
            $tier2_menit = $pengaturan->denda_tier2_menit ?? 30;
            $tier2_persen = ($pengaturan->denda_tier2_persen ?? 5) / 100;
            $tier3_menit = $pengaturan->denda_tier3_menit ?? 60;
            $tier3_persen = ($pengaturan->denda_tier3_persen ?? 10) / 100;
            $tier4_persen = ($pengaturan->denda_tier4_persen ?? 25) / 100;

            $notifikasi_hari_ini = null;
            
            // Build daily log HANYA dari data yang ada di database hr_absensi
            $log_harian = [];
            $running_potongan = 0;

            $hari_nama = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

            // Urutkan absensi_db berdasarkan tanggal ASC untuk running_potongan yang benar
            usort($absensi_db, function($a, $b) {
                return strcmp($a->tanggal, $b->tanggal);
            });

            foreach($absensi_db as $absen) {
                $date_str = $absen->tanggal;
                $status = $absen->status_kehadiran;
                $dt = new DateTime($date_str);
                $day_of_week = $dt->format('N');
                $nama_hari = $hari_nama[$day_of_week];

                $nominal_potongan = 0;
                $status_tampil = '';
                $status_badge = 'default';
                $icon = 'fa-question-circle';
                $keterangan = '';

                if (in_array($status, ['Hadir', 'Tepat Waktu'])) {
                    $status_tampil = 'Hadir Tepat Waktu';
                    $status_badge = 'success';
                    $icon = 'fa-check-circle';
                    $keterangan = 'Kehadiran tercatat dengan baik';
                } else if (in_array($status, ['Telat', 'Terlambat'])) {
                    $menit_telat = $absen->terlambat_menit ?? 0;
                    $persen = 0;
                    if ($menit_telat <= $tier1_menit) {
                        $persen = $tier1_persen;
                    } else if ($menit_telat <= $tier2_menit) {
                        $persen = $tier2_persen;
                    } else if ($menit_telat <= $tier3_menit) {
                        $persen = $tier3_persen;
                    } else {
                        $persen = $tier4_persen;
                    }
                    $nominal_potongan = $gaji_harian * $persen;
                    $persen_tampil = $persen * 100;
                    $status_tampil = 'Telat ' . $menit_telat . ' menit';
                    $status_badge = 'warning';
                    $icon = 'fa-clock-o';
                    $keterangan = "Denda keterlambatan {$persen_tampil}% dari gaji harian";

                } else if ($status == 'Alpha') {
                    $nominal_potongan = $gaji_harian + $tunj_tetap_harian + $tunj_kehadiran_harian;
                    $status_tampil = 'Alpha / Mangkir';
                    $status_badge = 'danger';
                    $icon = 'fa-times-circle';
                    $keterangan = 'Potong gaji harian + tunjangan tetap + tunjangan kehadiran';

                } else if ($status == 'Sakit (Tanpa Surat)') {
                    $is_potong = $pengaturan->potong_gaji_sakit_tanpa_surat ?? 1;
                    if ($is_potong == 1) {
                        $nominal_potongan = $gaji_harian + $tunj_tetap_harian + $tunj_kehadiran_harian;
                    }
                    $status_tampil = 'Sakit (Tanpa Surat)';
                    $status_badge = 'danger';
                    $icon = 'fa-medkit';
                    $keterangan = $is_potong ? 'Potong gaji harian + tunjangan' : 'Tanpa potongan (kebijakan perusahaan)';

                } else if (in_array($status, ['Sakit (Dengan Surat)', 'Izin Khusus', 'Cuti'])) {
                    $nominal_potongan = $tunj_kehadiran_harian;
                    $status_tampil = $status;
                    $status_badge = 'info';
                    $icon = ($status == 'Cuti') ? 'fa-plane' : (strpos($status, 'Sakit') !== false ? 'fa-medkit' : 'fa-calendar-check-o');
                    $keterangan = 'Potong tunjangan kehadiran harian saja';

                } else if ($status == 'Izin') {
                    $nominal_potongan = $tunj_kehadiran_harian;
                    $status_tampil = 'Izin';
                    $status_badge = 'info';
                    $icon = 'fa-calendar-check-o';
                    $keterangan = 'Potong tunjangan kehadiran harian saja';

                } else {
                    // Other unknown statuses
                    $status_tampil = $status;
                    $status_badge = 'default';
                    $icon = 'fa-info-circle';
                    $keterangan = '-';
                }

                $running_potongan += $nominal_potongan;

                $log_harian[] = [
                    'tanggal' => $date_str,
                    'hari' => $nama_hari,
                    'status' => $status_tampil,
                    'status_badge' => $status_badge,
                    'icon' => $icon,
                    'potongan' => $nominal_potongan,
                    'running_total' => $running_potongan,
                    'keterangan' => $keterangan,
                    'waktu_masuk' => $absen->waktu_masuk ?? null,
                    'waktu_pulang' => $absen->waktu_pulang ?? null
                ];
            }

            // Set notifikasi hari ini (for the alert card)
            $day_of_week = date('N');
            $is_weekend = false;
            if ($hari_kerja_per_minggu == 'senin_jumat' && $day_of_week > 5) $is_weekend = true;
            if ($hari_kerja_per_minggu == 'senin_sabtu' && $day_of_week > 6) $is_weekend = true;
            
            if (!$is_weekend && !in_array($real_today, $hari_libur)) {
                if (isset($absen_map[$real_today])) {
                    $absen_today = $absen_map[$real_today];
                    $status = $absen_today->status_kehadiran;
                    
                    if (in_array($status, ['Hadir', 'Tepat Waktu'])) {
                        $notifikasi_hari_ini = [
                            'status' => '✅ Anda sudah absen masuk hari ini. Semangat bekerja!',
                            'potongan' => 0,
                            'tipe' => 'success'
                        ];
                    } else if (in_array($status, ['Telat', 'Terlambat'])) {
                        $menit_telat = $absen_today->terlambat_menit ?? 0;
                        $persen = 0;
                        if ($menit_telat <= $tier1_menit) $persen = $tier1_persen;
                        else if ($menit_telat <= $tier2_menit) $persen = $tier2_persen;
                        else if ($menit_telat <= $tier3_menit) $persen = $tier3_persen;
                        else $persen = $tier4_persen;
                        $nominal_potongan = $gaji_harian * $persen;
                        $persen_tampil = $persen * 100;
                        $notifikasi_hari_ini = [
                            'status' => "Telat $menit_telat menit ($persen_tampil% dari gaji harian)",
                            'potongan' => $nominal_potongan,
                            'tipe' => 'warning'
                        ];
                    } else if ($status == 'Alpha') {
                        $notifikasi_hari_ini = [
                            'status' => 'Alpha / Mangkir',
                            'potongan' => $gaji_harian + $tunj_tetap_harian + $tunj_kehadiran_harian,
                            'tipe' => 'danger'
                        ];
                    } else if ($status == 'Sakit (Tanpa Surat)') {
                        $is_potong = $pengaturan->potong_gaji_sakit_tanpa_surat ?? 1;
                        $notifikasi_hari_ini = [
                            'status' => 'Sakit (Tanpa Surat)',
                            'potongan' => $is_potong ? ($gaji_harian + $tunj_tetap_harian + $tunj_kehadiran_harian) : 0,
                            'tipe' => 'danger'
                        ];
                    } else if (in_array($status, ['Sakit (Dengan Surat)', 'Izin', 'Izin Khusus', 'Cuti'])) {
                        $notifikasi_hari_ini = [
                            'status' => $status,
                            'potongan' => $tunj_kehadiran_harian,
                            'tipe' => 'info'
                        ];
                    }
                }
            }
            
            $this->data['notifikasi_hari_ini'] = $notifikasi_hari_ini;
            $this->data['log_harian'] = array_reverse($log_harian); // newest first
        }

        // Ambil riwayat 6 payroll terakhir
        $this->db->where('karyawan_id', $karyawan->id);
        $this->db->order_by('periode_bulan', 'DESC');
        $this->db->limit(6);
        $riwayat_payroll = $this->db->get('hr_payroll')->result();

        // INJECT LIVE DATA FOR CURRENT DRAFT PAYROLL
        // Agar dashboard bersifat "Live" untuk bulan berjalan, override data dengan yang terhitung secara live
        if (isset($running_potongan) && isset($jumlah_hadir) && $payroll && $payroll->status == 'Draft' && $payroll->periode_bulan == date('Y-m')) {
            // Recalculate accrued salary based on attendance
            $gaji_harian = $payroll->gaji_harian_pokok ?? 0;
            $tunj_tetap_harian = $payroll->tunj_tetap_harian ?? 0;
            $tunj_kehadiran_harian = $payroll->tunj_kehadiran_harian ?? 0;

            // Hitung hari yang dibayar (Sama dengan logika Hr_payroll)
            // Di sini kita gunakan jumlah hadir fisik + cuti + sakit dengan surat + izin khusus
            $hari_dibayar = $jumlah_hadir + $jumlah_cuti + $jumlah_sakit + $jumlah_izin;
            
            // Hitung ulang akumulasi pendapatan live
            $payroll->gaji_pokok_diterima = $hari_dibayar * $gaji_harian;
            $payroll->tunj_tetap_diterima = $hari_dibayar * $tunj_tetap_harian;
            $payroll->tunj_kehadiran_diterima = $jumlah_hadir * $tunj_kehadiran_harian; // Kehadiran fisik saja
            
            $payroll->gaji_kotor = $payroll->gaji_pokok_diterima + $payroll->tunj_tetap_diterima + $payroll->tunj_kehadiran_diterima;
            
            // Karena sudah diakumulasi, potongan hanyalah denda telat
            // $running_potongan di atas saat ini menjumlahkan denda telat DAN alpha. 
            // Kita perlu memastikan $running_potongan hanya berisi denda telat karena alpha tidak dibayar dari awal.
            // Wait, I will just calculate total denda telat here:
            $total_denda = 0;
            foreach ($log_harian as $log) {
                if (strpos($log['status'], 'Telat') !== false) {
                    $total_denda += $log['potongan'];
                }
            }

            $payroll->total_potongan = $total_denda;
            $payroll->gaji_bersih = $payroll->gaji_kotor - $total_denda;
            
            // Juga override pada riwayat
            foreach($riwayat_payroll as $rp) {
                if ($rp->id == $payroll->id) {
                    $rp->total_potongan = $total_denda;
                    $rp->gaji_bersih = $payroll->gaji_kotor - $total_denda;
                }
            }
        }

        $this->data['riwayat_payroll'] = $riwayat_payroll;

        $this->template->title('Profil Karyawan & Slip Gaji');
		$this->render('backend/standart/administrator/hr_profile/hr_profile_view', $this->data);
	}

    public function update_profile()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $user_id = get_user_data('id');
        $karyawan = $this->db->get_where('hr_karyawan', ['user_id' => $user_id])->row();

        if (!$karyawan) {
            echo json_encode(['success' => false, 'message' => 'Profil tidak ditemukan.']);
            return;
        }

        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required');
        $this->form_validation->set_rules('no_telepon', 'No. Telepon', 'trim|required');
        
        $password = $this->input->post('password');
        if (!empty($password)) {
            $this->form_validation->set_rules('password', 'Password Baru', 'trim|min_length[6]');
            $this->form_validation->set_rules('password_confirm', 'Konfirmasi Password', 'trim|matches[password]');
        }
        
        if ($this->form_validation->run()) {
            $nama_lengkap = $this->input->post('nama_lengkap');
            
            $update_data = [
                'nama_lengkap' => $nama_lengkap,
                'no_telepon' => $this->input->post('no_telepon'),
                'jenis_kelamin' => $this->input->post('jenis_kelamin'),
                'tanggal_lahir' => $this->input->post('tanggal_lahir') ?: NULL,
                'alamat' => $this->input->post('alamat')
            ];

            $this->db->where('id', $karyawan->id);
            if ($this->db->update('hr_karyawan', $update_data)) {
                // Update aauth_users full_name
                $this->db->where('id', $user_id);
                $this->db->update('aauth_users', ['full_name' => $nama_lengkap]);

                // Update password if provided
                if (!empty($password)) {
                    $this->aauth->update_user($user_id, FALSE, $password);
                }

                echo json_encode(['success' => true, 'message' => 'Data profil berhasil diperbarui!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
        }
    }
}
