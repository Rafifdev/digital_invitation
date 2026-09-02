<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Gudang extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_gudang');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('gudang_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['gudangs']      = $this->model_gudang->get($filter, $field, $this->limit_page, $offset);
		$this->data['gudang_counts'] = $this->model_gudang->count_all($filter, $field);

		$config = [
			'base_url'    => 'administrator/gudang/index/',
			'total_rows'  => $this->data['gudang_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Gudang List');
		$this->render('backend/standart/administrator/gudang/gudang_list', $this->data);
	}

	public function dashboard()
	{
		$this->is_allowed('gudang_list');
		$this->data['total_stok']      = $this->db->select_sum('qty')->get('stok')->row()->qty ?: 0;
		$this->data['stok_rusak']      = $this->db->select_sum('qty_rusak')->get('stok')->row()->qty_rusak ?: 0;
		
		// Total Nilai Barang Masuk (Confirmed Inbound GRN)
		$this->data['barang_masuk']    = $this->db->query("
			SELECT SUM(pbi.qty_baik * pbi.harga_satuan) as total_masuk 
			FROM penerimaan_barang_item pbi 
			JOIN penerimaan_barang pb ON pb.id = pbi.penerimaan_id 
			WHERE pb.status = 'confirmed'
		")->row()->total_masuk ?: 0;

		// Total Nilai Barang Keluar (Approved/Completed Outbound Sales)
		$this->data['barang_keluar']   = $this->db->query("
			SELECT SUM(total) as total_keluar 
			FROM sales_order 
			WHERE status IN ('approved', 'completed')
		")->row()->total_keluar ?: 0;

		$this->data['produk_minimum']  = $this->db->query("
			SELECT 
				p.id as produk_id,
				p.kode_produk,
				p.nama_produk,
				p.stok_minimum,
				k.nama_kategori,
				g.id as gudang_id,
				g.nama_gudang,
				s.qty
			FROM stok s
			JOIN produk p ON p.id = s.produk_id
			JOIN gudang g ON g.id = s.gudang_id
			LEFT JOIN kategori_produk k ON k.id = p.kategori_id
			WHERE s.qty <= p.stok_minimum AND p.status = 'aktif' AND g.status = 'aktif'
			ORDER BY g.nama_gudang ASC, s.qty ASC
		")->result();

		$this->template->title('Real-time Warehouse Dashboard');
		$this->render('backend/standart/administrator/gudang/gudang_dashboard', $this->data);
	}

	public function add()
	{
		$this->is_allowed('gudang_add');
		$this->data['kode_baru'] = $this->model_gudang->generate_kode();
		$this->template->title('Tambah Gudang');
		$this->render('backend/standart/administrator/gudang/gudang_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('gudang_add', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('nama_gudang', 'Nama Gudang', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('kode_gudang', 'Kode Gudang', 'trim|required|max_length[20]');

		if ($this->form_validation->run()) {
			$data = [
				'kode_gudang'  => $this->input->post('kode_gudang'),
				'nama_gudang'  => $this->input->post('nama_gudang'),
				'tipe_gudang'  => $this->input->post('tipe_gudang') ?: 'utama',
				'alamat'       => $this->input->post('alamat'),
				'pic_name'     => $this->input->post('pic_name'),
				'telepon'      => $this->input->post('telepon'),
				'status'       => $this->input->post('status') ?: 'aktif',
				'created_by'   => get_user_data('username'),
				'created_at'   => date('Y-m-d H:i:s'),
			];

			$id = $this->model_gudang->store($data);

			if ($id) {
				// Inisialisasi stok 0 untuk semua produk di gudang baru
				$produks = $this->db->where('status', 'aktif')->get('produk')->result();
				foreach ($produks as $p) {
					$this->db->insert('stok', ['produk_id' => $p->id, 'gudang_id' => $id, 'qty' => 0, 'qty_rusak' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
				}

				// Tambah rak jika ada
				if ($this->input->post('rak')) {
					foreach ($this->input->post('rak') as $rak) {
						if (!empty($rak['kode_rak'])) {
							$this->db->insert('rak', ['gudang_id' => $id, 'kode_rak' => $rak['kode_rak'], 'baris' => $rak['baris'], 'deskripsi' => $rak['deskripsi'], 'created_at' => date('Y-m-d H:i:s')]);
						}
					}
				}

				set_message('Gudang berhasil disimpan.', 'success');
				$this->data['success']  = true;
				$this->data['redirect'] = base_url('administrator/gudang');
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
		$this->is_allowed('gudang_update');
		$this->data['gudang']  = $this->model_gudang->find($id);
		$this->data['rak_list'] = $this->model_gudang->get_rak_by_gudang($id);
		$this->template->title('Edit Gudang');
		$this->render('backend/standart/administrator/gudang/gudang_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('gudang_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('nama_gudang', 'Nama Gudang', 'trim|required|max_length[100]');

		if ($this->form_validation->run()) {
			$data = [
				'nama_gudang'  => $this->input->post('nama_gudang'),
				'tipe_gudang'  => $this->input->post('tipe_gudang') ?: 'utama',
				'alamat'       => $this->input->post('alamat'),
				'pic_name'     => $this->input->post('pic_name'),
				'telepon'      => $this->input->post('telepon'),
				'status'       => $this->input->post('status') ?: 'aktif',
			];

			$save = $this->model_gudang->change($id, $data);
			set_message('Gudang berhasil diperbarui.', 'success');
			$this->data['success']  = true;
			$this->data['redirect'] = base_url('administrator/gudang');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function view($id)
	{
		$this->is_allowed('gudang_view');
		$this->data['gudang']     = $this->model_gudang->find($id);
		$this->data['rak_list']   = $this->model_gudang->get_rak_by_gudang($id);
		$this->data['stok_list']  = $this->model_gudang->get_stok_per_produk($id);
		$this->template->title('Detail Gudang');
		$this->render('backend/standart/administrator/gudang/gudang_view', $this->data);
	}

	public function delete($id = null)
	{
		$this->is_allowed('gudang_delete');
		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->model_gudang->remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $aid) {
				$remove = $this->model_gudang->remove($aid);
			}
		}

		set_message($remove ? 'Gudang berhasil dihapus.' : 'Gagal menghapus.', $remove ? 'success' : 'error');
		redirect_back();
	}

	public function export()
	{
		$this->is_allowed('gudang_export');
		$this->model_gudang->export('gudang', 'gudang');
	}

	public function export_pdf()
	{
		$this->is_allowed('gudang_export');
		$this->model_gudang->pdf('gudang', 'gudang');
	}

}
