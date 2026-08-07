<?php
$is_edit = isset($so);
$action_url = $is_edit
    ? site_url('administrator/sales_order/edit_save/' . $so->id)
    : site_url('administrator/sales_order/add_save');

$diskon_persen_init = 0;
if ($is_edit && isset($so->subtotal) && $so->subtotal > 0 && isset($so->diskon_total)) {
    $diskon_persen_init = round(($so->diskon_total / $so->subtotal) * 100, 2);
}
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
   <h1><i class="fa fa-shopping-bag text-primary"></i> <?= $is_edit ? 'Edit Sales Order' : 'Buat Sales Order'; ?> <small><?= _ent($is_edit ? ($so->nomor_so ?? '') : ($nomor_baru ?? '')); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/sales_order'); ?>">Sales Order</a></li>
      <li class="active"><?= $is_edit ? 'Edit' : 'Buat Baru'; ?></li>
   </ol>
</section>

<section class="content">
<form id="form_so">
<div class="row">
   <div class="col-md-12">
      <!-- Header Box -->
      <div class="box form-card" style="border-top: 3px solid #3b82f6;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-file-text-o text-primary"></i> Header &amp; Konfigurasi Sales Order</h3>
         </div>
         <div class="box-body">
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group"><label>Pelanggan <span class="text-danger">*</span></label>
                     <select class="form-control chosen" name="pelanggan_id" id="pelanggan_id" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php foreach($pelanggans as $p): ?>
                           <option value="<?= $p->id; ?>" data-tipe="<?= $p->tipe_pelanggan; ?>" <?= ($is_edit && $so->pelanggan_id == $p->id) ? 'selected' : ''; ?>>
                              <?= _ent($p->kode_pelanggan.' - '.$p->nama_pelanggan); ?>
                           </option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group"><label>Gudang Sumber Stok <span class="text-danger">*</span></label>
                     <select class="form-control chosen" name="gudang_id" id="gudang_id" required>
                        <option value="">-- Pilih Gudang --</option>
                        <?php foreach($gudangs as $g): ?>
                           <option value="<?= $g->id; ?>" <?= ($is_edit && $so->gudang_id == $g->id) ? 'selected' : ''; ?>>
                              <?= _ent($g->nama_gudang); ?>
                           </option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-2">
                  <div class="form-group"><label>Tanggal SO <span class="text-danger">*</span></label>
                     <input type="date" class="form-control" name="tanggal_so" value="<?= $is_edit ? $so->tanggal_so : date('Y-m-d'); ?>" required>
                  </div>
               </div>
               <div class="col-md-2">
                  <div class="form-group"><label>Est. Tanggal Kirim</label>
                     <input type="date" class="form-control" name="tanggal_estimasi_kirim" value="<?= $is_edit ? $so->tanggal_estimasi_kirim : ''; ?>">
                  </div>
               </div>
            </div>

            <div class="row">
               <div class="col-md-3">
                  <div class="form-group"><label>Diskon Keseluruhan (%)</label>
                     <input type="number" step="0.1" min="0" max="100" class="form-control" name="diskon_persen_total" id="diskon_persen_total" value="<?= $diskon_persen_init; ?>">
                     <input type="hidden" name="diskon_total" id="diskon_total" value="<?= $is_edit ? (float)$so->diskon_total : 0; ?>">
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group"><label>Pajak (%)</label>
                     <input type="number" step="1" min="0" class="form-control" name="pajak_persen" id="pajak_persen" value="<?= $is_edit ? (float)$so->pajak_persen : 0; ?>">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group" style="margin-bottom:0;"><label>Catatan Sales Order</label>
                     <textarea class="form-control" name="catatan" rows="1" placeholder="Catatan khusus transaksi SO..."><?= $is_edit ? _ent($so->catatan) : ''; ?></textarea>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Barcode Scanner Simulation Widget -->
      <div class="box form-card" style="border-top: 3px solid #00c0ef;">
         <div class="box-header with-border" style="background: #f8fafc;">
            <h3 class="box-title" style="color: #0284c7; font-weight: bold;">
               <i class="fa fa-barcode"></i> Barcode Scanner &amp; Simulasi Scan Local
            </h3>
            <span class="label label-success pull-right" style="font-size: 11px; padding: 4px 8px;">
               <i class="fa fa-bolt"></i> Ready to Scan
            </span>
         </div>
         <div class="box-body">
            <div class="input-group input-group-lg" style="margin-bottom: 10px;">
               <span class="input-group-addon bg-blue" style="border-color: #0284c7;">
                  <i class="fa fa-qrcode fa-lg"></i>
               </span>
               <input type="text" id="barcode_scan_input" class="form-control" placeholder="Tempatkan kursor di sini untuk scan barcode ATAU pilih tombol di bawah..." style="font-weight: bold; letter-spacing: 0.5px;">
               <span class="input-group-btn">
                  <button type="button" id="btn_do_scan" class="btn btn-info btn-flat"><i class="fa fa-search"></i> Scan!</button>
               </span>
            </div>
            <div>
               <small class="text-muted" style="display:block; margin-bottom:6px;">
                  <i class="fa fa-hand-o-right"></i> <strong>Klik Tombol Simulasi Scan di Bawah (Tes Tanpa Hardware Scanner):</strong>
               </small>
               <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                  <?php foreach($produks as $p): ?>
                     <button type="button" class="btn btn-default btn-xs btn-simulasi-barcode" 
                             data-barcode="<?= _ent($p->barcode ?: $p->kode_produk); ?>"
                             data-id="<?= $p->id; ?>"
                             data-nama="<?= _ent($p->nama_produk); ?>"
                             style="border-radius: 15px; background: #eef2f7; border: 1px solid #cbd5e1; padding: 3px 10px;">
                        <i class="fa fa-barcode text-primary"></i> <strong><?= _ent($p->barcode ?: $p->kode_produk); ?></strong> (<?= _ent($p->nama_produk); ?>)
                     </button>
                  <?php endforeach; ?>
               </div>
            </div>
         </div>
      </div>

      <!-- Item Box -->
      <div class="box form-card" style="border-top: 3px solid #10b981;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-cubes text-success"></i> Item Produk Pesanan</h3>
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
                        <th width="8%">Satuan</th>
                        <th width="18%">Harga Jual (IDR)</th>
                        <th width="10%">Disc%</th>
                        <th width="15%">Subtotal (IDR)</th>
                        <th width="80">Stok</th>
                        <th width="40"></th>
                     </tr>
                  </thead>
                  <tbody id="item_rows">
                     <?php if ($is_edit && !empty($items)): ?>
                        <?php foreach($items as $idx => $it): ?>
                           <tr id="item_row_<?= $idx; ?>" class="item-row">
                              <td>
                                 <select class="form-control produk-select" style="width:100%;" name="items[<?= $idx; ?>][produk_id]">
                                    <option value="">-- Pilih Produk --</option>
                                    <?php foreach($produks as $p): ?>
                                       <option value="<?= $p->id; ?>" data-harga="<?= $p->harga_jual; ?>" <?= ($it->produk_id == $p->id) ? 'selected' : ''; ?>>
                                          <?= _ent($p->kode_produk.' - '.$p->nama_produk); ?>
                                       </option>
                                    <?php endforeach; ?>
                                 </select>
                              </td>
                              <td><input type="number" step="1" min="1" class="form-control qty-input" name="items[<?= $idx; ?>][qty]" value="<?= (int)$it->qty; ?>"></td>
                              <td><input type="text" class="form-control" name="items[<?= $idx; ?>][satuan]" style="width:60px" value="<?= _ent($it->singkatan ?? $it->satuan); ?>"></td>
                              <td><input type="text" class="form-control harga-input rupiah" name="items[<?= $idx; ?>][harga_satuan]" value="<?= (float)$it->harga_satuan; ?>"></td>
                              <td><input type="text" class="form-control disc-input" name="items[<?= $idx; ?>][diskon_persen]" value="<?= (float)$it->diskon_persen; ?>"></td>
                              <td><input type="text" class="form-control subtotal-display" value="<?= (float)$it->subtotal; ?>" readonly></td>
                              <td><span class="stok-display badge bg-green">-</span></td>
                              <td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td>
                           </tr>
                        <?php endforeach; ?>
                     <?php else: ?>
                        <tr id="item_row_0" class="item-row">
                           <td><select class="form-control produk-select" style="width:100%;" name="items[0][produk_id]"><option value="">-- Pilih Produk --</option><?php foreach($produks as $p): ?><option value="<?= $p->id; ?>" data-harga="<?= $p->harga_jual; ?>"><?= _ent($p->kode_produk.' - '.$p->nama_produk); ?></option><?php endforeach; ?></select></td>
                           <td><input type="number" step="1" min="1" class="form-control qty-input" name="items[0][qty]" value="1"></td>
                           <td><input type="text" class="form-control" name="items[0][satuan]" style="width:60px"></td>
                           <td><input type="text" class="form-control harga-input rupiah" name="items[0][harga_satuan]" value="0"></td>
                           <td><input type="text" class="form-control disc-input" name="items[0][diskon_persen]" value="0"></td>
                           <td><input type="text" class="form-control subtotal-display" value="0" readonly></td>
                           <td><span class="stok-display badge bg-green">-</span></td>
                           <td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td>
                        </tr>
                     <?php endif; ?>
                  </tbody>
                  <tfoot>
                     <tr><td colspan="5" class="text-right"><strong>Subtotal:</strong></td><td colspan="3"><strong id="display_subtotal">Rp 0</strong></td></tr>
                     <tr><td colspan="5" class="text-right">Diskon Keseluruhan:</td><td colspan="3" class="text-danger" id="display_diskon">- Rp 0</td></tr>
                     <tr><td colspan="5" class="text-right">Pajak:</td><td colspan="3" id="display_pajak">Rp 0</td></tr>
                     <tr class="active"><td colspan="5" class="text-right"><strong>TOTAL:</strong></td><td colspan="3"><strong class="text-success" id="display_total" style="font-size: 16px;">Rp 0</strong></td></tr>
                  </tfoot>
               </table>
            </div>
         </div>

         <!-- Card Footer: Action Buttons at the Bottom! -->
         <div class="box-footer" style="padding:15px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
            <div class="pull-left">
               <a href="<?= base_url('administrator/sales_order'); ?>" class="btn btn-default btn-flat" style="border-radius:6px; font-weight:600; padding:8px 18px;">
                  <i class="fa fa-arrow-left"></i> Kembali ke Daftar SO
               </a>
            </div>
            <div class="pull-right">
               <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:6px; font-weight:700; padding:8px 24px; font-size:14px;">
                  <i class="fa fa-save"></i> <?= $is_edit ? 'Update Sales Order' : 'Simpan Sales Order'; ?>
               </button>
            </div>
         </div>
      </div>
   </div>
</div>
</form>
</section>

<script>
var item_count = <?= ($is_edit && !empty($items)) ? count($items) : 1; ?>;
var available_produks = [];
var produks_initial = <?= json_encode(array_map(function($p){ return ['id'=>$p->id, 'kode'=>$p->kode_produk, 'barcode'=>$p->barcode ?: $p->kode_produk, 'nama'=>$p->nama_produk, 'satuan'=>$p->nama_satuan ?? 'Pcs', 'harga_jual'=>$p->harga_jual, 'harga_jual_grosir'=>$p->harga_jual_grosir, 'harga_jual_distributor'=>$p->harga_jual_distributor, 'harga_jual_vip'=>$p->harga_jual_vip]; }, $produks)); ?>;

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

function build_options() {
   if (!available_produks || available_produks.length === 0) {
      if ($('#gudang_id').val()) {
         return '<option value="">-- Tidak ada barang ber-stok di gudang ini --</option>';
      }
      return '<option value="">-- Pilih Gudang Sumber Terlebih Dahulu --</option>';
   }
   var opts = '<option value="">-- Pilih Produk --</option>';
   available_produks.forEach(function(p) {
      opts += '<option value="' + p.id + '" data-harga="' + (p.harga_jual || 0) + '">' + (p.kode_produk || p.kode) + ' - ' + (p.nama_produk || p.nama) + '</option>';
   });
   return opts;
}

function update_all_product_dropdowns() {
   var new_opts = build_options();
   $('.produk-select').each(function() {
      var current_val = $(this).val();
      $(this).html(new_opts);
      if (current_val) {
         $(this).val(current_val);
      }
      var $row = $(this).closest('.item-row');
      cek_stok($(this).val(), $('#gudang_id').val(), $row.find('.stok-display'));
   });
}

function load_produk_by_gudang(gudang_id) {
   if (!gudang_id) {
      available_produks = [];
      update_all_product_dropdowns();
      return;
   }
   
   $.ajax({
      url: '<?= base_url("administrator/sales_order/get_produk_by_gudang"); ?>',
      data: {gudang_id: gudang_id},
      dataType: 'json',
      cache: false,
      success: function(res) {
         if (res && res.success && res.data) {
            available_produks = res.data;
         } else {
            available_produks = [];
         }
         update_all_product_dropdowns();
      },
      error: function() {
         available_produks = [];
         update_all_product_dropdowns();
      }
   });
}

function process_barcode_scan(barcode) {
   if (!barcode) return;
   barcode = $.trim(barcode).toLowerCase();
   
   var list = (available_produks && available_produks.length > 0) ? available_produks : produks_initial;
   var found_product = null;
   list.forEach(function(p) {
      var bCode = (p.barcode || p.kode_produk || p.kode || '').toLowerCase();
      var kCode = (p.kode_produk || p.kode || '').toLowerCase();
      if ((bCode && bCode === barcode) || (kCode && kCode === barcode)) {
         found_product = p;
      }
   });
   
   if (!found_product) {
      swal("Barcode Tidak Ditemukan!", "Kode [" + barcode + "] tidak terdaftar atau tidak memiliki stok pada gudang ini.", "warning");
      $('#barcode_scan_input').val('').focus();
      return;
   }
   
   play_scanner_beep();
   
   var existing_row = null;
   $('.item-row').each(function() {
      var val = $(this).find('.produk-select').val();
      if (parseInt(val) === parseInt(found_product.id)) {
         existing_row = $(this);
      }
   });
   
   if (existing_row) {
      var $qty_inp = existing_row.find('.qty-input');
      var curr_qty = parseFloat($qty_inp.val()) || 0;
      $qty_inp.val(curr_qty + 1);
      recalc_row(existing_row);
      existing_row.css('background-color', '#d4edda').animate({backgroundColor: '#ffffff'}, 1500);
   } else {
      var $first_row = $('.item-row').first();
      var first_val = $first_row.find('.produk-select').val();
      
      if (!first_val) {
         $first_row.find('.produk-select').val(found_product.id).trigger('change');
         $first_row.css('background-color', '#d4edda').animate({backgroundColor: '#ffffff'}, 1500);
      } else {
         $('#btn_add_item').click();
         var $new_row = $('.item-row').last();
         $new_row.find('.produk-select').val(found_product.id).trigger('change');
         $new_row.css('background-color', '#d4edda').animate({backgroundColor: '#ffffff'}, 1500);
      }
   }
   
   if (typeof toastr !== 'undefined') {
      toastr.success('Item: ' + (found_product.nama_produk || found_product.nama), 'Scan Success ✓');
   }
   $('#barcode_scan_input').val('').focus();
}

function format_rp(n){return 'Rp '+parseInt(n||0).toLocaleString('id-ID');}
function recalc_row($row){var qty=parseFloat($row.find('.qty-input').val())||0;var harga=parseFloat($row.find('.harga-input').val())||0;var disc=parseFloat($row.find('.disc-input').val())||0;var sub=(qty*harga)*(1-disc/100);$row.find('.subtotal-display').val(sub.toFixed(0));recalc_total();}

function recalc_total(){
   var sub = 0;
   $('.item-row').each(function(){
      sub += parseFloat($(this).find('.subtotal-display').val()) || 0;
   });
   
   var disc_pct = parseFloat($('#diskon_persen_total').val()) || 0;
   var dis_nominal = sub * (disc_pct / 100);
   $('#diskon_total').val(dis_nominal.toFixed(0));
   
   var pjk_pct = parseFloat($('#pajak_persen').val()) || 0;
   var after_disc = Math.max(0, sub - dis_nominal);
   var pjk_nominal = after_disc * (pjk_pct / 100);
   var total = after_disc + pjk_nominal;
   
   $('#display_subtotal').text(format_rp(sub));
   $('#display_diskon').text('- ' + format_rp(dis_nominal));
   $('#display_pajak').text(format_rp(pjk_nominal) + (pjk_pct > 0 ? ' (' + pjk_pct + '%)' : ''));
   $('#display_total').text(format_rp(total));
}

function validate_row_qty($row) {
   var $qtyInp  = $row.find('.qty-input');
   var qty      = parseFloat($qtyInp.val()) || 0;
   var stokMax  = parseFloat($qtyInp.data('stok-max'));
   if (isNaN(stokMax)) {
      stokMax = parseFloat($row.find('.stok-display').text()) || 0;
   }
   
   $row.find('.stok-warn-msg').remove();

   if (stokMax > 0 && qty > stokMax) {
      $qtyInp.val(stokMax);
      $qtyInp.css({'border': '2px solid #ef4444', 'background-color': '#fef2f2'});
      $qtyInp.after('<small class="text-danger stok-warn-msg" style="display:block; font-weight:600; font-size:11px; margin-top:3px;"><i class="fa fa-exclamation-triangle"></i> Max Stok: <b>' + stokMax + '</b></small>');
      if (typeof swal !== 'undefined') {
         swal("Stok Tidak Cukup!", "Jumlah pesanan melebihi stok yang tersedia di gudang ini (" + stokMax + " unit). Qty otomatis disesuaikan ke " + stokMax + ".", "warning");
      }
      recalc_row($row);
   } else if (qty <= 0) {
      $qtyInp.css({'border': '1px solid #cbd5e1', 'background-color': '#ffffff'});
   } else {
      $qtyInp.css({'border': '1px solid #cbd5e1', 'background-color': '#ffffff'});
   }
}

function cek_stok(produk_id, gudang_id, $span){
   if(!produk_id || !gudang_id){
      $span.text('-').removeClass('bg-red bg-green').addClass('bg-gray');
      return;
   }
   $.get('<?= site_url("administrator/produk/cek_stok"); ?>', {produk_id: produk_id, gudang_id: gudang_id}, function(r){
      var stok_num = parseFloat(r.stok) || 0;
      var $row = $span.closest('.item-row');
      $row.find('.qty-input').data('stok-max', stok_num).attr('max', stok_num);

      if (stok_num > 0) {
         $span.text(stok_num).removeClass('bg-red bg-gray').addClass('bg-green');
      } else {
         $span.text('0').removeClass('bg-green bg-gray').addClass('bg-red');
      }
      validate_row_qty($row);
   }, 'json');
}

function get_harga_by_tipe(produk_id, tipe_pelanggan) {
   var list = (available_produks && available_produks.length > 0) ? available_produks : produks_initial;
   var p = list.find(function(x) { return parseInt(x.id) === parseInt(produk_id); });
   if (!p) return 0;
   if (!tipe_pelanggan) return parseFloat(p.harga_jual || 0);
   tipe_pelanggan = $.trim(tipe_pelanggan).toLowerCase();
   if (tipe_pelanggan === 'grosir' && parseFloat(p.harga_jual_grosir) > 0) return parseFloat(p.harga_jual_grosir);
   if (tipe_pelanggan === 'distributor' && parseFloat(p.harga_jual_distributor) > 0) return parseFloat(p.harga_jual_distributor);
   if (tipe_pelanggan === 'vip' && parseFloat(p.harga_jual_vip) > 0) return parseFloat(p.harga_jual_vip);
   return parseFloat(p.harga_jual || 0);
}

function get_satuan_by_produk(produk_id) {
   var list = (available_produks && available_produks.length > 0) ? available_produks : produks_initial;
   var p = list.find(function(x) { return parseInt(x.id) === parseInt(produk_id); });
   if (!p) return 'Pcs';
   return p.nama_satuan || p.satuan || 'Pcs';
}

$(document).ready(function(){
   var initial_gudang = $('#gudang_id').val();
   if (initial_gudang) {
      load_produk_by_gudang(initial_gudang);
   } else {
      update_all_product_dropdowns();
   }
   // If items loaded with 0 disc, check if header has discount percentage
   var init_disc = parseFloat($('#diskon_persen_total').val()) || 0;
   if (init_disc > 0) {
      $('.disc-input').each(function(){
         if (parseFloat($(this).val()) === 0) {
            $(this).val(init_disc);
         }
      });
   }

   $('.item-row').each(function(){
      recalc_row($(this));
   });
   recalc_total();

   $('#diskon_persen_total').on('input', function(){
      var val = $(this).val();
      $('.disc-input').val(val);
      $('.item-row').each(function(){
         recalc_row($(this));
      });
      recalc_total();
   });

   $('#barcode_scan_input').on('keypress', function(e) {
      if (e.which === 13) {
         e.preventDefault();
         process_barcode_scan($(this).val());
      }
   });
   $('#btn_do_scan').click(function() {
      process_barcode_scan($('#barcode_scan_input').val());
   });
   $(document).on('click', '.btn-simulasi-barcode', function() {
      var bc = $(this).data('barcode');
      process_barcode_scan(bc);
   });

   $('#btn_add_item').click(function(){
      var idx = item_count++;
      $('#item_rows').append('<tr id="item_row_'+idx+'" class="item-row"><td><select class="form-control produk-select" style="width:100%;" name="items['+idx+'][produk_id]">'+build_options()+'</select></td><td><input type="number" step="1" min="1" class="form-control qty-input" name="items['+idx+'][qty]" value="1"></td><td><input type="text" class="form-control" name="items['+idx+'][satuan]" style="width:60px"></td><td><input type="number" class="form-control harga-input rupiah" name="items['+idx+'][harga_satuan]" value="0"></td><td><input type="number" step="1" min="1" class="form-control disc-input rupiah" name="items['+idx+'][diskon_persen]" value="0"></td><td><input type="text" class="form-control subtotal-display" value="0" readonly></td><td><span class="stok-display badge bg-gray">-</span></td><td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td></tr>');
      var $new_row = $('#item_rows tr:last');
      cek_stok($new_row.find('.produk-select').val(), $('#gudang_id').val(), $new_row.find('.stok-display'));
   });

   $(document).on('change','.produk-select',function(){
      var $row = $(this).closest('.item-row');
      var pid = $(this).val();
      var tipe = $('#pelanggan_id option:selected').data('tipe') || '';
      var harga = get_harga_by_tipe(pid, tipe);
      var satuan = get_satuan_by_produk(pid);
      $row.find('.harga-input').val(harga);
      $row.find('input[name*="[satuan]"]').val(satuan);
      recalc_row($row);
      cek_stok(pid, $('#gudang_id').val(), $row.find('.stok-display'));
   });

   $(document).on('input keyup change','.qty-input', function(){
      var $row = $(this).closest('.item-row');
      recalc_row($row);
      validate_row_qty($row);
   });
   $(document).on('input','.harga-input,.disc-input',function(){recalc_row($(this).closest('.item-row'));});
   $(document).on('click','.remove-row',function(){if($('.item-row').length>1){$(this).closest('.item-row').remove();recalc_total();}});
   $('#diskon_persen_total,#diskon_total,#pajak_persen').on('input change',function(){recalc_total();});
   
   $('#gudang_id').on('change',function(){
      var gid = $(this).val();
      load_produk_by_gudang(gid);
   });

   $('#pelanggan_id').on('change',function(){
      var tipe = $(this).find(':selected').data('tipe') || '';
      $('.item-row').each(function(){
         var pid = $(this).find('.produk-select').val();
         if(pid){
            var h = get_harga_by_tipe(pid, tipe);
            $(this).find('.harga-input').val(h);
            recalc_row($(this));
         }
      });
   });

   $('#btn_save').click(function(){
      var btn = $(this);
      if (!$('#gudang_id').val()) {
         show_alert('Pilih Gudang Sumber terlebih dahulu!', 'warning');
         return;
      }

      var is_valid = true;
      var err_msg = '';

      $('.item-row').each(function() {
         var $sel = $(this).find('.produk-select');
         var pid = $sel.val();
         var qty = parseFloat($(this).find('.qty-input').val()) || 0;
         var stok = parseFloat($(this).find('.stok-display').text()) || 0;
         var prod_nama = $sel.find('option:selected').text();

         if (!pid) {
            is_valid = false;
            err_msg = 'Silakan pilih produk untuk semua baris item!';
            return false;
         }

         if (qty <= 0) {
            is_valid = false;
            err_msg = 'Qty penjualan harus lebih dari 0!';
            return false;
         }

         if (qty > stok) {
            is_valid = false;
            err_msg = 'Qty pesanan untuk (' + prod_nama + ') sejumlah ' + qty + ' melebihi Stok Gudang yang tersedia (' + stok + ')!';
            return false;
         }
      });

      if (!is_valid) {
         show_alert(err_msg, 'warning');
         return;
      }

      btn.prop('disabled',true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
      $.ajax({
         url: '<?= $action_url; ?>',
         method: 'POST',
         data: $('#form_so').serialize(),
         dataType: 'json',
         success: function(res){
            if(res.success){
               if(res.redirect){
                  window.location.href = res.redirect;
               }
            } else {
               show_alert(res.message, 'error');
               btn.prop('disabled',false).html('<i class="fa fa-save"></i> <?= $is_edit ? "Update Sales Order" : "Simpan Sales Order"; ?>');
            }
         },
         error: function(){
            btn.prop('disabled',false).html('<i class="fa fa-save"></i> <?= $is_edit ? "Update Sales Order" : "Simpan Sales Order"; ?>');
         }
      });
   });
});
</script>
