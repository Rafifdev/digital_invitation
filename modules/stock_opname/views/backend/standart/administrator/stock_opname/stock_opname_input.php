<?php /* stock_opname_input.php — input stok fisik */ ?>
<section class="content-header">
   <h1>Input Stok Fisik <small><?= _ent($opname->nomor_opname); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/stock_opname'); ?>">Stock Opname</a></li>
      <li class="active">Input Stok Fisik</li>
   </ol>
</section>

<section class="content">
   <div class="callout callout-info" style="border-left: 5px solid #00c0ef;">
      <h4><i class="fa fa-info-circle"></i> Petunjuk Pengisian Hasil Hitung Fisik:</h4>
      <p>Anda dapat menginput jumlah hasil hitung fisik dengan 2 cara mudah:</p>
      <ul>
         <li><strong>Pindai Barcode (USB Scanner / Mobile)</strong>: Arahkan scanner ke kolom Scan Barcode di bawah. Setiap scan akan otomatis menambah jumlah fisik (+1) pada produk bersangkutan.</li>
         <li><strong>Input Manual</strong>: Ketik jumlah fisik secara langsung pada kolom tabel di bawah ini.</li>
      </ul>
   </div>

   <!-- Box Scan Barcode / Mobile Input -->
   <div class="box box-info">
      <div class="box-header with-border">
         <h3 class="box-title"><i class="fa fa-barcode"></i> Scan Barcode Scanner / Mobile Input</h3>
      </div>
      <div class="box-body">
         <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-barcode fa-lg"></i></span>
            <input type="text" id="barcode_scanner_input" class="form-control input-lg" placeholder="Scan barcode produk atau ketik Kode Produk (misal: PRD-0003, 899100100003)..." autofocus>
            <span class="input-group-btn">
               <button class="btn btn-info btn-lg" type="button" id="btn_scan_add"><i class="fa fa-plus-circle"></i> Hitung (+1)</button>
            </span>
         </div>
         <small class="text-muted"><i class="fa fa-laptop"></i> Arahkan USB Barcode Scanner atau Alat Scanner Mobile ke kolom input di atas. Tekan Enter atau otomatis terdeteksi saat scan.</small>
      </div>
   </div>

   <div class="callout callout-warning">
      <p>Tanggal Opname: <strong><?= date('d/m/Y', strtotime($opname->tanggal_opname)); ?></strong> | Gudang: <strong><?= _ent($opname->nama_gudang); ?></strong> | Status: <span class="label label-warning"><?= strtoupper($opname->status); ?></span></p>
   </div>

   <form id="form_fisik">
      <div class="box box-primary">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list-ol"></i> Daftar Hasil Hitung Fisik</h3>
            <div class="box-tools pull-right">
               <button type="button" id="btn_save" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Simpan & Review Opname</button>
            </div>
         </div>
         <div class="box-body">
            <div class="table-responsive">
               <table class="table table-bordered table-striped table-hover" id="table_opname_items">
                  <thead>
                     <tr>
                        <th width="40">#</th>
                        <th>Kode & Nama Produk</th>
                        <th class="text-center" width="120">Stok Sistem</th>
                        <th class="text-center" width="140">Stok Fisik</th>
                        <th class="text-center" width="120">Selisih</th>
                        <th width="150">Batch No (Opsional)</th>
                        <th>Alasan Selisih (Wajib jika ada selisih)</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach($items as $i => $item): ?>
                     <tr id="row_item_<?= $item->id; ?>" data-barcode="<?= _ent(isset($item->barcode) ? $item->barcode : ''); ?>" class="<?= $item->selisih != 0 && $item->stok_fisik !== null ? ($item->selisih < 0 ? 'danger' : 'warning') : ''; ?>">
                        <td><?= $i+1; ?></td>
                        <td>
                           <strong class="nama-prd-text"><?= _ent($item->nama_produk); ?></strong><br>
                           <small class="text-muted">Kode: <span class="kode-prd-text"><?= _ent($item->kode_produk); ?></span> <?= !empty($item->barcode) ? '| Barcode: ' . _ent($item->barcode) : ''; ?></small>
                        </td>
                        <td class="text-center">
                           <strong><?= number_format($item->stok_sistem, 0, ',', '.'); ?></strong>
                           <input type="hidden" name="items[<?= $item->id; ?>][stok_sistem]" value="<?= $item->stok_sistem; ?>">
                           <input type="hidden" name="items[<?= $item->id; ?>][harga_satuan]" value="<?= $item->harga_satuan; ?>">
                        </td>
                        <td class="text-center">
                           <input type="number" step="1" min="0" class="form-control text-center fisik-input" name="items[<?= $item->id; ?>][stok_fisik]" value="<?= $item->stok_fisik !== null ? intval($item->stok_fisik) : ''; ?>" data-sistem="<?= intval($item->stok_sistem); ?>">
                        </td>
                        <td class="text-center selisih-col">
                           <span class="selisih-val <?= $item->selisih < 0 ? 'text-danger' : ($item->selisih > 0 ? 'text-success' : ''); ?>">
                              <?= $item->selisih !== null ? ($item->selisih > 0 ? '+' : '') . intval($item->selisih) : '-'; ?>
                           </span>
                        </td>
                        <td>
                           <input type="text" class="form-control" name="items[<?= $item->id; ?>][batch_no]" value="<?= _ent($item->batch_no ?? ''); ?>" placeholder="Batch...">
                        </td>
                        <td>
                           <input type="text" class="form-control alasan-input" name="items[<?= $item->id; ?>][alasan_selisih]" value="<?= _ent($item->alasan_selisih); ?>" placeholder="<?= ($item->selisih != 0 && $item->stok_fisik !== null) ? 'Wajib isi alasan selisih!' : 'Alasan selisih (jika ada)'; ?>" <?= ($item->selisih != 0 && $item->stok_fisik !== null) ? 'required' : ''; ?>>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </form>
</section>

<script>
$(document).ready(function(){
   function processBarcodeScan(code) {
      code = $.trim(code).toLowerCase();
      if (!code) return;
      var found = false;
      $('.fisik-input').each(function(){
         var $row = $(this).closest('tr');
         var kodePrd = $.trim($row.find('.kode-prd-text').text()).toLowerCase();
         var barcodePrd = $.trim($row.attr('data-barcode') || '').toLowerCase();
         if (kodePrd === code || barcodePrd === code) {
            found = true;
            var currentVal = parseFloat($(this).val()) || 0;
            var newVal = currentVal + 1;
            $(this).val(newVal).trigger('input');
            
            $row.addClass('success');
            setTimeout(function(){ $row.removeClass('success'); }, 1500);
            
            $('html, body').animate({
               scrollTop: $row.offset().top - 120
            }, 300);
            return false;
         }
      });
      if (!found) {
         show_alert('Produk dengan Kode/Barcode "' + code + '" tidak ditemukan pada opname gudang ini!', 'warning');
      }
      $('#barcode_scanner_input').val('').focus();
   }

   $('#barcode_scanner_input').on('keypress', function(e){
      if (e.which == 13) {
         e.preventDefault();
         processBarcodeScan($(this).val());
      }
   });

   $('#btn_scan_add').click(function(){
      processBarcodeScan($('#barcode_scanner_input').val());
   });

   $(document).on('input', '.fisik-input', function(){
      var $row = $(this).closest('tr');
      var sistem = parseFloat($(this).data('sistem')) || 0;
      var fisik = parseFloat($(this).val()) || 0;
      var sel = fisik - sistem;
      var $span = $row.find('.selisih-val');
      var $alasan = $row.find('.alasan-input');
      $span.text((sel > 0 ? '+' : '') + sel.toFixed(0));
      $span.removeClass('text-danger text-success text-muted');
      if (sel < 0) {
         $span.addClass('text-danger');
         $alasan.prop('required', true).attr('placeholder', 'Wajib isi alasan selisih!').parent().addClass('has-warning');
      } else if (sel > 0) {
         $span.addClass('text-success');
         $alasan.prop('required', true).attr('placeholder', 'Wajib isi alasan selisih!').parent().addClass('has-warning');
      } else {
         $span.addClass('text-muted');
         $alasan.prop('required', false).attr('placeholder', 'Alasan selisih (opsional jika sesuai)').parent().removeClass('has-warning');
      }
   });

   $('#btn_save').click(function(){
      var btn = $(this);
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
      $.ajax({
         url: '<?= site_url("administrator/stock_opname/save_fisik/" . $opname->id); ?>',
         method: 'POST',
         data: $('#form_fisik').serialize(),
         dataType: 'json',
         success: function(res){
            if (res.success) {
               window.location.href = res.redirect;
            } else {
               show_alert(res.message, 'error');
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan & Review Opname');
            }
         },
         error: function(){
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan & Review Opname');
         }
      });
   });
});
</script>
