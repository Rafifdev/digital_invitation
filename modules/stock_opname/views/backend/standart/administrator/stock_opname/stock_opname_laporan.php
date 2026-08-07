<section class="content-header">
   <h1>Laporan Selisih Stock Opname <small>Audit & Loss Log Per Lokasi & Produk</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/stock_opname'); ?>"><i class="fa fa-adjust"></i> Stock Opname</a></li>
      <li class="active">Laporan Selisih</li>
   </ol>
</section>

<section class="content">
<div class="box box-warning">
   <div class="box-header with-border">
      <h3 class="box-title"><i class="fa fa-filter"></i> Filter Laporan Selisih Stok Per Lokasi & Produk</h3>
   </div>
   <div class="box-body">
      <form action="<?= current_url(); ?>" method="GET">
         <div class="row">
            <div class="col-md-3">
               <div class="form-group">
                  <label>Filter Lokasi Gudang</label>
                  <select class="form-control chosen" name="gudang_id">
                     <option value="">-- Semua Gudang --</option>
                     <?php foreach($gudangs as $g): ?>
                        <option value="<?= $g->id; ?>" <?= $gudang_sel == $g->id ? 'selected' : ''; ?>>
                           <?= _ent($g->nama_gudang); ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>
            <div class="col-md-3">
               <div class="form-group">
                  <label>Filter Spesifik Produk</label>
                  <select class="form-control chosen" name="produk_id">
                     <option value="">-- Semua Produk --</option>
                     <?php foreach($produks as $p): ?>
                        <option value="<?= $p->id; ?>" <?= $produk_sel == $p->id ? 'selected' : ''; ?>>
                           [<?= _ent($p->kode_produk); ?>] <?= _ent($p->nama_produk); ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>
            <div class="col-md-2">
               <div class="form-group">
                  <label>Dari Tanggal</label>
                  <input type="date" class="form-control" name="date_start" value="<?= _ent($date_start); ?>">
               </div>
            </div>
            <div class="col-md-2">
               <div class="form-group">
                  <label>Sampai Tanggal</label>
                  <input type="date" class="form-control" name="date_end" value="<?= _ent($date_end); ?>">
               </div>
            </div>
            <div class="col-md-2">
               <div class="form-group">
                  <label>Status Item</label>
                  <select class="form-control" name="hanya_selisih">
                     <option value="1" <?= $this->input->get('hanya_selisih') != '0' ? 'selected' : ''; ?>>Hanya Selisih</option>
                     <option value="0" <?= $this->input->get('hanya_selisih') == '0' ? 'selected' : ''; ?>>Semua Item</option>
                  </select>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-12 text-right">
               <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i> Tampilkan Laporan</button>
               <a href="<?= current_url(); ?>" class="btn btn-default"><i class="fa fa-undo"></i> Reset Filter</a>
            </div>
         </div>
      </form>

      <hr>

      <!-- Metric Cards -->
      <?php
         $total_items = count($laporan);
         $total_loss_val = 0;
         $total_surplus_val = 0;
         foreach ($laporan as $row) {
            if ($row->selisih < 0) {
               $total_loss_val += abs($row->nilai_selisih);
            } else if ($row->selisih > 0) {
               $total_surplus_val += $row->nilai_selisih;
            }
         }
      ?>
      <div class="row">
         <div class="col-md-4">
            <div class="info-box bg-aqua">
               <span class="info-box-icon"><i class="fa fa-list"></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Total Baris Record</span>
                  <span class="info-box-number"><?= $total_items; ?> item</span>
               </div>
            </div>
         </div>
         <div class="col-md-4">
            <div class="info-box bg-red">
               <span class="info-box-icon"><i class="fa fa-arrow-down"></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Total Kerugian / Loss (Rp)</span>
                  <span class="info-box-number">Rp <?= number_format($total_loss_val, 0, ',', '.'); ?></span>
               </div>
            </div>
         </div>
         <div class="col-md-4">
            <div class="info-box bg-green">
               <span class="info-box-icon"><i class="fa fa-arrow-up"></i></span>
               <div class="info-box-content">
                  <span class="info-box-text">Total Kelebihan / Surplus (Rp)</span>
                  <span class="info-box-number">Rp <?= number_format($total_surplus_val, 0, ',', '.'); ?></span>
               </div>
            </div>
         </div>
      </div>

      <div class="table-responsive">
         <table class="table table-bordered table-striped">
            <thead>
               <tr class="bg-gray">
                  <th width="40" class="text-center">No</th>
                  <th>Nomor Opname</th>
                  <th>Tanggal</th>
                  <th>Lokasi Gudang</th>
                  <th>Kode &amp; Nama Produk</th>
                  <th class="text-right">Stok Sistem</th>
                  <th class="text-right">Stok Fisik</th>
                  <th class="text-right">Selisih Qty</th>
                  <th class="text-right">Nilai Selisih (Rp)</th>
                  <th>Alasan Selisih (Wajib)</th>
               </tr>
            </thead>
            <tbody>
               <?php if (!empty($laporan)): ?>
                  <?php $no = 1; foreach ($laporan as $l): ?>
                     <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><strong><?= _ent($l->nomor_opname); ?></strong></td>
                        <td><?= date('d/m/Y', strtotime($l->tanggal_opname)); ?></td>
                        <td><i class="fa fa-building-o text-muted"></i> <strong><?= _ent($l->nama_gudang); ?></strong></td>
                        <td><code><?= _ent($l->kode_produk); ?></code> - <strong><?= _ent($l->nama_produk); ?></strong></td>
                        <td class="text-right"><?= number_format($l->stok_sistem, 0, ',', '.'); ?></td>
                        <td class="text-right"><?= number_format($l->stok_fisik, 0, ',', '.'); ?></td>
                        <td class="text-right">
                           <strong class="<?= $l->selisih < 0 ? 'text-danger' : ($l->selisih > 0 ? 'text-success' : ''); ?>">
                              <?= ($l->selisih > 0 ? '+' : '') . number_format((float)$l->selisih, 0, ',', '.'); ?>
                           </strong>
                        </td>
                        <td class="text-right">Rp <?= number_format($l->nilai_selisih, 0, ',', '.'); ?></td>
                        <td><span class="label label-warning"><?= _ent($l->alasan_selisih ?: '-'); ?></span></td>
                     </tr>
                  <?php endforeach; ?>
               <?php else: ?>
                  <tr><td colspan="10" class="text-center text-muted">Belum ada data selisih stock opname sesuai filter.</td></tr>
               <?php endif; ?>
            </tbody>
         </table>
      </div>
   </div>
</div>
</section>
