<style>
        .slip-wrapper { font-family: 'Inter', sans-serif; font-size: 13px; line-height: 1.5; color: #2c3e50; padding: 40px; background: #eef2f5; }
        .slip-container { width: 100%; max-width: 850px; margin: 0 auto; background: #fff; padding: 40px 50px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-radius: 8px; position: relative; }
        
        .header-section { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #3498db; padding-bottom: 20px; margin-bottom: 30px; }
        .company-info h2 { margin: 0; font-size: 24px; color: #2c3e50; font-weight: 700; letter-spacing: 1px; }
        .company-info p { margin: 5px 0 0; color: #7f8c8d; font-size: 13px; }
        .slip-title { text-align: right; }
        .slip-title h1 { margin: 0; font-size: 28px; color: #3498db; text-transform: uppercase; font-weight: 800; letter-spacing: 2px; }
        .slip-title p { margin: 5px 0 0; color: #34495e; font-size: 14px; font-weight: 600; background: #f1f8ff; display: inline-block; padding: 4px 12px; border-radius: 4px; }
        
        .info-grid { display: flex; justify-content: space-between; background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 30px; border: 1px solid #eee; }
        .info-col { width: 48%; }
        .info-table { width: 100%; }
        .info-table td { padding: 6px 0; font-size: 13px; }
        .info-table td:nth-child(1) { font-weight: 600; color: #7f8c8d; width: 35%; }
        .info-table td:nth-child(2) { font-weight: 600; color: #2c3e50; }

        .details-container { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .details-col { width: 48%; }
        
        .rincian-table { width: 100%; border-collapse: collapse; }
        .rincian-table th { background: #f8f9fa; color: #2c3e50; font-weight: 700; padding: 12px 15px; text-align: left; border-bottom: 2px solid #ddd; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        .rincian-table td { padding: 12px 15px; border-bottom: 1px solid #eee; vertical-align: top; }
        .rincian-table td.angka { text-align: right; font-weight: 600; font-family: monospace; font-size: 14px; }
        .rincian-table tr.total th, .rincian-table tr.total td { background: #fcfcfc; font-weight: 700; border-top: 2px solid #ddd; border-bottom: none; color: #2c3e50; }
        
        .catatan-box { background: #fffcf5; border-left: 4px solid #f39c12; padding: 15px; margin-top: 20px; border-radius: 0 4px 4px 0; }
        .catatan-box p { margin: 0; font-size: 12px; color: #d35400; line-height: 1.6; }
        
        .grand-total-box { display: flex; justify-content: space-between; align-items: center; background: #3498db; color: #fff; padding: 20px 30px; border-radius: 8px; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3); }
        .grand-total-label { font-size: 18px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .grand-total-value { font-size: 24px; font-weight: 800; }
        
        .footer-ttd { display: flex; justify-content: space-between; padding: 0 40px; }
        .ttd-box { text-align: center; width: 200px; }
        .ttd-box p { margin: 0 0 10px 0; color: #7f8c8d; font-size: 13px; }
        .ttd-space { height: 80px; border-bottom: 1px dashed #ccc; margin-bottom: 10px; }
        .ttd-name { font-weight: 700; color: #2c3e50; font-size: 14px; }

        @media print {
            @page { margin: 0; size: auto; }
            .main-header, .main-sidebar, .main-footer, .control-sidebar { display: none !important; }
            .slip-wrapper { padding: 0 !important; background: #fff !important; }
            .content-wrapper { margin-left: 0 !important; z-index: 999; }
            .slip-container { box-shadow: none; max-width: 100%; border: none; padding: 15mm !important; }
            .no-print { display: none !important; }
            .grand-total-box { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .rincian-table th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        
        @media (max-width: 768px) {
            .slip-wrapper { padding: 10px; }
            .slip-container { padding: 20px; }
            .header-section { flex-direction: column; text-align: center; align-items: center; }
            .slip-title { text-align: center; margin-top: 15px; }
            .info-grid { flex-direction: column; padding: 15px; }
            .info-col { width: 100%; margin-bottom: 10px; }
            .info-col:last-child { margin-bottom: 0; }
            .details-container { flex-direction: column; }
            .details-col { width: 100%; margin-bottom: 20px; }
            .details-col:last-child { margin-bottom: 0; }
            .grand-total-box { flex-direction: column; text-align: center; padding: 15px; }
            .grand-total-label { margin-bottom: 5px; font-size: 14px; }
            .grand-total-value { font-size: 20px; }
            .footer-ttd { flex-direction: column; align-items: center; padding: 0; }
            .ttd-box { margin-bottom: 20px; }
            .rincian-table th, .rincian-table td { padding: 8px 5px; font-size: 11px; }
            .rincian-table td.angka { font-size: 11px; }
            .info-table td { font-size: 11px; }
            .company-info h2 { font-size: 20px; }
            .slip-title h1 { font-size: 22px; }
        }

        .main-footer {
            margin-left: 0 !important;
            text-align: left !important;
        }
    </style>

<div class="slip-wrapper">
    <div class="no-print" style="text-align: center; margin-bottom: 30px;">
        <a href="<?= site_url('administrator/hr_payroll') ?>" style="display:inline-block; padding:12px 25px; background:#fff; color:#34495e; border:1px solid #ddd; text-decoration:none; margin-right:15px; font-weight:600; border-radius:6px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); transition: 0.3s;"><i class="fa fa-arrow-left margin-r-5"></i> Kembali</a>
        <button id="btn-download-pdf" style="padding:12px 25px; background:#3498db; color:#fff; border:none; cursor:pointer; font-weight:600; border-radius:6px; box-shadow: 0 4px 10px rgba(52,152,219,0.3); font-family: 'Inter', sans-serif; font-size: 14px; transition: 0.3s;"><i class="fa fa-print margin-r-5"></i> Cetak Dokumen (PDF)</button>
    </div>

    <div class="slip-container">

            <div class="header-section">
                <div class="company-info">
                    <h2>PT. IDMETAFORA</h2>
                    <p>Jl. Perusahaan No. 123, Kota Yogyakarta<br>Telp: (0274) 123456 • Email: hrd@idmetafora.com</p>
                </div>
                <div class="slip-title">
                    <h1>SLIP GAJI</h1>
                    <p>PERIODE: <?= date('F Y', strtotime($slip->periode_bulan.'-01')) ?></p>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-col">
                    <table class="info-table">
                        <tr><td>NAMA KARYAWAN</td><td>: <?= _ent($slip->nama_lengkap) ?></td></tr>
                        <tr><td>NOMOR INDUK (NIP)</td><td>: <?= _ent($slip->nik) ?></td></tr>
                    </table>
                </div>
                <div class="info-col">
                    <table class="info-table">
                        <tr><td>DEPARTEMEN</td><td>: <?= _ent($slip->departemen) ?></td></tr>
                        <tr><td>JABATAN</td><td>: <?= _ent($slip->jabatan) ?> (<?= _ent($slip->tipe_kerja) ?>)</td></tr>
                    </table>
                </div>
            </div>

            <div class="details-container">
                <!-- Kolom Penghasilan -->
                <div class="details-col">
                    <table class="rincian-table">
                        <tr><th colspan="2" style="border-top: 3px solid #2ecc71;">PENDAPATAN</th></tr>
                        <tr>
                            <td>
                                <strong style="color:#2c3e50;">Gaji Pokok Harian</strong><br>
                                <span style="font-size:11px; color:#7f8c8d;">(<?= $slip->hari_dibayar_penuh ?> hari &times; Rp <?= number_format((float)$slip->gaji_harian_pokok,0,',','.') ?>)</span>
                            </td>
                            <td class="angka">Rp <?= number_format((float)$slip->gaji_pokok_diterima,0,',','.') ?></td>
                        </tr>
                        <tr>
                            <td>
                                <strong style="color:#2c3e50;">Tunjangan Tetap</strong><br>
                                <span style="font-size:11px; color:#7f8c8d;">(<?= $slip->hari_dibayar_penuh ?> hari &times; Rp <?= number_format((float)$slip->tunj_tetap_harian,0,',','.') ?>)</span>
                            </td>
                            <td class="angka">Rp <?= number_format((float)$slip->tunj_tetap_diterima,0,',','.') ?></td>
                        </tr>
                        <tr>
                            <td>
                                <strong style="color:#2c3e50;">Tunjangan Kehadiran</strong><br>
                                <span style="font-size:11px; color:#7f8c8d;">(<?= $slip->hari_hadir_fisik ?> hari &times; Rp <?= number_format((float)$slip->tunj_kehadiran_harian,0,',','.') ?>)</span>
                            </td>
                            <td class="angka">Rp <?= number_format((float)$slip->tunj_kehadiran_diterima,0,',','.') ?></td>
                        </tr>
                        <?php if($slip->uang_lembur > 0): ?>
                        <tr>
                            <td><strong style="color:#2c3e50;">Uang Lembur / Insentif</strong></td>
                            <td class="angka">Rp <?= number_format((float)$slip->uang_lembur,0,',','.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="total">
                            <td>TOTAL PENDAPATAN (A)</td>
                            <td class="angka" style="color:#2ecc71;">Rp <?= number_format((float)$slip->gaji_kotor,0,',','.') ?></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Kolom Potongan -->
                <div class="details-col">
                    <table class="rincian-table">
                        <tr><th colspan="2" style="border-top: 3px solid #e74c3c;">POTONGAN</th></tr>
                        <tr>
                            <td>
                                <strong style="color:#2c3e50;">Potongan Keterlambatan</strong><br>
                                <span style="font-size:11px; color:#7f8c8d;">(Total akumulasi denda progresif)</span>
                            </td>
                            <td class="angka text-danger" style="color:#e74c3c;">- Rp <?= number_format((float)$slip->potongan_terlambat,0,',','.') ?></td>
                        </tr>
                        <?php if($slip->potongan_alfa > 0): ?>
                        <tr>
                            <td>
                                <strong style="color:#2c3e50;">Potongan Alpha / Mangkir</strong><br>
                                <span style="font-size:11px; color:#7f8c8d;">(<?= $slip->jumlah_alfa ?> hari absen tanpa keterangan)</span>
                            </td>
                            <td class="angka text-danger" style="color:#e74c3c;">- Rp <?= number_format((float)$slip->potongan_alfa,0,',','.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($slip->potongan_izin_sakit > 0): ?>
                        <tr>
                            <td>
                                <strong style="color:#2c3e50;">Potongan Izin / Cuti Unpaid</strong><br>
                                <span style="font-size:11px; color:#7f8c8d;">(Pemotongan tunjangan tidak hadir)</span>
                            </td>
                            <td class="angka text-danger" style="color:#e74c3c;">- Rp <?= number_format((float)$slip->potongan_izin_sakit,0,',','.') ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="total">
                            <td>TOTAL POTONGAN (B)</td>
                            <td class="angka" style="color:#e74c3c;">- Rp <?= number_format((float)$slip->total_potongan,0,',','.') ?></td>
                        </tr>
                    </table>
                    
                    <div class="catatan-box">
                        <p><strong>Catatan Kehadiran (<?= $slip->hari_kerja_sebulan ?> Hari Efektif):</strong><br>
                        Hadir Fisik: <strong><?= $slip->jumlah_hadir ?></strong> | Cuti: <strong><?= $slip->jumlah_cuti ?></strong> | Sakit: <strong><?= $slip->jumlah_sakit_surat + $slip->jumlah_sakit_tanpa_surat ?></strong> | Alpha: <strong><?= $slip->jumlah_alfa ?></strong><br>
                        Tercatat Terlambat: <strong><?= $slip->jumlah_terlambat_ringan ?? 0 ?> hari</strong></p>
                    </div>
                </div>
            </div>

            <div class="grand-total-box">
                <div class="grand-total-label">Gaji Bersih Diterima (Take Home Pay)</div>
                <div class="grand-total-value">Rp <?= number_format((float)$slip->gaji_bersih,0,',','.') ?></div>
            </div>

            <div class="footer-ttd">
                <div class="ttd-box">
                    <p>Diterima Oleh,</p>
                    <div class="ttd-space"></div>
                    <div class="ttd-name"><?= _ent($slip->nama_lengkap) ?></div>
                </div>
                <div class="ttd-box">
                    <p>Disetujui Oleh,</p>
                    <div class="ttd-space"></div>
                    <div class="ttd-name">HRD / Finance Manager</div>
                </div>
            </div>


    <script>
        document.getElementById('btn-download-pdf').addEventListener('click', function () {
            window.print();
        });
        <?php if(isset($_GET['download']) && $_GET['download'] == 'true'): ?>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 800);
        };
        <?php endif; ?>
    </script>
</div>
