-- ==============================================================
-- Permissions untuk semua modul baru
-- ==============================================================

INSERT INTO `aauth_perms` (`name`, `definition`) VALUES
-- Supplier
('supplier_list',    'Lihat daftar supplier'),
('supplier_view',    'Lihat detail supplier'),
('supplier_add',     'Tambah supplier'),
('supplier_update',  'Edit supplier'),
('supplier_delete',  'Hapus supplier'),
('supplier_export',  'Export supplier'),

-- Pelanggan
('pelanggan_list',   'Lihat daftar pelanggan'),
('pelanggan_view',   'Lihat detail pelanggan'),
('pelanggan_add',    'Tambah pelanggan'),
('pelanggan_update', 'Edit pelanggan'),
('pelanggan_delete', 'Hapus pelanggan'),
('pelanggan_export', 'Export pelanggan'),

-- Produk
('produk_list',      'Lihat daftar produk'),
('produk_view',      'Lihat detail produk'),
('produk_add',       'Tambah produk'),
('produk_update',    'Edit produk'),
('produk_delete',    'Hapus produk'),
('produk_export',    'Export produk'),

-- Gudang
('gudang_list',      'Lihat daftar gudang'),
('gudang_view',      'Lihat detail gudang'),
('gudang_add',       'Tambah gudang'),
('gudang_update',    'Edit gudang'),
('gudang_delete',    'Hapus gudang'),
('gudang_export',    'Export gudang'),

-- Purchase Order
('purchase_order_list',    'Lihat daftar PO'),
('purchase_order_view',    'Lihat detail PO'),
('purchase_order_add',     'Buat PO'),
('purchase_order_update',  'Edit PO'),
('purchase_order_approve', 'Approve/tolak PO'),
('purchase_order_delete',  'Hapus PO'),
('purchase_order_export',  'Export PO'),

-- Penerimaan Barang
('penerimaan_barang_list',   'Lihat daftar penerimaan'),
('penerimaan_barang_view',   'Lihat detail penerimaan'),
('penerimaan_barang_add',    'Buat penerimaan'),
('penerimaan_barang_update', 'Konfirmasi penerimaan'),
('penerimaan_barang_delete', 'Hapus penerimaan'),

-- Retur Pembelian
('retur_pembelian_list',   'Lihat daftar retur pembelian'),
('retur_pembelian_view',   'Lihat detail retur pembelian'),
('retur_pembelian_add',    'Buat retur pembelian'),
('retur_pembelian_update', 'Konfirmasi retur pembelian'),
('retur_pembelian_delete', 'Hapus retur pembelian'),

-- Sales Order
('sales_order_list',   'Lihat daftar SO'),
('sales_order_view',   'Lihat detail SO'),
('sales_order_add',    'Buat SO'),
('sales_order_update', 'Konfirmasi SO'),
('sales_order_delete', 'Hapus SO'),
('sales_order_export', 'Export SO'),

-- Invoice
('invoice_list',   'Lihat daftar invoice'),
('invoice_view',   'Lihat detail invoice'),
('invoice_add',    'Buat invoice'),
('invoice_update', 'Kirim invoice'),
('invoice_delete', 'Hapus invoice'),
('invoice_export', 'Export invoice'),

-- Retur Penjualan
('retur_penjualan_list',   'Lihat daftar retur penjualan'),
('retur_penjualan_view',   'Lihat detail retur penjualan'),
('retur_penjualan_add',    'Buat retur penjualan'),
('retur_penjualan_update', 'Proses retur penjualan'),
('retur_penjualan_delete', 'Hapus retur penjualan'),

-- Stock Opname
('stock_opname_list',    'Lihat daftar opname'),
('stock_opname_view',    'Lihat detail opname'),
('stock_opname_add',     'Buat opname'),
('stock_opname_update',  'Input fisik opname'),
('stock_opname_approve', 'Approve opname'),
('stock_opname_delete',  'Hapus opname'),

-- Transfer Stok
('transfer_stok_list',    'Lihat daftar transfer'),
('transfer_stok_view',    'Lihat detail transfer'),
('transfer_stok_add',     'Buat transfer'),
('transfer_stok_update',  'Submit/kirim/terima transfer'),
('transfer_stok_approve', 'Approve transfer'),
('transfer_stok_delete',  'Hapus transfer'),

-- Stok
('stok_list',   'Lihat dashboard stok'),
('stok_export', 'Export data stok');

-- Assign semua permissions ke grup Admin (group_id=1 biasanya admin)
-- Cek dulu id group admin
-- Tambahkan semua permission baru ke admin group (id=1)
INSERT INTO `aauth_perm_to_group` (`perm_id`, `group_id`)
SELECT p.id, 1
FROM aauth_perms p
WHERE p.name IN (
  'supplier_list','supplier_view','supplier_add','supplier_update','supplier_delete','supplier_export',
  'pelanggan_list','pelanggan_view','pelanggan_add','pelanggan_update','pelanggan_delete','pelanggan_export',
  'produk_list','produk_view','produk_add','produk_update','produk_delete','produk_export',
  'gudang_list','gudang_view','gudang_add','gudang_update','gudang_delete','gudang_export',
  'purchase_order_list','purchase_order_view','purchase_order_add','purchase_order_update','purchase_order_approve','purchase_order_delete','purchase_order_export',
  'penerimaan_barang_list','penerimaan_barang_view','penerimaan_barang_add','penerimaan_barang_update','penerimaan_barang_delete',
  'retur_pembelian_list','retur_pembelian_view','retur_pembelian_add','retur_pembelian_update','retur_pembelian_delete',
  'sales_order_list','sales_order_view','sales_order_add','sales_order_update','sales_order_delete','sales_order_export',
  'invoice_list','invoice_view','invoice_add','invoice_update','invoice_delete','invoice_export',
  'retur_penjualan_list','retur_penjualan_view','retur_penjualan_add','retur_penjualan_update','retur_penjualan_delete',
  'stock_opname_list','stock_opname_view','stock_opname_add','stock_opname_update','stock_opname_approve','stock_opname_delete',
  'transfer_stok_list','transfer_stok_view','transfer_stok_add','transfer_stok_update','transfer_stok_approve','transfer_stok_delete',
  'stok_list','stok_export'
)
AND NOT EXISTS (
  SELECT 1 FROM aauth_perm_to_group g2 WHERE g2.perm_id = p.id AND g2.group_id = 1
);

SELECT COUNT(*) as total_permissions_added FROM aauth_perms WHERE name IN ('supplier_list','pelanggan_list','produk_list','gudang_list','purchase_order_list','sales_order_list','invoice_list','stok_list');
