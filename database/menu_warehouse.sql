-- ==============================================================
-- Menu Modul Penjualan & Warehouse
-- Jalankan script ini untuk menambahkan menu navigasi sidebar
-- ==============================================================

-- Ambil max sort saat ini (biasanya 15), kita mulai dari 20

-- =====================
-- MASTER DATA (parent)
-- =====================
INSERT INTO `menu` (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`) VALUES
('Master Data', 'item', NULL, '', 20, 0, 'fa fa-database', 1, 1);

-- Simpan ID parent master data (asumsi auto_increment melanjutkan)
SET @master_id = LAST_INSERT_ID();

INSERT INTO `menu` (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`) VALUES
('Supplier',   'item', NULL, '{admin_url}/supplier',  21, @master_id, 'fa fa-truck',      1, 1),
('Pelanggan',  'item', NULL, '{admin_url}/pelanggan', 22, @master_id, 'fa fa-users',      1, 1),
('Produk',     'item', NULL, '{admin_url}/produk',    23, @master_id, 'fa fa-cube',       1, 1),
('Gudang',     'item', NULL, '{admin_url}/gudang',    24, @master_id, 'fa fa-building',   1, 1);

-- =====================
-- PEMBELIAN (parent)
-- =====================
INSERT INTO `menu` (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`) VALUES
('Pembelian', 'item', NULL, '', 30, 0, 'fa fa-shopping-cart', 1, 1);

SET @beli_id = LAST_INSERT_ID();

INSERT INTO `menu` (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`) VALUES
('Purchase Order',    'item', NULL, '{admin_url}/purchase_order',   31, @beli_id, 'fa fa-file-text',    1, 1),
('Penerimaan Barang', 'item', NULL, '{admin_url}/penerimaan_barang',32, @beli_id, 'fa fa-truck',        1, 1),
('Retur Pembelian',   'item', NULL, '{admin_url}/retur_pembelian',  33, @beli_id, 'fa fa-reply',        1, 1);

-- =====================
-- PENJUALAN (parent)
-- =====================
INSERT INTO `menu` (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`) VALUES
('Penjualan', 'item', NULL, '', 40, 0, 'fa fa-line-chart', 1, 1);

SET @jual_id = LAST_INSERT_ID();

INSERT INTO `menu` (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`) VALUES
('Sales Order',    'item', NULL, '{admin_url}/sales_order',     41, @jual_id, 'fa fa-file-text-o', 1, 1),
('Invoice',        'item', NULL, '{admin_url}/invoice',         42, @jual_id, 'fa fa-file-text',   1, 1),
('Retur Penjualan','item', NULL, '{admin_url}/retur_penjualan', 43, @jual_id, 'fa fa-reply-all',   1, 1);

-- =====================
-- WAREHOUSE (parent)
-- =====================
INSERT INTO `menu` (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`) VALUES
('Warehouse', 'item', NULL, '', 50, 0, 'fa fa-cubes', 1, 1);

SET @wh_id = LAST_INSERT_ID();

INSERT INTO `menu` (`label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`) VALUES
('Dashboard Stok',  'item', NULL, '{admin_url}/stok',           51, @wh_id, 'fa fa-database',  1, 1),
('Stock Opname',    'item', NULL, '{admin_url}/stock_opname',   52, @wh_id, 'fa fa-clipboard', 1, 1),
('Transfer Stok',   'item', NULL, '{admin_url}/transfer_stok',  53, @wh_id, 'fa fa-exchange',  1, 1);

-- Verifikasi menu yang ditambahkan
SELECT id, label, link, parent, sort FROM menu ORDER BY sort;
