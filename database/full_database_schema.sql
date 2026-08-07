-- ========================================================
-- DATABASE SCHEMA: CI3 METAFORA (PENJUALAN & WAREHOUSE)
-- Generated At: 2026-08-04 01:27:00
-- Database Engine: MySQL 8.0+ / MariaDB
-- ========================================================

SET FOREIGN_KEY_CHECKS=0;

-- Table structure for `aauth_group_to_group` --
DROP TABLE IF EXISTS `aauth_group_to_group`;
CREATE TABLE `aauth_group_to_group` (
  `group_id` int unsigned NOT NULL,
  `subgroup_id` int unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`subgroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `aauth_groups` --
DROP TABLE IF EXISTS `aauth_groups`;
CREATE TABLE `aauth_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `definition` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- Table structure for `aauth_login_attempts` --
DROP TABLE IF EXISTS `aauth_login_attempts`;
CREATE TABLE `aauth_login_attempts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(39) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `login_attempts` tinyint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=690 DEFAULT CHARSET=utf8mb3;

-- Table structure for `aauth_perm_to_group` --
DROP TABLE IF EXISTS `aauth_perm_to_group`;
CREATE TABLE `aauth_perm_to_group` (
  `perm_id` int NOT NULL,
  `group_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `aauth_perm_to_user` --
DROP TABLE IF EXISTS `aauth_perm_to_user`;
CREATE TABLE `aauth_perm_to_user` (
  `perm_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`perm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `aauth_perms` --
DROP TABLE IF EXISTS `aauth_perms`;
CREATE TABLE `aauth_perms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `definition` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=530 DEFAULT CHARSET=utf8mb3;

-- Table structure for `aauth_pms` --
DROP TABLE IF EXISTS `aauth_pms`;
CREATE TABLE `aauth_pms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int unsigned NOT NULL,
  `receiver_id` int unsigned NOT NULL,
  `title` varchar(225) NOT NULL,
  `message` text,
  `date_sent` datetime DEFAULT NULL,
  `date_read` datetime DEFAULT NULL,
  `pm_deleted_sender` int DEFAULT NULL,
  `pm_deleted_receiver` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `aauth_user` --
DROP TABLE IF EXISTS `aauth_user`;
CREATE TABLE `aauth_user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `definition` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `aauth_user_to_group` --
DROP TABLE IF EXISTS `aauth_user_to_group`;
CREATE TABLE `aauth_user_to_group` (
  `user_id` int unsigned NOT NULL,
  `group_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `aauth_user_variables` --
DROP TABLE IF EXISTS `aauth_user_variables`;
CREATE TABLE `aauth_user_variables` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `data_key` varchar(100) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `aauth_users` --
DROP TABLE IF EXISTS `aauth_users`;
CREATE TABLE `aauth_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `oauth_uid` text,
  `oauth_provider` varchar(100) DEFAULT NULL,
  `pass` varchar(64) NOT NULL,
  `username` varchar(100) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `avatar` text NOT NULL,
  `banned` tinyint(1) DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `forgot_exp` text,
  `remember_time` datetime DEFAULT NULL,
  `remember_exp` text,
  `verification_code` text,
  `top_secret` varchar(16) DEFAULT NULL,
  `ip_address` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3;

-- Table structure for `administrator` --
DROP TABLE IF EXISTS `administrator`;
CREATE TABLE `administrator` (
  `id_administrator` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(50) NOT NULL,
  `bisa_portofolio` int NOT NULL,
  `bisa_cashflow` int NOT NULL,
  `bisa_artikel` int NOT NULL,
  `bisa_category` int NOT NULL,
  `bisa_promo` int NOT NULL,
  `bisa_whois` int NOT NULL,
  `bisa_contact` int NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_administrator`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Table structure for `approval_log` --
DROP TABLE IF EXISTS `approval_log`;
CREATE TABLE `approval_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `referensi_type` varchar(50) NOT NULL COMMENT 'purchase_order/transfer_stok/stock_opname',
  `referensi_id` int NOT NULL,
  `level` tinyint(1) DEFAULT '1',
  `action` enum('submitted','approved','rejected') NOT NULL,
  `catatan` text,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `blog` --
DROP TABLE IF EXISTS `blog`;
CREATE TABLE `blog` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `image` text NOT NULL,
  `tags` text NOT NULL,
  `category` varchar(200) NOT NULL,
  `status` varchar(10) NOT NULL,
  `author` varchar(100) NOT NULL,
  `viewers` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Table structure for `blog_category` --
DROP TABLE IF EXISTS `blog_category`;
CREATE TABLE `blog_category` (
  `category_id` int unsigned NOT NULL,
  `category_name` varchar(200) NOT NULL,
  `category_desc` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `captcha` --
DROP TABLE IF EXISTS `captcha`;
CREATE TABLE `captcha` (
  `captcha_id` int unsigned NOT NULL AUTO_INCREMENT,
  `captcha_time` int DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `word` varchar(20) NOT NULL,
  PRIMARY KEY (`captcha_id`)
) ENGINE=MyISAM AUTO_INCREMENT=204 DEFAULT CHARSET=utf8mb3;

-- Table structure for `category` --
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id_category` int NOT NULL AUTO_INCREMENT,
  `category` varchar(200) NOT NULL,
  `deskripsi` text NOT NULL,
  PRIMARY KEY (`id_category`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Table structure for `cc_options` --
DROP TABLE IF EXISTS `cc_options`;
CREATE TABLE `cc_options` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(200) NOT NULL,
  `option_value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3;

-- Table structure for `cc_session` --
DROP TABLE IF EXISTS `cc_session`;
CREATE TABLE `cc_session` (
  `id` int NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int NOT NULL,
  `data` blob NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `ci_sessions` --
DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table structure for `counter` --
DROP TABLE IF EXISTS `counter`;
CREATE TABLE `counter` (
  `id_counter` int NOT NULL AUTO_INCREMENT,
  `jumlah` int NOT NULL,
  PRIMARY KEY (`id_counter`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Table structure for `crud` --
DROP TABLE IF EXISTS `crud`;
CREATE TABLE `crud` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `table_name` varchar(200) NOT NULL,
  `primary_key` varchar(200) NOT NULL,
  `page_read` varchar(20) DEFAULT NULL,
  `page_create` varchar(20) DEFAULT NULL,
  `page_update` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb3;

-- Table structure for `crud_custom_option` --
DROP TABLE IF EXISTS `crud_custom_option`;
CREATE TABLE `crud_custom_option` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `crud_field_id` int NOT NULL,
  `crud_id` int NOT NULL,
  `option_value` text NOT NULL,
  `option_label` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb3;

-- Table structure for `crud_field` --
DROP TABLE IF EXISTS `crud_field`;
CREATE TABLE `crud_field` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `crud_id` int NOT NULL,
  `field_name` varchar(200) NOT NULL,
  `field_label` varchar(200) DEFAULT NULL,
  `input_type` varchar(200) NOT NULL,
  `show_column` varchar(10) DEFAULT NULL,
  `show_add_form` varchar(10) DEFAULT NULL,
  `show_update_form` varchar(10) DEFAULT NULL,
  `show_detail_page` varchar(10) DEFAULT NULL,
  `sort` int NOT NULL,
  `relation_table` varchar(200) DEFAULT NULL,
  `relation_value` varchar(200) DEFAULT NULL,
  `relation_label` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1403 DEFAULT CHARSET=utf8mb3;

-- Table structure for `crud_field_validation` --
DROP TABLE IF EXISTS `crud_field_validation`;
CREATE TABLE `crud_field_validation` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `crud_field_id` int NOT NULL,
  `crud_id` int NOT NULL,
  `validation_name` varchar(200) NOT NULL,
  `validation_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1297 DEFAULT CHARSET=utf8mb3;

-- Table structure for `crud_input_type` --
DROP TABLE IF EXISTS `crud_input_type`;
CREATE TABLE `crud_input_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(200) NOT NULL,
  `relation` varchar(20) NOT NULL,
  `custom_value` int NOT NULL,
  `validation_group` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb3;

-- Table structure for `crud_input_validation` --
DROP TABLE IF EXISTS `crud_input_validation`;
CREATE TABLE `crud_input_validation` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `validation` varchar(200) NOT NULL,
  `input_able` varchar(20) NOT NULL,
  `group_input` text NOT NULL,
  `input_placeholder` text NOT NULL,
  `call_back` varchar(10) NOT NULL,
  `input_validation` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb3;

-- Table structure for `districts` --
DROP TABLE IF EXISTS `districts`;
CREATE TABLE `districts` (
  `id` char(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `regency_id` char(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `districts_id_index` (`regency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Table structure for `form` --
DROP TABLE IF EXISTS `form`;
CREATE TABLE `form` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `table_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `form_custom_attribute` --
DROP TABLE IF EXISTS `form_custom_attribute`;
CREATE TABLE `form_custom_attribute` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `form_field_id` int NOT NULL,
  `form_id` int NOT NULL,
  `attribute_value` text NOT NULL,
  `attribute_label` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `form_custom_option` --
DROP TABLE IF EXISTS `form_custom_option`;
CREATE TABLE `form_custom_option` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `form_field_id` int NOT NULL,
  `form_id` int NOT NULL,
  `option_value` text NOT NULL,
  `option_label` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `form_field` --
DROP TABLE IF EXISTS `form_field`;
CREATE TABLE `form_field` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int NOT NULL,
  `sort` int NOT NULL,
  `field_name` varchar(200) NOT NULL,
  `input_type` varchar(200) NOT NULL,
  `field_label` varchar(200) DEFAULT NULL,
  `placeholder` text,
  `auto_generate_help_block` varchar(10) DEFAULT NULL,
  `help_block` text,
  `relation_table` varchar(200) DEFAULT NULL,
  `relation_value` varchar(200) DEFAULT NULL,
  `relation_label` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `form_field_validation` --
DROP TABLE IF EXISTS `form_field_validation`;
CREATE TABLE `form_field_validation` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `form_field_id` int NOT NULL,
  `form_id` int NOT NULL,
  `validation_name` varchar(200) NOT NULL,
  `validation_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `gudang` --
DROP TABLE IF EXISTS `gudang`;
CREATE TABLE `gudang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_gudang` varchar(20) NOT NULL,
  `nama_gudang` varchar(100) NOT NULL,
  `alamat` text,
  `pic_name` varchar(100) DEFAULT NULL,
  `telepon` varchar(30) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_gudang` (`kode_gudang`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Table structure for `harga_pelanggan` --
DROP TABLE IF EXISTS `harga_pelanggan`;
CREATE TABLE `harga_pelanggan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produk_id` int NOT NULL,
  `tipe_pelanggan` enum('retail','grosir','distributor','vip') NOT NULL,
  `min_qty` int DEFAULT '1',
  `harga` decimal(18,2) NOT NULL,
  `diskon_persen` decimal(5,2) DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `invoice` --
DROP TABLE IF EXISTS `invoice`;
CREATE TABLE `invoice` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_invoice` varchar(30) NOT NULL,
  `so_id` int DEFAULT NULL,
  `pelanggan_id` int NOT NULL,
  `gudang_id` int DEFAULT NULL,
  `tanggal_invoice` date NOT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `subtotal` decimal(18,2) DEFAULT '0.00',
  `diskon_total` decimal(18,2) DEFAULT '0.00',
  `pajak_persen` decimal(5,2) DEFAULT '0.00',
  `pajak_nominal` decimal(18,2) DEFAULT '0.00',
  `total` decimal(18,2) DEFAULT '0.00',
  `status` enum('draft','sent','partial','paid','overdue','cancelled') DEFAULT 'draft',
  `status_kirim` enum('pending','processing','shipped','delivered') DEFAULT 'pending',
  `nomor_resi` varchar(100) DEFAULT NULL,
  `ekspedisi` varchar(100) DEFAULT NULL,
  `catatan` text,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_invoice` (`nomor_invoice`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Table structure for `invoice_item` --
DROP TABLE IF EXISTS `invoice_item`;
CREATE TABLE `invoice_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `so_item_id` int DEFAULT NULL,
  `qty` decimal(18,3) NOT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `harga_satuan` decimal(18,2) DEFAULT '0.00',
  `diskon_persen` decimal(5,2) DEFAULT '0.00',
  `diskon_nominal` decimal(18,2) DEFAULT '0.00',
  `subtotal` decimal(18,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `kabupaten` --
DROP TABLE IF EXISTS `kabupaten`;
CREATE TABLE `kabupaten` (
  `kabupatenId` int NOT NULL AUTO_INCREMENT,
  `nama_kabupaten` varchar(50) NOT NULL,
  `provinsiId` int NOT NULL,
  `updatedby` int DEFAULT NULL,
  `updatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` int DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`kabupatenId`),
  KEY `provinsiId` (`provinsiId`)
) ENGINE=InnoDB AUTO_INCREMENT=516 DEFAULT CHARSET=latin1;

-- Table structure for `kategori_produk` --
DROP TABLE IF EXISTS `kategori_produk`;
CREATE TABLE `kategori_produk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Table structure for `keys` --
DROP TABLE IF EXISTS `keys`;
CREATE TABLE `keys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `key` varchar(40) NOT NULL,
  `level` int NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL,
  `is_private_key` tinyint(1) NOT NULL,
  `ip_addresses` text,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Table structure for `kurs` --
DROP TABLE IF EXISTS `kurs`;
CREATE TABLE `kurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mata_uang` varchar(10) NOT NULL,
  `nilai_kurs` decimal(18,4) NOT NULL,
  `tanggal` date NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `language` --
DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bahasa` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Table structure for `menu` --
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(200) DEFAULT NULL,
  `type` varchar(200) DEFAULT NULL,
  `icon_color` varchar(200) DEFAULT NULL,
  `link` varchar(200) DEFAULT NULL,
  `sort` int NOT NULL,
  `parent` int NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `menu_type_id` int NOT NULL,
  `active` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb3;

-- Table structure for `menu_type` --
DROP TABLE IF EXISTS `menu_type`;
CREATE TABLE `menu_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `definition` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Table structure for `migrations` --
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `version` bigint NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `page` --
DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `fresh_content` text NOT NULL,
  `keyword` text,
  `description` text,
  `link` varchar(200) DEFAULT NULL,
  `template` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `page_block_element` --
DROP TABLE IF EXISTS `page_block_element`;
CREATE TABLE `page_block_element` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `image_preview` varchar(200) NOT NULL,
  `block_name` varchar(200) NOT NULL,
  `content_type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `pelanggan` --
DROP TABLE IF EXISTS `pelanggan`;
CREATE TABLE `pelanggan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_pelanggan` varchar(20) NOT NULL,
  `nama_pelanggan` varchar(150) NOT NULL,
  `tipe_pelanggan` enum('retail','grosir','distributor','vip') DEFAULT 'retail',
  `kontak_person` varchar(100) DEFAULT NULL,
  `telepon` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text,
  `kota` varchar(100) DEFAULT NULL,
  `npwp` varchar(30) DEFAULT NULL,
  `kredit_poin` decimal(18,2) DEFAULT '0.00',
  `limit_kredit` decimal(18,2) DEFAULT '0.00',
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `catatan` text,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_pelanggan` (`kode_pelanggan`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Table structure for `penerimaan_barang` --
DROP TABLE IF EXISTS `penerimaan_barang`;
CREATE TABLE `penerimaan_barang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_penerimaan` varchar(30) NOT NULL,
  `po_id` int DEFAULT NULL,
  `supplier_id` int NOT NULL,
  `gudang_id` int NOT NULL,
  `tanggal_terima` date NOT NULL,
  `nomor_surat_jalan` varchar(50) DEFAULT NULL,
  `status` enum('draft','confirmed','diterima','cancelled') DEFAULT 'draft',
  `catatan` text,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_penerimaan` (`nomor_penerimaan`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Table structure for `penerimaan_barang_item` --
DROP TABLE IF EXISTS `penerimaan_barang_item`;
CREATE TABLE `penerimaan_barang_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `penerimaan_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `po_item_id` int DEFAULT NULL,
  `qty_terima` decimal(18,3) NOT NULL,
  `qty_baik` decimal(18,3) DEFAULT '0.000',
  `qty_rusak` decimal(18,3) DEFAULT '0.000',
  `satuan` varchar(20) DEFAULT NULL,
  `harga_satuan` decimal(18,2) DEFAULT '0.00',
  `batch_no` varchar(50) DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `kondisi` enum('baik','rusak','sebagian_rusak') DEFAULT 'baik',
  `catatan` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `piutang_usaha` --
DROP TABLE IF EXISTS `piutang_usaha`;
CREATE TABLE `piutang_usaha` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_piutang` varchar(30) NOT NULL,
  `pelanggan_id` int NOT NULL,
  `invoice_id` int DEFAULT NULL,
  `total` decimal(18,2) NOT NULL,
  `terbayar` decimal(18,2) DEFAULT '0.00',
  `sisa` decimal(18,2) NOT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `status` enum('belum_bayar','sebagian','lunas','macet') DEFAULT 'belum_bayar',
  `catatan` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_piutang` (`nomor_piutang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `produk` --
DROP TABLE IF EXISTS `produk`;
CREATE TABLE `produk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_produk` varchar(30) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `nama_produk` varchar(200) NOT NULL,
  `kategori_id` int DEFAULT NULL,
  `satuan_id` int DEFAULT NULL,
  `harga_beli` decimal(18,2) DEFAULT '0.00',
  `harga_jual` decimal(18,2) DEFAULT '0.00',
  `stok_minimum` int DEFAULT '0',
  `berat` decimal(10,3) DEFAULT NULL COMMENT 'kg',
  `deskripsi` text,
  `gambar` varchar(200) DEFAULT NULL,
  `metode_stok` enum('FIFO','FEFO','AVERAGE') DEFAULT 'FIFO',
  `batch_tracking` tinyint(1) DEFAULT '0',
  `tanggal_kadaluarsa` tinyint(1) DEFAULT '0',
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_produk` (`kode_produk`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Table structure for `purchase_order` --
DROP TABLE IF EXISTS `purchase_order`;
CREATE TABLE `purchase_order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_po` varchar(30) NOT NULL,
  `supplier_id` int NOT NULL,
  `gudang_id` int DEFAULT NULL,
  `tanggal_po` date NOT NULL,
  `tanggal_estimasi` date DEFAULT NULL,
  `mata_uang` varchar(10) DEFAULT 'IDR',
  `kurs` decimal(18,4) DEFAULT '1.0000',
  `subtotal` decimal(18,2) DEFAULT '0.00',
  `diskon` decimal(18,2) DEFAULT '0.00',
  `pajak_persen` decimal(5,2) DEFAULT '0.00',
  `pajak_nominal` decimal(18,2) DEFAULT '0.00',
  `total` decimal(18,2) DEFAULT '0.00',
  `status` enum('draft','submitted','approved','rejected','received','partial','cancelled') DEFAULT 'draft',
  `approval_level` tinyint(1) DEFAULT '0',
  `catatan` text,
  `created_by` varchar(100) DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_po` (`nomor_po`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Table structure for `purchase_order_item` --
DROP TABLE IF EXISTS `purchase_order_item`;
CREATE TABLE `purchase_order_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `po_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `qty_pesan` decimal(18,3) NOT NULL,
  `qty_terima` decimal(18,3) DEFAULT '0.000',
  `satuan` varchar(20) DEFAULT NULL,
  `harga_satuan` decimal(18,2) DEFAULT '0.00',
  `diskon_persen` decimal(5,2) DEFAULT '0.00',
  `diskon_nominal` decimal(18,2) DEFAULT '0.00',
  `subtotal` decimal(18,2) DEFAULT '0.00',
  `catatan` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Table structure for `rak` --
DROP TABLE IF EXISTS `rak`;
CREATE TABLE `rak` (
  `id` int NOT NULL AUTO_INCREMENT,
  `gudang_id` int NOT NULL,
  `kode_rak` varchar(20) NOT NULL,
  `baris` varchar(10) DEFAULT NULL,
  `deskripsi` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `rest` --
DROP TABLE IF EXISTS `rest`;
CREATE TABLE `rest` (
  `id` int unsigned NOT NULL,
  `subject` varchar(200) NOT NULL,
  `table_name` varchar(200) NOT NULL,
  `primary_key` varchar(200) NOT NULL,
  `x_api_key` varchar(20) DEFAULT NULL,
  `x_token` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Table structure for `rest_field` --
DROP TABLE IF EXISTS `rest_field`;
CREATE TABLE `rest_field` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `rest_id` int NOT NULL,
  `field_name` varchar(200) NOT NULL,
  `field_label` varchar(200) DEFAULT NULL,
  `input_type` varchar(200) NOT NULL,
  `show_column` varchar(10) DEFAULT NULL,
  `show_add_api` varchar(10) DEFAULT NULL,
  `show_update_api` varchar(10) DEFAULT NULL,
  `show_detail_api` varchar(10) DEFAULT NULL,
  `relation_table` varchar(200) DEFAULT NULL,
  `relation_value` varchar(200) DEFAULT NULL,
  `relation_label` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

-- Table structure for `rest_field_validation` --
DROP TABLE IF EXISTS `rest_field_validation`;
CREATE TABLE `rest_field_validation` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `rest_field_id` int NOT NULL,
  `rest_id` int NOT NULL,
  `validation_name` varchar(200) NOT NULL,
  `validation_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- Table structure for `rest_input_type` --
DROP TABLE IF EXISTS `rest_input_type`;
CREATE TABLE `rest_input_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(200) NOT NULL,
  `relation` varchar(20) NOT NULL,
  `validation_group` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Table structure for `retur_pembelian` --
DROP TABLE IF EXISTS `retur_pembelian`;
CREATE TABLE `retur_pembelian` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_retur` varchar(30) NOT NULL,
  `penerimaan_id` int DEFAULT NULL,
  `supplier_id` int NOT NULL,
  `gudang_id` int NOT NULL,
  `tanggal_retur` date NOT NULL,
  `alasan` text NOT NULL,
  `total` decimal(18,2) DEFAULT '0.00',
  `status` enum('draft','confirmed','cancelled') DEFAULT 'draft',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_retur` (`nomor_retur`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Table structure for `retur_pembelian_item` --
DROP TABLE IF EXISTS `retur_pembelian_item`;
CREATE TABLE `retur_pembelian_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `retur_pembelian_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `qty_retur` decimal(18,3) NOT NULL,
  `harga_satuan` decimal(18,2) DEFAULT '0.00',
  `subtotal` decimal(18,2) DEFAULT '0.00',
  `alasan` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `retur_penjualan` --
DROP TABLE IF EXISTS `retur_penjualan`;
CREATE TABLE `retur_penjualan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_retur` varchar(30) NOT NULL,
  `invoice_id` int DEFAULT NULL,
  `pelanggan_id` int NOT NULL,
  `gudang_id` int DEFAULT NULL,
  `tanggal_retur` date NOT NULL,
  `alasan` text NOT NULL,
  `tindak_lanjut` enum('tukar_barang','refund','kredit_poin') DEFAULT 'tukar_barang',
  `total` decimal(18,2) DEFAULT '0.00',
  `status` enum('draft','approved','selesai','cancelled') DEFAULT 'draft',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_retur` (`nomor_retur`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Table structure for `retur_penjualan_item` --
DROP TABLE IF EXISTS `retur_penjualan_item`;
CREATE TABLE `retur_penjualan_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `retur_penjualan_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `qty_retur` decimal(18,3) NOT NULL,
  `harga_satuan` decimal(18,2) DEFAULT '0.00',
  `subtotal` decimal(18,2) DEFAULT '0.00',
  `kondisi` enum('baik','rusak') DEFAULT 'baik',
  `alasan` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `sales_order` --
DROP TABLE IF EXISTS `sales_order`;
CREATE TABLE `sales_order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_so` varchar(30) NOT NULL,
  `pelanggan_id` int NOT NULL,
  `gudang_id` int DEFAULT NULL,
  `tanggal_so` date NOT NULL,
  `tanggal_estimasi_kirim` date DEFAULT NULL,
  `subtotal` decimal(18,2) DEFAULT '0.00',
  `diskon_total` decimal(18,2) DEFAULT '0.00',
  `pajak_persen` decimal(5,2) DEFAULT '0.00',
  `pajak_nominal` decimal(18,2) DEFAULT '0.00',
  `total` decimal(18,2) DEFAULT '0.00',
  `status` enum('draft','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'draft',
  `catatan` text,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_so` (`nomor_so`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Table structure for `sales_order_item` --
DROP TABLE IF EXISTS `sales_order_item`;
CREATE TABLE `sales_order_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `so_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `qty` decimal(18,3) NOT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `harga_satuan` decimal(18,2) DEFAULT '0.00',
  `diskon_persen` decimal(5,2) DEFAULT '0.00',
  `diskon_nominal` decimal(18,2) DEFAULT '0.00',
  `subtotal` decimal(18,2) DEFAULT '0.00',
  `catatan` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `satuan` --
DROP TABLE IF EXISTS `satuan`;
CREATE TABLE `satuan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_satuan` varchar(50) NOT NULL,
  `singkatan` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

-- Table structure for `stock_opname` --
DROP TABLE IF EXISTS `stock_opname`;
CREATE TABLE `stock_opname` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_opname` varchar(30) NOT NULL,
  `gudang_id` int NOT NULL,
  `tanggal_opname` date NOT NULL,
  `jadwal` enum('harian','mingguan','bulanan','insidental') DEFAULT 'bulanan',
  `status` enum('draft','process','review','approved','cancelled') DEFAULT 'draft',
  `total_selisih_nilai` decimal(18,2) DEFAULT '0.00',
  `catatan` text,
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_opname` (`nomor_opname`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Table structure for `stock_opname_item` --
DROP TABLE IF EXISTS `stock_opname_item`;
CREATE TABLE `stock_opname_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `opname_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `rak_id` int DEFAULT NULL,
  `stok_sistem` decimal(18,3) DEFAULT '0.000',
  `stok_fisik` decimal(18,3) DEFAULT '0.000',
  `selisih` decimal(18,3) DEFAULT '0.000',
  `harga_satuan` decimal(18,2) DEFAULT '0.00',
  `nilai_selisih` decimal(18,2) DEFAULT '0.00',
  `alasan_selisih` varchar(300) DEFAULT NULL,
  `status_sesuai` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `stok` --
DROP TABLE IF EXISTS `stok`;
CREATE TABLE `stok` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produk_id` int NOT NULL,
  `gudang_id` int NOT NULL,
  `qty` decimal(18,3) DEFAULT '0.000',
  `qty_rusak` decimal(18,3) DEFAULT '0.000',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produk_gudang` (`produk_id`,`gudang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Table structure for `stok_log` --
DROP TABLE IF EXISTS `stok_log`;
CREATE TABLE `stok_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produk_id` int NOT NULL,
  `gudang_id` int NOT NULL,
  `batch_no` varchar(50) DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `jenis` enum('masuk','keluar','penyesuaian','transfer_masuk','transfer_keluar') NOT NULL,
  `qty` decimal(18,3) NOT NULL,
  `harga_satuan` decimal(18,2) DEFAULT '0.00',
  `referensi_type` varchar(50) DEFAULT NULL COMMENT 'purchase_order/sales_order/retur/opname/transfer',
  `referensi_id` int DEFAULT NULL,
  `keterangan` varchar(300) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `supplier` --
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_supplier` varchar(20) NOT NULL,
  `nama_supplier` varchar(150) NOT NULL,
  `kontak_person` varchar(100) DEFAULT NULL,
  `telepon` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text,
  `kota` varchar(100) DEFAULT NULL,
  `negara` varchar(100) DEFAULT 'Indonesia',
  `mata_uang` varchar(10) DEFAULT 'IDR',
  `kurs_manual` decimal(18,4) DEFAULT '1.0000',
  `npwp` varchar(30) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `bank_holder` varchar(100) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `catatan` text,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_supplier` (`kode_supplier`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Table structure for `transfer_stok` --
DROP TABLE IF EXISTS `transfer_stok`;
CREATE TABLE `transfer_stok` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_transfer` varchar(30) NOT NULL,
  `gudang_asal_id` int NOT NULL,
  `gudang_tujuan_id` int NOT NULL,
  `tanggal_transfer` date NOT NULL,
  `tanggal_estimasi_tiba` date DEFAULT NULL,
  `status` enum('draft','submitted','approved','shipped','received','cancelled') DEFAULT 'draft',
  `nomor_bukti_kirim` varchar(50) DEFAULT NULL,
  `catatan` text,
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `received_by` varchar(100) DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_transfer` (`nomor_transfer`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Table structure for `transfer_stok_item` --
DROP TABLE IF EXISTS `transfer_stok_item`;
CREATE TABLE `transfer_stok_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transfer_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `rak_asal_id` int DEFAULT NULL,
  `rak_tujuan_id` int DEFAULT NULL,
  `qty` decimal(18,3) NOT NULL,
  `batch_no` varchar(50) DEFAULT NULL,
  `catatan` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `utang_usaha` --
DROP TABLE IF EXISTS `utang_usaha`;
CREATE TABLE `utang_usaha` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_utang` varchar(30) NOT NULL,
  `supplier_id` int NOT NULL,
  `penerimaan_id` int DEFAULT NULL,
  `total` decimal(18,2) NOT NULL,
  `terbayar` decimal(18,2) DEFAULT '0.00',
  `sisa` decimal(18,2) NOT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `status` enum('belum_bayar','sebagian','lunas') DEFAULT 'belum_bayar',
  `catatan` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_utang` (`nomor_utang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table structure for `village` --
DROP TABLE IF EXISTS `village`;
CREATE TABLE `village` (
  `villageId` int NOT NULL AUTO_INCREMENT,
  `nama_village` varchar(50) NOT NULL,
  `provinsiId` int NOT NULL,
  `updatedby` int DEFAULT NULL,
  `updatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` int DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`villageId`),
  KEY `provinsiId` (`provinsiId`)
) ENGINE=InnoDB AUTO_INCREMENT=518 DEFAULT CHARSET=latin1;

-- Table structure for `villages` --
DROP TABLE IF EXISTS `villages`;
CREATE TABLE `villages` (
  `id` char(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `district_id` char(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `villages_district_id_index` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
