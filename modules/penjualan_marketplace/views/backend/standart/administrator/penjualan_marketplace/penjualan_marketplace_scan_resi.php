<?php
/**
 * View: Workstation Terpadu Scan Resi & Dokumen PDF/Foto Label Thermal
 * File: penjualan_marketplace_scan_resi.php
 * Dual-Layer PDF.js Client-Side Engine & Multi-Tier Unmasking Parser
 */
?>
<!-- Include PDF.js Library for Client-Side PDF Text Extraction & Thumbnail Canvas Rendering -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<!-- Include HTML5-QRCode Library for Camera Scanning -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<!-- Include QuaggaJS Library for High-Accuracy 1D Code 128 Thermal Label Barcode Scanning -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<!-- Include JsBarcode Library for Rendering Dynamic Barcodes in Thermal Label Preview -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<!-- Include Tesseract.js Library for OCR Text Extraction of Scanned/Image Thermal Labels -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap');

.workstation-container {
   font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
   padding: 15px 20px;
}

/* Main Box Styling */
.ws-main-box {
   border-radius: 16px;
   border: 1px solid #e2e8f0;
   box-shadow: 0 10px 35px rgba(15, 23, 42, 0.05);
   overflow: hidden;
   transition: all 0.3s ease;
}

.ws-card-header {
   background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
   color: #ffffff;
   border-top-left-radius: 15px;
   border-top-right-radius: 15px;
   padding: 18px 24px;
   display: flex;
   justify-content: space-between;
   align-items: center;
}

/* Nav Tabs & Micro-Animations */
.ws-nav-tabs {
   border-bottom: none;
   margin-bottom: 20px;
   background: #f1f5f9;
   border-radius: 12px;
   padding: 5px;
   display: flex;
   gap: 6px;
}
.ws-nav-tabs > li {
   flex: 1;
   text-align: center;
}
.ws-nav-tabs > li > a {
   border-radius: 8px !important;
   font-weight: 700;
   font-size: 13px;
   color: #64748b;
   padding: 11px 16px;
   border: none !important;
   margin-right: 0 !important;
   transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.ws-nav-tabs > li.active > a,
.ws-nav-tabs > li.active > a:focus,
.ws-nav-tabs > li.active > a:hover {
   background: #ffffff !important;
   color: #0284c7 !important;
   box-shadow: 0 4px 14px rgba(2, 132, 199, 0.12);
   transform: translateY(-1px);
}

.tab-pane {
   animation: fadeInScale 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Dropzone & Touch Area */
.dropzone-box {
   border: 2px dashed #cbd5e1;
   background: #f8fafc;
   border-radius: 14px;
   padding: 28px 20px;
   text-align: center;
   cursor: pointer;
   transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.dropzone-box:hover, .dropzone-box.drag-over {
   border-color: #0284c7;
   background: #f0f9ff;
   transform: translateY(-2px);
   box-shadow: 0 8px 25px rgba(2, 132, 199, 0.08);
}
.dropzone-box:active {
   transform: scale(0.99);
}

#reader {
   width: 100%;
   max-width: 380px;
   margin: 0 auto;
   border-radius: 14px;
   overflow: hidden;
   border: 3px solid #0284c7;
   box-shadow: 0 10px 25px rgba(2, 132, 199, 0.25);
   background: #0f172a;
}

/* Unmasked Banner Card & Pulse Badge */
.unmask-alert-box {
   background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
   border: 1px solid #86efac;
   border-radius: 14px;
   padding: 18px 20px;
   margin-bottom: 18px;
   box-shadow: 0 4px 20px rgba(34, 197, 94, 0.08);
   animation: slideUpFade 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
   transition: all 0.25s ease;
}

.badge-unmasked-pulse {
   animation: greenPulse 2s infinite;
}

/* Bento Summary Stat Cards */
.bento-card {
   background: #ffffff;
   border: 1px solid #e2e8f0;
   border-radius: 12px;
   padding: 14px 16px;
   box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
   margin-bottom: 12px;
   transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
   position: relative;
   overflow: hidden;
}
.bento-card:hover {
   transform: translateY(-3px) scale(1.01);
   box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
   border-color: #cbd5e1;
}

.bento-icon-wrapper {
   width: 36px;
   height: 36px;
   border-radius: 10px;
   display: flex;
   align-items: center;
   justify-content: center;
   font-size: 15px;
   transition: all 0.25s ease;
}
.bento-card:hover .bento-icon-wrapper {
   transform: scale(1.1) rotate(-4deg);
}

/* Keyframes Micro-Animations */
@keyframes slideUpFade {
   from {
      opacity: 0;
      transform: translateY(14px);
   }
   to {
      opacity: 1;
      transform: translateY(0);
   }
}

@keyframes fadeInScale {
   from {
      opacity: 0;
      transform: scale(0.98);
   }
   to {
      opacity: 1;
      transform: scale(1);
   }
}

@keyframes greenPulse {
   0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
   70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
   100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

#scan_results_container {
   animation: slideUpFade 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Table Styling & Custom Scrollbar */
.table-responsive {
   -webkit-overflow-scrolling: touch;
}
.table-responsive::-webkit-scrollbar {
   height: 6px;
   width: 6px;
}
.table-responsive::-webkit-scrollbar-track {
   background: #f1f5f9;
   border-radius: 4px;
}
.table-responsive::-webkit-scrollbar-thumb {
   background: #cbd5e1;
   border-radius: 4px;
}
.table-responsive::-webkit-scrollbar-thumb:hover {
   background: #94a3b8;
}

tbody#tbody_bulk_summary tr {
   transition: background-color 0.2s ease;
}
tbody#tbody_bulk_summary tr:hover {
   background: #f1f5f9 !important;
}

tbody#tbody_bulk_summary tr.active-scanned-row {
   background: #eff6ff !important;
   border-left: 4px solid #0284c7;
}

.sku-chip {
   font-family: 'JetBrains Mono', 'Fira Code', monospace;
   font-weight: 700;
   font-size: 11px;
   background: #f1f5f9;
   color: #334155;
   border: 1px solid #cbd5e1;
   border-radius: 6px;
   padding: 2px 7px;
   display: inline-block;
   white-space: nowrap;
   transition: all 0.2s ease;
}

.variasi-pill {
   font-size: 10.5px;
   font-weight: 600;
   background: #f0f9ff;
   color: #0284c7;
   border: 1px solid #bae6fd;
   padding: 2px 8px;
   border-radius: 12px;
   display: inline-block;
   white-space: nowrap;
}

.qty-badge {
   font-size: 11.5px;
   font-weight: 800;
   background: #e0e7ff;
   color: #4338ca;
   padding: 2px 9px;
   border-radius: 12px;
   display: inline-block;
}

/* Button Click Press Effect */
.btn {
   transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
.btn:active {
   transform: scale(0.96) !important;
}

/* -------------------------------------------------------------
   COMPREHENSIVE RESPONSIVE MEDIA QUERIES (Mobile, Tablet, Desktop)
   ------------------------------------------------------------- */
@media (max-width: 991px) {
   .col-md-5 #pdf_thumbnail_container {
      position: relative !important;
      top: 0 !important;
      margin-top: 20px;
   }
   .ws-card-header {
      padding: 16px 20px;
   }
}

@media (max-width: 767px) {
   .workstation-container {
      padding: 6px 8px !important;
   }
   
   .ws-card-header {
      flex-direction: column;
      align-items: flex-start !important;
      gap: 12px;
      padding: 14px 16px !important;
   }
   .ws-card-header > div:last-child {
      width: 100%;
      text-align: right;
   }
   
   .ws-nav-tabs {
      flex-direction: column;
      gap: 5px !important;
   }
   .ws-nav-tabs > li > a {
      font-size: 12.5px !important;
      padding: 10px 12px !important;
      text-align: left;
   }

   .dropzone-box {
      padding: 22px 14px !important;
   }
   .dropzone-box h4 {
      font-size: 15px !important;
   }
   .dropzone-box label {
      width: 100%;
      white-space: normal;
      font-size: 12px !important;
      padding: 10px 12px;
   }

   /* Unmask Section Mobile */
   .unmask-alert-box {
      padding: 14px 15px !important;
   }
   .unmask-header-flex {
      flex-direction: column;
      align-items: flex-start !important;
      gap: 6px;
   }
   .unmask-btn-wrapper {
      text-align: center !important;
      margin-top: 10px;
   }
   .unmask-btn-wrapper #btn_save_unmask {
      width: 100% !important;
      padding: 10px;
      font-size: 13px;
   }

   /* Bento Cards Grid on Mobile (2 per row for compact view) */
   .bento-card {
      padding: 10px 12px !important;
      margin-bottom: 10px !important;
   }
   .bento-card .bento-icon-wrapper {
      width: 30px !important;
      height: 30px !important;
      font-size: 13px !important;
   }

   /* Preview Container Mobile Scaling */
   #pdf_thumbnail_container {
      padding: 12px !important;
   }
   #pdf_thumbnail_canvas {
      max-width: 100% !important;
      height: auto !important;
      object-fit: contain;
   }
   #preview_type_tabs {
      width: 100%;
      display: flex;
      margin-bottom: 8px;
   }
   #preview_type_tabs button {
      flex: 1;
      padding: 8px;
      font-size: 11px !important;
   }
   #pdf_page_nav_controls {
      width: 100%;
      justify-content: center;
      margin-top: 4px;
   }

   /* Footer Flex Mobile */
   .ws-footer-flex {
      flex-direction: column;
      gap: 10px;
      padding: 14px 16px !important;
   }
   .ws-footer-flex a {
      width: 100%;
      text-align: center;
      padding: 10px;
   }
}

@media (max-width: 480px) {
   /* Extra small phone screens */
   .col-xs-6 {
      width: 50% !important;
      float: left;
      padding-left: 5px !important;
      padding-right: 5px !important;
   }
   .bento-card {
      padding: 8px 10px !important;
   }
   .bento-card div[style*="font-size:18px"] {
      font-size: 14px !important;
   }
   .bento-card div[style*="font-size:17px"] {
      font-size: 13px !important;
   }
   .bento-card div[style*="font-size:13.5px"] {
      font-size: 11.5px !important;
   }
   
   /* Thermal Label Responsive Fit */
   #live_thermal_label_wrapper {
      max-width: 100% !important;
      overflow-x: auto !important;
      -webkit-overflow-scrolling: touch;
   }
   #live_thermal_label_wrapper svg,
   #live_thermal_label_wrapper canvas,
   #live_thermal_label_wrapper img {
      max-width: 100% !important;
      height: auto !important;
   }
}
</style>

<section class="content-header" style="padding-top:15px; padding-bottom:10px;">
   <div class="row">
      <div class="col-md-8 col-sm-12">
         <h3 style="margin:0; font-weight:800; color:#0f172a;">
            <i class="fa fa-search text-info"></i> Workstation Terpadu Scan Resi &amp; Breakdown Pesanan
            <small style="font-size:12px; color:#64748b; display:inline-block; margin-left:8px;">Upload Dokumen PDF/Foto Resi Thermal atau Kamera Real-Time</small>
         </h3>
      </div>
      <div class="col-md-4 col-sm-12 text-right">
         <ol class="breadcrumb" style="background:transparent; margin:0; padding:0;">
            <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?= site_url('administrator/penjualan_marketplace'); ?>">Penjualan Marketplace</a></li>
            <li class="active">Scan &amp; Breakdown Workstation</li>
         </ol>
      </div>
   </div>
</section>

<section class="content workstation-container" style="padding-top:0;">

   <!-- Main Workstation Box -->
   <div class="box box-solid" style="border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 8px 30px rgba(0,0,0,0.04); overflow:hidden;">
      
      <!-- Box Header -->
      <div class="ws-card-header">
         <div>
            <strong style="font-size:15px; font-weight:800;"><i class="fa fa-file-text-o text-info"></i> Intelligent Thermal Label &amp; PDF Reader</strong>
            <div style="font-size:12px; opacity:0.8; margin-top:2px;">Membaca Dokumen PDF Label Thermal Shopee/SPX/JNE, Ekstraksi Data Unmasked Penerima, &amp; Breakdown Produk ERP</div>
         </div>
         <div>
            <a href="<?= site_url('administrator/penjualan_marketplace'); ?>" class="btn btn-sm btn-default" style="border-radius:6px; font-weight:700;">
               <i class="fa fa-arrow-left"></i> Kembali
            </a>
         </div>
      </div>

      <div class="box-body" style="padding:22px;">

         <!-- 2 Simplified Main Tabs -->
         <ul class="nav ws-nav-tabs" role="tablist">
            <li role="presentation" class="active">
               <a href="#tab_upload_pdf" aria-controls="tab_upload_pdf" role="tab" data-toggle="tab"><i class="fa fa-folder-open-o"></i> 📁 1. Upload Foto / Dokumen PDF Resi</a>
            </li>
            <li role="presentation">
               <a href="#tab_kamera_scan" aria-controls="tab_kamera_scan" role="tab" data-toggle="tab"><i class="fa fa-camera"></i> 📷 2. Kamera Pemindai HP / Laptop</a>
            </li>
         </ul>

         <div class="tab-content" style="padding-top:6px;">
            
            <!-- TAB 1: PDF / FOTO UPLOAD -->
            <div role="tabpanel" class="tab-pane active" id="tab_upload_pdf">
               <!-- 1. Dropzone Box & Manual Input -->
               <div class="dropzone-box" id="dropzone_box_container">
                  <i class="fa fa-cloud-upload text-primary" style="font-size:38px; margin-bottom:8px;"></i>
                  <h4 style="font-weight:800; color:#0f172a; margin-top:0; margin-bottom:4px;">Pilih atau Drag &amp; Drop File PDF / Foto Resi</h4>
                  <p style="color:#64748b; font-size:12px; margin-bottom:12px;">Mendukung berkas <strong>PDF Thermal Shopee/JNE/SPX</strong>, <strong>JPG</strong>, <strong>PNG</strong>, <strong>WEBP</strong> (Pilih Banyak File / Bulk Upload).</p>
                  <label for="file_pdf_input" class="btn btn-primary flat" style="border-radius:6px; font-weight:700; background:#0284c7; border-color:#0284c7; cursor:pointer; margin-bottom:0;">
                     <i class="fa fa-files-o"></i> 📁 Buka File PDF / Foto Resi (Pilih Banyak Sekaligus)
                  </label>
                  <input type="file" id="file_pdf_input" accept=".pdf,image/*" multiple style="display:none;">
               </div>
                  
               <!-- Batch Bulk Progress & Auto-Input Summary Box -->
               <div id="bulk_progress_container" style="display:none; background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; padding:20px; margin-top:16px; box-shadow:0 10px 30px rgba(0,0,0,0.04);">
                     
                     <!-- 3 Bento Summary Stat Cards -->
                     <div class="row" style="margin-bottom:18px;">
                        <div class="col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                           <div style="background:#eff6ff; border:1px solid #dbeafe; border-radius:14px; padding:16px 20px; display:flex; align-items:center; gap:16px; box-shadow:0 2px 10px rgba(59,130,246,0.05);">
                              <div style="width:48px; height:48px; border-radius:12px; background:#dbeafe; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                 <i class="fa fa-files-o" style="font-size:22px; color:#2563eb;"></i>
                              </div>
                              <div>
                                 <div style="font-size:11.5px; font-weight:800; color:#3b82f6; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">TOTAL LABEL DIPROSES</div>
                                 <div style="font-size:26px; font-weight:800; color:#1e40af; line-height:1;" id="stat_total_processed">0</div>
                              </div>
                           </div>
                        </div>

                        <div class="col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                           <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:14px; padding:16px 20px; display:flex; align-items:center; gap:16px; box-shadow:0 2px 10px rgba(34,197,94,0.05);">
                              <div style="width:48px; height:48px; border-radius:12px; background:#dcfce7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                 <i class="fa fa-check-circle" style="font-size:24px; color:#16a34a;"></i>
                              </div>
                              <div>
                                 <div style="font-size:11.5px; font-weight:800; color:#16a34a; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">TERSIMPAN KE DATABASE</div>
                                 <div style="font-size:26px; font-weight:800; color:#15803d; line-height:1;" id="stat_total_success">0</div>
                              </div>
                           </div>
                        </div>

                        <div class="col-md-4 col-sm-4 col-xs-12" style="margin-bottom:10px;">
                           <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:14px; padding:16px 20px; display:flex; align-items:center; gap:16px; box-shadow:0 2px 10px rgba(239,68,68,0.05);">
                              <div style="width:48px; height:48px; border-radius:12px; background:#fee2e2; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                 <i class="fa fa-exclamation-triangle" style="font-size:22px; color:#dc2626;"></i>
                              </div>
                              <div>
                                 <div style="font-size:11.5px; font-weight:800; color:#dc2626; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">TIDAK DITEMUKAN / GAGAL</div>
                                 <div style="font-size:26px; font-weight:800; color:#991b1b; line-height:1;" id="stat_total_failed">0</div>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Progress Header & Bar -->
                     <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <strong style="color:#0f172a; font-size:13.5px;"><i class="fa fa-refresh fa-spin text-primary"></i> <span id="bulk_progress_title">Memproses Batch Upload Resi Massal...</span></strong>
                        <span class="badge" style="background:#0284c7; font-size:12px; padding:6px 14px; border-radius:12px;" id="bulk_progress_counter">0 / 0 Resi</span>
                     </div>
                     <div class="progress" style="height:10px; border-radius:6px; background:#e2e8f0; margin-bottom:16px;">
                        <div class="progress-bar progress-bar-primary progress-bar-striped active" id="bulk_progress_bar" role="progressbar" style="width:0%; background:#0284c7;"></div>
                     </div>
                     
                     <strong style="font-size:12.5px; color:#0f172a; display:block; margin-bottom:10px;"><i class="fa fa-database text-info"></i> Ringkasan Batch Upload &amp; Auto-Simpan Ke Database:</strong>
                     <div class="table-responsive" style="max-height:220px; overflow-y:auto; border:1px solid #cbd5e1; border-radius:8px; background:#fff; box-shadow:0 4px 15px rgba(0,0,0,0.03);">
                        <table class="table table-condensed table-striped table-hover" style="font-size:12px; margin-bottom:0;">
                           <thead>
                              <tr style="background:#0f172a; color:#fff; font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">
                                 <th width="30" class="text-center">#</th>
                                 <th>FILE / HALAMAN RESI</th>
                                 <th>NO. RESI (AWB)</th>
                                 <th>NO. PESANAN</th>
                                 <th>EKSPEDISI</th>
                                 <th>PENERIMA UNMASKED</th>
                                 <th>NO TELEPON</th>
                                 <th class="text-center">QTY ITEM</th>
                                 <th class="text-right">TOTAL NILAI</th>
                                 <th>STATUS DATABASE</th>
                                 <th class="text-center" width="80">AKSI</th>
                              </tr>
                           </thead>
                           <tbody id="tbody_bulk_summary">
                           </tbody>
                        </table>
                     </div>
                  </div>

               <!-- Direct Resi Search Fallback -->
               <div style="margin-top:12px;">
                  <div class="input-group">
                     <input type="text" id="manual_resi_input" class="form-control" placeholder="Atau ketikkan No. Resi / No. Pesanan di sini..." style="border-radius:8px 0 0 8px; font-family:monospace; text-transform:uppercase; font-weight:700;">
                     <span class="input-group-btn">
                        <button type="button" id="btn_search_manual" class="btn btn-primary" style="border-radius:0 8px 8px 0; background:#0284c7; border-color:#0284c7; font-weight:700;"><i class="fa fa-search"></i> Cari</button>
                     </span>
                  </div>
               </div>
            </div>

            <!-- TAB 2: LIVE CAMERA SCANNER -->
            <div role="tabpanel" class="tab-pane" id="tab_kamera_scan">
               <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px; text-align:center;">
                  <div id="https_camera_warning" class="alert alert-warning text-left" style="display:none; border-radius:8px; font-size:12px; margin-bottom:15px; background:#fffbeb; border-color:#fde68a; color:#92400e;">
                     <i class="fa fa-shield text-warning"></i> <strong>Pemberitahuan Keamanan Browser (Akses Kamera Web):</strong><br>
                     Browser memblokir kamera langsung jika diakses melalui koneksi non-HTTPS pada domain custom (<code>http://ci3_metafora.test:8080</code>).<br>
                     <strong>Solusi Cepat:</strong><br>
                     1. Gunakan koneksi aman HTTPS: <a href="https://ci3_metafora.test:8443/administrator/penjualan_marketplace/scan_resi" class="alert-link" style="text-decoration:underline; font-weight:700;">https://ci3_metafora.test:8443/administrator/penjualan_marketplace/scan_resi</a><br>
                     2. Atau klik tombol <strong>"📸 Ambil Foto Barcode via Kamera HP"</strong> di bawah untuk memotret barcode langsung.
                  </div>

                  <div class="row" style="margin-bottom:12px;">
                     <div class="col-md-6 col-md-offset-3 col-sm-12">
                        <div class="input-group">
                           <select id="camera_select" class="form-control" style="border-radius:6px; font-weight:600; font-size:12.5px;">
                              <option value="">Mengidentifikasi Kamera HP / Laptop...</option>
                           </select>
                           <span class="input-group-btn">
                              <button type="button" id="btn_start_camera" class="btn btn-primary" style="border-radius:6px; font-weight:700; background:#0284c7; border-color:#0284c7; font-size:12px;"><i class="fa fa-play"></i> Mulai Kamera</button>
                              <button type="button" id="btn_stop_camera" class="btn btn-danger" style="border-radius:6px; font-weight:700; font-size:12px; display:none;"><i class="fa fa-stop"></i> Stop Kamera</button>
                           </span>
                        </div>
                     </div>
                  </div>

                  <div style="margin-top:8px; margin-bottom:15px;">
                     <span style="color:#94a3b8; font-size:12px;">Atau potret langsung dengan kamera HP:</span><br>
                     <label for="camera_file_input" class="btn btn-info flat" style="border-radius:6px; font-weight:700; background:#0891b2; border-color:#0891b2; margin-top:6px; cursor:pointer;">
                        <i class="fa fa-camera"></i> 📸 Ambil Foto Barcode via Kamera HP
                     </label>
                     <input type="file" id="camera_file_input" accept="image/*" capture="environment" style="display:none;">
                  </div>

                  <p style="color:#64748b; font-size:12px; margin-bottom:12px;"><i class="fa fa-info-circle text-info"></i> Arahkan barcode resi (Code 128 / QR Code) ke dalam kotak kamera hingga terdengar bunyi Beep.</p>
                  
                  <div id="reader" style="display:none;"></div>
               </div>
            </div>

         </div>

         <!-- Status Notification Feedback -->
         <div id="scan_resi_feedback" style="margin-top:15px;"></div>

         <!-- RESULTS CONTAINER: 2-COLUMN ELEGANT WORKSTATION LAYOUT -->
         <div id="scan_results_container" style="display:none; margin-top:20px;">
            <div class="row">
               
               <!-- LEFT COLUMN (COL-MD-7): DATA UNMASKING, BENTO STATS, & ERP PRODUCT TABLE -->
               <div class="col-md-7 col-sm-12">
                  
                  <!-- 1. UNMASKED RECIPIENT INFORMATION BANNER -->
                  <div class="unmask-alert-box">
                     <div class="unmask-header-flex" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <div>
                           <strong style="font-size:14px; color:#166534; white-space:nowrap;"><i class="fa fa-unlock-alt text-success"></i> Data Unmasked Penerima</strong>
                           <div style="font-size:11.5px; color:#15803d; margin-top:2px;">Hasil ekstraksi label pengiriman</div>
                        </div>
                        <span class="label label-success" style="font-weight:700; font-size:11px; padding:5px 10px; border-radius:12px;"><i class="fa fa-check-circle"></i> 🔓 Unmasked</span>
                     </div>

                     <input type="hidden" id="unmask_target_id">

                     <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                           <div class="form-group" style="margin-bottom:8px;">
                              <label style="font-size:11px; font-weight:700; color:#0f172a;">Nama Penerima Asli:</label>
                              <input type="text" id="unmask_input_name" class="form-control" style="border-radius:6px; font-weight:700; color:#0284c7; height:34px; font-size:12.5px;">
                           </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6">
                           <div class="form-group" style="margin-bottom:8px;">
                              <label style="font-size:11px; font-weight:700; color:#0f172a;">No. Telepon Asli:</label>
                              <input type="text" id="unmask_input_phone" class="form-control" style="border-radius:6px; font-weight:700; color:#0284c7; height:34px; font-size:12.5px;">
                           </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                           <div class="form-group" style="margin-bottom:8px;">
                              <label style="font-size:11px; font-weight:700; color:#0f172a;">Alamat Pengiriman:</label>
                              <input type="text" id="unmask_input_address" class="form-control" style="border-radius:6px; font-weight:600; font-size:12px; height:34px;">
                           </div>
                        </div>
                     </div>

                     <div class="text-right unmask-btn-wrapper" style="margin-top:6px;">
                        <button type="button" id="btn_save_unmask" class="btn btn-success btn-flat btn-sm" style="border-radius:6px; font-weight:700; background:#10b981; border-color:#10b981;">
                           <i class="fa fa-save"></i> 💾 Simpan ke Database
                        </button>
                     </div>
                  </div>

                  <!-- 2. BENTO BREAKDOWN SUMMARY CARDS -->
                  <div class="row">
                     <div class="col-md-6 col-sm-6 col-xs-6">
                        <div class="bento-card" style="padding:12px 15px; margin-bottom:12px;">
                           <div style="display:flex; align-items:center; gap:12px;">
                              <div class="bento-icon-wrapper" style="background:#e0f2fe; color:#0284c7; width:34px; height:34px; border-radius:8px; margin-bottom:0;">
                                 <i class="fa fa-barcode"></i>
                              </div>
                              <div>
                                 <div style="font-size:10.5px; font-weight:800; color:#64748b; text-transform:uppercase;">No. Pesanan &amp; Resi</div>
                                 <div style="font-size:13.5px; font-weight:900; color:#0284c7; font-family:monospace;" id="bento_no_pesanan">-</div>
                                 <div style="font-size:11px; color:#475569;" id="bento_nomor_resi">Resi: -</div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6 col-sm-6 col-xs-6">
                        <div class="bento-card" style="padding:12px 15px; margin-bottom:12px;">
                           <div style="display:flex; align-items:center; gap:12px;">
                              <div class="bento-icon-wrapper" style="background:#ffedd5; color:#ea580c; width:34px; height:34px; border-radius:8px; margin-bottom:0;">
                                 <i class="fa fa-truck"></i>
                              </div>
                              <div>
                                 <div style="font-size:10.5px; font-weight:800; color:#64748b; text-transform:uppercase;">Opsi Pengiriman</div>
                                 <div style="font-size:13px; font-weight:800; color:#0f172a;" id="bento_courier">-</div>
                                 <div style="font-size:11px; color:#64748b;" id="bento_waktu_bayar">-</div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6 col-sm-6 col-xs-6">
                        <div class="bento-card" style="padding:12px 15px; margin-bottom:12px;">
                           <div style="display:flex; align-items:center; gap:12px;">
                              <div class="bento-icon-wrapper" style="background:#e0e7ff; color:#4f46e5; width:34px; height:34px; border-radius:8px; margin-bottom:0;">
                                 <i class="fa fa-cubes"></i>
                              </div>
                              <div>
                                 <div style="font-size:10.5px; font-weight:800; color:#64748b; text-transform:uppercase;">Total Item Qty</div>
                                 <div style="font-size:18px; font-weight:900; color:#0f172a;" id="bento_total_qty">0 <small style="font-size:11px; font-weight:600; color:#64748b;">pcs</small></div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6 col-sm-6 col-xs-6">
                        <div class="bento-card" style="padding:12px 15px; margin-bottom:12px;">
                           <div style="display:flex; align-items:center; gap:12px;">
                              <div class="bento-icon-wrapper" style="background:#dcfce7; color:#16a34a; width:34px; height:34px; border-radius:8px; margin-bottom:0;">
                                 <i class="fa fa-money"></i>
                              </div>
                              <div>
                                 <div style="font-size:10.5px; font-weight:800; color:#64748b; text-transform:uppercase;">Total Nilai Pesanan</div>
                                 <div style="font-size:17px; font-weight:900; color:#059669;" id="bento_total_price">Rp 0</div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- 3. PRODUCT ITEMS BREAKDOWN TABLE -->
                  <div style="margin-top:5px; margin-bottom:15px;">
                     <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <strong style="font-size:13.5px; color:#0f172a; white-space:nowrap;"><i class="fa fa-cubes text-info"></i> Breakdown Produk Master ERP</strong>
                        <span style="font-size:11.5px; color:#64748b;" id="breakdown_count_label">Item terdaftar</span>
                     </div>
                     
                     <div class="table-responsive" style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.02);">
                        <table class="table table-hover table-striped" style="font-size:12px; margin-bottom:0; background:#fff;">
                           <thead>
                              <tr style="background:#0f172a; color:#fff; font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">
                                 <th width="35" class="text-center">#</th>
                                 <th>Nama Produk Master ERP</th>
                                 <th width="110" class="text-center">SKU Induk</th>
                                 <th width="100" class="text-center">Variasi</th>
                                 <th width="105" class="text-right">Harga</th>
                                 <th width="50" class="text-center">Qty</th>
                                 <th width="115" class="text-right">Subtotal</th>
                              </tr>
                           </thead>
                           <tbody id="tbody_breakdown_items">
                           </tbody>
                        </table>
                     </div>
                  </div>

               </div>

               <!-- RIGHT COLUMN (COL-MD-5): STICKY PDF CANVAS & LIVE THERMAL PREVIEW -->
               <div class="col-md-5 col-sm-12">
                  <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:14px; padding:16px; text-align:center; position:sticky; top:15px;" id="pdf_thumbnail_container">
                     
                     <!-- Header & View Switcher Tabs -->
                     <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:8px;">
                        <div class="btn-group btn-group-xs" role="group" id="preview_type_tabs">
                           <button type="button" class="btn btn-default active" id="btn_toggle_pdf_view" style="font-weight:700; border-radius:6px 0 0 6px;"><i class="fa fa-file-pdf-o text-danger"></i> PDF Asli</button>
                           <button type="button" class="btn btn-default" id="btn_toggle_thermal_view" style="font-weight:700; border-radius:0 6px 6px 0;"><i class="fa fa-print text-success"></i> Label Unmasked ERP</button>
                        </div>

                        <div id="pdf_page_nav_controls" style="display:none;">
                           <button type="button" class="btn btn-xs btn-default flat" id="btn_pdf_prev" style="border-radius:4px; font-weight:700;"><i class="fa fa-chevron-left"></i> Prev</button>
                           <span id="pdf_page_info_text" style="font-weight:700; font-size:11px; margin:0 6px; color:#0284c7;">Halaman 1/1</span>
                           <button type="button" class="btn btn-xs btn-default flat" id="btn_pdf_next" style="border-radius:4px; font-weight:700;">Next <i class="fa fa-chevron-right"></i></button>
                        </div>
                     </div>
                     
                     <div id="pdf_preview_wrapper">
                        <i class="fa fa-file-pdf-o fa-3x text-muted" style="margin-top:20px; color:#cbd5e1;"></i>
                        <div style="color:#94a3b8; font-size:12px; margin-top:8px; font-weight:600;">Belum ada label thermal yang diunggah.</div>
                     </div>

                     <!-- 1. TOP: PDF CANVAS ORIGINAL DOCUMENT -->
                     <div id="wrapper_pdf_canvas" style="display:block;">
                        <div style="font-size:11px; font-weight:700; color:#475569; margin-bottom:6px; text-align:left;"><i class="fa fa-file-pdf-o text-danger"></i> Render Halaman Dokumen PDF Asli:</div>
                        <canvas id="pdf_thumbnail_canvas" style="display:none; max-width:100%; height:auto; border-radius:8px; border:2px solid #cbd5e1; box-shadow:0 6px 20px rgba(0,0,0,0.08); margin:0 auto 10px auto; background:#ffffff;"></canvas>
                     </div>

                     <!-- 2. BOTTOM: GENERATED UNMASKED THERMAL LABEL (ERP SYNTHESIZED) -->
                     <div id="wrapper_thermal_label" style="display:block; border-top:1px dashed #cbd5e1; padding-top:12px; margin-top:12px;">
                        <div style="font-size:11px; font-weight:700; color:#166534; margin-bottom:6px; text-align:left;"><i class="fa fa-print text-success"></i> Label Thermal Hasil Unmasking &amp; Data ERP:</div>
                        <div id="live_thermal_label_wrapper" style="display:none;"></div>
                     </div>

                  </div>
               </div>

            </div>
         </div>

      </div>

      <!-- Box Footer -->
      <div class="box-footer ws-footer-flex" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:16px 22px; display:flex; justify-content:space-between; align-items:center;">
         <a href="<?= site_url('administrator/penjualan_marketplace'); ?>" class="btn btn-default flat" style="border-radius:6px; font-weight:700;"><i class="fa fa-arrow-left"></i> Kembali ke Penjualan Marketplace</a>
         <a href="<?= site_url('administrator/penjualan_marketplace'); ?>" class="btn btn-primary flat" style="border-radius:6px; font-weight:700; background:#0f172a; border-color:#0f172a;"><i class="fa fa-check-circle"></i> Selesai &amp; Kembali ke Daftar</a>
      </div>

   </div>
</section>

<!-- Audio Beep Context & Client Engine Script -->
<script>
if (typeof pdfjsLib !== 'undefined') {
   try {
      var workerCode = "importScripts('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js');";
      var blob = new Blob([workerCode], { type: 'text/javascript' });
      pdfjsLib.GlobalWorkerOptions.workerSrc = URL.createObjectURL(blob);
   } catch(e) {
      pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
   }
}

function playBeepSound(type) {
   try {
      var ctx = new (window.AudioContext || window.webkitAudioContext)();
      var osc = ctx.createOscillator();
      var gain = ctx.createGain();
      osc.connect(gain);
      gain.connect(ctx.destination);
      if (type === 'success') {
         osc.frequency.value = 1046.5;
         gain.gain.setValueAtTime(0.3, ctx.currentTime);
         osc.start();
         osc.stop(ctx.currentTime + 0.15);
      } else {
         osc.frequency.value = 220;
         gain.gain.setValueAtTime(0.3, ctx.currentTime);
         osc.start();
         osc.stop(ctx.currentTime + 0.3);
      }
   } catch(e) {}
}

$(document).ready(function(){
   var html5QrCode = null;

   // Toggle preview views (PDF Original vs Live Thermal Label)
   $('#btn_toggle_pdf_view').on('click', function(){
      $(this).addClass('active btn-primary').removeClass('btn-default');
      $('#btn_toggle_thermal_view').removeClass('active btn-primary').addClass('btn-default');
      $('#wrapper_pdf_canvas').show();
      $('#wrapper_thermal_label').hide();
   });

   $('#btn_toggle_thermal_view').on('click', function(){
      $(this).addClass('active btn-primary').removeClass('btn-default');
      $('#btn_toggle_pdf_view').removeClass('active btn-primary').addClass('btn-default');
      $('#wrapper_pdf_canvas').hide();
      $('#wrapper_thermal_label').show();
   });

   // Drag & Drop handlers for dropzone
   var dropzone = $('.dropzone-box');
   dropzone.on('dragover dragenter', function(e){
      e.preventDefault();
      e.stopPropagation();
      $(this).css({'border-color': '#0284c7', 'background': '#f0f9ff'});
   });
   dropzone.on('dragleave dragend drop', function(e){
      e.preventDefault();
      e.stopPropagation();
      $(this).css({'border-color': '#cbd5e1', 'background': '#f8fafc'});
   });
   dropzone.on('drop', function(e){
      var files = e.originalEvent.dataTransfer.files;
      if (files && files.length > 0) {
         document.getElementById('file_pdf_input').files = files;
         $('#file_pdf_input').trigger('change');
      }
   });

   $('#dropzone_box_container').click(function(e){
      if (!$(e.target).is('#file_pdf_input') && !$(e.target).closest('label').length) {
         $('#file_pdf_input').click();
      }
   });

   // Handle PDF / Image File Selection (Single, Multi-Page PDF, or Multi-File Bulk Upload)
   $('#file_pdf_input').change(function(e){
      var files = e.target.files;
      if (!files || files.length === 0) return;

      if (files.length === 1) {
         var file = files[0];
         var fileExt = file.name.split('.').pop().toLowerCase();

         if (fileExt === 'pdf') {
            $('#scan_resi_feedback').html('<div class="alert alert-info" style="border-radius:8px; font-weight:700;"><i class="fa fa-spinner fa-spin"></i> Membaca dokumen PDF label thermal &amp; menghitung jumlah halaman resi...</div>');

            processPDFFile(file, function(pagesData, totalPages){
               processMultiPagePDF(file, pagesData, totalPages);
            });
         } else {
            processBatchFiles(files);
         }
      } else {
         // Batch Bulk Multi-File Processing & Auto-Save to DB
         processBatchFiles(files);
      }
   });

   function runTesseractOCR(imageSource, callback) {
      if (typeof Tesseract === 'undefined') {
         console.warn('Tesseract.js library not available');
         if (callback) callback([]);
         return;
      }
      Tesseract.recognize(
         imageSource,
         'eng',
         {
            logger: function(m) {
               if (m.status === 'recognizing text') {
                  var pct = Math.round((m.progress || 0) * 100);
                  $('#bulk_progress_title').html('<i class="fa fa-eye text-info"></i> Membaca OCR Tesseract Label Thermal... ' + pct + '%');
               }
            }
         }
      ).then(function(result) {
         var lines = [];
         if (result && result.data && result.data.text) {
            var rawLines = result.data.text.split(/\r?\n/);
            rawLines.forEach(function(l) {
               var trimmed = l.trim();
               if (trimmed.length > 0) {
                  lines.push({ str: trimmed });
               }
            });
         }
         console.log('Tesseract OCR extracted ' + lines.length + ' lines');
         if (callback) callback(lines);
      }).catch(function(err) {
         console.error('Tesseract OCR error:', err);
         if (callback) callback([]);
      });
   }

   function processMultiPagePDF(file, pagesData, totalPages) {
      $('#bulk_progress_container').show();
      $('#tbody_bulk_summary').empty();
      var processed = 0;
      var totalSuccess = 0;

      $('#stat_total_processed').text(totalPages);
      $('#stat_total_success').text(0);
      $('#stat_total_failed').text(0);

      $('#bulk_progress_counter').text('0 / ' + totalPages + ' Resi (Halaman)');
      $('#bulk_progress_bar').css('width', '0%');
      $('#bulk_progress_title').html('<i class="fa fa-file-pdf-o text-danger"></i> Memproses Multi-Page PDF Resi: <strong>' + file.name + '</strong> (' + totalPages + ' Halaman Resi)...');
      $('#scan_resi_feedback').html('<div class="alert alert-info" style="border-radius:8px; font-weight:700;"><i class="fa fa-spinner fa-spin"></i> Dokumen PDF berisi ' + totalPages + ' halaman resi. Memproses &amp; otomatis meng-input data ke database...</div>');

      function ocrPageAndSend(index) {
         if (index >= totalPages) {
            playBeepSound('success');
            $('#bulk_progress_bar').removeClass('active progress-bar-striped');
            $('#bulk_progress_title').html('<i class="fa fa-check-circle text-success"></i> PDF Multi-Resi (' + totalPages + ' Halaman) &amp; Auto-Input Database Selesai!');
            $('#scan_resi_feedback').html('<div class="alert alert-success" style="border-radius:8px; font-weight:700;"><i class="fa fa-check-circle"></i> Selesai! Total ' + totalSuccess + ' dari ' + totalPages + ' resi dalam PDF berhasil di-unmask &amp; otomatis tersimpan ke database.</div>');
            return;
         }

         var pageNum = index + 1;
         var labelName = file.name + ' [Hal ' + pageNum + ']';

         $('#bulk_progress_title').html('<i class="fa fa-eye text-info"></i> OCR Membaca Label Resi Halaman ' + pageNum + ' / ' + totalPages + '...');

         // Render PDF page to offscreen canvas for OCR
         if (currentPdfDocument) {
            currentPdfDocument.getPage(pageNum).then(function(page) {
               var viewport = page.getViewport({ scale: 2.0 });
               var offCanvas = document.createElement('canvas');
               offCanvas.width = viewport.width;
               offCanvas.height = viewport.height;
               var offCtx = offCanvas.getContext('2d');
               offCtx.fillStyle = '#ffffff';
               offCtx.fillRect(0, 0, offCanvas.width, offCanvas.height);

               page.render({ canvasContext: offCtx, viewport: viewport }).promise.then(function() {
                  // Pre-scan canvas for barcode if supported by browser
                  var detectBarcodePromise = Promise.resolve([]);
                  if ('BarcodeDetector' in window) {
                     try {
                        var detector = new BarcodeDetector({ formats: ['code_128', 'code_39', 'qr_code', 'ean_13'] });
                        detectBarcodePromise = detector.detect(offCanvas).catch(function(){ return []; });
                     } catch(e) {}
                  }

                  detectBarcodePromise.then(function(barcodes){
                     var barcodeItems = [];
                     if (barcodes && barcodes.length > 0) {
                        barcodes.forEach(function(b){
                           if (b.rawValue && b.rawValue.length >= 6) {
                              barcodeItems.push({ str: 'BARCODE: ' + b.rawValue });
                              barcodeItems.push({ str: b.rawValue });
                           }
                        });
                     }

                     // Run Tesseract.js OCR on rendered canvas
                     runTesseractOCR(offCanvas, function(ocrItems) {
                        var extractedItems = barcodeItems.concat(ocrItems || []);
                        doSendPage(labelName, extractedItems, index, pageNum);
                     });
                  });
               }).catch(function() {
                  doSendPage(labelName, [], index, pageNum);
               });
            }).catch(function() {
               doSendPage(labelName, [], index, pageNum);
            });
         } else {
            // No PDF document reference (timeout fallback)
            doSendPage(labelName, [], index, pageNum);
         }
      }

      window.currentLoadedPdfFile = file;

      function doSendPage(labelName, extractedItems, index, pageNum) {
         sendScanAndSaveAJAXPayload(labelName, extractedItems, file, index, index, function(resData){
            processed++;
            totalSuccess++;
            updateProgressText(processed, totalPages, 'Halaman');
            $('#stat_total_success').text(totalSuccess);
            $('#stat_total_failed').text(processed - totalSuccess);
            appendSuccessRow(index, labelName, resData);
            if (index === 0 || index === (currentPdfPage - 1)) {
               renderResults(resData);
               renderLiveThermalLabel(resData);
               $('#tbody_bulk_summary tr').removeClass('active-scanned-row');
               $('#tbody_bulk_summary tr button[data-page-idx="' + index + '"]').closest('tr').addClass('active-scanned-row');
            }
            setTimeout(function(){ ocrPageAndSend(index + 1); }, 30);
         }, function(errMsg){
            processed++;
            updateProgressText(processed, totalPages, 'Halaman');
            $('#stat_total_failed').text(processed - totalSuccess);
            appendFailedRow(index, labelName, errMsg);
            setTimeout(function(){ ocrPageAndSend(index + 1); }, 30);
         });
      }

      ocrPageAndSend(0);
   }

   function processBatchFiles(files) {
      $('#bulk_progress_container').show();
      $('#tbody_bulk_summary').empty();
      var fileList = Array.from(files);
      var totalFiles = fileList.length;
      var processedCount = 0;
      var successCount = 0;

      $('#stat_total_processed').text(totalFiles);
      $('#stat_total_success').text(0);
      $('#stat_total_failed').text(0);

      $('#bulk_progress_counter').text('0 / ' + totalFiles + ' File');
      $('#bulk_progress_bar').css('width', '0%');
      $('#bulk_progress_title').html('<i class="fa fa-spinner fa-spin"></i> Memproses Batch Upload Resi Massal (' + totalFiles + ' File)...');
      $('#scan_resi_feedback').html('<div class="alert alert-info" style="border-radius:8px; font-weight:700;"><i class="fa fa-spinner fa-spin"></i> Memproses ' + totalFiles + ' file resi secara simultan &amp; otomatis meng-input data ke database...</div>');

      function runQueueIndex(index) {
         if (index >= totalFiles) {
            playBeepSound('success');
            $('#bulk_progress_bar').removeClass('active progress-bar-striped');
            $('#bulk_progress_title').html('<i class="fa fa-check-circle text-success"></i> Batch Upload &amp; Auto-Input Database Selesai!');
            $('#scan_resi_feedback').html('<div class="alert alert-success" style="border-radius:8px; font-weight:700;"><i class="fa fa-check-circle"></i> Selesai! Total ' + successCount + ' dari ' + totalFiles + ' file resi berhasil di-unmask &amp; otomatis tersimpan ke database.</div>');
            return;
         }

         var file = fileList[index];
         var fileExt = file.name.split('.').pop().toLowerCase();
         var labelName = file.name;

         if (fileExt === 'pdf') {
            processPDFFile(file, function(pagesData, totalPages){
               if (totalPages > 1) {
                  var pSubIdx = 0;
                  function processPdfSubPage(pIdx) {
                     if (pIdx >= totalPages) {
                        processedCount++;
                        updateProgressText(processedCount, totalFiles, 'File');
                        setTimeout(function(){ runQueueIndex(index + 1); }, 60);
                        return;
                     }
                     var subLabel = file.name + ' [Hal ' + (pIdx + 1) + ']';
                     sendScanAndSaveAJAXPayload(subLabel, [], file, index, pIdx, function(resData){
                        successCount++;
                        $('#stat_total_success').text(successCount);
                        $('#stat_total_failed').text(processedCount - successCount);
                        appendSuccessRow(index + pIdx, subLabel, resData);
                        if (index === 0 && pIdx === 0) {
                           renderResults(resData);
                           renderLiveThermalLabel(resData);
                           $('#tbody_bulk_summary tr:first').addClass('active-scanned-row');
                        }
                        setTimeout(function(){ processPdfSubPage(pIdx + 1); }, 60);
                     }, function(errMsg){
                        appendFailedRow(index + pIdx, subLabel, errMsg);
                        $('#stat_total_failed').text(processedCount - successCount);
                        setTimeout(function(){ processPdfSubPage(pIdx + 1); }, 60);
                     });
                  }
                  processPdfSubPage(0);
               } else {
                  sendScanAndSaveAJAXPayload(labelName, [], file, index, 0, function(resData){
                     processedCount++;
                     successCount++;
                     updateProgressText(processedCount, totalFiles, 'File');
                     $('#stat_total_success').text(successCount);
                     $('#stat_total_failed').text(processedCount - successCount);
                     appendSuccessRow(index, labelName, resData);
                     if (index === 0) {
                        renderResults(resData);
                        renderLiveThermalLabel(resData);
                        $('#tbody_bulk_summary tr:first').addClass('active-scanned-row');
                     }
                     setTimeout(function(){ runQueueIndex(index + 1); }, 60);
                  }, function(errMsg){
                     appendFailedRow(index, labelName, errMsg);
                     processedCount++;
                     updateProgressText(processedCount, totalFiles, 'File');
                     $('#stat_total_failed').text(processedCount - successCount);
                     setTimeout(function(){ runQueueIndex(index + 1); }, 60);
                  });
               }
            });
         } else {
            // Image file (.jpg, .png, .webp): Run Tesseract OCR on receipt image
            runTesseractOCR(file, function(ocrItems){
               if (!ocrItems || ocrItems.length === 0) {
                  ocrItems = [{ str: file.name }];
               }
               sendScanAndSaveAJAXPayload(labelName, ocrItems, file, index, 0, function(resData){
                  processedCount++;
                  successCount++;
                  updateProgressText(processedCount, totalFiles, 'File');
                  $('#stat_total_success').text(successCount);
                  $('#stat_total_failed').text(processedCount - successCount);
                  appendSuccessRow(index, labelName, resData);
                  if (index === 0) {
                     renderResults(resData);
                     renderLiveThermalLabel(resData);
                     $('#tbody_bulk_summary tr:first').addClass('active-scanned-row');
                  }
                  setTimeout(function(){ runQueueIndex(index + 1); }, 60);
               }, function(errMsg){
                  appendFailedRow(index, labelName, errMsg);
                  processedCount++;
                  updateProgressText(processedCount, totalFiles, 'File');
                  $('#stat_total_failed').text(processedCount - successCount);
                  setTimeout(function(){ runQueueIndex(index + 1); }, 60);
               });
            });
         }
      }

      runQueueIndex(0);
   }

   function updateProgressText(current, total, unitLabel) {
      var pct = Math.round((current / total) * 100);
      $('#bulk_progress_bar').css('width', pct + '%');
      $('#bulk_progress_counter').text(current + ' / ' + total + ' ' + (unitLabel || 'File'));
   }

   function cleanRecipientName(name) {
      if (!name) return '-';
      return name.replace(/\s+(No\s*Hp|No\s*Telp|Notelp|Telp|Telepon|Phone)$/i, '').trim();
   }

   function sendScanAndSaveAJAXPayload(resiLabelName, extractedItems, fileObj, index, pageIndex, onSuccess, onError, retryCount) {
      retryCount = retryCount || 0;
      var formData = new FormData();
      formData.append('nomor_resi', resiLabelName);
      formData.append('extracted_text_items', JSON.stringify(extractedItems || []));
      formData.append('page_index', pageIndex || 0);
      
      if (fileObj) {
         formData.append('file_pdf', fileObj);
      }

      $.ajax({
         url: '<?= site_url("administrator/penjualan_marketplace/scan_resi_ajax"); ?>',
         type: 'POST',
         data: formData,
         processData: false,
         contentType: false,
         dataType: 'json',
         timeout: 25000,
         success: function(res) {
            if (res.status === 'success') {
               var cleanName = cleanRecipientName(res.data.unmasked_name);
               res.data.unmasked_name = cleanName;
               if (onSuccess) onSuccess(res.data);
            } else {
               if (onError) onError(res.message);
            }
         },
         error: function(xhr, status, error) {
            if (retryCount < 3) {
               setTimeout(function(){
                  sendScanAndSaveAJAXPayload(resiLabelName, extractedItems, fileObj, index, pageIndex, onSuccess, onError, retryCount + 1);
               }, 350);
            } else {
               if (onError) onError('Gagal koneksi ke server');
            }
         }
      });
   }

   if (!window.bulkSummaryResults) {
      window.bulkSummaryResults = {};
   }

   function renderResults(data) {
      if (!data) return;
      
      // Make scan results container visible
      $('#scan_results_container').show();

      // 1. Unmasked Recipient Inputs
      $('#unmask_target_id').val(data.id || '');
      $('#unmask_input_name').val(cleanRecipientName(data.unmasked_name));
      $('#unmask_input_phone').val(data.unmasked_phone || '-');
      $('#unmask_input_address').val(data.unmasked_address || '-');

      // 2. Bento Stat Cards
      $('#bento_no_pesanan').text(data.no_pesanan || '-');
      $('#bento_nomor_resi').text('Resi: ' + (data.nomor_resi || '-'));
      $('#bento_courier').text(data.opsi_pengiriman || 'SPX Standard');
      $('#bento_waktu_bayar').text(data.waktu_pembayaran ? ('Bayar: ' + data.waktu_pembayaran) : '-');
      $('#bento_total_qty').html((data.total_qty || 1) + ' <small style="font-size:11px; font-weight:600; color:#64748b;">pcs</small>');
      
      var priceFormatted = new Intl.NumberFormat('id-ID').format(data.total_price || 0);
      $('#bento_total_price').text('Rp ' + priceFormatted);

      // 3. Product Items Breakdown Table
      var itemsHtml = '';
      if (data.items && data.items.length > 0) {
         data.items.forEach(function(item, i){
            var itemQty = parseInt(item.qty) || 1;
            var itemPrice = parseFloat(item.harga) || (parseFloat(item.subtotal) / itemQty) || 0;
            var itemSubtotal = parseFloat(item.subtotal) || (itemPrice * itemQty) || 0;
            
            itemsHtml += '<tr style="transition:background 0.15s ease;">' +
               '<td class="text-center"><strong>' + (i + 1) + '</strong></td>' +
               '<td><strong style="color:#0f172a; font-size:12.5px;">' + (item.nama_produk || '-') + '</strong></td>' +
               '<td class="text-center"><span class="label label-default" style="font-family:monospace; font-size:11px; background:#f1f5f9; color:#334155; border:1px solid #cbd5e1;">' + (item.sku_induk || item.sku || '-') + '</span></td>' +
               '<td class="text-center"><span class="label label-info" style="font-size:11px; background:#e0f2fe; color:#0369a1; border-radius:10px;">' + (item.variasi || item.opsi_variasi || '-') + '</span></td>' +
               '<td class="text-right" style="font-family:monospace;">Rp ' + new Intl.NumberFormat('id-ID').format(itemPrice) + '</td>' +
               '<td class="text-center"><span class="label label-primary" style="font-size:11.5px; font-weight:700; background:#0f172a;">' + itemQty + '</span></td>' +
               '<td class="text-right"><strong style="color:#059669; font-family:monospace;">Rp ' + new Intl.NumberFormat('id-ID').format(itemSubtotal) + '</strong></td>' +
            '</tr>';
         });
         $('#breakdown_count_label').text(data.items.length + ' produk master ERP');
      } else {
         itemsHtml = '<tr><td colspan="7" class="text-center text-muted" style="padding:15px;">Tidak ada rincian produk master ERP.</td></tr>';
         $('#breakdown_count_label').text('0 produk master ERP');
      }
      $('#tbody_breakdown_items').html(itemsHtml);
   }

   function appendSuccessRow(idx, filename, data) {
      if (!window.bulkSummaryResults) window.bulkSummaryResults = {};
      window.bulkSummaryResults[idx] = data;

      var cleanName = cleanRecipientName(data.unmasked_name);
      var courier = data.opsi_pengiriman || 'SPX Standard';
      var courierLower = courier.toLowerCase();
      var courierBadge = '<span class="label" style="background:#e2e8f0; color:#334155; font-weight:700; border-radius:10px; padding:4px 8px;"><i class="fa fa-truck"></i> ' + courier + '</span>';
      
      if (courierLower.indexOf('spx') !== -1 || courierLower.indexOf('shopee') !== -1) {
         courierBadge = '<span class="label" style="background:#ffedd5; color:#c2410c; font-weight:700; border-radius:10px; padding:4px 8px;"><i class="fa fa-shopping-bag"></i> ' + courier + '</span>';
      } else if (courierLower.indexOf('jne') !== -1) {
         courierBadge = '<span class="label" style="background:#e0f2fe; color:#0369a1; font-weight:700; border-radius:10px; padding:4px 8px;"><i class="fa fa-plane"></i> ' + courier + '</span>';
      } else if (courierLower.indexOf('j&t') !== -1 || courierLower.indexOf('jnt') !== -1) {
         courierBadge = '<span class="label" style="background:#fef2f2; color:#b91c1c; font-weight:700; border-radius:10px; padding:4px 8px;"><i class="fa fa-truck"></i> ' + courier + '</span>';
      } else if (courierLower.indexOf('sicepat') !== -1) {
         courierBadge = '<span class="label" style="background:#fef2f2; color:#dc2626; font-weight:700; border-radius:10px; padding:4px 8px;"><i class="fa fa-bolt"></i> ' + courier + '</span>';
      }

      var totalQty = data.total_qty || (data.items ? data.items.reduce(function(a,b){ return a + (parseInt(b.qty)||1); }, 0) : 1);
      var totalPrice = data.total_price || (data.items ? data.items.reduce(function(a,b){ return a + (parseFloat(b.subtotal)||0); }, 0) : 0);

      var row = '<tr style="transition:background 0.15s ease;">' +
         '<td class="text-center"><strong>' + (idx + 1) + '</strong></td>' +
         '<td><strong style="color:#0f172a;"><i class="fa fa-file-pdf-o text-danger"></i> ' + filename + '</strong></td>' +
         '<td><span class="label label-primary" style="font-family:monospace; font-size:11px; padding:4px 8px; background:#0284c7;">' + (data.nomor_resi || '-') + '</span></td>' +
         '<td><span class="label label-default" style="font-family:monospace; font-size:11px; padding:4px 8px;">' + (data.no_pesanan || '-') + '</span></td>' +
         '<td>' + courierBadge + '</td>' +
         '<td><strong style="color:#0284c7; font-size:12.5px;">' + cleanName + '</strong></td>' +
         '<td><i class="fa fa-phone text-muted"></i> ' + (data.unmasked_phone || '-') + '</td>' +
         '<td class="text-center"><span class="label label-info" style="font-size:11.5px; font-weight:700; background:#0f172a; padding:4px 8px;">' + totalQty + ' pcs</span></td>' +
         '<td class="text-right"><strong style="color:#059669; font-size:12px;">Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice) + '</strong></td>' +
         '<td><span class="label label-success" style="font-weight:700; background:#10b981; padding:4px 8px; border-radius:10px;"><i class="fa fa-check-circle"></i> Auto-Saved DB</span></td>' +
         '<td class="text-center"><button type="button" class="btn btn-xs btn-info flat btn-view-res-detail" data-row-idx="' + idx + '" data-page-idx="' + idx + '" style="border-radius:6px; font-weight:700; padding:3px 10px; background:#0284c7; border-color:#0284c7;"><i class="fa fa-eye"></i> Detail</button></td>' +
         '</tr>';
      $('#tbody_bulk_summary').append(row);
   }

   $(document).on('click', '.btn-view-res-detail', function(){
      $('#tbody_bulk_summary tr').removeClass('active-scanned-row');
      $(this).closest('tr').addClass('active-scanned-row');

      var idx = parseInt($(this).attr('data-row-idx'));
      var data = (window.bulkSummaryResults && window.bulkSummaryResults[idx]) ? window.bulkSummaryResults[idx] : $(this).data('res');
      if (typeof data === 'string') {
         try { data = JSON.parse(data); } catch(e){}
      }
      
      $('#scan_results_container').show();
      if (data) {
         renderResults(data);
         renderLiveThermalLabel(data);
      }

      // Navigate canvas to the correct PDF page for this row
      var pageIdx = parseInt($(this).data('page-idx'));
      if (!isNaN(pageIdx) && currentPdfDocument) {
         currentPdfPage = pageIdx + 1; // page numbers are 1-indexed
         renderCanvasPage(currentPdfPage);
      }

      if ($('#scan_results_container').length > 0) {
         $('html, body').stop().animate({ scrollTop: $('#scan_results_container').offset().top - 20 }, 300);
      }
   });

   function appendFailedRow(idx, filename, errMsg) {
      var row = '<tr>' +
         '<td class="text-center">' + (idx + 1) + '</td>' +
         '<td><strong style="color:#64748b;">' + filename + '</strong></td>' +
         '<td>-</td>' +
         '<td>-</td>' +
         '<td>-</td>' +
         '<td>-</td>' +
         '<td>-</td>' +
         '<td class="text-center">-</td>' +
         '<td class="text-right">-</td>' +
         '<td><span class="label label-danger" style="font-weight:700;"><i class="fa fa-times"></i> ' + errMsg + '</span></td>' +
         '<td class="text-center">-</td>' +
         '</tr>';
      $('#tbody_bulk_summary').append(row);
   }

   var currentPdfDocument = null;
   var currentPdfPage = 1;
   var totalPdfPages = 1;

   var isRenderingCanvas = false;

   function renderCanvasPage(pageNumber) {
      if (!currentPdfDocument) {
         console.log('renderCanvasPage: No PDF document loaded');
         return;
      }
      if (isRenderingCanvas) {
         console.log('renderCanvasPage: Already rendering, queuing page ' + pageNumber);
         setTimeout(function(){ renderCanvasPage(pageNumber); }, 300);
         return;
      }
      isRenderingCanvas = true;

      currentPdfDocument.getPage(pageNumber).then(function(page) {
         var containerWidth = $('#pdf_thumbnail_container').width() - 40;
         if (containerWidth < 300) containerWidth = 320;
         var unscaledViewport = page.getViewport({ scale: 1.0 });
         var scale = containerWidth / unscaledViewport.width;
         if (scale > 2.0) scale = 2.0;
         if (scale < 0.5) scale = 0.5;

         var viewport = page.getViewport({ scale: scale });
         var canvas = document.getElementById('pdf_thumbnail_canvas');
         if (!canvas) {
            console.log('renderCanvasPage: Canvas element not found!');
            isRenderingCanvas = false;
            return;
         }
         var context = canvas.getContext('2d');
         canvas.width = viewport.width;
         canvas.height = viewport.height;

         // Fill canvas with solid white background before rendering PDF content
         context.fillStyle = '#ffffff';
         context.fillRect(0, 0, canvas.width, canvas.height);

         // Make canvas visible immediately (so user sees white before content loads)
         $('#pdf_preview_wrapper').hide();
         $(canvas).css({'display': 'block', 'visibility': 'visible', 'opacity': '1'}).show();
         $('#pdf_page_nav_controls').show();
         $('#pdf_page_info_text').text('Halaman ' + pageNumber + ' dari ' + totalPdfPages);

         var renderContext = {
            canvasContext: context,
            viewport: viewport
         };

         var renderTask = page.render(renderContext);
         renderTask.promise.then(function(){
            console.log('PDF Canvas rendered page ' + pageNumber + ' successfully');
            isRenderingCanvas = false;
         }).catch(function(err){
            console.log('PDF Render Exception on page ' + pageNumber + ':', err);
            isRenderingCanvas = false;
         });
      }).catch(function(err){
         console.log('PDF getPage error for page ' + pageNumber + ':', err);
         isRenderingCanvas = false;
      });
   }

   function renderLiveThermalLabel(data) {
      if (!data) return;
      var courier = data.opsi_pengiriman || 'SPX Standard';
      var cleanName = cleanRecipientName(data.unmasked_name);
      var phone = data.unmasked_phone || '-';
      var address = data.unmasked_address || '-';
      var resiNo = data.nomor_resi || '-';
      var orderNo = data.no_pesanan || '-';
      var qty = data.total_qty || 1;

      // Generate a safe unique ID for the barcode SVG element
      var barcodeId = 'live_barcode_' + Date.now() + '_' + Math.floor(Math.random() * 10000);

      // Detect courier type for header styling
      var courierLower = courier.toLowerCase();
      var courierColor = '#0284c7';
      var platformLabel = 'Shopee';
      var platformIcon = 'fa-shopping-bag';
      var platformColor = '#ee4d2d';
      if (courierLower.indexOf('jne') !== -1) { courierColor = '#0369a1'; }
      else if (courierLower.indexOf('j&t') !== -1 || courierLower.indexOf('jnt') !== -1) { courierColor = '#b91c1c'; }
      else if (courierLower.indexOf('sicepat') !== -1) { courierColor = '#dc2626'; }

      // Build items list HTML if items data available
      var itemsHtml = '';
      if (data.items && data.items.length > 0) {
         itemsHtml = '<div style="border-top:2px solid #000; padding:6px 0; font-size:10px;">' +
            '<strong style="font-size:11px;">📦 Daftar Barang (' + data.items.length + ' produk):</strong>' +
            '<table style="width:100%; margin-top:4px; font-size:10px; border-collapse:collapse;">';
         data.items.forEach(function(item, idx){
            itemsHtml += '<tr style="border-bottom:1px dashed #ccc;">' +
               '<td style="padding:2px 0; width:16px; color:#64748b;">' + (idx+1) + '.</td>' +
               '<td style="padding:2px 4px;">' + item.master_product_name + '</td>' +
               '<td style="padding:2px 0; text-align:right; white-space:nowrap; font-weight:700;">' + item.qty + 'x</td>' +
            '</tr>';
         });
         itemsHtml += '</table></div>';
      }

      var html = '<div class="thermal-resi-card" style="width:100%; max-width:380px; margin:0 auto 15px auto; background:#ffffff; border:2px solid #000000; padding:14px; font-family:Arial, sans-serif; text-align:left; color:#000; box-shadow:0 8px 25px rgba(0,0,0,0.12); border-radius:6px;">' +
         '<div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #000; padding-bottom:6px; margin-bottom:6px;">' +
            '<strong style="font-size:16px; color:' + platformColor + ';"><i class="fa ' + platformIcon + '"></i> ' + platformLabel + '</strong>' +
            '<strong style="font-size:18px; font-weight:900;">STANDARD</strong>' +
            '<strong style="font-size:14px; color:' + courierColor + ';">' + courier + '</strong>' +
         '</div>' +
         '<div style="border-bottom:2px solid #000; padding:4px 0; font-size:11px; text-align:center;">' +
            'No. Resi: <strong style="font-size:14px; font-family:monospace; color:#0f172a;">' + resiNo + '</strong>' +
         '</div>' +
         '<div style="padding:8px 0; border-bottom:2px solid #000; text-align:center;">' +
            '<svg id="' + barcodeId + '" style="width:100%; height:55px;"></svg>' +
         '</div>' +
         '<div style="padding:6px 0; border-bottom:2px solid #000; font-size:11px; line-height:1.35;">' +
            '<strong style="font-size:12px; color:#0f172a;">Penerima: ' + cleanName + '</strong><br>' +
            'No. Telepon: <strong>' + phone + '</strong><br>' +
            'Alamat: ' + address +
         '</div>' +
         itemsHtml +
         '<div style="display:flex; justify-content:space-between; padding-top:6px; font-size:11px; border-top:' + (data.items && data.items.length > 0 ? '0' : '0') + 'px solid #000;">' +
            '<div>No. Pesanan: <strong style="font-family:monospace;">' + orderNo + '</strong></div>' +
            '<div>Total Qty: <strong>' + qty + ' pcs</strong></div>' +
         '</div>' +
      '</div>';

      $('#live_thermal_label_wrapper').html(html).show();
      // Don't hide pdf_preview_wrapper — let canvas stay visible

      setTimeout(function(){
         try {
            if (typeof JsBarcode !== 'undefined' && resiNo && resiNo !== '-') {
               JsBarcode('#' + barcodeId, resiNo, {
                  format: 'CODE128',
                  height: 45,
                  displayValue: false,
                  margin: 0
               });
            }
         } catch(e) {
            console.log('JsBarcode error:', e);
         }
      }, 150);
   }

   function renderSinglePdfPageOnTheFly(file, pageNum) {
      if (!currentPdfDocument || !file) return;
      currentPdfDocument.getPage(pageNum).then(function(page) {
         var viewport = page.getViewport({ scale: 2.0 });
         var offCanvas = document.createElement('canvas');
         offCanvas.width = viewport.width;
         offCanvas.height = viewport.height;
         var offCtx = offCanvas.getContext('2d');
         offCtx.fillStyle = '#ffffff';
         offCtx.fillRect(0, 0, offCanvas.width, offCanvas.height);

         page.render({ canvasContext: offCtx, viewport: viewport }).promise.then(function() {
            var detectBarcodePromise = Promise.resolve([]);
            if ('BarcodeDetector' in window) {
               try {
                  var detector = new BarcodeDetector({ formats: ['code_128', 'code_39', 'qr_code', 'ean_13'] });
                  detectBarcodePromise = detector.detect(offCanvas).catch(function(){ return []; });
               } catch(e) {}
            }

            detectBarcodePromise.then(function(barcodes){
               var barcodeItems = [];
               if (barcodes && barcodes.length > 0) {
                  barcodes.forEach(function(b){
                     if (b.rawValue && b.rawValue.length >= 6) {
                        barcodeItems.push({ str: 'BARCODE: ' + b.rawValue });
                        barcodeItems.push({ str: b.rawValue });
                     }
                  });
               }

               runTesseractOCR(offCanvas, function(ocrItems) {
                  var extractedItems = barcodeItems.concat(ocrItems || []);
                  var labelName = file.name + ' [Hal ' + pageNum + ']';
                  sendScanAndSaveAJAXPayload(labelName, extractedItems, file, pageNum - 1, pageNum - 1, function(resData){
                     renderResults(resData);
                     renderLiveThermalLabel(resData);
                     if (!window.bulkSummaryResults) window.bulkSummaryResults = {};
                     window.bulkSummaryResults[pageNum - 1] = resData;
                  });
               });
            });
         });
      });
   }

   function syncDataWithPage(pageNumber) {
      var targetIdx = pageNumber - 1;
      var btn = $('#tbody_bulk_summary button[data-page-idx="' + targetIdx + '"]');
      if (btn.length > 0) {
         $('#tbody_bulk_summary tr').removeClass('active-scanned-row');
         var tr = btn.closest('tr');
         tr.addClass('active-scanned-row');

         var idx = parseInt(btn.attr('data-row-idx'));
         var data = (window.bulkSummaryResults && window.bulkSummaryResults[idx]) ? window.bulkSummaryResults[idx] : btn.data('res');
         if (typeof data === 'string') {
            try { data = JSON.parse(data); } catch(e){}
         }
         if (data) {
            renderResults(data);
            renderLiveThermalLabel(data);
         }

         // Scroll table container so active row is visible
         var container = $('#tbody_bulk_summary').closest('.table-responsive');
         if (container.length > 0 && tr.length > 0) {
            var containerTop = container.scrollTop();
            var trTop = tr.position().top;
            var scrollTop = containerTop + trTop - (container.height() / 2) + (tr.height() / 2);
            container.stop().animate({ scrollTop: scrollTop }, 150);
         }
      } else if (currentPdfDocument && window.currentLoadedPdfFile) {
         renderSinglePdfPageOnTheFly(window.currentLoadedPdfFile, pageNumber);
      }
   }

   $('#btn_pdf_prev').click(function(){
      if (currentPdfPage > 1) {
         currentPdfPage--;
         renderCanvasPage(currentPdfPage);
         syncDataWithPage(currentPdfPage);
      }
   });

   $('#btn_pdf_next').click(function(){
      if (currentPdfPage < totalPdfPages) {
         currentPdfPage++;
         renderCanvasPage(currentPdfPage);
         syncDataWithPage(currentPdfPage);
      }
   });

   // Preview View Toggle Tabs (PDF Asli vs Label Unmasked ERP)
   $('#btn_toggle_pdf_view').click(function(){
      $(this).addClass('btn-primary active').removeClass('btn-default');
      $('#btn_toggle_thermal_view').removeClass('btn-success btn-primary active').addClass('btn-default');
      $('#wrapper_pdf_canvas').stop().hide().fadeIn(250);
      $('#wrapper_thermal_label').stop().fadeOut(150);
   });

   $('#btn_toggle_thermal_view').click(function(){
      $(this).addClass('btn-success active').removeClass('btn-default');
      $('#btn_toggle_pdf_view').removeClass('btn-primary active').addClass('btn-default');
      $('#wrapper_thermal_label').stop().hide().fadeIn(250);
      $('#wrapper_pdf_canvas').stop().fadeOut(150);
   });

   function scanBarcodeFromCanvas(canvas, callback) {
      if ('BarcodeDetector' in window) {
         try {
            var detector = new BarcodeDetector({ formats: ['code_128', 'code_39', 'qr_code', 'ean_13'] });
            detector.detect(canvas).then(function(barcodes) {
               var found = [];
               if (barcodes && barcodes.length) {
                  barcodes.forEach(function(b) {
                     if (b.rawValue && b.rawValue.trim()) {
                        found.push({ str: b.rawValue.trim() });
                     }
                  });
               }
               callback(found);
            }).catch(function() { callback([]); });
            return;
         } catch(e) {}
      }
      
      if (typeof Quagga !== 'undefined') {
         try {
            var dataUrl = canvas.toDataURL('image/png');
            Quagga.decodeSingle({
               decoder: { readers: ["code_128_reader", "code_39_reader", "ean_reader"] },
               locate: true,
               src: dataUrl
            }, function(result) {
               if (result && result.codeResult && result.codeResult.code) {
                  callback([{ str: result.codeResult.code.trim() }]);
               } else {
                  callback([]);
               }
            });
            return;
         } catch(e) {}
      }
      
      callback([]);
   }

   function processPDFFile(file, callback) {
      var reader = new FileReader();
      var isResolved = false;

      // Safety timeout: if PDF.js hangs (cross-origin worker issue), fallback after 4 seconds
      var timeoutTimer = setTimeout(function(){
         if (!isResolved) {
            isResolved = true;
            console.warn('PDF.js page count timed out — server will handle everything.');
            // Assume 1 page; server-side smalot/pdfparser will detect the real page count
            if (callback) callback(null, 1);
         }
      }, 4000);

      reader.onload = function(e) {
         var typedarray = new Uint8Array(e.target.result);

         try {
            pdfjsLib.getDocument({ data: typedarray }).promise.then(function(pdf) {
               if (isResolved) return;
               clearTimeout(timeoutTimer);
               isResolved = true;

               currentPdfDocument = pdf;
               totalPdfPages = pdf.numPages;
               currentPdfPage = 1;

               // Render visual preview of page 1
               renderCanvasPage(1);

               // TEXT EXTRACTION IS DONE SERVER-SIDE (smalot/pdfparser)
               // Client only provides page count; each AJAX call sends file + page_index
               if (callback) callback(null, totalPdfPages);

            }).catch(function(err){
               if (isResolved) return;
               clearTimeout(timeoutTimer);
               isResolved = true;
               console.error('PDF.js page count error:', err);
               if (callback) callback(null, 1);
            });
         } catch(err) {
            if (isResolved) return;
            clearTimeout(timeoutTimer);
            isResolved = true;
            if (callback) callback(null, 1);
         }
      };

      reader.readAsArrayBuffer(file);
   }

   function processImageFile(file, callback) {
      var reader = new FileReader();
      reader.onload = function(e) {
         $('#pdf_preview_wrapper').html('<img src="' + e.target.result + '" style="max-width:100%; max-height:250px; border-radius:8px; border:1px solid #cbd5e1;">').show();
         $('#pdf_thumbnail_canvas').hide();

         if (callback) callback();
      };
      reader.readAsDataURL(file);
   }

   $('#btn_search_manual').click(function(){
      var resi = $('#manual_resi_input').val().trim();
      if (!resi) return;
      sendScanAJAX(resi, []);
   });

   $('#manual_resi_input').keypress(function(e){
      if (e.which == 13) {
         e.preventDefault();
         $('#btn_search_manual').click();
      }
   });

   function sendScanAJAX(resiOrFileName, extractedItems) {
      var formData = new FormData();
      formData.append('nomor_resi', resiOrFileName);
      formData.append('extracted_text_items', JSON.stringify(extractedItems));

      var fileInput = document.getElementById('file_pdf_input');
      if (fileInput.files.length > 0) {
         formData.append('file_pdf', fileInput.files[0]);
      }

      $.ajax({
         url: '<?= site_url("administrator/penjualan_marketplace/scan_resi_ajax"); ?>',
         type: 'POST',
         data: formData,
         processData: false,
         contentType: false,
         dataType: 'json',
         success: function(res) {
            if (res.status === 'success') {
               playBeepSound('success');
               $('#scan_resi_feedback').html('<div class="alert alert-success" style="border-radius:8px; font-weight:700;"><i class="fa fa-check-circle"></i> ' + res.message + '</div>');

               renderResults(res.data);
               renderLiveThermalLabel(res.data);
            } else {
               playBeepSound('error');
               $('#scan_resi_feedback').html('<div class="alert alert-danger" style="border-radius:8px; font-weight:700;"><i class="fa fa-exclamation-triangle"></i> ' + res.message + '</div>');
               $('#scan_results_container').hide();
            }
         },
         error: function() {
            playBeepSound('error');
            $('#scan_resi_feedback').html('<div class="alert alert-danger" style="border-radius:8px;"><i class="fa fa-exclamation-triangle"></i> Terjadi kesalahan koneksi ke server.</div>');
         }
      });
   }

   function renderResults(data) {
      $('#scan_results_container').show();

      var cleanName = cleanRecipientName(data.unmasked_name);
      data.unmasked_name = cleanName;

      // Populate Unmasked Recipient Fields
      $('#unmask_target_id').val(data.id);
      $('#unmask_input_name').val(cleanName);
      $('#unmask_input_phone').val(data.unmasked_phone);
      $('#unmask_input_address').val(data.unmasked_address);

      // Populate Bento Summary Cards
      $('#bento_no_pesanan').text(data.no_pesanan);
      $('#bento_nomor_resi').text('Resi: ' + data.nomor_resi);
      $('#bento_courier').text(data.opsi_pengiriman);
      $('#bento_waktu_bayar').text('Bayar: ' + (data.waktu_pembayaran || '-'));
      $('#bento_total_qty').html(data.total_qty + ' <small style="font-size:12px;">pcs</small>');
      $('#bento_total_price').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.total_price || 0));

      // Populate Items Breakdown Table
      var tbody = $('#tbody_breakdown_items');
      tbody.empty();

      if (data.items && data.items.length > 0) {
         $('#breakdown_count_label').text('Total ' + data.items.length + ' varian produk terdaftar');
         data.items.forEach(function(item, idx){
            var skuBadge = item.sku && item.sku !== '-' 
               ? '<span class="sku-chip"><i class="fa fa-barcode text-muted"></i> ' + item.sku + '</span>'
               : '<span class="sku-chip" style="opacity:0.6;">-</span>';

            var variasiPill = '<span class="variasi-pill">' + (item.variasi || 'Default') + '</span>';

            var row = '<tr style="transition:background 0.15s ease;">' +
               '<td class="text-center" style="font-weight:700; color:#64748b;">' + (idx + 1) + '</td>' +
               '<td><strong style="color:#0f172a; font-size:13px;"><i class="fa fa-cube text-info" style="margin-right:4px;"></i> ' + item.master_product_name + '</strong></td>' +
               '<td class="text-center">' + skuBadge + '</td>' +
               '<td class="text-center">' + variasiPill + '</td>' +
               '<td class="text-right" style="color:#475569;">Rp ' + new Intl.NumberFormat('id-ID').format(item.harga_satuan || 0) + '</td>' +
               '<td class="text-center"><span class="qty-badge">' + item.qty + '</span></td>' +
               '<td class="text-right"><strong style="color:#059669; font-size:13px;">Rp ' + new Intl.NumberFormat('id-ID').format(item.subtotal || 0) + '</strong></td>' +
               '</tr>';
            tbody.append(row);
         });
      } else {
         $('#breakdown_count_label').text('0 produk');
         tbody.append('<tr><td colspan="7" class="text-center text-muted" style="padding:20px;">Tidak ada rincian produk.</td></tr>');
      }
   }

   // Handle Save Unmasked Recipient Info to Database
   $('#btn_save_unmask').click(function(){
      var id = $('#unmask_target_id').val();
      var name = $('#unmask_input_name').val().trim();
      var phone = $('#unmask_input_phone').val().trim();
      var addr = $('#unmask_input_address').val().trim();

      if (!id || !name) {
         alert('Nama Penerima tidak boleh kosong!');
         return;
      }

      $.ajax({
         url: '<?= site_url("administrator/penjualan_marketplace/save_unmask_ajax"); ?>',
         type: 'POST',
         dataType: 'json',
         data: {
            id: id,
            nama_penerima: name,
            no_telepon: phone,
            alamat_pengiriman: addr
         },
         success: function(res) {
            if (res.status === 'success') {
               playBeepSound('success');
               alert(res.message);
            } else {
               playBeepSound('error');
               alert(res.message);
            }
         }
      });
   });

   // Initialize Camera Scanner (Tab 2)
   if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
      $('#https_camera_warning').show();
   }

   $('#camera_file_input').change(function(e){
      var file = e.target.files[0];
      if (!file) return;
      sendScanAJAX(file.name, [{ str: file.name }]);
   });

   function loadCameras() {
      if (typeof Html5Qrcode === 'undefined') return;
      Html5Qrcode.getCameras().then(function(devices) {
         var select = $('#camera_select');
         select.empty();
         if (devices && devices.length > 0) {
            devices.forEach(function(device, index) {
               var label = device.label || ('Kamera ' + (index + 1));
               select.append($('<option>', {
                  value: device.id,
                  text: label
               }));
            });
         } else {
            select.append('<option value="">Kamera Default (Kamera HP / Laptop)</option>');
         }
      }).catch(function(err) {
         $('#camera_select').html('<option value="">Kamera Default (Klik "Mulai Kamera" untuk Izinkan)</option>');
      });
   }

   // Request permission automatically when switching to Tab 2
   $('a[href="#tab_kamera_scan"]').on('shown.bs.tab', function (e) {
      if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
         navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
            stream.getTracks().forEach(function(track) { track.stop(); });
            loadCameras();
         }).catch(function(err) {
            loadCameras();
         });
      } else {
         loadCameras();
      }
   });

   var isScanning = false;

   function handleDecodedCode(rawCode) {
      if (!rawCode) return;
      var cleanCode = rawCode.trim().replace(/[^A-Za-z0-9]/g, '');
      if (cleanCode && cleanCode.length >= 5 && !isScanning) {
         isScanning = true;
         $('#manual_resi_input').val(cleanCode);
         sendScanAJAX(cleanCode, [{ str: cleanCode }]);
         setTimeout(function(){ isScanning = false; }, 2000);
      }
   }

   $('#btn_start_camera').click(function(){
      var selectedCam = $('#camera_select').val();
      var cameraConfig = selectedCam ? selectedCam : { facingMode: "environment" };

      $('#reader').show();

      var supportedFormats = (typeof Html5QrcodeSupportedFormats !== 'undefined') ? [
         Html5QrcodeSupportedFormats.CODE_128,
         Html5QrcodeSupportedFormats.CODE_39,
         Html5QrcodeSupportedFormats.CODE_93,
         Html5QrcodeSupportedFormats.EAN_13,
         Html5QrcodeSupportedFormats.QR_CODE,
         Html5QrcodeSupportedFormats.DATA_MATRIX,
         Html5QrcodeSupportedFormats.UPC_A
      ] : [0, 1, 2, 5, 8, 4, 9];

      if (!html5QrCode) {
         try {
            html5QrCode = new Html5Qrcode("reader", { formatsToSupport: supportedFormats, verbose: false });
         } catch(e) {
            html5QrCode = new Html5Qrcode("reader");
         }
      }

      var scanOptions = {
         fps: 20,
         qrbox: function(viewfinderWidth, viewfinderHeight) {
            return {
               width: Math.floor(viewfinderWidth * 0.95),
               height: Math.floor(viewfinderHeight * 0.82)
            };
         },
         aspectRatio: 1.333333,
         experimentalFeatures: { useBarCodeDetectorIfSupported: true }
      };

      html5QrCode.start(
         cameraConfig,
         scanOptions,
         function(decodedText, decodedResult) {
            handleDecodedCode(decodedText);
         },
         function(errorMessage) {}
      ).then(function() {
         $('#btn_start_camera').hide();
         $('#btn_stop_camera').show();
         loadCameras();
      }).catch(function(err) {
         // Fallback to default/user facing camera
         html5QrCode.start(
            { facingMode: "user" },
            scanOptions,
            function(decodedText, decodedResult) {
               handleDecodedCode(decodedText);
            },
            function(e) {}
         ).then(function(){
            $('#btn_start_camera').hide();
            $('#btn_stop_camera').show();
            loadCameras();
         }).catch(function(e2){
            alert('Gagal membuka kamera: Silakan klik tombol "Izinkan/Allow" pada browser Anda untuk mengakses kamera.');
         });
      });
   });

   $('#btn_stop_camera').click(function(){
      if (html5QrCode) {
         html5QrCode.stop().then(function() {
            $('#reader').hide();
            $('#btn_start_camera').show();
            $('#btn_stop_camera').hide();
         });
      }
   });

   // Camera Stop

});
</script>
