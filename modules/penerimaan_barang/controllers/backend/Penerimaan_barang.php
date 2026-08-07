<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Penerimaan_barang extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_penerimaan_barang');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('penerimaan_barang_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['penerimaans']      = $this->model_penerimaan_barang->get($filter, $field, $this->limit_page, $offset);
		$this->data['penerimaan_counts'] = $this->model_penerimaan_barang->count_all($filter, $field);

		$config = [
			'base_url'    => 'administrator/penerimaan_barang/index/',
			'total_rows'  => $this->data['penerimaan_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Penerimaan Barang');
		$this->render('backend/standart/administrator/penerimaan_barang/penerimaan_barang_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('penerimaan_barang_add');
		$po_id = $this->input->get('po_id');
		if ($po_id) {
			$existing_draft = $this->db->get_where('penerimaan_barang', ['po_id' => $po_id, 'status' => 'draft'])->row();
			if ($existing_draft) {
				set_message('PO ini memiliki dokumen Penerimaan Barang berstatus DRAFT (#' . $existing_draft->nomor_penerimaan . '). Membuka dokumen draft...', 'warning');
				redirect('administrator/penerimaan_barang/view/' . $existing_draft->id);
				return;
			}
		}

		$this->data['nomor_baru']      = $this->model_penerimaan_barang->generate_nomor();
		$this->data['suppliers']       = $this->db->where('status', 'aktif')->get('supplier')->result();
		$this->data['gudangs']         = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->data['raks']            = $this->db->where('status', 'aktif')->get('rak')->result();
		$this->data['pos']             = $this->db->where_in('status', ['approved', 'partial'])->get('purchase_order')->result();
		$this->data['produks']         = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->data['selected_po_id']  = $po_id;
		$this->template->title('Penerimaan Barang Baru');
		$this->render('backend/standart/administrator/penerimaan_barang/penerimaan_barang_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('penerimaan_barang_add', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('supplier_id', 'Supplier', 'trim|required');
		$this->form_validation->set_rules('gudang_id', 'Gudang', 'trim|required');
		$this->form_validation->set_rules('tanggal_terima', 'Tanggal Terima', 'trim|required');

		if ($this->form_validation->run()) {
			$po_id = $this->input->post('po_id');
			if ($po_id) {
				$existing_draft = $this->db->get_where('penerimaan_barang', ['po_id' => $po_id, 'status' => 'draft'])->row();
				if ($existing_draft) {
					$this->response([
						'success'  => false,
						'message'  => 'PO ini sudah memiliki dokumen Penerimaan Barang berstatus DRAFT (#' . $existing_draft->nomor_penerimaan . '). Mengalihkan ke dokumen draft...',
						'redirect' => base_url('administrator/penerimaan_barang/view/' . $existing_draft->id)
					]);
					return;
				}
			}

			$data = [
				'nomor_penerimaan'  => $this->model_penerimaan_barang->generate_nomor(),
				'po_id'             => $po_id ?: null,
				'supplier_id'       => $this->input->post('supplier_id'),
				'gudang_id'         => $this->input->post('gudang_id'),
				'tanggal_terima'    => $this->input->post('tanggal_terima'),
				'nomor_surat_jalan' => $this->input->post('nomor_surat_jalan'),
				'status'            => 'draft',
				'catatan'           => $this->input->post('catatan'),
				'created_by'        => get_user_data('username'),
				'created_at'        => date('Y-m-d H:i:s'),
			];

			$penerimaan_id = $this->model_penerimaan_barang->store($data);

			if ($penerimaan_id && $this->input->post('items')) {
				foreach ($this->input->post('items') as $item) {
					if (empty($item['produk_id'])) continue;
					$qty_terima = max(1, (int) round($item['qty_terima']));
					$qty_rusak  = (float) ($item['qty_rusak'] ?: 0);
					$qty_baik   = $qty_terima - $qty_rusak;
					$this->db->insert('penerimaan_barang_item', [
						'penerimaan_id' => $penerimaan_id,
						'produk_id'     => $item['produk_id'],
						'rak_id'        => !empty($item['rak_id']) ? $item['rak_id'] : null,
						'po_item_id'    => isset($item['po_item_id']) && !empty($item['po_item_id']) ? $item['po_item_id'] : null,
						'qty_terima'    => $qty_terima,
						'qty_baik'      => $qty_baik,
						'qty_rusak'     => $qty_rusak,
						'satuan'        => $item['satuan'],
						'harga_satuan'  => (float) str_replace(',', '.', str_replace('.', '', $item['harga_satuan'] ?? 0)),
						'batch_no'      => $item['batch_no'],
						'expired_date'  => $item['expired_date'] ?: null,
						'kondisi'       => $item['kondisi'] ?: 'baik',
						'catatan'       => isset($item['catatan']) ? $item['catatan'] : '',
					]);
				}
			}

			set_message('Penerimaan barang berhasil disimpan.', 'success');
			$this->data['success']  = true;
			$this->data['redirect'] = base_url('administrator/penerimaan_barang');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function view($id = null)
	{
		$this->is_allowed('penerimaan_barang_view');
		if (!$id) {
			redirect('administrator/penerimaan_barang');
			return;
		}
		$this->data['penerimaan'] = $this->model_penerimaan_barang->find($id);
		if (!$this->data['penerimaan']) {
			redirect('administrator/penerimaan_barang');
			return;
		}
		$this->data['items']      = $this->model_penerimaan_barang->get_items($id);
		$this->data['penerimaan_detail'] = $this->model_penerimaan_barang->join_available()->find($id);
		$this->template->title('Detail Penerimaan Barang');
		$this->render('backend/standart/administrator/penerimaan_barang/penerimaan_barang_view', $this->data);
	}

	public function konfirmasi($id)
	{
		$this->is_allowed('penerimaan_barang_update');
		$penerimaan = $this->model_penerimaan_barang->find($id);

		if ($penerimaan && $penerimaan->status == 'draft') {
			$result = $this->model_penerimaan_barang->konfirmasi($id, get_user_data('username'));
			if ($result) {
				set_message('Penerimaan dikonfirmasi. Stok dan utang usaha telah diperbarui.', 'success');
			} else {
				set_message('Gagal mengkonfirmasi penerimaan.', 'error');
			}
		} else {
			set_message('Penerimaan sudah dikonfirmasi sebelumnya.', 'warning');
		}
		redirect('administrator/penerimaan_barang/view/' . $id);
	}

	public function get_po_items()
	{
		$po_id = $this->input->get('po_id');
		$po    = $this->db->get_where('purchase_order', ['id' => $po_id])->row();
		$items = $this->db->select('purchase_order_item.*, produk.nama_produk, produk.kode_produk')
			->join('produk', 'produk.id = purchase_order_item.produk_id')
			->get_where('purchase_order_item', ['po_id' => $po_id])->result();
		$this->response(['success' => true, 'po' => $po, 'items' => $items]);
	}


	public function edit($id)
	{
		$this->is_allowed('penerimaan_barang_update');
		$this->data['penerimaan'] = $this->model_penerimaan_barang->find($id);
		if (!$this->data['penerimaan'] || $this->data['penerimaan']->status != 'draft') {
			set_message('Hanya data berstatus Draft yang bisa diedit.', 'error');
			redirect('administrator/penerimaan_barang');
		}
		$this->data['suppliers']   = $this->db->where('status', 'aktif')->get('supplier')->result();
		$this->data['gudangs']     = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->data['raks']        = $this->db->where('status', 'aktif')->get('rak')->result();
		$this->data['produks']     = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->data['pos']         = $this->db->where_in('status', ['approved', 'partial'])->get('purchase_order')->result();
		$this->data['items']       = $this->model_penerimaan_barang->get_items($id);
		$this->template->title('Edit Penerimaan Barang');
		$this->render('backend/standart/administrator/penerimaan_barang/penerimaan_barang_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('penerimaan_barang_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$existing = $this->model_penerimaan_barang->find($id);
		if (!$existing || $existing->status != 'draft') {
			$this->response(['success' => false, 'message' => 'Hanya data berstatus Draft yang bisa diedit.']); return;
		}

		$data = [
			'catatan'           => $this->input->post('catatan'),
			'updated_at'        => date('Y-m-d H:i:s'),
			'updated_by'        => get_user_data('username'),
			'po_id'             => $this->input->post('po_id') ?: null,
			'nomor_surat_jalan' => $this->input->post('nomor_surat_jalan'),
			'tanggal_terima'    => $this->input->post('tanggal_terima'),
			'gudang_id'         => $this->input->post('gudang_id'),
			'supplier_id'       => $this->input->post('supplier_id'),
		];

		$this->model_penerimaan_barang->change($id, $data);

		// Update items: delete old and insert new
		$this->db->delete('penerimaan_barang_item', ['penerimaan_id' => $id]);
		if ($this->input->post('items')) {
			foreach ($this->input->post('items') as $item) {
				if (empty($item['produk_id'])) continue;
				$qty_terima = max(1, (int) round($item['qty_terima']));
				$qty_rusak  = (float) ($item['qty_rusak'] ?? 0);
				$qty_baik   = $qty_terima - $qty_rusak;
				$this->db->insert('penerimaan_barang_item', [
					'penerimaan_id' => $id,
					'produk_id'     => $item['produk_id'],
					'rak_id'        => !empty($item['rak_id']) ? $item['rak_id'] : null,
					'po_item_id'    => !empty($item['po_item_id']) ? $item['po_item_id'] : null,
					'qty_terima'    => $qty_terima,
					'qty_baik'      => $qty_baik,
					'qty_rusak'     => $qty_rusak,
					'satuan'        => $item['satuan'] ?? 'pcs',
					'harga_satuan'  => (float) str_replace(',', '.', str_replace('.', '', $item['harga_satuan'] ?? 0)),
					'batch_no'      => $item['batch_no'] ?? '',
					'expired_date'  => !empty($item['expired_date']) ? $item['expired_date'] : null,
					'kondisi'       => $item['kondisi'] ?? 'baik',
					'catatan'       => $item['catatan'] ?? '',
				]);
			}
		}

		set_message('Penerimaan Barang berhasil diupdate.', 'success');
		$this->data['success']  = true;
		$this->data['redirect'] = base_url('administrator/penerimaan_barang');
		$this->response($this->data);
	}

	public function delete($id = null)
	{
		$this->is_allowed('penerimaan_barang_delete');
		$p = $this->model_penerimaan_barang->find($id);
		if ($p && $p->status == 'draft') {
			$this->db->delete('penerimaan_barang_item', ['penerimaan_id' => $id]);
			$this->model_penerimaan_barang->remove($id);
			set_message('Penerimaan berhasil dihapus.', 'success');
		} else {
			set_message('Hanya penerimaan berstatus Draft yang bisa dihapus.', 'error');
		}
		redirect('administrator/penerimaan_barang');
	}

	

	

	public function cetak($id)
	{
		$this->is_allowed('penerimaan_barang_view');
		$this->data['penerimaan'] = $this->model_penerimaan_barang->join_available()->find($id);
		$this->data['items'] = $this->model_penerimaan_barang->get_items($id);
		
		$this->load->library('HtmlPdf');
		$this->htmlpdf->initialize(['orientation' => 'P', 'format' => 'A4', 'marges' => [5, 5, 5, 5]]);
		$content = $this->load->view('backend/standart/administrator/penerimaan_barang/penerimaan_barang_pdf', $this->data, true);
		
		$this->htmlpdf->writeHTML($content);
		$penerimaan = $this->data['penerimaan'];
		$doc_num = preg_replace('/[^a-zA-Z0-9_-]/', '_', $penerimaan->nomor_penerimaan ?? $id);
		$filename = 'penerimaan_barang_' . $doc_num . '.pdf';
		if (ob_get_length()) { ob_clean(); }
		$this->htmlpdf->Output($filename, 'D');
	}

	public function export()
	{
		$this->is_allowed('penerimaan_barang_export');
		$this->model_penerimaan_barang->export('penerimaan_barang', 'penerimaan_barang');
	}

	public function export_pdf()
	{
		$this->is_allowed('penerimaan_barang_export');
		$this->model_penerimaan_barang->pdf('penerimaan_barang', 'penerimaan_barang');
	}

}
