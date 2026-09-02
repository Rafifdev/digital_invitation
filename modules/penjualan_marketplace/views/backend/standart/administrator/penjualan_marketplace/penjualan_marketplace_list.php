<?php
/**
 * View: Penjualan Marketplace (Register All & Fulfillment Multi-Channel)
 * File: penjualan_marketplace_list.php
 * Ultra-Premium Executive UI/UX Design System
 */

/* Privacy Masking Helpers (Neat & Compact) */
if (!function_exists('mask_nama_penerima')) {
    function mask_nama_penerima($name) {
        if (empty($name) || $name == '-') return '-';
        $words = explode(' ', trim($name));
        $masked_words = [];
        foreach ($words as $idx => $w) {
            $len = strlen($w);
            if ($len <= 2) {
                $masked_words[] = $w;
            } else {
                $first = substr($w, 0, 1);
                $last  = substr($w, -1);
                $masked_words[] = $first . '***' . $last;
            }
            if ($idx >= 1 && count($words) > 2) {
               break;
            }
        }
        return implode(' ', $masked_words);
    }
}

if (!function_exists('mask_notelp')) {
    function mask_notelp($phone) {
        $phone = preg_replace('/[^0-9]/', '', trim($phone));
        if (empty($phone) || strlen($phone) < 6) return '08****00';
        $prefix = substr($phone, 0, 4);
        $suffix = substr($phone, -2);
        return $prefix . '****' . $suffix;
    }
}

$status_color = [
    'draft'      => 'label-default',
    'submitted'  => 'label-default',
    'confirmed'  => 'label-info',
    'approved'   => 'label-info',
    'packing'    => 'label-warning',
    'processing' => 'label-warning',
    'shipped'    => 'label-primary',
    'delivered'  => 'label-success',
    'completed'  => 'label-success',
    'cancelled'  => 'label-danger'
];
$per_page = isset($per_page) ? $per_page : 25;
?>
<style>
/* Modern Premium UI/UX Design Tokens */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.content-wrapper, .right-side {
   font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
}

/* Header Container */
.pm-header-container {
   background: #ffffff;
   border-radius: 14px;
   padding: 20px 26px;
   margin-bottom: 22px;
   box-shadow: 0 10px 30px rgba(0,0,0,0.03);
   border: 1px solid #e2e8f0;
}
.pm-header-icon {
   width: 48px;
   height: 48px;
   background: linear-gradient(135deg, #ee4d2d 0%, #ff6b4a 100%);
   color: #ffffff;
   border-radius: 12px;
   display: inline-flex;
   align-items: center;
   justify-content: center;
   font-size: 22px;
   box-shadow: 0 6px 16px rgba(238, 77, 45, 0.3);
   float: left;
   margin-right: 16px;
}
.pm-header-title {
   font-size: 22px;
   font-weight: 800;
   color: #0f172a;
   margin: 0;
   letter-spacing: -0.4px;
   line-height: 1.2;
}
.pm-header-subtitle {
   font-size: 12.5px;
   color: #64748b;
   margin-top: 4px;
}

/* Button Bar */
.pm-btn-group {
   display: flex;
   gap: 8px;
   flex-wrap: wrap;
   justify-content: flex-end;
   align-items: center;
}
.pm-btn {
   border-radius: 8px;
   font-weight: 700;
   font-size: 11.5px;
   padding: 8px 14px;
   border: none;
   transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
   display: inline-flex;
   align-items: center;
   justify-content: center;
   gap: 6px;
   color: #ffffff !important;
   box-shadow: 0 2px 8px rgba(0,0,0,0.06);
   white-space: nowrap;
   text-decoration: none !important;
   cursor: pointer;
}
.pm-btn:hover {
   transform: translateY(-2px);
   box-shadow: 0 6px 16px rgba(0,0,0,0.12);
}
.pm-btn-packed { background: #10b981; }
.pm-btn-import-packed { background: #0d9488; }
.pm-btn-import-penjualan { background: #6366f1; }
.pm-btn-add { background: #ee4d2d; }
.pm-btn-xls { background: #059669; }
.pm-btn-pdf { background: #f43f5e; }

/* 4 KPI Metric Cards */
.pm-kpi-card {
   background: #ffffff;
   border-radius: 14px;
   padding: 18px 22px;
   box-shadow: 0 4px 20px rgba(0,0,0,0.02);
   border: 1px solid #e2e8f0;
   margin-bottom: 22px;
   transition: all 0.2s ease;
   position: relative;
   overflow: hidden;
}
.pm-kpi-card:hover {
   transform: translateY(-3px);
   box-shadow: 0 10px 30px rgba(0,0,0,0.06);
   border-color: #cbd5e1;
}
.pm-kpi-title {
   font-size: 11px;
   font-weight: 800;
   text-transform: uppercase;
   color: #64748b;
   letter-spacing: 0.6px;
}
.pm-kpi-num {
   font-size: 24px;
   font-weight: 900;
   color: #0f172a;
   margin: 6px 0 2px 0;
   line-height: 1.1;
}
.pm-kpi-sub {
   font-size: 11.5px;
   color: #94a3b8;
}
.pm-kpi-icon {
   width: 44px;
   height: 44px;
   border-radius: 12px;
   display: flex;
   align-items: center;
   justify-content: center;
   font-size: 20px;
   float: right;
   margin-top: -38px;
}

/* Filter Card */
.pm-filter-card {
   background: #ffffff;
   border: 1px solid #e2e8f0;
   border-radius: 14px;
   padding: 20px 24px;
   margin-bottom: 22px;
   box-shadow: 0 4px 15px rgba(0,0,0,0.02);
}

/* Channel & Courier Badges */
.channel-pill {
   display: inline-flex;
   align-items: center;
   gap: 4px;
   padding: 3px 9px;
   border-radius: 12px;
   font-size: 10.5px;
   font-weight: 700;
   margin-top: 4px;
}
.channel-shopee { background: #fff2ee; color: #ee4d2d; border: 1px solid #ffccbe; }
.channel-tokopedia { background: #f2faf3; color: #03ac0e; border: 1px solid #c8edd0; }
.channel-manual { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

.courier-pill {
   display: inline-block;
   padding: 4px 9px;
   border-radius: 6px;
   font-size: 11px;
   font-weight: 700;
}
.courier-spx { background: #ffedd5; color: #c2410c; }
.courier-sicepat { background: #fce7f3; color: #be185d; }
.courier-jnt { background: #fee2e2; color: #b91c1c; }

.status-packed-badge {
   background: #0284c7;
   color: #ffffff;
   font-size: 10.5px;
   font-weight: 800;
   padding: 4px 9px;
   border-radius: 6px;
   display: inline-block;
}

/* Action Icon Buttons */
.act-icon-btn {
   display: inline-flex;
   align-items: center;
   justify-content: center;
   width: 28px;
   height: 28px;
   background: #00a65a;
   color: #ffffff !important;
   border-radius: 6px;
   font-size: 12px;
   transition: all 0.15s ease;
   text-decoration: none !important;
}
.act-icon-btn:hover {
   background: #008d4c;
   transform: scale(1.08);
}

/* Table Responsive Adjustments */
@media (max-width: 991px) {
   .pm-header-container { padding: 18px; }
   .pm-btn-group { justify-content: flex-start; margin-top: 14px; width: 100%; }
   .pm-btn { flex: 1 1 calc(33.333% - 8px); }
}
@media (max-width: 640px) {
   .pm-btn { flex: 1 1 calc(50% - 8px); font-size: 11px; padding: 8px 10px; }
}
</style>

<section class="content-header" style="padding-top:16px; padding-bottom:0;">
   <div class="pm-header-container">
      <div class="row" style="display:flex; flex-wrap:wrap; align-items:center;">
         <div class="col-md-4 col-sm-12">
            <div class="pm-header-icon">
               <i class="fa fa-shopping-bag"></i>
            </div>
            <div>
               <h1 class="pm-header-title">Penjualan Marketplace</h1>
               <div class="pm-header-subtitle">
                  Register All &amp; Fulfillment Multi-Channel 
                  <span class="badge" style="background:#0284c7; font-size:11px; padding:3px 10px; border-radius:12px; margin-left:6px;"><?= $so_counts; ?> Order</span>
               </div>
            </div>
         </div>
         <div class="col-md-8 col-sm-12 text-right">
            <div class="pm-btn-group">
               <a href="<?= site_url('administrator/penjualan_marketplace/scan_resi'); ?>" class="pm-btn pm-btn-packed" style="background:#0284c7; border-color:#0284c7; font-weight:800;">
                  <i class="fa fa-search"></i> 🔍 Scan Resi &amp; Breakdown
               </a>
               <button type="button" class="pm-btn pm-btn-import-penjualan" data-toggle="modal" data-target="#modalImportMarketplace">
                  <i class="fa fa-file-excel-o"></i> Import Penjualan
               </button>
               <a href="<?= site_url('administrator/penjualan_marketplace/add'); ?>" class="pm-btn pm-btn-add">
                  <i class="fa fa-plus-circle"></i> + Add Penjualan Shopee
               </a>
               <a href="<?= site_url('administrator/penjualan_marketplace/export'); ?>" class="pm-btn pm-btn-xls">
                  <i class="fa fa-file-excel-o"></i> Export XLS
               </a>
               <a href="<?= site_url('administrator/penjualan_marketplace/export_pdf'); ?>" class="pm-btn pm-btn-pdf">
                  <i class="fa fa-file-pdf-o"></i> Export PDF
               </a>
            </div>
         </div>
      </div>
   </div>
</section>

<section class="content" style="padding-top:0;">

   <!-- Sleek High-Contrast Result Notification Banner Card -->
   <?php 
   $f_message = $this->session->flashdata('f_message');
   $f_type    = $this->session->flashdata('f_type');
   if (!empty($f_message)):
      $alert_bg = ($f_type == 'error') ? '#fef2f2' : (($f_type == 'warning') ? '#fffbeb' : '#f0fdf4');
      $alert_border = ($f_type == 'error') ? '#ef4444' : (($f_type == 'warning') ? '#f59e0b' : '#10b981');
      $alert_icon = ($f_type == 'error') ? 'fa-exclamation-triangle text-danger' : (($f_type == 'warning') ? 'fa-exclamation-circle text-warning' : 'fa-check-circle text-success');
      $alert_title = ($f_type == 'error') ? 'Peringatan / Gagal Import' : 'Laporan Ringkasan Import Penjualan';
   ?>
   <div class="alert alert-dismissible" style="background:<?= $alert_bg; ?>; border:1px solid <?= $alert_border; ?>; border-left:6px solid <?= $alert_border; ?>; border-radius:12px; color:#0f172a; padding:16px 22px; margin-bottom:20px; box-shadow:0 4px 20px rgba(0,0,0,0.04);">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="color:#0f172a; opacity:0.6; font-size:22px;">&times;</button>
      <div style="display:flex; align-items:flex-start; gap:14px;">
         <i class="fa <?= $alert_icon; ?>" style="font-size:24px; margin-top:2px;"></i>
         <div>
            <strong style="font-size:14.5px; font-weight:800; color:#0f172a; font-family:sans-serif;"><?= $alert_title; ?></strong>
            <div style="font-size:13px; color:#334155; margin-top:6px; line-height:1.7;">
               <?= $f_message; ?>
            </div>
         </div>
      </div>
   </div>
   <?php endif; ?>



   <!-- Filter Panel -->
   <div class="pm-filter-card">
      <form action="<?= base_url('administrator/penjualan_marketplace/index'); ?>" method="GET">
         <div class="row">
            <div class="col-md-3 col-sm-6">
               <label class="text-muted" style="font-size:11px; font-weight:700;">Saluran Penjualan:</label>
               <select class="form-control input-sm" name="source" style="border-radius:6px; font-weight:600;">
                  <option value="">-- Semua Saluran --</option>
                  <option value="shopee" <?= $this->input->get('source') == 'shopee' ? 'selected':''; ?>>Penjualan Shopee</option>
                  <option value="tokopedia" <?= $this->input->get('source') == 'tokopedia' ? 'selected':''; ?>>Penjualan Tokopedia</option>
                  <option value="manual" <?= $this->input->get('source') == 'manual' ? 'selected':''; ?>>Sales Order Regular / Manual</option>
               </select>
            </div>
            <div class="col-md-3 col-sm-6">
               <label class="text-muted" style="font-size:11px; font-weight:700;">Gudang Asal:</label>
               <select class="form-control input-sm" name="gudang_id" style="border-radius:6px;">
                  <option value="">-- Semua Gudang --</option>
                  <?php foreach($gudangs as $g): ?>
                  <option value="<?= $g->id; ?>" <?= $this->input->get('gudang_id') == $g->id ? 'selected':''; ?>><?= _ent($g->nama_gudang); ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
            <div class="col-md-4 col-sm-8">
               <label class="text-muted" style="font-size:11px; font-weight:700;">Cari No Pesanan / Resi / Buyer / Receiver:</label>
               <input type="text" class="form-control input-sm" name="q" placeholder="Ketik No. Pesanan, Resi, atau Username..." value="<?= _ent($this->input->get('q')); ?>" style="border-radius:6px;">
            </div>
            <div class="col-md-2 col-sm-4" style="padding-top:20px;">
               <button type="submit" class="btn btn-primary btn-sm btn-flat btn-block" style="border-radius:6px; font-weight:700; background:#0284c7; border-color:#0284c7;"><i class="fa fa-search"></i> Q Search</button>
            </div>
         </div>
         
         <div class="row" style="margin-top:12px;">
            <div class="col-md-3 col-sm-6">
               <label class="text-muted" style="font-size:11px; font-weight:700;">Pilih Status Pesanan:</label>
               <select class="form-control input-sm" name="status" style="border-radius:6px;">
                  <option value="">-- Semua Status --</option>
                  <option value="draft" <?= $this->input->get('status') == 'draft' ? 'selected':''; ?>>Draft / Pesanan Baru</option>
                  <option value="confirmed" <?= $this->input->get('status') == 'confirmed' ? 'selected':''; ?>>Terkonfirmasi (Stok Ready)</option>
                  <option value="packing" <?= $this->input->get('status') == 'packing' ? 'selected':''; ?>>Sedang Dikemas (Packing)</option>
                  <option value="shipped" <?= $this->input->get('status') == 'shipped' ? 'selected':''; ?>>Dalam Pengiriman (Shipped)</option>
                  <option value="delivered" <?= $this->input->get('status') == 'delivered' ? 'selected':''; ?>>Pesanan Selesai (Delivered)</option>
                  <option value="cancelled" <?= $this->input->get('status') == 'cancelled' ? 'selected':''; ?>>Dibatalkan</option>
               </select>
            </div>
            <div class="col-md-3 col-sm-6">
               <label class="text-muted" style="font-size:11px; font-weight:700;">Tampilkan Data (Limit):</label>
               <select class="form-control input-sm" name="per_page" onchange="this.form.submit()" style="border-radius:6px;">
                  <option value="25" <?= ($per_page == 25) ? 'selected':''; ?>>Limit: 25 Data</option>
                  <option value="50" <?= ($per_page == 50) ? 'selected':''; ?>>Limit: 50 Data</option>
                  <option value="100" <?= ($per_page == 100) ? 'selected':''; ?>>Limit: 100 Data</option>
                  <option value="500" <?= ($per_page == 500) ? 'selected':''; ?>>Limit: 500 Data</option>
               </select>
            </div>
            <div class="col-md-3 col-sm-6" style="padding-top:20px;">
               <a href="<?= site_url('administrator/penjualan_marketplace'); ?>" class="btn btn-default btn-sm btn-flat" style="border-radius:6px;"><i class="fa fa-undo"></i> Reset Filter</a>
            </div>
         </div>
      </form>
   </div>

   <!-- Main Executive Table System -->
   <div class="box box-solid" style="border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 4px 25px rgba(0,0,0,0.03); overflow:hidden;">
      <div class="box-body" style="padding:0;">
         <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
            <table class="table table-hover" style="font-size:12.5px; margin-bottom:0; vertical-align:middle;">
               <thead>
                  <tr style="background:#0f172a; color:#ffffff; font-size:11px; text-transform:uppercase; letter-spacing:0.6px;">
                     <th width="30" class="text-center" style="padding:15px 10px;"><input type="checkbox" id="check_all"></th>
                     <th style="padding:15px 10px;">NO PESANAN</th>
                     <th style="padding:15px 10px;">STATUS PESANAN</th>
                     <th style="padding:15px 10px;">NO RESI</th>
                     <th style="padding:15px 10px;" class="text-center">QTY ITEM</th>
                     <th style="padding:15px 10px;">OPSI PENGIRIMAN</th>
                     <th style="padding:15px 10px;">WAKTU PEMBAYARAN</th>
                     <th style="padding:15px 10px;">USERNAME PEMBELI</th>
                     <th style="padding:15px 10px;">NAMA PENERIMA</th>
                     <th style="padding:15px 10px;">NOTELP</th>
                     <th style="padding:15px 10px;">STATUS</th>
                     <th style="padding:15px 10px;">KOMPLAIN</th>
                     <th style="padding:15px 10px;" class="text-center" width="110">ACTION</th>
                  </tr>
               </thead>
               <tbody>
                  <?php if (!empty($sos)): ?>
                     <?php foreach ($sos as $so): 
                        $display_no_pesanan = !empty($so->no_pesanan) ? $so->no_pesanan : $so->nomor_so;
                        $display_status_pesanan = !empty($so->status_pesanan) ? $so->status_pesanan : 'Perlu Dikirim';
                        $display_resi = !empty($so->nomor_resi) ? $so->nomor_resi : '-';
                        $qty_item = isset($so->total_qty) ? (int)$so->total_qty : 1;
                        $opsi_kirim = !empty($so->opsi_pengiriman) ? $so->opsi_pengiriman : (!empty($so->kurir_ekspedisi) ? $so->kurir_ekspedisi : 'SPX Standard');
                        $waktu_bayar = !empty($so->waktu_pembayaran) ? date('Y-m-d H:i', strtotime($so->waktu_pembayaran)) : date('Y-m-d H:i', strtotime($so->created_at ?? $so->tanggal_so));
                        
                        $raw_username = !empty($so->username_pembeli) ? $so->username_pembeli : strtolower(str_replace(' ', '_', $so->nama_pelanggan ?? 'buyer'));
                        $raw_penerima = !empty($so->nama_penerima) ? $so->nama_penerima : ($so->nama_pelanggan ?? '-');
                        $raw_notelp   = !empty($so->no_telepon) ? $so->no_telepon : '-';
                        
                        /* Privacy Masking Rules:
                           - Username Pembeli: FULL UNMASKED (e.g. dimas_anggoro)
                           - Nama Penerima: SENSORED WITH ASTERISKS (e.g. D***s A*****o)
                           - Notelp: SENSORED WITH ASTERISKS (e.g. 0821****3322)
                        */
                        $masked_penerima = mask_nama_penerima($raw_penerima);
                        $masked_notelp   = mask_notelp($raw_notelp);

                        $source_lower = strtolower($so->source ?? 'shopee');
                        if ($source_lower == 'tokopedia') {
                            $channel_badge = '<span class="channel-pill channel-tokopedia"><i class="fa fa-shopping-cart"></i> Tokopedia</span>';
                        } elseif ($source_lower == 'shopee') {
                            $channel_badge = '<span class="channel-pill channel-shopee"><i class="fa fa-shopping-bag"></i> Shopee</span>';
                        } else {
                            $channel_badge = '<span class="channel-pill channel-manual"><i class="fa fa-cube"></i> Sales Order Regular</span>';
                        }

                        $courier_lower = strtolower($opsi_kirim);
                        if (strpos($courier_lower, 'sicepat') !== false) {
                            $courier_badge_class = 'courier-sicepat';
                        } elseif (strpos($courier_lower, 'j&t') !== false || strpos($courier_lower, 'jnt') !== false) {
                            $courier_badge_class = 'courier-jnt';
                        } else {
                            $courier_badge_class = 'courier-spx';
                        }
                     ?>
                     <tr style="border-bottom:1px solid #f1f5f9; transition:background 0.15s ease;">
                        <td class="text-center" style="vertical-align:middle;"><input type="checkbox" class="check_item" value="<?= $so->id; ?>"></td>
                        
                        <!-- 1. NO PESANAN -->
                        <td style="vertical-align:middle;">
                           <strong style="color:#0f172a; font-family:monospace; font-size:13px;"><a href="<?= site_url('administrator/penjualan_marketplace/view/' . $so->id); ?>" style="color:#0f172a;"><?= _ent($display_no_pesanan); ?></a></strong>
                           <button type="button" class="btn btn-link btn-xs text-muted" onclick="navigator.clipboard.writeText('<?= _ent($display_no_pesanan); ?>'); alert('No Pesanan Copied!');" title="Copy No Pesanan" style="padding:0; margin-left:4px;"><i class="fa fa-clone"></i></button>
                           <br><?= $channel_badge; ?>
                        </td>

                        <!-- 2. STATUS PESANAN -->
                        <td style="vertical-align:middle;">
                           <span class="label" style="background:#fef3c7; color:#b45309; font-size:10.5px; font-weight:700; padding:5px 9px; border-radius:12px;">
                              <?= _ent($display_status_pesanan); ?>
                           </span>
                        </td>

                        <!-- 3. NO RESI -->
                        <td style="vertical-align:middle;">
                           <span style="font-family:monospace; font-weight:700; color:#0284c7; font-size:12.5px;"><?= _ent($display_resi); ?></span>
                           <?php if($display_resi != '-'): ?>
                              <button type="button" class="btn btn-link btn-xs text-muted" onclick="navigator.clipboard.writeText('<?= _ent($display_resi); ?>'); alert('No Resi Copied!');" title="Copy Resi" style="padding:0; margin-left:4px;"><i class="fa fa-clone"></i></button>
                           <?php endif; ?>
                        </td>

                        <!-- 4. QTY ITEM -->
                        <td class="text-center" style="vertical-align:middle;"><strong style="font-size:13.5px; color:#0f172a;"><?= $qty_item; ?></strong></td>

                        <!-- 5. OPSI PENGIRIMAN -->
                        <td style="vertical-align:middle;">
                           <span class="courier-pill <?= $courier_badge_class; ?>"><?= _ent($opsi_kirim); ?></span>
                        </td>

                        <!-- 6. WAKTU PEMBAYARAN -->
                        <td style="vertical-align:middle;"><small style="color:#475569; font-weight:600;"><i class="fa fa-clock-o text-muted"></i> <?= $waktu_bayar; ?></small></td>

                        <!-- 7. USERNAME PEMBELI (UNMASKED / FULL) -->
                        <td style="vertical-align:middle;">
                           <strong style="color:#0284c7; font-size:12.5px; font-weight:700;"><?= _ent($raw_username); ?></strong>
                        </td>

                        <!-- 8. NAMA PENERIMA (MASKED WITH ASTERISKS: D***s A*****o) -->
                        <td style="vertical-align:middle; white-space:nowrap;">
                           <strong style="color:#334155; font-size:12px; font-weight:700;" title="Disensor untuk Privasi Halaman Web"><?= _ent($masked_penerima); ?></strong>
                        </td>

                        <!-- 9. NOTELP (MASKED WITH ASTERISKS: 0821****3322) -->
                        <td style="vertical-align:middle; white-space:nowrap;">
                           <small class="text-muted" style="font-size:11.5px; font-weight:600;" title="Disensor untuk Privasi Halaman Web"><i class="fa fa-phone text-muted"></i> <?= _ent($masked_notelp); ?></small>
                        </td>

                        <!-- 10. STATUS (DINAMIS DARI SCANNER WORKSTATION) -->
                        <td style="vertical-align:middle;">
                           <?php 
                           $st_lower = strtolower($so->status ?? 'packing');
                           if (in_array($st_lower, ['shipped', 'dikirim'])) {
                               $st_badge_bg = '#10b981';
                               $st_label = 'SHIPPED';
                           } elseif (in_array($st_lower, ['delivered', 'completed', 'selesai'])) {
                               $st_badge_bg = '#059669';
                               $st_label = 'DELIVERED';
                           } elseif (in_array($st_lower, ['cancelled', 'batal'])) {
                               $st_badge_bg = '#ef4444';
                               $st_label = 'CANCELLED';
                           } else {
                               $st_badge_bg = '#0284c7';
                               $st_label = (!empty($so->status_pesanan) && strtoupper($so->status_pesanan) != 'PERLU DIKIRIM') ? strtoupper($so->status_pesanan) : 'PACKED';
                           }
                           ?>
                           <span class="status-packed-badge" style="background:<?= $st_badge_bg; ?>;"><?= $st_label; ?></span>
                        </td>

                        <!-- 11. KOMPLAIN -->
                        <td style="vertical-align:middle; color:#64748b;">-</td>

                        <!-- 12. ACTION (HANYA UNTUK DETAIL DAN CETAK STRUK) -->
                        <td class="text-center" style="vertical-align:middle; white-space:nowrap;">
                           <a href="<?= site_url('administrator/penjualan_marketplace/view/' . $so->id); ?>" class="btn btn-default btn-xs" style="border-radius:6px; font-weight:600; padding:5px 9px; margin-right:4px;" title="Lihat Detail Pesanan">
                              <i class="fa fa-eye text-primary"></i> Detail
                           </a>
                           <a href="<?= site_url('administrator/penjualan_marketplace/cetak_resi/' . $so->id); ?>" target="_blank" class="btn btn-info btn-xs" style="background:#0284c7; border-color:#0284c7; border-radius:6px; font-weight:600; padding:5px 9px;" title="Cetak Struk / Resi Thermal AWB">
                              <i class="fa fa-print"></i> Cetak Struk
                           </a>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <tr>
                        <td colspan="13" class="text-center text-muted" style="padding:50px;">
                           <i class="fa fa-shopping-cart fa-3x" style="color:#cbd5e1; margin-bottom:12px;"></i><br>
                           <span style="font-size:16px; font-weight:bold; color:#334155;">Belum ada data Penjualan Marketplace.</span><br>
                           <small class="text-muted">Silakan import CSV Shopee/Tokopedia atau buat pesanan baru.</small>
                        </td>
                     </tr>
                  <?php endif; ?>
               </tbody>
            </table>
         </div>
      </div>
      <div class="box-footer" style="background:#ffffff; border-top:1px solid #e2e8f0; padding:16px 24px;">
         <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12" style="padding-top:6px;">
               <span class="text-muted" style="font-size:12.5px;">Menampilkan total <strong><?= count($sos); ?></strong> dari <strong><?= $so_counts; ?></strong> Pesanan Penjualan Marketplace</span>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 text-right">
               <?= $pagination; ?>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL IMPORT PENJUALAN WITH GUDANG SELECTOR & CSV LIVE PREVIEW -->
<div class="modal fade" id="modalImportMarketplace" tabindex="-1" role="dialog" aria-labelledby="modalImportMarketplaceLabel">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="border-radius:14px; border:none; box-shadow: 0 10px 35px rgba(0,0,0,0.15);">
         <div class="modal-header style-primary" style="background:#6366f1; color:#fff; border-top-left-radius:14px; border-top-right-radius:14px; padding:18px 26px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:0.8;"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalImportMarketplaceLabel" style="font-weight:700;"><i class="fa fa-file-excel-o"></i> Import Penjualan Marketplace (Shopee / Tokopedia)</h4>
         </div>
         <div class="modal-body" style="padding:25px;">
            <ul class="nav nav-tabs" role="tablist" style="margin-bottom:20px;">
               <li role="presentation" class="active">
                  <a href="#shopee_tab" aria-controls="shopee_tab" role="tab" data-toggle="tab" style="font-weight:700; color:#ee4d2d;"><i class="fa fa-shopping-bag"></i> Import Shopee CSV</a>
               </li>
               <li role="presentation">
                  <a href="#tokopedia_tab" aria-controls="tokopedia_tab" role="tab" data-toggle="tab" style="font-weight:700; color:#03ac0e;"><i class="fa fa-shopping-cart"></i> Import Tokopedia CSV</a>
               </li>
            </ul>

            <div class="tab-content">
               <!-- SHOPEE TAB -->
               <div role="tabpanel" class="tab-pane active" id="shopee_tab">
                  <div class="callout callout-info" style="border-left-color: #ee4d2d; background-color:#fff5f2; color:#333; border-radius:8px;">
                     <h5 style="font-weight:700; color:#ee4d2d;"><i class="fa fa-info-circle"></i> Petunjuk Import CSV Shopee (45 Kolom):</h5>
                     <p style="font-size:12px;">Format file sesuai dengan export pesanan Shopee Seller Centre. Pilih file CSV &amp; Gudang Tujuan untuk melihat pratinjau data.</p>
                     <a href="<?= site_url('administrator/penjualan_marketplace/download_template_shopee'); ?>" class="btn btn-sm btn-danger btn-flat" style="margin-top:5px; background-color:#ee4d2d; border:none; border-radius:6px; font-weight:700;"><i class="fa fa-download"></i> Download Template CSV Shopee</a>
                  </div>

                  <form action="<?= site_url('administrator/penjualan_marketplace/import_shopee'); ?>" method="POST" enctype="multipart/form-data">
                     <div class="row">
                        <div class="col-md-6 col-sm-12">
                           <div class="form-group">
                              <label style="font-weight:700; color:#334155;"><i class="fa fa-building text-danger"></i> Pilih Gudang Tujuan Pesanan:</label>
                              <select name="gudang_id" class="form-control" required style="border-radius:6px; font-weight:600;">
                                 <option value="">-- Pilih Gudang Tujuan --</option>
                                 <?php foreach($gudangs as $g): ?>
                                    <option value="<?= $g->id; ?>" <?= ($g->id == 1 || $g->status == 'aktif') ? 'selected':''; ?>><?= _ent($g->nama_gudang); ?></option>
                                 <?php endforeach; ?>
                              </select>
                           </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                           <div class="form-group">
                              <label style="font-weight:700; color:#334155;"><i class="fa fa-file-text-o text-danger"></i> Pilih File CSV Shopee (.csv):</label>
                              <input type="file" id="file_csv_shopee" name="file_csv" class="form-control" accept=".csv" required style="border-radius:6px;" onchange="previewCSV(this, 'preview_shopee_box', 'btn_submit_shopee', 'Shopee')">
                           </div>
                        </div>
                     </div>

                     <!-- CSV Live Preview Box -->
                     <div id="preview_shopee_box"></div>

                     <div class="text-right" style="margin-top:20px;">
                        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius:6px;">Batal</button>
                        <button type="submit" id="btn_submit_shopee" class="btn btn-success btn-flat" style="font-weight:700; background-color:#ee4d2d; border-color:#ee4d2d; border-radius:6px;" disabled><i class="fa fa-upload"></i> Proses &amp; Import Shopee CSV</button>
                     </div>
                  </form>
               </div>

               <!-- TOKOPEDIA TAB -->
               <div role="tabpanel" class="tab-pane" id="tokopedia_tab">
                  <div class="callout callout-success" style="border-left-color: #03ac0e; background-color:#f2faf3; color:#333; border-radius:8px;">
                     <h5 style="font-weight:700; color:#03ac0e;"><i class="fa fa-info-circle"></i> Petunjuk Import CSV Tokopedia:</h5>
                     <p style="font-size:12px;">Upload file CSV export laporan pesanan Tokopedia Seller Center. Pilih Gudang Tujuan untuk melihat pratinjau data.</p>
                     <a href="<?= site_url('administrator/penjualan_marketplace/download_template_tokopedia'); ?>" class="btn btn-sm btn-success btn-flat" style="margin-top:5px; background-color:#03ac0e; border:none; border-radius:6px; font-weight:700;"><i class="fa fa-download"></i> Download Template CSV Tokopedia</a>
                  </div>

                  <form action="<?= site_url('administrator/penjualan_marketplace/import_tokopedia'); ?>" method="POST" enctype="multipart/form-data">
                     <div class="row">
                        <div class="col-md-6 col-sm-12">
                           <div class="form-group">
                              <label style="font-weight:700; color:#334155;"><i class="fa fa-building text-success"></i> Pilih Gudang Tujuan Pesanan:</label>
                              <select name="gudang_id" class="form-control" required style="border-radius:6px; font-weight:600;">
                                 <option value="">-- Pilih Gudang Tujuan --</option>
                                 <?php foreach($gudangs as $g): ?>
                                    <option value="<?= $g->id; ?>" <?= ($g->id == 1 || $g->status == 'aktif') ? 'selected':''; ?>><?= _ent($g->nama_gudang); ?></option>
                                 <?php endforeach; ?>
                              </select>
                           </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                           <div class="form-group">
                              <label style="font-weight:700; color:#334155;"><i class="fa fa-file-text-o text-success"></i> Pilih File CSV Tokopedia (.csv):</label>
                              <input type="file" id="file_csv_tokopedia" name="file_csv" class="form-control" accept=".csv" required style="border-radius:6px;" onchange="previewCSV(this, 'preview_tokopedia_box', 'btn_submit_tokopedia', 'Tokopedia')">
                           </div>
                        </div>
                     </div>

                     <!-- CSV Live Preview Box -->
                     <div id="preview_tokopedia_box"></div>

                     <div class="text-right" style="margin-top:20px;">
                        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="border-radius:6px;">Batal</button>
                        <button type="submit" id="btn_submit_tokopedia" class="btn btn-success btn-flat" style="font-weight:700; background-color:#03ac0e; border-color:#03ac0e; border-radius:6px;" disabled><i class="fa fa-upload"></i> Proses &amp; Import Tokopedia CSV</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- MODAL IMPORT PACKED -->
<div class="modal fade" id="modalImportPacked" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content" style="border-radius:14px; border:none; box-shadow: 0 10px 35px rgba(0,0,0,0.15);">
         <div class="modal-header" style="background:#10b981; color:#fff; border-top-left-radius:14px; border-top-right-radius:14px; padding:18px 24px;">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8;">&times;</button>
            <h4 class="modal-title" style="font-weight:700;"><i class="fa fa-barcode"></i> Import Status Packed / Scan Barcode</h4>
         </div>
         <div class="modal-body" style="padding:24px;">
            <p style="color:#64748b;">Fitur Scan Resi / Update Status Packed Massal dari Gudang Pengemasan.</p>

            <div class="text-center" style="margin-bottom:20px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:16px;">
               <h5 style="font-weight:800; color:#166534; margin-top:0;"><i class="fa fa-camera"></i> Gunakan Workstation Scan Kamera HP / Laptop</h5>
               <p style="font-size:12px; color:#15803d; margin-bottom:12px;">Fitur scan barcode presisi menggunakan Kamera HP / Laptop, Physical Scanner USB, &amp; Paste Massal dengan bunyi Beep.</p>
               <a href="<?= site_url('administrator/penjualan_marketplace/import_packed'); ?>" class="btn btn-success btn-flat" style="border-radius:6px; font-weight:700; background:#10b981; border-color:#10b981;"><i class="fa fa-crosshairs"></i> Buka Workstation Scan Kamera &amp; Barcode</a>
            </div>

            <hr style="margin:15px 0;">

            <div class="form-group">
               <label style="font-weight:700; color:#0f172a;">Scan / Input No. Resi atau No. Pesanan:</label>
               <div class="input-group">
                  <input type="text" id="modal_quick_scan_input" class="form-control input-lg" placeholder="Scan Barcode No Resi di sini..." autofocus style="border-radius:8px 0 0 8px; font-family:monospace; text-transform:uppercase; font-weight:700;">
                  <span class="input-group-btn">
                     <button type="button" id="modal_quick_scan_btn" class="btn btn-success btn-lg" style="border-radius:0 8px 8px 0; font-weight:700; background:#10b981; border-color:#10b981;"><i class="fa fa-check"></i> Submit</button>
                  </span>
               </div>
               <div id="modal_quick_scan_feedback" style="margin-top:10px; font-weight:700;"></div>
            </div>
         </div>
         <div class="modal-footer" style="padding:16px 24px; background:#f8fafc; border-bottom-left-radius:14px; border-bottom-right-radius:14px;">
            <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius:6px;">Tutup</button>
         </div>
      </div>
   </div>
</div>

<script>
$(document).ready(function(){
   $('#check_all').click(function(){
      $('.check_item').prop('checked', this.checked);
   });

   // Disable overlapping toastr box to prevent header buttons from being covered
   if (typeof toastr !== 'undefined') {
      toastr.clear();
   }

   $('#modal_quick_scan_input').keypress(function(e){
      if (e.which == 13) {
         e.preventDefault();
         $('#modal_quick_scan_btn').click();
      }
   });

   $('#modal_quick_scan_btn').click(function(){
      var resi = $('#modal_quick_scan_input').val().trim();
      if (!resi) return;

      $.ajax({
         url: '<?= site_url("administrator/penjualan_marketplace/process_scan_resi"); ?>',
         type: 'POST',
         dataType: 'json',
         data: { nomor_resi: resi, target_status: 'packing' },
         success: function(res) {
            if (res.status === 'success') {
               $('#modal_quick_scan_feedback').html('<span class="text-success"><i class="fa fa-check-circle"></i> ' + res.message + '</span>');
               $('#modal_quick_scan_input').val('').focus();
               setTimeout(function(){ location.reload(); }, 1200);
            } else {
               $('#modal_quick_scan_feedback').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> ' + res.message + '</span>');
            }
         }
      });
   });
});

/* CSV Live Review / Preview Function */
function previewCSV(fileInput, previewContainerId, submitBtnId, channelName) {
   var file = fileInput.files[0];
   if (!file) return;

   var reader = new FileReader();
   reader.onload = function(e) {
      var text = e.target.result;
      var lines = text.split(/\r\n|\n/);
      if (lines.length <= 1) {
         $('#' + previewContainerId).html('<div class="alert alert-warning" style="margin-top:15px; border-radius:6px;">File CSV kosong atau tidak valid.</div>');
         $('#' + submitBtnId).prop('disabled', true);
         return;
      }

      function parseCSVLine(line) {
         var values = [];
         var current = '';
         var inQuotes = false;
         for (var i = 0; i < line.length; i++) {
            var char = line[i];
            if (char === '"') {
               inQuotes = !inQuotes;
            } else if (char === ',' && !inQuotes) {
               values.push(current.trim().replace(/^"|"$/g, ''));
               current = '';
            } else {
               current += char;
            }
         }
         values.push(current.trim().replace(/^"|"$/g, ''));
         return values;
      }

      var header = parseCSVLine(lines[0]);
      var rows = [];
      for (var i = 1; i < lines.length; i++) {
         if (lines[i].trim() !== '') {
            var parsed = parseCSVLine(lines[i]);
            if (parsed.length >= 3) {
               rows.push(parsed);
            }
         }
      }

      if (rows.length === 0) {
         $('#' + previewContainerId).html('<div class="alert alert-warning" style="margin-top:15px; border-radius:6px;">Tidak ada data pesanan valid ditemukan di file CSV.</div>');
         $('#' + submitBtnId).prop('disabled', true);
         return;
      }

      var html = '<div style="margin-top:15px; border:1px solid #e2e8f0; border-radius:8px; padding:15px; background:#f8fafc;">';
      html += '<div style="margin-bottom:10px; display:flex; justify-content:space-between; align-items:center;">';
      html += '<strong style="color:#0f172a; font-size:13px;"><i class="fa fa-table text-primary"></i> Review Pratinjau CSV (' + rows.length + ' Total Pesanan ' + channelName + ')</strong>';
      html += '<span class="badge" style="background:#0284c7; font-size:11px; padding:4px 8px;">Pratinjau 8 Pesanan Pertama</span>';
      html += '</div>';

      html += '<div class="table-responsive" style="max-height:220px; overflow-y:auto; border:1px solid #cbd5e1; border-radius:6px;">';
      html += '<table class="table table-bordered table-striped table-hover" style="font-size:11.5px; background:#fff; margin-bottom:0;">';
      html += '<thead><tr style="background:#0f172a; color:#fff;"><th>#</th><th>NO PESANAN</th><th>NO RESI</th><th>NAMA PENERIMA</th><th>TOTAL PEMBAYARAN</th></tr></thead>';
      html += '<tbody>';

      var limit = Math.min(8, rows.length);
      for (var j = 0; j < limit; j++) {
         var r = rows[j];
         var no_pesanan = r[0] || '-';
         var no_resi = (channelName === 'Shopee') ? (r[4] || '-') : (r[2] || '-');
         var penerima = (channelName === 'Shopee') ? (r[39] || r[38] || '-') : (r[14] || r[13] || '-');
         var total = (channelName === 'Shopee') ? (r[34] || r[18] || '-') : (r[11] || r[10] || '-');

         html += '<tr>';
         html += '<td>' + (j + 1) + '</td>';
         html += '<td><strong style="font-family:monospace; color:#0f172a;">' + no_pesanan + '</strong></td>';
         html += '<td><span style="font-family:monospace; color:#0284c7; font-weight:bold;">' + no_resi + '</span></td>';
         html += '<td><strong>' + penerima + '</strong></td>';
         html += '<td><strong class="text-success">Rp ' + total + '</strong></td>';
         html += '</tr>';
      }

      html += '</tbody></table></div>';
      html += '<div style="margin-top:10px; font-size:11.5px; color:#15803d; font-weight:700;"><i class="fa fa-check-circle"></i> File CSV Berhasil Dibaca! Silakan klik tombol "Proses & Import" di bawah megedit ke database.</div>';
      html += '</div>';

      $('#' + previewContainerId).html(html);
      $('#' + submitBtnId).prop('disabled', false);
   };
   reader.readAsText(file);
}
</script>
