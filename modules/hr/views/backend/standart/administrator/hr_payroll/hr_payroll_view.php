<style>
    /* Modern Premium Variables */
    :root {
        --primary: #4F46E5;
        --primary-light: #EEF2FF;
        --secondary: #10B981;
        --secondary-light: #ECFDF5;
        --danger: #EF4444;
        --danger-light: #FEF2F2;
        --warning: #F59E0B;
        --warning-light: #FFFBEB;
        --dark: #1E293B;
        --gray: #64748B;
        --light-gray: #F1F5F9;
        --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        --transition: all 0.3s ease;
    }

    .content-header h1 { font-weight: 700; color: var(--dark); font-size: 24px; }
    
    .premium-card {
        background: #fff;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 25px;
        transition: var(--transition);
    }
    
    /* Left Profile Panel */
    .profile-panel {
        background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
        color: white;
        text-align: center;
        padding: 40px 20px 30px;
        position: relative;
    }
    .profile-panel::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('https://www.transparenttextures.com/patterns/cubes.png');
        opacity: 0.1;
    }
    .avatar-circle {
        width: 100px; height: 100px;
        background: rgba(255,255,255,0.2);
        border: 3px solid rgba(255,255,255,0.5);
        border-radius: 50%;
        margin: 0 auto 15px;
        display: flex; align-items: center; justify-content: center;
        font-size: 36px; font-weight: bold; color: white;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        backdrop-filter: blur(4px);
    }
    .profile-name { font-size: 22px; font-weight: 700; margin: 0 0 5px; letter-spacing: 0.5px; }
    .profile-role { font-size: 14px; opacity: 0.9; margin-bottom: 0; font-weight: 500; }
    .profile-badge {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        padding: 4px 12px; border-radius: 20px;
        font-size: 12px; margin-top: 10px; backdrop-filter: blur(4px);
    }

    .thp-box {
        background: white;
        margin: -20px 20px 20px;
        padding: 25px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        position: relative; z-index: 10;
        text-align: center;
    }
    .thp-title { font-size: 13px; color: var(--gray); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
    .thp-amount { font-size: 28px; font-weight: 800; color: var(--secondary); margin: 0; }
    
    .status-badge {
        padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;
    }
    .status-draft { background: var(--warning-light); color: var(--warning); }
    .status-paid { background: var(--secondary-light); color: var(--secondary); }

    /* Right Details Panel */
    .detail-header {
        padding: 20px 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;
    }
    .detail-header h3 { margin: 0; font-size: 18px; font-weight: 700; color: var(--dark); display: flex; align-items: center; gap: 10px;}
    .detail-header h3 i { color: var(--primary); background: var(--primary-light); padding: 8px; border-radius: 8px; font-size: 14px; }
    
    .breakdown-section { padding: 25px; }
    .section-title {
        font-size: 15px; font-weight: 700; color: var(--dark); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;
    }
    .section-title span { background: var(--primary-light); color: var(--primary); width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 12px; }
    
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; margin-bottom: 25px; }
    .modern-table tr { transition: var(--transition); }
    .modern-table tr:hover td { background: var(--light-gray); }
    .modern-table th { color: var(--gray); font-weight: 500; font-size: 14px; padding: 12px 15px; width: 40%; background: transparent; border: none; border-bottom: 1px dashed #e2e8f0; }
    .modern-table td { color: var(--dark); font-weight: 600; font-size: 14px; padding: 12px 15px; text-align: right; border: none; border-bottom: 1px dashed #e2e8f0; }
    .modern-table .sub-text { font-size: 12px; color: var(--gray); font-weight: 400; display: block; margin-top: 2px; }
    
    .total-row td, .total-row th { border-bottom: none !important; background: var(--primary-light); color: var(--primary); font-size: 16px; font-weight: 700; }
    .total-row td { text-align: right; }
    .total-row:hover td { background: var(--primary-light); }

    .deduction-row th, .deduction-row td { color: var(--danger); }
    .deduction-row .sub-text { color: var(--danger); opacity: 0.8; }

    .btn-back { border-radius: 8px; padding: 10px 20px; font-weight: 600; border: 1px solid #e2e8f0; color: var(--gray); transition: var(--transition); }
    .btn-back:hover { background: var(--light-gray); color: var(--dark); }
</style>

<section class="content-header">
   <h1>
      Detail Kalkulasi Payroll
      <small style="color:#777; font-weight:400;">Transparansi perhitungan gaji</small>
   </h1>
   <ol class="breadcrumb" style="background:transparent;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/hr_payroll'); ?>">Rekap Payroll</a></li>
      <li class="active">Detail</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <!-- KIRI: Profil & THP -->
      <div class="col-md-4">
         <div class="premium-card">
            <div class="profile-panel">
               <div class="avatar-circle">
                  <?= strtoupper(substr($payroll->nama_lengkap, 0, 2)); ?>
               </div>
               <h3 class="profile-name"><?= _ent($payroll->nama_lengkap); ?></h3>
               <p class="profile-role"><?= _ent($payroll->jabatan); ?></p>
               <div class="profile-badge"><i class="fa fa-laptop margin-r-5"></i> <?= _ent($payroll->tipe_kerja); ?></div>
            </div>
            
            <div class="thp-box">
                <div class="thp-title">Take Home Pay</div>
                <h2 class="thp-amount">Rp <?= number_format((float)$payroll->gaji_bersih, 0, ',', '.'); ?></h2>
            </div>
            
            <div style="padding: 0 20px 25px;">
                <table style="width:100%; margin-bottom: 20px;">
                    <tr>
                        <td style="color:var(--gray); padding:8px 0; border-bottom:1px solid #f1f5f9;">Periode Gaji</td>
                        <td style="text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;"><?= date('F Y', strtotime($payroll->periode_bulan.'-01')); ?></td>
                    </tr>
                    <tr>
                        <td style="color:var(--gray); padding:8px 0;">Status Slip</td>
                        <td style="text-align:right;">
                            <span class="status-badge <?= $payroll->status == 'Paid' ? 'status-paid' : 'status-draft' ?>">
                                <?= $payroll->status; ?>
                            </span>
                        </td>
                    </tr>
                </table>
                <a href="<?= site_url('administrator/hr_payroll?periode='.$payroll->periode_bulan); ?>" class="btn btn-default btn-block btn-back">
                    <i class="fa fa-arrow-left margin-r-5"></i> Kembali ke Daftar
                </a>
            </div>
         </div>
      </div>

      <!-- KANAN: Breakdown Detail -->
      <div class="col-md-8">
         <div class="premium-card">
            <div class="detail-header">
                <h3><i class="fa fa-pie-chart"></i> Rincian Kalkulasi Sistem</h3>
            </div>
            
            <div class="breakdown-section">
                <!-- 1. Referensi Master -->
                <h4 class="section-title"><span>1</span> Data Gaji Pokok & Tunjangan</h4>
                <table class="modern-table">
                    <tr>
                        <th>Total Hari Kerja Sebulan <span class="sub-text">Berdasarkan jadwal kalender perusahaan</span></th>
                        <td><?= $payroll->hari_kerja_sebulan; ?> Hari</td>
                    </tr>
                    <tr>
                        <th>Gaji Pokok (Bulanan)</th>
                        <td>Rp <?= number_format((float)$payroll->gaji_pokok, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <th>Tunjangan Tetap</th>
                        <td>Rp <?= number_format((float)$payroll->tunjangan_tetap, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <th>Tunjangan Kehadiran (Bulan Penuh)</th>
                        <td>Rp <?= number_format((float)$payroll->tunjangan_kehadiran, 0, ',', '.'); ?></td>
                    </tr>
                </table>

                <!-- 2. Konversi Harian -->
                <h4 class="section-title" style="margin-top:35px;"><span>2</span> Gaji & Tunjangan per Hari</h4>
                <table class="modern-table">
                    <tr>
                        <th>Gaji Pokok per Hari <span class="sub-text">Gaji Pokok ÷ Total Hari Kerja Sebulan</span></th>
                        <td>Rp <?= number_format((float)$payroll->gaji_harian_pokok, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <th>Tunjangan Tetap per Hari</th>
                        <td>Rp <?= number_format((float)$payroll->tunj_tetap_harian, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <th>Tunjangan Kehadiran per Hari</th>
                        <td>Rp <?= number_format((float)$payroll->tunj_kehadiran_harian, 0, ',', '.'); ?></td>
                    </tr>
                </table>

                <!-- 3. Kehadiran -->
                <h4 class="section-title" style="margin-top:35px;"><span>3</span> Catatan Kehadiran Karyawan</h4>
                <table class="modern-table">
                    <tr>
                        <th>Hari Kerja Diakui <span class="sub-text">Hadir Fisik + Izin Resmi + Libur Nasional</span></th>
                        <td><?= $payroll->hari_dibayar_penuh; ?> Hari</td>
                    </tr>
                    <tr>
                        <th>Kehadiran Penuh (Masuk Kantor) <span class="sub-text">Syarat mendapatkan Tunjangan Kehadiran</span></th>
                        <td><?= $payroll->hari_hadir_fisik; ?> Hari</td>
                    </tr>
                    <tr>
                        <th>Tidak Masuk Kerja (Hangus) <span class="sub-text">Tanpa keterangan / Sakit tanpa surat dokter</span></th>
                        <td><?= ($payroll->jumlah_alfa + $payroll->jumlah_sakit_tanpa_surat); ?> Hari</td>
                    </tr>
                    <tr>
                        <th>Jumlah Keterlambatan <span class="sub-text">Mempengaruhi potongan denda</span></th>
                        <td><?= ($payroll->jumlah_terlambat_ringan + $payroll->jumlah_terlambat_berat); ?> Kali</td>
                    </tr>
                </table>

                <!-- 4. Final -->
                <h4 class="section-title" style="margin-top:35px;"><span>4</span> Rincian Gaji Diterima</h4>
                <table class="modern-table" style="background:#f8fafc; border-radius:12px; padding: 15px;">
                    <tr>
                        <th style="border:none; padding-bottom:5px;">Total Gaji Pokok <span class="sub-text">Hari Kerja Diakui × Gaji Pokok per Hari</span></th>
                        <td style="border:none; padding-bottom:5px; color:var(--secondary);">Rp <?= number_format((float)$payroll->gaji_pokok_diterima, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <th style="border:none; padding-bottom:5px;">Total Tunjangan Tetap <span class="sub-text">Hari Kerja Diakui × Tunjangan Tetap per Hari</span></th>
                        <td style="border:none; padding-bottom:5px; color:var(--secondary);">Rp <?= number_format((float)$payroll->tunj_tetap_diterima, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <th style="border-bottom:2px solid #e2e8f0; padding-bottom:15px;">Total Tunjangan Kehadiran <span class="sub-text">Kehadiran Penuh × Tunjangan Kehadiran per Hari</span></th>
                        <td style="border-bottom:2px solid #e2e8f0; padding-bottom:15px; color:var(--secondary);">Rp <?= number_format((float)$payroll->tunj_kehadiran_diterima, 0, ',', '.'); ?></td>
                    </tr>
                    <tr class="total-row" style="background:transparent;">
                        <th style="padding-top:15px; background:transparent;">Total Gaji Sebelum Potongan</th>
                        <td style="padding-top:15px; background:transparent;">Rp <?= number_format((float)$payroll->gaji_kotor, 0, ',', '.'); ?></td>
                    </tr>
                    <tr class="deduction-row">
                        <th style="border-none; padding-bottom:5px;">Potongan Denda Keterlambatan</th>
                        <td style="border-none; padding-bottom:5px;">- Rp <?= number_format((float)$payroll->potongan_terlambat, 0, ',', '.'); ?></td>
                    </tr>
                    <?php if($payroll->potongan_alfa > 0): ?>
                    <tr class="deduction-row">
                        <th style="border-none; padding-bottom:5px;">Potongan Alpha / Mangkir</th>
                        <td style="border-none; padding-bottom:5px;">- Rp <?= number_format((float)$payroll->potongan_alfa, 0, ',', '.'); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($payroll->potongan_izin_sakit > 0): ?>
                    <tr class="deduction-row">
                        <th style="border-none; padding-bottom:5px;">Potongan Izin / Tidak Hadir</th>
                        <td style="border-none; padding-bottom:5px;">- Rp <?= number_format((float)$payroll->potongan_izin_sakit, 0, ',', '.'); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="deduction-row">
                        <th style="border-bottom:2px solid #e2e8f0; padding-bottom:15px; color:#e53e3e; font-weight:700;">Total Semua Potongan</th>
                        <td style="border-bottom:2px solid #e2e8f0; padding-bottom:15px; color:#e53e3e; font-weight:700;">- Rp <?= number_format((float)$payroll->total_potongan, 0, ',', '.'); ?></td>
                    </tr>
                    <tr class="total-row">
                        <th style="border-radius:0 0 0 12px;">Gaji Bersih (Take Home Pay)</th>
                        <td style="border-radius:0 0 12px 0;">Rp <?= number_format((float)$payroll->gaji_bersih, 0, ',', '.'); ?></td>
                    </tr>
                </table>

            </div>
         </div>
      </div>
   </div>
</section>
