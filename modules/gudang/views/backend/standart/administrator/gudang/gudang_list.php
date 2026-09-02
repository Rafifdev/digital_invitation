<?php /* gudang views - list, add, update, view */
// gudang_list.php
?>
<section class="content-header">
   <h1>Data Gudang <small>Kelola Daftar Gudang Utama & Cabang</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/gudang'); ?>">Master Data</a></li>
      <li class="active">Data Gudang</li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-bank"></i> Daftar Seluruh Gudang <span class="label label-warning" style="font-size:11px;"><?= $gudang_counts; ?> Gudang</span></h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('gudang_add', function(){ ?>
                  <a href="<?= site_url('administrator/gudang/add'); ?>" class="btn btn-success btn-sm btn-flat">
                     <i class="fa fa-plus"></i> Tambah Gudang Baru
                  </a>
                  <?php }); ?>
                  <?php is_allowed('gudang_export', function(){ ?>
                  <a href="<?= site_url('administrator/gudang/export'); ?>" class="btn btn-primary btn-sm btn-flat">
                     <i class="fa fa-file-excel-o"></i> Export XLS
                  </a>
                  <a href="<?= site_url('administrator/gudang/export_pdf'); ?>" class="btn btn-info btn-sm btn-flat">
                     <i class="fa fa-file-pdf-o"></i> Export PDF
                  </a>
                  <?php }); ?>
               </div>
            </div>
            <div class="box-body">
               <form action="<?= base_url('administrator/gudang/index'); ?>">
                  <div class="row margin-bottom">
                     <div class="col-md-4">
                        <div class="input-group">
                           <input type="text" class="form-control" name="q" placeholder="Cari kode / nama gudang..." value="<?= $this->input->get('q'); ?>">
                           <span class="input-group-btn"><button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button><a href="<?= base_url('administrator/gudang'); ?>" class="btn btn-default"><i class="fa fa-undo"></i></a></span>
                        </div>
                     </div>
                  </div>
                  <div class="table-responsive">
                     <table class="table table-bordered table-striped dataTable">
                        <thead>
                           <tr>
                              <th>Kode Gudang</th>
                              <th>Nama Gudang</th>
                              <th>Alamat Gudang</th>
                              <th>Telepon</th>
                              <th class="text-center">Tipe Gudang</th>
                              <th class="text-center">Status Operasional</th>
                              <th>Tanggal Dibuat</th>
                              <th>Terakhir Diubah</th>
                              <th class="text-center" width="120">Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php foreach($gudangs as $g): ?>
                           <tr>
                              <td><span class="label label-default"><?= _ent($g->kode_gudang); ?></span></td>
                              <td><strong><a href="<?= site_url('administrator/gudang/view/'.$g->id); ?>"><?= _ent($g->nama_gudang); ?></a></strong></td>
                              <td><small><?= _ent($g->alamat ?? '-'); ?></small></td>
                              <td><i class="fa fa-phone"></i> <?= _ent($g->telepon ?? '-'); ?></td>
                              <td class="text-center">
                                 <?php if($g->tipe_gudang == 'utama'): ?>
                                    <span class="label label-success"><i class="fa fa-star"></i> Utama</span>
                                 <?php elseif($g->tipe_gudang == 'cabang'): ?>
                                    <span class="label label-info">Cabang</span>
                                 <?php elseif($g->tipe_gudang == 'transit'): ?>
                                    <span class="label label-warning">Transit</span>
                                 <?php elseif($g->tipe_gudang == 'retur'): ?>
                                    <span class="label label-danger">Retur</span>
                                 <?php else: ?>
                                    <span class="label label-default"><?= ucfirst($g->tipe_gudang ?? 'Utama'); ?></span>
                                 <?php endif; ?>
                              </td>
                              <td class="text-center">
                                 <span class="label <?= $g->status=='aktif'?'label-success':'label-danger'; ?>">
                                    <?= $g->status=='aktif' ? 'Aktif' : 'Nonaktif'; ?>
                                 </span>
                              </td>
                              <td><small><?= date('d/m/Y H:i', strtotime($g->created_at ?? date('Y-m-d H:i'))); ?></small></td>
                              <td><small><?= date('d/m/Y H:i', strtotime($g->created_at ?? date('Y-m-d H:i'))); ?></small></td>
                              <td class="text-center">
                                 <?php is_allowed('gudang_view', function() use ($g){ ?><a href="<?= site_url('administrator/gudang/view/'.$g->id); ?>" class="btn btn-xs btn-default" title="Detail"><i class="fa fa-eye"></i></a><?php }); ?>
                                 <?php is_allowed('gudang_update', function() use ($g){ ?><a href="<?= site_url('administrator/gudang/edit/'.$g->id); ?>" class="btn btn-xs btn-warning" title="Edit"><i class="fa fa-edit"></i></a><?php }); ?>
                                 <?php is_allowed('gudang_delete', function() use ($g){ ?><a href="javascript:void(0);" data-href="<?= site_url('administrator/gudang/delete/'.$g->id); ?>" class="btn btn-xs btn-danger remove-data" title="Hapus"><i class="fa fa-trash"></i></a><?php }); ?>
                              </td>
                           </tr>
                           <?php endforeach; ?>
                           <?php if(count($gudangs)==0): ?><tr><td colspan="9" class="text-center text-muted">Tidak ada data gudang</td></tr><?php endif; ?>
                        </tbody>
                     </table>
                  </div>
               </form>
               <div class="row" style="margin-top:15px;"><div class="col-md-6"><span class="text-muted">Total: <strong><?= $gudang_counts; ?></strong> gudang</span></div><div class="col-md-6"><div class="pull-right"><?= $pagination; ?></div></div></div>
            </div>
         </div>
      </div>
   </div>
</section>
<script>$(document).ready(function(){$('.remove-data').click(function(){var url=$(this).attr('data-href');swal({title:"Hapus Gudang?",text:"Data tidak bisa dikembalikan!",type:"warning",showCancelButton:true,confirmButtonColor:"#DD6B55",confirmButtonText:"Ya, Hapus!",cancelButtonText:"Batal"},function(ok){if(ok) window.location.href=url;});});});</script>
