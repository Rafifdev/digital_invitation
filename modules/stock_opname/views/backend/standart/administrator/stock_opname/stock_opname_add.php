<?php /* stock_opname_add.php - Modern Full-Width Layout */ ?>
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
   <h1><i class="fa fa-clipboard text-primary"></i> Buat Stock Opname <small>Siklus Audit &amp; Penghitungan Stok</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/stock_opname'); ?>">Stock Opname</a></li>
      <li class="active">Buat</li>
   </ol>
</section>

<section class="content">
<form id="form_opname">
<div class="row">
   <div class="col-md-12">
      <div class="box form-card" style="border-top: 3px solid #3b82f6;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-file-text-o text-primary"></i> Detail &amp; Parameter Stock Opname</h3>
         </div>
         <div class="box-body">
            <div class="callout callout-info" style="border-radius:6px; margin-bottom:20px;">
               <p><i class="fa fa-info-circle"></i> Setelah disimpan, stok sistem akan dimuat secara otomatis. Anda kemudian dapat menginput stok fisik hasil penghitungan riil pergudangan.</p>
            </div>

            <!-- Row 1: Nomor, Gudang, Tanggal, Jadwal -->
            <div class="row">
               <div class="col-md-3">
                  <div class="form-group">
                     <label>Nomor Dokumentasi Opname</label>
                     <input type="text" class="form-control" value="<?= _ent($nomor_baru ?? ''); ?>" readonly>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group">
                     <label>Gudang Target Opname <span class="text-danger">*</span></label>
                     <select class="form-control chosen" name="gudang_id" required>
                        <option value="">-- Pilih Gudang --</option>
                        <?php foreach($gudangs as $g): ?>
                           <option value="<?= $g->id; ?>"><?= _ent($g->nama_gudang); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group">
                     <label>Tanggal Opname <span class="text-danger">*</span></label>
                     <input type="date" class="form-control" name="tanggal_opname" value="<?= date('Y-m-d'); ?>" required>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group">
                     <label>Jadwal / Periode Opname</label>
                     <select class="form-control" name="jadwal">
                        <option value="harian">Harian (Daily Check)</option>
                        <option value="mingguan">Mingguan (Weekly Audit)</option>
                        <option value="bulanan" selected>Bulanan (Monthly Audit)</option>
                        <option value="tahunan">Tahunan (Annual Opname)</option>
                        <option value="mendadak">Mendadak (Spot Check)</option>
                     </select>
                  </div>
               </div>
            </div>

            <!-- Row 2: Catatan -->
            <div class="form-group" style="margin-bottom:0;">
               <label>Catatan / Peruntukan Stock Opname</label>
               <textarea class="form-control" name="catatan" rows="3" placeholder="Catatan tujuan opname, tim pemeriksa, atau keterangan khusus..."></textarea>
            </div>
         </div>

         <!-- Card Footer: Action Buttons at the Bottom! -->
         <div class="box-footer" style="padding:15px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
            <div class="pull-left">
               <a href="<?= base_url('administrator/stock_opname'); ?>" class="btn btn-default btn-flat" style="border-radius:6px; font-weight:600; padding:8px 18px;">
                  <i class="fa fa-arrow-left"></i> Kembali ke Daftar Opname
               </a>
            </div>
            <div class="pull-right">
               <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:6px; font-weight:700; padding:8px 24px; font-size:14px;">
                  <i class="fa fa-save"></i> Buat Opname &amp; Muat Stok
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
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memuat...');
      $.ajax({
         url: '<?= site_url("administrator/stock_opname/add_save"); ?>',
         method: 'POST',
         data: $('#form_opname').serialize(),
         dataType: 'json',
         success: function(res){
            if(res.success){
               window.location.href = res.redirect;
            } else {
               show_alert(res.message, 'error');
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> Buat Opname &amp; Muat Stok');
            }
         },
         error: function(){
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Buat Opname &amp; Muat Stok');
         }
      });
   });
});
</script>
