<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_payroll extends Admin
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
        if (!$this->aauth->is_admin()) {
            $is_karyawan = $this->db->get_where('hr_karyawan', ['user_id' => get_user_data('id')])->row();
            if ($is_karyawan) {
                redirect('administrator/hr_profile');
            }
        }
		$this->is_allowed('hr_payroll_list');
        
        $periode = $this->input->get('periode') ?: date('Y-m');
        $this->data['periode'] = $periode;

        $this->db->select('hr_payroll.*, hr_karyawan.nama_lengkap, hr_karyawan.nik, hr_karyawan.jabatan, hr_karyawan.tipe_kerja');
        $this->db->from('hr_payroll');
        $this->db->join('hr_karyawan', 'hr_karyawan.id = hr_payroll.karyawan_id');
        $this->db->where('hr_payroll.periode_bulan', $periode);
		$this->data['payrolls'] = $this->db->get()->result();

		$this->template->title('Rekap Payroll (Gaji)');
		$this->render('backend/standart/administrator/hr_payroll/hr_payroll_list', $this->data);
	}

	public function gaji_karyawan()
	{
		$this->is_allowed('hr_payroll_list');
        $this->db->select('id, nama_lengkap, nik, jabatan, departemen, tipe_kerja, gaji_pokok, tunjangan_tetap, tunjangan_kehadiran, tunjangan');
        $this->db->from('hr_karyawan');
        $this->db->order_by('nama_lengkap', 'ASC');
		$all_karyawans = $this->db->get()->result();

        $this->data['karyawans_sudah_diset'] = [];
        $this->data['karyawans_belum_diset'] = [];
        foreach($all_karyawans as $k) {
            if ($k->gaji_pokok > 0) {
                $this->data['karyawans_sudah_diset'][] = $k;
            } else {
                $this->data['karyawans_belum_diset'][] = $k;
            }
        }

		$this->template->title('Master Gaji Karyawan');
		$this->render('backend/standart/administrator/hr_payroll/hr_gaji_karyawan_list', $this->data);
	}

    public function edit_gaji_save($id)
    {
        $this->is_allowed('hr_payroll_list'); // Assuming they have payroll access
        if ($this->input->is_ajax_request()) {
            $gaji_pokok = str_replace(['.', ','], '', $this->input->post('gaji_pokok')) ?: 0;
            $tunjangan_tetap = str_replace(['.', ','], '', $this->input->post('tunjangan_tetap')) ?: 0;
            $tunjangan_kehadiran = str_replace(['.', ','], '', $this->input->post('tunjangan_kehadiran')) ?: 0;

            // Dapatkan karyawan untuk cek tipe kerjanya
            $karyawan = $this->db->get_where('hr_karyawan', ['id' => $id])->row();
            if (!$karyawan) {
                echo json_encode(['success' => false, 'message' => 'Karyawan tidak ditemukan']); return;
            }

            if ($karyawan->tipe_kerja != 'WFO') {
                $tunjangan_kehadiran = 0;
            }

            $tunjangan_total = $tunjangan_tetap + $tunjangan_kehadiran;

            $update = $this->db->where('id', $id)->update('hr_karyawan', [
                'gaji_pokok' => $gaji_pokok,
                'tunjangan_tetap' => $tunjangan_tetap,
                'tunjangan_kehadiran' => $tunjangan_kehadiran,
                'tunjangan' => $tunjangan_total,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($update) {
                echo json_encode(['success' => true, 'message' => 'Gaji berhasil diperbarui']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui gaji']);
            }
        }
    }

    public function tambah_gaji_save()
    {
        $this->is_allowed('hr_payroll_list'); 
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('karyawan_id');
            $gaji_pokok = str_replace(['.', ','], '', $this->input->post('gaji_pokok')) ?: 0;
            $tunjangan_tetap = str_replace(['.', ','], '', $this->input->post('tunjangan_tetap')) ?: 0;
            $tunjangan_kehadiran = str_replace(['.', ','], '', $this->input->post('tunjangan_kehadiran')) ?: 0;

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Pilih karyawan terlebih dahulu']); return;
            }

            $karyawan = $this->db->get_where('hr_karyawan', ['id' => $id])->row();
            if (!$karyawan) {
                echo json_encode(['success' => false, 'message' => 'Karyawan tidak ditemukan']); return;
            }

            if ($karyawan->tipe_kerja != 'WFO') {
                $tunjangan_kehadiran = 0;
            }

            $tunjangan_total = $tunjangan_tetap + $tunjangan_kehadiran;

            $update = $this->db->where('id', $id)->update('hr_karyawan', [
                'gaji_pokok' => $gaji_pokok,
                'tunjangan_tetap' => $tunjangan_tetap,
                'tunjangan_kehadiran' => $tunjangan_kehadiran,
                'tunjangan' => $tunjangan_total,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($update) {
                echo json_encode(['success' => true, 'message' => 'Data gaji berhasil ditambahkan/diperbarui']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan gaji']);
            }
        }
    }

    public function pengaturan()
    {
        $this->is_allowed('hr_payroll_update');
        $this->data['pengaturan'] = $this->db->get('hr_pengaturan')->row();
        
        $this->template->title('Pengaturan Penggajian');
        $this->render('backend/standart/administrator/hr_payroll/hr_pengaturan_payroll_form', $this->data);
    }

    public function pengaturan_save()
    {
        if (!$this->is_allowed('hr_payroll_update', false)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada akses']);
            return;
        }

        $save_data = [
            'hari_kerja_per_minggu' => $this->input->post('hari_kerja_per_minggu'),
            'potong_gaji_sakit_tanpa_surat' => $this->input->post('potong_gaji_sakit_tanpa_surat'),
            'potong_gaji_sakit_dengan_surat' => $this->input->post('potong_gaji_sakit_dengan_surat'),
            'denda_tier1_menit' => $this->input->post('denda_tier1_menit') ?: 15,
            'denda_tier1_persen' => $this->input->post('denda_tier1_persen') ?: 0,
            'denda_tier2_menit' => $this->input->post('denda_tier2_menit') ?: 30,
            'denda_tier2_persen' => $this->input->post('denda_tier2_persen') ?: 5,
            'denda_tier3_menit' => $this->input->post('denda_tier3_menit') ?: 60,
            'denda_tier3_persen' => $this->input->post('denda_tier3_persen') ?: 10,
            'denda_tier4_persen' => $this->input->post('denda_tier4_persen') ?: 25,
            'cap_denda_persen' => $this->input->post('cap_denda_persen') ?: 30,
            'tgl_cutoff_mulai' => $this->input->post('tgl_cutoff_mulai') ?: 21,
            'tgl_cutoff_akhir' => $this->input->post('tgl_cutoff_akhir') ?: 20,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $pengaturan = $this->db->get('hr_pengaturan')->row();
        if ($pengaturan) {
            $this->db->update('hr_pengaturan', $save_data, ['id' => $pengaturan->id]);
        } else {
            // Need default values for the other fields if it's the very first insert, though usually the other page is saved first.
            $save_data['jam_masuk'] = '08:00';
            $save_data['jam_pulang'] = '17:00';
            $save_data['toleransi_terlambat_menit'] = 15;
            $save_data['batas_jam_pulang'] = '20:00';
            $this->db->insert('hr_pengaturan', $save_data);
        }

        set_message('Pengaturan Penggajian berhasil disimpan.', 'success');
        echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_payroll/pengaturan')]);
    }

    public function generate()
    {
        $this->is_allowed('hr_payroll_add');
        $periode = $this->input->post('periode'); // Format: YYYY-MM
        if (!$periode) {
            echo json_encode(['success' => false, 'message' => 'Periode tidak valid.']); return;
        }

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

        // Cek apakah periode sudah ada yang PAID
        $cek_paid = $this->db->get_where('hr_payroll', ['periode_bulan' => $periode, 'status' => 'Paid'])->num_rows();
        if ($cek_paid > 0) {
            echo json_encode(['success' => false, 'message' => 'Gagal generate. Sebagian atau seluruh gaji pada periode ini sudah berstatus PAID (Terkunci).']); return;
        }

        $pengaturan = $this->db->get('hr_pengaturan')->row();
        $hari_kerja_per_minggu = $pengaturan->hari_kerja_per_minggu ?? 'senin_jumat';

        // Cut-Off Logic
        $tgl_mulai = $pengaturan->tgl_cutoff_mulai ?? 21;
        $tgl_akhir = $pengaturan->tgl_cutoff_akhir ?? 20;

        $tahun = substr($periode, 0, 4);
        $bulan = substr($periode, 5, 2);

        if ($tgl_mulai > $tgl_akhir) {
            // Cut-off lintas bulan (misal 21 bulan lalu s/d 20 bulan ini)
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
            // Cut-off dalam bulan yang sama (misal 1 s/d 31)
            $max_days_curr = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $actual_mulai = min($tgl_mulai, $max_days_curr);
            $actual_akhir = min($tgl_akhir, $max_days_curr);
            $start_date = sprintf("%04d-%02d-%02d", $tahun, $bulan, $actual_mulai);
            $end_date = sprintf("%04d-%02d-%02d", $tahun, $bulan, $actual_akhir);
        }

        // Denda Bertingkat
        $tier1_menit = $pengaturan->denda_tier1_menit ?? 15;
        $tier1_persen = ($pengaturan->denda_tier1_persen ?? 0) / 100;
        $tier2_menit = $pengaturan->denda_tier2_menit ?? 30;
        $tier2_persen = ($pengaturan->denda_tier2_persen ?? 5) / 100;
        $tier3_menit = $pengaturan->denda_tier3_menit ?? 60;
        $tier3_persen = ($pengaturan->denda_tier3_persen ?? 10) / 100;
        $tier4_persen = ($pengaturan->denda_tier4_persen ?? 25) / 100;
        $cap_denda_persen = ($pengaturan->cap_denda_persen ?? 30) / 100;

        // Ambil Data Hari Libur Nasional dalam rentang Cut-Off
        $hari_libur = [];
        $this->db->where("tanggal >=", $start_date);
        $this->db->where("tanggal <=", $end_date);
        $libur_db = $this->db->get('hr_hari_libur')->result();
        foreach($libur_db as $ldb) {
            $hari_libur[] = $ldb->tanggal;
        }

        // Build Array Tanggal Cut-Off dan Hitung Hari Kerja Efektif
        $hari_kerja_efektif = 0;
        $tanggal_kerja_array = [];
        
        $begin = new DateTime($start_date);
        $end = new DateTime($end_date);
        $end = $end->modify('+1 day'); // modifier for DatePeriod inclusion
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);

        foreach($daterange as $date){
            $tgl = $date->format("Y-m-d");
            $day_of_week = $date->format('N'); // 1=Mon, 7=Sun
            
            $is_weekend = false;
            if ($hari_kerja_per_minggu == 'senin_jumat' && $day_of_week > 5) $is_weekend = true;
            if ($hari_kerja_per_minggu == 'senin_sabtu' && $day_of_week > 6) $is_weekend = true;

            if (!$is_weekend && !in_array($tgl, $hari_libur)) { 
                $hari_kerja_efektif++;
                $tanggal_kerja_array[] = $tgl;
            }
        }

        if ($hari_kerja_efektif == 0) {
            echo json_encode(['success' => false, 'message' => "Tidak ada hari kerja di bulan ini ($start_date s/d $end_date)."]); return;
        }

        $karyawan = $this->db->get('hr_karyawan')->result();
        
        $this->db->trans_start();
        $this->db->delete('hr_payroll', ['periode_bulan' => $periode, 'status' => 'Draft']);

        foreach ($karyawan as $k) {
            // Semua Komponen Gaji adalah nilai Bulanan, dibagi Hari Kerja Efektif
            $gaji_bulanan = $k->gaji_pokok ?? 0;
            $tunj_tetap_bulanan = $k->tunjangan_tetap ?? 0;
            $tunj_kehadiran_bulanan = $k->tunjangan_kehadiran ?? 0;
            
            $gaji_harian = round($gaji_bulanan / $hari_kerja_efektif);
            $tunj_tetap_harian = round($tunj_tetap_bulanan / $hari_kerja_efektif);
            
            // Tunjangan Kehadiran HANYA untuk WFO
            $tunj_kehadiran_harian = 0;
            if (isset($k->tipe_kerja) && $k->tipe_kerja == 'WFO') {
                $tunj_kehadiran_harian = round($tunj_kehadiran_bulanan / $hari_kerja_efektif);
            }

            // Counters
            $jml_hadir = 0; $jml_alfa = 0; $jml_cuti = 0; $jml_telat = 0;
            $jml_sakit_surat = 0; $jml_sakit_tanpa_surat = 0; $jml_izin_khusus = 0; $jml_izin = 0;
            
            $hari_gapok_dibayar = 0;    // Gapok + Tunj Tetap dibayar
            $hari_kehadiran_dibayar = 0; // Tunj Kehadiran dibayar (hanya hadir fisik)
            $total_denda_telat = 0;

            foreach ($tanggal_kerja_array as $tgl) {
                if ($k->tanggal_bergabung && $tgl < $k->tanggal_bergabung) continue; 

                $absen = $this->db->get_where('hr_absensi', ['karyawan_id' => $k->id, 'tanggal' => $tgl])->row();
                
                if (!$absen) {
                    // Do nothing for empty days to keep data strictly synced with DB
                    continue;
                } else {
                    $status = $absen->status_kehadiran;
                    if ($status == 'Alpha') {
                        $jml_alfa++;
                    } else if ($status == 'Sakit (Tanpa Surat)') {
                        $jml_sakit_tanpa_surat++;
                        $is_potong_tanpa_surat = isset($pengaturan->potong_gaji_sakit_tanpa_surat) ? $pengaturan->potong_gaji_sakit_tanpa_surat : 1; // Default 1 (Hangus)
                        if ($is_potong_tanpa_surat == 0) { // 0 = Tetap Dibayar Penuh (Gapok & Tunjangan)
                            $hari_gapok_dibayar++;
                            $hari_kehadiran_dibayar++;
                        }
                    } else if ($status == 'Sakit (Dengan Surat)') {
                        $jml_sakit_surat++;
                        $is_potong_dengan_surat = isset($pengaturan->potong_gaji_sakit_dengan_surat) ? $pengaturan->potong_gaji_sakit_dengan_surat : 0; // Default 0 (Dibayar Penuh)
                        if ($is_potong_dengan_surat == 0) { // 0 = Tetap Dibayar Penuh (Gapok & Tunjangan)
                            $hari_gapok_dibayar++;
                            $hari_kehadiran_dibayar++;
                        }
                    } else if ($status == 'Cuti') {
                        $jml_cuti++;
                        $hari_gapok_dibayar++;
                        $hari_kehadiran_dibayar++; // Dibayar penuh (termasuk variabel)
                    } else if ($status == 'Izin Khusus') {
                        $jml_izin_khusus++;
                        $hari_gapok_dibayar++;
                        $hari_kehadiran_dibayar++; 
                    } else if ($status == 'Izin') {
                        $jml_izin++;
                        // Izin Pribadi Biasa -> Unpaid leave
                    } else {
                        // Cek Absen Gantung (Tidak absen pulang sampai ganti hari)
                        if (empty($absen->waktu_pulang) && $tgl < date('Y-m-d')) {
                            // Only count as Alpha if they clock in but don't clock out (and we strictly treat it as Alpha)
                            // But usually auto-checkout handles this. We will keep this as it relies on an existing DB record.
                            $jml_alfa++;
                        } else {
                            // Hadir Fisik (Termasuk Telat)
                            $jml_hadir++;
                            $hari_gapok_dibayar++;
                            $hari_kehadiran_dibayar++;
                            
                            if (in_array($status, ['Telat', 'Terlambat'])) {
                                $jml_telat++;
                            }

                            // Hitung denda telat dinamis berdasarkan persentase dari Gaji Harian
                            $menit_telat = $absen->terlambat_menit ?? 0;
                            if ($menit_telat > 0) {
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
                                $total_denda_telat += $gaji_harian * $persen;
                            }
                        }
                    }
                }
            }

            $jml_libur_nasional = count($hari_libur);
            
            $jml_unpaid = $jml_alfa + $jml_sakit_tanpa_surat + $jml_cuti; 
            
            $total_hari_gapok_dibayar = $hari_gapok_dibayar + $jml_libur_nasional;


            // OPSI A: DAILY ACCRUAL MODEL (Sesuai permintaan)
            // Gaji Kotor bertambah setiap harinya sesuai kehadiran
            $gapok_diterima = $hari_gapok_dibayar * $gaji_harian;
            $tunj_tetap_diterima = $hari_gapok_dibayar * $tunj_tetap_harian;
            $tunj_kehadiran_diterima = $hari_kehadiran_dibayar * $tunj_kehadiran_harian;

            // Cap Limit Denda Telat
            $cap_limit = $gaji_bulanan * $cap_denda_persen;
            if ($total_denda_telat > $cap_limit) {
                $total_denda_telat = $cap_limit;
            }

            // Karena gaji sudah bersifat akumulasi harian (bertambah saat hadir),
            // maka hari Alpha/Sakit Tanpa Surat otomatis tidak menambah gaji.
            // Kita tidak perlu lagi memotong dari atas (karena gajinya memang belum ditambahkan).
            // Potongan murni hanya dari DENDA (seperti denda telat).
            
            $potongan_alfa = 0;
            $potongan_izin_sakit = 0;
            
            $total_potongan = $total_denda_telat;

            // Ambil uang lembur manual (akan di-update di Phase 5 SPL)
            $existing = $this->db->get_where('hr_payroll', ['karyawan_id' => $k->id, 'periode_bulan' => $periode])->row();
            $uang_lembur = $existing ? ($existing->uang_lembur ?? 0) : 0;

            $gaji_kotor = $gapok_diterima + $tunj_tetap_diterima + $tunj_kehadiran_diterima + $uang_lembur;
            $gaji_bersih = round($gaji_kotor - $total_potongan);
            if ($gaji_bersih < 0) $gaji_bersih = 0;

            $data_payroll = [
                'karyawan_id' => $k->id,
                'periode_bulan' => $periode,
                'gaji_pokok' => $gaji_bulanan, // Simpan sbg referensi Bulanan
                'tunjangan' => $tunj_tetap_bulanan + $tunj_kehadiran_bulanan, 
                'tunjangan_tetap' => $tunj_tetap_bulanan,
                'tunjangan_kehadiran' => $tunj_kehadiran_bulanan, // Referensi Bulanan
                'hari_kerja_sebulan' => $hari_kerja_efektif,
                'hari_dibayar_penuh' => $total_hari_gapok_dibayar,
                'hari_hadir_fisik' => $hari_kehadiran_dibayar,
                'gaji_harian_pokok' => $gaji_harian,
                'tunjangan_harian' => $tunj_tetap_harian + $tunj_kehadiran_harian,
                'tunj_tetap_harian' => $tunj_tetap_harian,
                'tunj_kehadiran_harian' => $tunj_kehadiran_harian,
                'gaji_pokok_diterima' => $gapok_diterima,
                'tunjangan_diterima' => $tunj_tetap_diterima + $tunj_kehadiran_diterima,
                'tunj_tetap_diterima' => $tunj_tetap_diterima,
                'tunj_kehadiran_diterima' => $tunj_kehadiran_diterima,
                'jumlah_hadir' => $jml_hadir,
                'jumlah_alfa' => $jml_alfa,
                'jumlah_cuti' => $jml_cuti,
                'jumlah_sakit_surat' => $jml_sakit_surat,
                'jumlah_sakit_tanpa_surat' => $jml_sakit_tanpa_surat,
                'jumlah_izin_khusus' => $jml_izin_khusus,
                'jumlah_terlambat_ringan' => $jml_telat,
                'jumlah_terlambat_berat' => 0,
                'jumlah_izin_sakit' => $jml_sakit_surat + $jml_sakit_tanpa_surat + $jml_izin_khusus + $jml_izin + $jml_cuti,
                'potongan_terlambat' => $total_denda_telat,
                'potongan_tidak_hadir' => $potongan_tidak_hadir,
                'potongan_alfa' => $potongan_alfa,
                'potongan_izin_sakit' => $potongan_izin_sakit,
                'total_potongan' => $total_potongan,
                'uang_lembur' => $uang_lembur,
                'gaji_kotor' => $gaji_kotor,
                'gaji_bersih' => $gaji_bersih,
                'status' => 'Draft',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('hr_payroll', $data_payroll);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Gagal generate payroll.']);
        } else {
            set_message("Berhasil meng-generate payroll periode $periode untuk ".count($karyawan)." karyawan. (Cut-Off: $start_date s/d $end_date)", 'success');
            echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_payroll?periode=' . $periode)]);
        }
    }

    public function mark_paid($periode)
    {
        $this->is_allowed('hr_payroll_update');
        $this->db->update('hr_payroll', ['status' => 'Paid'], ['periode_bulan' => $periode]);
        set_message("Payroll periode $periode telah dikunci menjadi PAID.", 'success');
        redirect('administrator/hr_payroll?periode=' . $periode);
    }
    
    public function unlock_paid($periode)
    {
        $this->is_allowed('hr_payroll_update');
        $this->db->update('hr_payroll', ['status' => 'Draft'], ['periode_bulan' => $periode]);
        set_message("Gembok Payroll periode $periode berhasil DIBUKA (kembali menjadi Draft). Anda sekarang bisa Generate Ulang.", 'success');
        redirect('administrator/hr_payroll?periode=' . $periode);
    }
    
    public function view($id)
    {
        $this->is_allowed('hr_payroll_list');
        $this->db->select('hr_payroll.*, hr_karyawan.nama_lengkap, hr_karyawan.nik, hr_karyawan.jabatan, hr_karyawan.tipe_kerja');
        $this->db->from('hr_payroll');
        $this->db->join('hr_karyawan', 'hr_karyawan.id = hr_payroll.karyawan_id');
        $this->db->where('hr_payroll.id', $id);
        $this->data['payroll'] = $this->db->get()->row();

        if (!$this->data['payroll']) {
            show_404();
        }

        $this->template->title('Detail Kalkulasi Gaji - ' . $this->data['payroll']->nama_lengkap);
        $this->render('backend/standart/administrator/hr_payroll/hr_payroll_view', $this->data);
    }

    public function cetak_slip($id)
    {
        $is_karyawan = false;
        $karyawan_id_login = 0;
        
        if (!$this->aauth->is_admin()) {
            $k_row = $this->db->get_where('hr_karyawan', ['user_id' => get_user_data('id')])->row();
            if ($k_row) {
                $is_karyawan = true;
                $karyawan_id_login = $k_row->id;
            } else {
                $this->is_allowed('hr_payroll_view');
            }
        } else {
            $this->is_allowed('hr_payroll_view');
        }

        $this->db->select('hr_payroll.*, hr_karyawan.nama_lengkap, hr_karyawan.nik, hr_karyawan.jabatan, hr_karyawan.departemen, hr_karyawan.tipe_kerja');
        $this->db->from('hr_payroll');
        $this->db->join('hr_karyawan', 'hr_karyawan.id = hr_payroll.karyawan_id');
        $this->db->where('hr_payroll.id', $id);
        
        // Jika login sebagai karyawan, HANYA BISA LIHAT SLIP SENDIRI
        if ($is_karyawan) {
            $this->db->where('hr_payroll.karyawan_id', $karyawan_id_login);
        }
        
		$this->data['slip'] = $this->db->get()->row();
        if(!$this->data['slip']) show_404();

		$this->template->title('Slip Gaji - ' . $this->data['slip']->nama_lengkap);
		$this->render('backend/standart/administrator/hr_payroll/hr_payroll_slip', $this->data);
    }

    /**
     * Cetak slip gaji SEMUA karyawan dalam 1 halaman (untuk print massal).
     */
    public function cetak_semua($periode = null)
    {
        $this->is_allowed('hr_payroll_view');
        if (!$periode) $periode = date('Y-m');

        $this->db->select('hr_payroll.*, hr_karyawan.nama_lengkap, hr_karyawan.nik, hr_karyawan.jabatan, hr_karyawan.departemen, hr_karyawan.tipe_kerja');
        $this->db->from('hr_payroll');
        $this->db->join('hr_karyawan', 'hr_karyawan.id = hr_payroll.karyawan_id');
        $this->db->where('hr_payroll.periode_bulan', $periode);
        $this->db->order_by('hr_karyawan.nama_lengkap', 'ASC');
        $this->data['slips'] = $this->db->get()->result();
        $this->data['periode'] = $periode;

        if (empty($this->data['slips'])) {
            set_message('Tidak ada data payroll untuk periode ' . $periode, 'warning');
            redirect('administrator/hr_payroll?periode=' . $periode);
        }

        $this->template->title('Cetak Semua Slip Gaji - ' . $periode);
        $this->render('backend/standart/administrator/hr_payroll/hr_payroll_slip_semua', $this->data);
    }

    public function edit($id)
    {
        $this->is_allowed('hr_payroll_update');
        
        $this->db->select('hr_payroll.*, hr_karyawan.nama_lengkap, hr_karyawan.nik, hr_karyawan.jabatan');
        $this->db->from('hr_payroll');
        $this->db->join('hr_karyawan', 'hr_karyawan.id = hr_payroll.karyawan_id');
        $this->db->where('hr_payroll.id', $id);
        $this->db->where('hr_payroll.status', 'Draft'); // Hanya bisa diedit jika Draft
        $this->data['payroll'] = $this->db->get()->row();

        if (!$this->data['payroll']) {
            set_message('Data payroll tidak ditemukan atau sudah berstatus PAID (terkunci).', 'warning');
            redirect('administrator/hr_payroll');
        }

        $this->template->title('Edit Manual Payroll - ' . $this->data['payroll']->nama_lengkap);
        $this->render('backend/standart/administrator/hr_payroll/hr_payroll_edit', $this->data);
    }

    public function edit_save($id)
    {
        $this->is_allowed('hr_payroll_update');
        
        $payroll = $this->db->get_where('hr_payroll', ['id' => $id, 'status' => 'Draft'])->row();
        if (!$payroll) {
            echo json_encode(['success' => false, 'message' => 'Data payroll tidak ditemukan atau sudah dikunci.']); return;
        }

        if ($this->input->is_ajax_request()) {
            $gaji_pokok_diterima = str_replace(['.', ','], '', $this->input->post('gaji_pokok_diterima')) ?: 0;
            $tunj_tetap_diterima = str_replace(['.', ','], '', $this->input->post('tunj_tetap_diterima')) ?: 0;
            $tunj_kehadiran_diterima = str_replace(['.', ','], '', $this->input->post('tunj_kehadiran_diterima')) ?: 0;
            $uang_lembur = str_replace(['.', ','], '', $this->input->post('uang_lembur')) ?: 0;
            
            $potongan_alfa = str_replace(['.', ','], '', $this->input->post('potongan_alfa')) ?: 0;
            $potongan_terlambat = str_replace(['.', ','], '', $this->input->post('potongan_terlambat')) ?: 0;
            $potongan_izin_sakit = str_replace(['.', ','], '', $this->input->post('potongan_izin_sakit')) ?: 0;

            // Recalculate
            $tunjangan_diterima = $tunj_tetap_diterima + $tunj_kehadiran_diterima;
            $gaji_kotor = $gaji_pokok_diterima + $tunjangan_diterima + $uang_lembur;
            
            $potongan_tidak_hadir = $potongan_alfa + $potongan_izin_sakit;
            $total_potongan = $potongan_terlambat + $potongan_tidak_hadir;

            $gaji_bersih = $gaji_kotor - $total_potongan;
            if ($gaji_bersih < 0) $gaji_bersih = 0;

            $update_data = [
                'gaji_pokok_diterima' => $gaji_pokok_diterima,
                'tunj_tetap_diterima' => $tunj_tetap_diterima,
                'tunj_kehadiran_diterima' => $tunj_kehadiran_diterima,
                'tunjangan_diterima' => $tunjangan_diterima,
                'uang_lembur' => $uang_lembur,
                'gaji_kotor' => $gaji_kotor,
                'potongan_alfa' => $potongan_alfa,
                'potongan_terlambat' => $potongan_terlambat,
                'potongan_izin_sakit' => $potongan_izin_sakit,
                'potongan_tidak_hadir' => $potongan_tidak_hadir,
                'total_potongan' => $total_potongan,
                'gaji_bersih' => $gaji_bersih,
            ];

            $update = $this->db->update('hr_payroll', $update_data, ['id' => $id]);

            if ($update) {
                set_message('Perubahan nilai payroll berhasil disimpan.', 'success');
                echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_payroll?periode=' . $payroll->periode_bulan)]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem saat menyimpan.']);
            }
        }
    }
}
