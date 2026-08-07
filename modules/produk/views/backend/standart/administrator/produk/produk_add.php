<?php /* produk_add.php - Modern & Clean Full-Width Layout */
$is_edit = isset($produk);
$action_url = $is_edit ? site_url('administrator/produk/edit_save/'.$produk->id) : site_url('administrator/produk/add_save');
?>
<style>
.form-card { border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.04); background: #fff; margin-bottom: 20px; }
.form-card .box-header { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; background: #fafafa; border-radius: 8px 8px 0 0; }
.form-card .box-title { font-weight: 700; font-size: 15px; color: #1e293b; }
.form-card .box-body { padding: 20px; }
.form-group label { font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 6px; }
.form-control { border-radius: 6px; border: 1px solid #cbd5e1; padding: 8px 12px; font-size: 13px; transition: border-color .2s; }
.form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.section-divider { margin: 20px 0 15px 0; font-size: 14px; font-weight: 700; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; }
</style>

<section class="content-header">
   <h1><i class="fa fa-cube text-primary"></i> <?= $is_edit ? 'Edit' : 'Tambah'; ?> Produk <small>Kelola Katalis Master Produk</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/produk'); ?>">Produk</a></li>
      <li class="active"><?= $is_edit ? 'Edit' : 'Tambah'; ?></li>
   </ol>
</section>

<section class="content">
<form id="form_produk">
   <div class="row">
      <div class="col-md-12">
         <div class="box form-card" style="border-top: 3px solid #3b82f6;">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-cubes text-primary"></i> Informasi &amp; Pengaturan Produk</h3>
            </div>
            <div class="box-body">
               <!-- Section 1: Informasi Utama Produk -->
               <div class="section-divider"><i class="fa fa-info-circle text-primary"></i> Identitas Utama Produk</div>
               
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Kode Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_produk"
                           value="<?= $is_edit ? _ent($produk->kode_produk) : _ent($kode_baru); ?>"
                           <?= $is_edit ? 'readonly' : ''; ?> required>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Barcode Scanner <small class="text-muted">(Otomatis/Manual)</small></label>
                        <input type="text" class="form-control" name="barcode" value="<?= $is_edit ? _ent($produk->barcode) : _ent($barcode_baru ?? ''); ?>" placeholder="EAN-13 / Code128">
                     </div>
                  </div>
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Nama Produk / Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_produk" placeholder="Nama lengkap produk..."
                           value="<?= $is_edit ? _ent($produk->nama_produk) : ''; ?>" required>
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Kategori Produk</label>
                        <select class="form-control chosen" name="kategori_id">
                           <option value="">-- Pilih Kategori --</option>
                           <?php foreach($kategoris as $k): ?>
                           <option <?= ($is_edit && $produk->kategori_id == $k->id) ? 'selected' : ''; ?> value="<?= $k->id; ?>"><?= _ent($k->nama_kategori); ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Satuan Barang</label>
                        <select class="form-control chosen" name="satuan_id">
                           <option value="">-- Pilih Satuan --</option>
                           <?php foreach($satuans as $s): ?>
                           <option <?= ($is_edit && $produk->satuan_id == $s->id) ? 'selected' : ''; ?> value="<?= $s->id; ?>"><?= _ent($s->nama_satuan); ?> (<?= $s->singkatan; ?>)</option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Berat Satuan (kg)</label>
                        <input type="number" step="0.001" class="form-control" name="berat" placeholder="0.00" value="<?= $is_edit ? $produk->berat : ''; ?>">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Status Katalis <span class="text-danger">*</span></label>
                        <select class="form-control" name="status">
                           <option <?= (!$is_edit || $produk->status == 'aktif') ? 'selected' : ''; ?> value="aktif">Aktif (Dapat Dijual)</option>
                           <option <?= ($is_edit && $produk->status == 'nonaktif') ? 'selected' : ''; ?> value="nonaktif">Nonaktif (Arsip)</option>
                        </select>
                     </div>
                  </div>
               </div>

               <!-- Section 2: Struktur Harga Jual -->
               <div class="section-divider" style="margin-top:25px;"><i class="fa fa-money text-success"></i> Harga Beli &amp; Bertingkat (Retail, Grosir, Distributor)</div>

               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Harga Beli HPP (Rp)</label>
                        <input type="text" class="form-control rupiah" name="harga_beli" placeholder="0" value="<?= $is_edit ? number_format($produk->harga_beli, 0, ',', '.') : '0'; ?>">
                        <?php if($is_edit): ?>
                           <small style="display:block; margin-top:4px;">
                              <a href="<?= site_url('administrator/produk/view/'.$produk->id); ?>" target="_blank" class="text-primary" style="font-weight:600;"><i class="fa fa-line-chart"></i> Cek Riwayat Perubahan Harga</a>
                           </small>
                        <?php endif; ?>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Harga Retail / Eceran (Rp) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rupiah" name="harga_jual" value="<?= $is_edit ? number_format($produk->harga_jual, 0, ',', '.') : '0'; ?>" placeholder="0" required>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Harga Grosir (Rp)</label>
                        <input type="text" class="form-control rupiah" name="harga_jual_grosir" value="<?= $is_edit ? number_format($produk->harga_jual_grosir ?? 0, 0, ',', '.') : '0'; ?>" placeholder="0">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Harga Distributor (Rp)</label>
                        <input type="text" class="form-control rupiah" name="harga_jual_distributor" value="<?= $is_edit ? number_format($produk->harga_jual_distributor ?? 0, 0, ',', '.') : '0'; ?>" placeholder="0">
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Batas Stok Minimum Alert</label>
                        <input type="text" class="form-control rupiah" name="stok_minimum" value="<?= $is_edit ? number_format($produk->stok_minimum, 0, ',', '.') : '0'; ?>" placeholder="0">
                     </div>
                  </div>
                  <div class="col-md-9">
                     <div class="form-group">
                        <label>Deskripsi Ringkas / Spesifikasi</label>
                        <input type="text" class="form-control" name="deskripsi" placeholder="Spesifikasi teknis, warna, garansi..." value="<?= $is_edit ? _ent($produk->deskripsi) : ''; ?>">
                     </div>
                  </div>
               </div>

               <!-- Section 3: Pengaturan Batch & Rak Default -->
               <div class="section-divider" style="margin-top:25px;"><i class="fa fa-sliders text-warning"></i> Pengaturan Batch, Expired &amp; Default Rak Simpan</div>

               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Default Rak Simpan Utama</label>
                        <select class="form-control" name="default_rak_id">
                           <option value="">-- Tanpa Rak Default --</option>
                           <?php foreach($raks as $r): ?>
                           <option <?= ($is_edit && isset($produk->default_rak_id) && $produk->default_rak_id == $r->id) ? 'selected' : ''; ?> value="<?= $r->id; ?>"><?= _ent($r->kode_rak.' (Baris '.$r->baris.')'); ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Metode Penilaian Stok</label>
                        <select class="form-control" name="metode_stok">
                           <?php foreach(['FIFO','FEFO','AVERAGE'] as $m): ?>
                           <option <?= ($is_edit && $produk->metode_stok == $m) ? 'selected' : ($m=='FIFO' && !$is_edit ? 'selected' : ''); ?> value="<?= $m; ?>"><?= $m; ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group" style="padding-top:24px;">
                        <label class="checkbox-inline" style="font-weight:600; margin-right:15px;">
                           <input type="checkbox" name="batch_tracking" value="1" <?= ($is_edit && $produk->batch_tracking) ? 'checked' : ''; ?>> Batch Tracking
                        </label>
                        <label class="checkbox-inline" style="font-weight:600;">
                           <input type="checkbox" name="tanggal_kadaluarsa" value="1" <?= ($is_edit && $produk->tanggal_kadaluarsa) ? 'checked' : ''; ?>> Tracking Expired Date
                        </label>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Card Footer: Action Buttons at the Bottom! -->
            <div class="box-footer" style="padding:15px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
               <div class="pull-left">
                  <a href="<?= base_url('administrator/produk'); ?>" class="btn btn-default btn-flat" style="border-radius:6px; font-weight:600; padding:8px 18px;">
                     <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                  </a>
               </div>
               <div class="pull-right">
                  <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:6px; font-weight:700; padding:8px 24px; font-size:14px;">
                     <i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Produk' : 'Simpan Produk Baru'; ?>
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
         data: $('#form_produk').serialize() + '&save_type=redirect', 
         dataType: 'json',
         success: function(res){
            if(res.success){ 
               if(res.redirect){ window.location.href = res.redirect; } 
               else { show_alert(res.message, 'success'); } 
            } else { 
               show_alert(res.message, 'error'); 
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Produk' : 'Simpan Produk Baru'; ?>'); 
            }
         }, 
         error: function(){ 
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Produk' : 'Simpan Produk Baru'; ?>'); 
         }
      });
   });
});
</script>
