<style>
    /* Premium Modern Aesthetics for Detail Absensi */
    :root {
        --primary: #4F46E5;
        --primary-light: #EEF2FF;
        --success: #10B981;
        --success-hover: #059669;
        --warning: #F59E0B;
        --danger: #EF4444;
        --dark: #1F2937;
        --gray: #6B7280;
        --light: #F3F4F6;
        --white: #FFFFFF;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    body {
        background-color: #F8FAFC;
    }

    .detail-header-card {
        background: linear-gradient(135deg, var(--primary) 0%, #6366F1 100%);
        border-radius: 16px;
        padding: 30px;
        color: white;
        margin-bottom: 25px;
        box-shadow: var(--shadow-md);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .detail-header-card h2 {
        margin: 0;
        font-weight: 700;
        font-size: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .detail-header-card .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
    }

    .detail-header-card .btn-back:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    .content-wrapper-premium {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    @media (max-width: 991px) {
        .content-wrapper-premium {
            grid-template-columns: 1fr;
        }
    }

    .premium-card {
        background: var(--white);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        padding: 25px;
        border: 1px solid rgba(229, 231, 235, 0.5);
        transition: all 0.3s ease;
    }

    .premium-card:hover {
        box-shadow: var(--shadow-md);
    }

    .card-title-premium {
        font-size: 18px;
        font-weight: 700;
        color: var(--dark);
        margin-top: 0;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--light);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .info-item:last-child {
        margin-bottom: 0;
    }

    .info-label {
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 600;
        color: var(--gray);
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 500;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-hadir { background: #D1FAE5; color: #065F46; }
    .status-telat { background: #FEE2E2; color: #991B1B; }
    .status-sakit { background: #FEF3C7; color: #92400E; }
    .status-izin { background: #DBEAFE; color: #1E40AF; }
    .status-cuti { background: #E0E7FF; color: #3730A3; }

    .alasan-box {
        background: #FEF9C3;
        border-left: 4px solid var(--warning);
        padding: 15px;
        border-radius: 8px;
        color: #854D0E;
        font-style: italic;
        font-size: 14px;
        margin-top: 5px;
    }

    .media-box {
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid var(--light);
        position: relative;
        background: var(--light);
        height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .media-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .media-box:hover img {
        transform: scale(1.05);
    }

    .media-box .overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .media-box:hover .overlay {
        opacity: 1;
    }

    .media-empty {
        text-align: center;
        color: var(--gray);
    }

    .media-empty i {
        font-size: 32px;
        margin-bottom: 10px;
        opacity: 0.5;
    }

    #map_detail {
        width: 100%;
        height: 100%;
        z-index: 1;
    }
</style>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<section class="content" style="padding-top: 20px;">
    
    <!-- Premium Header -->
    <div class="detail-header-card">
        <div>
            <h2 style="font-family: 'Inter', sans-serif;"><i class="fa fa-user-circle"></i> Detail Laporan: <?= _ent($laporan->username); ?></h2>
            <div style="opacity: 0.8; margin-top: 8px; font-size: 14px;">
                <i class="fa fa-calendar margin-r-5"></i> Log Kehadiran Tanggal <?= date('d F Y', strtotime($laporan->tanggal)); ?>
            </div>
        </div>
        <a href="<?= site_url('administrator/hr_laporan'); ?>" class="btn-back">
            <i class="fa fa-arrow-left margin-r-5"></i> Kembali
        </a>
    </div>

    <div class="content-wrapper-premium">
        
        <!-- Kolom Kiri: Info Text -->
        <div class="premium-card">
            <h3 class="card-title-premium"><i class="fa fa-list-alt text-primary"></i> Rincian Informasi</h3>
            
            <ul class="info-list">
                <div class="row">
                    <div class="col-xs-6">
                        <li class="info-item">
                            <span class="info-label">Tipe Pekerjaan</span>
                            <span class="info-value">
                                <?php if($laporan->tipe_kerja == 'WFO'): ?>
                                    <i class="fa fa-building text-info"></i> WFO (Kantor)
                                <?php elseif($laporan->tipe_kerja == 'WFH'): ?>
                                    <i class="fa fa-home text-warning"></i> WFH (Remote)
                                <?php else: ?>
                                    <i class="fa fa-globe text-success"></i> WFA (Anywhere)
                                <?php endif; ?>
                            </span>
                        </li>
                    </div>
                    <div class="col-xs-6">
                        <li class="info-item">
                            <span class="info-label">Jam Masuk</span>
                            <span class="info-value">
                                <i class="fa fa-clock-o <?= $laporan->waktu_masuk ? 'text-success' : 'text-muted' ?>"></i> 
                                <?= $laporan->waktu_masuk ? date('H:i:s', strtotime($laporan->waktu_masuk)) : '-'; ?>
                            </span>
                        </li>
                    </div>
                </div>

                <li class="info-item" style="margin-top:20px;">
                    <span class="info-label">Status Kehadiran</span>
                    <div class="info-value">
                        <?php 
                            $status_class = 'status-izin';
                            if(in_array($laporan->status_kehadiran, ['Hadir', 'Tepat Waktu'])) $status_class = 'status-hadir';
                            if(in_array($laporan->status_kehadiran, ['Telat', 'Terlambat'])) $status_class = 'status-telat';
                            if(strpos($laporan->status_kehadiran, 'Sakit') !== false) $status_class = 'status-sakit';
                            if($laporan->status_kehadiran == 'Cuti') $status_class = 'status-cuti';
                        ?>
                        <span class="status-badge <?= $status_class; ?>">
                            <?= _ent($laporan->status_kehadiran); ?>
                        </span>
                    </div>
                </li>

                <?php if(!empty($laporan->alasan_terlambat)): ?>
                <li class="info-item" style="margin-top:20px;">
                    <span class="info-label">Alasan Detail (Izin / Sakit / Telat)</span>
                    <div class="alasan-box">
                        "<?= nl2br(_ent($laporan->alasan_terlambat)); ?>"
                    </div>
                </li>
                <?php endif; ?>

                <li class="info-item" style="margin-top:20px;">
                    <span class="info-label">Titik Koordinat GPS</span>
                    <span class="info-value">
                        <?php if($laporan->lat_masuk && $laporan->lng_masuk): ?>
                            <a href="https://www.google.com/maps?q=<?= _ent($laporan->lat_masuk); ?>,<?= _ent($laporan->lng_masuk); ?>" target="_blank" class="text-primary" style="text-decoration:none;">
                                <i class="fa fa-map-marker text-danger"></i> <?= _ent($laporan->lat_masuk); ?>, <?= _ent($laporan->lng_masuk); ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Tidak Ada Data GPS Terdeteksi</span>
                        <?php endif; ?>
                    </span>
                </li>
            </ul>
        </div>

        <!-- Kolom Kanan: Visual & Peta -->
        <div class="premium-card">
            <h3 class="card-title-premium"><i class="fa fa-camera-retro text-success"></i> Bukti Visual Kehadiran</h3>
            
            <div class="row">
                <div class="col-sm-6">
                    <div class="info-label text-center" style="margin-bottom:10px;">
                        <?= (isset($laporan->file_surat) && $laporan->file_surat) ? 'Dokumen Surat Sakit' : 'Foto Selfie Masuk' ?>
                    </div>
                    
                    <div class="media-box">
                        <?php if($laporan->foto_masuk): ?>
                            <img src="<?= base_url($laporan->foto_masuk); ?>" onclick="previewImage('<?= base_url($laporan->foto_masuk); ?>', '<?= _ent($laporan->username); ?>', '<?= date('d M Y - H:i:s', strtotime($laporan->waktu_masuk)); ?>', '<?= _ent($laporan->status_kehadiran); ?>')">
                            <div class="overlay"><i class="fa fa-search-plus fa-2x"></i></div>
                        <?php elseif(isset($laporan->file_surat) && $laporan->file_surat): ?>
                            <img src="<?= base_url($laporan->file_surat); ?>" onclick="previewImage('<?= base_url($laporan->file_surat); ?>', '<?= _ent($laporan->username); ?>', 'Surat Izin/Sakit', '<?= _ent($laporan->status_kehadiran); ?>')" style="border: 3px solid var(--warning);">
                            <div class="overlay"><i class="fa fa-search-plus fa-2x"></i></div>
                        <?php else: ?>
                            <div class="media-empty">
                                <i class="fa fa-image"></i><br>Tidak Ada Dokumen
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="info-label text-center" style="margin-bottom:10px;">Peta Lokasi GPS</div>
                    <div class="media-box">
                        <?php if($laporan->lat_masuk && $laporan->lng_masuk): ?>
                            <div id="map_detail"></div>
                            <script>
                            $(document).ready(function() {
                                var mapDetail = L.map('map_detail').setView([<?= $laporan->lat_masuk ?>, <?= $laporan->lng_masuk ?>], 16);
                                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                                    attribution: '&copy; OpenStreetMap &copy; CARTO'
                                }).addTo(mapDetail);
                                L.marker([<?= $laporan->lat_masuk ?>, <?= $laporan->lng_masuk ?>]).bindPopup("<b>Lokasi Absen:</b><br><?= _ent($laporan->username); ?>").addTo(mapDetail);
                            });
                            </script>
                        <?php else: ?>
                            <div class="media-empty">
                                <i class="fa fa-map-o"></i><br>Peta Tidak Tersedia
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Modal Preview Foto Absensi -->
<div class="modal fade" id="modalImagePreview" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: var(--shadow-lg);">
      <div class="modal-header" style="background: var(--dark); color: white; padding: 15px 25px; border:none;">
        <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1; text-shadow:none;">
          <span aria-hidden="true" style="font-size: 24px;">&times;</span>
        </button>
        <h4 class="modal-title" style="font-weight: 600; font-size: 16px; letter-spacing:0.5px;">
          <i class="fa fa-eye margin-r-5"></i> Preview Dokumen
        </h4>
      </div>
      <div class="modal-body text-center" style="background: #F8FAFC; padding: 30px;">
        <div style="margin-bottom: 20px;">
          <h4 id="preview_user_name" style="margin:0 0 5px 0; font-weight:700; color:var(--dark);"></h4>
          <div style="display:flex; justify-content:center; align-items:center; gap:10px;">
              <small id="preview_time_info" style="color:var(--gray); font-weight:500;"></small>
              <span id="preview_status_badge" class="status-badge" style="background:var(--primary); color:white; padding:4px 10px; font-size:11px;"></span>
          </div>
        </div>

        <div style="background: white; padding: 10px; border-radius: 12px; box-shadow: var(--shadow-md); display: inline-block; width: 100%;">
          <img id="preview_img_target" src="" alt="Preview Foto" style="width: 100%; max-height: 500px; border-radius: 8px; object-fit: contain;">
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function previewImage(url, username, time, status) {
    $('#preview_img_target').attr('src', url);
    $('#preview_user_name').text(username);
    $('#preview_time_info').html('<i class="fa fa-clock-o"></i> ' + time);
    $('#preview_status_badge').text(status);
    $('#modalImagePreview').modal('show');
}
</script>
