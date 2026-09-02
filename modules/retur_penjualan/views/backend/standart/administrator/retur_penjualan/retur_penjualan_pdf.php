<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm">
   <table style="width: 100%; border-bottom: 2px solid #222; padding-bottom: 10px;">
      <tr>
         <td style="width: 55%;">
            <strong style="font-size: 16px; color: #111;">IDMETAFORA / CI3 METAFORA</strong><br>
            <span style="font-size: 11px; color: #555;">Sistem Penjualan, Pembelian &amp; Inventory</span><br>
            <span style="font-size: 10px; color: #666;">Telp: (0274) 123456 | Email: info@idmetafora.com</span>
         </td>
         <td style="width: 45%; text-align: right;">
            <strong style="font-size: 16px; color: #0056b3;">NOTA RETUR PENJUALAN</strong><br>
            <span style="font-size: 11px;"><strong>No Retur:</strong> <?= _ent($retur->nomor_retur ?? ''); ?></span><br>
            <span style="font-size: 11px;"><strong>Tanggal:</strong> <?= isset($retur->tanggal_retur) ? date('d/m/Y', strtotime($retur->tanggal_retur)) : '-'; ?></span>
         </td>
      </tr>
   </table>

   <br>

   <table style="width: 100%; font-size: 11px;">
      <tr>
         <td style="width: 50%; vertical-align: top;">
            <table style="width: 100%;">
               <tr><td style="width: 35%;"><strong>Pelanggan:</strong></td><td><?= _ent($retur->nama_pelanggan ?? '-'); ?></td></tr>
               <tr><td style="width: 35%;"><strong>Ref. Invoice:</strong></td><td><?= _ent($retur->nomor_invoice ?? '-'); ?></td></tr>
               <tr><td style="width: 35%;"><strong>Alasan Retur:</strong></td><td><?= _ent($retur->alasan ?? '-'); ?></td></tr>
            </table>
         </td>
         <td style="width: 50%; vertical-align: top;">
            <table style="width: 100%;">
               <tr><td style="width: 40%;"><strong>Gudang Tujuan:</strong></td><td><?= _ent($retur->nama_gudang ?? '-'); ?></td></tr>
               <tr><td style="width: 40%;"><strong>Tindak Lanjut:</strong></td><td><?= _ent($retur->tindak_lanjut ?? '-'); ?></td></tr>
               <tr><td style="width: 40%;"><strong>Petugas:</strong></td><td><?= _ent($retur->created_by ?? '-'); ?></td></tr>
            </table>
         </td>
      </tr>
   </table>

   <br>

   <table style="width: 100%; border: 1px solid #ccc; border-collapse: collapse; font-size: 10px; table-layout: fixed;">
      <thead>
         <tr style="background: #f0f0f0;">
            <th style="width: 5%; border: 1px solid #ccc; padding: 5px; text-align: center;">No</th>
            <th style="width: 35%; border: 1px solid #ccc; padding: 5px; text-align: left;">Produk</th>
            <th style="width: 12%; border: 1px solid #ccc; padding: 5px; text-align: right;">Qty Retur</th>
            <th style="width: 18%; border: 1px solid #ccc; padding: 5px; text-align: right;">Harga Satuan</th>
            <th style="width: 12%; border: 1px solid #ccc; padding: 5px; text-align: center;">Kondisi</th>
            <th style="width: 18%; border: 1px solid #ccc; padding: 5px; text-align: right;">Subtotal</th>
         </tr>
      </thead>
      <tbody>
         <?php if (!empty($items)): ?>
            <?php $no = 1; foreach ($items as $it): ?>
               <tr>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: center;"><?= $no++; ?></td>
                  <td style="border: 1px solid #ccc; padding: 5px;"><?= _ent($it->nama_produk ?? '-'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: right;"><?= number_format($it->qty_retur ?? 0, 0, ',', '.'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: right;">Rp <?= number_format($it->harga_satuan ?? 0, 0, ',', '.'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: center;"><?= _ent($it->kondisi ?? 'Baik'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: right;">Rp <?= number_format($it->subtotal ?? 0, 0, ',', '.'); ?></td>
               </tr>
            <?php endforeach; ?>
         <?php else: ?>
            <tr><td colspan="6" style="border: 1px solid #ccc; padding: 5px; text-align: center;">Tidak ada item.</td></tr>
         <?php endif; ?>
      </tbody>
      <tfoot>
         <tr style="background: #eef7ff;">
            <td colspan="5" style="border: 1px solid #ccc; padding: 5px; text-align: right;"><strong>TOTAL RETUR PENJUALAN:</strong></td>
            <td style="border: 1px solid #ccc; padding: 5px; text-align: right;"><strong style="color:#0056b3;">Rp <?= number_format($retur->total ?? 0, 0, ',', '.'); ?></strong></td>
         </tr>
      </tfoot>
   </table>

   <br><br><br>

   <table style="width: 100%; text-align: center; font-size: 11px;">
      <tr>
         <td style="width: 33%;">
            Penerima Retur,<br><br><br><br>
            ( ____________________ )
         </td>
         <td style="width: 33%;">
            Pelanggan,<br><br><br><br>
            ( ____________________ )
         </td>
         <td style="width: 34%;">
            Manager CS,<br><br><br><br>
            ( ____________________ )
         </td>
      </tr>
   </table>
</page>