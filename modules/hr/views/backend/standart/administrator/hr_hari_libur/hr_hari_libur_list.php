<section class="content-header">
   <h1>Pengaturan HR <small style="font-weight: 400; color: #777;">Kalender Hari Libur</small></h1>
   <ol class="breadcrumb" style="background: transparent;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Hari Libur</li>
   </ol>
</section>

<!-- FullCalendar -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<style>
   .modern-box { border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: #fff; margin-bottom: 25px; }
   .modern-box-header { padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
   .modern-box-title { margin: 0; font-size: 16px; font-weight: 600; color: #333; }
   
   .table-modern { border-collapse: separate; border-spacing: 0; width: 100%; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
   .table-modern thead th { background: #f4f6f9; color: #333; font-weight: 600; border-bottom: 2px solid #ddd; padding: 12px; }
   .table-modern tbody td { padding: 12px; vertical-align: middle; border-bottom: 1px solid #f0f0f0; }
   .table-modern tbody tr:hover { background-color: #f8fbff; }
   
   .btn-action { border-radius: 50%; width: 32px; height: 32px; padding: 0; line-height: 32px; text-align: center; margin: 0 2px; }
   .fc-toolbar-title { font-size: 1.25em !important; font-weight: 600 !important; color: #3c8dbc; }
   .fc-button-primary { background-color: #3c8dbc !important; border-color: #367fa9 !important; }
   .fc-day-sun { background-color: #fff4f4 !important; } /* Highlight Sunday */
</style>

<section class="content">
   <div class="row">
      
      <!-- DAFTAR LIBUR (KIRI) -->
      <div class="col-md-4">
         <div class="modern-box" style="border-top: 4px solid #f39c12;">
            <div class="modern-box-header">
               <h3 class="modern-box-title"><i class="fa fa-list text-warning margin-r-5"></i> Daftar Hari Libur</h3>
               <div class="box-tools">
                  <a href="<?= site_url('administrator/hr_hari_libur/add'); ?>" class="btn btn-primary btn-sm" style="border-radius: 20px;"><i class="fa fa-plus"></i> Tambah</a>
               </div>
            </div>
            
            <div style="padding: 15px; border-bottom: 1px solid #eee; background: #fafafa;">
               <button type="button" class="btn btn-success btn-block" id="btn-sync" style="border-radius: 20px; font-weight: bold;"><i class="fa fa-refresh margin-r-5"></i> Sinkron Otomatis Libur Nasional</button>
            </div>

            <div class="box-body" style="height: 500px; overflow-y: auto; padding: 15px;">
               <table class="table table-modern">
                  <thead>
                     <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th width="70" class="text-center">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach($liburs as $l): ?>
                     <tr>
                        <td>
                           <span class="label <?= $l->is_national ? 'label-danger' : 'label-warning' ?>" style="font-size: 12px; border-radius: 10px; padding: 4px 8px;">
                              <?= date('d M Y', strtotime($l->tanggal)); ?>
                           </span>
                        </td>
                        <td><strong><?= _ent($l->keterangan); ?></strong></td>
                        <td class="text-center">
                           <a href="<?= site_url('administrator/hr_hari_libur/edit/'.$l->id); ?>" class="btn btn-default btn-action" title="Edit"><i class="fa fa-pencil text-info"></i></a>
                           <a href="<?= site_url('administrator/hr_hari_libur/delete/'.$l->id); ?>" class="btn btn-default btn-action" onclick="return confirm('Hapus hari libur ini?');" title="Hapus"><i class="fa fa-trash text-danger"></i></a>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                     <?php if(empty($liburs)): ?>
                        <tr><td colspan="3" class="text-center text-muted" style="padding: 20px;">Belum ada data hari libur</td></tr>
                     <?php endif; ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      
      <!-- KALENDER (KANAN) -->
      <div class="col-md-8">
         <div class="modern-box" style="border-top: 4px solid #3c8dbc;">
            <div class="modern-box-header">
               <h3 class="modern-box-title"><i class="fa fa-calendar text-primary margin-r-5"></i> Kalender HR</h3>
            </div>
            <div class="box-body" style="padding: 20px;">
               <div id="calendar"></div>
            </div>
         </div>
      </div>
      
   </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth'
        },
        themeSystem: 'bootstrap',
        events: [
            <?php foreach($liburs as $l): ?>
            {
                title: '<?= addslashes($l->keterangan) ?>',
                start: '<?= $l->tanggal ?>',
                color: '<?= $l->is_national ? "#dd4b39" : "#f39c12" ?>',
                allDay: true
            },
            <?php endforeach; ?>
        ],
        eventClick: function(info) {
            swal({
                title: info.event.title,
                text: "Tanggal: " + info.event.startStr,
                type: "info",
                confirmButtonColor: "#3c8dbc",
                confirmButtonText: "Tutup"
            });
        }
    });
    calendar.render();

    // Sync Button
    $('#btn-sync').click(function() {
        var btn = $(this);
        swal({
            title: "Sinkronkan Libur Nasional?",
            text: "Sistem akan mengambil dan memasukkan data tanggal merah resmi tahun ini secara otomatis dari API Pemerintah.",
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#00a65a",
            confirmButtonText: "Ya, Sinkronkan!",
            cancelButtonText: "Batal",
            closeOnConfirm: false
        }, function() {
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Proses Sinkronisasi...');
            $.ajax({
                url: '<?= site_url("administrator/hr_hari_libur/sync_national") ?>',
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        swal({
                            title: "Berhasil! 🚀", 
                            text: res.message, 
                            type: "success",
                            confirmButtonColor: "#00a65a"
                        }, function(){
                            location.reload();
                        });
                    } else {
                        swal("Gagal", res.message, "error");
                        btn.prop('disabled', false).html('<i class="fa fa-refresh margin-r-5"></i> Sinkron Otomatis Libur Nasional');
                    }
                },
                error: function() {
                    swal("Error", "Gagal terhubung ke server.", "error");
                    btn.prop('disabled', false).html('<i class="fa fa-refresh margin-r-5"></i> Sinkron Otomatis Libur Nasional');
                }
            });
        });
    });
});
</script>
