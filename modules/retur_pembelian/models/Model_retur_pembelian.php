<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Model_retur_pembelian extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'retur_pembelian';
	private $field_search 	= ['retur_pembelian.nomor_retur', 'supplier.nama_supplier', 'retur_pembelian.status'];

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
		$this->db->select('retur_pembelian.*, supplier.nama_supplier, supplier.kode_supplier, gudang.nama_gudang, gudang.alamat as alamat_gudang, penerimaan_barang.nomor_penerimaan, purchase_order.mata_uang, purchase_order.nomor_po, purchase_order.kurs');
		$this->db->join('supplier', 'supplier.id = retur_pembelian.supplier_id', 'LEFT');
		$this->db->join('gudang', 'gudang.id = retur_pembelian.gudang_id', 'LEFT');
		$this->db->join('penerimaan_barang', 'penerimaan_barang.id = retur_pembelian.penerimaan_id', 'LEFT');
		$this->db->join('purchase_order', 'purchase_order.id = penerimaan_barang.po_id', 'LEFT');
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
		$this->db->order_by('retur_pembelian.'.$this->primary_key, 'DESC');
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
		$prefix = 'RPB-' . date('Ym') . '-';
		$last = $this->db->like('nomor_retur', $prefix, 'after')
			->order_by('id', 'DESC')->limit(1)->get($this->table_name)->row();
		$num = $last ? ((int) substr($last->nomor_retur, -4)) + 1 : 1;
		return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
	}

	public function get_items($retur_id)
	{
		return $this->db->select('retur_pembelian_item.*, produk.nama_produk, produk.kode_produk')
			->join('produk', 'produk.id = retur_pembelian_item.produk_id')
			->get_where('retur_pembelian_item', ['retur_pembelian_id' => $retur_id])->result();
	}

	public function konfirmasi($retur_id, $user)
	{
		$retur = $this->find($retur_id);
		$items = $this->get_items($retur_id);

		foreach ($items as $item) {
			$stok = $this->db->get_where('stok', ['produk_id' => $item->produk_id, 'gudang_id' => $retur->gudang_id])->row();
			if ($stok) {
				$this->db->update('stok', ['qty' => max(0, $stok->qty - $item->qty_retur), 'updated_at' => date('Y-m-d H:i:s')], ['id' => $stok->id]);
			}
			$this->db->insert('stok_log', [
				'produk_id' => $item->produk_id,
				'gudang_id' => $retur->gudang_id,
				'jenis' => 'keluar',
				'qty' => $item->qty_retur,
				'harga_satuan' => $item->harga_satuan,
				'referensi_type' => 'retur_pembelian',
				'referensi_id' => $retur_id,
				'keterangan' => 'Retur Pembelian ke Supplier - ' . $retur->nomor_retur,
				'created_by' => $user,
				'created_at' => date('Y-m-d H:i:s'),
			]);
		}

		// Potong Utang Usaha
		if ($retur->total > 0 && $retur->penerimaan_id) {
			$penerimaan = $this->db->get_where('penerimaan_barang', ['id' => $retur->penerimaan_id])->row();
			if ($penerimaan) {
				$po = $penerimaan->po_id ? $this->db->get_where('purchase_order', ['id' => $penerimaan->po_id])->row() : null;
				$kurs = ($po && (float)$po->kurs > 0) ? (float)$po->kurs : 1;
				$mata_uang = $po ? ($po->mata_uang ?: 'IDR') : 'IDR';
				
				$total_retur_idr = ($mata_uang !== 'IDR') ? ($retur->total * $kurs) : $retur->total;
				
				$utang = $this->db->get_where('utang_usaha', ['penerimaan_id' => $retur->penerimaan_id])->row();
				if ($utang) {
					$new_total = max(0, $utang->total - $total_retur_idr);
					$new_sisa = max(0, $utang->sisa - $total_retur_idr);
					$new_status = ($new_sisa <= 0) ? 'lunas' : (($utang->terbayar > 0) ? 'sebagian' : 'belum_bayar');
					$this->db->update('utang_usaha', [
						'total' => $new_total,
						'sisa' => $new_sisa,
						'status' => $new_status,
						'updated_at' => date('Y-m-d H:i:s')
					], ['id' => $utang->id]);
				}
			}
		}

		$this->db->update($this->table_name, ['status' => 'confirmed'], ['id' => $retur_id]);
		return true;
	}

}

/* End of file Model_retur_pembelian.php */
/* Location: ./modules/retur_pembelian/models/Model_retur_pembelian.php */
