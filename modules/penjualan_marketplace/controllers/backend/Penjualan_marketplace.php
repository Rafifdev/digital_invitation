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

		$per_page = (int) ($this->input->get('per_page') ?: 25);
		if ($per_page <= 0) $per_page = 25;

		$this->data['per_page']  = $per_page;
		$this->data['sos']       = $this->Model_penjualan_marketplace->get($filter, $field, $per_page, $offset);
		$this->data['so_counts'] = $this->Model_penjualan_marketplace->count_all($filter, $field);
		$this->data['stats']     = $this->Model_penjualan_marketplace->get_stats();
		$this->data['pelanggans']= $this->db->get('pelanggan')->result();
		$this->data['gudangs']   = $this->db->get('gudang')->result();

		$config = [
			'base_url'    => 'administrator/penjualan_marketplace/index/',
			'total_rows'  => $this->data['so_counts'],
			'per_page'    => $per_page,
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

	public function cetak_resi($id)
	{
		$this->is_allowed('sales_order_view');
		$this->data['so']    = $this->Model_penjualan_marketplace->find_with_detail($id);
		$this->data['items'] = $this->Model_penjualan_marketplace->get_items($id);
		$this->load->view('backend/standart/administrator/penjualan_marketplace/penjualan_marketplace_resi', $this->data);
	}

	public function add()
	{
		$this->is_allowed('sales_order_add');
		$this->data['gudangs'] = $this->db->get('gudang')->result();
		$this->data['produks'] = $this->db->get('produk')->result();
		$this->template->title('Tambah Penjualan Shopee / Online Shop');
		$this->render('backend/standart/administrator/penjualan_marketplace/penjualan_marketplace_add', $this->data);
	}

	public function add_save()
	{
		$this->is_allowed('sales_order_add');

		$source           = $this->input->post('source') ?: 'shopee';
		$no_pesanan       = trim($this->input->post('no_pesanan'));
		$status           = $this->input->post('status') ?: 'packing';
		$gudang_id        = $this->input->post('gudang_id') ?: 1;
		$opsi_pengiriman  = $this->input->post('opsi_pengiriman') ?: 'SPX Express Standard';
		$nomor_resi       = $this->input->post('nomor_resi') ?: ('SPX' . rand(1000000000, 9999999999));
		$waktu_pembayaran = $this->input->post('waktu_pembayaran') ? date('Y-m-d H:i:s', strtotime($this->input->post('waktu_pembayaran'))) : date('Y-m-d H:i:s');
		
		// Duplicate Check
		if (!empty($no_pesanan)) {
			$existing = $this->db->get_where('penjualan_marketplace', ['no_pesanan' => $no_pesanan])->row();
			if ($existing) {
				set_message("Gagal menyimpan! Nomor Pesanan '{$no_pesanan}' sudah ada di database (Duplikat).", 'error');
				redirect('administrator/penjualan_marketplace/add');
				return;
			}
		}
		
		$username_pembeli  = $this->input->post('username_pembeli');
		$nama_penerima     = $this->input->post('nama_penerima');
		$no_telepon        = $this->input->post('no_telepon');
		$alamat_pengiriman = $this->input->post('alamat_pengiriman');
		$kota_kabupaten    = $this->input->post('kota_kabupaten');
		$provinsi          = $this->input->post('provinsi');
		$catatan           = $this->input->post('catatan');
		$total             = (float) $this->input->post('total');

		$default_pelanggan = $this->db->get_where('pelanggan', ['kode_pelanggan' => 'CUS-MARKETPLACE'])->row() 
			?: $this->db->get('pelanggan')->row();
		$pelanggan_id = $default_pelanggan ? $default_pelanggan->id : 1;

		$nomor_so = $this->Model_penjualan_marketplace->generate_nomor();

		$data_pm = [
			'nomor_so'            => $nomor_so,
			'no_pesanan'          => $no_pesanan,
			'source'              => $source,
			'pelanggan_id'        => $pelanggan_id,
			'gudang_id'           => $gudang_id,
			'tanggal_so'          => date('Y-m-d'),
			'status'              => $status,
			'status_pesanan'      => strtoupper($status),
			'nomor_resi'          => $nomor_resi,
			'kurir_ekspedisi'     => $opsi_pengiriman,
			'opsi_pengiriman'     => $opsi_pengiriman,
			'waktu_pembayaran'    => $waktu_pembayaran,
			'username_pembeli'    => $username_pembeli,
			'nama_penerima'       => $nama_penerima,
			'no_telepon'          => $no_telepon,
			'alamat_pengiriman'   => $alamat_pengiriman,
			'kota_kabupaten'      => $kota_kabupaten,
			'provinsi'            => $provinsi,
			'subtotal'            => $total,
			'total'               => $total,
			'catatan'             => $catatan,
			'created_by'          => get_user_data('username'),
			'created_at'          => date('Y-m-d H:i:s'),
		];

		$pm_id = $this->Model_penjualan_marketplace->store($data_pm);

		if ($pm_id) {
			$produk_ids    = $this->input->post('produk_id');
			$qtys          = $this->input->post('qty');
			$hargas        = $this->input->post('harga_satuan');
			$subtotals     = $this->input->post('subtotal_item');

			if (!empty($produk_ids) && is_array($produk_ids)) {
				foreach ($produk_ids as $idx => $prod_id) {
					if (empty($prod_id)) continue;
					$prod = $this->db->get_where('produk', ['id' => $prod_id])->row();
					$qty = (float) ($qtys[$idx] ?? 1);
					$harga = (float) ($hargas[$idx] ?? 0);
					$subtotal = (float) ($subtotals[$idx] ?? ($qty * $harga));

					$this->db->insert('penjualan_marketplace_item', [
						'penjualan_marketplace_id' => $pm_id,
						'produk_id'     => $prod_id,
						'qty'           => max(1, $qty),
						'satuan'        => 'pcs',
						'harga_satuan'  => $harga,
						'diskon_persen' => 0,
						'diskon_nominal'=> 0,
						'subtotal'      => $subtotal,
						'catatan'       => $prod ? $prod->nama_produk : '',
					]);
				}
			}

			set_message("Berhasil menambahkan pesanan Online Shop baru (#{$no_pesanan})!", 'success');
		} else {
			set_message("Gagal menambahkan pesanan Online Shop baru.", 'error');
		}

		redirect('administrator/penjualan_marketplace');
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

		$header_str = strtolower(implode(' ', $header));
		if (strpos($header_str, 'shopee') === false && strpos($header_str, 'username (pembeli)') === false && strpos($header_str, 'opsi pengiriman') === false) {
			fclose($handle);
			set_message('Gagal Import! File CSV yang diunggah bukan format Shopee. Silakan upload file CSV Shopee atau gunakan tab Import Tokopedia!', 'error');
			redirect('administrator/penjualan_marketplace');
			return;
		}

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

		$post_gudang = $this->input->post('gudang_id');
		$default_gudang = !empty($post_gudang) 
			? $this->db->get_where('gudang', ['id' => $post_gudang])->row() 
			: ($this->db->get_where('gudang', ['status' => 'aktif'])->row() ?: $this->db->get('gudang')->row());
		$pelanggan_id = $default_pelanggan ? $default_pelanggan->id : 1;
		$gudang_id = $default_gudang ? $default_gudang->id : 1;

		$inserted_count = 0;
		$updated_count = 0;
		$skipped_count = 0;

		while (($row = fgetcsv($handle, 4000, ",")) !== FALSE) {
			if (count($row) < 5 || empty(array_filter($row))) continue;
			$data = array_combine(array_slice($header, 0, count($row)), $row);
			if (!$data) continue;

			$no_pesanan = $get_val($data, ['No. Pesanan', 'No Pesanan', 'Nomor Pesanan']);
			if (empty($no_pesanan)) continue;

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

			$no_resi = $get_val($data, ['No. Resi', 'No Resi']);

			$existing = $this->db->get_where('penjualan_marketplace', ['no_pesanan' => $no_pesanan])->row();
			if ($existing) {
				$need_update = false;
				$update_fields = [];

				if (!empty($no_resi) && $existing->nomor_resi !== $no_resi) {
					$update_fields['nomor_resi'] = $no_resi;
					$need_update = true;
				}
				if (!empty($status_pesanan) && $existing->status_pesanan !== $status_pesanan) {
					$update_fields['status_pesanan'] = $status_pesanan;
					$update_fields['status'] = $status;
					$need_update = true;
				}

				if ($need_update) {
					$this->db->where('id', $existing->id)->update('penjualan_marketplace', $update_fields);
					$updated_count++;
				} else {
					$skipped_count++;
				}
				continue;
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

				$inserted_count++;
			}
		}

		fclose($handle);

		$msg = "<b>Ringkasan Import CSV Shopee:</b><br>" .
		       "🆕 <b>{$inserted_count} Pesanan Baru</b> berhasil ditambahkan.<br>" .
		       "🔄 <b>{$updated_count} Pesanan</b> diperbarui (status/resi).<br>" .
		       "⛔ <b>{$skipped_count} Pesanan</b> dilewati/ditolak (duplikat).";

		if ($inserted_count > 0 || $updated_count > 0) {
			set_message($msg, 'success');
		} else {
			set_message($msg, 'error');
		}
		redirect('administrator/penjualan_marketplace?source=shopee');
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

		$header_str = strtolower(implode(' ', $header));
		if (strpos($header_str, 'tokopedia') === false && strpos($header_str, 'username pembeli') === false && strpos($header_str, 'kurir') === false) {
			fclose($handle);
			set_message('Gagal Import! File CSV yang diunggah bukan format Tokopedia. Silakan upload file CSV Tokopedia atau gunakan tab Import Shopee!', 'error');
			redirect('administrator/penjualan_marketplace');
			return;
		}

		$post_gudang = $this->input->post('gudang_id');
		$default_gudang = !empty($post_gudang) 
			? $this->db->get_where('gudang', ['id' => $post_gudang])->row() 
			: ($this->db->get_where('gudang', ['status' => 'aktif'])->row() ?: $this->db->get('gudang')->row());
		$pelanggan_id = $default_pelanggan ? $default_pelanggan->id : 1;
		$gudang_id = $default_gudang ? $default_gudang->id : 1;

		$inserted_count = 0;
		$updated_count = 0;
		$skipped_count = 0;

		while (($row = fgetcsv($handle, 4000, ",")) !== FALSE) {
			if (count($row) < 4 || empty(array_filter($row))) continue;
			$data = array_combine(array_slice($header, 0, count($row)), $row);
			if (!$data) continue;

			$no_pesanan = trim($data['No. Pesanan'] ?? '');
			if (empty($no_pesanan)) continue;

			$raw_status = strtolower(trim($data['Status Pesanan'] ?? ''));
			$status_pesanan = $data['Status Pesanan'] ?? '';
			$status = 'draft';
			if (strpos($raw_status, 'selesai') !== false) {
				$status = 'delivered';
			} elseif (strpos($raw_status, 'batal') !== false) {
				$status = 'cancelled';
			} elseif (strpos($raw_status, 'dikirim') !== false) {
				$status = 'shipped';
			}

			$no_resi = trim($data['No. Resi'] ?? '');

			$existing = $this->db->get_where('penjualan_marketplace', ['no_pesanan' => $no_pesanan])->row();
			if ($existing) {
				$need_update = false;
				$update_fields = [];

				if (!empty($no_resi) && $existing->nomor_resi !== $no_resi) {
					$update_fields['nomor_resi'] = $no_resi;
					$need_update = true;
				}
				if (!empty($status_pesanan) && $existing->status_pesanan !== $status_pesanan) {
					$update_fields['status_pesanan'] = $status_pesanan;
					$update_fields['status'] = $status;
					$need_update = true;
				}

				if ($need_update) {
					$this->db->where('id', $existing->id)->update('penjualan_marketplace', $update_fields);
					$updated_count++;
				} else {
					$skipped_count++;
				}
				continue;
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
				'status_pesanan'      => $status_pesanan,
				'nomor_resi'          => $no_resi,
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

				$inserted_count++;
			}
		}

		fclose($handle);

		$msg = "<b>Ringkasan Import CSV Tokopedia:</b><br>" .
		       "🆕 <b>{$inserted_count} Pesanan Baru</b> berhasil ditambahkan.<br>" .
		       "🔄 <b>{$updated_count} Pesanan</b> diperbarui (status/resi).<br>" .
		       "⛔ <b>{$skipped_count} Pesanan</b> dilewati/ditolak (duplikat).";

		if ($inserted_count > 0 || $updated_count > 0) {
			set_message($msg, 'success');
		} else {
			set_message($msg, 'error');
		}
		redirect('administrator/penjualan_marketplace?source=tokopedia');
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

	public function import_packed()
	{
		$this->is_allowed('sales_order_list');
		$this->template->title('Workstation Scan Barcode & Packing');
		$this->render('backend/standart/administrator/penjualan_marketplace/penjualan_marketplace_import_packed', $this->data);
	}

	public function process_scan_resi()
	{
		$nomor_resi = trim($this->input->post('nomor_resi'));
		$target_status = $this->input->post('target_status') ?: 'packing';

		if (empty($nomor_resi)) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'Nomor Resi / Pesanan tidak boleh kosong!'
			]);
			return;
		}

		// Search order by nomor_resi OR no_pesanan OR nomor_so
		$this->db->group_start();
		$this->db->where('nomor_resi', $nomor_resi);
		$this->db->or_where('no_pesanan', $nomor_resi);
		$this->db->or_where('nomor_so', $nomor_resi);
		$this->db->group_end();
		$so = $this->db->get('penjualan_marketplace')->row();

		if (!$so) {
			echo json_encode([
				'status'  => 'error',
				'message' => "Nomor Resi / Pesanan '{$nomor_resi}' TIDAK DITEMUKAN dalam database!"
			]);
			return;
		}

		$status_db = ($target_status == 'shipped') ? 'shipped' : 'packing';
		$status_label = ($target_status == 'shipped') ? 'SHIPPED' : 'PACKED';

		// Update order status
		$this->db->where('id', $so->id)->update('penjualan_marketplace', [
			'status'         => $status_db,
			'status_pesanan' => $status_label,
			'updated_at'     => date('Y-m-d H:i:s')
		]);

		// Calculate items count & totals
		$items = $this->db->get_where('penjualan_marketplace_item', ['penjualan_marketplace_id' => $so->id])->result();
		$total_qty = 0;
		foreach ($items as $it) {
			$total_qty += (int)$it->qty;
		}
		if ($total_qty <= 0) $total_qty = 1;

		$resi_num = !empty($so->nomor_resi) ? $so->nomor_resi : $nomor_resi;
		$order_num = !empty($so->no_pesanan) ? $so->no_pesanan : $so->nomor_so;
		$penerima = !empty($so->nama_penerima) ? $so->nama_penerima : ($so->nama_pelanggan ?: 'Penerima Paket');

		echo json_encode([
			'status'  => 'success',
			'message' => "Paket '{$resi_num}' Berhasil Discan & Diupdate Ke {$status_label}!",
			'data'    => [
				'id'              => $so->id,
				'nomor_resi'      => $resi_num,
				'no_pesanan'      => $order_num,
				'opsi_pengiriman' => !empty($so->opsi_pengiriman) ? $so->opsi_pengiriman : 'SPX Standard',
				'nama_penerima'   => $penerima,
				'total_qty'       => $total_qty,
				'total'           => (float)($so->total ?: $so->subtotal ?: 0),
				'status_label'    => $status_label,
				'waktu'           => date('H:i:s')
			]
		]);
	}

	public function download_pdf_resi($id = null)
	{
		$this->is_allowed('sales_order_list');
		$id = $id ? $id : $this->input->get('id');
		$so = $this->Model_penjualan_marketplace->find_with_detail($id);
		if (!$so) {
			show_404();
			return;
		}

		$items = $this->Model_penjualan_marketplace->get_items($id);
		$this->data['so'] = $so;
		$this->data['items'] = $items;
		$this->data['auto_download'] = true; // Flag to auto-trigger print/save-as-PDF

		$this->load->view('backend/standart/administrator/penjualan_marketplace/penjualan_marketplace_resi', $this->data);
	}

	public function scan_resi()
	{
		$this->is_allowed('sales_order_list');
		$this->template->title('Workstation Terpadu Scan Resi & Dokumen Thermal');
		$this->render('backend/standart/administrator/penjualan_marketplace/penjualan_marketplace_scan_resi', $this->data);
	}

	private function _extract_text_from_pdf_file($file_path)
	{
		if (!file_exists($file_path)) return [];
		
		$text_by_page = [];

		// Method 1: Use smalot/pdfparser library (best quality)
		try {
			$autoload = FCPATH . 'vendor/autoload.php';
			if (file_exists($autoload)) {
				require_once $autoload;
			}
			if (class_exists('\Smalot\PdfParser\Parser')) {
				$parser = new \Smalot\PdfParser\Parser();
				$pdf = $parser->parseFile($file_path);
				$pages = $pdf->getPages();
				
				foreach ($pages as $page) {
					$page_items = [];
					$text = $page->getText();
					if (!empty($text)) {
						$lines = preg_split('/\r?\n/', $text);
						foreach ($lines as $line) {
							$line = trim($line);
							if ($line !== '') {
								$page_items[] = ['str' => $line];
							}
						}
					}
					$text_by_page[] = $page_items;
				}
				
				// Check if we got any text at all
				$total_items = 0;
				foreach ($text_by_page as $p) { $total_items += count($p); }
				if ($total_items > 0) {
					return $text_by_page;
				}
			}
		} catch (\Exception $e) {
			// Log error but continue to fallback
			@file_put_contents(APPPATH . 'logs/scan_resi_debug.log', 
				"[" . date('Y-m-d H:i:s') . "] smalot/pdfparser error: " . $e->getMessage() . "\n", FILE_APPEND);
		}

		// Method 2: Fallback - raw PDF stream regex extraction
		$content = @file_get_contents($file_path);
		if (empty($content)) return [];

		preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $content, $matches);
		
		if (empty($matches[1])) {
			return [];
		}

		foreach ($matches[1] as $stream_data) {
			$uncompressed = @gzuncompress($stream_data);
			if ($uncompressed === false) {
				$uncompressed = @gzinflate($stream_data);
			}
			if ($uncompressed === false) {
				$uncompressed = $stream_data;
			}

			$page_items = [];
			preg_match_all('/\((.*?)\)\s*Tj/s', $uncompressed, $tj_matches);
			if (!empty($tj_matches[1])) {
				foreach ($tj_matches[1] as $t) {
					$t_clean = str_replace(['\\(', '\\)', '\\\\'], ['(', ')', '\\'], $t);
					$t_clean = trim($t_clean);
					if ($t_clean !== '') {
						$page_items[] = ['str' => $t_clean];
					}
				}
			}

			preg_match_all('/\[\s*(.*?)\s*\]\s*TJ/s', $uncompressed, $tj_arr_matches);
			if (!empty($tj_arr_matches[1])) {
				foreach ($tj_arr_matches[1] as $arr_str) {
					preg_match_all('/\((.*?)\)/s', $arr_str, $str_in_arr);
					if (!empty($str_in_arr[1])) {
						$combined = implode('', array_map(function($s) {
							return str_replace(['\\(', '\\)', '\\\\'], ['(', ')', '\\'], $s);
						}, $str_in_arr[1]));
						$combined = trim($combined);
						if ($combined !== '') {
							$page_items[] = ['str' => $combined];
						}
					}
				}
			}

			if (!empty($page_items)) {
				$text_by_page[] = $page_items;
			}
		}

		return $text_by_page;
	}

	public function scan_resi_ajax()
	{
		$raw_input = trim($this->input->post('nomor_resi'));
		$page_index = (int)$this->input->post('page_index');
		$extracted_text = $this->input->post('extracted_text_items'); // Array or JSON string of text items from PDF.js
		
		if (is_string($extracted_text)) {
			$extracted_text = json_decode($extracted_text, true);
		}
		if (!is_array($extracted_text)) {
			$extracted_text = [];
		}

		// SERVER-SIDE PDF EXTRACTION: Extract text directly from uploaded PDF using smalot/pdfparser
		if (!empty($_FILES['file_pdf']['tmp_name']) && file_exists($_FILES['file_pdf']['tmp_name'])) {
			$pdf_tmp = $_FILES['file_pdf']['tmp_name'];
			$pdf_hash = md5_file($pdf_tmp);
			$cache_file = APPPATH . 'logs/pdf_cache_' . $pdf_hash . '.json';
			
			$pdf_pages = null;
			
			// Check cache first (same PDF already parsed in a previous AJAX call)
			if (file_exists($cache_file) && (time() - filemtime($cache_file)) < 300) {
				$cached = @json_decode(@file_get_contents($cache_file), true);
				if (is_array($cached) && !empty($cached)) {
					$pdf_pages = $cached;
				}
			}
			
			// Parse PDF if not cached
			if ($pdf_pages === null) {
				$pdf_pages = $this->_extract_text_from_pdf_file($pdf_tmp);
				if (!empty($pdf_pages)) {
					@file_put_contents($cache_file, json_encode($pdf_pages));
				}
			}
			
			if (!empty($pdf_pages)) {
				if ($page_index >= 0 && isset($pdf_pages[$page_index])) {
					$extracted_text = array_merge($extracted_text, $pdf_pages[$page_index]);
				} elseif (isset($pdf_pages[0])) {
					$extracted_text = array_merge($extracted_text, $pdf_pages[0]);
				}
			}
		}

		// Debug logging: write extracted text items to log file for diagnostics
		$debug_log = APPPATH . 'logs/scan_resi_debug.log';
		$log_entry = "[" . date('Y-m-d H:i:s') . "] raw_input=" . $raw_input . " | page_index=" . $page_index . " | text_items_count=" . count($extracted_text) . "\n";
		if (!empty($extracted_text)) {
			foreach ($extracted_text as $ti_idx => $ti) {
				$ti_str = is_array($ti) ? ($ti['str'] ?? '') : $ti;
				$log_entry .= "  item[$ti_idx]: " . $ti_str . "\n";
			}
		}
		@file_put_contents($debug_log, $log_entry . "---\n", FILE_APPEND);

		// Comprehensive Multi-Tier PDF Label Parsing
		$parsed_label = $this->_parse_full_pdf_label($extracted_text, $raw_input);

		$nomor_resi = !empty($parsed_label['resi']) ? $parsed_label['resi'] : "";
		$no_pesanan = !empty($parsed_label['pesanan']) ? $parsed_label['pesanan'] : "";

		// 1. Check for cetak_resi/{id} URL pattern extracted from PDF text
		if (empty($nomor_resi) && !empty($extracted_text)) {
			foreach ($extracted_text as $item) {
				$txt = trim(is_array($item) ? ($item['str'] ?? '') : $item);
				if (preg_match('/cetak_resi\/(\d+)/i', $txt, $m)) {
					$order_id = $m[1];
					$so_match = $this->db->get_where('penjualan_marketplace', ['id' => $order_id])->row();
					if ($so_match) {
						$nomor_resi = !empty($so_match->nomor_resi) ? $so_match->nomor_resi : ($so_match->no_pesanan ?: $so_match->nomor_so);
						break;
					}
				}
			}
		}

		// 2. Extract valid resi/order token pattern from raw_input
		// IMPORTANT: Skip filename-based extraction if raw_input looks like a PDF filename (e.g. "resi4.pdf [Hal 1]")
		$is_pdf_page_label = (bool) preg_match('/\.(pdf|PDF)\s*\[/i', $raw_input);
		if (empty($nomor_resi) && !$is_pdf_page_label) {
			$clean_input = trim(preg_replace('/[^A-Za-z0-9]/', '', $raw_input));
			if (preg_match('/(SPXID[0-9A-Z]{7,25}|2[0-9]{5}SHP[A-Z0-9]{4,15}|[0-9]{6}SHP[A-Z0-9]{4,15}|SPX[A-Z0-9]{6,25}|JNE[0-9]{6,25}|JNT[0-9]{6,25}|SIC[A-Z0-9]{6,25}|SHP[0-9]{6,25}|JP[0-9]{9,16}|JX[0-9]{9,16}|IDS[0-9A-Z]{7,20}|IDE[0-9A-Z]{7,20}|NLID[0-9A-Z]{7,20})/i', $raw_input, $m)) {
				$nomor_resi = $m[0];
			} elseif (!empty($clean_input) && !preg_match('/\.(pdf|jpg|png|webp|jpeg)$/i', $raw_input) && strlen($clean_input) >= 6 && !preg_match('/^(resi|label|thermal|halaman|page|file|doc|scan)/i', $clean_input)) {
				$nomor_resi = $clean_input;
			}
		}

		if (empty($nomor_resi) && !empty($no_pesanan)) {
			$nomor_resi = $no_pesanan;
		}

		if (empty($nomor_resi) && !empty($_FILES['file_pdf']['name'])) {
			$fname = $_FILES['file_pdf']['name'];
			if (preg_match('/(2[0-9]{5}SHP[A-Z0-9]{4,15}|[0-9]{6}SHP[A-Z0-9]{4,15}|SPX[A-Z0-9]{6,25}|JNE[0-9]{6,25}|JNT[0-9]{6,25}|SIC[A-Z0-9]{6,25}|SHP[0-9]{6,25})/i', $fname, $m)) {
				$nomor_resi = $m[0];
			}
		}

		// Search existing order in database by resi or order number
		$so = null;
		if (!empty($nomor_resi)) {
			$this->db->group_start();
			$this->db->where('nomor_resi', $nomor_resi);
			$this->db->or_where('no_pesanan', $nomor_resi);
			$this->db->or_where('nomor_so', $nomor_resi);
			$this->db->group_end();
			$so = $this->db->get('penjualan_marketplace')->row();
		}

		if (!$so && !empty($no_pesanan)) {
			$so = $this->db->get_where('penjualan_marketplace', ['no_pesanan' => $no_pesanan])->row();
		}

		// Fallback search using valid resi/order tokens in database
		if (!$so && !empty($extracted_text)) {
			foreach ($extracted_text as $item) {
				$txt = trim(is_array($item) ? ($item['str'] ?? '') : $item);
				if (preg_match('/(2[0-9]{5}SHP[A-Z0-9]{4,15}|[0-9]{6}SHP[A-Z0-9]{4,15}|SPX[A-Z0-9]{6,25}|JNE[0-9]{6,25}|JNT[0-9]{6,25}|SIC[A-Z0-9]{6,25}|SHP[0-9]{6,25}|JP[0-9]{9,16}|JX[0-9]{9,16})/i', $txt, $m)) {
					$clean = $m[0];
					$match_so = $this->db->group_start()
						->where('nomor_resi', $clean)
						->or_where('no_pesanan', $clean)
						->or_where('nomor_so', $clean)
						->group_end()
						->get('penjualan_marketplace')->row();
					if ($match_so) {
						$so = $match_so;
						$nomor_resi = $clean;
						break;
					}
				}
			}
		}

		// IF STILL NOT FOUND IN DATABASE: Create & Store this unique order into database!
		if (!$so) {
			$final_resi = !empty($nomor_resi) ? $nomor_resi : (!empty($parsed_label['resi']) ? $parsed_label['resi'] : 'RESI-' . date('ymdHis') . '-' . rand(100,999));
			$final_pesanan = !empty($no_pesanan) ? $no_pesanan : (!empty($parsed_label['pesanan']) ? $parsed_label['pesanan'] : 'ORD-' . date('ymdHis') . '-' . rand(100,999));
			$extracted_str_all = is_array($extracted_text) ? implode(' ', array_map(function($i){ return is_array($i) ? ($i['str']??'') : $i; }, $extracted_text)) : '';

			$final_courier = $this->_resolve_courier($parsed_label['courier'] ?? '', $final_resi, $extracted_text);

			$final_penerima = !empty($parsed_label['penerima']) ? $parsed_label['penerima'] : '-';
			$final_phone = !empty($parsed_label['phone']) ? $parsed_label['phone'] : '-';
			$final_address = !empty($parsed_label['address']) ? $parsed_label['address'] : '-';
			$final_produk = !empty($parsed_label['produk']) ? $parsed_label['produk'] : '';
			$final_variasi = !empty($parsed_label['variasi']) ? $parsed_label['variasi'] : 'Default';
			$final_qty = !empty($parsed_label['qty']) ? (int)$parsed_label['qty'] : 1;
			$final_harga = !empty($parsed_label['harga']) ? (float)$parsed_label['harga'] : 0;
			$final_total = ($final_harga > 0 && $final_qty > 0) ? ($final_harga * $final_qty) : 0;

			// Debug log parsed values
			@file_put_contents($debug_log, "  PARSED => resi=$final_resi | pesanan=$final_pesanan | courier=$final_courier | penerima=$final_penerima | phone=$final_phone\n", FILE_APPEND);

			$nomor_so = $this->Model_penjualan_marketplace->generate_nomor();
			$default_gudang = $this->db->get_where('gudang', ['status' => 'aktif'])->row() ?: $this->db->get('gudang')->row();

			$new_so_data = [
				'nomor_so'            => $nomor_so,
				'no_pesanan'          => $final_pesanan,
				'source'              => 'pdf_scan',
				'pelanggan_id'        => 1,
				'gudang_id'           => $default_gudang ? $default_gudang->id : 1,
				'tanggal_so'          => date('Y-m-d'),
				'status'              => 'draft',
				'status_pesanan'      => 'Siap Kirim',
				'nomor_resi'          => $final_resi,
				'kurir_ekspedisi'     => $final_courier,
				'opsi_pengiriman'     => $final_courier,
				'waktu_pembayaran'    => date('Y-m-d H:i:s'),
				'username_pembeli'    => strtolower(str_replace(' ', '_', $final_penerima)),
				'nama_penerima'       => $final_penerima,
				'no_telepon'          => $final_phone,
				'alamat_pengiriman'   => $final_address,
				'nama_produk_marketplace' => $final_produk,
				'nama_variasi'        => $final_variasi,
				'subtotal'            => $final_total > 0 ? $final_total : 0,
				'total'               => $final_total > 0 ? $final_total : 0,
				'catatan'             => 'Auto-Created from PDF Scan Label - ' . $final_courier,
				'created_by'          => get_user_data('username') ?: 'administrator',
				'created_at'          => date('Y-m-d H:i:s'),
			];

			$so_id = $this->Model_penjualan_marketplace->store($new_so_data);
			if ($so_id) {
				// Insert product items from parsed label
				$parsed_products = !empty($parsed_label['products']) ? $parsed_label['products'] : [];
				
				if (!empty($parsed_products)) {
					// Insert each parsed product as a separate item
					foreach ($parsed_products as $pp) {
						$pp_name = $pp['name'];
						$pp_qty = max(1, (int)$pp['qty']);
						
						// Try to find matching product in ERP by name (first word match)
						$pp_first_word = strtok($pp_name, ' ');
						$prod = null;
						if (strlen($pp_first_word) >= 3) {
							$prod = $this->db->like('nama_produk', $pp_first_word)->get('produk')->row();
						}
						
						$item_price = $this->_estimate_item_price($pp_name, $final_harga, $prod);
						$this->db->insert('penjualan_marketplace_item', [
							'penjualan_marketplace_id' => $so_id,
							'produk_id'     => $prod ? $prod->id : 0,
							'qty'           => $pp_qty,
							'satuan'        => 'pcs',
							'harga_satuan'  => $item_price,
							'subtotal'      => $item_price * $pp_qty,
							'catatan'       => $pp_name,
						]);
					}
				} else {
					$fallback_price = $this->_estimate_item_price($final_produk, $final_harga, null);
					// Fallback: insert single generic item
					$this->db->insert('penjualan_marketplace_item', [
						'penjualan_marketplace_id' => $so_id,
						'produk_id'     => 0,
						'qty'           => max(1, $final_qty),
						'satuan'        => 'pcs',
						'harga_satuan'  => $fallback_price,
						'subtotal'      => $fallback_price * max(1, $final_qty),
						'catatan'       => !empty($final_produk) ? $final_produk : ('Produk Resi ' . $final_resi),
					]);
				}
				$so = $this->db->get_where('penjualan_marketplace', ['id' => $so_id])->row();
			}
		}

		if (!$so) {
			echo json_encode([
				'status'  => 'error',
				'message' => "Gagal memproses data resi dari dokumen PDF."
			]);
			return;
		}

		// Extract recipient info from PDF
		$unmask_data = $this->_extract_pdf_recipient_info($extracted_text, $so);

		// Update database record with unmasked data
		if ($so && !empty($so->id)) {
			$update_data = [
				'nama_penerima'     => $unmask_data['name'],
				'no_telepon'        => $unmask_data['phone'],
				'alamat_pengiriman' => $unmask_data['address'],
				'updated_at'        => date('Y-m-d H:i:s')
			];
			
			// Update nomor_resi in DB if parsed label extracted a specific resi number (e.g. JNE..., JT..., SPX...) and current DB resi equals order number or is generic
			$new_resi = !empty($parsed_label['resi']) ? $parsed_label['resi'] : (!empty($nomor_resi) ? $nomor_resi : '');
			if (!empty($new_resi) && (empty($so->nomor_resi) || $so->nomor_resi === $so->no_pesanan || strpos($so->nomor_resi, 'RESI-') === 0 || preg_match('/^\d{6}SHP/i', $so->nomor_resi))) {
				$update_data['nomor_resi'] = $new_resi;
				$so->nomor_resi = $new_resi;
			}

			// Also update product name and qty from parsed label if available
			if (!empty($parsed_label['produk'])) {
				$update_data['nama_produk_marketplace'] = $parsed_label['produk'];
			}

			// Update courier using master resolver
			$detect_courier = $this->_resolve_courier($parsed_label['courier'] ?? '', $so->nomor_resi, $extracted_text);
			if (!empty($detect_courier)) {
				$update_data['opsi_pengiriman'] = $detect_courier;
				$update_data['kurir_ekspedisi'] = $detect_courier;
				$so->opsi_pengiriman = $detect_courier;
				$so->kurir_ekspedisi = $detect_courier;
			}
			
			$this->db->where('id', $so->id)->update('penjualan_marketplace', $update_data);
			$so->nama_penerima = $unmask_data['name'];
			$so->no_telepon = $unmask_data['phone'];
			$so->alamat_pengiriman = $unmask_data['address'];
			
			// Update items if parsed label has product data and existing items are incomplete or dummy
			$parsed_products = !empty($parsed_label['products']) ? $parsed_label['products'] : [];
			$existing_items = $this->db->get_where('penjualan_marketplace_item', ['penjualan_marketplace_id' => $so->id])->result();
			$existing_total_qty = 0;
			$has_dummy_item = false;
			foreach ($existing_items as $ei) {
				$existing_total_qty += (int)$ei->qty;
				if (empty($ei->catatan) || $ei->catatan === 'SKU Variasi' || strpos($ei->catatan, 'Produk Resi') !== false) {
					$has_dummy_item = true;
				}
			}
			
			if (!empty($parsed_products) && ($has_dummy_item || $existing_total_qty <= 1 || count($existing_items) < count($parsed_products))) {
				// Delete old items and re-insert from OCR parsed data
				$this->db->where('penjualan_marketplace_id', $so->id)->delete('penjualan_marketplace_item');
				
				foreach ($parsed_products as $pp) {
					$pp_name = $pp['name'];
					$pp_qty = max(1, (int)$pp['qty']);
					
					$prod = null;
					$pp_first_word = strtok($pp_name, ' ');
					if (strlen($pp_first_word) >= 3) {
						$prod = $this->db->like('nama_produk', $pp_first_word)->get('produk')->row();
					}
					$item_price = $this->_estimate_item_price($pp_name, $final_harga, $prod);
					
					$this->db->insert('penjualan_marketplace_item', [
						'penjualan_marketplace_id' => $so->id,
						'produk_id'     => $prod ? $prod->id : 0,
						'qty'           => $pp_qty,
						'satuan'        => 'pcs',
						'harga_satuan'  => $item_price,
						'subtotal'      => $item_price * $pp_qty,
						'catatan'       => $pp_name,
					]);
				}
			}
		}

		// Get Items Breakdown
		$items = $this->db->get_where('penjualan_marketplace_item', ['penjualan_marketplace_id' => $so->id])->result();
		$items_breakdown = [];
		$total_qty = 0;
		$total_price = 0;

		if (!empty($items)) {
			foreach ($items as $it) {
				$prod = null;
				if (!empty($it->produk_id)) {
					$prod = $this->db->get_where('produk', ['id' => $it->produk_id])->row();
				}

				$prod_name = '';
				if ($prod && !empty($prod->nama_produk)) {
					$prod_name = $prod->nama_produk;
				} elseif (!empty($it->catatan)) {
					$prod_name = preg_replace('/\s*\(.*?\)\s*/', '', $it->catatan);
				} else {
					$prod_name = !empty($so->nama_produk_marketplace) ? $so->nama_produk_marketplace : 'Produk Marketplace';
				}

				$item_sku = ($prod && !empty($prod->kode_produk)) ? $prod->kode_produk : (!empty($so->sku_induk) ? $so->sku_induk : '-');

				$qty = (float)($it->qty ?: 1);
				$harga = (float)($it->harga_satuan ?: 0);
				if ($harga <= 0) {
					$harga = $this->_estimate_item_price($prod_name, $final_harga, $prod);
					$this->db->where('id', $it->id)->update('penjualan_marketplace_item', [
						'harga_satuan' => $harga,
						'subtotal'     => $harga * $qty
					]);
				}
				$subtotal = $qty * $harga;

				$total_qty += $qty;
				$total_price += $subtotal;

				$item_variasi = (!empty($it->catatan) && $it->catatan !== $prod_name) ? $it->catatan : ($so->nama_variasi ?: 'Default');

				$items_breakdown[] = [
					'qty'                 => $qty,
					'master_product_name' => $prod_name,
					'sku'                 => $item_sku,
					'variasi'             => $item_variasi,
					'harga_satuan'        => $harga,
					'subtotal'            => $subtotal
				];
			}
		} else {
			$total_qty = 1;
			$total_price = (float)($so->total ?: 0);
			if ($total_price <= 0) $total_price = 150000;
			$items_breakdown[] = [
				'qty'                 => 1,
				'master_product_name' => $so->nama_produk_marketplace ?: 'Produk Online Shop',
				'sku'                 => $so->sku_induk ?: '-',
				'variasi'             => $so->nama_variasi ?: 'Default',
				'harga_satuan'        => $total_price,
				'subtotal'            => $total_price
			];
		}

		// Sync total & subtotal to penjualan_marketplace row in DB
		if ($total_price > 0 && $so && !empty($so->id)) {
			$this->db->where('id', $so->id)->update('penjualan_marketplace', [
				'subtotal' => $total_price,
				'total'    => $total_price
			]);
		}

		$response_courier = !empty($so->opsi_pengiriman) ? $so->opsi_pengiriman : (!empty($parsed_label['courier']) ? $parsed_label['courier'] : (!empty($final_courier) ? $final_courier : 'JNE Express REG'));

		echo json_encode([
			'status'  => 'success',
			'message' => "Pesanan '{$so->no_pesanan}' (Resi: {$so->nomor_resi}) Berhasil Ditemukan & Di-breakdown!",
			'data'    => [
				'id'               => $so->id,
				'no_pesanan'       => $so->no_pesanan ?: $so->nomor_so,
				'nomor_resi'       => $so->nomor_resi ?: ($nomor_resi ?: $parsed_label['resi']),
				'opsi_pengiriman'  => $response_courier,
				'waktu_pembayaran' => $so->waktu_pembayaran ?: $so->created_at,
				'unmasked_name'    => $unmask_data['name'],
				'unmasked_phone'   => $unmask_data['phone'],
				'unmasked_address' => $unmask_data['address'],
				'total_qty'        => $total_qty,
				'total_price'      => $total_price,
				'items'            => $items_breakdown
			]
		]);
	}

	/**
	 * Robust Multi-Format PDF Label Parser
	 * Handles text items from PDF.js which may come as:
	 * - Single text items containing "Label: Value" pairs
	 * - Separate text items where label is in one item and value in the next
	 * - Concatenated full-page text with various delimiters
	 * 
	 * Supports: Shopee (SPX), JNE, J&T, SiCepat, Anteraja, ID Express, Ninja
	 */
	private function _parse_full_pdf_label($extracted_text, $raw_input = '')
	{
		$full_str = "";
		$items_str = [];
		if (is_array($extracted_text)) {
			foreach ($extracted_text as $ti) {
				$s = trim(is_array($ti) ? ($ti['str'] ?? '') : $ti);
				if ($s !== '') {
					$items_str[] = $s;
					$full_str .= " " . $s;
				}
			}
		}

		// Also build newline-separated version for multi-line regex
		$full_lines = implode("\n", $items_str);

		$resi = "";
		$pesanan = "";
		$courier = "";
		$penerima = "";
		$phone = "";
		$address = "";
		$produk = "";
		$variasi = "";
		$qty = 0;

		// ===== 1. RESI EXTRACTION (Multi-Strategy) =====
		$resi_patterns = [
			'/\b(SPXID[0-9A-Z]{7,25})\b/i',
			'/\b(SPX[0-9A-Z]{7,25})\b/i',
			'/\b(00[0-9]{10,14})\b/i', // SiCepat (e.g. 004134580493)
			'/\b(SIC[0-9A-Z]{7,25})\b/i',
			'/\b(JT[0-9]{8,20})\b/i',
			'/\b(JP[0-9]{8,20})\b/i',
			'/\b(JX[0-9]{8,20})\b/i',
			'/\b(JNT[0-9A-Z]{7,25})\b/i',
			'/\b(EZ[0-9A-Z]{7,25})\b/i',
			'/\b(JNE[0-9A-Z]{7,25})\b/i',
			'/\b(CTC[0-9A-Z]{7,25})\b/i',
			'/\b(IDS[0-9A-Z]{7,20})\b/i',
			'/\b(IDE[0-9A-Z]{7,20})\b/i',
			'/\b(NLID[0-9A-Z]{7,20})\b/i',
			'/\b(ANTR[0-9A-Z]{6,20})\b/i',
			'/\b(LP[0-9]{9,20})\b/i',
			'/\b(TKP[0-9A-Z]{7,20})\b/i', // Tokopedia
			'/\b(1000[0-9]{8,14})\b/i',
		];

		foreach ($resi_patterns as $rp) {
			if (preg_match($rp, $full_str, $m)) {
				$resi = $m[1];
				break;
			}
		}

		if (empty($resi)) {
			if (preg_match('/(?:No\.?\s*Resi|Resi|AWB|Tracking\s*(?:No|Number|ID)?)\s*[:\/ ]\s*([A-Za-z0-9\-]{6,30})/i', $full_str, $m)) {
				$resi = trim($m[1]);
			}
		}

		if (empty($resi)) {
			foreach ($items_str as $idx => $str) {
				$str_clean = trim($str);
				if (preg_match('/^(?:No\.?\s*Resi|Resi|AWB|No\.?\s*Resi\s*\/\s*AWB)\s*:?\s*$/i', $str_clean) && isset($items_str[$idx + 1])) {
					$nxt = trim($items_str[$idx + 1]);
					if (strlen($nxt) >= 6 && preg_match('/^[A-Za-z0-9\-]+$/', $nxt) && !preg_match('/^(No\.?|Penerima|Pengirim|Alamat|Halaman|Page|Variasi)/i', $nxt)) {
						$resi = $nxt;
						break;
					}
				}
				if (preg_match('/(?:No\.?\s*Resi|Resi|AWB)\s*[:\/ ]\s*([A-Za-z0-9\-]{6,30})/i', $str_clean, $m)) {
					$resi = trim($m[1]);
					break;
				}
			}
		}

		if (empty($resi)) {
			foreach ($items_str as $str) {
				$str_clean = trim($str);
				if (preg_match('/^([A-Z]{2,5}[0-9]{8,20})$/i', $str_clean, $m)) {
					$resi = $m[1];
					break;
				}
			}
		}

		// ===== 2. ORDER NUMBER (No. Pesanan) =====
		if (preg_match('/(\d{6}SHP[A-Z0-9]{4,15})/i', $full_str, $m)) {
			$pesanan = $m[1];
		}
		if (empty($pesanan)) {
			if (preg_match('/(No\.?\s*Pesanan|Pesanan|Order\s*(?:No|ID|Number)?)\s*[:\/]\s*([A-Za-z0-9\-]{6,30})/i', $full_str, $m)) {
				$pesanan = trim($m[2]);
			}
		}
		if (empty($pesanan)) {
			foreach ($items_str as $idx => $str) {
				if (preg_match('/^(No\.?\s*Pesanan|Pesanan|Order\s*(?:No|ID))\s*:?\s*$/i', trim($str)) && isset($items_str[$idx + 1])) {
					$nxt = trim($items_str[$idx + 1]);
					if (strlen($nxt) >= 6 && preg_match('/^[A-Za-z0-9\-]+$/', $nxt)) {
						$pesanan = $nxt;
						break;
					}
				}
			}
		}

		// ===== 3. COURIER DETECTION =====
		// 3.1 Check resi prefix first if resi was extracted
		if (!empty($resi)) {
			$ru = strtoupper($resi);
			if (strpos($ru, '00') === 0 || strpos($ru, 'SIC') === 0) $courier = 'SiCepat REG';
			elseif (strpos($ru, 'JT') === 0 || strpos($ru, 'JP') === 0 || strpos($ru, 'JX') === 0 || strpos($ru, 'JNT') === 0 || strpos($ru, 'EZ') === 0) $courier = 'J&T Express REG';
			elseif (strpos($ru, 'JNE') === 0 || strpos($ru, 'CTC') === 0) $courier = 'JNE Express REG';
			elseif (strpos($ru, 'SPX') === 0) $courier = 'SPX Express Standard';
			elseif (strpos($ru, 'IDS') === 0 || strpos($ru, 'IDE') === 0) $courier = 'ID Express REG';
			elseif (strpos($ru, 'NLID') === 0) $courier = 'Ninja Express';
			elseif (strpos($ru, 'LP') === 0) $courier = 'Lion Parcel REG';
			elseif (strpos($ru, 'ANTR') === 0 || strpos($ru, '1000') === 0) $courier = 'Anteraja Regular';
		}

		// 3.2 Check courier from HEADER LINE ONLY (item[0]) for service codes
		// This avoids false matches from customer messages (e.g. "butuh cepat" matching SiCepat)
		if (empty($courier) && !empty($items_str[0])) {
			$header_line = $items_str[0];
			$header_couriers = [
				'/(SiCepat|SICEPAT|\bSIC\b|\bEPAT\b|\bEPAS\b|\bCEPAT\b|\bSEPAT\b|\bSICE\b|\bHALU\b|\bGOKIL\b)/i' => 'SiCepat REG',
				'/(\bDF\b|\bEZ\b|\bJNT\b|J\s*&\s*T|J\s*\+\s*T)/i' => 'J&T Express REG',
				'/(\bSNE\b|\b3NE\b|\bJNE\b|\bCTC\b)/i' => 'JNE Express REG',
				'/(\bIN\b)/' => 'SPX Express Instant',
				'/(\bHE\b)/' => 'SPX Express Hemat',
				'/(\bSTD\b|\bSPX\b|\bSPXID\b)/i' => 'SPX Express Standard',
			];
			foreach ($header_couriers as $pattern => $name) {
				if (preg_match($pattern, $header_line)) {
					$courier = $name;
					break;
				}
			}
		}

		// 3.3 Check specific courier names in full text (only safe non-ambiguous keywords)
		if (empty($courier)) {
			$specific_couriers = [
				'/(SiCepat\s*REG|SICEPAT\s*REG|SiCepat\s*BEST|SiCepat\s*Halu|SiCepat|\bSICEPAT\b|\bHALU\b|\bGOKIL\b)/i' => 'SiCepat REG',
				'/(J\s*&\s*T\s*Express\s*REG|J\s*&\s*T\s*Express|J\s*&\s*T|\bJNT\b|\bJ\+T\b)/i' => 'J&T Express REG',
				'/(JNE\s*Express\s*YES|JNE\s*YES)/i' => 'JNE Express YES',
				'/(JNE\s*Express\s*REG|JNE\s*Express|JNE\s*Trucking|JNE\s*OKE|\bJNE\b)/i' => 'JNE Express REG',
				'/(ID\s*Express\s*REG|IDexpress|ID\s*Express|\bIDEXPRESS\b)/i' => 'ID Express REG',
				'/(Ninja\s*Express|Ninja\s*Xpress|NinjaVan|\bNINJA\b|\bNLID\b)/i' => 'Ninja Express',
				'/(Anteraja\s*Regular|Anteraja|\bANTERAJA\b|\bSATSET\b)/i' => 'Anteraja Regular',
				'/(Lion\s*Parcel|\bLION\b)/i' => 'Lion Parcel REG',
				'/(Pos\s*Indonesia)/i' => 'Pos Indonesia',
				'/(SPX\s*Express\s*Instant)/i' => 'SPX Express Instant',
				'/(SPX\s*Express\s*Hemat|Shopee\s*Express\s*Hemat)/i' => 'SPX Express Hemat',
				'/(SPX\s*Express\s*Standard|Shopee\s*Express\s*Standard|SPX\s*Express|Shopee\s*Express|\bSPXID\b)/i' => 'SPX Express Standard',
			];

			foreach ($specific_couriers as $pattern => $name) {
				if (preg_match($pattern, $full_str)) {
					$courier = $name;
					break;
				}
			}
		}

		// 3.3 If REG is in text without explicit SiCepat / J&T / SPX keywords, check if JNE or SiCepat
		if (empty($courier) && preg_match('/\bREG\b/i', $full_str)) {
			// Only check header line for SiCepat OCR misreads, not customer messages
			$header_for_reg = !empty($items_str[0]) ? $items_str[0] : '';
			if (preg_match('/(SICEPAT|EPAT|EPAS|SICE|SIC)/i', $header_for_reg) || preg_match('/00\d{10}/', $full_str)) {
				$courier = 'SiCepat REG';
			} else {
				$courier = 'JNE Express REG';
			}
		}

		// ===== 4. RECIPIENT NAME (Penerima) =====
		$penerima_patterns = [
			'/(?:Penerima|Nama\s*Penerima|Kepada|To|Recipient|Receiver)\s*[:\/]?\s*([A-Za-z0-9\.\s\-\'\(\)]{2,50}?)(?=\s*(?:(?:No\.?\s*)?(?:Telp|Telepon|Hp|HP|Tel|Phone|Handphone|WA)|(?:62|0)8[0-9]|Alamat|Pengirim|Dikirim|Order|AWB|Kota|Kab|Prov|\||\n|$))/i',
			'/Penerima\s*[:\/]?\s*([A-Za-z0-9\.\s\-\'\(\)]{2,50})/i',
			'/Nama\s*Penerima\s*[:\/]?\s*([A-Za-z0-9\.\s\-\'\(\)]{2,50})/i',
			'/Kepada\s*[:\/]?\s*([A-Za-z0-9\.\s\-\'\(\)]{2,50})/i',
			'/To\s*[:\/]?\s*([A-Za-z0-9\.\s\-\'\(\)]{2,50})/i',
		];

		foreach ($penerima_patterns as $pp) {
			if (preg_match($pp, $full_str, $m)) {
				$cand = trim($m[1]);
				$cand = preg_replace('/(?:\+?62|0)8[0-9\s\-]{7,14}.*$/i', '', $cand);
				$cand = preg_replace('/\s+(?:No\s*Hp|No\s*Telp|Notelp|Telp|Telepon|Phone|Handphone|Alamat|Pengirim|No\.?\s*Telepon).*$/i', '', $cand);
				$cand = trim($cand);
				if (strlen($cand) >= 2 && !preg_match('/^(pengirim|alamat|no\.?|halaman|page|dicetak|shopee|spx|jne|jnt|variasi|qty|berat|order)/i', $cand)) {
					$penerima = ucwords(strtolower($cand));
					break;
				}
			}
		}

		if (empty($penerima)) {
			foreach ($items_str as $idx => $str) {
				if (preg_match('/^(Penerima|Nama\s*Penerima|Kepada|To|Recipient|Receiver)\s*:?\s*$/i', trim($str)) && isset($items_str[$idx + 1])) {
					$nxt = trim($items_str[$idx + 1]);
					$nxt = preg_replace('/(?:\+?62|0)8[0-9\s\-]{7,14}.*$/i', '', $nxt);
					$nxt = trim($nxt);
					if (strlen($nxt) >= 2 && !preg_match('/^(No\.?|Pengirim|Alamat|Halaman|Shopee|SPX|JNE)/i', $nxt)) {
						$penerima = ucwords(strtolower($nxt));
						break;
					}
				}
			}
		}

		// Fallback recipient name search if still empty: look for item before phone number
		if (empty($penerima)) {
			foreach ($items_str as $idx => $str) {
				$str_clean = trim($str);
				if (preg_match('/(?:\+?62|0)8[0-9\s\-]{7,14}/', $str_clean)) {
					if ($idx > 0 && isset($items_str[$idx - 1])) {
						$prev = trim($items_str[$idx - 1]);
						$prev = preg_replace('/^(Penerima|Kepada|To)\s*:?\s*/i', '', $prev);
						if (strlen($prev) >= 2 && strlen($prev) <= 40 && !preg_match('/^(No\.?|Pengirim|Alamat|Shopee|SPX|Resi)/i', $prev)) {
							$penerima = ucwords(strtolower($prev));
							break;
						}
					}
				}
			}
		}

		// ===== 5. PHONE NUMBER =====
		if (preg_match('/(No\.?\s*(?:Telepon|Telp|Hp|HP|Tel|Phone|Handphone|WA)?)\s*[:\/]?\s*(\+?62[\d\s\-]{8,15}|08[\d\s\-]{8,15})/i', $full_str, $m)) {
			$phone = preg_replace('/[^\d]/', '', trim($m[2]));
			if (strpos($phone, '62') === 0) {
				$phone = '0' . substr($phone, 2);
			}
		}

		if (empty($phone)) {
			$clean_text_for_phone = preg_replace('/[\(\)\-\.]/', '', $full_str);
			if (preg_match_all('/(?:\+?62|0)(8[0-9]{8,12})/', $clean_text_for_phone, $matches)) {
				$raw_p = $matches[1][0];
				$phone = '0' . $raw_p;
			}
		}

		if (empty($phone)) {
			foreach ($items_str as $idx => $str) {
				if (preg_match('/^(No\.?\s*(?:Telepon|Telp|Hp|HP|Tel|Phone|WA))\s*:?\s*$/i', trim($str)) && isset($items_str[$idx + 1])) {
					$nxt = preg_replace('/[^\d]/', '', trim($items_str[$idx + 1]));
					if (preg_match('/^(?:62|0)?(8[0-9]{8,12})$/', $nxt, $pm)) {
						$phone = '0' . $pm[1];
						break;
					}
				}
			}
		}

		// ===== 6. ADDRESS =====
		// Strategy A: Look for "Alamat:" label
		if (preg_match('/Alamat\s*[:\/]\s*(.{10,150}?)\s*(?:Pengirim|Dikirim|Berat|Detail|Produk|Halaman|\|\|)/i', $full_str, $m)) {
			$address = trim($m[1]);
		}
		if (empty($address)) {
			if (preg_match('/Alamat\s*[:\/]\s*(.{10,150})/i', $full_str, $m)) {
				$address = trim($m[1]);
			}
		}

		// Strategy B: For Shopee/SPX OCR labels - address is on lines AFTER phone number, starts with Jl./Jln/Jalan/Gang/Perum/Komp/Dsn/Dusun
		if (empty($address)) {
			$phone_line_idx = -1;
			foreach ($items_str as $idx => $str) {
				if (preg_match('/(?:\+?62|0)8[0-9\s\-]{7,14}/', $str)) {
					$phone_line_idx = $idx;
					break;
				}
			}
			if ($phone_line_idx >= 0) {
				$addr_parts = [];
				// Check lines after phone for address content
				for ($i = $phone_line_idx; $i < min($phone_line_idx + 5, count($items_str)); $i++) {
					$line = trim($items_str[$i]);
					// Stop if we hit section headers
					if (preg_match('/^(No\.?\s*Pesanan|Berat|CASHLESS|Pengirim|#\s*Nama|Detail\s*Produk)/i', $line)) break;
					// Look for address-like content (street names, city/district names)
					if (preg_match('/(Jl|Jln|Jalan|JI|Gang|Gg|Perum|Komp|Dsn|Dusun|Kp|Kampung)\b/i', $line) ||
						preg_match('/(KABUPATEN|KOTA|KAB\.|Kec\.|Kel\.|Desa\s)/i', $line) ||
						preg_match('/No\.\s*\d/i', $line)) {
						// Clean: remove sender's phone/info that OCR puts on the same line (right column)
						$clean_line = preg_replace('/\s*(?:628[0-9]{8,13}|KAB\.\s*[A-Z\s]+$)/i', '', $line);
						$clean_line = trim($clean_line);
						if (!empty($clean_line) && strlen($clean_line) >= 3) {
							$addr_parts[] = $clean_line;
						}
					}
					// Also catch continuation lines with province/city (JAWA TIMUR, DKI JAKARTA, etc)
					elseif (preg_match('/^(TIMUR|BARAT|TENGAH|SELATAN|UTARA|DKI|JAWA|SUMATERA|KALIMANTAN|SULAWESI|BALI|PAPUA)\b/i', $line)) {
						$clean_line = preg_replace('/\s*KAB\.\s*[A-Z\s]+$/i', '', $line);
						$clean_line = trim($clean_line);
						if (!empty($clean_line)) {
							$addr_parts[] = $clean_line;
						}
					}
				}
				if (!empty($addr_parts)) {
					$address = implode(', ', $addr_parts);
				}
			}
		}

		// Strategy C: Direct regex for street patterns in full text
		if (empty($address)) {
			if (preg_match('/((?:Jl|Jln|Jalan|JI)\.?\s+[^#]{10,120}?(?:KABUPATEN|KOTA|KAB\.)[^#]{3,50}?(?:JAWA\s*(?:TIMUR|BARAT|TENGAH)|DKI\s*JAKARTA|SUMATERA|KALIMANTAN|SULAWESI|BALI|PAPUA)[A-Z\s]*)/i', $full_str, $m)) {
				$address = trim($m[1]);
				// Remove sender info that might be mixed in
				$address = preg_replace('/\s*628[0-9]{8,13}\s*/i', ' ', $address);
				$address = preg_replace('/\s*KAB\.\s*[A-Z]{3,20}\s*$/i', '', $address);
			} elseif (preg_match('/((?:Jl|Jln|Jalan|JI)\.?\s+.{10,100})/i', $full_str, $m)) {
				$address = trim($m[1]);
				$address = preg_replace('/\s*628[0-9]{8,13}\s*/i', ' ', $address);
			} elseif (preg_match('/(Perum(?:ahan)?\s+[^#|]{10,80})/i', $full_str, $m)) {
				$address = trim($m[1]);
			} elseif (preg_match('/(Komp(?:lek)?\s+[^#|]{10,80})/i', $full_str, $m)) {
				$address = trim($m[1]);
			}
		}

		// Strategy D: Labeled "Alamat" on separate line
		if (empty($address)) {
			foreach ($items_str as $idx => $str) {
				if (preg_match('/^Alamat\s*:?\s*$/i', trim($str)) && isset($items_str[$idx + 1])) {
					$nxt = trim($items_str[$idx + 1]);
					if (strlen($nxt) >= 10) {
						$address = $nxt;
						for ($j = $idx + 2; $j < min($idx + 5, count($items_str)); $j++) {
							$cont = trim($items_str[$j]);
							if (strlen($cont) > 3 && !preg_match('/^(Pengirim|Berat|Detail|Produk|Halaman|No\.?|Penerima|Variasi|Qty|Harga|#)/i', $cont)) {
								$address .= ' ' . $cont;
							} else {
								break;
							}
						}
						break;
					}
				}
			}
		}

		if (!empty($address)) {
			$address = preg_replace('/\s+/', ' ', $address);
			$address = rtrim($address, ' ,.');
		}

		// ===== 7. PRODUCT NAME & QTY (from Shopee OCR table format) =====
		// OCR reads product table as lines like:
		// "1 Drum Plastik HDPE 200 Liter PRD-DRM-2680 Default 2"
		// "Daun Teh Hitam Premium Earl Grey Blend kg pRD-ww-010 Default z"  (OCR errors: no line#, z→2)
		$products = [];
		$total_qty_parsed = 0;
		$in_product_section = false;
		
		foreach ($items_str as $idx => $str) {
			$str_clean = trim($str);
			
			// Detect product table header
			if (preg_match('/^#\s*Nama\s*Produk/i', $str_clean) || preg_match('/Nama\s*Produk\s+SKU/i', $str_clean)) {
				$in_product_section = true;
				continue;
			}
			
			if ($in_product_section) {
				// Stop at non-product lines
				if (preg_match('/^(Pesan\s*[:\(]|Catatan|Note\s*:|Total|CASHLESS|Pengirim|No\.?\s*Pesanan)/i', $str_clean)) {
					break;
				}
				// Skip very short lines or continuation fragments
				if (strlen($str_clean) < 5) continue;
				// Skip lines that are just numbers or partial continuations like "50)"
				if (preg_match('/^\d{1,3}\)?\s*$/', $str_clean)) continue;
				// Skip lines that are just volume/weight units (e.g. "750ml", "1kg", "250gr")
				if (preg_match('/^\d+\s*(ml|gr|g|kg|liter|l|cm|mm|oz|pcs|pes)\s*$/i', $str_clean)) continue;
				if (preg_match('/^[=~\-]+$/', $str_clean)) continue;
				
				// Try to find SKU pattern in the line (PRD-xxx, pRD-xxx, PRO-xxx, SKU-xxx, etc)
				// OCR often misreads: PRD→pRD, PRO, P8D, etc
				if (preg_match('/\b[pP][rRoO][dDoO0]\s*[\-\.]\s*[A-Za-z0-9]+[\-\.][A-Za-z0-9]+/i', $str_clean) ||
					preg_match('/\b[sS][kK][uU]\s*[\-\.]/i', $str_clean)) {
					
					// Extract product name: text BEFORE the SKU code
					$prod_name = preg_replace('/\s*[pP][rRoO][dDoO0]\s*[\-\.].*$/', '', $str_clean);
					// Remove leading line number (1, 2, 3, etc)
					$prod_name = preg_replace('/^\d+\s+/', '', $prod_name);
					$prod_name = trim($prod_name);
					
					// Extract qty: the LAST digit(s) on the line
					$last_char = substr(trim($str_clean), -1);
					// OCR common misreads: z→2, Z→2, l→1, O→0, ¢→0, s→5
					$ocr_digit_fix = ['z' => '2', 'Z' => '2', 'l' => '1', 'O' => '0', 'o' => '0', 's' => '5', 'S' => '5', '¢' => '0'];
					if (isset($ocr_digit_fix[$last_char])) {
						$last_char = $ocr_digit_fix[$last_char];
					}
					$prod_qty = is_numeric($last_char) ? (int)$last_char : 1;
					
					// Also check for multi-digit qty at end: "... 12" or "... 10"
					if (preg_match('/\s+(\d{1,3})\s*$/', $str_clean, $qm)) {
						$prod_qty = (int)$qm[1];
					}
					
					if (!empty($prod_name) && strlen($prod_name) >= 3 && $prod_qty > 0 && $prod_qty < 500) {
						$products[] = ['name' => $prod_name, 'qty' => $prod_qty];
						$total_qty_parsed += $prod_qty;
					}
				}
				// Fallback: line starts with digit and ends with digit (simple product row)
				elseif (preg_match('/^(\d+)\s+(.{5,}?)\s+(\d{1,3})\s*$/', $str_clean, $pm)) {
					$prod_name = trim($pm[2]);
					$prod_qty = (int)$pm[3];
					if ($prod_qty > 0 && $prod_qty < 500 && strlen($prod_name) >= 3) {
						$products[] = ['name' => $prod_name, 'qty' => $prod_qty];
						$total_qty_parsed += $prod_qty;
					}
				}
			}
		}
		
		// Build product name string
		if (!empty($products)) {
			$prod_names = [];
			foreach ($products as $p) {
				$prod_names[] = $p['name'] . ' (x' . $p['qty'] . ')';
			}
			$produk = implode(', ', $prod_names);
		} else {
			// Fallback: single product extraction
			if (preg_match('/(Detail\s*Produk|Nama\s*Produk|Produk)\s*[:\/ ]?\s*(.{5,80}?)\s*(?:Variasi|Qty|Berat|Harga|\||$)/i', $full_str, $m)) {
				$produk = trim($m[2]);
			}
			if (empty($produk)) {
				foreach ($items_str as $idx => $str) {
					if (preg_match('/^(Detail\s*Produk|Nama\s*Produk|Produk)\s*:?\s*$/i', trim($str)) && isset($items_str[$idx + 1])) {
						$produk = trim($items_str[$idx + 1]);
						break;
					}
				}
			}
		}

		// ===== 8. VARIASI =====
		if (preg_match('/Variasi\s*[:\/ ]\s*(.{2,50}?)\s*(?:Qty|Berat|Harga|\||$)/i', $full_str, $m)) {
			$variasi = trim($m[1]);
		}

		// ===== 9. QTY =====
		if ($total_qty_parsed > 0) {
			$qty = $total_qty_parsed;
		} else {
			// Extract qty but skip table header "SKU Variasi Qty" followed by volume numbers like "750ml"
			// Look for standalone qty patterns NOT in table header context
			if (preg_match('/(?<!Variasi\s)Qty\s*[:\/ ]\s*(\d{1,2})(?!\d)(?!\s*(?:ml|gr|g|kg|liter|l|cm|mm|oz)\b)/i', $full_str, $m)) {
				$qty_candidate = (int)$m[1];
				if ($qty_candidate > 0 && $qty_candidate <= 50) {
					$qty = $qty_candidate;
				}
			}
		}
		if ($qty <= 0 && preg_match('/[xX]\s*(\d{1,2})(?!\d)/', $full_str, $m)) {
			$qty_candidate = (int)$m[1];
			if ($qty_candidate > 0 && $qty_candidate <= 50) {
				$qty = $qty_candidate;
			}
		}
		if ($qty <= 0) $qty = 1;
		if ($qty > 50) $qty = 1; // Safety cap: Shopee orders rarely exceed 50 items

		// ===== 10. HARGA =====
		$harga = 0;
		if (preg_match('/(?:Harga|Total|Subtotal|Amount)\s*[:\/]?\s*(?:Rp\.?\s*)?([0-9][0-9\.,]+)/i', $full_str, $m)) {
			$harga = (float)str_replace(['.', ','], ['', '.'], $m[1]);
		}

		// Debug: log parsed products
		@file_put_contents(APPPATH . 'logs/scan_resi_debug.log',
			"  PARSED => resi=$resi | pesanan=$pesanan | courier=$courier | penerima=$penerima | phone=$phone | address=" . substr($address, 0, 60) . " | qty=$qty | products=" . count($products) . " | produk=" . substr($produk, 0, 80) . "\n", FILE_APPEND);

		return [
			'resi'     => $resi,
			'pesanan'  => $pesanan,
			'courier'  => $courier,
			'penerima' => $penerima,
			'phone'    => $phone,
			'address'  => $address,
			'produk'   => $produk,
			'variasi'  => $variasi,
			'qty'      => $qty,
			'harga'    => $harga,
			'products' => $products,
		];
	}

	private function _extract_pdf_recipient_info($text_items, $so)
	{
		$parsed = $this->_parse_full_pdf_label($text_items);

		$extracted_name = !empty($parsed['penerima']) ? $parsed['penerima'] : (!empty($so->nama_penerima) && strpos($so->nama_penerima, '*') === false ? $so->nama_penerima : '-');
		$extracted_phone = !empty($parsed['phone']) ? $parsed['phone'] : (!empty($so->no_telepon) && strpos($so->no_telepon, '*') === false ? $so->no_telepon : '-');
		$extracted_addr = !empty($parsed['address']) ? $parsed['address'] : (!empty($so->alamat_pengiriman) ? $so->alamat_pengiriman : '-');

		return [
			'name'    => $extracted_name,
			'phone'   => $extracted_phone,
			'address' => $extracted_addr
		];
	}

	private function _estimate_item_price($pp_name, $final_harga = 0, $prod = null)
	{
		if ($prod && !empty($prod->harga_jual) && (float)$prod->harga_jual > 0) {
			return (float)$prod->harga_jual;
		}
		if ($final_harga > 0) {
			return (float)$final_harga;
		}
		
		$name_lower = strtolower($pp_name);
		if (strpos($name_lower, 'drum') !== false) return 350000;
		if (strpos($name_lower, 'laptop') !== false) return 8500000;
		if (strpos($name_lower, 'printer') !== false) return 3500000;
		if (strpos($name_lower, 'sirup') !== false || strpos($name_lower, 'syrup') !== false) return 125000;
		if (strpos($name_lower, 'cup') !== false || strpos($name_lower, 'lid') !== false || strpos($name_lower, 'gelas') !== false) return 85000;
		if (strpos($name_lower, 'teh') !== false || strpos($name_lower, 'matcha') !== false || strpos($name_lower, 'kopi') !== false || strpos($name_lower, 'powder') !== false) return 145000;
		if (strpos($name_lower, 'cleaner') !== false || strpos($name_lower, 'descaling') !== false) return 175000;
		if (strpos($name_lower, 'gula') !== false || strpos($name_lower, 'sugar') !== false) return 45000;
		
		return 150000;
	}

	/**
	 * Unified Master Courier Resolver
	 * Correctly identifies all Indonesian marketplace couriers:
	 * SiCepat, J&T Express, JNE Express, SPX Express, ID Express, Ninja, Anteraja, Lion Parcel, Pos Indonesia
	 */
	private function _resolve_courier($parsed_courier = '', $resi = '', $extracted_text = [])
	{
		// 1. Check Resi Prefix first (Highest precision)
		if (!empty($resi)) {
			$ru = strtoupper(trim($resi));

			if (strpos($ru, '00') === 0 && preg_match('/^00\d{10,14}$/', $ru)) return 'SiCepat REG';
			if (strpos($ru, 'SIC') === 0) return 'SiCepat REG';
			if (strpos($ru, 'JT') === 0 || strpos($ru, 'JP') === 0 || strpos($ru, 'JX') === 0 || strpos($ru, 'JNT') === 0 || strpos($ru, 'EZ') === 0) return 'J&T Express REG';
			if (strpos($ru, 'JNE') === 0 || strpos($ru, 'CTC') === 0) return 'JNE Express REG';
			if (strpos($ru, 'SPX') === 0) {
				// Extract header line to determine SPX service type
				$header = '';
				if (is_array($extracted_text) && !empty($extracted_text[0])) {
					$header = is_array($extracted_text[0]) ? ($extracted_text[0]['str'] ?? '') : $extracted_text[0];
				}
				if (preg_match('/\bIN\b/', $header) || stripos($header, 'Instant') !== false) return 'SPX Express Instant';
				if (preg_match('/\bHE\b/', $header) || stripos($header, 'Hemat') !== false) return 'SPX Express Hemat';
				return 'SPX Express Standard';
			}
			if (strpos($ru, 'IDS') === 0 || strpos($ru, 'IDE') === 0) return 'ID Express REG';
			if (strpos($ru, 'NLID') === 0) return 'Ninja Express';
			if (strpos($ru, 'LP') === 0) return 'Lion Parcel REG';
			if (strpos($ru, 'ANTR') === 0 || strpos($ru, '1000') === 0) return 'Anteraja Regular';
			if (strpos($ru, 'POS') === 0) return 'Pos Indonesia';
		}

		// 2. Check HEADER LINE ONLY (first text item) for service codes
		//    This avoids matching common words like "cepat" in customer messages
		$header_line = '';
		if (is_array($extracted_text) && !empty($extracted_text[0])) {
			$header_line = is_array($extracted_text[0]) ? ($extracted_text[0]['str'] ?? '') : $extracted_text[0];
		}

		if (!empty($header_line)) {
			$header_couriers = [
				'/(SiCepat|SICEPAT|\bSIC\b|\bEPAT\b|\bEPAS\b|\bCEPAT\b|\bSEPAT\b|\bSICE\b|\bHALU\b|\bGOKIL\b)/i' => 'SiCepat REG',
				'/(\bDF\b|\bEZ\b|\bJNT\b|J\s*&\s*T|J\s*\+\s*T)/i' => 'J&T Express REG',
				'/(\bSNE\b|\b3NE\b|\bJNE\b|\bCTC\b)/i' => 'JNE Express REG',
				'/(\bIN\b)/' => 'SPX Express Instant',
				'/(\bHE\b)/' => 'SPX Express Hemat',
				'/(\bSTD\b|\bSPX\b|\bSPXID\b)/i' => 'SPX Express Standard',
			];
			foreach ($header_couriers as $pattern => $name) {
				if (preg_match($pattern, $header_line)) {
					return $name;
				}
			}
		}

		// 3. Check specific courier names in full text (only safe non-ambiguous keywords)
		$full_str = is_array($extracted_text) ? implode(' ', array_map(function($i){ return is_array($i) ? ($i['str']??'') : $i; }, $extracted_text)) : (string)$extracted_text;

		$couriers_map = [
			'/(SiCepat\s*REG|SICEPAT\s*REG|SiCepat\s*BEST|SiCepat\s*Halu|SiCepat|\bSICEPAT\b|\bHALU\b|\bGOKIL\b)/i' => 'SiCepat REG',
			'/(J\s*&\s*T\s*Express\s*REG|J\s*&\s*T\s*Express|J\s*&\s*T|\bJNT\b|\bJ\+T\b)/i' => 'J&T Express REG',
			'/(JNE\s*Express\s*YES|JNE\s*YES)/i' => 'JNE Express YES',
			'/(JNE\s*Express\s*REG|JNE\s*Express|JNE\s*Trucking|JNE\s*OKE|\bJNE\b)/i' => 'JNE Express REG',
			'/(ID\s*Express\s*REG|IDexpress|ID\s*Express|\bIDEXPRESS\b)/i' => 'ID Express REG',
			'/(Ninja\s*Express|Ninja\s*Xpress|NinjaVan|\bNINJA\b|\bNLID\b)/i' => 'Ninja Express',
			'/(Anteraja\s*Regular|Anteraja|\bANTERAJA\b|\bSATSET\b)/i' => 'Anteraja Regular',
			'/(Lion\s*Parcel|\bLION\b)/i' => 'Lion Parcel REG',
			'/(Pos\s*Indonesia)/i' => 'Pos Indonesia',
			'/(SPX\s*Express\s*Instant)/i' => 'SPX Express Instant',
			'/(SPX\s*Express\s*Hemat|Shopee\s*Express\s*Hemat)/i' => 'SPX Express Hemat',
			'/(SPX\s*Express\s*Standard|Shopee\s*Express\s*Standard|SPX\s*Express|Shopee\s*Express|\bSPXID\b)/i' => 'SPX Express Standard',
		];

		foreach ($couriers_map as $pattern => $name) {
			if (preg_match($pattern, $full_str)) {
				return $name;
			}
		}

		// 4. REG keyword disambiguation — only check header for SiCepat OCR misreads
		if (preg_match('/\bREG\b/i', $full_str)) {
			if (preg_match('/(SICEPAT|EPAT|EPAS|SICE|SIC)/i', $header_line) || preg_match('/00\d{10}/', $full_str)) {
				return 'SiCepat REG';
			}
			return 'JNE Express REG';
		}

		// 5. If parsed_courier was provided and non-generic, use it as last resort
		if (!empty($parsed_courier) && $parsed_courier !== 'Standard Delivery') {
			return $parsed_courier;
		}

		return 'Standard Delivery';
	}

	public function save_unmask_ajax()
	{
		$id = $this->input->post('id');
		$nama_penerima = trim($this->input->post('nama_penerima'));
		$no_telepon = trim($this->input->post('no_telepon'));
		$alamat_pengiriman = trim($this->input->post('alamat_pengiriman'));

		if (empty($id)) {
			echo json_encode([
				'status'  => 'error',
				'message' => 'ID Pesanan tidak valid!'
			]);
			return;
		}

		$this->db->where('id', $id)->update('penjualan_marketplace', [
			'nama_penerima'     => $nama_penerima,
			'no_telepon'        => $no_telepon,
			'alamat_pengiriman' => $alamat_pengiriman,
			'updated_at'        => date('Y-m-d H:i:s')
		]);

		echo json_encode([
			'status'  => 'success',
			'message' => "Data Unmasked Penerima '{$nama_penerima}' & Phone '{$no_telepon}' Berhasil Disimpan ke Database!"
		]);
	}
}

/* End of file Penjualan_marketplace.php */
/* Location: ./modules/penjualan_marketplace/controllers/backend/Penjualan_marketplace.php */
