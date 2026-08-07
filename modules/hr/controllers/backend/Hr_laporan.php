<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_laporan extends Admin
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->is_allowed('hr_laporan_list');
        $bulan = $this->input->get('bulan') ?: date('Y-m');

        $this->db->select('a.*, k.nik, k.tipe_kerja, u.full_name');
        $this->db->from('hr_absensi a');
        $this->db->join('hr_karyawan k', 'k.id = a.karyawan_id');
        $this->db->join('aauth_users u', 'u.id = k.user_id');
        $this->db->like('a.tanggal', $bulan, 'after');
        $this->db->order_by('a.tanggal', 'DESC');
        $this->db->order_by('a.waktu_masuk', 'DESC');

		$this->data['laporan'] = $this->db->get()->result();
        $this->data['bulan_aktif'] = $bulan;
		
		$this->template->title('Laporan Absensi Karyawan');
		$this->render('backend/standart/administrator/hr_laporan/hr_laporan_list', $this->data);
	}
}
