<section class="content-header">
   <h1>Detail Lokasi Rak <small><?= _ent($rak->kode_rak); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/rak'); ?>"><i class="fa fa-cubes"></i> Lokasi Rak</a></li>
      <li class="active">Detail</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-8">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title">Informasi Lokasi Rak</h3>
               <div class="box-tools pull-right">
                  <a href="<?= base_url('administrator/rak'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
               </div>
            </div>
            <div class="box-body">
               <table class="table table-striped">
                  <tr>
                     <th width="30%">Gudang</th>
                     <td><strong><?= _ent($rak->nama_gudang ?? '-'); ?></strong> (<?= _ent($rak->kode_gudang ?? '-'); ?>)</td>
                  </tr>
                  <tr>
                     <th>Kode Rak</th>
                     <td><span class="label label-primary font-14"><i class="fa fa-cubes"></i> <?= _ent($rak->kode_rak); ?></span></td>
                  </tr>
                  <tr>
                     <th>Baris / Tingkat</th>
                     <td><span class="label label-info font-14"><?= _ent($rak->baris); ?></span></td>
                  </tr>
                  <tr>
                     <th>Kategori / Zona</th>
                     <td><span class="label label-default"><?= _ent($rak->kategori_rak ?? 'Utama'); ?></span></td>
                  </tr>
                  <tr>
                     <th>Keterangan</th>
                     <td><?= nl2br(_ent($rak->deskripsi ?? '-')); ?></td>
                  </tr>
                  <tr>
                     <th>Status</th>
                     <td><span class="label label-<?= $rak->status == 'aktif' ? 'success' : 'danger'; ?>"><?= strtoupper($rak->status); ?></span></td>
                  </tr>
                  <tr>
                     <th>Dibuat Pada</th>
                     <td><?= date('d/m/Y H:i:s', strtotime($rak->created_at)); ?></td>
                  </tr>
               </table>
            </div>
            <div class="box-footer">
               <?php is_allowed('rak_update', function() use ($rak){?>
               <a href="<?= site_url('administrator/rak/edit/' . $rak->id); ?>" class="btn btn-warning"><i class="fa fa-edit"></i> Edit Rak Ini</a>
               <?php }); ?>
            </div>
         </div>
      </div>
   </div>
</section>
