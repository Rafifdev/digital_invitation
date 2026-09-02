<style>
/* ===== Dashboard Metafora - Premium Layout ===== */
.dash-stat-card {
   border-radius: 8px;
   padding: 20px 18px;
   margin-bottom: 20px;
   color: #fff;
   position: relative;
   overflow: hidden;
   cursor: pointer;
   box-shadow: 0 4px 14px rgba(0,0,0,0.13);
   transition: transform .2s, box-shadow .2s;
}
.dash-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 22px rgba(0,0,0,0.18); }
.dash-stat-card .ds-num { font-size: 36px; font-weight: 700; line-height: 1.1; margin: 4px 0 2px; }
.dash-stat-card .ds-label { font-size: 13px; font-weight: 600; letter-spacing: .5px; opacity: .92; }
.dash-stat-card .ds-sub { font-size: 11px; opacity: .78; margin-top: 6px; }
.dash-stat-card .ds-icon { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-size: 56px; opacity: .15; }
.dash-stat-card .ds-badge { font-size: 10px; background: rgba(255,255,255,.2); border-radius: 10px; padding: 2px 8px; display: inline-block; margin-top: 4px; }

.dash-flow-card {
   border-radius: 8px;
   padding: 18px 20px;
   margin-bottom: 20px;
   color: #fff;
   position: relative;
   overflow: hidden;
   box-shadow: 0 4px 14px rgba(0,0,0,0.12);
}
.dash-flow-card .df-icon-wrap {
   width: 54px; height: 54px;
   border-radius: 50%;
   background: rgba(255,255,255,.2);
   display: flex; align-items: center; justify-content: center;
   font-size: 24px;
   float: left;
   margin-right: 15px;
}
.dash-flow-card .df-body { overflow: hidden; }
.dash-flow-card .df-num { font-size: 32px; font-weight: 700; line-height: 1.1; }
.dash-flow-card .df-label { font-size: 12px; font-weight: 600; letter-spacing: .5px; opacity: .88; margin-bottom: 2px; }
.dash-flow-card .df-desc { font-size: 11px; opacity: .75; clear: both; margin-top: 12px; border-top: 1px solid rgba(255,255,255,.18); padding-top: 8px; }

.bg-dash-blue { background: linear-gradient(135deg, #1565C0, #1E88E5); }
.bg-dash-green { background: linear-gradient(135deg, #2E7D32, #43A047); }
.bg-dash-amber { background: linear-gradient(135deg, #E65100, #FB8C00); }
.bg-dash-red { background: linear-gradient(135deg, #B71C1C, #E53935); }
.bg-flow-teal  { background: linear-gradient(135deg, #00695C, #26A69A); }
.bg-flow-green { background: linear-gradient(135deg, #1B5E20, #388E3C); }
.bg-flow-orange { background: linear-gradient(135deg, #BF360C, #F4511E); }

.low-stock-box { border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,.08); border: none; }
.low-stock-box .box-header { background: #fff7e6; border-bottom: 2px solid #ffc107; border-radius: 8px 8px 0 0; }
.low-stock-box .box-title { color: #856404; font-weight: 700; }
.low-stock-box table thead tr { background: linear-gradient(90deg,#fff3cd,#ffe8a1); }
.low-stock-box table thead th { border-bottom: 2px solid #ffc107; color: #5d4037; font-size: 12px; text-transform: uppercase; letter-spacing: .4px; }
.low-stock-box .btn-auto-restock {
   background: linear-gradient(135deg, #1B5E20, #43A047);
   color: #fff; border: none; border-radius: 4px;
   padding: 4px 10px; font-size: 11px; font-weight: 600;
   transition: opacity .2s;
}
.low-stock-box .btn-auto-restock:hover { opacity: .85; color: #fff; }

.dash-section-title {
   font-size: 11px; font-weight: 700; color: #888;
   letter-spacing: 1px; text-transform: uppercase;
   margin: 10px 0 12px; padding-left: 4px;
   border-left: 3px solid #1E88E5;
}
</style>

<section class="content-header">
    <h1><?= cclang('dashboard') ?> <small><?= cclang('control_panel') ?></small></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> <?= cclang('home') ?></a></li>
        <li class="active"><?= cclang('dashboard') ?></li>
    </ol>
</section>

<section class="content">

    <p class="dash-section-title"><i class="fa fa-bar-chart"></i> Ringkasan Transaksi</p>

    <!-- Row 1: Quick Stats -->
    <div class="row">
       <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="dash-stat-card bg-dash-blue" onclick="goUrl('administrator/sales_order')">
             <div class="ds-label">Sales Order</div>
             <div class="ds-num"><?= isset($total_so) ? $total_so : 0; ?></div>
             <div class="ds-sub">Total dokumen SO aktif</div>
             <div class="ds-icon"><i class="fa fa-shopping-cart"></i></div>
          </div>
       </div>
       <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="dash-stat-card bg-dash-green" onclick="goUrl('administrator/purchase_order')">
             <div class="ds-label">Purchase Order</div>
             <div class="ds-num"><?= isset($total_po) ? $total_po : 0; ?></div>
             <div class="ds-sub">Total dokumen PO</div>
             <div class="ds-icon"><i class="fa fa-truck"></i></div>
          </div>
       </div>
       <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="dash-stat-card bg-dash-amber" onclick="goUrl('administrator/produk')">
             <div class="ds-label">Master Produk</div>
             <div class="ds-num"><?= isset($total_produk) ? $total_produk : 0; ?></div>
             <div class="ds-sub">Produk aktif terdaftar</div>
             <div class="ds-icon"><i class="fa fa-cubes"></i></div>
          </div>
       </div>
       <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="dash-stat-card bg-dash-red" onclick="goUrl('administrator/gudang')">
             <div class="ds-label">Master Gudang</div>
             <div class="ds-num"><?= isset($total_gudang) ? $total_gudang : 0; ?></div>
             <div class="ds-sub">Lokasi gudang aktif</div>
             <div class="ds-icon"><i class="fa fa-building"></i></div>
          </div>
       </div>
    </div>

    <p class="dash-section-title"><i class="fa fa-exchange"></i> Arus Barang Real-Time (Hari Ini)</p>

    <!-- Row 2: Realtime Flow -->
    <div class="row">
       <div class="col-md-4 col-sm-12">
          <div class="dash-flow-card bg-flow-teal">
             <div class="df-icon-wrap"><i class="fa fa-database"></i></div>
             <div class="df-body">
                <div class="df-label">Total Stok Tersedia</div>
                <div class="df-num"><?= number_format($total_stok ?? 0, 0, ',', '.'); ?> <small style="font-size:14px;font-weight:400;">pcs</small></div>
             </div>
             <div class="df-desc"><i class="fa fa-info-circle"></i> Akumulasi stok baik di semua gudang aktif</div>
          </div>
       </div>
       <div class="col-md-4 col-sm-12">
          <div class="dash-flow-card bg-flow-green">
             <div class="df-icon-wrap"><i class="fa fa-arrow-circle-down"></i></div>
             <div class="df-body">
                <div class="df-label">Barang Masuk Hari Ini</div>
                <div class="df-num">
                   <?php $masuk = (int)($masuk_hari_ini ?? 0); ?>
                   <?= $masuk > 0 ? '+' : ''; ?><?= number_format($masuk, 0, ',', '.'); ?> <small style="font-size:14px;font-weight:400;">pcs</small>
                </div>
             </div>
             <div class="df-desc"><i class="fa fa-file-text-o"></i> Dari Penerimaan Barang (GR) yang dikonfirmasi hari ini</div>
          </div>
       </div>
       <div class="col-md-4 col-sm-12">
          <div class="dash-flow-card bg-flow-orange">
             <div class="df-icon-wrap"><i class="fa fa-arrow-circle-up"></i></div>
             <div class="df-body">
                <div class="df-label">Barang Keluar Hari Ini</div>
                <div class="df-num">
                   <?php $keluar = abs((int)($keluar_hari_ini ?? 0)); ?>
                   <?= number_format($keluar, 0, ',', '.'); ?> <small style="font-size:14px;font-weight:400;">pcs</small>
                </div>
             </div>
             <div class="df-desc"><i class="fa fa-file-text-o"></i> Berdasarkan Sales Order & Invoice yang dikirim hari ini</div>
          </div>
       </div>
    </div>

    <p class="dash-section-title"><i class="fa fa-exclamation-triangle"></i> Peringatan Stok</p>

    <!-- Row 3: Low Stock Alert -->
    <div class="row">
       <div class="col-md-12">
          <div class="box low-stock-box">
             <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Peringatan Stok Minimum &mdash; Low Stock Alert</h3>
                <div class="box-tools pull-right">
                   <?php if (!empty($low_stocks)): ?>
                      <span class="label label-danger" style="font-size:11px; padding:4px 10px;"><?= count($low_stocks); ?> Produk Kritis</span>
                   <?php else: ?>
                      <span class="label label-success" style="font-size:11px; padding:4px 10px;"><i class="fa fa-check"></i> Stok Aman</span>
                   <?php endif; ?>
                   <a href="<?= site_url('administrator/stok'); ?>" class="btn btn-sm btn-default" style="margin-left:8px;"><i class="fa fa-database"></i> Lihat Semua Stok</a>
                </div>
             </div>
             <div class="box-body" style="padding:0;">
                <?php if (!empty($low_stocks)): ?>
                   <div class="table-responsive">
                      <table class="table table-striped" style="margin-bottom:0;">
                         <thead>
                            <tr>
                               <th class="text-center" width="40">No</th>
                               <th>Kode Produk</th>
                               <th>Nama Produk</th>
                               <th class="text-center">Stok Saat Ini</th>
                               <th class="text-center">Stok Minimum</th>
                               <th class="text-center">Status</th>
                               <th class="text-center" width="160">Aksi</th>
                            </tr>
                         </thead>
                         <tbody>
                            <?php $no = 1; foreach ($low_stocks as $ls): ?>
                            <tr>
                               <td class="text-center"><?= $no++; ?></td>
                               <td><code style="background:#f0f0f0; padding:3px 7px; border-radius:3px;"><?= _ent($ls->kode_produk); ?></code></td>
                               <td><strong><?= _ent($ls->nama_produk); ?></strong></td>
                               <td class="text-center">
                                  <span style="font-size:18px; font-weight:700; color:<?= $ls->total_stok <= 0 ? '#e53935' : '#fb8c00'; ?>">
                                     <?= number_format($ls->total_stok, 0, ',', '.'); ?>
                                  </span>
                               </td>
                               <td class="text-center text-muted"><?= number_format($ls->stok_minimum, 0, ',', '.'); ?></td>
                               <td class="text-center">
                                  <?php if ($ls->total_stok <= 0): ?>
                                     <span class="label label-danger" style="padding:4px 9px; font-size:11px;"><i class="fa fa-times-circle"></i> Habis</span>
                                  <?php else: ?>
                                     <span class="label label-warning" style="padding:4px 9px; font-size:11px;"><i class="fa fa-exclamation-circle"></i> Menipis</span>
                                  <?php endif; ?>
                               </td>
                               <td class="text-center">
                                  <a href="<?= site_url('administrator/purchase_order/auto_restock/' . $ls->id); ?>"
                                     class="btn-auto-restock"
                                     onclick="return confirm('Sistem akan membuat Draft PO otomatis untuk barang ini. Lanjutkan?')">
                                     <i class="fa fa-bolt"></i> Auto Restock
                                  </a>
                                  &nbsp;
                                  <a href="<?= site_url('administrator/stok/kartu/' . $ls->id); ?>" class="btn btn-xs btn-default" title="Kartu Stok">
                                     <i class="fa fa-list-alt"></i>
                                  </a>
                               </td>
                            </tr>
                            <?php endforeach; ?>
                         </tbody>
                      </table>
                   </div>
                <?php else: ?>
                   <div class="text-center" style="padding: 30px; color: #5d4037;">
                      <i class="fa fa-check-circle" style="font-size: 42px; color: #43a047; display:block; margin-bottom:10px;"></i>
                      <strong>Semua Stok Aman</strong><br>
                      <small class="text-muted">Tidak ada produk yang berada di bawah batas stok minimum.</small>
                   </div>
                <?php endif; ?>
             </div>
          </div>
       </div>
    </div>

     <p class="dash-section-title"><i class="fa fa-bell text-warning"></i> Notifikasi Real-Time Retur Penjualan &amp; Pengembalian Barang</p>

     <!-- Row 4: Real-Time Retur Penjualan Widget -->
     <div class="row">
        <div class="col-md-12">
           <div class="box box-warning" style="border-radius:8px; border-top: 3px solid #f39c12;">
              <div class="box-header with-border" style="background:#fcf8e3; border-radius:8px 8px 0 0;">
                 <h3 class="box-title" style="color:#8a6d3b; font-weight:700;"><i class="fa fa-refresh text-warning"></i> Transaksi Retur Penjualan Terbaru (Real-Time Feed)</h3>
                 <div class="box-tools pull-right">
                    <a href="<?= site_url('administrator/retur_penjualan/add'); ?>" class="btn btn-xs btn-warning" style="font-weight:600;"><i class="fa fa-plus"></i> Input Retur Baru</a>
                    <a href="<?= site_url('administrator/retur_penjualan/laporan'); ?>" class="btn btn-xs btn-default" style="margin-left:5px;"><i class="fa fa-bar-chart"></i> Laporan Analytics</a>
                 </div>
              </div>
              <div class="box-body" style="padding:0;">
                 <?php if (!empty($recent_returs)): ?>
                    <div class="table-responsive">
                       <table class="table table-striped table-hover" style="margin-bottom:0;">
                          <thead>
                             <tr style="background:#fafafa;">
                                <th class="text-center" width="40">No</th>
                                <th>No. Retur</th>
                                <th>Pelanggan</th>
                                <th>Gudang Tujuan</th>
                                <th>Tgl Retur</th>
                                <th>Tindak Lanjut</th>
                                <th class="text-right">Total Nominal</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" width="100">Aksi</th>
                             </tr>
                          </thead>
                          <tbody>
                             <?php $r_no = 1; foreach ($recent_returs as $ret): ?>
                             <tr>
                                <td class="text-center"><?= $r_no++; ?></td>
                                <td><strong class="text-primary"><?= _ent($ret->nomor_retur); ?></strong></td>
                                <td><strong><?= _ent($ret->nama_pelanggan ?? 'Pelanggan Umum'); ?></strong></td>
                                <td><?= _ent($ret->nama_gudang ?? '-'); ?></td>
                                <td><i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime($ret->tanggal_retur)); ?></td>
                                <td>
                                   <?php if ($ret->tindak_lanjut == 'tukar_barang'): ?>
                                      <span class="label label-info"><i class="fa fa-refresh"></i> Tukar Barang</span>
                                   <?php elseif ($ret->tindak_lanjut == 'refund'): ?>
                                      <span class="label label-danger"><i class="fa fa-money"></i> Refund Dana</span>
                                   <?php else: ?>
                                      <span class="label label-warning"><i class="fa fa-star"></i> Kredit Poin</span>
                                   <?php endif; ?>
                                </td>
                                <td class="text-right"><strong class="text-danger">Rp <?= number_format($ret->total, 0, ',', '.'); ?></strong></td>
                                <td class="text-center">
                                   <?php if ($ret->status == 'selesai' || $ret->status == 'approved'): ?>
                                      <span class="label label-success"><i class="fa fa-check"></i> PROSES SELESAI</span>
                                   <?php else: ?>
                                      <span class="label label-warning"><i class="fa fa-clock-o"></i> DRAFT / MENUNGGU</span>
                                   <?php endif; ?>
                                </td>
                                <td class="text-center">
                                   <a href="<?= site_url('administrator/retur_penjualan/view/' . $ret->id); ?>" class="btn btn-xs btn-primary btn-flat" style="border-radius:3px;">
                                      <i class="fa fa-eye"></i> Detail
                                   </a>
                                </td>
                             </tr>
                             <?php endforeach; ?>
                          </tbody>
                       </table>
                    </div>
                 <?php else: ?>
                    <div class="text-center" style="padding: 25px; color: #8a6d3b;">
                       <i class="fa fa-info-circle" style="font-size: 32px; color: #f39c12; display:block; margin-bottom:8px;"></i>
                       <strong>Belum Ada Notifikasi Retur Penjualan Baru</strong><br>
                       <small class="text-muted">Setiap pengembalian barang dari pelanggan akan masuk secara otomatis ke notifikasi ini.</small>
                    </div>
                 <?php endif; ?>
              </div>
           </div>
        </div>
     </div>

     <?php cicool()->eventListen('dashboard_content_bottom'); ?>
</section>
