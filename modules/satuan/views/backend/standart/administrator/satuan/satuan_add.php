<?php /* satuan_add.php */
$is_edit = isset($satuan);
$action_url = $is_edit ? site_url('administrator/satuan/edit_save/'.$satuan->id) : site_url('administrator/satuan/add_save');
?>
<section class="content-header">
   <h1><?= $is_edit ? 'Edit' : 'Tambah'; ?> Satuan / Unit Barang</h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/satuan'); ?>">Satuan Barang</a></li>
      <li class="active"><?= $is_edit ? 'Edit' : 'Tambah'; ?></li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-6 col-md-offset-3">
         <div class="box box-primary" style="border-radius:6px;">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-tag text-primary"></i> Form <?= $is_edit ? 'Edit' : 'Tambah'; ?> Satuan</h3>
            </div>
            <form id="form_satuan">
               <div class="box-body">
                  <div class="form-group">
                     <label>Nama Satuan / Unit <span class="text-danger">*</span></label>
                     <input type="text" class="form-control" name="nama_satuan" value="<?= $is_edit ? _ent($satuan->nama_satuan) : ''; ?>" placeholder="Contoh: Kilogram, Pieces, Box, Roll" required>
                  </div>
                  <div class="form-group">
                     <label>Singkatan / Simbol <span class="text-danger">*</span></label>
                     <input type="text" class="form-control" name="singkatan" value="<?= $is_edit ? _ent($satuan->singkatan) : ''; ?>" placeholder="Contoh: kg, pcs, box, rl" required>
                  </div>
                  <div class="form-group">
                     <label>Kode Satuan</label>
                     <input type="text" class="form-control" name="kode_satuan" value="<?= $is_edit ? _ent($satuan->kode_satuan) : ''; ?>" placeholder="Contoh: UNT-001">
                  </div>
                  <div class="form-group">
                     <label>Deskripsi / Keterangan</label>
                     <textarea class="form-control" name="deskripsi" rows="3" placeholder="Keterangan penggunaan satuan..."><?= $is_edit ? _ent($satuan->deskripsi) : ''; ?></textarea>
                  </div>
               </div>
               <div class="box-footer text-right">
                  <a href="<?= base_url('administrator/satuan'); ?>" class="btn btn-default btn-flat" style="border-radius:4px;"><i class="fa fa-arrow-left"></i> Batal</a>
                  <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:4px;"><i class="fa fa-save"></i> Simpan Data</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</section>

<script>
$(document).ready(function(){
   $('#btn_save').click(function(){
      var btn = $(this); btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
      $.ajax({
         url: '<?= $action_url; ?>',
         method: 'POST',
         data: $('#form_satuan').serialize(),
         dataType: 'json',
         success: function(res) {
            if (res.success) {
               window.location.href = res.redirect;
            } else {
               swal("Gagal", res.message || "Gagal menyimpan data", "error");
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Data');
            }
         },
         error: function() {
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Data');
         }
      });
   });
});
</script>
