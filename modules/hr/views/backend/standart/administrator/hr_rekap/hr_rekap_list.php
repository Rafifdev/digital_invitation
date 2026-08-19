<section class="content-header">
   <h1>Laporan Absensi Karyawan<small>Matrix Rekap Bulanan</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Laporan Absensi Karyawan</li>
   </ol>
</section>
<section class="content">
   <div class="box box-warning">
      <div class="box-body">
         <div class="row" style="margin-bottom: 20px;">
            <div class="col-md-4">
                <form action="<?= site_url('administrator/absensi/laporan'); ?>" method="GET">
                    <div class="input-group">
                        <input type="month" name="bulan" class="form-control" value="<?= _ent($bulan_aktif); ?>">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit"><i class="fa fa-filter"></i> Filter Bulan</button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-md-8 text-right">
                <span class="label label-success" style="margin-left:5px;">Hadir/Tepat Waktu</span>
                <span class="label label-warning" style="margin-left:5px;">Terlambat/Izin</span>
                <span class="label label-danger" style="margin-left:5px;">Alfa</span>
                <span class="label" style="background:#d2d6de; color:#333; margin-left:5px;">Libur</span>
                <span class="label" style="background:#111; margin-left:5px;">Belum Bergabung</span>
            </div>
         </div>
         
         <div class="table-responsive">
            <style>
                .td-hadir { background-color: #00a65a !important; color: white !important; text-align: center; font-weight: bold; }
                .td-telat { background-color: #f39c12 !important; color: white !important; text-align: center; font-weight: bold; }
                .td-alfa { background-color: #dd4b39 !important; color: white !important; text-align: center; font-weight: bold; }
                .td-libur { background-color: #d2d6de !important; color: #333 !important; text-align: center; }
                .td-belum { background-color: #111 !important; color: white !important; text-align: center; font-size:10px; }
                .td-kosong { text-align: center; color:#ccc; }
                .table-matrix th { text-align: center; min-width: 35px; }
            </style>
            <table class="table table-bordered table-matrix">
               <thead>
                  <tr class="bg-primary">
                     <th width="300" style="text-align:left;">Nama Karyawan</th>
                     <?php for($i=1; $i<=$jumlah_hari; $i++): ?>
                        <th><?= $i ?></th>
                     <?php endfor; ?>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach($matrix as $r): ?>
                  <tr>
                     <td><strong><?= _ent($r['nama_lengkap']); ?></strong></td>
                     <?php for($i=1; $i<=$jumlah_hari; $i++): ?>
                        <?php 
                            $status = $r['days'][$i];
                            $cls = 'td-kosong';
                            $txt = '-';
                            if ($status == 'Hadir') { $cls = 'td-hadir'; $txt = 'H'; }
                            else if ($status == 'Terlambat') { $cls = 'td-telat'; $txt = 'T'; }
                            else if ($status == 'Izin' || $status == 'Sakit' || $status == 'Cuti') { $cls = 'td-telat'; $txt = substr($status,0,1); }
                            else if ($status == 'Alfa') { $cls = 'td-alfa'; $txt = 'A'; }
                            else if ($status == 'Libur') { $cls = 'td-libur'; $txt = 'L'; }
                            else if ($status == 'Belum Bergabung') { $cls = 'td-belum'; $txt = 'X'; }
                        ?>
                        <td class="<?= $cls ?>" title="Tanggal <?= $i ?>: <?= $status ?>"><?= $txt ?></td>
                     <?php endfor; ?>
                  </tr>
                  <?php endforeach; ?>
                  
                  <?php if(empty($matrix)): ?>
                  <tr>
                     <td colspan="<?= $jumlah_hari + 1 ?>" class="text-center">Tidak ada karyawan / data.</td>
                  </tr>
                  <?php endif; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</section>
