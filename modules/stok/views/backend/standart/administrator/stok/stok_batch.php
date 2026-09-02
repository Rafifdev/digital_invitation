<?php /** View: Batch/Lot Tracking - stok_batch.php */ ?>
<section class="content-header">
   <h1>Batch / Lot Tracking <small>Ketelusuran Produk per Batch</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/stok'); ?>"><i class="fa fa-database"></i> Stok</a></li>
      <li class="active">Batch Tracking</li>
   </ol>
</section>
<section class="content">

   <?php if (!empty($kadaluarsa_soon)): ?>
   <div class="callout callout-danger" style="border-left: 6px solid #e74c3c;">
      <h4><i class="fa fa-exclamation-circle"></i> Peringatan! <?= count($kadaluarsa_soon); ?> Batch Mendekati / Sudah Kadaluarsa (≤ 30 Hari)</h4>
      <div class="table-responsive">
         <table class="table table-condensed" style="margin-bottom:0;">
            <thead><tr class="bg-red" style="color:#fff;"><th>Produk</th><th>Batch No</th><th>Gudang</th><th class="text-center">Qty</th><th class="text-center">Tanggal Kadaluarsa</th><th class="text-center">Sisa Hari</th></tr></thead>
            <tbody>
               <?php foreach($kadaluarsa_soon as $k):
                  $sisa_hari = (int) ceil((strtotime($k->expired_date) - time()) / 86400);
                  $badge_class = $sisa_hari <= 0 ? 'label-danger' : ($sisa_hari <= 7 ? 'label-warning' : 'label-default');
               ?>
               <tr class="<?= $sisa_hari <= 0 ? 'danger' : 'warning'; ?>">
                  <td><strong><?= _ent($k->nama_produk); ?></strong><br><small class="text-muted"><?= _ent($k->kode_produk); ?></small></td>
                  <td><span class="label label-primary"><?= _ent($k->batch_no); ?></span></td>
                  <td><?= _ent($k->nama_gudang); ?></td>
                  <td class="text-center"><strong><?= number_format($k->qty, 0, ',', '.'); ?></strong></td>
                  <td class="text-center"><?= date('d/m/Y', strtotime($k->expired_date)); ?></td>
                  <td class="text-center">
                     <span class="label <?= $badge_class; ?>">
                        <?= $sisa_hari <= 0 ? 'KADALUARSA!' : $sisa_hari . ' hari lagi'; ?>
                     </span>
                  </td>
               </tr>
               <?php endforeach; ?>
            </tbody>
         </table>
      </div>
   </div>
   <?php endif; ?>

   <!-- Filter -->
   <div class="box box-default">
      <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-filter"></i> Filter Pencarian Batch</h3></div>
      <div class="box-body">
         <form method="GET" action="<?= site_url('administrator/stok/batch_tracking'); ?>" class="form-inline">
            <div class="form-group" style="margin-right:10px;">
               <label>Produk: </label>
               <select name="produk_id" class="form-control chosen" style="min-width:220px;">
                  <option value="">-- Semua Produk --</option>
                  <?php foreach($produks as $p): ?>
                     <option value="<?= $p->id; ?>" <?= $produk_sel == $p->id ? 'selected' : ''; ?>><?= _ent($p->kode_produk . ' - ' . $p->nama_produk); ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
            <div class="form-group" style="margin-right:10px;">
               <label>Gudang: </label>
               <select name="gudang_id" class="form-control">
                  <option value="">-- Semua Gudang --</option>
                  <?php foreach($gudangs as $g): ?>
                     <option value="<?= $g->id; ?>" <?= $gudang_sel == $g->id ? 'selected' : ''; ?>><?= _ent($g->nama_gudang); ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
            <div class="form-group" style="margin-right:10px;">
               <label>Tgl Terima: </label>
               <input type="date" name="date_start" class="form-control" value="<?= $date_start; ?>"> s/d
               <input type="date" name="date_end" class="form-control" value="<?= $date_end; ?>">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
            <a href="<?= site_url('administrator/stok/batch_tracking'); ?>" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
         </form>
      </div>
   </div>

   <!-- Batch Data Table -->
   <div class="box box-primary">
      <div class="box-header with-border">
         <h3 class="box-title"><i class="fa fa-qrcode"></i> Daftar Batch / Lot Masuk</h3>
         <div class="box-tools pull-right">
            <span class="label label-info"><?= count($batches); ?> Batch Tercatat</span>
         </div>
      </div>
      <div class="box-body">
         <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
               <thead>
                  <tr>
                     <th width="40">#</th>
                     <th>Produk</th>
                     <th>No. Batch / Lot</th>
                     <th>Gudang</th>
                     <th>Dokumen Terima</th>
                     <th class="text-center">Tgl. Masuk</th>
                     <th class="text-right">Qty Masuk</th>
                     <th class="text-center">Kode Rak</th>
                     <th class="text-center">Tingkat / Baris Rak</th>
                     <th class="text-center">Sekat / Kolom Rak</th>
                  </tr>
               </thead>
               <tbody>
                  <?php if (!empty($batches)): ?>
                     <?php $no = 1; foreach($batches as $b): ?>
                     <tr>
                        <td><?= $no++; ?></td>
                        <td>
                           <strong><?= _ent($b->nama_produk); ?></strong><br>
                           <small class="text-muted"><?= _ent($b->kode_produk); ?></small>
                        </td>
                        <td><span class="label label-primary" style="font-size:13px; padding:5px 8px;"><i class="fa fa-qrcode"></i> <?= _ent($b->batch_no); ?></span></td>
                        <td><?= _ent($b->nama_gudang); ?></td>
                        <td><a href="<?= site_url('administrator/penerimaan_barang/view'); ?>" class="text-primary"><i class="fa fa-file-text-o"></i> <?= _ent($b->nomor_penerimaan); ?></a></td>
                        <td class="text-center"><?= $b->tanggal_terima ? date('d/m/Y', strtotime($b->tanggal_terima)) : '-'; ?></td>
                        <td class="text-right"><strong><?= number_format($b->total_qty_masuk, 0, ',', '.'); ?></strong></td>
                        <td class="text-center"><span class="label label-info" style="font-size:12px; padding:4px 9px;"><i class="fa fa-tag"></i> <?= _ent($b->kode_rak ?: 'RAK-01'); ?></span></td>
                        <td class="text-center"><span class="label label-info" style="border-radius:12px; padding:4px 10px; font-size:11px; font-weight:600; background-color:#3498db !important;"><?= (strpos(strtolower($b->baris ?? ''), 'tingkat') !== false) ? _ent($b->baris) : 'Tingkat ' . _ent($b->baris ?: '1'); ?></span></td>
                        <td class="text-center"><span class="label label-success" style="border-radius:12px; padding:4px 10px; font-size:11px; font-weight:600; background-color:#2ecc71 !important;"><?= (strpos(strtolower($b->kolom ?? ''), 'kolom') !== false) ? _ent($b->kolom) : 'Kolom ' . _ent($b->kolom ?: '1'); ?></span></td>
                     </tr>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <tr>
                        <td colspan="9" class="text-center text-muted" style="padding:30px;">
                           <i class="fa fa-info-circle fa-2x"></i><br>
                           Belum ada data batch yang tercatat.<br>
                           <small>Batch No. diisi saat menerima barang di menu Penerimaan Barang.</small>
                        </td>
                     </tr>
                  <?php endif; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>

</section>
