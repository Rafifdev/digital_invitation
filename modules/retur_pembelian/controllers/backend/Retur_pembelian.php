<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Retur_pembelian extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_retur_pembelian');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('retur_pembelian_list');
		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['returs']       = $this->model_retur_pembelian->get($filter, $field, $this->limit_page, $offset);
		$this->data['retur_counts'] = $this->model_retur_pembelian->count_all($filter, $field);

		$config = ['base_url' => 'administrator/retur_pembelian/index/', 'total_rows' => $this->data['retur_counts'], 'per_page' => $this->limit_page, 'uri_segment' => 4];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Retur Pembelian');
		$this->render('backend/standart/administrator/retur_pembelian/retur_pembelian_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('retur_pembelian_add');
		$this->data['nomor_baru']             = $this->model_retur_pembelian->generate_nomor();
		$this->data['penerimaans']            = $this->db->select('penerimaan_barang.*, purchase_order.nomor_po')
			->from('penerimaan_barang')
			->join('purchase_order', 'purchase_order.id = penerimaan_barang.po_id', 'LEFT')
			->get()->result();
		$this->data['suppliers']             = $this->db->where('status', 'aktif')->get('supplier')->result();
		$this->data['gudangs']                = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->data['produks']                = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->data['selected_penerimaan_id'] = $this->input->get('penerimaan_id');
		$this->template->title('Buat Retur Pembelian');
		$this->render('backend/standart/administrator/retur_pembelian/retur_pembelian_add', $this->data);
	}

	public function get_items()
	{
		$penerimaan_id = $this->input->get('penerimaan_id');
		$penerimaan = $this->db->select('penerimaan_barang.*, supplier.nama_supplier, gudang.nama_gudang')
			->from('penerimaan_barang')
			->join('supplier', 'supplier.id = penerimaan_barang.supplier_id', 'left')
			->join('gudang', 'gudang.id = penerimaan_barang.gudang_id', 'left')
			->where('penerimaan_barang.id', $penerimaan_id)
			->get()->row();

		$items = $this->db->select('penerimaan_barang_item.*, produk.nama_produk, produk.kode_produk, produk.harga_beli')
			->from('penerimaan_barang_item')
			->join('produk', 'produk.id = penerimaan_barang_item.produk_id', 'left')
			->where('penerimaan_id', $penerimaan_id)
			->get()->result();

		foreach ($items as &$it) {
			$it->qty_terima = (int) round((float) $it->qty_terima);
			$it->qty_baik   = (int) round((float) $it->qty_baik);
			$it->qty_rusak  = (int) round((float) $it->qty_rusak);
		}

		$this->response(['success' => true, 'penerimaan' => $penerimaan, 'items' => $items]);
	}

	public function add_save()
	{
		if (!$this->is_allowed('retur_pembelian_add', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$penerimaan_id = $this->input->post('penerimaan_id');
		
		$this->form_validation->set_rules('alasan', 'Alasan Retur', 'trim|required');
		$this->form_validation->set_rules('tanggal_retur', 'Tanggal Retur', 'trim|required');

		if (empty($penerimaan_id)) {
			$this->form_validation->set_rules('supplier_id', 'Supplier', 'trim|required');
			$this->form_validation->set_rules('gudang_id', 'Gudang', 'trim|required');
		}

		if ($this->form_validation->run()) {
			$supplier_id = $this->input->post('supplier_id');
			$gudang_id   = $this->input->post('gudang_id');

			if (!empty($penerimaan_id)) {
				$penerimaan = $this->db->get_where('penerimaan_barang', ['id' => $penerimaan_id])->row();
				if ($penerimaan) {
					$supplier_id = $penerimaan->supplier_id;
					$gudang_id   = $penerimaan->gudang_id;
				}
			}

			$data = [
				'nomor_retur'      => $this->model_retur_pembelian->generate_nomor(),
				'penerimaan_id'    => !empty($penerimaan_id) ? $penerimaan_id : null,
				'supplier_id'      => $supplier_id,
				'gudang_id'        => $gudang_id,
				'tanggal_retur'    => $this->input->post('tanggal_retur'),
				'alasan'           => $this->input->post('alasan'),
				'status'           => 'draft',
				'created_by'       => get_user_data('username'),
				'created_at'       => date('Y-m-d H:i:s'),
			];

			$retur_id = $this->model_retur_pembelian->store($data);
			$total = 0;

			if ($retur_id && $this->input->post('items')) {
				foreach ($this->input->post('items') as $item) {
					if (empty($item['produk_id'])) continue;
					$qty    = max(1, (int) round($item['qty_retur']));
					$harga  = (float) str_replace(['.', ','], '', $item['harga_satuan']);
					$sub    = $qty * $harga;
					$total += $sub;
					$this->db->insert('retur_pembelian_item', [
						'retur_pembelian_id' => $retur_id,
						'produk_id'          => $item['produk_id'],
						'qty_retur'          => $qty,
						'harga_satuan'       => $harga,
						'subtotal'           => $sub,
						'alasan'             => isset($item['alasan']) ? $item['alasan'] : '',
					]);
				}
				$this->db->update('retur_pembelian', ['total' => $total], ['id' => $retur_id]);
			}

			set_message('Retur pembelian berhasil dibuat.', 'success');
			$this->data['success']  = true;
			$this->data['redirect'] = base_url('administrator/retur_pembelian');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function view($id)
	{
		$this->is_allowed('retur_pembelian_view');
		$this->data['retur'] = $this->model_retur_pembelian->join_available()->find($id);
		$this->data['items'] = $this->model_retur_pembelian->get_items($id);
		$this->template->title('Detail Retur Pembelian');
		$this->render('backend/standart/administrator/retur_pembelian/retur_pembelian_view', $this->data);
	}

	public function konfirmasi($id)
	{
		$this->is_allowed('retur_pembelian_update');
		$retur = $this->model_retur_pembelian->find($id);
		if ($retur && $retur->status == 'draft') {
			$this->model_retur_pembelian->konfirmasi($id, get_user_data('username'));
			set_message('Retur pembelian dikonfirmasi. Stok telah diperbarui.', 'success');
		}
		redirect('administrator/retur_pembelian/view/' . $id);
	}


	public function edit($id)
	{
		$this->is_allowed('retur_pembelian_update');
		$this->data['retur'] = $this->model_retur_pembelian->find($id);
		if (!$this->data['retur'] || $this->data['retur']->status != 'draft') {
			set_message('Hanya data berstatus Draft yang bisa diedit.', 'error');
			redirect('administrator/retur_pembelian');
		}
		$this->data['penerimaans'] = $this->db->select('penerimaan_barang.*, purchase_order.nomor_po')
			->from('penerimaan_barang')
			->join('purchase_order', 'purchase_order.id = penerimaan_barang.po_id', 'LEFT')
			->get()->result();
		$this->data['suppliers']  = $this->db->where('status', 'aktif')->get('supplier')->result();
		$this->data['gudangs']    = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->data['pos']        = $this->db->get('purchase_order')->result();
		$this->data['produks']    = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->data['items']      = $this->model_retur_pembelian->get_items($id);
		$this->template->title('Edit Retur Pembelian');
		$this->render('backend/standart/administrator/retur_pembelian/retur_pembelian_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('retur_pembelian_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$existing = $this->model_retur_pembelian->find($id);
		if (!$existing || $existing->status != 'draft') {
			$this->response(['success' => false, 'message' => 'Hanya data berstatus Draft yang bisa diedit.']); return;
		}

		$data = [
			'catatan'    => $this->input->post('catatan'),
			'updated_at' => date('Y-m-d H:i:s'),
			'updated_by' => get_user_data('username'),
		];
		$data['po_id'] = $this->input->post('po_id');
		$data['supplier_id'] = $this->input->post('supplier_id');
		$data['tanggal_retur'] = $this->input->post('tanggal_retur');
		$data['alasan'] = $this->input->post('alasan');
		$data['tindak_lanjut'] = $this->input->post('tindak_lanjut');

		$this->model_retur_pembelian->change($id, $data);

		// Update items: delete old and insert new
		$this->db->delete('retur_pembelian_item', ['retur_id' => $id]);
		if ($this->input->post('items')) {
			foreach ($this->input->post('items') as $item) {
				if (empty($item['produk_id'])) continue;
				$qty     = max(1, (int) round($item['qty'] ?? ($item['qty_retur'] ?? 1)));
				$harga   = (float) str_replace(['.', ','], '', $item['harga_satuan']);
				$diskon  = isset($item['diskon_persen']) ? (float) $item['diskon_persen'] : 0;
				$d_nom   = $harga * $qty * ($diskon / 100);
				$subtotal = ($harga * $qty) - $d_nom;
				$this->db->insert('retur_pembelian_item', [
					'retur_id' => $id,
					'produk_id'     => $item['produk_id'],
					'qty'          => $qty,
					'satuan'        => $item['satuan'] ?? 'pcs',
					'harga_satuan'  => $harga,
					'subtotal'      => $subtotal,
				]);
			}
		}

		set_message('Retur Pembelian berhasil diupdate.', 'success');
		$this->data['success']  = true;
		$this->data['redirect'] = base_url('administrator/retur_pembelian');
		$this->response($this->data);
	}

	public function delete($id = null)
	{
		$this->is_allowed('retur_pembelian_delete');
		$r = $this->model_retur_pembelian->find($id);
		if ($r && $r->status == 'draft') {
			$this->db->delete('retur_pembelian_item', ['retur_pembelian_id' => $id]);
			$this->model_retur_pembelian->remove($id);
			set_message('Retur berhasil dihapus.', 'success');
		} else {
			set_message('Hanya retur berstatus Draft yang bisa dihapus.', 'error');
		}
		redirect('administrator/retur_pembelian');
	}

	

	

	public function cetak($id)
	{
		$this->is_allowed('retur_pembelian_view');
		$this->data['retur'] = $this->model_retur_pembelian->join_available()->find($id);
		$this->data['items'] = $this->model_retur_pembelian->get_items($id);
		
		$this->load->library('HtmlPdf');
		$this->htmlpdf->initialize(['orientation' => 'P', 'format' => 'A4', 'marges' => [5, 5, 5, 5]]);
		$content = $this->load->view('backend/standart/administrator/retur_pembelian/retur_pembelian_pdf', $this->data, true);
		
		$this->htmlpdf->writeHTML($content);
		$doc_num = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->data['retur']->nomor_retur ?? $id);
		$filename = 'retur_pembelian_' . $doc_num . '.pdf';
		if (ob_get_length()) { ob_clean(); }
		$this->htmlpdf->Output($filename, 'D');
	}

	public function export()
	{
		$this->is_allowed('retur_pembelian_export');
		$this->model_retur_pembelian->export('retur_pembelian', 'retur_pembelian');
	}

	public function export_pdf()
	{
		$this->is_allowed('retur_pembelian_export');
		$this->model_retur_pembelian->pdf('retur_pembelian', 'retur_pembelian');
	}

}
