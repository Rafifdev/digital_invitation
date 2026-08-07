<?php /* produk_kartu_stok.php - Audit Ledger Kartu Stok Produk */ ?>
<section class="content-header">
   <h1>Kartu Stok &amp; Audit Mutasi Barang <small><?= _ent($produk->nama_produk); ?> (<?= _ent($produk->kode_produk); ?>)</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/produk'); ?>">Produk</a></li>
      <li class="active">Kartu Stok</li>
   </ol>
</section>

<section class="content">
   <div class="box box-primary" style="border-radius:6px;">
      <div class="box-header with-border">
         <h3 class="box-title"><i class="fa fa-book text-primary"></i> Buku Besar Mutasi Stok (Audit Ledger)</h3>
         <div class="box-tools pull-right">
            <a href="<?= site_url('administrator/produk/view/'.$produk->id); ?>" class="btn btn-info btn-xs btn-flat"><i class="fa fa-eye"></i> Detail Produk</a>
            <a href="<?= base_url('administrator/produk'); ?>" class="btn btn-default btn-xs btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
         </div>
      </div>
      <div class="box-body">
         <div class="callout callout-info" style="border-radius:6px;">
            <h4><i class="fa fa-info-circle"></i> Ringkasan Barang: <strong><?= _ent($produk->nama_produk); ?></strong></h4>
            <p>Kode Produk: <code><?= _ent($produk->kode_produk); ?></code> | Metode Stok: <strong><?= _ent($produk->metode_stok); ?></strong> | Barcode: <code><?= _ent($produk->barcode ?? '-'); ?></code></p>
         </div>

         <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
               <thead>
                  <tr style="background:#f8fafc;">
                     <th width="40" class="text-center">No</th>
                     <th>Tanggal &amp; Waktu</th>
                     <th>Lokasi Gudang</th>
                     <th>Jenis Mutasi</th>
                     <th>Ref Dokumen</th>
                     <th class="text-right">Masuk (+)</th>
                     <th class="text-right">Keluar (-)</th>
                     <th class="text-right">Saldo Akhir</th>
                     <th>Petugas</th>
                     <th>Keterangan</th>
                  </tr>
               </thead>
               <tbody>
                  <?php 
                     $no = 1; 
                     $saldo_running = 0; 
                     foreach($logs as $log): 
                        if ($log->jenis == 'masuk') {
                           $saldo_running += $log->qty;
                        } else if ($log->jenis == 'keluar') {
                           $saldo_running -= $log->qty;
                        } else if ($log->jenis == 'koreksi') {
                           $saldo_running = $log->qty;
                        }
                  ?>
                  <tr>
                     <td class="text-center"><?= $no++; ?></td>
                     <td><i class="fa fa-clock-o text-muted"></i> <?= date('d/m/Y H:i', strtotime($log->created_at)); ?></td>
                     <td><strong><?= _ent($log->nama_gudang ?? 'Gudang Utama'); ?></strong></td>
                     <td>
                        <?php 
                           if($log->jenis == 'masuk') {
                              echo '<span class="label label-success"><i class="fa fa-arrow-down"></i> MASUK</span>';
                           } else if($log->jenis == 'keluar') {
                              echo '<span class="label label-danger"><i class="fa fa-arrow-up"></i> KELUAR</span>';
                           } else {
                              echo '<span class="label label-warning"><i class="fa fa-adjust"></i> KOREKSI</span>';
                           }
                        ?>
                     </td>
                     <td><span class="label label-default"><?= strtoupper($log->referensi_type ?? 'SYST'); ?> #<?= $log->referensi_id; ?></span></td>
                     <td class="text-right text-success"><?= $log->jenis == 'masuk' ? '+' . number_format($log->qty, 0, ',', '.') : '-'; ?></td>
                     <td class="text-right text-danger"><?= $log->jenis == 'keluar' ? '-' . number_format($log->qty, 0, ',', '.') : '-'; ?></td>
                     <td class="text-right"><strong style="font-size:13px; color:#1e293b;"><?= number_format($saldo_running, 0, ',', '.'); ?> Unit</strong></td>
                     <td><?= _ent($log->created_by ?? 'system'); ?></td>
                     <td><?= _ent($log->keterangan ?? '-'); ?></td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(empty($logs)): ?>
                     <tr><td colspan="10" class="text-center text-muted" style="padding:25px;"><i class="fa fa-inbox fa-2x"></i><br>Belum ada riwayat mutasi kartu stok untuk produk ini.</td></tr>
                  <?php endif; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</section>
