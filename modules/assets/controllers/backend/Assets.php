<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Assets Controller
*| --------------------------------------------------------------------------
*| Assets site
*|
*/
class Assets extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_assets');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Assetss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('assets_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['assetss'] = $this->model_assets->get($filter, $field, $this->limit_page, $offset);
		$this->data['assets_counts'] = $this->model_assets->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/assets/index/',
			'total_rows'   => $this->model_assets->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Assets List');
		$this->render('backend/standart/administrator/assets/assets_list', $this->data);
	}
	
	/**
	* Add new assetss
	*
	*/
	public function add()
	{
		$this->is_allowed('assets_add');

		$this->template->title('Assets New');
		$this->render('backend/standart/administrator/assets/assets_add', $this->data);
	}

	/**
	* Add New Assetss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('assets_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('category_id', 'Category Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('uploaded_by', 'Uploaded By', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('assets_file_url_name', 'File Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('thumbnail_url', 'Thumbnail Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tags', 'Tags', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('metadata', 'Metadata', 'trim|required');
		$this->form_validation->set_rules('is_public', 'Is Public', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('is_active', 'Is Active', 'trim|required|max_length[1]');
		

		if ($this->form_validation->run()) {
			$assets_file_url_uuid = $this->input->post('assets_file_url_uuid');
			$assets_file_url_name = $this->input->post('assets_file_url_name');
		
			$save_data = [
				'category_id' => $this->input->post('category_id'),
				'uploaded_by' => $this->input->post('uploaded_by'),
				'title' => $this->input->post('title'),
				'thumbnail_url' => $this->input->post('thumbnail_url'),
				'tags' => $this->input->post('tags'),
				'metadata' => $this->input->post('metadata'),
				'is_public' => $this->input->post('is_public'),
				'is_active' => $this->input->post('is_active'),
			];

			if (!is_dir(FCPATH . '/uploads/assets/')) {
				mkdir(FCPATH . '/uploads/assets/');
			}

			if (!empty($assets_file_url_name)) {
				$assets_file_url_name_copy = date('YmdHis') . '-' . $assets_file_url_name;

				rename(FCPATH . 'uploads/tmp/' . $assets_file_url_uuid . '/' . $assets_file_url_name, 
						FCPATH . 'uploads/assets/' . $assets_file_url_name_copy);

				if (!is_file(FCPATH . '/uploads/assets/' . $assets_file_url_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_url'] = $assets_file_url_name_copy;
			}
		
			
			$save_assets = $this->model_assets->store($save_data);
            

			if ($save_assets) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_assets;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/assets/edit/' . $save_assets, 'Edit Assets'),
						anchor('administrator/assets', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/assets/edit/' . $save_assets, 'Edit Assets')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/assets');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/assets');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Assetss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('assets_update');

		$this->data['assets'] = $this->model_assets->find($id);

		$this->template->title('Assets Update');
		$this->render('backend/standart/administrator/assets/assets_update', $this->data);
	}

	/**
	* Update Assetss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('assets_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('category_id', 'Category Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('uploaded_by', 'Uploaded By', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('assets_file_url_name', 'File Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('thumbnail_url', 'Thumbnail Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tags', 'Tags', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('metadata', 'Metadata', 'trim|required');
		$this->form_validation->set_rules('is_public', 'Is Public', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('is_active', 'Is Active', 'trim|required|max_length[1]');
		
		if ($this->form_validation->run()) {
			$assets_file_url_uuid = $this->input->post('assets_file_url_uuid');
			$assets_file_url_name = $this->input->post('assets_file_url_name');
		
			$save_data = [
				'category_id' => $this->input->post('category_id'),
				'uploaded_by' => $this->input->post('uploaded_by'),
				'title' => $this->input->post('title'),
				'thumbnail_url' => $this->input->post('thumbnail_url'),
				'tags' => $this->input->post('tags'),
				'metadata' => $this->input->post('metadata'),
				'is_public' => $this->input->post('is_public'),
				'is_active' => $this->input->post('is_active'),
			];

			if (!is_dir(FCPATH . '/uploads/assets/')) {
				mkdir(FCPATH . '/uploads/assets/');
			}

			if (!empty($assets_file_url_uuid)) {
				$assets_file_url_name_copy = date('YmdHis') . '-' . $assets_file_url_name;

				rename(FCPATH . 'uploads/tmp/' . $assets_file_url_uuid . '/' . $assets_file_url_name, 
						FCPATH . 'uploads/assets/' . $assets_file_url_name_copy);

				if (!is_file(FCPATH . '/uploads/assets/' . $assets_file_url_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_url'] = $assets_file_url_name_copy;
			}
		
			
			$save_assets = $this->model_assets->change($id, $save_data);

			if ($save_assets) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/assets', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/assets');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/assets');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Assetss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('assets_delete');

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
            set_message(cclang('has_been_deleted', 'assets'), 'success');
        } else {
            set_message(cclang('error_delete', 'assets'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Assetss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('assets_view');

		$this->data['assets'] = $this->model_assets->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Assets Detail');
		$this->render('backend/standart/administrator/assets/assets_view', $this->data);
	}
	
	/**
	* delete Assetss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$assets = $this->model_assets->find($id);

		if (!empty($assets->file_url)) {
			$path = FCPATH . '/uploads/assets/' . $assets->file_url;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_assets->remove($id);
	}
	
	/**
	* Upload Image Assets	* 
	* @return JSON
	*/
	public function upload_file_url_file()
	{
		if (!$this->is_allowed('assets_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'assets',
		]);
	}

	/**
	* Delete Image Assets	* 
	* @return JSON
	*/
	public function delete_file_url_file($uuid)
	{
		if (!$this->is_allowed('assets_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_url', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'assets',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/assets/'
        ]);
	}

	/**
	* Get Image Assets	* 
	* @return JSON
	*/
	public function get_file_url_file($id)
	{
		if (!$this->is_allowed('assets_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$assets = $this->model_assets->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_url', 
            'table_name'        => 'assets',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/assets/',
            'delete_endpoint'   => 'administrator/assets/delete_file_url_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('assets_export');

		$this->model_assets->export('assets', 'assets');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('assets_export');

		$this->model_assets->pdf('assets', 'assets');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('assets_export');

		$table = $title = 'assets';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_assets->find($id);
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


/* End of file assets.php */
/* Location: ./application/controllers/administrator/Assets.php */