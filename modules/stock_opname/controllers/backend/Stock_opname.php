<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Stock_opname extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_stock_opname');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('stock_opname_list');
		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['opnames']      = $this->model_stock_opname->get($filter, $field, $this->limit_page, $offset);
		$this->data['opname_counts'] = $this->model_stock_opname->count_all($filter, $field);

		$config = ['base_url' => 'administrator/stock_opname/index/', 'total_rows' => $this->data['opname_counts'], 'per_page' => $this->limit_page, 'uri_segment' => 4];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Stock Opname');
		$this->render('backend/standart/administrator/stock_opname/stock_opname_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('stock_opname_add');
		$this->data['nomor_baru'] = $this->model_stock_opname->generate_nomor();
		$this->data['gudangs']    = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->template->title('Buat Stock Opname');
		$this->render('backend/standart/administrator/stock_opname/stock_opname_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('stock_opname_add', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('gudang_id', 'Gudang', 'trim|required');
		$this->form_validation->set_rules('tanggal_opname', 'Tanggal Opname', 'trim|required');

		if ($this->form_validation->run()) {
			$data = [
				'nomor_opname'   => $this->model_stock_opname->generate_nomor(),
				'gudang_id'      => $this->input->post('gudang_id'),
				'tanggal_opname' => $this->input->post('tanggal_opname'),
				'jadwal'         => $this->input->post('jadwal') ?: 'bulanan',
				'status'         => 'draft',
				'catatan'        => $this->input->post('catatan'),
				'created_by'     => get_user_data('username'),
				'created_at'     => date('Y-m-d H:i:s'),
			];

			$opname_id = $this->model_stock_opname->store($data);
			if ($opname_id) {
				$this->model_stock_opname->load_stok_sistem($opname_id);
				set_message('Stock Opname berhasil dibuat. Stok sistem sudah dimuat.', 'success');
				$this->data['success']  = true;
				$this->data['redirect'] = base_url('administrator/stock_opname/edit/' . $opname_id);
			} else {
				$this->data['success'] = false;
				$this->data['message'] = 'Gagal membuat opname.';
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function edit($id)
	{
		$this->is_allowed('stock_opname_update');
		$this->data['opname'] = $this->model_stock_opname->find_with_join($id);
		$this->data['items']  = $this->model_stock_opname->get_items($id);
		$this->template->title('Input Stok Fisik');
		$this->render('backend/standart/administrator/stock_opname/stock_opname_input', $this->data);
	}

	public function save_fisik($id)
	{
		if (!$this->is_allowed('stock_opname_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		if ($this->input->post('items')) {
			$res = $this->model_stock_opname->save_fisik_items($id, $this->input->post('items'));
			if (is_array($res) && isset($res['success']) && !$res['success']) {
				$this->response($res);
				return;
			}
		}

		set_message('Data stok fisik berhasil disimpan.', 'success');
		$this->data['success']  = true;
		$this->data['redirect'] = base_url('administrator/stock_opname/view/' . $id);
		$this->response($this->data);
	}

	public function view($id)
	{
		$this->is_allowed('stock_opname_view');
		$this->data['opname'] = $this->model_stock_opname->find_with_join($id);
		$this->data['items']  = $this->model_stock_opname->get_items($id);
		$this->template->title('Detail Stock Opname');
		$this->render('backend/standart/administrator/stock_opname/stock_opname_view', $this->data);
	}

	public function approve($id)
	{
		$this->is_allowed('stock_opname_approve');
		$opname = $this->model_stock_opname->find($id);
		if ($opname && in_array($opname->status, ['review', 'draft'])) {
			// Threshold check: Penyesuaian stok > Rp 5.000.000 memerlukan wewenang manajer/admin
			$threshold = 5000000;
			if ($opname->total_selisih_nilai >= $threshold && !$this->aauth->is_admin()) {
				set_message('Penyesuaian stok di atas Rp 5.000.000 (Nominal: Rp ' . number_format($opname->total_selisih_nilai, 0, ',', '.') . ') memerlukan approval dari Administrator / Manajer!', 'error');
				redirect('administrator/stock_opname/view/' . $id);
				return;
			}
			$this->model_stock_opname->approve($id, get_user_data('username'));
			set_message('Stock Opname berhasil diapprove. Saldo stok telah disesuaikan.', 'success');
		} else {
			set_message('Opname harus berstatus Review / Draft untuk diapprove.', 'warning');
		}
		redirect('administrator/stock_opname/view/' . $id);
	}

	public function delete($id = null)
	{
		$this->is_allowed('stock_opname_delete');
		$op = $this->model_stock_opname->find($id);
		if ($op && $op->status == 'draft') {
			$this->db->delete('stock_opname_item', ['opname_id' => $id]);
			$this->model_stock_opname->remove($id);
			set_message('Opname berhasil dihapus.', 'success');
		} else {
			set_message('Hanya opname berstatus Draft yang bisa dihapus.', 'error');
		}
		redirect('administrator/stock_opname');
	}

	public function laporan()
	{
		$this->is_allowed('stock_opname_list');
		$gudang_id     = $this->input->get('gudang_id');
		$produk_id     = $this->input->get('produk_id');
		$date_start    = $this->input->get('date_start');
		$date_end      = $this->input->get('date_end');
		$hanya_selisih = $this->input->get('hanya_selisih') != '0';

		$this->data['laporan']    = $this->model_stock_opname->get_laporan_selisih($gudang_id, $hanya_selisih, $produk_id, $date_start, $date_end);
		$this->data['gudangs']    = $this->db->get('gudang')->result();
		$this->data['produks']    = $this->db->select('id, nama_produk, kode_produk')->get('produk')->result();
		$this->data['gudang_sel'] = $gudang_id;
		$this->data['produk_sel'] = $produk_id;
		$this->data['date_start'] = $date_start;
		$this->data['date_end']   = $date_end;

		$this->template->title('Laporan Selisih Stock Opname');
		$this->render('backend/standart/administrator/stock_opname/stock_opname_laporan', $this->data);
	}

	public function cycle_count()
	{
		$this->is_allowed('stock_opname_add');
		$gudang_id = $this->input->get('gudang_id');

		if ($gudang_id) {
			$opname_id = $this->model_stock_opname->create_cycle_count($gudang_id, get_user_data('username'));
			set_message('Cycle count harian berhasil dibuat untuk produk fast moving.', 'success');
			redirect('administrator/stock_opname/edit/' . $opname_id);
		}

		$this->data['gudangs'] = $this->db->where('status', 'aktif')->get('gudang')->result();
		$this->template->title('Cycle Counting Harian');
		$this->render('backend/standart/administrator/stock_opname/stock_opname_cycle_count', $this->data);
	}

	public function cetak($id)
	{
		$this->is_allowed('stock_opname_view');
		$this->data['opname'] = $this->model_stock_opname->find_with_join($id);
		$this->data['items']  = $this->model_stock_opname->get_items($id);

		$this->load->library('HtmlPdf');
		$this->htmlpdf->initialize(['orientation' => 'P', 'format' => 'A4', 'marges' => [5, 5, 5, 5]]);
		$content = $this->load->view('backend/standart/administrator/stock_opname/stock_opname_pdf', $this->data, true);

		$this->htmlpdf->writeHTML($content);
		$doc_num = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->data['opname']->nomor_opname ?? $id);
		$filename = 'stock_opname_' . $doc_num . '.pdf';
		if (ob_get_length()) { ob_clean(); }
		$this->htmlpdf->Output($filename, 'D');
	}

	public function export()
	{
		$this->is_allowed('stock_opname_export');
		$this->model_stock_opname->export('stock_opname', 'stock_opname');
	}

	public function export_pdf()
	{
		$this->is_allowed('stock_opname_export');
		$this->model_stock_opname->pdf('stock_opname', 'stock_opname');
	}

}
