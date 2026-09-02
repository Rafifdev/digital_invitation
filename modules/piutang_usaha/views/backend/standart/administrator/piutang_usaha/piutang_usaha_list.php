<?php /* piutang_usaha_list.php - Ledger Piutang Usaha (AR) */ ?>
<style>
.kpi-card { background:#fff; border-radius:8px; padding:15px; margin-bottom:15px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.05); position:relative; }
.kpi-title { font-size:11px; text-transform:uppercase; color:#64748b; font-weight:600; }
.kpi-value { font-size:20px; font-weight:700; color:#1e293b; margin:4px 0; }
.kpi-sub { font-size:11px; color:#94a3b8; }
.kpi-icon { position:absolute; right:15px; top:18px; font-size:28px; opacity:0.15; }
</style>

<section class="content-header">
   <h1>Piutang Usaha (Accounts Receivable) <small>Monitoring &amp; Pelunasan Tagihan Penjualan Pelanggan</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li>Keuangan</li>
      <li class="active">Piutang Usaha (AR)</li>
   </ol>
</section>

<section class="content">
   <!-- KPI Metric Summary -->
   <div class="row">
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 4px solid #3b82f6;">
            <div class="kpi-title">TOTAL HAK PIUTANG</div>
            <div class="kpi-value">Rp <?= number_format($stats['total_hak'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-file-text-o"></i> <?= number_format($stats['total_faktur'] ?? 0); ?> Total Faktur Penjualan</div>
            <i class="fa fa-calculator kpi-icon text-primary"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 4px solid #10b981;">
            <div class="kpi-title">TOTAL TELAH DITERIMA</div>
            <div class="kpi-value">Rp <?= number_format($stats['total_terima'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-check-circle"></i> Kas Masuk Terealisasi</div>
            <i class="fa fa-money kpi-icon text-success"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 4px solid #f59e0b;">
            <div class="kpi-title">SISA TAGIHAN BELUM LUNAS</div>
            <div class="kpi-value">Rp <?= number_format($stats['sisa_piutang'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-clock-o"></i> Saldo Piutang Aktif</div>
            <i class="fa fa-hourglass-half kpi-icon text-warning"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 4px solid #ef4444;">
            <div class="kpi-title">LEWAT JATUH TEMPO (OVERDUE)</div>
            <div class="kpi-value">Rp <?= number_format($stats['overdue_nominal'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub" style="color:#ef4444;"><i class="fa fa-warning"></i> <?= number_format($stats['overdue_count'] ?? 0); ?> Tagihan Mendesak Ditagih</div>
            <i class="fa fa-bell kpi-icon text-danger"></i>
         </div>
      </div>
   </div>

   <div class="box box-primary" style="border-radius:6px;">
      <div class="box-header with-border">
         <h3 class="box-title"><i class="fa fa-book text-primary"></i> Buku Besar Piutang Usaha &amp; Status Pelunasan</h3>
         <div class="box-tools pull-right">
            <a href="<?= site_url('administrator/piutang_usaha/kas_masuk'); ?>" class="btn btn-info btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-history"></i> Riwayat Kas Masuk (BKM)</a>
         </div>
      </div>
      <div class="box-body">
         <!-- Filter Bar -->
         <form method="GET" action="<?= base_url('administrator/piutang_usaha'); ?>">
            <div class="row" style="margin-bottom:15px;">
               <div class="col-md-3">
                  <select name="pelanggan_id" class="form-control chosen">
                     <option value="">-- Semua Pelanggan --</option>
                     <?php foreach($pelanggans as $p): ?>
                        <option value="<?= $p->id; ?>" <?= $this->input->get('pelanggan_id')==$p->id?'selected':''; ?>><?= _ent($p->nama_pelanggan); ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>
               <div class="col-md-3">
                  <select name="status" class="form-control">
                     <option value="">-- Semua Status --</option>
                     <option value="belum_bayar" <?= $this->input->get('status')=='belum_bayar'?'selected':''; ?>>Belum Bayar (Unpaid)</option>
                     <option value="sebagian" <?= $this->input->get('status')=='sebagian'?'selected':''; ?>>Bayar Sebagian (Partial)</option>
                     <option value="lunas" <?= $this->input->get('status')=='lunas'?'selected':''; ?>>Lunas (Paid)</option>
                  </select>
               </div>
               <div class="col-md-4">
                  <input type="text" class="form-control" name="q" value="<?= _ent($this->input->get('q')); ?>" placeholder="Cari No. Piutang / Pelanggan...">
               </div>
               <div class="col-md-2">
                  <button type="submit" class="btn btn-primary btn-block btn-flat" style="border-radius:4px;"><i class="fa fa-filter"></i> Filter</button>
               </div>
            </div>
         </form>

         <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
               <thead>
                  <tr style="background:#f8fafc;">
                     <th width="40" class="text-center">No</th>
                     <th>No. Tagihan Piutang</th>
                     <th>Pelanggan</th>
                     <th>Ref SO / Invoice</th>
                     <th>Tgl Faktur / Jatuh Tempo</th>
                     <th class="text-right" style="white-space: nowrap; min-width: 120px;">Total Tagihan</th>
                     <th class="text-right" style="white-space: nowrap; min-width: 110px;">Telah Diterima</th>
                     <th class="text-right" style="white-space: nowrap; min-width: 120px;">Sisa Tagihan</th>
                     <th class="text-center">Status</th>
                     <th width="100" class="text-center">Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php $no = $this->uri->segment(4) + 1; foreach($piutang_list as $row): ?>
                  <tr>
                     <td class="text-center"><?= $no++; ?></td>
                     <td><strong class="text-primary"><?= _ent($row->nomor_piutang); ?></strong></td>
                     <td>
                        <strong><?= _ent($row->nama_pelanggan ?? 'Umum'); ?></strong>
                        <br><small class="text-muted"><i class="fa fa-tag"></i> <?= _ent($row->kode_pelanggan ?? '-'); ?></small>
                     </td>
                     <td>
                        <?php if($row->nomor_so): ?><span class="label label-default">SO: <?= _ent($row->nomor_so); ?></span><br><?php endif; ?>
                        <?php if($row->nomor_invoice): ?><span class="label label-info">INV: <?= _ent($row->nomor_invoice); ?></span><?php endif; ?>
                     </td>
                     <td>
                        <?php 
                           $tgl_faktur = !empty($row->tanggal_invoice) ? $row->tanggal_invoice : $row->created_at;
                        ?>
                        <i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime($tgl_faktur)); ?>
                        <br>
                        <?php if(empty($row->tanggal_jatuh_tempo) || $row->tanggal_jatuh_tempo == '0000-00-00'): ?>
                           <small class="text-danger"><strong>JT: -</strong></small>
                        <?php else: ?>
                           <small class="text-danger"><strong>JT: <?= date('d/m/Y', strtotime($row->tanggal_jatuh_tempo)); ?></strong></small>
                        <?php endif; ?>
                     </td>
                     <td class="text-right" style="white-space: nowrap;">Rp <?= number_format($row->total, 0, ',', '.'); ?></td>
                     <td class="text-right text-success" style="white-space: nowrap;">Rp <?= number_format($row->terbayar, 0, ',', '.'); ?></td>
                     <td class="text-right text-danger" style="white-space: nowrap;"><strong>Rp <?= number_format($row->sisa, 0, ',', '.'); ?></strong></td>
                     <td class="text-center">
                        <?php 
                           if($row->status == 'lunas'){
                              echo '<span class="label label-success">LUNAS (PAID)</span>';
                           } else if($row->status == 'sebagian'){
                              echo '<span class="label label-warning">SEBAGIAN</span>';
                           } else {
                              echo '<span class="label label-danger">BELUM BAYAR (UNPAID)</span>';
                           }
                        ?>
                     </td>
                     <td class="text-center">
                        <?php if($row->sisa > 0): ?>
                           <button type="button" class="btn btn-success btn-xs btn-flat btn-bayar" data-id="<?= $row->id; ?>" data-nomor="<?= _ent($row->nomor_piutang); ?>" data-pelanggan="<?= _ent($row->nama_pelanggan); ?>" data-sisa="<?= $row->sisa; ?>" style="border-radius:3px;">
                              <i class="fa fa-money"></i> Terima Kas
                           </button>
                        <?php else: ?>
                           <span class="text-muted"><i class="fa fa-check-circle text-success"></i> Selesai</span>
                        <?php endif; ?>
                     </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(empty($piutang_list)): ?><tr><td colspan="10" class="text-center text-muted">Belum ada data piutang usaha.</td></tr><?php endif; ?>
               </tbody>
            </table>
         </div>

         <div class="row">
            <div class="col-md-6"><p class="text-muted">Total Data: <strong><?= number_format($piutang_counts); ?></strong> Record</p></div>
            <div class="col-md-6 text-right"><?= $pagination; ?></div>
         </div>
      </div>
   </div>
</section>

<!-- Modal Penerimaan Kas Masuk (BKM) -->
<div class="modal fade" id="modal_bayar" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content" style="border-radius:6px;">
         <div class="modal-header bg-success" style="color:#fff; border-top-left-radius:6px; border-top-right-radius:6px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><i class="fa fa-money"></i> Form Penerimaan Kas / Pelunasan Piutang (BKM)</h4>
         </div>
         <form id="form_terima_kas">
            <input type="hidden" name="piutang_id" id="bayar_piutang_id">
            <div class="modal-body">
               <div class="form-group">
                  <label>No. Tagihan Piutang</label>
                  <input type="text" class="form-control" id="bayar_nomor_piutang" readonly>
               </div>
               <div class="form-group">
                  <label>Pelanggan</label>
                  <input type="text" class="form-control" id="bayar_pelanggan" readonly>
               </div>
               <div class="form-group">
                  <label>Sisa Piutang Saat Ini</label>
                  <div class="input-group">
                     <span class="input-group-addon">Rp</span>
                     <input type="text" class="form-control" id="bayar_sisa_display" readonly style="font-weight:bold; color:#d9534f;">
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Tanggal Diterima <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_bayar" value="<?= date('Y-m-d'); ?>" required>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Akun Kas / Bank Tujuan <span class="text-danger">*</span></label>
                        <select class="form-control" name="akun_kas_bank" id="akun_kas_bank" required>
                           <option value="Kas Tunai Kasir">💵 Kas Tunai Kasir / Cash</option>
                           <option value="Kas Utama Gudang">🏢 Kas Utama Gudang</option>
                           <option value="Bank BCA" selected>🏦 Bank BCA - Operasional</option>
                           <option value="Bank Mandiri">🏦 Bank Mandiri - Penerimaan</option>
                           <option value="Bank BNI">🏦 Bank BNI</option>
                           <option value="Bank BRI">🏦 Bank BRI</option>
                           <option value="Bank CIMB Niaga">🏦 Bank CIMB Niaga</option>
                           <option value="Kas E-Wallet / QRIS">📱 Kas E-Wallet / QRIS</option>
                           <option value="Lainnya">📦 Akun Lainnya</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-control" name="metode_pembayaran" id="metode_pembayaran">
                           <option value="tunai">💵 Tunai / Cash</option>
                           <option value="bank_transfer" selected>🏦 Bank Transfer / M-Banking</option>
                           <option value="qris">📱 QRIS / E-Wallet (GoPay, OVO, Dana)</option>
                           <option value="debit_kredit">💳 Kartu Debit / Kartu Kredit</option>
                           <option value="giro">📄 Cek / Giro</option>
                           <option value="lainnya">📦 Lainnya</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>No. Ref Transfer / Giro</label>
                        <input type="text" class="form-control" name="nomor_referensi" placeholder="Contoh: TRX-998822 / Struk">
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <label>Nominal Diterima (Rp) <span class="text-danger">*</span></label>
                  <input type="text" class="form-control input-lg rupiah" name="nominal" id="bayar_nominal" required style="font-weight:bold; color:#270;">
               </div>
               <div class="form-group">
                  <label>Catatan Pelunasan</label>
                  <textarea class="form-control" name="catatan" rows="2" placeholder="Keterangan tambahan..."></textarea>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Batal</button>
               <button type="submit" id="btn_save_bayar" class="btn btn-success btn-flat"><i class="fa fa-save"></i> Simpan Kas Masuk (BKM)</button>
            </div>
         </form>
      </div>
   </div>
</div>

<script>
$(document).ready(function(){
   $('#metode_pembayaran').on('change', function(){
      var m = $(this).val();
      if (m === 'tunai') {
         $('#akun_kas_bank').val('Kas Tunai Kasir');
      } else if (m === 'qris') {
         $('#akun_kas_bank').val('Kas E-Wallet / QRIS');
      } else if (m === 'bank_transfer') {
         if ($('#akun_kas_bank').val().indexOf('Bank') === -1) {
            $('#akun_kas_bank').val('Bank BCA');
         }
      }
   });
   $(document).on('keyup input', '.rupiah', function(){
      var val = $(this).val().replace(/[^0-9]/g, '');
      if (val !== '') {
         $(this).val(parseInt(val, 10).toLocaleString('id-ID'));
      } else {
         $(this).val('');
      }
   });

   $('.btn-bayar').click(function(){
      var id = $(this).data('id');
      var nomor = $(this).data('nomor');
      var pelanggan = $(this).data('pelanggan');
      var sisa = parseFloat($(this).data('sisa')) || 0;
      var clean_sisa = Math.round(sisa);

      $('#bayar_piutang_id').val(id);
      $('#bayar_nomor_piutang').val(nomor);
      $('#bayar_pelanggan').val(pelanggan);
      $('#bayar_sisa_display').val(clean_sisa.toLocaleString('id-ID'));
      $('#bayar_nominal').val(clean_sisa.toLocaleString('id-ID'));
      $('#modal_bayar').modal('show');
   });

   $('#form_terima_kas').submit(function(e){
      e.preventDefault();
      var btn = $('#btn_save_bayar');
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
      $.ajax({
         url: '<?= site_url("administrator/piutang_usaha/bayar_save"); ?>',
         method: 'POST',
         data: $(this).serialize(),
         dataType: 'json',
         success: function(res) {
            if (res.success) {
               window.location.href = res.redirect;
            } else {
               swal("Gagal", res.message, "error");
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Kas Masuk (BKM)');
            }
         },
         error: function() {
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Kas Masuk (BKM)');
         }
      });
   });
});
</script>
