<?php /* pelanggan_list.php - Sleek High-End UI */ ?>
<style>
.kpi-card { background:#fff; border-radius:8px; padding:15px; margin-bottom:15px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.05); position:relative; }
.kpi-title { font-size:11px; text-transform:uppercase; color:#64748b; font-weight:600; }
.kpi-value { font-size:20px; font-weight:700; color:#1e293b; margin:4px 0; }
.kpi-sub { font-size:11px; color:#94a3b8; }
.kpi-icon { position:absolute; right:15px; top:18px; font-size:28px; opacity:0.15; }
</style>

<section class="content-header">
   <h1>Data Pelanggan (Customers) <small>Manajemen Mitra, Tier Pelanggan &amp; Limit Kredit</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li>Master Data</li>
      <li class="active">Pelanggan</li>
   </ol>
</section>

<section class="content">
   <!-- KPI Metric Summary -->
   <div class="row">
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left:4px solid #3b82f6;">
            <div class="kpi-title">TOTAL PELANGGAN</div>
            <div class="kpi-value"><?= number_format($pelanggan_counts); ?></div>
            <div class="kpi-sub"><i class="fa fa-users text-primary"></i> Mitra Terdaftar</div>
            <i class="fa fa-users kpi-icon text-primary"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left:4px solid #8b5cf6;">
            <div class="kpi-title">DISTRIBUTOR UTAMA</div>
            <div class="kpi-value" style="color:#8b5cf6;">
               <?= count(array_filter($pelanggans, function($p){ return strtolower($p->tipe_pelanggan)=='distributor'; })); ?>
            </div>
            <div class="kpi-sub"><i class="fa fa-building"></i> Partner Volume Besar</div>
            <i class="fa fa-building kpi-icon" style="color:#8b5cf6;"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left:4px solid #f59e0b;">
            <div class="kpi-title">MITRA GROSIR</div>
            <div class="kpi-value text-warning">
               <?= count(array_filter($pelanggans, function($p){ return strtolower($p->tipe_pelanggan)=='grosir'; })); ?>
            </div>
            <div class="kpi-sub"><i class="fa fa-shopping-bag"></i> Toko Grosir</div>
            <i class="fa fa-shopping-bag kpi-icon text-warning"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left:4px solid #10b981;">
            <div class="kpi-title">PELANGGAN RETAIL</div>
            <div class="kpi-value text-success">
               <?= count(array_filter($pelanggans, function($p){ return strtolower($p->tipe_pelanggan)=='retail'; })); ?>
            </div>
            <div class="kpi-sub"><i class="fa fa-user"></i> Penjualan Eceran</div>
            <i class="fa fa-user kpi-icon text-success"></i>
         </div>
      </div>
   </div>

   <div class="box box-primary" style="border-radius:6px;">
      <div class="box-header with-border">
         <h3 class="box-title"><i class="fa fa-address-book text-primary"></i> Daftar Master Pelanggan</h3>
         <div class="box-tools pull-right">
            <?php is_allowed('pelanggan_add', function(){ ?>
            <a href="<?= site_url('administrator/pelanggan/add'); ?>" class="btn btn-success btn-sm btn-flat" style="border-radius:4px; font-weight:600;"><i class="fa fa-plus-circle"></i> Tambah Pelanggan Baru</a>
            <?php }); ?>
         </div>
      </div>
      <div class="box-body">
         <form id="form_pelanggan" action="<?= base_url('administrator/pelanggan/index'); ?>">
            <div class="row" style="margin-bottom:15px;">
               <div class="col-md-5">
                  <div class="input-group">
                     <input type="text" class="form-control" name="q" placeholder="Cari Kode, Nama, Kota, Telepon..." value="<?= _ent($this->input->get('q')); ?>">
                     <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search"></i> Cari</button>
                        <a href="<?= base_url('administrator/pelanggan'); ?>" class="btn btn-default btn-flat" title="Reset Filter"><i class="fa fa-refresh"></i></a>
                     </span>
                  </div>
               </div>
               <div class="col-md-3">
                  <select name="f" class="form-control">
                     <option value="">-- Semua Kriteria --</option>
                     <option <?= $this->input->get('f') == 'nama_pelanggan' ? 'selected' : ''; ?> value="nama_pelanggan">Nama Pelanggan</option>
                     <option <?= $this->input->get('f') == 'tipe_pelanggan' ? 'selected' : ''; ?> value="tipe_pelanggan">Tipe Pelanggan</option>
                     <option <?= $this->input->get('f') == 'kota' ? 'selected' : ''; ?> value="kota">Kota / Wilayah</option>
                  </select>
               </div>
            </div>

            <div class="table-responsive">
               <table class="table table-bordered table-striped table-hover">
                  <thead>
                     <tr style="background:#f8fafc;">
                        <th width="40" class="text-center">No</th>
                        <th>Kode</th>
                        <th>Nama Pelanggan</th>
                        <th>Tipe / Tier</th>
                        <th>No. Telepon</th>
                        <th>Kota</th>
                        <th class="text-right">Limit Kredit</th>
                        <th class="text-center">Status</th>
                        <th width="100" class="text-center">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php $no = 1; foreach($pelanggans as $p): ?>
                     <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><span class="label label-default" style="font-size:11px;"><?= _ent($p->kode_pelanggan); ?></span></td>
                        <td><strong><?= _ent($p->nama_pelanggan); ?></strong></td>
                        <td>
                           <?php 
                              $tipe = strtolower($p->tipe_pelanggan);
                              if($tipe == 'distributor'){
                                 echo '<span class="badge bg-purple">DISTRIBUTOR</span>';
                              } else if($tipe == 'grosir'){
                                 echo '<span class="badge bg-orange">GROSIR</span>';
                              } else {
                                 echo '<span class="badge bg-blue">RETAIL</span>';
                              }
                           ?>
                        </td>
                        <td><i class="fa fa-phone text-muted"></i> <?= _ent($p->telepon ?? '-'); ?></td>
                        <td><?= _ent($p->kota ?? '-'); ?></td>
                        <td class="text-right"><strong>Rp <?= number_format($p->limit_kredit, 0, ',', '.'); ?></strong></td>
                        <td class="text-center"><span class="label <?= $p->status == 'aktif' ? 'label-success' : 'label-danger'; ?>"><?= ucfirst($p->status); ?></span></td>
                        <td class="text-center">
                           <div class="btn-group">
                              <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-flat" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius:3px;">
                                 Aksi <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu dropdown-menu-right" style="border-radius:4px; box-shadow:0 3px 10px rgba(0,0,0,0.15);">
                                 <?php is_allowed('pelanggan_view', function() use ($p){ ?>
                                    <li><a href="<?= site_url('administrator/pelanggan/view/'.$p->id); ?>"><i class="fa fa-eye text-info"></i> Detail Pelanggan</a></li>
                                 <?php }); ?>
                                 <?php is_allowed('pelanggan_update', function() use ($p){ ?>
                                    <li><a href="<?= site_url('administrator/pelanggan/edit/'.$p->id); ?>"><i class="fa fa-edit text-warning"></i> Edit Data</a></li>
                                 <?php }); ?>
                                 <li role="separator" class="divider"></li>
                                 <?php is_allowed('pelanggan_delete', function() use ($p){ ?>
                                    <li><a href="javascript:void(0);" data-href="<?= site_url('administrator/pelanggan/delete/'.$p->id); ?>" class="remove-data text-danger"><i class="fa fa-trash"></i> Hapus Pelanggan</a></li>
                                 <?php }); ?>
                              </ul>
                           </div>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                     <?php if(count($pelanggans) == 0): ?><tr><td colspan="9" class="text-center text-muted"><i class="fa fa-inbox"></i> Tidak ada data pelanggan.</td></tr><?php endif; ?>
                  </tbody>
               </table>
            </div>
         </form>
         <div class="row">
            <div class="col-md-6"><span class="text-muted">Total Data: <strong><?= number_format($pelanggan_counts); ?></strong> Pelanggan</span></div>
            <div class="col-md-6 text-right"><?= $pagination; ?></div>
         </div>
      </div>
   </div>
</section>

<script>
$(document).ready(function(){
   $('.remove-data').click(function(){
      var url = $(this).attr('data-href');
      swal({
         title: "Hapus Pelanggan?",
         text: "Data yang dihapus tidak bisa dikembalikan!",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "Ya, Hapus!",
         cancelButtonText: "Batal"
      }, function(ok){ if(ok) window.location.href=url; });
   });
});
</script>
