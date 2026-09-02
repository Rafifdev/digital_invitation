<?php /* gudang_view.php */ ?>
<section class="content-header">
   <h1>Detail Gudang <small><?= _ent($gudang->nama_gudang); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/gudang'); ?>"><i class="fa fa-building"></i> Gudang</a></li>
      <li class="active">Detail</li>
   </ol>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-4">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><?= _ent($gudang->nama_gudang); ?></h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('gudang_update', function() use ($gudang){ ?><a href="<?= site_url('administrator/gudang/edit/'.$gudang->id); ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a><?php }); ?>
               </div>
            </div>
            <div class="box-body">
               <table class="table table-striped">
                  <tr><th>Kode</th><td><span class="label label-default"><?= _ent($gudang->kode_gudang); ?></span></td></tr>
                  <tr><th>Tipe</th><td><span class="label label-info"><?= ucfirst($gudang->tipe_gudang ?? 'Utama'); ?></span></td></tr>
                  <tr><th>PIC</th><td><?= _ent($gudang->pic_name); ?></td></tr>
                  <tr><th>Telepon</th><td><?= _ent($gudang->telepon); ?></td></tr>
                  <tr><th>Alamat</th><td><?= nl2br(_ent($gudang->alamat)); ?></td></tr>
                  <tr><th>Status</th><td><span class="label <?= $gudang->status=='aktif'?'label-success':'label-danger'; ?>"><?= $gudang->status; ?></span></td></tr>
               </table>
               <a href="<?= base_url('administrator/gudang'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
         </div>
         <?php if(!empty($rak_list)): ?>
         <div class="box box-info">
            <div class="box-header with-border"><h3 class="box-title">Daftar Rak</h3></div>
            <div class="box-body">
               <table class="table table-sm"><thead><tr><th>Kode</th><th>Baris</th></tr></thead>
                  <tbody><?php foreach($rak_list as $r): ?><tr><td><?= _ent($r->kode_rak); ?></td><td><?= _ent($r->baris); ?></td></tr><?php endforeach; ?></tbody>
               </table>
            </div>
         </div>
         <?php endif; ?>
      </div>
      <div class="col-md-8">
         <div class="box box-success">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-cubes"></i> Stok Produk di Gudang Ini</h3>
               <?php is_allowed('stok_list', function() use ($gudang){ ?><a href="<?= site_url('administrator/stok/index?gudang_id='.$gudang->id); ?>" class="btn btn-sm btn-primary pull-right"><i class="fa fa-external-link"></i> Lihat Dashboard Stok</a><?php }); ?>
            </div>
            <div class="box-body">
               <div class="table-responsive">
                  <table class="table table-sm table-striped">
                     <thead><tr><th>Kode</th><th>Nama Produk</th><th>Stok</th><th>Sat</th><th>Rusak</th><th>Status</th></tr></thead>
                     <tbody>
                        <?php foreach($stok_list as $s): ?>
                        <tr class="<?= $s->qty <= $s->stok_minimum ? 'warning' : ''; ?>">
                           <td><?= _ent($s->kode_produk); ?></td>
                           <td><?= _ent($s->nama_produk); ?></td>
                           <td><strong><?= number_format($s->qty, 0); ?></strong></td>
                           <td><?= _ent($s->singkatan); ?></td>
                           <td><?= $s->qty_rusak > 0 ? '<span class="text-danger">'.number_format($s->qty_rusak,0).'</span>' : '-'; ?></td>
                           <td><?php if($s->qty <= $s->stok_minimum): ?><span class="label label-danger"><i class="fa fa-warning"></i> Menipis</span><?php else: ?><span class="label label-success">OK</span><?php endif; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($stok_list)): ?><tr><td colspan="6" class="text-center text-muted">Tidak ada data stok</td></tr><?php endif; ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
