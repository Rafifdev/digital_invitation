<style>
   .modern-box { border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #3c8dbc; }
   .table-modern { border-collapse: separate; border-spacing: 0; width: 100%; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
   .table-modern thead th { background: #f4f6f9; color: #333; font-weight: 600; border-bottom: 2px solid #ddd; padding: 12px; }
   .table-modern tbody td { padding: 12px; vertical-align: middle; border-bottom: 1px solid #f0f0f0; }
   .table-modern tbody tr:hover { background-color: #f8fbff; }
   .btn-modern { border-radius: 20px; padding: 6px 15px; font-weight: 500; transition: all 0.3s; }
   .btn-modern:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
   .btn-action { border-radius: 50%; width: 32px; height: 32px; padding: 0; line-height: 32px; text-align: center; margin: 0 2px; }
   .widget-search { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
   .widget-search form { flex-grow: 1; max-width: 300px; }
   .widget-search form input { border-radius: 20px; padding-left: 15px; }
</style>

<section class="content-header">
   <h1>Daftar Karyawan <small style="font-weight: 400; color: #777;">Data Lengkap HR</small></h1>
   <ol class="breadcrumb" style="background: transparent;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Karyawan</li>
   </ol>
</section>
<section class="content">
   <div class="box modern-box">
      <div class="box-body" style="padding: 20px;">
         <div class="widget-search">
            <a href="<?= site_url('administrator/hr_karyawan/add'); ?>" class="btn btn-primary btn-modern"><i class="fa fa-plus"></i> Tambah Karyawan Baru</a>
            <form action="<?= site_url('administrator/hr_karyawan'); ?>" method="GET">
               <div class="input-group">
                  <input type="text" name="q" value="<?= $this->input->get('q'); ?>" class="form-control" placeholder="Cari nama, NIP, posisi...">
                  <span class="input-group-btn">
                     <button type="submit" class="btn btn-default" style="border-radius: 0 20px 20px 0;"><i class="fa fa-search"></i></button>
                  </span>
               </div>
            </form>
         </div>
         
         <div class="table-responsive">
            <table class="table table-modern dataTable">
               <thead>
                   <tr>
                     <th width="5%">No</th>
                     <th width="25%">Profil Karyawan</th>
                     <th width="20%">Kontak & Email</th>
                     <th width="15%">Posisi / Dept</th>
                     <th width="10%">Tipe Kerja</th>
                     <th width="15%">Status Device</th>
                     <th width="10%">Action</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach($hr_karyawans as $idx => $k): ?>
                  <tr>
                     <td><?= $idx + 1; ?></td>
                     <td>
                        <strong><?= _ent($k->nama_lengkap); ?></strong> 
                        <br><small class="text-muted"><i class="fa fa-id-card-o"></i> NIP: <?= _ent($k->nik); ?> &nbsp;|&nbsp; <i class="fa <?= $k->jenis_kelamin == 'L' ? 'fa-mars' : 'fa-venus' ?>"></i> <?= $k->jenis_kelamin == 'L' ? 'Laki-Laki' : 'Perempuan' ?></small>
                     </td>
                     <td>
                        <i class="fa fa-phone"></i> <?= _ent($k->no_telepon ?: '-'); ?> <br>
                        <small class="text-muted"><i class="fa fa-envelope-o"></i> <?= _ent($k->email); ?></small>
                     </td>
                     <td>
                        <strong><?= _ent($k->jabatan); ?></strong> <br>
                        <small class="text-muted"><?= _ent($k->departemen); ?></small>
                        <div style="margin-top: 4px;">
                           <?php if (strtolower($k->group_name ?? '') == 'manager'): ?>
                              <span class="label label-warning" style="font-size: 10px;" title="Manager / Atasan"><i class="fa fa-shield"></i> Manager (Lvl 2)</span>
                           <?php elseif (strtolower($k->group_name ?? '') == 'admin'): ?>
                              <span class="label label-danger" style="font-size: 10px;" title="Direktur / Superadmin"><i class="fa fa-star"></i> Direktur (Full)</span>
                           <?php else: ?>
                              <span class="label label-default" style="font-size: 10px;" title="Staff / Member"><i class="fa fa-user"></i> Staff (Lvl 1)</span>
                           <?php endif; ?>
                        </div>
                     </td>
                     <td>
                        <?php if($k->tipe_kerja == 'WFO'): ?>
                            <span class="label label-success">WFO</span>
                        <?php elseif($k->tipe_kerja == 'WFH'): ?>
                            <span class="label label-warning">WFH</span>
                        <?php else: ?>
                            <span class="label label-info">WFA</span>
                        <?php endif; ?>
                        <br><br>
                        <span class="label label-default" style="font-size:11px;"><i class="fa fa-clock-o"></i> <?= $k->nama_shift ?: 'Reguler' ?> (<?= substr($k->shift_jam_masuk,0,5) ?> - <?= substr($k->shift_jam_pulang,0,5) ?>)</span>
                        <br><span style="font-size:11px; color:#777; margin-top:3px; display:inline-block;"><i class="fa fa-building"></i> <?= $k->nama_kantor ?: 'Pusat' ?></span>
                     </td>
                     <td>
                        <?php if($k->device_id): ?>
                           <span class="label label-danger" title="Device Locked"><i class="fa fa-lock"></i> Locked</span>
                           <a href="<?= site_url('administrator/hr_karyawan/reset_device/'.$k->id); ?>" class="btn btn-xs btn-default" onclick="return confirm('Reset Device ID untuk karyawan ini agar bisa login/absen dari HP lain?');"><i class="fa fa-unlock"></i> Reset</a>
                        <?php else: ?>
                           <span class="label label-success"><i class="fa fa-unlock"></i> Open</span>
                        <?php endif; ?>
                     </td>
                     <td>
                        <a href="<?= site_url('administrator/hr_karyawan/view/'.$k->id); ?>" class="btn btn-default btn-action" title="Detail"><i class="fa fa-eye text-info"></i></a>
                        <a href="<?= site_url('administrator/hr_karyawan/edit/'.$k->id); ?>" class="btn btn-default btn-action" title="Edit"><i class="fa fa-pencil text-warning"></i></a>
                        <a href="<?= site_url('administrator/hr_karyawan/delete/'.$k->id); ?>" class="btn btn-default btn-action" title="Hapus" onclick="return confirm('Yakin hapus data ini?');"><i class="fa fa-trash text-danger"></i></a>
                     </td>
                  </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</section>
