<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Administrator Controller
*| --------------------------------------------------------------------------
*| Administrator site
*|
*/
class Administrator extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_administrator');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Administrators
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('administrator_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['administrators'] = $this->model_administrator->get($filter, $field, $this->limit_page, $offset);
		$this->data['administrator_counts'] = $this->model_administrator->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/administrator/index/',
			'total_rows'   => $this->model_administrator->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Administrator List');
		$this->render('backend/standart/administrator/administrator/administrator_list', $this->data);
	}
	
	/**
	* Add new administrators
	*
	*/
	public function add()
	{
		$this->is_allowed('administrator_add');

		$this->template->title('Administrator New');
		$this->render('backend/standart/administrator/administrator/administrator_add', $this->data);
	}

	/**
	* Add New Administrators
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('administrator_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('bisa_portofolio', 'Bisa Portofolio', 'trim|required');
		$this->form_validation->set_rules('bisa_cashflow', 'Bisa Cashflow', 'trim|required');
		$this->form_validation->set_rules('bisa_artikel', 'Bisa Artikel', 'trim|required');
		$this->form_validation->set_rules('bisa_category', 'Bisa Category', 'trim|required');
		$this->form_validation->set_rules('bisa_promo', 'Bisa Promo', 'trim|required');
		$this->form_validation->set_rules('bisa_whois', 'Bisa Whois', 'trim|required');
		$this->form_validation->set_rules('bisa_contact', 'Bisa Contact', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'username' => $this->input->post('username'),
				'password' => $this->input->post('password'),
				'email' => $this->input->post('email'),
				'bisa_portofolio' => $this->input->post('bisa_portofolio'),
				'bisa_cashflow' => $this->input->post('bisa_cashflow'),
				'bisa_artikel' => $this->input->post('bisa_artikel'),
				'bisa_category' => $this->input->post('bisa_category'),
				'bisa_promo' => $this->input->post('bisa_promo'),
				'bisa_whois' => $this->input->post('bisa_whois'),
				'bisa_contact' => $this->input->post('bisa_contact'),
				'last_login' => date('Y-m-d H:i:s'),
			];

			
			$save_administrator = $this->model_administrator->store($save_data);
            

			if ($save_administrator) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_administrator;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/administrator/edit/' . $save_administrator, 'Edit Administrator'),
						anchor('administrator/administrator', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/administrator/edit/' . $save_administrator, 'Edit Administrator')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/administrator');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/administrator');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Administrators
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('administrator_update');

		$this->data['administrator'] = $this->model_administrator->find($id);

		$this->template->title('Administrator Update');
		$this->render('backend/standart/administrator/administrator/administrator_update', $this->data);
	}

	/**
	* Update Administrators
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('administrator_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[32]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('bisa_portofolio', 'Bisa Portofolio', 'trim|required');
		$this->form_validation->set_rules('bisa_cashflow', 'Bisa Cashflow', 'trim|required');
		$this->form_validation->set_rules('bisa_artikel', 'Bisa Artikel', 'trim|required');
		$this->form_validation->set_rules('bisa_category', 'Bisa Category', 'trim|required');
		$this->form_validation->set_rules('bisa_promo', 'Bisa Promo', 'trim|required');
		$this->form_validation->set_rules('bisa_whois', 'Bisa Whois', 'trim|required');
		$this->form_validation->set_rules('bisa_contact', 'Bisa Contact', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'username' => $this->input->post('username'),
				'password' => $this->input->post('password'),
				'email' => $this->input->post('email'),
				'bisa_portofolio' => $this->input->post('bisa_portofolio'),
				'bisa_cashflow' => $this->input->post('bisa_cashflow'),
				'bisa_artikel' => $this->input->post('bisa_artikel'),
				'bisa_category' => $this->input->post('bisa_category'),
				'bisa_promo' => $this->input->post('bisa_promo'),
				'bisa_whois' => $this->input->post('bisa_whois'),
				'bisa_contact' => $this->input->post('bisa_contact'),
				'last_login' => date('Y-m-d H:i:s'),
			];

			
			$save_administrator = $this->model_administrator->change($id, $save_data);

			if ($save_administrator) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/administrator', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/administrator');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/administrator');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Administrators
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('administrator_delete');

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
            set_message(cclang('has_been_deleted', 'administrator'), 'success');
        } else {
            set_message(cclang('error_delete', 'administrator'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Administrators
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('administrator_view');

		$this->data['administrator'] = $this->model_administrator->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Administrator Detail');
		$this->render('backend/standart/administrator/administrator/administrator_view', $this->data);
	}
	
	/**
	* delete Administrators
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$administrator = $this->model_administrator->find($id);

		
		
		return $this->model_administrator->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('administrator_export');

		$this->model_administrator->export('administrator', 'administrator');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('administrator_export');

		$this->model_administrator->pdf('administrator', 'administrator');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('administrator_export');

		$table = $title = 'administrator';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_administrator->find($id);
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


/* End of file administrator.php */
/* Location: ./application/controllers/administrator/Administrator.php */