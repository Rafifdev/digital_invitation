<?php /* penjualan_marketplace_view.php */
if (!function_exists('mask_nama_penerima')) {
    function mask_nama_penerima($name) {
        if (empty($name) || $name == '-') return '-';
        $words = explode(' ', trim($name));
        $masked_words = [];
        foreach ($words as $w) {
            $len = strlen($w);
            if ($len <= 2) {
                $masked_words[] = $w;
            } else {
                $first = substr($w, 0, 1);
                $last  = substr($w, -1);
                $masked_words[] = $first . '***' . $last;
            }
        }
        return implode(' ', $masked_words);
    }
}

if (!function_exists('mask_notelp')) {
    function mask_notelp($phone) {
        $clean = preg_replace('/[^0-9]/', '', trim($phone));
        if (empty($clean) || strlen($clean) < 6) return '08**********';
        $start = substr($clean, 0, 4);
        $end   = substr($clean, -2);
        return $start . '****' . $end;
    }
}

$status_color = [
    'draft'      => 'default',
    'packing'    => 'warning',
    'shipped'    => 'info',
    'delivered'  => 'success',
    'cancelled'  => 'danger'
];

$source_name = strtolower($so->source ?? 'shopee');
$badge_bg = ($source_name == 'tokopedia') ? '#03ac0e' : (($source_name == 'shopee') ? '#ee4d2d' : '#3c8dbc');
$source_title = strtoupper($so->source ?? 'SHOPEE');

$st_display = !empty($so->status_pesanan) ? strtoupper($so->status_pesanan) : strtoupper($so->status);
?>

<section class="content-header">
   <h1>Detail Pesanan Online Shop <small>(<?= _ent($source_title); ?>)</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/penjualan_marketplace'); ?>"><i class="fa fa-shopping-bag"></i> Penjualan Online Shop</a></li>
      <li class="active">Detail Pesanan</li>
   </ol>
</section>

<section class="content">
<div class="row">
   <div class="col-md-12">
      <div class="box box-solid" style="border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-top: 4px solid <?= $badge_bg; ?>;">
         
         <div class="box-header with-border" style="padding: 16px 20px; background:#fff;">
            <h3 class="box-title" style="font-weight:700; font-size:18px; color:#1e293b;">
               <span class="label" style="background-color:<?= $badge_bg; ?>; font-size:12px; padding:5px 10px; margin-right:8px;"><i class="fa fa-shopping-cart"></i> <?= $source_title; ?></span>
               No. Pesanan: <span style="font-family:monospace; color:#0f172a;"><?= _ent($so->no_pesanan ?: $so->nomor_so); ?></span>
               &nbsp;
               <span class="label label-<?= $status_color[$so->status]??'primary'; ?>" style="font-size:11px; padding:5px 9px;"><?= _ent($st_display); ?></span>
            </h3>
            <div class="box-tools pull-right">
               <a href="<?= site_url('administrator/penjualan_marketplace/cetak_resi/' . $so->id); ?>" target="_blank" class="btn btn-info btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-print"></i> Cetak Label Resi Thermal AWB</a> 
               <a href="<?= base_url('administrator/penjualan_marketplace'); ?>" class="btn btn-default btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-arrow-left"></i> Kembali ke Register All</a>
            </div>
         </div>

         <div class="box-body" style="padding: 24px; background:#fafafa;">
            
            <!-- Header Grid: 2 Columns tailored for E-Commerce -->
            <div class="row" style="margin-bottom: 20px;">
               
               <!-- Column 1: Marketplace Buyer & Delivery Info -->
               <div class="col-md-6">
                  <div class="panel panel-default" style="border-radius: 6px; border:1px solid #e2e8f0; box-shadow:none;">
                     <div class="panel-heading" style="background:#f8fafc; font-weight:700; color:#334155; border-bottom:1px solid #e2e8f0;">
                        <i class="fa fa-user-circle text-primary"></i> Informasi Pembeli &amp; Penerima Paket
                     </div>
                     <div class="panel-body" style="padding: 16px;">
                        <table class="table table-condensed table-borderless" style="margin-bottom:0; font-size:13px;">
                           <tr>
                              <th style="width:38%; color:#64748b;">Username Pembeli:</th>
                              <td><strong style="color:#0284c7; font-size:14px;"><i class="fa fa-at"></i> <?= _ent($so->username_pembeli ?: 'buyer_marketplace'); ?></strong></td>
                           </tr>
                           <tr>
                              <th style="color:#64748b;">Nama Penerima:</th>
                              <td>
                                 <strong id="disp_nama_penerima" style="color:#1e293b; font-size:14px;"><?= _ent(mask_nama_penerima($so->nama_penerima)); ?></strong>
                                 <button type="button" class="btn btn-default btn-xs" style="border-radius:12px; font-size:10px; padding:2px 8px; margin-left:6px; font-weight:700;" onclick="var el=$('#disp_nama_penerima'); if(el.text() === '<?= _ent(addslashes(mask_nama_penerima($so->nama_penerima))); ?>'){ el.text('<?= _ent(addslashes($so->nama_penerima)); ?>'); $(this).text('🔒 Sensor'); } else { el.text('<?= _ent(addslashes(mask_nama_penerima($so->nama_penerima))); ?>'); $(this).text('👁️ Buka Sensor'); }">👁️ Buka Sensor</button>
                              </td>
                           </tr>
                           <tr>
                              <th style="color:#64748b;">No. Telepon / WA:</th>
                              <td>
                                 <strong id="disp_no_telepon" style="color:#16a34a;"><i class="fa fa-phone"></i> <?= _ent(mask_notelp($so->no_telepon)); ?></strong>
                                 <button type="button" class="btn btn-default btn-xs" style="border-radius:12px; font-size:10px; padding:2px 8px; margin-left:6px; font-weight:700;" onclick="var el=$('#disp_no_telepon'); if(el.text().indexOf('****') !== -1){ el.html('<i class=\'fa fa-phone\'></i> <?= _ent(addslashes($so->no_telepon)); ?>'); $(this).text('🔒 Sensor'); } else { el.html('<i class=\'fa fa-phone\'></i> <?= _ent(addslashes(mask_notelp($so->no_telepon))); ?>'); $(this).text('👁️ Buka Sensor'); }">👁️ Buka Sensor</button>
                              </td>
                           </tr>
                           <tr>
                              <th style="color:#64748b;">Alamat Pengiriman:</th>
                              <td>
                                 <div style="background:#f1f5f9; padding:8px 12px; border-radius:6px; color:#334155; font-size:12px; line-height:1.4;">
                                    <i class="fa fa-map-marker text-danger"></i> <?= _ent($so->alamat_pengiriman ?: '-'); ?>
                                    <?php if(!empty($so->kota_kabupaten)): ?><br><strong>Kota/Kab:</strong> <?= _ent($so->kota_kabupaten); ?><?php endif; ?>
                                    <?php if(!empty($so->provinsi)): ?><br><strong>Provinsi:</strong> <?= _ent($so->provinsi); ?><?php endif; ?>
                                 </div>
                              </td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Column 2: Logistics & Payment Timeline -->
               <div class="col-md-6">
                  <div class="panel panel-default" style="border-radius: 6px; border:1px solid #e2e8f0; box-shadow:none;">
                     <div class="panel-heading" style="background:#f8fafc; font-weight:700; color:#334155; border-bottom:1px solid #e2e8f0;">
                        <i class="fa fa-truck text-success"></i> Logistik Pengiriman &amp; Pembayaran
                     </div>
                     <div class="panel-body" style="padding: 16px;">
                        <table class="table table-condensed table-borderless" style="margin-bottom:0; font-size:13px;">
                           <tr>
                              <th style="width:40%; color:#64748b;">Kurir / Ekspedisi:</th>
                              <td><span class="label label-primary" style="font-size:11px; padding:4px 8px;"><i class="fa fa-paper-plane"></i> <?= _ent($so->opsi_pengiriman ?: ($so->kurir_ekspedisi ?: 'Reguler')); ?></span></td>
                           </tr>
                           <tr>
                              <th style="color:#64748b;">No. Resi (AWB):</th>
                              <td><strong style="font-family:monospace; font-size:14px; color:#0f172a; background:#e2e8f0; padding:2px 8px; border-radius:4px;"><?= _ent($so->nomor_resi ?: 'Belum Terbit'); ?></strong></td>
                           </tr>
                           <tr>
                              <th style="color:#64748b;">Waktu Pembayaran:</th>
                              <td><i class="fa fa-clock-o text-muted"></i> <?= !empty($so->waktu_pembayaran) ? date('d/m/Y H:i:s', strtotime($so->waktu_pembayaran)) : date('d/m/Y H:i:s', strtotime($so->tanggal_so)); ?></td>
                           </tr>
                           <tr>
                              <th style="color:#64748b;">Deadline Kirim:</th>
                              <td><strong class="text-warning"><i class="fa fa-hourglass-half"></i> <?= !empty($so->pesanan_harus_dikirim_sebelum) ? date('d/m/Y H:i', strtotime($so->pesanan_harus_dikirim_sebelum)) : date('d/m/Y', strtotime("+1 day", strtotime($so->tanggal_so))); ?></strong></td>
                           </tr>
                           <tr>
                              <th style="color:#64748b;">Gudang Pengirim:</th>
                              <td><i class="fa fa-bank text-muted"></i> <?= _ent($so->nama_gudang ?: 'Gudang Utama Jakarta'); ?></td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

            </div>

            <!-- E-Commerce Product Items Table -->
            <h4 style="border-bottom:2px solid <?= $badge_bg; ?>; padding-bottom:8px; margin-bottom:15px; font-weight:700; color:#0f172a;">
               <i class="fa fa-cube" style="color:<?= $badge_bg; ?>;"></i> Rincian Produk Pesanan Online Shop
            </h4>
            
            <div class="table-responsive" style="margin-bottom: 25px;">
               <table class="table table-bordered table-striped" style="background:#fff; font-size:13px;">
                  <thead>
                     <tr style="background:#f1f5f9; color:#475569;">
                        <th style="width:5%; text-align:center;">#</th>
                        <th style="width:45%;">Nama Produk &amp; Variasi Marketplace</th>
                        <th style="width:12%; text-align:center;">Jumlah (Qty)</th>
                        <th style="width:18%; text-align:right;">Harga Satuan</th>
                        <th style="width:20%; text-align:right;">Total Produk</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach($items as $i=>$item): ?>
                     <tr>
                        <td class="text-center" style="vertical-align:middle;"><?= $i+1; ?></td>
                        <td>
                           <strong style="color:#0f172a; font-size:14px;"><?= _ent($item->catatan ?: ($so->nama_produk_marketplace ?: $item->nama_produk)); ?></strong>
                           <?php if(!empty($so->nama_variasi)): ?>
                              <br><small class="text-muted"><i class="fa fa-tags"></i> Variasi: <strong><?= _ent($so->nama_variasi); ?></strong></small>
                           <?php endif; ?>
                           <?php if(!empty($so->sku_induk)): ?>
                              <br><small class="text-muted"><i class="fa fa-barcode"></i> SKU: <code><?= _ent($so->sku_induk); ?></code></small>
                           <?php endif; ?>
                        </td>
                        <td class="text-center" style="vertical-align:middle;">
                           <strong style="font-size:15px;"><?= number_format((float)$item->qty, 0, ',', '.'); ?></strong> <?= _ent($item->singkatan??$item->satuan??'pcs'); ?>
                        </td>
                        <td class="text-right" style="vertical-align:middle;">Rp <?= number_format($item->harga_satuan, 0, ',', '.'); ?></td>
                        <td class="text-right" style="vertical-align:middle;">
                           <strong class="text-success" style="font-size:14px;">Rp <?= number_format($item->subtotal, 0, ',', '.'); ?></strong>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                  </tbody>
               </table>
            </div>

            <!-- Footer Notes & Summary -->
            <div class="row">
               <div class="col-md-7">
                  <?php if (!empty($so->catatan)): ?>
                     <div class="callout callout-info" style="border-left-width: 4px; background:#f0f9ff; border-color:#0284c7; color:#0369a1; padding: 14px; border-radius:6px;">
                        <h5 style="margin-top:0; font-weight:bold;"><i class="fa fa-commenting-o"></i> Catatan Pembeli Marketplace:</h5>
                        <p style="margin-bottom:0; font-style:italic;"><?= nl2br(_ent($so->catatan)); ?></p>
                     </div>
                  <?php endif; ?>
               </div>
               
               <div class="col-md-5">
                  <div class="well well-sm" style="background: #fff; border: 1px solid #e2e8f0; padding: 16px; border-radius: 8px;">
                     <table class="table table-condensed" style="margin-bottom: 0; font-size:14px;">
                        <tr>
                           <td style="border:none;">Subtotal Produk:</td>
                           <td style="border:none;" class="text-right">Rp <?= number_format($so->subtotal, 0, ',', '.'); ?></td>
                        </tr>
                        <tr style="font-size: 16px; border-top: 2px solid #e2e8f0;">
                           <td style="padding-top:10px;"><strong>TOTAL PEMBAYARAN:</strong></td>
                           <td style="padding-top:10px;" class="text-right">
                              <strong style="color:<?= $badge_bg; ?>; font-size:18px;">Rp <?= number_format($so->total, 0, ',', '.'); ?></strong>
                           </td>
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
