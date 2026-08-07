<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Invoice extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_invoice');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('invoice_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['invoices']      = $this->model_invoice->get($filter, $field, $this->limit_page, $offset);
		$this->data['invoice_counts'] = $this->model_invoice->count_all($filter, $field);

		$config = [
			'base_url'    => 'administrator/invoice/index/',
			'total_rows'  => $this->data['invoice_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->data['produks'] = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->data['sales_orders'] = $this->db->select('sales_order.*, pelanggan.nama_pelanggan, gudang.nama_gudang')
			->join('pelanggan', 'pelanggan.id = sales_order.pelanggan_id', 'LEFT')
			->join('gudang', 'gudang.id = sales_order.gudang_id', 'LEFT')
			->order_by('sales_order.id', 'DESC')
			->get('sales_order')->result();

		$this->template->title('Invoice');
		$this->render('backend/standart/administrator/invoice/invoice_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('invoice_add');
		$so_id = $this->input->get('so_id');
		if ($so_id) {
			redirect('administrator/invoice/from_so/' . $so_id);
			return;
		}

		$first_so = $this->db->order_by('id', 'DESC')->get('sales_order')->row();
		if ($first_so) {
			redirect('administrator/invoice/from_so/' . $first_so->id);
			return;
		}

		set_message('Belum ada Sales Order. Silakan buat Sales Order terlebih dahulu.', 'warning');
		redirect('administrator/sales_order/add');
	}

	public function from_so($so_id)
	{
		$this->is_allowed('invoice_add');
		$this->load->model('sales_order/model_sales_order');
		$this->data['so']         = $this->model_sales_order->find_with_detail($so_id);
		$this->data['so_items']   = $this->model_sales_order->get_items($so_id);
		$this->data['nomor_baru'] = $this->model_invoice->generate_nomor();
		$this->data['gudangs']    = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->data['produks']    = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->template->title('Buat Invoice dari SO');
		$this->render('backend/standart/administrator/invoice/invoice_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('invoice_add', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('pelanggan_id', 'Pelanggan', 'trim|required');
		$this->form_validation->set_rules('tanggal_invoice', 'Tanggal Invoice', 'trim|required');

		if ($this->form_validation->run()) {
				$so_id = $this->input->post('so_id') ?: null;
				$gudang_id = $this->input->post('gudang_id') ?: null;
				if (empty($gudang_id) && !empty($so_id)) {
					$so_rec = $this->db->get_where('sales_order', ['id' => $so_id])->row();
					if ($so_rec && !empty($so_rec->gudang_id)) {
						$gudang_id = $so_rec->gudang_id;
					}
				}
				if (empty($gudang_id)) {
					$first_g = $this->db->get('gudang')->row();
					$gudang_id = $first_g ? $first_g->id : 1;
				}

				$data = [
					'nomor_invoice'     => $this->model_invoice->generate_nomor(),
					'so_id'             => $so_id,
					'pelanggan_id'      => $this->input->post('pelanggan_id'),
					'gudang_id'         => $gudang_id,
					'tanggal_invoice'   => $this->input->post('tanggal_invoice'),
				'tanggal_jatuh_tempo' => $this->input->post('tanggal_jatuh_tempo') ?: null,
				'nomor_resi'        => $this->input->post('nomor_resi'),
				'ekspedisi'         => $this->input->post('ekspedisi'),
				'kurir_ekspedisi'   => $this->input->post('ekspedisi'),
				'diskon_total'      => str_replace(['.', ','], '', $this->input->post('diskon_total')) ?: 0,
				'pajak_persen'      => $this->input->post('pajak_persen') ?: 0,
				'status'            => 'draft',
				'catatan'           => $this->input->post('catatan'),
				'created_by'        => get_user_data('username'),
				'created_at'        => date('Y-m-d H:i:s'),
			];

			$invoice_id = $this->model_invoice->store($data);

			if ($invoice_id && $this->input->post('items')) {
				$subtotal_total = 0;
				foreach ($this->input->post('items') as $item) {
					if (empty($item['produk_id'])) continue;
					$qty     = max(1, (int) round($item['qty']));
					$harga   = (float) str_replace(['.', ','], '', $item['harga_satuan']);
					$diskon  = (float) $item['diskon_persen'];
					$d_nom   = $harga * $qty * ($diskon / 100);
					$subtotal = ($harga * $qty) - $d_nom;
					$subtotal_total += $subtotal;

					$this->db->insert('invoice_item', [
						'invoice_id'    => $invoice_id,
						'produk_id'     => $item['produk_id'],
						'so_item_id'    => $item['so_item_id'] ?: null,
						'qty'           => $qty,
						'satuan'        => $item['satuan'],
						'harga_satuan'  => $harga,
						'diskon_persen' => $diskon,
						'diskon_nominal'=> $d_nom,
						'subtotal'      => $subtotal,
					]);
				}

				$diskon_tot = (float) str_replace(['.', ','], '', $this->input->post('diskon_total'));
				if ($diskon_tot > $subtotal_total && $subtotal_total > 0) {
					$diskon_tot = $subtotal_total * (min(100, $diskon_tot) / 100);
				}
				$pajak      = (float) ($this->input->post('pajak_persen') ?: 0);
				$after_disc = max(0, $subtotal_total - $diskon_tot);
				$pajak_nom  = $after_disc * ($pajak / 100);
				$total      = $after_disc + $pajak_nom;

				$this->db->update('invoice', [
					'subtotal'     => $subtotal_total,
					'pajak_nominal'=> $pajak_nom,
					'total'        => $total,
				], ['id' => $invoice_id]);
			}

			set_message('Invoice berhasil dibuat.', 'success');
			$this->data['success']  = true;
			$this->data['redirect'] = base_url('administrator/invoice');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function view($id)
	{
		$this->is_allowed('invoice_view');
		$this->data['invoice'] = $this->model_invoice->join_available()->find($id);
		$this->data['items']   = $this->model_invoice->get_items($id);
		$this->data['produks'] = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->template->title('Detail Invoice');
		$this->render('backend/standart/administrator/invoice/invoice_view', $this->data);
	}

	

	public function kirim($id)
	{
		$this->is_allowed('invoice_update');
		$invoice = $this->model_invoice->find($id);
		if ($invoice) {
			// Kurangi stok saat invoice dikirim
			if (in_array($invoice->status, ['draft', 'sent'])) {
				$this->model_invoice->kurangi_stok($id, get_user_data('username'));
				// Buat piutang
				$this->model_invoice->buat_piutang($id);
				$this->db->update('invoice', [
					'status'       => 'sent',
					'status_kirim' => 'shipped',
					'nomor_resi'   => $this->input->post('nomor_resi') ?: $invoice->nomor_resi,
					'ekspedisi'    => $this->input->post('ekspedisi') ?: $invoice->ekspedisi,
					'updated_at'   => date('Y-m-d H:i:s'),
				], ['id' => $id]);

				// Otomatis update status Sales Order terkait menjadi SHIPPED
				if (!empty($invoice->so_id)) {
					$this->db->update('sales_order', [
						'status'     => 'shipped',
						'updated_at' => date('Y-m-d H:i:s'),
					], ['id' => $invoice->so_id]);
				}

				set_message('Invoice berhasil dikirim. Stok, piutang, dan status Sales Order diperbarui menjadi SHIPPED.', 'success');
			}
		}
		redirect('administrator/invoice/view/' . $id);
	}

	public function update_status_kirim($id)
	{
		$this->is_allowed('invoice_update');
		$status = $this->input->post('status_kirim');
		$this->db->update('invoice', ['status_kirim' => $status, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $id]);
		$this->response(['success' => true]);
	}


	public function edit($id)
	{
		$this->is_allowed('invoice_update');
		$this->data['inv'] = $this->model_invoice->find($id);
		if (!$this->data['inv'] || $this->data['inv']->status != 'draft') {
			set_message('Hanya data berstatus Draft yang bisa diedit.', 'error');
			redirect('administrator/invoice');
		}
		$this->data['pelanggans'] = $this->db->where('status', 'aktif')->get('pelanggan')->result();
		$this->data['sales_orders'] = $this->db->get('sales_order')->result();
		$this->data['items']      = $this->model_invoice->get_items($id);
		$this->data['produks'] = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->template->title('Edit Invoice');
		$this->render('backend/standart/administrator/invoice/invoice_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('invoice_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$existing = $this->model_invoice->find($id);
		if (!$existing || $existing->status != 'draft') {
			$this->response(['success' => false, 'message' => 'Hanya data berstatus Draft yang bisa diedit.']); return;
		}

		$data = [
			'catatan'             => $this->input->post('catatan'),
			'updated_at'          => date('Y-m-d H:i:s'),
			'updated_by'          => get_user_data('username'),
			'so_id'               => $this->input->post('so_id') ?: null,
			'pelanggan_id'        => $this->input->post('pelanggan_id'),
			'nomor_resi'          => $this->input->post('nomor_resi'),
			'ekspedisi'           => $this->input->post('ekspedisi'),
			'kurir_ekspedisi'     => $this->input->post('ekspedisi'),
			'tanggal_invoice'     => $this->input->post('tanggal_invoice'),
			'tanggal_jatuh_tempo' => $this->input->post('tanggal_jatuh_tempo') ?: null,
			'diskon_total'        => (float) str_replace(['.', ','], '', $this->input->post('diskon_total')),
			'pajak_persen'        => (float) $this->input->post('pajak_persen'),
		];

		$this->model_invoice->change($id, $data);

		// Update items: delete old and insert new
		$this->db->delete('invoice_item', ['invoice_id' => $id]);
		$subtotal_total = 0;
		if ($this->input->post('items')) {
			foreach ($this->input->post('items') as $item) {
				if (empty($item['produk_id'])) continue;
				$qty     = max(1, (int) round($item['qty']));
				$harga   = (float) str_replace(['.', ','], '', $item['harga_satuan']);
				$diskon  = isset($item['diskon_persen']) ? (float) $item['diskon_persen'] : 0;
				$d_nom   = $harga * $qty * ($diskon / 100);
				$subtotal = ($harga * $qty) - $d_nom;
				$subtotal_total += $subtotal;
				$this->db->insert('invoice_item', [
					'invoice_id'    => $id,
					'produk_id'     => $item['produk_id'],
					'so_item_id'    => !empty($item['so_item_id']) ? $item['so_item_id'] : null,
					'qty'           => $qty,
					'satuan'        => $item['satuan'] ?? 'pcs',
					'harga_satuan'  => $harga,
					'diskon_persen' => $diskon,
					'diskon_nominal'=> $d_nom,
					'subtotal'      => $subtotal,
				]);
			}
		}

		// Recalculate totals
		$diskon_tot = (float) str_replace(['.', ','], '', $this->input->post('diskon_total'));
		if ($diskon_tot > $subtotal_total && $subtotal_total > 0) {
			$diskon_tot = $subtotal_total * (min(100, $diskon_tot) / 100);
		}
		$pajak      = (float) ($this->input->post('pajak_persen') ?: 0);
		$after_disc = max(0, $subtotal_total - $diskon_tot);
		$pajak_nom  = $after_disc * ($pajak / 100);
		$total      = $after_disc + $pajak_nom;
		$this->db->update('invoice', [
			'subtotal'      => $subtotal_total,
			'pajak_nominal' => $pajak_nom,
			'total'         => $total,
		], ['id' => $id]);

		set_message('Invoice berhasil diupdate.', 'success');
		$this->data['success']  = true;
		$this->data['redirect'] = base_url('administrator/invoice');
		$this->response($this->data);
	}

	public function delete($id = null)
	{
		$this->is_allowed('invoice_delete');
		$inv = $this->model_invoice->find($id);
		if ($inv && $inv->status == 'draft') {
			$this->db->delete('invoice_item', ['invoice_id' => $id]);
			$this->model_invoice->remove($id);
			set_message('Invoice berhasil dihapus.', 'success');
		} else {
			set_message('Hanya invoice berstatus Draft yang bisa dihapus.', 'error');
		}
		redirect('administrator/invoice');
	}

	public function export()
	{
		$this->is_allowed('invoice_export');
		$this->model_invoice->export('invoice', 'invoice');
	}

	

	

	public function cetak($id)
	{
		$this->is_allowed('invoice_view');
		$this->data['invoice'] = $this->model_invoice->join_available()->find($id);
		$this->data['items'] = $this->model_invoice->get_items($id);
		
		$this->load->library('HtmlPdf');
		$this->htmlpdf->initialize(['orientation' => 'P', 'format' => 'A4', 'marges' => [5, 5, 5, 5]]);
		$content = $this->load->view('backend/standart/administrator/invoice/invoice_pdf', $this->data, true);
		
		$this->htmlpdf->writeHTML($content);
		$invoice = $this->data['invoice'];
		$doc_num = preg_replace('/[^a-zA-Z0-9_-]/', '_', $invoice->nomor_invoice ?? $id);
		$filename = 'invoice_' . $doc_num . '.pdf';
		if (ob_get_length()) { ob_clean(); }
		$this->htmlpdf->Output($filename, 'D');
	}

	public function print_invoice($id)
	{
		$this->cetak($id);
	}

	public function export_pdf()
	{
		$this->is_allowed('invoice_export');
		$this->model_invoice->pdf('invoice', 'invoice');
	}

}
