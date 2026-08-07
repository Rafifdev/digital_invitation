# Brief Modul Penjualan dan Warehouse

## Tujuan Modul

Modul ini mengatur alur barang dari pembelian sampai penjualan. Modul ini mencatat retur dan stok secara akurat. Sistem ini mengontrol stok, harga, dan margin secara real time.

## Menu Pembelian

-   Purchase order ke supplier dengan nomor PO otomatis
-   Approval berjenjang untuk PO di atas nominal tertentu
-   Penerimaan barang dengan pengecekan jumlah dan kondisi fisik
-   Pencatatan harga beli per supplier dan riwayat perubahan harga
-   Retur pembelian ke supplier untuk barang rusak atau salah kirim
-   Utang usaha terbentuk otomatis saat barang diterima
-   Transaksi multi mata uang untuk supplier luar negeri

## Menu Penjualan

-   Sales order dan invoice dalam satu alur kerja
-   Harga bertingkat berdasarkan tipe pelanggan atau volume
-   Diskon per item atau per transaksi
-   Cek stok otomatis sebelum order dikonfirmasi
-   Piutang usaha terbentuk otomatis saat invoice terbit
-   Update status kirim lewat integrasi pengiriman
-   Riwayat transaksi per pelanggan untuk analisis pembelian ulang

## Menu Retur Penjualan

-   Retur barang dari pelanggan dengan alasan wajib diisi
-   Pilihan tindak lanjut: tukar barang, refund, atau kredit poin
-   Barang retur masuk kembali ke stok jika kondisi masih baik
-   Barang rusak masuk ke kategori stok terpisah dan tidak dijual ulang
-   Laporan retur per produk untuk melihat pola barang bermasalah

## Menu Stock Opname

-   Jadwal opname mingguan atau bulanan
-   Input hasil hitung fisik lewat barcode scanner atau aplikasi mobile
-   Perbandingan stok fisik dengan stok sistem secara otomatis
-   Selisih stok tercatat sebagai penyesuaian dengan alasan wajib diisi
-   Approval untuk penyesuaian stok di atas nilai tertentu
-   Laporan selisih stok per lokasi dan per produk

## Menu Warehouse Tambahan

-   Multi gudang dengan stok terpisah per lokasi
-   Transfer stok antar gudang dengan approval dan bukti kirim
-   Lokasi rak detail, kode rak dan baris, untuk mempercepat picking
-   Metode FIFO atau FEFO untuk produk dengan tanggal kadaluarsa
-   Barcode scanning saat terima, picking, dan kirim barang
-   Notifikasi stok minimum untuk trigger pembelian ulang otomatis
-   Batch atau lot tracking untuk produk yang butuh ketelusuran
-   Dashboard real time untuk level stok, barang masuk, dan barang keluar
-   Cycle counting harian untuk produk fast moving tanpa hentikan operasional gudang

## Alur Data Antar Menu

Pembelian menambah stok. Penjualan mengurangi stok. Retur penjualan menambah stok kembali jika kondisi baik. Stock opname mengoreksi selisih stok. Semua transaksi ini masuk ke kartu stok per produk. Kartu stok ini bisa diaudit kapan saja.