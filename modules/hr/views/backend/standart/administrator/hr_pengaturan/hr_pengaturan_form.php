<section class="content-header">
   <h1>Pengaturan HR & Geofencing <small style="font-weight: 400; color: #777;">Konfigurasi Sistem Karyawan</small></h1>
   <ol class="breadcrumb" style="background: transparent;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Pengaturan HR</li>
   </ol>
</section>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
   .modern-panel { border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; border-top: 4px solid #3c8dbc; background: #fff; }
   .modern-panel-heading { padding: 15px 25px; border-bottom: 1px solid #eee; }
   .modern-panel-title { margin: 0; font-size: 16px; font-weight: 600; }
   .input-group-addon { background-color: #f4f6f9; border-color: #d2d6de; }
   .setting-section { padding: 25px; }
   
   /* Warna spesifik panel */
   .panel-geo { border-top-color: #3c8dbc; }
   .panel-geo .modern-panel-heading { background-color: #f0f7fb; border-bottom-color: #d7ebf7; }
   .panel-geo .modern-panel-title { color: #296b92; }

   .panel-time { border-top-color: #00a65a; }
   .panel-time .modern-panel-heading { background-color: #ebf7f0; border-bottom-color: #d0ebd8; }
   .panel-time .modern-panel-title { color: #007540; }

   .panel-pay { border-top-color: #dd4b39; }
   .panel-pay .modern-panel-heading { background-color: #faebea; border-bottom-color: #f2d0cf; }
   .panel-pay .modern-panel-title { color: #a93223; }

   .table-denda th { background-color: #dd4b39; color: white; border-color: #c74333 !important; }
   .table-denda td { vertical-align: middle !important; }
</style>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         
         <form id="form_pengaturan" method="POST" action="<?= site_url('administrator/hr_pengaturan/save'); ?>">
            
            <div class="row">
               <div class="col-md-6">
                  
                  <!-- PANEL GEO -->
                  <div class="modern-panel panel-geo">
                     <div class="modern-panel-heading">
                        <h4 class="modern-panel-title"><i class="fa fa-map-marker margin-r-5"></i> Lokasi Kantor (Geofencing)</h4>
                     </div>
                     <div class="setting-section">
                        <div class="form-group">
                           <label>Kantor Utama (Default) <span class="text-danger">*</span></label>
                           <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-building"></i></span>
                              <select class="form-control" name="default_kantor_id" id="default_kantor_id">
                                 <?php foreach($kantors as $kantor): ?>
                                     <option value="<?= $kantor->id ?>" <?= (isset($pengaturan->default_kantor_id) && $pengaturan->default_kantor_id == $kantor->id) ? 'selected' : ''; ?>>
                                         <?= _ent($kantor->nama_kantor) ?> (Radius: <?= $kantor->radius_meter ?>m)
                                     </option>
                                 <?php endforeach; ?>
                              </select>
                           </div>
                           <small class="text-muted" style="display:block; margin-top:8px;">Karyawan yang tidak diatur penempatan kantornya secara spesifik akan menggunakan titik kordinat Kantor Utama ini sebagai lokasi absensi.</small>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="col-md-6">

                  <!-- PANEL WAKTU KERJA -->
                  <div class="modern-panel panel-time">
                     <div class="modern-panel-heading">
                        <h4 class="modern-panel-title"><i class="fa fa-clock-o margin-r-5"></i> Pengaturan Jam Kerja</h4>
                     </div>
                     <div class="setting-section">
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label>Jam Masuk Standar</label>
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-sign-in text-success"></i></span>
                                    <input type="time" class="form-control" name="jam_masuk" value="<?= _ent($pengaturan->jam_masuk ?? '08:00'); ?>" required>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label>Toleransi Terlambat</label>
                                 <div class="input-group">
                                    <input type="number" class="form-control" name="toleransi_terlambat_menit" value="<?= _ent($pengaturan->toleransi_terlambat_menit ?? 15); ?>" required>
                                    <span class="input-group-addon">Menit</span>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row" style="margin-top: 10px;">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label>Jam Pulang Standar</label>
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-sign-out text-danger"></i></span>
                                    <input type="time" class="form-control" name="jam_pulang" value="<?= _ent($pengaturan->jam_pulang ?? '17:00'); ?>" required>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label>Toleransi Pulang Awal</label>
                                 <div class="input-group">
                                    <input type="number" class="form-control" name="toleransi_pulang_awal_menit" value="<?= _ent($pengaturan->toleransi_pulang_awal_menit ?? 0); ?>" required>
                                    <span class="input-group-addon">Menit</span>
                                 </div>
                                 <small class="text-muted">Boleh pulang sebelum Jam Pulang.</small>
                              </div>
                           </div>
                        </div>

                        <div class="form-group" style="margin-top: 10px;">
                           <label>Batas Akhir Jam Pulang (Batas Tutup Absen)</label>
                           <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-ban text-warning"></i></span>
                              <input type="time" class="form-control" name="batas_jam_pulang" value="<?= _ent($pengaturan->batas_jam_pulang ?? '20:00'); ?>" required>
                           </div>
                           <small class="text-muted">Batas maksimal karyawan boleh melakukan absen pulang. Lewat dari jam ini sistem menolak absensi pulang.</small>
                        </div>
                     </div>
                  </div>



               </div>
               

            </div>

            <!-- ACTION BUTTON -->
            <div class="row">
               <div class="col-md-12 text-center" style="margin-bottom: 40px; margin-top: 10px;">
                  <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 30px; padding: 12px 40px; font-size: 16px; font-weight: bold; box-shadow: 0 4px 10px rgba(60,141,188,0.4); transition: all 0.3s;"><i class="fa fa-save margin-r-5"></i> Simpan Konfigurasi Sistem</button>
               </div>
            </div>

         </form>
      </div>
   </div>
</section>

<script>
$(document).ready(function(){
   $("#form_pengaturan").submit(function(e){
      e.preventDefault();
      var btn = $(this).find('button[type="submit"]');
      var originalText = btn.html();
      btn.html('<i class="fa fa-spinner fa-spin margin-r-5"></i> Memproses...');
      btn.prop('disabled', true);
      
      $.ajax({
         url: $(this).attr('action'),
         type: 'POST',
         data: $(this).serialize(),
         dataType: 'json',
         success: function(res){
            if(res.success){
               toastr.success('Pengaturan HR berhasil disimpan!');
               setTimeout(function(){
                  window.location.href = res.redirect;
               }, 1000);
            }else{
               swal({
                   title: "Gagal Simpan ❌",
                   text: res.message,
                   type: "warning",
                   confirmButtonColor: "#f39c12"
               });
               btn.html(originalText);
               btn.prop('disabled', false);
            }
         },
         error: function() {
            swal({
                title: "Error",
                text: "Terjadi kesalahan pada server saat menyimpan pengaturan.",
                type: "error",
                confirmButtonColor: "#d33"
            });
            btn.html(originalText);
            btn.prop('disabled', false);
         }
      });
   });
});
</script>
