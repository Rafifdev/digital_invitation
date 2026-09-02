<section class="content-header">
    <h1 style="font-size: 22px; font-weight: 600; color: #333;">Portal Karyawan & Slip Gaji <small style="font-weight: 400; color: #888; font-size: 13px;">Employee Self-Service (ESS) & Dashboard Pribadi</small></h1>
    <ol class="breadcrumb" style="background: transparent;">
        <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">User</a></li>
        <li class="active">Profile</li>
    </ol>
</section>

<style>
    /* Profile Left Column */
    .profile-card-left { background: #0f3b73; color: white; border-radius: 10px 10px 0 0; padding: 30px 20px; text-align: center; }
    .profile-avatar-new { width: 100px; height: 100px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.3); object-fit: cover; margin-bottom: 15px; }
    .badge-custom { font-size: 11px; padding: 4px 10px; border-radius: 4px; display: inline-block; font-weight: 500; }
    .badge-blue { background: #1976d2; color: #fff; }
    .badge-dark { background: #34495e; color: #fff; }
    .badge-green { background: #00c853; color: #fff; }

    .info-section { background: #fff; padding: 20px; border-bottom: 1px solid #f0f0f0; }
    .info-section-title { font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; margin-bottom: 15px; display: flex; align-items: center; letter-spacing: 0.5px; }
    .info-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; font-size: 13px; gap: 10px; }
    .info-label { color: #888; white-space: nowrap; flex-shrink: 0; }
    .info-value { font-weight: 600; color: #333; text-align: right; word-break: break-word; }

    /* Action Buttons */
    .btn-action { width: 100%; border-radius: 6px; padding: 10px; font-weight: 600; margin-bottom: 10px; transition: all 0.3s ease; cursor: pointer; }
    .btn-action-blue { background: #1976d2; color: #fff; border: none; }
    .btn-action-blue:hover { background: #1565c0; color: #fff; }
    .btn-action-outline { background: #fff; color: #555; border: 1px solid #ddd; }
    .btn-action-outline:hover { background: #f5f5f5; color: #333; }

    /* THP Banner */
    .thp-banner { background: linear-gradient(135deg, #2ecc71, #27ae60); border-radius: 10px; padding: 25px 30px; color: #fff; position: relative; overflow: hidden; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(46,204,113,0.25); }
    .thp-banner-title { font-size: 12px; font-weight: 700; letter-spacing: 1.5px; opacity: 0.95; margin-bottom: 8px; }
    .thp-banner-value { font-size: 36px; font-weight: 800; margin-bottom: 12px; line-height: 1.2; }
    .thp-banner-periode { font-size: 13px; opacity: 0.9; }
    .btn-slip-pdf { background: #fff; color: #e74c3c; border-radius: 6px; padding: 8px 16px; font-weight: 600; text-decoration: none !important; display: inline-block; margin-bottom: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.12); font-size: 12px; transition: all 0.2s; }
    .btn-slip-pdf:hover { background: #fefefe; color: #c0392b; box-shadow: 0 4px 12px rgba(0,0,0,0.18); }
    .btn-rincian { color: rgba(255,255,255,0.85); font-size: 12px; text-decoration: none !important; transition: color 0.2s; }
    .btn-rincian:hover { color: #fff; }

    /* Stat Cards */
    .stat-card { background: #fff; border-radius: 10px; padding: 18px 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); border: 1px solid #f0f0f0; margin-bottom: 20px; min-height: 105px; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; }
    .stat-card .stat-title { font-size: 10px; font-weight: 700; color: #777; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.5px; }
    .stat-card .stat-value { font-size: 22px; font-weight: 800; line-height: 1.2; }
    .stat-card .stat-desc { font-size: 11px; color: #999; margin-top: 4px; }
    .stat-card .stat-icon { position: absolute; right: 12px; top: 12px; font-size: 22px; opacity: 0.15; }

    .border-green { border-left: 4px solid #2ecc71; }
    .border-yellow { border-left: 4px solid #f1c40f; }
    .border-blue { border-left: 4px solid #3498db; }
    .border-red { border-left: 4px solid #e74c3c; }

    /* Alert Card */
    .alert-card { background: #fffdf2; border-left: 6px solid #ffb822; border-radius: 8px; padding: 18px 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: center; gap: 20px; }

    /* Box Widget */
    .box-widget { background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); border: 1px solid #f0f0f0; margin-bottom: 20px; }
    .box-title { font-size: 14px; font-weight: 700; color: #333; margin-bottom: 20px; display: flex; align-items: center; }

    /* History Table */
    .table-history th { background: #1976d2; color: #fff; font-size: 11px; text-transform: uppercase; border: none !important; padding: 12px 15px !important; letter-spacing: 0.3px; }
    .table-history td { vertical-align: middle !important; font-size: 13px; padding: 12px 15px !important; border-top: 1px solid #f0f0f0 !important; font-weight: 500; }

    /* Daily Log Table */
    .table-log th { background: #263238; color: #fff; font-size: 10px; text-transform: uppercase; border: none !important; padding: 10px 12px !important; letter-spacing: 0.5px; }
    .table-log td { vertical-align: middle !important; font-size: 12px; padding: 10px 12px !important; border-top: 1px solid #f5f5f5 !important; font-weight: 500; }
    .table-log tr.row-danger { background: #fff5f5; }
    .badge-status { padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block; }
    .badge-status.bg-success { background: #e8f5e9; color: #2e7d32; }
    .badge-status.bg-warning { background: #fff8e1; color: #f57f17; }
    .badge-status.bg-danger { background: #ffebee; color: #c62828; }
    .badge-status.bg-info { background: #e3f2fd; color: #1565c0; }
    .badge-status.bg-default { background: #f5f5f5; color: #999; }
    .alert-card-success { background: #f0faf0; border-left: 6px solid #2ecc71; border-radius: 8px; padding: 18px 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: center; gap: 20px; }
</style>

<section class="content">
    <div class="row">
        <!-- LEFT COLUMN (Profile Info) -->
        <div class="col-md-4">
            <!-- Header Profile -->
            <div class="profile-card-left">
                <?php 
                    $user_avatar = get_user_data('avatar');
                    $avatar_file_path = FCPATH . 'uploads/user/' . $user_avatar;
                    if (!empty($user_avatar) && is_file($avatar_file_path)) {
                        $avatar_src = BASE_URL . 'uploads/user/' . $user_avatar;
                    } else {
                        $avatar_src = BASE_ASSET . 'admin-lte/dist/img/avatar.png';
                    }
                ?>
                <img src="<?= $avatar_src; ?>" class="profile-avatar-new" alt="User Avatar" onerror="this.onerror=null; this.src='<?= BASE_ASSET; ?>admin-lte/dist/img/avatar.png';">
                <h3 style="margin: 0 0 5px 0; font-weight: 700; font-size: 20px;"><?= _ent($karyawan->nama_lengkap) ?></h3>
                <p style="margin: 0 0 15px 0; font-size: 13px; opacity: 0.9;"><i class="fa fa-briefcase margin-r-5"></i> <?= _ent($karyawan->jabatan) ?></p>
                <div>
                    <span class="badge-custom badge-blue margin-r-5"><?= _ent($karyawan->departemen) ?></span>
                    <span class="badge-custom badge-dark margin-r-5"><?= _ent($karyawan->nik) ?></span>
                    <span class="badge-custom badge-green"><i class="fa fa-check-circle"></i> Aktif</span>
                </div>
            </div>
            
            <!-- Penempatan & Shift Kerja -->
            <div class="info-section">
                <div class="info-section-title"><i class="fa fa-building-o margin-r-5"></i> Penempatan & Shift Kerja</div>
                <div class="info-row">
                    <span class="info-label"><i class="fa fa-map-marker margin-r-5 text-muted"></i> Kantor Default:</span>
                    <span class="info-value">IDMETAFORA ERP Yogyakarta<br>Office</span>
                </div>
                <div class="info-row" style="margin-top: 15px;">
                    <span class="info-label"><i class="fa fa-clock-o margin-r-5 text-muted"></i> Jadwal Shift:</span>
                    <span class="info-value">
                        <?= _ent($karyawan->nama_shift ?: 'Shift Regular') ?><br>
                        <small class="text-muted">(<?= substr($karyawan->jam_masuk ?? '08:30', 0, 5) ?> - <?= substr($karyawan->jam_pulang ?? '16:00', 0, 5) ?> WIB)</small>
                    </span>
                </div>
                <div class="info-row" style="margin-top: 15px;">
                    <span class="info-label"><i class="fa fa-shield margin-r-5 text-muted"></i> Mode Kerja:</span>
                    <span class="info-value"><?= _ent($karyawan->tipe_kerja ?: 'WFO, WFH, WFA') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa fa-envelope-o margin-r-5 text-muted"></i> Email Akun:</span>
                    <span class="info-value"><?= get_user_data('email') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa fa-phone margin-r-5 text-muted"></i> Nomor HP:</span>
                    <span class="info-value"><?= _ent($karyawan->no_telepon ?: '-') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa fa-users margin-r-5 text-muted"></i> Role / Grup:</span>
                    <?php 
                        $role_label = !empty($karyawan->group_name) ? $karyawan->group_name : (($fg = get_user_first_group()) ? $fg->name : ($karyawan->jabatan ?: 'Karyawan'));
                    ?>
                    <span class="info-value"><?= _ent($role_label); ?></span>
                </div>
                
                <div style="margin-top: 25px;">
                    <a href="<?= site_url('administrator/hr_absensi') ?>" class="btn btn-action btn-action-blue"><i class="fa fa-camera margin-r-5"></i> Presensi Mandiri (Selfie + GPS)</a>
                    <div class="row">
                        <div class="col-xs-6" style="padding-right: 5px;">
                            <a href="<?= site_url('administrator/hr_absensi') ?>" class="btn btn-action btn-action-outline"><i class="fa fa-calendar-plus-o margin-r-5 text-info"></i> Ajukan Izin</a>
                        </div>
                        <div class="col-xs-6" style="padding-left: 5px;">
                            <button class="btn btn-action btn-action-outline" data-toggle="modal" data-target="#modalEditProfile"><i class="fa fa-pencil margin-r-5 text-warning"></i> Edit Profil</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Keamanan & Aktivitas Akun -->
            <div class="info-section" style="border-radius: 0 0 10px 10px; border-bottom: none;">
                <div class="info-section-title"><i class="fa fa-lock margin-r-5"></i> Keamanan & Aktivitas Akun</div>
                <div class="info-row">
                    <span class="info-label"><i class="fa fa-history margin-r-5 text-muted"></i> Login Terakhir</span>
                    <span class="info-value" style="font-weight: 500; font-size: 12px;"><?= date('Y-m-d H:i:s') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa fa-globe margin-r-5 text-muted"></i> IP Address Terakhir</span>
                    <span class="info-value" style="font-weight: 500; font-size: 12px;">127.0.0.1</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fa fa-calendar margin-r-5 text-muted"></i> Tanggal Terdaftar</span>
                    <span class="info-value" style="font-weight: 500; font-size: 12px;"><?= date('d M Y', strtotime($karyawan->created_at ?? '2026-08-08')) ?></span>
                </div>
            </div>
        </div>
        
        <!-- RIGHT COLUMN (Dashboard Widgets) -->
        <div class="col-md-8">
            
            <!-- THP BANNER -->
            <?php if($payroll_terakhir): ?>
                <?php 
                    $total_pendapatan = (float)$payroll_terakhir->gaji_pokok + (float)$payroll_terakhir->tunjangan_tetap + (float)$payroll_terakhir->tunjangan_kehadiran;
                    $total_potongan = (float)$payroll_terakhir->total_potongan;
                    $thp_bersih = (float)$payroll_terakhir->gaji_bersih;
                ?>
                <div class="thp-banner">
                    <div style="float: right; text-align: right;">
                        <?php if($payroll_terakhir->status == 'Paid'): ?>
                            <a href="<?= site_url('administrator/hr_payroll/cetak_slip/'.$payroll_terakhir->id) ?>?download=true" target="_blank" class="btn-slip-pdf"><i class="fa fa-file-pdf-o margin-r-5"></i> Unduh Slip Gaji (PDF)</a>
                        <?php endif; ?>
                        <br>
                        <a href="#" style="color: rgba(255,255,255,0.8); font-size: 12px; text-decoration: none;"><i class="fa fa-eye margin-r-5"></i> Rincian Komponen</a>
                    </div>
                    
                    <div class="thp-banner-title"><i class="fa fa-money margin-r-5"></i> TAKE HOME PAY (THP)</div>
                    <div class="thp-banner-value">Rp <?= number_format($thp_bersih, 0, ',', '.') ?></div>
                    <div class="thp-banner-periode">
                        <?php if($payroll_terakhir->status == 'Paid'): ?>
                            <span style="background: rgba(0,0,0,0.2); padding: 3px 8px; border-radius: 4px; font-size: 11px; margin-left: 10px;"><i class="fa fa-check-circle margin-r-5"></i> Slip Gaji Final Terbit</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Decorative Background -->
                    <i class="fa fa-money" style="position: absolute; right: -20px; bottom: -30px; font-size: 150px; opacity: 0.1; transform: rotate(-15deg);"></i>
                </div>
            <?php else: ?>
                <div class="thp-banner" style="background: linear-gradient(135deg, #7f8c8d, #95a5a6);">
                    <div class="thp-banner-title"><i class="fa fa-money margin-r-5"></i> TAKE HOME PAY (THP)</div>
                    <div class="thp-banner-value">Belum Ada Data</div>
                    <div class="thp-banner-periode"><i class="fa fa-info-circle margin-r-5"></i> Data gaji Anda akan muncul di sini setelah diproses oleh HRD.</div>
                </div>
            <?php endif; ?>
            
            <!-- ABSENSI STATS ROW -->
            <div class="row" style="margin-left: -10px; margin-right: -10px;">
                <div class="col-md-3 col-xs-6" style="padding-left: 10px; padding-right: 10px;">
                    <div class="stat-card border-green">
                        <div class="stat-title text-success">Hadir Efektif</div>
                        <div class="stat-value text-success"><?= $absensi ? $absensi['hadir'] : 0 ?> <span style="font-size:14px; font-weight:600; color:#333;">Hari</span></div>
                        <div class="stat-desc">Dari <?= $absensi ? $absensi['total_hari_kerja'] : 0 ?> hari kerja</div>
                        <i class="fa fa-check-circle text-success stat-icon"></i>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6" style="padding-left: 10px; padding-right: 10px;">
                    <div class="stat-card border-yellow">
                        <div class="stat-title text-warning">Terlambat</div>
                        <div class="stat-value text-warning"><?= $absensi ? $absensi['telat'] : 0 ?> <span style="font-size:14px; font-weight:600; color:#333;">Kali</span></div>
                        <div class="stat-desc"><?= $absensi ? $absensi['total_menit_telat'] : 0 ?> menit total</div>
                        <i class="fa fa-clock-o text-warning stat-icon"></i>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6" style="padding-left: 10px; padding-right: 10px;">
                    <div class="stat-card border-blue">
                        <div class="stat-title text-info" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Izin / Cuti / Sakit</div>
                        <div class="stat-value text-info"><?= $absensi ? ($absensi['sakit'] + $absensi['izin'] + $absensi['cuti']) : 0 ?> <span style="font-size:14px; font-weight:600; color:#333;">Hari</span></div>
                        <div class="stat-desc">Izin: <?= $absensi ? $absensi['izin'] : 0 ?> | Sakit: <?= $absensi ? $absensi['sakit'] : 0 ?> | Cuti: <?= $absensi ? $absensi['cuti'] : 0 ?></div>
                        <i class="fa fa-calendar-check-o text-info stat-icon"></i>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6" style="padding-left: 10px; padding-right: 10px;">
                    <div class="stat-card border-red">
                        <div class="stat-title text-danger">Alpa / Absen</div>
                        <div class="stat-value text-danger"><?= $absensi ? $absensi['alfa'] : 0 ?> <span style="font-size:14px; font-weight:600; color:#333;">Hari</span></div>
                        <div class="stat-desc">Tanpa keterangan</div>
                        <i class="fa fa-times-circle text-danger stat-icon"></i>
                    </div>
                </div>
            </div>
            
            <!-- DAILY ALERT ROW -->
            <?php if(!empty($notifikasi_hari_ini)): ?>
                <?php if($notifikasi_hari_ini['tipe'] == 'success'): ?>
                    <div class="alert-card-success">
                        <div>
                            <span style="display: inline-block; background: #2ecc71; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-bottom: 8px;">
                                <i class="fa fa-check-circle margin-r-5"></i> Status Kehadiran Hari Ini
                            </span>
                            <span style="margin-left: 10px; font-size: 13px; color: #666; font-weight: 500;"><i class="fa fa-calendar margin-r-5"></i> <?= date('d F Y') ?></span>
                            <h4 style="margin: 5px 0 0 0; font-weight: 700; font-size: 16px; color: #2e7d32;">
                                <?= $notifikasi_hari_ini['status'] ?>
                            </h4>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 11px; font-weight: 700; color: #2e7d32; text-transform: uppercase;">Potongan:</div>
                            <div style="font-size: 24px; font-weight: 800; color: #2ecc71; margin-top: 5px;">Rp 0</div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert-card">
                        <div>
                            <span style="display: inline-block; background: <?= $notifikasi_hari_ini['tipe'] == 'danger' ? '#e74c3c' : ($notifikasi_hari_ini['tipe'] == 'warning' ? '#ffb822' : '#3498db') ?>; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-bottom: 8px;">
                                <?php if($notifikasi_hari_ini['tipe'] == 'danger'): ?>
                                    <i class="fa fa-times-circle margin-r-5"></i>
                                <?php elseif($notifikasi_hari_ini['tipe'] == 'warning'): ?>
                                    <i class="fa fa-exclamation-triangle margin-r-5"></i>
                                <?php else: ?>
                                    <i class="fa fa-info-circle margin-r-5"></i>
                                <?php endif; ?>
                                <?= strpos(strtolower($notifikasi_hari_ini['status']), 'telat') !== false ? 'Denda Keterlambatan' : 'Pemberitahuan Absensi' ?>
                            </span>
                            <span style="margin-left: 10px; font-size: 13px; color: #666; font-weight: 500;"><i class="fa fa-calendar margin-r-5"></i> <?= date('d F Y') ?></span>
                            
                            <h4 style="margin: 5px 0 0 0; font-weight: 700; font-size: 16px; color: #333;">
                                <?= $notifikasi_hari_ini['status'] ?>
                            </h4>
                        </div>
                        <?php if($notifikasi_hari_ini['potongan'] > 0): ?>
                        <div style="text-align: right;">
                            <div style="font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase;">Nominal Potongan:</div>
                            <div style="font-size: 24px; font-weight: 800; color: #e74c3c; margin-top: 5px;">
                                - Rp <?= number_format(round($notifikasi_hari_ini['potongan']), 0, ',', '.') ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <!-- CHARTS & RINCIAN KOMPONEN ROW -->
            <?php if($payroll_terakhir): ?>
            <div class="row" style="margin-left: -10px; margin-right: -10px;">
                <div class="col-md-6" style="padding-left: 10px; padding-right: 10px;">
                    <div class="box-widget">
                        <div class="box-title"><i class="fa fa-pie-chart text-primary margin-r-5"></i> Komposisi Pendapatan vs Potongan</div>
                        <div style="width: 100%; max-width: 220px; margin: 0 auto; position: relative; height: 220px;">
                            <canvas id="komposisiChart"></canvas>
                        </div>
                        <div class="row" style="margin-top: 25px; text-align: center;">
                            <div class="col-xs-6">
                                <div style="font-weight: 600; color: #333; font-size: 12px;"><i class="fa fa-circle text-success margin-r-5"></i> Total Pendapatan</div>
                                <div style="color: #2ecc71; font-weight: 700; font-size: 15px; margin-top: 5px;">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
                            </div>
                            <div class="col-xs-6">
                                <div style="font-weight: 600; color: #333; font-size: 12px;"><i class="fa fa-circle text-danger margin-r-5"></i> Total Potongan</div>
                                <div style="color: #ef5350; font-weight: 700; font-size: 15px; margin-top: 5px;">Rp <?= number_format($total_potongan, 0, ',', '.') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6" style="padding-left: 10px; padding-right: 10px;">
                    <div class="box-widget" style="padding: 0; display: flex; flex-direction: column;">
                        <div class="box-title" style="padding: 20px 20px 0 20px; margin-bottom: 15px;"><i class="fa fa-list-alt text-primary margin-r-5"></i> Rincian Komponen Gaji</div>
                        <div style="flex: 1; padding: 0 20px;">
                            <table style="width: 100%; font-size: 13px;">
                                <tbody>
                                    <tr>
                                        <td style="padding: 12px 0; color: #555; border-bottom: 1px solid #f9f9f9;">Gaji Pokok</td>
                                        <td style="padding: 12px 0; text-align: right; font-weight: 600; border-bottom: 1px solid #f9f9f9;">Rp <?= number_format(isset($payroll_terakhir->gaji_pokok_diterima) ? (float)$payroll_terakhir->gaji_pokok_diterima : (float)$payroll_terakhir->gaji_pokok, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; color: #555; border-bottom: 1px solid #f9f9f9;">Tunjangan Tetap</td>
                                        <td style="padding: 12px 0; text-align: right; font-weight: 600; border-bottom: 1px solid #f9f9f9;">Rp <?= number_format(isset($payroll_terakhir->tunj_tetap_diterima) ? (float)$payroll_terakhir->tunj_tetap_diterima : (float)$payroll_terakhir->tunjangan_tetap, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; color: #555; border-bottom: 1px solid #f9f9f9;">Tunjangan Variabel (Kehadiran)</td>
                                        <td style="padding: 12px 0; text-align: right; font-weight: 600; border-bottom: 1px solid #f9f9f9;">Rp <?= number_format(isset($payroll_terakhir->tunj_kehadiran_diterima) ? (float)$payroll_terakhir->tunj_kehadiran_diterima : (float)$payroll_terakhir->tunjangan_kehadiran, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; color: #555; border-bottom: 1px solid #f9f9f9;">Total Potongan (Denda / Pajak / Kasbon)</td>
                                        <td style="padding: 12px 0; text-align: right; font-weight: 600; color: #e74c3c; border-bottom: 1px solid #f9f9f9;">- Rp <?= number_format($total_potongan, 0, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div style="background: #e8f5e9; padding: 15px 20px; border-radius: 0 0 10px 10px; display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                            <span style="color: #1b5e20; font-weight: 700; font-size: 14px;">Take Home Pay (Bersih)</span>
                            <span style="font-size: 16px; font-weight: 800; color: #1b5e20;">Rp <?= number_format($thp_bersih, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
        </div>
    </div>

    <!-- FULL WIDTH: Riwayat Potongan & Riwayat Slip -->
    <div class="row" style="margin-top: 10px;">
        
        <!-- KOLOM KIRI: Riwayat Potongan Harian -->
        <div class="col-md-12 col-lg-6" style="display: flex; flex-direction: column;">
            <?php if(!empty($log_harian)): ?>
                <div class="box box-warning" style="border-top-color: #f39c12; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); flex: 1; margin-bottom: 30px;">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="font-size: 14px; font-weight: 700;"><i class="fa fa-calendar-check-o margin-r-5"></i> Riwayat Kehadiran (Bulan Ini)</h3>
                        <div class="box-tools pull-right">
                            <span style="font-size: 11px; color: #999;">Log Potongan</span>
                        </div>
                    </div>
                <div class="table-responsive">
                    <table class="table table-log" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th style="width: 30px;">#</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th style="text-align: right;">Potongan</th>
                                <th style="text-align: right;">Akumulasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($log_harian as $log): ?>
                            <tr class="<?= $log['status_badge'] == 'danger' ? 'row-danger' : '' ?>">
                                <td style="color: #bbb; font-size: 11px;"><?= $no++ ?></td>
                                <td style="font-weight: 600; font-size: 12px; white-space: nowrap;">
                                    <?= date('d M', strtotime($log['tanggal'])) ?><br>
                                    <small style="color:#888;"><?= $log['hari'] ?></small>
                                </td>
                                <td>
                                    <span class="badge-status bg-<?= $log['status_badge'] ?>" style="margin-bottom: 4px; display: inline-block;">
                                        <i class="fa <?= $log['icon'] ?> margin-r-5"></i><?= $log['status'] ?>
                                    </span>
                                </td>
                                <td style="text-align: right; font-weight: 600; <?= $log['potongan'] > 0 ? 'color: #e74c3c;' : 'color: #ccc;' ?>">
                                    <?php if($log['potongan'] > 0): ?>
                                        - Rp <?= number_format(round($log['potongan']), 0, ',', '.') ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; font-weight: 700; font-size: 12px; color: #e74c3c;">
                                    Rp <?= number_format(round($log['running_total']), 0, ',', '.') ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background: #fff3e0;">
                                <td colspan="3" style="font-weight: 700; color: #e65100; font-size: 12px !important;"><i class="fa fa-calculator margin-r-5"></i> TOTAL POTONGAN</td>
                                <td colspan="2" style="text-align: right; font-weight: 800; color: #e74c3c; font-size: 14px !important;">
                                    - Rp <?= number_format(round($log_harian[0]['running_total'] ?? 0), 0, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php else: ?>
            <div class="box-widget" style="padding: 30px; text-align: center; margin-bottom: 30px; flex: 1;">
                <i class="fa fa-check-circle text-success" style="font-size: 40px; margin-bottom: 10px;"></i>
                <h4 style="color: #2ecc71; font-weight: 700; margin-bottom: 5px;">Tidak Ada Potongan 🎉</h4>
                <p style="color: #999; font-size: 13px; margin: 0;">Periode ini belum ada catatan keterlambatan, izin, sakit, atau alpha.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- KOLOM KANAN: Riwayat Slip Gaji -->
        <div class="col-md-12 col-lg-6" style="display: flex; flex-direction: column;">
            <div class="box-widget" style="padding: 0; margin-bottom: 30px; flex: 1;">
                <div class="box-title" style="padding: 20px; margin-bottom: 0; border-bottom: 1px solid #f0f0f0;"><i class="fa fa-history text-primary margin-r-5"></i> Riwayat Slip Gaji (6 Bln Terakhir)</div>
                <div class="table-responsive">
                    <table class="table table-history" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th>PERIODE</th>
                                <th>HADIR</th>
                                <th>THP</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($riwayat_payroll)): ?>
                                <?php foreach($riwayat_payroll as $rp): ?>
                                <tr>
                                    <td>
                                        <div style="color: #1976d2; font-weight: 700; font-size: 13px;"><?= date('F Y', strtotime($rp->periode_bulan.'-01')) ?></div>
                                    </td>
                                    <td><span style="background: #e8f5e9; color: #2ecc71; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;"><?= $rp->jumlah_hadir ?>/<?= $absensi['total_hari_kerja'] ?? 22 ?> H</span></td>
                                    <td style="color: #2ecc71; font-weight: 700; font-size: 14px;">Rp <?= number_format((float)$rp->gaji_bersih, 0, ',', '.') ?></td>
                                    <td>
                                        <a href="<?= site_url('administrator/hr_payroll/cetak_slip/'.$rp->id) ?>?download=true" target="_blank" class="btn btn-xs btn-success" style="border-radius: 4px;"><i class="fa fa-file-pdf-o"></i> PDF</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center" style="padding: 20px !important;">Belum ada riwayat.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    
    </div> <!-- End Row Full Width -->
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    <?php if($payroll_terakhir): ?>
    // Chart Komposisi Pendapatan vs Potongan
    var ctxKomposisi = document.getElementById('komposisiChart');
    if (ctxKomposisi) {
        var total_pendapatan = <?= (float)$payroll_terakhir->gaji_pokok + (float)$payroll_terakhir->tunjangan_tetap + (float)$payroll_terakhir->tunjangan_kehadiran ?>;
        var total_potongan = <?= (float)$payroll_terakhir->total_potongan ?>;
        
        if (total_pendapatan > 0 || total_potongan > 0) {
            new Chart(ctxKomposisi, {
                type: 'doughnut',
                data: {
                    labels: ['Total Pendapatan', 'Total Potongan'],
                    datasets: [{
                        data: [total_pendapatan, total_potongan],
                        backgroundColor: ['#2ecc71', '#ef5350'],
                        borderWidth: 0,
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    }
    <?php endif; ?>
});
</script>

<!-- Modal Edit Profile -->
<div class="modal fade" id="modalEditProfile" tabindex="-1" role="dialog" aria-labelledby="modalEditProfileLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 12px 12px 0 0; padding: 20px 25px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 28px; font-weight: 300; opacity: 0.5;"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalEditProfileLabel" style="font-weight: 700; color: #2c3e50; font-size: 18px;"><i class="fa fa-user-circle-o text-primary margin-r-5"></i> Pengaturan Profil Pribadi</h4>
      </div>
      <form id="formEditProfile" action="<?= site_url('administrator/hr_profile/update_profile') ?>" method="POST">
      <div class="modal-body" style="padding: 25px;">
        <div class="row">
            <!-- Kolom Data Pribadi -->
            <div class="col-md-7" style="border-right: 1px solid #eee; padding-right: 25px;">
                <h5 style="font-weight: 700; color: #34495e; margin-bottom: 20px; text-transform: uppercase; font-size: 13px; letter-spacing: 1px;"><i class="fa fa-id-card-o margin-r-5 text-muted"></i> Informasi Umum</h5>
                
                <div class="form-group">
                    <label style="font-weight: 600; color: #555;">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_lengkap" value="<?= _ent($karyawan->nama_lengkap) ?>" required style="border-radius: 6px; padding: 10px 12px; height: auto;">
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #555;">No. Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="no_telepon" value="<?= _ent($karyawan->no_telepon) ?>" required style="border-radius: 6px; padding: 10px 12px; height: auto;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #555;">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tanggal_lahir" value="<?= _ent($karyawan->tanggal_lahir) ?>" style="border-radius: 6px; padding: 10px 12px; height: auto;">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label style="font-weight: 600; color: #555;">Jenis Kelamin</label>
                    <select class="form-control" name="jenis_kelamin" style="border-radius: 6px; height: 42px;">
                        <option value="">- Pilih Jenis Kelamin -</option>
                        <option value="L" <?= $karyawan->jenis_kelamin == 'L' ? 'selected' : '' ?>>Laki-Laki</option>
                        <option value="P" <?= $karyawan->jenis_kelamin == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label style="font-weight: 600; color: #555;">Alamat Lengkap</label>
                    <textarea class="form-control" name="alamat" rows="3" style="border-radius: 6px; padding: 10px 12px; resize: none;"><?= _ent($karyawan->alamat) ?></textarea>
                </div>
            </div>
            
            <!-- Kolom Password -->
            <div class="col-md-5" style="padding-left: 25px;">
                <h5 style="font-weight: 700; color: #34495e; margin-bottom: 20px; text-transform: uppercase; font-size: 13px; letter-spacing: 1px;"><i class="fa fa-lock margin-r-5 text-muted"></i> Keamanan & Password</h5>
                
                <div class="alert alert-info" style="border-radius: 8px; padding: 12px 15px; font-size: 12px; background-color: #f1f8ff; color: #0366d6; border: 1px solid #c8e1ff;">
                    <i class="fa fa-info-circle margin-r-5"></i> Biarkan kosong jika tidak ingin mengubah password akun Anda.
                </div>
                
                <div class="form-group">
                    <label style="font-weight: 600; color: #555;">Password Baru</label>
                    <div class="input-group">
                        <span class="input-group-addon" style="border-radius: 6px 0 0 6px; border-right: none; background: #fff; color: #aaa;"><i class="fa fa-key"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Minimal 6 karakter" style="border-radius: 0 6px 6px 0; border-left: none; box-shadow: none; padding-left: 0; height: 42px;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label style="font-weight: 600; color: #555;">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <span class="input-group-addon" style="border-radius: 6px 0 0 6px; border-right: none; background: #fff; color: #aaa;"><i class="fa fa-check-circle"></i></span>
                        <input type="password" class="form-control" name="password_confirm" placeholder="Ketik ulang password" style="border-radius: 0 6px 6px 0; border-left: none; box-shadow: none; padding-left: 0; height: 42px;">
                    </div>
                </div>
                
                <!-- Decorative Icon -->
                <div style="text-align: center; margin-top: 40px; opacity: 0.05;">
                    <i class="fa fa-shield" style="font-size: 100px;"></i>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer" style="background: #fcfcfc; border-top: 1px solid #f0f0f0; border-radius: 0 0 12px 12px; padding: 15px 25px;">
        <button type="button" class="btn btn-default" style="border-radius:6px; padding: 8px 20px; font-weight: 600; color: #666;" data-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-primary" style="border-radius:6px; padding: 8px 20px; font-weight: 600; box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);" id="btnSaveProfile"><i class="fa fa-save margin-r-5"></i> Simpan Perubahan</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    $('#formEditProfile').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = $('#btnSaveProfile');
        var btnText = btn.html();
        
        btn.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    toastr.success(res.message);
                    $('#modalEditProfile').modal('hide');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    toastr.error(res.message);
                    btn.html(btnText).prop('disabled', false);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan koneksi.');
                btn.html(btnText).prop('disabled', false);
            }
        });
    });
});
</script>
