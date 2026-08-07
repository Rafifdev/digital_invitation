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

}
