<section class="content-header">
   <h1>Pengaturan Penggajian & Potongan <small style="font-weight: 400; color: #777;">Konfigurasi Sistem Payroll</small></h1>
   <ol class="breadcrumb" style="background: transparent;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Penggajian</a></li>
      <li class="active">Pengaturan</li>
   </ol>
</section>

<style>
   .modern-panel { border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; background: #fff; }
   .modern-panel-heading { padding: 15px 25px; border-bottom: 1px solid #eee; }
   .modern-panel-title { margin: 0; font-size: 16px; font-weight: 600; }
   .input-group-addon { background-color: #f4f6f9; border-color: #d2d6de; }
   .setting-section { padding: 25px; }

   .panel-pay { border-top: 4px solid #dd4b39; }
   .panel-pay .modern-panel-heading { background-color: #faebea; border-bottom-color: #f2d0cf; }
   .panel-pay .modern-panel-title { color: #a93223; }

   .table-denda th { background-color: #dd4b39; color: white; border-color: #c74333 !important; }
   .table-denda td { vertical-align: middle !important; }
</style>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         
         <form id="form_pengaturan_payroll" method="POST" action="<?= site_url('administrator/hr_payroll/pengaturan_save'); ?>">
            
            <div class="row">
               <!-- KOLOM KIRI: ATURAN DASAR -->
               <div class="col-md-6">
                  <div class="modern-panel panel-pay">
                     <div class="modern-panel-heading">
                        <h4 class="modern-panel-title"><i class="fa fa-calendar-check-o margin-r-5"></i> Aturan Dasar & Potongan Absensi</h4>
                     </div>
                     <div class="setting-section">
                        
                        <div class="row" style="margin-bottom: 15px;">
                           <div class="col-md-12">
                              <div class="form-group" style="background: #fff4f4; padding: 15px; border-radius: 8px; border: 1px solid #f2dede;">
                                 <label style="color: #dd4b39;"><i class="fa fa-calendar margin-r-5"></i> Periode Cut-Off Absensi & Penggajian</label>
                                 <div class="row">
                                    <div class="col-xs-6">
                                       <div class="input-group">
                                          <span class="input-group-addon" style="font-size: 12px;">Mulai Tgl</span>
                                          <input type="number" class="form-control" name="tgl_cutoff_mulai" value="<?= _ent($pengaturan->tgl_cutoff_mulai ?? 21); ?>" min="1" max="31" required>
                                       </div>
                                    </div>
                                    <div class="col-xs-6">
                                       <div class="input-group">
                                          <span class="input-group-addon" style="font-size: 12px;">Akhir Tgl</span>
                                          <input type="number" class="form-control" name="tgl_cutoff_akhir" value="<?= _ent($pengaturan->tgl_cutoff_akhir ?? 20); ?>" min="1" max="31" required>
                                       </div>
                                    </div>
                                 </div>
                                 <small class="text-muted" style="display:block; margin-top:5px; font-size: 11px;">Contoh: Jika diset 21 dan 20, maka gaji bulan ini dihitung berdasarkan absensi dari <strong>tanggal 21 bulan sebelumnya</strong> sampai <strong>tanggal 20 bulan ini</strong>. Berlaku terus setiap bulan.</small>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label>Hari Kerja per Minggu</label>
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-briefcase"></i></span>
                                    <select class="form-control" name="hari_kerja_per_minggu" id="hari_kerja_per_minggu">
                                       <option value="senin_jumat" <?= (isset($pengaturan->hari_kerja_per_minggu) && $pengaturan->hari_kerja_per_minggu == 'senin_jumat') ? 'selected' : ''; ?>>5 Hari (Senin - Jumat)</option>
                                       <option value="senin_sabtu" <?= (isset($pengaturan->hari_kerja_per_minggu) && $pengaturan->hari_kerja_per_minggu == 'senin_sabtu') ? 'selected' : ''; ?>>6 Hari (Senin - Sabtu)</option>
                                    </select>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label>Batas Max Denda Telat</label>
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                                    <input type="number" class="form-control" name="cap_denda_persen" value="<?= _ent($pengaturan->cap_denda_persen ?? 30); ?>" required>
                                    <span class="input-group-addon">/ Bln</span>
                                 </div>
                                 <small class="text-muted">Max denda dari Gaji Pokok.</small>
                              </div>
                           </div>
                        </div>

                        <div class="form-group" style="margin-top: 15px;">
                           <label>Kebijakan Izin Sakit (Gaji & Tunjangan)</label>
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="input-group" style="margin-bottom:10px;">
                                    <span class="input-group-addon" style="width: 160px; text-align: left; font-weight: bold; background: #fff;">TANPA Surat Dokter</span>
                                    <select class="form-control" name="potong_gaji_sakit_tanpa_surat">
                                       <option value="1" <?= (!isset($pengaturan->potong_gaji_sakit_tanpa_surat) || $pengaturan->potong_gaji_sakit_tanpa_surat == 1) ? 'selected' : ''; ?>>Potong 100% (Hangus)</option>
                                       <option value="0" <?= (isset($pengaturan->potong_gaji_sakit_tanpa_surat) && $pengaturan->potong_gaji_sakit_tanpa_surat == 0) ? 'selected' : ''; ?>>Tetap Dibayar Penuh</option>
                                    </select>
                                 </div>
                                 <div class="input-group">
                                    <span class="input-group-addon" style="width: 160px; text-align: left; font-weight: bold; background: #fff;">DENGAN Surat Dokter</span>
                                    <select class="form-control" name="potong_gaji_sakit_dengan_surat">
                                       <option value="1" <?= (isset($pengaturan->potong_gaji_sakit_dengan_surat) && $pengaturan->potong_gaji_sakit_dengan_surat == 1) ? 'selected' : ''; ?>>Potong 100% (Hangus)</option>
                                       <option value="0" <?= (!isset($pengaturan->potong_gaji_sakit_dengan_surat) || $pengaturan->potong_gaji_sakit_dengan_surat == 0) ? 'selected' : ''; ?>>Tetap Dibayar Penuh</option>
                                    </select>
                                 </div>
                              </div>
                           </div>
                        </div>

                     </div>
                  </div>
               </div>

               <!-- KOLOM KANAN: DENDA BERTINGKAT -->
               <div class="col-md-6">
                  <div class="modern-panel panel-pay" style="border-top-color: #f39c12;">
                     <div class="modern-panel-heading" style="background-color: #fcf8e3; border-bottom-color: #faebcc;">
                        <h4 class="modern-panel-title" style="color: #8a6d3b;"><i class="fa fa-warning margin-r-5"></i> Denda Keterlambatan Bertingkat</h4>
                     </div>
                     <div class="setting-section" style="padding: 0;">
                        <table class="table table-bordered table-denda" style="margin-bottom:0; border: none;">
                           <thead>
                              <tr>
                                 <th style="width: 40%; background-color: #f39c12; border-color: #e08e0b !important;">Tingkat</th>
                                 <th style="background-color: #f39c12; border-color: #e08e0b !important;">Batas Keterlambatan</th>
                                 <th style="background-color: #f39c12; border-color: #e08e0b !important;">Denda Harian</th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td style="padding: 15px;"><strong>Tier 1</strong><br><small class="text-muted">(Toleransi)</small></td>
                                 <td style="padding: 15px;"><div class="input-group"><input type="number" class="form-control" name="denda_tier1_menit" value="<?= _ent($pengaturan->denda_tier1_menit ?? 15); ?>"><span class="input-group-addon">mnt</span></div></td>
                                 <td style="padding: 15px;"><div class="input-group"><input type="number" class="form-control" name="denda_tier1_persen" value="<?= _ent($pengaturan->denda_tier1_persen ?? 0); ?>"><span class="input-group-addon">%</span></div></td>
                              </tr>
                              <tr>
                                 <td style="padding: 15px;"><strong>Tier 2</strong><br><small class="text-muted">(Kategori 1)</small></td>
                                 <td style="padding: 15px;"><div class="input-group"><input type="number" class="form-control" name="denda_tier2_menit" value="<?= _ent($pengaturan->denda_tier2_menit ?? 30); ?>"><span class="input-group-addon">mnt</span></div></td>
                                 <td style="padding: 15px;"><div class="input-group"><input type="number" class="form-control" name="denda_tier2_persen" value="<?= _ent($pengaturan->denda_tier2_persen ?? 5); ?>"><span class="input-group-addon">%</span></div></td>
                              </tr>
                              <tr>
                                 <td style="padding: 15px;"><strong>Tier 3</strong><br><small class="text-muted">(Kategori 2)</small></td>
                                 <td style="padding: 15px;"><div class="input-group"><input type="number" class="form-control" name="denda_tier3_menit" value="<?= _ent($pengaturan->denda_tier3_menit ?? 60); ?>"><span class="input-group-addon">mnt</span></div></td>
                                 <td style="padding: 15px;"><div class="input-group"><input type="number" class="form-control" name="denda_tier3_persen" value="<?= _ent($pengaturan->denda_tier3_persen ?? 10); ?>"><span class="input-group-addon">%</span></div></td>
                              </tr>
                              <tr>
                                 <td style="padding: 15px;"><strong>Tier 4</strong><br><small class="text-muted">(Telat Parah)</small></td>
                                 <td class="text-danger" style="font-size: 13px; padding: 15px;">Lebih dari Tier 3</td>
                                 <td style="padding: 15px;"><div class="input-group"><input type="number" class="form-control" name="denda_tier4_persen" value="<?= _ent($pengaturan->denda_tier4_persen ?? 25); ?>"><span class="input-group-addon">%</span></div></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>

            <!-- ACTION BUTTON -->
            <div class="text-center" style="margin-bottom: 40px; margin-top: 10px;">
               <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 30px; padding: 12px 40px; font-size: 16px; font-weight: bold; box-shadow: 0 4px 10px rgba(60,141,188,0.4); transition: all 0.3s;"><i class="fa fa-save margin-r-5"></i> Simpan Aturan Penggajian</button>
            </div>

         </form>
      </div>
   </div>
</section>

<script>
$(document).ready(function(){
   $("#form_pengaturan_payroll").submit(function(e){
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
               toastr.success('Pengaturan Penggajian berhasil disimpan!');
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
