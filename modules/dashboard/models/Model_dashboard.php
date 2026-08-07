<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_dashboard extends CI_Model {

	/**
	 * Get produk dengan stok di bawah minimum
	 */
	public function get_low_stocks($limit = 10)
	{
		return $this->db->query("
			SELECT p.id, p.kode_produk, p.nama_produk, p.stok_minimum,
			       COALESCE(SUM(s.qty), 0) AS total_stok
			FROM produk p
			LEFT JOIN stok s ON s.produk_id = p.id
			WHERE p.status = 'aktif'
			GROUP BY p.id, p.kode_produk, p.nama_produk, p.stok_minimum
			HAVING total_stok <= p.stok_minimum
			ORDER BY total_stok ASC
			LIMIT ?
		", [$limit])->result();
	}

	/**
	 * Get summary metrics for dashboard
	 */
	public function get_summary()
	{
		return [
			'total_so'      => $this->db->count_all_results('sales_order'),
			'total_po'      => $this->db->count_all_results('purchase_order'),
			'total_produk'  => $this->db->where('status', 'aktif')->count_all_results('produk'),
			'total_gudang'  => $this->db->where('status', 'aktif')->count_all_results('gudang'),
		];
	}

	/**
	 * Get real-time stock and flow metrics
	 */
	public function get_realtime_stats()
	{
		$total_stok = $this->db->query("SELECT COALESCE(SUM(qty), 0) as total FROM stok")->row()->total;
		
		$masuk_hari_ini = $this->db->query("
			SELECT COALESCE(SUM(pbi.qty_terima), 0) as total 
			FROM penerimaan_barang_item pbi 
			JOIN penerimaan_barang pb ON pb.id = pbi.penerimaan_id 
			WHERE pb.status = 'confirmed' AND pb.tanggal_terima = CURDATE()
		")->row()->total;

		$keluar_hari_ini = $this->db->query("
			SELECT COALESCE(SUM(soi.qty), 0) as total 
			FROM sales_order_item soi 
			JOIN sales_order so ON so.id = soi.so_id 
			WHERE so.status NOT IN ('draft','cancelled') AND so.tanggal_so = CURDATE()
		")->row()->total;

		return [
			'total_stok'      => $total_stok,
			'masuk_hari_ini'  => $masuk_hari_ini,
			'keluar_hari_ini' => $keluar_hari_ini
		];
	}

	/**
	 * Get real-time recent retur penjualan for notification widget
	 */
	public function get_recent_returs($limit = 5)
	{
		return $this->db->select('retur_penjualan.*, pelanggan.nama_pelanggan, gudang.nama_gudang')
			->join('pelanggan', 'pelanggan.id = retur_penjualan.pelanggan_id', 'LEFT')
			->join('gudang', 'gudang.id = retur_penjualan.gudang_id', 'LEFT')
			->order_by('retur_penjualan.id', 'DESC')
			->limit($limit)
			->get('retur_penjualan')->result();
	}

}

/* End of file Model_dashboard.php */
/* Location: ./modules/dashboard/models/Model_dashboard.php */
