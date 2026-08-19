<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Sales_order extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_sales_order');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('sales_order_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		// Auto-sync SO status with sent/shipped Invoices (only for early stages draft/confirmed/packing)
		$this->db->query("
			UPDATE sales_order so
			JOIN invoice inv ON inv.so_id = so.id
			SET so.status = 'shipped'
			WHERE (inv.status_kirim = 'shipped' OR inv.status = 'sent') AND so.status IN ('draft', 'confirmed', 'packing')
		");

		$this->data['sos']       = $this->model_sales_order->get($filter, $field, $this->limit_page, $offset);
		$this->data['so_counts'] = $this->model_sales_order->count_all($filter, $field);
		$this->data['stats']     = $this->model_sales_order->get_stats();
		$this->data['pelanggans']= $this->db->get('pelanggan')->result();
		$this->data['gudangs']   = $this->db->get('gudang')->result();

		$config = [
			'base_url'    => 'administrator/sales_order/index/',
			'total_rows'  => $this->data['so_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Sales Order');
		$this->render('backend/standart/administrator/sales_order/sales_order_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('sales_order_add');
		$this->data['nomor_baru']  = $this->model_sales_order->generate_nomor();
		$this->data['pelanggans']  = $this->db->where('status', 'aktif')->get('pelanggan')->result();
		$this->data['gudangs']     = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->data['produks']     = $this->db->select('produk.*, satuan.singkatan as nama_satuan')
			->join('satuan', 'satuan.id = produk.satuan_id', 'LEFT')
			->where('produk.status', 'aktif')->get('produk')->result();
		$this->template->title('Buat Sales Order');
		$this->render('backend/standart/administrator/sales_order/sales_order_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('sales_order_add', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('pelanggan_id', 'Pelanggan', 'trim|required');
		$this->form_validation->set_rules('gudang_id', 'Gudang', 'trim|required');
		$this->form_validation->set_rules('tanggal_so', 'Tanggal SO', 'trim|required');

		if ($this->form_validation->run()) {
			$data = [
				'nomor_so'              => $this->model_sales_order->generate_nomor(),
				'pelanggan_id'          => $this->input->post('pelanggan_id'),
				'gudang_id'             => $this->input->post('gudang_id'),
				'tanggal_so'            => $this->input->post('tanggal_so'),
				'tanggal_estimasi_kirim'=> $this->input->post('tanggal_estimasi_kirim') ?: null,
				'diskon'                => (float) $this->input->post('diskon_persen_total'),
				'diskon_total'          => str_replace(['.', ','], '', $this->input->post('diskon_total')) ?: 0,
				'pajak_persen'          => $this->input->post('pajak_persen') ?: 0,
				'status'                => 'draft',
				'catatan'               => $this->input->post('catatan'),
				'created_by'            => get_user_data('username'),
				'created_at'            => date('Y-m-d H:i:s'),
			];

			$so_id = $this->model_sales_order->store($data);

			if ($so_id && $this->input->post('items')) {
				$gudang_id = $this->input->post('gudang_id');

				// Backend Stock Check
				foreach ($this->input->post('items') as $item) {
					if (empty($item['produk_id'])) continue;
					$qty = max(1, (int) round($item['qty']));
					$stok_row = $this->db->get_where('stok', ['produk_id' => $item['produk_id'], 'gudang_id' => $gudang_id])->row();
					$stok_avail = $stok_row ? (float)$stok_row->qty : 0;

					if ($qty > $stok_avail) {
						$prod = $this->db->get_where('produk', ['id' => $item['produk_id']])->row();
						$prod_name = $prod ? $prod->nama_produk : 'Produk';
						$this->db->delete('sales_order', ['id' => $so_id]);
						$this->response([
							'success' => false,
							'message' => "Gagal Simpan: Jumlah pesanan untuk ({$prod_name}) sejumlah {$qty} unit melebihi stok gudang yang tersedia ({$stok_avail} unit)!"
						]);
						return;
					}
				}

				foreach ($this->input->post('items') as $item) {
					if (empty($item['produk_id'])) continue;
					$qty     = max(1, (int) round($item['qty']));
					$harga   = (float) str_replace(['.', ','], '', $item['harga_satuan']);
					$diskon  = (float) $item['diskon_persen'];
					$d_nom   = $harga * $qty * ($diskon / 100);
					$subtotal = ($harga * $qty) - $d_nom;
					$this->db->insert('sales_order_item', [
						'so_id'         => $so_id,
						'produk_id'     => $item['produk_id'],
						'qty'           => $qty,
						'satuan'        => $item['satuan'],
						'harga_satuan'  => $harga,
						'diskon_persen' => $diskon,
						'diskon_nominal'=> $d_nom,
						'subtotal'      => $subtotal,
						'catatan'       => $item['catatan'] ?? '',
					]);
				}
				$this->model_sales_order->update_total($so_id);
			}

			set_message('Sales Order berhasil dibuat.', 'success');
			$this->data['success']  = true;
			$this->data['redirect'] = base_url('administrator/sales_order');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function view($id)
	{
		$this->is_allowed('sales_order_view');
		$this->data['so']    = $this->model_sales_order->find_with_detail($id);
		$this->data['items'] = $this->model_sales_order->get_items($id);
		$this->template->title('Detail Sales Order');
		$this->render('backend/standart/administrator/sales_order/sales_order_view', $this->data);
	}

	public function konfirmasi($id)
	{
		$this->is_allowed('sales_order_update');
		$so = $this->model_sales_order->find($id);

		if ($so && $so->status == 'draft') {
			// Cek stok dulu
			$stok_errors = $this->model_sales_order->cek_stok($id);
			if (count($stok_errors) > 0) {
				set_message('Gagal: ' . implode(', ', $stok_errors), 'error');
			} else {
				$this->db->update('sales_order', ['status' => 'confirmed', 'updated_at' => date('Y-m-d H:i:s')], ['id' => $id]);
				set_message('Sales Order berhasil dikonfirmasi.', 'success');
			}
		} else {
			set_message('SO sudah dikonfirmasi sebelumnya.', 'warning');
		}
		redirect('administrator/sales_order/view/' . $id);
	}

	public function buat_invoice($id)
	{
		$this->is_allowed('invoice_add');
		redirect('administrator/invoice/from_so/' . $id);
	}


	public function edit($id)
	{
		$this->is_allowed('sales_order_update');
		$this->data['so'] = $this->model_sales_order->find($id);
		if (!$this->data['so'] || $this->data['so']->status != 'draft') {
			set_message('Hanya data berstatus Draft yang bisa diedit.', 'error');
			redirect('administrator/sales_order');
		}
		$this->data['pelanggans'] = $this->db->where('status', 'aktif')->get('pelanggan')->result();
		$this->data['gudangs']    = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->data['produks']    = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->data['items']      = $this->model_sales_order->get_items($id);
		$this->template->title('Edit Sales Order');
		$this->render('backend/standart/administrator/sales_order/sales_order_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('sales_order_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$existing = $this->model_sales_order->find($id);
		if (!$existing || $existing->status != 'draft') {
			$this->response(['success' => false, 'message' => 'Hanya data berstatus Draft yang bisa diedit.']); return;
		}

		$data = [
			'catatan'               => $this->input->post('catatan'),
			'updated_at'            => date('Y-m-d H:i:s'),
			'updated_by'            => get_user_data('username'),
			'pelanggan_id'          => $this->input->post('pelanggan_id'),
			'gudang_id'             => $this->input->post('gudang_id'),
			'tanggal_so'            => $this->input->post('tanggal_so'),
			'tanggal_estimasi_kirim'=> $this->input->post('tanggal_estimasi_kirim') ?: null,
			'diskon'                => (float) $this->input->post('diskon_persen_total'),
			'diskon_total'          => (float) str_replace(['.', ','], '', $this->input->post('diskon_total')),
			'pajak_persen'          => (float) str_replace(['.', ','], '', $this->input->post('pajak_persen')),
		];

		$this->model_sales_order->change($id, $data);

		// Update items: delete old and insert new
		$this->db->delete('sales_order_item', ['so_id' => $id]);
		if ($this->input->post('items')) {
			foreach ($this->input->post('items') as $item) {
				if (empty($item['produk_id'])) continue;
				$qty     = max(1, (int) round($item['qty']));
				$harga   = (float) str_replace(['.', ','], '', $item['harga_satuan']);
				$diskon  = isset($item['diskon_persen']) ? (float) $item['diskon_persen'] : 0;
				$d_nom   = $harga * $qty * ($diskon / 100);
				$subtotal = ($harga * $qty) - $d_nom;
				$this->db->insert('sales_order_item', [
					'so_id'         => $id,
					'produk_id'     => $item['produk_id'],
					'qty'           => $qty,
					'satuan'        => $item['satuan'] ?? 'pcs',
					'harga_satuan'  => $harga,
					'diskon_persen' => $diskon,
					'diskon_nominal'=> $d_nom,
					'subtotal'      => $subtotal,
					'catatan'       => $item['catatan'] ?? '',
				]);
			}
			$this->model_sales_order->update_total($id);
		}

		set_message('Sales Order berhasil diupdate.', 'success');
		$this->data['success']  = true;
		$this->data['redirect'] = base_url('administrator/sales_order');
		$this->response($this->data);
	}

	public function update_status($id)
	{
		$this->is_allowed('sales_order_update');
		$status = $this->input->post('status') ?: $this->input->get('status');
		$so = $this->model_sales_order->find($id);

		if ($so && $status) {
			if (in_array($status, ['confirmed', 'packing']) && $so->status == 'draft') {
				$err = $this->model_sales_order->cek_stok($id);
				if (!empty($err)) {
					set_message(implode('<br>', $err), 'error');
					redirect('administrator/sales_order');
					return;
				}
			}

			$this->db->update('sales_order', [
				'status'     => $status,
				'updated_at' => date('Y-m-d H:i:s'),
				'updated_by' => get_user_data('username')
			], ['id' => $id]);

			// Auto sync Invoice status if shipped/delivered
			if (in_array($status, ['shipped', 'delivered'])) {
				$this->db->update('invoice', ['status_kirim' => $status], ['so_id' => $id]);
			}

			$status_labels = [
				'draft'        => 'Draft (Pesanan Baru)',
				'confirmed'    => 'Terkonfirmasi (Stok Ready)',
				'packing'      => 'Sedang Dikemas (Packing Gudang)',
				'shipped'      => 'Dalam Pengiriman (Shipped)',
				'delivered'    => 'Pesanan Selesai (Delivered)',
				'cancelled'    => 'Dibatalkan'
			];

			$lbl = isset($status_labels[$status]) ? $status_labels[$status] : strtoupper($status);
			set_message('Status Sales Order <strong>' . $so->nomor_so . '</strong> berhasil diperbarui ke: <strong>' . $lbl . '</strong>', 'success');
		}

		redirect('administrator/sales_order');
	}

	public function delete($id = null)
	{
		$this->is_allowed('sales_order_delete');
		$so = $this->model_sales_order->find($id);
		if ($so && $so->status == 'draft') {
			$this->db->delete('sales_order_item', ['so_id' => $id]);
			$this->model_sales_order->remove($id);
			set_message('SO berhasil dihapus.', 'success');
		} else {
			set_message('Hanya SO berstatus Draft yang bisa dihapus.', 'error');
		}
		redirect('administrator/sales_order');
	}

	public function export()
	{
		$this->is_allowed('sales_order_export');
		$this->model_sales_order->export('sales_order', 'sales_order');
	}

	

	

	public function cetak($id)
	{
		$this->is_allowed('sales_order_view');
		$this->data['so'] = $this->model_sales_order->join_available()->find($id);
		$this->data['items'] = $this->model_sales_order->get_items($id);
		
		$this->load->library('HtmlPdf');
		$this->htmlpdf->initialize(['orientation' => 'P', 'format' => 'A4', 'marges' => [5, 5, 5, 5]]);
		$content = $this->load->view('backend/standart/administrator/sales_order/sales_order_pdf', $this->data, true);
		
		$this->htmlpdf->writeHTML($content);
		$so = $this->data['so'];
		$doc_num = preg_replace('/[^a-zA-Z0-9_-]/', '_', $so->nomor_so ?? $id);
		$filename = 'sales_order_' . $doc_num . '.pdf';
		if (ob_get_length()) { ob_clean(); }
		$this->htmlpdf->Output($filename, 'D');
	}

	public function get_produk_by_gudang()
	{
		$gudang_id = $this->input->get('gudang_id');
		if (!$gudang_id) {
			$this->response(['success' => false, 'data' => []]);
			return;
		}
		
		$produks = $this->db->select('produk.id, produk.kode_produk, produk.nama_produk, produk.harga_jual, produk.harga_jual_grosir, produk.harga_jual_distributor, produk.harga_jual_vip, produk.barcode, stok.qty, satuan.singkatan as nama_satuan')
			->from('stok')
			->join('produk', 'produk.id = stok.produk_id')
			->join('satuan', 'satuan.id = produk.satuan_id', 'LEFT')
			->where('stok.gudang_id', $gudang_id)
			->where('stok.qty >', 0)
			->get()->result();
			
		$this->response(['success' => true, 'data' => $produks]);
	}

	public function export_pdf()
	{
		$this->is_allowed('sales_order_export');
		$this->model_sales_order->pdf('sales_order', 'sales_order');
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
			redirect('administrator/sales_order');
			return;
		}

		$file_path = $_FILES['file_csv']['tmp_name'];
		$handle = fopen($file_path, 'r');
		if (!$handle) {
			set_message('Gagal membaca file CSV.', 'error');
			redirect('administrator/sales_order');
			return;
		}

		// Remove BOM if present
		$bom = fread($handle, 3);
		if ($bom !== "\xEF\xBB\xBF") {
			rewind($handle);
		}

		$header = fgetcsv($handle, 4000, ",");
		if (!$header) {
			set_message('File CSV kosong atau format tidak sesuai.', 'error');
			redirect('administrator/sales_order');
			return;
		}

		// Clean header string
		$header = array_map(function($h) {
			return trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $h));
		}, $header);

		// Helper to search flexible key matching
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

		// Get default customer and warehouse
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

			// Check if order already imported
			$existing = $this->db->get_where('sales_order', ['no_pesanan' => $no_pesanan])->row();
			if ($existing) {
				$skipped_count++;
				continue;
			}

			// Map Shopee status to internal status
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
			$nomor_so = $this->model_sales_order->generate_nomor();

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

			$so_id = $this->model_sales_order->store($so_data);

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

				$this->db->insert('sales_order_item', [
					'so_id'         => $so_id,
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
		redirect('administrator/sales_order');
	}

	public function import_tokopedia()
	{
		$this->is_allowed('sales_order_add');

		if (empty($_FILES['file_csv']['name'])) {
			set_message('Silakan pilih file CSV/Excel Tokopedia yang akan diimport.', 'error');
			redirect('administrator/sales_order');
			return;
		}

		$file_path = $_FILES['file_csv']['tmp_name'];
		$handle = fopen($file_path, 'r');
		if (!$handle) {
			set_message('Gagal membaca file CSV.', 'error');
			redirect('administrator/sales_order');
			return;
		}

		$bom = fread($handle, 3);
		if ($bom !== "\xEF\xBB\xBF") {
			rewind($handle);
		}

		$header = fgetcsv($handle, 4000, ",");
		if (!$header) {
			set_message('File CSV kosong atau format tidak sesuai.', 'error');
			redirect('administrator/sales_order');
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

			$existing = $this->db->get_where('sales_order', ['no_pesanan' => $no_pesanan])->row();
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
			$nomor_so = $this->model_sales_order->generate_nomor();

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

			$so_id = $this->model_sales_order->store($so_data);

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

				$this->db->insert('sales_order_item', [
					'so_id'         => $so_id,
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
		redirect('administrator/sales_order');
	}
}

// Helper function for CSV line parsing if not existing
if (!function_exists('fputcsv_get_row')) {
	function fputcsv_get_row($handle) {
		return fgetcsv($handle, 4000, ",");
	}
}

