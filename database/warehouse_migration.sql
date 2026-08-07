-- ============================================================
-- MIGRATION: Modul Penjualan & Warehouse
-- CI3 Metafora | Created: 2026-08-03
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- MASTER DATA
-- ============================================================

CREATE TABLE IF NOT EXISTS `supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_supplier` varchar(20) NOT NULL,
  `nama_supplier` varchar(150) NOT NULL,
  `kontak_person` varchar(100) DEFAULT NULL,
  `telepon` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `negara` varchar(100) DEFAULT 'Indonesia',
  `mata_uang` varchar(10) DEFAULT 'IDR',
  `kurs_manual` decimal(18,4) DEFAULT 1.0000,
  `npwp` varchar(30) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `bank_holder` varchar(100) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `catatan` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_supplier` (`kode_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pelanggan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_pelanggan` varchar(20) NOT NULL,
  `nama_pelanggan` varchar(150) NOT NULL,
  `tipe_pelanggan` enum('retail','grosir','distributor','vip') DEFAULT 'retail',
  `kontak_person` varchar(100) DEFAULT NULL,
  `telepon` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `npwp` varchar(30) DEFAULT NULL,
  `kredit_poin` decimal(18,2) DEFAULT 0.00,
  `limit_kredit` decimal(18,2) DEFAULT 0.00,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `catatan` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_pelanggan` (`kode_pelanggan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `satuan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_satuan` varchar(50) NOT NULL,
  `singkatan` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `kategori_produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_produk` varchar(30) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `nama_produk` varchar(200) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `satuan_id` int(11) DEFAULT NULL,
  `harga_beli` decimal(18,2) DEFAULT 0.00,
  `harga_jual` decimal(18,2) DEFAULT 0.00,
  `stok_minimum` int(11) DEFAULT 0,
  `berat` decimal(10,3) DEFAULT NULL COMMENT 'kg',
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(200) DEFAULT NULL,
  `metode_stok` enum('FIFO','FEFO','AVERAGE') DEFAULT 'FIFO',
  `batch_tracking` tinyint(1) DEFAULT 0,
  `tanggal_kadaluarsa` tinyint(1) DEFAULT 0,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_produk` (`kode_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `harga_pelanggan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produk_id` int(11) NOT NULL,
  `tipe_pelanggan` enum('retail','grosir','distributor','vip') NOT NULL,
  `min_qty` int(11) DEFAULT 1,
  `harga` decimal(18,2) NOT NULL,
  `diskon_persen` decimal(5,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gudang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_gudang` varchar(20) NOT NULL,
  `nama_gudang` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `pic_name` varchar(100) DEFAULT NULL,
  `telepon` varchar(30) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_gudang` (`kode_gudang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rak` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gudang_id` int(11) NOT NULL,
  `kode_rak` varchar(20) NOT NULL,
  `baris` varchar(10) DEFAULT NULL,
  `deskripsi` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- STOK & KURS
-- ============================================================

CREATE TABLE IF NOT EXISTS `stok` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produk_id` int(11) NOT NULL,
  `gudang_id` int(11) NOT NULL,
  `qty` decimal(18,3) DEFAULT 0.000,
  `qty_rusak` decimal(18,3) DEFAULT 0.000,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produk_gudang` (`produk_id`, `gudang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `stok_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produk_id` int(11) NOT NULL,
  `gudang_id` int(11) NOT NULL,
  `batch_no` varchar(50) DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `jenis` enum('masuk','keluar','penyesuaian','transfer_masuk','transfer_keluar') NOT NULL,
  `qty` decimal(18,3) NOT NULL,
  `harga_satuan` decimal(18,2) DEFAULT 0.00,
  `referensi_type` varchar(50) DEFAULT NULL COMMENT 'purchase_order/sales_order/retur/opname/transfer',
  `referensi_id` int(11) DEFAULT NULL,
  `keterangan` varchar(300) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `kurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mata_uang` varchar(10) NOT NULL,
  `nilai_kurs` decimal(18,4) NOT NULL,
  `tanggal` date NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- MODUL PEMBELIAN
-- ============================================================

CREATE TABLE IF NOT EXISTS `purchase_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_po` varchar(30) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `gudang_id` int(11) DEFAULT NULL,
  `tanggal_po` date NOT NULL,
  `tanggal_estimasi` date DEFAULT NULL,
  `mata_uang` varchar(10) DEFAULT 'IDR',
  `kurs` decimal(18,4) DEFAULT 1.0000,
  `subtotal` decimal(18,2) DEFAULT 0.00,
  `diskon` decimal(18,2) DEFAULT 0.00,
  `pajak_persen` decimal(5,2) DEFAULT 0.00,
  `pajak_nominal` decimal(18,2) DEFAULT 0.00,
  `total` decimal(18,2) DEFAULT 0.00,
  `status` enum('draft','submitted','approved','rejected','received','partial','cancelled') DEFAULT 'draft',
  `approval_level` tinyint(1) DEFAULT 0,
  `catatan` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_po` (`nomor_po`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `purchase_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `po_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty_pesan` decimal(18,3) NOT NULL,
  `qty_terima` decimal(18,3) DEFAULT 0.000,
  `satuan` varchar(20) DEFAULT NULL,
  `harga_satuan` decimal(18,2) DEFAULT 0.00,
  `diskon_persen` decimal(5,2) DEFAULT 0.00,
  `diskon_nominal` decimal(18,2) DEFAULT 0.00,
  `subtotal` decimal(18,2) DEFAULT 0.00,
  `catatan` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `penerimaan_barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_penerimaan` varchar(30) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) NOT NULL,
  `gudang_id` int(11) NOT NULL,
  `tanggal_terima` date NOT NULL,
  `nomor_surat_jalan` varchar(50) DEFAULT NULL,
  `status` enum('draft','confirmed','cancelled') DEFAULT 'draft',
  `catatan` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_penerimaan` (`nomor_penerimaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `penerimaan_barang_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `penerimaan_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `po_item_id` int(11) DEFAULT NULL,
  `qty_terima` decimal(18,3) NOT NULL,
  `qty_baik` decimal(18,3) DEFAULT 0.000,
  `qty_rusak` decimal(18,3) DEFAULT 0.000,
  `satuan` varchar(20) DEFAULT NULL,
  `harga_satuan` decimal(18,2) DEFAULT 0.00,
  `batch_no` varchar(50) DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `kondisi` enum('baik','rusak','sebagian_rusak') DEFAULT 'baik',
  `catatan` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `retur_pembelian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_retur` varchar(30) NOT NULL,
  `penerimaan_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `gudang_id` int(11) NOT NULL,
  `tanggal_retur` date NOT NULL,
  `alasan` text NOT NULL,
  `total` decimal(18,2) DEFAULT 0.00,
  `status` enum('draft','confirmed','cancelled') DEFAULT 'draft',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_retur` (`nomor_retur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `retur_pembelian_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `retur_pembelian_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty_retur` decimal(18,3) NOT NULL,
  `harga_satuan` decimal(18,2) DEFAULT 0.00,
  `subtotal` decimal(18,2) DEFAULT 0.00,
  `alasan` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `utang_usaha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_utang` varchar(30) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `penerimaan_id` int(11) DEFAULT NULL,
  `total` decimal(18,2) NOT NULL,
  `terbayar` decimal(18,2) DEFAULT 0.00,
  `sisa` decimal(18,2) NOT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `status` enum('belum_bayar','sebagian','lunas') DEFAULT 'belum_bayar',
  `catatan` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_utang` (`nomor_utang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- MODUL PENJUALAN
-- ============================================================

CREATE TABLE IF NOT EXISTS `sales_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_so` varchar(30) NOT NULL,
  `pelanggan_id` int(11) NOT NULL,
  `gudang_id` int(11) DEFAULT NULL,
  `tanggal_so` date NOT NULL,
  `tanggal_estimasi_kirim` date DEFAULT NULL,
  `subtotal` decimal(18,2) DEFAULT 0.00,
  `diskon_total` decimal(18,2) DEFAULT 0.00,
  `pajak_persen` decimal(5,2) DEFAULT 0.00,
  `pajak_nominal` decimal(18,2) DEFAULT 0.00,
  `total` decimal(18,2) DEFAULT 0.00,
  `status` enum('draft','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'draft',
  `catatan` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_so` (`nomor_so`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sales_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `so_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty` decimal(18,3) NOT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `harga_satuan` decimal(18,2) DEFAULT 0.00,
  `diskon_persen` decimal(5,2) DEFAULT 0.00,
  `diskon_nominal` decimal(18,2) DEFAULT 0.00,
  `subtotal` decimal(18,2) DEFAULT 0.00,
  `catatan` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_invoice` varchar(30) NOT NULL,
  `so_id` int(11) DEFAULT NULL,
  `pelanggan_id` int(11) NOT NULL,
  `gudang_id` int(11) DEFAULT NULL,
  `tanggal_invoice` date NOT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `subtotal` decimal(18,2) DEFAULT 0.00,
  `diskon_total` decimal(18,2) DEFAULT 0.00,
  `pajak_persen` decimal(5,2) DEFAULT 0.00,
  `pajak_nominal` decimal(18,2) DEFAULT 0.00,
  `total` decimal(18,2) DEFAULT 0.00,
  `status` enum('draft','sent','partial','paid','overdue','cancelled') DEFAULT 'draft',
  `status_kirim` enum('pending','processing','shipped','delivered') DEFAULT 'pending',
  `nomor_resi` varchar(100) DEFAULT NULL,
  `ekspedisi` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_invoice` (`nomor_invoice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `invoice_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `so_item_id` int(11) DEFAULT NULL,
  `qty` decimal(18,3) NOT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `harga_satuan` decimal(18,2) DEFAULT 0.00,
  `diskon_persen` decimal(5,2) DEFAULT 0.00,
  `diskon_nominal` decimal(18,2) DEFAULT 0.00,
  `subtotal` decimal(18,2) DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `piutang_usaha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_piutang` varchar(30) NOT NULL,
  `pelanggan_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `total` decimal(18,2) NOT NULL,
  `terbayar` decimal(18,2) DEFAULT 0.00,
  `sisa` decimal(18,2) NOT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `status` enum('belum_bayar','sebagian','lunas','macet') DEFAULT 'belum_bayar',
  `catatan` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_piutang` (`nomor_piutang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `retur_penjualan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_retur` varchar(30) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `pelanggan_id` int(11) NOT NULL,
  `gudang_id` int(11) DEFAULT NULL,
  `tanggal_retur` date NOT NULL,
  `alasan` text NOT NULL,
  `tindak_lanjut` enum('tukar_barang','refund','kredit_poin') DEFAULT 'tukar_barang',
  `total` decimal(18,2) DEFAULT 0.00,
  `status` enum('draft','approved','selesai','cancelled') DEFAULT 'draft',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_retur` (`nomor_retur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `retur_penjualan_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `retur_penjualan_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty_retur` decimal(18,3) NOT NULL,
  `harga_satuan` decimal(18,2) DEFAULT 0.00,
  `subtotal` decimal(18,2) DEFAULT 0.00,
  `kondisi` enum('baik','rusak') DEFAULT 'baik',
  `alasan` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- MODUL STOCK OPNAME & WAREHOUSE
-- ============================================================

CREATE TABLE IF NOT EXISTS `stock_opname` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_opname` varchar(30) NOT NULL,
  `gudang_id` int(11) NOT NULL,
  `tanggal_opname` date NOT NULL,
  `jadwal` enum('harian','mingguan','bulanan','insidental') DEFAULT 'bulanan',
  `status` enum('draft','process','review','approved','cancelled') DEFAULT 'draft',
  `total_selisih_nilai` decimal(18,2) DEFAULT 0.00,
  `catatan` text DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_opname` (`nomor_opname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `stock_opname_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `opname_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `rak_id` int(11) DEFAULT NULL,
  `stok_sistem` decimal(18,3) DEFAULT 0.000,
  `stok_fisik` decimal(18,3) DEFAULT 0.000,
  `selisih` decimal(18,3) DEFAULT 0.000,
  `harga_satuan` decimal(18,2) DEFAULT 0.00,
  `nilai_selisih` decimal(18,2) DEFAULT 0.00,
  `alasan_selisih` varchar(300) DEFAULT NULL,
  `status_sesuai` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `transfer_stok` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_transfer` varchar(30) NOT NULL,
  `gudang_asal_id` int(11) NOT NULL,
  `gudang_tujuan_id` int(11) NOT NULL,
  `tanggal_transfer` date NOT NULL,
  `tanggal_estimasi_tiba` date DEFAULT NULL,
  `status` enum('draft','submitted','approved','shipped','received','cancelled') DEFAULT 'draft',
  `nomor_bukti_kirim` varchar(50) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `received_by` varchar(100) DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_transfer` (`nomor_transfer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `transfer_stok_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `rak_asal_id` int(11) DEFAULT NULL,
  `rak_tujuan_id` int(11) DEFAULT NULL,
  `qty` decimal(18,3) NOT NULL,
  `batch_no` varchar(50) DEFAULT NULL,
  `catatan` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `approval_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referensi_type` varchar(50) NOT NULL COMMENT 'purchase_order/transfer_stok/stock_opname',
  `referensi_id` int(11) NOT NULL,
  `level` tinyint(1) DEFAULT 1,
  `action` enum('submitted','approved','rejected') NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- SEED DATA AWAL
-- ============================================================

INSERT IGNORE INTO `satuan` (`nama_satuan`, `singkatan`, `created_at`) VALUES
('Pieces', 'pcs', NOW()),
('Kilogram', 'kg', NOW()),
('Liter', 'ltr', NOW()),
('Meter', 'm', NOW()),
('Box', 'box', NOW()),
('Karton', 'ktn', NOW());

INSERT IGNORE INTO `kategori_produk` (`nama_kategori`, `deskripsi`, `created_at`) VALUES
('Umum', 'Produk umum', NOW()),
('Elektronik', 'Produk elektronik', NOW()),
('Makanan & Minuman', 'Produk F&B', NOW());

INSERT IGNORE INTO `gudang` (`kode_gudang`, `nama_gudang`, `alamat`, `status`, `created_at`) VALUES
('GDG-001', 'Gudang Utama', 'Jakarta', 'aktif', NOW());

INSERT IGNORE INTO `supplier` (`kode_supplier`, `nama_supplier`, `status`, `created_at`) VALUES
('SUP-001', 'Supplier Contoh', 'aktif', NOW());

INSERT IGNORE INTO `pelanggan` (`kode_pelanggan`, `nama_pelanggan`, `tipe_pelanggan`, `status`, `created_at`) VALUES
('CUS-001', 'Pelanggan Contoh', 'retail', 'aktif', NOW());

INSERT IGNORE INTO `produk` (`kode_produk`, `nama_produk`, `harga_beli`, `harga_jual`, `stok_minimum`, `status`, `created_at`) VALUES
('PRD-001', 'Produk Contoh', 50000.00, 75000.00, 10, 'aktif', NOW());

INSERT IGNORE INTO `stok` (`produk_id`, `gudang_id`, `qty`, `qty_rusak`, `updated_at`)
SELECT p.id, g.id, 0, 0, NOW()
FROM produk p, gudang g
WHERE p.kode_produk = 'PRD-001' AND g.kode_gudang = 'GDG-001';

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- END OF MIGRATION
-- ============================================================
