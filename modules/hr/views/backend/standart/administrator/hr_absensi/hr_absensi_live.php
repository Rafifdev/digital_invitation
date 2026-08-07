
<style>
.camera-box { border-radius: 10px; overflow: hidden; background: #000; position: relative; width: 100%; max-width: 400px; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
#video_cam { width: 100%; height: auto; transform: scaleX(-1); display: block; }
.absen-btn { padding: 12px 25px; font-size: 16px; border-radius: 25px; font-weight: bold; width: 100%; max-width: 200px; margin: 10px auto; display: block; }
.info-card { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; text-align: center; }
</style>

<section class="content-header">
   <h1><i class="fa fa-camera text-primary"></i> Live Absensi</h1>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-6 col-md-offset-3">
         <div class="info-card">
            <h4>Halo, <strong><?= _ent($karyawan->full_name ?? get_user_data('full_name')); ?></strong></h4>
            <p class="text-muted">Tipe Kerja: <strong><span class="label label-info"><?= $karyawan->tipe_kerja; ?></span></strong></p>
            <hr style="margin: 10px 0;">
            <p><i class="fa fa-clock-o text-success"></i> Jam Masuk: <strong><?= substr($pengaturan->jam_masuk,0,5); ?></strong> | <i class="fa fa-sign-out text-danger"></i> Jam Pulang: <strong><?= substr($pengaturan->jam_pulang,0,5); ?></strong></p>
            <p id="location_status" class="text-warning"><i class="fa fa-spinner fa-spin"></i> Mendapatkan lokasi GPS Anda...</p>
         </div>

         <div class="camera-box text-center">
            <video id="video_cam" autoplay playsinline></video>
            <canvas id="canvas" style="display:none;"></canvas>
         </div>

         <div class="text-center" style="margin-top:20px;">
            <?php if(!$absen_hari_ini || !$absen_hari_ini->waktu_masuk): ?>
                <button class="btn btn-success absen-btn" onclick="takeSnapshot('masuk')"><i class="fa fa-sign-in"></i> Absen Masuk</button>
            <?php elseif(!$absen_hari_ini->waktu_pulang): ?>
                <div class="alert alert-success">Anda sudah absen masuk pada pukul <strong><?= date('H:i', strtotime($absen_hari_ini->waktu_masuk)); ?></strong></div>
                <button class="btn btn-danger absen-btn" onclick="takeSnapshot('pulang')"><i class="fa fa-sign-out"></i> Absen Pulang</button>
            <?php else: ?>
                <div class="alert alert-info"><i class="fa fa-check-circle fa-2x"></i><br>Anda sudah menyelesaikan absensi hari ini. Terima kasih!</div>
            <?php endif; ?>
         </div>
      </div>
   </div>
</section>

<script>
// Generate or Get Device UUID for Device Lock
function getDeviceUUID() {
    let uuid = localStorage.getItem('device_uuid');
    if (!uuid) {
        uuid = 'dev_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
        localStorage.setItem('device_uuid', uuid);
    }
    return uuid;
}

const device_uuid = getDeviceUUID();
let current_lat = null;
let current_lng = null;

// Init Camera
const video = document.getElementById('video_cam');
const canvas = document.getElementById('canvas');

if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
    .then(function(stream) {
        video.srcObject = stream;
    })
    .catch(function(err) {
        alert("Gagal mengakses kamera: " + err);
    });
} else {
    alert("Kamera tidak didukung di browser ini.");
}

// Get Location
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos){
        current_lat = pos.coords.latitude;
        current_lng = pos.coords.longitude;
        $("#location_status").html('<i class="fa fa-check-circle text-success"></i> Lokasi GPS berhasil didapatkan.');
    }, function(err){
        $("#location_status").html('<i class="fa fa-times-circle text-danger"></i> Gagal mendapatkan lokasi GPS. Pastikan izin lokasi aktif!');
    }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
}

function takeSnapshot(jenis) {
    if (<?= $karyawan->tipe_kerja == 'WFO' ? 'true' : 'false' ?> && (current_lat == null || current_lng == null)) {
        alert("Harap tunggu hingga lokasi GPS berhasil didapatkan, atau pastikan GPS/Location Anda menyala!");
        return;
    }

    // Set canvas dimensions to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw the current video frame onto the canvas
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Get base64 image
    const dataURL = canvas.toDataURL('image/jpeg', 0.8);
    
    // Send via AJAX
    var btn = $(".absen-btn");
    var originalText = btn.html();
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');

    $.ajax({
        url: '<?= site_url("administrator/hr_absensi/process_absen") ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            jenis_absen: jenis,
            lat: current_lat,
            lng: current_lng,
            foto: dataURL,
            device_id: device_uuid
        },
        success: function(res) {
            if(res.success) {
                swal({ title: "Berhasil!", text: res.message, type: "success" }, function(){
                    location.reload();
                });
            } else {
                swal("Gagal!", res.message, "error");
                btn.prop('disabled', false).html(originalText);
            }
        },
        error: function() {
            swal("Error", "Terjadi kesalahan server.", "error");
            btn.prop('disabled', false).html(originalText);
        }
    });
}
</script>
