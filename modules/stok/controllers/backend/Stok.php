<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Stok extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_stok');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('stok_list');
		$filter    = $this->input->get('q');
		$field     = $this->input->get('f');
		$gudang_id = $this->input->get('gudang_id');

		$this->data['stoks']       = $this->model_stok->get($filter, $field, $this->limit_page, $offset, $gudang_id);
		$this->data['stok_counts'] = $this->model_stok->count_all($filter, $field, $gudang_id);
		$this->data['summary']     = $this->model_stok->get_summary_dashboard($gudang_id);
		$this->data['menipis']     = $this->model_stok->get_stok_menipis($gudang_id);
		$this->data['gudangs']     = $this->db->get('gudang')->result();
		$this->data['gudang_sel']  = $gudang_id;

		$config = ['base_url' => 'administrator/stok/index/', 'total_rows' => $this->data['stok_counts'], 'per_page' => $this->limit_page, 'uri_segment' => 4];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Dashboard Stok');
		$this->render('backend/standart/administrator/stok/stok_list', $this->data);
	}

	public function kartu($produk_id)
	{
		$this->is_allowed('stok_list');
		$gudang_id  = $this->input->get('gudang_id');
		$date_start = $this->input->get('date_start');
		$date_end   = $this->input->get('date_end');

		$this->data['produk']     = $this->db->get_where('produk', ['id' => $produk_id])->row();
		$this->data['kartu']      = $this->model_stok->get_kartu_stok($produk_id, $gudang_id, $date_start, $date_end);
		$this->data['gudangs']    = $this->db->get('gudang')->result();
		$this->data['gudang_sel'] = $gudang_id;
		$this->data['date_start'] = $date_start;
		$this->data['date_end']   = $date_end;

		$this->template->title('Kartu Stok - ' . ($this->data['produk'] ? $this->data['produk']->nama_produk : ''));
		$this->render('backend/standart/administrator/stok/stok_kartu', $this->data);
	}

	public function api_summary()
	{
		$gudang_id = $this->input->get('gudang_id');
		$summary   = $this->model_stok->get_summary_dashboard($gudang_id);
		$this->response(['success' => true, 'data' => $summary]);
	}

	public function batch_tracking()
	{
		$this->is_allowed('stok_list');
		$produk_id  = $this->input->get('produk_id');
		$gudang_id  = $this->input->get('gudang_id');
		$date_start = $this->input->get('date_start');
		$date_end   = $this->input->get('date_end');

		// Batch summary: current batches per product per gudang
		$this->db->select('pbi.batch_no, pbi.expired_date, p.kode_produk, p.nama_produk, g.nama_gudang, r.kode_rak, r.baris, r.kolom, r.keterangan as rak_keterangan, MAX(pb.tanggal_terima) as tanggal_terima, MAX(pb.nomor_penerimaan) as nomor_penerimaan, SUM(pbi.qty_baik) as total_qty_masuk');
		$this->db->from('penerimaan_barang_item pbi');
		$this->db->join('penerimaan_barang pb', 'pb.id = pbi.penerimaan_id');
		$this->db->join('produk p', 'p.id = pbi.produk_id');
		$this->db->join('gudang g', 'g.id = pb.gudang_id');
		$this->db->join('stok s', 's.produk_id = pbi.produk_id AND s.gudang_id = pb.gudang_id', 'LEFT');
		$this->db->join('rak r', 'r.id = IFNULL(s.rak_id, p.default_rak_id)', 'LEFT');
		$this->db->where('pb.status', 'confirmed');
		$this->db->where('pbi.batch_no IS NOT NULL', null, false);
		$this->db->where("pbi.batch_no != ''", null, false);
		if ($produk_id) $this->db->where('pbi.produk_id', $produk_id);
		if ($gudang_id) $this->db->where('pb.gudang_id', $gudang_id);
		if ($date_start) $this->db->where('pb.tanggal_terima >=', $date_start);
		if ($date_end) $this->db->where('pb.tanggal_terima <=', $date_end);
		$this->db->group_by('pbi.batch_no, pbi.produk_id, pb.gudang_id, pbi.expired_date, p.kode_produk, p.nama_produk, g.nama_gudang, r.kode_rak, r.baris, r.kolom, r.keterangan');
		$this->db->order_by('tanggal_terima', 'DESC');

		$this->data['batches']    = $this->db->get()->result();
		$this->data['produks']    = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->data['gudangs']    = $this->db->get('gudang')->result();
		$this->data['produk_sel'] = $produk_id;
		$this->data['gudang_sel'] = $gudang_id;
		$this->data['date_start'] = $date_start;
		$this->data['date_end']   = $date_end;

		// Products expiring within 30 days
		$this->data['kadaluarsa_soon'] = $this->db->query("
			SELECT pbi.batch_no, pbi.expired_date, p.kode_produk, p.nama_produk, g.nama_gudang, SUM(pbi.qty_baik) as qty
			FROM penerimaan_barang_item pbi
			JOIN penerimaan_barang pb ON pb.id = pbi.penerimaan_id
			JOIN produk p ON p.id = pbi.produk_id
			JOIN gudang g ON g.id = pb.gudang_id
			WHERE pb.status = 'confirmed' AND pbi.expired_date IS NOT NULL AND pbi.expired_date > '1900-01-01'
			AND pbi.expired_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
			GROUP BY pbi.batch_no, pbi.produk_id, pb.gudang_id, pbi.expired_date, p.kode_produk, p.nama_produk, g.nama_gudang
			ORDER BY pbi.expired_date ASC
		")->result();

		$this->template->title('Laporan Batch & Lot Tracking');
		$this->render('backend/standart/administrator/stok/stok_batch', $this->data);
	}

	public function api_chart()
	{
		// Chart data: stok masuk vs keluar per hari (30 hari terakhir)
		$days = [];
		for ($i = 29; $i >= 0; $i--) {
			$date = date('Y-m-d', strtotime("-{$i} days"));
			$masuk  = $this->db->select('SUM(qty) AS total')->where('jenis', 'masuk')->where('DATE(created_at)', $date)->get('stok_log')->row();
			$keluar = $this->db->select('SUM(qty) AS total')->where('jenis', 'keluar')->where('DATE(created_at)', $date)->get('stok_log')->row();
			$days[] = [
				'date'   => $date,
				'masuk'  => $masuk ? (float) $masuk->total : 0,
				'keluar' => $keluar ? (float) $keluar->total : 0,
			];
		}
		$this->response(['success' => true, 'data' => $days]);
	}

	public function export()
	{
		$this->is_allowed('stok_export');
		$gudang_id = $this->input->get('gudang_id');
		$this->model_stok->join_available();
		if ($gudang_id) $this->db->where('stok.gudang_id', $gudang_id);
		$this->model_stok->export('stok', 'stok_export');
	}

	public function add()
	{
		$this->is_allowed('stok_add');
		$this->data['produks'] = $this->db->get_where('produk', ['status' => 'aktif'])->result();
		$this->data['gudangs'] = $this->db->get_where('gudang', ['status' => 'aktif'])->result();
		$this->template->title('Tambah Data Stok');
		$this->render('backend/standart/administrator/stok/stok_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('stok_add', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('produk_id', 'Produk', 'trim|required|numeric');
		$this->form_validation->set_rules('gudang_id', 'Gudang', 'trim|required|numeric');
		$this->form_validation->set_rules('qty', 'Qty Stok Baik', 'trim|required|numeric');

		if ($this->form_validation->run()) {
			$produk_id = $this->input->post('produk_id');
			$gudang_id = $this->input->post('gudang_id');
			$qty = max(1, (int) round($this->input->post('qty')));
			$qty_rusak = (float) $this->input->post('qty_rusak');

			// Check existing stok
			$existing = $this->db->get_where('stok', ['produk_id' => $produk_id, 'gudang_id' => $gudang_id])->row();
			if ($existing) {
				$this->db->update('stok', ['qty' => $qty, 'qty_rusak' => $qty_rusak, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $existing->id]);
				$id = $existing->id;
			} else {
				$id = $this->model_stok->store([
					'produk_id' => $produk_id,
					'gudang_id' => $gudang_id,
					'qty'       => $qty,
					'qty_rusak' => $qty_rusak,
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}

			// Add stok log
			$this->db->insert('stok_log', [
				'produk_id' => $produk_id,
				'gudang_id' => $gudang_id,
				'jenis'     => 'stok_awal',
				'qty'       => $qty,
				'keterangan'=> 'Input Penyesuaian Manual Stok',
				'created_at'=> date('Y-m-d H:i:s'),
				'created_by'=> get_user_data('username')
			]);

			set_message('Data Stok berhasil disimpan.', 'success');
			$this->data['success']  = true;
			$this->data['redirect'] = base_url('administrator/stok');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function edit($id)
	{
		$this->is_allowed('stok_update');
		$this->data['stok']    = $this->model_stok->find($id);
		$this->data['produks'] = $this->db->get('produk')->result();
		$this->data['gudangs'] = $this->db->get('gudang')->result();
		$this->template->title('Edit Data Stok');
		$this->render('backend/standart/administrator/stok/stok_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('stok_update', false)) {
			$this->response(['success' => false, 'message' => 'Tidak punya akses']); return;
		}

		$this->form_validation->set_rules('qty', 'Qty Stok Baik', 'trim|required|numeric');

		if ($this->form_validation->run()) {
			$data = [
				'qty'       => (float) $this->input->post('qty'),
				'qty_rusak' => (float) $this->input->post('qty_rusak'),
				'updated_at'=> date('Y-m-d H:i:s')
			];
			$this->model_stok->change($id, $data);
			set_message('Data Stok berhasil diperbarui.', 'success');
			$this->data['success']  = true;
			$this->data['redirect'] = base_url('administrator/stok');
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}
		$this->response($this->data);
	}

	public function view($id)
	{
		$this->is_allowed('stok_view');
		$this->model_stok->join_available();
		$this->data['stok']  = $this->db->get_where('stok', ['stok.id' => $id])->row();
		$this->template->title('Detail Stok Barang');
		$this->render('backend/standart/administrator/stok/stok_view', $this->data);
	}

	public function delete($id = null)
	{
		$this->is_allowed('stok_delete');
		$this->model_stok->remove($id);
		set_message('Stok berhasil dihapus.', 'success');
		redirect('administrator/stok');
	}

	public function export_pdf()
	{
		$this->is_allowed('stok_export');
		$this->model_stok->pdf('stok', 'stok');
	}

}
