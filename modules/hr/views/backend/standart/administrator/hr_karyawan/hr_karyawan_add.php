
<section class="content-header">
   <h1>Tambah Karyawan</h1>
</section>
<section class="content">
   <div class="box box-warning">
      <div class="box-body">
         <form id="form_karyawan" method="POST" action="<?= site_url('administrator/hr_karyawan/add_save'); ?>">
            <div class="form-group">
               <label>Pilih Akun User <span class="text-danger">*</span></label>
               <select class="form-control chosen" name="user_id" required>
                  <option value="">-- Pilih User --</option>
                  <?php foreach($users as $u): ?>
                     <option value="<?= $u->id; ?>"><?= $u->full_name; ?> (<?= $u->username; ?>)</option>
                  <?php endforeach; ?>
               </select>
            </div>
            <div class="form-group">
               <label>NIK <span class="text-danger">*</span></label>
               <input type="text" class="form-control" name="nik" required>
            </div>
            <div class="form-group">
               <label>Jabatan</label>
               <input type="text" class="form-control" name="jabatan">
            </div>
            <div class="form-group">
               <label>Departemen</label>
               <input type="text" class="form-control" name="departemen">
            </div>
            <div class="form-group">
               <label>Tipe Kerja</label>
               <select class="form-control" name="tipe_kerja">
                  <option value="WFO">Work From Office (WFO) - Wajib Absen di Kantor</option>
                  <option value="WFH">Work From Home (WFH) - Bebas Lokasi</option>
                  <option value="WFA">Work From Anywhere (WFA) - Bebas Lokasi</option>
               </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
            <a href="<?= site_url('administrator/hr_karyawan'); ?>" class="btn btn-default"><i class="fa fa-undo"></i> Batal</a>
         </form>
      </div>
   </div>
</section>
<script>
$(document).ready(function(){
   $("#form_karyawan").submit(function(e){
      e.preventDefault();
      var btn = $(this).find('button[type="submit"]');
      btn.prop('disabled', true);
      $.ajax({
         url: $(this).attr('action'),
         type: 'POST',
         data: $(this).serialize(),
         dataType: 'json',
         success: function(res){
            if(res.success){
               window.location.href = res.redirect;
            }else{
               alert(res.message);
               btn.prop('disabled', false);
            }
         }
      });
   });
});
</script>
