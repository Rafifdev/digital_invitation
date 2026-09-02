<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm">
   <table style="width: 100%; border-bottom: 2px solid #222; padding-bottom: 10px;">
      <tr>
         <td style="width: 55%;">
            <strong style="font-size: 16px; color: #111;">IDMETAFORA / CI3 METAFORA</strong><br>
            <span style="font-size: 11px; color: #555;">Sistem Penjualan, Pembelian &amp; Inventory</span><br>
            <span style="font-size: 10px; color: #666;">Telp: (0274) 123456 | Email: info@idmetafora.com</span>
         </td>
         <td style="width: 45%; text-align: right;">
            <strong style="font-size: 16px; color: #0056b3;">SURAT JALAN MUTASI / TRANSFER STOK</strong><br>
            <span style="font-size: 11px;"><strong>No Transfer:</strong> <?= _ent($transfer->nomor_transfer ?? ''); ?></span><br>
            <span style="font-size: 11px;"><strong>Tanggal:</strong> <?= isset($transfer->tanggal_transfer) ? date('d/m/Y', strtotime($transfer->tanggal_transfer)) : '-'; ?></span>
         </td>
      </tr>
   </table>

   <br>

   <table style="width: 100%; font-size: 11px;">
      <tr>
         <td style="width: 50%; vertical-align: top;">
            <table style="width: 100%;">
               <tr><td style="width: 35%;"><strong>Gudang Asal:</strong></td><td><?= _ent($transfer->nama_gudang_asal ?? '-'); ?></td></tr>
               <tr><td style="width: 35%;"><strong>Petugas Pengirim:</strong></td><td><?= _ent($transfer->created_by ?? '-'); ?></td></tr>
            </table>
         </td>
         <td style="width: 50%; vertical-align: top;">
            <table style="width: 100%;">
               <tr><td style="width: 40%;"><strong>Gudang Tujuan:</strong></td><td><?= _ent($transfer->nama_gudang_tujuan ?? '-'); ?></td></tr>
               <tr><td style="width: 40%;"><strong>Catatan:</strong></td><td><?= _ent($transfer->catatan ?? '-'); ?></td></tr>
            </table>
         </td>
      </tr>
   </table>

   <br>

   <table style="width: 100%; border: 1px solid #ccc; border-collapse: collapse; font-size: 10px; table-layout: fixed;">
      <thead>
         <tr style="background: #f0f0f0;">
            <th style="width: 5%; border: 1px solid #ccc; padding: 5px; text-align: center;">No</th>
            <th style="width: 40%; border: 1px solid #ccc; padding: 5px; text-align: left;">Produk</th>
            <th style="width: 15%; border: 1px solid #ccc; padding: 5px; text-align: right;">Qty Transfer</th>
            <th style="width: 15%; border: 1px solid #ccc; padding: 5px; text-align: center;">Satuan</th>
            <th style="width: 25%; border: 1px solid #ccc; padding: 5px; text-align: left;">Batch No / Catatan</th>
         </tr>
      </thead>
      <tbody>
         <?php if (!empty($items)): ?>
            <?php $no = 1; foreach ($items as $it): ?>
               <tr>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: center;"><?= $no++; ?></td>
                  <td style="border: 1px solid #ccc; padding: 5px;"><strong><?= _ent($it->nama_produk ?? '-'); ?></strong><br><small style="color: #666;"><?= _ent($it->kode_produk ?? ''); ?></small></td>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: right;"><?= number_format($it->qty ?? 0, 0, ',', '.'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: center;"><?= _ent($it->singkatan ?? $it->nama_satuan ?? '-'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 5px;"><?= _ent(!empty($it->batch_no) ? 'Batch: '.$it->batch_no : (!empty($it->catatan) ? $it->catatan : '-')); ?></td>
               </tr>
            <?php endforeach; ?>
         <?php else: ?>
            <tr><td colspan="5" style="border: 1px solid #ccc; padding: 5px; text-align: center;">Tidak ada item.</td></tr>
         <?php endif; ?>
      </tbody>
   </table>

   <br><br><br>

   <table style="width: 100%; text-align: center; font-size: 11px;">
      <tr>
         <td style="width: 33%;">
            Pengirim (Gudang Asal),<br><br><br><br>
            ( ____________________ )
         </td>
         <td style="width: 33%;">
            Driver / Kurir,<br><br><br><br>
            ( ____________________ )
         </td>
         <td style="width: 34%;">
            Penerima (Gudang Tujuan),<br><br><br><br>
            ( ____________________ )
         </td>
      </tr>
   </table>
</page>