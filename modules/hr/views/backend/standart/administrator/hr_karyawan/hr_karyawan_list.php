
<section class="content-header">
   <h1>Daftar Karyawan<small>List All Karyawan</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Karyawan</li>
   </ol>
</section>
<section class="content">
   <div class="box box-warning">
      <div class="box-body">
         <div class="box-widget widget-search">
            <form action="<?= site_url('administrator/hr_karyawan'); ?>" method="GET">
               <input type="text" name="q" value="<?= $this->input->get('q'); ?>" class="form-control" placeholder="Search...">
            </form>
         </div>
         <a href="<?= site_url('administrator/hr_karyawan/add'); ?>" class="btn btn-flat btn-success btn_add_new" style="margin-bottom:15px"><i class="fa fa-plus-square-o"></i> Tambah Karyawan Baru</a>
         
         <div class="table-responsive">
            <table class="table table-bordered table-striped dataTable">
               <thead>
                  <tr class="bg-primary">
                     <th>No</th>
                     <th>Nama Karyawan</th>
                     <th>NIK</th>
                     <th>Jabatan</th>
                     <th>Dept</th>
                     <th>Tipe Kerja</th>
                     <th>Status Device</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach($hr_karyawans as $idx => $k): ?>
                  <tr>
                     <td><?= $idx + 1; ?></td>
                     <td><?= _ent($k->full_name); ?> (<?= _ent($k->username); ?>)</td>
                     <td><?= _ent($k->nik); ?></td>
                     <td><?= _ent($k->jabatan); ?></td>
                     <td><?= _ent($k->departemen); ?></td>
                     <td>
                        <?php if($k->tipe_kerja == 'WFO'): ?><span class="label label-success">WFO</span>
                        <?php elseif($k->tipe_kerja == 'WFH'): ?><span class="label label-warning">WFH</span>
                        <?php else: ?><span class="label label-info">WFA</span><?php endif; ?>
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
                        <a href="<?= site_url('administrator/hr_karyawan/view/'.$k->id); ?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
                        <a href="<?= site_url('administrator/hr_karyawan/edit/'.$k->id); ?>" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                        <a href="<?= site_url('administrator/hr_karyawan/delete/'.$k->id); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Yakin hapus data ini?');"><i class="fa fa-trash"></i></a>
                     </td>
                  </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</section>
