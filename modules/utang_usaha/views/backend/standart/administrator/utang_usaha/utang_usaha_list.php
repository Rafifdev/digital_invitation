<?php /* utang_usaha_list.php - Ledger Utang Usaha (AP) Modern UI */ ?>
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
.kpi-card.green  { border-left-color: #00a65a; }
.kpi-card.purple { border-left-color: #605ca8; }
.kpi-card.red    { border-left-color: #dd4b39; }

.kpi-title { font-size: 11px; text-transform: uppercase; font-weight: 700; color: #888; letter-spacing: 0.5px; }
.kpi-value { font-size: 20px; font-weight: 700; color: #222; margin: 4px 0; }
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
   <h1>Utang Usaha (Accounts Payable) <small>Monitoring & Pembayaran Tagihan Supplier</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Utang Usaha</li>
   </ol>
</section>

<section class="content">
   <!-- 4 Modern Minimalist KPI Cards -->
   <div class="row">
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card">
            <div class="kpi-title">Total Kewajiban Utang</div>
            <div class="kpi-value">Rp <?= number_format($stats['total_kewajiban'], 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-calculator"></i> <?= $stats['total_faktur']; ?> Tagihan Faktur</div>
            <i class="fa fa-calculator kpi-icon text-info"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card green">
            <div class="kpi-title">Total Telah Dibayar</div>
            <div class="kpi-value">Rp <?= number_format($stats['total_dibayar'], 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-check-circle"></i> Kas Keluar Terealikasi</div>
            <i class="fa fa-money kpi-icon text-success"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card purple">
            <div class="kpi-title">Sisa Tagihan Belum Lunas</div>
            <div class="kpi-value">Rp <?= number_format($stats['sisa_tagihan'], 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-hourglass-half"></i> Saldo Utang Aktif</div>
            <i class="fa fa-hourglass-half kpi-icon text-purple"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card red">
            <div class="kpi-title">Lewat Jatuh Tempo (Overdue)</div>
            <div class="kpi-value">Rp <?= number_format($stats['overdue_nominal'], 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-warning"></i> <?= $stats['overdue_count']; ?> Tagihan Overdue</div>
            <i class="fa fa-bell kpi-icon text-danger"></i>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary" style="border-radius:6px; border-top: 3px solid #3c8dbc;">
            <div class="box-header with-border" style="padding: 15px 20px;">
               <h3 class="box-title" style="font-weight:600;"><i class="fa fa-book text-primary"></i> Buku Besar Utang Usaha & Status Pelunasan</h3>
               <div class="box-tools pull-right">
                  <a href="<?= site_url('administrator/utang_usaha/kas_keluar'); ?>" class="btn btn-info btn-sm btn-flat" style="border-radius:4px;">
                     <i class="fa fa-list-alt"></i> Riwayat Kas Keluar (BKK)
                  </a>
               </div>
            </div>
            <div class="box-body" style="padding: 20px;">
               <!-- Clean Filter Bar -->
               <div class="clean-filter-box">
                  <form action="<?= base_url('administrator/utang_usaha/index'); ?>" method="get">
                     <div class="row">
                        <div class="col-md-3 col-sm-6">
                           <label class="text-muted" style="font-size:12px;">Pilih Supplier:</label>
                           <select class="form-control chosen" name="supplier_id">
                              <option value="">-- Semua Supplier --</option>
                              <?php foreach($suppliers as $s): ?>
                              <option value="<?= $s->id; ?>" <?= $this->input->get('supplier_id') == $s->id ? 'selected':''; ?>><?= _ent($s->nama_supplier); ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                           <label class="text-muted" style="font-size:12px;">Status Pelunasan:</label>
                           <select class="form-control" name="status">
                              <option value="">-- Semua Status --</option>
                              <option value="belum_bayar" <?= $this->input->get('status') == 'belum_bayar' ? 'selected':''; ?>>Belum Bayar (Unpaid)</option>
                              <option value="sebagian" <?= $this->input->get('status') == 'sebagian' ? 'selected':''; ?>>Sebagian (Partial)</option>
                              <option value="lunas" <?= $this->input->get('status') == 'lunas' ? 'selected':''; ?>>Lunas (Paid)</option>
                           </select>
                        </div>
                        <div class="col-md-4 col-sm-8">
                           <label class="text-muted" style="font-size:12px;">Cari Nomor / Keyword:</label>
                           <input type="text" class="form-control" name="q" placeholder="Cari No. Utang / Supplier / GRN..." value="<?= _ent($this->input->get('q')); ?>">
                        </div>
                        <div class="col-md-2 col-sm-4" style="padding-top: 23px;">
                           <button type="submit" class="btn btn-primary btn-flat btn-block" style="border-radius:4px;"><i class="fa fa-filter"></i> Filter</button>
                        </div>
                     </div>
                  </form>
               </div>

               <div class="table-responsive">
                  <table class="table table-hover table-striped table-bordered" style="vertical-align: middle;">
                     <thead>
                        <tr style="background:#f8fafc; font-size:12px; color:#555;">
                           <th width="40" class="text-center">No</th>
                           <th>No. Tagihan Utang</th>
                           <th>Supplier</th>
                           <th>Ref Penerimaan (GRN)</th>
                           <th>Tgl Jatuh Tempo</th>
                           <th class="text-right">Total Tagihan</th>
                           <th class="text-right">Telah Dibayar</th>
                           <th class="text-right">Sisa Tagihan</th>
                           <th class="text-center">Posisi Akun</th>
                           <th class="text-center">Status</th>
                           <th width="100" class="text-center">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (!empty($utangs)): ?>
                           <?php $no = $this->uri->segment(4) + 1; foreach ($utangs as $u): ?>
                           <tr>
                              <td class="text-center"><?= $no++; ?></td>
                              <td><strong class="text-primary"><a href="javascript:void(0);"><?= _ent($u->nomor_utang); ?></a></strong></td>
                              <td>
                                 <strong><?= _ent($u->nama_supplier); ?></strong><br>
                                 <small class="text-muted"><i class="fa fa-tag"></i> <?= _ent($u->kode_supplier); ?></small>
                              </td>
                              <td>
                                 <span class="label label-default"><i class="fa fa-cubes"></i> <?= _ent($u->nomor_penerimaan ?? '-'); ?></span><br>
                                 <small class="text-muted">PO: <?= _ent($u->nomor_po ?? '-'); ?></small>
                              </td>
                              <td>
                                 <?php if(empty($u->tanggal_jatuh_tempo) || $u->tanggal_jatuh_tempo == '0000-00-00'): ?>
                                    <i class="fa fa-calendar text-muted"></i> <span class="text-muted">-</span>
                                 <?php else: ?>
                                    <i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime($u->tanggal_jatuh_tempo)); ?>
                                    <?php if(date('Y-m-d') > $u->tanggal_jatuh_tempo && $u->status != 'lunas'): ?>
                                    <br><span class="label label-danger" style="font-size:9px;">OVERDUE</span>
                                    <?php endif; ?>
                                 <?php endif; ?>
                              </td>
                              <td class="text-right"><strong>Rp <?= number_format($u->total, 0, ',', '.'); ?></strong></td>
                              <td class="text-right text-success">Rp <?= number_format($u->terbayar, 0, ',', '.'); ?></td>
                              <td class="text-right text-danger"><strong style="font-size:13px;">Rp <?= number_format($u->sisa, 0, ',', '.'); ?></strong></td>
                              <td class="text-center"><span class="badge bg-red" style="font-size:10px;">KREDIT</span></td>
                              <td class="text-center">
                                 <?php if($u->status == 'lunas'): ?>
                                    <span class="label label-success">LUNAS</span>
                                 <?php elseif($u->status == 'sebagian'): ?>
                                    <span class="label label-warning">SEBAGIAN</span>
                                 <?php else: ?>
                                    <span class="label label-danger">BELUM BAYAR</span>
                                 <?php endif; ?>
                              </td>
                              <td class="text-center">
                                 <?php if($u->status != 'lunas'): ?>
                                 <button type="button" class="btn btn-success btn-xs btn-bayar-utang" style="border-radius:4px; font-weight:600;" 
                                         data-id="<?= $u->id; ?>"
                                         data-nomor="<?= _ent($u->nomor_utang); ?>"
                                         data-supplier="<?= _ent($u->nama_supplier); ?>"
                                         data-sisa="<?= $u->sisa; ?>">
                                    <i class="fa fa-money"></i> Bayar
                                 </button>
                                 <?php endif; ?>
                              </td>
                           </tr>
                           <?php endforeach; ?>
                        <?php else: ?>
                           <tr><td colspan="11" class="text-center text-muted" style="padding:30px;"><i class="fa fa-inbox fa-2x"></i><br>Data utang usaha tidak ditemukan.</td></tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>

               <div class="row" style="margin-top:15px;">
                  <div class="col-md-6"><span class="text-muted">Total: <strong><?= $utang_counts; ?></strong> tagihan</span></div>
                  <div class="col-md-6"><div class="pull-right"><?= $pagination; ?></div></div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Modal Bayar Utang -->
<div class="modal fade" id="modal_bayar_utang" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content" style="border-radius:8px;">
         <div class="modal-header bg-green" style="border-top-left-radius:8px; border-top-right-radius:8px;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="fa fa-money"></i> Form Pembayaran Utang Usaha (BKK)</h4>
         </div>
         <form id="form_bayar_utang">
            <input type="hidden" name="utang_id" id="bayar_utang_id">
            <div class="modal-body">
               <div class="callout callout-info" style="margin-bottom: 15px; border-radius:6px;">
                  <p><strong>Tagihan:</strong> <span id="bayar_nomor_utang">-</span></p>
                  <p><strong>Supplier:</strong> <span id="bayar_supplier_name">-</span></p>
                  <p><strong>Sisa Tagihan:</strong> <strong class="text-danger" id="bayar_sisa_display">Rp 0</strong></p>
               </div>
               <div class="form-group">
                  <label>Nominal Pembayaran (Rp) <span class="text-danger">*</span></label>
                  <input type="text" class="form-control rupiah" name="nominal" id="bayar_nominal" required style="font-weight:bold; font-size:16px;">
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Tgl Pembayaran <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_bayar" value="<?= date('Y-m-d'); ?>" required>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Akun Kas / Bank <span class="text-danger">*</span></label>
                        <select class="form-control" name="akun_kas_bank" id="akun_kas_bank" required>
                           <option value="Kas Tunai / Cash">💵 Kas Tunai / Cash (Kasir/Operasional)</option>
                           <option value="Kas Utama Gudang">🏢 Kas Utama Gudang</option>
                           <option value="Bank BCA" selected>🏦 Bank BCA - Operasional</option>
                           <option value="Bank Mandiri">🏦 Bank Mandiri - Operasional</option>
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
                           <option value="cek_giro">📄 Cek / Giro</option>
                           <option value="lainnya">📦 Lainnya</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>No Referensi / Struk</label>
                        <input type="text" class="form-control" name="nomor_referensi" placeholder="Ref Transfer / No Struk / Giro">
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <label>Catatan Pembayaran</label>
                  <textarea class="form-control" name="catatan" rows="2" placeholder="Catatan pembayaran..."></textarea>
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" id="btn_submit_bayar" class="btn btn-success" style="border-radius:4px;"><i class="fa fa-check"></i> Proses Bayar &amp; Buat BKK</button>
               <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius:4px;">Batal</button>
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
         $('#akun_kas_bank').val('Kas Tunai / Cash');
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

   $('.btn-bayar-utang').click(function(){
      var id = $(this).data('id');
      var nomor = $(this).data('nomor');
      var supplier = $(this).data('supplier');
      var sisa = parseFloat($(this).data('sisa')) || 0;
      var clean_sisa = Math.round(sisa);

      $('#bayar_utang_id').val(id);
      $('#bayar_nomor_utang').text(nomor);
      $('#bayar_supplier_name').text(supplier);
      $('#bayar_sisa_display').text('Rp ' + clean_sisa.toLocaleString('id-ID'));
      $('#bayar_nominal').val(clean_sisa.toLocaleString('id-ID'));

      $('#modal_bayar_utang').modal('show');
   });

   $('#form_bayar_utang').submit(function(e){
      e.preventDefault();
      var btn = $('#btn_submit_bayar');
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');
      $.ajax({
         url: '<?= site_url("administrator/utang_usaha/bayar_save"); ?>',
         method: 'POST',
         data: $(this).serialize(),
         dataType: 'json',
         success: function(res) {
            if (res.success) {
               if (res.redirect) window.location.href = res.redirect;
            } else {
               swal("Gagal!", res.message, "error");
               btn.prop('disabled', false).html('<i class="fa fa-check"></i> Proses Bayar & Buat BKK');
            }
         },
         error: function() {
            btn.prop('disabled', false).html('<i class="fa fa-check"></i> Proses Bayar & Buat BKK');
         }
      });
   });
});
</script>
