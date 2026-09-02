<?php
$is_edit = isset($inv);
$action_url = $is_edit
    ? site_url('administrator/invoice/edit_save/' . $inv->id)
    : site_url('administrator/invoice/add_save');

$so_obj     = $so ?? null;
$items_list = $so_items ?? ($items ?? []);
$nomor_inv  = $inv->nomor_invoice ?? ($nomor_baru ?? '');

// Calculate discount percentage fallback from SO/Invoice subtotal & diskon_total if diskon_persen_total is 0
$so_disc_pct = (float)($so_obj->diskon_persen_total ?? ($inv->diskon_persen_total ?? 0));
if ($so_disc_pct == 0) {
   $sub_v  = (float)($so_obj->subtotal ?? ($inv->subtotal ?? 0));
   $disc_v = (float)($so_obj->diskon_total ?? ($inv->diskon_total ?? 0));
   if ($disc_v > 0 && $sub_v > 0) {
      $so_disc_pct = round(($disc_v / $sub_v) * 100, 1);
   }
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
   <h1><i class="fa fa-file-text-o text-primary"></i> <?= $is_edit ? 'Edit Invoice' : 'Buat Invoice Penjualan'; ?> <small><?= $is_edit ? _ent($nomor_inv) : ('dari SO: '._ent($so_obj->nomor_so ?? '')); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/invoice'); ?>">Invoice</a></li>
      <li class="active"><?= $is_edit ? 'Edit' : 'Buat Baru'; ?></li>
   </ol>
</section>

<section class="content">
<form id="form_invoice">
   <input type="hidden" name="so_id" value="<?= $so_obj->id ?? ($inv->so_id ?? ''); ?>">
   <input type="hidden" name="pelanggan_id" value="<?= $so_obj->pelanggan_id ?? ($inv->pelanggan_id ?? ''); ?>">
<div class="row">
   <div class="col-md-12">
      <!-- Header Box -->
      <div class="box form-card" style="border-top: 3px solid #3b82f6;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-info-circle text-primary"></i> Header &amp; Informasi Invoice</h3>
         </div>
         <div class="box-body">
            <div class="callout callout-info" style="border-radius: 6px;">
               <p style="margin: 0; font-size: 14px;">
                  <i class="fa fa-shopping-bag"></i> <strong>Referensi SO:</strong> <?= _ent($so_obj->nomor_so ?? ($inv->nomor_so ?? '-')); ?> 
                  &nbsp;&bull;&nbsp; 
                  <i class="fa fa-user"></i> <strong>Pelanggan:</strong> <?= _ent($so_obj->nama_pelanggan ?? ($inv->nama_pelanggan ?? '-')); ?>
               </p>
            </div>
            
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group">
                     <label>No. Invoice</label>
                     <input type="text" class="form-control" value="<?= _ent($nomor_inv); ?>" style="font-weight: bold; background: #f8fafc;" readonly>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group">
                     <label>Tanggal Invoice <span class="text-danger">*</span></label>
                     <input type="date" class="form-control" name="tanggal_invoice" value="<?= $is_edit ? $inv->tanggal_invoice : date('Y-m-d'); ?>" required>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group">
                     <label>Jatuh Tempo</label>
                     <input type="date" class="form-control" name="tanggal_jatuh_tempo" value="<?= $is_edit ? $inv->tanggal_jatuh_tempo : date('Y-m-d', strtotime('+30 days')); ?>">
                  </div>
               </div>
            </div>

            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Gudang Pengeluaran Stok</label>
                     <select class="form-control chosen" name="gudang_id">
                        <option value="">-- Pilih Gudang --</option>
                        <?php 
                           $selected_gudang = $so_obj->gudang_id ?? ($inv->gudang_id ?? 0);
                           foreach($gudangs as $g): 
                        ?>
                           <option value="<?= $g->id; ?>" <?= $g->id == $selected_gudang ? 'selected':'' ?>><?= _ent($g->nama_gudang); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group">
                     <label>Diskon Keseluruhan (%)</label>
                     <input type="number" step="0.1" min="0" max="100" class="form-control" name="diskon_persen_total" id="diskon_persen_total" value="<?= $so_disc_pct; ?>">
                     <input type="hidden" name="diskon_total" id="diskon_total" value="<?= (float)($so_obj->diskon_total ?? ($inv->diskon_total ?? 0)); ?>">
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group">
                     <label>Pajak PPN (%)</label>
                     <input type="number" step="0.1" min="0" max="100" class="form-control" name="pajak_persen" id="pajak_persen" value="<?= (float)($so_obj->pajak_persen ?? ($inv->pajak_persen ?? 0)); ?>">
                  </div>
               </div>
            </div>

            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label>No. Resi / AWB / Bukti Pengiriman</label>
                     <input type="text" class="form-control" name="nomor_resi" placeholder="Misal: JNE123456789" value="<?= _ent($inv->nomor_resi ?? ''); ?>">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Pilihan Ekspedisi / Kurir Pengiriman</label>
                     <?php $curr_exp = $inv->ekspedisi ?? ''; ?>
                     <select class="form-control" name="ekspedisi">
                        <option value="">-- Pilih Ekspedisi / Kurir --</option>
                        <option value="JNE Express" <?= $curr_exp=='JNE Express'?'selected':''; ?>>JNE Express</option>
                        <option value="J&T Express" <?= $curr_exp=='J&T Express'?'selected':''; ?>>J&T Express</option>
                        <option value="SiCepat" <?= $curr_exp=='SiCepat'?'selected':''; ?>>SiCepat Express</option>
                        <option value="TIKI" <?= $curr_exp=='TIKI'?'selected':''; ?>>TIKI</option>
                        <option value="POS Indonesia" <?= $curr_exp=='POS Indonesia'?'selected':''; ?>>POS Indonesia</option>
                        <option value="AnterAja" <?= $curr_exp=='AnterAja'?'selected':''; ?>>AnterAja</option>
                        <option value="Ninja Xpress" <?= $curr_exp=='Ninja Xpress'?'selected':''; ?>>Ninja Xpress</option>
                        <option value="GoSend / GrabExpress" <?= $curr_exp=='GoSend / GrabExpress'?'selected':''; ?>>GoSend / GrabExpress</option>
                        <option value="Kurir Toko / Internal" <?= $curr_exp=='Kurir Toko / Internal'?'selected':''; ?>>Kurir Toko / Internal</option>
                        <option value="Lainnya" <?= $curr_exp=='Lainnya'?'selected':''; ?>>Lainnya</option>
                     </select>
                  </div>
               </div>
            </div>

            <div class="form-group" style="margin-bottom:0;">
               <label>Catatan Invoice</label>
               <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan tambahan invoice..."><?= _ent($so_obj->catatan ?? ($inv->catatan ?? '')); ?></textarea>
            </div>
         </div>
      </div>

      <!-- Item Box -->
      <div class="box form-card" style="border-top: 3px solid #f59e0b;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list text-warning"></i> Detail Rincian Item Invoice</h3>
         </div>
         <div class="box-body">
            <div class="table-responsive">
               <table class="table table-bordered table-striped">
                  <thead>
                     <tr class="active">
                        <th>Produk</th>
                        <th class="text-center" width="110">Qty SO</th>
                        <th width="130">Qty Invoice</th>
                        <th width="180">Harga Satuan (IDR)</th>
                        <th width="110">Disc%</th>
                        <th width="180">Subtotal</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach($items_list as $i=>$item): ?>
                     <?php 
                        $it_disc = (float)($item->diskon_persen ?? 0);
                        if ($it_disc == 0) {
                           $it_disc = $so_disc_pct;
                        }
                     ?>
                     <tr class="item-row">
                        <input type="hidden" name="items[<?= $i; ?>][produk_id]" value="<?= $item->produk_id; ?>">
                        <input type="hidden" name="items[<?= $i; ?>][so_item_id]" value="<?= $item->so_item_id ?? ($item->id ?? ''); ?>">
                        <input type="hidden" name="items[<?= $i; ?>][satuan]" value="<?= _ent($item->satuan); ?>">
                        <td>
                           <strong><?= _ent($item->nama_produk); ?></strong><br>
                           <small class="text-muted"><i class="fa fa-barcode"></i> <?= _ent($item->kode_produk); ?></small>
                        </td>
                        <td class="text-center"><strong><?= number_format((float)($item->qty_so ?? $item->qty), 0, ',', '.'); ?></strong> <?= _ent($item->singkatan??$item->satuan); ?></td>
                        <td><input type="number" step="1" class="form-control qty-input" name="items[<?= $i; ?>][qty]" value="<?= (int)$item->qty; ?>" min="1"></td>
                        <td><input type="text" class="form-control harga-input rupiah" name="items[<?= $i; ?>][harga_satuan]" value="<?= number_format((float)$item->harga_satuan, 0, ',', '.'); ?>"></td>
                        <td><input type="number" step="0.1" class="form-control disc-input" name="items[<?= $i; ?>][diskon_persen]" value="<?= $it_disc; ?>" max="100"></td>
                        <td><input type="text" class="form-control subtotal-display" value="Rp <?= number_format((float)$item->subtotal, 0, ',', '.'); ?>" style="font-weight: bold; background: #fafafa;" readonly></td>
                     </tr>
                     <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                     <tr><td colspan="5" class="text-right"><strong>Subtotal:</strong></td><td><strong id="display_subtotal">Rp 0</strong></td></tr>
                     <tr><td colspan="5" class="text-right">Diskon Keseluruhan:</td><td class="text-danger" id="display_diskon">- Rp 0</td></tr>
                     <tr><td colspan="5" class="text-right">Pajak PPN:</td><td id="display_pajak">Rp 0</td></tr>
                     <tr class="active"><td colspan="5" class="text-right"><strong>TOTAL INVOICE:</strong></td><td><strong class="text-success" id="display_total" style="font-size: 16px;">Rp 0</strong></td></tr>
                  </tfoot>
               </table>
            </div>
         </div>

         <!-- Card Footer: Action Buttons at the Bottom! -->
         <div class="box-footer" style="padding:15px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
            <div class="pull-left">
               <a href="<?= base_url('administrator/invoice'); ?>" class="btn btn-default btn-flat" style="border-radius:6px; font-weight:600; padding:8px 18px;">
                  <i class="fa fa-arrow-left"></i> Kembali ke Daftar Invoice
               </a>
            </div>
            <div class="pull-right">
               <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:6px; font-weight:700; padding:8px 24px; font-size:14px;">
                  <i class="fa fa-save"></i> <?= $is_edit ? 'Update Invoice' : 'Simpan &amp; Buat Invoice'; ?>
               </button>
            </div>
         </div>
      </div>
   </div>
</div>
</form>
</section>

<script>
function format_rp(n){ return 'Rp ' + Math.round(n || 0).toLocaleString('id-ID'); }
function parse_number(val) {
   if (typeof val === 'number') return val;
   var str = String(val || 0).replace(/\./g, '').replace(/,/g, '.').replace(/[^0-9.-]/g, '');
   return parseFloat(str) || 0;
}
function formatRupiahVal(val) {
   var number_string = Math.round(parse_number(val)).toString().replace(/[^0-9]/g, '');
   var sisa   = number_string.length % 3;
   var rupiah = number_string.substr(0, sisa);
   var ribuan = number_string.substr(sisa).match(/\d{3}/g);
   if (ribuan) {
      var separator = sisa ? '.' : '';
      rupiah += separator + ribuan.join('.');
   }
   return rupiah || '0';
}

function recalc_row($row){
   var qty = parseFloat($row.find('.qty-input').val()) || 0;
   var h   = parse_number($row.find('.harga-input').val());
   var sub = qty * h;
   $row.find('.subtotal-display').val(format_rp(sub));
   recalc_total();
}

function recalc_total(){
   var sub = 0;
   $('.item-row').each(function(){
      var qty = parseFloat($(this).find('.qty-input').val()) || 0;
      var h   = parse_number($(this).find('.harga-input').val());
      sub += qty * h;
   });

   var disc_pct = parseFloat($('#diskon_persen_total').val()) || 0;
   var dis_nominal = sub * (disc_pct / 100);
   $('#diskon_total').val(Math.round(dis_nominal));

   var pjk_pct = parseFloat($('#pajak_persen').val()) || 0;
   var after_disc = Math.max(0, sub - dis_nominal);
   var pjk_nominal = after_disc * (pjk_pct / 100);
   var total = after_disc + pjk_nominal;

   $('#display_subtotal').text(format_rp(sub));
   $('#display_diskon').text('- ' + format_rp(dis_nominal) + (disc_pct > 0 ? ' (' + disc_pct + '%)' : ''));
   $('#display_pajak').text(format_rp(pjk_nominal) + (pjk_pct > 0 ? ' (' + pjk_pct + '%)' : ''));
   $('#display_total').text(format_rp(total));
}

$(document).ready(function(){
   $(document).on('keyup input', '.rupiah', function() {
      var val = $(this).val().replace(/[^0-9]/g, '');
      $(this).val(formatRupiahVal(val));
   });

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

   $(document).on('input keyup change','.qty-input,.harga-input,.disc-input',function(){
      recalc_row($(this).closest('.item-row'));
   });
   
   $('#diskon_persen_total').on('input', function(){
      var val = $(this).val();
      $('.disc-input').val(val);
      $('.item-row').each(function(){
         var qty = parseFloat($(this).find('.qty-input').val()) || 0;
         var h   = parse_number($(this).find('.harga-input').val());
         var d   = parseFloat($(this).find('.disc-input').val()) || 0;
         var sub = (qty * h) * (1 - d / 100);
         $(this).find('.subtotal-display').val(format_rp(sub));
      });
      recalc_total();
   });

   $('#pajak_persen').on('input',function(){recalc_total();});

   $('#btn_save').click(function(){
      var btn = $(this);
      btn.prop('disabled',true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
      $.ajax({
         url:'<?= $action_url; ?>',
         method:'POST',
         data:$('#form_invoice').serialize(),
         dataType:'json',
         success:function(res){
            if(res.success){
               window.location.href=res.redirect||'<?= base_url("administrator/invoice"); ?>';
            }else{
               show_alert(res.message,'error');
               btn.prop('disabled',false).html('<i class="fa fa-save"></i> <?= $is_edit ? "Update Invoice" : "Simpan &amp; Buat Invoice"; ?>');
            }
         },
         error:function(){
            btn.prop('disabled',false).html('<i class="fa fa-save"></i> <?= $is_edit ? "Update Invoice" : "Simpan &amp; Buat Invoice"; ?>');
         }
      });
   });
});
</script>
