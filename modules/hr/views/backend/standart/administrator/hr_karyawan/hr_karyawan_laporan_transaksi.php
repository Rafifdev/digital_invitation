<section class="content-header">
   <h1>Laporan Transaksi Hr_karyawan <small>Riwayat Pembelian &amp; Omset</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/hr_karyawan'); ?>"><i class="fa fa-users"></i> Hr_karyawan</a></li>
      <li class="active">Laporan Transaksi</li>
   </ol>
</section>

<section class="content">
<div class="box box-primary">
   <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-bar-chart"></i> Ringkasan Performa Pembelian Hr_karyawan</h3>
   </div>
   <div class="box-body">
      <div class="table-responsive">
         <table class="table table-bordered table-striped">
            <thead>
               <tr class="bg-gray">
                  <th width="40" class="text-center">No</th>
                  <th>Kode</th>
                  <th>Nama Hr_karyawan</th>
                  <th>Tipe</th>
                  <th class="text-center">Total Transaksi (SO)</th>
                  <th class="text-center">Total Invoice</th>
                  <th class="text-right">Total Nominal Belanja</th>
                  <th class="text-right">Sisa Piutang</th>
                  <th width="120" class="text-center">Aksi</th>
               </tr>
            </thead>
            <tbody>
               <?php if (!empty($laporan)): ?>
                  <?php $no = 1; foreach ($laporan as $l): ?>
                     <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><code><?= _ent($l->kode_hr_karyawan); ?></code></td>
                        <td><strong><?= _ent($l->nama_hr_karyawan); ?></strong></td>
                        <td><span class="label label-info"><?= strtoupper($l->tipe_hr_karyawan); ?></span></td>
                        <td class="text-center"><?= number_format($l->total_so); ?> Transaksi</td>
                        <td class="text-center"><?= number_format($l->total_inv); ?> Invoice</td>
                        <td class="text-right"><strong class="text-success">Rp <?= number_format($l->total_belanja, 0, ',', '.'); ?></strong></td>
                        <td class="text-right"><strong class="text-danger">Rp <?= number_format($l->sisa_piutang, 0, ',', '.'); ?></strong></td>
                        <td class="text-center">
                           <a href="<?= site_url('administrator/sales_order?q=' . urlencode($l->nama_hr_karyawan)); ?>" class="btn btn-xs btn-primary">
                              <i class="fa fa-list"></i> Lihat SO
                           </a>
                        </td>
                     </tr>
                  <?php endforeach; ?>
               <?php else: ?>
                  <tr><td colspan="9" class="text-center text-muted">Belum ada transaksi hr_karyawan.</td></tr>
               <?php endif; ?>
            </tbody>
         </table>
      </div>
   </div>
</div>
</section>
