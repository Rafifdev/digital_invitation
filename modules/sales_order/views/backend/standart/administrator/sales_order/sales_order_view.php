<?php /* sales_order_view.php */
$status_color=['draft'=>'default','confirmed'=>'info','processing'=>'primary','shipped'=>'warning','delivered'=>'success','cancelled'=>'danger'];
?>
<section class="content-header">
   <h1>Detail SO <small><?= _ent($so->nomor_so); ?></small></h1>
   <ol class="breadcrumb"><li><a href="<?= base_url('administrator/sales_order'); ?>"><i class="fa fa-file-text"></i> Sales Order</a></li><li class="active">Detail</li></ol>
</section>
<section class="content">
<div class="row">
   <div class="col-md-12">
      <div class="box box-primary">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-shopping-cart"></i> Sales Order <?= _ent($so->nomor_so); ?> &nbsp;
               <span class="label label-<?= $status_color[$so->status]??'default'; ?>"><?= strtoupper($so->status); ?></span>
            </h3>
            <div class="box-tools pull-right">
               <a href="<?= site_url('administrator/sales_order/cetak/' . $so->id); ?>" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-print"></i> Cetak PDF</a> 
               <?php if($so->status=='draft'): ?><?php is_allowed('sales_order_update', function() use ($so){ ?><a href="<?= site_url('administrator/sales_order/konfirmasi/'.$so->id); ?>" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Konfirmasi</a><?php }); ?><?php endif; ?>
               <?php if(in_array($so->status,['confirmed','processing'])): ?><?php is_allowed('invoice_add', function() use ($so){ ?><a href="<?= site_url('administrator/invoice/from_so/'.$so->id); ?>" class="btn btn-success btn-sm"><i class="fa fa-file-text-o"></i> Buat Invoice</a><?php }); ?><?php endif; ?>
               <a href="<?= base_url('administrator/sales_order'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
         </div>
         <div class="box-body">
            
            <!-- Header Grid: 2 Columns -->
            <div class="row" style="margin-bottom: 20px;">
               <div class="col-md-6">
                  <div class="panel panel-default" style="border-radius: 4px; box-shadow: none;">
                     <div class="panel-heading" style="background:#f8f9fa; font-weight:bold;"><i class="fa fa-user"></i> Informasi Pelanggan &amp; Order</div>
                     <div class="panel-body" style="padding: 10px 15px;">
                        <table class="table table-condensed table-borderless" style="margin-bottom:0;">
                           <tr><th style="width:35%;">Pelanggan:</th><td><strong style="font-size:14px; color:#333;"><?= _ent($so->nama_pelanggan); ?></strong> &nbsp;<span class="label label-info"><?= strtoupper($so->tipe_pelanggan ?? 'RETAIL'); ?></span></td></tr>
                           <tr><th>Alamat:</th><td><?= _ent($so->alamat_pelanggan ?? '-'); ?></td></tr>
                           <tr><th>No. Telp:</th><td><?= _ent($so->telp_pelanggan ?? '-'); ?></td></tr>
                           <tr><th>Gudang Sumber:</th><td><?= _ent($so->nama_gudang??'-'); ?></td></tr>
                           <tr><th>Dibuat Oleh:</th><td><?= _ent($so->created_by); ?></td></tr>
                        </table>
                     </div>
                  </div>
               </div>
               
               <div class="col-md-6">
                  <div class="panel panel-default" style="border-radius: 4px; box-shadow: none;">
                     <div class="panel-heading" style="background:#f8f9fa; font-weight:bold;"><i class="fa fa-calendar"></i> Tanggal &amp; Status Pesanan</div>
                     <div class="panel-body" style="padding: 10px 15px;">
                        <table class="table table-condensed table-borderless" style="margin-bottom:0;">
                           <tr><th style="width:40%;">Tanggal SO:</th><td><?= date('d/m/Y', strtotime($so->tanggal_so)); ?></td></tr>
                           <tr><th>Est. Tanggal Kirim:</th><td><?= $so->tanggal_estimasi_kirim ? date('d/m/Y', strtotime($so->tanggal_estimasi_kirim)) : '-'; ?></td></tr>
                           <tr><th>Status SO:</th><td><strong class="text-primary"><?= strtoupper($so->status); ?></strong></td></tr>
                        </table>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Item Table -->
            <h4 style="border-bottom:2px solid #3c8dbc; padding-bottom:5px; margin-bottom:15px; font-weight:bold; color:#333;"><i class="fa fa-cubes"></i> Item Pesanan Barang</h4>
            <div class="table-responsive" style="margin-bottom: 25px;">
               <table class="table table-bordered table-striped">
                  <thead>
                     <tr style="background:#f4f6f9;">
                        <th style="width:5%; text-align:center;">#</th>
                        <th style="width:40%;">Nama Produk</th>
                        <th style="width:10%; text-align:center;">Qty</th>
                        <th style="width:15%; text-align:right;">Harga Satuan</th>
                        <th style="width:10%; text-align:right;">Disc (%)</th>
                        <th style="width:20%; text-align:right;">Subtotal</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach($items as $i=>$item): ?>
                     <?php 
                        $so_disc_pct = (float)($so->diskon_persen_total ?? 0);
                        if ($so_disc_pct == 0 && !empty($so->diskon_total) && !empty($so->subtotal) && $so->subtotal > 0) {
                           $so_disc_pct = round(($so->diskon_total / $so->subtotal) * 100, 1);
                        }
                        if ($so_disc_pct > 100) {
                           $so_disc_pct = min(100, $so_disc_pct / 100);
                        }
                        $it_disc = (float)(($item->diskon_persen > 0) ? $item->diskon_persen : $so_disc_pct);
                        if ($it_disc > 100) {
                           if ($it_disc <= 10000) {
                              $it_disc = $it_disc / 100;
                           } else {
                              $it_disc = $so_disc_pct;
                           }
                        }
                        $it_disc_fmt = ($it_disc == floor($it_disc)) ? number_format($it_disc, 0) : number_format($it_disc, 1);
                     ?>
                     <tr>
                        <td class="text-center"><?= $i+1; ?></td>
                        <td>
                           <strong><?= _ent($item->nama_produk); ?></strong><br>
                           <small class="text-muted">Kode: <?= _ent($item->kode_produk); ?></small>
                           <?php if($item->kode_rak): ?>
                              <br><span class="label bg-purple" style="font-size:10px;"><i class="fa fa-map-marker"></i> Rak: <?= _ent($item->kode_rak); ?> - Baris <?= _ent($item->baris); ?></span>
                           <?php endif; ?>
                        </td>
                        <td class="text-center"><strong><?= number_format((float)$item->qty, 0, ',', '.'); ?></strong> <?= _ent($item->singkatan??$item->satuan); ?></td>
                        <td class="text-right">Rp <?= number_format($item->harga_satuan, 0, ',', '.'); ?></td>
                        <td class="text-right"><strong><?= $it_disc_fmt; ?>%</strong></td>
                        <td class="text-right"><strong>Rp <?= number_format($item->subtotal, 0, ',', '.'); ?></strong></td>
                     </tr>
                     <?php endforeach; ?>
                  </tbody>
               </table>
            </div>

            <!-- Footer Grid: Catatan & Ringkasan Total -->
            <?php 
               $subtotal_val = (float) ($so->subtotal ?? 0);
               $diskon_val   = (float) ($so->diskon_total ?? 0);
               $pajak_pct    = (float) ($so->pajak_persen ?? 0);
               $pajak_val    = (float) ($so->pajak_nominal ?? 0);
               $total_val    = (float) ($so->total ?? 0);
               $diskon_pct_display = (float) ($so->diskon_persen_total ?? ($subtotal_val > 0 ? round(($diskon_val / $subtotal_val) * 100, 1) : 0));
            ?>

            <div class="row">
               <div class="col-md-7">
                  <?php if (!empty($so->catatan)): ?>
                     <div class="callout callout-info" style="border-left-width: 4px; background:#f4f8fa; border-color:#5bc0de; color:#31708f; padding: 12px 15px;">
                        <h5 style="margin-top:0; font-weight:bold;"><i class="fa fa-commenting-o"></i> Catatan Pesanan:</h5>
                        <p style="margin-bottom:0; font-style:italic;"><?= nl2br(_ent($so->catatan)); ?></p>
                     </div>
                  <?php endif; ?>
               </div>
               
               <div class="col-md-5">
                  <div class="well well-sm" style="background: #fff; border: 1px solid #e3e3e3; padding: 15px; border-radius: 4px;">
                     <table class="table table-condensed" style="margin-bottom: 0;">
                        <tr>
                           <td style="border:none;"><strong>Subtotal:</strong></td>
                           <td style="border:none;" class="text-right">Rp <?= number_format($subtotal_val, 0, ',', '.'); ?></td>
                        </tr>
                        <?php if ($diskon_val > 0): ?>
                        <tr>
                           <td style="border:none;" class="text-danger">Diskon Keseluruhan <?= $diskon_pct_display > 0 ? '('.$diskon_pct_display.'%)' : ''; ?>:</td>
                           <td style="border:none;" class="text-right text-danger">- Rp <?= number_format($diskon_val, 0, ',', '.'); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($pajak_val > 0): ?>
                        <tr>
                           <td style="border:none;">Pajak (<?= $pajak_pct; ?>%):</td>
                           <td style="border:none;" class="text-right">Rp <?= number_format($pajak_val, 0, ',', '.'); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr style="font-size: 16px; border-top: 2px solid #ddd;">
                           <td style="padding-top:10px;"><strong>TOTAL SALES ORDER:</strong></td>
                           <td style="padding-top:10px;" class="text-right"><strong class="text-success">Rp <?= number_format($total_val, 0, ',', '.'); ?></strong></td>
                        </tr>
                     </table>
                  </div>
               </div>
            </div>

         </div>
      </div>
   </div>
</div>
</section>
