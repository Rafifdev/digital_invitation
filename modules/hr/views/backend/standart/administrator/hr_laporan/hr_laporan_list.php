
<section class="content-header">
   <h1>Laporan Rekap Absensi</h1>
</section>
<section class="content">
   <div class="box box-warning">
      <div class="box-body">
         <form method="GET" class="form-inline" style="margin-bottom: 20px;">
            <div class="form-group">
                <label>Filter Bulan: </label>
                <input type="month" name="bulan" class="form-control" value="<?= $bulan_aktif; ?>">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
         </form>

         <div class="table-responsive">
            <table class="table table-bordered table-striped">
               <thead>
                  <tr class="bg-primary">
                     <th>Tanggal</th>
                     <th>Nama Karyawan</th>
                     <th>Tipe Kerja</th>
                     <th>Masuk</th>
                     <th>Pulang</th>
                     <th>Status Kehadiran</th>
                  </tr>
               </thead>
               <tbody>
                  <?php if(empty($laporan)): ?>
                  <tr><td colspan="6" class="text-center">Tidak ada data absensi bulan ini.</td></tr>
                  <?php endif; ?>

                  <?php foreach($laporan as $row): ?>
                  <tr>
                     <td><?= date('d M Y', strtotime($row->tanggal)); ?></td>
                     <td><strong><?= _ent($row->full_name); ?></strong><br><small><?= $row->nik; ?></small></td>
                     <td>
                        <?php if($row->tipe_kerja == 'WFO'): ?><span class="label label-success">WFO</span>
                        <?php elseif($row->tipe_kerja == 'WFH'): ?><span class="label label-warning">WFH</span>
                        <?php else: ?><span class="label label-info">WFA</span><?php endif; ?>
                     </td>
                     <td>
                        <?php if($row->waktu_masuk): ?>
                            <span class="text-success"><i class="fa fa-clock-o"></i> <?= date('H:i:s', strtotime($row->waktu_masuk)); ?></span><br>
                            <?php if($row->foto_masuk): ?>
                                <a href="<?= base_url($row->foto_masuk); ?>" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-camera"></i> Foto</a>
                            <?php endif; ?>
                            <?php if($row->lat_masuk && $row->tipe_kerja == 'WFO'): ?>
                                <br><small class="text-muted"><i class="fa fa-map-marker"></i> Jarak: <?= $row->jarak_masuk_meter; ?>m</small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                     </td>
                     <td>
                        <?php if($row->waktu_pulang): ?>
                            <span class="text-danger"><i class="fa fa-clock-o"></i> <?= date('H:i:s', strtotime($row->waktu_pulang)); ?></span><br>
                            <?php if($row->foto_pulang): ?>
                                <a href="<?= base_url($row->foto_pulang); ?>" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-camera"></i> Foto</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                     </td>
                     <td>
                        <?php if($row->status_kehadiran == 'Terlambat'): ?>
                            <span class="label label-danger">Terlambat</span>
                        <?php elseif($row->status_kehadiran == 'Tepat Waktu'): ?>
                            <span class="label label-success">Tepat Waktu</span>
                        <?php else: ?>
                            <span class="label label-default"><?= $row->status_kehadiran; ?></span>
                        <?php endif; ?>
                     </td>
                  </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</section>
