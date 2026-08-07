<?php
$is_edit = isset($retur);
$action_url = $is_edit
    ? site_url('administrator/retur_pembelian/edit_save/' . $retur->id)
    : site_url('administrator/retur_pembelian/add_save');
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
   <h1><i class="fa fa-reply text-primary"></i> <?= $is_edit ? 'Edit' : 'Buat'; ?> Retur Pembelian <small><?= _ent($nomor_baru ?? ''); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/retur_pembelian'); ?>">Retur Pembelian</a></li>
      <li class="active"><?= $is_edit ? 'Edit' : 'Buat'; ?></li>
   </ol>
</section>

<section class="content">
<form id="form_retur">
<div class="row">
   <div class="col-md-12">
      <!-- Header Retur Box -->
      <div class="box form-card" style="border-top: 3px solid #3b82f6;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-file-text-o text-primary"></i> Header &amp; Informasi Retur Pembelian</h3>
         </div>
         <div class="box-body">
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group"><label>Referensi Penerimaan (Opsional)</label>
                     <select class="form-control chosen" name="penerimaan_id" id="penerimaan_id">
                        <option value="">-- Manual / Tanpa Referensi PO --</option>
                        <?php foreach($penerimaans as $p): ?>
                            <?php $po_lbl = !empty($p->nomor_po) ? ' (Ref PO: ' . $p->nomor_po . ')' : ''; ?>
                            <option value="<?= $p->id; ?>" <?= ((isset($selected_penerimaan_id) && $selected_penerimaan_id == $p->id) || ($is_edit && isset($retur) && $retur->penerimaan_id == $p->id)) ? 'selected' : ''; ?>><?= _ent($p->nomor_penerimaan . $po_lbl); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group"><label>Tanggal Retur <span class="text-danger">*</span></label>
                     <input type="date" class="form-control" name="tanggal_retur" value="<?= $is_edit ? $retur->tanggal_retur : date('Y-m-d'); ?>" required>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group"><label>Alasan Utama <span class="text-danger">*</span></label>
                     <select class="form-control" name="alasan" required>
                        <option value="cacat" <?= ($is_edit && $retur->alasan == 'cacat') ? 'selected' : ''; ?>>Cacat / Rusak</option>
                        <option value="salah_kirim" <?= ($is_edit && $retur->alasan == 'salah_kirim') ? 'selected' : ''; ?>>Salah Kirim</option>
                        <option value="kadaluarsa" <?= ($is_edit && $retur->alasan == 'kadaluarsa') ? 'selected' : ''; ?>>Kadaluarsa</option>
                        <option value="lainnya" <?= ($is_edit && $retur->alasan == 'lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                     </select>
                  </div>
               </div>
            </div>

            <div class="row">
               <div class="col-md-6">
                  <div class="form-group"><label>Supplier <span class="text-danger">*</span></label>
                     <select class="form-control chosen" name="supplier_id" id="supplier_id" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach($suppliers as $s): ?>
                           <option value="<?= $s->id; ?>" <?= ($is_edit && $retur->supplier_id == $s->id) ? 'selected' : ''; ?>><?= _ent($s->kode_supplier.' - '.$s->nama_supplier); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group" style="margin-bottom:0;"><label>Gudang Asal Barang <span class="text-danger">*</span></label>
                     <select class="form-control chosen" name="gudang_id" id="gudang_id" required>
                        <option value="">-- Pilih Gudang --</option>
                        <?php foreach($gudangs as $g): ?>
                           <option value="<?= $g->id; ?>" <?= ($is_edit && $retur->gudang_id == $g->id) ? 'selected' : ''; ?>><?= _ent($g->nama_gudang); ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Item Retur Box -->
      <div class="box form-card" style="border-top: 3px solid #f59e0b;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list text-warning"></i> Item Produk Retur</h3>
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
                        <th width="12%">Qty Retur</th>
                        <th width="18%">Harga Satuan (IDR)</th>
                        <th width="18%">Subtotal (IDR)</th>
                        <th>Alasan Spesifik Item</th>
                        <th width="40"></th>
                     </tr>
                  </thead>
                  <tbody id="item_rows">
                     <?php if ($is_edit && !empty($items)): ?>
                        <?php foreach ($items as $idx => $it): ?>
                           <tr class="item-row" id="item_row_<?= $idx; ?>">
                              <td>
                                 <select class="form-control produk-select" name="items[<?= $idx; ?>][produk_id]" style="width:100%;">
                                    <option value="">-- Pilih Produk --</option>
                                    <?php foreach($produks as $pr): ?>
                                       <option value="<?= $pr->id; ?>" data-harga="<?= (int) round($pr->harga_beli); ?>" <?= $it->produk_id == $pr->id ? 'selected' : ''; ?>><?= _ent($pr->kode_produk.' - '.$pr->nama_produk); ?></option>
                                    <?php endforeach; ?>
                                 </select>
                              </td>
                              <td><input type="number" step="1" min="1" class="form-control qty" name="items[<?= $idx; ?>][qty_retur]" value="<?= (int)$it->qty_retur; ?>" min="1"></td>
                              <td><input type="text" class="form-control rupiah harga" name="items[<?= $idx; ?>][harga_satuan]" value="<?= number_format($it->harga_satuan, 0, ',', '.'); ?>"></td>
                              <td><input type="text" class="form-control rupiah subtotal-disp" value="<?= number_format($it->subtotal, 0, ',', '.'); ?>" readonly></td>
                              <td><input type="text" class="form-control" name="items[<?= $idx; ?>][alasan]" value="<?= _ent($it->alasan ?? ''); ?>" placeholder="Alasan"></td>
                              <td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td>
                           </tr>
                        <?php endforeach; ?>
                     <?php else: ?>
                        <tr class="item-row" id="item_row_0">
                           <td>
                              <select class="form-control produk-select" name="items[0][produk_id]" style="width:100%;">
                                 <option value="">-- Pilih Produk --</option>
                                 <?php foreach($produks as $pr): ?>
                                    <option value="<?= $pr->id; ?>" data-harga="<?= (int) round($pr->harga_beli); ?>"><?= _ent($pr->kode_produk.' - '.$pr->nama_produk); ?></option>
                                 <?php endforeach; ?>
                              </select>
                           </td>
                           <td><input type="number" step="1" min="1" class="form-control qty" name="items[0][qty_retur]" value="1" min="1"></td>
                           <td><input type="text" class="form-control rupiah harga" name="items[0][harga_satuan]" value="0"></td>
                           <td><input type="text" class="form-control rupiah subtotal-disp" value="0" readonly></td>
                           <td><input type="text" class="form-control" name="items[0][alasan]" placeholder="Alasan"></td>
                           <td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td>
                        </tr>
                     <?php endif; ?>
                  </tbody>
               </table>
            </div>
            <div id="items_loaded" class="callout callout-info" style="display:none; margin-top:15px; border-radius:6px;">
               <p><i class="fa fa-info-circle"></i> Item dari penerimaan dimuat secara otomatis. Anda dapat menyesuaikan Qty atau Alasan.</p>
            </div>
         </div>

         <!-- Card Footer: Action Buttons at the Bottom! -->
         <div class="box-footer" style="padding:15px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
            <div class="pull-left">
               <a href="<?= base_url('administrator/retur_pembelian'); ?>" class="btn btn-default btn-flat" style="border-radius:6px; font-weight:600; padding:8px 18px;">
                  <i class="fa fa-arrow-left"></i> Kembali ke Daftar Retur
               </a>
            </div>
            <div class="pull-right">
               <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:6px; font-weight:700; padding:8px 24px; font-size:14px;">
                  <i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Retur' : 'Simpan Retur Pembelian'; ?>
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
var produks_data = <?= json_encode(array_map(function($p){
   return ['id' => $p->id, 'nama' => $p->kode_produk . ' - ' . $p->nama_produk, 'harga_beli' => (int) round($p->harga_beli)];
}, $produks)); ?>;

function build_produk_options() {
   var html = '<option value="">-- Pilih Produk --</option>';
   produks_data.forEach(function(p) {
      html += '<option value="' + p.id + '" data-harga="' + p.harga_beli + '">' + p.nama + '</option>';
   });
   return html;
}

function add_item_row() {
   var idx = item_count++;
   var rowHtml = '<tr class="item-row" id="item_row_' + idx + '">' +
      '<td><select class="form-control produk-select" name="items[' + idx + '][produk_id]" style="width:100%;">' + build_produk_options() + '</select></td>' +
      '<td><input type="number" step="1" min="1" class="form-control qty" name="items[' + idx + '][qty_retur]" value="1" min="1"></td>' +
      '<td><input type="text" class="form-control rupiah harga" name="items[' + idx + '][harga_satuan]" value="0"></td>' +
      '<td><input type="text" class="form-control rupiah subtotal-disp" value="0" readonly></td>' +
      '<td><input type="text" class="form-control" name="items[' + idx + '][alasan]" placeholder="Alasan"></td>' +
      '<td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td>' +
      '</tr>';
   $('#item_rows').append(rowHtml);
}

function update_row_subtotal($r) {
   var qty = Math.max(1, (parseInt($r.find('.qty').val(), 10) || 1));
   var rawHarga = ($r.find('.harga').val() || '').replace(/[^0-9]/g, '');
   var harga = parseInt(rawHarga || 0, 10);
   var sub = qty * harga;
   $r.find('.subtotal-disp').val(sub > 0 ? sub.toLocaleString('id-ID') : '0');
}

$(document).ready(function() {
   $('#btn_add_item').click(function() {
      add_item_row();
   });

   $('#penerimaan_id').on('change', function() {
      var pid = $(this).val();
      if (!pid) return;
      $.get('<?= site_url("administrator/retur_pembelian/get_items"); ?>', { penerimaan_id: pid }, function(r) {
         if (r.success) {
            if (r.penerimaan) {
               $('#supplier_id').val(r.penerimaan.supplier_id).trigger('chosen:updated');
               $('#gudang_id').val(r.penerimaan.gudang_id).trigger('chosen:updated');
            }
            if (r.items && r.items.length > 0) {
               $('#item_rows').empty();
               item_count = 0;
               var has_damaged = false;
               r.items.forEach(function(it) {
                  var idx = item_count++;
                  var qty_rusak = Math.round(parseFloat(it.qty_rusak) || 0);
                  var qty_terima = Math.round(parseFloat(it.qty_terima) || 1);
                  var default_qty = qty_rusak > 0 ? qty_rusak : qty_terima;
                  var harga = Math.round(parseFloat(it.harga_satuan || it.harga_beli) || 0);
                  var sub = default_qty * harga;
                  var harga_fmt = harga > 0 ? harga.toLocaleString('id-ID') : '0';
                  var sub_fmt = sub > 0 ? sub.toLocaleString('id-ID') : '0';
                  var default_alasan = qty_rusak > 0 ? ("Barang Cacat/Rusak saat Penerimaan (" + qty_rusak + " unit)") : "";
                  
                  if (qty_rusak > 0) {
                     has_damaged = true;
                  }

                  var damaged_badge = qty_rusak > 0 
                     ? '<br><span class="label label-danger" style="font-size:10px; margin-top:3px; display:inline-block;"><i class="fa fa-exclamation-triangle"></i> Terdeteksi ' + qty_rusak + ' Unit Rusak saat Penerimaan</span>' 
                     : '';

                  var rowHtml = '<tr class="item-row" id="item_row_' + idx + '">' +
                     '<td>' +
                        '<strong>' + (it.kode_produk ? it.kode_produk + ' - ' : '') + (it.nama_produk ? it.nama_produk : '<i class="text-muted">(Produk Telah Dihapus)</i>') + '</strong>' + damaged_badge +
                        '<input type="hidden" name="items[' + idx + '][produk_id]" value="' + it.produk_id + '">' +
                     '</td>' +
                     '<td><input type="number" step="1" min="1" class="form-control qty" name="items[' + idx + '][qty_retur]" value="' + default_qty + '" max="' + qty_terima + '" min="1"></td>' +
                     '<td><input type="text" class="form-control rupiah harga" name="items[' + idx + '][harga_satuan]" value="' + harga_fmt + '"></td>' +
                     '<td><input type="text" class="form-control rupiah subtotal-disp" value="' + sub_fmt + '" readonly></td>' +
                     '<td><input type="text" class="form-control" name="items[' + idx + '][alasan]" value="' + default_alasan + '" placeholder="Alasan"></td>' +
                     '<td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td>' +
                     '</tr>';
                  $('#item_rows').append(rowHtml);
               });

               if (has_damaged) {
                  $('select[name="alasan"]').val('cacat');
                  $('#items_loaded').html('<p><i class="fa fa-exclamation-circle text-warning"></i> <strong>Otomatisasi Barang Rusak:</strong> Terdeteksi barang kondisi rusak dari dokumen penerimaan ini. Kuantitas retur dan alasan telah diisi secara otomatis sesuai jumlah barang rusak!</p>').addClass('callout-warning').removeClass('callout-info').show();
               } else {
                  $('#items_loaded').html('<p><i class="fa fa-info-circle"></i> Item dari penerimaan dimuat secara otomatis. Anda dapat menyesuaikan Qty atau Alasan.</p>').addClass('callout-info').removeClass('callout-warning').show();
               }
            }
         }
      }, 'json');
   });

<?php if (!$is_edit): ?>
   if ($('#penerimaan_id').val()) {
      $('#penerimaan_id').trigger('change');
   }
<?php else: ?>
   setTimeout(function(){
      $('#penerimaan_id, #supplier_id, #gudang_id').trigger('chosen:updated');
   }, 150);
<?php endif; ?>

   $(document).on('change', '.produk-select', function() {
      var $r = $(this).closest('.item-row');
      var harga = Math.round(parseFloat($(this).find(':selected').data('harga')) || 0);
      $r.find('.harga').val(harga > 0 ? harga.toLocaleString('id-ID') : '0');
      update_row_subtotal($r);
   });

   $(document).on('input keyup change', '.qty, .harga', function() {
      var $r = $(this).closest('.item-row');
      update_row_subtotal($r);
   });

   $(document).on('click', '.remove-row', function() {
      if ($('.item-row').length > 1) {
         $(this).closest('.item-row').remove();
      }
   });

   $('#btn_save').click(function() {
      var btn = $(this);
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
      $.ajax({
         url: '<?= $action_url; ?>',
         method: 'POST',
         data: $('#form_retur').serialize(),
         dataType: 'json',
         success: function(res) {
            if (res.success) {
               window.location.href = res.redirect || '<?= base_url("administrator/retur_pembelian"); ?>';
            } else {
               show_alert(res.message, 'error');
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Retur' : 'Simpan Retur Pembelian'; ?>');
            }
         },
         error: function() {
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? 'Update Data Retur' : 'Simpan Retur Pembelian'; ?>');
         }
      });
   });
});
</script>
