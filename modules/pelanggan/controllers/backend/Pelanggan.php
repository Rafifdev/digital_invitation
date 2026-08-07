<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Pelanggan extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_pelanggan');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('pelanggan_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['pelanggans']      = $this->model_pelanggan->get($filter, $field, $this->limit_page, $offset);
		$this->data['pelanggan_counts'] = $this->model_pelanggan->count_all($filter, $field);

		$config = [
			'base_url'    => 'administrator/pelanggan/index/',
			'total_rows'  => $this->data['pelanggan_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pelanggan List');
		$this->render('backend/standart/administrator/pelanggan/pelanggan_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('pelanggan_add');
		$this->data['kode_baru'] = $this->model_pelanggan->generate_kode();
		$this->template->title('Tambah Pelanggan');
		$this->render('backend/standart/administrator/pelanggan/pelanggan_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('pelanggan_add', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('nama_pelanggan', 'Nama Pelanggan', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('kode_pelanggan', 'Kode Pelanggan', 'trim|required|max_length[20]');

		if ($this->form_validation->run()) {
			$data = [
				'kode_pelanggan'  => $this->input->post('kode_pelanggan'),
				'nama_pelanggan'  => $this->input->post('nama_pelanggan'),
				'tipe_pelanggan'  => $this->input->post('tipe_pelanggan') ?: 'retail',
				'kontak_person'   => $this->input->post('kontak_person'),
				'telepon'         => $this->input->post('telepon'),
				'email'           => $this->input->post('email'),
				'alamat'          => $this->input->post('alamat'),
				'kota'            => $this->input->post('kota'),
				'npwp'            => $this->input->post('npwp'),
				'limit_kredit'    => preg_replace('/[^0-9]/', '', $this->input->post('limit_kredit')) ?: 0,
				'status'          => $this->input->post('status') ?: 'aktif',
				'catatan'         => $this->input->post('catatan'),
				'created_by'      => get_user_data('username'),
				'created_at'      => date('Y-m-d H:i:s'),
			];

			$id = $this->model_pelanggan->store($data);

			if ($id) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['message'] = 'Pelanggan berhasil disimpan. ' . anchor('administrator/pelanggan/edit/' . $id, 'Edit');
				} else {
					set_message('Pelanggan berhasil disimpan.', 'success');
					$this->data['success']  = true;
					$this->data['redirect'] = base_url('administrator/pelanggan');
				}
			} else {
				$this->data['success'] = false;
				$this->data['message'] = 'Gagal menyimpan data.';
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function edit($id)
	{
		$this->is_allowed('pelanggan_update');
		$this->data['pelanggan'] = $this->model_pelanggan->find($id);
		$this->template->title('Edit Pelanggan');
		$this->render('backend/standart/administrator/pelanggan/pelanggan_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('pelanggan_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('nama_pelanggan', 'Nama Pelanggan', 'trim|required|max_length[150]');

		if ($this->form_validation->run()) {
			$data = [
				'nama_pelanggan'  => $this->input->post('nama_pelanggan'),
				'tipe_pelanggan'  => $this->input->post('tipe_pelanggan') ?: 'retail',
				'kontak_person'   => $this->input->post('kontak_person'),
				'telepon'         => $this->input->post('telepon'),
				'email'           => $this->input->post('email'),
				'alamat'          => $this->input->post('alamat'),
				'kota'            => $this->input->post('kota'),
				'npwp'            => $this->input->post('npwp'),
				'limit_kredit'    => preg_replace('/[^0-9]/', '', $this->input->post('limit_kredit')) ?: 0,
				'status'          => $this->input->post('status') ?: 'aktif',
				'catatan'         => $this->input->post('catatan'),
				'updated_at'      => date('Y-m-d H:i:s'),
			];

			$save = $this->model_pelanggan->change($id, $data);

			if ($save) {
				set_message('Pelanggan berhasil diperbarui.', 'success');
				$this->data['success']  = true;
				$this->data['redirect'] = base_url('administrator/pelanggan');
			} else {
				$this->data['success'] = false;
				$this->data['message'] = 'Tidak ada perubahan data.';
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function view($id)
	{
		$this->is_allowed('pelanggan_view');
		$this->data['pelanggan'] = $this->model_pelanggan->find($id);
		
		// Riwayat transaksi per pelanggan untuk analisis pembelian ulang
		$this->data['riwayat_so'] = $this->db->get_where('sales_order', ['pelanggan_id' => $id])->result();
		$this->data['riwayat_invoice'] = $this->db->get_where('invoice', ['pelanggan_id' => $id])->result();
		$this->data['total_volume'] = $this->db->select_sum('total')->get_where('sales_order', ['pelanggan_id' => $id])->row()->total ?: 0;

		$this->template->title('Detail & Analisis Transaksi Pelanggan');
		$this->render('backend/standart/administrator/pelanggan/pelanggan_view', $this->data);
	}

	public function delete($id = null)
	{
		$this->is_allowed('pelanggan_delete');
		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->model_pelanggan->remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $aid) {
				$remove = $this->model_pelanggan->remove($aid);
			}
		}

		set_message($remove ? 'Pelanggan berhasil dihapus.' : 'Gagal menghapus.', $remove ? 'success' : 'error');
		redirect_back();
	}

	public function export()
	{
		$this->is_allowed('pelanggan_export');
		$this->model_pelanggan->export('pelanggan', 'pelanggan');
	}

	public function laporan_transaksi()
	{
		$this->is_allowed('pelanggan_list');
		$this->data['laporan'] = $this->model_pelanggan->get_laporan_transaksi();
		$this->template->title('Laporan Transaksi Pelanggan');
		$this->render('backend/standart/administrator/pelanggan/pelanggan_laporan_transaksi', $this->data);
	}

	public function export_pdf()
	{
		$this->is_allowed('pelanggan_export');
		$this->model_pelanggan->pdf('pelanggan', 'pelanggan');
	}

}
