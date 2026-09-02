<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
.crm-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); color: #fff; padding: 25px 30px; border-radius: 8px; margin-bottom: 20px; }
.crm-header h2 { margin: 0 0 5px 0; font-weight: 700; font-size: 22px; }
.crm-header p { margin: 0; opacity: 0.8; font-size: 13px; }
.stat-box { background: #fff; border-radius: 8px; padding: 15px 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-align: center; margin-bottom: 15px; }
.stat-box .num { font-size: 28px; font-weight: 800; }
.stat-box .lbl { font-size: 11px; color: #7f8c8d; text-transform: uppercase; }
.stat-box.sb-red { border-left: 4px solid #e74c3c; }
.stat-box.sb-red .num { color: #e74c3c; }
.stat-box.sb-orange { border-left: 4px solid #f39c12; }
.stat-box.sb-orange .num { color: #f39c12; }
.stat-box.sb-blue { border-left: 4px solid #3498db; }
.stat-box.sb-blue .num { color: #3498db; }
.badge-source { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; color: #fff; }
.badge-toko { background: #2c3e50; }
.badge-shopee { background: #ee4d2d; }
.badge-tokopedia { background: #42b549; }
.badge-tiktok { background: #010101; }
.status-select { padding: 3px 8px; font-size: 12px; border-radius: 4px; border: 1px solid #ddd; }
.btn-wa-sm { background: #25D366; color: #fff; border: none; padding: 3px 10px; border-radius: 4px; font-size: 11px; }
.btn-wa-sm:hover { background: #128C7E; color: #fff; }
.btn-wa-sm.disabled { background: #bdc3c7; cursor: not-allowed; }
.filter-section .form-control { font-size: 13px; height: 34px; }
.table-journal th { background: #f8f9fa; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; }
.table-journal td { font-size: 13px; vertical-align: middle !important; }
.cat-pill { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; margin: 3px; cursor: pointer; border: 2px solid #dee2e6; color: #555; transition: all 0.2s; }
.cat-pill:hover, .cat-pill.active { background: #3498db; color: #fff; border-color: #3498db; }

@media (max-width: 767px) {
  .crm-header { padding: 18px 15px; text-align: center; }
  .crm-header h2 { font-size: 18px; }
  .crm-header .text-right { text-align: center !important; margin-top: 15px; }
  .stat-box { padding: 10px 12px; margin-bottom: 10px; }
  .stat-box .num { font-size: 22px; }
  .stat-box .lbl { font-size: 10px; }
  .filter-section .col-md-2 { margin-bottom: 8px; }
  .cat-pill { padding: 4px 10px; font-size: 11px; }
}
</style>

<section class="content-header">
  <h1><i class="fa fa-tasks"></i> Journal Follow-Up <small>After Sales Monitoring</small></h1>
  <ol class="breadcrumb">
    <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="<?= site_url('administrator/crm'); ?>">CRM</a></li>
    <li class="active">Journal Follow-Up</li>
  </ol>
</section>

<section class="content">
  <div class="crm-header">
    <div class="row">
      <div class="col-md-8">
        <h2><i class="fa fa-tasks"></i> Journal Follow-Up</h2>
        <p>Pantau tahapan after sales — Konfirmasi Pengiriman, H+3 Cek Sampai, H+7 Experience, Custom H+n</p>
      </div>
      <div class="col-md-5 text-right" style="padding-top: 10px;">
        <button class="btn btn-sm" style="background:#8e44ad;color:#fff;border:none;" id="btn-sync-cron" title="Pindai transaksi baru & buat label H+"><i class="fa fa-refresh"></i> Sync Label H+</button>
        <a href="<?= site_url('administrator/crm/export_journal?' . http_build_query($_GET)); ?>" class="btn btn-sm btn-success" title="Export Laporan Follow-Up CSV"><i class="fa fa-file-excel-o"></i> Export CSV</a>
        <a href="<?= site_url('administrator/crm/broadcast'); ?>" class="btn btn-sm" style="background:#25D366;color:#fff;border:none;"><i class="fa fa-bullhorn"></i> Broadcast WA</a>
        <a href="<?= site_url('administrator/crm/followup_setting'); ?>" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);"><i class="fa fa-cogs"></i> Settings</a>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="row">
    <div class="col-md-4">
      <div class="stat-box sb-red">
        <div class="num"><?= $stats['total_pending']; ?></div>
        <div class="lbl">Total Pending</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-box sb-orange">
        <div class="num"><?= $stats['overdue']; ?></div>
        <div class="lbl">Overdue (Lewat Jadwal)</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-box sb-blue">
        <div class="num"><?= $stats['today']; ?></div>
        <div class="lbl">Follow-Up Hari Ini</div>
      </div>
    </div>
  </div>

  <!-- Category Tabs -->
  <div style="margin-bottom: 15px;">
    <a href="<?= site_url('administrator/crm/journal_followup'); ?>" class="cat-pill <?= empty($filters['category_id']) ? 'active' : ''; ?>">Semua</a>
    <?php foreach ($categories as $cat): ?>
    <a href="<?= site_url('administrator/crm/journal_followup?category_id=' . $cat->id); ?>" class="cat-pill <?= ($filters['category_id'] == $cat->id) ? 'active' : ''; ?>">
      <?= htmlspecialchars($cat->nama_kategori); ?> (H+<?= $cat->jumlah_hari; ?>)
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Filters -->
  <div class="box box-default filter-section">
    <div class="box-body">
      <form method="get" action="<?= site_url('administrator/crm/journal_followup'); ?>">
        <?php if (!empty($filters['category_id'])): ?>
        <input type="hidden" name="category_id" value="<?= $filters['category_id']; ?>">
        <?php endif; ?>
        <div class="row">
          <div class="col-md-2">
            <select name="order_source" class="form-control">
              <option value="">Semua Sumber</option>
              <option value="Toko" <?= ($filters['order_source'] ?? '') == 'Toko' ? 'selected' : ''; ?>>Toko</option>
              <option value="Shopee" <?= ($filters['order_source'] ?? '') == 'Shopee' ? 'selected' : ''; ?>>Shopee</option>
              <option value="Tokopedia" <?= ($filters['order_source'] ?? '') == 'Tokopedia' ? 'selected' : ''; ?>>Tokopedia</option>
              <option value="TikTok Shop" <?= ($filters['order_source'] ?? '') == 'TikTok Shop' ? 'selected' : ''; ?>>TikTok Shop</option>
            </select>
          </div>
          <div class="col-md-2">
            <select name="status_label" class="form-control">
              <option value="">Semua Status</option>
              <option value="Pending" <?= ($filters['status_label'] ?? '') == 'Pending' ? 'selected' : ''; ?>>Pending</option>
              <option value="Sudah Dikirim" <?= ($filters['status_label'] ?? '') == 'Sudah Dikirim' ? 'selected' : ''; ?>>Sudah Dikirim</option>
              <option value="Direspon" <?= ($filters['status_label'] ?? '') == 'Direspon' ? 'selected' : ''; ?>>Direspon</option>
              <option value="Belum Respon" <?= ($filters['status_label'] ?? '') == 'Belum Respon' ? 'selected' : ''; ?>>Belum Respon</option>
              <option value="Komplain" <?= ($filters['status_label'] ?? '') == 'Komplain' ? 'selected' : ''; ?>>Komplain</option>
              <option value="Selesai" <?= ($filters['status_label'] ?? '') == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
            </select>
          </div>
          <div class="col-md-2">
            <input type="date" name="date_from" class="form-control" placeholder="Dari tanggal" value="<?= $filters['date_from'] ?? ''; ?>">
          </div>
          <div class="col-md-2">
            <input type="date" name="date_to" class="form-control" placeholder="Sampai tanggal" value="<?= $filters['date_to'] ?? ''; ?>">
          </div>
          <div class="col-md-2">
            <input type="text" name="q" class="form-control" placeholder="Cari nama/HP..." value="<?= htmlspecialchars($filters['search'] ?? ''); ?>">
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-filter"></i> Filter</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Journal Table -->
  <div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-list-alt"></i> Data Follow-Up (<?= number_format($entry_counts); ?> entri)</h3>
      <div class="box-tools">
        <button class="btn btn-sm btn-success" id="btn-broadcast-selected" disabled><i class="fa fa-whatsapp"></i> Broadcast Terpilih</button>
        <button class="btn btn-sm btn-default" id="btn-select-all"><i class="fa fa-check-square-o"></i> Pilih Semua Pending</button>
      </div>
    </div>
    <div class="box-body table-responsive no-padding">
      <table class="table table-hover table-journal">
        <thead>
          <tr>
            <th style="width:30px"><input type="checkbox" id="check-all"></th>
            <th>Customer</th>
            <th>No. HP</th>
            <th>No. Pesanan</th>
            <th>Sumber</th>
            <th>Kategori H+</th>
            <th>Tgl Kirim</th>
            <th>Target Follow-Up</th>
            <th>Status</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($entries)): ?>
          <tr><td colspan="10" class="text-center" style="padding:40px;color:#95a5a6;"><i class="fa fa-check-circle fa-2x text-success"></i><br>Tidak ada follow-up pending saat ini.</td></tr>
          <?php else: foreach ($entries as $e): ?>
          <tr>
            <td>
              <?php if ($e->status_label === 'Pending' && !$e->is_broadcast_sent && !empty($e->customer_phone)): ?>
              <input type="checkbox" class="cb-label" value="<?= $e->id; ?>">
              <?php endif; ?>
            </td>
            <td><strong><?= htmlspecialchars($e->customer_nama); ?></strong></td>
            <td style="font-size:12px;"><i class="fa fa-phone" style="color:#27ae60;"></i> <?= htmlspecialchars($e->customer_phone); ?></td>
            <td style="font-size:12px;"><?= htmlspecialchars($e->no_pesanan ?: '-'); ?></td>
            <td>
              <?php
              $cls = 'badge-toko';
              if (stripos($e->order_source, 'Shopee') !== false) $cls = 'badge-shopee';
              elseif (stripos($e->order_source, 'Tokopedia') !== false) $cls = 'badge-tokopedia';
              elseif (stripos($e->order_source, 'TikTok') !== false) $cls = 'badge-tiktok';
              ?>
              <span class="badge-source <?= $cls; ?>"><?= $e->order_source; ?></span>
            </td>
            <td><span class="label label-info"><?= htmlspecialchars($e->nama_kategori); ?></span></td>
            <td style="font-size:12px;"><?= $e->tanggal_pengiriman ? date('d/m/Y', strtotime($e->tanggal_pengiriman)) : '-'; ?></td>
            <td style="font-size:12px;">
              <?php
              $target = $e->tanggal_label_muncul;
              $is_overdue = (strtotime($target) < strtotime('today') && $e->status_label === 'Pending');
              ?>
              <span style="<?= $is_overdue ? 'color:#e74c3c;font-weight:700;' : ''; ?>"><?= date('d/m/Y', strtotime($target)); ?></span>
              <?php if ($is_overdue): ?><br><small style="color:#e74c3c;"><i class="fa fa-exclamation-triangle"></i> Overdue</small><?php endif; ?>
            </td>
            <td>
              <select class="status-select" data-label-id="<?= $e->id; ?>" data-current="<?= $e->status_label; ?>">
                <?php foreach (['Pending','Sudah Dikirim','Direspon','Sudah Konfirmasi Diterima','Belum Respon','Komplain','Tawarkan Repeat Order','Selesai'] as $st): ?>
                <option value="<?= $st; ?>" <?= ($e->status_label == $st) ? 'selected' : ''; ?>><?= $st; ?></option>
                <?php endforeach; ?>
              </select>
            </td>
            <td class="text-center" style="white-space:nowrap;">
              <?php if (!empty($e->customer_phone)): ?>
              <button class="btn-wa-sm btn-send-wa <?= $e->is_broadcast_sent ? 'disabled' : ''; ?>" data-label-id="<?= $e->id; ?>" <?= $e->is_broadcast_sent ? 'disabled title="Sudah dikirim"' : 'title="Kirim WA"'; ?>>
                <i class="fa fa-whatsapp"></i>
              </button>
              <?php endif; ?>
              <button class="btn btn-xs btn-default btn-add-note" data-label-id="<?= $e->id; ?>" data-nama="<?= htmlspecialchars($e->customer_nama); ?>" title="Tambah Catatan">
                <i class="fa fa-comment"></i>
              </button>
              <a href="<?= site_url('administrator/sales_order/add'); ?>" class="btn btn-xs btn-success" title="Buat Repeat Order (SO)">
                <i class="fa fa-cart-plus"></i>
              </a>
              <a href="<?= site_url('administrator/crm/customer_detail?phone=' . urlencode($e->customer_phone)); ?>" class="btn btn-xs btn-info" title="Profile Customer">
                <i class="fa fa-user"></i>
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

<!-- Modal: Add Note -->
<div class="modal fade" id="modal-note" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#f8f9fa;border-bottom:2px solid #3498db;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-comment"></i> Tambah Catatan Follow-Up</h4>
      </div>
      <div class="modal-body">
        <p><strong>Customer:</strong> <span id="note-customer-name"></span></p>
        <div class="form-group">
          <label>Status:</label>
          <select class="form-control" id="note-status">
            <option value="">-- Tidak ubah status --</option>
            <option value="Direspon">Direspon</option>
            <option value="Sudah Konfirmasi Diterima">Sudah Konfirmasi Diterima</option>
            <option value="Belum Respon">Belum Respon</option>
            <option value="Komplain">Komplain</option>
            <option value="Tawarkan Repeat Order">Tawarkan Repeat Order</option>
            <option value="Selesai">Selesai</option>
          </select>
        </div>
        <div class="form-group">
          <label>Catatan:</label>
          <textarea class="form-control" id="note-text" rows="3" placeholder="Tulis catatan follow-up..."></textarea>
        </div>
        <input type="hidden" id="note-label-id">
        <div id="note-history" style="max-height:200px;overflow-y:auto;margin-top:10px;"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="btn-save-note"><i class="fa fa-save"></i> Simpan</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Sync manual CRON label generator
  $('#btn-sync-cron').on('click', function() {
    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fa fa-spin fa-spinner"></i> Memindai...');
    $.get(BASE_URL + 'administrator/crm/trigger_cron_manual', function(res) {
      var r = JSON.parse(res);
      toastr.success(r.message || 'Sinkronisasi label H+ berhasil!');
      setTimeout(function() { location.reload(); }, 1500);
    });
  });

  // Status change inline (FB-07)
  $('.status-select').on('change', function() {
    var $sel = $(this);
    var label_id = $sel.data('label-id');
    var status = $sel.val();
    $.post(BASE_URL + 'administrator/crm/update_status_ajax', {
      label_id: label_id, status: status, [csrf]: token
    }, function(res) {
      var r = JSON.parse(res);
      if (r.success) {
        toastr.success('Status diperbarui');
        $sel.data('current', status);
      } else {
        toastr.error(r.message || 'Gagal');
      }
    });
  });

  // Single WA send
  $('.btn-send-wa').on('click', function() {
    if ($(this).hasClass('disabled')) return;
    var $btn = $(this);
    var label_id = $btn.data('label-id');
    $.get(BASE_URL + 'administrator/crm/get_wa_link_ajax', { label_id: label_id }, function(res) {
      var r = JSON.parse(res);
      if (r.success) {
        window.open(r.wa_link, '_blank');
        // Log broadcast
        $.post(BASE_URL + 'administrator/crm/send_broadcast_ajax', {
          label_ids: [label_id], [csrf]: token
        }, function() {
          $btn.addClass('disabled').prop('disabled', true);
          toastr.success('WA terkirim ke ' + r.nama);
        });
      }
    });
  });

  // Select all pending
  $('#check-all').on('change', function() {
    $('.cb-label').prop('checked', this.checked);
    toggleBroadcastBtn();
  });
  $('#btn-select-all').on('click', function() {
    $('.cb-label').prop('checked', true);
    toggleBroadcastBtn();
  });
  $(document).on('change', '.cb-label', toggleBroadcastBtn);

  function toggleBroadcastBtn() {
    var count = $('.cb-label:checked').length;
    $('#btn-broadcast-selected').prop('disabled', count === 0).html('<i class="fa fa-whatsapp"></i> Broadcast (' + count + ')');
  }

  // Broadcast selected (FB-04)
  $('#btn-broadcast-selected').on('click', function() {
    var ids = [];
    $('.cb-label:checked').each(function() { ids.push($(this).val()); });
    if (ids.length === 0) return;

    swal({
      title: 'Broadcast WA?',
      text: 'Kirim WA ke ' + ids.length + ' customer terpilih?',
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Kirim!',
      cancelButtonText: 'Batal'
    }, function(confirmed) {
      if (!confirmed) return;
      $.post(BASE_URL + 'administrator/crm/send_broadcast_ajax', {
        label_ids: ids, [csrf]: token
      }, function(res) {
        var r = JSON.parse(res);
        if (r.success && r.wa_links) {
          // Open WA links in batch with 2sec delay
          var delay = 0;
          $.each(r.wa_links, function(i, link) {
            setTimeout(function() { window.open(link.link, '_blank'); }, delay);
            delay += 2000;
          });
          toastr.success(r.count + ' pesan WA berhasil dikirim!');
          setTimeout(function() { location.reload(); }, 3000);
        }
      });
    });
  });

  // Add note modal (FB-07)
  $('.btn-add-note').on('click', function() {
    var lid = $(this).data('label-id');
    var nama = $(this).data('nama');
    $('#note-label-id').val(lid);
    $('#note-customer-name').text(nama);
    $('#note-text').val('');
    $('#note-status').val('');
    // Load existing notes
    $.get(BASE_URL + 'administrator/crm/get_notes_ajax', { label_id: lid }, function(res) {
      var r = JSON.parse(res);
      var html = '';
      if (r.notes && r.notes.length > 0) {
        html += '<hr><h5 style="font-weight:700;font-size:12px;">Riwayat Catatan:</h5>';
        $.each(r.notes, function(i, n) {
          html += '<div style="background:#f8f9fa;padding:8px 12px;border-radius:5px;margin-bottom:5px;font-size:12px;">';
          html += '<strong>' + (n.username || 'system') + '</strong> <span style="color:#95a5a6;">' + n.created_at + '</span>';
          if (n.status_manual) html += ' <span class="label label-info">' + n.status_manual + '</span>';
          html += '<br>' + n.catatan;
          html += '</div>';
        });
      }
      $('#note-history').html(html);
    });
    $('#modal-note').modal('show');
  });

  // Save note
  $('#btn-save-note').on('click', function() {
    var lid = $('#note-label-id').val();
    var catatan = $('#note-text').val();
    var status = $('#note-status').val();
    if (!catatan && !status) { toastr.warning('Isi catatan atau pilih status'); return; }
    $.post(BASE_URL + 'administrator/crm/update_status_ajax', {
      label_id: lid,
      status: status || $('.status-select[data-label-id="'+lid+'"]').val(),
      catatan: catatan,
      [csrf]: token
    }, function(res) {
      var r = JSON.parse(res);
      if (r.success) {
        toastr.success('Catatan disimpan');
        $('#modal-note').modal('hide');
        location.reload();
      }
    });
  });
});
</script>
