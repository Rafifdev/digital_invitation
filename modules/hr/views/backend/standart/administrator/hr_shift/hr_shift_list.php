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
</style>

<section class="content-header">
   <h1>Jadwal Shift <small style="font-weight: 400; color: #777;">Daftar Jadwal Shift</small></h1>
   <ol class="breadcrumb" style="background: transparent;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Jadwal Shift</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box modern-box">
            <div class="box-body" style="padding: 20px;">
               
               <div class="widget-search">
                  <a href="<?= site_url('administrator/hr_shift/add'); ?>" class="btn btn-primary btn-modern"><i class="fa fa-plus"></i> Tambah Shift Baru</a>
               </div>
               
               <div class="table-responsive">
                  <table class="table table-modern dataTable">
                     <thead>
                        <tr>
                           <th width="50">No</th>
                           <th>Nama Shift</th>
                           <th>Jam Masuk</th>
                           <th>Jam Pulang</th>
                           <th width="150" class="text-center">Action</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_hr_shift">
                        <?php foreach($hr_shifts as $idx => $s): ?>
                        <tr>
                           <td><?= $idx + 1 ?></td>
                           <td><strong><?= _ent($s->nama_shift) ?></strong></td>
                           <td><span class="label label-success" style="font-size:13px; border-radius:12px; padding:4px 10px;"><i class="fa fa-clock-o"></i> <?= substr($s->jam_masuk, 0, 5) ?></span></td>
                           <td><span class="label label-danger" style="font-size:13px; border-radius:12px; padding:4px 10px;"><i class="fa fa-clock-o"></i> <?= substr($s->jam_pulang, 0, 5) ?></span></td>
                           <td class="text-center">
                              <a href="<?= site_url('administrator/hr_shift/edit/'.$s->id); ?>" class="btn btn-default btn-action" title="Edit"><i class="fa fa-pencil text-warning"></i></a>
                              <?php if($s->id != 1): // Cegah hapus shift default ?>
                                 <a href="<?= site_url('administrator/hr_shift/delete/'.$s->id); ?>" class="btn btn-default btn-action" title="Hapus" onclick="return confirm('Hapus shift ini?');"><i class="fa fa-trash text-danger"></i></a>
                              <?php endif; ?>
                           </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($hr_shifts)): ?>
                        <tr>
                           <td colspan="5" class="text-center text-muted" style="padding: 30px;">Data Shift Masih Kosong</td>
                        </tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
