<?php /* pelanggan_view.php - Customer Analytics & Repeat Purchase History */ ?>
<section class="content-header">
   <h1>Detail &amp; Analisis Pelanggan <small><?= _ent($pelanggan->nama_pelanggan); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/pelanggan'); ?>">Pelanggan</a></li>
      <li class="active">Detail &amp; Analisis</li>
   </ol>
</section>

<section class="content">
   <!-- Top Analytics Summary Cards -->
   <div class="row">
      <div class="col-md-4 col-sm-6">
         <div class="small-box bg-aqua" style="border-radius:6px;">
            <div class="inner">
               <h3>Rp <?= number_format($total_volume, 0, ',', '.'); ?></h3>
               <p>Total Volume Transaksi (Lifetime)</p>
            </div>
            <div class="icon"><i class="fa fa-line-chart"></i></div>
         </div>
      </div>
      <div class="col-md-4 col-sm-6">
         <div class="small-box bg-green" style="border-radius:6px;">
            <div class="inner">
               <h3><?= count($riwayat_so); ?> Kali</h3>
               <p>Frekuensi Pembelian Ulang (Repeat Order)</p>
            </div>
            <div class="icon"><i class="fa fa-refresh"></i></div>
         </div>
      </div>
      <div class="col-md-4 col-sm-6">
         <div class="small-box bg-purple" style="border-radius:6px;">
            <div class="inner">
               <h3>Rp <?= number_format($pelanggan->limit_kredit, 0, ',', '.'); ?></h3>
               <p>Limit Kredit Belanja</p>
            </div>
            <div class="icon"><i class="fa fa-credit-card"></i></div>
         </div>
      </div>
   </div>

   <div class="row">
      <!-- Sisi Kiri: Profil Pelanggan -->
      <div class="col-md-5">
         <div class="box box-primary" style="border-radius:6px;">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-user-circle text-primary"></i> Informasi Profil Pelanggan</h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('pelanggan_update', function() use ($pelanggan){ ?>
                     <a href="<?= site_url('administrator/pelanggan/edit/'.$pelanggan->id); ?>" class="btn btn-warning btn-xs btn-flat"><i class="fa fa-edit"></i> Edit</a>
                  <?php }); ?>
                  <a href="<?= base_url('administrator/pelanggan'); ?>" class="btn btn-default btn-xs btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
               </div>
            </div>
            <div class="box-body">
               <table class="table table-striped table-hover">
                  <tr><th width="40%">Kode Pelanggan</th><td><span class="label label-default"><?= _ent($pelanggan->kode_pelanggan); ?></span></td></tr>
                  <tr><th>Nama Pelanggan</th><td><strong><?= _ent($pelanggan->nama_pelanggan); ?></strong></td></tr>
                  <tr><th>Tipe Tier</th><td><span class="badge bg-purple"><?= strtoupper($pelanggan->tipe_pelanggan); ?></span></td></tr>
                  <tr><th>Kontak Person</th><td><?= _ent($pelanggan->kontak_person ?? '-'); ?></td></tr>
                  <tr><th>Telepon</th><td><i class="fa fa-phone text-muted"></i> <?= _ent($pelanggan->telepon ?? '-'); ?></td></tr>
                  <tr><th>Email</th><td><i class="fa fa-envelope text-muted"></i> <?= _ent($pelanggan->email ?? '-'); ?></td></tr>
                  <tr><th>Kota / Wilayah</th><td><?= _ent($pelanggan->kota ?? '-'); ?></td></tr>
                  <tr><th>Alamat Lengkap</th><td><?= nl2br(_ent($pelanggan->alamat ?? '-')); ?></td></tr>
                  <tr><th>NPWP</th><td><?= _ent($pelanggan->npwp ?? '-'); ?></td></tr>
                  <tr><th>Status</th><td><span class="label <?= $pelanggan->status == 'aktif' ? 'label-success' : 'label-danger'; ?>"><?= ucfirst($pelanggan->status); ?></span></td></tr>
               </table>
            </div>
         </div>
      </div>

      <!-- Sisi Kanan: Tab Riwayat Transaksi & Pembelian Ulang -->
      <div class="col-md-7">
         <div class="nav-tabs-custom" style="border-radius:6px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <ul class="nav nav-tabs">
               <li class="active"><a href="#tab_so" data-toggle="tab"><i class="fa fa-shopping-cart text-primary"></i> Riwayat Sales Order (<?= count($riwayat_so); ?>)</a></li>
               <li><a href="#tab_invoice" data-toggle="tab"><i class="fa fa-file-text-o text-success"></i> Riwayat Invoice (<?= count($riwayat_invoice); ?>)</a></li>
            </ul>
            <div class="tab-content">
               <!-- Tab SO -->
               <div class="tab-pane active" id="tab_so">
                  <table class="table table-bordered table-striped">
                     <thead>
                        <tr style="background:#f8fafc;">
                           <th>No. SO</th>
                           <th>Tanggal</th>
                           <th class="text-right">Total Transaksi</th>
                           <th class="text-center">Status Kirim</th>
                           <th class="text-center">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach($riwayat_so as $so): ?>
                        <tr>
                           <td><strong class="text-primary"><?= _ent($so->nomor_so); ?></strong></td>
                           <td><?= date('d/m/Y', strtotime($so->tanggal_so)); ?></td>
                           <td class="text-right"><strong>Rp <?= number_format($so->total, 0, ',', '.'); ?></strong></td>
                           <td class="text-center">
                              <span class="label label-info"><?= strtoupper($so->status_kirim ?? 'DIPROSES'); ?></span>
                           </td>
                           <td class="text-center">
                              <a href="<?= site_url('administrator/sales_order/view/'.$so->id); ?>" class="btn btn-default btn-xs"><i class="fa fa-eye"></i> Detail</a>
                           </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($riwayat_so)): ?><tr><td colspan="5" class="text-center text-muted">Belum ada riwayat Sales Order.</td></tr><?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <!-- Tab Invoice -->
               <div class="tab-pane" id="tab_invoice">
                  <table class="table table-bordered table-striped">
                     <thead>
                        <tr style="background:#f8fafc;">
                           <th>No. Invoice</th>
                           <th>Tanggal</th>
                           <th class="text-right">Total Tagihan</th>
                           <th class="text-center">Status Bayar</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach($riwayat_invoice as $inv): ?>
                        <tr>
                           <td><strong class="text-success"><?= _ent($inv->nomor_invoice); ?></strong></td>
                           <td><?= date('d/m/Y', strtotime($inv->tanggal_invoice)); ?></td>
                           <td class="text-right"><strong>Rp <?= number_format($inv->total, 0, ',', '.'); ?></strong></td>
                           <td class="text-center">
                              <span class="label <?= $inv->status == 'paid' ? 'label-success' : 'label-warning'; ?>"><?= strtoupper($inv->status); ?></span>
                           </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($riwayat_invoice)): ?><tr><td colspan="4" class="text-center text-muted">Belum ada riwayat Invoice.</td></tr><?php endif; ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
