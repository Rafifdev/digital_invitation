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

		$limit = $this->input->get('limit') ? (int)$this->input->get('limit') : 20;
		$this->data['hr_karyawans'] = $this->model_hr_karyawan->get($filter, $field, $limit, $offset);
		$this->data['hr_karyawan_counts'] = $this->model_hr_karyawan->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/hr_karyawan/index/',
			'total_rows'   => $this->data['hr_karyawan_counts'],
			'per_page'     => $limit,
			'uri_segment'  => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Karyawan List');
		$this->render('backend/standart/administrator/hr_karyawan/hr_karyawan_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('hr_karyawan_add');
        
        // Ambil pilihan shift & kantor
        $this->data['shifts'] = $this->db->get('hr_shift')->result();
        $this->data['kantors'] = $this->db->get('hr_kantor')->result();

		$this->template->title('Tambah Karyawan Baru');
		$this->render('backend/standart/administrator/hr_karyawan/hr_karyawan_add', $this->data);
	}

	public function add_save()
	{
		$this->is_allowed('hr_karyawan_add');

        $this->form_validation->set_rules('email', 'Email Karyawan', 'trim|required|valid_email|is_unique[aauth_users.email]');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[aauth_users.username]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]');
		$this->form_validation->set_rules('nik', 'NIK/NIP', 'trim|required');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required');
		
		if ($this->form_validation->run()) {
            $nama_lengkap = $this->input->post('nama_lengkap');
            $email        = $this->input->post('email');
            $username     = $this->input->post('username');
            $password     = $this->input->post('password');

            // Otomatis buatkan akun user login di sistem Aauth
            $user_id = $this->aauth->create_user($email, $password, $username, ['full_name' => $nama_lengkap]);
            if (!$user_id) {
                echo json_encode(['success' => false, 'message' => strip_tags($this->aauth->print_errors())]);
                return;
            }

            // Tentukan grup user dari form input (Role Akses) atau otomatis dari Jabatan
            $group_id = $this->input->post('group_id');
            if (!$group_id) {
                $jabatan_input = strtolower($this->input->post('jabatan') ?: '');
                $is_mgr = (strpos($jabatan_input, 'manager') !== false || strpos($jabatan_input, 'manajer') !== false || strpos($jabatan_input, 'direktur') !== false || strpos($jabatan_input, 'head') !== false || strpos($jabatan_input, 'supervisor') !== false || strpos($jabatan_input, 'ceo') !== false);
                $group_id = $is_mgr ? 6 : 4;
            }
            $this->db->delete('aauth_user_to_group', ['user_id' => $user_id]);
            $this->aauth->add_member($user_id, $group_id);

			$save_data = [
				'user_id'           => $user_id,
				'nama_lengkap'      => $nama_lengkap,
				'jenis_kelamin'     => $this->input->post('jenis_kelamin'),
				'tanggal_lahir'     => $this->input->post('tanggal_lahir') ?: NULL,
				'alamat'            => $this->input->post('alamat'),
				'no_telepon'        => $this->input->post('no_telepon'),
				'tanggal_bergabung' => $this->input->post('tanggal_bergabung') ?: NULL,
				'jam_masuk_standar' => $this->input->post('jam_masuk_standar') ?: '08:00:00',
				'nik'               => $this->input->post('nik'),
				'jabatan'           => $this->input->post('jabatan'),
				'departemen'        => $this->input->post('departemen'),
				'tipe_kerja'        => $this->input->post('tipe_kerja'),
				'gaji_pokok'        => str_replace(['.', ','], '', $this->input->post('gaji_pokok')) ?: 0,
				'tunjangan'         => (str_replace(['.', ','], '', $this->input->post('tunjangan_tetap')) ?: 0) + (($this->input->post('tipe_kerja') == 'WFO') ? (str_replace(['.', ','], '', $this->input->post('tunjangan_kehadiran')) ?: 0) : 0),
				'tunjangan_tetap'   => str_replace(['.', ','], '', $this->input->post('tunjangan_tetap')) ?: 0,
				'tunjangan_kehadiran' => ($this->input->post('tipe_kerja') == 'WFO') ? (str_replace(['.', ','], '', $this->input->post('tunjangan_kehadiran')) ?: 0) : 0,
				'shift_id'          => $this->input->post('shift_id') ?: 1,
				'kantor_id'         => $this->input->post('kantor_id') ?: 1,
				'created_at'        => date('Y-m-d H:i:s')
			];
			$save_hr_karyawan = $this->model_hr_karyawan->store($save_data);
			if ($save_hr_karyawan) {
				set_message('Data Karyawan & Akun Login berhasil ditambahkan.', 'success');
				echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_karyawan')]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data karyawan']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function edit($id)
	{
		$this->is_allowed('hr_karyawan_update');
		$this->data['hr_karyawan'] = $this->model_hr_karyawan->find($id);

        // Ambil pilihan shift & kantor
        $this->data['shifts'] = $this->db->get('hr_shift')->result();
        $this->data['kantors'] = $this->db->get('hr_kantor')->result();

		// Ambil data akun login dari karyawan ini & grup aksesnya
		if ($this->data['hr_karyawan']) {
			$this->data['user_login'] = $this->db->get_where('aauth_users', ['id' => $this->data['hr_karyawan']->user_id])->row();
			$user_group = $this->db->get_where('aauth_user_to_group', ['user_id' => $this->data['hr_karyawan']->user_id])->row();
			$this->data['current_group_id'] = $user_group ? $user_group->group_id : 4;
		} else {
			$this->data['current_group_id'] = 4;
		}

		$this->template->title('Edit Karyawan');
		$this->render('backend/standart/administrator/hr_karyawan/hr_karyawan_update', $this->data);
	}

	public function edit_save($id)
	{
		$this->is_allowed('hr_karyawan_update');

        $karyawan = $this->model_hr_karyawan->find($id);
        if (!$karyawan) {
            echo json_encode(['success' => false, 'message' => 'Data Karyawan tidak ditemukan.']);
            return;
        }

        $user_login = $this->db->get_where('aauth_users', ['id' => $karyawan->user_id])->row();
        
        $email_rule = 'trim|required|valid_email';
        if ($user_login && $user_login->email !== $this->input->post('email')) {
            $email_rule .= '|is_unique[aauth_users.email]';
        }

        $username_rule = 'trim|required';
        if ($user_login && $user_login->username !== $this->input->post('username')) {
            $username_rule .= '|is_unique[aauth_users.username]';
        }

		$this->form_validation->set_rules('email', 'Email Karyawan', $email_rule);
		$this->form_validation->set_rules('username', 'Username', $username_rule);
        $this->form_validation->set_rules('nik', 'NIP', 'trim|required');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required');

		if ($this->form_validation->run()) {
            $email        = $this->input->post('email');
            $username     = $this->input->post('username');
            $password     = $this->input->post('password');
            $nama_lengkap = $this->input->post('nama_lengkap');

            // Update akun user login terkait di aauth_users
            $pass = !empty($password) ? $password : FALSE;
            $update_user = $this->aauth->update_user($karyawan->user_id, $email, $pass, $username, ['full_name' => $nama_lengkap]);
            if (!$update_user && $this->aauth->print_errors()) {
                echo json_encode(['success' => false, 'message' => strip_tags($this->aauth->print_errors())]);
                return;
            }

            // Tentukan grup user dari form input (Role Akses) atau otomatis dari Jabatan
            $group_id = $this->input->post('group_id');
            if (!$group_id) {
                $jabatan_input = strtolower($this->input->post('jabatan') ?: '');
                $is_mgr = (strpos($jabatan_input, 'manager') !== false || strpos($jabatan_input, 'manajer') !== false || strpos($jabatan_input, 'direktur') !== false || strpos($jabatan_input, 'head') !== false || strpos($jabatan_input, 'supervisor') !== false || strpos($jabatan_input, 'ceo') !== false);
                $group_id = $is_mgr ? 6 : 4;
            }
            $this->db->delete('aauth_user_to_group', ['user_id' => $karyawan->user_id]);
            $this->aauth->add_member($karyawan->user_id, $group_id);

			$save_data = [
				'nama_lengkap'      => $nama_lengkap,
				'jenis_kelamin'     => $this->input->post('jenis_kelamin'),
				'tanggal_lahir'     => $this->input->post('tanggal_lahir') ?: NULL,
				'alamat'            => $this->input->post('alamat'),
				'no_telepon'        => $this->input->post('no_telepon'),
				'tanggal_bergabung' => $this->input->post('tanggal_bergabung') ?: NULL,
				'jam_masuk_standar' => $this->input->post('jam_masuk_standar') ?: '08:00:00',
				'nik'               => $this->input->post('nik'),
				'jabatan'           => $this->input->post('jabatan'),
				'departemen'        => $this->input->post('departemen'),
				'tipe_kerja'        => $this->input->post('tipe_kerja'),
				'gaji_pokok'        => str_replace(['.', ','], '', $this->input->post('gaji_pokok')) ?: 0,
				'tunjangan'         => (str_replace(['.', ','], '', $this->input->post('tunjangan_tetap')) ?: 0) + (($this->input->post('tipe_kerja') == 'WFO') ? (str_replace(['.', ','], '', $this->input->post('tunjangan_kehadiran')) ?: 0) : 0),
				'tunjangan_tetap'   => str_replace(['.', ','], '', $this->input->post('tunjangan_tetap')) ?: 0,
				'tunjangan_kehadiran' => ($this->input->post('tipe_kerja') == 'WFO') ? (str_replace(['.', ','], '', $this->input->post('tunjangan_kehadiran')) ?: 0) : 0,
				'shift_id'          => $this->input->post('shift_id') ?: 1,
				'kantor_id'         => $this->input->post('kantor_id') ?: 1,
				'updated_at'        => date('Y-m-d H:i:s')
			];

			$save_hr_karyawan = $this->model_hr_karyawan->change($id, $save_data);
			if ($save_hr_karyawan) {
				set_message('Data Karyawan & Akun Login berhasil diperbarui.', 'success');
				echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_karyawan')]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data']);
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
