<?php
/**
 * View: Daftar Penerimaan Barang (GRN) - Modern Sleek UI
 * File: penerimaan_barang_list.php
 */
$status_color = [
    'draft'     => 'label-default',
    'confirmed' => 'label-success',
    'cancelled' => 'label-danger'
];
?>
<section class="content-header">
   <h1>
      Penerimaan Barang (GRN)
      <small>Pengecekan Fisik & Stok Masuk Gudang</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/penerimaan_barang'); ?>">Pembelian</a></li>
      <li class="active">Penerimaan Barang</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary" style="border-radius:6px; border-top: 3px solid #3c8dbc;">
            <div class="box-header with-border" style="padding: 15px 20px;">
               <h3 class="box-title" style="font-weight:600;"><i class="fa fa-download text-primary"></i> Daftar Dokumen Penerimaan Barang (Goods Receipt Note)</h3>
               <div class="box-tools pull-right">
                  <?php if (is_allowed('penerimaan_barang_add', false)): ?>
                     <a href="<?= site_url('administrator/penerimaan_barang/add'); ?>" class="btn btn-success btn-sm btn-flat" style="border-radius:4px; font-weight:600;">
                        <i class="fa fa-plus"></i> Terima Barang Baru
                     </a>
                  <?php endif; ?>
               </div>
            </div>

            <div class="box-body" style="padding: 20px;">
               <form action="<?= base_url('administrator/penerimaan_barang/index'); ?>" method="GET">
                  <div class="row" style="margin-bottom: 15px;">
                     <div class="col-md-4">
                        <div class="input-group">
                           <input type="text" class="form-control" name="q" placeholder="Cari No GRN / Supplier / PO..." value="<?= _ent($this->input->get('q')); ?>">
                           <span class="input-group-btn">
                              <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search"></i></button>
                              <a href="<?= base_url('administrator/penerimaan_barang'); ?>" class="btn btn-default btn-flat"><i class="fa fa-undo"></i></a>
                           </span>
                        </div>
                     </div>
                  </div>

                  <div class="table-responsive">
                     <table class="table table-hover table-striped table-bordered" style="vertical-align: middle;">
                        <thead>
                           <tr style="background:#f8fafc; font-size:12px; color:#555;">
                              <th width="40" class="text-center">No</th>
                              <th>No. Penerimaan (GRN)</th>
                              <th>Tanggal Masuk</th>
                              <th>No. Referensi PO</th>
                              <th>Supplier</th>
                              <th>Gudang Tujuan</th>
                              <th>Diterima Oleh</th>
                              <th class="text-center">Status</th>
                              <th class="text-center" width="160">Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php if (!empty($penerimaans)): ?>
                              <?php $no = $this->uri->segment(4) + 1; foreach ($penerimaans as $p): ?>
                                 <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><strong class="text-primary"><a href="<?= site_url('administrator/penerimaan_barang/view/' . $p->id); ?>"><?= _ent($p->nomor_penerimaan); ?></a></strong></td>
                                    <td><i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime(isset($p->tanggal_terima) ? $p->tanggal_terima : (isset($p->tanggal_penerimaan) ? $p->tanggal_penerimaan : $p->created_at))); ?></td>
                                    <td>
                                       <a href="<?= site_url('administrator/purchase_order/view/' . $p->po_id); ?>" class="label label-info">
                                          <i class="fa fa-file-text-o"></i> <?= _ent($p->nomor_po ?? '-'); ?>
                                       </a>
                                    </td>
                                    <td><strong><?= _ent($p->nama_supplier); ?></strong></td>
                                    <td><span class="label label-primary"><i class="fa fa-bank"></i> <?= _ent($p->nama_gudang); ?></span></td>
                                    <td><small><i class="fa fa-user text-muted"></i> <?= _ent($p->diterima_oleh ?? 'Admin'); ?></small></td>
                                    <td class="text-center">
                                       <span class="label <?= isset($status_color[$p->status]) ? $status_color[$p->status] : 'label-default'; ?>">
                                          <?= $p->status == 'confirmed' ? 'CONFIRMED' : 'DRAFT'; ?>
                                       </span>
                                    </td>
                                    <td class="text-center">
                                       <div class="btn-group">
                                          <a href="<?= site_url('administrator/penerimaan_barang/view/' . $p->id); ?>" class="btn btn-default btn-xs" title="Detail"><i class="fa fa-eye"></i> Detail</a>
                                          <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                             <span class="caret"></span>
                                          </button>
                                          <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                             <li><a href="<?= site_url('administrator/penerimaan_barang/cetak/' . $p->id); ?>" target="_blank"><i class="fa fa-print text-info"></i> Cetak GRN PDF</a></li>
                                             <?php if ($p->status == 'draft' && is_allowed('penerimaan_barang_update', false)): ?>
                                             <li><a href="<?= site_url('administrator/penerimaan_barang/edit/' . $p->id); ?>"><i class="fa fa-edit text-warning"></i> Edit GRN</a></li>
                                             <li><a href="<?= site_url('administrator/penerimaan_barang/konfirmasi/' . $p->id); ?>"><i class="fa fa-check text-success"></i> Konfirmasi & Tambah Stok</a></li>
                                             <?php endif; ?>

                                             <?php if (is_allowed('penerimaan_barang_delete', false)): ?>
                                             <li class="divider"></li>
                                             <li><a href="javascript:void(0);" data-href="<?= site_url('administrator/penerimaan_barang/delete/' . $p->id); ?>" class="remove-data text-danger"><i class="fa fa-trash"></i> Hapus GRN</a></li>
                                             <?php endif; ?>
                                          </ul>
                                       </div>
                                    </td>
                                 </tr>
                              <?php endforeach; ?>
                           <?php else: ?>
                              <tr>
                                 <td colspan="9" class="text-center text-muted" style="padding:30px;"><i class="fa fa-inbox fa-2x"></i><br>Belum ada data penerimaan barang.</td>
                              </tr>
                           <?php endif; ?>
                        </tbody>
                     </table>
                  </div>

                  <div class="row" style="margin-top: 15px;">
                     <div class="col-md-6">
                        <span class="text-muted">Total: <strong><?= $penerimaan_counts; ?></strong> data Penerimaan Barang</span>
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
         title: "Hapus Penerimaan Barang?",
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
