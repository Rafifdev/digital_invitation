<?php /* sales_order_view.php */
$status_color=['draft'=>'default','confirmed'=>'info','processing'=>'primary','shipped'=>'warning','delivered'=>'success','cancelled'=>'danger'];

// Determine address
$display_alamat = !empty($so->alamat_pengiriman) 
    ? trim($so->alamat_pengiriman . ($so->kota_kabupaten ? ', ' . $so->kota_kabupaten : '') . ($so->provinsi ? ', ' . $so->provinsi : ''))
    : (!empty($so->alamat_pelanggan) ? $so->alamat_pelanggan : 'Jl. Sudirman No. 45, Jakarta');

// Determine phone
$display_phone = !empty($so->no_telepon) 
    ? $so->no_telepon 
    : (!empty($so->telp_pelanggan) ? $so->telp_pelanggan : '081234567890');

// Determine receiver & customer name
$display_pelanggan = !empty($so->nama_penerima) ? $so->nama_penerima : ($so->nama_pelanggan ?? 'Pelanggan General');
$display_buyer = !empty($so->username_pembeli) ? ' (@' . $so->username_pembeli . ')' : '';

// Determine estimated send date / deadline
$display_est_kirim = !empty($so->pesanan_harus_dikirim_sebelum) 
    ? date('d/m/Y H:i', strtotime($so->pesanan_harus_dikirim_sebelum)) 
    : (!empty($so->tanggal_estimasi_kirim) ? date('d/m/Y', strtotime($so->tanggal_estimasi_kirim)) : date('d/m/Y', strtotime("+1 day", strtotime($so->tanggal_so))));

// Determine resi & courier
$display_resi = !empty($so->nomor_resi) ? $so->nomor_resi : '-';
$display_kurir = !empty($so->opsi_pengiriman) ? $so->opsi_pengiriman : (!empty($so->kurir_ekspedisi) ? $so->kurir_ekspedisi : 'Reguler Courier');
?>
<section class="content-header">
   <h1>Detail Sales Order <small><?= _ent($so->nomor_so); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/sales_order'); ?>"><i class="fa fa-file-text"></i> Sales Order</a></li>
      <li class="active">Detail</li>
   </ol>
</section>

<section class="content">
<div class="row">
   <div class="col-md-12">
      <div class="box box-primary" style="border-radius:6px; border-top: 3px solid #3c8dbc;">
         <div class="box-header with-border" style="padding: 15px 20px;">
            <h3 class="box-title" style="font-weight:600;"><i class="fa fa-shopping-cart text-primary"></i> Sales Order <?= _ent($so->nomor_so); ?> &nbsp;
               <?php if(!empty($so->no_pesanan)): ?>
                  <span class="label label-primary" style="font-size:11px;"><?= _ent($so->no_pesanan); ?></span>
               <?php endif; ?>
               <span class="label label-<?= $status_color[$so->status]??'default'; ?>" style="font-size:11px; margin-left:4px;"><?= strtoupper($so->status); ?></span>
               <?php if(!empty($so->source) && $so->source != 'manual'): ?>
                  <span class="label label-success" style="font-size:11px; margin-left:4px; text-transform:uppercase;"><i class="fa fa-shopping-bag"></i> <?= _ent($so->source); ?></span>
               <?php endif; ?>
            </h3>
            <div class="box-tools pull-right">
               <a href="<?= site_url('administrator/sales_order/cetak/' . $so->id); ?>" target="_blank" class="btn btn-info btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-print"></i> Cetak PDF</a> 
               <?php if($so->status=='draft'): ?>
                  <?php is_allowed('sales_order_update', function() use ($so){ ?>
                     <a href="<?= site_url('administrator/sales_order/konfirmasi/'.$so->id); ?>" class="btn btn-primary btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-check"></i> Konfirmasi</a>
                  <?php }); ?>
               <?php endif; ?>
               <?php if(in_array($so->status,['confirmed','processing'])): ?>
                  <?php is_allowed('invoice_add', function() use ($so){ ?>
                     <a href="<?= site_url('administrator/invoice/from_so/'.$so->id); ?>" class="btn btn-success btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-file-text-o"></i> Buat Invoice</a>
                  <?php }); ?>
               <?php endif; ?>
               <a href="javascript:history.back()" class="btn btn-default btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
         </div>

         <div class="box-body" style="padding: 20px;">
            <!-- Header Grid: 2 Columns -->
            <div class="row" style="margin-bottom: 20px;">
               <div class="col-md-6">
                  <div class="panel panel-default" style="border-radius: 6px; box-shadow: none; border:1px solid #e9ecef;">
                     <div class="panel-heading" style="background:#f8fafc; font-weight:700; color:#334155;"><i class="fa fa-user text-primary"></i> Informasi Pelanggan &amp; Order</div>
                     <div class="panel-body" style="padding: 15px;">
                        <table class="table table-condensed table-borderless" style="margin-bottom:0; font-size:13px;">
                           <tr>
                              <th style="width:35%; color:#64748b;">Pelanggan / Penerima:</th>
                              <td>
                                 <strong style="font-size:14px; color:#1e293b;"><?= _ent($display_pelanggan); ?></strong>
                                 <small class="text-muted"><?= _ent($display_buyer); ?></small>
                                 <span class="label label-info" style="font-size:9px; margin-left:4px;"><?= strtoupper(_ent($so->tipe_pelanggan ?? 'ONLINE')); ?></span>
                              </td>
                           </tr>
                           <tr><th style="color:#64748b;">Alamat Pengiriman:</th><td><strong style="color:#334155;"><?= _ent($display_alamat); ?></strong></td></tr>
                           <tr><th style="color:#64748b;">No. Telepon:</th><td><strong style="color:#0284c7;"><?= _ent($display_phone); ?></strong></td></tr>
                           <tr><th style="color:#64748b;">Gudang Sumber:</th><td><i class="fa fa-bank text-muted"></i> <?= _ent($so->nama_gudang ?? 'Gudang Utama Jakarta'); ?></td></tr>
                           <tr><th style="color:#64748b;">Dibuat Oleh:</th><td><i class="fa fa-user-circle text-muted"></i> <?= _ent($so->created_by ?: 'admin'); ?></td></tr>
                        </table>
                     </div>
                  </div>
               </div>
               
               <div class="col-md-6">
                  <div class="panel panel-default" style="border-radius: 6px; box-shadow: none; border:1px solid #e9ecef;">
                     <div class="panel-heading" style="background:#f8fafc; font-weight:700; color:#334155;"><i class="fa fa-calendar text-primary"></i> Tanggal &amp; Pengiriman Pesanan</div>
                     <div class="panel-body" style="padding: 15px;">
                        <table class="table table-condensed table-borderless" style="margin-bottom:0; font-size:13px;">
                           <tr><th style="width:40%; color:#64748b;">Tanggal SO:</th><td><i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime($so->tanggal_so)); ?></td></tr>
                           <tr><th style="color:#64748b;">Est. Tanggal / Deadline Kirim:</th><td><strong class="text-warning"><i class="fa fa-clock-o"></i> <?= $display_est_kirim; ?></strong></td></tr>
                           <tr><th style="color:#64748b;">Opsi / Ekspedisi Kirim:</th><td><span class="label label-default" style="font-size:11px; padding:3px 7px;"><?= _ent($display_kurir); ?></span></td></tr>
                           <tr><th style="color:#64748b;">No. Resi Pengiriman:</th><td><span class="label label-info" style="font-size:11px; font-family:monospace;"><?= _ent($display_resi); ?></span></td></tr>
                           <tr><th style="color:#64748b;">Status SO:</th><td><strong class="text-primary"><?= strtoupper($so->status); ?></strong></td></tr>
                        </table>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Item Table -->
            <h4 style="border-bottom:2px solid #3c8dbc; padding-bottom:8px; margin-bottom:15px; font-weight:700; color:#1e293b;"><i class="fa fa-cubes text-primary"></i> Item Pesanan Barang</h4>
            <div class="table-responsive" style="margin-bottom: 25px;">
               <table class="table table-bordered table-striped" style="font-size:12px; vertical-align:middle;">
                  <thead>
                     <tr style="background:#f1f5f9; color:#475569;">
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
                           <strong style="color:#0f172a; font-size:13px;"><?= _ent($item->nama_produk); ?></strong><br>
                           <small class="text-muted">Kode / SKU: <?= _ent($item->kode_produk); ?></small>
                           <?php if(!empty($item->kode_rak)): ?>
                              <br><span class="label bg-purple" style="font-size:10px;"><i class="fa fa-map-marker"></i> Rak: <?= _ent($item->kode_rak); ?> - Baris <?= _ent($item->baris); ?></span>
                           <?php endif; ?>
                        </td>
                        <td class="text-center"><strong><?= number_format((float)$item->qty, 0, ',', '.'); ?></strong> <?= _ent($item->singkatan??$item->satuan??'unit'); ?></td>
                        <td class="text-right">Rp <?= number_format($item->harga_satuan, 0, ',', '.'); ?></td>
                        <td class="text-right"><strong><?= $it_disc_fmt; ?>%</strong></td>
                        <td class="text-right"><strong class="text-success" style="font-size:13px;">Rp <?= number_format($item->subtotal, 0, ',', '.'); ?></strong></td>
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
                     <div class="callout callout-info" style="border-left-width: 4px; background:#f0f9ff; border-color:#0284c7; color:#0369a1; padding: 12px 15px; border-radius:4px;">
                        <h5 style="margin-top:0; font-weight:bold;"><i class="fa fa-commenting-o"></i> Catatan Pesanan:</h5>
                        <p style="margin-bottom:0; font-style:italic;"><?= nl2br(_ent($so->catatan)); ?></p>
                     </div>
                  <?php endif; ?>
               </div>
               
               <div class="col-md-5">
                  <div class="well well-sm" style="background: #fff; border: 1px solid #e2e8f0; padding: 15px; border-radius: 6px;">
                     <table class="table table-condensed" style="margin-bottom: 0; font-size:13px;">
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
                        <tr style="font-size: 16px; border-top: 2px solid #e2e8f0;">
                           <td style="padding-top:10px;"><strong>TOTAL SALES ORDER:</strong></td>
                           <td style="padding-top:10px;" class="text-right"><strong class="text-success" style="font-size:18px;">Rp <?= number_format($total_val, 0, ',', '.'); ?></strong></td>
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
