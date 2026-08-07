<?php /* gudang_add.php - Modern & Clean Full-Width Layout */
$is_edit = isset($gudang);
$action_url = $is_edit ? site_url('administrator/gudang/edit_save/'.$gudang->id) : site_url('administrator/gudang/add_save');
?>
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
   <h1><i class="fa fa-building text-primary"></i> <?= $is_edit ? 'Edit' : 'Tambah'; ?> Gudang <small>Kelola Master Gudang Penyimpanan</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/gudang'); ?>">Gudang</a></li>
      <li class="active"><?= $is_edit ? 'Edit' : 'Tambah'; ?></li>
   </ol>
</section>

<section class="content">
<form id="form_gudang">
   <div class="row">
      <div class="col-md-12">
         <div class="box form-card" style="border-top: 3px solid #3b82f6;">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-building-o text-primary"></i> Informasi &amp; Lokasi Gudang</h3>
            </div>
            <div class="box-body">
               <!-- Row 1: Kode, Nama, Tipe Gudang -->
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Kode Gudang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_gudang"
                           value="<?= $is_edit ? _ent($gudang->kode_gudang) : _ent($kode_baru); ?>"
                           <?= $is_edit ? 'readonly' : ''; ?> required>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Nama Gudang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_gudang" placeholder="Masukkan nama gudang..."
                           value="<?= $is_edit ? _ent($gudang->nama_gudang) : ''; ?>" required>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Tipe Gudang <span class="text-danger">*</span></label>
                        <select class="form-control" name="tipe_gudang">
                           <option <?= ($is_edit && $gudang->tipe_gudang == 'utama') ? 'selected' : ''; ?> value="utama">Utama (Gudang Induk)</option>
                           <option <?= ($is_edit && $gudang->tipe_gudang == 'cabang') ? 'selected' : ''; ?> value="cabang">Cabang (Gudang Daerah)</option>
                           <option <?= ($is_edit && $gudang->tipe_gudang == 'transit') ? 'selected' : ''; ?> value="transit">Transit (Pengiriman)</option>
                           <option <?= ($is_edit && $gudang->tipe_gudang == 'retur') ? 'selected' : ''; ?> value="retur">Retur (Barang Rusak/Afkir)</option>
                        </select>
                     </div>
                  </div>
               </div>

               <!-- Row 2: PIC, Telepon, Status -->
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>PIC (Penanggung Jawab)</label>
                        <input type="text" class="form-control" name="pic_name" placeholder="Nama Kepala Gudang / Supervisor" value="<?= $is_edit ? _ent($gudang->pic_name) : ''; ?>">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>No. Telepon Kontak</label>
                        <input type="text" class="form-control" name="telepon" placeholder="08xxxxxxxxxx" value="<?= $is_edit ? _ent($gudang->telepon) : ''; ?>">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Status Gudang <span class="text-danger">*</span></label>
                        <select class="form-control" name="status">
                           <option <?= (!$is_edit || $gudang->status=='aktif') ? 'selected' : ''; ?> value="aktif">Aktif (Dapat Digunakan)</option>
                           <option <?= ($is_edit && $gudang->status=='nonaktif') ? 'selected' : ''; ?> value="nonaktif">Nonaktif (Dikunci)</option>
                        </select>
                     </div>
                  </div>
               </div>

               <!-- Row 3: Alamat Lengkap -->
               <div class="form-group" style="margin-bottom:0;">
                  <label>Alamat Lengkap Gudang</label>
                  <textarea class="form-control" name="alamat" rows="3" placeholder="Jalan, Blok, Kompleks Pergudangan, Kota..."><?= $is_edit ? _ent($gudang->alamat) : ''; ?></textarea>
               </div>

               <?php if($is_edit && !empty($rak_list)): ?>
               <div style="margin-top:25px;">
                  <h4 style="font-weight:700; font-size:14px; color:#334155; border-bottom:2px solid #e2e8f0; padding-bottom:8px; margin-bottom:15px;">
                     <i class="fa fa-cubes text-info"></i> Daftar Rak Penyimpanan Terdaftar
                  </h4>
                  <table class="table table-bordered table-striped">
                     <thead>
                        <tr class="active">
                           <th>Kode Rak</th>
                           <th>Baris</th>
                           <th>Deskripsi Keterangan</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach($rak_list as $r): ?>
                        <tr>
                           <td><span class="label label-info"><i class="fa fa-tag"></i> <?= _ent($r->kode_rak); ?></span></td>
                           <td>Baris Ke-<?= _ent($r->baris); ?></td>
                           <td><?= _ent($r->deskripsi ?: '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>
               <?php endif; ?>
            </div>

            <!-- Card Footer: Action Buttons at the Bottom! -->
            <div class="box-footer" style="padding:15px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
               <div class="pull-left">
                  <a href="<?= base_url('administrator/gudang'); ?>" class="btn btn-default btn-flat" style="border-radius:6px; font-weight:600; padding:8px 18px;">
                     <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                  </a>
               </div>
               <div class="pull-right">
                  <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:6px; font-weight:700; padding:8px 24px; font-size:14px;">
                     <i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Gudang' : 'Simpan Gudang Baru'; ?>
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
   $('#btn_save').click(function(){
      var btn = $(this);
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
      $.ajax({ 
         url: '<?= $action_url; ?>', 
         method: 'POST', 
         data: $('#form_gudang').serialize() + '&save_type=redirect', 
         dataType: 'json',
         success: function(res){ 
            if(res.success){ 
               window.location.href = res.redirect || '<?= base_url("administrator/gudang"); ?>'; 
            } else { 
               show_alert(res.message, 'error'); 
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Gudang' : 'Simpan Gudang Baru'; ?>'); 
            } 
         },
         error: function(){ 
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Gudang' : 'Simpan Gudang Baru'; ?>'); 
         }
      });
   });
});
</script>
