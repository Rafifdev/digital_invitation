<?php
$is_edit = isset($po);
$action_url = $is_edit
    ? site_url('administrator/purchase_order/edit_save/' . $po->id)
    : site_url('administrator/purchase_order/add_save');
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
   <h1><i class="fa fa-shopping-cart text-primary"></i> <?= $is_edit ? 'Edit' : 'Buat'; ?> Purchase Order <small><?= _ent($nomor_baru ?? ''); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/purchase_order'); ?>">Purchase Order</a></li>
      <li class="active"><?= $is_edit ? 'Edit' : 'Buat Baru'; ?></li>
   </ol>
</section>

<section class="content">
<form id="form_po">
<input type="hidden" name="nomor_po" value="<?= _ent($nomor_baru ?? ''); ?>">
<div class="row">
   <div class="col-md-12">
      <!-- Header Box -->
      <div class="box form-card" style="border-top: 3px solid #3b82f6;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-file-text-o text-primary"></i> Header &amp; Konfigurasi PO</h3>
            <div class="box-tools pull-right">
               <button type="button" id="btn_refresh_live_rate" class="btn btn-xs btn-info" style="border-radius:4px;" title="Refresh Kurs Real-Time API">
                  <i class="fa fa-refresh"></i> Refresh Kurs Live API (open.er-api.com)
               </button>
            </div>
         </div>
         <div class="box-body">
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group"><label>Supplier <span class="text-danger">*</span></label>
                     <select class="form-control chosen" name="supplier_id" id="supplier_id" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach($suppliers as $s): ?>
                           <option value="<?= $s->id; ?>" data-currency="<?= _ent($s->mata_uang ?? 'IDR'); ?>">
                              <?= _ent($s->kode_supplier.' - '.$s->nama_supplier.' ('.($s->mata_uang ?? 'IDR').')'); ?>
                           </option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group"><label>Gudang Tujuan</label>
                     <select class="form-control chosen" name="gudang_id">
                        <option value="">-- Pilih Gudang --</option>
                        <?php foreach($gudangs as $g): ?><option value="<?= $g->id; ?>"><?= _ent($g->nama_gudang); ?></option><?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-2">
                  <div class="form-group"><label>Tanggal PO <span class="text-danger">*</span></label>
                     <input type="date" class="form-control" name="tanggal_po" value="<?= date('Y-m-d'); ?>" required>
                  </div>
               </div>
               <div class="col-md-2">
                  <div class="form-group"><label>Est. Tanggal Terima</label>
                     <input type="date" class="form-control" name="tanggal_estimasi">
                  </div>
               </div>
            </div>

            <div class="row">
               <div class="col-md-3">
                  <div class="form-group"><label>Mata Uang (Otomatis)</label>
                     <select class="form-control" name="mata_uang" id="mata_uang">
                        <option value="IDR">IDR - Rupiah Indonesia</option>
                        <option value="USD">USD - US Dollar</option>
                        <option value="EUR">EUR - Euro</option>
                        <option value="SGD">SGD - Singapore Dollar</option>
                        <option value="JPY">JPY - Japanese Yen</option>
                        <option value="CNY">CNY - Chinese Yuan</option>
                        <option value="GBP">GBP - British Pound</option>
                        <option value="AUD">AUD - Australian Dollar</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group"><label>Kurs to RP (vs IDR)</label>
                     <input type="number" step="1" min="1" class="form-control" name="kurs" id="kurs" value="1">
                     <small class="text-muted" id="kurs_update_label" style="font-size:10px;"><i class="fa fa-globe"></i> API open.er-api.com</small>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group"><label>Diskon Keseluruhan (%)</label>
                     <input type="number" step="0.01" min="0" max="100" class="form-control" name="diskon" id="diskon" value="0">
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group"><label>Pajak (%)</label>
                     <input type="number" step="1" min="1" class="form-control" name="pajak_persen" id="pajak_persen" value="0">
                  </div>
               </div>
            </div>

            <div class="form-group" style="margin-bottom:0;"><label>Catatan PO</label>
               <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan instruksi ke supplier..."></textarea>
            </div>
         </div>
      </div>

      <!-- Item Lines Box -->
      <div class="box form-card" style="border-top: 3px solid #10b981;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list text-success"></i> Item Produk (Mata Uang: <span class="lbl-currency text-primary" style="font-weight:bold;">IDR</span>)</h3>
            <div class="box-tools pull-right">
               <button type="button" id="btn_add_item" class="btn btn-sm btn-success" style="border-radius:4px;"><i class="fa fa-plus"></i> Tambah Item Produk</button>
            </div>
         </div>
         <div class="box-body">
            <div class="table-responsive">
               <table class="table table-bordered table-striped" id="tbl_items">
                  <thead>
                     <tr class="active">
                        <th width="35%">Produk</th>
                        <th width="10%">Qty</th>
                        <th width="10%">Satuan</th>
                        <th width="20%">Harga Satuan (<span class="lbl-currency">IDR</span>)</th>
                        <th width="10%">Disc%</th>
                        <th width="15%">Subtotal (<span class="lbl-currency">IDR</span>)</th>
                        <th width="40"></th>
                     </tr>
                  </thead>
                  <tbody id="item_rows">
                     <tr id="item_row_0" class="item-row">
                        <td><select class="form-control produk-select" style="width:100%;" name="items[0][produk_id]"><option value="">-- Pilih Produk --</option><?php foreach($produks as $p): ?><option value="<?= $p->id; ?>" data-harga-idr="<?= $p->harga_beli; ?>" data-satuan="<?= _ent($p->singkatan ?? $p->nama_satuan ?? 'unit'); ?>"><?= _ent($p->kode_produk.' - '.$p->nama_produk); ?></option><?php endforeach; ?></select></td>
                        <td><input type="number" step="1" min="1" class="form-control qty-input" name="items[0][qty]" value="1" min="1" step="1"></td>
                        <td><input type="text" class="form-control satuan-input" name="items[0][satuan]" readonly placeholder="Unit"></td>
                        <td><input type="text" class="form-control harga-input rupiah" name="items[0][harga_satuan]" value="0"></td>
                        <td><input type="text" class="form-control disc-input" name="items[0][diskon_persen]" value="0"></td>
                        <td><input type="text" class="form-control subtotal-display" name="items[0][subtotal_display]" value="0" readonly></td>
                        <td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td>
                     </tr>
                  </tbody>
                  <tfoot>
                     <tr><td colspan="5" class="text-right"><strong>Subtotal (<span class="lbl-currency">IDR</span>):</strong></td><td colspan="2"><strong id="display_subtotal">0</strong></td></tr>
                     <tr><td colspan="5" class="text-right">Diskon (<span class="lbl-currency">IDR</span>):</td><td colspan="2" id="display_diskon">0</td></tr>
                     <tr><td colspan="5" class="text-right">Pajak:</td><td colspan="2" id="display_pajak">0</td></tr>
                     <tr><td colspan="5" class="text-right"><strong>TOTAL (<span class="lbl-currency">IDR</span>):</strong></td><td colspan="2"><strong class="text-success" id="display_total" style="font-size:16px;">0</strong></td></tr>
                     <tr id="row_ekuivalen_idr" style="background:#f4f6f9;"><td colspan="5" class="text-right"><strong>Estimasi Harga IDR:</strong></td><td colspan="2"><strong class="text-primary" id="display_total_idr" style="font-size:16px;">Rp 0</strong></td></tr>
                  </tfoot>
               </table>
            </div>
         </div>

         <!-- Card Footer: Action Buttons at the Bottom! -->
         <div class="box-footer" style="padding:15px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
            <div class="pull-left">
               <a href="<?= base_url('administrator/purchase_order'); ?>" class="btn btn-default btn-flat" style="border-radius:6px; font-weight:600; padding:8px 18px;">
                  <i class="fa fa-arrow-left"></i> Kembali ke Daftar PO
               </a>
            </div>
            <div class="pull-right">
               <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:6px; font-weight:700; padding:8px 24px; font-size:14px;">
                  <i class="fa fa-save"></i> <?= $is_edit ? 'Update Purchase Order' : 'Simpan Purchase Order'; ?>
               </button>
            </div>
         </div>
      </div>
   </div>
</div>
</form>
</section>

<script>
var item_count = 1;
var produks_data = <?= json_encode(array_map(function($p){ return ['id'=>$p->id,'nama'=>$p->kode_produk.' - '.$p->nama_produk,'harga_beli'=>$p->harga_beli]; }, $produks)); ?>;

var live_kurs_map = {
   'IDR': 1,
   'USD': 18015.28,
   'EUR': 20760.00,
   'SGD': 14050.00,
   'JPY': 114.30,
   'CNY': 2664.00,
   'GBP': 24220.00,
   'AUD': 12678.00
};

function fetch_live_rates_api(silent) {
   $.ajax({
      url: '<?= site_url("administrator/purchase_order/get_live_rates"); ?>',
      method: 'GET',
      dataType: 'json',
      success: function(res) {
         if (res.success && res.rates) {
            live_kurs_map = res.rates;
            var current_curr = $('#mata_uang').val();
            if (live_kurs_map[current_curr]) {
               $('#kurs').val(live_kurs_map[current_curr]);
            }
            $('#kurs_update_label').html('<i class="fa fa-check text-success"></i> Live API (' + res.provider + ')');
            update_currency_labels();
            if (!silent) {
               swal("Kurs Diperbarui!", "Kurs live berhasil diambil dari open.er-api.com", "success");
            }
         }
      }
   });
}

function update_currency_labels() {
   var curr = $('#mata_uang').val() || 'IDR';
   $('.lbl-currency').text(curr);
   if (curr === 'IDR') {
      $('#row_ekuivalen_idr').hide();
   } else {
      $('#row_ekuivalen_idr').show();
   }
}

function parseMoneyVal(val) {
   if (val === null || val === undefined || val === '') return 0;
   if (typeof val === 'number') return val;
   var str = String(val).trim();
   if (str.indexOf(',') !== -1) {
      str = str.replace(/\./g, '').replace(',', '.');
   } else {
      var parts = str.split('.');
      if (parts.length > 2) {
         str = str.replace(/\./g, '');
      }
   }
   return parseFloat(str) || 0;
}

function recalc_row($row){
   var qty = parseFloat($row.find('.qty-input').val()) || 1;
   var harga = parseMoneyVal($row.find('.harga-input').val());
   var disc = parseMoneyVal($row.find('.disc-input').val());
   var sub = (qty * harga) * (1 - disc/100);
   var curr = $('#mata_uang').val() || 'IDR';
   var formatted_sub = (curr === 'IDR') 
      ? Math.round(sub).toLocaleString('id-ID') 
      : sub.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
   $row.find('.subtotal-display').val(formatted_sub);
   recalc_total();
}

function recalc_total(){
   var curr = $('#mata_uang').val() || 'IDR';
   var kurs = parseFloat($('#kurs').val()) || 1;
   var subtotal_kotor = 0;
   var total_diskon_nominal = 0;
   var subtotal_bersih = 0;
   
   $('.item-row').each(function(){ 
      var qty = parseFloat($(this).find('.qty-input').val()) || 1;
      var harga = parseMoneyVal($(this).find('.harga-input').val());
      var disc_pct = parseMoneyVal($(this).find('.disc-input').val());
      
      var s_kotor = qty * harga;
      var d_nom = s_kotor * (disc_pct / 100);
      var s_bersih = s_kotor - d_nom;
      
      subtotal_kotor += s_kotor;
      total_diskon_nominal += d_nom;
      subtotal_bersih += s_bersih;
   });

   var pajak_pct = parseMoneyVal($('#pajak_persen').val());
   var pajak = subtotal_bersih * (pajak_pct / 100);
   var total = subtotal_bersih + pajak;

   var fmt = function(num) {
      return (curr === 'IDR')
         ? Math.round(num).toLocaleString('id-ID')
         : num.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
   };

   $('#display_subtotal').text(curr + ' ' + fmt(subtotal_kotor));
   $('#display_diskon').text(curr + ' ' + fmt(total_diskon_nominal));
   $('#display_pajak').text(curr + ' ' + fmt(pajak));
   $('#display_total').text(curr + ' ' + fmt(total));

   var total_idr = total * kurs;
   $('#display_total_idr').text('Rp ' + Math.round(total_idr).toLocaleString('id-ID'));
}

function add_item_row(){
   var idx = item_count++;
   var curr = $('#mata_uang').val() || 'IDR';
   var rupiah_class = (curr === 'IDR') ? 'rupiah' : '';
   var options = '<option value="">-- Pilih Produk --</option>';
   produks_data.forEach(function(p){ options += '<option value="'+p.id+'" data-harga-idr="'+p.harga_beli+'">'+p.nama+'</option>'; });
   var row = '<tr id="item_row_'+idx+'" class="item-row"><td><select class="form-control produk-select" style="width:100%;" name="items['+idx+'][produk_id]">'+options+'</select></td><td><input type="number" step="1" min="1" class="form-control qty-input" name="items['+idx+'][qty]" value="1" min="1" step="1"></td><td><input type="text" class="form-control" name="items['+idx+'][satuan]"></td><td><input type="text" class="form-control harga-input '+rupiah_class+'" name="items['+idx+'][harga_satuan]" value="0"></td><td><input type="text" class="form-control disc-input" name="items['+idx+'][diskon_persen]" value="0"></td><td><input type="text" class="form-control subtotal-display" name="items['+idx+'][subtotal_display]" value="0" readonly></td><td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td></tr>';
   $('#item_rows').append(row);
   if(typeof $.fn.chosen === 'function'){ $('#item_rows tr:last .chosen').chosen({width:'100%'}); }
}

$(document).ready(function(){
   fetch_live_rates_api(true);
   update_currency_labels();

   if ($('#mata_uang').val() !== 'IDR') {
      $('.harga-input').removeClass('rupiah');
   }

   $('#btn_refresh_live_rate').click(function(){
      fetch_live_rates_api(false);
   });

   $('#supplier_id').on('change', function(){
      var currency = $(this).find(':selected').data('currency') || 'IDR';
      $('#mata_uang').val(currency).trigger('change');
   });

   $('#mata_uang').on('change', function(){
      var curr = $(this).val();
      var rate = live_kurs_map[curr] || 1;
      $('#kurs').val(rate);
      update_currency_labels();
      
      $('.item-row').each(function(){
         var $row = $(this);
         
         if (curr === 'IDR') {
            $row.find('.harga-input').addClass('rupiah');
         } else {
            $row.find('.harga-input').removeClass('rupiah');
         }

         var $sel = $row.find('.produk-select');
         var harga_idr = parseFloat($sel.find(':selected').data('harga-idr')) || 0;
         if (harga_idr > 0) {
            if (curr === 'IDR') {
               $row.find('.harga-input').val(Math.round(harga_idr).toLocaleString('id-ID'));
            } else {
               $row.find('.harga-input').val((harga_idr / rate).toFixed(2));
            }
            recalc_row($row);
         }
      });
      recalc_total();
   });

   $('#kurs').on('input', function(){
      update_currency_labels();
      var rate = parseFloat($(this).val()) || 1;
      var curr = $('#mata_uang').val() || 'IDR';
      
      $('.item-row').each(function(){
         var $row = $(this);
         var $sel = $row.find('.produk-select');
         var harga_idr = parseFloat($sel.find(':selected').data('harga-idr')) || 0;
         if (harga_idr > 0) {
            if (curr === 'IDR') {
               $row.find('.harga-input').val(Math.round(harga_idr).toLocaleString('id-ID'));
            } else {
               $row.find('.harga-input').val((harga_idr / rate).toFixed(2));
            }
            recalc_row($row);
         }
      });
      recalc_total();
   });

   $('#btn_add_item').click(function(){ add_item_row(); });

   $(document).on('change', '.produk-select', function(){
      var $row = $(this).closest('.item-row');
      var $sel = $(this).find(':selected');
      var harga_idr = parseFloat($sel.data('harga-idr'))||0;
      var satuan = $sel.data('satuan') || 'pcs';
      var curr = $('#mata_uang').val() || 'IDR';
      var rate = parseFloat($('#kurs').val()) || 1;

      $row.find('.satuan-input').val(satuan);
      if (curr === 'IDR') {
         $row.find('.harga-input').addClass('rupiah').val(Math.round(harga_idr).toLocaleString('id-ID'));
      } else {
         $row.find('.harga-input').removeClass('rupiah').val((harga_idr / rate).toFixed(2));
      }
      recalc_row($row);
   });

   $(document).on('input', '.qty-input, .harga-input, .disc-input', function(){
      recalc_row($(this).closest('.item-row'));
   });

   $(document).on('click', '.remove-row', function(){
      if($('.item-row').length > 1){ $(this).closest('.item-row').remove(); recalc_total(); }
   });

   $('#pajak_persen').on('input', function(){ recalc_total(); });
   
   $('#diskon').on('input', function(){
      var val = $(this).val();
      $('.disc-input').val(val);
      $('.item-row').each(function(){ recalc_row($(this)); });
   });

   $('#btn_save').click(function(){
      var btn = $(this);
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
      $.ajax({ 
         url: '<?= $action_url; ?>', 
         method: 'POST', 
         data: $('#form_po').serialize(), 
         dataType: 'json',
         success: function(res){ 
            if(res.success){ 
               if(res.redirect){ window.location.href = res.redirect; } 
            } else { 
               show_alert(res.message, 'error'); 
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? 'Update Purchase Order' : 'Simpan Purchase Order'; ?>'); 
            } 
         },
         error: function(){ 
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? 'Update Purchase Order' : 'Simpan Purchase Order'; ?>'); 
         }
      });
   });
});
</script>
