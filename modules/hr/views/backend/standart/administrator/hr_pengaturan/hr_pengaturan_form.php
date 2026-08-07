
<section class="content-header">
   <h1>Pengaturan HR & Geofencing</h1>
</section>
<section class="content">
   <div class="box box-warning">
      <div class="box-body">
         <form id="form_pengaturan" method="POST" action="<?= site_url('administrator/hr_pengaturan/save'); ?>">
            
            <h4 class="text-primary"><i class="fa fa-map-marker"></i> Lokasi Kantor (Geofencing)</h4>
            <div class="row">
               <div class="col-md-12">
                  <div class="form-group">
                     <label>Nama Kantor</label>
                     <input type="text" class="form-control" name="nama_kantor" value="<?= _ent($pengaturan->nama_kantor ?? ''); ?>" required>
                  </div>
               </div>
               <div class="col-md-5">
                  <div class="form-group">
                     <label>Latitude <span class="text-danger">*</span></label>
                     <input type="text" class="form-control" name="latitude_kantor" id="lat" value="<?= _ent($pengaturan->latitude_kantor ?? ''); ?>" required>
                  </div>
               </div>
               <div class="col-md-5">
                  <div class="form-group">
                     <label>Longitude <span class="text-danger">*</span></label>
                     <input type="text" class="form-control" name="longitude_kantor" id="lng" value="<?= _ent($pengaturan->longitude_kantor ?? ''); ?>" required>
                  </div>
               </div>
               <div class="col-md-2">
                  <div class="form-group">
                     <label>&nbsp;</label>
                     <button type="button" class="btn btn-info btn-block" onclick="getLocation()"><i class="fa fa-crosshairs"></i> Get My Location</button>
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="form-group">
                     <label>Radius Toleransi WFO (Meter)</label>
                     <input type="number" class="form-control" name="radius_toleransi_meter" value="<?= _ent($pengaturan->radius_toleransi_meter ?? 50); ?>" required>
                     <small class="text-muted">Karyawan WFO tidak akan bisa absen jika jarak mereka ke titik kantor melebihi radius ini.</small>
                  </div>
               </div>
            </div>

            <hr>
            <h4 class="text-success"><i class="fa fa-clock-o"></i> Pengaturan Jam Kerja</h4>
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Jam Masuk</label>
                     <input type="time" class="form-control" name="jam_masuk" value="<?= _ent($pengaturan->jam_masuk ?? '08:00'); ?>" required>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Toleransi Terlambat (Menit)</label>
                     <input type="number" class="form-control" name="toleransi_terlambat_menit" value="<?= _ent($pengaturan->toleransi_terlambat_menit ?? 15); ?>" required>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Jam Pulang</label>
                     <input type="time" class="form-control" name="jam_pulang" value="<?= _ent($pengaturan->jam_pulang ?? '17:00'); ?>" required>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Toleransi Pulang Awal (Menit)</label>
                     <input type="number" class="form-control" name="toleransi_pulang_awal_menit" value="<?= _ent($pengaturan->toleransi_pulang_awal_menit ?? 0); ?>" required>
                     <small class="text-muted">Menit kebolehan absen pulang sebelum Jam Pulang.</small>
                  </div>
               </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Pengaturan</button>
         </form>
      </div>
   </div>
</section>

<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else { 
        alert("Geolocation API tidak didukung di browser ini.");
    }
}
function showPosition(position) {
    $("#lat").val(position.coords.latitude);
    $("#lng").val(position.coords.longitude);
    toastr.success("Titik koordinat berhasil didapatkan!");
}
function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED: alert("Pengguna menolak izin Geolocation."); break;
        case error.POSITION_UNAVAILABLE: alert("Informasi lokasi tidak tersedia."); break;
        case error.TIMEOUT: alert("Request timeout saat mendapatkan lokasi."); break;
        case error.UNKNOWN_ERROR: alert("Terjadi kesalahan yang tidak diketahui."); break;
    }
}

$(document).ready(function(){
   $("#form_pengaturan").submit(function(e){
      e.preventDefault();
      var btn = $(this).find('button[type="submit"]');
      btn.prop('disabled', true);
      $.ajax({
         url: $(this).attr('action'),
         type: 'POST',
         data: $(this).serialize(),
         dataType: 'json',
         success: function(res){
            if(res.success){
               window.location.href = res.redirect;
            }else{
               alert(res.message);
               btn.prop('disabled', false);
            }
         }
      });
   });
});
</script>
