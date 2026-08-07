<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Model_transfer_stok extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'transfer_stok';
	private $field_search 	= ['transfer_stok.nomor_transfer', 'g1.nama_gudang', 'g2.nama_gudang', 'transfer_stok.status'];

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
		$this->db->select('transfer_stok.*, g1.nama_gudang AS nama_gudang_asal, g2.nama_gudang AS nama_gudang_tujuan');
		$this->db->join('gudang g1', 'g1.id = transfer_stok.gudang_asal_id', 'LEFT');
		$this->db->join('gudang g2', 'g2.id = transfer_stok.gudang_tujuan_id', 'LEFT');
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
        $this->db->order_by('transfer_stok.'.$this->primary_key, 'DESC');
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
		$prefix = 'TRF-' . date('Ym') . '-';
		$last = $this->db->like('nomor_transfer', $prefix, 'after')
			->order_by('id', 'DESC')
			->limit(1)
			->get($this->table_name)->row();
		$num = $last ? ((int) substr($last->nomor_transfer, -4)) + 1 : 1;
		return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
	}

	public function get_items($transfer_id)
	{
		return $this->db->select('transfer_stok_item.*, produk.nama_produk, produk.kode_produk, satuan.singkatan')
			->join('produk', 'produk.id = transfer_stok_item.produk_id')
			->join('satuan', 'satuan.id = produk.satuan_id', 'LEFT')
			->get_where('transfer_stok_item', ['transfer_id' => $transfer_id])->result();
	}

	/**
	 * Proses transfer: kurangi stok asal, tambah stok tujuan
	 */
	public function proses_terima($transfer_id, $user)
	{
		$transfer = $this->find($transfer_id);
		$items = $this->get_items($transfer_id);

		foreach ($items as $item) {
			// Kurangi stok gudang asal
			$stok_asal = $this->db->get_where('stok', ['produk_id' => $item->produk_id, 'gudang_id' => $transfer->gudang_asal_id])->row();
			if ($stok_asal) {
				$this->db->update('stok', ['qty' => max(0, $stok_asal->qty - $item->qty), 'updated_at' => date('Y-m-d H:i:s')], ['id' => $stok_asal->id]);
			}

			// Tambah stok gudang tujuan
			$stok_tujuan = $this->db->get_where('stok', ['produk_id' => $item->produk_id, 'gudang_id' => $transfer->gudang_tujuan_id])->row();
			if ($stok_tujuan) {
				$this->db->update('stok', ['qty' => $stok_tujuan->qty + $item->qty, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $stok_tujuan->id]);
			} else {
				$this->db->insert('stok', ['produk_id' => $item->produk_id, 'gudang_id' => $transfer->gudang_tujuan_id, 'qty' => $item->qty, 'qty_rusak' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
			}

			// Log keluar dari asal
			$this->db->insert('stok_log', ['produk_id' => $item->produk_id, 'gudang_id' => $transfer->gudang_asal_id, 'jenis' => 'transfer_keluar', 'qty' => $item->qty, 'referensi_type' => 'transfer_stok', 'referensi_id' => $transfer_id, 'keterangan' => 'Transfer ke gudang tujuan - ' . $transfer->nomor_transfer, 'created_by' => $user, 'created_at' => date('Y-m-d H:i:s')]);

			// Log masuk ke tujuan
			$this->db->insert('stok_log', ['produk_id' => $item->produk_id, 'gudang_id' => $transfer->gudang_tujuan_id, 'jenis' => 'transfer_masuk', 'qty' => $item->qty, 'referensi_type' => 'transfer_stok', 'referensi_id' => $transfer_id, 'keterangan' => 'Transfer dari gudang asal - ' . $transfer->nomor_transfer, 'created_by' => $user, 'created_at' => date('Y-m-d H:i:s')]);
		}

		$this->db->update($this->table_name, ['status' => 'received', 'received_by' => $user, 'received_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')], ['id' => $transfer_id]);

		return true;
	}

}

/* End of file Model_transfer_stok.php */
/* Location: ./modules/transfer_stok/models/Model_transfer_stok.php */
