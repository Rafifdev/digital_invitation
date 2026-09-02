
<section class="content-header">
   <h1>
      Detail Karyawan
      <small style="font-weight:400; color:#777;">Informasi Lengkap Profil HR</small>
   </h1>
   <ol class="breadcrumb" style="background:transparent;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/hr_karyawan'); ?>">Karyawan</a></li>
      <li class="active">Detail</li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <!-- WIDGET PROFIL ATAS -->
      <div class="col-md-12">
         <div class="box box-widget widget-user-2" style="border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
            <div class="widget-user-header bg-aqua-active" style="border-top-left-radius:10px; border-top-right-radius:10px; padding:30px;">
               <div class="widget-user-image">
                  <img class="img-circle" src="https://ui-avatars.com/api/?name=<?= urlencode($hr_karyawan->nama_lengkap) ?>&background=random&color=fff&size=128" alt="User Avatar" style="border:4px solid #fff; box-shadow:0 2px 10px rgba(0,0,0,0.1); width:90px; height:90px; float:left; margin-right:20px;">
               </div>
               <h3 class="widget-user-username" style="font-size:28px; font-weight:600; text-shadow:1px 1px 2px rgba(0,0,0,0.2); margin-top:10px; margin-left:110px;"><?= _ent($hr_karyawan->nama_lengkap); ?></h3>
               <h5 class="widget-user-desc" style="font-size:16px; margin-top:5px; margin-left:110px;"><i class="fa fa-briefcase"></i> <?= _ent($hr_karyawan->jabatan); ?> - <?= _ent($hr_karyawan->departemen); ?></h5>
            </div>
            <div class="box-footer" style="padding-top:20px; border-bottom-left-radius:10px; border-bottom-right-radius:10px;">
               <div class="row">
                  <div class="col-sm-4 border-right">
                     <div class="description-block">
                        <h5 class="description-header" style="font-size:18px; color:#333;"><?= _ent($hr_karyawan->nik); ?></h5>
                        <span class="description-text text-muted" style="text-transform:uppercase; font-size:12px;">NIP KARYAWAN</span>
                     </div>
                  </div>
                  <div class="col-sm-4 border-right">
                     <div class="description-block">
                        <h5 class="description-header" style="font-size:18px; color:#333;">
                           <?php if($hr_karyawan->tipe_kerja == 'WFO'): ?><span class="label label-success">WFO</span>
                           <?php elseif($hr_karyawan->tipe_kerja == 'WFH'): ?><span class="label label-warning">WFH</span>
                           <?php else: ?><span class="label label-info">WFA</span><?php endif; ?>
                        </h5>
                        <span class="description-text text-muted" style="text-transform:uppercase; font-size:12px;">TIPE KERJA</span>
                     </div>
                  </div>
                  <div class="col-sm-4">
                     <div class="description-block">
                        <h5 class="description-header" style="font-size:18px; color:#333;">
                           <?php if($hr_karyawan->device_id): ?>
                              <span class="text-danger"><i class="fa fa-lock"></i> Terkunci</span>
                           <?php else: ?>
                              <span class="text-success"><i class="fa fa-unlock"></i> Bebas</span>
                           <?php endif; ?>
                        </h5>
                        <span class="description-text text-muted" style="text-transform:uppercase; font-size:12px;">STATUS DEVICE</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- BAGIAN DETAIL GRID -->
   <div class="row" style="margin-top:10px;">
      <!-- KIRI: PROFIL -->
      <div class="col-md-6">
         <div class="box box-primary" style="border-radius:8px; border-top-width:4px;">
            <div class="box-header with-border">
               <h3 class="box-title" style="font-weight:600;"><i class="fa fa-user margin-r-5 text-primary"></i> Data Pribadi</h3>
            </div>
            <div class="box-body" style="padding:20px;">
               <strong><i class="fa fa-envelope margin-r-5"></i> Email / Username</strong>
               <p class="text-muted" style="margin-top:5px; margin-bottom:15px; font-size:14px;">
                  <?= _ent($hr_karyawan->email); ?><br><small>User: <?= _ent($hr_karyawan->username); ?></small>
               </p>

               <strong><i class="fa fa-venus-mars margin-r-5"></i> Kelamin & Tgl Lahir</strong>
               <p class="text-muted" style="margin-top:5px; margin-bottom:15px; font-size:14px;">
                  <?= _ent($hr_karyawan->jenis_kelamin == 'L' ? 'Laki-Laki' : ($hr_karyawan->jenis_kelamin == 'P' ? 'Perempuan' : '-')); ?><br>
                  <small><?= _ent($hr_karyawan->tanggal_lahir ? date('d F Y', strtotime($hr_karyawan->tanggal_lahir)) : '-'); ?></small>
               </p>

               <strong><i class="fa fa-phone margin-r-5"></i> Kontak & Alamat</strong>
               <p class="text-muted" style="margin-top:5px; margin-bottom:15px; font-size:14px;">
                  <?= _ent($hr_karyawan->no_telepon ?: '-'); ?><br>
                  <small><?= nl2br(_ent($hr_karyawan->alamat ?: '-')); ?></small>
               </p>
            </div>
         </div>
      </div>

      <!-- KANAN: PEKERJAAN -->
      <div class="col-md-6">
         <div class="box box-success" style="border-radius:8px; border-top-width:4px;">
            <div class="box-header with-border">
               <h3 class="box-title" style="font-weight:600;"><i class="fa fa-building margin-r-5 text-success"></i> Data Pekerjaan</h3>
            </div>
            <div class="box-body" style="padding:20px;">
               <strong><i class="fa fa-calendar-check-o margin-r-5"></i> Tanggal Bergabung</strong>
               <p class="text-muted" style="margin-top:5px; margin-bottom:15px; font-size:14px;">
                  <?= _ent($hr_karyawan->tanggal_bergabung ? date('d F Y', strtotime($hr_karyawan->tanggal_bergabung)) : '-'); ?>
               </p>

               <strong><i class="fa fa-map-marker margin-r-5"></i> Penempatan Cabang</strong>
               <p class="text-muted" style="margin-top:5px; margin-bottom:15px; font-size:14px;">
                  <?= _ent($hr_karyawan->nama_kantor ?: 'Belum di set'); ?><br>
                  <small>Radius Absen: <?= _ent($hr_karyawan->radius_meter ?: 0); ?> Meter</small>
               </p>

               <strong><i class="fa fa-clock-o margin-r-5"></i> Jadwal & Jam Kerja</strong>
               <p class="text-muted" style="margin-top:5px; margin-bottom:15px; font-size:14px;">
                  <?= _ent($hr_karyawan->nama_shift ?: 'Belum di set'); ?><br>
                  <small>Jam: <?= _ent($hr_karyawan->shift_jam_masuk ?: '-'); ?> - <?= _ent($hr_karyawan->shift_jam_pulang ?: '-'); ?></small>
               </p>
            </div>
         </div>
      </div>

      <!-- TOMBOL KEMBALI -->
      <div class="col-md-12 text-center" style="margin-top: 20px;">
          <a href="<?= site_url('administrator/hr_karyawan/edit/'.$hr_karyawan->id); ?>" class="btn btn-warning" style="border-radius:20px; font-weight:bold; padding: 10px 30px;"><i class="fa fa-pencil"></i> Edit Profil Karyawan</a>
          <a href="<?= site_url('administrator/hr_karyawan'); ?>" class="btn btn-default" style="border-radius:20px; font-weight:bold; padding: 10px 30px; margin-left: 10px;"><i class="fa fa-undo"></i> Kembali ke Daftar</a>
      </div>


   </div>
</section>
