<section class="content-header">
   <h1>Hari Libur <small style="font-weight: 400; color: #777;">Tambah Hari Libur Baru</small></h1>
   <ol class="breadcrumb" style="background: transparent;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/hr_hari_libur'); ?>">Hari Libur</a></li>
      <li class="active">Tambah</li>
   </ol>
</section>

<style>
   .modern-panel { border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; border-top: 4px solid #3c8dbc; max-width: 600px; margin-left: auto; margin-right: auto; }
   .modern-panel-heading { background: #f8fbff; padding: 15px 20px; border-bottom: 1px solid #eee; text-align: center; }
   .modern-panel-title { margin: 0; font-size: 18px; font-weight: 600; color: #333; }
   .input-group-addon { background-color: #f4f6f9; border-color: #d2d6de; }
</style>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         
         <form id="form_libur" method="POST" action="<?= site_url('administrator/hr_hari_libur/add_save'); ?>">
            
            <div class="box modern-panel">
               <div class="modern-panel-heading">
                  <h4 class="modern-panel-title"><i class="fa fa-calendar-plus-o text-primary margin-r-5"></i> Form Tambah Hari Libur</h4>
               </div>
               <div class="box-body" style="padding: 30px;">
                  
                  <div class="form-group">
                     <label>Tanggal Libur <span class="text-danger">*</span></label>
                     <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="date" class="form-control input-lg" name="tanggal" required>
                     </div>
                  </div>

                  <div class="form-group">
                     <label>Keterangan <span class="text-danger">*</span></label>
                     <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-quote-left"></i></span>
                        <input type="text" class="form-control input-lg" name="keterangan" placeholder="Contoh: Hari Kemerdekaan RI" required>
                     </div>
                  </div>

                  <div class="form-group" style="margin-top: 20px; background: #fff4f4; padding: 15px; border-radius: 8px; border: 1px solid #f2dede;">
                     <div class="checkbox" style="margin: 0;">
                        <label style="font-weight: bold; color: #dd4b39;">
                           <input type="checkbox" name="is_national" value="1"> 
                           <i class="fa fa-flag margin-r-5"></i> Tandai sebagai Libur Nasional / Tanggal Merah
                        </label>
                     </div>
                  </div>

               </div>
               <div class="box-footer text-center" style="padding: 20px 25px; background: #fdfdfd; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                  <a class="btn btn-default btn-lg" id="btn_cancel" href="<?= site_url('administrator/hr_hari_libur'); ?>" title="Batal" style="border-radius: 6px; padding: 8px 25px; margin-right: 10px;"><i class="fa fa-undo"></i> Batal</a>
                  <button class="btn btn-primary btn-lg" id="btn_save" type="submit" style="border-radius: 6px; padding: 8px 30px;"><i class="fa fa-save"></i> Simpan Data</button>
                  <span class="loading loading-hide" style="display: block; margin-top: 15px;"><img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg"> <i style="color: #666;">Memproses...</i></span>
               </div>
            </div>

         </form>
      </div>
   </div>
</section>

<script>
$(document).ready(function(){
   $("#form_libur").submit(function(e){
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
               window.location.href = res.redirect;
            } else {
               swal("Gagal", res.message, "error");
               btn.html(originalText).prop('disabled', false);
            }
         },
         error: function() {
            swal("Error", "Gagal terhubung ke server", "error");
            btn.html(originalText).prop('disabled', false);
         }
      });
   });
});
</script>
