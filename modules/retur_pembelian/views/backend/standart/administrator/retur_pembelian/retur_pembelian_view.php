<?php /* retur_pembelian_view.php */ ?>
<section class="content-header"><h1>Detail Retur Pembelian <small><?= _ent($retur->nomor_retur); ?></small></h1><ol class="breadcrumb"><li><a href="<?= base_url('administrator/retur_pembelian'); ?>">Retur Pembelian</a></li><li class="active">Detail</li></ol></section>
<section class="content"><div class="row"><div class="col-md-8">
   <div class="box box-primary">
      <div class="box-header with-border">
         <h3 class="box-title"><?= _ent($retur->nomor_retur); ?> <span class="label label-<?= $retur->status=='confirmed'?'success':'default'; ?>"><?= strtoupper($retur->status); ?></span></h3>
         <div class="box-tools pull-right"> <a href="<?= site_url('administrator/retur_pembelian/cetak/' . $retur->id); ?>" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-print"></i> Cetak PDF</a> 
            <?php if($retur->status=='draft'): ?><?php is_allowed('retur_pembelian_update', function() use ($retur){ ?><a href="<?= site_url('administrator/retur_pembelian/konfirmasi/'.$retur->id); ?>" class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi? Stok akan dikurangi.')"><i class="fa fa-check"></i> Konfirmasi</a><?php }); ?><?php endif; ?>
            <a href="<?= base_url('administrator/retur_pembelian'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
         </div>
      </div>
      <div class="box-body">
         <table class="table table-condensed"><tr><th width="30%">No Retur</th><td><?= _ent($retur->nomor_retur); ?></td></tr><tr><th>Penerimaan</th><td><?= _ent($retur->nomor_penerimaan??'-'); ?></td></tr><tr><th>Supplier</th><td><strong><?= _ent($retur->nama_supplier); ?></strong></td></tr><tr><th>Gudang Asal</th><td><?= _ent($retur->nama_gudang); ?><br><small class="text-muted"><i class="fa fa-map-marker"></i> <?= _ent($retur->alamat_gudang ?: 'Alamat tidak tersedia'); ?></small></td></tr><tr><th>Tanggal Retur</th><td><?= date('d/m/Y',strtotime($retur->tanggal_retur)); ?></td></tr><tr><th>Alasan</th><td><?= _ent($retur->alasan); ?></td></tr><tr><th>Total</th><td><strong class="text-danger"><?= _ent($retur->mata_uang ?? 'Rp'); ?> <?= fmod((float)$retur->total, 1) !== 0.0 ? number_format($retur->total, 2, ',', '.') : number_format($retur->total, 0, ',', '.'); ?></strong> <?php if(($retur->mata_uang ?? 'IDR') !== 'IDR' && !empty($retur->kurs)): ?><br><small class="text-muted">Estimasi Harga IDR: Rp <?= number_format($retur->total * $retur->kurs, 0, ',', '.'); ?></small><?php endif; ?></td></tr></table>
         <hr><h4>Item Retur</h4>
         <table class="table table-striped table-sm"><thead><tr><th>#</th><th>Produk</th><th>Qty Retur</th><th>Harga</th><th>Subtotal</th><th>Alasan</th></tr></thead>
            <tbody><?php foreach($items as $i=>$it): ?><tr><td><?= $i+1; ?></td><td><?= _ent($it->nama_produk); ?></td><td><?= number_format((float)$it->qty_retur, 0, ',', '.'); ?></td><td><?= _ent($retur->mata_uang ?? 'Rp'); ?> <?= fmod((float)$it->harga_satuan, 1) !== 0.0 ? number_format($it->harga_satuan, 2, ',', '.') : number_format($it->harga_satuan, 0, ',', '.'); ?></td><td><?= _ent($retur->mata_uang ?? 'Rp'); ?> <?= fmod((float)$it->subtotal, 1) !== 0.0 ? number_format($it->subtotal, 2, ',', '.') : number_format($it->subtotal, 0, ',', '.'); ?></td><td><?= _ent($it->alasan); ?></td></tr><?php endforeach; ?></tbody>
         </table>
      </div>
   </div>
</div></div></section>
