... [bagian awal hilang] ...

| 1 – 15 Menit | 0% (Batas toleransi / Aman) |
| 16 – 30 Menit | 10% |
| 31 – 60 Menit | 25% |
| > 60 Menit | 50% |
 
**Batas Maksimal (Cap Limit):** Total akumulasi denda keterlambatan dalam sebulan maksimal **30% dari Gaji Pokok**.
 
### 3b. Ketentuan Absen Pulang & Skenario Darurat (Force Majeure)
- **Penguncian Pulang Normal (Hard-Block):** Tombol "Absen Pulang" di aplikasi presensi terkunci dan tidak bisa ditekan sebelum jam kerja resmi berakhir.
- **Skenario Izin Pulang Cepat (Darurat/Sakit di Kantor):** Jika karyawan harus pulang awal karena alasan darurat yang disetujui atasan, HRD/Atasan dapat menggunakan **Manual Clock-Out (Bypass)** di dashboard sistem. Efek: hari itu ditutup sebagai 1 Hari Kerja Penuh (Gapok & Tunjangan aman), tanpa denda absen gantung.
- **Absen Gantung (Lupa Absen Pulang):** Karyawan diberi keringanan waktu untuk absen pulang mandiri hingga **pukul 24:00 (jam 12 malam)** hari yang sama. Jika hingga jam tersebut belum absen pulang dan tidak ada Manual Clock-Out dari HRD, kehadiran hari itu otomatis dibatalkan (dianggap Alpa/Tidak Masuk).
## 4. Kebijakan Ketidakhadiran, Cuti & Hari Libur
 
| Jenis | Gapok | Tunjangan Tetap | Tunjangan Variabel |
|---|---|---|---|
| **Cuti Tahunan** | Dibayar penuh | Dibayar penuh | Dibayar penuh |
| **Sakit DENGAN Surat** | Dibayar penuh | Dibayar penuh | Dipotong harian |
| **Sakit TANPA Surat / Mangkir** | Dipotong penuh | Dipotong penuh | Dipotong penuh |
| **Libur Nasional (Tidak Masuk)** | Dibayar penuh | Dibayar penuh | Dipotong harian |
| **Libur Nasional (Masuk Kerja)** | Dibayar penuh + Lembur (jika ada SPL) | Dibayar penuh | Dibayar penuh |

... [bagian hilang] ...

**Pendapatan:** Upah Harian Total, Tunjangan Tetap, Tunjangan Variabel, Upah Lembur (ber-SPL), Rapel Bulan Lalu (jika ada).
 
**Potongan:** Potongan Keterlambatan, Potongan Wajib/Pajak (jika ada — BPJS/PPh 21, skema detail di luar cakupan brief ini).
 
**Take Home Pay (THP):**
```
THP = Total Pendapatan − Total Potongan
```
 
## 7. Modul & Arsitektur Backend yang Dibutuhkan (CodeIgniter 3)
- **Modul Pengaturan Periode Cut-Off (`tb_payroll_settings`):** Menyimpan tanggal mulai/akhir cut-off dan tanggal payday sebagai parameter yang bisa diubah Finance, bukan nilai tetap di kode.
- **Modul Sinkronisasi Hari Libur:** Mengisi `tb_holidays` via API kalender nasional.
- **Modul Presensi Darurat (HRD Bypass):** Fitur Manual Clock-Out di dashboard admin, mencegah absen gantung dan mengakomodasi pulang cepat darurat.
- **Modul Lembur & SPL (`tb_spl`, `tb_overtime_logs`):** Alur persetujuan SPL oleh Manager sebelum multiplier PP 35/2021 dieksekusi.
- **Modul Dokumen Sakit (`document_status`):** Validasi tenggat waktu unggah surat dokter (Pending/Approved/Rejected).
- **Modul Penyesuaian/Rapel (`tb_payroll_adjustments`):** Menyimpan koreksi nominal (plus/minus) pasca cut-off.
- **Engine Kalkulator Gaji (Cronjob/Controller):** Membaca data presensi, memvalidasi SPL, menyimpan angka absolut ke `tb_payslips`.
