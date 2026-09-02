<?php
/**
 * View: Dashboard Stok
 * File: stok_list.php
 */
?>
<section class="content-header">
   <h1>
      Dashboard Stok
      <small>Real-Time</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Stok</li>
   </ol>
</section>

<section class="content">

   <!-- Card Summary -->
   <div class="row">
      <div class="col-md-3 col-sm-6 col-xs-12">
         <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
            <div class="info-box-content">
               <span class="info-box-text">Total Produk</span>
               <span class="info-box-number"><?= is_object($summary) ? $summary->total_produk : ($summary['total_produk'] ?? 0); ?></span>
            </div>
         </div>
      </div>
      
      <div class="col-md-3 col-sm-6 col-xs-12">
         <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
            <div class="info-box-content">
               <span class="info-box-text" style="font-size:12px;">Total Keseluruhan Stok</span>
               <span class="info-box-number"><?= number_format(is_object($summary) ? $summary->total_stok : ($summary['total_stok'] ?? 0), 0, ',', '.'); ?></span>
            </div>
         </div>
      </div>
      
      <div class="col-md-3 col-sm-6 col-xs-12">
         <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
               <span class="info-box-text">Stok Menipis</span>
               <span class="info-box-number"><?= is_object($summary) ? $summary->stok_menipis : ($summary['stok_menipis'] ?? 0); ?> produk</span>
            </div>
         </div>
      </div>
      
      <div class="col-md-3 col-sm-6 col-xs-12">
         <div class="info-box bg-red">
            <span class="info-box-icon"><i class="fa fa-times-circle"></i></span>
            <div class="info-box-content">
               <span class="info-box-text">Stok Habis</span>
               <span class="info-box-number"><?= is_object($summary) ? $summary->stok_habis : ($summary['stok_habis'] ?? 0); ?> produk</span>
            </div>
         </div>
      </div>
   </div>

   <!-- Filter dan Data Stok -->
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-database"></i> Data Stok Barang</h3>
               <div class="box-tools pull-right">
                  <a href="<?= site_url('administrator/stok/batch_tracking'); ?>" class="btn btn-info btn-sm" style="margin-right:5px;">
                     <i class="fa fa-qrcode"></i> Batch / Lot Tracking
                  </a>
                  <form class="form-inline" style="display:inline-block;" action="<?= base_url('administrator/stok'); ?>" method="GET">
                     <div class="form-group">
                        <select class="form-control input-sm" name="gudang_id" onchange="this.form.submit()">
                           <option value="">-- Semua Gudang --</option>
                           <?php foreach ($gudangs as $g): ?>
                              <option <?= $gudang_sel == $g->id ? 'selected' : ''; ?> value="<?= $g->id; ?>">
                                 <?= _ent($g->nama_gudang); ?>
                              </option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <?php if (is_allowed('stok_export', false)): ?>
                        <a href="<?= site_url('administrator/stok/export?gudang_id=' . $gudang_sel); ?>" class="btn btn-default btn-sm">
                           <i class="fa fa-file-excel-o"></i> Export Excel
                        </a>
                     <?php endif; ?>
                  </form>
               </div>
            </div>

            <div class="box-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th>Kode Produk</th>
                           <th>Nama Produk</th>
                           <?php if (!$gudang_sel): ?>
                              <th>Gudang</th>
                           <?php endif; ?>
                           <th>Lokasi Rak</th>
                           <th class="text-center">Stok</th>
                           <th class="text-center">Rusak</th>
                           <th class="text-center">Min. Stok</th>
                           <th>Status</th>
                           <th class="text-center">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (!empty($stoks)): ?>
                           <?php foreach ($stoks as $s): ?>
                              <tr class="<?= $s->qty == 0 ? 'danger' : ($s->qty <= $s->stok_minimum ? 'warning' : ''); ?>">
                                 <td><?= _ent($s->kode_produk); ?></td>
                                 <td><strong><?= _ent($s->nama_produk); ?></strong></td>
                                 <?php if (!$gudang_sel): ?>
                                    <td><?= _ent($s->nama_gudang); ?></td>
                                 <?php endif; ?>
                                 <td>
                                    <?php if($s->kode_rak): ?>
                                       <span class="label label-info"><i class="fa fa-cubes"></i> <?= _ent($s->kode_rak); ?></span>
                                    <?php else: ?>
                                       <span class="text-muted">-</span>
                                    <?php endif; ?>
                                 </td>
                                 <td class="text-center"><strong><?= number_format((float)$s->qty, 0, ',', '.'); ?></strong></td>
                                 <td class="text-center <?= $s->qty_rusak > 0 ? 'text-danger' : ''; ?>">
                                    <?= $s->qty_rusak > 0 ? number_format((float)$s->qty_rusak, 0, ',', '.') : '-'; ?>
                                 </td>
                                 <td class="text-center"><?= $s->stok_minimum; ?></td>
                                 <td>
                                    <?php if ($s->qty == 0): ?>
                                       <span class="label label-danger"><i class="fa fa-times"></i> Habis</span>
                                    <?php elseif ($s->qty <= $s->stok_minimum): ?>
                                       <span class="label label-warning"><i class="fa fa-warning"></i> Menipis</span>
                                    <?php else: ?>
                                       <span class="label label-success"><i class="fa fa-check"></i> Normal</span>
                                    <?php endif; ?>
                                 </td>
                                 <td class="text-center">
                                    <a href="<?= site_url('administrator/stok/kartu/' . $s->produk_id . ($s->gudang_id ? '?gudang_id=' . $s->gudang_id : '')); ?>" class="btn btn-xs btn-primary" title="Kartu Stok">
                                       <i class="fa fa-list-alt"></i> Kartu Stok
                                    </a>
                                    <?php if (is_allowed('stok_delete', false)): ?>
                                       <a href="javascript:void(0);" data-href="<?= site_url('administrator/stok/delete/' . $s->id); ?>" class="btn btn-xs btn-danger remove-data" style="margin: 2px 1px;" title="Hapus">
                                          <i class="fa fa-trash"></i> Hapus
                                       </a>
                                    <?php endif; ?>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr>
                              <td colspan="<?= !$gudang_sel ? '9' : '8'; ?>" class="text-center text-muted">Belum ada data stok barang.</td>
                           </tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <div class="row" style="margin-top: 15px;">
                  <div class="col-md-6">
                     <span class="text-muted">Total: <strong><?= $stok_counts; ?></strong> record stok</span>
                  </div>
                  <div class="col-md-6">
                     <div class="pull-right">
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Peringatan Stok Menipis -->
   <?php if (!empty($menipis)): ?>
      <div class="row">
         <div class="col-md-12">
            <div class="box box-warning">
               <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-warning text-yellow"></i> Peringatan: Stok Menipis & Habis</h3>
               </div>
               <div class="box-body">
                  <table class="table table-bordered table-striped table-condensed">
                     <thead>
                        <tr>
                           <th>Nama Produk</th>
                           <th>Gudang</th>
                           <th class="text-center">Stok Saat Ini</th>
                           <th class="text-center">Batas Minimal</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach ($menipis as $m): ?>
                           <tr class="<?= $m->qty == 0 ? 'danger' : 'warning'; ?>">
                              <td><?= _ent($m->nama_produk); ?></td>
                              <td><?= _ent($m->nama_gudang); ?></td>
                              <td class="text-center"><strong><?= number_format((float)$m->qty, 0, ',', '.'); ?></strong></td>
                              <td class="text-center"><?= $m->stok_minimum; ?></td>
                           </tr>
                        <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   <?php endif; ?>

</section>

<script>
$(document).ready(function(){
   $('.remove-data').click(function(){
      var url = $(this).attr('data-href');
      swal({
         title: 'Hapus Stok?',
         text: 'Data stok yang dihapus tidak dapat dikembalikan!',
         type: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#DD6B55',
         confirmButtonText: 'Ya, Hapus!',
         cancelButtonText: 'Batal',
         closeOnConfirm: true
      }, function(ok){
         if (ok) { window.location.href = url; }
      });
   });
});
</script>
