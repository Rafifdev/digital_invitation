<section class="content-header">
   <h1>Jadwal Shift <small style="font-weight: 400; color: #777;">Tambah Jadwal Baru</small></h1>
   <ol class="breadcrumb" style="background: transparent;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/hr_shift'); ?>">Jadwal Shift</a></li>
      <li class="active">Tambah</li>
   </ol>
</section>

<style>
   .modern-panel { border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; border-top: 4px solid #3c8dbc; max-width: 800px; margin-left: auto; margin-right: auto; }
   .modern-panel-heading { background: #f8fbff; padding: 15px 20px; border-bottom: 1px solid #eee; text-align: center; }
   .modern-panel-title { margin: 0; font-size: 18px; font-weight: 600; color: #333; }
   .input-group-addon { background-color: #f4f6f9; border-color: #d2d6de; }
</style>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         
         <form id="form_hr_shift" action="<?= site_url('administrator/hr_shift/add_save'); ?>" method="POST">
            
            <div class="box modern-panel">
               <div class="modern-panel-heading">
                  <h4 class="modern-panel-title"><i class="fa fa-clock-o text-primary margin-r-5"></i> Form Tambah Jadwal Shift</h4>
                  <p class="text-muted" style="margin-top: 5px; margin-bottom: 0; font-size: 13px;">Lengkapi data jadwal kerja shift baru di bawah ini.</p>
               </div>
               <div class="box-body" style="padding: 30px;">
                  
                  <div class="form-group">
                     <label>Nama Shift <span class="text-danger">*</span></label>
                     <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                        <input type="text" class="form-control input-lg" name="nama_shift" id="nama_shift" placeholder="Contoh: Shift Pagi / Reguler" required>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>Jam Masuk <span class="text-danger">*</span></label>
                           <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-sign-in text-success"></i></span>
                              <input type="time" class="form-control input-lg" name="jam_masuk" id="jam_masuk" value="08:00" required>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>Jam Pulang <span class="text-danger">*</span></label>
                           <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-sign-out text-danger"></i></span>
                              <input type="time" class="form-control input-lg" name="jam_pulang" id="jam_pulang" value="17:00" required>
                           </div>
                        </div>
                     </div>
                  </div>

               </div>
               <div class="box-footer text-center" style="padding: 20px 25px; background: #fdfdfd; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                  <a class="btn btn-default btn-lg" id="btn_cancel" title="Batal" style="border-radius: 6px; padding: 8px 25px; margin-right: 10px;"><i class="fa fa-undo"></i> Batal</a>
                  <button class="btn btn-primary btn-lg" id="btn_save" type="submit" style="border-radius: 6px; padding: 8px 30px;"><i class="fa fa-save"></i> Simpan Shift</button>
                  <span class="loading loading-hide" style="display: block; margin-top: 15px;"><img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg"> <i style="color: #666;">Memproses...</i></span>
               </div>
            </div>

         </form>
      </div>
   </div>
</section>

<script>
$(document).ready(function(){
   $('#btn_cancel').click(function(){
      window.location.href = '<?= site_url("administrator/hr_shift"); ?>';
   });

   $('#form_hr_shift').submit(function(e){
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
</script>
