<?php /* keuangan_dashboard.php - Financial Overview Dashboard */ ?>
<style>
.kpi-card { background:#fff; border-radius:8px; padding:18px; margin-bottom:15px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.05); position:relative; }
.kpi-title { font-size:11px; text-transform:uppercase; color:#64748b; font-weight:600; }
.kpi-value { font-size:22px; font-weight:700; color:#1e293b; margin:4px 0; }
.kpi-sub { font-size:11px; color:#94a3b8; }
.kpi-icon { position:absolute; right:15px; top:18px; font-size:32px; opacity:0.15; }
</style>

<section class="content-header">
   <h1>Dashboard Keuangan &amp; Arus Kas <small>Ringkasan Kas Masuk, Kas Keluar, Utang &amp; Piutang</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Keuangan</li>
   </ol>
</section>

<section class="content">
   <!-- Summary KPI Cards -->
   <div class="row">
      <div class="col-md-4 col-sm-6">
         <div class="kpi-card" style="border-left: 5px solid #10b981;">
            <div class="kpi-title">KAS MASUK (BKM)</div>
            <div class="kpi-value text-success">Rp <?= number_format($summary['kas_masuk'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-arrow-down text-success"></i> Total Penerimaan Piutang Customer</div>
            <i class="fa fa-download kpi-icon text-success"></i>
         </div>
      </div>
      <div class="col-md-4 col-sm-6">
         <div class="kpi-card" style="border-left: 5px solid #ef4444;">
            <div class="kpi-title">KAS KELUAR (BKK)</div>
            <div class="kpi-value text-danger">Rp <?= number_format($summary['kas_keluar'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-arrow-up text-danger"></i> Total Pembayaran Utang Supplier</div>
            <i class="fa fa-upload kpi-icon text-danger"></i>
         </div>
      </div>
      <div class="col-md-4 col-sm-6">
         <div class="kpi-card" style="border-left: 5px solid #3b82f6;">
            <div class="kpi-title">NET SALDO KAS OPERASIONAL</div>
            <div class="kpi-value text-primary">Rp <?= number_format($summary['saldo_kas'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-bank"></i> Saldo Kas Terkumpul</div>
            <i class="fa fa-university kpi-icon text-primary"></i>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-6">
         <div class="kpi-card" style="border-left: 5px solid #f59e0b;">
            <div class="kpi-title">TOTAL KEWAJIBAN UTANG (AP)</div>
            <div class="kpi-value text-warning">Rp <?= number_format($summary['utang_aktif'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-exclamation-triangle"></i> Saldo Kewajiban Belum Lunas ke Supplier</div>
            <i class="fa fa-calculator kpi-icon text-warning"></i>
         </div>
      </div>
      <div class="col-md-6">
         <div class="kpi-card" style="border-left: 5px solid #8b5cf6;">
            <div class="kpi-title">TOTAL HAK PIUTANG (AR)</div>
            <div class="kpi-value" style="color:#8b5cf6;">Rp <?= number_format($summary['piutang_aktif'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-clock-o"></i> Tagihan Belum Diterima dari Pelanggan</div>
            <i class="fa fa-file-text-o kpi-icon" style="color:#8b5cf6;"></i>
         </div>
      </div>
   </div>

   <!-- Navigation Quick Buttons -->
   <div class="row" style="margin-bottom:15px;">
      <div class="col-md-3">
         <a href="<?= site_url('administrator/keuangan/utang'); ?>" class="btn btn-primary btn-block btn-flat" style="padding:12px; font-weight:600; border-radius:4px;">
            <i class="fa fa-book"></i> Utang Usaha (AP)
         </a>
      </div>
      <div class="col-md-3">
         <a href="<?= site_url('administrator/keuangan/kas_keluar'); ?>" class="btn btn-danger btn-block btn-flat" style="padding:12px; font-weight:600; border-radius:4px;">
            <i class="fa fa-upload"></i> Pengeluaran Kas (BKK)
         </a>
      </div>
      <div class="col-md-3">
         <a href="<?= site_url('administrator/keuangan/piutang'); ?>" class="btn btn-warning btn-block btn-flat" style="padding:12px; font-weight:600; border-radius:4px;">
            <i class="fa fa-file-text"></i> Piutang Usaha (AR)
         </a>
      </div>
      <div class="col-md-3">
         <a href="<?= site_url('administrator/keuangan/kas_masuk'); ?>" class="btn btn-success btn-block btn-flat" style="padding:12px; font-weight:600; border-radius:4px;">
            <i class="fa fa-download"></i> Penerimaan Kas (BKM)
         </a>
      </div>
   </div>

   <!-- Recent Transactions -->
   <div class="row">
      <div class="col-md-6">
         <div class="box box-danger" style="border-radius:6px;">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-upload text-danger"></i> Transaksi Kas Keluar Terakhir (BKK)</h3>
            </div>
            <div class="box-body">
               <table class="table table-striped table-hover">
                  <thead><tr><th>No. BKK</th><th>Supplier</th><th>Tgl</th><th class="text-right">Nominal</th></tr></thead>
                  <tbody>
                     <?php foreach($bkk_recent as $b): ?>
                     <tr>
                        <td><strong class="text-danger"><?= _ent($b->nomor_bkk); ?></strong></td>
                        <td><?= _ent($b->nama_supplier); ?></td>
                        <td><?= date('d/m/Y', strtotime($b->tanggal_bayar)); ?></td>
                        <td class="text-right"><strong class="text-danger">Rp <?= number_format($b->nominal, 0, ',', '.'); ?></strong></td>
                     </tr>
                     <?php endforeach; ?>
                     <?php if(empty($bkk_recent)): ?><tr><td colspan="4" class="text-center text-muted">Belum ada kas keluar</td></tr><?php endif; ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="box box-success" style="border-radius:6px;">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-download text-success"></i> Transaksi Kas Masuk Terakhir (BKM)</h3>
            </div>
            <div class="box-body">
               <table class="table table-striped table-hover">
                  <thead><tr><th>No. BKM</th><th>Pelanggan</th><th>Tgl</th><th class="text-right">Nominal</th></tr></thead>
                  <tbody>
                     <?php foreach($bkm_recent as $b): ?>
                     <tr>
                        <td><strong class="text-success"><?= _ent($b->nomor_bkm); ?></strong></td>
                        <td><?= _ent($b->nama_pelanggan); ?></td>
                        <td><?= date('d/m/Y', strtotime($b->tanggal_bayar)); ?></td>
                        <td class="text-right"><strong class="text-success">Rp <?= number_format($b->nominal, 0, ',', '.'); ?></strong></td>
                     </tr>
                     <?php endforeach; ?>
                     <?php if(empty($bkm_recent)): ?><tr><td colspan="4" class="text-center text-muted">Belum ada kas masuk</td></tr><?php endif; ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</section>
