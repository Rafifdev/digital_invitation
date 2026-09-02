<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm">
   <table style="width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px;">
      <tr>
         <td style="width: 100%; text-align: center;">
            <strong style="font-size: 16px; color: #111; text-transform: uppercase;">VOUCHER BUKTI KAS MASUK (BKM)</strong><br>
            <span style="font-size: 10px; color: #666;">Bukti Penerimaan Uang Kas / Bank Pelunasan Piutang Pelanggan</span>
         </td>
      </tr>
   </table>

   <table style="width: 100%; font-size: 11px;">
      <tr>
         <td style="width: 55%; vertical-align: top;">
            <strong>Diterima Dari (Pelanggan):</strong><br>
            <strong style="font-size: 13px; color: #10b981;"><?= _ent($bkm->nama_pelanggan); ?></strong><br>
            Kode Pelanggan: <?= _ent($bkm->kode_pelanggan ?? '-'); ?><br>
            Alamat: <?= _ent($bkm->alamat ?? '-'); ?>
         </td>
         <td style="width: 45%; vertical-align: top;">
            <table style="width: 100%; font-size: 11px;">
               <tr><td style="width: 45%;"><strong>No. Voucher BKM:</strong></td><td><strong><?= _ent($bkm->nomor_bkm); ?></strong></td></tr>
               <tr><td><strong>Tanggal Terima:</strong></td><td><?= date('d/m/Y', strtotime($bkm->tanggal_bayar)); ?></td></tr>
               <tr><td><strong>No. Tagihan Piutang:</strong></td><td><?= _ent($bkm->nomor_piutang ?? '-'); ?></td></tr>
               <tr><td><strong>Akun Kas / Bank:</strong></td><td><?= _ent($bkm->akun_kas_bank); ?></td></tr>
               <tr><td><strong>Metode Terima:</strong></td><td><?= strtoupper(str_replace('_', ' ', $bkm->metode_pembayaran)); ?></td></tr>
            </table>
         </td>
      </tr>
   </table>

   <br>

   <table style="width: 100%; border: 1px solid #ccc; border-collapse: collapse; font-size: 10px; table-layout: fixed;">
      <thead>
         <tr style="background: #f2f2f2;">
            <th style="width: 6%; border: 1px solid #ccc; padding: 6px; text-align: center;">No</th>
            <th style="width: 54%; border: 1px solid #ccc; padding: 6px; text-align: left;">Keterangan / Deskripsi Penerimaan</th>
            <th style="width: 20%; border: 1px solid #ccc; padding: 6px; text-align: left;">Ref Transfer / Bank</th>
            <th style="width: 20%; border: 1px solid #ccc; padding: 6px; text-align: right;">Jumlah Diterima</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td style="border: 1px solid #ccc; padding: 6px; text-align: center;">1</td>
            <td style="border: 1px solid #ccc; padding: 6px;">
               <strong>Penerimaan Pelunasan Piutang <?= _ent($bkm->nomor_piutang); ?></strong><br>
               <span style="color: #666;">Catatan: <?= _ent($bkm->catatan ?: '-'); ?></span>
            </td>
            <td style="border: 1px solid #ccc; padding: 6px;"><?= _ent($bkm->nomor_referensi ?: '-'); ?></td>
            <td style="border: 1px solid #ccc; padding: 6px; text-align: right;"><strong>Rp <?= number_format($bkm->nominal, 0, ',', '.'); ?></strong></td>
         </tr>
      </tbody>
      <tfoot>
         <tr style="background: #eef7ff;">
            <td colspan="3" style="border: 1px solid #ccc; padding: 6px; text-align: right;"><strong>TOTAL DITERIMA:</strong></td>
            <td style="border: 1px solid #ccc; padding: 6px; text-align: right;"><strong style="font-size: 12px; color: #10b981;">Rp <?= number_format($bkm->nominal, 0, ',', '.'); ?></strong></td>
         </tr>
      </tfoot>
   </table>

   <br>

   <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 4px; font-size: 11px;">
      <strong>Terbilang:</strong> <em># <?= ucwords(trim(terbilang($bkm->nominal))); ?> Rupiah #</em>
   </div>

   <br><br><br>

   <table style="width: 100%; text-align: center; font-size: 11px;">
      <tr>
         <td style="width: 33%;">
            Diterima Oleh (Kasir),<br><br><br><br>
            ( <strong><?= _ent($bkm->created_by); ?></strong> )
         </td>
         <td style="width: 33%;">
            Disetujui Oleh,<br><br><br><br>
            ( <strong>Manajer Keuangan</strong> )
         </td>
         <td style="width: 34%;">
            Pembayar / Pelanggan,<br><br><br><br>
            ( <strong><?= _ent($bkm->nama_pelanggan); ?></strong> )
         </td>
      </tr>
   </table>
</page>
