<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Backgrounds Controller
*| --------------------------------------------------------------------------
*| Backgrounds site
*|
*/
class Backgrounds extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_backgrounds');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Backgroundss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('backgrounds_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['backgroundss'] = $this->model_backgrounds->get($filter, $field, $this->limit_page, $offset);
		$this->data['backgrounds_counts'] = $this->model_backgrounds->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/backgrounds/index/',
			'total_rows'   => $this->model_backgrounds->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Master Background');
		$this->render('backend/standart/administrator/backgrounds/backgrounds_list', $this->data);
	}
	
	/**
	* Add new backgroundss
	*
	*/
	public function add()
	{
		$this->is_allowed('backgrounds_add');

		$this->template->title('Tambah Master Background');
		$this->render('backend/standart/administrator/backgrounds/backgrounds_add', $this->data);
	}

	/**
	* Add New Backgroundss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('backgrounds_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('backgrounds_image_url_name', 'Image Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('is_active', 'Status Aktif', 'trim|max_length[1]');
		
		

		if ($this->form_validation->run()) {
			$backgrounds_image_url_uuid = $this->input->post('backgrounds_image_url_uuid');
			$backgrounds_image_url_name = $this->input->post('backgrounds_image_url_name');
		
			$save_data = [
				'name' => $this->input->post('name'),
				'category' => $this->input->post('category'),
				'is_active' => $this->input->post('is_active'),
				'created_at' => date('Y-m-d H:i:s'),
			];

			if (!is_dir(FCPATH . '/uploads/backgrounds/')) {
				mkdir(FCPATH . '/uploads/backgrounds/');
			}

			if (!empty($backgrounds_image_url_name)) {
				$backgrounds_image_url_name_copy = date('YmdHis') . '-' . $backgrounds_image_url_name;

				rename(FCPATH . 'uploads/tmp/' . $backgrounds_image_url_uuid . '/' . $backgrounds_image_url_name, 
						FCPATH . 'uploads/backgrounds/' . $backgrounds_image_url_name_copy);

				if (!is_file(FCPATH . '/uploads/backgrounds/' . $backgrounds_image_url_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['image_url'] = $backgrounds_image_url_name_copy;
			}
		
			
			$save_backgrounds = $this->model_backgrounds->store($save_data);
            

			if ($save_backgrounds) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_backgrounds;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/backgrounds/edit/' . $save_backgrounds, 'Edit Backgrounds'),
						anchor('administrator/backgrounds', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/backgrounds/edit/' . $save_backgrounds, 'Edit Backgrounds')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/backgrounds');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/backgrounds');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Backgroundss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('backgrounds_update');

		$this->data['backgrounds'] = $this->model_backgrounds->find($id);

		$this->template->title('Edit Master Background');
		$this->render('backend/standart/administrator/backgrounds/backgrounds_update', $this->data);
	}

	/**
	* Update Backgroundss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('backgrounds_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('backgrounds_image_url_name', 'Image Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('is_active', 'Status Aktif', 'trim|max_length[1]');
		
		
		if ($this->form_validation->run()) {
			$backgrounds_image_url_uuid = $this->input->post('backgrounds_image_url_uuid');
			$backgrounds_image_url_name = $this->input->post('backgrounds_image_url_name');
		
			$save_data = [
				'name' => $this->input->post('name'),
				'category' => $this->input->post('category'),
				'is_active' => $this->input->post('is_active'),
				'created_at' => date('Y-m-d H:i:s'),
			];

			if (!is_dir(FCPATH . '/uploads/backgrounds/')) {
				mkdir(FCPATH . '/uploads/backgrounds/');
			}

			if (!empty($backgrounds_image_url_uuid)) {
				$backgrounds_image_url_name_copy = date('YmdHis') . '-' . $backgrounds_image_url_name;

				rename(FCPATH . 'uploads/tmp/' . $backgrounds_image_url_uuid . '/' . $backgrounds_image_url_name, 
						FCPATH . 'uploads/backgrounds/' . $backgrounds_image_url_name_copy);

				if (!is_file(FCPATH . '/uploads/backgrounds/' . $backgrounds_image_url_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['image_url'] = $backgrounds_image_url_name_copy;
			}
		
			
			$save_backgrounds = $this->model_backgrounds->change($id, $save_data);

			if ($save_backgrounds) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/backgrounds', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/backgrounds');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/backgrounds');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Backgroundss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('backgrounds_delete');

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
            set_message(cclang('has_been_deleted', 'backgrounds'), 'success');
        } else {
            set_message(cclang('error_delete', 'backgrounds'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Backgroundss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('backgrounds_view');

		$this->data['backgrounds'] = $this->model_backgrounds->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Detail Master Background');
		$this->render('backend/standart/administrator/backgrounds/backgrounds_view', $this->data);
	}
	
	/**
	* delete Backgroundss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$backgrounds = $this->model_backgrounds->find($id);

		if (!empty($backgrounds->image_url)) {
			$path = FCPATH . '/uploads/backgrounds/' . $backgrounds->image_url;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_backgrounds->remove($id);
	}
	
	/**
	* Upload Image Backgrounds	* 
	* @return JSON
	*/
	public function upload_image_url_file()
	{
		if (!$this->is_allowed('backgrounds_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'backgrounds',
		]);
	}

	/**
	* Delete Image Backgrounds	* 
	* @return JSON
	*/
	public function delete_image_url_file($uuid)
	{
		if (!$this->is_allowed('backgrounds_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'image_url', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'backgrounds',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/backgrounds/'
        ]);
	}

	/**
	* Get Image Backgrounds	* 
	* @return JSON
	*/
	public function get_image_url_file($id)
	{
		if (!$this->is_allowed('backgrounds_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$backgrounds = $this->model_backgrounds->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'image_url', 
            'table_name'        => 'backgrounds',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/backgrounds/',
            'delete_endpoint'   => 'administrator/backgrounds/delete_image_url_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('backgrounds_export');

		$this->model_backgrounds->export('backgrounds', 'backgrounds');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('backgrounds_export');

		$this->model_backgrounds->pdf('backgrounds', 'backgrounds');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('backgrounds_export');

		$table = $title = 'backgrounds';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_backgrounds->find($id);
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


/* End of file backgrounds.php */
/* Location: ./application/controllers/administrator/Backgrounds.php */