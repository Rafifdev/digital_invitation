<?php
/**
 * View: Workstation Scan Barcode & Packing (Real-Time Camera & Scanner)
 * File: penjualan_marketplace_import_packed.php
 * Ultra-Compact Executive UI/UX System
 */
?>
<!-- Include HTML5-QRCode JS Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.workstation-container {
   font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
   padding: 15px 20px;
}

/* Card Header */
.ws-card-header {
   background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
   color: #ffffff;
   border-top-left-radius: 14px;
   border-top-right-radius: 14px;
   padding: 18px 24px;
   display: flex;
   justify-content: space-between;
   align-items: center;
}

/* Custom Nav Tabs */
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
   font-size: 12.5px;
   color: #64748b;
   padding: 10px 14px;
   border: none !important;
   margin-right: 0 !important;
   transition: all 0.2s ease;
}
.ws-nav-tabs > li.active > a,
.ws-nav-tabs > li.active > a:focus,
.ws-nav-tabs > li.active > a:hover {
   background: #ffffff !important;
   color: #0284c7 !important;
   box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

/* Compact Live Camera Reader Box */
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
#reader video {
   object-fit: cover !important;
   border-radius: 12px;
}

/* Live Session Statistic Cards */
.ws-stat-card {
   background: #ffffff;
   border: 1px solid #e2e8f0;
   border-radius: 14px;
   padding: 16px 20px;
   box-shadow: 0 4px 15px rgba(0,0,0,0.02);
   margin-bottom: 20px;
   transition: all 0.2s ease;
}
.ws-stat-card:hover {
   transform: translateY(-2px);
   box-shadow: 0 8px 25px rgba(0,0,0,0.06);
}
.ws-stat-title {
   font-size: 11px;
   font-weight: 800;
   text-transform: uppercase;
   color: #64748b;
   letter-spacing: 0.5px;
}
.ws-stat-num {
   font-size: 24px;
   font-weight: 900;
   color: #0f172a;
   margin-top: 4px;
}
</style>

<section class="content-header" style="padding-top:15px; padding-bottom:10px;">
   <div class="row">
      <div class="col-md-8 col-sm-12">
         <h3 style="margin:0; font-weight:800; color:#0f172a;">
            <i class="fa fa-barcode text-success"></i> Workstation Scan Barcode &amp; Packing
            <small style="font-size:12px; color:#64748b; display:inline-block; margin-left:8px;">Scan Resi Real-Time Menggunakan Barcode Scanner &amp; Kamera</small>
         </h3>
      </div>
      <div class="col-md-4 col-sm-12 text-right">
         <ol class="breadcrumb" style="background:transparent; margin:0; padding:0;">
            <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?= site_url('administrator/penjualan_marketplace'); ?>">Penjualan Marketplace</a></li>
            <li class="active">Scan Workstation</li>
         </ol>
      </div>
   </div>
</section>

<section class="content workstation-container" style="padding-top:0;">

   <!-- Main Workstation Card -->
   <div class="box box-solid" style="border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 8px 30px rgba(0,0,0,0.04); overflow:hidden;">
      
      <!-- Box Header -->
      <div class="ws-card-header">
         <div>
            <strong style="font-size:15px; font-weight:800;"><i class="fa fa-crosshairs text-info"></i> Scan-to-Pack / Ship Workstation</strong>
            <div style="font-size:12px; opacity:0.8; margin-top:2px;">Setiap resi yang discan otomatis diubah statusnya &amp; memotong stok di Buku Besar Stok (FIFO)</div>
         </div>
         <div style="display:flex; align-items:center; gap:10px;">
            <span style="font-size:12px; font-weight:700;">Status Target:</span>
            <div class="btn-group" data-toggle="buttons">
               <label class="btn btn-sm btn-success active" id="btn_target_packed" style="font-weight:700; border-radius:6px 0 0 6px;">
                  <input type="radio" name="target_status" value="packing" checked> <i class="fa fa-check"></i> PACKED
               </label>
               <label class="btn btn-sm btn-default" id="btn_target_shipped" style="font-weight:700; border-radius:0 6px 6px 0;">
                  <input type="radio" name="target_status" value="shipped"> <i class="fa fa-truck"></i> SHIPPED
               </label>
            </div>
            <button type="button" class="btn btn-sm btn-warning" id="btn_toggle_sound" title="Toggle Beep Sound" style="border-radius:6px; font-weight:700;">
               <i class="fa fa-volume-up"></i>
            </button>
         </div>
      </div>

      <div class="box-body" style="padding:22px;">

         <!-- 3 Input Tabs -->
         <ul class="nav ws-nav-tabs" role="tablist">
            <li role="presentation" class="active">
               <a href="#tab_scanner_fisik" aria-controls="tab_scanner_fisik" role="tab" data-toggle="tab"><i class="fa fa-barcode"></i> 1. Barcode Scanner Fisik</a>
            </li>
            <li role="presentation">
               <a href="#tab_kamera_hp" aria-controls="tab_kamera_hp" role="tab" data-toggle="tab"><i class="fa fa-camera"></i> 2. Kamera HP / Laptop</a>
            </li>
            <li role="presentation">
               <a href="#tab_paste_massal" aria-controls="tab_paste_massal" role="tab" data-toggle="tab"><i class="fa fa-clipboard"></i> 3. Tempel / Paste Massal</a>
            </li>
         </ul>

         <div class="tab-content" style="padding-top:6px;">
            
            <!-- TAB 1: PHYSICAL BARCODE SCANNER -->
            <div role="tabpanel" class="tab-pane active" id="tab_scanner_fisik">
               <div style="background:#f8fafc; border:2px dashed #cbd5e1; border-radius:12px; padding:25px 20px; text-align:center;">
                  <i class="fa fa-barcode text-primary" style="font-size:42px; margin-bottom:10px;"></i>
                  <h4 style="font-weight:800; color:#0f172a; margin-top:0;">Scan Barcode No. Resi di Sini</h4>
                  <p style="color:#64748b; font-size:12.5px; max-width:480px; margin:0 auto 16px auto;">Arahkan barcode scanner USB / Bluetooth ke barcode resi pada paket. Sistem akan membaca &amp; mengupdate status secara otomatis.</p>
                  
                  <div style="max-width:500px; margin:0 auto;">
                     <div class="input-group input-group-lg">
                        <input type="text" id="input_scan_fisik" class="form-control" placeholder="Scan Barcode No. Resi atau No. Pesanan..." autofocus style="border-radius:8px 0 0 8px; font-weight:700; font-family:monospace; text-transform:uppercase; font-size:14px;">
                        <span class="input-group-btn">
                           <button type="button" id="btn_submit_scan_fisik" class="btn btn-primary btn-flat" style="border-radius:0 8px 8px 0; background:#0284c7; border-color:#0284c7; font-weight:700; font-size:14px;"><i class="fa fa-search"></i> Process Scan</button>
                        </span>
                     </div>
                  </div>
                  <div id="scan_feedback_fisik" style="margin-top:14px; font-weight:700; font-size:13.5px;"></div>
               </div>
            </div>

            <!-- TAB 2: LIVE CAMERA BARCODE SCANNER (COMPACT BOX) -->
            <div role="tabpanel" class="tab-pane" id="tab_kamera_hp">
               <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px; text-align:center;">
                  
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

                  <p style="color:#64748b; font-size:12px; margin-bottom:12px;"><i class="fa fa-info-circle text-info"></i> Arahkan barcode resi (Code 128 / QR Code) ke dalam kotak kamera hingga terdengar bunyi Beep.</p>
                  
                  <!-- Compact Camera Render Box -->
                  <div id="reader" style="display:none;"></div>
                  
                  <div id="scan_feedback_camera" style="margin-top:12px; font-weight:700; font-size:13.5px;"></div>
               </div>
            </div>

            <!-- TAB 3: BULK PASTE TEXTAREA -->
            <div role="tabpanel" class="tab-pane" id="tab_paste_massal">
               <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px;">
                  <label style="font-weight:700; color:#0f172a; font-size:12.5px;"><i class="fa fa-paste text-success"></i> Tempelkan/Paste Banyak No. Resi (Satu Resi Per Baris):</label>
                  <textarea id="textarea_bulk_resi" class="form-control" rows="5" placeholder="Contoh:&#10;SPX999000333&#10;SPX999000222&#10;JP1234567890" style="font-family:monospace; border-radius:8px; font-size:12.5px;"></textarea>
                  <div class="text-right" style="margin-top:10px;">
                     <button type="button" id="btn_process_bulk" class="btn btn-success btn-flat" style="border-radius:6px; font-weight:700; background:#10b981; border-color:#10b981; font-size:12px;"><i class="fa fa-check-square-o"></i> Proses Update Massal Resi</button>
                  </div>
               </div>
            </div>

         </div>

         <!-- Live Session KPI Summary Cards -->
         <div class="row" style="margin-top:20px;">
            <div class="col-md-4 col-sm-4 col-xs-12">
               <div class="ws-stat-card">
                  <div style="float:right; background:#d1fae5; color:#10b981; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px;"><i class="fa fa-check-circle"></i></div>
                  <div class="ws-stat-title">PAKET SUKSES DISCAN</div>
                  <div class="ws-stat-num" id="stat_total_scanned">0</div>
               </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
               <div class="ws-stat-card">
                  <div style="float:right; background:#e0f2fe; color:#0284c7; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px;"><i class="fa fa-cubes"></i></div>
                  <div class="ws-stat-title">TOTAL QTY PRODUK</div>
                  <div class="ws-stat-num" id="stat_total_qty">0</div>
               </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
               <div class="ws-stat-card">
                  <div style="float:right; background:#fef3c7; color:#d97706; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px;"><i class="fa fa-money"></i></div>
                  <div class="ws-stat-title">TOTAL NILAI PESANAN</div>
                  <div class="ws-stat-num" id="stat_total_nilai">Rp 0</div>
               </div>
            </div>
         </div>

         <!-- Live Scan Session History Table -->
         <div style="margin-top:5px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
               <strong style="font-size:13.5px; color:#0f172a;"><i class="fa fa-history text-muted"></i> Riwayat Paket Yang Baru Discan Sesi Ini</strong>
               <button type="button" id="btn_clear_history" class="btn btn-default btn-xs" style="border-radius:4px;"><i class="fa fa-trash text-danger"></i> Bersihkan Riwayat Sesi</button>
            </div>

            <div class="table-responsive" style="border:1px solid #e2e8f0; border-radius:10px;">
               <table class="table table-hover table-striped" id="table_scan_history" style="font-size:12px; margin-bottom:0; background:#fff;">
                  <thead>
                     <tr style="background:#0f172a; color:#fff; font-size:11px; text-transform:uppercase;">
                        <th width="40" class="text-center">#</th>
                        <th width="80">Jam</th>
                        <th>No. Resi (AWB)</th>
                        <th>No. Pesanan</th>
                        <th>Kurir</th>
                        <th>Penerima</th>
                        <th class="text-center">Total Qty</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" width="60">Aksi</th>
                     </tr>
                  </thead>
                  <tbody id="tbody_scan_history">
                     <tr id="row_empty_history">
                        <td colspan="9" class="text-center text-muted" style="padding:30px;">
                           <i class="fa fa-barcode fa-2x" style="color:#cbd5e1; margin-bottom:6px;"></i><br>
                           <span>Belum ada paket yang discan pada sesi ini.</span><br>
                           <small class="text-muted">Mulai tembak barcode resi menggunakan scanner atau kamera di atas.</small>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>

      </div>

      <!-- Box Footer -->
      <div class="box-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:16px 22px; display:flex; justify-content:space-between; align-items:center;">
         <a href="<?= site_url('administrator/penjualan_marketplace'); ?>" class="btn btn-default flat" style="border-radius:6px; font-weight:700;"><i class="fa fa-arrow-left"></i> Kembali ke Penjualan Marketplace</a>
         <a href="<?= site_url('administrator/penjualan_marketplace'); ?>" class="btn btn-primary flat" style="border-radius:6px; font-weight:700; background:#0f172a; border-color:#0f172a;"><i class="fa fa-check-circle"></i> Selesai &amp; Buka Daftar Paket</a>
      </div>

   </div>
</section>

<!-- Audio Context & JS Handler -->
<script>
var soundEnabled = true;

function playBeepSound(type) {
   if (!soundEnabled) return;
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
   var scannedCount = 0;
   var totalQtySum = 0;
   var totalNilaiSum = 0;
   var html5QrCode = null;

   $('#btn_toggle_sound').click(function(){
      soundEnabled = !soundEnabled;
      if (soundEnabled) {
         $(this).removeClass('btn-default').addClass('btn-warning').html('<i class="fa fa-volume-up"></i>');
         playBeepSound('success');
      } else {
         $(this).removeClass('btn-warning').addClass('btn-default').html('<i class="fa fa-volume-off"></i>');
      }
   });

   $('input[name="target_status"]').change(function(){
      if (this.value === 'shipped') {
         $('#btn_target_shipped').removeClass('btn-default').addClass('btn-primary active');
         $('#btn_target_packed').removeClass('btn-success active').addClass('btn-default');
      } else {
         $('#btn_target_packed').removeClass('btn-default').addClass('btn-success active');
         $('#btn_target_shipped').removeClass('btn-primary active').addClass('btn-default');
      }
   });

   $('#input_scan_fisik').keypress(function(e){
      if (e.which == 13) {
         e.preventDefault();
         processScan($(this).val().trim(), 'fisik');
      }
   });

   $('#btn_submit_scan_fisik').click(function(){
      processScan($('#input_scan_fisik').val().trim(), 'fisik');
   });

   function processScan(resi, source) {
      if (!resi) return;

      var targetStatus = $('input[name="target_status"]:checked').val();

      $.ajax({
         url: '<?= site_url("administrator/penjualan_marketplace/process_scan_resi"); ?>',
         type: 'POST',
         dataType: 'json',
         data: {
            nomor_resi: resi,
            target_status: targetStatus
         },
         success: function(res) {
            if (res.status === 'success') {
               playBeepSound('success');
               scannedCount++;
               totalQtySum += parseInt(res.data.total_qty || 1);
               totalNilaiSum += parseFloat(res.data.total || 0);

               $('#stat_total_scanned').text(scannedCount);
               $('#stat_total_qty').text(totalQtySum);
               $('#stat_total_nilai').text('Rp ' + new Intl.NumberFormat('id-ID').format(totalNilaiSum));

               $('#row_empty_history').remove();

               var newRow = '<tr>' +
                  '<td>' + scannedCount + '</td>' +
                  '<td><small class="text-muted">' + res.data.waktu + '</small></td>' +
                  '<td><strong style="font-family:monospace; color:#0284c7;">' + res.data.nomor_resi + '</strong></td>' +
                  '<td><strong style="font-family:monospace;">' + res.data.no_pesanan + '</strong></td>' +
                  '<td><span class="label label-info">' + res.data.opsi_pengiriman + '</span></td>' +
                  '<td>' + res.data.nama_penerima + '</td>' +
                  '<td class="text-center"><strong>' + res.data.total_qty + '</strong></td>' +
                  '<td class="text-center"><span class="label label-success">' + res.data.status_label + '</span></td>' +
                  '<td class="text-center"><a href="<?= site_url("administrator/penjualan_marketplace/view/"); ?>' + res.data.id + '" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-eye"></i></a></td>' +
                  '</tr>';

               $('#tbody_scan_history').prepend(newRow);

               var feedbackHtml = '<span class="text-success"><i class="fa fa-check-circle"></i> ' + res.message + '</span>';
               if (source === 'fisik') $('#scan_feedback_fisik').html(feedbackHtml);
               else $('#scan_feedback_camera').html(feedbackHtml);

               $('#input_scan_fisik').val('').focus();
            } else {
               playBeepSound('error');
               var errorHtml = '<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> ' + res.message + '</span>';
               if (source === 'fisik') $('#scan_feedback_fisik').html(errorHtml);
               else $('#scan_feedback_camera').html(errorHtml);

               $('#input_scan_fisik').select().focus();
            }
         },
         error: function() {
            playBeepSound('error');
            alert('Terjadi kesalahan koneksi ke server.');
         }
      });
   }

   // Initialize HTML5-QRCode Camera Scanner with Compact Size
   Html5Qrcode.getCameras().then(devices => {
      if (devices && devices.length) {
         var select = $('#camera_select');
         select.empty();
         devices.forEach(device => {
            select.append($('<option>', {
               value: device.id,
               text: device.label || ('Camera ' + device.id)
            }));
         });
      } else {
         $('#camera_select').html('<option value="">Tidak ada kamera terdeteksi pada perangkat ini</option>');
      }
   }).catch(err => {
      $('#camera_select').html('<option value="">Izin akses kamera belum diberikan</option>');
   });

   var isScanning = false;
   $('#btn_start_camera').click(function(){
      var cameraId = $('#camera_select').val();
      if (!cameraId) {
         alert('Silakan pilih kamera terlebih dahulu!');
         return;
      }

      $('#reader').show();
      html5QrCode = new Html5Qrcode("reader");
      html5QrCode.start(
         cameraId,
         {
            fps: 15,
            qrbox: { width: 240, height: 120 }, // Compact rectangular ratio for shipping AWB resi barcodes
            aspectRatio: 1.333333
         },
         (decodedText, decodedResult) => {
            if (!isScanning) {
               isScanning = true;
               processScan(decodedText, 'camera');
               setTimeout(function(){ isScanning = false; }, 1500); // 1.5s debounce
            }
         },
         (errorMessage) => {}
      ).then(() => {
         $('#btn_start_camera').hide();
         $('#btn_stop_camera').show();
      }).catch((err) => {
         alert('Gagal membuka kamera: ' + err);
      });
   });

   $('#btn_stop_camera').click(function(){
      if (html5QrCode) {
         html5QrCode.stop().then(() => {
            $('#reader').hide();
            $('#btn_start_camera').show();
            $('#btn_stop_camera').hide();
         });
      }
   });

   $('#btn_process_bulk').click(function(){
      var text = $('#textarea_bulk_resi').val().trim();
      if (!text) {
         alert('Silakan tempelkan nomor resi terlebih dahulu!');
         return;
      }
      var lines = text.split(/\r\n|\n/);
      var count = 0;
      lines.forEach(function(resi){
         if (resi.trim() !== '') {
            processScan(resi.trim(), 'bulk');
            count++;
         }
      });
      alert('Memproses ' + count + ' nomor resi...');
      $('#textarea_bulk_resi').val('');
   });

   $('#btn_clear_history').click(function(){
      if (confirm('Bersihkan riwayat scan sesi ini?')) {
         $('#tbody_scan_history').html('<tr id="row_empty_history"><td colspan="9" class="text-center text-muted" style="padding:30px;"><i class="fa fa-barcode fa-2x" style="color:#cbd5e1; margin-bottom:6px;"></i><br><span>Belum ada paket yang discan pada sesi ini.</span></td></tr>');
         scannedCount = 0;
         totalQtySum = 0;
         totalNilaiSum = 0;
         $('#stat_total_scanned').text(0);
         $('#stat_total_qty').text(0);
         $('#stat_total_nilai').text('Rp 0');
      }
   });
});
</script>
