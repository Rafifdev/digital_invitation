<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <title>Invoice <?= _ent($invoice->nomor_invoice); ?></title>
   <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
   <style>
      body{font-family:'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:13px;margin:20px;}
      .header{display:flex;justify-content:space-between;border-bottom:2px solid #333;padding-bottom:15px;margin-bottom:20px;}
      .company-name{font-size:22px;font-weight:bold;}
      .invoice-title{font-size:28px;color:#555;}
      table{width:100%;border-collapse:collapse;margin-bottom:15px;}
      th,td{padding:8px 10px;text-align:left;border:1px solid #ddd;}
      th{background:#f5f5f5;}
      .text-right{text-align:right;}
      .total-row td{font-weight:bold;font-size:16px;background:#e8f5e9;}
      .footer{margin-top:40px;display:flex;justify-content:space-around;}
      .sign-box{text-align:center;width:180px;}
      .sign-line{border-top:1px solid #333;margin-top:60px;padding-top:5px;}
      @media print{.no-print{display:none;}}
   </style>
</head>
<body>
   <div class="no-print" style="margin-bottom:15px;">
      <button onclick="window.print()" style="padding:8px 20px;background:#337ab7;color:#fff;border:none;cursor:pointer;border-radius:4px;">Cetak Invoice</button>
      <button onclick="window.close()" style="padding:8px 20px;margin-left:10px;border:1px solid #ccc;cursor:pointer;border-radius:4px;">Tutup</button>
   </div>
   <div class="header">
      <div>
         <div class="company-name">PERUSAHAAN</div>
         <div>Alamat Perusahaan</div>
         <div>Telp: xxx-xxxx | Email: info@perusahaan.com</div>
      </div>
      <div style="text-align:right;">
         <div class="invoice-title">INVOICE</div>
         <div><strong>No: <?= _ent($invoice->nomor_invoice); ?></strong></div>
         <div>Tanggal: <?= date('d/m/Y',strtotime($invoice->tanggal_invoice)); ?></div>
         <div>Jatuh Tempo: <?= $invoice->tanggal_jatuh_tempo ? date('d/m/Y',strtotime($invoice->tanggal_jatuh_tempo)) : '-'; ?></div>
      </div>
   </div>
   <div style="margin-bottom:20px;">
      <strong>Kepada Yth:</strong><br>
      <strong><?= _ent($invoice->nama_pelanggan); ?></strong>
   </div>
   <table>
      <thead><tr><th>#</th><th>Nama Produk</th><th class="text-right">Qty</th><th class="text-right">Harga</th><th class="text-right">Disc%</th><th class="text-right">Subtotal</th></tr></thead>
      <tbody>
         <?php foreach($items as $i=>$item): ?>
         <tr>
            <td><?= $i+1; ?></td>
            <td><?= _ent($item->nama_produk); ?><br><small><?= _ent($item->kode_produk); ?></small></td>
            <td class="text-right"><?= number_format($item->qty, 0, ',', '.'); ?> <?= _ent($item->singkatan??$item->satuan); ?></td>
            <td class="text-right">Rp <?= number_format($item->harga_satuan,0,',','.'); ?></td>
            <td class="text-right"><?= $item->diskon_persen; ?>%</td>
            <td class="text-right">Rp <?= number_format($item->subtotal,0,',','.'); ?></td>
         </tr>
         <?php endforeach; ?>
      </tbody>
      <tfoot>
         <tr><td colspan="5" class="text-right">Subtotal:</td><td class="text-right">Rp <?= number_format($invoice->subtotal,0,',','.'); ?></td></tr>
         <tr><td colspan="5" class="text-right">Diskon:</td><td class="text-right">- Rp <?= number_format($invoice->diskon_total,0,',','.'); ?></td></tr>
         <tr><td colspan="5" class="text-right">Pajak (<?= $invoice->pajak_persen; ?>%):</td><td class="text-right">Rp <?= number_format($invoice->pajak_nominal,0,',','.'); ?></td></tr>
         <tr class="total-row"><td colspan="5" class="text-right">TOTAL:</td><td class="text-right">Rp <?= number_format($invoice->total,0,',','.'); ?></td></tr>
      </tfoot>
   </table>
   <?php if($invoice->catatan): ?><p><em>Catatan: <?= nl2br(_ent($invoice->catatan)); ?></em></p><?php endif; ?>
   <div class="footer">
      <div class="sign-box"><div class="sign-line">Pelanggan</div><div><?= _ent($invoice->nama_pelanggan); ?></div></div>
      <div class="sign-box"><div class="sign-line">Dibuat Oleh</div><div><?= _ent($invoice->created_by); ?></div></div>
   </div>

<script>
window.onload = function() {
   window.print();
};
</script>
</body>
</html>
