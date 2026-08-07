<?php /* utang_list.php - Ledger Utang Usaha (AP) */ ?>
<style>
.kpi-card { background:#fff; border-radius:8px; padding:15px; margin-bottom:15px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.05); position:relative; }
.kpi-title { font-size:11px; text-transform:uppercase; color:#64748b; font-weight:600; }
.kpi-value { font-size:20px; font-weight:700; color:#1e293b; margin:4px 0; }
.kpi-sub { font-size:11px; color:#94a3b8; }
.kpi-icon { position:absolute; right:15px; top:18px; font-size:28px; opacity:0.15; }
</style>

<section class="content-header">
   <h1>Utang Usaha (Accounts Payable) <small>Monitoring &amp; Pembayaran Tagihan Pembelian Supplier</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/keuangan'); ?>">Keuangan &amp; Ledger</a></li>
      <li class="active">Utang Usaha (AP)</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 4px solid #0284c7;">
            <div class="kpi-title">TOTAL KEWAJIBAN UTANG</div>
            <div class="kpi-value">Rp <?= number_format($stats['total_kewajiban'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-calculator"></i> <?= number_format($stats['total_faktur'] ?? 0); ?> Total Faktur Tagihan</div>
            <i class="fa fa-calculator kpi-icon text-info"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 4px solid #16a34a;">
            <div class="kpi-title">TOTAL TELAH DIBAYAR</div>
            <div class="kpi-value">Rp <?= number_format($stats['total_dibayar'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-check-circle"></i> Kas Keluar Terealisasi</div>
            <i class="fa fa-money kpi-icon text-success"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 4px solid #f43f5e;">
            <div class="kpi-title">SISA TAGIHAN BELUM LUNAS</div>
            <div class="kpi-value">Rp <?= number_format($stats['sisa_utang'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub"><i class="fa fa-hourglass-half"></i> Saldo Utang Aktif Perusahaan</div>
            <i class="fa fa-hourglass-half kpi-icon text-warning"></i>
         </div>
      </div>
      <div class="col-md-3 col-sm-6">
         <div class="kpi-card" style="border-left: 4px solid #dc2626;">
            <div class="kpi-title">LEWAT JATUH TEMPO (OVERDUE)</div>
            <div class="kpi-value">Rp <?= number_format($stats['overdue_nominal'] ?? 0, 0, ',', '.'); ?></div>
            <div class="kpi-sub" style="color:#dc2626;"><i class="fa fa-bell"></i> <?= number_format($stats['overdue_count'] ?? 0); ?> Faktur Mendesak</div>
            <i class="fa fa-bell kpi-icon text-danger"></i>
         </div>
      </div>
   </div>

   <div class="box box-primary" style="border-radius:6px;">
      <div class="box-header with-border">
         <h3 class="box-title"><i class="fa fa-list text-primary"></i> Buku Besar Utang Usaha &amp; Status Pelunasan</h3>
         <div class="box-tools pull-right">
            <a href="<?= site_url('administrator/keuangan/kas_keluar'); ?>" class="btn btn-info btn-sm btn-flat" style="border-radius:4px;"><i class="fa fa-history"></i> Riwayat Kas Keluar (BKK)</a>
         </div>
      </div>
      <div class="box-body">
         <form method="GET" action="<?= base_url('administrator/keuangan/utang'); ?>">
            <div class="row" style="margin-bottom:15px;">
               <div class="col-md-3">
                  <select name="supplier_id" class="form-control chosen">
                     <option value="">-- Semua Supplier --</option>
                     <?php foreach($suppliers as $s): ?>
                        <option value="<?= $s->id; ?>" <?= $this->input->get('supplier_id')==$s->id?'selected':''; ?>><?= _ent($s->nama_supplier); ?></option>
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
                  <input type="text" class="form-control" name="q" value="<?= _ent($this->input->get('q')); ?>" placeholder="Cari No. Utang / Supplier...">
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
                     <th>No. Tagihan Utang</th>
                     <th>Supplier</th>
                     <th>Ref Penerimaan (GRN)</th>
                     <th>Tgl Faktur / Jatuh Tempo</th>
                     <th class="text-right">Total Tagihan</th>
                     <th class="text-right">Telah Dibayar</th>
                     <th class="text-right">Sisa Tagihan</th>
                     <th class="text-center">Posisi Akun</th>
                     <th class="text-center">Status</th>
                     <th width="100" class="text-center">Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php $no = $this->uri->segment(4) + 1; foreach($debts as $row): ?>
                  <tr>
                     <td class="text-center"><?= $no++; ?></td>
                     <td><strong class="text-primary"><?= _ent($row->nomor_utang); ?></strong></td>
                     <td>
                        <strong><?= _ent($row->nama_supplier ?? 'Umum'); ?></strong>
                        <br><small class="text-muted"><i class="fa fa-tag"></i> <?= _ent($row->kode_supplier ?? '-'); ?></small>
                     </td>
                     <td>
                        <?php if($row->nomor_penerimaan): ?><span class="label label-default">GRN: <?= _ent($row->nomor_penerimaan); ?></span><br><?php endif; ?>
                        <?php if($row->nomor_po): ?><small class="text-muted">PO: <?= _ent($row->nomor_po); ?></small><?php endif; ?>
                     </td>
                     <td>
                        <i class="fa fa-calendar text-muted"></i> <?= date('d/m/Y', strtotime($row->tanggal_utang ?: date('Y-m-d'))); ?>
                        <br><small class="text-danger"><strong>JT: <?= date('d/m/Y', strtotime($row->tanggal_jatuh_tempo)); ?></strong></small>
                     </td>
                     <td class="text-right">Rp <?= number_format($row->total, 0, ',', '.'); ?></td>
                     <td class="text-right text-success">Rp <?= number_format($row->terbayar, 0, ',', '.'); ?></td>
                     <td class="text-right text-danger"><strong>Rp <?= number_format($row->sisa, 0, ',', '.'); ?></strong></td>
                     <td class="text-center"><span class="badge bg-red" style="font-size:10px;">KREDIT</span></td>
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
                           <button type="button" class="btn btn-success btn-xs btn-flat btn-bayar" data-id="<?= $row->id; ?>" data-nomor="<?= _ent($row->nomor_utang); ?>" data-supplier="<?= _ent($row->nama_supplier); ?>" data-sisa="<?= $row->sisa; ?>" style="border-radius:3px;">
                              <i class="fa fa-money"></i> Bayar
                           </button>
                        <?php else: ?>
                           <span class="text-muted"><i class="fa fa-check-circle text-success"></i> Selesai</span>
                        <?php endif; ?>
                     </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if(empty($debts)): ?><tr><td colspan="11" class="text-center text-muted">Belum ada data utang usaha.</td></tr><?php endif; ?>
               </tbody>
            </table>
         </div>

         <div class="row">
            <div class="col-md-6"><p class="text-muted">Total Data: <strong><?= number_format($debt_counts); ?></strong> Record</p></div>
            <div class="col-md-6 text-right"><?= $pagination; ?></div>
         </div>
      </div>
   </div>
</section>

<!-- Modal Pembayaran Kas Keluar (BKK) -->
<div class="modal fade" id="modal_bayar" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content" style="border-radius:6px;">
         <div class="modal-header bg-primary" style="color:#fff; border-top-left-radius:6px; border-top-right-radius:6px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><i class="fa fa-money"></i> Form Pembayaran Utang Supplier (BKK)</h4>
         </div>
         <form id="form_bayar_utang">
            <input type="hidden" name="utang_id" id="bayar_utang_id">
            <div class="modal-body">
               <div class="form-group"><label>No. Tagihan Utang</label><input type="text" class="form-control" id="bayar_nomor_utang" readonly></div>
               <div class="form-group"><label>Supplier</label><input type="text" class="form-control" id="bayar_supplier" readonly></div>
               <div class="form-group"><label>Sisa Utang Saat Ini</label><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control" id="bayar_sisa_display" readonly style="font-weight:bold; color:#d9534f;"></div></div>
               <div class="row">
                  <div class="col-md-6"><div class="form-group"><label>Tanggal Bayar <span class="text-danger">*</span></label><input type="date" class="form-control" name="tanggal_bayar" value="<?= date('Y-m-d'); ?>" required></div></div>
                  <div class="col-md-6"><div class="form-group"><label>Akun Kas / Bank <span class="text-danger">*</span></label><select class="form-control" name="akun_kas_bank" required><option value="Bank Mandiri">Bank Mandiri - Operasional</option><option value="Bank BCA">Bank BCA</option><option value="Bank BRI">Bank BRI</option><option value="Kas Tunai Utama">Kas Tunai Utama</option></select></div></div>
               </div>
               <div class="row">
                  <div class="col-md-6"><div class="form-group"><label>Metode Pembayaran</label><select class="form-control" name="metode_pembayaran"><option value="bank_transfer">Bank Transfer / M-Banking</option><option value="tunai">Tunai / Cash</option><option value="giro">Giro / Cek</option></select></div></div>
                  <div class="col-md-6"><div class="form-group"><label>No. Ref Transfer / Giro</label><input type="text" class="form-control" name="nomor_referensi" placeholder="Contoh: Ref 9912001"></div></div>
               </div>
               <div class="form-group"><label>Nominal Pembayaran (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control input-lg" name="nominal" id="bayar_nominal" required style="font-weight:bold; color:#d9534f;"></div>
               <div class="form-group"><label>Catatan Pembayaran</label><textarea class="form-control" name="catatan" rows="2" placeholder="Keterangan tambahan..."></textarea></div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Batal</button>
               <button type="submit" id="btn_save_bayar" class="btn btn-primary btn-flat"><i class="fa fa-save"></i> Simpan Kas Keluar (BKK)</button>
            </div>
         </form>
      </div>
   </div>
</div>

<script>
$(document).ready(function(){
   $('.btn-bayar').click(function(){
      var id = $(this).data('id');
      var nomor = $(this).data('nomor');
      var supplier = $(this).data('supplier');
      var sisa = $(this).data('sisa');

      $('#bayar_utang_id').val(id);
      $('#bayar_nomor_utang').val(nomor);
      $('#bayar_supplier').val(supplier);
      $('#bayar_sisa_display').val(parseInt(sisa).toLocaleString('id-ID'));
      $('#bayar_nominal').val(sisa);
      $('#modal_bayar').modal('show');
   });

   $('#form_bayar_utang').submit(function(e){
      e.preventDefault();
      var btn = $('#btn_save_bayar');
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
      $.ajax({
         url: '<?= site_url("administrator/keuangan/bayar_utang_save"); ?>',
         method: 'POST', data: $(this).serialize(), dataType: 'json',
         success: function(res) { if (res.success) { window.location.href = res.redirect; } else { swal("Gagal", res.message, "error"); btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Kas Keluar (BKK)'); } },
         error: function() { btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Kas Keluar (BKK)'); }
      });
   });
});
</script>
