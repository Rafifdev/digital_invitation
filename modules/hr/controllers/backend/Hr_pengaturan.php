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
		$this->data['kantors'] = $this->db->get('hr_kantor')->result();
		
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
			'default_kantor_id' => $this->input->post('default_kantor_id'),
			'jam_masuk' => $this->input->post('jam_masuk'),
			'toleransi_terlambat_menit' => $this->input->post('toleransi_terlambat_menit'),
			'jam_pulang' => $this->input->post('jam_pulang'),
			'toleransi_pulang_awal_menit' => $this->input->post('toleransi_pulang_awal_menit'),
            'batas_jam_pulang' => $this->input->post('batas_jam_pulang'),
			'updated_at' => date('Y-m-d H:i:s')
		];

        $pengaturan = $this->db->get('hr_pengaturan')->row();
        if ($pengaturan) {
            $this->db->update('hr_pengaturan', $save_data, ['id' => $pengaturan->id]);
        } else {
            $this->db->insert('hr_pengaturan', $save_data);
        }

        if ($this->input->post('po_approval_threshold') !== null) {
            $raw_thresh = str_replace('.', '', $this->input->post('po_approval_threshold'));
            $raw_thresh = (float) str_replace(',', '.', $raw_thresh);
            set_option('po_approval_threshold', $raw_thresh);
        }

		set_message('Pengaturan HR & System berhasil disimpan.', 'success');
		echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_pengaturan')]);
	}

    public function test_save_debug()
    {
        // Bypass auth for testing
        $save_data = [
			'nama_kantor' => 'Kantor Pusat Test',
			'latitude_kantor' => '-7.5754887',
			'longitude_kantor' => '110.8243272',
			'radius_toleransi_meter' => 50,
			'jam_masuk' => '08:00',
			'toleransi_terlambat_menit' => 15,
			'jam_pulang' => '17:00',
			'toleransi_pulang_awal_menit' => 0,
            'batas_jam_pulang' => '20:00',
			'updated_at' => date('Y-m-d H:i:s')
		];
        $pengaturan = $this->db->get('hr_pengaturan')->row();
        if ($pengaturan) {
            $res = $this->db->update('hr_pengaturan', $save_data, ['id' => $pengaturan->id]);
        } else {
            $res = $this->db->insert('hr_pengaturan', $save_data);
        }
        echo "DB UPDATE RESULT: " . ($res ? 'TRUE' : 'FALSE') . "\n";
        echo "DB ERROR: " . $this->db->error()['message'] . "\n";
    }
}
