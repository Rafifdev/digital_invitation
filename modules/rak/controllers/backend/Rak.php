<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rak extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_rak');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('rak_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['raks']       = $this->model_rak->get($filter, $field, $this->limit_page, $offset);
		$this->data['rak_counts'] = $this->model_rak->count_all($filter, $field);

		$config = [
			'base_url'    => 'administrator/rak/index/',
			'total_rows'  => $this->data['rak_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Lokasi Rak Gudang List');
		$this->render('backend/standart/administrator/rak/rak_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('rak_add');
		$this->data['gudangs'] = $this->db->get_where('gudang', ['status' => 'aktif'])->result();
		$this->template->title('Tambah Lokasi Rak');
		$this->render('backend/standart/administrator/rak/rak_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('rak_add', false)) {
			return $this->response(['success' => false, 'message' => 'Tidak punya akses']);
		}

		$this->form_validation->set_rules('gudang_id', 'Gudang', 'trim|required|numeric');
		$this->form_validation->set_rules('kode_rak', 'Kode Rak', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('baris', 'Baris / Tingkat', 'trim|required|max_length[50]');

		if ($this->form_validation->run()) {
			$data = [
				'gudang_id'     => $this->input->post('gudang_id'),
				'kode_rak'      => $this->input->post('kode_rak'),
				'baris'         => $this->input->post('baris'),
				'kategori_rak'  => $this->input->post('kategori_rak') ?: 'Utama',
				'deskripsi'     => $this->input->post('deskripsi'),
				'status'        => $this->input->post('status') ?: 'aktif',
				'created_at'    => date('Y-m-d H:i:s'),
			];

			$insert = $this->db->insert('rak', $data);

			if ($insert) {
				set_message('Lokasi Rak berhasil disimpan.', 'success');
				return $this->response([
					'success'  => true,
					'message'  => 'Lokasi Rak berhasil disimpan.',
					'redirect' => base_url('administrator/rak')
				]);
			} else {
				return $this->response(['success' => false, 'message' => 'Gagal menyimpan data rak.']);
			}
		} else {
			return $this->response(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function edit($id)
	{
		$this->is_allowed('rak_update');
		$this->data['rak']     = $this->model_rak->find($id);
		$this->data['gudangs'] = $this->db->get_where('gudang', ['status' => 'aktif'])->result();
		$this->template->title('Edit Lokasi Rak');
		$this->render('backend/standart/administrator/rak/rak_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('rak_update', false)) {
			return $this->response(['success' => false, 'message' => 'Tidak punya akses']);
		}

		$this->form_validation->set_rules('gudang_id', 'Gudang', 'trim|required|numeric');
		$this->form_validation->set_rules('kode_rak', 'Kode Rak', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('baris', 'Baris / Tingkat', 'trim|required|max_length[50]');

		if ($this->form_validation->run()) {
			$data = [
				'gudang_id'     => $this->input->post('gudang_id'),
				'kode_rak'      => $this->input->post('kode_rak'),
				'baris'         => $this->input->post('baris'),
				'kategori_rak'  => $this->input->post('kategori_rak') ?: 'Utama',
				'deskripsi'     => $this->input->post('deskripsi'),
				'status'        => $this->input->post('status') ?: 'aktif',
				'updated_at'    => date('Y-m-d H:i:s'),
			];

			$update = $this->db->update('rak', $data, ['id' => $id]);

			if ($update) {
				set_message('Lokasi Rak berhasil diperbarui.', 'success');
				return $this->response([
					'success'  => true,
					'message'  => 'Lokasi Rak berhasil diperbarui.',
					'redirect' => base_url('administrator/rak')
				]);
			} else {
				return $this->response(['success' => false, 'message' => 'Gagal memperbarui data rak.']);
			}
		} else {
			return $this->response(['success' => false, 'message' => validation_errors()]);
		}
	}

	public function view($id)
	{
		$this->is_allowed('rak_view');
		$this->data['rak'] = $this->model_rak->find_with_detail($id);
		$this->template->title('Detail Lokasi Rak');
		$this->render('backend/standart/administrator/rak/rak_view', $this->data);
	}

	public function delete($id = null)
	{
		$this->is_allowed('rak_delete');
		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->model_rak->remove($id);
		} elseif (is_array($arr_id) && count($arr_id) > 0) {
			foreach ($arr_id as $aid) {
				$remove = $this->model_rak->remove($aid);
			}
		}

		set_message($remove ? 'Lokasi Rak berhasil dihapus.' : 'Gagal menghapus.', $remove ? 'success' : 'error');
		redirect_back();
	}

	public function export()
	{
		$this->is_allowed('rak_export');
		$this->model_rak->join_available();
		$this->model_rak->export('rak', 'lokasi_rak');
	}

	public function export_pdf()
	{
		$this->is_allowed('rak_export');
		$this->model_rak->pdf('rak', 'rak');
	}

}
