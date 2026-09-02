<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
.crm-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); color: #fff; padding: 25px 30px; border-radius: 8px; margin-bottom: 20px; }
.crm-header h2 { margin: 0 0 5px 0; font-weight: 700; font-size: 22px; }
.crm-header p { margin: 0; opacity: 0.8; font-size: 13px; }
.stat-card { background: #fff; border-radius: 8px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-left: 4px solid #3498db; margin-bottom: 15px; }
.stat-card .stat-num { font-size: 26px; font-weight: 700; color: #2c3e50; }
.stat-card .stat-label { font-size: 12px; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.5px; }
.badge-source { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; color: #fff; }
.badge-toko { background: #2c3e50; }
.badge-shopee { background: #ee4d2d; }
.badge-tokopedia { background: #42b549; }
.badge-tiktok { background: #010101; }
.table-crm th { background: #f8f9fa; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; border-bottom: 2px solid #dee2e6 !important; }
.table-crm td { vertical-align: middle !important; font-size: 13px; }
.pending-badge { background: #e74c3c; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.btn-wa { background: #25D366; color: #fff; border: none; padding: 4px 12px; border-radius: 5px; font-size: 12px; }
.btn-wa:hover { background: #128C7E; color: #fff; }

@media (max-width: 767px) {
  .crm-header { padding: 18px 15px; text-align: center; }
  .crm-header h2 { font-size: 18px; }
  .table-crm td, .table-crm th { font-size: 12px; padding: 8px 6px !important; }
  .badge-source { font-size: 10px; padding: 2px 6px; }
}
</style>

<section class="content-header">
  <h1><i class="fa fa-handshake-o"></i> CRM & After Sales <small>Customer Directory</small></h1>
  <ol class="breadcrumb">
    <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li class="active">CRM</li>
  </ol>
</section>

<section class="content">
  <div class="crm-header">
    <h2><i class="fa fa-users"></i> Customer Directory</h2>
    <p>Daftar pelanggan otomatis dari seluruh sumber penjualan — Toko, Shopee, Tokopedia, TikTok Shop</p>
  </div>

  <!-- Search & Filter -->
  <div class="box box-default">
    <div class="box-body">
      <form method="get" action="<?= site_url('administrator/crm'); ?>">
        <div class="row">
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-search"></i></span>
              <input type="text" name="q" class="form-control" placeholder="Cari nama customer atau nomor HP..." value="<?= htmlspecialchars($filter ?? ''); ?>">
            </div>
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
            <a href="<?= site_url('administrator/crm'); ?>" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
            <a href="<?= site_url('administrator/crm/export_customers?q=' . urlencode($filter ?? '')); ?>" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Export CSV</a>
            <a href="<?= site_url('administrator/crm/journal_followup'); ?>" class="btn btn-warning"><i class="fa fa-tasks"></i> Journal</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Customer Table -->
  <div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-address-book"></i> Daftar Customer (<?= number_format($customer_counts); ?> total)</h3>
    </div>
    <div class="box-body table-responsive no-padding">
      <table class="table table-hover table-striped table-crm">
        <thead>
          <tr>
            <th style="width:40px">#</th>
            <th>Customer</th>
            <th>No. HP</th>
            <th>Sumber Penjualan</th>
            <th class="text-center">Total Order</th>
            <th>Transaksi Terakhir</th>
            <th class="text-center">Pending Follow-Up</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($customers)): ?>
          <tr><td colspan="8" class="text-center" style="padding:40px;color:#95a5a6;"><i class="fa fa-inbox fa-2x"></i><br>Belum ada data customer.</td></tr>
          <?php else: ?>
          <?php $no = ($this->uri->segment(4) ?: 0) + 1; foreach ($customers as $c): ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><strong><?= htmlspecialchars($c->customer_nama); ?></strong></td>
            <td>
              <i class="fa fa-phone" style="color:#27ae60;"></i> <?= htmlspecialchars($c->customer_phone); ?>
            </td>
            <td>
              <?php
              $sources = explode(', ', $c->sumber_penjualan);
              foreach ($sources as $s):
                $s = trim($s);
                $cls = 'badge-toko';
                if (stripos($s, 'Shopee') !== false) $cls = 'badge-shopee';
                elseif (stripos($s, 'Tokopedia') !== false) $cls = 'badge-tokopedia';
                elseif (stripos($s, 'TikTok') !== false) $cls = 'badge-tiktok';
              ?>
              <span class="badge-source <?= $cls; ?>"><?= $s; ?></span>
              <?php endforeach; ?>
            </td>
            <td class="text-center"><strong><?= $c->total_orders; ?></strong></td>
            <td><?= $c->last_order_date ? date('d M Y', strtotime($c->last_order_date)) : '-'; ?></td>
            <td class="text-center">
              <?php if ($c->pending_followups > 0): ?>
                <span class="pending-badge"><?= $c->pending_followups; ?> pending</span>
              <?php else: ?>
                <span style="color:#27ae60;font-size:12px;"><i class="fa fa-check-circle"></i> Done</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <a href="<?= site_url('administrator/crm/customer_detail?phone=' . urlencode($c->customer_phone)); ?>" class="btn btn-sm btn-info" title="Lihat Detail">
                <i class="fa fa-eye"></i> Detail
              </a>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
    <?php if (!empty($pagination)): ?>
    <div class="box-footer text-center"><?= $pagination; ?></div>
    <?php endif; ?>
  </div>
</section>
