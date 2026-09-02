
<section class="content-header">
   <h1>Laporan Rekap Absensi</h1>
</section>
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    .leaflet-map-mini { height: 150px; width: 100%; border: 1px solid #ddd; z-index: 1; }
</style>
<section class="content">
   <div class="box box-warning">
      <div class="box-body">
         <form method="GET" class="form-inline" style="margin-bottom: 20px;">
            <div class="form-group">
                <label>Filter Bulan: </label>
                <input type="month" name="bulan" class="form-control" value="<?= $bulan_aktif; ?>">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
         </form>

         <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
               <li class="active"><a href="#tab_rekap" data-toggle="tab"><i class="fa fa-bar-chart"></i> Rekap Bulanan</a></li>
               <li><a href="#tab_log" data-toggle="tab"><i class="fa fa-list"></i> Log Absensi Harian</a></li>
            </ul>
            <div class="tab-content" style="padding-top: 15px;">
               
               <!-- TAB REKAP -->
               <div class="tab-pane active" id="tab_rekap">
                  <div class="table-responsive">
                     <table class="table table-bordered table-striped">
                        <thead>
                           <tr class="bg-primary" style="background-color: #3c8dbc !important; color: white;">
                              <th>No</th>
                              <th>NIP</th>
                              <th>Nama Karyawan</th>
                              <th>Kantor</th>
                              <th>Total Hadir</th>
                              <th>Total Telat</th>
                              <th>Menit Telat</th>
                              <th>Total Sakit</th>
                              <th>Total Izin</th>
                              <th>Total Cuti</th>
                              <th>Total Alpha</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $no=1; foreach($rekap as $r): ?>
                           <tr>
                              <td><?= $no++ ?></td>
                              <td><?= _ent($r->nik) ?></td>
                              <td><span class="text-primary text-bold"><?= _ent($r->nama_lengkap) ?></span></td>
                              <td><span class="label label-info"><i class="fa fa-building"></i> <?= $r->nama_kantor ?: 'Kantor Utama' ?></span></td>
                              <td><span class="label label-success"><?= $r->total_hadir ?> Hari</span></td>
                              <td><?= $r->total_terlambat > 0 ? '<span class="label label-danger">'.$r->total_terlambat.' Kali</span>' : '-' ?></td>
                              <td><?= $r->total_menit_telat > 0 ? '<span class="text-danger text-bold"><i class="fa fa-clock-o"></i> '.$r->total_menit_telat.' Menit</span>' : '-' ?></td>
                              <td><?= $r->total_sakit > 0 ? '<span class="label label-warning">'.$r->total_sakit.' Hari</span>' : '-' ?></td>
                              <td><?= $r->total_izin > 0 ? '<span class="label label-primary">'.$r->total_izin.' Hari</span>' : '-' ?></td>
                              <td><?= isset($r->total_cuti) && $r->total_cuti > 0 ? '<span class="label label-info" style="background:#17a2b8;">'.$r->total_cuti.' Hari</span>' : '-' ?></td>
                              <td><?= $r->total_alpha > 0 ? '<span class="label label-default">'.$r->total_alpha.' Hari</span>' : '-' ?></td>
                           </tr>
                           <?php endforeach; ?>
                           <?php if(empty($rekap)): ?>
                           <tr><td colspan="10" class="text-center">Tidak ada data rekap absen di bulan ini.</td></tr>
                           <?php endif; ?>
                        </tbody>
                     </table>
                  </div>
               </div>
               
               <!-- TAB LOG -->
               <div class="tab-pane" id="tab_log">
                  <div class="table-responsive">
                     <table class="table table-bordered table-striped">
                        <thead>
                           <tr class="bg-primary" style="background-color: #3c8dbc !important; color: white;">
                              <th>Tanggal</th>
                              <th>Waktu Absensi</th>
                              <th>Lokasi</th>
                              <th>Lokasi Kantor</th>
                              <th>Foto</th>
                              <th>Created By</th>
                              <th>Status</th>
                              <th>Here Maps</th>
                              <th>Google Maps</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                  <?php if(empty($laporan)): ?>
                  <tr><td colspan="10" class="text-center">Tidak ada data absensi bulan ini.</td></tr>
                  <?php endif; ?>

                  <?php foreach($laporan as $row): ?>
                  <tr>
                     <td>
                        <?= date('Y-m-d', strtotime($row->tanggal)); ?><br>
                        <?php if($row->waktu_masuk): ?>
                            <small class="text-muted"><?= date('H:i:s', strtotime($row->waktu_masuk)); ?></small>
                        <?php else: ?>
                            <small class="text-muted">-</small>
                        <?php endif; ?>
                     </td>
                     <td>
                        <?php if($row->waktu_masuk): ?>
                            <?= date('H:i:s', strtotime($row->waktu_masuk)); ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                     </td>
                     <td>
                        <?php 
                        if ($row->lat_masuk && $row->lng_masuk) {
                            echo "Latitude: " . $row->lat_masuk . "<br>Longitude: " . $row->lng_masuk;
                        } else {
                            echo "Tidak ada lokasi (WFH/WFA)";
                        }
                        ?>
                     </td>
                     <td>
                        <?php if($row->tipe_kerja == 'WFO'): ?>
                           <span class="label label-info"><i class="fa fa-building"></i> <?= $row->nama_kantor ?: 'Kantor' ?></span>
                        <?php elseif($row->tipe_kerja == 'WFH'): ?>
                           <span class="label label-warning"><i class="fa fa-home"></i> WFH</span>
                        <?php else: ?>
                           <span class="label label-primary"><i class="fa fa-globe"></i> WFA</span>
                        <?php endif; ?>
                     </td>
                      <td>
                         <?php if($row->foto_masuk): ?>
                             <a href="javascript:void(0);" onclick="previewImage('<?= base_url($row->foto_masuk); ?>', '<?= _ent($row->username); ?>', '<?= date('d M Y - H:i:s', strtotime($row->waktu_masuk)); ?>', '<?= _ent($row->status_kehadiran); ?>')">
                                 <img src="<?= base_url($row->foto_masuk); ?>" alt="Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; cursor: pointer; border: 2px solid #3c8dbc; transition: transform 0.2s;" class="img-preview-thumb" title="Klik untuk lihat preview foto">
                             </a>
                         <?php elseif(isset($row->file_surat) && $row->file_surat): ?>
                             <a href="javascript:void(0);" onclick="previewImage('<?= base_url($row->file_surat); ?>', '<?= _ent($row->username); ?>', 'Surat Dokter/Izin', '<?= _ent($row->status_kehadiran); ?>')">
                                 <img src="<?= base_url($row->file_surat); ?>" alt="Surat" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; cursor: pointer; border: 2px solid #f39c12; transition: transform 0.2s;" class="img-preview-thumb" title="Klik untuk lihat surat bukti">
                             </a>
                         <?php else: ?>
                             <span class="text-muted">-</span>
                         <?php endif; ?>
                      </td>
                     <td>
                        <span class="text-primary"><?= _ent($row->username); ?></span>
                     </td>
                     <td>
                         <?php if(in_array($row->status_kehadiran, ['Telat', 'Terlambat'])): ?>
                             <span class="label label-danger">Telat</span>
                             <?php if(isset($row->terlambat_menit) && $row->terlambat_menit > 0): ?>
                                 <div style="margin-top: 5px; font-size: 12px; font-weight: bold; color: #d73925;">
                                     <i class="fa fa-clock-o"></i> Telat <?= $row->terlambat_menit ?> Menit
                                 </div>
                             <?php endif; ?>
                         <?php elseif(in_array($row->status_kehadiran, ['Hadir', 'Tepat Waktu'])): ?>
                             <span class="label label-success" style="background-color: #00a65a !important;">Hadir</span>
                         <?php elseif(in_array($row->status_kehadiran, ['Izin', 'Izin Khusus'])): ?>
                             <span class="label label-primary"><?= $row->status_kehadiran; ?></span>
                             <?php if(!empty($row->alasan_terlambat)): ?>
                                 <div style="margin-top: 5px; font-size: 11px; font-style: italic; color: #666;">
                                     <i class="fa fa-comment-o"></i> <?= _ent(strlen($row->alasan_terlambat) > 35 ? substr($row->alasan_terlambat,0,35).'...' : $row->alasan_terlambat) ?>
                                 </div>
                             <?php endif; ?>
                         <?php elseif(strpos($row->status_kehadiran, 'Sakit') !== false): ?>
                             <span class="label label-warning"><?= $row->status_kehadiran; ?></span>
                             <?php if(!empty($row->alasan_terlambat)): ?>
                                 <div style="margin-top: 5px; font-size: 11px; font-style: italic; color: #666;">
                                     <i class="fa fa-comment-o"></i> <?= _ent(strlen($row->alasan_terlambat) > 35 ? substr($row->alasan_terlambat,0,35).'...' : $row->alasan_terlambat) ?>
                                 </div>
                             <?php endif; ?>
                         <?php elseif($row->status_kehadiran == 'Cuti'): ?>
                             <span class="label label-info" style="background:#17a2b8;">Cuti</span>
                             <?php if(!empty($row->alasan_terlambat)): ?>
                                 <div style="margin-top: 5px; font-size: 11px; font-style: italic; color: #666;">
                                     <i class="fa fa-comment-o"></i> <?= _ent(strlen($row->alasan_terlambat) > 35 ? substr($row->alasan_terlambat,0,35).'...' : $row->alasan_terlambat) ?>
                                 </div>
                             <?php endif; ?>
                         <?php else: ?>
                             <span class="label label-default"><?= $row->status_kehadiran; ?></span>
                         <?php endif; ?>
                     </td>
                     <td>
                        <?php if($row->lat_masuk && $row->lng_masuk): ?>
                            <div id="map_here_<?= $row->id; ?>" class="leaflet-map-mini"></div>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                     </td>
                     <td>
                        <?php if($row->lat_masuk && $row->lng_masuk): ?>
                            <div id="map_google_<?= $row->id; ?>" class="leaflet-map-mini"></div>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                     </td>
                     <td>
                        <a href="<?= site_url('administrator/hr_laporan/view/'.$row->id); ?>" class="btn btn-xs btn-default"><i class="fa fa-eye"></i> see</a>
                     </td>
                  </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
               </div> <!-- End Tab Log -->
            </div> <!-- End Tab Content -->
         </div> <!-- End Nav Tabs Custom -->
      </div>
   </div>
</section>

<script>
var mapsInitialized = false;

function initLogMaps() {
    if (mapsInitialized) return;
    mapsInitialized = true;

    var userIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/149/149059.png',
        iconSize: [24, 24],
        iconAnchor: [12, 24]
    });

    <?php foreach($laporan as $row): ?>
        <?php if($row->lat_masuk && $row->lng_masuk): ?>
            // Here Maps
            var mapHere_<?= $row->id ?> = L.map('map_here_<?= $row->id ?>', { zoomControl: false, attributionControl: false }).setView([<?= $row->lat_masuk ?>, <?= $row->lng_masuk ?>], 15);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(mapHere_<?= $row->id ?>);
            L.marker([<?= $row->lat_masuk ?>, <?= $row->lng_masuk ?>], {icon: userIcon}).addTo(mapHere_<?= $row->id ?>).bindPopup("Lokasi Absen Karyawan");

            <?php if($row->tipe_kerja == 'WFO' && $row->kantor_lat && $row->kantor_lng): ?>
                L.circle([<?= $row->kantor_lat ?>, <?= $row->kantor_lng ?>], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.2,
                    radius: <?= $row->kantor_radius ?: 50 ?>
                }).addTo(mapHere_<?= $row->id ?>).bindPopup("Zona Radius Kantor");

                var boundsHere_<?= $row->id ?> = L.latLngBounds([
                    [<?= $row->lat_masuk ?>, <?= $row->lng_masuk ?>],
                    [<?= $row->kantor_lat ?>, <?= $row->kantor_lng ?>]
                ]);
                mapHere_<?= $row->id ?>.fitBounds(boundsHere_<?= $row->id ?>.pad(0.3));
            <?php endif; ?>

            // Google Maps (OSM Tile)
            var mapGoogle_<?= $row->id ?> = L.map('map_google_<?= $row->id ?>', { zoomControl: false, attributionControl: false }).setView([<?= $row->lat_masuk ?>, <?= $row->lng_masuk ?>], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapGoogle_<?= $row->id ?>);
            L.marker([<?= $row->lat_masuk ?>, <?= $row->lng_masuk ?>], {icon: userIcon}).addTo(mapGoogle_<?= $row->id ?>).bindPopup("Lokasi Absen Karyawan");

            <?php if($row->tipe_kerja == 'WFO' && $row->kantor_lat && $row->kantor_lng): ?>
                L.circle([<?= $row->kantor_lat ?>, <?= $row->kantor_lng ?>], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.2,
                    radius: <?= $row->kantor_radius ?: 50 ?>
                }).addTo(mapGoogle_<?= $row->id ?>).bindPopup("Zona Radius Kantor");

                var boundsGoogle_<?= $row->id ?> = L.latLngBounds([
                    [<?= $row->lat_masuk ?>, <?= $row->lng_masuk ?>],
                    [<?= $row->kantor_lat ?>, <?= $row->kantor_lng ?>]
                ]);
                mapGoogle_<?= $row->id ?>.fitBounds(boundsGoogle_<?= $row->id ?>.pad(0.3));
            <?php endif; ?>

        <?php endif; ?>
    <?php endforeach; ?>
}

$(document).ready(function() {
    // If Tab Log is active on load
    if ($('#tab_log').hasClass('active')) {
        initLogMaps();
    }

    // Trigger lazy load map when tab log is opened
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if ($(e.target).attr('href') === '#tab_log') {
            setTimeout(function() {
                initLogMaps();
            }, 100);
        }
    });
});
</script>

<!-- Modal Preview Foto Absensi -->
<div class="modal fade" id="modalImagePreview" tabindex="-1" role="dialog" aria-labelledby="modalImagePreviewLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="border-radius: 12px; overflow: hidden; box-shadow: 0 5px 25px rgba(0,0,0,0.3);">
      <div class="modal-header" style="background: #3c8dbc; color: white; padding: 12px 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9; margin-top: 2px;">
          <span aria-hidden="true" style="font-size: 24px;">&times;</span>
        </button>
        <h4 class="modal-title" id="modalImagePreviewLabel" style="font-weight: bold; font-size: 16px;">
          <i class="fa fa-camera"></i> Preview Foto Absensi
        </h4>
      </div>
      <div class="modal-body text-center" style="background: #f8f9fa; padding: 25px;">
        <div style="margin-bottom: 15px;">
          <span id="preview_user_name" class="text-bold" style="font-size: 16px; color: #333;">-</span>
          <br>
          <small id="preview_time_info" class="text-muted" style="font-size: 13px;"><i class="fa fa-clock-o"></i> -</small>
          <span id="preview_status_badge" class="label label-primary" style="margin-left: 8px;">-</span>
        </div>

        <div style="background: #fff; padding: 10px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); display: inline-block; max-width: 100%;">
          <img id="preview_img_target" src="" alt="Preview Foto" style="max-width: 100%; max-height: 450px; border-radius: 6px; display: block; margin: 0 auto; object-fit: contain;">
        </div>
      </div>
      <div class="modal-footer" style="background: #fff; padding: 12px 20px;">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="border-radius: 4px;">
          <i class="fa fa-times"></i> Tutup
        </button>
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
