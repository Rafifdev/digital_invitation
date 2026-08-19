<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_kantor extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_hr_kantor');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('hr_kantor_list');
		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['hr_kantors'] = $this->model_hr_kantor->get($filter, $field, $this->limit_page, $offset);
		$this->data['hr_kantor_counts'] = $this->model_hr_kantor->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/hr_kantor/index/',
			'total_rows'   => $this->data['hr_kantor_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Master Data Kantor / Cabang');
		$this->render('backend/standart/administrator/hr_kantor/hr_kantor_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('hr_kantor_add');
		$this->template->title('Tambah Kantor Cabang Baru');
		$this->render('backend/standart/administrator/hr_kantor/hr_kantor_add', $this->data);
	}

	public function add_save()
	{
		$this->is_allowed('hr_kantor_add');
		
		$this->form_validation->set_rules('nama_kantor', 'Nama Kantor', 'trim|required');
		$this->form_validation->set_rules('latitude', 'Latitude', 'trim|required');
		$this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');
		$this->form_validation->set_rules('radius_meter', 'Radius (Meter)', 'trim|required|numeric');
		
		if ($this->form_validation->run()) {
			$save_data = [
				'nama_kantor' => $this->input->post('nama_kantor'),
				'latitude' => $this->input->post('latitude'),
				'longitude' => $this->input->post('longitude'),
				'radius_meter' => $this->input->post('radius_meter'),
				'alamat' => $this->input->post('alamat'),
				'created_at' => date('Y-m-d H:i:s')
			];
			$save_hr_kantor = $this->model_hr_kantor->store($save_data);
			if ($save_hr_kantor) {
				set_message('Data Kantor Cabang berhasil ditambahkan.', 'success');
				echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_kantor')]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function edit($id)
	{
		$this->is_allowed('hr_kantor_update');
		$this->data['hr_kantor'] = $this->model_hr_kantor->find($id);
		$this->template->title('Edit Kantor Cabang');
		$this->render('backend/standart/administrator/hr_kantor/hr_kantor_update', $this->data);
	}

	public function edit_save($id)
	{
		$this->is_allowed('hr_kantor_update');

		$this->form_validation->set_rules('nama_kantor', 'Nama Kantor', 'trim|required');
		$this->form_validation->set_rules('latitude', 'Latitude', 'trim|required');
		$this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');
		$this->form_validation->set_rules('radius_meter', 'Radius (Meter)', 'trim|required|numeric');

		if ($this->form_validation->run()) {
			$save_data = [
				'nama_kantor' => $this->input->post('nama_kantor'),
				'latitude' => $this->input->post('latitude'),
				'longitude' => $this->input->post('longitude'),
				'radius_meter' => $this->input->post('radius_meter'),
				'alamat' => $this->input->post('alamat'),
				'updated_at' => date('Y-m-d H:i:s')
			];
			
			$save_hr_kantor = $this->model_hr_kantor->change($id, $save_data);
			if ($save_hr_kantor) {
				set_message('Data Kantor Cabang berhasil diperbarui.', 'success');
				echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_kantor')]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function delete($id = null)
	{
		$this->is_allowed('hr_kantor_delete');
		$remove = $this->model_hr_kantor->remove($id);
		if ($remove) {
			set_message('Data Kantor Cabang berhasil dihapus.', 'success');
		} else {
			set_message('Gagal menghapus data.', 'error');
		}
		redirect('administrator/hr_kantor');
	}
}
