<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Background Categories Controller
*| --------------------------------------------------------------------------
*| Background Categories site
*|
*/
class Background_categories extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_background_categories');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Background Categoriess
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('background_categories_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['background_categoriess'] = $this->model_background_categories->get($filter, $field, $this->limit_page, $offset);
		$this->data['background_categories_counts'] = $this->model_background_categories->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/background_categories/index/',
			'total_rows'   => $this->model_background_categories->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kategori Background');
		$this->render('backend/standart/administrator/background_categories/background_categories_list', $this->data);
	}
	
	/**
	* Add new background_categoriess
	*
	*/
	public function add()
	{
		$this->is_allowed('background_categories_add');

		$this->template->title('Tambah Kategori Background');
		$this->render('backend/standart/administrator/background_categories/background_categories_add', $this->data);
	}

	/**
	* Add New Background Categoriess
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('background_categories_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('slug', 'Slug', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('description', 'Description', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'slug' => $this->input->post('slug'),
				'description' => $this->input->post('description'),
			];

			
			$save_background_categories = $this->model_background_categories->store($save_data);
            

			if ($save_background_categories) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_background_categories;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/background_categories/edit/' . $save_background_categories, 'Edit Background Categories'),
						anchor('administrator/background_categories', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/background_categories/edit/' . $save_background_categories, 'Edit Background Categories')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/background_categories');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/background_categories');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Background Categoriess
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('background_categories_update');

		$this->data['background_categories'] = $this->model_background_categories->find($id);

		$this->template->title('Edit Kategori Background');
		$this->render('backend/standart/administrator/background_categories/background_categories_update', $this->data);
	}

	/**
	* Update Background Categoriess
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('background_categories_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('slug', 'Slug', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('description', 'Description', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'slug' => $this->input->post('slug'),
				'description' => $this->input->post('description'),
			];

			
			$save_background_categories = $this->model_background_categories->change($id, $save_data);

			if ($save_background_categories) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/background_categories', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/background_categories');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/background_categories');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Background Categoriess
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('background_categories_delete');

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
            set_message(cclang('has_been_deleted', 'background_categories'), 'success');
        } else {
            set_message(cclang('error_delete', 'background_categories'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Background Categoriess
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('background_categories_view');

		$this->data['background_categories'] = $this->model_background_categories->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Detail Kategori Background');
		$this->render('backend/standart/administrator/background_categories/background_categories_view', $this->data);
	}
	
	/**
	* delete Background Categoriess
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$background_categories = $this->model_background_categories->find($id);

		
		
		return $this->model_background_categories->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('background_categories_export');

		$this->model_background_categories->export('background_categories', 'background_categories');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('background_categories_export');

		$this->model_background_categories->pdf('background_categories', 'background_categories');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('background_categories_export');

		$table = $title = 'background_categories';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_background_categories->find($id);
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


/* End of file background_categories.php */
/* Location: ./application/controllers/administrator/Background Categories.php */