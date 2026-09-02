<?php
/**
 * View: Daftar Retur Penjualan
 * File: retur_penjualan_list.php
 */
$status_color = [
    'draft'     => 'default',
    'approved'  => 'success',
    'cancelled' => 'danger'
];
?>
<section class="content-header">
   <h1>
      Retur Penjualan
      <small>Daftar Retur Pelanggan</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Retur Penjualan</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-reply-all"></i> Data Retur Penjualan</h3>
               <div class="box-tools pull-right">
                  <?php if (is_allowed('retur_penjualan_add', false)): ?>
                     <a href="<?= site_url('administrator/retur_penjualan/add'); ?>" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Buat Retur Baru
                     </a>
                  <?php endif; ?>
               </div>
            </div>

            <div class="box-body">
               <form action="<?= base_url('administrator/retur_penjualan/index'); ?>" method="GET">
                  <div class="row" style="margin-bottom: 15px;">
                     <div class="col-md-4">
                        <div class="input-group">
                           <input type="text" class="form-control" name="q" placeholder="Cari nomor retur / pelanggan..." value="<?= $this->input->get('q'); ?>">
                           <span class="input-group-btn">
                              <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                              <a href="<?= base_url('administrator/retur_penjualan'); ?>" class="btn btn-default"><i class="fa fa-undo"></i></a>
                           </span>
                        </div>
                     </div>
                  </div>

                  <div class="table-responsive">
                     <table class="table table-bordered table-striped">
                        <thead>
                           <tr>
                              <th>No. Retur</th>
                              <th>Invoice</th>
                              <th>Pelanggan</th>
                              <th>Tgl. Retur</th>
                              <th class="text-right">Total</th>
                              <th>Tindak Lanjut</th>
                              <th class="text-center">Status</th>
                              <th class="text-center" width="220">Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php if (!empty($returs)): ?>
                              <?php foreach ($returs as $r): ?>
                                 <tr>
                                    <td><strong><?= _ent($r->nomor_retur); ?></strong></td>
                                    <td><?= _ent(isset($r->nomor_invoice) ? $r->nomor_invoice : '-'); ?></td>
                                    <td><?= _ent($r->nama_pelanggan); ?></td>
                                    <td><?= date('d/m/Y', strtotime($r->tanggal_retur)); ?></td>
                                    <td class="text-right">Rp <?= number_format($r->total, 0, ',', '.'); ?></td>
                                    <td><span class="label label-info"><?= _ent(ucwords(str_replace('_', ' ', $r->tindak_lanjut))); ?></span></td>
                                    <td class="text-center">
                                       <span class="label label-<?= isset($status_color[$r->status]) ? $status_color[$r->status] : 'default'; ?>">
                                          <?= strtoupper($r->status); ?>
                                       </span>
                                    </td>
                                    <td class="text-center">
                                       <?php if (is_allowed('retur_penjualan_view', false)): ?>
                                          <a href="<?= site_url('administrator/retur_penjualan/view/' . $r->id); ?>" class="btn btn-xs btn-default" style="margin: 2px 1px;" title="Detail"><i class="fa fa-eye"></i> Detail</a>
                                          <a href="<?= site_url('administrator/retur_penjualan/cetak/' . $r->id); ?>" target="_blank" class="btn btn-xs btn-info" style="margin: 2px 1px;" title="Cetak / PDF"><i class="fa fa-print"></i> Cetak</a>
                                       <?php endif; ?>

                                       <?php if ($r->status == 'draft' && is_allowed('retur_penjualan_update', false)): ?>
                                          <a href="<?= site_url('administrator/retur_penjualan/edit/' . $r->id); ?>" class="btn btn-xs btn-warning" style="margin: 2px 1px;" title="Edit"><i class="fa fa-edit"></i> Edit</a>
                                          <a href="<?= site_url('administrator/retur_penjualan/proses/' . $r->id); ?>" class="btn btn-xs btn-success" style="margin: 2px 1px;" onclick="return confirm('Proses retur ini? Stok barang akan dikembalikan ke gudang.')" title="Proses Retur">
                                             <i class="fa fa-cogs"></i> Proses
                                          </a>
                                       <?php endif; ?>

                                       <?php if (is_allowed('retur_penjualan_delete', false)): ?>
                                          <a href="javascript:void(0);" data-href="<?= site_url('administrator/retur_penjualan/delete/' . $r->id); ?>" class="btn btn-xs btn-danger remove-data" style="margin: 2px 1px;" title="Hapus">
                                             <i class="fa fa-trash"></i> Hapus
                                          </a>
                                       <?php endif; ?>
                                    </td>
                                 </tr>
                              <?php endforeach; ?>
                           <?php else: ?>
                              <tr>
                                 <td colspan="8" class="text-center text-muted">Belum ada data retur penjualan.</td>
                              </tr>
                           <?php endif; ?>
                        </tbody>
                     </table>
                  </div>
               </form>

               <div class="row" style="margin-top: 15px;">
                  <div class="col-md-6">
                     <span class="text-muted">Total: <strong><?= $retur_counts; ?></strong> record Retur</span>
                  </div>
                  <div class="col-md-6">
                     <div class="pull-right">
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>
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
         title: 'Hapus Retur Penjualan?',
         text: 'Data yang dihapus tidak dapat dikembalikan!',
         type: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#DD6B55',
         confirmButtonText: 'Ya, Hapus!',
         cancelButtonText: 'Batal',
         closeOnConfirm: true
      }, function(ok){
         if (ok) { window.location.href = url; }
      });
   });
});
</script>
