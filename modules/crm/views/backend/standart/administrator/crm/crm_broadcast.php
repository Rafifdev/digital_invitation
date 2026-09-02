<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
.crm-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); color: #fff; padding: 25px 30px; border-radius: 8px; margin-bottom: 20px; }
.crm-header h2 { margin: 0 0 5px 0; font-weight: 700; font-size: 22px; }
.crm-header p { margin: 0; opacity: 0.8; font-size: 13px; }
.table-log th { background: #f8f9fa; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; }
.table-log td { font-size: 13px; vertical-align: middle !important; }
.badge-source { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; color: #fff; }
.badge-toko { background: #2c3e50; }
.badge-shopee { background: #ee4d2d; }
.badge-tokopedia { background: #42b549; }
.badge-tiktok { background: #010101; }

@media (max-width: 767px) {
  .crm-header { padding: 18px 15px; text-align: center; }
  .crm-header h2 { font-size: 18px; }
  .crm-header .text-right { text-align: center !important; margin-top: 15px; }
}
</style>

<section class="content-header">
  <h1><i class="fa fa-bullhorn"></i> Broadcast WhatsApp <small>Pesan Terjadwal per Kelompok Label</small></h1>
  <ol class="breadcrumb">
    <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="<?= site_url('administrator/crm'); ?>">CRM</a></li>
    <li class="active">Broadcast WA</li>
  </ol>
</section>

<section class="content">
  <div class="crm-header">
    <div class="row">
      <div class="col-md-8">
        <h2><i class="fa fa-bullhorn"></i> Broadcast WhatsApp</h2>
        <p>Kirim pesan massal ke kelompok customer berdasarkan label H+ & Cegah duplikasi pengiriman otomatis</p>
      </div>
      <div class="col-md-4 text-right" style="padding-top: 10px;">
        <a href="<?= site_url('administrator/crm/journal_followup'); ?>" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);"><i class="fa fa-tasks"></i> Journal Follow-Up</a>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Panel Pengiriman Broadcast -->
    <div class="col-md-5">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-paper-plane"></i> Kirim Broadcast Baru</h3>
        </div>
        <div class="box-body">
          <div class="form-group">
            <label>Pilih Kategori Label H+ <span class="text-danger">*</span></label>
            <select class="form-control" id="bc-category">
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat->id; ?>"><?= htmlspecialchars($cat->nama_kategori); ?> (H+<?= $cat->jumlah_hari; ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Filter Sumber Penjualan</label>
            <select class="form-control" id="bc-source">
              <option value="">Semua Sumber (Toko & Online)</option>
              <option value="Toko">Toko / Offline SO</option>
              <option value="Shopee">Shopee</option>
              <option value="Tokopedia">Tokopedia</option>
              <option value="TikTok Shop">TikTok Shop</option>
            </select>
          </div>

          <div class="form-group text-center">
            <button class="btn btn-info btn-block" id="btn-load-recipients"><i class="fa fa-users"></i> Muat Penerima Eligible</button>
          </div>

          <!-- Preview Penerima -->
          <div id="recipients-container" style="display:none; border-top: 1px solid #eee; padding-top: 15px; margin-top: 15px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
              <h5 style="margin:0; font-weight:700;"><i class="fa fa-user-check"></i> Penerima Siap Kirim (<span id="recipient-count">0</span>)</h5>
              <button class="btn btn-xs btn-default" id="btn-toggle-all-bc">Pilih Semua</button>
            </div>

            <div id="recipients-list" style="max-height: 250px; overflow-y: auto; background: #f8f9fa; border: 1px solid #ddd; border-radius: 6px; padding: 10px;">
              <!-- Dynamic Checkbox List -->
            </div>

            <div class="alert alert-info" style="margin-top: 12px; font-size: 11px; padding: 8px 12px;">
              <i class="fa fa-info-circle"></i> Sesuai Aturan (FB-10), customer yang sudah menerima broadcast untuk label ini tidak akan terkirim ulang secara otomatis.
            </div>

            <button class="btn btn-success btn-block btn-lg" id="btn-execute-broadcast" style="margin-top: 15px;">
              <i class="fa fa-whatsapp"></i> Mulai Broadcast Sekarang
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Riwayat / Log Broadcast -->
    <div class="col-md-7">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-history"></i> Log Broadcast Terakhir</h3>
        </div>
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover table-striped table-log">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Pengirim</th>
                <th>Customer</th>
                <th>No. HP</th>
                <th>Kategori</th>
                <th>Sumber</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($logs)): ?>
              <tr><td colspan="7" class="text-center" style="padding:30px; color:#95a5a6;"><i class="fa fa-inbox fa-2x"></i><br>Belum ada riwayat broadcast.</td></tr>
              <?php else: foreach ($logs as $log): ?>
              <tr>
                <td style="font-size:11px; white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($log->waktu_kirim)); ?></td>
                <td><strong><?= htmlspecialchars($log->dikirim_oleh); ?></strong></td>
                <td><?= htmlspecialchars($log->customer_nama); ?></td>
                <td style="font-size:12px;"><?= htmlspecialchars($log->customer_phone); ?></td>
                <td><span class="label label-info"><?= htmlspecialchars($log->nama_kategori); ?></span></td>
                <td>
                  <?php
                  $cls = 'badge-toko';
                  if (stripos($log->order_source, 'Shopee') !== false) $cls = 'badge-shopee';
                  elseif (stripos($log->order_source, 'Tokopedia') !== false) $cls = 'badge-tokopedia';
                  elseif (stripos($log->order_source, 'TikTok') !== false) $cls = 'badge-tiktok';
                  ?>
                  <span class="badge-source <?= $cls; ?>"><?= $log->order_source; ?></span>
                </td>
                <td class="text-center">
                  <span class="label label-success"><i class="fa fa-check"></i> <?= $log->status_kirim; ?></span>
                </td>
              </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
$(document).ready(function() {
  $('#btn-load-recipients').on('click', function() {
    var cat_id = $('#bc-category').val();
    var source = $('#bc-source').val();

    if (!cat_id) {
      toastr.warning('Pilih kategori label H+ terlebih dahulu.');
      return;
    }

    $.get(BASE_URL + 'administrator/crm/get_broadcast_recipients_ajax', {
      category_id: cat_id, order_source: source
    }, function(res) {
      var r = JSON.parse(res);
      if (r.success) {
        var html = '';
        if (r.recipients && r.recipients.length > 0) {
          $.each(r.recipients, function(i, item) {
            html += '<div style="margin-bottom:6px; font-size:12px;">';
            html += '<label style="font-weight:normal; cursor:pointer;">';
            html += '<input type="checkbox" class="cb-bc-item" value="' + item.id + '" checked> ';
            html += '<strong>' + item.customer_nama + '</strong> (' + item.customer_phone + ') — <span class="text-muted">' + item.no_pesanan + '</span>';
            html += '</label>';
            html += '</div>';
          });
          $('#recipient-count').text(r.recipients.length);
          $('#btn-execute-broadcast').prop('disabled', false);
        } else {
          html = '<div class="text-center text-muted" style="padding:15px;"><i class="fa fa-info-circle"></i> Tidak ada customer pending untuk kategori ini.</div>';
          $('#recipient-count').text(0);
          $('#btn-execute-broadcast').prop('disabled', true);
        }
        $('#recipients-list').html(html);
        $('#recipients-container').slideDown(300);
      }
    });
  });

  $('#btn-toggle-all-bc').on('click', function() {
    var allChecked = $('.cb-bc-item:checked').length === $('.cb-bc-item').length;
    $('.cb-bc-item').prop('checked', !allChecked);
  });

  $('#btn-execute-broadcast').on('click', function() {
    var selectedIds = [];
    $('.cb-bc-item:checked').each(function() {
      selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
      toastr.warning('Pilih minimal 1 customer untuk dikirimkan broadcast.');
      return;
    }

    swal({
      title: 'Mulai Broadcast WA?',
      text: 'Total ' + selectedIds.length + ' pesan akan disiapkan & dicatat ke sistem.',
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Kirim Sekarang!',
      cancelButtonText: 'Batal'
    }, function(confirmed) {
      if (!confirmed) return;

      $.post(BASE_URL + 'administrator/crm/send_broadcast_ajax', {
        label_ids: selectedIds, [csrf]: token
      }, function(res) {
        var r = JSON.parse(res);
        if (r.success && r.wa_links) {
          var delay = 0;
          $.each(r.wa_links, function(i, item) {
            setTimeout(function() {
              window.open(item.link, '_blank');
            }, delay);
            delay += 2000;
          });
          toastr.success(r.count + ' pesan WA sedang diproses batch!');
          setTimeout(function() {
            location.reload();
          }, 3000);
        } else {
          toastr.error(r.message || 'Gagal mengirim broadcast');
        }
      });
    });
  });
});
</script>
