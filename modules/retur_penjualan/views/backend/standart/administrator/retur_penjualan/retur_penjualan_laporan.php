<?php /* retur_penjualan_laporan.php - Analisis Pola Barang Bermasalah */ ?>
<style>
.kpi-card { background:#fff; border-radius:8px; padding:15px; margin-bottom:15px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.05); position:relative; }
.kpi-title { font-size:11px; text-transform:uppercase; color:#64748b; font-weight:600; }
.kpi-value { font-size:22px; font-weight:700; color:#1e293b; margin:4px 0; }
.kpi-sub { font-size:11px; color:#94a3b8; }
.kpi-icon { position:absolute; right:15px; top:18px; font-size:32px; opacity:0.15; }
</style>

<section class="content-header">
   <h1>Laporan Retur Penjualan per Produk <small>Analisis &amp; Pola Barang Bermasalah</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/retur_penjualan'); ?>">Retur Penjualan</a></li>
      <li class="active">Laporan per Produk</li>
   </ol>
</section>

<section class="content">
   <?php
      $sum_total_qty = 0;
      $sum_baik_qty  = 0;
      $sum_rusak_qty = 0;
      $sum_nominal   = 0;

      if (!empty($laporan)) {
         foreach ($laporan as $item_lap) {
            $sum_total_qty += $item_lap->qty_retur;
            $sum_nominal   += $item_lap->subtotal;
            if ($item_lap->kondisi == 'baik') {
               $sum_baik_qty += $item_lap->qty_retur;
            } else {
               $sum_rusak_qty += $item_lap->qty_retur;
            }
         }
      }
   ?>

   <!-- KPI Metric Summary -->
   <div class="row">
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 4px solid #3b82f6;">
            <div class="kpi-title">TOTAL DITERIMA RETUR</div>
            <div class="kpi-value"><?= number_format($sum_total_qty, 0, ',', '.'); ?> <small style="font-size:13px;">Unit</small></div>
            <div class="kpi-sub"><i class="fa fa-calculator"></i> Total Fisik Diterima</div>
            <i class="fa fa-refresh kpi-icon text-primary"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 4px solid #ef4444;">
            <div class="kpi-title">KONDISI RUSAK / CACAT</div>
            <div class="kpi-value text-danger"><?= number_format($sum_rusak_qty, 0, ',', '.'); ?> <small style="font-size:13px;">Unit</small></div>
            <div class="kpi-sub" style="color:#ef4444;"><i class="fa fa-warning"></i> Barang Afkir (Masuk Stok Rusak)</div>
            <i class="fa fa-times-circle kpi-icon text-danger"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 4px solid #10b981;">
            <div class="kpi-title">KONDISI BAIK (RESTOCK)</div>
            <div class="kpi-value text-success"><?= number_format($sum_baik_qty, 0, ',', '.'); ?> <small style="font-size:13px;">Unit</small></div>
            <div class="kpi-sub"><i class="fa fa-check-circle"></i> Masuk Kembali ke Stok Jual</div>
            <i class="fa fa-check-square-o kpi-icon text-success"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 4px solid #f59e0b;">
            <div class="kpi-title">TOTAL NOMINAL RETUR</div>
            <div class="kpi-value text-warning">Rp <?= number_format($sum_nominal, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-money"></i> Nilai Total Pengembalian</div>
            <i class="fa fa-calculator kpi-icon text-warning"></i>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary" style="border-radius:6px;">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-filter text-primary"></i> Filter Analisis Produk Bermasalah</h3>
            </div>
            <div class="box-body">
               <form action="<?= base_url('administrator/retur_penjualan/laporan'); ?>" method="GET">
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <label>Pilih Produk Specific:</label>
                           <select name="produk_id" class="form-control chosen">
                              <option value="">-- Semua Produk (Tampilkan Semua) --</option>
                              <?php foreach($produks as $p): ?>
                                 <option value="<?= $p->id; ?>" <?= ($this->input->get('produk_id') == $p->id) ? 'selected' : ''; ?>>
                                    <?= _ent($p->kode_produk . ' - ' . $p->nama_produk); ?>
                                 </option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label>Periode Mulai:</label>
                           <input type="date" name="date_start" class="form-control" value="<?= $this->input->get('date_start'); ?>">
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label>Periode Selesai:</label>
                           <input type="date" name="date_end" class="form-control" value="<?= $this->input->get('date_end'); ?>">
                        </div>
                     </div>
                     <div class="col-md-2" style="padding-top:25px;">
                        <button type="submit" class="btn btn-primary btn-block btn-flat" style="border-radius:4px;"><i class="fa fa-filter"></i> Analisis</button>
                     </div>
                  </div>
               </form>
            </div>
         </div>

         <div class="box box-warning" style="border-radius:6px;">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-list text-warning"></i> Rekapitulasi &amp; Pola Retur per Produk</h3>
            </div>
            <div class="box-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr style="background:#f8fafc;">
                           <th width="40" class="text-center">No</th>
                           <th>Tanggal Retur</th>
                           <th>No. Retur</th>
                           <th>Pelanggan</th>
                           <th>Produk &amp; Kode</th>
                           <th class="text-center">Kondisi Fisik</th>
                           <th class="text-right">Qty Retur</th>
                           <th class="text-right">Subtotal Nilai</th>
                           <th>Alasan Retur (Pola Masalah)</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (!empty($laporan)): ?>
                           <?php $no = 1; foreach ($laporan as $row): ?>
                              <tr>
                                 <td class="text-center"><?= $no++; ?></td>
                                 <td><i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime($row->tanggal_retur)); ?></td>
                                 <td><strong class="text-primary"><?= _ent($row->nomor_retur); ?></strong></td>
                                 <td><strong><?= _ent($row->nama_pelanggan); ?></strong></td>
                                 <td>
                                    <strong><?= _ent($row->nama_produk); ?></strong><br>
                                    <small class="text-muted"><i class="fa fa-barcode"></i> <?= _ent($row->kode_produk); ?></small>
                                 </td>
                                 <td class="text-center">
                                    <?php if ($row->kondisi == 'baik'): ?>
                                       <span class="label label-success"><i class="fa fa-check-circle"></i> BAIK (RESTOCK)</span>
                                    <?php else: ?>
                                       <span class="label label-danger"><i class="fa fa-times-circle"></i> RUSAK / AFKIR</span>
                                    <?php endif; ?>
                                 </td>
                                 <td class="text-right"><strong><?= number_format($row->qty_retur, 0, ',', '.'); ?> Unit</strong></td>
                                 <td class="text-right">Rp <?= number_format($row->subtotal, 0, ',', '.'); ?></td>
                                 <td>
                                    <?php if(!empty($row->alasan_utama)): ?>
                                       <span class="label label-default"><?= strtoupper(_ent($row->alasan_utama)); ?></span><br>
                                    <?php endif; ?>
                                    <small class="text-muted"><?= _ent($row->catatan_retur ?: ($row->alasan ?? '-')); ?></small>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr>
                              <td colspan="9" class="text-center text-muted" style="padding:30px;">
                                 <i class="fa fa-info-circle fa-2x"></i><br>Belum ada data retur produk untuk kriteria pencarian ini.
                              </td>
                           </tr>
                        <?php endif; ?>
                     </tbody>
                     <?php if (!empty($laporan)): ?>
                        <tfoot>
                           <tr style="background:#f1f5f9;">
                              <th colspan="6" class="text-right">TOTAL RETUR:</th>
                              <th class="text-right"><strong class="text-danger"><?= number_format($sum_total_qty, 0, ',', '.'); ?> Unit</strong></th>
                              <th class="text-right"><strong class="text-danger">Rp <?= number_format($sum_nominal, 0, ',', '.'); ?></strong></th>
                              <th></th>
                           </tr>
                        </tfoot>
                     <?php endif; ?>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
