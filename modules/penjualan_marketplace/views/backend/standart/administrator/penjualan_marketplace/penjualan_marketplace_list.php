<?php
/**
 * View: Penjualan Register All / Marketplace (Shopee/Tokopedia Import)
 * File: penjualan_marketplace_list.php
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
.btn-marketplace-action {
   margin-bottom: 5px;
   border-radius: 4px;
   font-weight: 600;
   font-size: 12px;
}
</style>

<section class="content-header">
   <h1>
      Penjualan Register All
      <small>Daftar Pesanan Tokopedia, Shopee & Sales Order Regular</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Penjualan Register All</li>
   </ol>
</section>

<section class="content">
   <!-- Modern Minimalist KPI Cards -->
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
               <div class="pull-left" style="margin-bottom:10px;">
                  <button type="button" class="btn btn-success btn-flat btn-sm btn-marketplace-action" data-toggle="modal" data-target="#modalImportPacked">
                     <i class="fa fa-barcode"></i> Packed
                  </button>
                  <button type="button" class="btn btn-success btn-flat btn-sm btn-marketplace-action" data-toggle="modal" data-target="#modalImportPacked">
                     <i class="fa fa-plus"></i> Add Import Packed New
                  </button>
                  <button type="button" class="btn btn-success btn-flat btn-sm btn-marketplace-action" data-toggle="modal" data-target="#modalImportMarketplace">
                     <i class="fa fa-plus"></i> Add Import Penjualan (Tokopedia/Shopee) New
                  </button>
                  <a href="<?= site_url('administrator/sales_order/add?source=shopee'); ?>" class="btn btn-success btn-flat btn-sm btn-marketplace-action">
                     <i class="fa fa-plus"></i> Add Penjualan Shopee New
                  </a>
                  <a href="<?= site_url('administrator/penjualan_marketplace/export'); ?>" class="btn btn-success btn-flat btn-sm btn-marketplace-action">
                     <i class="fa fa-file-excel-o"></i> Export XLS
                  </a>
                  <a href="<?= site_url('administrator/penjualan_marketplace/export_pdf'); ?>" class="btn btn-success btn-flat btn-sm btn-marketplace-action">
                     <i class="fa fa-file-pdf-o"></i> Export PDF
                  </a>
               </div>
            </div>

            <div class="box-body" style="padding: 20px;">
               <!-- Filter Row 1 (Marketplace & Date Range) -->
               <div class="clean-filter-box">
                  <form action="<?= base_url('administrator/penjualan_marketplace/index'); ?>" method="GET">
                     <div class="row">
                        <div class="col-md-3 col-sm-6">
                           <label class="text-muted" style="font-size:12px;">Saluran Penjualan:</label>
                           <select class="form-control" name="source">
                              <option value="">-- Semua Saluran --</option>
                              <option value="shopee" <?= $this->input->get('source') == 'shopee' ? 'selected':''; ?>>Penjualan Shopee</option>
                              <option value="tokopedia" <?= $this->input->get('source') == 'tokopedia' ? 'selected':''; ?>>Penjualan Tokopedia</option>
                              <option value="manual" <?= $this->input->get('source') == 'manual' ? 'selected':''; ?>>Sales Order Regular / Manual</option>
                           </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                           <label class="text-muted" style="font-size:12px;">Gudang Asal:</label>
                           <select class="form-control" name="gudang_id">
                              <option value="">-- Semua Gudang --</option>
                              <?php foreach($gudangs as $g): ?>
                              <option value="<?= $g->id; ?>" <?= $this->input->get('gudang_id') == $g->id ? 'selected':''; ?>><?= _ent($g->nama_gudang); ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                        <div class="col-md-4 col-sm-8">
                           <label class="text-muted" style="font-size:12px;">Cari No Pesanan / Resi / Buyer / Receiver:</label>
                           <input type="text" class="form-control" name="q" placeholder="Ketik No. Pesanan, Resi, atau Username..." value="<?= _ent($this->input->get('q')); ?>">
                        </div>
                        <div class="col-md-2 col-sm-4" style="padding-top: 23px;">
                           <button type="submit" class="btn btn-primary btn-flat btn-block" style="border-radius:4px;"><i class="fa fa-search"></i> Q Search</button>
                        </div>
                     </div>
                     
                     <div class="row" style="margin-top:10px;">
                        <div class="col-md-3 col-sm-6">
                           <label class="text-muted" style="font-size:12px;">Pilih Status Pesanan:</label>
                           <select class="form-control" name="status">
                              <option value="">-- Semua Status --</option>
                              <option value="draft" <?= $this->input->get('status') == 'draft' ? 'selected':''; ?>>Draft / Pesanan Baru</option>
                              <option value="confirmed" <?= $this->input->get('status') == 'confirmed' ? 'selected':''; ?>>Terkonfirmasi (Stok Ready)</option>
                              <option value="packing" <?= $this->input->get('status') == 'packing' ? 'selected':''; ?>>Sedang Dikemas (Packing)</option>
                              <option value="shipped" <?= $this->input->get('status') == 'shipped' ? 'selected':''; ?>>Dalam Pengiriman (Shipped)</option>
                              <option value="delivered" <?= $this->input->get('status') == 'delivered' ? 'selected':''; ?>>Pesanan Selesai (Delivered)</option>
                              <option value="cancelled" <?= $this->input->get('status') == 'cancelled' ? 'selected':''; ?>>Dibatalkan</option>
                           </select>
                        </div>
                        <div class="col-md-3 col-sm-6" style="padding-top:23px;">
                           <a href="<?= site_url('administrator/penjualan_marketplace'); ?>" class="btn btn-default btn-flat"><i class="fa fa-undo"></i> Reset Filter</a>
                        </div>
                     </div>
                  </form>
               </div>

               <!-- Table matching Exact Screenshot 1 layout -->
               <div class="table-responsive" style="overflow-x: auto; min-height: 300px;">
                  <table class="table table-hover table-striped table-bordered" style="font-size:12px; vertical-align: middle; margin-bottom: 0;">
                     <thead>
                        <tr style="background:#f8fafc; font-size:12px; color:#555;">
                           <th width="30" class="text-center"><input type="checkbox" id="check_all"></th>
                           <th>No Pesanan</th>
                           <th class="text-center">Status Pesanan</th>
                           <th>No Resi</th>
                           <th class="text-center">Qty Item</th>
                           <th>Opsi Pengiriman</th>
                           <th>Waktu Pembayaran</th>
                           <th>Username Pembeli</th>
                           <th>Nama Penerima</th>
                           <th>Notelp</th>
                           <th class="text-center">Status</th>
                           <th class="text-center">Komplain</th>
                           <th class="text-center" width="140">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (!empty($sos)): ?>
                           <?php foreach ($sos as $so): 
                              $display_no_pesanan = !empty($so->no_pesanan) ? $so->no_pesanan : $so->nomor_so;
                              $display_status_pesanan = !empty($so->status_pesanan) ? $so->status_pesanan : (isset($status_display[$so->status]) ? $status_display[$so->status] : strtoupper($so->status));
                              $display_resi = !empty($so->nomor_resi) ? $so->nomor_resi : '-';
                              $qty_item = isset($so->total_qty) ? (int)$so->total_qty : 1;
                              $opsi_kirim = !empty($so->opsi_pengiriman) ? $so->opsi_pengiriman : (!empty($so->kurir_ekspedisi) ? $so->kurir_ekspedisi : 'Reguler');
                              $waktu_bayar = !empty($so->waktu_pembayaran) ? date('Y-m-d H:i', strtotime($so->waktu_pembayaran)) : date('Y-m-d H:i', strtotime($so->created_at ?? $so->tanggal_so));
                              $username_buyer = !empty($so->username_pembeli) ? $so->username_pembeli : strtolower(str_replace(' ', '_', $so->nama_pelanggan ?? 'buyer'));
                              $nama_penerima = !empty($so->nama_penerima) ? $so->nama_penerima : ($so->nama_pelanggan ?? '-');
                              $notelp = !empty($so->no_telepon) ? $so->no_telepon : '-';
                           ?>
                           <tr>
                              <td class="text-center"><input type="checkbox" class="check_item" value="<?= $so->id; ?>"></td>
                              <td>
                                 <strong class="text-primary"><a href="<?= site_url('administrator/penjualan_marketplace/view/' . $so->id); ?>"><?= _ent($display_no_pesanan); ?></a></strong><br>
                                 <small class="text-muted"><?= _ent($so->nomor_so); ?></small>
                              </td>
                              <td class="text-center">
                                 <span class="label label-info" style="font-size:10px; padding:3px 7px;">
                                    <?= _ent($display_status_pesanan); ?>
                                 </span>
                              </td>
                              <td><span class="text-muted" style="font-family:monospace;"><?= _ent($display_resi); ?></span></td>
                              <td class="text-center"><strong><?= $qty_item; ?></strong></td>
                              <td><small><?= _ent($opsi_kirim); ?></small></td>
                              <td><small><i class="fa fa-clock-o text-muted"></i> <?= $waktu_bayar; ?></small></td>
                              <td><strong><?= _ent($username_buyer); ?></strong></td>
                              <td><?= _ent($nama_penerima); ?></td>
                              <td><small class="text-muted"><?= _ent($notelp); ?></small></td>
                              <td class="text-center">
                                 <?php if (in_array($so->status, ['packing', 'confirmed'])): ?>
                                    <span class="label label-warning" style="font-size:10px; font-weight:700; padding:4px 8px; border-radius:3px;">PACKED</span>
                                 <?php else: ?>
                                    <span class="label <?= isset($status_color[$so->status]) ? $status_color[$so->status] : 'label-default'; ?>" style="font-size:10px; padding:3px 7px;">
                                       <?= strtoupper($so->status); ?>
                                    </span>
                                 <?php endif; ?>
                              </td>
                              <td class="text-center"><span class="text-muted">-</span></td>
                              <td class="text-center">
                                 <a href="<?= site_url('administrator/penjualan_marketplace/view/' . $so->id); ?>" class="btn btn-default btn-xs" title="Lihat Detail"><i class="fa fa-eye"></i></a>
                                 <a href="<?= site_url('administrator/penjualan_marketplace/export_pdf'); ?>" target="_blank" class="btn btn-info btn-xs" title="Cetak PDF"><i class="fa fa-print"></i></a>
                              </td>
                           </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr>
                              <td colspan="13" class="text-center text-muted" style="padding:40px;">
                                 <i class="fa fa-shopping-cart fa-3x" style="color:#e0e0e0; margin-bottom:10px;"></i><br>
                                 <span style="font-size:16px;">Belum ada data Penjualan Register All.</span><br>
                                 <small>Silakan import CSV Shopee/Tokopedia atau buat pesanan baru.</small>
                              </td>
                           </tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <div class="row" style="margin-top: 15px;">
                  <div class="col-md-6">
                     <span class="text-muted">Total: <strong><?= $so_counts; ?></strong> Pesanan Penjualan</span>
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

<!-- MODAL IMPORT PENJUALAN (SHOPEE / TOKOPEDIA) -->
<div class="modal fade" id="modalImportMarketplace" tabindex="-1" role="dialog" aria-labelledby="modalImportMarketplaceLabel">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="border-radius:8px;">
         <div class="modal-header style-primary" style="background:#00a65a; color:#fff; border-top-left-radius:8px; border-top-right-radius:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalImportMarketplaceLabel" style="font-weight:700;"><i class="fa fa-file-excel-o"></i> Import Penjualan Marketplace (Shopee / Tokopedia)</h4>
         </div>
         <div class="modal-body" style="padding:25px;">
            <!-- Tabs for Shopee & Tokopedia -->
            <ul class="nav nav-tabs" role="tablist" style="margin-bottom:20px;">
               <li role="presentation" class="active">
                  <a href="#shopee_tab" aria-controls="shopee_tab" role="tab" data-toggle="tab" style="font-weight:700; color:#ee4d2d;"><i class="fa fa-shopping-bag"></i> Import Shopee CSV</a>
               </li>
               <li role="presentation">
                  <a href="#tokopedia_tab" aria-controls="tokopedia_tab" role="tab" data-toggle="tab" style="font-weight:700; color:#42b549;"><i class="fa fa-shopping-cart"></i> Import Tokopedia CSV</a>
               </li>
            </ul>

            <div class="tab-content">
               <!-- SHOPEE TAB -->
               <div role="tabpanel" class="tab-pane active" id="shopee_tab">
                  <div class="callout callout-info" style="border-left-color: #ee4d2d; background-color:#fff5f2; color:#333;">
                     <h5 style="font-weight:700; color:#ee4d2d;"><i class="fa fa-info-circle"></i> Petunjuk Import CSV Shopee (45 Kolom):</h5>
                     <p style="font-size:12px;">Format file sesuai dengan export pesanan Shopee Seller Centre (`template shoppe 18 08 2026`). Menyertakan 45 kolom lengkap (<code>No. Pesanan</code>, <code>Status Pesanan</code>, <code>No. Resi</code>, <code>Username (Pembeli)</code>, <code>Alasan Pembatalan (tambahan kolom)</code>, <code>Waktu Pembayaran Dilakukan</code>, dll).</p>
                     <a href="<?= site_url('administrator/penjualan_marketplace/download_template_shopee'); ?>" class="btn btn-sm btn-danger btn-flat" style="margin-top:5px; background-color:#ee4d2d; border:none;"><i class="fa fa-download"></i> Download Template CSV Shopee (45 Kolom)</a>
                  </div>

                  <form action="<?= site_url('administrator/penjualan_marketplace/import_shopee'); ?>" method="POST" enctype="multipart/form-data">
                     <div class="form-group">
                        <label>Pilih File CSV Shopee (.csv):</label>
                        <input type="file" name="file_csv" class="form-control" accept=".csv" required>
                     </div>
                     <div class="text-right" style="margin-top:20px;">
                        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-flat" style="font-weight:700; background-color:#ee4d2d; border-color:#ee4d2d;"><i class="fa fa-upload"></i> Upload & Import Shopee CSV</button>
                     </div>
                  </form>
               </div>

               <!-- TOKOPEDIA TAB -->
               <div role="tabpanel" class="tab-pane" id="tokopedia_tab">
                  <div class="callout callout-success" style="border-left-color: #42b549; background-color:#f2faf3; color:#333;">
                     <h5 style="font-weight:700; color:#42b549;"><i class="fa fa-info-circle"></i> Petunjuk Import CSV Tokopedia:</h5>
                     <p style="font-size:12px;">Upload file CSV export laporan pesanan Tokopedia Seller Center. Kolom wajib: <code>No. Pesanan</code>, <code>Status Pesanan</code>, <code>No. Resi</code>, <code>Kurir</code>, <code>Username Pembeli</code>, dll.</p>
                     <a href="<?= site_url('administrator/penjualan_marketplace/download_template_tokopedia'); ?>" class="btn btn-sm btn-success btn-flat" style="margin-top:5px; background-color:#42b549; border:none;"><i class="fa fa-download"></i> Download Template CSV Tokopedia</a>
                  </div>

                  <form action="<?= site_url('administrator/penjualan_marketplace/import_tokopedia'); ?>" method="POST" enctype="multipart/form-data">
                     <div class="form-group">
                        <label>Pilih File CSV Tokopedia (.csv):</label>
                        <input type="file" name="file_csv" class="form-control" accept=".csv" required>
                     </div>
                     <div class="text-right" style="margin-top:20px;">
                        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-flat" style="font-weight:700; background-color:#42b549; border-color:#42b549;"><i class="fa fa-upload"></i> Upload & Import Tokopedia CSV</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- MODAL IMPORT PACKED -->
<div class="modal fade" id="modalImportPacked" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content" style="border-radius:8px;">
         <div class="modal-header" style="background:#00a65a; color:#fff;">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
            <h4 class="modal-title" style="font-weight:700;"><i class="fa fa-barcode"></i> Import Status Packed / Scan Barcode</h4>
         </div>
         <div class="modal-body">
            <p>Fitur Scan Resi / Update Status Packed Massal dari Gudang Pengemasan.</p>
            <div class="form-group">
               <label>Scan No. Resi / No. Pesanan:</label>
               <input type="text" class="form-control input-lg" placeholder="Scan Barcode No Resi di sini..." autofocus>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
         </div>
      </div>
   </div>
</div>

<script>
$(document).ready(function(){
   $('#check_all').click(function(){
      $('.check_item').prop('checked', this.checked);
   });

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
