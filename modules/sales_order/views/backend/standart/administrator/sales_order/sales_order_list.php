<?php
/**
 * View: Daftar Sales Order (Modern & Sleek UI)
 * File: sales_order_list.php
 */
$status_color = [
    'draft'      => 'label-default',
    'submitted'  => 'label-default',
    'confirmed'  => 'label-info',
    'approved'   => 'label-info',
    'packing'    => 'label-warning',
    'processing' => 'label-warning',
    'shipped'    => 'label-primary',
    'delivered'  => 'label-success',
    'completed'  => 'label-success',
    'cancelled'  => 'label-danger'
];

$status_display = [
    'draft'      => '1. DRAFT',
    'submitted'  => '1. DRAFT',
    'confirmed'  => '2. TERKONFIRMASI',
    'approved'   => '2. TERKONFIRMASI',
    'packing'    => '3. SEDANG DIKEMAS',
    'processing' => '3. SEDANG DIKEMAS',
    'shipped'    => '4. DALAM PENGIRIMAN',
    'delivered'  => '5. SELESAI',
    'completed'  => '5. SELESAI',
    'cancelled'  => 'DIBATALKAN'
];
?>
<style>
.kpi-card {
   background: #ffffff;
   border-radius: 8px;
   padding: 16px 20px;
   box-shadow: 0 2px 10px rgba(0,0,0,0.04);
   border-left: 4px solid #00c0ef;
   margin-bottom: 20px;
   transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.kpi-card:hover {
   transform: translateY(-2px);
   box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}
.kpi-card.orange { border-left-color: #f39c12; }
.kpi-card.green  { border-left-color: #00a65a; }
.kpi-card.purple { border-left-color: #605ca8; }

.kpi-title { font-size: 11px; text-transform: uppercase; font-weight: 700; color: #888; letter-spacing: 0.5px; }
.kpi-value { font-size: 22px; font-weight: 700; color: #222; margin: 4px 0; }
.kpi-sub { font-size: 11px; color: #999; }
.kpi-icon { font-size: 28px; opacity: 0.2; float: right; margin-top: -35px; }

.clean-filter-box {
   background: #fdfdfd;
   border: 1px solid #e9ecef;
   border-radius: 6px;
   padding: 15px;
   margin-bottom: 20px;
}
</style>

<section class="content-header">
   <h1>
      Pesanan Penjualan (Sales Order)
      <small>Daftar Pesanan Masuk & Status Pengiriman Berkala</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Sales Order</li>
   </ol>
</section>

<section class="content">
   <!-- 4 Modern Minimalist KPI Cards -->
   <div class="row">
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card">
            <div class="kpi-title">Total Omset Penjualan</div>
            <div class="kpi-value">Rp <?= number_format($stats['penjualan_bulan_ini'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub" style="font-size:10px; margin-top:3px;">
               <span class="text-success" title="Kas Masuk (Lunas)"><i class="fa fa-check-circle"></i> Lunas: Rp <?= number_format($stats['terbayar_lunas'] ?? 0, 0, ',', '.'); ?></span> | 
               <span class="text-danger" title="Piutang Tempo (Unpaid)"><i class="fa fa-clock-o"></i> Tempo: Rp <?= number_format($stats['piutang_unpaid'] ?? 0, 0, ',', '.'); ?></span>
            </div>
            <i class="fa fa-line-chart kpi-icon text-info"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card orange">
            <div class="kpi-title">Pesanan Aktif</div>
            <div class="kpi-value"><?= $stats['pesanan_aktif'] ?? 0; ?> <small style="font-size:14px;">Order</small></div>
            <div class="kpi-sub"><i class="fa fa-clock-o"></i> Draft / Baru Masuk</div>
            <i class="fa fa-shopping-cart kpi-icon text-warning"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card green">
            <div class="kpi-title">Proses Gudang & Kirim</div>
            <div class="kpi-value"><?= ($stats['proses_gudang'] ?? 0) + ($stats['siap_kirim'] ?? 0); ?> <small style="font-size:14px;">Order</small></div>
            <div class="kpi-sub"><i class="fa fa-truck"></i> Packing & Pengiriman</div>
            <i class="fa fa-truck kpi-icon text-success"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card purple">
            <div class="kpi-title">Pesanan Selesai</div>
            <div class="kpi-value"><?= $stats['pesanan_terkirim'] ?? 0; ?> <small style="font-size:14px;">Order</small></div>
            <div class="kpi-sub"><i class="fa fa-check-circle"></i> Delivered / Selesai</div>
            <i class="fa fa-check-square-o kpi-icon text-purple"></i>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary" style="border-radius:6px; border-top: 3px solid #3c8dbc;">
            <div class="box-header with-border" style="padding: 15px 20px;">
               <h3 class="box-title" style="font-weight:600;"><i class="fa fa-list-alt text-primary"></i> Daftar Dokumen Sales Order</h3>
               <div class="box-tools pull-right">
                  <?php if (is_allowed('pelanggan_list', false)): ?>
                     <a href="<?= site_url('administrator/pelanggan'); ?>" class="btn btn-default btn-sm btn-flat" style="margin-right:5px; border-radius:4px;">
                        <i class="fa fa-users"></i> Data Pelanggan
                     </a>
                  <?php endif; ?>
                  <?php if (is_allowed('sales_order_add', false)): ?>
                     <a href="<?= site_url('administrator/sales_order/add'); ?>" class="btn btn-success btn-sm btn-flat" style="border-radius:4px; font-weight:600;">
                        <i class="fa fa-plus"></i> Buat Sales Order Baru
                     </a>
                  <?php endif; ?>
               </div>
            </div>

            <div class="box-body" style="padding: 20px;">
               <!-- Clean Filter Bar -->
               <div class="clean-filter-box">
                  <form action="<?= base_url('administrator/sales_order/index'); ?>" method="GET">
                     <div class="row">
                        <div class="col-md-3 col-sm-6">
                           <label class="text-muted" style="font-size:12px;">Pelanggan:</label>
                           <select class="form-control chosen" name="pelanggan_id">
                              <option value="">-- Semua Pelanggan --</option>
                              <?php foreach($pelanggans as $p): ?>
                              <option value="<?= $p->id; ?>" <?= $this->input->get('pelanggan_id') == $p->id ? 'selected':''; ?>><?= _ent($p->nama_pelanggan); ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                           <label class="text-muted" style="font-size:12px;">Gudang Asal:</label>
                           <select class="form-control chosen" name="gudang_id">
                              <option value="">-- Semua Gudang --</option>
                              <?php foreach($gudangs as $g): ?>
                              <option value="<?= $g->id; ?>" <?= $this->input->get('gudang_id') == $g->id ? 'selected':''; ?>><?= _ent($g->nama_gudang); ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                        <div class="col-md-4 col-sm-8">
                           <label class="text-muted" style="font-size:12px;">Cari No SO / Nama:</label>
                           <input type="text" class="form-control" name="q" placeholder="Ketik No. SO atau Pelanggan..." value="<?= _ent($this->input->get('q')); ?>">
                        </div>
                        <div class="col-md-2 col-sm-4" style="padding-top: 23px;">
                           <button type="submit" class="btn btn-primary btn-flat btn-block" style="border-radius:4px;"><i class="fa fa-filter"></i> Filter Data</button>
                        </div>
                     </div>
                  </form>
               </div>

               <div class="table-responsive" style="overflow-x: auto; min-height: 250px;">
                  <table class="table table-hover table-striped table-bordered" style="vertical-align: middle; margin-bottom: 0;">
                     <thead>
                        <tr style="background:#f8fafc; font-size:12px; color:#555;">
                           <th width="40" class="text-center">No</th>
                           <th width="150">No. Sales Order</th>
                           <th>Tanggal SO</th>
                           <th>Pelanggan & Tier</th>
                           <th>Gudang Asal</th>
                           <th class="text-right">Grand Total (Rp)</th>
                           <th class="text-center">Status Pesanan</th>
                           <th class="text-center">Pembayaran</th>
                           <th class="text-center" width="220">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (!empty($sos)): ?>
                           <?php $no = $this->uri->segment(4) + 1; foreach ($sos as $so): ?>
                           <tr>
                              <td class="text-center"><?= $no++; ?></td>
                              <td><strong class="text-primary"><a href="<?= site_url('administrator/sales_order/view/' . $so->id); ?>"><?= _ent($so->nomor_so); ?></a></strong></td>
                              <td><i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime($so->tanggal_so)); ?></td>
                              <td>
                                 <strong><?= _ent($so->nama_pelanggan); ?></strong><br>
                                 <small><span class="label label-info" style="font-size:9px;"><?= strtoupper(_ent($so->tipe_pelanggan ?? 'UMUM')); ?></span></small>
                              </td>
                              <td><i class="fa fa-bank text-muted"></i> <?= _ent(isset($so->nama_gudang) ? $so->nama_gudang : '-'); ?></td>
                              <td class="text-right"><strong class="text-success" style="font-size:13px;">Rp <?= number_format($so->total, 0, ',', '.'); ?></strong></td>
                               <td class="text-center">
                                  <span class="label <?= isset($status_color[$so->status]) ? $status_color[$so->status] : 'label-default'; ?>" style="font-size:11px; padding:4px 8px; display:inline-block;">
                                     <?= isset($status_display[$so->status]) ? $status_display[$so->status] : strtoupper($so->status); ?>
                                  </span>

                                  <?php if (is_allowed('sales_order_update', false)): ?>
                                  <div class="dropdown" style="margin-top:4px;">
                                     <button class="btn btn-xs btn-default dropdown-toggle" type="button" data-toggle="dropdown" style="font-size:10px; padding:2px 7px; border-radius:3px;">
                                        <i class="fa fa-refresh text-primary"></i> Ubah Status <span class="caret"></span>
                                     </button>
                                     <ul class="dropdown-menu dropdown-menu-right" style="font-size:11px; text-align:left; min-width:210px; box-shadow:0 4px 15px rgba(0,0,0,0.15);">
                                        <li class="dropdown-header" style="font-weight:700; color:#475569;">Progres Status Berkala:</li>
                                        <li><a href="<?= site_url('administrator/sales_order/update_status/'.$so->id.'?status=draft'); ?>">🟡 1. DRAFT (Pesanan Baru)</a></li>
                                        <li><a href="<?= site_url('administrator/sales_order/update_status/'.$so->id.'?status=confirmed'); ?>">🔵 2. TERKONFIRMASI (Stok Ready)</a></li>
                                        <li><a href="<?= site_url('administrator/sales_order/update_status/'.$so->id.'?status=packing'); ?>">📦 3. SEDANG DIKEMAS (Packing)</a></li>
                                        <li><a href="<?= site_url('administrator/sales_order/update_status/'.$so->id.'?status=shipped'); ?>">🚚 4. DALAM PENGIRIMAN (Shipped)</a></li>
                                        <li><a href="<?= site_url('administrator/sales_order/update_status/'.$so->id.'?status=delivered'); ?>">🟢 5. PESANAN SELESAI (Delivered)</a></li>
                                        <li class="divider"></li>
                                        <li><a href="<?= site_url('administrator/sales_order/update_status/'.$so->id.'?status=cancelled'); ?>" class="text-danger"><i class="fa fa-times-circle"></i> Dibatalkan</a></li>
                                     </ul>
                                  </div>
                                  <?php endif; ?>
                               </td>
                              <td class="text-center">
                                 <?php
                                    $inv = $this->db->get_where('invoice', ['so_id' => $so->id])->row();
                                    if ($inv) {
                                       if ($inv->status == 'paid') {
                                          echo '<span class="label label-success" style="font-size:11px; padding:4px 8px;">PAID</span>';
                                       } else {
                                          echo '<span class="label label-danger" style="font-size:11px; padding:4px 8px;">UNPAID</span>';
                                       }
                                    } else {
                                       echo '<span class="label label-default" style="font-size:11px; padding:4px 8px;">UNPAID</span>';
                                    }
                                 ?>
                              </td>
                              <td class="text-center">
                                 <!-- Inline Buttons for Better UX (No Dropdown Clipping) -->
                                 <a href="<?= site_url('administrator/sales_order/view/' . $so->id); ?>" class="btn btn-default btn-xs" title="Detail"><i class="fa fa-eye"></i></a>
                                 <a href="<?= site_url('administrator/sales_order/cetak/' . $so->id); ?>" target="_blank" class="btn btn-info btn-xs" title="Cetak PDF"><i class="fa fa-print"></i></a>
                                 
                                 <?php if ($so->status == 'draft' && is_allowed('sales_order_update', false)): ?>
                                 <a href="<?= site_url('administrator/sales_order/edit/'.$so->id); ?>" class="btn btn-warning btn-xs" title="Edit Order"><i class="fa fa-edit"></i></a>
                                 <a href="<?= site_url('administrator/sales_order/konfirmasi/' . $so->id); ?>" class="btn btn-primary btn-xs" title="Konfirmasi Order"><i class="fa fa-check"></i></a>
                                 <?php endif; ?>

                                 <?php 
                                 $linked_inv = $this->db->get_where('invoice', ['so_id' => $so->id])->row();
                                 if ($linked_inv): 
                                 ?>
                                    <a href="<?= site_url('administrator/invoice/view/' . $linked_inv->id); ?>" class="btn btn-info btn-xs" title="Lihat Invoice <?= _ent($linked_inv->nomor_invoice); ?>"><i class="fa fa-file-text-o"></i> <?= _ent($linked_inv->nomor_invoice); ?></a>
                                 <?php elseif (in_array($so->status, ['confirmed', 'processing', 'approved']) && is_allowed('invoice_add', false)): ?>
                                    <a href="<?= site_url('administrator/invoice/from_so/' . $so->id); ?>" class="btn btn-success btn-xs" title="Terbitkan Invoice"><i class="fa fa-file-text-o"></i> Invoice</a>
                                 <?php endif; ?>

                                 <?php if (is_allowed('sales_order_delete', false) && $so->status == 'draft'): ?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/sales_order/delete/' . $so->id); ?>" class="btn btn-danger btn-xs remove-data" title="Hapus"><i class="fa fa-trash"></i></a>
                                 <?php endif; ?>
                              </td>
                           </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr>
                              <td colspan="9" class="text-center text-muted" style="padding:40px;">
                                 <i class="fa fa-shopping-cart fa-3x" style="color:#e0e0e0; margin-bottom:10px;"></i><br>
                                 <span style="font-size:16px;">Belum ada data Sales Order.</span><br>
                                 <small>Silakan buat Sales Order baru melalui tombol di kanan atas.</small>
                              </td>
                           </tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <div class="row" style="margin-top: 15px;">
                  <div class="col-md-6">
                     <span class="text-muted">Total: <strong><?= $so_counts; ?></strong> Sales Order</span>
                  </div>
                  <div class="col-md-6">
                     <div class="pull-right">
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
$(document).ready(function(){
   $('.remove-data').click(function(){
      var url = $(this).attr('data-href');
      swal({
         title: "Hapus Sales Order?",
         text: "Data yang dihapus tidak dapat dikembalikan!",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "Ya, Hapus!",
         cancelButtonText: "Batal",
         closeOnConfirm: true
      }, function(ok){
         if (ok) {
            window.location.href = url;
         }
      });
   });
});
</script>
