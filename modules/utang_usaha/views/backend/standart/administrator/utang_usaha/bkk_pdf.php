<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm">
   <table style="width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px;">
      <tr>
         <td style="width: 100%; text-align: center;">
            <strong style="font-size: 16px; color: #111; text-transform: uppercase;">VOUCHER BUKTI KAS KELUAR (BKK)</strong><br>
            <span style="font-size: 10px; color: #666;">Bukti Pelunasan / Pembayaran Utang Supplier</span>
         </td>
      </tr>
   </table>

   <table style="width: 100%; font-size: 11px;">
      <tr>
         <td style="width: 55%; vertical-align: top;">
            <strong>Dibayarkan Kepada (Supplier):</strong><br>
            <strong style="font-size: 13px; color: #0056b3;"><?= _ent($bkk->nama_supplier); ?></strong><br>
            Kode Supplier: <?= _ent($bkk->kode_supplier); ?><br>
            Alamat: <?= _ent($bkk->alamat ?? '-'); ?>
         </td>
         <td style="width: 45%; vertical-align: top;">
            <table style="width: 100%; font-size: 11px;">
               <tr><td style="width: 45%;"><strong>No. Voucher BKK:</strong></td><td><strong><?= _ent($bkk->nomor_bkk); ?></strong></td></tr>
               <tr><td><strong>Tanggal Bayar:</strong></td><td><?= date('d/m/Y', strtotime($bkk->tanggal_bayar)); ?></td></tr>
               <tr><td><strong>No. Tagihan Utang:</strong></td><td><?= _ent($bkk->nomor_utang); ?></td></tr>
               <tr><td><strong>Akun Kas / Bank:</strong></td><td><?= _ent($bkk->akun_kas_bank); ?></td></tr>
               <tr><td><strong>Metode Bayar:</strong></td><td><?= strtoupper(str_replace('_', ' ', $bkk->metode_pembayaran)); ?></td></tr>
            </table>
         </td>
      </tr>
   </table>

   <br>

   <table style="width: 100%; border: 1px solid #ccc; border-collapse: collapse; font-size: 10px; table-layout: fixed;">
      <thead>
         <tr style="background: #f2f2f2;">
            <th style="width: 6%; border: 1px solid #ccc; padding: 6px; text-align: center;">No</th>
            <th style="width: 54%; border: 1px solid #ccc; padding: 6px; text-align: left;">Keterangan / Deskripsi Transaksi</th>
            <th style="width: 20%; border: 1px solid #ccc; padding: 6px; text-align: left;">Ref Transfer / Giro</th>
            <th style="width: 20%; border: 1px solid #ccc; padding: 6px; text-align: right;">Jumlah Bayar</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td style="border: 1px solid #ccc; padding: 6px; text-align: center;">1</td>
            <td style="border: 1px solid #ccc; padding: 6px;">
               <strong>Pembayaran Tagihan Utang <?= _ent($bkk->nomor_utang); ?></strong><br>
               <span style="color: #666;">Catatan: <?= _ent($bkk->catatan ?: '-'); ?></span>
            </td>
            <td style="border: 1px solid #ccc; padding: 6px;"><?= _ent($bkk->nomor_referensi ?: '-'); ?></td>
            <td style="border: 1px solid #ccc; padding: 6px; text-align: right;"><strong>Rp <?= number_format($bkk->nominal, 0, ',', '.'); ?></strong></td>
         </tr>
      </tbody>
      <tfoot>
         <tr style="background: #eef2f7;">
            <td colspan="3" style="border: 1px solid #ccc; padding: 6px; text-align: right;"><strong>TOTAL DIBAYARKAN:</strong></td>
            <td style="border: 1px solid #ccc; padding: 6px; text-align: right;"><strong style="font-size: 12px; color: #0284c7;">Rp <?= number_format($bkk->nominal, 0, ',', '.'); ?></strong></td>
         </tr>
      </tfoot>
   </table>

   <br>

   <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 4px; font-size: 11px;">
      <strong>Terbilang:</strong> <em># <?= ucwords(trim(terbilang($bkk->nominal))); ?> Rupiah #</em>
   </div>

   <br><br><br>

   <table style="width: 100%; text-align: center; font-size: 11px;">
      <tr>
         <td style="width: 33%;">
            Dibuat Oleh,<br><br><br><br>
            ( <strong><?= _ent($bkk->created_by); ?></strong> )
         </td>
         <td style="width: 33%;">
            Disetujui Oleh,<br><br><br><br>
            ( <strong>Manajer Keuangan</strong> )
         </td>
         <td style="width: 34%;">
            Penerima / Supplier,<br><br><br><br>
            ( <strong><?= _ent($bkk->nama_supplier); ?></strong> )
         </td>
      </tr>
   </table>
</page>
