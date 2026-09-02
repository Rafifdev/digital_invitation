<?php 
/* transfer_stok_view.php */ 
$st = [
   'draft'     => ['label' => 'DRAFT',     'class' => 'label-default'],
   'submitted' => ['label' => 'SUBMITTED', 'class' => 'label-info'],
   'approved'  => ['label' => 'APPROVED',  'class' => 'label-primary'],
   'shipped'   => ['label' => 'SHIPPED',   'class' => 'label-warning'],
   'received'  => ['label' => 'RECEIVED',  'class' => 'label-success']
];
$curr_st = $st[$transfer->status] ?? ['label' => strtoupper($transfer->status), 'class' => 'label-default'];
?>
<section class="content-header">
   <h1>Detail Transfer Stok <small><?= _ent($transfer->nomor_transfer); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/transfer_stok'); ?>"><i class="fa fa-exchange"></i> Transfer Stok</a></li>
      <li class="active">Detail</li>
   </ol>
</section>

<section class="content">
   <!-- Top Action & Status Bar (No Overlap) -->
   <div class="box box-solid" style="border-top: 3px solid #3c8dbc; margin-bottom: 20px;">
      <div class="box-body" style="padding: 12px 20px;">
         <div class="row" style="display: flex; align-items: center; flex-wrap: wrap;">
            <div class="col-md-5 col-sm-6" style="margin-bottom: 5px;">
               <h3 style="margin: 0; font-size: 20px; font-weight: bold; display: inline-block; vertical-align: middle;">
                  <?= _ent($transfer->nomor_transfer); ?>
               </h3>
               <span class="label <?= $curr_st['class']; ?>" style="font-size: 13px; margin-left: 10px; padding: 5px 10px; vertical-align: middle;">
                  <?= $curr_st['label']; ?>
               </span>
            </div>
            <div class="col-md-7 col-sm-6 text-right" style="margin-bottom: 5px;">
               <a href="<?= site_url('administrator/transfer_stok/cetak/' . $transfer->id); ?>" target="_blank" class="btn btn-info btn-flat"><i class="fa fa-print"></i> Cetak PDF</a>
               
               <?php if($transfer->status=='draft'): ?>
                  <?php is_allowed('transfer_stok_update', function() use ($transfer){ ?>
                     <a href="<?= site_url('administrator/transfer_stok/submit/'.$transfer->id); ?>" class="btn btn-primary btn-flat"><i class="fa fa-paper-plane"></i> Submit</a>
                  <?php }); ?>
               <?php endif; ?>

               <?php if($transfer->status=='submitted'): ?>
                  <?php is_allowed('transfer_stok_approve', function() use ($transfer){ ?>
                     <a href="<?= site_url('administrator/transfer_stok/approve/'.$transfer->id); ?>" class="btn btn-success btn-flat"><i class="fa fa-check"></i> Approve</a>
                  <?php }); ?>
               <?php endif; ?>

               <?php if($transfer->status=='approved'): ?>
                  <?php is_allowed('transfer_stok_update', function() use ($transfer){ ?>
                     <button type="button" class="btn btn-warning btn-flat" onclick="promptKirim()"><i class="fa fa-truck"></i> Kirim</button>
                  <?php }); ?>
               <?php endif; ?>

               <?php if($transfer->status=='shipped'): ?>
                  <?php is_allowed('transfer_stok_update', function() use ($transfer){ ?>
                     <a href="<?= site_url('administrator/transfer_stok/terima/'.$transfer->id); ?>" class="btn btn-success btn-flat" onclick="return confirm('Konfirmasi penerimaan fisik barang? Stok kedua gudang akan otomatis diperbarui!')"><i class="fa fa-check-circle"></i> Terima Barang</a>
                  <?php }); ?>
               <?php endif; ?>

               <a href="<?= base_url('administrator/transfer_stok'); ?>" class="btn btn-default btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
         </div>
      </div>
   </div>

   <div class="row">
      <!-- Header Information -->
      <div class="col-md-5">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-info-circle text-primary"></i> Informasi Transfer</h3>
            </div>
            <div class="box-body" style="padding: 0;">
               <table class="table table-striped" style="margin-bottom: 0;">
                  <tr>
                     <th style="width: 40%; padding-left: 15px;">No Transfer</th>
                     <td><strong><?= _ent($transfer->nomor_transfer); ?></strong></td>
                  </tr>
                  <tr>
                     <th style="padding-left: 15px;">Gudang Asal</th>
                     <td><strong class="text-primary"><?= _ent($transfer->nama_gudang_asal); ?></strong></td>
                  </tr>
                  <tr>
                     <th style="padding-left: 15px;">Gudang Tujuan</th>
                     <td><strong class="text-success"><?= _ent($transfer->nama_gudang_tujuan); ?></strong></td>
                  </tr>
                  <tr>
                     <th style="padding-left: 15px;">Tanggal Transfer</th>
                     <td><?= date('d/m/Y', strtotime($transfer->tanggal_transfer)); ?></td>
                  </tr>
                  <tr>
                     <th style="padding-left: 15px;">Est. Tiba</th>
                     <td>
                        <?php if ($transfer->tanggal_estimasi_tiba): ?>
                           <?= date('d/m/Y', strtotime($transfer->tanggal_estimasi_tiba)); ?>
                        <?php else: ?>
                           <span class="text-muted"><em>Belum Diatur</em></span>
                        <?php endif; ?>
                     </td>
                  </tr>
                  <tr>
                     <th style="padding-left: 15px;">No Bukti Kirim</th>
                     <td>
                        <?php if (!empty($transfer->nomor_bukti_kirim)): ?>
                           <span class="label label-warning" style="font-size: 11px;"><?= _ent($transfer->nomor_bukti_kirim); ?></span>
                        <?php else: ?>
                           <span class="text-muted"><i class="fa fa-clock-o"></i> Belum Dikirim</span>
                        <?php endif; ?>
                     </td>
                  </tr>
                  <tr>
                     <th style="padding-left: 15px;">Catatan</th>
                     <td><?= !empty($transfer->catatan) ? _ent($transfer->catatan) : '<span class="text-muted">-</span>'; ?></td>
                  </tr>
                  <tr>
                     <th style="padding-left: 15px;">Dibuat Oleh</th>
                     <td><i class="fa fa-user text-muted"></i> <?= _ent($transfer->created_by); ?></td>
                  </tr>
                  <?php if($transfer->approved_by): ?>
                     <tr>
                        <th style="padding-left: 15px;">Diapprove Oleh</th>
                        <td><i class="fa fa-check text-success"></i> <?= _ent($transfer->approved_by); ?></td>
                     </tr>
                  <?php endif; ?>
               </table>
            </div>
         </div>
      </div>

      <!-- Items List -->
      <div class="col-md-7">
         <div class="box box-default">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-cubes text-warning"></i> Item Transfer</h3>
            </div>
            <div class="box-body" style="padding: 0;">
               <div class="table-responsive">
                  <table class="table table-striped table-hover" style="margin-bottom: 0;">
                     <thead>
                        <tr style="background: #f8fafc;">
                           <th style="width: 5%; text-align: center;">#</th>
                           <th style="width: 40%;">Produk</th>
                           <th style="width: 15%; text-align: center;">Qty Transfer</th>
                           <th style="width: 15%; text-align: center;">Satuan</th>
                           <th style="width: 15%; text-align: center;">Qty Terima</th>
                           <th style="width: 10%;">Batch No</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach($items as $i=>$it): ?>
                           <?php $qty_terima_val = isset($it->qty_terima) ? $it->qty_terima : ($transfer->status == 'received' ? $it->qty : 0); ?>
                           <tr>
                              <td style="text-align: center;"><?= $i+1; ?></td>
                              <td>
                                 <strong><?= _ent($it->nama_produk); ?></strong><br>
                                 <small class="text-muted"><?= _ent($it->kode_produk); ?></small>
                              </td>
                              <td style="text-align: center;"><strong><?= number_format($it->qty, 0, ',', '.'); ?></strong></td>
                              <td style="text-align: center;"><span class="badge bg-blue"><?= _ent($it->singkatan ?? $it->nama_satuan ?? '-'); ?></span></td>
                              <td style="text-align: center;">
                                 <?php if ($transfer->status == 'received'): ?>
                                    <span class="text-success" style="font-weight: bold;"><?= number_format($qty_terima_val, 0, ',', '.'); ?></span>
                                 <?php else: ?>
                                    <span class="text-muted">-</span>
                                 <?php endif; ?>
                              </td>
                              <td><?= _ent($it->batch_no ?? '-'); ?></td>
                           </tr>
                        <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
function promptKirim() {
   var resi = prompt("Masukkan No. Bukti Kirim / Resi Pengiriman (Kosongkan untuk otomatis):");
   if (resi !== null) {
      window.location.href = "<?= site_url('administrator/transfer_stok/kirim/' . $transfer->id); ?>?bukti_kirim=" + encodeURIComponent(resi);
   }
}
</script>
