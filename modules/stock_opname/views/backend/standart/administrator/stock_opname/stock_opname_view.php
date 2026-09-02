<?php /* stock_opname_view.php */ $st=['draft'=>'default','review'=>'info','approved'=>'success']; ?>
<section class="content-header"><h1>Detail Stock Opname <small><?= _ent($opname->nomor_opname); ?></small></h1><ol class="breadcrumb"><li><a href="<?= base_url('administrator/stock_opname'); ?>">Stock Opname</a></li><li class="active">Detail</li></ol></section>
<section class="content">
<?php if ($opname->total_selisih_nilai >= 5000000): ?>
   <div class="callout callout-danger" style="border-left: 5px solid #dd4b39;">
      <h4><i class="fa fa-shield"></i> Perlu Approval Manajer / Admin (Threshold High Value)</h4>
      <p>Total nilai selisih penyesuaian opname ini sebesar <strong>Rp <?= number_format($opname->total_selisih_nilai, 0, ',', '.'); ?></strong> (di atas batas Rp 5.000.000). Sesuai kebijakan hirarki, persetujuan penyesuaian hanya dapat dilakukan oleh Manajer Gudang atau Administrator.</p>
   </div>
<?php endif; ?>
<div class="row">
   <div class="col-md-4">
      <div class="box box-primary">
         <div class="box-header with-border"><h3 class="box-title">Info Opname</h3><div class="box-tools pull-right"> <a href="<?= site_url('administrator/stock_opname/cetak/' . $opname->id); ?>" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-print"></i> Cetak PDF</a> 
            <?php if($opname->status=='draft'): ?><?php is_allowed('stock_opname_update', function() use ($opname){ ?><a href="<?= site_url('administrator/stock_opname/edit/'.$opname->id); ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Input Fisik</a><?php }); ?><?php endif; ?>
            <?php if($opname->status=='review'): ?><?php is_allowed('stock_opname_approve', function() use ($opname){ ?><a href="<?= site_url('administrator/stock_opname/approve/'.$opname->id); ?>" class="btn btn-success btn-sm" onclick="return confirm('Approve? Stok sistem akan disesuaikan dengan stok fisik!')"><i class="fa fa-check"></i> Approve</a><?php }); ?><?php endif; ?>
         </div></div>
         <div class="box-body">
            <table class="table table-striped">
               <tr><th>No Opname</th><td><?= _ent($opname->nomor_opname); ?></td></tr>
               <tr><th>Gudang</th><td><?= _ent($opname->nama_gudang); ?></td></tr>
               <tr><th>Tanggal</th><td><?= date('d/m/Y',strtotime($opname->tanggal_opname)); ?></td></tr>
               <tr><th>Jadwal</th><td><?= ucfirst($opname->jadwal); ?></td></tr>
               <tr><th>Status</th><td><span class="label label-<?= $st[$opname->status]??'default'; ?>"><?= strtoupper($opname->status); ?></span></td></tr>
               <tr><th>Total Selisih</th><td class="<?= $opname->total_selisih_nilai > 0 ? 'text-danger' : ''; ?>"><strong>Rp <?= number_format($opname->total_selisih_nilai,0,',','.'); ?></strong></td></tr>
            </table>
            <a href="<?= base_url('administrator/stock_opname'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
         </div>
      </div>
   </div>
   <div class="col-md-8">
      <div class="box box-default">
         <div class="box-header with-border"><h3 class="box-title">Detail Stok</h3></div>
         <div class="box-body">
            <div class="table-responsive"><table class="table table-sm table-striped">
               <thead><tr><th>#</th><th>Produk</th><th class="text-center">Batch No</th><th class="text-center">Sistem</th><th class="text-center">Fisik</th><th class="text-center">Selisih</th><th>Nilai Selisih</th><th>Status</th></tr></thead>
               <tbody>
                  <?php foreach($items as $i=>$item): ?>
                  <tr class="<?= $item->selisih != 0 ? ($item->selisih < 0 ? 'danger' : 'warning') : ''; ?>">
                     <td><?= $i+1; ?></td>
                     <td><?= _ent($item->nama_produk); ?></td>
                     <td class="text-center"><span class="badge bg-purple"><?= _ent($item->batch_no ?? '-'); ?></span></td>
                     <td class="text-center"><?= number_format((float)$item->stok_sistem, 0, ',', '.'); ?></td>
                     <td class="text-center"><?= $item->stok_fisik !== null ? number_format((float)$item->stok_fisik, 0, ',', '.') : '<span class="text-muted">-</span>'; ?></td>
                     <td class="text-center <?= $item->selisih < 0 ? 'text-danger' : ($item->selisih > 0 ? 'text-success':''); ?>"><strong><?= $item->selisih !== null ? ($item->selisih > 0 ? '+' : '') . number_format((float)$item->selisih, 0, ',', '.') : '-'; ?></strong></td>
                     <td><?= $item->nilai_selisih > 0 ? '<span class="text-danger">Rp '.number_format($item->nilai_selisih,0,',','.').'</span>' : '-'; ?></td>
                     <td><?= $item->status_sesuai ? '<span class="label label-success">Sesuai</span>' : '<span class="label label-danger">Selisih</span>'; ?></td>
                  </tr>
                  <?php endforeach; ?>
               </tbody>
            </table></div>
         </div>
      </div>
   </div>
</div>
</section>
