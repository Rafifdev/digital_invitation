<section class="content-header">
   <h1>Data Kantor Cabang <small style="font-weight: 400; color: #777;">Tambah Kantor Baru</small></h1>
   <ol class="breadcrumb" style="background: transparent;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/hr_kantor'); ?>">Daftar Kantor</a></li>
      <li class="active">Tambah</li>
   </ol>
</section>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
   .modern-panel { border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; border-top: 4px solid #3c8dbc; }
   .modern-panel-heading { background: #f8fbff; padding: 15px 20px; border-bottom: 1px solid #eee; }
   .modern-panel-title { margin: 0; font-size: 16px; font-weight: 600; color: #333; }
   .map-container { height: 350px; width: 100%; border: 2px solid #e0e0e0; border-radius: 8px; box-shadow: inset 0 0 10px rgba(0,0,0,0.05); z-index: 1; }
   .input-group-addon { background-color: #f4f6f9; border-color: #d2d6de; }
</style>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         
         <form id="form_hr_kantor" action="<?= site_url('administrator/hr_kantor/add_save'); ?>" method="POST">
            
            <div class="box modern-panel">
               <div class="modern-panel-heading">
                  <h4 class="modern-panel-title"><i class="fa fa-building text-primary margin-r-5"></i> Informasi Kantor Cabang</h4>
               </div>
               <div class="box-body" style="padding: 25px;">
                  
                  <div class="row">
                     <!-- KIRI: Data Dasar -->
                     <div class="col-md-5">
                        <div class="form-group">
                           <label>Nama Kantor / Cabang <span class="text-danger">*</span></label>
                           <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-building-o"></i></span>
                              <input type="text" class="form-control" name="nama_kantor" id="nama_kantor" placeholder="Contoh: Cabang Utama / Pabrik 1" required>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label>Latitude <span class="text-danger">*</span></label>
                                 <input type="text" class="form-control" name="latitude" id="lat" placeholder="Contoh: -6.2088" required>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label>Longitude <span class="text-danger">*</span></label>
                                 <input type="text" class="form-control" name="longitude" id="lng" placeholder="Contoh: 106.8456" required>
                              </div>
                           </div>
                        </div>
                        
                        <div class="form-group">
                           <button type="button" class="btn btn-info btn-sm btn-block" onclick="getLocation()" style="border-radius: 20px;"><i class="fa fa-crosshairs"></i> Deteksi Lokasi Saya Saat Ini</button>
                        </div>

                        <div class="form-group" style="margin-top: 20px;">
                           <label>Batas Radius Absensi (Meter) <span class="text-danger">*</span></label>
                           <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-bullseye text-warning"></i></span>
                              <input type="number" class="form-control" name="radius_meter" id="radius_meter" placeholder="Contoh: 50" value="50" required>
                              <span class="input-group-addon">Meter</span>
                           </div>
                           <small class="text-muted">Karyawan tidak bisa absen jika jaraknya lebih dari radius ini dari titik kantor.</small>
                        </div>

                        <div class="form-group">
                           <label>Alamat Lengkap</label>
                           <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                              <textarea class="form-control" name="alamat" id="alamat" rows="4" placeholder="Detail alamat kantor..."></textarea>
                           </div>
                        </div>
                     </div>

                     <!-- KANAN: Peta -->
                     <div class="col-md-7">
                        <label>Pilih Titik Kordinat Manual di Peta</label>
                        <p class="text-muted" style="margin-bottom: 10px; font-size: 13px;">Geser marker merah pada peta atau gunakan tombol deteksi lokasi di samping.</p>
                        <div id="map_picker" class="map-container"></div>
                     </div>
                  </div>

               </div>
               <div class="box-footer" style="padding: 20px 25px; background: #fdfdfd; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                  <button class="btn btn-primary btn-lg" id="btn_save" type="submit" style="border-radius: 6px; padding: 8px 25px;"><i class="fa fa-save"></i> Simpan Data Kantor</button>
                  <a class="btn btn-default btn-lg" id="btn_cancel" title="Batal" style="border-radius: 6px; padding: 8px 20px; margin-left: 10px;"><i class="fa fa-undo"></i> Batal</a>
                  <span class="loading loading-hide" style="margin-left: 15px;"><img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg"> <i style="color: #666;">Memproses...</i></span>
               </div>
            </div>

         </form>
      </div>
   </div>
</section>

<script>
$(document).ready(function(){
   $('#btn_cancel').click(function(){
      window.location.href = '<?= site_url("administrator/hr_kantor"); ?>';
   });

   $('#form_hr_kantor').submit(function(e){
      e.preventDefault();
      var form = $(this);
      $('.loading').removeClass('loading-hide');
      $('#btn_save').prop('disabled', true);

      $.ajax({
         url: form.attr('action'),
         type: 'POST',
         data: form.serialize(),
         dataType: 'json',
         success: function(res) {
            $('.loading').addClass('loading-hide');
            $('#btn_save').prop('disabled', false);

            if(res.success) {
               window.location.href = res.redirect;
            } else {
               swal("Gagal", res.message, "error");
            }
         },
         error: function() {
            $('.loading').addClass('loading-hide');
            $('#btn_save').prop('disabled', false);
            swal("Error", "Gagal terhubung ke server", "error");
         }
      });
   });
});

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
    
    var newLatLng = new L.LatLng(position.coords.latitude, position.coords.longitude);
    markerPicker.setLatLng(newLatLng);
    if(circlePicker) circlePicker.setLatLng(newLatLng);
    mapPicker.panTo(newLatLng);
    mapPicker.setZoom(17);
}
function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED: alert("Pengguna menolak izin Geolocation."); break;
        case error.POSITION_UNAVAILABLE: alert("Informasi lokasi tidak tersedia."); break;
        case error.TIMEOUT: alert("Request timeout saat mendapatkan lokasi."); break;
        case error.UNKNOWN_ERROR: alert("Terjadi kesalahan yang tidak diketahui."); break;
    }
}

// Map Picker Logic
var mapPicker;
var markerPicker;
var circlePicker;
var initLat = -6.2088;
var initLng = 106.8456;

$(document).ready(function(){
    var initRadius = $("#radius_meter").val() ? parseInt($("#radius_meter").val()) : 50;

    // Initialize Map
    mapPicker = L.map('map_picker').setView([initLat, initLng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapPicker);
    
    markerPicker = L.marker([initLat, initLng], {draggable: true}).addTo(mapPicker);
    
    circlePicker = L.circle([initLat, initLng], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.2,
        radius: initRadius
    }).addTo(mapPicker);
    
    // Update inputs when marker is dragged
    markerPicker.on('dragend', function(event){
        var marker = event.target;
        var position = marker.getLatLng();
        $("#lat").val(position.lat);
        $("#lng").val(position.lng);
        circlePicker.setLatLng(position);
        mapPicker.panTo(new L.LatLng(position.lat, position.lng));
    });

    // Update marker when inputs change
    $("#lat, #lng").on('input change', function() {
        var newLat = $("#lat").val();
        var newLng = $("#lng").val();
        if(newLat && newLng) {
            var newLatLng = new L.LatLng(newLat, newLng);
            markerPicker.setLatLng(newLatLng);
            circlePicker.setLatLng(newLatLng);
            mapPicker.panTo(newLatLng);
        }
    });

    // Update radius when input changes
    $("#radius_meter").on('input change', function() {
        var newRad = parseInt($(this).val());
        if(!isNaN(newRad) && newRad > 0) {
            circlePicker.setRadius(newRad);
        }
    });
});
</script>
