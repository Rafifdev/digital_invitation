<section class="content-header">
   <h1>Rekap Payroll <small style="font-weight: 400; color: #777;">Gaji & Absensi Karyawan</small></h1>
   <ol class="breadcrumb" style="background: transparent;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Rekap Payroll</li>
   </ol>
</section>

<style>
   .modern-box { border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #00a65a; background: #fff; margin-bottom: 25px; }
   .modern-box-header { padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #f9fbf9; border-radius: 8px 8px 0 0; }
   .modern-box-title { margin: 0; font-size: 18px; font-weight: 600; color: #007540; }
   
   .table-modern { border-collapse: separate; border-spacing: 0; width: 100%; }
   .table-modern thead th { background: #f4f6f9; color: #333; font-weight: 600; border-bottom: 2px solid #ddd; padding: 12px; }
   .table-modern tbody td { padding: 12px; vertical-align: middle; border-bottom: 1px solid #f0f0f0; }
   .table-modern tbody tr:hover { background-color: #f8fbff; }
   
   .btn-modern { border-radius: 20px; padding: 6px 15px; font-weight: 500; transition: all 0.3s; }
   .btn-modern:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
   .btn-action { border-radius: 20px; padding: 4px 12px; font-size: 13px; font-weight: 500; }
   
   .payroll-stat { font-size: 12px; color: #777; margin-bottom: 2px; }
   .payroll-val { font-size: 13px; font-weight: 600; color: #333; }
</style>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box modern-box">
            <div class="modern-box-header">
               <h3 class="modern-box-title"><i class="fa fa-money margin-r-5"></i> Data Payroll Bulan: <strong><?= date('F Y', strtotime($periode.'-01')) ?></strong></h3>
               <div class="box-tools">
                    <form method="GET" action="<?= site_url('administrator/hr_payroll') ?>" style="display:flex; gap:10px;">
                        <input type="month" name="periode" class="form-control input-sm" style="border-radius: 20px; width: 180px; text-align: center;" value="<?= $periode ?>">
                        <button type="submit" class="btn btn-default btn-sm btn-modern"><i class="fa fa-search text-primary"></i> Tampilkan</button>
                    </form>
               </div>
            </div>
            
            <div class="box-body" style="padding: 20px;">
                <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee; display: flex; gap: 10px; flex-wrap: wrap;">
                    
                    <form id="form_generate" action="<?= site_url('administrator/hr_payroll/generate') ?>" method="POST" style="margin:0;">
                        <input type="hidden" name="periode" value="<?= $periode ?>">
                        <button type="button" id="btn-generate" class="btn btn-primary btn-modern"><i class="fa fa-refresh margin-r-5"></i> Generate Ulang Payroll</button>
                    </form>
                    
                    <?php 
                    $semua_paid = true;
                    if(empty($payrolls)) $semua_paid = false;
                    foreach($payrolls as $p) {
                        if($p->status != 'Paid') $semua_paid = false;
                    }
                    ?>

                    <?php if(!$semua_paid && !empty($payrolls)): ?>
                        <a href="<?= site_url('administrator/hr_payroll/mark_paid/'.$periode) ?>" class="btn btn-success btn-modern" id="btn-lock"><i class="fa fa-lock margin-r-5"></i> Kunci & Tandai PAID</a>
                    <?php endif; ?>

                    <?php if($semua_paid && !empty($payrolls)): ?>
                        <a href="<?= site_url('administrator/hr_payroll/unlock_paid/'.$periode) ?>" class="btn btn-warning btn-modern" id="btn-unlock"><i class="fa fa-unlock margin-r-5"></i> Buka Gembok (Revisi)</a>
                    <?php endif; ?>

                    <?php if(!empty($payrolls)): ?>
                        <a href="<?= site_url('administrator/hr_payroll/cetak_semua/'.$periode) ?>" target="_blank" class="btn btn-info btn-modern"><i class="fa fa-print margin-r-5"></i> Cetak Semua Slip Gaji</a>
                    <?php endif; ?>
                </div>

               <div class="table-responsive">
                  <table class="table table-modern">
                     <thead>
                        <tr>
                           <th>Karyawan</th>
                           <th>Hari Kerja</th>
                           <th>Kehadiran & Pelanggaran</th>
                           <th>Rincian Penghasilan</th>
                           <th>Potongan</th>
                           <th>Take Home Pay</th>
                           <th class="text-center">Status</th>
                           <th width="100" class="text-center">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach($payrolls as $p): ?>
                        <tr>
                           <td>
                              <div style="font-weight: 600; font-size: 14px; color: #333;"><?= _ent($p->nama_lengkap) ?></div>
                              <div class="text-muted" style="font-size: 12px;">NIP: <?= _ent($p->nik) ?></div>
                              <div style="font-size: 12px; margin-top:3px;">
                                <span class="label bg-blue-active" style="border-radius:10px;"><?= _ent($p->jabatan) ?></span>
                                <?php if($p->tipe_kerja == 'WFO'): ?>
                                    <span class="label bg-green" style="border-radius:10px; margin-left:3px;" title="Work From Office"><i class="fa fa-building"></i> WFO</span>
                                <?php elseif($p->tipe_kerja == 'WFH'): ?>
                                    <span class="label bg-yellow" style="border-radius:10px; margin-left:3px;" title="Work From Home"><i class="fa fa-home"></i> WFH</span>
                                <?php elseif($p->tipe_kerja == 'WFA'): ?>
                                    <span class="label bg-purple" style="border-radius:10px; margin-left:3px;" title="Work From Anywhere"><i class="fa fa-globe"></i> WFA</span>
                                <?php endif; ?>
                              </div>
                           </td>
                           
                           <td>
                               <div class="payroll-stat">Efektif: <span class="payroll-val"><?= $p->hari_kerja_sebulan ?></span> hr</div>
                               <div class="payroll-stat">Dibayar: <span class="payroll-val"><?= $p->hari_dibayar_penuh ?></span> hr</div>
                           </td>
                           
                           <td>
                               <div style="display:flex; gap:5px; flex-wrap:wrap; margin-bottom:5px;">
                                 <span class="label label-success" style="border-radius:10px;" title="Hadir"><i class="fa fa-check-circle"></i> H: <?= $p->jumlah_hadir ?></span>
                                 <span class="label label-danger" style="border-radius:10px;" title="Alpha/Hangus"><i class="fa fa-times-circle"></i> A: <?= $p->jumlah_alfa ?></span>
                                 <span class="label label-info" style="border-radius:10px;" title="Izin/Sakit"><i class="fa fa-medkit"></i> I/S: <?= $p->jumlah_izin_sakit ?></span>
                                 <span class="label label-warning" style="border-radius:10px;" title="Telat Kena Denda"><i class="fa fa-clock-o"></i> Telat: <?= $p->jumlah_terlambat_ringan ?></span>
                               </div>
                           </td>
                           
                           <td>
                               <div class="payroll-stat">Gaji Pokok (Harian): <span class="payroll-val">Rp <?= number_format((float)$p->gaji_harian_pokok,0,',','.') ?></span></div>
                               <div class="payroll-stat">Tunjangan Tetap (Harian): <span class="payroll-val">Rp <?= number_format((float)$p->tunj_tetap_harian,0,',','.') ?></span></div>
                               <div class="payroll-stat">Tunjangan Hadir (Harian): <span class="payroll-val">Rp <?= number_format((float)$p->tunj_kehadiran_harian,0,',','.') ?></span></div>
                               <?php if($p->uang_lembur > 0): ?>
                               <div class="payroll-stat text-success"><i class="fa fa-plus"></i> Lembur: <span class="payroll-val">Rp <?= number_format((float)$p->uang_lembur,0,',','.') ?></span></div>
                               <?php endif; ?>
                               <div style="margin-top: 5px; font-weight: 600; font-size: 13px; color: #3c8dbc;">
                                  Total: Rp <?= number_format((float)$p->gaji_kotor,0,',','.') ?>
                               </div>
                           </td>
                           
                           <td>
                               <strong class="text-danger" style="font-size: 14px;">- Rp <?= number_format((float)$p->total_potongan,0,',','.') ?></strong>
                           </td>
                           
                           <td>
                               <strong class="text-success" style="font-size:18px;">Rp <?= number_format((float)$p->gaji_bersih,0,',','.') ?></strong>
                           </td>
                           
                           <td class="text-center">
                               <?php if($p->status == 'Paid'): ?>
                                   <span class="label label-success" style="border-radius:12px; padding:5px 12px; font-size: 12px;"><i class="fa fa-check margin-r-5"></i> PAID</span>
                               <?php else: ?>
                                   <span class="label label-default" style="border-radius:12px; padding:5px 12px; font-size: 12px;">DRAFT</span>
                               <?php endif; ?>
                           </td>
                           
                           <td class="text-center" style="display:flex; gap:5px; justify-content:center;">
                               <a href="<?= site_url('administrator/hr_payroll/view/'.$p->id) ?>" class="btn btn-primary btn-action" title="Detail Breakdown Kalkulasi"><i class="fa fa-eye margin-r-5"></i> Detail</a>
                               <?php if($p->status == 'Draft'): ?>
                                   <a href="<?= site_url('administrator/hr_payroll/edit/'.$p->id) ?>" class="btn btn-warning btn-action" title="Edit Manual"><i class="fa fa-pencil margin-r-5"></i> Edit</a>
                               <?php endif; ?>
                               <a href="<?= site_url('administrator/hr_payroll/cetak_slip/'.$p->id) ?>" target="_blank" class="btn btn-default btn-action" title="Cetak Slip Gaji"><i class="fa fa-print text-info margin-r-5"></i> Slip</a>
                           </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($payrolls)): ?>
                           <tr><td colspan="8" class="text-center text-muted" style="padding: 40px;">
                               <i class="fa fa-file-text-o fa-3x" style="color: #ddd; margin-bottom:15px; display:block;"></i>
                               Belum ada data payroll untuk bulan ini.<br>Silakan klik tombol <strong>Generate Ulang Payroll</strong> di atas.
                           </td></tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
$(document).ready(function(){
   $("#btn-generate").click(function(e){
       e.preventDefault();
       var form = $("#form_generate");
       var btn = $(this);
       var originalHtml = btn.html();

       swal({
           title: "Generate Payroll?",
           text: "Proses ini akan menghitung ulang seluruh absensi dan gaji karyawan untuk bulan <?= $periode ?>. Lanjutkan?",
           type: "warning",
           showCancelButton: true,
           confirmButtonColor: "#3c8dbc",
           confirmButtonText: "Ya, Generate!",
           cancelButtonText: "Batal",
           closeOnConfirm: false
       }, function() {
           btn.html('<i class="fa fa-spinner fa-spin margin-r-5"></i> Memproses...').prop('disabled', true);
           swal.close(); 
           
           $.ajax({
               url: form.attr('action'),
               type: 'POST',
               data: form.serialize(),
               dataType: 'json',
               success: function(res){
                   if(res.success){
                       window.location.href = res.redirect;
                   } else {
                       swal({
                           title: "Gagal Proses ❌",
                           text: res.message,
                           type: "warning",
                           confirmButtonColor: "#f39c12"
                       });
                       btn.html(originalHtml).prop('disabled', false);
                   }
               },
               error: function(){
                   swal({
                       title: "Error Server",
                       text: "Terjadi kesalahan server saat generate.",
                       type: "error",
                       confirmButtonColor: "#d33"
                   });
                   btn.html(originalHtml).prop('disabled', false);
               }
           });
       });
   });

   $("#btn-lock").click(function(e){
       e.preventDefault();
       var href = $(this).attr('href');
       
       swal({
           title: "Kunci & Tandai PAID?",
           text: "Data bulan ini akan terkunci secara permanen dan tidak bisa di-generate ulang. Lanjutkan?",
           type: "warning",
           showCancelButton: true,
           confirmButtonColor: "#00a65a",
           confirmButtonText: "Ya, Kunci Sekarang!",
           cancelButtonText: "Batal",
           closeOnConfirm: false
       }, function() {
           window.location.href = href;
       });
   });

   $("#btn-unlock").click(function(e){
       e.preventDefault();
       var href = $(this).attr('href');
       
       swal({
           title: "Buka Kunci Payroll?",
           text: "Status bulan ini akan dikembalikan menjadi Draft, sehingga Anda bisa melakukan Generate Ulang. Lanjutkan?",
           type: "warning",
           showCancelButton: true,
           confirmButtonColor: "#f39c12",
           confirmButtonText: "Ya, Buka Gembok!",
           cancelButtonText: "Batal",
           closeOnConfirm: false
       }, function() {
           window.location.href = href;
       });
   });
});
</script>
