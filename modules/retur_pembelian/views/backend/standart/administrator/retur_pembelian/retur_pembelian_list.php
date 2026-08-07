<?php
/**
 * View: Daftar Retur Pembelian
 * File: retur_pembelian_list.php
 */
$status_color = [
    'draft'     => 'default',
    'confirmed' => 'success',
    'cancelled' => 'danger'
];
?>
<section class="content-header">
   <h1>
      Retur Pembelian
      <small>Return to Vendor</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/purchase_order'); ?>">Pembelian</a></li>
      <li class="active">Retur Pembelian</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-reply"></i> Daftar Pengembalian Barang ke Supplier</h3>
               <div class="box-tools pull-right">
                  <?php if (is_allowed('retur_pembelian_add', false)): ?>
                     <a href="<?= site_url('administrator/retur_pembelian/add'); ?>" class="btn btn-danger btn-sm btn-flat">
                        <i class="fa fa-plus-circle"></i> Buat Retur Baru
                     </a>
                  <?php endif; ?>
               </div>
            </div>

            <div class="box-body">
               <form action="<?= base_url('administrator/retur_pembelian/index'); ?>" method="GET">
                  <div class="row" style="margin-bottom: 15px;">
                     <div class="col-md-4">
                        <div class="input-group">
                           <input type="text" class="form-control" name="q" placeholder="Cari nomor retur / supplier..." value="<?= $this->input->get('q'); ?>">
                           <span class="input-group-btn">
                              <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                              <a href="<?= base_url('administrator/retur_pembelian'); ?>" class="btn btn-default"><i class="fa fa-undo"></i></a>
                           </span>
                        </div>
                     </div>
                  </div>

                  <div class="table-responsive">
                     <table class="table table-bordered table-striped dataTable">
                        <thead>
                           <tr>
                              <th>No. Retur</th>
                              <th>Tanggal Retur</th>
                              <th>No. Referensi (GRN / PO)</th>
                              <th>Supplier</th>
                              <th>Gudang Asal</th>
                              <th>Alasan Retur</th>
                              <th class="text-right">Total Nilai</th>
                              <th class="text-center">Status</th>
                              <th class="text-center" width="200">Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php if (!empty($returs)): ?>
                              <?php foreach ($returs as $r): ?>
                                 <tr>
                                    <td><strong class="text-danger"><a href="<?= site_url('administrator/retur_pembelian/view/' . $r->id); ?>"><?= _ent($r->nomor_retur); ?></a></strong></td>
                                    <td><i class="fa fa-calendar"></i> <?= date('d/m/Y', strtotime($r->tanggal_retur)); ?></td>
                                    <td><span class="label label-info"><i class="fa fa-file-text-o"></i> <?= _ent($r->nomor_penerimaan ?? $r->nomor_po ?? '-'); ?></span></td>
                                    <td>
                                       <strong><?= _ent($r->nama_supplier); ?></strong><br>
                                       <small class="text-muted"><i class="fa fa-tag"></i> <?= _ent(isset($r->kode_supplier) && $r->kode_supplier ? $r->kode_supplier : 'SUP-'.$r->supplier_id); ?></small>
                                    </td>
                                    <td><span class="label label-primary"><i class="fa fa-bank"></i> <?= _ent($r->nama_gudang); ?></span></td>
                                    <td><?= _ent($r->alasan ?? '-'); ?></td>
                                    <td class="text-right">
                                       <strong class="text-danger">
                                          <?= _ent($r->mata_uang ?? 'Rp'); ?> 
                                          <?= fmod((float)$r->total, 1) !== 0.0 ? number_format($r->total, 2, ',', '.') : number_format($r->total, 0, ',', '.'); ?>
                                       </strong>
                                       <?php if(($r->mata_uang ?? 'IDR') !== 'IDR' && !empty($r->kurs)): ?>
                                          <br><small class="text-muted">(Rp <?= number_format($r->total * $r->kurs, 0, ',', '.'); ?>)</small>
                                       <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                       <?php if($r->status == 'confirmed'): ?>
                                          <span class="label label-info">Selesai (Utang Terpotong)</span>
                                       <?php else: ?>
                                          <span class="label label-default">DRAFT</span>
                                       <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                       <?php if (is_allowed('retur_pembelian_view', false)): ?>
                                          <a href="<?= site_url('administrator/retur_pembelian/view/' . $r->id); ?>" class="btn btn-xs btn-default" style="margin: 2px 1px;" title="Detail"><i class="fa fa-eye"></i> Detail</a>
                                          <a href="<?= site_url('administrator/retur_pembelian/cetak/' . $r->id); ?>" target="_blank" class="btn btn-xs btn-info" style="margin: 2px 1px;" title="Cetak / PDF"><i class="fa fa-print"></i> Cetak</a>
                                       <?php endif; ?>

                                       <?php if ($r->status == 'draft' && is_allowed('retur_pembelian_update', false)): ?>
                                          <a href="<?= site_url('administrator/retur_pembelian/edit/' . $r->id); ?>" class="btn btn-xs btn-warning" style="margin: 2px 1px;" title="Edit"><i class="fa fa-edit"></i> Edit</a>
                                          <a href="<?= site_url('administrator/retur_pembelian/konfirmasi/' . $r->id); ?>" class="btn btn-xs btn-success" style="margin: 2px 1px;" onclick="return confirm('Konfirmasi retur ini? Stok barang akan dikurangi secara otomatis.')" title="Konfirmasi Retur">
                                             <i class="fa fa-check"></i> Konfirmasi
                                          </a>
                                       <?php endif; ?>

                                       <?php if (is_allowed('retur_pembelian_delete', false)): ?>
                                          <a href="javascript:void(0);" data-href="<?= site_url('administrator/retur_pembelian/delete/' . $r->id); ?>" class="btn btn-xs btn-danger remove-data" style="margin: 2px 1px;" title="Hapus">
                                             <i class="fa fa-trash"></i> Hapus
                                          </a>
                                       <?php endif; ?>
                                    </td>
                                 </tr>
                              <?php endforeach; ?>
                           <?php else: ?>
                              <tr>
                                 <td colspan="9" class="text-center text-muted">Belum ada data retur pembelian.</td>
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
         title: 'Hapus Retur Pembelian?',
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
