<style>
.form-card { border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.04); background: #fff; margin-bottom: 20px; }
.form-card .box-header { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; background: #fafafa; border-radius: 8px 8px 0 0; }
.form-card .box-title { font-weight: 700; font-size: 15px; color: #1e293b; }
.form-card .box-body { padding: 20px; }
.form-group label { font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 6px; }
.form-control { border-radius: 6px; border: 1px solid #cbd5e1; padding: 8px 12px; font-size: 13px; transition: border-color .2s; }
.form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
</style>

<section class="content-header">
   <h1><i class="fa fa-cubes text-primary"></i> Tambah Lokasi Rak <small>Rak Gudang &amp; Baris Picking</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/rak'); ?>">Lokasi Rak</a></li>
      <li class="active">Tambah</li>
   </ol>
</section>

<section class="content">
   <form id="form_rak">
      <div class="row">
         <div class="col-md-12">
            <div class="box form-card" style="border-top: 3px solid #3b82f6;">
               <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-tags text-primary"></i> Form Detail Lokasi Rak</h3>
               </div>
               <div class="box-body">
                  <!-- Row 1: Gudang, Kode Rak, Baris -->
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <label>Pilih Gudang Lokasi <span class="text-danger">*</span></label>
                           <select name="gudang_id" class="form-control" required>
                              <option value="">-- Pilih Gudang --</option>
                              <?php foreach($gudangs as $g): ?>
                              <option value="<?= $g->id; ?>"><?= _ent($g->nama_gudang); ?> (<?= _ent($g->kode_gudang); ?>)</option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label>Kode Rak Fisik <span class="text-danger">*</span></label>
                           <input type="text" class="form-control" name="kode_rak" placeholder="Contoh: RAK-A1, RAK-B2" required>
                           <small class="text-muted">Kode identifikasi unik rak fisik di gudang.</small>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label>Baris / Tingkat Level <span class="text-danger">*</span></label>
                           <input type="text" class="form-control" name="baris" placeholder="Contoh: Baris 1, Level 2, Saf B" required>
                           <small class="text-muted">Posisi vertikal tempat produk disimpan.</small>
                        </div>
                     </div>
                  </div>

                  <!-- Row 2: Kategori & Status -->
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>Kategori / Zona Rak</label>
                           <input type="text" class="form-control" name="kategori_rak" placeholder="Contoh: Fast Moving, Area Cold Storage, Transit, Overstock" value="Utama">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>Status Rak <span class="text-danger">*</span></label>
                           <select name="status" class="form-control">
                              <option value="aktif">Aktif (Dapat Digunakan)</option>
                              <option value="nonaktif">Non-Aktif (Dikunci)</option>
                           </select>
                        </div>
                     </div>
                  </div>

                  <!-- Row 3: Keterangan Deskripsi -->
                  <div class="form-group" style="margin-bottom:0;">
                     <label>Keterangan / Peruntukan Rak</label>
                     <textarea class="form-control" name="deskripsi" rows="3" placeholder="Catatan tambahan mengenai lokasi atau peruntukan barang pada rak ini..."></textarea>
                  </div>
               </div>

               <!-- Card Footer: Action Buttons at the Bottom! -->
               <div class="box-footer" style="padding:15px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
                  <div class="pull-left">
                     <a href="<?= base_url('administrator/rak'); ?>" class="btn btn-default btn-flat" style="border-radius:6px; font-weight:600; padding:8px 18px;">
                        <i class="fa fa-arrow-left"></i> Batal / Kembali
                     </a>
                  </div>
                  <div class="pull-right">
                     <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:6px; font-weight:700; padding:8px 24px; font-size:14px;">
                        <i class="fa fa-save"></i> Simpan Lokasi Rak
                     </button>
                  </div>
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
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

      $.ajax({
         url: '<?= site_url("administrator/rak/add_save"); ?>',
         method: 'POST',
         data: $('#form_rak').serialize() + '&save_type=redirect',
         dataType: 'json',
         success: function(res){
            if(res.success){
               if(res.redirect){
                  window.location.href = res.redirect;
               } else {
                  show_alert(res.message || 'Lokasi Rak berhasil disimpan.', 'success');
                  window.location.href = '<?= base_url("administrator/rak"); ?>';
               }
            } else {
               show_alert(res.message || 'Gagal menyimpan data.', 'error');
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Lokasi Rak');
            }
         },
         error: function(xhr, status, err){
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (xhr.responseText ? 'Error: ' + xhr.status + ' ' + xhr.statusText : 'Terjadi kesalahan server saat menyimpan data.');
            show_alert(msg, 'error');
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Lokasi Rak');
         }
      });
   });
});
</script>
