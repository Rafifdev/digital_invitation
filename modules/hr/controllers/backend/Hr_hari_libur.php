<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_hari_libur extends Admin
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->is_allowed('hr_hari_libur_list');
        $this->db->order_by('tanggal', 'DESC');
		$this->data['liburs'] = $this->db->get('hr_hari_libur')->result();

		$this->template->title('Master Data Hari Libur');
		$this->render('backend/standart/administrator/hr_hari_libur/hr_hari_libur_list', $this->data);
	}

    public function get_events()
    {
        $this->is_allowed('hr_hari_libur_list');
        $liburs = $this->db->get('hr_hari_libur')->result();
        $events = [];
        foreach ($liburs as $l) {
            $events[] = [
                'id' => $l->id,
                'title' => $l->keterangan,
                'start' => $l->tanggal,
                'color' => $l->is_national ? '#d33' : '#3c8dbc', // Red for national, blue for company
                'allDay' => true
            ];
        }

        // Ambil pengaturan cut-off
        $pengaturan = $this->db->get('hr_pengaturan')->row();
        $cutoff_mulai = $pengaturan ? (int)$pengaturan->tgl_cutoff_mulai : 21;
        $cutoff_akhir = $pengaturan ? (int)$pengaturan->tgl_cutoff_akhir : 20;

        // Tampilkan indikator Cut-Off di kalender (untuk tahun lalu, sekarang, dan tahun depan)
        $current_year = date('Y');
        for ($y = $current_year - 1; $y <= $current_year + 1; $y++) {
            for ($m = 1; $m <= 12; $m++) {
                $month = str_pad($m, 2, '0', STR_PAD_LEFT);
                
                // Event Mulai Periode Baru
                $tgl_start = $y . '-' . $month . '-' . str_pad($cutoff_mulai, 2, '0', STR_PAD_LEFT);
                $events[] = [
                    'id' => 'cutoff_start_' . $y . '_' . $m,
                    'title' => 'Mulai Periode Gaji',
                    'start' => $tgl_start,
                    'color' => '#00a65a', // Hijau
                    'allDay' => true,
                    'textColor' => '#fff'
                ];

                // Event Tutup Buku
                $tgl_end = $y . '-' . $month . '-' . str_pad($cutoff_akhir, 2, '0', STR_PAD_LEFT);
                $events[] = [
                    'id' => 'cutoff_end_' . $y . '_' . $m,
                    'title' => 'Tutup Buku (Cut-Off)',
                    'start' => $tgl_end,
                    'color' => '#f39c12', // Oranye
                    'allDay' => true,
                    'textColor' => '#fff'
                ];
            }
        }
        header('Content-Type: application/json');
        echo json_encode($events);
    }

    public function sync_national()
    {
        $this->is_allowed('hr_hari_libur_add');
        
        $year = date('Y'); // Menarik libur untuk tahun ini
        $api_url = "https://date.nager.at/api/v3/PublicHolidays/{$year}/ID";

        // Menggunakan cURL untuk request ke API Eksternal
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Abaikan verifikasi SSL untuk koneksi lokal
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200 || !$response) {
            echo json_encode(['success' => false, 'message' => 'Gagal terhubung ke API Nager.Date. HTTP Code: ' . $http_code]);
            return;
        }

        $holidays = json_decode($response, true);
        if (!$holidays || !is_array($holidays)) {
            echo json_encode(['success' => false, 'message' => 'Format respons JSON dari API tidak valid.']);
            return;
        }

        $inserted = 0;
        foreach ($holidays as $holiday) {
            $tanggal = $holiday['date'];
            // localName dari API Nager.Date biasanya sudah berbahasa Indonesia
            $keterangan = !empty($holiday['localName']) ? $holiday['localName'] : $holiday['name'];

            // Cek apakah tanggal tersebut sudah ada di database agar tidak duplikat
            $exists = $this->db->get_where('hr_hari_libur', ['tanggal' => $tanggal])->row();
            if (!$exists) {
                $this->db->insert('hr_hari_libur', [
                    'tanggal' => $tanggal,
                    'keterangan' => $keterangan,
                    'is_national' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $inserted++;
            }
        }

        if ($inserted > 0) {
            echo json_encode(['success' => true, 'message' => "Berhasil mensinkronkan $inserted hari libur nasional tahun $year dari API Nager.Date!"]);
        } else {
            echo json_encode(['success' => true, 'message' => "Sinkronisasi berhasil. Semua hari libur nasional tahun $year sudah tersinkron (Up-to-date)."]);
        }
    }

	public function add()
	{
		$this->is_allowed('hr_hari_libur_add');
		$this->template->title('Tambah Hari Libur');
		$this->render('backend/standart/administrator/hr_hari_libur/hr_hari_libur_add', $this->data);
	}

	public function add_save()
	{
		$this->is_allowed('hr_hari_libur_add');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'trim|required');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required');

		if ($this->form_validation->run()) {
			$save_data = [
				'tanggal' => $this->input->post('tanggal'),
				'keterangan' => $this->input->post('keterangan'),
				'is_national' => $this->input->post('is_national') ? 1 : 0,
				'created_at' => date('Y-m-d H:i:s')
			];
			if ($this->db->insert('hr_hari_libur', $save_data)) {
				set_message('Hari Libur berhasil ditambahkan.', 'success');
				echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_hari_libur')]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function edit($id)
	{
		$this->is_allowed('hr_hari_libur_update');
		$this->data['libur'] = $this->db->get_where('hr_hari_libur', ['id' => $id])->row();
		$this->template->title('Edit Hari Libur');
		$this->render('backend/standart/administrator/hr_hari_libur/hr_hari_libur_update', $this->data);
	}

	public function edit_save($id)
	{
		$this->is_allowed('hr_hari_libur_update');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'trim|required');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required');

		if ($this->form_validation->run()) {
			$save_data = [
				'tanggal' => $this->input->post('tanggal'),
				'keterangan' => $this->input->post('keterangan'),
				'is_national' => $this->input->post('is_national') ? 1 : 0
			];
			if ($this->db->update('hr_hari_libur', $save_data, ['id' => $id])) {
				set_message('Hari Libur berhasil diperbarui.', 'success');
				echo json_encode(['success' => true, 'redirect' => site_url('administrator/hr_hari_libur')]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function delete($id = null)
	{
		$this->is_allowed('hr_hari_libur_delete');
		if ($this->db->delete('hr_hari_libur', ['id' => $id])) {
			set_message('Hari Libur berhasil dihapus.', 'success');
		} else {
			set_message('Gagal menghapus data.', 'error');
		}
		redirect('administrator/hr_hari_libur');
	}
}
