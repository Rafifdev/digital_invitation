<?php
$is_edit = isset($penerimaan);
$action_url = $is_edit
    ? site_url('administrator/penerimaan_barang/edit_save/' . $penerimaan->id)
    : site_url('administrator/penerimaan_barang/add_save');
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
   <h1><i class="fa fa-truck text-primary"></i> <?= $is_edit ? 'Edit Penerimaan Barang' : 'Penerimaan Barang Baru'; ?> <small><?= _ent($is_edit ? ($penerimaan->nomor_penerimaan ?? '') : ($nomor_baru ?? '')); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/penerimaan_barang'); ?>">Penerimaan Barang</a></li>
      <li class="active"><?= $is_edit ? 'Edit' : 'Baru'; ?></li>
   </ol>
</section>

<section class="content">
<form id="form_pb">
<div class="row">
   <div class="col-md-12">
      <!-- Header Penerimaan Box -->
      <div class="box form-card" style="border-top: 3px solid #3b82f6;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-file-text-o text-primary"></i> Header &amp; Informasi Penerimaan</h3>
         </div>
         <div class="box-body">
            <div class="row">
               <div class="col-md-3">
                  <div class="form-group"><label>Referensi PO (Opsional)</label>
                     <select class="form-control chosen" name="po_id" id="po_id">
                        <option value="">-- Tanpa Referensi PO --</option>
                        <?php foreach($pos as $po): ?>
                           <option value="<?= $po->id; ?>" <?= (($is_edit && $penerimaan->po_id == $po->id) || (isset($selected_po_id) && $selected_po_id == $po->id)) ? 'selected' : ''; ?>><?= _ent($po->nomor_po); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group"><label>Supplier <span class="text-danger">*</span></label>
                     <select class="form-control chosen" name="supplier_id" id="supplier_id" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach($suppliers as $s): ?>
                           <option value="<?= $s->id; ?>" <?= ($is_edit && $penerimaan->supplier_id == $s->id) ? 'selected' : ''; ?>><?= _ent($s->kode_supplier.' - '.$s->nama_supplier); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group"><label>Gudang Tujuan <span class="text-danger">*</span></label>
                     <select class="form-control chosen" name="gudang_id" id="gudang_id" required>
                        <option value="">-- Pilih Gudang --</option>
                        <?php foreach($gudangs as $g): ?>
                           <option value="<?= $g->id; ?>" <?= ($is_edit && $penerimaan->gudang_id == $g->id) ? 'selected' : ''; ?>><?= _ent($g->nama_gudang); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group"><label>Tanggal Terima <span class="text-danger">*</span></label>
                     <input type="date" class="form-control" name="tanggal_terima" value="<?= $is_edit ? $penerimaan->tanggal_terima : date('Y-m-d'); ?>" required>
                  </div>
               </div>
            </div>

            <div class="row">
               <div class="col-md-4">
                  <div class="form-group"><label>No. Surat Jalan Supplier</label>
                     <input type="text" class="form-control" name="nomor_surat_jalan" placeholder="SJ-XXXXX" value="<?= $is_edit ? _ent($penerimaan->nomor_surat_jalan) : ''; ?>">
                  </div>
               </div>
               <div class="col-md-8">
                  <div class="form-group" style="margin-bottom:0;"><label>Catatan Penerimaan</label>
                     <textarea class="form-control" name="catatan" rows="1" placeholder="Catatan penerimaan barang..."><?= $is_edit ? _ent($penerimaan->catatan) : ''; ?></textarea>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Barcode Scanner Widget -->
      <div class="box form-card" style="border-top: 3px solid #00c0ef;">
         <div class="box-header with-border" style="background: #f4faff;">
            <h3 class="box-title"><i class="fa fa-barcode text-info"></i> Scan Barcode Penerimaan Barang</h3>
            <div class="box-tools pull-right">
               <small class="text-muted"><i class="fa fa-usb"></i> USB Scanner / Mobile Scanner Ready</small>
            </div>
         </div>
         <div class="box-body">
            <div class="input-group">
               <span class="input-group-addon" style="background:#00c0ef; color:#fff; border-color:#00c0ef;"><i class="fa fa-barcode fa-lg"></i></span>
               <input type="text" id="barcode_scan_input" class="form-control input-lg" placeholder="Arahkan scanner ke sini atau ketik kode produk lalu Enter..." autofocus>
               <span class="input-group-btn">
                  <button class="btn btn-info btn-lg" type="button" id="btn_do_scan" style="font-weight:700;"><i class="fa fa-search"></i> Cari &amp; Tambah</button>
               </span>
            </div>
            <small class="text-muted" style="margin-top:6px; display:inline-block;"><i class="fa fa-info-circle"></i> Setiap scan akan otomatis menambahkan item baru ke daftar penerimaan atau menambah qty jika produk sudah ada.</small>
         </div>
      </div>

      <!-- Item Barang Diterima Box -->
      <div class="box form-card" style="border-top: 3px solid #10b981;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-cubes text-success"></i> Item Barang Diterima</h3>
            <div class="box-tools pull-right">
               <button type="button" id="btn_add_item" class="btn btn-sm btn-success" style="border-radius:4px;"><i class="fa fa-plus"></i> Tambah Item Barang</button>
            </div>
         </div>
         <div class="box-body">
            <div class="table-responsive">
               <table class="table table-bordered table-striped" id="tbl_items">
                   <?php
                   $default_currency = 'IDR';
                   if (isset($is_edit) && $is_edit && !empty($penerimaan->po_id) && isset($pos)) {
                       foreach ($pos as $p) {
                           if ($p->id == $penerimaan->po_id) {
                               $default_currency = !empty($p->mata_uang) ? $p->mata_uang : 'IDR';
                               break;
                           }
                       }
                   }
                   ?>
                   <thead>
                      <tr class="active">
                         <th width="30%">Produk</th>
                         <th width="20%">Lokasi Rak Gudang</th>
                         <th width="10%">Qty Terima</th>
                         <th width="10%">Qty Rusak</th>
                         <th width="8%">Satuan</th>
                         <th width="12%">Harga Satuan (<span class="lbl-currency"><?= _ent($default_currency); ?></span>)</th>
                         <th width="10%">Batch No</th>
                         <th width="40"></th>
                      </tr>
                   </thead>
                   <tbody id="item_rows">
                      <?php if ($is_edit && !empty($items)): ?>
                         <?php foreach ($items as $idx => $it): ?>
                            <tr id="item_row_<?= $idx; ?>" class="item-row">
                               <td>
                                  <select class="form-control produk-select" style="width:100%;" name="items[<?= $idx; ?>][produk_id]">
                                     <option value="">-- Pilih Produk --</option>
                                     <?php foreach($produks as $p): ?>
                                        <option value="<?= $p->id; ?>" <?= ($p->id == $it->produk_id) ? 'selected' : ''; ?> data-harga="<?= $p->harga_beli; ?>">
                                           <?= _ent($p->kode_produk.' - '.$p->nama_produk); ?>
                                        </option>
                                     <?php endforeach; ?>
                                  </select>
                                  <input type="hidden" name="items[<?= $idx; ?>][po_item_id]" value="<?= $it->po_item_id ?? ''; ?>">
                                  <div class="qty-breakdown-badge" style="margin-top:2px;">
                                     <span class="label label-success" style="font-size:10px;"><i class="fa fa-check"></i> <?= (int)($it->qty_baik ?? $it->qty_terima); ?> Baik</span>
                                     <?php if(($it->qty_rusak ?? 0) > 0): ?>
                                        <span class="label label-danger" style="font-size:10px;"><i class="fa fa-times"></i> <?= (int)$it->qty_rusak; ?> Rusak</span>
                                     <?php endif; ?>
                                  </div>
                               </td>
                               <td>
                                  <select class="form-control rak-select" name="items[<?= $idx; ?>][rak_id]">
                                     <option value="">-- Tanpa Rak / Rak Default --</option>
                                     <?php foreach($raks as $r): ?>
                                        <option class="rak-opt" data-gudang="<?= $r->gudang_id; ?>" value="<?= $r->id; ?>" <?= ($r->id == $it->rak_id) ? 'selected' : ''; ?>><?= _ent($r->kode_rak.' (Baris '.$r->baris.')'); ?></option>
                                     <?php endforeach; ?>
                                  </select>
                               </td>
                               <td><input type="number" step="1" min="1" class="form-control qty-terima-input qty-input" name="items[<?= $idx; ?>][qty_terima]" value="<?= (int)($it->qty_terima ?? 1); ?>" data-total-qty="<?= (int)(($it->qty_terima ?? 1) + ($it->qty_rusak ?? 0)); ?>" min="1" step="1"></td>
                               <td><input type="number" step="1" min="0" class="form-control qty-rusak-input" name="items[<?= $idx; ?>][qty_rusak]" value="<?= (int)($it->qty_rusak ?? 0); ?>" min="0"></td>
                               <td><input type="text" class="form-control" name="items[<?= $idx; ?>][satuan]" value="<?= _ent($it->satuan ?? 'pcs'); ?>" style="width:60px"></td>
                               <td><input type="text" class="form-control harga-input rupiah" name="items[<?= $idx; ?>][harga_satuan]" value="<?= fmod((float)($it->harga_satuan ?? 0), 1) !== 0.0 ? number_format($it->harga_satuan, 2, ',', '.') : number_format($it->harga_satuan ?? 0, 0, ',', '.'); ?>"></td>
                               <td><input type="text" class="form-control" name="items[<?= $idx; ?>][batch_no]" value="<?= _ent($it->batch_no ?? ''); ?>"></td>
                               <td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td>
                            </tr>
                         <?php endforeach; ?>
                      <?php else: ?>
                         <tr id="item_row_0" class="item-row">
                            <td><select class="form-control produk-select" style="width:100%;" name="items[0][produk_id]"><option value="">-- Pilih Produk --</option><?php foreach($produks as $p): ?><option value="<?= $p->id; ?>" data-harga="<?= $p->harga_beli; ?>"><?= _ent($p->kode_produk.' - '.$p->nama_produk); ?></option><?php endforeach; ?></select><div class="qty-breakdown-badge" style="margin-top:2px;"></div></td>
                            <td><select class="form-control" name="items[0][rak_id]"><option value="">-- Tanpa Rak / Rak Default --</option><?php foreach($raks as $r): ?><option value="<?= $r->id; ?>"><?= _ent($r->kode_rak.' (Baris '.$r->baris.')'); ?></option><?php endforeach; ?></select></td>
                            <td><input type="number" step="1" min="1" class="form-control qty-terima-input qty-input" name="items[0][qty_terima]" value="1" data-total-qty="1" min="1" step="1"></td>
                            <td><input type="number" step="1" min="0" class="form-control qty-rusak-input" name="items[0][qty_rusak]" value="0" min="0"></td>
                            <td><input type="text" class="form-control" name="items[0][satuan]" style="width:60px"></td>
                            <td><input type="text" class="form-control harga-input rupiah" name="items[0][harga_satuan]" value="0"></td>
                            <td><input type="text" class="form-control" name="items[0][batch_no]"></td>
                            <td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td>
                         </tr>
                      <?php endif; ?>
                   </tbody>
               </table>
            </div>
         </div>

         <!-- Card Footer: Action Buttons at the Bottom! -->
         <div class="box-footer" style="padding:15px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
            <div class="pull-left" style="line-height:35px;">
               <a href="<?= base_url('administrator/penerimaan_barang'); ?>" class="btn btn-default btn-flat" style="border-radius:6px; font-weight:600; padding:8px 18px;">
                  <i class="fa fa-arrow-left"></i> Kembali ke Daftar Penerimaan
               </a>
            </div>
            <div class="pull-right">
               <small class="text-warning font-weight-bold" style="font-size:12px; margin-right:15px;"><i class="fa fa-exclamation-triangle"></i> Stok akan bertambah saat status dikonfirmasi.</small>
               <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:6px; font-weight:700; padding:8px 24px; font-size:14px;">
                  <i class="fa fa-save"></i> <?= $is_edit ? 'Update Draft Penerimaan' : 'Simpan Draft Penerimaan'; ?>
               </button>
            </div>
         </div>
      </div>
   </div>
</div>
</form>
</section>

<script>
var item_count=1;
var produks_data=<?= json_encode(array_map(function($p){return['id'=>$p->id,'kode'=>$p->kode_produk,'barcode'=>$p->barcode ?: $p->kode_produk,'nama'=>$p->nama_produk,'harga_beli'=>$p->harga_beli];}, $produks)); ?>;

function play_scanner_beep() {
   try {
      var ctx = new (window.AudioContext || window.webkitAudioContext)();
      var osc = ctx.createOscillator();
      var gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.value = 1800;
      gain.gain.setValueAtTime(0.3, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.00001, ctx.currentTime + 0.12);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + 0.12);
   } catch(e){}
}

function process_barcode_scan(barcode) {
   if (!barcode) return;
   barcode = $.trim(barcode).toLowerCase();
   
   var found_product = null;
   produks_data.forEach(function(p) {
      if ((p.barcode && p.barcode.toLowerCase() === barcode) || (p.kode && p.kode.toLowerCase() === barcode)) {
         found_product = p;
      }
   });
   
   if (!found_product) {
      swal("Barcode Tidak Ditemukan!", "Kode [" + barcode + "] tidak terdaftar.", "warning");
      $('#barcode_scan_input').val('').focus();
      return;
   }
   
   play_scanner_beep();
   
   var existing_row = null;
   $('.item-row').each(function() {
      var val = $(this).find('.produk-select').length > 0 ? $(this).find('.produk-select').val() : $(this).find('input[name$="[produk_id]"]').val();
      if (parseInt(val) === parseInt(found_product.id)) {
         existing_row = $(this);
      }
   });
   
   var po_id = $('#po_id').val();

   if (existing_row) {
      var $qty_inp = existing_row.find('.qty-input');
      var curr_qty = parseFloat($qty_inp.val()) || 0;
      
      var max_qty = parseFloat($qty_inp.attr('data-total-qty'));
      if (po_id && max_qty && curr_qty >= max_qty) {
         swal("Validasi Kuantitas", "Kuantitas " + found_product.nama + " sudah mencapai maksimal pesanan PO (" + max_qty + ").", "warning");
         play_scanner_beep();
         $('#barcode_scan_input').val('').focus();
         return;
      }
      
      $qty_inp.val(curr_qty + 1).trigger('input');
      existing_row.css('background-color', '#d4edda').animate({backgroundColor: '#ffffff'}, 1500);
      if (typeof toastr !== 'undefined') {
         toastr.success('Qty Ditambahkan: ' + found_product.nama, 'Scan Success ✓');
      }
   } else {
      if (po_id) {
         swal("Validasi PO", "Produk [" + found_product.nama + "] tidak ada dalam daftar pesanan PO ini! Tidak bisa menambahkan barang secara sembarangan.", "error");
         $('#barcode_scan_input').val('').focus();
         return;
      }

      var $first_row = $('.item-row').first();
      var first_val = $first_row.find('.produk-select').val();
      
      if (!first_val && $first_row.length > 0) {
         $first_row.find('.produk-select').val(found_product.id).trigger('change');
         $first_row.css('background-color', '#d4edda').animate({backgroundColor: '#ffffff'}, 1500);
      } else {
         $('#btn_add_item').click();
         var $new_row = $('.item-row').last();
         $new_row.find('.produk-select').val(found_product.id).trigger('change');
         $new_row.css('background-color', '#d4edda').animate({backgroundColor: '#ffffff'}, 1500);
      }
      if (typeof toastr !== 'undefined') {
         toastr.success('Item Baru Ditambahkan: ' + found_product.nama, 'Scan Success ✓');
      }
   }
   
   $('#barcode_scan_input').val('').focus();
}

function build_opts(){var s='<option value="">-- Pilih Produk --</option>';produks_data.forEach(function(p){s+='<option value="'+p.id+'" data-harga="'+p.harga_beli+'">'+p.kode+' - '+p.nama+'</option>';});return s;}

var raks_data=<?= json_encode(array_map(function($r){return['id'=>$r->id,'kode'=>$r->kode_rak,'baris'=>$r->baris,'gudang_id'=>$r->gudang_id];}, $raks)); ?>;
function build_rak_opts(gudangId){var s='<option value="">-- Tanpa Rak / Rak Default --</option>';raks_data.forEach(function(r){if(!gudangId || r.gudang_id == gudangId){s+='<option value="'+r.id+'">'+r.kode+' (Baris '+r.baris+')</option>';}});return s;}

$(document).ready(function(){
   $('#barcode_scan_input').on('keypress', function(e) {
      if (e.which === 13) {
         e.preventDefault();
         process_barcode_scan($(this).val());
      }
   });
   $('#gudang_id').change(function(){
      var g_id = $(this).val();
      $('.rak-select').each(function(){
         var current_val = $(this).val();
         $(this).html(build_rak_opts(g_id));
         $(this).val(current_val);
      });
      $('.rak-opt').each(function(){
         if(g_id == '' || $(this).data('gudang') == g_id) {
            $(this).show();
         } else {
            $(this).hide();
            if($(this).is(':selected')) $(this).parent().val('');
         }
      });
   });

   $('#btn_do_scan').click(function() {
      process_barcode_scan($('#barcode_scan_input').val());
   });
   $(document).on('click', '.btn-simulasi-barcode', function() {
      var bc = $(this).data('barcode');
      process_barcode_scan(bc);
   });

   $('#po_id').on('change',function(){
      var po_id=$(this).val();
      if(!po_id) return;
      $.get('<?= site_url("administrator/penerimaan_barang/get_po_items"); ?>',{po_id:po_id},function(r){
         if(r.success){
            if(r.po){
               if(r.po.supplier_id) $('#supplier_id').val(r.po.supplier_id).trigger('chosen:updated').trigger('change');
               if(r.po.gudang_id) $('#gudang_id').val(r.po.gudang_id).trigger('chosen:updated').trigger('change');
               $('.lbl-currency').text(r.po.mata_uang || 'IDR');
            }
            if(r.items && r.items.length>0){
               $('#item_rows').empty();
                r.items.forEach(function(it,idx){
                     var qty_pesan = Math.round(parseFloat(it.qty_pesan || 0));
                     var qty_terima_sebelumnya = Math.round(parseFloat(it.qty_terima || 0));
                     var rem_qty = Math.max(0, qty_pesan - qty_terima_sebelumnya);
                     var default_input_qty = rem_qty > 0 ? rem_qty : (qty_pesan > 0 ? qty_pesan : 1);
                     var formatted_harga = (it.harga_satuan % 1 !== 0) ? parseFloat(it.harga_satuan).toLocaleString('id-ID', {minimumFractionDigits: 2}) : Math.round(it.harga_satuan).toLocaleString('id-ID');
                     var sisa_label = rem_qty === 0 ? '<strong class="text-success">0 (Terkirim Semua)</strong>' : '<strong class="text-danger">' + rem_qty + ' ' + (it.satuan||'unit') + '</strong>';
                     var po_tracking_info = '<br><small class="text-info" style="font-size:11px;"><i class="fa fa-info-circle"></i> Total PO: <strong>' + qty_pesan + ' ' + (it.satuan||'unit') + '</strong> | Terkonfirmasi: <strong class="text-success">' + qty_terima_sebelumnya + '</strong> | Sisa PO: ' + sisa_label + '</small>';
                     
                     $('#item_rows').append('<tr id="item_row_'+idx+'" class="item-row"><td><strong>'+it.nama_produk+'</strong><br><small class="text-muted"><i class="fa fa-barcode"></i> '+it.kode_produk+'</small>'+po_tracking_info+'<div class="qty-breakdown-badge" style="margin-top:2px;"><span class="label label-success" style="font-size:10px;"><i class="fa fa-check"></i> '+default_input_qty+' Baik (Masuk Stok)</span></div><input type="hidden" name="items['+idx+'][produk_id]" value="'+it.produk_id+'"><input type="hidden" name="items['+idx+'][po_item_id]" value="'+it.id+'"></td><td><select class="form-control rak-select" name="items['+idx+'][rak_id]">'+build_rak_opts($('#gudang_id').val())+'</select></td><td><input type="number" step="1" min="1" class="form-control qty-terima-input qty-input" name="items['+idx+'][qty_terima]" value="'+default_input_qty+'" data-total-qty="'+default_input_qty+'" min="1" step="1"></td><td><input type="number" step="1" min="0" class="form-control qty-rusak-input" name="items['+idx+'][qty_rusak]" value="0" min="0"></td><td><input type="text" class="form-control" name="items['+idx+'][satuan]" value="'+(it.satuan||'pcs')+'" style="width:60px" readonly></td><td><input type="text" class="form-control harga-input rupiah" name="items['+idx+'][harga_satuan]" value="'+formatted_harga+'"></td><td><input type="text" class="form-control" name="items['+idx+'][batch_no]"></td><td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td></tr>');
                     item_count++;
                 });
            }
         }
      },'json');
   });

   var is_edit_mode = <?= $is_edit ? 'true' : 'false'; ?>;
   if(!is_edit_mode && $('#po_id').val()){
      $('#po_id').trigger('change');
   }
   
   setTimeout(function() {
      if($('#gudang_id').val()) {
         $('#gudang_id').trigger('change');
      }
   }, 100);

   $(document).on('input change', '.qty-rusak-input', function() {
      var $row = $(this).closest('.item-row');
      var $qty_terima = $row.find('.qty-terima-input');
      var total_orig = parseFloat($qty_terima.val()) || 0;

      var qty_rusak = parseInt($(this).val()) || 0;
      if (qty_rusak < 0) qty_rusak = 0;
      if (qty_rusak > total_orig) {
         qty_rusak = total_orig;
         $(this).val(qty_rusak);
      }

      var qty_baik = Math.max(0, total_orig - qty_rusak);

      var badgeHtml = '<span class="label label-success" style="font-size:10px;"><i class="fa fa-check"></i> ' + qty_baik + ' Baik (Masuk Stok)</span>';
      if (qty_rusak > 0) {
         badgeHtml += ' <span class="label label-danger" style="font-size:10px;"><i class="fa fa-times"></i> ' + qty_rusak + ' Rusak/Karantina</span>';
      }
      $row.find('.qty-breakdown-badge').html(badgeHtml);
   });

   $(document).on('input change', '.qty-terima-input', function() {
      var $row = $(this).closest('.item-row');
      var $qty_rusak = $row.find('.qty-rusak-input');
      var total_orig = parseInt($(this).val()) || 0;
      var curr_rusak = parseInt($qty_rusak.val()) || 0;

      if (curr_rusak > total_orig) {
          curr_rusak = total_orig;
          $qty_rusak.val(curr_rusak);
      }
      
      var qty_baik = Math.max(0, total_orig - curr_rusak);

      var badgeHtml = '<span class="label label-success" style="font-size:10px;"><i class="fa fa-check"></i> ' + qty_baik + ' Baik (Masuk Stok)</span>';
      if (curr_rusak > 0) {
         badgeHtml += ' <span class="label label-danger" style="font-size:10px;"><i class="fa fa-times"></i> ' + curr_rusak + ' Rusak/Karantina</span>';
      }
      $row.find('.qty-breakdown-badge').html(badgeHtml);
   });

   $('#btn_add_item').click(function(){var idx=item_count++;$('#item_rows').append('<tr id="item_row_'+idx+'" class="item-row"><td><select class="form-control produk-select" style="width:100%;" name="items['+idx+'][produk_id]">'+build_opts()+'</select><div class="qty-breakdown-badge" style="margin-top:2px;"></div></td><td><select class="form-control rak-select" name="items['+idx+'][rak_id]">'+build_rak_opts($('#gudang_id').val())+'</select></td><td><input type="number" step="1" min="1" class="form-control qty-terima-input qty-input" name="items['+idx+'][qty_terima]" value="1" data-total-qty="1"></td><td><input type="number" step="1" min="0" class="form-control qty-rusak-input" name="items['+idx+'][qty_rusak]" value="0"></td><td><input type="text" class="form-control" name="items['+idx+'][satuan]" style="width:60px"></td><td><input type="text" class="form-control harga-input rupiah" name="items['+idx+'][harga_satuan]" value="0"></td><td><input type="text" class="form-control" name="items['+idx+'][batch_no]"></td><td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td></tr>');if(typeof $.fn.chosen==='function'){$('#item_rows tr:last .chosen').chosen({width:'100%'});}});
   $(document).on('change','.produk-select',function(){var $row=$(this).closest('.item-row');var harga=$(this).find(':selected').data('harga')||0;$row.find('.harga-input').val(harga);});
   $(document).on('click','.remove-row',function(){if($('.item-row').length>1) $(this).closest('.item-row').remove();});
   $('#btn_save').click(function(){var btn=$(this);btn.prop('disabled',true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');$.ajax({url:'<?= site_url("administrator/penerimaan_barang/add_save"); ?>',method:'POST',data:$('#form_pb').serialize(),dataType:'json',success:function(res){if(res.success){window.location.href=res.redirect||'<?= base_url("administrator/penerimaan_barang"); ?>';}else{show_alert(res.message,'error');btn.prop('disabled',false).html('<i class="fa fa-save"></i> Simpan Draft');}},error:function(){btn.prop('disabled',false).html('<i class="fa fa-save"></i> Simpan Draft');}});});
});
</script>
