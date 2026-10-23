<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Templates Controller
*| --------------------------------------------------------------------------
*| Templates site
*|
*/
class Templates extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_templates');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Templatess
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('templates_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['templatess'] = $this->model_templates->get($filter, $field, $this->limit_page, $offset);
		$this->data['templates_counts'] = $this->model_templates->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/templates/index/',
			'total_rows'   => $this->model_templates->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Template Desain');
		$this->render('backend/standart/administrator/templates/templates_list', $this->data);
	}
	
	/**
	* Add new templatess
	*
	*/
	public function add()
	{
		$this->is_allowed('templates_add');

		$this->template->title('Tambah Template Desain');
		$this->render('backend/standart/administrator/templates/templates_add', $this->data);
	}

	/**
	* Add New Templatess
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('templates_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('slug', 'Slug', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('templates_preview_image_name', 'Preview Image', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('demo_url', 'Demo Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('default_config', 'Default Config', 'trim');
		$this->form_validation->set_rules('tier', 'Tier', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Is Active', 'trim|required|max_length[1]');
		
		$this->form_validation->set_rules('price', 'Harga', 'trim|numeric');
		

		if ($this->form_validation->run()) {
			$templates_preview_image_uuid = $this->input->post('templates_preview_image_uuid');
			$templates_preview_image_name = $this->input->post('templates_preview_image_name');
		
			$save_data = [
				'name' => $this->input->post('name'),
				'slug' => $this->input->post('slug'),
				'demo_url' => $this->input->post('demo_url'),
				'category' => $this->input->post('category'),
				'default_config' => $this->input->post('default_config'),
				'tier' => $this->input->post('tier'),
				'is_active' => $this->input->post('is_active'),
			];

			if (!is_dir(FCPATH . '/uploads/templates/')) {
				mkdir(FCPATH . '/uploads/templates/');
			}

			if (!empty($templates_preview_image_name)) {
				$templates_preview_image_name_copy = date('YmdHis') . '-' . $templates_preview_image_name;

				rename(FCPATH . 'uploads/tmp/' . $templates_preview_image_uuid . '/' . $templates_preview_image_name, 
						FCPATH . 'uploads/templates/' . $templates_preview_image_name_copy);

				if (!is_file(FCPATH . '/uploads/templates/' . $templates_preview_image_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['preview_image'] = $templates_preview_image_name_copy;
			}
		
			
			$save_templates = $this->model_templates->store($save_data);
            

			if ($save_templates) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_templates;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/templates/edit/' . $save_templates, 'Edit Templates'),
						anchor('administrator/templates', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/templates/edit/' . $save_templates, 'Edit Templates')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/templates');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/templates');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Templatess
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('templates_update');

		$this->data['templates'] = $this->model_templates->find($id);

		$this->template->title('Edit Template Desain');
		$this->render('backend/standart/administrator/templates/templates_update', $this->data);
	}

	/**
	* Update Templatess
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('templates_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('slug', 'Slug', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('templates_preview_image_name', 'Preview Image', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('demo_url', 'Demo Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('default_config', 'Default Config', 'trim');
		$this->form_validation->set_rules('tier', 'Tier', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Is Active', 'trim|required|max_length[1]');
		
		$this->form_validation->set_rules('price', 'Harga', 'trim|numeric');
		
		if ($this->form_validation->run()) {
			$templates_preview_image_uuid = $this->input->post('templates_preview_image_uuid');
			$templates_preview_image_name = $this->input->post('templates_preview_image_name');
		
			$save_data = [
				'name' => $this->input->post('name'),
				'slug' => $this->input->post('slug'),
				'demo_url' => $this->input->post('demo_url'),
				'category' => $this->input->post('category'),
				'default_config' => $this->input->post('default_config'),
				'tier' => $this->input->post('tier'),
				'is_active' => $this->input->post('is_active'),
			];

			if (!is_dir(FCPATH . '/uploads/templates/')) {
				mkdir(FCPATH . '/uploads/templates/');
			}

			if (!empty($templates_preview_image_uuid)) {
				$templates_preview_image_name_copy = date('YmdHis') . '-' . $templates_preview_image_name;

				rename(FCPATH . 'uploads/tmp/' . $templates_preview_image_uuid . '/' . $templates_preview_image_name, 
						FCPATH . 'uploads/templates/' . $templates_preview_image_name_copy);

				if (!is_file(FCPATH . '/uploads/templates/' . $templates_preview_image_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['preview_image'] = $templates_preview_image_name_copy;
			}
		
			
			$save_templates = $this->model_templates->change($id, $save_data);

			if ($save_templates) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/templates', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/templates');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/templates');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Templatess
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('templates_delete');

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
            set_message(cclang('has_been_deleted', 'templates'), 'success');
        } else {
            set_message(cclang('error_delete', 'templates'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Templatess
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('templates_view');

		$this->data['templates'] = $this->model_templates->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Detail Template Desain');
		$this->render('backend/standart/administrator/templates/templates_view', $this->data);
	}
	
	/**
	* delete Templatess
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$templates = $this->model_templates->find($id);

		if (!empty($templates->preview_image)) {
			$path = FCPATH . '/uploads/templates/' . $templates->preview_image;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_templates->remove($id);
	}
	
	/**
	* Upload Image Templates	* 
	* @return JSON
	*/
	public function upload_preview_image_file()
	{
		if (!$this->is_allowed('templates_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'templates',
		]);
	}

	/**
	* Delete Image Templates	* 
	* @return JSON
	*/
	public function delete_preview_image_file($uuid)
	{
		if (!$this->is_allowed('templates_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'preview_image', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'templates',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/templates/'
        ]);
	}

	/**
	* Get Image Templates	* 
	* @return JSON
	*/
	public function get_preview_image_file($id)
	{
		if (!$this->is_allowed('templates_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$templates = $this->model_templates->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'preview_image', 
            'table_name'        => 'templates',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/templates/',
            'delete_endpoint'   => 'administrator/templates/delete_preview_image_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('templates_export');

		$this->model_templates->export('templates', 'templates');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('templates_export');

		$this->model_templates->pdf('templates', 'templates');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('templates_export');

		$table = $title = 'templates';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_templates->find($id);
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


/* End of file templates.php */
/* Location: ./application/controllers/administrator/Templates.php */