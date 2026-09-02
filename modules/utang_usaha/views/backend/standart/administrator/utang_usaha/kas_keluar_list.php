<?php /* kas_keluar_list.php - Pengeluaran Kas (BKK / Disbursement) */ ?>
<section class="content-header">
   <h1>Pembayaran Kas Keluar (Disbursement) <small>Riwayat Pengeluaran Uang Kas & Bank Pelunasan Utang</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/utang_usaha'); ?>"><i class="fa fa-book"></i> Utang Usaha</a></li>
      <li class="active">Kas Keluar (BKK)</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-success">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-money"></i> Daftar Voucher Kas Keluar (BKK)</h3>
               <div class="box-tools pull-right">
                  <a href="<?= site_url('administrator/utang_usaha'); ?>" class="btn btn-primary btn-sm btn-flat">
                     <i class="fa fa-book"></i> Buku Utang Usaha
                  </a>
               </div>
            </div>
            <div class="box-body">
               <form action="<?= base_url('administrator/utang_usaha/kas_keluar'); ?>" method="get">
                  <div class="row style-select mb-3" style="margin-bottom: 15px;">
                     <div class="col-sm-3">
                        <label>Pilih Supplier:</label>
                        <select class="form-control chosen" name="supplier_id">
                           <option value="">-- Semua Supplier --</option>
                           <?php foreach($suppliers as $s): ?>
                           <option value="<?= $s->id; ?>" <?= $this->input->get('supplier_id') == $s->id ? 'selected':''; ?>><?= _ent($s->nama_supplier); ?></option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="col-sm-3">
                        <label>Metode Pembayaran:</label>
                        <select class="form-control" name="metode">
                           <option value="">-- Semua Metode --</option>
                           <option value="bank_transfer" <?= $this->input->get('metode') == 'bank_transfer' ? 'selected':''; ?>>Bank Transfer</option>
                           <option value="tunai" <?= $this->input->get('metode') == 'tunai' ? 'selected':''; ?>>Tunai / Kas</option>
                           <option value="cek_giro" <?= $this->input->get('metode') == 'cek_giro' ? 'selected':''; ?>>Cek / Giro</option>
                        </select>
                     </div>
                     <div class="col-sm-4">
                        <label>Cari Nomor / Keyword:</label>
                        <input type="text" class="form-control" name="q" placeholder="Cari No. BKK / Tagihan / Supplier..." value="<?= _ent($this->input->get('q')); ?>">
                     </div>
                     <div class="col-sm-2" style="padding-top: 25px;">
                        <button type="submit" class="btn btn-flat btn-info btn-block"><i class="fa fa-filter"></i> Filter</button>
                     </div>
                  </div>
               </form>
               <br>

               <div class="table-responsive">
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr>
                           <th width="40" class="text-center">No</th>
                           <th>No. Bukti Kas Keluar</th>
                           <th>Tanggal Bayar</th>
                           <th>Supplier</th>
                           <th>No. Tagihan Utang</th>
                           <th>Akun Kas / Bank</th>
                           <th>Metode & Referensi</th>
                           <th class="text-center">Tipe Transaksi</th>
                           <th class="text-right">Nominal Keluar (Rp)</th>
                           <th>Dicatat Oleh</th>
                           <th width="100" class="text-center">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (!empty($kas_keluars)): ?>
                           <?php $no = $this->uri->segment(4) + 1; foreach ($kas_keluars as $b): ?>
                           <tr>
                              <td class="text-center"><?= $no++; ?></td>
                              <td><strong class="text-primary"><?= _ent($b->nomor_bkk); ?></strong></td>
                              <td><i class="fa fa-calendar"></i> <?= date('d/m/Y', strtotime($b->tanggal_bayar)); ?></td>
                              <td>
                                 <strong><?= _ent($b->nama_supplier); ?></strong><br>
                                 <small class="text-muted"><i class="fa fa-tag"></i> <?= _ent($b->kode_supplier); ?></small>
                              </td>
                              <td><span class="label label-default"><i class="fa fa-file-text-o"></i> <?= _ent($b->nomor_utang ?? '-'); ?></span></td>
                              <td><i class="fa fa-bank"></i> <strong><?= _ent($b->akun_kas_bank); ?></strong></td>
                              <td>
                                 <span class="label label-info"><?= strtoupper(str_replace('_', ' ', $b->metode_pembayaran)); ?></span>
                                 <?php if($b->nomor_referensi): ?><br><small class="text-muted"><?= _ent($b->nomor_referensi); ?></small><?php endif; ?>
                              </td>
                              <td class="text-center">
                                 <span class="badge bg-red" style="font-size:10px;">KREDIT KAS</span><br>
                                 <span class="badge bg-blue" style="font-size:10px;">DEBIT UTANG</span>
                              </td>
                              <td class="text-right"><strong class="text-danger">Rp <?= number_format($b->nominal, 0, ',', '.'); ?></strong></td>
                              <td><?= _ent($b->created_by); ?></td>
                              <td class="text-center">
                                 <a href="<?= site_url('administrator/utang_usaha/cetak_bkk/' . $b->id); ?>" target="_blank" class="btn btn-default btn-xs" title="Cetak Voucher BKK PDF">
                                    <i class="fa fa-print"></i> Cetak
                                 </a>
                              </td>
                           </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr><td colspan="11" class="text-center text-muted">Data pengeluaran kas keluar tidak ditemukan.</td></tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <div class="row" style="margin-top:15px;">
                  <div class="col-md-6"><span class="text-muted">Total: <strong><?= $kas_keluar_count; ?></strong> transaksi kas keluar</span></div>
                  <div class="col-md-6"><div class="pull-right"><?= $pagination; ?></div></div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
