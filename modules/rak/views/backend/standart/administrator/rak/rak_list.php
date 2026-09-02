<section class="content-header">
   <h1>Lokasi Rak Gudang <small>Kelola Tingkat & Sekat Rak Gudang</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/rak'); ?>">Master Data</a></li>
      <li class="active">Lokasi Rak</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-cubes"></i> Daftar Seluruh Lokasi Rak <span class="label label-warning" style="font-size:11px;"><?= count($raks); ?> Rak</span></h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('rak_add', function(){?>
                  <a class="btn btn-flat btn-success btn-sm" title="Tambah Rak Baru" href="<?= site_url('administrator/rak/add'); ?>"><i class="fa fa-plus"></i> Tambah Rak Baru</a>
                  <?php }); ?>
                  <?php is_allowed('rak_export', function(){?>
                  <a class="btn btn-flat btn-primary btn-sm" title="Export Excel" href="<?= site_url('administrator/rak/export'); ?>"><i class="fa fa-file-excel-o"></i> Export XLS</a>
                  <a class="btn btn-flat btn-info btn-sm" title="Export PDF" href="<?= site_url('administrator/rak/export_pdf'); ?>"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
                  <?php }); ?>
               </div>
            </div>
            <div class="box-body">
               <form name="form_rak" id="form_rak" action="<?= base_url('administrator/rak/index'); ?>">
                  <div class="row margin-bottom">
                     <div class="col-sm-4">
                        <div class="input-group">
                           <input type="text" class="form-control" name="q" id="filter" placeholder="Cari Kode Rak / Baris / Gudang..." value="<?= $this->input->get('q'); ?>">
                           <span class="input-group-btn">
                              <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                              <a href="<?= base_url('administrator/rak'); ?>" class="btn btn-default"><i class="fa fa-undo"></i></a>
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="table-responsive">
                     <table class="table table-bordered table-striped dataTable">
                        <thead>
                           <tr>
                              <th>Lokasi Gudang</th>
                              <th>Kode Rak</th>
                              <th class="text-center">Tingkat / Baris Rak</th>
                              <th class="text-center">Sekat / Kolom Rak</th>
                              <th>Keterangan Rak</th>
                              <th>Tanggal Dibuat</th>
                              <th>Terakhir Diubah</th>
                              <th width="120" class="text-center">Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php if (!empty($raks)): ?>
                              <?php foreach ($raks as $rak): ?>
                              <tr>
                                 <td><span class="label label-primary"><i class="fa fa-bank"></i> <?= _ent($rak->nama_gudang ?? 'Gudang Utama'); ?></span></td>
                                 <td><strong><a href="<?= site_url('administrator/rak/view/' . $rak->id); ?>"><?= _ent($rak->kode_rak); ?></a></strong></td>
                                 <td class="text-center"><span class="badge bg-blue">Tingkat <?= _ent($rak->baris); ?></span></td>
                                 <td class="text-center"><span class="badge bg-green">Kolom 1</span></td>
                                 <td><small><?= _ent($rak->deskripsi ?? '-'); ?></small></td>
                                 <td><small><?= date('d/m/Y H:i', strtotime($rak->created_at ?? date('Y-m-d H:i'))); ?></small></td>
                                 <td><small><?= date('d/m/Y H:i', strtotime($rak->created_at ?? date('Y-m-d H:i'))); ?></small></td>
                                 <td class="text-center">
                                    <?php is_allowed('rak_view', function() use ($rak){?>
                                    <a href="<?= site_url('administrator/rak/view/' . $rak->id); ?>" class="btn btn-default btn-xs" title="Detail"><i class="fa fa-eye"></i></a>
                                    <?php }); ?>
                                    <?php is_allowed('rak_update', function() use ($rak){?>
                                    <a href="<?= site_url('administrator/rak/edit/' . $rak->id); ?>" class="btn btn-warning btn-xs" title="Edit"><i class="fa fa-edit"></i></a>
                                    <?php }); ?>
                                    <?php is_allowed('rak_delete', function() use ($rak){?>
                                    <a href="javascript:void(0);" onclick="delete_rak('<?= $rak->id; ?>')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-trash"></i></a>
                                    <?php }); ?>
                                 </td>
                              </tr>
                              <?php endforeach; ?>
                           <?php else: ?>
                              <tr><td colspan="8" class="text-center text-muted">Data Rak Gudang tidak ditemukan.</td></tr>
                           <?php endif; ?>
                        </tbody>
                     </table>
                  </div>
                  <div class="row" style="margin-top:15px;">
                     <div class="col-md-12">
                        <?= $pagination; ?>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
function delete_rak(id) {
   swal({
      title: "Apakah Anda Yakin?",
      text: "Data lokasi rak ini akan dihapus dari sistem!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Ya, Hapus!",
      cancelButtonText: "Batal",
      closeOnConfirm: false
   }, function(){
      window.location.href = '<?= site_url("administrator/rak/delete/"); ?>' + id;
   });
}
</script>
