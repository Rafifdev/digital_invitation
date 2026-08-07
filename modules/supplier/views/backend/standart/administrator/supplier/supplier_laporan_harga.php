<section class="content-header">
   <h1>Riwayat Harga Beli Supplier <small>Laporan Harga Pembelian</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/supplier'); ?>"><i class="fa fa-truck"></i> Supplier</a></li>
      <li class="active">Laporan Harga</li>
   </ol>
</section>

<section class="content">
<div class="box box-primary">
   <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-line-chart"></i> Filter Riwayat Harga Beli</h3>
   </div>
   <div class="box-body">
      <form action="<?= current_url(); ?>" method="GET">
         <div class="row">
            <div class="col-md-4">
               <div class="form-group">
                  <label>Pilih Supplier</label>
                  <select class="form-control chosen" name="supplier_id">
                     <option value="">-- Semua Supplier --</option>
                     <?php foreach($suppliers as $s): ?>
                        <option value="<?= $s->id; ?>" <?= $this->input->get('supplier_id') == $s->id ? 'selected' : ''; ?>>
                           <?= _ent($s->kode_supplier . ' - ' . $s->nama_supplier); ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label>Pilih Produk</label>
                  <select class="form-control chosen" name="produk_id">
                     <option value="">-- Semua Produk --</option>
                     <?php foreach($produks as $p): ?>
                        <option value="<?= $p->id; ?>" <?= $this->input->get('produk_id') == $p->id ? 'selected' : ''; ?>>
                           <?= _ent($p->kode_produk . ' - ' . $p->nama_produk); ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>
            <div class="col-md-4" style="margin-top: 25px;">
               <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
               <a href="<?= current_url(); ?>" class="btn btn-default"><i class="fa fa-undo"></i> Reset</a>
            </div>
         </div>
      </form>

      <hr>

      <div class="table-responsive">
         <table class="table table-bordered table-striped">
            <thead>
               <tr class="bg-gray">
                  <th width="40" class="text-center">No</th>
                  <th>Tanggal PO</th>
                  <th>Nomor PO</th>
                  <th>Supplier</th>
                  <th>Kode &amp; Nama Produk</th>
                  <th class="text-right">Qty Pesan</th>
                  <th class="text-right">Harga Satuan</th>
                  <th class="text-right">Diskon (%)</th>
                  <th class="text-right">Subtotal</th>
               </tr>
            </thead>
            <tbody>
               <?php if (!empty($riwayat)): ?>
                  <?php $no = 1; foreach ($riwayat as $r): ?>
                     <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= date('d/m/Y', strtotime($r->tanggal_po)); ?></td>
                        <td><strong><?= _ent($r->nomor_po); ?></strong></td>
                        <td><?= _ent($r->nama_supplier); ?></td>
                        <td><code><?= _ent($r->kode_produk); ?></code> - <?= _ent($r->nama_produk); ?></td>
                        <td class="text-right"><?= number_format($r->qty_pesan, 0, ',', '.'); ?> <?= _ent($r->satuan); ?></td>
                        <td class="text-right">Rp <?= number_format($r->harga_satuan, 0, ',', '.'); ?></td>
                        <td class="text-right"><?= number_format($r->diskon_persen, 2); ?>%</td>
                        <td class="text-right"><strong>Rp <?= number_format($r->subtotal, 0, ',', '.'); ?></strong></td>
                     </tr>
                  <?php endforeach; ?>
               <?php else: ?>
                  <tr><td colspan="9" class="text-center text-muted">Belum ada riwayat harga beli.</td></tr>
               <?php endif; ?>
            </tbody>
         </table>
      </div>
   </div>
</div>
</section>
