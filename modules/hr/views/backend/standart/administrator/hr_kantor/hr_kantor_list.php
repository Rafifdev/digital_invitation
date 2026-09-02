<style>
   .modern-box { border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #3c8dbc; }
   .table-modern { border-collapse: separate; border-spacing: 0; width: 100%; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
   .table-modern thead th { background: #f4f6f9; color: #333; font-weight: 600; border-bottom: 2px solid #ddd; padding: 12px; }
   .table-modern tbody td { padding: 12px; vertical-align: middle; border-bottom: 1px solid #f0f0f0; }
   .table-modern tbody tr:hover { background-color: #f8fbff; }
   .btn-modern { border-radius: 20px; padding: 6px 15px; font-weight: 500; transition: all 0.3s; }
   .btn-modern:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
   .btn-action { border-radius: 50%; width: 32px; height: 32px; padding: 0; line-height: 32px; text-align: center; margin: 0 2px; }
   .widget-search { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
   .widget-search form { flex-grow: 1; max-width: 300px; }
   .widget-search form input { border-radius: 20px; padding-left: 15px; }
</style>

<section class="content-header">
   <h1>Data Kantor Cabang <small style="font-weight: 400; color: #777;">Daftar Kantor / Office</small></h1>
   <ol class="breadcrumb" style="background: transparent;">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Daftar Kantor</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box modern-box">
            <div class="box-body" style="padding: 20px;">
               
               <div class="widget-search">
                  <a href="<?= site_url('administrator/hr_kantor/add'); ?>" class="btn btn-primary btn-modern"><i class="fa fa-plus"></i> Tambah Kantor Baru</a>
                  <!-- If you want to add search later, uncomment this block
                  <form action="<?= base_url('administrator/hr_kantor/index'); ?>" method="GET">
                     <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Cari nama kantor...">
                        <span class="input-group-btn">
                           <button type="submit" class="btn btn-default" style="border-radius: 0 20px 20px 0;"><i class="fa fa-search"></i></button>
                        </span>
                     </div>
                  </form>
                  -->
               </div>
               
               <form name="form_hr_kantor" id="form_hr_kantor" action="<?= base_url('administrator/hr_kantor/index'); ?>">
                  <div class="table-responsive">
                     <table class="table table-modern dataTable">
                        <thead>
                           <tr>
                              <th width="50">No</th>
                              <th>Nama Kantor / Cabang</th>
                              <th>Koordinat (Lat, Lng)</th>
                              <th>Radius (Meter)</th>
                              <th>Alamat Lengkap</th>
                              <th width="150" class="text-center">Action</th>
                           </tr>
                        </thead>
                        <tbody id="tbody_hr_kantor">
                           <?php $i = 0; foreach($hr_kantors as $k): $i++; ?>
                           <tr>
                              <td><?= $i; ?></td>
                              <td><strong><?= _ent($k->nama_kantor); ?></strong></td>
                              <td>
                                  <a href="https://maps.google.com/?q=<?= $k->latitude ?>,<?= $k->longitude ?>" target="_blank" class="text-info"><i class="fa fa-map-marker"></i> <?= $k->latitude; ?>, <?= $k->longitude; ?></a>
                              </td>
                              <td><span class="label label-warning" style="font-size:13px; border-radius: 12px; padding: 4px 10px;"><?= $k->radius_meter; ?> Meter</span></td>
                              <td><small><?= _ent($k->alamat); ?></small></td>
                              <td class="text-center">
                                 <a href="<?= site_url('administrator/hr_kantor/edit/'.$k->id); ?>" class="btn btn-default btn-action" title="Edit"><i class="fa fa-pencil text-warning"></i></a>
                                 <?php if($k->id != 1): ?>
                                 <a href="javascript:void(0);" onclick="delete_data('<?= site_url('administrator/hr_kantor/delete/'.$k->id); ?>')" class="btn btn-default btn-action" title="Hapus"><i class="fa fa-trash text-danger"></i></a>
                                 <?php endif; ?>
                              </td>
                           </tr>
                           <?php endforeach; ?>
                           <?php if($hr_kantor_counts == 0): ?>
                           <tr>
                              <td colspan="6" class="text-center text-muted" style="padding: 30px;">Data Kantor Masih Kosong</td>
                           </tr>
                           <?php endif; ?>
                        </tbody>
                     </table>
                  </div>
               </form>
               <div class="row" style="margin-top: 15px;">
                  <div class="col-md-8">
                     <?= $pagination; ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
function delete_data(url) {
   swal({
      title: "Hapus Kantor?",
      text: "Apakah Anda yakin ingin menghapus kantor/cabang ini?",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Ya, Hapus!",
      cancelButtonText: "Batal",
      closeOnConfirm: false
   }, function(){
      window.location.href = url;
   });
}
</script>
