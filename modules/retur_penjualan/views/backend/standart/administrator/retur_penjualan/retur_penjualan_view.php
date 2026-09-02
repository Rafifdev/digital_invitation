<?php /* retur_penjualan_view.php */ ?>
<section class="content-header"><h1>Detail Retur Penjualan <small><?= _ent($retur->nomor_retur); ?></small></h1><ol class="breadcrumb"><li><a href="<?= base_url('administrator/retur_penjualan'); ?>">Retur Penjualan</a></li><li class="active">Detail</li></ol></section>
<section class="content"><div class="row"><div class="col-md-8">
   <div class="box box-primary">
      <div class="box-header with-border">
         <h3 class="box-title"><?= _ent($retur->nomor_retur); ?> <span class="label label-<?= $retur->status=='approved'?'success':'default'; ?>"><?= strtoupper($retur->status); ?></span></h3>
         <div class="box-tools pull-right"> <a href="<?= site_url('administrator/retur_penjualan/cetak/' . $retur->id); ?>" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-print"></i> Cetak PDF</a> 
            <?php if($retur->status=='draft'): ?><?php is_allowed('retur_penjualan_update', function() use ($retur){ ?><a href="<?= site_url('administrator/retur_penjualan/proses/'.$retur->id); ?>" class="btn btn-success btn-sm" onclick="return confirm('Proses retur? Stok akan dikembalikan.')"><i class="fa fa-cogs"></i> Proses Retur</a><?php }); ?><?php endif; ?>
            <a href="<?= base_url('administrator/retur_penjualan'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
         </div>
      </div>
      <div class="box-body">
         <table class="table table-condensed"><tr><th width="30%">No Retur</th><td><?= _ent($retur->nomor_retur); ?></td></tr><tr><th>Invoice</th><td><?= _ent($retur->nomor_invoice??'-'); ?></td></tr><tr><th>Pelanggan</th><td><strong><?= _ent($retur->nama_pelanggan); ?></strong></td></tr><tr><th>Gudang</th><td><?= _ent($retur->nama_gudang??'-'); ?></td></tr><tr><th>Tanggal</th><td><?= date('d/m/Y',strtotime($retur->tanggal_retur)); ?></td></tr><tr><th>Alasan</th><td><?= _ent($retur->alasan); ?></td></tr><tr><th>Tindak Lanjut</th><td><span class="label label-info"><?= _ent($retur->tindak_lanjut); ?></span></td></tr><tr><th>Total Retur</th><td><strong class="text-danger">Rp <?= number_format($retur->total,0,',','.'); ?></strong></td></tr></table>
         <hr><h4>Item Retur</h4>
         <table class="table table-striped table-sm"><thead><tr><th>#</th><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th><th>Kondisi</th></tr></thead>
            <tbody><?php foreach($items as $i=>$it): ?><tr><td><?= $i+1; ?></td><td><?= _ent($it->nama_produk); ?></td><td><?= number_format((float)$it->qty_retur, 0, ',', '.'); ?></td><td>Rp <?= number_format($it->harga_satuan,0,',','.'); ?></td><td>Rp <?= number_format($it->subtotal,0,',','.'); ?></td><td><span class="label label-<?= $it->kondisi=='baik'?'success':'danger'; ?>"><?= $it->kondisi; ?></span></td></tr><?php endforeach; ?></tbody>
         </table>
      </div>
   </div>
</div></div></section>
