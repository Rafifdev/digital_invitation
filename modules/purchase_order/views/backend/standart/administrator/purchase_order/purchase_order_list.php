<?php
/**
 * View: Daftar Purchase Order (Modern & Sleek UI)
 * File: purchase_order_list.php
 */
$status_color = [
    'draft'     => 'label-default',
    'submitted' => 'label-info',
    'approved'  => 'label-primary',
    'rejected'  => 'label-danger',
    'received'  => 'label-success',
    'partial'   => 'label-warning',
    'cancelled' => 'label-danger'
];
?>
<section class="content-header">
   <h1>
      Purchase Order (PO)
      <small>Daftar Pesanan Pembelian Ke Supplier</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/purchase_order'); ?>">Pembelian</a></li>
      <li class="active">Purchase Order</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary" style="border-radius:6px; border-top: 3px solid #3c8dbc;">
            <div class="box-header with-border" style="padding: 15px 20px;">
               <h3 class="box-title" style="font-weight:600;"><i class="fa fa-shopping-cart text-primary"></i> Daftar Seluruh Pesanan Pembelian <span class="label label-warning" style="font-size:11px;"><?= $po_counts; ?> Data</span></h3>
               <div class="box-tools pull-right">
                  <?php if (is_allowed('purchase_order_add', false)): ?>
                     <a href="<?= site_url('administrator/purchase_order/add'); ?>" class="btn btn-success btn-sm btn-flat" style="border-radius:4px; font-weight:600;">
                        <i class="fa fa-plus-circle"></i> Buat Purchase Order
                     </a>
                  <?php endif; ?>
               </div>
            </div>

            <div class="box-body" style="padding: 20px;">
               <form action="<?= base_url('administrator/purchase_order/index'); ?>" method="GET">
                  <div class="row" style="margin-bottom: 15px;">
                     <div class="col-md-4">
                        <div class="input-group">
                           <input type="text" class="form-control" name="q" placeholder="Cari nomor PO / supplier..." value="<?= _ent($this->input->get('q')); ?>">
                           <span class="input-group-btn">
                              <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search"></i></button>
                              <a href="<?= base_url('administrator/purchase_order'); ?>" class="btn btn-default btn-flat"><i class="fa fa-undo"></i></a>
                           </span>
                        </div>
                     </div>
                  </div>

               <div class="table-responsive" style="overflow-x: auto; min-height: 250px;">
                  <table class="table table-hover table-striped table-bordered" style="vertical-align: middle; margin-bottom: 0;">
                     <thead>
                        <tr style="background:#f8fafc; font-size:12px; color:#555;">
                           <th width="150">Nomor PO</th>
                           <th>Tanggal Order</th>
                           <th>Supplier</th>
                           <th class="text-center">Mata Uang</th>
                           <th class="text-right">Grand Total</th>
                           <th class="text-center">Status</th>
                           <th class="text-center" width="220">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (!empty($pos)): ?>
                           <?php foreach ($pos as $po): ?>
                              <tr>
                                 <td><strong class="text-primary"><a href="<?= site_url('administrator/purchase_order/view/' . $po->id); ?>"><?= _ent($po->nomor_po); ?></a></strong></td>
                                 <td><i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime($po->tanggal_po)); ?></td>
                                 <td>
                                    <strong><?= _ent($po->nama_supplier); ?></strong>
                                 </td>
                                 <td class="text-center"><span class="badge bg-purple"><?= _ent($po->mata_uang ?? 'IDR'); ?></span></td>
                                 <td class="text-right">
                                    <?php if(($po->mata_uang ?? 'IDR') !== 'IDR'): ?>
                                       <strong class="text-success" style="font-size:13px;"><?= $po->mata_uang; ?> <?= number_format($po->total, 2, ',', '.'); ?></strong><br>
                                       <small class="text-muted" style="font-size:10px;">(Rp <?= number_format($po->total * ($po->kurs ?? 1), 0, ',', '.'); ?>)</small>
                                    <?php else: ?>
                                       <strong class="text-success" style="font-size:13px;">Rp <?= number_format($po->total, 0, ',', '.'); ?></strong>
                                    <?php endif; ?>
                                 </td>
                                 <td class="text-center">
                                    <span class="label <?= isset($status_color[$po->status]) ? $status_color[$po->status] : 'label-default'; ?>" style="font-size:11px; padding:4px 8px;">
                                       <?= strtoupper($po->status); ?>
                                    </span>
                                 </td>
                                 <td class="text-center">
                                    <!-- Inline Buttons -->
                                    <a href="<?= site_url('administrator/purchase_order/view/' . $po->id); ?>" class="btn btn-default btn-xs" title="Detail"><i class="fa fa-eye"></i></a>
                                    <a href="<?= site_url('administrator/purchase_order/cetak/' . $po->id); ?>" target="_blank" class="btn btn-info btn-xs" title="Cetak PDF"><i class="fa fa-print"></i></a>

                                    <?php if ($po->status == 'draft' && is_allowed('purchase_order_update', false)): ?>
                                    <a href="<?= site_url('administrator/purchase_order/edit/'.$po->id); ?>" class="btn btn-warning btn-xs" title="Edit PO"><i class="fa fa-edit"></i></a>
                                    <a href="<?= site_url('administrator/purchase_order/submit/' . $po->id); ?>" class="btn btn-primary btn-xs" title="Submit Approval"><i class="fa fa-paper-plane"></i></a>
                                    <?php endif; ?>

                                    <?php if ($po->status == 'submitted' && is_allowed('purchase_order_approve', false)): ?>
                                    <a href="<?= site_url('administrator/purchase_order/approve/' . $po->id); ?>" class="btn btn-success btn-xs" title="Setujui PO (Approve)"><i class="fa fa-check"></i> Approve</a>
                                    <?php endif; ?>

                                    <?php if (in_array($po->status, ['approved', 'partial']) && is_allowed('penerimaan_barang_add', false)): ?>
                                       <?php if (isset($draft_grn_map[$po->id])): ?>
                                       <a href="<?= site_url('administrator/penerimaan_barang/view/' . $draft_grn_map[$po->id]->id); ?>" class="btn btn-warning btn-xs" title="Lanjutkan GRN Draft (<?= _ent($draft_grn_map[$po->id]->nomor_penerimaan); ?>)"><i class="fa fa-edit"></i> GRN</a>
                                       <?php else: ?>
                                       <a href="<?= site_url('administrator/penerimaan_barang/add?po_id=' . $po->id); ?>" class="btn bg-purple btn-xs" title="Terima Barang (GRN Baru)"><i class="fa fa-arrow-down"></i> GRN</a>
                                       <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (is_allowed('purchase_order_delete', false) && $po->status == 'draft'): ?>
                                    <a href="javascript:void(0);" data-href="<?= site_url('administrator/purchase_order/delete/' . $po->id); ?>" class="btn btn-danger btn-xs remove-data" title="Hapus"><i class="fa fa-trash"></i></a>
                                    <?php endif; ?>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr>
                              <td colspan="7" class="text-center text-muted" style="padding:40px;">
                                 <i class="fa fa-inbox fa-3x" style="color:#e0e0e0; margin-bottom:10px;"></i><br>
                                 <span style="font-size:16px;">Belum ada data Purchase Order.</span><br>
                                 <small>Silakan buat PO baru ke supplier melalui tombol di kanan atas.</small>
                              </td>
                           </tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>

                  <div class="row" style="margin-top: 15px;">
                     <div class="col-md-6">
                        <span class="text-muted">Total: <strong><?= isset($po_counts) ? $po_counts : (isset($po_count) ? $po_count : count($pos)); ?></strong> data Purchase Order</span>
                     </div>
                     <div class="col-md-6">
                        <div class="pull-right">
                           <?= $pagination; ?>
                        </div>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
$(document).ready(function(){
   $('.remove-data').click(function(){
      var url = $(this).attr('data-href');
      swal({
         title: "Hapus Purchase Order?",
         text: "Data yang dihapus tidak dapat dikembalikan!",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "Ya, Hapus!",
         cancelButtonText: "Batal",
         closeOnConfirm: true
      }, function(ok){
         if (ok) {
            window.location.href = url;
         }
      });
   });
});
</script>
