<section class="content-header">
   <h1>Kategori Barang <small>Register All</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/kategori_produk'); ?>">Master Data</a></li>
      <li class="active">Kategori Barang</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-tags"></i> Register All Kategori Barang <span class="label label-warning" style="font-size:11px;"><?= $kategori_produk_counts; ?> Item</span></h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('kategori_produk_add', function(){?>
                  <a class="btn btn-flat btn-success btn-sm" href="<?= site_url('administrator/kategori_produk/add'); ?>"><i class="fa fa-plus"></i> Tambah Kategori Barang</a>
                  <?php }); ?>
                  <?php is_allowed('kategori_produk_export', function(){?>
                  <a class="btn btn-flat btn-primary btn-sm" href="<?= site_url('administrator/kategori_produk/export'); ?>"><i class="fa fa-file-excel-o"></i> Export XLS</a>
                  <a class="btn btn-flat btn-info btn-sm" href="<?= site_url('administrator/kategori_produk/export_pdf'); ?>"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
                  <?php }); ?>
               </div>
            </div>
            <div class="box-body">
               <form name="form_kategori_produk" id="form_kategori_produk" action="<?= base_url('administrator/kategori_produk/index'); ?>">
                  <div class="row margin-bottom">
                     <div class="col-sm-4">
                        <div class="input-group">
                           <input type="text" class="form-control" name="q" id="filter" placeholder="Cari kategori barang..." value="<?= $this->input->get('q'); ?>">
                           <span class="input-group-btn">
                              <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                              <a href="<?= base_url('administrator/kategori_produk'); ?>" class="btn btn-default"><i class="fa fa-undo"></i></a>
                           </span>
                        </div>
                     </div>
                  </div>

                  <div class="table-responsive"> 
                     <table class="table table-bordered table-striped dataTable">
                        <thead>
                           <tr>
                              <th>Category Code</th>
                              <th>Category Name</th>
                              <th>Description</th>
                              <th>Created At</th>
                              <th>Updated At</th>
                              <th width="120" class="text-center">Action</th>
                           </tr>
                        </thead>
                        <tbody>
                        <?php foreach($kategori_produks as $k): ?>
                           <tr>
                              <td><span class="label label-default">KAT-<?= strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $k->nama_kategori), 0, 8)); ?></span></td>
                              <td><strong><a href="<?= site_url('administrator/kategori_produk/view/' . $k->id); ?>"><?= _ent($k->nama_kategori); ?></a></strong></td> 
                              <td><small><?= _ent($k->deskripsi ?? '-'); ?></small></td> 
                              <td><small><?= date('Y-m-d H:i', strtotime($k->created_at ?? date('Y-m-d H:i'))); ?></small></td> 
                              <td><small><?= date('Y-m-d H:i', strtotime($k->created_at ?? date('Y-m-d H:i'))); ?></small></td> 
                              <td class="text-center">
                                 <?php is_allowed('kategori_produk_view', function() use ($k){?>
                                 <a href="<?= site_url('administrator/kategori_produk/view/' . $k->id); ?>" class="btn btn-default btn-xs" title="Detail"><i class="fa fa-eye"></i></a>
                                 <?php }); ?>
                                 <?php is_allowed('kategori_produk_update', function() use ($k){?>
                                 <a href="<?= site_url('administrator/kategori_produk/edit/' . $k->id); ?>" class="btn btn-warning btn-xs" title="Edit"><i class="fa fa-edit"></i></a>
                                 <?php }); ?>
                                 <?php is_allowed('kategori_produk_delete', function() use ($k){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/kategori_produk/delete/' . $k->id); ?>" class="btn btn-danger btn-xs remove-data" title="Hapus"><i class="fa fa-trash"></i></a>
                                 <?php }); ?>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                        <?php if ($kategori_produk_counts == 0) :?>
                           <tr><td colspan="6" class="text-center text-muted">Kategori Barang data is not available</td></tr>
                        <?php endif; ?>
                        </tbody>
                     </table>
                  </div>
                  <div class="row" style="margin-top:15px;">
                     <div class="col-md-6"><span class="text-muted">Total: <strong><?= $kategori_produk_counts; ?></strong> kategori</span></div>
                     <div class="col-md-6"><div class="pull-right"><?= $pagination; ?></div></div>
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
         title: "Hapus Kategori?",
         text: "Data tidak bisa dikembalikan!",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "Ya, Hapus!",
         cancelButtonText: "Batal",
         closeOnConfirm: true
      }, function(ok){ if(ok) window.location.href = url; });
   });
});
</script>