<?php /* produk_list.php */ ?>
<section class="content-header">
   <h1>Data Products <small>Register All Data Products</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/produk'); ?>">Master Data</a></li>
      <li class="active">Data Products</li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-cube"></i> Data Products <span class="label label-warning" style="font-size:11px;"><?= $produk_counts; ?> Item</span></h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('produk_add', function(){ ?>
                  <a href="<?= site_url('administrator/produk/add'); ?>" class="btn btn-success btn-sm btn-flat">
                     <i class="fa fa-plus"></i> Tambah Produk Baru
                  </a>
                  <?php }); ?>
                  <?php is_allowed('produk_export', function(){ ?>
                  <a href="<?= site_url('administrator/produk/export'); ?>" class="btn btn-primary btn-sm btn-flat">
                     <i class="fa fa-file-excel-o"></i> Export XLS
                  </a>
                  <a href="<?= site_url('administrator/produk/export_pdf'); ?>" class="btn btn-info btn-sm btn-flat">
                     <i class="fa fa-file-pdf-o"></i> Export PDF
                  </a>
                  <?php }); ?>
               </div>
            </div>
            <div class="box-body">
               <form action="<?= base_url('administrator/produk/index'); ?>">
                  <div class="row margin-bottom">
                     <div class="col-md-4">
                        <div class="input-group">
                           <input type="text" class="form-control" name="q" placeholder="Cari produk..." value="<?= $this->input->get('q'); ?>">
                           <span class="input-group-btn">
                              <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                              <a href="<?= base_url('administrator/produk'); ?>" class="btn btn-default"><i class="fa fa-undo"></i></a>
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="table-responsive">
                     <table class="table table-bordered table-striped dataTable">
                        <thead>
                           <tr>
                              <th>Kode Produk</th>
                              <th>Nama Produk</th>
                              <th>Kategori</th>
                              <th>Satuan</th>
                              <th class="text-right">Harga Beli</th>
                              <th class="text-right">Harga Jual</th>
                              <th class="text-center">Status</th>
                              <th class="text-center" width="140">Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php foreach($produks as $p): ?>
                           <tr>
                              <td><span class="label label-default"><?= _ent($p->kode_produk); ?></span></td>
                              <td>
                                 <strong><a href="<?= site_url('administrator/produk/view/'.$p->id); ?>"><?= _ent($p->nama_produk); ?></a></strong>
                                 <?php if($p->barcode): ?><br><small class="text-muted"><i class="fa fa-barcode"></i> <?= _ent($p->barcode); ?></small><?php endif; ?>
                              </td>
                              <td><?= _ent($p->nama_kategori ?? 'Umum'); ?></td>
                              <td><span class="badge bg-blue"><?= _ent($p->singkatan ?? $p->nama_satuan ?? 'pcs'); ?></span></td>
                              <td class="text-right">Rp <?= number_format($p->harga_beli, 0, ',', '.'); ?></td>
                              <td class="text-right"><strong class="text-success">Rp <?= number_format($p->harga_jual, 0, ',', '.'); ?></strong></td>
                              <td class="text-center">
                                 <span class="label <?= $p->status == 'aktif' ? 'label-success' : 'label-danger'; ?>">
                                    <?= strtoupper($p->status); ?>
                                 </span>
                              </td>
                              <td class="text-center">
                                 <?php is_allowed('produk_view', function() use ($p){ ?><a href="<?= site_url('administrator/produk/view/'.$p->id); ?>" class="btn btn-xs btn-default" title="Detail"><i class="fa fa-eye"></i></a><?php }); ?>
                                 <?php is_allowed('produk_update', function() use ($p){ ?><a href="<?= site_url('administrator/produk/edit/'.$p->id); ?>" class="btn btn-xs btn-warning" title="Edit"><i class="fa fa-edit"></i></a><?php }); ?>
                                 <?php is_allowed('produk_view', function() use ($p){ ?><a href="<?= site_url('administrator/produk/kartu_stok/'.$p->id); ?>" class="btn btn-xs btn-primary" title="Kartu Stok & Audit"><i class="fa fa-book"></i></a><?php }); ?>
                                 <?php is_allowed('produk_delete', function() use ($p){ ?><a href="javascript:void(0);" data-href="<?= site_url('administrator/produk/delete/'.$p->id); ?>" class="btn btn-xs btn-danger remove-data" title="Hapus"><i class="fa fa-trash"></i></a><?php }); ?>
                              </td>
                           </tr>
                           <?php endforeach; ?>
                           <?php if(count($produks) == 0): ?><tr><td colspan="8" class="text-center text-muted"><i class="fa fa-inbox"></i> Tidak ada data produk</td></tr><?php endif; ?>
                        </tbody>
                     </table>
                  </div>
               </form>
               <div class="row" style="margin-top:15px;">
                  <div class="col-md-6"><span class="text-muted">Total: <strong><?= $produk_counts; ?></strong> produk</span></div>
                  <div class="col-md-6"><div class="pull-right"><?= $pagination; ?></div></div>
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
      swal({title:"Hapus Produk?",text:"Data tidak bisa dikembalikan!",type:"warning",showCancelButton:true,confirmButtonColor:"#DD6B55",confirmButtonText:"Ya, Hapus!",cancelButtonText:"Batal"},function(ok){if(ok) window.location.href=url;});
   });
});
</script>
