<?php /* kas_masuk_list.php - Penerimaan Kas Masuk (BKM) */ ?>
<section class="content-header">
   <h1>Penerimaan Kas Masuk (Bukti Kas Masuk - BKM) <small>Riwayat Penerimaan Uang Kas &amp; Bank Pelunasan Piutang</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/keuangan'); ?>">Keuangan &amp; Ledger</a></li>
      <li class="active">Penerimaan Kas (BKM)</li>
   </ol>
</section>

<section class="content">
   <div class="box box-success" style="border-radius:6px;">
      <div class="box-header with-border">
         <h3 class="box-title"><i class="fa fa-list text-success"></i> Daftar Voucher Kas Masuk (BKM)</h3>
         <div class="box-tools pull-right">
            <a href="<?= site_url('administrator/keuangan/piutang'); ?>" class="btn btn-warning btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-book"></i> Buku Piutang Usaha (AR)</a>
         </div>
      </div>
      <div class="box-body">
         <form method="GET" action="<?= base_url('administrator/keuangan/kas_masuk'); ?>">
            <div class="row" style="margin-bottom:15px;">
               <div class="col-md-4">
                  <select name="pelanggan_id" class="form-control chosen">
                     <option value="">-- Semua Pelanggan --</option>
                     <?php foreach($pelanggans as $p): ?>
                        <option value="<?= $p->id; ?>" <?= $this->input->get('pelanggan_id')==$p->id?'selected':''; ?>><?= _ent($p->nama_pelanggan); ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>
               <div class="col-md-5">
                  <input type="text" class="form-control" name="q" value="<?= _ent($this->input->get('q')); ?>" placeholder="Cari No. BKM / Pelanggan / Ref...">
               </div>
               <div class="col-md-3">
                  <button type="submit" class="btn btn-success btn-block btn-flat" style="border-radius:4px;"><i class="fa fa-filter"></i> Filter</button>
               </div>
            </div>
         </form>

         <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
               <thead>
                  <tr style="background:#f8fafc;">
                     <th width="40" class="text-center">No</th>
                     <th>No. Bukti Kas Masuk (BKM)</th>
                     <th>Tanggal Terima</th>
                     <th>Pelanggan</th>
                     <th>No. Tagihan Piutang</th>
                     <th>Akun Kas / Bank</th>
                     <th>Metode &amp; Ref</th>
                     <th class="text-right">Nominal Diterima (Rp)</th>
                     <th>Petugas</th>
                     <th width="80" class="text-center">Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php $no = 1; foreach($bkm_list as $row): ?>
                  <tr>
                     <td class="text-center"><?= $no++; ?></td>
                     <td><strong class="text-success"><?= _ent($row->nomor_bkm); ?></strong></td>
                     <td><i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime($row->tanggal_bayar)); ?></td>
                     <td>
                        <strong><?= _ent($row->nama_pelanggan ?? 'Umum'); ?></strong>
                        <br><small class="text-muted"><i class="fa fa-tag"></i> <?= _ent($row->kode_pelanggan ?? '-'); ?></small>
                     </td>
                     <td><span class="label label-default"><?= _ent($row->nomor_piutang ?? '-'); ?></span></td>
                     <td><strong class="text-primary"><i class="fa fa-bank"></i> <?= _ent($row->akun_kas_bank); ?></strong></td>
                     <td>
                        <span class="label label-info"><?= strtoupper(str_replace('_', ' ', $row->metode_pembayaran)); ?></span>
                        <?php if($row->nomor_referensi): ?><br><small class="text-muted">Ref: <?= _ent($row->nomor_referensi); ?></small><?php endif; ?>
                     </td>
                     <td class="text-right"><strong style="color:#10b981; font-size:14px;">Rp <?= number_format($row->nominal, 0, ',', '.'); ?></strong></td>
                     <td><?= _ent($row->created_by ?? 'system'); ?></td>
                     <td class="text-center">
                        <a href="<?= site_url('administrator/piutang_usaha/cetak_bkm/' . $row->id); ?>" class="btn btn-info btn-xs btn-flat" title="Cetak Voucher BKM" style="border-radius:3px;">
                           <i class="fa fa-print"></i> Cetak
                        </a>
                     </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(empty($bkm_list)): ?><tr><td colspan="10" class="text-center text-muted">Belum ada transaksi kas masuk.</td></tr><?php endif; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</section>
