<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm">
   <table style="width: 100%; border-bottom: 2px solid #222; padding-bottom: 10px;">
      <tr>
         <td style="width: 55%;">
            <strong style="font-size: 16px; color: #111;">IDMETAFORA / CI3 METAFORA</strong><br>
            <span style="font-size: 11px; color: #555;">Sistem Penjualan, Pembelian &amp; Inventory</span><br>
            <span style="font-size: 10px; color: #666;">Telp: (0274) 123456 | Email: info@idmetafora.com</span>
         </td>
         <td style="width: 45%; text-align: right;">
            <strong style="font-size: 16px; color: #0056b3;">SALES ORDER / PESANAN PENJUALAN</strong><br>
            <span style="font-size: 11px;"><strong>No SO:</strong> <?= _ent($so->nomor_so ?? ''); ?></span><br>
            <span style="font-size: 11px;"><strong>Tanggal:</strong> <?= isset($so->tanggal_so) ? date('d/m/Y', strtotime($so->tanggal_so)) : '-'; ?></span>
         </td>
      </tr>
   </table>

   <br>

   <table style="width: 100%; font-size: 11px;">
      <tr>
         <td style="width: 50%; vertical-align: top;">
            <table style="width: 100%;">
               <tr><td style="width: 35%;"><strong>Pelanggan:</strong></td><td><?= _ent($so->nama_pelanggan ?? '-'); ?></td></tr>
               <tr><td style="width: 35%;"><strong>Alamat:</strong></td><td><?= _ent($so->alamat_pelanggan ?? '-'); ?></td></tr>
               <tr><td style="width: 35%;"><strong>No Telp:</strong></td><td><?= _ent($so->telp_pelanggan ?? '-'); ?></td></tr>
            </table>
         </td>
         <td style="width: 50%; vertical-align: top;">
            <table style="width: 100%;">
               <tr><td style="width: 40%;"><strong>Gudang Pengirim:</strong></td><td><?= _ent($so->nama_gudang ?? '-'); ?></td></tr>
               <tr><td style="width: 40%;"><strong>Est. Kirim:</strong></td><td><?= !empty($so->tanggal_estimasi_kirim) ? date('d/m/Y', strtotime($so->tanggal_estimasi_kirim)) : '-'; ?></td></tr>
               <tr><td style="width: 40%;"><strong>Salesperson:</strong></td><td><?= _ent($so->created_by ?? '-'); ?></td></tr>
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
            <th style="width: 10%; border: 1px solid #ccc; padding: 5px; text-align: right;">Qty Order</th>
            <th style="width: 8%; border: 1px solid #ccc; padding: 5px; text-align: center;">Satuan</th>
            <th style="width: 15%; border: 1px solid #ccc; padding: 5px; text-align: right;">Harga Satuan</th>
            <th style="width: 9%; border: 1px solid #ccc; padding: 5px; text-align: right;">Disc (%)</th>
            <th style="width: 18%; border: 1px solid #ccc; padding: 5px; text-align: right;">Subtotal</th>
         </tr>
      </thead>
      <tbody>
         <?php if (!empty($items)): ?>
            <?php $no = 1; foreach ($items as $it): ?>
               <tr>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: center;"><?= $no++; ?></td>
                  <td style="border: 1px solid #ccc; padding: 5px;">
                     <?= _ent($it->nama_produk ?? '-'); ?>
                     <?php if(!empty($it->kode_rak)): ?>
                        <br><span style="font-size: 9px; color: #666;">[Rak: <?= _ent($it->kode_rak); ?> - Baris <?= _ent($it->baris); ?>]</span>
                     <?php endif; ?>
                  </td>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: right;"><?= number_format((float)($it->qty ?? 0), 0, ',', '.'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: center;"><?= _ent($it->singkatan ?? $it->satuan ?? '-'); ?></td>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: right;">Rp <?= number_format($it->harga_satuan ?? 0, 0, ',', '.'); ?></td>
                   <?php 
                      $so_disc_pct = (float)($so->diskon_persen_total ?? 0);
                      if ($so_disc_pct == 0 && !empty($so->diskon_total) && !empty($so->subtotal) && $so->subtotal > 0) {
                         $so_disc_pct = round(($so->diskon_total / $so->subtotal) * 100, 1);
                      }
                      if ($so_disc_pct > 100) {
                         $so_disc_pct = min(100, $so_disc_pct / 100);
                      }
                      $it_disc = (float)(($it->diskon_persen > 0) ? $it->diskon_persen : $so_disc_pct);
                      if ($it_disc > 100) {
                         if ($it_disc <= 10000) {
                            $it_disc = $it_disc / 100;
                         } else {
                            $it_disc = $so_disc_pct;
                         }
                      }
                      $it_disc_fmt = ($it_disc == floor($it_disc)) ? number_format($it_disc, 0) : number_format($it_disc, 1);
                   ?>
                   <td style="border: 1px solid #ccc; padding: 5px; text-align: right;"><?= $it_disc_fmt; ?>%</td>
                  <td style="border: 1px solid #ccc; padding: 5px; text-align: right;">Rp <?= number_format($it->subtotal ?? 0, 0, ',', '.'); ?></td>
               </tr>
            <?php endforeach; ?>
         <?php else: ?>
            <tr><td colspan="7" style="border: 1px solid #ccc; padding: 5px; text-align: center;">Tidak ada item.</td></tr>
         <?php endif; ?>
      </tbody>
      <tfoot>
         <tr style="background: #eef7ff;">
            <td colspan="6" style="border: 1px solid #ccc; padding: 5px; text-align: right;"><strong>GRAND TOTAL:</strong></td>
            <td style="border: 1px solid #ccc; padding: 5px; text-align: right;"><strong style="color:#0056b3;">Rp <?= number_format($so->total ?? 0, 0, ',', '.'); ?></strong></td>
         </tr>
      </tfoot>
   </table>

   <br><br><br>

   <table style="width: 100%; text-align: center; font-size: 11px;">
      <tr>
         <td style="width: 33%;">
            Sales Representative,<br><br><br><br>
            ( ____________________ )
         </td>
         <td style="width: 33%;">
            Customer / Pemesan,<br><br><br><br>
            ( ____________________ )
         </td>
         <td style="width: 34%;">
            Sales Manager,<br><br><br><br>
            ( ____________________ )
         </td>
      </tr>
   </table>
</page>