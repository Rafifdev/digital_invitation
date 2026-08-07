<?php
/**
 * View: Daftar Stock Opname
 * File: stock_opname_list.php
 */
$status_color = [
    'draft'     => 'default',
    'review'    => 'info',
    'approved'  => 'success',
    'cancelled' => 'danger'
];
?>
<section class="content-header">
   <h1>
      Stock Opname
      <small>Daftar Opname Fisik</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Stock Opname</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-clipboard"></i> Data Stock Opname</h3>
               <div class="box-tools pull-right">
                  <?php if (is_allowed('stock_opname_add', false)): ?>
                     <a href="<?= site_url('administrator/stock_opname/add'); ?>" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Buat Opname Baru
                     </a>
                  <?php endif; ?>
               </div>
            </div>

            <div class="box-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th>No. Opname</th>
                           <th>Gudang</th>
                           <th>Tgl. & Waktu Opname</th>
                           <th>Jadwal</th>
                           <th class="text-right">Total Selisih (Rp)</th>
                           <th class="text-center">Status</th>
                           <th class="text-center" width="220">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (!empty($opnames)): ?>
                           <?php foreach ($opnames as $op): ?>
                              <tr>
                                 <td><strong><?= _ent($op->nomor_opname); ?></strong></td>
                                 <td><?= _ent($op->nama_gudang); ?></td>
                                 <td>
                                    <?= date('d/m/Y', strtotime($op->tanggal_opname)); ?><br>
                                    <small class="text-muted"><i class="fa fa-clock-o"></i> <?= date('H:i', strtotime($op->created_at)); ?></small>
                                 </td>
                                 <td><?= ucfirst($op->jadwal); ?></td>
                                 <td class="text-right <?= $op->total_selisih_nilai > 0 ? 'text-danger' : ''; ?>">
                                    Rp <?= number_format($op->total_selisih_nilai, 0, ',', '.'); ?>
                                 </td>
                                 <td class="text-center">
                                    <span class="label label-<?= isset($status_color[$op->status]) ? $status_color[$op->status] : 'default'; ?>">
                                       <?= strtoupper($op->status); ?>
                                    </span>
                                 </td>
                                 <td class="text-center">
                                    <?php if (is_allowed('stock_opname_view', false)): ?>
                                       <a href="<?= site_url('administrator/stock_opname/view/' . $op->id); ?>" class="btn btn-xs btn-default" style="margin: 2px 1px;" title="Detail"><i class="fa fa-eye"></i> Detail</a>
                                       <a href="<?= site_url('administrator/stock_opname/cetak/' . $op->id); ?>" target="_blank" class="btn btn-xs btn-info" style="margin: 2px 1px;" title="Cetak / PDF"><i class="fa fa-print"></i> Cetak</a>
                                    <?php endif; ?>

                                    <?php if ($op->status == 'draft' && is_allowed('stock_opname_update', false)): ?>
                                       <a href="<?= site_url('administrator/stock_opname/edit/' . $op->id); ?>" class="btn btn-xs btn-warning" style="margin: 2px 1px;" title="Input Hasil Fisik">
                                          <i class="fa fa-edit"></i> Input Fisik
                                       </a>
                                    <?php endif; ?>

                                    <?php if ($op->status == 'review' && is_allowed('stock_opname_approve', false)): ?>
                                       <a href="<?= site_url('administrator/stock_opname/approve/' . $op->id); ?>" class="btn btn-xs btn-success" style="margin: 2px 1px;" onclick="return confirm('Approve Stock Opname ini? Stok di database akan otomatis disesuaikan.')" title="Approve Opname">
                                          <i class="fa fa-check"></i> Approve
                                       </a>
                                    <?php endif; ?>

                                    <?php if (is_allowed('stock_opname_delete', false)): ?>
                                       <a href="javascript:void(0);" data-href="<?= site_url('administrator/stock_opname/delete/' . $op->id); ?>" class="btn btn-xs btn-danger remove-data" style="margin: 2px 1px;" title="Hapus">
                                          <i class="fa fa-trash"></i> Hapus
                                       </a>
                                    <?php endif; ?>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr>
                              <td colspan="7" class="text-center text-muted">Belum ada data stock opname.</td>
                           </tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <div class="row" style="margin-top: 15px;">
                  <div class="col-md-6">
                     <span class="text-muted">Total: <strong><?= $opname_counts; ?></strong> record Stock Opname</span>
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
         title: 'Hapus Stock Opname?',
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
