<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Model_purchase_order extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'purchase_order';
	private $field_search 	= ['purchase_order.nomor_po', 'supplier.nama_supplier', 'purchase_order.status'];

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
		$this->db->select('purchase_order.*, supplier.nama_supplier, supplier.kode_supplier, gudang.nama_gudang');
		$this->db->join('supplier', 'supplier.id = purchase_order.supplier_id', 'LEFT');
		$this->db->join('gudang', 'gudang.id = purchase_order.gudang_id', 'LEFT');
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
        $this->db->order_by('purchase_order.'.$this->primary_key, 'DESC');
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

	public function find_with_detail($id)
	{
		$this->join_available();
		return $this->db->get_where($this->table_name, ['purchase_order.id' => $id])->row();
	}

	public function get_items($po_id)
	{
		return $this->db->select('purchase_order_item.*, produk.nama_produk, produk.kode_produk')
			->join('produk', 'produk.id = purchase_order_item.produk_id')
			->get_where('purchase_order_item', ['po_id' => $po_id])->result();
	}

	public function generate_nomor()
	{
		$prefix = 'PO-' . date('Ym') . '-';
		$last = $this->db->like('nomor_po', $prefix, 'after')
			->order_by('id', 'DESC')
			->limit(1)
			->get($this->table_name)->row();
		$num = $last ? ((int) substr($last->nomor_po, -4)) + 1 : 1;
		return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
	}

	public function update_total($po_id)
	{
		$items = $this->db->get_where('purchase_order_item', ['po_id' => $po_id])->result();
		$subtotal_bersih = 0;
        $total_diskon_nominal = 0;
		foreach ($items as $item) {
			$subtotal_bersih += $item->subtotal;
            $total_diskon_nominal += $item->diskon_nominal;
		}
		$po = $this->find($po_id);
		$pajak_nominal = $subtotal_bersih * ($po->pajak_persen / 100);
		$total = $subtotal_bersih + $pajak_nominal;

		$total_idr = $total * ($po->kurs ?: 1);
		$approval_level = ($total_idr >= 50000000) ? 2 : 1;

		$this->db->update($this->table_name, [
			'subtotal'       => $subtotal_bersih,
            'diskon'         => $total_diskon_nominal,
			'pajak_nominal'  => $pajak_nominal,
			'total'          => $total,
			'approval_level' => $approval_level,
		], ['id' => $po_id]);
	}

	public function record_price_history($po_id)
	{
		$po = $this->find($po_id);
		if (!$po) return;
		$items = $this->get_items($po_id);
		foreach ($items as $item) {
			$this->db->insert('riwayat_harga_beli', [
				'produk_id'   => $item->produk_id,
				'supplier_id' => $po->supplier_id,
				'harga_beli'  => $item->harga_satuan,
				'nomor_po'    => $po->nomor_po,
				'tanggal'     => $po->tanggal_po,
				'created_at'  => date('Y-m-d H:i:s'),
			]);
		}
	}

	public function get_penerimaan_list($po_id)
	{
		return $this->db->select('penerimaan_barang.*, gudang.nama_gudang')
			->join('gudang', 'gudang.id = penerimaan_barang.gudang_id', 'LEFT')
			->order_by('penerimaan_barang.id', 'DESC')
			->get_where('penerimaan_barang', ['po_id' => $po_id])->result();
	}

	public function get_draft_penerimaan($po_id)
	{
		return $this->db->get_where('penerimaan_barang', ['po_id' => $po_id, 'status' => 'draft'])->row();
	}


}

/* End of file Model_purchase_order.php */
/* Location: ./modules/purchase_order/models/Model_purchase_order.php */
