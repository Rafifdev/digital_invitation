<?php /* produk_view.php - Visual Barcode & QR Code Display */ ?>
<section class="content-header">
   <h1>Detail Produk & Barcode <small><?= _ent($produk->nama_produk); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/produk'); ?>"><i class="fa fa-cube"></i> Produk</a></li>
      <li class="active">Detail & Barcode</li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <!-- Info Detail Produk -->
      <div class="col-md-6">
         <div class="box box-primary" style="border-radius:6px;">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-cube text-primary"></i> <?= _ent($produk->nama_produk); ?></h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('produk_update', function() use ($produk){ ?>
                     <a href="<?= site_url('administrator/produk/edit/'.$produk->id); ?>" class="btn btn-warning btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-edit"></i> Edit</a>
                  <?php }); ?>
                  <a href="<?= base_url('administrator/produk'); ?>" class="btn btn-default btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-arrow-left"></i> Kembali</a>
               </div>
            </div>
            <div class="box-body">
               <table class="table table-striped table-hover">
                  <tr><th width="35%">Kode Produk</th><td><span class="label label-default" style="font-size:12px;"><?= _ent($produk->kode_produk); ?></span></td></tr>
                  <tr><th>Nomor Barcode</th><td><strong class="text-primary" style="font-size:14px; letter-spacing:1px;"><?= _ent($produk->barcode ?? $produk->kode_produk); ?></strong></td></tr>
                  <tr><th>Nama Produk</th><td><strong><?= _ent($produk->nama_produk); ?></strong></td></tr>
                  <tr><th>Kategori</th><td><?= _ent($produk->nama_kategori ?? '-'); ?></td></tr>
                  <tr><th>Satuan</th><td><span class="badge bg-blue"><?= _ent($produk->singkatan ?? 'PCS'); ?></span> <?= _ent($produk->nama_satuan ?? ''); ?></td></tr>
                  <tr><th>Harga Beli</th><td>Rp <?= number_format($produk->harga_beli, 0, ',', '.'); ?></td></tr>
                  <tr><th>Harga Jual</th><td><strong class="text-success" style="font-size:14px;">Rp <?= number_format($produk->harga_jual, 0, ',', '.'); ?></strong></td></tr>
                  <tr><th>Stok Minimum</th><td><?= $produk->stok_minimum; ?> unit</td></tr>
                  <tr><th>Metode Stok</th><td><span class="label label-info"><?= $produk->metode_stok; ?></span></td></tr>
                  <tr><th>Batch Tracking</th><td><?= $produk->batch_tracking ? '<span class="label label-success">Aktif</span>' : '-'; ?></td></tr>
                  <tr><th>Tracking Exp</th><td><?= $produk->tanggal_kadaluarsa ? '<span class="label label-warning">Aktif</span>' : '-'; ?></td></tr>
                  <tr><th>Status Operasional</th><td><span class="label <?= $produk->status=='aktif'?'label-success':'label-danger'; ?>"><?= ucfirst($produk->status); ?></span></td></tr>
                  <tr><th>Deskripsi</th><td><?= nl2br(_ent($produk->deskripsi ?? '-')); ?></td></tr>
               </table>
            </div>
         </div>
      </div>

      <!-- Generator Visual Barcode 1D & QR Code 2D & Stok -->
      <div class="col-md-6">
         <div class="box box-info" style="border-radius:6px;">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-qrcode text-info"></i> Label Barcode &amp; QR Code Produk</h3>
            </div>
            <div class="box-body text-center" style="padding: 15px;">
               <?php 
                  $code_val = !empty($produk->barcode) ? $produk->barcode : $produk->kode_produk;
                  $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=" . urlencode($code_val);
                  $barcode_url = "https://bwipjs-api.metafloor.com/?bcid=code128&text=" . urlencode($code_val) . "&scale=2&rotate=N&includetext";
               ?>
               
               <div id="printable_barcode_card" style="background:#fff; border: 2px dashed #3c8dbc; border-radius:8px; padding: 15px; margin-bottom: 12px;">
                  <h4 style="margin-top:0; font-weight:700; color:#333; font-size:15px;"><?= _ent($produk->nama_produk); ?></h4>
                  <p class="text-muted" style="margin-bottom:10px; font-size:12px;">Harga: <strong class="text-success">Rp <?= number_format($produk->harga_jual, 0, ',', '.'); ?></strong></p>
                  
                  <div class="row">
                     <!-- Barcode 1D -->
                     <div class="col-xs-12" style="margin-bottom: 10px;">
                        <img src="<?= $barcode_url; ?>" alt="Barcode <?= $code_val; ?>" style="max-width: 100%; height: 50px; object-fit: contain;">
                     </div>
                     <!-- QR Code 2D -->
                     <div class="col-xs-12">
                        <img src="<?= $qr_url; ?>" alt="QR Code <?= $code_val; ?>" style="width: 110px; height: 110px; border: 1px solid #eee; padding: 4px; border-radius: 4px;">
                        <br><small class="text-muted" style="display:block; margin-top:4px; font-family:monospace;"><?= _ent($code_val); ?></small>
                     </div>
                  </div>
               </div>

               <button type="button" onclick="cetak_label_barcode()" class="btn btn-info btn-block btn-flat" style="border-radius:4px; font-weight:600;">
                  <i class="fa fa-print"></i> Cetak Label Stiker Barcode &amp; QR
               </button>
            </div>
         </div>

         <!-- Stok per Gudang -->
         <div class="box box-success" style="border-radius:6px;">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-cubes text-success"></i> Stok per Gudang</h3></div>
            <div class="box-body" style="padding:10px 15px;">
               <table class="table table-striped table-hover" style="margin-bottom:10px;">
                  <thead><tr style="background:#f8fafc;"><th>Gudang</th><th class="text-center">Stok Baik</th><th class="text-center">Stok Rusak</th></tr></thead>
                  <tbody>
                     <?php foreach($stok_list as $s): ?>
                     <tr>
                        <td><?= _ent($s->nama_gudang); ?></td>
                        <td class="text-center"><?php if($s->qty <= $produk->stok_minimum): ?><span class="text-danger"><strong><?= number_format($s->qty, 0); ?></strong> <i class="fa fa-exclamation-triangle" title="Stok menipis!"></i></span><?php else: ?><strong><?= number_format($s->qty, 0); ?></strong><?php endif; ?></td>
                        <td class="text-center"><?= $s->qty_rusak > 0 ? '<span class="text-danger">'.number_format($s->qty_rusak,0).'</span>' : '-'; ?></td>
                     </tr>
                     <?php endforeach; ?>
                     <?php if(empty($stok_list)): ?><tr><td colspan="3" class="text-center text-muted">Belum ada stok</td></tr><?php endif; ?>
                  </tbody>
               </table>
               <?php is_allowed('stok_list', function() use ($produk){ ?>
               <a href="<?= site_url('administrator/stok/kartu/'.$produk->id); ?>" class="btn btn-primary btn-sm btn-block btn-flat" style="border-radius:4px;"><i class="fa fa-list-alt"></i> Lihat Kartu Stok Produk</a>
               <?php }); ?>
            </div>
         </div>
      </div>
   </div>

   <!-- Full-Width Card for Unified Price History & Audit -->
   <div class="row" style="margin-top: 10px;">
      <div class="col-md-12">
         <div class="box box-default" style="border-radius:6px; border-top: 3px solid #3c8dbc;">
            <div class="box-header with-border" style="background:#fafafa;">
               <h3 class="box-title" style="font-weight:700; color:#1e293b;"><i class="fa fa-line-chart text-primary"></i> Riwayat &amp; Audit Perubahan Harga Produk</h3>
            </div>
            <div class="box-body" style="padding: 20px;">
               <div class="row">
                  <!-- Log Audit Perubahan Harga Master -->
                  <div class="col-md-6">
                     <h4 style="font-weight:700; font-size:14px; margin-top:0; margin-bottom:12px; color:#0369a1;">
                        <i class="fa fa-history text-primary"></i> Log Audit Perubahan Harga Master
                     </h4>
                     <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                           <thead>
                              <tr style="background:#e0f2fe; color:#0369a1;">
                                 <th width="35" class="text-center">No</th>
                                 <th width="120">Waktu</th>
                                 <th>Harga Beli (Lama &rarr; Baru)</th>
                                 <th>Harga Jual (Lama &rarr; Baru)</th>
                                 <th width="80" class="text-center">User</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php 
                              $no_log = 1;
                              if (!empty($harga_logs)):
                                 foreach($harga_logs as $log): 
                                    $hb_changed = ($log->harga_beli_lama != $log->harga_beli_baru);
                                    $hj_changed = ($log->harga_jual_lama != $log->harga_jual_baru);
                                    
                                    $hb_diff = $log->harga_beli_baru - $log->harga_beli_lama;
                                    $hb_trend = $hb_diff > 0 ? '<span class="badge bg-red" style="font-size:10px; margin-left:3px;"><i class="fa fa-arrow-up"></i> Naik</span>' : ($hb_diff < 0 ? '<span class="badge bg-green" style="font-size:10px; margin-left:3px;"><i class="fa fa-arrow-down"></i> Turun</span>' : '');

                                    $hj_diff = $log->harga_jual_baru - $log->harga_jual_lama;
                                    $hj_trend = $hj_diff > 0 ? '<span class="badge bg-green" style="font-size:10px; margin-left:3px;"><i class="fa fa-arrow-up"></i> Naik</span>' : ($hj_diff < 0 ? '<span class="badge bg-red" style="font-size:10px; margin-left:3px;"><i class="fa fa-arrow-down"></i> Turun</span>' : '');
                                 ?>
                                 <tr>
                                    <td class="text-center"><?= $no_log++; ?></td>
                                    <td><small><i class="fa fa-clock-o text-muted"></i> <?= date('d/m/Y H:i', strtotime($log->created_at)); ?></small></td>
                                    <td>
                                       <?php if($hb_changed): ?>
                                          <span class="text-muted" style="text-decoration:line-through;">Rp <?= number_format($log->harga_beli_lama, 0, ',', '.'); ?></span> 
                                          <i class="fa fa-long-arrow-right text-muted" style="margin: 0 3px;"></i> 
                                          <strong class="text-danger">Rp <?= number_format($log->harga_beli_baru, 0, ',', '.'); ?></strong> <?= $hb_trend; ?>
                                       <?php else: ?>
                                          <span class="text-muted">Rp <?= number_format($log->harga_beli_baru, 0, ',', '.'); ?> <small class="text-muted">(Tidak Berubah)</small></span>
                                       <?php endif; ?>
                                    </td>
                                    <td>
                                       <?php if($hj_changed): ?>
                                          <span class="text-muted" style="text-decoration:line-through;">Rp <?= number_format($log->harga_jual_lama, 0, ',', '.'); ?></span> 
                                          <i class="fa fa-long-arrow-right text-muted" style="margin: 0 3px;"></i> 
                                          <strong class="text-success">Rp <?= number_format($log->harga_jual_baru, 0, ',', '.'); ?></strong> <?= $hj_trend; ?>
                                       <?php else: ?>
                                          <span class="text-muted">Rp <?= number_format($log->harga_jual_baru, 0, ',', '.'); ?> <small class="text-muted">(Tidak Berubah)</small></span>
                                       <?php endif; ?>
                                    </td>
                                    <td class="text-center"><span class="label label-primary"><?= _ent($log->created_by); ?></span></td>
                                 </tr>
                              <?php endforeach; else: ?>
                                 <tr><td colspan="5" class="text-center text-muted" style="padding:15px;">Belum ada catatan perubahan harga master.</td></tr>
                              <?php endif; ?>
                           </tbody>
                        </table>
                     </div>
                  </div>

                  <!-- Riwayat Harga Beli per Supplier (PO) -->
                  <div class="col-md-6">
                     <h4 style="font-weight:700; font-size:14px; margin-top:0; margin-bottom:12px; color:#92400e;">
                        <i class="fa fa-shopping-cart text-warning"></i> Riwayat Harga Beli per Supplier (PO)
                     </h4>
                     <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                           <thead>
                              <tr style="background:#fef3c7; color:#92400e;">
                                 <th width="35" class="text-center">No</th>
                                 <th>Tanggal &amp; No PO</th>
                                 <th>Nama Supplier</th>
                                 <th class="text-right">Harga Modal PO</th>
                                 <th class="text-center">Tren</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php 
                              $no = 1;
                              $total = count($riwayat_harga);
                              foreach($riwayat_harga as $index => $r): 
                                 $older_harga = ($index + 1 < $total) ? $riwayat_harga[$index + 1]->harga_beli : $r->harga_beli;
                                 
                                 $trend_icon = '<i class="fa fa-minus text-muted" title="Sama"></i>';
                                 if ($r->harga_beli > $older_harga) {
                                     $trend_icon = '<i class="fa fa-arrow-up text-danger" title="Naik"></i>';
                                 } elseif ($r->harga_beli < $older_harga) {
                                     $trend_icon = '<i class="fa fa-arrow-down text-success" title="Turun"></i>';
                                 }
                                 $mata_uang = !empty($r->mata_uang) ? $r->mata_uang : 'IDR';
                                 $is_idr = ($mata_uang == 'IDR');
                              ?>
                              <tr>
                                 <td class="text-center"><?= $no++; ?></td>
                                 <td>
                                    <i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime($r->tanggal)); ?>
                                    <br><span class="label label-default" style="font-size:10px;"><?= _ent($r->nomor_po); ?></span>
                                 </td>
                                 <td><strong><?= _ent($r->nama_supplier); ?></strong></td>
                                 <td class="text-right">
                                    <strong class="<?= $is_idr ? 'text-danger' : 'text-primary' ?>">
                                       <?= $mata_uang ?> <?= number_format($r->harga_beli, ($is_idr ? 0 : 2), ',', '.'); ?>
                                    </strong>
                                 </td>
                                 <td class="text-center"><?= $trend_icon; ?></td>
                              </tr>
                              <?php endforeach; ?>
                              <?php if(empty($riwayat_harga)): ?>
                              <tr><td colspan="5" class="text-center text-muted" style="padding:15px;">Belum ada riwayat transaksi PO ke supplier.</td></tr>
                              <?php endif; ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
function cetak_label_barcode() {
   var printContents = document.getElementById('printable_barcode_card').outerHTML;
   var win = window.open('', '', 'height=500,width=600');
   win.document.write('<html><head><title>Cetak Label Barcode</title>');
   win.document.write('<style>body{font-family:Arial,sans-serif; text-align:center; padding:20px;} img{max-width:100%;}</style>');
   win.document.write('</head><body>');
   win.document.write(printContents);
   win.document.write('</body></html>');
   win.document.close();
   win.focus();
   setTimeout(function(){ win.print(); win.close(); }, 500);
}
</script>
