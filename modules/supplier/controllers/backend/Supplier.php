<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Supplier extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_supplier');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('supplier_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['suppliers']      = $this->model_supplier->get($filter, $field, $this->limit_page, $offset);
		$this->data['supplier_counts'] = $this->model_supplier->count_all($filter, $field);

		$config = [
			'base_url'    => 'administrator/supplier/index/',
			'total_rows'  => $this->data['supplier_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Supplier List');
		$this->render('backend/standart/administrator/supplier/supplier_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('supplier_add');
		$this->data['kode_baru'] = $this->model_supplier->generate_kode();
		$this->template->title('Tambah Supplier');
		$this->render('backend/standart/administrator/supplier/supplier_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('supplier_add', false)) {
			return $this->response(['success' => false, 'message' => 'Tidak punya akses']);
		}

		$this->form_validation->set_rules('nama_supplier', 'Nama Supplier', 'trim|required|max_length[150]');

		if ($this->form_validation->run()) {
			$kode = $this->input->post('kode_supplier') ?: $this->model_supplier->generate_kode();
			$data = [
				'kode_supplier'  => $kode,
				'nama_supplier'  => $this->input->post('nama_supplier'),
				'kontak_person'  => $this->input->post('kontak_person'),
				'telepon'        => $this->input->post('telepon'),
				'email'          => $this->input->post('email'),
				'alamat'         => $this->input->post('alamat'),
				'kota'           => $this->input->post('kota'),
				'negara'         => $this->input->post('negara') ?: 'Indonesia',
				'mata_uang'      => $this->input->post('mata_uang') ?: 'IDR',
				'kurs_manual'    => $this->input->post('kurs_manual') ?: 1,
				'npwp'           => $this->input->post('npwp'),
				'bank_name'      => $this->input->post('bank_name'),
				'bank_account'   => $this->input->post('bank_account'),
				'bank_holder'    => $this->input->post('bank_holder'),
				'status'         => $this->input->post('status') ?: 'aktif',
				'catatan'        => $this->input->post('catatan'),
				'created_by'     => get_user_data('username') ?: 'admin',
				'created_at'     => date('Y-m-d H:i:s'),
			];

			$id = $this->model_supplier->store($data);

			if ($id) {
				set_message('Supplier berhasil disimpan.', 'success');
				return $this->response([
					'success'  => true,
					'message'  => 'Supplier berhasil disimpan.',
					'redirect' => base_url('administrator/supplier')
				]);
			} else {
				return $this->response([
					'success' => false,
					'message' => 'Gagal menyimpan data ke database.'
				]);
			}
		} else {
			return $this->response([
				'success' => false,
				'message' => validation_errors()
			]);
		}
	}

	public function edit($id)
	{
		$this->is_allowed('supplier_update');
		$this->data['supplier'] = $this->model_supplier->find($id);
		$this->template->title('Edit Supplier');
		$this->render('backend/standart/administrator/supplier/supplier_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('supplier_update', false)) {
			return $this->response(['success' => false, 'message' => 'Tidak punya akses']);
		}

		$this->form_validation->set_rules('nama_supplier', 'Nama Supplier', 'trim|required|max_length[150]');

		if ($this->form_validation->run()) {
			$data = [
				'nama_supplier'  => $this->input->post('nama_supplier'),
				'kontak_person'  => $this->input->post('kontak_person'),
				'telepon'        => $this->input->post('telepon'),
				'email'          => $this->input->post('email'),
				'alamat'         => $this->input->post('alamat'),
				'kota'           => $this->input->post('kota'),
				'negara'         => $this->input->post('negara') ?: 'Indonesia',
				'mata_uang'      => $this->input->post('mata_uang') ?: 'IDR',
				'kurs_manual'    => $this->input->post('kurs_manual') ?: 1,
				'npwp'           => $this->input->post('npwp'),
				'bank_name'      => $this->input->post('bank_name'),
				'bank_account'   => $this->input->post('bank_account'),
				'bank_holder'    => $this->input->post('bank_holder'),
				'status'         => $this->input->post('status') ?: 'aktif',
				'catatan'        => $this->input->post('catatan'),
				'updated_at'     => date('Y-m-d H:i:s'),
			];

			$save = $this->model_supplier->change($id, $data);

			set_message('Supplier berhasil diperbarui.', 'success');
			return $this->response([
				'success'  => true,
				'message'  => 'Supplier berhasil diperbarui.',
				'redirect' => base_url('administrator/supplier')
			]);
		} else {
			return $this->response([
				'success' => false,
				'message' => validation_errors()
			]);
		}
	}

	public function view($id)
	{
		$this->is_allowed('supplier_view');
		$this->data['supplier']      = $this->model_supplier->find($id);
		$this->data['riwayat_harga'] = $this->model_supplier->get_riwayat_harga($id);
		$this->template->title('Detail Supplier');
		$this->render('backend/standart/administrator/supplier/supplier_view', $this->data);
	}

	public function delete($id = null)
	{
		$this->is_allowed('supplier_delete');
		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->model_supplier->remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $aid) {
				$remove = $this->model_supplier->remove($aid);
			}
		}

		if ($remove) {
			set_message('Supplier berhasil dihapus.', 'success');
		} else {
			set_message('Gagal menghapus supplier.', 'error');
		}
		redirect_back();
	}

	public function export()
	{
		$this->is_allowed('supplier_export');
		$this->model_supplier->export('supplier', 'supplier');
	}

	public function laporan_harga()
	{
		$this->is_allowed('supplier_list');
		$supplier_id = $this->input->get('supplier_id');
		$produk_id   = $this->input->get('produk_id');

		$this->data['riwayat']   = $this->model_supplier->get_riwayat_harga($supplier_id, $produk_id);
		$this->data['suppliers'] = $this->db->get('supplier')->result();
		$this->data['produks']   = $this->db->get('produk')->result();
		$this->template->title('Riwayat Harga Beli Supplier');
		$this->render('backend/standart/administrator/supplier/supplier_laporan_harga', $this->data);
	}

	public function export_pdf()
	{
		$this->is_allowed('supplier_export');
		$this->model_supplier->pdf('supplier', 'supplier');
	}

	public function get_live_rate()
	{
		$currency = $this->input->get('currency') ?: 'USD';
		$rate = get_live_exchange_rate($currency);
		$this->response(['success' => true, 'currency' => strtoupper($currency), 'rate' => $rate]);
	}

}
