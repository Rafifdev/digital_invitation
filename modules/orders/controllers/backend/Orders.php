<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Orders Controller
*| --------------------------------------------------------------------------
*| Orders site
*|
*/
class Orders extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_orders');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Orderss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('orders_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['orderss'] = $this->model_orders->get($filter, $field, $this->limit_page, $offset);
		$this->data['orders_counts'] = $this->model_orders->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/orders/index/',
			'total_rows'   => $this->model_orders->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Daftar Order');
		$this->render('backend/standart/administrator/orders/orders_list', $this->data);
	}
	
	/**
	* Add new orderss
	*
	*/
	public function add()
	{
		$this->is_allowed('orders_add');

		$this->template->title('Tambah Order Baru');
		$this->render('backend/standart/administrator/orders/orders_add', $this->data);
	}

	/**
	* Add New Orderss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('orders_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('invoice_number', 'Invoice Number', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('user_id', 'User Id', 'trim|required');
		$this->form_validation->set_rules('template_id', 'Template Id', 'trim|required');
		$this->form_validation->set_rules('amount', 'Amount', 'trim|required');
		$this->form_validation->set_rules('payment_status', 'Payment Status', 'trim|required');

		if ($this->form_validation->run()) {
			$now = date('Y-m-d H:i:s');
			$save_data = [
				'invoice_number' => $this->input->post('invoice_number'),
				'user_id' => $this->input->post('user_id'),
				'template_id' => $this->input->post('template_id'),
				'invitation_id' => $this->input->post('invitation_id') ? $this->input->post('invitation_id') : null,
				'amount' => $this->input->post('amount'),
				'payment_method' => $this->input->post('payment_method') ? $this->input->post('payment_method') : 'BCA Transfer',
				'payment_proof' => $this->input->post('payment_proof'),
				'payment_status' => $this->input->post('payment_status'),
				'verified_by' => $this->input->post('verified_by') ? $this->input->post('verified_by') : null,
				'paid_at' => ($this->input->post('payment_status') == 'paid') ? ($this->input->post('paid_at') ?: $now) : null,
				'created_at' => $now,
				'updated_at' => $now,
			];

			
			$save_orders = $this->model_orders->store($save_data);
            

			if ($save_orders) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_orders;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/orders/edit/' . $save_orders, 'Edit Orders'),
						anchor('administrator/orders', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/orders/edit/' . $save_orders, 'Edit Orders')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/orders');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/orders');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Orderss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('orders_update');

		$this->data['orders'] = $this->model_orders->find($id);

		$this->template->title('Edit Transaksi Order');
		$this->render('backend/standart/administrator/orders/orders_update', $this->data);
	}

	/**
	* Update Orderss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('orders_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('invoice_number', 'Invoice Number', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('user_id', 'User Id', 'trim|required');
		$this->form_validation->set_rules('template_id', 'Template Id', 'trim|required');
		$this->form_validation->set_rules('amount', 'Amount', 'trim|required');
		$this->form_validation->set_rules('payment_status', 'Payment Status', 'trim|required');
		
		if ($this->form_validation->run()) {
			$now = date('Y-m-d H:i:s');
			$save_data = [
				'invoice_number' => $this->input->post('invoice_number'),
				'user_id' => $this->input->post('user_id'),
				'template_id' => $this->input->post('template_id'),
				'invitation_id' => $this->input->post('invitation_id') ? $this->input->post('invitation_id') : null,
				'amount' => $this->input->post('amount'),
				'payment_method' => $this->input->post('payment_method'),
				'payment_proof' => $this->input->post('payment_proof'),
				'payment_status' => $this->input->post('payment_status'),
				'verified_by' => $this->input->post('verified_by') ? $this->input->post('verified_by') : null,
				'paid_at' => ($this->input->post('payment_status') == 'paid') ? ($this->input->post('paid_at') ?: $now) : null,
				'updated_at' => $now,
			];

			
			$save_orders = $this->model_orders->change($id, $save_data);

			if ($save_orders) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/orders', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/orders');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/orders');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Orderss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('orders_delete');

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
            set_message(cclang('has_been_deleted', 'orders'), 'success');
        } else {
            set_message(cclang('error_delete', 'orders'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Orderss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('orders_view');

		$this->data['orders'] = $this->model_orders->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Detail Transaksi Order');
		$this->render('backend/standart/administrator/orders/orders_view', $this->data);
	}
	
	/**
	* delete Orderss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$orders = $this->model_orders->find($id);

		
		
		return $this->model_orders->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('orders_export');

		$this->model_orders->export('orders', 'orders');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('orders_export');

		$this->model_orders->pdf('orders', 'orders');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('orders_export');

		$table = $title = 'orders';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_orders->find($id);
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


/* End of file orders.php */
/* Location: ./application/controllers/administrator/Orders.php */