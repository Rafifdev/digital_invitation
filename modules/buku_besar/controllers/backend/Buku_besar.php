<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buku_besar extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_buku_besar');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('utang_usaha_list'); // Using existing finance permission scope

		$date_start = $this->input->get('date_start');
		$date_end   = $this->input->get('date_end');
		$category   = $this->input->get('category');
		$search     = $this->input->get('q');

		$this->data['entries']      = $this->model_buku_besar->get_ledger_entries($date_start, $date_end, $category, $search, $this->limit_page, $offset);
		$this->data['total_rows']   = $this->model_buku_besar->count_ledger_entries($date_start, $date_end, $category, $search);
		$this->data['summary']      = $this->model_buku_besar->get_summary_kpi($date_start, $date_end, $category, $search);

		$config = [
			'base_url'    => 'administrator/buku_besar/index/',
			'total_rows'  => $this->data['total_rows'],
			'per_page'    => $this->limit_page,
			'uri_segment' => 4,
			'reuse_query_string' => true,
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->data['filter_date_start'] = $date_start;
		$this->data['filter_date_end']   = $date_end;
		$this->data['filter_category']   = $category;
		$this->data['filter_search']     = $search;

		$this->template->title('Buku Besar (General Ledger & Audit Transaksi)');
		$this->render('backend/standart/administrator/buku_besar/buku_besar_list', $this->data);
	}

	public function export_excel()
	{
		$this->is_allowed('utang_usaha_list');

		$date_start = $this->input->get('date_start');
		$date_end   = $this->input->get('date_end');
		$category   = $this->input->get('category');
		$search     = $this->input->get('q');

		$entries = $this->model_buku_besar->get_ledger_entries($date_start, $date_end, $category, $search, 0, 0);

		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=Buku_Besar_Laporan_Keuangan_" . date('Ymd_His') . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo '<table border="1">';
		echo '<thead>
				<tr style="background-color: #3b82f6; color: #ffffff;">
					<th>No</th>
					<th>Tanggal Transaksi</th>
					<th>No. Referensi / Dokumen</th>
					<th>Kategori Modul</th>
					<th>Entitas / Pihak Terkait</th>
					<th>Deskripsi / Uraian Transaksi</th>
					<th>Debit / Masuk (Rp)</th>
					<th>Kredit / Keluar (Rp)</th>
				</tr>
			  </thead><tbody>';

		$no = 1;
		$tot_debit = 0;
		$tot_kredit = 0;

		foreach ($entries as $row) {
			$tot_debit += $row->debit;
			$tot_kredit += $row->kredit;
			echo '<tr>';
			echo '<td>' . $no++ . '</td>';
			echo '<td>' . date('d/m/Y', strtotime($row->tanggal)) . '</td>';
			echo '<td>' . _ent($row->nomor_ref) . '</td>';
			echo '<td>' . _ent($row->modul_label) . '</td>';
			echo '<td>' . _ent($row->entitas) . '</td>';
			echo '<td>' . _ent($row->deskripsi) . '</td>';
			echo '<td>' . number_format($row->debit, 0, ',', '.') . '</td>';
			echo '<td>' . number_format($row->kredit, 0, ',', '.') . '</td>';
			echo '</tr>';
		}

		echo '</tbody><tfoot><tr style="font-weight:bold; background-color:#f1f5f9;">';
		echo '<td colspan="6" align="right">TOTAL KESELURUHAN:</td>';
		echo '<td>Rp ' . number_format($tot_debit, 0, ',', '.') . '</td>';
		echo '<td>Rp ' . number_format($tot_kredit, 0, ',', '.') . '</td>';
		echo '</tr></tfoot></table>';
	}
}

/* End of file Buku_besar.php */
/* Location: ./modules/buku_besar/controllers/backend/Buku_besar.php */
