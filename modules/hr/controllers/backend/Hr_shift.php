<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_shift extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_hr_shift');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('hr_shift_list');
		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['hr_shifts'] = $this->model_hr_shift->get($filter, $field, $this->limit_page, $offset);
		$this->data['hr_shift_counts'] = $this->model_hr_shift->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/hr_shift/index/',
			'total_rows'   => $this->data['hr_shift_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Master Shift List');
		$this->render('backend/standart/administrator/hr_shift/hr_shift_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('hr_shift_add');
		$this->template->title('Tambah Shift Baru');
		$this->render('backend/standart/administrator/hr_shift/hr_shift_add', $this->data);
	}

	public function add_save()
	{
		$this->is_allowed('hr_shift_add');
		
		$this->form_validation->set_rules('nama_shift', 'Nama Shift', 'trim|required');
		$this->form_validation->set_rules('jam_masuk', 'Jam Masuk', 'trim|required');
		$this->form_validation->set_rules('jam_pulang', 'Jam Pulang', 'trim|required');
		
		if ($this->form_validation->run()) {
			$save_data = [
				'nama_shift' => $this->input->post('nama_shift'),
				'jam_masuk' => $this->input->post('jam_masuk'),
				'jam_pulang' => $this->input->post('jam_pulang'),
				'created_at' => date('Y-m-d H:i:s')
			];
			$save_hr_shift = $this->model_hr_shift->store($save_data);
			if ($save_hr_shift) {
				set_message('Data Shift berhasil ditambahkan.', 'success');
				echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_shift')]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function edit($id)
	{
		$this->is_allowed('hr_shift_update');
		$this->data['hr_shift'] = $this->model_hr_shift->find($id);
		$this->template->title('Edit Shift');
		$this->render('backend/standart/administrator/hr_shift/hr_shift_update', $this->data);
	}

	public function edit_save($id)
	{
		$this->is_allowed('hr_shift_update');

		$this->form_validation->set_rules('nama_shift', 'Nama Shift', 'trim|required');
		$this->form_validation->set_rules('jam_masuk', 'Jam Masuk', 'trim|required');
		$this->form_validation->set_rules('jam_pulang', 'Jam Pulang', 'trim|required');

		if ($this->form_validation->run()) {
			$save_data = [
				'nama_shift' => $this->input->post('nama_shift'),
				'jam_masuk' => $this->input->post('jam_masuk'),
				'jam_pulang' => $this->input->post('jam_pulang'),
				'updated_at' => date('Y-m-d H:i:s')
			];
			
			$save_hr_shift = $this->model_hr_shift->change($id, $save_data);
			if ($save_hr_shift) {
				set_message('Data Shift berhasil diperbarui.', 'success');
				echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_shift')]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function delete($id = null)
	{
		$this->is_allowed('hr_shift_delete');
		$remove = $this->model_hr_shift->remove($id);
		if ($remove) {
			set_message('Data Shift berhasil dihapus.', 'success');
		} else {
			set_message('Gagal menghapus data.', 'error');
		}
		redirect('administrator/hr_shift');
	}
}
