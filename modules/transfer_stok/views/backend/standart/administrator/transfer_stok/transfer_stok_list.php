<?php
/**
 * View: Daftar Transfer Stok
 * File: transfer_stok_list.php
 */
$status_color = [
    'draft'     => 'default',
    'submitted' => 'info',
    'approved'  => 'primary',
    'shipped'   => 'warning',
    'received'  => 'success',
    'cancelled' => 'danger'
];
?>
<section class="content-header">
   <h1>
      Transfer Stok
      <small>Mutasi Antar Gudang</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Transfer Stok</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-exchange"></i> Data Transfer Stok Antar Gudang</h3>
               <div class="box-tools pull-right">
                  <?php if (is_allowed('transfer_stok_add', false)): ?>
                     <a href="<?= site_url('administrator/transfer_stok/add'); ?>" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Buat Transfer Baru
                     </a>
                  <?php endif; ?>
               </div>
            </div>

            <div class="box-body">
               <form action="<?= base_url('administrator/transfer_stok/index'); ?>" method="GET">
                  <div class="row" style="margin-bottom: 15px;">
                     <div class="col-md-4">
                        <div class="input-group">
                           <input type="text" class="form-control" name="q" placeholder="Cari nomor transfer..." value="<?= $this->input->get('q'); ?>">
                           <span class="input-group-btn">
                              <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                              <a href="<?= base_url('administrator/transfer_stok'); ?>" class="btn btn-default"><i class="fa fa-undo"></i></a>
                           </span>
                        </div>
                     </div>
                  </div>

                  <div class="table-responsive">
                     <table class="table table-bordered table-striped">
                        <thead>
                           <tr>
                              <th>No. Transfer</th>
                              <th>Gudang Asal</th>
                              <th class="text-center" width="40">→</th>
                              <th>Gudang Tujuan</th>
                              <th>Tgl. Transfer</th>
                              <th class="text-center">Status</th>
                              <th class="text-center" width="220">Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php if (!empty($transfers)): ?>
                              <?php foreach ($transfers as $t): ?>
                                 <tr>
                                    <td><strong><?= _ent($t->nomor_transfer); ?></strong></td>
                                    <td><?= _ent($t->nama_gudang_asal); ?></td>
                                    <td class="text-center"><i class="fa fa-arrow-right text-muted"></i></td>
                                    <td><?= _ent($t->nama_gudang_tujuan); ?></td>
                                    <td><?= date('d/m/Y', strtotime($t->tanggal_transfer)); ?></td>
                                    <td class="text-center">
                                       <span class="label label-<?= isset($status_color[$t->status]) ? $status_color[$t->status] : 'default'; ?>">
                                          <?= strtoupper($t->status); ?>
                                       </span>
                                    </td>
                                    <td class="text-center">
                                       <?php if (is_allowed('transfer_stok_view', false)): ?>
                                          <a href="<?= site_url('administrator/transfer_stok/view/' . $t->id); ?>" class="btn btn-xs btn-default" style="margin: 2px 1px;" title="Detail"><i class="fa fa-eye"></i> Detail</a>
                                          <a href="<?= site_url('administrator/transfer_stok/cetak/' . $t->id); ?>" target="_blank" class="btn btn-xs btn-info" style="margin: 2px 1px;" title="Cetak / PDF"><i class="fa fa-print"></i> Cetak</a>
                                       <?php endif; ?>

                                       <?php if ($t->status == 'draft' && is_allowed('transfer_stok_update', false)): ?>
                                          <a href="<?= site_url('administrator/transfer_stok/edit/' . $t->id); ?>" class="btn btn-xs btn-warning" style="margin: 2px 1px;" title="Edit"><i class="fa fa-edit"></i> Edit</a>
                                          <a href="<?= site_url('administrator/transfer_stok/submit/' . $t->id); ?>" class="btn btn-xs btn-primary" style="margin: 2px 1px;" title="Submit Approval">
                                             <i class="fa fa-paper-plane"></i> Submit
                                          </a>
                                       <?php endif; ?>

                                       <?php if ($t->status == 'submitted' && is_allowed('transfer_stok_approve', false)): ?>
                                          <a href="<?= site_url('administrator/transfer_stok/approve/' . $t->id); ?>" class="btn btn-xs btn-success" style="margin: 2px 1px;" title="Approve Transfer">
                                             <i class="fa fa-check"></i> Approve
                                          </a>
                                       <?php endif; ?>

                                       <?php if ($t->status == 'approved' && is_allowed('transfer_stok_update', false)): ?>
                                          <a href="<?= site_url('administrator/transfer_stok/kirim/' . $t->id); ?>" class="btn btn-xs btn-warning" style="margin: 2px 1px;" title="Kirim Barang">
                                             <i class="fa fa-truck"></i> Kirim
                                          </a>
                                       <?php endif; ?>

                                       <?php if ($t->status == 'shipped' && is_allowed('transfer_stok_update', false)): ?>
                                          <a href="<?= site_url('administrator/transfer_stok/terima/' . $t->id); ?>" class="btn btn-xs btn-success" style="margin: 2px 1px;" onclick="return confirm('Konfirmasi penerimaan barang? Stok gudang tujuan akan bertambah.')" title="Konfirmasi Terima">
                                             <i class="fa fa-check-circle"></i> Terima
                                          </a>
                                       <?php endif; ?>

                                       <?php if (is_allowed('transfer_stok_delete', false)): ?>
                                          <a href="javascript:void(0);" data-href="<?= site_url('administrator/transfer_stok/delete/' . $t->id); ?>" class="btn btn-xs btn-danger remove-data" style="margin: 2px 1px;" title="Hapus">
                                             <i class="fa fa-trash"></i> Hapus
                                          </a>
                                       <?php endif; ?>
                                    </td>
                                 </tr>
                              <?php endforeach; ?>
                           <?php else: ?>
                              <tr>
                                 <td colspan="7" class="text-center text-muted">Belum ada data transfer stok.</td>
                              </tr>
                           <?php endif; ?>
                        </tbody>
                     </table>
                  </div>
               </form>

               <div class="row" style="margin-top: 15px;">
                  <div class="col-md-6">
                     <span class="text-muted">Total: <strong><?= $transfer_counts; ?></strong> record Transfer</span>
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
         title: 'Hapus Transfer Stok?',
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
