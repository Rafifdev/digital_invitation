<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_pengaturan extends Admin
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->is_allowed('hr_pengaturan_update');
		$this->data['pengaturan'] = $this->db->get('hr_pengaturan')->row();
		
		$this->template->title('Pengaturan HR');
		$this->render('backend/standart/administrator/hr_pengaturan/hr_pengaturan_form', $this->data);
	}

	public function save()
	{
		if (!$this->is_allowed('hr_pengaturan_update', false)) {
			echo json_encode(['success' => false, 'message' => 'Tidak ada akses']);
			return;
		}

		$save_data = [
			'nama_kantor' => $this->input->post('nama_kantor'),
			'latitude_kantor' => $this->input->post('latitude_kantor'),
			'longitude_kantor' => $this->input->post('longitude_kantor'),
			'radius_toleransi_meter' => $this->input->post('radius_toleransi_meter'),
			'jam_masuk' => $this->input->post('jam_masuk'),
			'toleransi_terlambat_menit' => $this->input->post('toleransi_terlambat_menit'),
			'jam_pulang' => $this->input->post('jam_pulang'),
			'toleransi_pulang_awal_menit' => $this->input->post('toleransi_pulang_awal_menit'),
			'updated_at' => date('Y-m-d H:i:s')
		];

        $pengaturan = $this->db->get('hr_pengaturan')->row();
        if ($pengaturan) {
            $this->db->update('hr_pengaturan', $save_data, ['id' => $pengaturan->id]);
        } else {
            $this->db->insert('hr_pengaturan', $save_data);
        }

		set_message('Pengaturan HR berhasil disimpan.', 'success');
		echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_pengaturan')]);
	}
}
