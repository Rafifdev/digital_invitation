<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Satuan extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_satuan');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('satuan_list');
		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['satuans']       = $this->model_satuan->get($filter, $field, $this->limit_page, $offset);
		$this->data['satuan_counts'] = $this->model_satuan->count_all($filter, $field);

		$config = [
			'base_url'    => 'administrator/satuan/index/',
			'total_rows'  => $this->data['satuan_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Satuan / Unit Barang');
		$this->render('backend/standart/administrator/satuan/satuan_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('satuan_add');
		$this->template->title('Tambah Satuan Barang');
		$this->render('backend/standart/administrator/satuan/satuan_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('satuan_add', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('nama_satuan', 'Nama Satuan', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('singkatan', 'Singkatan', 'trim|required|max_length[20]');

		if ($this->form_validation->run()) {
			$this->db->insert('satuan', [
				'kode_satuan' => $this->input->post('kode_satuan'),
				'nama_satuan' => $this->input->post('nama_satuan'),
				'singkatan'   => $this->input->post('singkatan'),
				'deskripsi'   => $this->input->post('deskripsi'),
				'created_at'  => date('Y-m-d H:i:s'),
			]);
			set_message('Satuan Barang berhasil ditambahkan.', 'success');
			$this->response(['success' => true, 'redirect' => site_url('administrator/satuan')]);
		} else {
			$this->response(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function edit($id)
	{
		$this->is_allowed('satuan_update');
		$this->data['satuan'] = $this->db->get_where('satuan', ['id' => $id])->row();
		if (!$this->data['satuan']) {
			show_404();
		}
		$this->template->title('Edit Satuan Barang');
		$this->render('backend/standart/administrator/satuan/satuan_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('satuan_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('nama_satuan', 'Nama Satuan', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('singkatan', 'Singkatan', 'trim|required|max_length[20]');

		if ($this->form_validation->run()) {
			$this->db->update('satuan', [
				'kode_satuan' => $this->input->post('kode_satuan'),
				'nama_satuan' => $this->input->post('nama_satuan'),
				'singkatan'   => $this->input->post('singkatan'),
				'deskripsi'   => $this->input->post('deskripsi'),
				'updated_at'  => date('Y-m-d H:i:s'),
			], ['id' => $id]);
			set_message('Satuan Barang berhasil diperbarui.', 'success');
			$this->response(['success' => true, 'redirect' => site_url('administrator/satuan')]);
		} else {
			$this->response(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function delete($id)
	{
		$this->is_allowed('satuan_delete');
		$this->db->delete('satuan', ['id' => $id]);
		set_message('Satuan Barang berhasil dihapus.', 'success');
		redirect('administrator/satuan');
	}

	public function export()
	{
		$this->is_allowed('satuan_export');
		$this->model_satuan->export('satuan', 'satuan');
	}

	public function export_pdf()
	{
		$this->is_allowed('satuan_export');
		$this->model_satuan->pdf('satuan', 'satuan');
	}
}

/* End of file Satuan.php */
/* Location: ./modules/satuan/controllers/backend/Satuan.php */
