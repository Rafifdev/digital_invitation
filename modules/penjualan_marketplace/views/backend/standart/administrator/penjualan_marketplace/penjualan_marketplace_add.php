<?php /* penjualan_marketplace_add.php */ ?>
<section class="content-header">
   <h1>
      Form Tambah Penjualan Shopee / Marketplace Baru
      <small>Input Pesanan Online Shop Manual</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/penjualan_marketplace'); ?>"><i class="fa fa-shopping-bag"></i> Penjualan Online Shop</a></li>
      <li class="active">Tambah Pesanan Baru</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-success" style="border-radius:8px; border-top: 4px solid #ee4d2d; box-shadow:0 2px 10px rgba(0,0,0,0.06);">
            <div class="box-header with-border" style="padding: 16px 20px; background:#fff;">
               <h3 class="box-title" style="font-weight:700; color:#1e293b;"><i class="fa fa-plus-circle" style="color:#ee4d2d;"></i> Tambah Transaksi Penjualan Shopee / Online Shop</h3>
               <div class="box-tools pull-right">
                  <a href="<?= site_url('administrator/penjualan_marketplace'); ?>" class="btn btn-default btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-arrow-left"></i> Batal / Kembali</a>
               </div>
            </div>

            <form action="<?= site_url('administrator/penjualan_marketplace/add_save'); ?>" method="POST" id="form_penjualan_marketplace" class="form-horizontal">
               <div class="box-body" style="padding: 24px; background:#fafafa;">

                  <!-- Section 1: Data Transaksi Marketplace -->
                  <h4 style="font-weight:700; color:#ee4d2d; border-bottom:2px solid #ee4d2d; padding-bottom:8px; margin-bottom:20px;">
                     <i class="fa fa-shopping-bag"></i> 1. Informasi Pesanan &amp; Saluran Penjualan
                  </h4>

                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">Saluran Penjualan <span class="text-danger">*</span></label>
                           <div class="col-sm-8">
                              <select name="source" class="form-control" required style="border-radius:4px; font-weight:600;">
                                 <option value="shopee" selected>Shopee</option>
                                 <option value="tokopedia">Tokopedia</option>
                                 <option value="tiktok">TikTok Shop</option>
                                 <option value="lazada">Lazada</option>
                              </select>
                           </div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">No. Pesanan Marketplace <span class="text-danger">*</span></label>
                           <div class="col-sm-8">
                              <input type="text" name="no_pesanan" class="form-control" placeholder="Contoh: 260819A4CA88 / INV/2026/MP/001" required style="border-radius:4px; font-family:monospace;">
                           </div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">Status Pesanan <span class="text-danger">*</span></label>
                           <div class="col-sm-8">
                              <select name="status" class="form-control" required style="border-radius:4px;">
                                 <option value="packing" selected>Sedang Dikemas (Packing)</option>
                                 <option value="draft">Draft / Baru Masuk</option>
                                 <option value="shipped">Dalam Pengiriman (Shipped)</option>
                                 <option value="delivered">Pesanan Selesai (Delivered)</option>
                              </select>
                           </div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">Gudang Pengirim <span class="text-danger">*</span></label>
                           <div class="col-sm-8">
                              <select name="gudang_id" class="form-control" required style="border-radius:4px;">
                                 <?php foreach($gudangs as $g): ?>
                                    <option value="<?= $g->id; ?>"><?= _ent($g->nama_gudang); ?></option>
                                 <?php endforeach; ?>
                              </select>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">Kurir / Ekspedisi</label>
                           <div class="col-sm-8">
                              <select name="opsi_pengiriman" class="form-control" style="border-radius:4px;">
                                 <option value="SPX Express Standard">SPX Express Standard</option>
                                 <option value="J&T Express">J&T Express</option>
                                 <option value="SiCepat REG">SiCepat REG</option>
                                 <option value="Anteraja Reguler">Anteraja Reguler</option>
                                 <option value="Pos Reguler (Cashless)">Pos Reguler (Cashless)</option>
                              </select>
                           </div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">No. Resi (AWB)</label>
                           <div class="col-sm-8">
                              <input type="text" name="nomor_resi" class="form-control" placeholder="Contoh: SPX892301923" style="border-radius:4px; font-family:monospace;">
                           </div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">Waktu Pembayaran</label>
                           <div class="col-sm-8">
                              <input type="datetime-local" name="waktu_pembayaran" class="form-control" value="<?= date('Y-m-d\TH:i'); ?>" style="border-radius:4px;">
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Section 2: Data Pembeli & Penerima -->
                  <h4 style="font-weight:700; color:#ee4d2d; border-bottom:2px solid #ee4d2d; padding-bottom:8px; margin-top:20px; margin-bottom:20px;">
                     <i class="fa fa-user"></i> 2. Informasi Pembeli &amp; Alamat Pengiriman
                  </h4>

                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">Username Pembeli <span class="text-danger">*</span></label>
                           <div class="col-sm-8">
                              <input type="text" name="username_pembeli" class="form-control" placeholder="Contoh: budi_santoso88" required style="border-radius:4px;">
                           </div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">Nama Penerima <span class="text-danger">*</span></label>
                           <div class="col-sm-8">
                              <input type="text" name="nama_penerima" class="form-control" placeholder="Contoh: Budi Santoso" required style="border-radius:4px;">
                           </div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">No. Telepon / WA <span class="text-danger">*</span></label>
                           <div class="col-sm-8">
                              <input type="text" name="no_telepon" class="form-control" placeholder="Contoh: 081234567890" required style="border-radius:4px;">
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">Alamat Pengiriman</label>
                           <div class="col-sm-8">
                              <textarea name="alamat_pengiriman" class="form-control" rows="2" placeholder="Jl. Merdeka No. 12, RT 02/05" style="border-radius:4px;"></textarea>
                           </div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">Kota / Kabupaten</label>
                           <div class="col-sm-8">
                              <input type="text" name="kota_kabupaten" class="form-control" placeholder="Contoh: Surabaya" style="border-radius:4px;">
                           </div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 control-label text-left">Provinsi</label>
                           <div class="col-sm-8">
                              <input type="text" name="provinsi" class="form-control" placeholder="Contoh: Jawa Timur" style="border-radius:4px;">
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Section 3: Product Items -->
                  <h4 style="font-weight:700; color:#ee4d2d; border-bottom:2px solid #ee4d2d; padding-bottom:8px; margin-top:20px; margin-bottom:20px;">
                     <i class="fa fa-cubes"></i> 3. Item Produk Penjualan Shopee
                  </h4>

                  <div class="table-responsive">
                     <table class="table table-bordered table-striped" id="table_items" style="background:#fff;">
                        <thead>
                           <tr style="background:#f1f5f9; color:#475569;">
                              <th style="width:40%;">Pilih Produk</th>
                              <th style="width:15%; text-align:center;">Qty</th>
                              <th style="width:20%; text-align:right;">Harga Satuan (Rp)</th>
                              <th style="width:20%; text-align:right;">Subtotal (Rp)</th>
                              <th style="width:5%; text-align:center;">Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr class="item-row">
                              <td>
                                 <select name="produk_id[]" class="form-control select-produk" required style="border-radius:4px;">
                                    <option value="">-- Pilih Produk --</option>
                                    <?php foreach($produks as $p): ?>
                                       <option value="<?= $p->id; ?>" data-harga="<?= $p->harga_jual; ?>" data-nama="<?= _ent($p->nama_produk); ?>"><?= _ent($p->nama_produk); ?> (<?= _ent($p->kode_produk); ?>)</option>
                                    <?php endforeach; ?>
                                 </select>
                              </td>
                              <td>
                                 <input type="number" name="qty[]" class="form-control input-qty text-center" value="1" min="1" required style="border-radius:4px;">
                              </td>
                              <td>
                                 <input type="number" name="harga_satuan[]" class="form-control input-harga text-right" value="0" min="0" required style="border-radius:4px;">
                              </td>
                              <td>
                                 <input type="number" name="subtotal_item[]" class="form-control input-subtotal text-right" value="0" readonly style="border-radius:4px; background:#f8fafc; font-weight:700;">
                              </td>
                              <td class="text-center">
                                 <button type="button" class="btn btn-danger btn-xs btn-remove-row"><i class="fa fa-trash"></i></button>
                              </td>
                           </tr>
                        </tbody>
                        <tfoot>
                           <tr>
                              <td colspan="3" class="text-right"><strong>TOTAL BAYAR PESANAN:</strong></td>
                              <td>
                                 <input type="number" name="total" id="grand_total" class="form-control text-right" value="0" readonly style="font-size:16px; font-weight:700; color:#ee4d2d; background:#fff5f2;">
                              </td>
                              <td>
                                 <button type="button" class="btn btn-success btn-xs" id="btn_add_row"><i class="fa fa-plus"></i> Tambah Item</button>
                              </td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>

                  <div class="form-group" style="margin-top:20px;">
                     <label class="col-sm-2 control-label text-left">Catatan Pesanan</label>
                     <div class="col-sm-10">
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan tambahan dari pembeli Shopee..." style="border-radius:4px;"></textarea>
                     </div>
                  </div>

               </div>

               <div class="box-footer" style="padding: 16px 24px; background:#fff; border-top:1px solid #e2e8f0;">
                  <div class="pull-right">
                     <a href="<?= site_url('administrator/penjualan_marketplace'); ?>" class="btn btn-default btn-flat" style="border-radius:4px; margin-right:8px;">Batal</a>
                     <button type="submit" class="btn btn-success btn-flat" style="border-radius:4px; font-weight:700; background-color:#ee4d2d; border-color:#ee4d2d;"><i class="fa fa-save"></i> Simpan Pesanan Shopee</button>
                  </div>
               </div>
            </form>

         </div>
      </div>
   </div>
</section>

<script>
$(document).ready(function(){
   function calculateTotal() {
      var grandTotal = 0;
      $('.item-row').each(function(){
         var qty = parseFloat($(this).find('.input-qty').val()) || 0;
         var harga = parseFloat($(this).find('.input-harga').val()) || 0;
         var subtotal = qty * harga;
         $(this).find('.input-subtotal').val(subtotal);
         grandTotal += subtotal;
      });
      $('#grand_total').val(grandTotal);
   }

   $(document).on('change', '.select-produk', function(){
      var harga = $(this).find(':selected').data('harga') || 0;
      $(this).closest('tr').find('.input-harga').val(harga);
      calculateTotal();
   });

   $(document).on('input', '.input-qty, .input-harga', function(){
      calculateTotal();
   });

   $('#btn_add_row').click(function(){
      var firstRow = $('.item-row:first').clone();
      firstRow.find('input').val('');
      firstRow.find('.input-qty').val(1);
      firstRow.find('.input-harga').val(0);
      firstRow.find('.input-subtotal').val(0);
      firstRow.find('select').val('');
      $('#table_items tbody').append(firstRow);
   });

   $(document).on('click', '.btn-remove-row', function(){
      if ($('.item-row').length > 1) {
         $(this).closest('tr').remove();
         calculateTotal();
      } else {
         alert('Minimal harus ada 1 item produk!');
      }
   });
});
</script>
