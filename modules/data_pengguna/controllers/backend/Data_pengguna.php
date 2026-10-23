<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Data Pengguna Controller
*| --------------------------------------------------------------------------
*| Data Pengguna site
*|
*/
class Data_pengguna extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_data_pengguna');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Data Penggunas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('data_pengguna_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['data_penggunas'] = $this->model_data_pengguna->get($filter, $field, $this->limit_page, $offset);
		$this->data['data_pengguna_counts'] = $this->model_data_pengguna->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/data_pengguna/index/',
			'total_rows'   => $this->model_data_pengguna->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Daftar User');
		$this->render('backend/standart/administrator/data_pengguna/data_pengguna_list', $this->data);
	}
	
	/**
	* Add new data_penggunas
	*
	*/
	public function add()
	{
		redirect('administrator/user/add');
	}

	/**
	* Add New Data Penggunas
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('data_pengguna_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id', 'Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('full_name', 'Full Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('avatar', 'Avatar', 'trim|required');
		$this->form_validation->set_rules('banned', 'Banned', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('date_created', 'Date Created', 'trim|required');
		$this->form_validation->set_rules('last_login', 'Last Login', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id' => $this->input->post('id'),
				'username' => $this->input->post('username'),
				'full_name' => $this->input->post('full_name'),
				'email' => $this->input->post('email'),
				'phone_number' => $this->input->post('phone_number'),
				'avatar' => $this->input->post('avatar'),
				'banned' => $this->input->post('banned'),
				'date_created' => $this->input->post('date_created'),
				'last_login' => $this->input->post('last_login'),
			];

			
			$save_data_pengguna = $this->model_data_pengguna->store($save_data);
                        $save_data_pengguna = true;
            

			if ($save_data_pengguna) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_data_pengguna;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/data_pengguna/edit/' . $save_data_pengguna, 'Edit Data Pengguna'),
						anchor('administrator/data_pengguna', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/data_pengguna/edit/' . $save_data_pengguna, 'Edit Data Pengguna')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/data_pengguna');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/data_pengguna');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Data Penggunas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		redirect('administrator/user/edit/' . $id);
	}

	/**
	* Update Data Penggunas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('data_pengguna_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id', 'Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('full_name', 'Full Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('avatar', 'Avatar', 'trim|required');
		$this->form_validation->set_rules('banned', 'Banned', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('date_created', 'Date Created', 'trim|required');
		$this->form_validation->set_rules('last_login', 'Last Login', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id' => $this->input->post('id'),
				'username' => $this->input->post('username'),
				'full_name' => $this->input->post('full_name'),
				'email' => $this->input->post('email'),
				'phone_number' => $this->input->post('phone_number'),
				'avatar' => $this->input->post('avatar'),
				'banned' => $this->input->post('banned'),
				'date_created' => $this->input->post('date_created'),
				'last_login' => $this->input->post('last_login'),
			];

			
			$save_data_pengguna = $this->model_data_pengguna->change($id, $save_data);

			if ($save_data_pengguna) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/data_pengguna', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/data_pengguna');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/data_pengguna');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Data Penggunas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('data_pengguna_delete');

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
            set_message(cclang('has_been_deleted', 'data_pengguna'), 'success');
        } else {
            set_message(cclang('error_delete', 'data_pengguna'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Data Penggunas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('data_pengguna_view');

		$this->data['data_pengguna'] = $this->model_data_pengguna->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Detail Data User');
		$this->render('backend/standart/administrator/data_pengguna/data_pengguna_view', $this->data);
	}
	
	/**
	* delete Data Penggunas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$data_pengguna = $this->model_data_pengguna->find($id);

		
		
		return $this->model_data_pengguna->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('data_pengguna_export');

		$this->model_data_pengguna->export('data_pengguna', 'data_pengguna');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('data_pengguna_export');

		$this->model_data_pengguna->pdf('data_pengguna', 'data_pengguna');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('data_pengguna_export');

		$table = $title = 'data_pengguna';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_data_pengguna->find($id);
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


/* End of file data_pengguna.php */
/* Location: ./application/controllers/administrator/Data Pengguna.php */