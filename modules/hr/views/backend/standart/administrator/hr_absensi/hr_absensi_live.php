<style>
    /* Premium Modern Aesthetics for Live Absensi */
    :root {
        --primary: #4F46E5;
        --primary-light: #EEF2FF;
        --success: #10B981;
        --success-hover: #059669;
        --warning: #F59E0B;
        --danger: #EF4444;
        --dark: #1E293B;
        --gray: #64748B;
        --light: #F8FAFC;
        --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        --transition: all 0.3s ease;
    }

    .content-header h1 { font-weight: 700; color: var(--dark); font-size: 24px; }
    
    .modern-card {
        background: #fff;
        border-radius: 20px;
        border: none;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 25px;
    }

    /* Camera Section */
    .camera-wrapper {
        position: relative;
        width: 100%;
        background: #000;
        border-radius: 20px 20px 0 0;
        overflow: hidden;
        aspect-ratio: 16/9;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #video_cam {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scaleX(-1);
    }
    
    /* Action Area */
    .action-area {
        padding: 30px;
        background: #fff;
        text-align: center;
    }

    .btn-absen {
        padding: 15px 30px;
        font-size: 16px;
        font-weight: 700;
        border-radius: 50px;
        width: 100%;
        transition: var(--transition);
        border: none;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .btn-masuk { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; }
    .btn-masuk:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); color: white;}
    
    .btn-pulang { background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); color: white; }
    .btn-pulang:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3); color: white;}
    
    .btn-izin { 
        background: transparent; 
        color: var(--gray); 
        border: none;
        box-shadow: none;
        margin-top: 15px;
        text-transform: none;
        font-weight: 600;
        font-size: 14px;
        padding: 10px;
    }
    .btn-izin:hover { color: var(--warning); background: transparent; text-decoration: underline; box-shadow: none; transform: none;}

    .modern-alert {
        border-radius: 12px;
        padding: 15px 20px;
        border: none;
        display: flex;
        flex-direction: column;
        text-align: left;
        margin-bottom: 20px;
    }
    .modern-alert-warning { background: #FFFBEB; border-left: 4px solid var(--warning); color: #B45309; }
    .modern-alert-warning label { font-size: 14px; font-weight: 700; color: #92400E; margin-bottom: 8px;}
    .modern-alert-warning textarea { border: 1px solid #FDE68A; border-radius: 8px; box-shadow: none; font-size: 13px;}
    .modern-alert-warning textarea:focus { border-color: var(--warning); }
    
    .modern-alert-info { background: var(--primary-light); border-left: 4px solid var(--primary); color: #3730A3; }
    .modern-alert-success { background: #ECFDF5; border-left: 4px solid var(--success); color: #065F46; }

    /* Profile Panel */
    .profile-glass-card {
        background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%);
        color: white;
        padding: 30px 25px;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        margin-bottom: 25px;
    }
    .profile-glass-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: url('https://www.transparenttextures.com/patterns/cubes.png');
        opacity: 0.1;
    }
    .profile-info { position: relative; z-index: 10; }
    .profile-info h3 { margin: 0 0 5px 0; font-weight: 700; font-size: 22px; }
    .badge-tipe { background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; backdrop-filter: blur(5px); display: inline-block; margin-bottom: 15px;}
    
    .time-info { background: rgba(0,0,0,0.15); border-radius: 12px; padding: 12px 15px; display: flex; align-items: center; gap: 10px; backdrop-filter: blur(5px); }
    .time-info i { font-size: 20px; color: #A7F3D0; }
    .time-info div { line-height: 1.2; }
    .time-info span { font-size: 11px; opacity: 0.8; display: block;}
    .time-info strong { font-size: 15px; }

    /* Location Panel */
    .location-panel {
        background: #fff;
        border-radius: 20px;
        padding: 25px;
        box-shadow: var(--card-shadow);
        margin-bottom: 25px;
    }
    .loc-header { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;}
    .loc-header i { background: #FEE2E2; color: var(--danger); width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 16px; }
    .loc-header h4 { margin: 0; font-weight: 700; font-size: 15px; color: var(--dark); }
    .loc-header p { margin: 0; font-size: 12px; color: var(--gray); }
    
    .status-gps { padding: 10px 15px; border-radius: 10px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; margin-bottom: 15px;}
    .status-gps.loading { background: #FEF3C7; color: #B45309; }
    .status-gps.success { background: #ECFDF5; color: #059669; }
    .status-gps.error { background: #FEE2E2; color: #DC2626; }
    
    .map-container {
        border-radius: 12px;
        overflow: hidden;
        height: 200px;
        border: 2px solid #f1f5f9;
        position: relative;
    }
    #distance_info {
        position: absolute;
        bottom: 10px; left: 50%;
        transform: translateX(-50%);
        background: rgba(255,255,255,0.95);
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 1000;
        white-space: nowrap;
    }

    /* Modal Form Izin */
    .modal-content { border-radius: 20px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
    .modal-header { border-bottom: 1px solid #f1f5f9; padding: 20px 25px; border-radius: 20px 20px 0 0; }
    .modal-title { font-weight: 700; color: var(--dark); font-size: 18px; }
    .modal-body { padding: 25px; }
    .modal-footer { border-top: 1px solid #f1f5f9; padding: 15px 25px; }
</style>

<section class="content-header">
   <h1>Live Absensi <small style="color:#777; font-weight:400;">Perekaman Kehadiran Real-time</small></h1>
</section>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- FingerprintJS v4 Open Source - Akurat tanpa localStorage -->
<script>
const fpScript = document.createElement('script');
fpScript.src = 'https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@4/dist/fp.min.js';
fpScript.async = true;
document.head.appendChild(fpScript);
</script>

<section class="content">
   <div class="row">
      <!-- KOLOM KIRI: Kamera & Aksi -->
      <div class="col-md-6">
         <div class="modern-card">
            <div class="camera-wrapper">
                <video id="video_cam" autoplay playsinline></video>
                <canvas id="canvas" style="display:none;"></canvas>
            </div>

            <div class="action-area">
                <?php if($absen_hari_ini && in_array($absen_hari_ini->status_kehadiran, ['Izin', 'Izin Khusus', 'Sakit (Dengan Surat)', 'Sakit (Tanpa Surat)', 'Cuti'])): ?>
                    <div class="modern-alert modern-alert-info text-center">
                        <i class="fa fa-envelope-open-o fa-3x" style="margin-bottom:15px; color:var(--primary);"></i>
                        <h4 style="margin:0 0 5px 0; font-weight:700;">Status: <?= $absen_hari_ini->status_kehadiran; ?></h4>
                        <p style="margin:0; font-size:13px; opacity:0.8;">Anda telah mengajukan <?= $absen_hari_ini->status_kehadiran; ?> untuk hari ini. Anda tidak perlu absen masuk.</p>
                    </div>
                <?php elseif(!$absen_hari_ini || !$absen_hari_ini->waktu_masuk): ?>
                    
                    <?php 
                    $is_late = false;
                    $jam_sekarang = date('H:i:s');
                    $batas_terlambat = date('H:i:s', strtotime(($shift_info ? $shift_info->jam_masuk : $pengaturan->jam_masuk) . ' + ' . ($pengaturan->toleransi_terlambat_menit ?? 15) . ' minutes'));
                    if ($jam_sekarang > $batas_terlambat) {
                        $is_late = true;
                    }
                    ?>
                    
                    <?php if($is_late): ?>
                    <div class="modern-alert modern-alert-warning">
                        <label><i class="fa fa-exclamation-triangle margin-r-5"></i> Anda Terlambat!</label>
                        <textarea id="alasan_terlambat" class="form-control" rows="2" placeholder="Wajib mengisi alasan keterlambatan..." style="background: rgba(255,255,255,0.7);"></textarea>
                    </div>
                    <?php endif; ?>

                    <button class="btn btn-absen btn-masuk absen-btn" onclick="takeSnapshot('masuk', this)">
                        <i class="fa fa-sign-in" style="font-size:20px;"></i> Absen Masuk
                    </button>
                    
                    <button class="btn btn-absen btn-izin absen-btn" onclick="bukaModalIzin()">
                        Tidak bisa hadir? <span style="color:var(--warning);">Ajukan Izin / Sakit disini</span>
                    </button>

                <?php elseif(!$absen_hari_ini->waktu_pulang): ?>
                    <div class="modern-alert modern-alert-success text-center">
                        <i class="fa fa-check-circle" style="font-size:24px; margin-bottom:5px;"></i><br>
                        Berhasil absen masuk pada <strong><?= date('H:i', strtotime($absen_hari_ini->waktu_masuk)); ?></strong>
                    </div>
                    <button class="btn btn-absen btn-pulang absen-btn" onclick="takeSnapshot('pulang', this)">
                        <i class="fa fa-sign-out" style="font-size:20px;"></i> Absen Pulang
                    </button>
                <?php else: ?>
                    <div class="modern-alert modern-alert-success text-center" style="background:#EEF2FF; border-left-color:var(--primary); color:#3730A3;">
                        <i class="fa fa-thumbs-up fa-3x" style="margin-bottom:15px; color:var(--primary);"></i>
                        <h4 style="margin:0 0 5px 0; font-weight:700;">Absensi Selesai!</h4>
                        <p style="margin:0; font-size:13px;">Anda sudah menyelesaikan absensi masuk dan pulang hari ini. Terima kasih atas kerja kerasnya!</p>
                    </div>
                <?php endif; ?>
            </div>
         </div>
      </div>

      <!-- KOLOM KANAN: Profil & Map -->
      <div class="col-md-6">
         
         <div class="profile-glass-card">
            <div class="profile-info">
                <h3><?= _ent($karyawan->full_name ?? get_user_data('full_name')); ?></h3>
                <div class="badge-tipe"><i class="fa fa-laptop margin-r-5"></i> <?= $karyawan->tipe_kerja; ?></div>
                
                <div class="time-info">
                    <i class="fa fa-clock-o"></i>
                    <div>
                        <span>Jadwal Jam Kerja</span>
                        <strong><?= substr($shift_info ? $shift_info->jam_masuk : $pengaturan->jam_masuk, 0, 5); ?> - <?= substr($shift_info ? $shift_info->jam_pulang : $pengaturan->jam_pulang, 0, 5); ?> WIB</strong>
                    </div>
                </div>
            </div>
         </div>

         <div class="location-panel">
            <div class="loc-header">
                <i class="fa fa-building"></i>
                <div>
                    <h4><?= isset($kantor_info) && $kantor_info ? _ent($kantor_info->nama_kantor) : 'Kantor Pusat' ?></h4>
                    <p>Lokasi Absensi Utama (Radius: <?= isset($kantor_info) && $kantor_info ? $kantor_info->radius_meter : '50' ?>m)</p>
                </div>
            </div>

            <div id="location_status" class="status-gps loading">
                <i class="fa fa-spinner fa-spin"></i> Mendapatkan kordinat GPS Anda...
            </div>

            <div class="map-container">
                <div id="live_map" style="height: 100%; width: 100%;"></div>
                <div id="distance_info">
                    <i class="fa fa-map margin-r-5"></i> Memuat Peta...
                </div>
            </div>
         </div>

         <!-- Panel Device Fingerprint (Hanya tampil untuk Admin/HRD) -->
         <?php if (!empty($is_admin)): ?>
         <div class="modern-card" style="padding:20px; font-size:12px;">
             <h4 style="margin:0 0 15px 0; color:var(--dark); font-weight:700; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">
                 <i class="fa fa-shield text-primary margin-r-5"></i> Device Fingerprint
                 <span id="fp_status_badge" class="label label-warning pull-right" style="font-size:10px;"><i class="fa fa-spinner fa-spin"></i></span>
             </h4>
             <table style="width:100%; border-collapse:collapse;">
                 <tr style="border-bottom:1px solid #f5f5f5;">
                     <td style="padding:6px 0; color:var(--gray); width:35%;">Device ID</td>
                     <td style="padding:6px 0; word-break:break-all;"><code id="fp_id" style="background:#f1f5f9; color:var(--dark); border-radius:4px; padding:2px 5px;">Menganalisa...</code></td>
                 </tr>
                 <tr style="border-bottom:1px solid #f5f5f5;"><td style="padding:6px 0; color:var(--gray);">OS / Platform</td><td id="fp_os" style="padding:6px 0; font-weight:600;">-</td></tr>
                 <tr style="border-bottom:1px solid #f5f5f5;"><td style="padding:6px 0; color:var(--gray);">Layar / Resolusi</td><td id="fp_screen" style="padding:6px 0; font-weight:600;">-</td></tr>
                 <tr><td style="padding:6px 0; color:var(--gray);">GPU Engine</td><td id="fp_gpu" style="padding:6px 0; font-weight:600;">-</td></tr>
             </table>
         </div>
         <?php else: ?>
         <span id="fp_id" style="display:none;"></span><span id="fp_status_badge" style="display:none;"></span><span id="fp_os" style="display:none;"></span><span id="fp_screen" style="display:none;"></span><span id="fp_hw" style="display:none;"></span><span id="fp_gpu" style="display:none;"></span><span id="fp_audio" style="display:none;"></span><span id="fp_tz" style="display:none;"></span><span id="fp_touch" style="display:none;"></span>
         <?php endif; ?>

      </div>
   </div>
</section>

<!-- Modal Izin / Sakit -->
<div class="modal fade" id="modalIzin" tabindex="-1" role="dialog" aria-labelledby="modalIzinLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modalIzinLabel"><i class="fa fa-envelope-open-o text-warning margin-r-5"></i> Form Pengajuan Izin & Sakit</h4>
      </div>
      <div class="modal-body">
        <form id="form_izin_modal" enctype="multipart/form-data">
        <div class="form-group">
            <label style="font-weight:600;">Jenis Pengajuan <span class="text-danger">*</span></label>
            <select id="input_jenis_izin" name="jenis_izin" class="form-control" style="border-radius:10px; height:40px;" onchange="toggleSuratDokter()">
                <option value="Izin">Izin (Keperluan Pribadi / Mendadak)</option>
                <option value="Izin Khusus">Izin Khusus (Menikah, Kedukaan, dll)</option>
                <option value="Sakit (Dengan Surat)">Sakit (Dengan Surat Dokter)</option>
                <option value="Sakit (Tanpa Surat)">Sakit (Tanpa Surat)</option>
                <option value="Cuti">Cuti Tahunan</option>
            </select>
        </div>
        <div class="form-group" id="container_surat_dokter" style="display:none; margin-top:15px; padding:15px; background:#FFFBEB; border-radius:10px; border:1px dashed #FCD34D;">
            <label style="font-weight:600; color:#B45309;"><i class="fa fa-upload margin-r-5"></i> Upload Surat Dokter <span class="text-danger">*</span></label>
            <input type="file" id="input_file_surat" name="file_surat" class="form-control" accept="image/*" style="border-radius:8px; border:none; padding-left:0; background:transparent;">
            <small style="color:#B45309; display:block; margin-top:5px;">Format yang didukung: JPG, PNG. Dokumen PDF tidak diizinkan.</small>
        </div>
        <div class="form-group" style="margin-top:15px;">
            <label style="font-weight:600;">Alasan Detail <span class="text-danger">*</span></label>
            <textarea id="input_alasan_izin" name="alasan" class="form-control" rows="4" style="border-radius:10px; resize:none;" placeholder="Jelaskan secara singkat namun jelas..."></textarea>
        </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" style="border-radius:20px; font-weight:600; padding:8px 20px;" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-warning" style="border-radius:20px; font-weight:700; padding:8px 25px; box-shadow:0 4px 10px rgba(245, 158, 11, 0.3);" id="btn_submit_izin" onclick="submitIzin()"><i class="fa fa-paper-plane margin-r-5"></i> Kirim Pengajuan</button>
      </div>
    </div>
  </div>
</div>

<script>
window.isWFO = <?= $karyawan->tipe_kerja == 'WFO' ? 'true' : 'false' ?>;
window.officeLat = <?= isset($kantor_info) && $kantor_info && $kantor_info->latitude ? $kantor_info->latitude : '-7.5754887' ?>;
window.officeLng = <?= isset($kantor_info) && $kantor_info && $kantor_info->longitude ? $kantor_info->longitude : '110.8243272' ?>;
window.officeName = "<?= isset($kantor_info) && $kantor_info ? _ent($kantor_info->nama_kantor) : 'Kantor Pusat' ?>";
window.officeRadius = <?= isset($kantor_info) && $kantor_info ? $kantor_info->radius_meter : '50' ?>;

// [Fingerprint Logic is the same...]
async function getCustomSignals() {
    const components = [];
    components.push(navigator.hardwareConcurrency || 0);
    components.push(navigator.deviceMemory || 0);
    components.push(screen.width + 'x' + screen.height + '@' + (window.devicePixelRatio || 1));
    components.push(Intl.DateTimeFormat().resolvedOptions().timeZone || '');
    try {
        const c = document.createElement('canvas');
        const gl = c.getContext('webgl') || c.getContext('experimental-webgl');
        if (gl) {
            components.push(gl.getParameter(gl.VENDOR));
            const dbg = gl.getExtension('WEBGL_debug_renderer_info');
            if (dbg) { components.push(gl.getParameter(dbg.UNMASKED_RENDERER_WEBGL)); }
        }
    } catch(e) { components.push('webgl_na'); }
    components.push('touch:' + navigator.maxTouchPoints);
    return components.join('|||');
}

async function getBrowserFingerprint() {
    try {
        if (window.FingerprintJS) {
            const fp = await window.FingerprintJS.load();
            const result = await fp.get();
            const customSig = await getCustomSignals();
            const combined = result.visitorId + '|||' + customSig;
            const msgBuffer = new TextEncoder().encode(combined);
            const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
            const hashArray  = Array.from(new Uint8Array(hashBuffer));
            return 'fpv4_' + hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
        }
    } catch(e) { }
    const customSig = await getCustomSignals();
    const msgBuffer = new TextEncoder().encode(customSig);
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
    const hashArray  = Array.from(new Uint8Array(hashBuffer));
    return 'fpx_' + hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
}

let device_uuid = 'fp_generating...';

function updateFingerprintPanel(fp) {
    const el_id = document.getElementById('fp_id'); if (el_id) el_id.textContent = fp;
    const badge = document.getElementById('fp_status_badge');
    if (badge) {
        badge.className = 'label label-success pull-right';
        badge.innerHTML = '<i class="fa fa-check"></i> Secured';
    }
    const el_os = document.getElementById('fp_os'); if (el_os) el_os.textContent = navigator.platform;
    const el_screen = document.getElementById('fp_screen'); if (el_screen) el_screen.textContent = screen.width + ' × ' + screen.height;
    const el_gpu = document.getElementById('fp_gpu');
    if (el_gpu) {
        try {
            const c = document.createElement('canvas'); const gl = c.getContext('webgl');
            if (gl) {
                const dbg = gl.getExtension('WEBGL_debug_renderer_info');
                el_gpu.textContent = dbg ? gl.getParameter(dbg.UNMASKED_RENDERER_WEBGL) : gl.getParameter(gl.RENDERER);
            } else { el_gpu.textContent = 'N/A'; }
        } catch(e) { el_gpu.textContent = 'Error'; }
    }
}

getBrowserFingerprint().then(fp => {
    device_uuid = fp;
    updateFingerprintPanel(fp);
    saveFingerprintToServer(fp);
});

async function saveFingerprintToServer(fp) {
    let gpu_info = 'N/A';
    try {
        const gl = document.createElement('canvas').getContext('webgl');
        if (gl) { const dbg = gl.getExtension('WEBGL_debug_renderer_info'); if(dbg) gpu_info = gl.getParameter(dbg.UNMASKED_RENDERER_WEBGL); }
    } catch(e){}

    $.ajax({
        url: '<?= site_url("administrator/hr_device/save_fingerprint") ?>',
        type: 'POST', dataType: 'json',
        data: {
            device_id: fp, os_platform: navigator.platform, screen_info: screen.width+'x'+screen.height,
            gpu_info: gpu_info, cpu_cores: navigator.hardwareConcurrency||0, ram_gb: navigator.deviceMemory||0,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone, is_touchscreen: (navigator.maxTouchPoints||0)>0?1:0,
            browser_info: navigator.userAgent.substring(0, 100)
        },
        success: function(res) {
            if (res && res.status === 'dicabut') {
                $('.absen-btn').prop('disabled', true);
                $('.action-area').prepend('<div class="modern-alert modern-alert-warning"><i class="fa fa-ban fa-2x margin-bottom"></i><strong>Perangkat Diblokir!</strong><p>Akses ditutup oleh Admin.</p></div>');
            }
        }
    });
}

let current_lat = null;
let current_lng = null;
const video = document.getElementById('video_cam');
const canvas = document.getElementById('canvas');

async function startCamera() {
    try {
        if (navigator.mediaDevices === undefined) { navigator.mediaDevices = {}; }
        if (navigator.mediaDevices.getUserMedia === undefined) {
            navigator.mediaDevices.getUserMedia = function(c) {
                var g = navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
                if (!g) return Promise.reject(new Error('Kamera tidak disupport'));
                return new Promise(function(r, j) { g.call(navigator, c, r, j); });
            }
        }
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user", width: { ideal: 640 }, height: { ideal: 480 } }, audio: false });
        video.srcObject = stream;
        video.onloadedmetadata = function(e) { video.play(); };
    } catch (err) {
        console.error(err);
        $('.camera-wrapper').html('<div style="color:white; text-align:center; padding:50px;"><i class="fa fa-video-camera fa-3x" style="margin-bottom:15px; opacity:0.5;"></i><br>Kamera tidak terdeteksi / izin ditolak.</div>');
    }
}
startCamera();

try {
    window.liveMap = L.map('live_map', { zoomControl: false }).setView([window.officeLat, window.officeLng], 16);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(window.liveMap);
    window.officeMarker = L.circleMarker([window.officeLat, window.officeLng], {
        radius: 12, fillColor: '#EF4444', color: '#ffffff', weight: 3, fillOpacity: 1
    }).addTo(window.liveMap).bindTooltip("<b>" + window.officeName + "</b>", {permanent: true, direction: 'top', offset: [0, -10]}).openTooltip();
    window.officeCircle = L.circle([window.officeLat, window.officeLng], {
        color: '#EF4444', fillColor: '#EF4444', fillOpacity: 0.15, radius: window.officeRadius
    }).addTo(window.liveMap);
    setTimeout(function() { if(window.liveMap) window.liveMap.invalidateSize(); }, 500);
    setTimeout(function() { if(window.liveMap) window.liveMap.invalidateSize(); }, 1500);
} catch(e) { }

if (navigator.geolocation) {
    let firstLocationUpdate = true;
    navigator.geolocation.watchPosition(function(pos){
        current_lat = pos.coords.latitude; current_lng = pos.coords.longitude;
        
        let locStat = document.getElementById("location_status");
        locStat.className = "status-gps success";
        locStat.innerHTML = '<i class="fa fa-check-circle"></i> Sinyal GPS Sangat Baik';

        if (!window.userMarker && window.liveMap) {
            window.userMarker = L.circleMarker([current_lat, current_lng], {
                radius: 10, fillColor: '#3B82F6', color: '#ffffff', weight: 3, fillOpacity: 1
            }).addTo(window.liveMap).bindPopup("Lokasi Anda");
        } else if (window.userMarker) {
            window.userMarker.setLatLng([current_lat, current_lng]);
        }

        if(window.liveMap && firstLocationUpdate) {
            window.liveMap.fitBounds(L.latLngBounds([ [current_lat, current_lng], [window.officeLat, window.officeLng] ]).pad(0.3));
            firstLocationUpdate = false;
            if (device_uuid && device_uuid !== 'fp_generating...') {
                $.post('<?= site_url("administrator/hr_device/save_fingerprint") ?>', { device_id: device_uuid, lat: current_lat, lng: current_lng });
            }
        }

        var distance = Math.round(L.latLng(current_lat, current_lng).distanceTo(L.latLng(window.officeLat, window.officeLng)));
        var distInfo = document.getElementById("distance_info");

        if (window.isWFO) {
            if (distance > window.officeRadius) {
                distInfo.innerHTML = '<span style="color:var(--danger);"><i class="fa fa-ban margin-r-5"></i> Luar Zona (' + distance + 'm)</span>';
                if(window.jQuery) { $(".btn-absen:not(.btn-izin)").prop('disabled', true); }
            } else {
                distInfo.innerHTML = '<span style="color:var(--success);"><i class="fa fa-check-circle margin-r-5"></i> Dalam Zona (' + distance + 'm)</span>';
                if(window.jQuery) { $(".btn-absen:not(.btn-izin)").prop('disabled', false); }
            }
        } else {
            distInfo.innerHTML = '<span style="color:var(--primary);"><i class="fa fa-globe margin-r-5"></i> WFH / WFA Mode</span>';
        }
    }, function(err){
        let locStat = document.getElementById("location_status");
        locStat.className = "status-gps error";
        locStat.innerHTML = '<i class="fa fa-times-circle"></i> Sinyal GPS Terputus / Izin Ditolak';
    }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
}

async function takeSnapshot(jenis, btnEl) {
    if (window.isWFO && (current_lat == null || current_lng == null)) {
        alert("Harap tunggu hingga lokasi GPS stabil!"); return;
    }
    if(video.videoWidth === 0) { alert("Kamera tidak menyala."); return; }

    canvas.width = video.videoWidth; canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
    const dataURL = canvas.toDataURL('image/jpeg', 0.8);
    
    var btn = $(btnEl); var originalText = btn.html();
    var alasan = $("#alasan_terlambat").length ? $("#alasan_terlambat").val() : "";
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin margin-r-5"></i> Sinkronisasi...');

    const fp = await getBrowserFingerprint();

    $.ajax({
        url: '<?= site_url("administrator/hr_absensi/process_absen") ?>',
        type: 'POST', dataType: 'json',
        data: { jenis: jenis, lat: current_lat, lng: current_lng, foto: dataURL, device_id: fp, alasan_terlambat: alasan },
        success: function(res) {
            if(res.success) {
                swal({ title: "Berhasil!", text: res.message, type: "success" }, function(){ location.reload(); });
            } else {
                swal("Gagal!", res.message, "error");
                btn.prop('disabled', false).html(originalText);
            }
        },
        error: function() {
            swal("Error", "Server sedang sibuk.", "error");
            btn.prop('disabled', false).html(originalText);
        }
    });
}

function bukaModalIzin() { $("#modalIzin").modal('show'); }
function toggleSuratDokter() {
    if($("#input_jenis_izin").val() === 'Sakit (Dengan Surat)') { $("#container_surat_dokter").slideDown(); } 
    else { $("#container_surat_dokter").slideUp(); $("#input_file_surat").val(''); }
}
function submitIzin() {
    var jenis_izin = $("#input_jenis_izin").val();
    var alasan = $("#input_alasan_izin").val();
    if(alasan.trim() === '') { alert("Alasan izin/sakit wajib diisi detail!"); return; }
    if (jenis_izin === 'Sakit (Dengan Surat)' && $("#input_file_surat")[0].files.length === 0) {
        alert("Harap lampirkan Surat Dokter terlebih dahulu!"); return;
    }

    var btn = $("#btn_submit_izin"); var originalText = btn.html();
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin margin-r-5"></i> Mengirim...');

    var formData = new FormData($("#form_izin_modal")[0]);
    $.ajax({
        url: '<?= site_url("administrator/hr_absensi/process_izin") ?>',
        type: 'POST', data: formData, processData: false, contentType: false, dataType: 'json',
        success: function(res) {
            if(res.success) {
                swal({ title: "Pengajuan Terkirim!", text: res.message, type: "success" }, function(){ location.reload(); });
            } else {
                swal("Gagal!", res.message, "error"); btn.prop('disabled', false).html(originalText);
            }
        },
        error: function() { swal("Error", "Gagal mengirim data.", "error"); btn.prop('disabled', false).html(originalText); }
    });
}
</script>
