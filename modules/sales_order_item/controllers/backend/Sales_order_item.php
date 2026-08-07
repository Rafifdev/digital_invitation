<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Sales Order Item Controller
*| --------------------------------------------------------------------------
*| Sales Order Item site
*|
*/
class Sales_order_item extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_sales_order_item');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Sales Order Items
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('sales_order_item_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['sales_order_items'] = $this->model_sales_order_item->get($filter, $field, $this->limit_page, $offset);
		$this->data['sales_order_item_counts'] = $this->model_sales_order_item->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/sales_order_item/index/',
			'total_rows'   => $this->model_sales_order_item->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->data['produks'] = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->template->title('Sales Order Item List');
		$this->render('backend/standart/administrator/sales_order_item/sales_order_item_list', $this->data);
	}
	
	/**
	* Add new sales_order_items
	*
	*/
	public function add()
	{
		$this->is_allowed('sales_order_item_add');

		$this->data['produks'] = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->template->title('Sales Order Item New');
		$this->render('backend/standart/administrator/sales_order_item/sales_order_item_add', $this->data);
	}

	/**
	* Add New Sales Order Items
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('sales_order_item_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('so_id', 'So Id', 'trim|required');
		$this->form_validation->set_rules('produk_id', 'Produk Id', 'trim|required');
		$this->form_validation->set_rules('qty', 'Qty', 'trim|required|max_length[18]');
		$this->form_validation->set_rules('satuan', 'Satuan', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('harga_satuan', 'Harga Satuan', 'trim|required|max_length[18]');
		$this->form_validation->set_rules('diskon_persen', 'Diskon Persen', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('diskon_nominal', 'Diskon Nominal', 'trim|required|max_length[18]');
		$this->form_validation->set_rules('subtotal', 'Subtotal', 'trim|required|max_length[18]');
		$this->form_validation->set_rules('catatan', 'Catatan', 'trim|required|max_length[300]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'so_id' => $this->input->post('so_id'),
				'produk_id' => $this->input->post('produk_id'),
				'qty' => $this->input->post('qty'),
				'satuan' => $this->input->post('satuan'),
				'harga_satuan' => $this->input->post('harga_satuan'),
				'diskon_persen' => $this->input->post('diskon_persen'),
				'diskon_nominal' => $this->input->post('diskon_nominal'),
				'subtotal' => $this->input->post('subtotal'),
				'catatan' => $this->input->post('catatan'),
			];

			
			$save_sales_order_item = $this->model_sales_order_item->store($save_data);
            

			if ($save_sales_order_item) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_sales_order_item;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/sales_order_item/edit/' . $save_sales_order_item, 'Edit Sales Order Item'),
						anchor('administrator/sales_order_item', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/sales_order_item/edit/' . $save_sales_order_item, 'Edit Sales Order Item')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/sales_order_item');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/sales_order_item');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Sales Order Items
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('sales_order_item_update');

		$this->data['sales_order_item'] = $this->model_sales_order_item->find($id);

		$this->data['produks'] = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->template->title('Sales Order Item Update');
		$this->render('backend/standart/administrator/sales_order_item/sales_order_item_update', $this->data);
	}

	/**
	* Update Sales Order Items
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('sales_order_item_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('so_id', 'So Id', 'trim|required');
		$this->form_validation->set_rules('produk_id', 'Produk Id', 'trim|required');
		$this->form_validation->set_rules('qty', 'Qty', 'trim|required|max_length[18]');
		$this->form_validation->set_rules('satuan', 'Satuan', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('harga_satuan', 'Harga Satuan', 'trim|required|max_length[18]');
		$this->form_validation->set_rules('diskon_persen', 'Diskon Persen', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('diskon_nominal', 'Diskon Nominal', 'trim|required|max_length[18]');
		$this->form_validation->set_rules('subtotal', 'Subtotal', 'trim|required|max_length[18]');
		$this->form_validation->set_rules('catatan', 'Catatan', 'trim|required|max_length[300]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'so_id' => $this->input->post('so_id'),
				'produk_id' => $this->input->post('produk_id'),
				'qty' => $this->input->post('qty'),
				'satuan' => $this->input->post('satuan'),
				'harga_satuan' => $this->input->post('harga_satuan'),
				'diskon_persen' => $this->input->post('diskon_persen'),
				'diskon_nominal' => $this->input->post('diskon_nominal'),
				'subtotal' => $this->input->post('subtotal'),
				'catatan' => $this->input->post('catatan'),
			];

			
			$save_sales_order_item = $this->model_sales_order_item->change($id, $save_data);

			if ($save_sales_order_item) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/sales_order_item', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/sales_order_item');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/sales_order_item');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Sales Order Items
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('sales_order_item_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'sales_order_item'), 'success');
        } else {
            set_message(cclang('error_delete', 'sales_order_item'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Sales Order Items
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('sales_order_item_view');

		$this->data['sales_order_item'] = $this->model_sales_order_item->join_avaiable()->filter_avaiable()->find($id);

		$this->data['produks'] = $this->db->where('status', 'aktif')->get('produk')->result();
		$this->template->title('Sales Order Item Detail');
		$this->render('backend/standart/administrator/sales_order_item/sales_order_item_view', $this->data);
	}
	
	/**
	* delete Sales Order Items
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$sales_order_item = $this->model_sales_order_item->find($id);

		
		
		return $this->model_sales_order_item->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('sales_order_item_export');

		$this->model_sales_order_item->export('sales_order_item', 'sales_order_item');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('sales_order_item_export');

		$this->model_sales_order_item->pdf('sales_order_item', 'sales_order_item');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('sales_order_item_export');

		$table = $title = 'sales_order_item';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_sales_order_item->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}
}


/* End of file sales_order_item.php */
/* Location: ./application/controllers/administrator/Sales Order Item.php */