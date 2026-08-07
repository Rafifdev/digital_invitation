<section class="content-header">
   <h1>Edit Lokasi Rak <small>Perbarui Kode Rak & Baris</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/rak'); ?>"><i class="fa fa-cubes"></i> Lokasi Rak</a></li>
      <li class="active">Edit</li>
   </ol>
</section>

<section class="content">
   <form id="form_rak_edit">
      <div class="row">
         <div class="col-md-8">
            <div class="box box-primary">
               <div class="box-header with-border">
                  <h3 class="box-title">Form Edit Lokasi Rak</h3>
               </div>
               <div class="box-body">
                  <div class="form-group">
                     <label>Pilih Gudang <span class="text-danger">*</span></label>
                     <select name="gudang_id" class="form-control" required>
                        <option value="">-- Pilih Gudang --</option>
                        <?php foreach($gudangs as $g): ?>
                        <option value="<?= $g->id; ?>" <?= $rak->gudang_id == $g->id ? 'selected' : ''; ?>><?= _ent($g->nama_gudang); ?> (<?= _ent($g->kode_gudang); ?>)</option>
                        <?php endforeach; ?>
                     </select>
                  </div>

                  <div class="form-group">
                     <label>Kode Rak <span class="text-danger">*</span></label>
                     <input type="text" class="form-control" name="kode_rak" value="<?= _ent($rak->kode_rak); ?>" required>
                  </div>

                  <div class="form-group">
                     <label>Baris / Tingkat <span class="text-danger">*</span></label>
                     <input type="text" class="form-control" name="baris" value="<?= _ent($rak->baris); ?>" required>
                  </div>

                  <div class="form-group">
                     <label>Kategori / Zona Rak</label>
                     <input type="text" class="form-control" name="kategori_rak" value="<?= _ent($rak->kategori_rak); ?>">
                  </div>

                  <div class="form-group">
                     <label>Keterangan</label>
                     <textarea class="form-control" name="deskripsi" rows="3"><?= _ent($rak->deskripsi); ?></textarea>
                  </div>

                  <div class="form-group">
                     <label>Status</label>
                     <select name="status" class="form-control">
                        <option value="aktif" <?= $rak->status == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="nonaktif" <?= $rak->status == 'nonaktif' ? 'selected' : ''; ?>>Non-Aktif</option>
                     </select>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-4">
            <div class="box">
               <div class="box-body">
                  <button type="button" id="btn_save" class="btn btn-primary btn-block btn-flat"><i class="fa fa-save"></i> Perbarui Lokasi Rak</button>
                  <a href="<?= base_url('administrator/rak'); ?>" class="btn btn-default btn-block btn-flat"><i class="fa fa-arrow-left"></i> Batal / Kembali</a>
               </div>
            </div>
         </div>
      </div>
   </form>
</section>

<script>
$(document).ready(function(){
   $('#btn_save').click(function(e){
      e.preventDefault();
      var btn = $(this);
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memperbarui...');

      $.ajax({
         url: '<?= site_url("administrator/rak/edit_save/" . $rak->id); ?>',
         method: 'POST',
         data: $('#form_rak_edit').serialize() + '&save_type=redirect',
         dataType: 'json',
         success: function(res){
            if(res.success){
               if(res.redirect){
                  window.location.href = res.redirect;
               } else {
                  show_alert(res.message || 'Lokasi Rak berhasil diperbarui.', 'success');
                  window.location.href = '<?= base_url("administrator/rak"); ?>';
               }
            } else {
               show_alert(res.message || 'Gagal memperbarui data.', 'error');
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> Perbarui Lokasi Rak');
            }
         },
         error: function(xhr, status, err){
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (xhr.responseText ? 'Error: ' + xhr.status + ' ' + xhr.statusText : 'Terjadi kesalahan server saat menyimpan data.');
            show_alert(msg, 'error');
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Perbarui Lokasi Rak');
         }
      });
   });
});
</script>
