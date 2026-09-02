<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_buku_besar extends MY_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function get_ledger_entries($date_start = '', $date_end = '', $category = '', $search = '', $limit = 0, $offset = 0)
	{
		$query = $this->build_union_query($date_start, $date_end, $category, $search);
		if ($limit > 0) {
			$query .= " LIMIT " . (int)$offset . ", " . (int)$limit;
		}
		return $this->db->query($query)->result();
	}

	public function count_ledger_entries($date_start = '', $date_end = '', $category = '', $search = '')
	{
		$query = "SELECT COUNT(*) as total FROM (" . $this->build_union_query($date_start, $date_end, $category, $search, false) . ") as combined";
		$row = $this->db->query($query)->row();
		return $row ? (int)$row->total : 0;
	}

	public function get_summary_kpi($date_start = '', $date_end = '', $category = '', $search = '')
	{
		$query = "SELECT 
					SUM(debit) as total_debit, 
					SUM(kredit) as total_kredit,
					COUNT(*) as total_transaksi
				  FROM (" . $this->build_union_query($date_start, $date_end, $category, $search, false) . ") as combined";
		$row = $this->db->query($query)->row();
		
		$debit  = $row ? (float)$row->total_debit : 0;
		$kredit = $row ? (float)$row->total_kredit : 0;
		$total  = $row ? (int)$row->total_transaksi : 0;

		return [
			'total_debit'     => $debit,
			'total_kredit'    => $kredit,
			'net_balance'     => $debit - $kredit,
			'total_transaksi' => $total
		];
	}

	private function build_union_query($date_start = '', $date_end = '', $category = '', $search = '', $include_order = true)
	{
		$queries = [];

		// 1. Penjualan (Invoice)
		$queries[] = "SELECT 
						inv.tanggal_invoice as tanggal,
						inv.nomor_invoice as nomor_ref,
						'penjualan' as modul_code,
						'Penjualan (Invoice)' as modul_label,
						COALESCE(p.nama_pelanggan, '-') as entitas,
						CONCAT('Tagihan Penjualan Barang / Invoice (Status: ', UPPER(inv.status), ')') as deskripsi,
						inv.total as debit,
						0 as kredit,
						inv.created_at
					  FROM invoice inv
					  LEFT JOIN pelanggan p ON p.id = inv.pelanggan_id";

		// 2. Pembelian (Purchase Order)
		$queries[] = "SELECT 
						po.tanggal_po as tanggal,
						po.nomor_po as nomor_ref,
						'pembelian' as modul_code,
						'Purchase Order (PO)' as modul_label,
						COALESCE(s.nama_supplier, '-') as entitas,
						CONCAT('Pemesanan Pembelian ke Supplier (Status: ', UPPER(po.status), ')') as deskripsi,
						0 as debit,
						po.total as kredit,
						po.created_at
					  FROM purchase_order po
					  LEFT JOIN supplier s ON s.id = po.supplier_id";

		// 3. Penerimaan Barang (GRN)
		$queries[] = "SELECT 
						pb.tanggal_terima as tanggal,
						pb.nomor_penerimaan as nomor_ref,
						'pembelian' as modul_code,
						'Penerimaan Barang (GRN)' as modul_label,
						COALESCE(s.nama_supplier, '-') as entitas,
						CONCAT('Penerimaan Fisik Barang dari Supplier (Surat Jalan: ', COALESCE(pb.nomor_surat_jalan, '-'), ')') as deskripsi,
						0 as debit,
						0 as kredit,
						pb.created_at
					  FROM penerimaan_barang pb
					  LEFT JOIN supplier s ON s.id = pb.supplier_id";

		// 4. Utang Usaha (AP) - Kewajiban
		$queries[] = "SELECT 
						DATE(u.created_at) as tanggal,
						u.nomor_utang as nomor_ref,
						'utang' as modul_code,
						'Utang Usaha (AP)' as modul_label,
						COALESCE(s.nama_supplier, '-') as entitas,
						CONCAT('Kewajiban Utang Pembelian (Jatuh Tempo: ', COALESCE(u.tanggal_jatuh_tempo, '-'), ')') as deskripsi,
						0 as debit,
						u.total as kredit,
						u.created_at
					  FROM utang_usaha u
					  LEFT JOIN supplier s ON s.id = u.supplier_id";

		// 5. Pengeluaran Kas (BKK) - Pelunasan Utang
		$queries[] = "SELECT 
						bkk.tanggal_bayar as tanggal,
						bkk.nomor_bkk as nomor_ref,
						'kas_keluar' as modul_code,
						'Pengeluaran Kas (BKK)' as modul_label,
						COALESCE(s.nama_supplier, '-') as entitas,
						CONCAT('Pengeluaran Kas / Bank untuk Pelunasan Utang (Metode: ', bkk.metode_pembayaran, ' - ', bkk.akun_kas_bank, ')') as deskripsi,
						0 as debit,
						bkk.nominal as kredit,
						bkk.created_at
					  FROM pembayaran_utang bkk
					  LEFT JOIN supplier s ON s.id = bkk.supplier_id";

		// 6. Piutang Usaha (AR) - Hak Tagih
		$queries[] = "SELECT 
						DATE(piu.created_at) as tanggal,
						piu.nomor_piutang as nomor_ref,
						'piutang' as modul_code,
						'Piutang Usaha (AR)' as modul_label,
						COALESCE(p.nama_pelanggan, '-') as entitas,
						CONCAT('Hak Tagih Piutang Penjualan (Jatuh Tempo: ', COALESCE(piu.tanggal_jatuh_tempo, '-'), ')') as deskripsi,
						piu.total as debit,
						0 as kredit,
						piu.created_at
					  FROM piutang_usaha piu
					  LEFT JOIN pelanggan p ON p.id = piu.pelanggan_id";

		// 7. Penerimaan Kas (BKM) - Pembayaran Piutang
		$queries[] = "SELECT 
						bkm.tanggal_bayar as tanggal,
						bkm.nomor_bkm as nomor_ref,
						'kas_masuk' as modul_code,
						'Penerimaan Kas (BKM)' as modul_label,
						COALESCE(p.nama_pelanggan, '-') as entitas,
						CONCAT('Penerimaan Kas / Bank dari Pelanggan (Metode: ', bkm.metode_pembayaran, ' - ', bkm.akun_kas_bank, ')') as deskripsi,
						bkm.nominal as debit,
						0 as kredit,
						bkm.created_at
					  FROM pembayaran_piutang bkm
					  LEFT JOIN pelanggan p ON p.id = bkm.pelanggan_id";

		// 8. Retur Pembelian
		$queries[] = "SELECT 
						rp.tanggal_retur as tanggal,
						rp.nomor_retur as nomor_ref,
						'retur' as modul_code,
						'Retur Pembelian' as modul_label,
						COALESCE(s.nama_supplier, '-') as entitas,
						CONCAT('Pengembalian Barang ke Supplier (Alasan: ', UPPER(rp.alasan), ')') as deskripsi,
						rp.total as debit,
						0 as kredit,
						rp.created_at
					  FROM retur_pembelian rp
					  LEFT JOIN supplier s ON s.id = rp.supplier_id";

		// 9. Retur Penjualan
		$queries[] = "SELECT 
						rj.tanggal_retur as tanggal,
						rj.nomor_retur as nomor_ref,
						'retur' as modul_code,
						'Retur Penjualan' as modul_label,
						COALESCE(p.nama_pelanggan, '-') as entitas,
						CONCAT('Penerimaan Retur dari Pelanggan (Alasan: ', UPPER(rj.alasan), ')') as deskripsi,
						0 as debit,
						rj.total as kredit,
						rj.created_at
					  FROM retur_penjualan rj
					  LEFT JOIN pelanggan p ON p.id = rj.pelanggan_id";

		// 10. Stock Opname (Warehouse)
		$queries[] = "SELECT 
						op.tanggal_opname as tanggal,
						op.nomor_opname as nomor_ref,
						'warehouse' as modul_code,
						'Stock Opname' as modul_label,
						COALESCE(g.nama_gudang, '-') as entitas,
						CONCAT('Penghitungan Stok Fisik Gudang (Jadwal: ', UPPER(op.jadwal), ')') as deskripsi,
						0 as debit,
						0 as kredit,
						op.created_at
					  FROM stock_opname op
					  LEFT JOIN gudang g ON g.id = op.gudang_id";

		// 11. Transfer Stok (Warehouse)
		$queries[] = "SELECT 
						tr.tanggal_transfer as tanggal,
						tr.nomor_transfer as nomor_ref,
						'warehouse' as modul_code,
						'Transfer Stok' as modul_label,
						CONCAT(COALESCE(ga.nama_gudang, '-'), ' -> ', COALESCE(gt.nama_gudang, '-')) as entitas,
						CONCAT('Mutasi Stok Antar Gudang (No Resi: ', COALESCE(tr.nomor_bukti_kirim, '-'), ')') as deskripsi,
						0 as debit,
						0 as kredit,
						tr.created_at
					  FROM transfer_stok tr
					  LEFT JOIN gudang ga ON ga.id = tr.gudang_asal_id
					  LEFT JOIN gudang gt ON gt.id = tr.gudang_tujuan_id";

		$full_union = implode(" UNION ALL ", $queries);

		// Apply Outer Filters
		$where_clauses = [];
		if (!empty($date_start)) {
			$where_clauses[] = "tanggal >= " . $this->db->escape($date_start);
		}
		if (!empty($date_end)) {
			$where_clauses[] = "tanggal <= " . $this->db->escape($date_end);
		}
		if (!empty($category)) {
			$where_clauses[] = "modul_code = " . $this->db->escape($category);
		}
		if (!empty($search)) {
			$s = $this->db->escape_like_str($search);
			$where_clauses[] = "(nomor_ref LIKE '%{$s}%' OR entitas LIKE '%{$s}%' OR deskripsi LIKE '%{$s}%')";
		}

		$sql = "SELECT * FROM ({$full_union}) as master_ledger";
		if (!empty($where_clauses)) {
			$sql .= " WHERE " . implode(" AND ", $where_clauses);
		}

		if ($include_order) {
			$sql .= " ORDER BY tanggal DESC, created_at DESC";
		}

		return $sql;
	}
}

/* End of file Model_buku_besar.php */
/* Location: ./modules/buku_besar/models/Model_buku_besar.php */
