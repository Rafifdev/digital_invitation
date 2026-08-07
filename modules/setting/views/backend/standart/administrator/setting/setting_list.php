<?php /* setting_list.php */ ?>
<section class="content-header">
   <h1>Setting <small>Daftar Data</small></h1>
   <ol class="breadcrumb"><li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li><li class="active">Setting</li></ol>
</section>
<section class="content">
   <div class="row"><div class="col-md-12">
      <div class="box box-primary">
         <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list"></i> Data Setting</h3>
            <div class="box-tools pull-right">
               <?php if (is_allowed('setting_add', false)): ?>
               <a href="<?= site_url('administrator/setting/add'); ?>" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah</a>
               <?php endif; ?>
            </div>
         </div>
         <div class="box-body">
            <div class="table-responsive">
               <table class="table table-bordered table-striped">
                  <thead><tr><th>No</th><th>Data</th><th width="120">Aksi</th></tr></thead>
                  <tbody>
                     <tr><td colspan="3" class="text-center text-muted"><i class="fa fa-inbox"></i> Belum ada data</td></tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div></div>
</section>