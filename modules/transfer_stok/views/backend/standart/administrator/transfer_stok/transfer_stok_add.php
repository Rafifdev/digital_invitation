<?php
$is_edit = isset($transfer);
$action_url = $is_edit
    ? site_url('administrator/transfer_stok/edit_save/' . $transfer->id)
    : site_url('administrator/transfer_stok/add_save');
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
   <h1><i class="fa fa-exchange text-primary"></i> <?= $is_edit ? 'Edit' : 'Buat'; ?> Transfer Stok <small><?= _ent($nomor_baru ?? ''); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/transfer_stok'); ?>">Transfer Stok</a></li>
      <li class="active"><?= $is_edit ? 'Edit' : 'Buat'; ?></li>
   </ol>
</section>

<section class="content">
<form id="form_transfer">
<div class="row">
   <div class="col-md-12">
      <!-- Header Box -->
      <div class="box form-card" style="border-top: 3px solid #3b82f6;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-file-text-o text-primary"></i> Header &amp; Alokasi Transfer Stok</h3>
         </div>
         <div class="box-body">
            <div class="row">
               <div class="col-md-5">
                  <div class="form-group"><label>Gudang Asal Barang <span class="text-danger">*</span></label>
                     <select class="form-control chosen" name="gudang_asal_id" id="gudang_asal_id" required>
                        <option value="">-- Pilih Gudang Asal --</option>
                        <?php foreach($gudangs as $g): ?><option value="<?= $g->id; ?>"><?= _ent($g->nama_gudang); ?></option><?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="col-md-2 text-center" style="padding-top:30px;"><i class="fa fa-arrow-right fa-2x text-primary"></i></div>
               <div class="col-md-5">
                  <div class="form-group"><label>Gudang Tujuan Barang <span class="text-danger">*</span></label>
                     <select class="form-control chosen" name="gudang_tujuan_id" id="gudang_tujuan_id" required>
                        <option value="">-- Pilih Gudang Tujuan --</option>
                        <?php foreach($gudangs as $g): ?><option value="<?= $g->id; ?>"><?= _ent($g->nama_gudang); ?></option><?php endforeach; ?>
                     </select>
                  </div>
               </div>
            </div>

            <div class="row">
               <div class="col-md-4"><div class="form-group"><label>Tanggal Transfer <span class="text-danger">*</span></label><input type="date" class="form-control" name="tanggal_transfer" value="<?= date('Y-m-d'); ?>" required></div></div>
               <div class="col-md-4"><div class="form-group"><label>Est. Tanggal Tiba</label><input type="date" class="form-control" name="tanggal_estimasi_tiba"></div></div>
               <div class="col-md-4"><div class="form-group"><label>No. Bukti Kirim / Resi</label><input type="text" class="form-control" name="nomor_bukti_kirim" placeholder="Opsional (misal: BK-2026-001)"></div></div>
            </div>
            
            <div class="form-group" style="margin-bottom:0;"><label>Catatan Instruksi Transfer</label><textarea class="form-control" name="catatan" rows="2" placeholder="Keterangan atau alasan mutasi antar pergudangan..."></textarea></div>
         </div>
      </div>

      <!-- Item Box -->
      <div class="box form-card" style="border-top: 3px solid #10b981;">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-cubes text-success"></i> Item Produk yang Ditransfer</h3>
            <div class="box-tools pull-right">
               <button type="button" id="btn_add_item" class="btn btn-sm btn-success" style="border-radius:4px;"><i class="fa fa-plus"></i> Tambah Item Produk</button>
            </div>
         </div>
         <div class="box-body">
            <div class="table-responsive">
               <table class="table table-bordered table-striped" id="tbl_items">
                  <thead>
                     <tr class="active">
                        <th width="45%">Produk</th>
                        <th width="15%">Qty Transfer</th>
                        <th width="15%">Stok Gudang Asal</th>
                        <th width="20%">Batch No</th>
                        <th width="40"></th>
                     </tr>
                  </thead>
                  <tbody id="item_rows">
                     <tr class="item-row">
                        <td><select class="form-control produk-select" style="width:100%;" name="items[0][produk_id]"><option value="">-- Pilih Produk --</option><?php foreach($produks as $p): ?><option value="<?= $p->id; ?>"><?= _ent($p->kode_produk.' - '.$p->nama_produk); ?></option><?php endforeach; ?></select></td>
                        <td><input type="number" step="1" min="1" class="form-control qty-input" name="items[0][qty]" value="1"></td>
                        <td><span class="badge bg-gray stok-display" style="padding:6px 10px; font-size:12px;">-</span></td>
                        <td><input type="text" class="form-control" name="items[0][batch_no]"></td>
                        <td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>

         <!-- Card Footer: Action Buttons at the Bottom! -->
         <div class="box-footer" style="padding:15px 20px; background:#f8fafc; border-top:1px solid #f1f5f9; border-radius: 0 0 8px 8px;">
            <div class="pull-left">
               <a href="<?= base_url('administrator/transfer_stok'); ?>" class="btn btn-default btn-flat" style="border-radius:6px; font-weight:600; padding:8px 18px;">
                  <i class="fa fa-arrow-left"></i> Kembali ke Daftar Transfer
               </a>
            </div>
            <div class="pull-right">
               <button type="button" id="btn_save" class="btn btn-primary btn-flat" style="border-radius:6px; font-weight:700; padding:8px 24px; font-size:14px;">
                  <i class="fa fa-save"></i> <?= $is_edit ? 'Update Transfer Stok' : 'Simpan Transfer Stok'; ?>
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
var available_produks = []; 

function build_options() {
   if (!available_produks || available_produks.length === 0) {
      if ($('#gudang_asal_id').val()) {
         return '<option value="">-- Tidak ada barang ber-stok di gudang ini --</option>';
      }
      return '<option value="">-- Pilih Gudang Asal Terlebih Dahulu --</option>';
   }
   var opts = '<option value="">-- Pilih Produk --</option>';
   available_produks.forEach(function(p) {
      opts += '<option value="' + p.id + '">' + p.kode_produk + ' - ' + p.nama_produk + '</option>';
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
      cek_stok($(this).val(), $('#gudang_asal_id').val(), $row.find('.stok-display'));
   });
}

function load_produk_by_gudang(gudang_id) {
   if (!gudang_id) {
      available_produks = [];
      update_all_product_dropdowns();
      return;
   }
   
   $.ajax({
      url: '<?= base_url("administrator/transfer_stok/get_produk_by_gudang"); ?>',
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

function cek_stok(produk_id, gudang_id, $span) {
   if(!produk_id || !gudang_id) {
      $span.text('-').removeClass('bg-red bg-green').addClass('bg-gray');
      return;
   }
   $.get('<?= site_url("administrator/produk/cek_stok"); ?>', {produk_id: produk_id, gudang_id: gudang_id}, function(r) {
      var stok_num = parseFloat(r.stok) || 0;
      if (stok_num > 0) {
         $span.text(stok_num).removeClass('bg-red bg-gray').addClass('bg-green');
      } else {
         $span.text('0').removeClass('bg-green bg-gray').addClass('bg-red');
      }
   }, 'json');
}

$(document).ready(function() {
   var initial_gudang = $('#gudang_asal_id').val();
   if (initial_gudang) {
      load_produk_by_gudang(initial_gudang);
   } else {
      update_all_product_dropdowns();
   }

   $('#btn_add_item').click(function() {
      var idx = item_count++;
      $('#item_rows').append('<tr class="item-row"><td><select class="form-control produk-select" style="width:100%;" name="items['+idx+'][produk_id]">'+build_options()+'</select></td><td><input type="number" step="1" min="1" class="form-control qty-input" name="items['+idx+'][qty]" value="1"></td><td><span class="badge bg-gray stok-display" style="padding:6px 10px; font-size:12px;">-</span></td><td><input type="text" class="form-control" name="items['+idx+'][batch_no]"></td><td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-times"></i></button></td></tr>');
      var $new_row = $('#item_rows tr:last');
      cek_stok($new_row.find('.produk-select').val(), $('#gudang_asal_id').val(), $new_row.find('.stok-display'));
   });

   $(document).on('change', '.produk-select', function() {
      var $row = $(this).closest('.item-row');
      cek_stok($(this).val(), $('#gudang_asal_id').val(), $row.find('.stok-display'));
   });

   $('#gudang_asal_id').on('change', function() {
      var gid = $(this).val();
      load_produk_by_gudang(gid);
   });

   $(document).on('click', '.remove-row', function() {
      if($('.item-row').length > 1) $(this).closest('.item-row').remove();
   });

   $(document).on('input change', 'input[name*="[qty]"]', function() {
      var $row = $(this).closest('.item-row');
      var qty = parseFloat($(this).val()) || 0;
      var stok = parseFloat($row.find('.stok-display').text()) || 0;
      if (qty > stok && stok > 0) {
         $(this).css({'border-color': '#dd4b39', 'background-color': '#fff5f5'});
      } else {
         $(this).css({'border-color': '', 'background-color': ''});
      }
   });

   $('#btn_save').click(function() {
      var btn = $(this);
      var gudang_asal = $('#gudang_asal_id').val();
      var gudang_tujuan = $('#gudang_tujuan_id').val();

      if(!gudang_asal || !gudang_tujuan) {
         show_alert('Pilih Gudang Asal dan Tujuan!', 'warning');
         return;
      }
      if (gudang_asal == gudang_tujuan) {
         show_alert('Gudang Asal dan Tujuan tidak boleh sama!', 'warning');
         return;
      }
      
      var is_valid = true;
      var err_msg = '';

      $('.item-row').each(function() {
         var $sel = $(this).find('.produk-select');
         var pid = $sel.val();
         var qty = parseFloat($(this).find('input[name*="[qty]"]').val()) || 0;
         var stok = parseFloat($(this).find('.stok-display').text()) || 0;
         var prod_nama = $sel.find('option:selected').text();

         if (!pid) {
            is_valid = false;
            err_msg = 'Silakan pilih produk untuk semua baris item!';
            return false;
         }

         if (qty <= 0) {
            is_valid = false;
            err_msg = 'Qty transfer harus lebih dari 0!';
            return false;
         }

         if (qty > stok) {
            is_valid = false;
            err_msg = 'Qty transfer untuk (' + prod_nama + ') sejumlah ' + qty + ' melebihi Stok Asal yang tersedia (' + stok + ')!';
            return false;
         }
      });

      if (!is_valid) {
         show_alert(err_msg, 'warning');
         return;
      }
      
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
      $.ajax({
         url: '<?= $action_url; ?>',
         method: 'POST',
         data: $('#form_transfer').serialize(),
         dataType: 'json',
         success: function(res) {
            if(res.success) {
               window.location.href = res.redirect;
            } else {
               show_alert(res.message, 'error');
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? "Update Transfer Stok" : "Simpan Transfer Stok"; ?>');
            }
         },
         error: function() {
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> <?= $is_edit ? "Update Transfer Stok" : "Simpan Transfer Stok"; ?>');
         }
      });
   });
});
</script>
