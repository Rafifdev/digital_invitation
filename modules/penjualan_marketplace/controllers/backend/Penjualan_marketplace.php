<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan_marketplace extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Model_penjualan_marketplace');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('sales_order_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['sos']       = $this->Model_penjualan_marketplace->get($filter, $field, $this->limit_page, $offset);
		$this->data['so_counts'] = $this->Model_penjualan_marketplace->count_all($filter, $field);
		$this->data['stats']     = $this->Model_penjualan_marketplace->get_stats();
		$this->data['pelanggans']= $this->db->get('pelanggan')->result();
		$this->data['gudangs']   = $this->db->get('gudang')->result();

		$config = [
			'base_url'    => 'administrator/penjualan_marketplace/index/',
			'total_rows'  => $this->data['so_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Penjualan Register All (Marketplace)');
		$this->render('backend/standart/administrator/penjualan_marketplace/penjualan_marketplace_list', $this->data);
	}

	public function view($id)
	{
		$this->is_allowed('sales_order_view');
		$this->data['so']    = $this->Model_penjualan_marketplace->find_with_detail($id);
		$this->data['items'] = $this->Model_penjualan_marketplace->get_items($id);
		$this->template->title('Detail Pesanan Online Shop Marketplace');
		$this->render('backend/standart/administrator/penjualan_marketplace/penjualan_marketplace_view', $this->data);
	}

	public function download_template_shopee()
	{
		$headers = [
			"No. Pesanan",
			"Status Pesanan",
			"Alasan Pembatalan (tambahan kolom)",
			"Status Pembatalan/ Pengembalian",
			"No. Resi",
			"Opsi Pengiriman",
			"Antar ke counter/ pick-up",
			"Pesanan Harus Dikirimkan Sebelum (Menghindari keterlambatan)",
			"Waktu Pengiriman Diatur",
			"Waktu Pesanan Dibuat",
			"Waktu Pembayaran Dilakukan",
			"SKU Induk",
			"Nama Produk",
			"Nomor Referensi SKU",
			"Nama Variasi",
			"Harga Awal",
			"Harga Setelah Diskon",
			"Jumlah",
			"Total Harga Produk",
			"Total Diskon",
			"Diskon Dari Penjual",
			"Diskon Dari Shopee",
			"Berat Produk",
			"Jumlah Produk di Pesan",
			"Total Berat",
			"Voucher Ditanggung Penjual",
			"Cashback Koin",
			"Voucher Ditanggung Shopee",
			"Paket Diskon",
			"Paket Diskon (Diskon dari Shopee)",
			"Paket Diskon (Diskon dari Penjual)",
			"Potongan Koin Shopee",
			"Diskon Kartu Kredit",
			"Ongkos Kirim Dibayar oleh Pembeli",
			"Total Pembayaran",
			"Perkiraan Ongkos Kirim",
			"Catatan dari Pembeli",
			"Catatan",
			"Username (Pembeli)",
			"Nama Penerima",
			"No. Telepon",
			"Alamat Pengiriman",
			"Kota/Kabupaten",
			"Provinsi",
			"Waktu Pesanan Selesai"
		];

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=template_shoppe_18_08_2026.csv');
		$output = fopen('php://output', 'w');
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
		fputcsv($output, $headers);
		fclose($output);
		exit;
	}

	public function download_template_tokopedia()
	{
		$headers = [
			"No. Pesanan", "Status Pesanan", "No. Resi", "Kurir",
			"Waktu Pembayaran", "SKU Induk", "Nama Produk", "Nama Variasi",
			"Harga Satuan", "Jumlah", "Total Harga Produk", "Total Pembayaran",
			"Catatan Pembeli", "Username Pembeli", "Nama Penerima", "No. Telepon",
			"Alamat Pengiriman", "Kota/Kabupaten", "Provinsi"
		];

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=template_import_tokopedia_sales.csv');
		$output = fopen('php://output', 'w');
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
		fputcsv($output, $headers);
		fclose($output);
		exit;
	}

	public function import_shopee()
	{
		$this->is_allowed('sales_order_add');

		if (empty($_FILES['file_csv']['name'])) {
			set_message('Silakan pilih file CSV/Excel Shopee yang akan diimport.', 'error');
			redirect('administrator/penjualan_marketplace');
			return;
		}

		$file_path = $_FILES['file_csv']['tmp_name'];
		$handle = fopen($file_path, 'r');
		if (!$handle) {
			set_message('Gagal membaca file CSV.', 'error');
			redirect('administrator/penjualan_marketplace');
			return;
		}

		$bom = fread($handle, 3);
		if ($bom !== "\xEF\xBB\xBF") {
			rewind($handle);
		}

		$header = fgetcsv($handle, 4000, ",");
		if (!$header) {
			set_message('File CSV kosong atau format tidak sesuai.', 'error');
			redirect('administrator/penjualan_marketplace');
			return;
		}

		$header = array_map(function($h) {
			return trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $h));
		}, $header);

		$get_val = function($data, $keys) {
			foreach ($keys as $k) {
				if (isset($data[$k]) && $data[$k] !== '') return trim($data[$k]);
				$k_norm = strtolower(preg_replace('/[^a-z0-9]/', '', $k));
				foreach ($data as $d_key => $d_val) {
					if (strtolower(preg_replace('/[^a-z0-9]/', '', $d_key)) === $k_norm) {
						if ($d_val !== '') return trim($d_val);
					}
				}
			}
			return '';
		};

		$default_pelanggan = $this->db->get_where('pelanggan', ['kode_pelanggan' => 'CUS-MARKETPLACE'])->row() 
			?: $this->db->get('pelanggan')->row();
		$default_gudang = $this->db->get_where('gudang', ['status' => 'aktif'])->row();
		$pelanggan_id = $default_pelanggan ? $default_pelanggan->id : 1;
		$gudang_id = $default_gudang ? $default_gudang->id : 1;

		$success_count = 0;
		$skipped_count = 0;

		while (($row = fgetcsv($handle, 4000, ",")) !== FALSE) {
			if (count($row) < 5 || empty(array_filter($row))) continue;
			$data = array_combine(array_slice($header, 0, count($row)), $row);
			if (!$data) continue;

			$no_pesanan = $get_val($data, ['No. Pesanan', 'No Pesanan', 'Nomor Pesanan']);
			if (empty($no_pesanan)) continue;

			$existing = $this->db->get_where('penjualan_marketplace', ['no_pesanan' => $no_pesanan])->row();
			if ($existing) {
				$skipped_count++;
				continue;
			}

			$status_pesanan = $get_val($data, ['Status Pesanan']);
			$raw_status = strtolower($status_pesanan);
			$status = 'draft';
			if (strpos($raw_status, 'selesai') !== false || strpos($raw_status, 'completed') !== false) {
				$status = 'delivered';
			} elseif (strpos($raw_status, 'batal') !== false || strpos($raw_status, 'cancelled') !== false) {
				$status = 'cancelled';
			} elseif (strpos($raw_status, 'perlu dikirim') !== false || strpos($raw_status, 'dikirim') !== false) {
				$status = 'shipped';
			}

			$tot_bayar_raw = $get_val($data, ['Total Pembayaran', 'Total Harga Produk']);
			$total_bayar = (float) str_replace(['.', ',', 'Rp', ' '], '', $tot_bayar_raw);
			$nomor_so = $this->Model_penjualan_marketplace->generate_nomor();

			$waktu_bayar_raw = $get_val($data, ['Waktu Pembayaran Dilakukan', 'Waktu Pembayaran', 'Waktu Pesanan Dibuat']);
			$waktu_bayar = !empty($waktu_bayar_raw) ? date('Y-m-d H:i:s', strtotime($waktu_bayar_raw)) : date('Y-m-d H:i:s');
			$tanggal_so = !empty($waktu_bayar_raw) ? date('Y-m-d', strtotime($waktu_bayar_raw)) : date('Y-m-d');

			$username_buyer = $get_val($data, ['Username (Pembeli)', 'Username Pembeli', 'Username']);
			$nama_penerima  = $get_val($data, ['Nama Penerima']);
			$no_telp       = $get_val($data, ['No. Telepon', 'No Telepon', 'Notelp']);
			$alamat        = $get_val($data, ['Alamat Pengiriman']);
			$kota          = $get_val($data, ['Kota/Kabupaten', 'Kota']);
			$provinsi      = $get_val($data, ['Provinsi']);
			$sku_induk     = $get_val($data, ['SKU Induk']);
			$nama_produk   = $get_val($data, ['Nama Produk']);
			$sku_ref       = $get_val($data, ['Nomor Referensi SKU']);
			$variasi       = $get_val($data, ['Nama Variasi']);
			$no_resi       = $get_val($data, ['No. Resi', 'No Resi']);
			$opsi_kirim    = $get_val($data, ['Opsi Pengiriman']);
			$alasan_batal  = $get_val($data, ['Alasan Pembatalan (tambahan kolom)', 'Alasan Pembatalan']);
			$st_batal      = $get_val($data, ['Status Pembatalan/ Pengembalian', 'Status Pembatalan/Pengembalian']);

			$so_data = [
				'nomor_so'                    => $nomor_so,
				'no_pesanan'                  => $no_pesanan,
				'source'                      => 'shopee',
				'pelanggan_id'                => $pelanggan_id,
				'gudang_id'                   => $gudang_id,
				'tanggal_so'                  => $tanggal_so,
				'status'                      => $status,
				'status_pesanan'              => $status_pesanan,
				'alasan_pembatalan'           => $alasan_batal,
				'status_pembatalan_pengembalian' => $st_batal,
				'nomor_resi'                  => $no_resi,
				'kurir_ekspedisi'             => $opsi_kirim,
				'opsi_pengiriman'             => $opsi_kirim,
				'waktu_pembayaran'            => $waktu_bayar,
				'username_pembeli'            => $username_buyer,
				'nama_penerima'               => $nama_penerima,
				'no_telepon'                  => $no_telp,
				'alamat_pengiriman'           => $alamat,
				'kota_kabupaten'              => $kota,
				'provinsi'                    => $provinsi,
				'sku_induk'                   => $sku_induk,
				'nama_produk_marketplace'     => $nama_produk,
				'nomor_referensi_sku'         => $sku_ref,
				'nama_variasi'                => $variasi,
				'subtotal'                    => $total_bayar,
				'total'                       => $total_bayar,
				'catatan'                     => 'Import Shopee CSV - Buyer: ' . $username_buyer,
				'created_by'                  => get_user_data('username'),
				'created_at'                  => date('Y-m-d H:i:s'),
			];

			$so_id = $this->Model_penjualan_marketplace->store($so_data);

			if ($so_id) {
				$sku = trim($sku_ref ?: $sku_induk);
				$prod = null;

				if ($sku) {
					$prod = $this->db->get_where('produk', ['kode_produk' => $sku])->row();
				}
				if (!$prod && $nama_produk) {
					$prod = $this->db->like('nama_produk', $nama_produk)->get('produk')->row();
				}
				if (!$prod) {
					$prod = $this->db->get('produk')->row();
				}

				$qty_raw = $get_val($data, ['Jumlah', 'Jumlah Produk di Pesan']);
				$qty = (float) str_replace(['.', ','], '', $qty_raw ?: 1);

				$harga_raw = $get_val($data, ['Harga Setelah Diskon', 'Harga Awal']);
				$harga = (float) str_replace(['.', ',', 'Rp', ' '], '', $harga_raw);
				if ($harga <= 0 && $qty > 0) {
					$harga = $total_bayar / $qty;
				}

				$this->db->insert('penjualan_marketplace_item', [
					'penjualan_marketplace_id' => $so_id,
					'produk_id'     => $prod ? $prod->id : 1,
					'qty'           => max(1, $qty),
					'satuan'        => 'pcs',
					'harga_satuan'  => $harga,
					'diskon_persen' => 0,
					'diskon_nominal'=> 0,
					'subtotal'      => $harga * $qty,
					'catatan'       => $nama_produk,
				]);

				$success_count++;
			}
		}

		fclose($handle);

		set_message("Berhasil mengimport {$success_count} pesanan Shopee. ({$skipped_count} pesanan dilewati karena duplikat).", 'success');
		redirect('administrator/penjualan_marketplace');
	}

	public function import_tokopedia()
	{
		$this->is_allowed('sales_order_add');

		if (empty($_FILES['file_csv']['name'])) {
			set_message('Silakan pilih file CSV/Excel Tokopedia yang akan diimport.', 'error');
			redirect('administrator/penjualan_marketplace');
			return;
		}

		$file_path = $_FILES['file_csv']['tmp_name'];
		$handle = fopen($file_path, 'r');
		if (!$handle) {
			set_message('Gagal membaca file CSV.', 'error');
			redirect('administrator/penjualan_marketplace');
			return;
		}

		$bom = fread($handle, 3);
		if ($bom !== "\xEF\xBB\xBF") {
			rewind($handle);
		}

		$header = fgetcsv($handle, 4000, ",");
		if (!$header) {
			set_message('File CSV kosong atau format tidak sesuai.', 'error');
			redirect('administrator/penjualan_marketplace');
			return;
		}

		$header = array_map(function($h) {
			return trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $h));
		}, $header);

		$default_pelanggan = $this->db->get_where('pelanggan', ['kode_pelanggan' => 'CUS-MARKETPLACE'])->row() 
			?: $this->db->get('pelanggan')->row();
		$default_gudang = $this->db->get_where('gudang', ['status' => 'aktif'])->row();
		$pelanggan_id = $default_pelanggan ? $default_pelanggan->id : 1;
		$gudang_id = $default_gudang ? $default_gudang->id : 1;

		$success_count = 0;
		$skipped_count = 0;

		while (($row = fgetcsv($handle, 4000, ",")) !== FALSE) {
			if (count($row) < 4 || empty(array_filter($row))) continue;
			$data = array_combine(array_slice($header, 0, count($row)), $row);
			if (!$data) continue;

			$no_pesanan = trim($data['No. Pesanan'] ?? '');
			if (empty($no_pesanan)) continue;

			$existing = $this->db->get_where('penjualan_marketplace', ['no_pesanan' => $no_pesanan])->row();
			if ($existing) {
				$skipped_count++;
				continue;
			}

			$raw_status = strtolower(trim($data['Status Pesanan'] ?? ''));
			$status = 'draft';
			if (strpos($raw_status, 'selesai') !== false) {
				$status = 'delivered';
			} elseif (strpos($raw_status, 'batal') !== false) {
				$status = 'cancelled';
			} elseif (strpos($raw_status, 'dikirim') !== false) {
				$status = 'shipped';
			}

			$total_bayar = (float) str_replace(['.', ',', 'Rp', ' '], '', $data['Total Pembayaran'] ?? $data['Total Harga Produk'] ?? 0);
			$nomor_so = $this->Model_penjualan_marketplace->generate_nomor();

			$waktu_bayar = !empty($data['Waktu Pembayaran']) ? date('Y-m-d H:i:s', strtotime($data['Waktu Pembayaran'])) : date('Y-m-d H:i:s');
			$tanggal_so = !empty($data['Waktu Pembayaran']) ? date('Y-m-d', strtotime($data['Waktu Pembayaran'])) : date('Y-m-d');

			$so_data = [
				'nomor_so'            => $nomor_so,
				'no_pesanan'          => $no_pesanan,
				'source'              => 'tokopedia',
				'pelanggan_id'        => $pelanggan_id,
				'gudang_id'           => $gudang_id,
				'tanggal_so'          => $tanggal_so,
				'status'              => $status,
				'status_pesanan'      => $data['Status Pesanan'] ?? '',
				'nomor_resi'          => $data['No. Resi'] ?? '',
				'kurir_ekspedisi'     => $data['Kurir'] ?? '',
				'waktu_pembayaran'    => $waktu_bayar,
				'username_pembeli'    => $data['Username Pembeli'] ?? '',
				'nama_penerima'       => $data['Nama Penerima'] ?? '',
				'no_telepon'          => $data['No. Telepon'] ?? '',
				'alamat_pengiriman'   => $data['Alamat Pengiriman'] ?? '',
				'kota_kabupaten'      => $data['Kota/Kabupaten'] ?? '',
				'provinsi'            => $data['Provinsi'] ?? '',
				'sku_induk'           => $data['SKU Induk'] ?? '',
				'nama_produk_marketplace' => $data['Nama Produk'] ?? '',
				'nama_variasi'        => $data['Nama Variasi'] ?? '',
				'subtotal'            => $total_bayar,
				'total'               => $total_bayar,
				'catatan'             => 'Import Tokopedia CSV - Buyer: ' . ($data['Username Pembeli'] ?? ''),
				'created_by'          => get_user_data('username'),
				'created_at'          => date('Y-m-d H:i:s'),
			];

			$so_id = $this->Model_penjualan_marketplace->store($so_data);

			if ($so_id) {
				$sku = trim($data['SKU Induk'] ?? '');
				$nama_prod = trim($data['Nama Produk'] ?? '');
				$prod = null;

				if ($sku) {
					$prod = $this->db->get_where('produk', ['kode_produk' => $sku])->row();
				}
				if (!$prod && $nama_prod) {
					$prod = $this->db->like('nama_produk', $nama_prod)->get('produk')->row();
				}
				if (!$prod) {
					$prod = $this->db->get('produk')->row();
				}

				$qty = (float) str_replace(['.', ','], '', $data['Jumlah'] ?? 1);
				$harga = (float) str_replace(['.', ',', 'Rp', ' '], '', $data['Harga Satuan'] ?? 0);
				if ($harga <= 0 && $qty > 0) {
					$harga = $total_bayar / $qty;
				}

				$this->db->insert('penjualan_marketplace_item', [
					'penjualan_marketplace_id' => $so_id,
					'produk_id'     => $prod ? $prod->id : 1,
					'qty'           => max(1, $qty),
					'satuan'        => 'pcs',
					'harga_satuan'  => $harga,
					'diskon_persen' => 0,
					'diskon_nominal'=> 0,
					'subtotal'      => $harga * $qty,
					'catatan'       => $nama_prod,
				]);

				$success_count++;
			}
		}

		fclose($handle);

		set_message("Berhasil mengimport {$success_count} pesanan Tokopedia. ({$skipped_count} pesanan dilewati karena duplikat).", 'success');
		redirect('administrator/penjualan_marketplace');
	}

	public function export()
	{
		$this->is_allowed('sales_order_export');
		$this->Model_penjualan_marketplace->export('penjualan_marketplace', 'penjualan_marketplace');
	}

	public function export_pdf()
	{
		$this->is_allowed('sales_order_export');
		$this->Model_penjualan_marketplace->pdf('penjualan_marketplace', 'penjualan_marketplace');
	}
}

/* End of file Penjualan_marketplace.php */
/* Location: ./modules/penjualan_marketplace/controllers/backend/Penjualan_marketplace.php */
