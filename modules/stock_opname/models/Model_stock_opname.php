<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Model_stock_opname extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'stock_opname';
	private $field_search 	= ['stock_opname.nomor_opname', 'gudang.nama_gudang', 'stock_opname.status'];

	public function __construct()
	{
		$config = array(
			'primary_key' 	=> $this->primary_key,
		 	'table_name' 	=> $this->table_name,
		 	'field_search' 	=> $this->field_search,
		 );

		parent::__construct($config);
	}

	public function join_available()
	{
		$this->db->select('stock_opname.*, gudang.nama_gudang');
		$this->db->join('gudang', 'gudang.id = stock_opname.gudang_id', 'LEFT');
		return $this;
	}

	public function count_all($q = '', $field = '')
	{
		$this->join_available();
		$where = $this->build_where($q, $field);
        $this->db->where($where);
		return $this->db->get($this->table_name)->num_rows();
	}

	public function get($q = '', $field = '', $limit = 0, $offset = 0)
	{
		$this->join_available();
		$where = $this->build_where($q, $field);
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('stock_opname.'.$this->primary_key, 'DESC');
		return $this->db->get($this->table_name)->result();
	}

	private function build_where($q, $field)
	{
		$iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
		$field = $this->scurity($field);

        if (empty($field)) {
	        foreach ($this->field_search as $f) {
	            if ($iterasi == 1) {
	                $where .= "(" . $f . " LIKE '%" . $q . "%' ";
	            } else if ($iterasi == $num) {
	                $where .= "OR " . $f . " LIKE '%" . $q . "%') ";
	            } else {
	                $where .= "OR " . $f . " LIKE '%" . $q . "%' ";
	            }
	            $iterasi++;
	        }
        } else {
        	$where .= "(" . $field . " LIKE '%" . $q . "%' )";
        }
		return $where;
	}

	public function generate_nomor()
	{
		$prefix = 'OPN-' . date('Ym') . '-';
		$last = $this->db->like('nomor_opname', $prefix, 'after')
			->order_by('id', 'DESC')
			->limit(1)
			->get($this->table_name)->row();
		$num = $last ? ((int) substr($last->nomor_opname, -4)) + 1 : 1;
		return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
	}

	public function get_items($opname_id)
	{
		return $this->db->select('stock_opname_item.*, produk.nama_produk, produk.kode_produk, satuan.singkatan, rak.kode_rak')
			->join('produk', 'produk.id = stock_opname_item.produk_id')
			->join('satuan', 'satuan.id = produk.satuan_id', 'LEFT')
			->join('rak', 'rak.id = stock_opname_item.rak_id', 'LEFT')
			->get_where('stock_opname_item', ['opname_id' => $opname_id])->result();
	}

	/**
	 * Load stok sistem ke item opname berdasarkan gudang
	 */
	public function load_stok_sistem($opname_id)
	{
		$opname = $this->find($opname_id);
		
		$this->db->select('stok.*, produk.nama_produk, produk.kode_produk, produk.harga_beli');
		$this->db->join('produk', 'produk.id = stok.produk_id');
		$this->db->where('stok.gudang_id', $opname->gudang_id);
		
		// Cycle Counting Smart Rotation Algorithm
		if ($opname->jadwal == 'harian' || $opname->jadwal == 'mingguan') {
			$subquery = "SELECT soi.produk_id, MAX(so.tanggal_opname) as last_counted 
						 FROM stock_opname_item soi 
						 JOIN stock_opname so ON so.id = soi.opname_id 
						 WHERE so.status = 'approved' AND so.gudang_id = " . $this->db->escape($opname->gudang_id) . " 
						 GROUP BY soi.produk_id";
			
			$this->db->join("($subquery) as history", "history.produk_id = stok.produk_id", "left");
			// NULLs (never counted) will be treated as smallest value in MySQL ASC, appearing first!
			$this->db->order_by('history.last_counted', 'ASC'); 
			$this->db->order_by('stok.qty', 'DESC'); 
			
			$limit = ($opname->jadwal == 'harian') ? 15 : 50;
			$this->db->limit($limit);
		}
		
		$stok_list = $this->db->get('stok')->result();

		// Hapus item lama jika masih draft
		$this->db->delete('stock_opname_item', ['opname_id' => $opname_id]);

		foreach ($stok_list as $stok) {
			$this->db->insert('stock_opname_item', [
				'opname_id' => $opname_id,
				'produk_id' => $stok->produk_id,
				'stok_sistem' => $stok->qty,
				'stok_fisik' => 0,
				'selisih' => 0,
				'harga_satuan' => $stok->harga_beli,
				'nilai_selisih' => 0,
				'status_sesuai' => 0,
			]);
		}
	}

	/**
	 * Approve dan terapkan penyesuaian stok
	 */
	public function approve($opname_id, $user)
	{
		$items = $this->get_items($opname_id);
		$opname = $this->find($opname_id);

		foreach ($items as $item) {
			if ($item->selisih != 0) {
				// Update stok
				$stok = $this->db->get_where('stok', ['produk_id' => $item->produk_id, 'gudang_id' => $opname->gudang_id])->row();
				if ($stok) {
					$this->db->update('stok', [
						'qty' => $item->stok_fisik,
						'updated_at' => date('Y-m-d H:i:s'),
					], ['id' => $stok->id]);
				}

				// Log penyesuaian
				$this->db->insert('stok_log', [
					'produk_id' => $item->produk_id,
					'gudang_id' => $opname->gudang_id,
					'jenis' => 'penyesuaian',
					'qty' => $item->selisih,
					'harga_satuan' => $item->harga_satuan,
					'referensi_type' => 'stock_opname',
					'referensi_id' => $opname_id,
					'keterangan' => 'Penyesuaian Stock Opname ' . $opname->nomor_opname . ' | Alasan: ' . $item->alasan_selisih,
					'created_by' => $user,
					'created_at' => date('Y-m-d H:i:s'),
				]);
			}
		}

		$this->db->update($this->table_name, [
			'status' => 'approved',
			'approved_by' => $user,
			'approved_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		], ['id' => $opname_id]);

		return true;
	}

	/**
	 * Find with gudang join for PDF/view
	 */
	public function find_with_join($id)
	{
		$this->join_available();
		return $this->db->get_where($this->table_name, ['stock_opname.id' => $id])->row();
	}

	/**
	 * Get laporan selisih opname
	 */
	public function get_laporan_selisih($gudang_id = null, $hanya_selisih = true, $produk_id = null, $date_start = null, $date_end = null)
	{
		$this->db->select('stock_opname_item.*, stock_opname.nomor_opname, stock_opname.tanggal_opname, gudang.nama_gudang, produk.nama_produk, produk.kode_produk, rak.kode_rak')
			->from('stock_opname_item')
			->join('stock_opname', 'stock_opname.id = stock_opname_item.opname_id')
			->join('gudang', 'gudang.id = stock_opname.gudang_id', 'left')
			->join('produk', 'produk.id = stock_opname_item.produk_id', 'left')
			->join('rak', 'rak.id = stock_opname_item.rak_id', 'left')
			->order_by('stock_opname.tanggal_opname', 'DESC');

		if ($gudang_id) { $this->db->where('stock_opname.gudang_id', $gudang_id); }
		if ($produk_id) { $this->db->where('stock_opname_item.produk_id', $produk_id); }
		if ($date_start) { $this->db->where('DATE(stock_opname.tanggal_opname) >=', $date_start); }
		if ($date_end) { $this->db->where('DATE(stock_opname.tanggal_opname) <=', $date_end); }
		if ($hanya_selisih) { $this->db->where('stock_opname_item.selisih !=', 0); }

		return $this->db->get()->result();
	}

	/**
	 * Buat cycle count harian dari produk fast moving
	 */
	public function create_cycle_count($gudang_id, $user)
	{
		$nomor = $this->generate_nomor();
		$opname_id = $this->store([
			'nomor_opname'   => $nomor,
			'gudang_id'      => $gudang_id,
			'tanggal_opname' => date('Y-m-d'),
			'jadwal'         => 'harian',
			'status'         => 'draft',
			'catatan'        => 'Cycle Counting Harian (Fast Moving Items)',
			'created_by'     => $user,
			'created_at'     => date('Y-m-d H:i:s'),
		]);

		$fast_moving = $this->db->select('produk_id, COUNT(*) as tx_count')
			->from('sales_order_item')
			->group_by('produk_id')
			->order_by('tx_count', 'DESC')
			->limit(10)
			->get()->result();

		foreach ($fast_moving as $fm) {
			$stok   = $this->db->get_where('stok', ['produk_id' => $fm->produk_id, 'gudang_id' => $gudang_id])->row();
			$produk = $this->db->get_where('produk', ['id' => $fm->produk_id])->row();
			$this->db->insert('stock_opname_item', [
				'opname_id'     => $opname_id,
				'produk_id'     => $fm->produk_id,
				'stok_sistem'   => $stok ? $stok->qty : 0,
				'stok_fisik'    => $stok ? $stok->qty : 0,
				'selisih'       => 0,
				'harga_satuan'  => $produk ? $produk->harga_beli : 0,
				'status_sesuai' => 1,
			]);
		}

		return $opname_id;
	}

	/**
	 * Simpan data stok fisik dari input opname
	 */
	public function save_fisik_items($opname_id, $items_data)
	{
		// Validate mandatory reason for any discrepancy item
		foreach ($items_data as $item_id => $item) {
			$stok_fisik  = (float) $item['stok_fisik'];
			$stok_sistem = (float) $item['stok_sistem'];
			$selisih     = $stok_fisik - $stok_sistem;
			$alasan      = trim(isset($item['alasan_selisih']) ? $item['alasan_selisih'] : '');
			if ($selisih != 0 && empty($alasan)) {
				$op_item = $this->db->select('produk.nama_produk')->join('produk', 'produk.id = stock_opname_item.produk_id')->get_where('stock_opname_item', ['stock_opname_item.id' => $item_id])->row();
				$nama = $op_item ? $op_item->nama_produk : 'produk';
				return ['success' => false, 'message' => 'Alasan selisih WAJIB DIISI untuk produk "' . $nama . '" karena terdapat selisih stok (' . ($selisih > 0 ? '+' : '') . $selisih . ')!'];
			}
		}

		$total_selisih = 0;
		foreach ($items_data as $item_id => $item) {
			$stok_fisik  = (float) $item['stok_fisik'];
			$stok_sistem = (float) $item['stok_sistem'];
			$selisih     = $stok_fisik - $stok_sistem;
			$harga       = (float) $item['harga_satuan'];
			$nilai_sel   = abs($selisih) * $harga;
			$total_selisih += $nilai_sel;

			$this->db->update('stock_opname_item', [
				'stok_fisik'      => $stok_fisik,
				'selisih'         => $selisih,
				'nilai_selisih'   => $nilai_sel,
				'batch_no'        => trim($item['batch_no'] ?? ''),
				'alasan_selisih'  => trim($item['alasan_selisih']),
				'status_sesuai'   => ($selisih == 0) ? 1 : 0,
			], ['id' => $item_id]);
		}
		$this->db->update($this->table_name, [
			'status'              => 'review',
			'total_selisih_nilai' => $total_selisih,
			'updated_at'          => date('Y-m-d H:i:s'),
		], ['id' => $opname_id]);

		return ['success' => true];
	}

}

/* End of file Model_stock_opname.php */
/* Location: ./modules/stock_opname/models/Model_stock_opname.php */

