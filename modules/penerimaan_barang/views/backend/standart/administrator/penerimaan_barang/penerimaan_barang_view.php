<?php /* penerimaan_barang_view.php */ 
$status_color = ['draft'=>'default','confirmed'=>'success','cancelled'=>'danger'];
?>
<section class="content-header">
   <h1>Detail Penerimaan Barang <small><?= _ent($penerimaan->nomor_penerimaan); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/penerimaan_barang'); ?>"><i class="fa fa-truck"></i> Penerimaan Barang</a></li>
      <li class="active">Detail</li>
   </ol>
</section>
<section class="content">
<div class="row">
   <div class="col-md-9">
      <div class="box box-primary">
         <div class="box-header with-border">
            <h3 class="box-title">
               <?= _ent($penerimaan->nomor_penerimaan); ?> &nbsp;
               <span class="label label-<?= $status_color[$penerimaan->status] ?? 'default'; ?>" style="font-size: 13px; padding: 4px 10px;">
                  <?= strtoupper($penerimaan->status); ?>
               </span>
            </h3>
            <div class="box-tools pull-right">
               <?php 
                  $total_rusak_grn = 0;
                  if (!empty($items)) {
                     foreach ($items as $it) {
                        $total_rusak_grn += (float)($it->qty_rusak ?? 0);
                     }
                  }
                  
                  // Check if return already exists
                  $existing_retur = null;
                  if ($penerimaan->status == 'confirmed') {
                     $existing_retur = $this->db->get_where('retur_pembelian', ['penerimaan_id' => $penerimaan->id])->row();
                  }
               ?>
               <a href="<?= site_url('administrator/penerimaan_barang/cetak/' . $penerimaan->id); ?>" target="_blank" class="btn btn-info btn-sm btn-flat"><i class="fa fa-print"></i> Cetak PDF</a> 
               
               <?php if($total_rusak_grn > 0 && $penerimaan->status == 'confirmed'): ?>
                  <?php if ($existing_retur): ?>
                     <a href="<?= site_url('administrator/retur_pembelian/view/' . $existing_retur->id); ?>" class="btn btn-warning btn-sm btn-flat"><i class="fa fa-eye"></i> Lihat Retur (<?= _ent($existing_retur->nomor_retur); ?>)</a>
                  <?php else: ?>
                     <a href="<?= site_url('administrator/retur_pembelian/add?penerimaan_id=' . $penerimaan->id); ?>" class="btn btn-danger btn-sm btn-flat"><i class="fa fa-reply"></i> Buat Retur Barang Rusak (<?= (int)$total_rusak_grn; ?> Unit)</a>
                  <?php endif; ?>
               <?php endif; ?>
               <?php if($penerimaan->status == 'draft'): ?>
                  <?php is_allowed('penerimaan_barang_update', function() use ($penerimaan){ ?>
                     <a href="<?= site_url('administrator/penerimaan_barang/edit/'.$penerimaan->id); ?>" class="btn btn-warning btn-sm btn-flat"><i class="fa fa-edit"></i> Edit Draft</a>
                     <button type="button" id="btn_konfirmasi_sweet" class="btn btn-success btn-sm btn-flat"><i class="fa fa-check-circle"></i> Konfirmasi Penerimaan</button>
                  <?php }); ?>
               <?php endif; ?>
               <a href="<?= base_url('administrator/penerimaan_barang'); ?>" class="btn btn-default btn-sm btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
         </div>
         <div class="box-body">
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-condensed table-borderless" style="font-size: 13px;">
                     <tr><th width="140">No Penerimaan</th><td>: <strong class="text-primary"><?= _ent($penerimaan->nomor_penerimaan); ?></strong></td></tr>
                     <tr><th>Referensi PO</th><td>: <?= !empty($penerimaan_detail->nomor_po) ? '<strong class="text-info">'._ent($penerimaan_detail->nomor_po).'</strong>' : '<span class="text-muted">- Tanpa PO -</span>'; ?></td></tr>
                     <tr><th>Supplier</th><td>: <strong><?= _ent($penerimaan_detail->nama_supplier ?? '-'); ?></strong></td></tr>
                     <tr><th>Gudang Tujuan</th><td>: <span class="label label-primary"><i class="fa fa-building"></i> <?= _ent($penerimaan_detail->nama_gudang ?? '-'); ?></span></td></tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <table class="table table-condensed table-borderless" style="font-size: 13px;">
                     <tr><th width="140">Tgl Terima</th><td>: <i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime($penerimaan->tanggal_terima)); ?></td></tr>
                     <tr><th>No Surat Jalan</th><td>: <strong><?= _ent($penerimaan->nomor_surat_jalan ?: '-'); ?></strong></td></tr>
                     <tr><th>Dibuat Oleh</th><td>: <i class="fa fa-user text-muted"></i> <?= _ent($penerimaan->created_by); ?></td></tr>
                     <tr><th>Tgl Dibuat</th><td>: <?= date('d/m/Y H:i', strtotime($penerimaan->created_at)); ?></td></tr>
                  </table>
               </div>
            </div>
            
            <?php if (!empty($penerimaan->catatan)): ?>
               <div class="alert alert-info" style="margin-top: 10px; margin-bottom: 15px; padding: 8px 12px; background: #e0f2fe; border-color: #7dd3fc; color: #0369a1;">
                  <strong><i class="fa fa-info-circle"></i> Catatan Penerimaan:</strong> <?= nl2br(_ent($penerimaan->catatan)); ?>
               </div>
            <?php endif; ?>

            <hr style="margin-top: 10px; margin-bottom: 15px;">
            <h4 style="font-weight: bold; color: #1e293b;"><i class="fa fa-list"></i> Detail Barang Diterima</h4>
            
            <div class="table-responsive">
               <table class="table table-striped table-bordered align-middle">
                  <?php 
                     $curr = $penerimaan_detail->mata_uang ?? 'IDR';
                     $rate = (float)($penerimaan_detail->kurs ?? 1);
                     $fmt_curr = function($val) use ($curr) {
                        if ($curr === 'IDR') {
                           return 'Rp ' . number_format((float)$val, 0, ',', '.');
                        } else {
                           return $curr . ' ' . number_format((float)$val, 2, '.', ',');
                        }
                     };
                  ?>
                  <thead>
                     <tr style="background: #f1f5f9; color: #334155; font-size: 12px;">
                        <th width="30">#</th>
                        <th>Produk</th>
                        <th>Lokasi Rak Gudang</th>
                        <th class="text-center">Qty Terima</th>
                        <th class="text-center">Qty Baik</th>
                        <th class="text-center">Qty Rusak</th>
                        <th class="text-right">Harga Satuan (<?= $curr; ?>)</th>
                        <th class="text-right">Subtotal Baik (<?= $curr; ?>)</th>
                        <th>Batch No.</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php 
                        $total_qty_terima = 0;
                        $total_qty_baik   = 0;
                        $total_qty_rusak  = 0;
                        $total_nilai_baik = 0;
                        foreach($items as $i => $item): 
                           $sub_baik = $item->harga_satuan * $item->qty_baik;
                           $total_qty_terima += $item->qty_terima;
                           $total_qty_baik   += $item->qty_baik;
                           $total_qty_rusak  += $item->qty_rusak;
                           $total_nilai_baik += $sub_baik;
                     ?>
                     <tr>
                        <td><?= $i+1; ?></td>
                        <td>
                           <strong class="text-dark" style="font-size: 13px;"><?= _ent($item->nama_produk); ?></strong><br>
                           <small class="text-muted"><i class="fa fa-barcode"></i> <?= _ent($item->kode_produk); ?></small>
                        </td>
                        <td>
                           <?php if(!empty($item->kode_rak)): ?>
                              <span class="label label-info"><i class="fa fa-cubes"></i> <?= _ent($item->kode_rak); ?> (Baris <?= _ent($item->baris); ?>)</span>
                           <?php else: ?>
                              <span class="label label-default">Rak Default</span>
                           <?php endif; ?>
                        </td>
                        <td class="text-center"><strong><?= number_format($item->qty_terima, 0, ',', '.'); ?></strong> <?= _ent($item->satuan); ?></td>
                        <td class="text-center text-success"><strong style="font-size: 14px;"><?= number_format($item->qty_baik, 0, ',', '.'); ?></strong></td>
                        <td class="text-center">
                           <?php if($item->qty_rusak > 0): ?>
                              <span class="label label-danger" style="font-size: 11px;"><?= number_format($item->qty_rusak, 0, ',', '.'); ?> Rusak</span>
                           <?php else: ?>
                              <span class="text-muted">0</span>
                           <?php endif; ?>
                        </td>
                        <td class="text-right"><strong><?= $fmt_curr($item->harga_satuan); ?></strong></td>
                        <td class="text-right text-success"><strong style="font-size: 13px;"><?= $fmt_curr($sub_baik); ?></strong></td>
                        <td>
                           <?php if (!empty($item->batch_no) || !empty($item->expired_date)): ?>
                              <small>
                                 <?php if(!empty($item->batch_no)): ?><strong>Batch:</strong> <?= _ent($item->batch_no); ?><br><?php endif; ?>
                                 <?php if(!empty($item->expired_date)): ?><strong>Exp:</strong> <?= date('d/m/Y', strtotime($item->expired_date)); ?><?php endif; ?>
                              </small>
                           <?php else: ?>
                              <span class="text-muted">-</span>
                           <?php endif; ?>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                     <tr style="background: #f8fafc; font-weight: bold;">
                        <td colspan="3" class="text-right">TOTAL:</td>
                        <td class="text-center"><?= number_format($total_qty_terima, 0, ',', '.'); ?></td>
                        <td class="text-center text-success" style="font-size: 14px;"><?= number_format($total_qty_baik, 0, ',', '.'); ?></td>
                        <td class="text-center text-danger"><?= number_format($total_qty_rusak, 0, ',', '.'); ?></td>
                        <td class="text-right">TOTAL NILAI (<?= $curr; ?>):</td>
                        <td class="text-right text-success" style="font-size: 15px;">
                           <?= $fmt_curr($total_nilai_baik); ?>
                           <?php if($curr !== 'IDR'): ?>
                              <br><small class="text-primary" style="font-size:11px; font-weight:normal;">(Estimasi IDR: Rp <?= number_format($total_nilai_baik * $rate, 0, ',', '.'); ?>)</small>
                           <?php endif; ?>
                        </td>
                        <td></td>
                     </tr>
                  </tfoot>
               </table>
            </div>
         </div>
      </div>
   </div>

   <div class="col-md-3">
      <div class="box box-info">
         <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-info-circle"></i> Ringkasan Penerimaan</h3></div>
         <div class="box-body" style="background: #f8fafc;">
            <div style="margin-bottom: 12px; padding: 10px; background: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0;">
               <small class="text-muted" style="display:block;">TOTAL NILAI BARANG MASUK STOK (<?= $curr; ?>)</small>
               <strong class="text-success" style="font-size: 18px;"><?= $fmt_curr($total_nilai_baik); ?></strong>
               <?php if($curr !== 'IDR'): ?>
                  <div style="font-size:11px; margin-top:4px;" class="text-primary"><i class="fa fa-exchange"></i> Estimasi IDR: <strong>Rp <?= number_format($total_nilai_baik * $rate, 0, ',', '.'); ?></strong></div>
               <?php endif; ?>
            </div>

            <div style="margin-bottom: 10px; padding: 8px 10px; background: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0;">
               <small class="text-muted" style="display:block;">STATUS DOKUMEN</small>
               <span class="label label-<?= $status_color[$penerimaan->status] ?? 'default'; ?>" style="font-size: 12px;">
                  <?= strtoupper($penerimaan->status); ?>
               </span>
            </div>

            <?php if($penerimaan->status == 'draft'): ?>
               <button type="button" id="btn_konfirmasi_side" class="btn btn-success btn-block btn-flat style-btn" style="margin-top: 15px; font-weight: bold; padding: 8px;">
                  <i class="fa fa-check-circle"></i> Konfirmasi Penerimaan
               </button>
            <?php else: ?>
               <div class="alert alert-success" style="margin-top: 15px; margin-bottom: 0; padding: 8px; font-size: 11px;">
                  <i class="fa fa-check-circle"></i> <strong>Penerimaan Dikonfirmasi:</strong> Stok fisik gudang & Utang Usaha telah aktif.
               </div>
            <?php endif; ?>
         </div>
      </div>
   </div>
</div>
</section>

<script>
$(document).ready(function(){
   $('#btn_konfirmasi_sweet, #btn_konfirmasi_side').click(function(e){
      e.preventDefault();
      swal({
         title: "Konfirmasi Penerimaan Barang?",
         text: "Stok fisik di gudang <?= _ent($penerimaan_detail->nama_gudang ?? ''); ?> akan bertambah dan Utang Usaha akan terbit secara permanen.",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#00a65a",
         confirmButtonText: "Ya, Konfirmasi & Update Stok!",
         cancelButtonText: "Batal",
         closeOnConfirm: false
      }, function(isConfirm){
         if (isConfirm) {
            window.location.href = "<?= site_url('administrator/penerimaan_barang/konfirmasi/'.$penerimaan->id); ?>";
         }
      });
   });
});
</script>
