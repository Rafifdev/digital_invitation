<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_karyawan extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_hr_karyawan');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('hr_karyawan_list');
		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['hr_karyawans'] = $this->model_hr_karyawan->get($filter, $field, $this->limit_page, $offset);
		$this->data['hr_karyawan_counts'] = $this->model_hr_karyawan->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/hr_karyawan/index/',
			'total_rows'   => $this->data['hr_karyawan_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Karyawan List');
		$this->render('backend/standart/administrator/hr_karyawan/hr_karyawan_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('hr_karyawan_add');
		$this->data['users'] = $this->db->get('aauth_users')->result();
		$this->template->title('Karyawan New');
		$this->render('backend/standart/administrator/hr_karyawan/hr_karyawan_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('hr_karyawan_add', false)) {
			echo json_encode(['success' => false, 'message' => 'Tidak ada akses']);
			return;
		}

		$this->form_validation->set_rules('user_id', 'User Akun', 'trim|required');
		$this->form_validation->set_rules('nik', 'NIK', 'trim|required');
		
		if ($this->form_validation->run()) {
			$save_data = [
				'user_id' => $this->input->post('user_id'),
				'nik' => $this->input->post('nik'),
				'jabatan' => $this->input->post('jabatan'),
				'departemen' => $this->input->post('departemen'),
				'tipe_kerja' => $this->input->post('tipe_kerja'),
				'created_at' => date('Y-m-d H:i:s')
			];
			$save_hr_karyawan = $this->model_hr_karyawan->store($save_data);
			if ($save_hr_karyawan) {
				set_message('Data Karyawan berhasil ditambahkan.', 'success');
				echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_karyawan')]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function edit($id)
	{
		$this->is_allowed('hr_karyawan_update');
		$this->data['hr_karyawan'] = $this->model_hr_karyawan->find($id);
		$this->data['users'] = $this->db->get('aauth_users')->result();
		$this->template->title('Karyawan Update');
		$this->render('backend/standart/administrator/hr_karyawan/hr_karyawan_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('hr_karyawan_update', false)) {
			echo json_encode(['success' => false, 'message' => 'Tidak ada akses']);
			return;
		}
		
		$this->form_validation->set_rules('user_id', 'User Akun', 'trim|required');
		$this->form_validation->set_rules('nik', 'NIK', 'trim|required');

		if ($this->form_validation->run()) {
			$save_data = [
				'user_id' => $this->input->post('user_id'),
				'nik' => $this->input->post('nik'),
				'jabatan' => $this->input->post('jabatan'),
				'departemen' => $this->input->post('departemen'),
				'tipe_kerja' => $this->input->post('tipe_kerja'),
				'updated_at' => date('Y-m-d H:i:s')
			];
			$save_hr_karyawan = $this->model_hr_karyawan->change($id, $save_data);
			if ($save_hr_karyawan) {
				set_message('Data Karyawan berhasil diperbarui.', 'success');
				echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_karyawan')]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Gagal mengupdate data']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function view($id)
	{
		$this->is_allowed('hr_karyawan_view');
		$this->data['hr_karyawan'] = $this->model_hr_karyawan->join_available()->find_with_detail($id);
		$this->template->title('Karyawan Detail');
		$this->render('backend/standart/administrator/hr_karyawan/hr_karyawan_view', $this->data);
	}

	public function delete($id = null)
	{
		$this->is_allowed('hr_karyawan_delete');
		$remove = $this->model_hr_karyawan->remove($id);
		if ($remove) {
			set_message('Karyawan berhasil dihapus.', 'success');
		} else {
			set_message('Gagal menghapus data.', 'error');
		}
		redirect('administrator/hr_karyawan');
	}

    public function reset_device($id)
    {
        $this->is_allowed('hr_karyawan_update');
        $this->model_hr_karyawan->change($id, ['device_id' => NULL]);
        set_message('Device Lock Karyawan berhasil di-reset.', 'success');
        redirect('administrator/hr_karyawan');
    }
}
