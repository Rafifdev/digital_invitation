<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
.profile-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); color: #fff; padding: 30px; border-radius: 8px; margin-bottom: 20px; }
.profile-header .name { font-size: 24px; font-weight: 700; margin-bottom: 5px; }
.profile-header .phone { font-size: 15px; opacity: 0.9; }
.summary-card { background: #fff; border-radius: 10px; padding: 18px 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); text-align: center; margin-bottom: 15px; transition: transform 0.2s; }
.summary-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
.summary-card .num { font-size: 28px; font-weight: 800; }
.summary-card .lbl { font-size: 11px; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }
.sc-blue .num { color: #2980b9; }
.sc-green .num { color: #27ae60; }
.sc-orange .num { color: #e67e22; }
.sc-purple .num { color: #8e44ad; }
.sc-red .num { color: #e74c3c; }
.badge-source { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; color: #fff; display: inline-block; margin: 2px; }
.badge-toko { background: #2c3e50; }
.badge-shopee { background: #ee4d2d; }
.badge-tokopedia { background: #42b549; }
.badge-tiktok { background: #010101; }
.badge-status { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.bs-shipped { background: #3498db; color: #fff; }
.bs-delivered { background: #27ae60; color: #fff; }
.bs-pending { background: #f39c12; color: #fff; }
.bs-cancelled { background: #e74c3c; color: #fff; }
.bs-completed { background: #2ecc71; color: #fff; }
.bs-processing { background: #9b59b6; color: #fff; }
.timeline-fu { position: relative; padding-left: 30px; margin: 10px 0; }
.timeline-fu::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background: #dee2e6; }
.tl-item { position: relative; margin-bottom: 12px; padding: 10px 14px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #3498db; }
.tl-item.tl-done { border-left-color: #27ae60; }
.tl-item.tl-pending { border-left-color: #f39c12; }
.tl-item .tl-title { font-weight: 700; font-size: 12px; }
.tl-item .tl-date { font-size: 11px; color: #95a5a6; }
.tl-item .tl-status { font-size: 11px; }
.trx-row { cursor: pointer; transition: background 0.2s; }
.trx-row:hover { background: #edf7ff !important; }
.trx-detail-panel { display: none; background: #fafbfc; border-top: 2px solid #3498db; }
.table-crm th { background: #f8f9fa; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; }
.table-crm td { font-size: 13px; vertical-align: middle !important; }
</style>

<section class="content-header">
  <h1><i class="fa fa-user-circle"></i> Detail Customer <small>360° Profile</small></h1>
  <ol class="breadcrumb">
    <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="<?= site_url('administrator/crm'); ?>">CRM</a></li>
    <li class="active">Detail Customer</li>
  </ol>
</section>

<section class="content">
  <!-- Profile Header -->
  <div class="profile-header">
    <div class="row">
      <div class="col-md-1 text-center" style="padding-top: 5px;">
        <i class="fa fa-user-circle-o" style="font-size: 50px; opacity: 0.8;"></i>
      </div>
      <div class="col-md-7">
        <div class="name"><?= htmlspecialchars($summary['nama']); ?></div>
        <div class="phone"><i class="fa fa-phone"></i> <?= htmlspecialchars($summary['phone']); ?></div>
        <div style="margin-top: 8px;">
          <?php foreach ($summary['sources'] as $s):
            $cls = 'badge-toko';
            if (stripos($s, 'Shopee') !== false) $cls = 'badge-shopee';
            elseif (stripos($s, 'Tokopedia') !== false) $cls = 'badge-tokopedia';
            elseif (stripos($s, 'TikTok') !== false) $cls = 'badge-tiktok';
          ?>
          <span class="badge-source <?= $cls; ?>"><?= $s; ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-md-4 text-right" style="padding-top: 10px;">
        <a href="<?= site_url('administrator/crm'); ?>" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);"><i class="fa fa-arrow-left"></i> Kembali</a>
        <a href="<?= site_url('administrator/sales_order/add'); ?>" class="btn btn-sm" style="background:#f39c12;color:#fff;border:none;" title="Buat Sales Order Repeat Order"><i class="fa fa-cart-plus"></i> Repeat Order</a>
        <?php if (!empty($summary['phone'])): ?>
        <a href="https://wa.me/<?= preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $summary['phone'])); ?>" target="_blank" class="btn btn-sm" style="background:#25D366;color:#fff;border:none;"><i class="fa fa-whatsapp"></i> Chat WA</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Summary Cards (FA-04) -->
  <div class="row">
    <div class="col-md-2 col-sm-4 col-xs-6">
      <div class="summary-card sc-blue">
        <div class="num"><?= $summary['total_orders']; ?></div>
        <div class="lbl">Total Order</div>
      </div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6">
      <div class="summary-card sc-green">
        <div class="num">Rp <?= number_format($summary['total_spend'], 0, ',', '.'); ?></div>
        <div class="lbl">Total Spend</div>
      </div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6">
      <div class="summary-card sc-orange">
        <div class="num"><?= $summary['order_count_toko']; ?></div>
        <div class="lbl">Order Toko</div>
      </div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6">
      <div class="summary-card sc-purple">
        <div class="num"><?= $summary['order_count_online']; ?></div>
        <div class="lbl">Order Online</div>
      </div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6">
      <div class="summary-card sc-blue">
        <div class="num"><?= $summary['first_order'] ? date('d/m/y', strtotime($summary['first_order'])) : '-'; ?></div>
        <div class="lbl">Order Pertama</div>
      </div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6">
      <div class="summary-card sc-red">
        <div class="num"><?= $summary['last_order'] ? date('d/m/y', strtotime($summary['last_order'])) : '-'; ?></div>
        <div class="lbl">Order Terakhir</div>
      </div>
    </div>
  </div>

  <!-- Transaction History (FA-01, FA-02) -->
  <div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-history"></i> Riwayat Transaksi (<?= count($transactions); ?>)</h3>
    </div>
    <div class="box-body table-responsive no-padding">
      <table class="table table-hover table-crm" id="tbl-trx">
        <thead>
          <tr>
            <th style="width:30px">#</th>
            <th>Tanggal</th>
            <th>No. Pesanan</th>
            <th>Sumber</th>
            <th>Produk</th>
            <th class="text-right">Nominal</th>
            <th>Status</th>
            <th>Resi</th>
            <th class="text-center">Follow-Up</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($transactions)): ?>
          <tr><td colspan="9" class="text-center" style="padding:40px;color:#95a5a6;"><i class="fa fa-inbox fa-2x"></i><br>Belum ada transaksi.</td></tr>
          <?php else: $no = 1; foreach ($transactions as $t): ?>
          <tr class="trx-row" data-table="<?= $t->tbl; ?>" data-id="<?= $t->id; ?>">
            <td><?= $no++; ?></td>
            <td><?= date('d M Y', strtotime($t->tanggal)); ?></td>
            <td><strong><?= htmlspecialchars($t->no_pesanan ?: $t->nomor_so); ?></strong></td>
            <td>
              <?php
              $cls = 'badge-toko';
              if (stripos($t->sumber_penjualan, 'Shopee') !== false) $cls = 'badge-shopee';
              elseif (stripos($t->sumber_penjualan, 'Tokopedia') !== false) $cls = 'badge-tokopedia';
              elseif (stripos($t->sumber_penjualan, 'TikTok') !== false) $cls = 'badge-tiktok';
              ?>
              <span class="badge-source <?= $cls; ?>"><?= $t->sumber_penjualan; ?></span>
            </td>
            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($t->produk_ringkasan ?: '-'); ?></td>
            <td class="text-right"><strong>Rp <?= number_format($t->total, 0, ',', '.'); ?></strong></td>
            <td>
              <?php
              $st = strtolower($t->status);
              $stcls = 'bs-pending';
              if (in_array($st, ['shipped','dikirim'])) $stcls = 'bs-shipped';
              elseif (in_array($st, ['delivered','terkirim'])) $stcls = 'bs-delivered';
              elseif (in_array($st, ['completed','selesai'])) $stcls = 'bs-completed';
              elseif (in_array($st, ['cancelled','dibatalkan'])) $stcls = 'bs-cancelled';
              elseif (in_array($st, ['processing','confirmed','packing'])) $stcls = 'bs-processing';
              ?>
              <span class="badge-status <?= $stcls; ?>"><?= ucfirst($t->status_pesanan ?: $t->status); ?></span>
            </td>
            <td style="font-size:11px;"><?= htmlspecialchars($t->nomor_resi ?: '-'); ?></td>
            <td class="text-center">
              <button class="btn btn-xs btn-default btn-timeline" data-table="<?= $t->tbl; ?>" data-id="<?= $t->id; ?>" title="Lihat Timeline Follow-Up">
                <i class="fa fa-clock-o"></i>
              </button>
            </td>
          </tr>
          <!-- Detail Panel (FA-03, FA-05) -->
          <tr class="trx-detail-panel" id="detail-<?= $t->tbl; ?>-<?= $t->id; ?>">
            <td colspan="9" style="padding: 15px 25px;">
              <div class="row">
                <div class="col-md-7">
                  <h5 style="font-weight:700;color:#2c3e50;"><i class="fa fa-cubes"></i> Detail Item</h5>
                  <div class="detail-items-<?= $t->tbl; ?>-<?= $t->id; ?>">
                    <p class="text-muted"><i class="fa fa-spinner fa-spin"></i> Memuat...</p>
                  </div>
                  <hr>
                  <div style="font-size:12px;color:#6c757d;">
                    <strong>Penerima:</strong> <?= htmlspecialchars($t->nama_penerima ?: '-'); ?><br>
                    <strong>Alamat:</strong> <?= htmlspecialchars($t->alamat_pengiriman ?: '-'); ?><br>
                    <strong>Ekspedisi:</strong> <?= htmlspecialchars($t->kurir_ekspedisi ?: '-'); ?>
                  </div>
                </div>
                <div class="col-md-5">
                  <h5 style="font-weight:700;color:#2c3e50;"><i class="fa fa-clock-o"></i> Timeline Follow-Up</h5>
                  <div class="timeline-fu timeline-content-<?= $t->tbl; ?>-<?= $t->id; ?>">
                    <p class="text-muted"><i class="fa fa-spinner fa-spin"></i> Memuat...</p>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<script>
$(document).ready(function() {
  // Toggle transaction detail panel
  $('.trx-row').on('click', function() {
    var tbl = $(this).data('table');
    var id = $(this).data('id');
    var panel = $('#detail-' + tbl + '-' + id);
    
    if (panel.is(':visible')) {
      panel.slideUp(200);
      return;
    }
    
    // Close other panels
    $('.trx-detail-panel').slideUp(200);
    panel.slideDown(300);
    
    // Load detail via AJAX (FA-03)
    $.ajax({
      url: BASE_URL + 'administrator/crm/transaction_detail_ajax',
      type: 'GET',
      data: { table: tbl, order_id: id },
      dataType: 'json',
      success: function(res) {
        if (res.success) {
          // Render items
          var itemHtml = '<table class="table table-condensed table-bordered" style="font-size:12px;margin:0;">';
          itemHtml += '<thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>';
          if (res.items.length > 0) {
            $.each(res.items, function(i, item) {
              itemHtml += '<tr>';
              itemHtml += '<td>' + (item.nama_produk || item.catatan || '-') + '</td>';
              itemHtml += '<td class="text-center">' + parseFloat(item.qty) + ' ' + (item.satuan || '') + '</td>';
              itemHtml += '<td class="text-right">Rp ' + parseInt(item.harga_satuan).toLocaleString('id-ID') + '</td>';
              itemHtml += '<td class="text-right">Rp ' + parseInt(item.subtotal).toLocaleString('id-ID') + '</td>';
              itemHtml += '</tr>';
            });
          } else {
            itemHtml += '<tr><td colspan="4" class="text-center text-muted">Tidak ada detail item</td></tr>';
          }
          itemHtml += '</tbody></table>';
          $('.detail-items-' + tbl + '-' + id).html(itemHtml);
          
          // Render timeline (FA-05)
          var tlHtml = '';
          if (res.timeline.length > 0) {
            $.each(res.timeline, function(i, tl) {
              var cls = 'tl-pending';
              if (tl.status_label === 'Selesai' || tl.status_label === 'Sudah Konfirmasi Diterima') cls = 'tl-done';
              tlHtml += '<div class="tl-item ' + cls + '">';
              tlHtml += '<div class="tl-title">' + tl.nama_kategori + ' (H+' + tl.jumlah_hari + ')</div>';
              tlHtml += '<div class="tl-date">Target: ' + tl.tanggal_label_muncul + '</div>';
              tlHtml += '<div class="tl-status">';
              tlHtml += '<span class="label label-' + (tl.status_label === 'Pending' ? 'warning' : tl.status_label === 'Selesai' ? 'success' : 'info') + '">' + tl.status_label + '</span>';
              if (tl.broadcast_count > 0) tlHtml += ' <span class="label label-primary"><i class="fa fa-whatsapp"></i> ' + tl.broadcast_count + 'x</span>';
              tlHtml += '</div>';
              if (tl.notes) tlHtml += '<div style="font-size:11px;color:#6c757d;margin-top:4px;"><i class="fa fa-comment"></i> ' + tl.notes + '</div>';
              tlHtml += '</div>';
            });
          } else {
            tlHtml = '<p class="text-muted" style="font-size:12px;">Belum ada data follow-up</p>';
          }
          $('.timeline-content-' + tbl + '-' + id).html(tlHtml);
        }
      }
    });
  });
});
</script>
