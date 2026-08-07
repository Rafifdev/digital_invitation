<?php /* invoice_view.php */ $status_c=['draft'=>'default','sent'=>'info','partial'=>'warning','paid'=>'success','overdue'=>'danger','cancelled'=>'danger']; ?>
<section class="content-header">
   <h1>Detail Invoice <small><?= _ent($invoice->nomor_invoice); ?></small></h1>
   <ol class="breadcrumb"><li><a href="<?= base_url('administrator/invoice'); ?>"><i class="fa fa-file-text-o"></i> Invoice</a></li><li class="active">Detail</li></ol>
</section>
<section class="content">
<div class="row">
   <div class="col-md-12">
      <div class="box box-primary">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-file-text-o"></i> Invoice <?= _ent($invoice->nomor_invoice); ?> 
               <span class="label label-<?= $status_c[$invoice->status]??'default'; ?>"><?= strtoupper($invoice->status); ?></span>
               <?php if (!empty($invoice->status_kirim)): ?>
                  <span class="label label-warning"><?= strtoupper($invoice->status_kirim); ?></span>
               <?php endif; ?>
            </h3>
            <div class="box-tools pull-right">
               <a href="<?= site_url('administrator/invoice/print_invoice/'.$invoice->id); ?>" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-print"></i> Cetak PDF</a>
               <?php if($invoice->status=='draft'): ?><?php is_allowed('invoice_update', function() use ($invoice){ ?>
               <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal_kirim"><i class="fa fa-truck"></i> Kirim &amp; Potong Stok</button>
               <?php }); ?><?php endif; ?>
               <a href="<?= base_url('administrator/invoice'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
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
                           <tr><th style="width:35%;">Pelanggan:</th><td><strong style="font-size:14px; color:#333;"><?= _ent($invoice->nama_pelanggan); ?></strong></td></tr>
                           <tr><th>Alamat:</th><td><?= _ent($invoice->alamat_pelanggan ?? '-'); ?></td></tr>
                           <tr><th>No. Telp:</th><td><?= _ent($invoice->telp_pelanggan ?? '-'); ?></td></tr>
                           <tr><th>Referensi SO:</th><td><span class="label label-info"><?= _ent($invoice->nomor_so??'-'); ?></span></td></tr>
                           <tr><th>Gudang Asal:</th><td><?= _ent($invoice->nama_gudang??'-'); ?></td></tr>
                        </table>
                     </div>
                  </div>
               </div>
               
               <div class="col-md-6">
                  <div class="panel panel-default" style="border-radius: 4px; box-shadow: none;">
                     <div class="panel-heading" style="background:#f8f9fa; font-weight:bold;"><i class="fa fa-truck"></i> Detail Pengiriman &amp; Tanggal</div>
                     <div class="panel-body" style="padding: 10px 15px;">
                        <table class="table table-condensed table-borderless" style="margin-bottom:0;">
                           <tr><th style="width:40%;">Tgl Invoice:</th><td><?= date('d/m/Y', strtotime($invoice->tanggal_invoice)); ?></td></tr>
                           <tr><th>Jatuh Tempo:</th><td><?= $invoice->tanggal_jatuh_tempo ? date('d/m/Y', strtotime($invoice->tanggal_jatuh_tempo)) : '-'; ?></td></tr>
                           <tr><th>Status Kirim:</th><td><strong class="text-primary"><?= strtoupper(_ent($invoice->status_kirim ?? 'BELUM DIKIRIM')); ?></strong></td></tr>
                           <tr><th>No. Resi / AWB:</th><td><strong><?= _ent($invoice->nomor_resi ?? '-'); ?></strong></td></tr>
                           <tr><th>Ekspedisi / Kurir:</th><td><?= _ent($invoice->ekspedisi ?? '-'); ?></td></tr>
                        </table>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Item Table -->
            <h4 style="border-bottom:2px solid #3c8dbc; padding-bottom:5px; margin-bottom:15px; font-weight:bold; color:#333;"><i class="fa fa-cubes"></i> Item Barang Penjualan</h4>
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
                        $inv_disc_pct = (float)($invoice->diskon_persen_total ?? 0);
                        if ($inv_disc_pct == 0 && !empty($invoice->diskon_total) && !empty($invoice->subtotal) && $invoice->subtotal > 0) {
                           $inv_disc_pct = round(($invoice->diskon_total / $invoice->subtotal) * 100, 1);
                        }
                        if ($inv_disc_pct > 100) {
                           $inv_disc_pct = min(100, $inv_disc_pct / 100);
                        }
                        $it_disc = (float)(($item->diskon_persen > 0) ? $item->diskon_persen : $inv_disc_pct);
                        if ($it_disc > 100) {
                           if ($it_disc <= 10000) {
                              $it_disc = $it_disc / 100;
                           } else {
                              $it_disc = $inv_disc_pct;
                           }
                        }
                        $it_disc_fmt = ($it_disc == floor($it_disc)) ? number_format($it_disc, 0) : number_format($it_disc, 1);
                     ?>
                     <tr>
                        <td class="text-center"><?= $i+1; ?></td>
                        <td><strong><?= _ent($item->nama_produk); ?></strong><br><small class="text-muted">Kode: <?= _ent($item->kode_produk); ?></small></td>
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
               $subtotal_val = (float) ($invoice->subtotal ?? 0);
               $diskon_val   = (float) ($invoice->diskon_total ?? 0);
               if ($diskon_val > $subtotal_val && $subtotal_val > 0) {
                  $pct = min(100, $diskon_val / $subtotal_val);
                  $diskon_val = $subtotal_val * ($pct / 100);
               }
               $pajak_pct     = (float) ($invoice->pajak_persen ?? 0);
               $after_disc_v  = max(0, $subtotal_val - $diskon_val);
               $pajak_val     = $after_disc_v * ($pajak_pct / 100);
               $total_val     = $after_disc_v + $pajak_val;
               $diskon_pct_display = $subtotal_val > 0 ? round(($diskon_val / $subtotal_val) * 100, 1) : 0;
            ?>

            <div class="row">
               <div class="col-md-7">
                  <?php if (!empty($invoice->catatan)): ?>
                     <div class="callout callout-info" style="border-left-width: 4px; background:#f4f8fa; border-color:#5bc0de; color:#31708f; padding: 12px 15px;">
                        <h5 style="margin-top:0; font-weight:bold;"><i class="fa fa-commenting-o"></i> Catatan Transaksi:</h5>
                        <p style="margin-bottom:0; font-style:italic;"><?= nl2br(_ent($invoice->catatan)); ?></p>
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
                           <td style="padding-top:10px;"><strong>TOTAL INVOICE:</strong></td>
                           <td style="padding-top:10px;" class="text-right"><strong class="text-primary">Rp <?= number_format($total_val, 0, ',', '.'); ?></strong></td>
                        </tr>
                     </table>
                  </div>
               </div>
            </div>

         </div>
      </div>
   </div>
</div>

<!-- Modal Kirim -->
<div class="modal fade" id="modal_kirim">
   <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header" style="background:#3c8dbc; color:#fff;"><button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button><h4 class="modal-title"><i class="fa fa-truck"></i> Konfirmasi Pengiriman Pesanan</h4></div>
      <form method="post" action="<?= site_url('administrator/invoice/kirim/'.$invoice->id); ?>">
         <div class="modal-body">
            <div class="form-group">
               <label>Pilihan Ekspedisi / Kurir <span class="text-danger">*</span></label>
               <select class="form-control" name="ekspedisi" required>
                  <option value="">-- Pilih Ekspedisi / Kurir --</option>
                  <option value="JNE Express">JNE Express</option>
                  <option value="J&T Express">J&T Express</option>
                  <option value="SiCepat">SiCepat Express</option>
                  <option value="TIKI">TIKI</option>
                  <option value="POS Indonesia">POS Indonesia</option>
                  <option value="AnterAja">AnterAja</option>
                  <option value="Ninja Xpress">Ninja Xpress</option>
                  <option value="GoSend / GrabExpress">GoSend / GrabExpress</option>
                  <option value="Kurir Toko / Internal">Kurir Toko / Internal</option>
                  <option value="Lainnya">Lainnya</option>
               </select>
            </div>
            <div class="form-group">
               <label>No. Resi / AWB / Bukti Kirim <span class="text-danger">*</span></label>
               <input type="text" class="form-control" name="nomor_resi" placeholder="Ketik No. Resi (Contoh: JNE123456789)..." required>
            </div>
            <div class="callout callout-warning" style="margin-bottom:0;"><p><i class="fa fa-info-circle"></i> Setelah dikonfirmasi: Status kirim berubah ke <strong>SHIPPED</strong>, stok barang di gudang otomatis terpotong, dan piutang usaha diterbitkan.</p></div>
         </div>
         <div class="modal-footer">
            <button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> Konfirmasi & Kirim</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
         </div>
      </form>
   </div></div>
</div>
</section>
