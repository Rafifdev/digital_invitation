<?php
/**
 * supplier_view.php
 * View: Detail Supplier
 */
?>
<section class="content-header">
   <h1>Detail Supplier <small><?= _ent($supplier->nama_supplier); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/supplier'); ?>"><i class="fa fa-truck"></i> Supplier</a></li>
      <li class="active">Detail</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-8">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-info-circle"></i> <?= _ent($supplier->nama_supplier); ?></h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('supplier_update', function() use ($supplier){ ?>
                  <a href="<?= site_url('administrator/supplier/edit/'.$supplier->id); ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                  <?php }); ?>
                  <a href="<?= base_url('administrator/supplier'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
               </div>
            </div>
            <div class="box-body">
               <table class="table table-striped">
                  <tr><th width="30%">Kode Supplier</th><td><span class="label label-default"><?= _ent($supplier->kode_supplier); ?></span></td></tr>
                  <tr><th>Nama Supplier</th><td><strong><?= _ent($supplier->nama_supplier); ?></strong></td></tr>
                  <tr><th>Kontak Person</th><td><?= _ent($supplier->kontak_person); ?></td></tr>
                  <tr><th>Telepon</th><td><?= _ent($supplier->telepon); ?></td></tr>
                  <tr><th>Email</th><td><?= _ent($supplier->email); ?></td></tr>
                  <tr><th>Alamat</th><td><?= nl2br(_ent($supplier->alamat)); ?></td></tr>
                  <tr><th>Kota</th><td><?= _ent($supplier->kota); ?></td></tr>
                  <tr><th>Negara</th><td><?= _ent($supplier->negara); ?></td></tr>
                  <tr><th>NPWP</th><td><?= _ent($supplier->npwp); ?></td></tr>
                  <tr><th>Status</th><td>
                     <span class="label <?= $supplier->status == 'aktif' ? 'label-success' : 'label-danger'; ?>">
                        <?= $supplier->status; ?>
                     </span>
                  </td></tr>
                  <tr><th>Catatan</th><td><?= nl2br(_ent($supplier->catatan)); ?></td></tr>
               </table>
            </div>
         </div>
      </div>
      <div class="col-md-4">
         <div class="box box-warning">
            <div class="box-header with-border"><h3 class="box-title">Info Keuangan</h3></div>
            <div class="box-body">
               <table class="table table-striped">
                  <tr><th>Mata Uang</th><td><span class="label label-info"><?= _ent($supplier->mata_uang); ?></span></td></tr>
                  <tr><th>Kurs Manual</th><td><?= number_format($supplier->kurs_manual, 4); ?></td></tr>
                  <tr><th>Nama Bank</th><td><?= _ent($supplier->bank_name); ?></td></tr>
                  <tr><th>No. Rekening</th><td><?= _ent($supplier->bank_account); ?></td></tr>
                  <tr><th>Pemilik Rekening</th><td><?= _ent($supplier->bank_holder); ?></td></tr>
               </table>
            </div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <div class="box box-info">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-history"></i> Riwayat Perubahan Harga Beli per Supplier</h3>
            </div>
            <div class="box-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                     <thead>
                        <tr class="bg-navy">
                           <th width="40" class="text-center">No</th>
                           <th>Tanggal PO</th>
                           <th>No. PO</th>
                           <th>Produk</th>
                           <th class="text-right">Harga Beli</th>
                           <th class="text-center">Mata Uang</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (!empty($riwayat_harga)): ?>
                           <?php $no = 1; foreach ($riwayat_harga as $r): ?>
                              <tr>
                                 <td class="text-center"><?= $no++; ?></td>
                                 <td><?= date('d/m/Y', strtotime($r->tanggal_order ?? $r->tanggal_po ?? date('Y-m-d'))); ?></td>
                                 <td><a href="<?= site_url('administrator/purchase_order/view/' . $r->po_id); ?>" target="_blank"><strong><?= _ent($r->nomor_po); ?></strong></a></td>
                                 <td><?= _ent(($r->kode_produk ? $r->kode_produk . ' - ' : '') . $r->nama_produk); ?></td>
                                 <td class="text-right">
                                    <strong>
                                       <?= ($r->mata_uang ?? 'IDR') === 'IDR' ? 'Rp ' . number_format($r->harga_satuan, 0, ',', '.') : ($r->mata_uang ?? 'IDR') . ' ' . number_format($r->harga_satuan, 2, '.', ','); ?>
                                    </strong>
                                 </td>
                                 <td class="text-center"><span class="label label-info"><?= _ent($r->mata_uang ?? 'IDR'); ?></span></td>
                              </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr><td colspan="6" class="text-center text-muted">Belum ada riwayat perubahan harga beli untuk supplier ini.</td></tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
