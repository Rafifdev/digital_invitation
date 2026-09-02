<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
.crm-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); color: #fff; padding: 25px 30px; border-radius: 8px; margin-bottom: 20px; }
.crm-header h2 { margin: 0 0 5px 0; font-weight: 700; font-size: 22px; }
.crm-header p { margin: 0; opacity: 0.8; font-size: 13px; }
.cat-card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); border-left: 4px solid #3498db; transition: transform 0.2s; }
.cat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
.cat-card.inactive { border-left-color: #bdc3c7; opacity: 0.6; }
.cat-card .cat-title { font-size: 16px; font-weight: 700; color: #2c3e50; margin-bottom: 4px; }
.cat-card .cat-code { font-size: 11px; color: #95a5a6; font-family: monospace; }
.cat-card .cat-days { display: inline-block; background: #e8f4fd; color: #2980b9; padding: 3px 12px; border-radius: 15px; font-weight: 700; font-size: 13px; }
.cat-card .cat-template { background: #f8f9fa; padding: 10px 14px; border-radius: 6px; margin-top: 10px; font-size: 12px; color: #555; white-space: pre-wrap; word-wrap: break-word; max-height: 120px; overflow-y: auto; }
.var-tag { display: inline-block; background: #dfe6e9; color: #2c3e50; padding: 1px 8px; border-radius: 3px; font-size: 11px; font-family: monospace; margin: 2px; cursor: pointer; }
.var-tag:hover { background: #3498db; color: #fff; }
</style>

<section class="content-header">
  <h1><i class="fa fa-cogs"></i> Pengaturan Kelompok Follow-Up <small>CRUD Kategori H+ & Template WA</small></h1>
  <ol class="breadcrumb">
    <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    <li><a href="<?= site_url('administrator/crm'); ?>">CRM</a></li>
    <li class="active">Kelompok Follow-Up</li>
  </ol>
</section>

<section class="content">
  <div class="crm-header">
    <div class="row">
      <div class="col-md-8">
        <h2><i class="fa fa-cogs"></i> Kelompok Follow-Up</h2>
        <p>Atur kategori H+ custom dan template pesan WhatsApp untuk setiap tahapan follow-up</p>
      </div>
      <div class="col-md-4 text-right" style="padding-top: 10px;">
        <button class="btn btn-sm" style="background:#27ae60;color:#fff;border:none;" id="btn-add-cat"><i class="fa fa-plus"></i> Tambah Kategori Baru</button>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-7">
      <h4 style="font-weight:700;color:#2c3e50;margin-bottom:15px;"><i class="fa fa-list"></i> Kategori Aktif (<?= count($categories); ?>)</h4>
      <?php if (empty($categories)): ?>
        <div class="alert alert-info"><i class="fa fa-info-circle"></i> Belum ada kategori follow-up.</div>
      <?php else: foreach ($categories as $cat): ?>
      <div class="cat-card <?= $cat->is_active ? '' : 'inactive'; ?>" data-id="<?= $cat->id; ?>">
        <div class="row">
          <div class="col-md-8">
            <div class="cat-title"><?= htmlspecialchars($cat->nama_kategori); ?></div>
            <div class="cat-code"><?= $cat->kode_kategori; ?></div>
          </div>
          <div class="col-md-4 text-right">
            <span class="cat-days">H+<?= $cat->jumlah_hari; ?></span>
            <br><br>
            <button class="btn btn-xs btn-warning btn-edit-cat" 
              data-id="<?= $cat->id; ?>" 
              data-nama="<?= htmlspecialchars($cat->nama_kategori); ?>"
              data-kode="<?= $cat->kode_kategori; ?>"
              data-hari="<?= $cat->jumlah_hari; ?>"
              data-template="<?= htmlspecialchars($cat->template_pesan_default); ?>"
              data-active="<?= $cat->is_active; ?>"
              data-urutan="<?= $cat->urutan; ?>"><i class="fa fa-edit"></i> Edit</button>
            <button class="btn btn-xs btn-danger btn-del-cat" data-id="<?= $cat->id; ?>" data-nama="<?= htmlspecialchars($cat->nama_kategori); ?>"><i class="fa fa-trash"></i></button>
          </div>
        </div>
        <?php if (!empty($cat->template_pesan_default)): ?>
        <div class="cat-template"><?= htmlspecialchars($cat->template_pesan_default); ?></div>
        <?php endif; ?>
        <div style="margin-top: 8px;">
          <span style="font-size:11px;color:#7f8c8d;">Status: <?= $cat->is_active ? '<span class="text-success"><i class="fa fa-check-circle"></i> Aktif</span>' : '<span class="text-muted"><i class="fa fa-times-circle"></i> Nonaktif</span>'; ?> | Urutan: <?= $cat->urutan; ?></span>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>

    <div class="col-md-5">
      <!-- Add/Edit Form -->
      <div class="box box-primary" id="form-category">
        <div class="box-header with-border">
          <h3 class="box-title" id="form-title"><i class="fa fa-plus"></i> Tambah Kategori Baru</h3>
        </div>
        <div class="box-body">
          <input type="hidden" id="cat-id" value="">
          <div class="form-group">
            <label>Nama Kategori <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="cat-nama" placeholder="e.g. H+14 — Follow Up Lanjutan">
          </div>
          <div class="form-group">
            <label>Kode Kategori <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="cat-kode" placeholder="e.g. H_14_FOLLOWUP" style="font-family:monospace;">
          </div>
          <div class="form-group">
            <label>Jumlah Hari (H+n) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="cat-hari" value="0" min="0">
            <small class="text-muted">H+0 = saat kirim, H+3 = 3 hari setelah kirim, dst.</small>
          </div>
          <div class="form-group">
            <label>Template Pesan WA</label>
            <textarea class="form-control" id="cat-template" rows="6" placeholder="Halo Kak {{nama_customer}}..."></textarea>
            <small class="text-muted">Variabel tersedia:</small><br>
            <span class="var-tag" onclick="insertVar('{{nama_customer}}')">{{nama_customer}}</span>
            <span class="var-tag" onclick="insertVar('{{no_pesanan}}')">{{no_pesanan}}</span>
            <span class="var-tag" onclick="insertVar('{{nomor_resi}}')">{{nomor_resi}}</span>
            <span class="var-tag" onclick="insertVar('{{produk}}')">{{produk}}</span>
            <span class="var-tag" onclick="insertVar('{{tanggal_kirim}}')">{{tanggal_kirim}}</span>
            <span class="var-tag" onclick="insertVar('{{sumber_penjualan}}')">{{sumber_penjualan}}</span>
            <span class="var-tag" onclick="insertVar('{{kurir}}')">{{kurir}}</span>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Urutan Tampil</label>
                <input type="number" class="form-control" id="cat-urutan" value="0" min="0">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Status</label>
                <select class="form-control" id="cat-active">
                  <option value="1">Aktif</option>
                  <option value="0">Nonaktif</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="box-footer">
          <button class="btn btn-primary" id="btn-save-cat"><i class="fa fa-save"></i> Simpan</button>
          <button class="btn btn-default" id="btn-reset-form"><i class="fa fa-refresh"></i> Reset</button>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function insertVar(v) {
  var ta = document.getElementById('cat-template');
  var start = ta.selectionStart;
  var end = ta.selectionEnd;
  ta.value = ta.value.substring(0, start) + v + ta.value.substring(end);
  ta.selectionStart = ta.selectionEnd = start + v.length;
  ta.focus();
}

$(document).ready(function() {
  // Add new
  $('#btn-add-cat').on('click', function() {
    resetForm();
    $('html,body').animate({ scrollTop: $('#form-category').offset().top - 100 }, 400);
  });

  // Edit
  $('.btn-edit-cat').on('click', function() {
    var $b = $(this);
    $('#cat-id').val($b.data('id'));
    $('#cat-nama').val($b.data('nama'));
    $('#cat-kode').val($b.data('kode'));
    $('#cat-hari').val($b.data('hari'));
    $('#cat-template').val($b.data('template'));
    $('#cat-active').val($b.data('active'));
    $('#cat-urutan').val($b.data('urutan'));
    $('#form-title').html('<i class="fa fa-edit"></i> Edit Kategori');
    $('html,body').animate({ scrollTop: $('#form-category').offset().top - 100 }, 400);
  });

  // Save
  $('#btn-save-cat').on('click', function() {
    var data = {
      id: $('#cat-id').val(),
      nama_kategori: $('#cat-nama').val(),
      kode_kategori: $('#cat-kode').val(),
      jumlah_hari: $('#cat-hari').val(),
      template_pesan_default: $('#cat-template').val(),
      is_active: $('#cat-active').val(),
      urutan: $('#cat-urutan').val(),
      [csrf]: token
    };
    if (!data.nama_kategori || !data.kode_kategori) { toastr.warning('Nama dan Kode wajib diisi'); return; }
    $.post(BASE_URL + 'administrator/crm/save_category_ajax', data, function(res) {
      var r = JSON.parse(res);
      if (r.success) {
        toastr.success(r.message);
        location.reload();
      } else {
        toastr.error(r.message);
      }
    });
  });

  // Delete
  $('.btn-del-cat').on('click', function() {
    var id = $(this).data('id');
    var nama = $(this).data('nama');
    swal({
      title: 'Hapus "' + nama + '"?',
      text: 'Kategori hanya bisa dihapus jika belum digunakan oleh label follow-up.',
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal'
    }, function(confirmed) {
      if (!confirmed) return;
      $.post(BASE_URL + 'administrator/crm/delete_category_ajax', { id: id, [csrf]: token }, function(res) {
        var r = JSON.parse(res);
        if (r.success) {
          toastr.success(r.message);
          location.reload();
        } else {
          toastr.error(r.message);
        }
      });
    });
  });

  // Reset
  $('#btn-reset-form').on('click', resetForm);

  function resetForm() {
    $('#cat-id').val('');
    $('#cat-nama, #cat-kode, #cat-template').val('');
    $('#cat-hari, #cat-urutan').val(0);
    $('#cat-active').val(1);
    $('#form-title').html('<i class="fa fa-plus"></i> Tambah Kategori Baru');
  }
});
</script>
