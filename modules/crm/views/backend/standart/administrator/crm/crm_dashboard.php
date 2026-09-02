<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<style>
:root {
  --crm-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #312e81 100%);
  --card-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04);
}

.crm-dash-header {
  background: var(--crm-gradient);
  color: #fff;
  padding: 30px 35px;
  border-radius: 12px;
  margin-bottom: 25px;
  box-shadow: 0 10px 30px rgba(49, 46, 129, 0.25);
  position: relative;
  overflow: hidden;
}
.crm-dash-header::after {
  content: '';
  position: absolute;
  right: -50px;
  bottom: -50px;
  width: 250px;
  height: 250px;
  background: rgba(255, 255, 255, 0.04);
  border-radius: 50%;
  pointer-events: none;
}
.crm-dash-header h2 {
  margin: 0 0 8px 0;
  font-weight: 800;
  font-size: 26px;
  letter-spacing: -0.5px;
}
.crm-dash-header p {
  margin: 0;
  opacity: 0.85;
  font-size: 14px;
}

/* KPI Cards */
.kpi-card {
  background: #fff;
  border-radius: 12px;
  padding: 20px 22px;
  box-shadow: var(--card-shadow);
  margin-bottom: 20px;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(0,0,0,0.04);
}
.kpi-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.12);
}
.kpi-card .kpi-icon {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  margin-bottom: 12px;
}
.kpi-card .kpi-value {
  font-size: 26px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.2;
}
.kpi-card .kpi-label {
  font-size: 12px;
  color: #64748b;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-top: 4px;
}

.kpi-blue .kpi-icon { background: #e0f2fe; color: #0284c7; }
.kpi-purple .kpi-icon { background: #f3e8ff; color: #9333ea; }
.kpi-amber .kpi-icon { background: #fef3c7; color: #d97706; }
.kpi-emerald .kpi-icon { background: #dcfce7; color: #16a34a; }
.kpi-rose .kpi-icon { background: #ffe4e6; color: #e11d48; }

/* Chart Container Cards */
.chart-box {
  background: #fff;
  border-radius: 12px;
  padding: 22px;
  box-shadow: var(--card-shadow);
  margin-bottom: 25px;
  border: 1px solid rgba(0,0,0,0.04);
}
.chart-box .chart-title {
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.chart-box .chart-title i {
  margin-right: 8px;
}

/* Custom Leaderboard & List Table */
.leader-table th {
  background: #f8fafc;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #64748b;
  border-bottom: 2px solid #e2e8f0 !important;
}
.leader-table td {
  vertical-align: middle !important;
  font-size: 13px;
}
.avatar-circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #6366f1;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
}

/* Alert badge pills */
.badge-urgent {
  background: #ef4444;
  color: #fff;
  padding: 3px 10px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
}
</style>

<section class="content-header">
  <h1><i class="fa fa-tachometer"></i> Dashboard CRM <small>Analytics & Performance Monitoring</small></h1>
  <ol class="breadcrumb">
    <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="<?= site_url('administrator/crm'); ?>">CRM</a></li>
    <li class="active">Dashboard Analytics</li>
  </ol>
</section>

<section class="content">
  <!-- Header Banner -->
  <div class="crm-dash-header">
    <div class="row" style="align-items: center;">
      <div class="col-md-8">
        <h2><i class="fa fa-chart-line"></i> Dashboard Performa After Sales & CRM</h2>
        <p>Pantau keterlibatan pelanggan, tren pengiriman follow-up, repeat order rate, dan performa tim CS secara real-time.</p>
      </div>
      <div class="col-md-4 text-right" style="padding-top: 10px;">
        <a href="<?= site_url('administrator/crm/journal_followup'); ?>" class="btn btn-sm" style="background:#6366f1; color:#fff; border:none; padding:8px 16px; border-radius:8px; font-weight:600;"><i class="fa fa-tasks"></i> Journal Follow-Up</a>
        <a href="<?= site_url('administrator/crm/broadcast'); ?>" class="btn btn-sm" style="background:#22c55e; color:#fff; border:none; padding:8px 16px; border-radius:8px; font-weight:600;"><i class="fa fa-whatsapp"></i> Broadcast WA</a>
      </div>
    </div>
  </div>

  <!-- KPI Cards Row -->
  <div class="row">
    <div class="col-md-2 col-sm-4 col-xs-6">
      <div class="kpi-card kpi-blue">
        <div class="kpi-icon"><i class="fa fa-users"></i></div>
        <div class="kpi-value"><?= number_format($kpis['total_customers']); ?></div>
        <div class="kpi-label">Total Pelanggan</div>
      </div>
    </div>
    <div class="col-md-3 col-sm-4 col-xs-6">
      <div class="kpi-card kpi-emerald">
        <div class="kpi-icon"><i class="fa fa-money"></i></div>
        <div class="kpi-value">Rp <?= number_format($kpis['total_spend'], 0, ',', '.'); ?></div>
        <div class="kpi-label">Total Volume Transaksi</div>
      </div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6">
      <div class="kpi-card kpi-purple">
        <div class="kpi-icon"><i class="fa fa-refresh"></i></div>
        <div class="kpi-value"><?= $kpis['repeat_rate']; ?>%</div>
        <div class="kpi-label">Repeat Order Rate</div>
      </div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6">
      <div class="kpi-card kpi-amber">
        <div class="kpi-icon"><i class="fa fa-clock-o"></i></div>
        <div class="kpi-value"><?= $kpis['pending_today']; ?> <small style="font-size:12px;color:#d97706;">(<?= $kpis['overdue']; ?> late)</small></div>
        <div class="kpi-label">Pending Hari Ini</div>
      </div>
    </div>
    <div class="col-md-3 col-sm-4 col-xs-6">
      <div class="kpi-card kpi-rose">
        <div class="kpi-icon"><i class="fa fa-paper-plane"></i></div>
        <div class="kpi-value"><?= number_format($kpis['total_broadcast']); ?></div>
        <div class="kpi-label">WA Broadcast Terkirim</div>
      </div>
    </div>
  </div>

  <!-- Charts Row 1 -->
  <div class="row">
    <!-- Trend Follow-Up (15 Hari) -->
    <div class="col-md-8">
      <div class="chart-box">
        <div class="chart-title">
          <span><i class="fa fa-line-chart text-indigo" style="color:#6366f1;"></i> Tren Tugas Follow-Up vs WA Broadcast Terkirim</span>
          <small class="text-muted">15 Hari Terakhir</small>
        </div>
        <div style="height: 280px; position: relative;">
          <canvas id="chartTrend"></canvas>
        </div>
      </div>
    </div>

    <!-- Distribusi Sumber Penjualan -->
    <div class="col-md-4">
      <div class="chart-box">
        <div class="chart-title">
          <span><i class="fa fa-pie-chart" style="color:#0ea5e9;"></i> Customer per Channel</span>
        </div>
        <div style="height: 280px; position: relative;">
          <canvas id="chartChannels"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row 2 & Leaderboards -->
  <div class="row">
    <!-- Breakdown Status Follow-Up per Kategori -->
    <div class="col-md-6">
      <div class="chart-box">
        <div class="chart-title">
          <span><i class="fa fa-bar-chart" style="color:#8b5cf6;"></i> Status Follow-Up per Kategori H+</span>
        </div>
        <div style="height: 280px; position: relative;">
          <canvas id="chartCategoryStatus"></canvas>
        </div>
      </div>
    </div>

    <!-- Top 5 Champions (Repeat Order Leaders) -->
    <div class="col-md-6">
      <div class="chart-box">
        <div class="chart-title">
          <span><i class="fa fa-trophy" style="color:#eab308;"></i> Top 5 Pelanggan Repeat Order</span>
          <a href="<?= site_url('administrator/crm'); ?>" class="btn btn-xs btn-default">Lihat Semua</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover leader-table" style="margin:0;">
            <thead>
              <tr>
                <th>Pelanggan</th>
                <th>Sumber</th>
                <th class="text-center">Total Order</th>
                <th>Order Terakhir</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($top_repeat)): ?>
              <tr><td colspan="5" class="text-center text-muted">Belum ada data pelanggan.</td></tr>
              <?php else: foreach ($top_repeat as $tr): ?>
              <tr>
                <td>
                  <div style="display:flex; align-items:center;">
                    <div class="avatar-circle" style="margin-right:10px; background: <?= sprintf('#%06X', mt_rand(0, 0xFFFFFF)); ?>;">
                      <?= strtoupper(substr($tr->customer_nama, 0, 1)); ?>
                    </div>
                    <div>
                      <strong><?= htmlspecialchars($tr->customer_nama); ?></strong><br>
                      <small class="text-muted"><?= htmlspecialchars($tr->customer_phone); ?></small>
                    </div>
                  </div>
                </td>
                <td><small><?= htmlspecialchars($tr->sources); ?></small></td>
                <td class="text-center">
                  <span class="badge" style="background:#6366f1; font-size:12px; font-weight:700;"><?= $tr->total_orders; ?>x Order</span>
                </td>
                <td><small><?= date('d/m/Y', strtotime($tr->last_order)); ?></small></td>
                <td class="text-center">
                  <a href="<?= site_url('administrator/crm/customer_detail?phone=' . urlencode($tr->customer_phone)); ?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> Detail</a>
                </td>
              </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Urgent Action Alerts & Activity Feed Row -->
  <div class="row">
    <!-- Urgent Action List -->
    <div class="col-md-6">
      <div class="chart-box" style="border-left: 4px solid #ef4444;">
        <div class="chart-title text-danger">
          <span><i class="fa fa-exclamation-triangle"></i> Membutuhkan Perhatian CS Segera</span>
          <span class="badge badge-urgent"><?= count($urgent_alerts); ?> Item</span>
        </div>
        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
          <table class="table table-hover table-condensed" style="font-size:12px; margin:0;">
            <thead>
              <tr style="background:#fff5f5;">
                <th>Customer</th>
                <th>Kategori</th>
                <th>Target Tgl</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($urgent_alerts)): ?>
              <tr><td colspan="5" class="text-center text-muted" style="padding:20px;"><i class="fa fa-check-circle text-success"></i> Tidak ada komplain / overdue urgent saat ini!</td></tr>
              <?php else: foreach ($urgent_alerts as $ua): ?>
              <tr>
                <td><strong><?= htmlspecialchars($ua->customer_nama); ?></strong><br><small><?= htmlspecialchars($ua->customer_phone); ?></small></td>
                <td><span class="label label-info"><?= htmlspecialchars($ua->nama_kategori); ?></span></td>
                <td><span style="color:#ef4444; font-weight:700;"><?= date('d/m/Y', strtotime($ua->tanggal_label_muncul)); ?></span></td>
                <td>
                  <span class="label label-danger"><?= $ua->status_label; ?></span>
                </td>
                <td class="text-center">
                  <a href="<?= site_url('administrator/crm/journal_followup?q=' . urlencode($ua->customer_phone)); ?>" class="btn btn-xs btn-warning"><i class="fa fa-share"></i> Follow Up</a>
                </td>
              </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Recent Activity Feed -->
    <div class="col-md-6">
      <div class="chart-box">
        <div class="chart-title">
          <span><i class="fa fa-history" style="color:#10b981;"></i> Aktivitas CS & Broadcast Terbaru</span>
        </div>
        <div style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
          <?php if (empty($recent_activities)): ?>
          <p class="text-center text-muted" style="padding:20px;">Belum ada riwayat aktivitas terbaru.</p>
          <?php else: foreach ($recent_activities as $act): ?>
          <div style="display:flex; align-items:flex-start; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size:12px;">
            <div style="margin-right:12px; font-size:16px; color: <?= $act->type === 'broadcast' ? '#22c55e' : '#3b82f6'; ?>;">
              <i class="fa <?= $act->type === 'broadcast' ? 'fa-whatsapp' : 'fa-commenting-o'; ?>"></i>
            </div>
            <div style="flex:1;">
              <strong><?= htmlspecialchars($act->user); ?></strong> <?= $act->type === 'broadcast' ? 'mengirim WA' : 'menambahkan catatan'; ?> ke <strong><?= htmlspecialchars($act->customer_nama); ?></strong>
              <div style="color:#64748b; font-size:11px; margin-top:2px;"><?= htmlspecialchars(mb_strimwidth($act->detail, 0, 80, '...')); ?></div>
            </div>
            <div style="font-size:10px; color:#94a3b8; white-space:nowrap; margin-left:10px;">
              <?= date('d/m H:i', strtotime($act->waktu)); ?>
            </div>
          </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Chart.js Logic -->
<script>
$(document).ready(function() {
  // Chart 1: Trend
  var trendData = <?= json_encode($trend); ?>;
  var ctxTrend = document.getElementById('chartTrend').getContext('2d');
  new Chart(ctxTrend, {
    type: 'line',
    data: {
      labels: trendData.dates,
      datasets: [
        {
          label: 'Target Follow-Up Label',
          data: trendData.labels,
          borderColor: '#6366f1',
          backgroundColor: 'rgba(99, 102, 241, 0.1)',
          tension: 0.3,
          fill: true,
          pointRadius: 4
        },
        {
          label: 'WA Broadcast Terkirim',
          data: trendData.broadcast,
          borderColor: '#22c55e',
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          tension: 0.3,
          fill: true,
          pointRadius: 4
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'top' } },
      scales: { y: { beginAtZero: true } }
    }
  });

  // Chart 2: Channels
  var channelData = <?= json_encode($channels); ?>;
  var ctxChannels = document.getElementById('chartChannels').getContext('2d');
  new Chart(ctxChannels, {
    type: 'doughnut',
    data: {
      labels: Object.keys(channelData),
      datasets: [{
        data: Object.values(channelData),
        backgroundColor: ['#1e293b', '#ee4d2d', '#42b549', '#010101'],
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom' } }
    }
  });

  // Chart 3: Category Status Breakdown
  var catStatusData = <?= json_encode($category_status); ?>;
  var catLabels = catStatusData.map(function(item) { return item.kategori; });
  var dataPending = catStatusData.map(function(item) { return item.pending; });
  var dataDikirim = catStatusData.map(function(item) { return item.dikirim; });
  var dataSelesai = catStatusData.map(function(item) { return item.selesai; });

  var ctxCatStatus = document.getElementById('chartCategoryStatus').getContext('2d');
  new Chart(ctxCatStatus, {
    type: 'bar',
    data: {
      labels: catLabels,
      datasets: [
        { label: 'Pending', data: dataPending, backgroundColor: '#f59e0b' },
        { label: 'Dikirim/Direspon', data: dataDikirim, backgroundColor: '#3b82f6' },
        { label: 'Selesai', data: dataSelesai, backgroundColor: '#10b981' }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'top' } },
      scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
    }
  });
});
</script>
