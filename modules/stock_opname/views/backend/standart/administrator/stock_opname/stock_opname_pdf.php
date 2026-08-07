<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm">
   <table style="width: 100%; border-bottom: 2px solid #222; padding-bottom: 10px;">
      <tr>
         <td style="width: 60%;">
            <strong style="font-size: 16px; color: #111;">IDMETAFORA / CI3 METAFORA</strong><br>
            <span style="font-size: 11px; color: #555;">Sistem Penjualan, Pembelian &amp; Inventory</span><br>
            <span style="font-size: 10px; color: #666;">Telp: (0274) 123456 | Email: info@idmetafora.com</span>
         </td>
         <td style="width: 40%; text-align: right;">
            <strong style="font-size: 16px; color: #0056b3;">BERITA ACARA STOCK OPNAME</strong><br>
            <span style="font-size: 11px;"><strong>No Opname:</strong> <?= _ent($opname->nomor_opname ?? ''); ?></span><br>
            <span style="font-size: 11px;"><strong>Tanggal:</strong> <?= isset($opname->tanggal_opname) ? date('d/m/Y', strtotime($opname->tanggal_opname)) : '-'; ?></span>
         </td>
      </tr>
   </table>

   <br>

   <table style="width: 100%; font-size: 11px;">
      <tr>
         <td style="width: 50%; vertical-align: top;">
            <table style="width: 100%;">
               <tr><td style="width: 35%;"><strong>Gudang:</strong></td><td><?= _ent($opname->nama_gudang ?? '-'); ?></td></tr>
               <tr><td style="width: 35%;"><strong>Petugas Audit:</strong></td><td><?= _ent(!empty($opname->petugas) ? $opname->petugas : (!empty($opname->created_by) ? $opname->created_by : '-')); ?></td></tr>
            </table>
         </td>
         <td style="width: 50%; vertical-align: top;">
            <table style="width: 100%;">
               <tr><td style="width: 40%;"><strong>Status:</strong></td><td><?= _ent(strtoupper($opname->status ?? '-')); ?></td></tr>
               <tr><td style="width: 40%;"><strong>Keterangan:</strong></td><td><?= _ent(!empty($opname->keterangan) ? $opname->keterangan : (!empty($opname->catatan) ? $opname->catatan : '-')); ?></td></tr>
            </table>
         </td>
      </tr>
   </table>

   <br>

   <table style="width: 100%; border: 1px solid #ccc; border-collapse: collapse; font-size: 11px; font-family: helvetica;">
      <thead>
         <tr style="background: #e0f2fe; color: #0369a1;">
            <th style="width: 5%; border: 1px solid #ccc; padding: 8px; text-align: center;">No</th>
            <th style="width: 25%; border: 1px solid #ccc; padding: 8px; text-align: left;">Produk</th>
            <th style="width: 15%; border: 1px solid #ccc; padding: 8px; text-align: center;">Batch No</th>
            <th style="width: 12%; border: 1px solid #ccc; padding: 8px; text-align: right;">Stok Sistem</th>
            <th style="width: 12%; border: 1px solid #ccc; padding: 8px; text-align: right;">Stok Fisik</th>
            <th style="width: 12%; border: 1px solid #ccc; padding: 8px; text-align: right;">Selisih</th>
            <th style="width: 19%; border: 1px solid #ccc; padding: 8px; text-align: left;">Catatan / Alasan</th>
         </tr>
      </thead>
      <tbody>
         <?php if (!empty($items)): ?>
            <?php $no = 1; foreach ($items as $it): ?>
               <tr>
                  <td style="border: 1px solid #ccc; padding: 8px; text-align: center;"><?= $no++; ?></td>
                  <td style="border: 1px solid #ccc; padding: 8px;"><?= _ent($it->nama_produk ?? '-'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 8px; text-align: center;"><?= _ent($it->batch_no ?? '-'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 8px; text-align: right;"><?= number_format((float)($it->stok_sistem ?? 0), 0, ',', '.'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 8px; text-align: right;"><?= number_format((float)($it->stok_fisik ?? 0), 0, ',', '.'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 8px; text-align: right; font-weight:bold; color: <?= ($it->selisih < 0) ? '#dc2626' : (($it->selisih > 0) ? '#16a34a' : '#333'); ?>;"><?= ($it->selisih > 0 ? '+' : '') . number_format((float)($it->selisih ?? 0), 0, ',', '.'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 8px; font-size: 10px;"><?= _ent(!empty($it->alasan_selisih) ? $it->alasan_selisih : (!empty($it->catatan) ? $it->catatan : '-')); ?></td>
               </tr>
            <?php endforeach; ?>
         <?php else: ?>
            <tr><td colspan="7" style="border: 1px solid #ccc; padding: 10px; text-align: center; color: #777;">Tidak ada item yang di-opname.</td></tr>
         <?php endif; ?>
      </tbody>
   </table>

   <br><br><br>

   <table style="width: 100%; text-align: center; font-size: 11px;">
      <tr>
         <td style="width: 33%;">
            Petugas Audit,<br><br><br><br>
            ( ____________________ )
         </td>
         <td style="width: 33%;">
            Kepala Gudang,<br><br><br><br>
            ( ____________________ )
         </td>
         <td style="width: 34%;">
            Accounting / Finance,<br><br><br><br>
            ( ____________________ )
         </td>
      </tr>
   </table>
</page>