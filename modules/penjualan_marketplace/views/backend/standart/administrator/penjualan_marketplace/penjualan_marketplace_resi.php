<?php
/* penjualan_marketplace_resi.php 
 * Thermal AWB Shipping Label Template (100x150mm)
 * Compatible with SPX Express, J&T Express, SiCepat, Anteraja, Tokopedia, etc.
 */

$courier_raw = strtolower($so->opsi_pengiriman ?: ($so->kurir_ekspedisi ?: ($so->source ?? 'shopee')));
$source_raw = strtolower($so->source ?? 'shopee');

// Determine branding color and title
if (strpos($courier_raw, 'spx') !== false || strpos($courier_raw, 'shopee') !== false || $source_raw == 'shopee') {
    $courier_name = "SPX Express";
    $brand_color  = "#ee4d2d";
    $service_type = "STANDARD";
    $hub_code     = "SUB-SBY-01";
} elseif (strpos($courier_raw, 'j&t') !== false || strpos($courier_raw, 'jnt') !== false) {
    $courier_name = "J&T Express";
    $brand_color  = "#e30613";
    $service_type = "EZ (REGULER)";
    $hub_code     = "SBY-HUB-02";
} elseif (strpos($courier_raw, 'sicepat') !== false) {
    $courier_name = "SiCepat REG";
    $brand_color  = "#d32f2f";
    $service_type = "HALU / REG";
    $hub_code     = "SBY-GUDANG";
} elseif (strpos($courier_raw, 'anteraja') !== false) {
    $courier_name = "Anteraja";
    $brand_color  = "#c2185b";
    $service_type = "REGULER";
    $hub_code     = "SBY-ANT-01";
} else {
    $courier_name = "Tokopedia Shipping";
    $brand_color  = "#03ac0e";
    $service_type = "REGULER";
    $hub_code     = "JKT-CGK-01";
}

$resi_num  = !empty($so->nomor_resi) ? $so->nomor_resi : ('SPX' . rand(1000000000, 9999999999));
$order_num = !empty($so->no_pesanan) ? $so->no_pesanan : $so->nomor_so;
?>
<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <title>Label Resi Pengiriman - <?= _ent($resi_num); ?></title>
   <style>
      @page {
         size: 100mm 150mm;
         margin: 0;
      }
      body {
         font-family: 'Helvetica Neue', Arial, sans-serif;
         margin: 0;
         padding: 0;
         background-color: #f4f6f9;
         color: #000;
         font-size: 11px;
         line-height: 1.2;
      }
      .page-container {
         width: 100mm;
         min-height: 150mm;
         margin: 10px auto;
         background: #fff;
         border: 2px solid #000;
         box-sizing: border-box;
         position: relative;
      }
      .no-print-bar {
         width: 100mm;
         margin: 15px auto 5px auto;
         text-align: right;
      }
      .btn-print {
         background: #00a65a;
         color: #fff;
         border: none;
         padding: 8px 16px;
         font-size: 13px;
         font-weight: bold;
         border-radius: 4px;
         cursor: pointer;
      }
      .btn-back {
         background: #666;
         color: #fff;
         border: none;
         padding: 8px 16px;
         font-size: 13px;
         text-decoration: none;
         border-radius: 4px;
         margin-right: 5px;
      }
      
      /* Thermal AWB Resi Layout */
      .resi-header {
         background-color: <?= $brand_color; ?>;
         color: #fff;
         padding: 8px 10px;
         display: flex;
         justify-content: space-between;
         align-items: center;
         border-bottom: 2px solid #000;
      }
      .resi-header .brand {
         font-size: 18px;
         font-weight: 900;
         letter-spacing: 0.5px;
         text-transform: uppercase;
      }
      .resi-header .service {
         background: #fff;
         color: #000;
         padding: 3px 8px;
         font-weight: 800;
         font-size: 11px;
         border-radius: 3px;
         text-transform: uppercase;
      }

      .barcode-section {
         text-align: center;
         padding: 10px;
         border-bottom: 2px solid #000;
         background: #fff;
      }
      .barcode-lines {
         font-family: 'Libre Barcode 128', 'CCode39', monospace;
         font-size: 42px;
         line-height: 1;
         letter-spacing: 2px;
         display: block;
         margin: 2px 0;
      }
      .resi-number {
         font-size: 16px;
         font-weight: 900;
         font-family: monospace;
         letter-spacing: 1px;
      }
      .order-number {
         font-size: 11px;
         color: #333;
         margin-top: 2px;
      }

      .info-grid {
         display: table;
         width: 100%;
         border-bottom: 2px solid #000;
         table-layout: fixed;
      }
      .info-box {
         display: table-cell;
         padding: 8px;
         vertical-align: top;
         word-wrap: break-word;
      }
      .info-box.left {
         width: 65%;
         border-right: 2px solid #000;
      }
      .info-box.right {
         width: 35%;
         text-align: center;
         background: #f9f9f9;
      }

      .address-title {
         font-weight: 800;
         text-transform: uppercase;
         font-size: 10px;
         color: #555;
         margin-bottom: 3px;
      }
      .receiver-name {
         font-size: 13px;
         font-weight: 800;
         color: #000;
      }
      .phone-num {
         font-weight: 800;
         font-size: 12px;
      }
      .address-text {
         font-size: 10.5px;
         line-height: 1.3;
         margin-top: 3px;
      }

      .hub-box {
         font-size: 20px;
         font-weight: 900;
         border: 2px solid #000;
         padding: 8px 4px;
         margin-top: 5px;
         background: #fff;
         text-align: center;
      }
      .cod-badge {
         display: inline-block;
         font-size: 14px;
         font-weight: 900;
         background: #000;
         color: #fff;
         padding: 4px 8px;
         margin-top: 5px;
      }

      .items-section {
         padding: 8px;
         border-bottom: 2px solid #000;
      }
      .items-title {
         font-weight: 800;
         font-size: 10px;
         text-transform: uppercase;
         margin-bottom: 4px;
      }
      .items-table {
         width: 100%;
         border-collapse: collapse;
         font-size: 10px;
      }
      .items-table th, .items-table td {
         border: 1px solid #999;
         padding: 4px;
      }
      .items-table th {
         background: #eee;
         text-align: left;
      }

      .footer-section {
         padding: 6px 8px;
         font-size: 9.5px;
         color: #444;
         display: flex;
         justify-content: space-between;
         align-items: center;
      }

      /* Barcode generator CSS simulation */
      .barcode-sim {
         height: 38px;
         background: repeating-linear-gradient(
            90deg,
            #000,
            #000 2px,
            #fff 2px,
            #fff 4px,
            #000 4px,
            #000 7px,
            #fff 7px,
            #fff 8px
         );
         width: 80%;
         margin: 4px auto;
      }

      @media print {
         .no-print-bar { display: none !important; }
         body { background: #fff; }
         .page-container {
            margin: 0;
            border: 2px solid #000;
            width: 100mm;
            height: 150mm;
            page-break-after: always;
         }
      }
   </style>
</head>
<body>

   <div class="no-print-bar">
      <a href="javascript:history.back()" class="btn-back">&laquo; Kembali</a>
      <button onclick="window.print()" class="btn-print"><i class="fa fa-print"></i> Cetak Label Resi Thermal</button>
   </div>

   <div class="page-container">
      
      <!-- Resi Header Bar -->
      <div class="resi-header">
         <div class="brand"><?= _ent($courier_name); ?></div>
         <div class="service"><?= _ent($service_type); ?></div>
      </div>

      <!-- Barcode & Resi AWB Section -->
      <div class="barcode-section">
         <div class="barcode-sim"></div>
         <div class="resi-number"><?= _ent($resi_num); ?></div>
         <div class="order-number">No. Pesanan: <strong><?= _ent($order_num); ?></strong></div>
      </div>

      <!-- Info Grid: Penerima & Pengirim & Sort Code -->
      <div class="info-grid">
         <div class="info-box left">
            <div class="address-title">Penerima (To):</div>
            <div class="receiver-name"><?= _ent($so->nama_penerima ?: 'Penerima Paket'); ?></div>
            <div class="phone-num"><?= _ent($so->no_telepon ?: '-'); ?></div>
            <div class="address-text">
               <?= _ent($so->alamat_pengiriman ?: 'Jl. Merdeka Utama No. 88'); ?>
               <?php if(!empty($so->kota_kabupaten)): ?><br><strong><?= _ent($so->kota_kabupaten); ?></strong><?php endif; ?>
               <?php if(!empty($so->provinsi)): ?>, <?= _ent($so->provinsi); ?><?php endif; ?>
            </div>

            <hr style="border:0; border-top:1px dashed #ccc; margin:8px 0 4px 0;">

            <div class="address-title">Pengirim (From):</div>
            <div style="font-weight:700; font-size:11px;"><?= _ent($so->nama_gudang ?: 'Store Official Metafora'); ?> (@<?= _ent($so->username_pembeli ?: 'seller'); ?>)</div>
            <div style="font-size:10px; color:#444;">Jakarta Selatan, DKI Jakarta</div>
         </div>

         <div class="info-box right">
            <div class="address-title">Sort Code / Hub</div>
            <div class="hub-box"><?= _ent($hub_code); ?></div>
            <div class="cod-badge">NON-COD</div>
            <div style="font-size:9px; margin-top:8px; color:#555;">Berat: <strong><?= !empty($so->total_qty) ? ($so->total_qty * 0.5) : 0.5; ?> Kg</strong></div>
         </div>
      </div>

      <!-- Items Section -->
      <div class="items-section">
         <div class="items-title">Rincian Produk (Content Summary):</div>
         <table class="items-table">
            <thead>
               <tr>
                  <th style="width:70%;">Nama Produk &amp; Variasi</th>
                  <th style="width:30%; text-align:center;">Qty</th>
               </tr>
            </thead>
            <tbody>
               <?php if(!empty($items)): ?>
                  <?php foreach($items as $item): ?>
                  <tr>
                     <td>
                        <strong><?= _ent($item->catatan ?: ($so->nama_produk_marketplace ?: $item->nama_produk)); ?></strong>
                        <?php if(!empty($so->nama_variasi)): ?><br><span style="color:#555;">Var: <?= _ent($so->nama_variasi); ?></span><?php endif; ?>
                     </td>
                     <td style="text-align:center; font-weight:bold; font-size:12px;"><?= number_format((float)$item->qty, 0); ?> pcs</td>
                  </tr>
                  <?php endforeach; ?>
               <?php else: ?>
                  <tr>
                     <td><strong><?= _ent($so->nama_produk_marketplace ?: 'Produk Online Shop'); ?></strong></td>
                     <td style="text-align:center; font-weight:bold;">1 pcs</td>
                  </tr>
               <?php endif; ?>
            </tbody>
         </table>

         <?php if(!empty($so->catatan)): ?>
            <div style="margin-top:5px; font-size:9.5px; font-style:italic; background:#f0f0f0; padding:4px; border-radius:3px;">
               <strong>Pesan Pembeli:</strong> <?= _ent($so->catatan); ?>
            </div>
         <?php endif; ?>
      </div>

      <!-- Footer Info -->
      <div class="footer-section">
         <div>Waktu Bayar: <?= !empty($so->waktu_pembayaran) ? date('d/m/Y H:i', strtotime($so->waktu_pembayaran)) : date('d/m/Y H:i'); ?></div>
         <div>Dicetak: <?= date('d/m/Y H:i'); ?></div>
      </div>

   </div>

</body>
</html>
