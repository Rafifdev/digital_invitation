<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Purchase_order extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_purchase_order');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('purchase_order_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['pos']       = $this->model_purchase_order->get($filter, $field, $this->limit_page, $offset);
		$this->data['po_counts'] = $this->model_purchase_order->count_all($filter, $field);
		$this->data['po_count']  = $this->data['po_counts'];

		$draft_grns = $this->db->get_where('penerimaan_barang', ['status' => 'draft'])->result();
		$draft_map = [];
		foreach ($draft_grns as $dg) {
			if ($dg->po_id) {
				$draft_map[$dg->po_id] = $dg;
			}
		}
		$this->data['draft_grn_map'] = $draft_map;

		$config = [
			'base_url'    => 'administrator/purchase_order/index/',
			'total_rows'  => $this->data['po_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Purchase Order');
		$this->render('backend/standart/administrator/purchase_order/purchase_order_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('purchase_order_add');
		$this->data['nomor_baru'] = $this->model_purchase_order->generate_nomor();
		$this->data['suppliers']  = $this->db->where('status', 'aktif')->get('supplier')->result();
		$this->data['gudangs']    = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->data['produks']    = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->template->title('Buat Purchase Order');
		$this->render('backend/standart/administrator/purchase_order/purchase_order_add', $this->data);
	}

	private function parse_number($val)
	{
		if ($val === null || $val === '' || $val === false) return 0.0;
		$val = trim((string)$val);
		if (strpos($val, ',') !== false) {
			$val = str_replace('.', '', $val);
			$val = str_replace(',', '.', $val);
		} else {
			$parts = explode('.', $val);
			if (count($parts) > 2) {
				$val = str_replace('.', '', $val);
			}
		}
		return (float) $val;
	}

	public function add_save()
	{
		if (!$this->is_allowed('purchase_order_add', false)) {
			return $this->response(['success' => false, 'message' => 'Tidak punya akses']);
		}

		$this->form_validation->set_rules('supplier_id', 'Supplier', 'trim|required');
		$this->form_validation->set_rules('tanggal_po', 'Tanggal PO', 'trim|required');

		if ($this->form_validation->run()) {
			$data = [
				'nomor_po'         => $this->model_purchase_order->generate_nomor(),
				'supplier_id'      => $this->input->post('supplier_id'),
				'gudang_id'        => $this->input->post('gudang_id') ?: null,
				'tanggal_po'       => $this->input->post('tanggal_po'),
				'tanggal_estimasi' => $this->input->post('tanggal_estimasi') ?: null,
				'mata_uang'        => $this->input->post('mata_uang') ?: 'IDR',
				'kurs'             => $this->parse_number($this->input->post('kurs')) ?: 1,
				'diskon'           => $this->parse_number($this->input->post('diskon')),
				'pajak_persen'     => $this->parse_number($this->input->post('pajak_persen')),
				'status'           => 'draft',
				'catatan'          => $this->input->post('catatan'),
				'created_by'       => get_user_data('username'),
				'created_at'       => date('Y-m-d H:i:s'),
			];

			$po_id = $this->model_purchase_order->store($data);

			if ($po_id && $this->input->post('items')) {
				foreach ($this->input->post('items') as $item) {
					if (empty($item['produk_id'])) continue;
					$qty       = max(1, (int) round($item['qty']));
					$harga     = $this->parse_number($item['harga_satuan'] ?? 0);
					$diskon    = $this->parse_number($item['diskon_persen'] ?? 0);
					$d_nom     = $harga * $qty * ($diskon / 100);
					$subtotal  = ($harga * $qty) - $d_nom;
					$this->db->insert('purchase_order_item', [
						'po_id'          => $po_id,
						'produk_id'      => $item['produk_id'],
						'qty_pesan'      => $qty,
						'satuan'         => $item['satuan'] ?? '',
						'harga_satuan'   => $harga,
						'diskon_persen'  => $diskon,
						'diskon_nominal' => $d_nom,
						'subtotal'       => $subtotal,
						'catatan'        => $item['catatan'] ?? '',
					]);
				}
				$this->model_purchase_order->update_total($po_id);
			}

			set_message('Purchase Order berhasil dibuat.', 'success');
			$this->data['success']  = true;
			$this->data['redirect'] = base_url('administrator/purchase_order');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function auto_restock($produk_id)
	{
		$this->is_allowed('purchase_order_add');
		$produk = $this->db->get_where('produk', ['id' => $produk_id])->row();
		if (!$produk) {
			set_message('Produk tidak ditemukan.', 'error');
			redirect('administrator/dashboard');
		}

		$supplier_id = null;
		$last_po_item = $this->db->select('purchase_order.supplier_id')
			->join('purchase_order', 'purchase_order.id = purchase_order_item.po_id')
			->where('purchase_order_item.produk_id', $produk_id)
			->order_by('purchase_order.tanggal_po', 'DESC')
			->limit(1)
			->get('purchase_order_item')->row();
			
		if ($last_po_item) {
			$supplier_id = $last_po_item->supplier_id;
		} else {
			$supp = $this->db->where('status', 'aktif')->get('supplier')->row();
			if($supp) $supplier_id = $supp->id;
		}

		$data = [
			'nomor_po' => $this->model_purchase_order->generate_nomor(),
			'supplier_id' => $supplier_id,
			'tanggal_po' => date('Y-m-d'),
			'mata_uang' => 'IDR',
			'kurs' => 1,
			'status' => 'draft',
			'created_by' => get_user_data('username'),
			'created_at' => date('Y-m-d H:i:s'),
		];
		$po_id = $this->model_purchase_order->store($data);

		$qty = $produk->stok_minimum > 0 ? $produk->stok_minimum * 2 : 10;
		$harga = $produk->harga_beli;

		$this->db->insert('purchase_order_item', [
			'po_id' => $po_id,
			'produk_id' => $produk_id,
			'qty_pesan' => $qty,
			'harga_satuan' => $harga,
			'subtotal' => $qty * $harga,
		]);
		
		$this->model_purchase_order->update_total($po_id);

		set_message('Draft Auto Restock berhasil dibuat. Silakan lengkapi data PO ini.', 'success');
		redirect('administrator/purchase_order/edit/' . $po_id);
	}

	public function view($id)
	{
		$this->is_allowed('purchase_order_view');
		$this->data['po']               = $this->model_purchase_order->find_with_detail($id);
		$this->data['items']            = $this->model_purchase_order->get_items($id);
		$this->data['penerimaans']      = $this->model_purchase_order->get_penerimaan_list($id);
		$this->data['draft_penerimaan'] = $this->model_purchase_order->get_draft_penerimaan($id);
		$this->template->title('Detail Purchase Order');
		$this->render('backend/standart/administrator/purchase_order/purchase_order_view', $this->data);
	}

	public function submit($id)
	{
		$this->is_allowed('purchase_order_update');
		$this->db->update('purchase_order', ['status' => 'submitted', 'updated_at' => date('Y-m-d H:i:s')], ['id' => $id, 'status' => 'draft']);

		$this->db->insert('approval_log', [
			'referensi_type' => 'purchase_order',
			'referensi_id'   => $id,
			'level'          => 1,
			'action'         => 'submitted',
			'created_by'     => get_user_data('username'),
			'created_at'     => date('Y-m-d H:i:s'),
		]);
		set_message('PO berhasil disubmit untuk approval.', 'success');
		redirect('administrator/purchase_order/view/' . $id);
	}

	public function approve($id)
	{
		$this->is_allowed('purchase_order_approve');
		$po = $this->model_purchase_order->find($id);
		if (!$po) {
			redirect('administrator/purchase_order');
			return;
		}

		$total_idr = $po->total * ($po->kurs ?: 1);
		$threshold = 50000000; // Threshold nominal Rp 50.000.000 untuk approval Level 2 (Manajer/Admin)

		if ($total_idr >= $threshold && !$this->aauth->is_admin()) {
			set_message('PO dengan nilai nominal di atas Rp 50.000.000 (Nominal: Rp ' . number_format($total_idr, 0, ',', '.') . ') memerlukan wewenang approval dari Administrator / Manajer Utama!', 'error');
			redirect('administrator/purchase_order/view/' . $id);
			return;
		}

		$level = ($total_idr >= $threshold) ? 2 : 1;

		$this->db->update('purchase_order', [
			'status'         => 'approved',
			'approval_level' => $level,
			'approved_by'    => get_user_data('username'),
			'approved_at'    => date('Y-m-d H:i:s'),
			'updated_at'     => date('Y-m-d H:i:s'),
		], ['id' => $id]);

		$this->db->insert('approval_log', [
			'referensi_type' => 'purchase_order',
			'referensi_id'   => $id,
			'level'          => $level,
			'action'         => 'approved',
			'created_by'     => get_user_data('username'),
			'created_at'     => date('Y-m-d H:i:s'),
		]);
		$this->model_purchase_order->record_price_history($id);

		set_message('PO berhasil diapprove (Level ' . $level . ') dan riwayat harga beli telah dicatat.', 'success');
		redirect('administrator/purchase_order/view/' . $id);
	}

	public function reject($id)
	{
		$this->is_allowed('purchase_order_approve');
		$this->db->update('purchase_order', ['status' => 'rejected', 'updated_at' => date('Y-m-d H:i:s')], ['id' => $id]);

		$this->db->insert('approval_log', [
			'referensi_type' => 'purchase_order',
			'referensi_id'   => $id,
			'level'          => 1,
			'action'         => 'rejected',
			'catatan'        => $this->input->post('catatan'),
			'created_by'     => get_user_data('username'),
			'created_at'     => date('Y-m-d H:i:s'),
		]);
		set_message('PO ditolak.', 'warning');
		redirect('administrator/purchase_order/view/' . $id);
	}


	public function edit($id)
	{
		$this->is_allowed('purchase_order_update');
		$this->data['po'] = $this->model_purchase_order->find($id);
		if (!$this->data['po'] || $this->data['po']->status != 'draft') {
			set_message('Hanya data berstatus Draft yang bisa diedit.', 'error');
			redirect('administrator/purchase_order');
		}
		$this->data['suppliers']  = $this->db->where('status', 'aktif')->get('supplier')->result();
		$this->data['gudangs']    = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->data['produks']    = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->data['items']      = $this->model_purchase_order->get_items($id);
		$this->template->title('Edit Purchase Order');
		$this->render('backend/standart/administrator/purchase_order/purchase_order_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('purchase_order_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$existing = $this->model_purchase_order->find($id);
		if (!$existing || $existing->status != 'draft') {
			$this->response(['success' => false, 'message' => 'Hanya data berstatus Draft yang bisa diedit.']); return;
		}

		$data = [
			'catatan'    => $this->input->post('catatan'),
			'updated_at' => date('Y-m-d H:i:s'),
			'updated_by' => get_user_data('username'),
		];
		$data['supplier_id'] = $this->input->post('supplier_id');
		$data['gudang_id'] = $this->input->post('gudang_id');
		$data['tanggal_po'] = $this->input->post('tanggal_po');
		$data['tanggal_estimasi'] = $this->input->post('tanggal_estimasi');
		$data['mata_uang'] = $this->input->post('mata_uang');
		$data['kurs'] = $this->parse_number($this->input->post('kurs')) ?: 1;
		$data['diskon'] = $this->parse_number($this->input->post('diskon'));
		$data['pajak_persen'] = $this->parse_number($this->input->post('pajak_persen'));

		$this->model_purchase_order->change($id, $data);

		// Update items: delete old and insert new
		$this->db->delete('purchase_order_item', ['po_id' => $id]);
		if ($this->input->post('items')) {
			foreach ($this->input->post('items') as $item) {
				$qty       = max(1, (int) round($item['qty']));
				$harga     = $this->parse_number($item['harga_satuan'] ?? 0);
				$diskon    = $this->parse_number($item['diskon_persen'] ?? 0);
				$d_nom     = $harga * $qty * ($diskon / 100);
				$subtotal  = ($harga * $qty) - $d_nom;
				$this->db->insert('purchase_order_item', [
					'po_id'          => $id,
					'produk_id'      => $item['produk_id'],
					'qty_pesan'      => $qty,
					'satuan'         => $item['satuan'] ?? 'pcs',
					'harga_satuan'   => $harga,
					'diskon_persen'  => $diskon,
					'diskon_nominal' => $d_nom,
					'subtotal'       => $subtotal,
					'catatan'        => $item['catatan'] ?? '',
				]);
			}
		}
		$this->model_purchase_order->update_total($id);

		set_message('Purchase Order berhasil diupdate.', 'success');
		$this->data['success']  = true;
		$this->data['redirect'] = base_url('administrator/purchase_order');
		$this->response($this->data);
	}

	public function delete($id = null)
	{
		$this->is_allowed('purchase_order_delete');
		$po = $this->model_purchase_order->find($id);
		if ($po && $po->status == 'draft') {
			$this->db->delete('purchase_order_item', ['po_id' => $id]);
			$this->model_purchase_order->remove($id);
			set_message('PO berhasil dihapus.', 'success');
		} else {
			set_message('Hanya PO berstatus Draft yang bisa dihapus.', 'error');
		}
		redirect('administrator/purchase_order');
	}

	public function export()
	{
		$this->is_allowed('purchase_order_export');
		$this->model_purchase_order->export('purchase_order', 'purchase_order');
	}

	

	

	public function cetak($id)
	{
		$this->is_allowed('purchase_order_view');
		$this->data['po'] = $this->model_purchase_order->join_available()->find($id);
		$this->data['items'] = $this->model_purchase_order->get_items($id);
		
		$this->load->library('HtmlPdf');
		$this->htmlpdf->initialize(['orientation' => 'P', 'format' => 'A4', 'marges' => [5, 5, 5, 5]]);
		$content = $this->load->view('backend/standart/administrator/purchase_order/purchase_order_pdf', $this->data, true);
		
		$this->htmlpdf->writeHTML($content);
		$po = $this->data['po'];
		$doc_num = preg_replace('/[^a-zA-Z0-9_-]/', '_', $po->nomor_po ?? $id);
		$filename = 'purchase_order_' . $doc_num . '.pdf';
		if (ob_get_length()) { ob_clean(); }
		$this->htmlpdf->Output($filename, 'D');
	}

	public function get_live_rates()
	{
		$url = 'https://open.er-api.com/v6/latest/USD';
		$json = @file_get_contents($url);

		if ($json) {
			$data = json_decode($json, true);
			if (isset($data['result']) && $data['result'] == 'success' && isset($data['rates'])) {
				$rates = $data['rates'];
				$idr = isset($rates['IDR']) ? (float)$rates['IDR'] : 16200;

				$calculated = [
					'IDR' => 1,
					'USD' => round($idr, 2),
					'EUR' => isset($rates['EUR']) && $rates['EUR'] > 0 ? round($idr / $rates['EUR'], 2) : 17500,
					'SGD' => isset($rates['SGD']) && $rates['SGD'] > 0 ? round($idr / $rates['SGD'], 2) : 12000,
					'JPY' => isset($rates['JPY']) && $rates['JPY'] > 0 ? round($idr / $rates['JPY'], 2) : 110,
					'CNY' => isset($rates['CNY']) && $rates['CNY'] > 0 ? round($idr / $rates['CNY'], 2) : 2250,
					'GBP' => isset($rates['GBP']) && $rates['GBP'] > 0 ? round($idr / $rates['GBP'], 2) : 20800,
					'AUD' => isset($rates['AUD']) && $rates['AUD'] > 0 ? round($idr / $rates['AUD'], 2) : 10500,
				];

				$this->response([
					'success' => true,
					'provider' => 'ExchangeRate-API (open.er-api.com)',
					'last_update' => $data['time_last_update_utc'] ?? date('r'),
					'rates' => $calculated
				]);
				return;
			}
		}

		$this->response(['success' => false, 'message' => 'Gagal mengambil data kurs dari API']);
	}

	public function export_pdf()
	{
		$this->is_allowed('purchase_order_export');
		$this->model_purchase_order->pdf('purchase_order', 'purchase_order');
	}

}
