<?php /* gudang_dashboard.php - Real-time Warehouse Dashboard */ ?>
<style>
.kpi-card { background:#fff; border-radius:8px; padding:18px; margin-bottom:15px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.05); position:relative; }
.kpi-title { font-size:11px; text-transform:uppercase; color:#64748b; font-weight:600; }
.kpi-value { font-size:22px; font-weight:700; color:#1e293b; margin:4px 0; }
.kpi-sub { font-size:11px; color:#94a3b8; }
.kpi-icon { position:absolute; right:15px; top:18px; font-size:32px; opacity:0.15; }
</style>

<section class="content-header">
   <h1>Dashboard Real-time Warehouse <small>Monitoring Stok, Inbound GRN, Outbound Sales &amp; Warning Minimum</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li>Warehouse</li>
      <li class="active">Dashboard Real-time</li>
   </ol>
</section>

<section class="content">
   <!-- Summary KPI Cards -->
   <div class="row">
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 5px solid #3b82f6;">
            <div class="kpi-title">TOTAL STOK FISIK AKTIF</div>
            <div class="kpi-value text-primary"><?= number_format($total_stok, 0, ',', '.'); ?> Unit</div>
            <div class="kpi-sub"><i class="fa fa-cubes text-primary"></i> Seluruh Gudang &amp; Rak</div>
            <i class="fa fa-cubes kpi-icon text-primary"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 5px solid #10b981;">
            <div class="kpi-title">TOTAL BARANG MASUK (INBOUND)</div>
            <div class="kpi-value text-success">Rp <?= number_format($barang_masuk, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-download text-success"></i> Penerimaan GRN Pembelian</div>
            <i class="fa fa-download kpi-icon text-success"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 5px solid #f59e0b;">
            <div class="kpi-title">TOTAL BARANG KELUAR (OUTBOUND)</div>
            <div class="kpi-value text-warning">Rp <?= number_format($barang_keluar, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-upload text-warning"></i> Pengiriman Sales Order</div>
            <i class="fa fa-upload kpi-icon text-warning"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 5px solid #ef4444;">
            <div class="kpi-title">STOK RUSAK / QUARANTINE</div>
            <div class="kpi-value text-danger"><?= number_format($stok_rusak, 0, ',', '.'); ?> Unit</div>
            <div class="kpi-sub" style="color:#ef4444;"><i class="fa fa-ban"></i> Barang Retur Defect</div>
            <i class="fa fa-ban kpi-icon text-danger"></i>
         </div>
      </div>
   </div>

   <!-- Warning Produk Stok Minimum -->
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning" style="border-radius:6px;">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-exclamation-triangle text-warning"></i> Warning Stok Minimum (Trigger Purchase Reorder Auto)</h3>
               <div class="box-tools pull-right">
                  <a href="<?= site_url('administrator/purchase_order/add'); ?>" class="btn btn-warning btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-plus-circle"></i> Buat Reorder PO</a>
               </div>
            </div>
            <div class="box-body">
               <table class="table table-bordered table-striped table-hover align-middle">
                  <thead>
                     <tr style="background:#fffbebf0; color:#854d0e; font-size:12px;">
                        <th width="40" class="text-center">No</th>
                        <th>Kode Barang</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Lokasi Gudang Spesifik</th>
                        <th class="text-center">Stok Gudang Ini</th>
                        <th class="text-center">Batas Minimum</th>
                        <th class="text-center">Status &amp; Tindakan</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php $no = 1; foreach($produk_minimum as $pm): ?>
                     <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><span class="label label-default"><?= _ent($pm->kode_produk); ?></span></td>
                        <td><strong><?= _ent($pm->nama_produk); ?></strong></td>
                        <td><?= _ent($pm->nama_kategori ?: 'Umum'); ?></td>
                        <td><span class="label label-primary"><i class="fa fa-building"></i> <?= _ent($pm->nama_gudang); ?></span></td>
                        <td class="text-center"><strong class="text-danger" style="font-size:14px;"><?= number_format($pm->qty, 0, ',', '.'); ?> Unit</strong></td>
                        <td class="text-center"><strong><?= number_format($pm->stok_minimum, 0, ',', '.'); ?> Unit</strong></td>
                        <td class="text-center">
                           <a href="<?= site_url('administrator/purchase_order/add?gudang_id=' . $pm->gudang_id); ?>" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-shopping-cart"></i> Reorder Gudang Ini</a>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                     <?php if(empty($produk_minimum)): ?>
                     <tr><td colspan="8" class="text-center text-muted" style="padding:20px;"><i class="fa fa-check-circle text-success fa-2x"></i><br>Seluruh produk di semua lokasi gudang dalam kondisi stok aman.</td></tr>
                     <?php endif; ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</section>
