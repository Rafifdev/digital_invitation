<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <title>NOTA RETUR PENJUALAN - <?= _ent($retur->nomor_retur ?? ''); ?></title>
   <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700">
   <style>
      body { font-family: 'Source Sans Pro', sans-serif; font-size: 13px; color: #333; margin: 20px; }
      .header-bar { display: flex; justify-content: space-between; border-bottom: 2px solid #222; padding-bottom: 15px; margin-bottom: 20px; }
      .company-title { font-size: 22px; font-weight: bold; color: #111; }
      .doc-title { font-size: 24px; font-weight: bold; color: #0056b3; text-align: right; }
      .info-table { width: 100%; margin-bottom: 20px; }
      .info-table td { padding: 4px 8px; vertical-align: top; }
      .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
      .data-table th, .data-table td { border: 1px solid #ccc; padding: 8px 10px; text-align: left; }
      .data-table th { background: #f4f6f9; font-weight: 600; text-transform: uppercase; font-size: 12px; }
      .text-right { text-align: right; }
      .text-center { text-align: center; }
      .total-box { font-size: 15px; font-weight: bold; background: #eef7ff; }
      .signatures { margin-top: 50px; display: flex; justify-content: space-between; text-align: center; }
      .sign-col { width: 28%; }
      .sign-space { height: 65px; }
      .sign-line { border-top: 1px solid #333; font-weight: 600; padding-top: 5px; }
      @media print {
         .no-print { display: none !important; }
         body { margin: 0; }
      }
   </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 20px; text-align: right;">
   <button onclick="window.print()" style="padding: 8px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
      <i class="fa fa-print"></i> Cetak / Simpan PDF
   </button>
   <button onclick="window.close()" style="padding: 8px 20px; background: #6c757d; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-left: 8px;">
      Tutup
   </button>
</div>

<div class="header-bar">
   <div>
      <div class="company-title">IDMETAFORA / CI3 METAFORA</div>
      <div>Sistem Penjualan, Pembelian & Inventory</div>
      <div>Telp: (0274) 123456 | Email: info@idmetafora.com</div>
   </div>
   <div>
      <div class="doc-title">NOTA RETUR PENJUALAN</div>
      <div class="text-right"><strong>No Retur:</strong> <?= _ent($retur->nomor_retur ?? ''); ?></div>
      <div class="text-right"><strong>Tanggal:</strong> <?= isset($retur->tanggal_retur) ? date('d/m/Y', strtotime($retur->tanggal_retur)) : '-'; ?></div>
   </div>
</div>

<table class="info-table">
   <tr>
      <td width="50%">
         <table style="width: 100%;">
            <tr><td width="35%"><strong>Pelanggan:</strong></td><td><?= _ent($retur->nama_pelanggan ?? '-'); ?></td></tr>
            <tr><td width="35%"><strong>Ref. Invoice:</strong></td><td><?= _ent($retur->nomor_invoice ?? '-'); ?></td></tr>
            <tr><td width="35%"><strong>Alasan Retur:</strong></td><td><?= _ent($retur->alasan ?? '-'); ?></td></tr>
         </table>
      </td>
      <td width="50%">
         <table style="width: 100%;">
            <tr><td width="40%"><strong>Gudang Tujuan:</strong></td><td><?= _ent($retur->nama_gudang ?? '-'); ?></td></tr>
            <tr><td width="40%"><strong>Tindak Lanjut:</strong></td><td><?= _ent($retur->tindak_lanjut ?? '-'); ?></td></tr>
            <tr><td width="40%"><strong>Petugas:</strong></td><td><?= _ent($retur->created_by ?? '-'); ?></td></tr>
         </table>
      </td>
   </tr>
</table>

<table class="data-table">
   <thead>
      <tr>
         <th width="40" class="text-center">No</th>
         <th >Produk</th>
         <th class="text-right">Qty Retur</th>
         <th class="text-right">Harga Satuan</th>
         <th >Kondisi</th>
         <th class="text-right">Subtotal</th>
      </tr>
   </thead>
   <tbody>
      <?php if (!empty($items)): ?>
         <?php $no = 1; foreach ($items as $it): ?>
            <tr>
               <td class="text-center"><?= $no++; ?></td>
               <td><?= _ent($it->nama_produk ?? '-'); ?></td>
               <td class="text-right"><?= number_format($it->qty_retur ?? 0, 0, ',', '.'); ?></td>
               <td class="text-right">Rp <?= number_format($it->harga_satuan ?? 0, 0, ',', '.'); ?></td>
               <td><?= _ent($it->kondisi ?? '-'); ?></td>
               <td class="text-right">Rp <?= number_format($it->subtotal ?? 0, 0, ',', '.'); ?></td>
            </tr>
         <?php endforeach; ?>
      <?php else: ?>
         <tr><td colspan="10" class="text-center">Tidak ada item.</td></tr>
      <?php endif; ?>
   </tbody>
   <tfoot>
      <tr class="total-box">
         <td colspan="5" class="text-right">TOTAL:</td>
         <td class="text-right">Rp <?= number_format($retur->total ?? 0, 0, ',', '.'); ?></td>
      </tr>
   </tfoot>
</table>

<div class="signatures">
   <div class="sign-col">
      <div>Penerima Retur,</div>
      <div class="sign-space"></div>
      <div class="sign-line">( ____________________ )</div>
   </div>
   <div class="sign-col">
      <div>Pelanggan,</div>
      <div class="sign-space"></div>
      <div class="sign-line">( ____________________ )</div>
   </div>
   <div class="sign-col">
      <div>Manager Customer Service,</div>
      <div class="sign-space"></div>
      <div class="sign-line">( ____________________ )</div>
   </div>
</div>


<script>
window.onload = function() {
   window.print();
};
</script>
</body>
</html>