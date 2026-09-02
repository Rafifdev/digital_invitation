<?php
$is_edit = isset($retur);
$action_url = $is_edit
    ? site_url('administrator/retur_penjualan/edit_save/' . $retur->id)
    : site_url('administrator/retur_penjualan/add_save');
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
   <h1><i class="fa fa-reply-all text-primary"></i> <?= $is_edit ? 'Edit' : 'Buat'; ?> Retur Penjualan <small><?= _ent($nomor_baru ?? ''); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/retur_penjualan'); ?>">Retur Penjualan</a></li>
      <li class="active"><?= $is_edit ? 'Edit' : 'Buat'; ?></li>
   </ol>
</section>

<section class="content">
<form id="form_retur_jual">
<div class="row">
   <div class="col-md-12">
      <!-- Header Retur Box -->
      <div class="box form-card" style="border-top: 3px solid #3b82f6;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-file-text-o text-primary"></i> Header &amp; Informasi Retur Penjualan</h3>
         </div>
         <div class="box-body">
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group">
                     <label>Referensi Invoice <span class="text-danger">*</span></label>
                     <select class="form-control chosen" name="invoice_id" id="invoice_id" required>
                        <option value="">-- Pilih Invoice --</option>
                        <?php foreach($invoices as $inv): ?><option value="<?= $inv->id; ?>"><?= _ent($inv->nomor_invoice); ?></option><?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group">
                     <label>Gudang Tujuan Pengembalian</label>
                     <select class="form-control" name="gudang_id">
                        <option value="">-- Auto dari Invoice --</option>
                        <?php foreach($gudangs as $g): ?><option value="<?= $g->id; ?>"><?= _ent($g->nama_gudang); ?></option><?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group">
                     <label>Tanggal Retur <span class="text-danger">*</span></label>
                     <input type="date" class="form-control" name="tanggal_retur" value="<?= date('Y-m-d'); ?>" required>
                  </div>
               </div>
            </div>

            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Alasan Retur Utama <span class="text-danger">* (Wajib Diisi)</span></label>
                     <select class="form-control" name="alasan" id="alasan" required>
                        <option value="">-- Pilih Alasan Retur (Wajib) --</option>
                        <option value="cacat" <?= (isset($retur) && $retur->alasan == 'cacat') ? 'selected' : ''; ?>>Cacat / Rusak</option>
                        <option value="salah_kirim" <?= (isset($retur) && $retur->alasan == 'salah_kirim') ? 'selected' : ''; ?>>Salah Kirim Barang</option>
                        <option value="tidak_sesuai" <?= (isset($retur) && $retur->alasan == 'tidak_sesuai') ? 'selected' : ''; ?>>Tidak Sesuai Spesifikasi Pesanan</option>
                        <option value="kadaluarsa" <?= (isset($retur) && $retur->alasan == 'kadaluarsa') ? 'selected' : ''; ?>>Kadaluarsa / Expired</option>
                        <option value="lainnya" <?= (isset($retur) && $retur->alasan == 'lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Tindak Lanjut Retur <span class="text-danger">*</span></label>
                     <select class="form-control" name="tindak_lanjut" required>
                        <option value="tukar_barang" <?= (isset($retur) && $retur->tindak_lanjut == 'tukar_barang') ? 'selected' : ''; ?>>Tukar Barang Baru</option>
                        <option value="refund" <?= (isset($retur) && $retur->tindak_lanjut == 'refund') ? 'selected' : ''; ?>>Refund (Pengembalian Dana)</option>
                        <option value="kredit_poin" <?= (isset($retur) && $retur->tindak_lanjut == 'kredit_poin') ? 'selected' : ''; ?>>Kredit Poin / Nota Kredit</option>
                     </select>
                  </div>
               </div>
            </div>

            <div class="form-group" style="margin-bottom:0;">
               <label>Detail / Catatan Alasan Retur <span class="text-danger">* (Wajib Diisi)</span></label>
               <textarea class="form-control" name="catatan" id="catatan_alasan" rows="2" placeholder="Jelaskan alasan detail pengembalian barang dari pelanggan (wajib)..." required><?= _ent($retur->catatan ?? ''); ?></textarea>
            </div>
         </div>
      </div>

      <!-- Item Box -->
      <div class="box form-card" style="border-top: 3px solid #f59e0b;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list text-warning"></i> Item Produk Retur</h3>
         </div>
         <div class="box-body">
            <div id="items_area">
               <div class="callout callout-info" style="border-radius:6px; margin-bottom:0;">
                  <p><i class="fa fa-info-circle"></i> Pilih invoice di atas untuk memuat item pesanan secara otomatis.</p>
               </div>
            </div>
         </div>

         <!-- Card Footer: Action Buttons at the Bottom! -->
         <div class="box-footer" style="padding:15px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
            <div class="pull-left">
               <a href="<?= base_url('administrator/retur_penjualan'); ?>" class="btn btn-default btn-flat" style="border-radius:6px; font-weight:600; padding:8px 18px;">
                  <i class="fa fa-arrow-left"></i> Kembali ke Daftar Retur
               </a>
            </div>
            <div class="pull-right">
               <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:6px; font-weight:700; padding:8px 24px; font-size:14px;" disabled>
                  <i class="fa fa-save"></i> <?= $is_edit ? 'Update Retur Penjualan' : 'Simpan Retur Penjualan'; ?>
               </button>
            </div>
         </div>
      </div>
   </div>
</div>
</form>
</section>

<script>
function formatRupiahVal(val) {
   var number_string = Math.round(parseFloat(val || 0)).toString().replace(/[^0-9]/g, '');
   var sisa   = number_string.length % 3;
   var rupiah = number_string.substr(0, sisa);
   var ribuan = number_string.substr(sisa).match(/\d{3}/g);
   if (ribuan) {
      var separator = sisa ? '.' : '';
      rupiah += separator + ribuan.join('.');
   }
   return rupiah || '0';
}

$(document).ready(function(){
   $(document).on('keyup input', '.rupiah', function() {
      var val = $(this).val().replace(/[^0-9]/g, '');
      $(this).val(formatRupiahVal(val));
   });

   $(document).on('input keyup change', '#catatan_alasan', function() {
      var currentVal = $(this).val();
      $('.item-alasan-input').val(currentVal);
   });

   $('#alasan').on('change', function() {
      var alasanVal = $(this).val();
      if (alasanVal == 'cacat' || alasanVal == 'kadaluarsa') {
         $('.item-kondisi-select').val('rusak');
      } else if (alasanVal == 'salah_kirim' || alasanVal == 'tidak_sesuai') {
         $('.item-kondisi-select').val('baik');
      }
   });

   $('#invoice_id').on('change',function(){
      var inv_id=$(this).val();
      if(!inv_id){$('#btn_save').prop('disabled',true);return;}
      $.get('<?= site_url("administrator/retur_penjualan/get_invoice_items"); ?>',{invoice_id:inv_id},function(r){
         if(r.success && r.items.length>0){
            var currentHeaderCatatan = $('#catatan_alasan').val() || '';
            var currentAlasanHeader  = $('#alasan').val() || '';
            var defaultKondisi       = (currentAlasanHeader == 'cacat' || currentAlasanHeader == 'kadaluarsa') ? 'rusak' : 'baik';

            var html='<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr class="active"><th>Produk</th><th class="text-center" width="90">Qty Invoice</th><th width="110">Qty Retur</th><th width="150">Harga Satuan (IDR)</th><th width="170">Kondisi Barang Retur</th><th width="180">Lokasi Rak Penempatan</th><th>Alasan Spesifik Item</th></tr></thead><tbody>';
            r.items.forEach(function(it,idx){
                var qty_inv = Math.round(parseFloat(it.qty || 0));
                var harga_sat = Math.round(parseFloat(it.harga_satuan || 0));
                var selBaik = defaultKondisi == 'baik' ? 'selected' : '';
                var selRusak = defaultKondisi == 'rusak' ? 'selected' : '';

                var rakOptions = '<option value="">-- Auto Rak Default --</option>';
                if (r.raks && r.raks.length > 0) {
                   r.raks.forEach(function(rk){
                      var selRk = (it.default_rak_id == rk.id) ? 'selected' : '';
                      rakOptions += '<option value="'+rk.id+'" '+selRk+'>📍 '+rk.kode_rak+' ('+(rk.nama_gudang || 'Gudang')+')</option>';
                   });
                }

                html+='<tr class="item-row"><input type="hidden" name="items['+idx+'][produk_id]" value="'+it.produk_id+'"><td><strong>'+(it.nama_produk ? it.nama_produk : '<i class="text-muted">(Produk Telah Dihapus)</i>')+'</strong></td><td class="text-center"><strong>'+qty_inv+'</strong></td><td><input type="number" step="1" min="1" class="form-control" name="items['+idx+'][qty_retur]" value="1" max="'+qty_inv+'"></td><td><input type="text" class="form-control rupiah" name="items['+idx+'][harga_satuan]" value="'+formatRupiahVal(harga_sat)+'"></td><td><select class="form-control item-kondisi-select" name="items['+idx+'][kondisi]"><option value="rusak" '+selRusak+'>🔴 Rusak (Stok Rusak/Afkir)</option><option value="baik" '+selBaik+'>🟢 Baik (Stok Aktif Dijual)</option></select></td><td><select class="form-control rak-select" name="items['+idx+'][rak_id]">'+rakOptions+'</select></td><td><input type="text" class="form-control item-alasan-input" name="items['+idx+'][alasan]" value="'+currentHeaderCatatan+'"></td></tr>';
             });
            html+='</tbody></table></div>';
            $('#items_area').html(html);
            $('#btn_save').prop('disabled',false);
         }
      },'json');
   });

   $('#btn_save').click(function(){
      var btn = $(this);
      btn.prop('disabled',true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
      $.ajax({
         url: '<?= $action_url; ?>',
         method: 'POST',
         data: $('#form_retur_jual').serialize(),
         dataType: 'json',
         success: function(res){
            if(res.success){
               window.location.href = res.redirect || '<?= base_url("administrator/retur_penjualan"); ?>';
            } else {
               show_alert(res.message, 'error');
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? "Update Retur Penjualan" : "Simpan Retur Penjualan"; ?>');
            }
         },
         error: function(){
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? "Update Retur Penjualan" : "Simpan Retur Penjualan"; ?>');
         }
      });
   });
});
</script>
