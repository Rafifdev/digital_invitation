<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Produk extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_produk');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('produk_list');

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['produks']       = $this->model_produk->get($filter, $field, $this->limit_page, $offset);
		$this->data['produk_counts'] = $this->model_produk->count_all($filter, $field);

		$config = [
			'base_url'    => 'administrator/produk/index/',
			'total_rows'  => $this->data['produk_counts'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Produk List');
		$this->render('backend/standart/administrator/produk/produk_list', $this->data);
	}

	public function add()
	{
		$this->is_allowed('produk_add');
		$this->data['kode_baru']    = $this->model_produk->generate_kode();
		$this->data['barcode_baru'] = $this->model_produk->generate_barcode();
		$this->data['kategoris']    = $this->db->get('kategori_produk')->result();
		$this->data['satuans']      = $this->db->get('satuan')->result();
		$this->data['raks']         = $this->db->where('status', 'aktif')->get('rak')->result();
		$this->template->title('Tambah Produk');
		$this->render('backend/standart/administrator/produk/produk_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('produk_add', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('nama_produk', 'Nama Produk', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('kode_produk', 'Kode Produk', 'trim|required|max_length[30]');

		if ($this->form_validation->run()) {
			$barcode_input = $this->input->post('barcode');
			$data = [
				'kode_produk'       => $this->input->post('kode_produk'),
				'barcode'           => !empty($barcode_input) ? $barcode_input : $this->model_produk->generate_barcode(),
				'nama_produk'       => $this->input->post('nama_produk'),
				'kategori_id'       => $this->input->post('kategori_id') ?: null,
				'satuan_id'         => $this->input->post('satuan_id') ?: null,
				'default_rak_id'    => $this->input->post('default_rak_id') ?: null,
				'harga_beli'        => str_replace(['.', ','], '', $this->input->post('harga_beli')) ?: 0,
				'harga_jual'        => str_replace(['.', ','], '', $this->input->post('harga_jual')) ?: 0,
				'harga_jual_grosir' => str_replace(['.', ','], '', $this->input->post('harga_jual_grosir')) ?: 0,
				'harga_jual_distributor' => str_replace(['.', ','], '', $this->input->post('harga_jual_distributor')) ?: 0,
				'harga_jual_vip'    => str_replace(['.', ','], '', $this->input->post('harga_jual_vip')) ?: 0,
				'stok_minimum'      => str_replace(['.', ','], '', $this->input->post('stok_minimum')) ?: 0,
				'berat'             => $this->input->post('berat') ?: null,
				'deskripsi'         => $this->input->post('deskripsi'),
				'metode_stok'       => $this->input->post('metode_stok') ?: 'FIFO',
				'batch_tracking'    => $this->input->post('batch_tracking') ? 1 : 0,
				'tanggal_kadaluarsa'=> $this->input->post('tanggal_kadaluarsa') ? 1 : 0,
				'status'            => $this->input->post('status') ?: 'aktif',
				'created_by'        => get_user_data('username'),
				'created_at'        => date('Y-m-d H:i:s'),
			];

			$id = $this->model_produk->store($data);

			if ($id) {
				// Inisialisasi stok 0 di semua gudang
				$gudangs = $this->db->get('gudang')->result();
				foreach ($gudangs as $g) {
					$exists = $this->db->get_where('stok', ['produk_id' => $id, 'gudang_id' => $g->id])->row();
					if (!$exists) {
						$this->db->insert('stok', ['produk_id' => $id, 'gudang_id' => $g->id, 'rak_id' => $data['default_rak_id'], 'qty' => 0, 'qty_rusak' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
					}
				}

				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['message'] = 'Produk berhasil disimpan. ' . anchor('administrator/produk/edit/' . $id, 'Edit');
				} else {
					set_message('Produk berhasil disimpan.', 'success');
					$this->data['success']  = true;
					$this->data['redirect'] = base_url('administrator/produk');
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
		$this->is_allowed('produk_update');
		$this->data['produk']    = $this->model_produk->find($id);
		$this->data['kategoris'] = $this->db->get('kategori_produk')->result();
		$this->data['satuans']   = $this->db->get('satuan')->result();
		$this->data['raks']      = $this->db->where('status', 'aktif')->get('rak')->result();
		$this->template->title('Edit Produk');
		$this->render('backend/standart/administrator/produk/produk_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('produk_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('nama_produk', 'Nama Produk', 'trim|required|max_length[200]');

		if ($this->form_validation->run()) {
			$old_produk = $this->model_produk->find($id);

			$data = [
				'nama_produk'       => $this->input->post('nama_produk'),
				'barcode'           => $this->input->post('barcode'),
				'kategori_id'       => $this->input->post('kategori_id') ?: null,
				'satuan_id'         => $this->input->post('satuan_id') ?: null,
				'default_rak_id'    => $this->input->post('default_rak_id') ?: null,
				'harga_beli'        => str_replace(['.', ','], '', $this->input->post('harga_beli')) ?: 0,
				'harga_jual'        => str_replace(['.', ','], '', $this->input->post('harga_jual')) ?: 0,
				'harga_jual_grosir' => str_replace(['.', ','], '', $this->input->post('harga_jual_grosir')) ?: 0,
				'harga_jual_distributor' => str_replace(['.', ','], '', $this->input->post('harga_jual_distributor')) ?: 0,
				'harga_jual_vip'    => str_replace(['.', ','], '', $this->input->post('harga_jual_vip')) ?: 0,
				'stok_minimum'      => str_replace(['.', ','], '', $this->input->post('stok_minimum')) ?: 0,
				'berat'             => $this->input->post('berat') ?: null,
				'deskripsi'         => $this->input->post('deskripsi'),
				'metode_stok'       => $this->input->post('metode_stok') ?: 'FIFO',
				'batch_tracking'    => $this->input->post('batch_tracking') ? 1 : 0,
				'tanggal_kadaluarsa'=> $this->input->post('tanggal_kadaluarsa') ? 1 : 0,
				'status'            => $this->input->post('status') ?: 'aktif',
				'updated_at'        => date('Y-m-d H:i:s'),
			];

			$save = $this->model_produk->change($id, $data);

			if ($save) {
				// Audit Log Perubahan Harga (Master Update)
				if ($old_produk) {
					$old_hb = (float)$old_produk->harga_beli;
					$new_hb = (float)$data['harga_beli'];
					$old_hj = (float)$old_produk->harga_jual;
					$new_hj = (float)$data['harga_jual'];

					if ($old_hb != $new_hb || $old_hj != $new_hj) {
						$this->db->insert('produk_harga_log', [
							'produk_id'       => $id,
							'tipe_perubahan'  => 'master_update',
							'harga_beli_lama' => $old_hb,
							'harga_beli_baru' => $new_hb,
							'harga_jual_lama' => $old_hj,
							'harga_jual_baru' => $new_hj,
							'catatan'         => 'Perubahan Manual pada Edit Master Produk',
							'created_by'      => get_user_data('username') ?: 'System',
							'created_at'      => date('Y-m-d H:i:s'),
						]);
					}
				}

				set_message('Produk berhasil diperbarui.', 'success');
				$this->data['success']  = true;
				$this->data['redirect'] = base_url('administrator/produk');
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
		$this->is_allowed('produk_view');
		$this->data['produk'] = $this->model_produk->join_available()->find($id);
		$this->data['stok_list'] = $this->db->select('stok.*, gudang.nama_gudang')
			->join('gudang', 'gudang.id = stok.gudang_id')
			->get_where('stok', ['produk_id' => $id])->result();
		$this->data['riwayat_harga'] = $this->model_produk->get_riwayat_harga($id);
		$this->data['harga_logs']    = $this->db->where('produk_id', $id)->order_by('id', 'DESC')->get('produk_harga_log')->result();
		$this->template->title('Detail Produk');
		$this->render('backend/standart/administrator/produk/produk_view', $this->data);
	}

	public function delete($id = null)
	{
		$this->is_allowed('produk_delete');
		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->model_produk->remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $aid) {
				$remove = $this->model_produk->remove($aid);
			}
		}

		set_message($remove ? 'Produk berhasil dihapus.' : 'Gagal menghapus.', $remove ? 'success' : 'error');
		redirect_back();
	}

	public function get_harga()
	{
		$produk_id  = $this->input->get('produk_id');
		$tipe       = $this->input->get('tipe');
		$qty        = $this->input->get('qty') ?: 1;

		$this->load->model('model_pelanggan');
		$harga = $this->model_pelanggan->get_harga($produk_id, $tipe, $qty);
		$this->response(['harga' => $harga, 'success' => true]);
	}

	public function cek_stok()
	{
		$produk_id  = $this->input->get('produk_id');
		$gudang_id  = $this->input->get('gudang_id');
		$stok = $this->model_produk->get_stok($produk_id, $gudang_id);
		$this->response(['stok' => $stok, 'success' => true]);
	}

	public function kartu_stok($id)
	{
		$this->is_allowed('produk_view');
		$this->data['produk']    = $this->model_produk->join_available()->find($id);
		$this->data['logs']      = $this->db->select('stok_log.*, gudang.nama_gudang')
			->join('gudang', 'gudang.id = stok_log.gudang_id', 'LEFT')
			->where('stok_log.produk_id', $id)
			->order_by('stok_log.id', 'ASC')
			->get('stok_log')->result();

		$this->template->title('Kartu Stok & Audit Ledger Produk');
		$this->render('backend/standart/administrator/produk/produk_kartu_stok', $this->data);
	}

	public function export()
	{
		$this->is_allowed('produk_export');
		$this->model_produk->export('produk', 'produk');
	}

	public function export_pdf()
	{
		$this->is_allowed('produk_export');
		$this->model_produk->pdf('produk', 'produk');
	}

}
