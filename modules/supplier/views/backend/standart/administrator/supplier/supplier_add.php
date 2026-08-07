<?php
/**
 * supplier_add.php / supplier_update.php (form base)
 * View: Form Tambah Supplier - Modern & Clean Full-Width Layout
 */
$is_edit = isset($supplier);
$action_url = $is_edit
    ? site_url('administrator/supplier/edit_save/' . $supplier->id)
    : site_url('administrator/supplier/add_save');
$page_title = $is_edit ? 'Edit Supplier' : 'Tambah Supplier';
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
   <h1><i class="fa fa-truck text-primary"></i> <?= $page_title; ?> <small><?= $is_edit ? _ent($supplier->nama_supplier) : 'Baru'; ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/supplier'); ?>">Supplier</a></li>
      <li class="active"><?= $page_title; ?></li>
   </ol>
</section>

<section class="content">
   <form id="form_supplier">
   <div class="row">
      <div class="col-md-12">
         <div class="box form-card" style="border-top: 3px solid #3b82f6;">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-building-o text-primary"></i> Informasi &amp; Rekening Supplier</h3>
            </div>
            <div class="box-body">
               <!-- Section 1: Data Kontak Supplier -->
               <div class="section-divider"><i class="fa fa-info-circle text-primary"></i> Data Umum &amp; Kontak</div>
               
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Kode Supplier <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_supplier" id="kode_supplier"
                           value="<?= $is_edit ? _ent($supplier->kode_supplier) : _ent($kode_baru); ?>"
                           <?= $is_edit ? 'readonly' : ''; ?> required>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Nama Supplier / Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_supplier" placeholder="Masukkan nama PT / CV / Toko..."
                           value="<?= $is_edit ? _ent($supplier->nama_supplier) : ''; ?>" required>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="status">
                           <option <?= ($is_edit && $supplier->status == 'aktif') || !$is_edit ? 'selected' : ''; ?> value="aktif">Aktif (Dapat Bertransaksi)</option>
                           <option <?= ($is_edit && $supplier->status == 'nonaktif') ? 'selected' : ''; ?> value="nonaktif">Nonaktif (Dikunci)</option>
                        </select>
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Kontak Person</label>
                        <input type="text" class="form-control" name="kontak_person" placeholder="Nama Penanggung Jawab"
                           value="<?= $is_edit ? _ent($supplier->kontak_person) : ''; ?>">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>No. Telepon / HP</label>
                        <input type="text" class="form-control" name="telepon" placeholder="08xxxxxxxxxx"
                           value="<?= $is_edit ? _ent($supplier->telepon) : ''; ?>">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Alamat Email</label>
                        <input type="email" class="form-control" name="email" placeholder="supplier@domain.com"
                           value="<?= $is_edit ? _ent($supplier->email) : ''; ?>">
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Kota / Wilayah</label>
                        <input type="text" class="form-control" name="kota" placeholder="Nama Kota / Kabupaten"
                           value="<?= $is_edit ? _ent($supplier->kota) : ''; ?>">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Nomor NPWP</label>
                        <input type="text" class="form-control" name="npwp" placeholder="00.000.000.0-000.000"
                           value="<?= $is_edit ? _ent($supplier->npwp) : ''; ?>">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Catatan Internal</label>
                        <input type="text" class="form-control" name="catatan" placeholder="Catatan internal supplier..."
                           value="<?= $is_edit ? _ent($supplier->catatan) : ''; ?>">
                     </div>
                  </div>
               </div>

               <div class="form-group">
                  <label>Alamat Lengkap Supplier</label>
                  <textarea class="form-control" name="alamat" rows="2" placeholder="Alamat kantor / gudang supplier..."><?= $is_edit ? _ent($supplier->alamat) : ''; ?></textarea>
               </div>

               <!-- Section 2: Data Keuangan & Rekening -->
               <div class="section-divider" style="margin-top:25px;"><i class="fa fa-bank text-success"></i> Informasi Keuangan &amp; Rekening Pembayaran</div>

               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Mata Uang Transaksi</label>
                        <select class="form-control" name="mata_uang" id="mata_uang_select">
                           <option <?= ($is_edit && $supplier->mata_uang == 'IDR') ? 'selected' : ''; ?> value="IDR">IDR (Rupiah)</option>
                           <option <?= ($is_edit && $supplier->mata_uang == 'USD') ? 'selected' : ''; ?> value="USD">USD (US Dollar)</option>
                           <option <?= ($is_edit && $supplier->mata_uang == 'EUR') ? 'selected' : ''; ?> value="EUR">EUR (Euro)</option>
                           <option <?= ($is_edit && $supplier->mata_uang == 'SGD') ? 'selected' : ''; ?> value="SGD">SGD (Singapore Dollar)</option>
                           <option <?= ($is_edit && $supplier->mata_uang == 'CNY') ? 'selected' : ''; ?> value="CNY">CNY (Chinese Yuan)</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-8">
                     <div class="form-group">
                        <label>Kurs Manual (Nilai Konversi vs IDR)</label>
                        <input type="number" step="0.0001" class="form-control" name="kurs_manual" id="kurs_manual_input"
                           value="<?= $is_edit ? $supplier->kurs_manual : '1'; ?>">
                        <small class="text-muted" id="kurs_help_text">1 IDR = Rp 1 (Rupiah Lokal)</small>
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Nama Bank</label>
                        <input type="text" class="form-control" name="bank_name" placeholder="BCA / Mandiri / BNI..."
                           value="<?= $is_edit ? _ent($supplier->bank_name) : ''; ?>">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>No. Rekening Bank</label>
                        <input type="text" class="form-control" name="bank_account" placeholder="1234567890"
                           value="<?= $is_edit ? _ent($supplier->bank_account) : ''; ?>">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Nama Pemilik Rekening</label>
                        <input type="text" class="form-control" name="bank_holder" placeholder="A/N Pemilik Rekening"
                           value="<?= $is_edit ? _ent($supplier->bank_holder) : ''; ?>">
                     </div>
                  </div>
               </div>
            </div>

            <!-- Card Footer: Action Buttons at the Bottom! -->
            <div class="box-footer" style="padding:15px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
               <div class="pull-left">
                  <a href="<?= base_url('administrator/supplier'); ?>" class="btn btn-default btn-flat" style="border-radius:6px; font-weight:600; padding:8px 18px;">
                     <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                  </a>
               </div>
               <div class="pull-right">
                  <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:6px; font-weight:700; padding:8px 24px; font-size:14px;">
                     <i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Supplier' : 'Simpan Supplier'; ?>
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
   function fetchLiveExchangeRate(currency) {
      if (currency === 'IDR') {
         $('#kurs_manual_input').val(1);
         $('#kurs_help_text').html('<i class="fa fa-check-circle text-success"></i> 1 IDR = Rp 1 (Rupiah Lokal)');
         return;
      }

      $('#kurs_help_text').html('<i class="fa fa-spinner fa-spin text-primary"></i> Mengambil kurs real-time dari Live API (open.er-api.com)...');

      $.ajax({
         url: 'https://open.er-api.com/v6/latest/' + currency,
         method: 'GET',
         dataType: 'json',
         timeout: 4000,
         success: function(res) {
            if (res && res.result === 'success' && res.rates && res.rates.IDR) {
               var liveRate = parseFloat(res.rates.IDR);
               $('#kurs_manual_input').val(liveRate.toFixed(4));
               $('#kurs_help_text').html('<i class="fa fa-refresh fa-spin text-success"></i> Live Rate (open.er-api.com): <strong>1 ' + currency + ' = Rp ' + liveRate.toLocaleString('id-ID', {maximumFractionDigits: 2}) + '</strong>');
            } else {
               fallbackBackendRate(currency);
            }
         },
         error: function() {
            fallbackBackendRate(currency);
         }
      });
   }

   function fallbackBackendRate(currency) {
      $.ajax({
         url: '<?= site_url("administrator/supplier/get_live_rate"); ?>',
         data: { currency: currency },
         dataType: 'json',
         success: function(res) {
            if (res.success && res.rate) {
               $('#kurs_manual_input').val(res.rate);
               $('#kurs_help_text').html('<i class="fa fa-globe text-primary"></i> Auto Live Rate: <strong>1 ' + currency + ' = Rp ' + Number(res.rate).toLocaleString('id-ID') + '</strong>');
            }
         }
      });
   }

   $('#mata_uang_select').change(function(){
      var val = $(this).val();
      fetchLiveExchangeRate(val);
   });

   var initialCurr = $('#mata_uang_select').val();
   if (initialCurr && initialCurr !== 'IDR' && $('#kurs_manual_input').val() == 1) {
      fetchLiveExchangeRate(initialCurr);
   }

   $('#kurs_manual_input').on('keyup change', function(){
      var val = $('#mata_uang_select').val();
      if (val !== 'IDR') {
         $('#kurs_help_text').html('<i class="fa fa-pencil text-warning"></i> Custom Rate: <strong>1 ' + val + ' = Rp ' + Number($(this).val()).toLocaleString('id-ID') + '</strong>');
      }
   });

   $('#btn_save').click(function(e){
      e.preventDefault();
      var btn = $(this);
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

      $.ajax({
         url: '<?= $action_url; ?>',
         method: 'POST',
         data: $('#form_supplier').serialize() + '&save_type=redirect',
         dataType: 'json',
         success: function(res){
            if(res.success){
               if(res.redirect){
                  window.location.href = res.redirect;
               } else {
                  show_alert(res.message || 'Supplier berhasil disimpan.', 'success');
                  btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Supplier' : 'Simpan Supplier'; ?>');
               }
            } else {
               show_alert(res.message || 'Gagal menyimpan data. Periksa kembali inputan Anda.', 'error');
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Supplier' : 'Simpan Supplier'; ?>');
            }
         },
         error: function(xhr, status, err){
            show_alert('Terjadi kesalahan server saat menyimpan data.', 'error');
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Supplier' : 'Simpan Supplier'; ?>');
         }
      });
   });
});
</script>
