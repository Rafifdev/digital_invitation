<style>
.device-card {
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 15px;
    background: #fff;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    border-left: 5px solid #3c8dbc;
    transition: box-shadow 0.2s;
}
.device-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
.device-card.dicabut { border-left-color: #dd4b39; opacity: 0.75; }
.device-card.aktif   { border-left-color: #00a65a; }
.device-stat-box {
    border-radius: 10px;
    padding: 18px;
    text-align: center;
    color: #fff;
    margin-bottom: 20px;
}
.fp-code {
    font-family: monospace;
    font-size: 10px;
    background: #f5f5f5;
    padding: 3px 8px;
    border-radius: 4px;
    word-break: break-all;
    color: #555;
}
</style>

<section class="content-header">
    <h1><i class="fa fa-shield text-primary"></i> Manajemen Perangkat Karyawan</h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('administrator/dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/hr_absensi') ?>">HR & Absensi</a></li>
        <li class="active">Perangkat Terdaftar</li>
    </ol>
</section>

<section class="content">

    <!-- STATISTIK -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="device-stat-box" style="background: linear-gradient(135deg,#3c8dbc,#1a5276);">
                <div style="font-size:36px; font-weight:bold;"><?= $total_device ?></div>
                <div><i class="fa fa-mobile"></i> Total Perangkat</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="device-stat-box" style="background: linear-gradient(135deg,#00a65a,#1e8449);">
                <div style="font-size:36px; font-weight:bold;"><?= $total_aktif ?></div>
                <div><i class="fa fa-check-circle"></i> Perangkat Aktif</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="device-stat-box" style="background: linear-gradient(135deg,#dd4b39,#922b21);">
                <div style="font-size:36px; font-weight:bold;"><?= $total_dicabut ?></div>
                <div><i class="fa fa-ban"></i> Dicabut</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="device-stat-box" style="background: linear-gradient(135deg,#f39c12,#b7770d);">
                <div style="font-size:36px; font-weight:bold;"><?= $total_user_unik ?></div>
                <div><i class="fa fa-users"></i> Karyawan Terdaftar</div>
            </div>
        </div>
    </div>

    <!-- FILTER & PENCARIAN -->
    <div class="box box-primary" style="border-radius:8px;">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Perangkat</h3>
        </div>
        <div class="box-body">
            <form action="<?= site_url('administrator/hr_device') ?>" method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                            <input type="text" class="form-control" name="q" value="<?= _ent($q) ?>" placeholder="Cari nama karyawan, device ID, GPU...">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="semua" <?= $status_filter == 'semua' ? 'selected' : '' ?>>Semua Status</option>
                            <option value="aktif" <?= $status_filter == 'aktif' ? 'selected' : '' ?>>Aktif Saja</option>
                            <option value="dicabut" <?= $status_filter == 'dicabut' ? 'selected' : '' ?>>Dicabut Saja</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= site_url('administrator/hr_device') ?>" class="btn btn-default btn-block"><i class="fa fa-refresh"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- DAFTAR PERANGKAT -->
    <?php if(empty($devices)): ?>
        <div class="alert alert-info" style="border-radius:10px; text-align:center; padding:40px;">
            <i class="fa fa-mobile fa-3x" style="margin-bottom:15px;"></i>
            <h4>Belum ada perangkat terdaftar</h4>
            <p>Data perangkat akan otomatis muncul di sini ketika karyawan mulai melakukan absensi.</p>
        </div>
    <?php else: ?>
        <?php foreach($devices as $d): ?>
        <div class="device-card <?= $d->status ?>">
            <div class="row">
                <!-- KOLOM KIRI: Identitas Karyawan + Device -->
                <div class="col-md-5">
                    <div style="display:flex; align-items:center; margin-bottom:12px;">
                        <div style="width:48px; height:48px; border-radius:50%; background:<?= $d->status == 'aktif' ? '#00a65a' : '#dd4b39' ?>; display:flex; align-items:center; justify-content:center; margin-right:12px; flex-shrink:0;">
                            <i class="fa fa-<?= $d->is_touchscreen ? 'mobile' : 'laptop' ?> fa-lg" style="color:white;"></i>
                        </div>
                        <div>
                            <strong style="font-size:15px;"><?= _ent($d->full_name ?: $d->username) ?></strong>
                            <br>
                            <span class="text-muted" style="font-size:12px;">
                                <i class="fa fa-user"></i> <?= _ent($d->username) ?>
                                &nbsp;|&nbsp;
                                <i class="fa fa-envelope"></i> <?= _ent($d->email ?? '-') ?>
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom:8px;">
                        <span class="label label-<?= $d->status == 'aktif' ? 'success' : 'danger' ?>" style="font-size:12px; padding:4px 10px; margin-right:5px;">
                            <i class="fa fa-<?= $d->status == 'aktif' ? 'check' : 'ban' ?>"></i> <?= ucfirst($d->status) ?>
                        </span>
                        <span class="label label-default" style="font-size:12px; padding:4px 10px;">
                            <i class="fa fa-<?= $d->is_touchscreen ? 'mobile' : 'laptop' ?>"></i>
                            <?= $d->is_touchscreen ? 'Smartphone/Tablet' : 'Laptop/PC' ?>
                        </span>
                    </div>

                    <div style="font-size:12px; color:#555; margin-top:5px;">
                        <i class="fa fa-tag text-primary"></i> <strong><?= _ent($d->device_label) ?></strong>
                    </div>
                    <div style="font-size:11px; margin-top:5px;">
                        <i class="fa fa-key text-muted"></i> Device ID:
                        <span class="fp-code"><?= substr(_ent($d->device_id), 0, 40) ?>...</span>
                    </div>
                </div>

                <!-- KOLOM TENGAH: Spesifikasi Perangkat -->
                <div class="col-md-4">
                    <table style="font-size:12px; width:100%;">
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#888; white-space:nowrap;"><i class="fa fa-desktop"></i> OS</td>
                            <td style="padding:3px 0;"><?= _ent($d->os_platform ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#888; white-space:nowrap;"><i class="fa fa-television"></i> Layar</td>
                            <td style="padding:3px 0;"><?= _ent($d->screen_info ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#888; white-space:nowrap;"><i class="fa fa-microchip"></i> CPU/RAM</td>
                            <td style="padding:3px 0;"><?= $d->cpu_cores ?> Core | <?= $d->ram_gb ? $d->ram_gb . ' GB' : 'N/A' ?></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#888; white-space:nowrap;"><i class="fa fa-paint-brush"></i> GPU</td>
                            <td style="padding:3px 0; font-size:11px;"><?= _ent(strlen($d->gpu_info) > 50 ? substr($d->gpu_info, 0, 50) . '...' : ($d->gpu_info ?: '-')) ?></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#888; white-space:nowrap;"><i class="fa fa-clock-o"></i> Timezone</td>
                            <td style="padding:3px 0;"><?= _ent($d->timezone ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#888; white-space:nowrap;"><i class="fa fa-wifi"></i> IP Terakhir</td>
                            <td style="padding:3px 0;"><code style="font-size:11px;"><?= _ent($d->ip_address ?: '-') ?></code></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#888; white-space:nowrap;"><i class="fa fa-map-marker"></i> Lokasi GPS</td>
                            <td style="padding:3px 0;">
                                <?php if($d->last_lat && $d->last_lng): ?>
                                    <a href="https://maps.google.com/?q=<?= $d->last_lat ?>,<?= $d->last_lng ?>" target="_blank" class="text-success" style="font-weight:bold; font-size:11px;">
                                        <i class="fa fa-map"></i> Lihat Peta (<?= substr($d->last_lat, 0, 8) ?>, <?= substr($d->last_lng, 0, 8) ?>)
                                    </a>
                                <?php else: ?>
                                    <span style="font-size:11px; color:#999;">Belum mendeteksi GPS</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- KOLOM KANAN: Aktivitas & Tombol -->
                <div class="col-md-3" style="text-align:center; border-left:1px solid #f0f0f0;">
                    <div style="margin-bottom:10px;">
                        <div style="font-size:28px; font-weight:bold; color:#3c8dbc;"><?= $d->total_absen_real ?></div>
                        <div style="font-size:12px; color:#888;"><i class="fa fa-calendar-check-o"></i> Total Absen Nyata</div>
                    </div>
                    <hr style="margin:8px 0;">
                    <div style="font-size:11px; color:#888; margin-bottom:5px;">
                        <i class="fa fa-calendar-check-o text-success"></i>
                        Pertama: <strong><?= date('d M Y', strtotime($d->first_seen)) ?></strong>
                    </div>
                    <div style="font-size:11px; color:#888; margin-bottom:12px;">
                        <i class="fa fa-clock-o text-warning"></i>
                        Terakhir: <strong><?= date('d M Y H:i', strtotime($d->last_seen)) ?></strong>
                    </div>
                    <div>
                        <?php if($d->status == 'aktif'): ?>
                            <a href="<?= site_url('administrator/hr_device/revoke/' . $d->id) ?>"
                               class="btn btn-danger btn-xs btn-block"
                               onclick="return confirm('Cabut akses perangkat ini?')"
                               style="border-radius:20px; margin-bottom:5px;">
                                <i class="fa fa-ban"></i> Cabut Akses
                            </a>
                        <?php else: ?>
                            <a href="<?= site_url('administrator/hr_device/activate/' . $d->id) ?>"
                               class="btn btn-success btn-xs btn-block"
                               style="border-radius:20px; margin-bottom:5px;">
                                <i class="fa fa-check"></i> Aktifkan
                            </a>
                        <?php endif; ?>
                        <a href="<?= site_url('administrator/hr_device/delete/' . $d->id) ?>"
                           class="btn btn-default btn-xs btn-block"
                           onclick="return confirm('Hapus permanen data perangkat ini?')"
                           style="border-radius:20px;">
                            <i class="fa fa-trash"></i> Hapus
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

</section>
