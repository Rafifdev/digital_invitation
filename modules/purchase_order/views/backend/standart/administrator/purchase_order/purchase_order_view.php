<?php /* purchase_order_view.php */ 
$status_color = ['draft'=>'default','submitted'=>'info','approved'=>'primary','rejected'=>'danger','received'=>'success','partial'=>'warning','cancelled'=>'danger'];
?>
<section class="content-header">
   <h1>Detail PO <small><?= _ent($po->nomor_po); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/purchase_order'); ?>"><i class="fa fa-shopping-cart"></i> Purchase Order</a></li>
      <li class="active">Detail</li>
   </ol>
</section>
<section class="content">
<div class="row">
   <div class="col-md-8">
      <div class="box box-primary">
         <div class="box-header with-border">
            <h3 class="box-title"><?= _ent($po->nomor_po); ?> &nbsp;<span class="label label-<?= $status_color[$po->status]??'default'; ?>"><?= strtoupper($po->status); ?></span></h3>
            <div class="box-tools pull-right"> <a href="<?= site_url('administrator/purchase_order/cetak/' . $po->id); ?>" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-print"></i> Cetak PDF</a> 
               <?php if($po->status=='submitted' && $this->aauth->is_admin()): ?>
                  <a href="<?= site_url('administrator/purchase_order/approve/'.$po->id); ?>" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Approve</a>
                  <a href="<?= site_url('administrator/purchase_order/reject/'.$po->id); ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Tolak</a>
               <?php endif; ?>
               <?php if(in_array($po->status,['approved','partial'])): ?>
                  <?php is_allowed('penerimaan_barang_add', function() use ($po, $draft_penerimaan){ ?>
                     <?php if(!empty($draft_penerimaan)): ?>
                        <a href="<?= site_url('administrator/penerimaan_barang/view/'.$draft_penerimaan->id); ?>" class="btn btn-warning btn-sm" title="Lanjutkan Draft GRN yang sudah ada"><i class="fa fa-edit"></i> Lanjutkan GRN Draft (<?= _ent($draft_penerimaan->nomor_penerimaan); ?>)</a>
                     <?php else: ?>
                        <a href="<?= site_url('administrator/penerimaan_barang/add?po_id='.$po->id); ?>" class="btn btn-warning btn-sm"><i class="fa fa-truck"></i> Terima Barang</a>
                     <?php endif; ?>
                  <?php }); ?>
               <?php endif; ?>
               <a href="<?= base_url('administrator/purchase_order'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
         </div>
         <div class="box-body">
             <?php 
                $po_thresh = (float) get_option('po_approval_threshold', 50000000);
                $po_tot = (float)$po->total * (float)($po->kurs ?: 1);
                if ($po->status == 'submitted' && $po_tot >= $po_thresh):
             ?>
                <div class="alert alert-warning" style="border-left: 5px solid #f39c12; background: #fffcf5; color: #8a6d3b; margin-bottom: 20px; border-radius: 4px;">
                   <h4 style="margin-top:0; font-weight:700;"><i class="icon fa fa-shield text-warning"></i> Perhatian: Approval Wewenang Direktur / Superadmin Required</h4>
                   <p style="margin-bottom:0;">Nilai transaksi PO ini sebesar <strong>Rp <?= number_format($po_tot, 0, ',', '.'); ?></strong> (melebihi batas threshold approval Rp <?= number_format($po_thresh, 0, ',', '.'); ?>). Pengajuan ini memerlukan konfirmasi approval khusus dari Direktur / Superadmin.</p>
                </div>
             <?php endif; ?>
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-condensed">
                     <tr><th>Nomor PO</th><td><?= _ent($po->nomor_po); ?></td></tr>
                     <tr><th>Supplier</th><td><strong><?= _ent($po->nama_supplier); ?></strong></td></tr>
                     <tr><th>Gudang</th><td><?= _ent($po->nama_gudang??'-'); ?></td></tr>
                     <tr><th>Tanggal PO</th><td><?= date('d/m/Y',strtotime($po->tanggal_po)); ?></td></tr>
                     <tr><th>Est. Terima</th><td><?= $po->tanggal_estimasi ? date('d/m/Y',strtotime($po->tanggal_estimasi)) : '-'; ?></td></tr>
                     <tr><th>Mata Uang</th><td><?= $po->mata_uang; ?> (<?= $po->kurs; ?>)</td></tr>
                     <tr><th>Dibuat Oleh</th><td><?= _ent($po->created_by); ?></td></tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <?php 
                     $curr = $po->mata_uang ?? 'IDR';
                     $rate = (float)($po->kurs ?? 1);
                     $fmt_curr = function($val) use ($curr) {
                        if ($curr === 'IDR') {
                           return 'Rp ' . number_format((float)$val, 0, ',', '.');
                        } else {
                           return $curr . ' ' . number_format((float)$val, 2, ',', '.');
                        }
                     };
                  ?>
                  <table class="table table-condensed">
                     <tr><th>Subtotal (<?= $curr; ?>)</th><td class="text-right"><?= $fmt_curr($po->subtotal); ?></td></tr>
                     <tr><th>Diskon (<?= $curr; ?>)</th><td class="text-right text-danger">- <?= $fmt_curr($po->diskon); ?></td></tr>
                     <tr><th>Pajak (<?= (float)$po->pajak_persen; ?>%)</th><td class="text-right"><?= $fmt_curr($po->pajak_nominal); ?></td></tr>
                     <tr class="active"><th><strong>TOTAL (<?= $curr; ?>)</strong></th><td class="text-right"><strong class="text-success"><?= $fmt_curr($po->total); ?></strong></td></tr>
                     <?php if($curr !== 'IDR'): ?>
                        <tr class="info" style="background:#e0f2fe;">
                           <th><strong>Estimasi Harga IDR:</strong></th>
                           <td class="text-right"><strong class="text-primary" style="font-size:14px;">Rp <?= number_format($po->total * $rate, 0, ',', '.'); ?></strong></td>
                        </tr>
                     <?php endif; ?>
                  </table>
                  <?php if($po->catatan): ?><p class="text-muted"><em><?= nl2br(_ent($po->catatan)); ?></em></p><?php endif; ?>
               </div>
            </div>
            <hr>
            <h4>Item Pesanan</h4>
            <div class="table-responsive">
               <table class="table table-striped table-sm">
                  <thead><tr><th>#</th><th>Produk</th><th class="text-center">Qty Pesan</th><th class="text-center">Qty Terima</th><th>Harga Satuan (<?= $curr; ?>)</th><th>Disc%</th><th class="text-right">Subtotal (<?= $curr; ?>)</th></tr></thead>
                  <tbody>
                     <?php foreach($items as $i => $item): ?>
                     <tr>
                        <td><?= $i+1; ?></td>
                        <td><strong><?= _ent($item->nama_produk); ?></strong><br><small class="text-muted"><?= _ent($item->kode_produk); ?></small></td>
                        <td class="text-center"><?= number_format((float)$item->qty_pesan, 0, ',', '.'); ?> <?= _ent($item->satuan); ?></td>
                        <td class="text-center <?= $item->qty_terima >= $item->qty_pesan ? 'text-success' : ''; ?>"><?= number_format((float)$item->qty_terima, 0, ',', '.'); ?></td>
                        <td><?= $fmt_curr($item->harga_satuan); ?></td>
                        <td><?= $item->diskon_persen; ?>%</td>
                        <td class="text-right"><?= $fmt_curr($item->subtotal); ?></td>
                     </tr>
                     <?php endforeach; ?>
                     <?php if(empty($items)): ?><tr><td colspan="7" class="text-center text-muted">Tidak ada item</td></tr><?php endif; ?>
                  </tbody>
               </table>
            </div>

            <!-- Linked GRN Documents History -->
            <hr style="margin-top:25px; margin-bottom:15px;">
            <h4 style="font-weight:bold; color:#0369a1;"><i class="fa fa-truck text-info"></i> Dokumen Penerimaan Barang (GRN) Terkoneksi</h4>
            <div class="table-responsive">
               <table class="table table-bordered table-striped" style="font-size:12px;">
                  <thead>
                     <tr style="background:#e0f2fe; color:#0369a1;">
                        <th>No. GRN</th>
                        <th>Tgl Terima</th>
                        <th>Gudang Tujuan</th>
                        <th>Dibuat Oleh</th>
                        <th class="text-center">Status GRN</th>
                        <th class="text-center" width="160">Aksi GRN</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php if(!empty($penerimaans)): ?>
                        <?php foreach($penerimaans as $pnr): ?>
                           <tr>
                              <td>
                                 <strong class="text-primary">
                                    <a href="<?= site_url('administrator/penerimaan_barang/view/' . $pnr->id); ?>"><?= _ent($pnr->nomor_penerimaan); ?></a>
                                 </strong>
                              </td>
                              <td><i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime($pnr->tanggal_terima)); ?></td>
                              <td><span class="label label-primary"><i class="fa fa-building"></i> <?= _ent($pnr->nama_gudang); ?></span></td>
                              <td><small><?= _ent($pnr->created_by); ?></small></td>
                              <td class="text-center">
                                 <?php if($pnr->status == 'confirmed'): ?>
                                    <span class="label label-success"><i class="fa fa-check-circle"></i> CONFIRMED</span>
                                 <?php else: ?>
                                    <span class="label label-warning"><i class="fa fa-clock-o"></i> DRAFT (Belum Konfirmasi)</span>
                                 <?php endif; ?>
                              </td>
                              <td class="text-center">
                                 <a href="<?= site_url('administrator/penerimaan_barang/view/' . $pnr->id); ?>" class="btn btn-xs btn-default"><i class="fa fa-eye"></i> Detail</a>
                                 <?php if($pnr->status == 'draft'): ?>
                                    <a href="<?= site_url('administrator/penerimaan_barang/edit/' . $pnr->id); ?>" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i> Edit & Konfirmasi</a>
                                 <?php endif; ?>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="6" class="text-center text-muted" style="padding:15px;">Belum ada dokumen Penerimaan Barang (GRN) yang terhubung dengan PO ini.</td>
                        </tr>
                     <?php endif; ?>
                  </tbody>
               </table>
            </div>

         </div>
      </div>
   </div>
   <div class="col-md-4">
      <div class="box box-info">
         <div class="box-header with-border"><h3 class="box-title">Informasi Supplier</h3></div>
         <div class="box-body">
            <p><i class="fa fa-building-o"></i> <?= _ent($po->nama_supplier); ?></p>
            <p><i class="fa fa-barcode"></i> <?= _ent($po->kode_supplier); ?></p>
         </div>
      </div>
   </div>
</div>
</section>
