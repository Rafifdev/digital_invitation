<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Invitation Galleries Controller
*| --------------------------------------------------------------------------
*| Invitation Galleries site
*|
*/
class Invitation_galleries extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_invitation_galleries');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Invitation Galleriess
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('invitation_galleries_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['invitation_galleriess'] = $this->model_invitation_galleries->get($filter, $field, $this->limit_page, $offset);
		$this->data['invitation_galleries_counts'] = $this->model_invitation_galleries->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/invitation_galleries/index/',
			'total_rows'   => $this->model_invitation_galleries->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Invitation Galleries List');
		$this->render('backend/standart/administrator/invitation_galleries/invitation_galleries_list', $this->data);
	}
	
	/**
	* Add new invitation_galleriess
	*
	*/
	public function add()
	{
		$this->is_allowed('invitation_galleries_add');

		$this->template->title('Invitation Galleries New');
		$this->render('backend/standart/administrator/invitation_galleries/invitation_galleries_add', $this->data);
	}

	/**
	* Add New Invitation Galleriess
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('invitation_galleries_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('media_type', 'Media Type', 'trim|required');
		$this->form_validation->set_rules('invitation_galleries_file_url_name', 'File Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('caption', 'Caption', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('sort_order', 'Sort Order', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {
			$invitation_galleries_file_url_uuid = $this->input->post('invitation_galleries_file_url_uuid');
			$invitation_galleries_file_url_name = $this->input->post('invitation_galleries_file_url_name');
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'media_type' => $this->input->post('media_type'),
				'caption' => $this->input->post('caption'),
				'sort_order' => $this->input->post('sort_order'),
			];

			if (!is_dir(FCPATH . '/uploads/invitation_galleries/')) {
				mkdir(FCPATH . '/uploads/invitation_galleries/');
			}

			if (!empty($invitation_galleries_file_url_name)) {
				$invitation_galleries_file_url_name_copy = date('YmdHis') . '-' . $invitation_galleries_file_url_name;

				rename(FCPATH . 'uploads/tmp/' . $invitation_galleries_file_url_uuid . '/' . $invitation_galleries_file_url_name, 
						FCPATH . 'uploads/invitation_galleries/' . $invitation_galleries_file_url_name_copy);

				if (!is_file(FCPATH . '/uploads/invitation_galleries/' . $invitation_galleries_file_url_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_url'] = $invitation_galleries_file_url_name_copy;
			}
		
			
			$save_invitation_galleries = $this->model_invitation_galleries->store($save_data);
            

			if ($save_invitation_galleries) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_invitation_galleries;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/invitation_galleries/edit/' . $save_invitation_galleries, 'Edit Invitation Galleries'),
						anchor('administrator/invitation_galleries', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/invitation_galleries/edit/' . $save_invitation_galleries, 'Edit Invitation Galleries')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_galleries');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_galleries');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Invitation Galleriess
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('invitation_galleries_update');

		$this->data['invitation_galleries'] = $this->model_invitation_galleries->find($id);

		$this->template->title('Invitation Galleries Update');
		$this->render('backend/standart/administrator/invitation_galleries/invitation_galleries_update', $this->data);
	}

	/**
	* Update Invitation Galleriess
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('invitation_galleries_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('media_type', 'Media Type', 'trim|required');
		$this->form_validation->set_rules('invitation_galleries_file_url_name', 'File Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('caption', 'Caption', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('sort_order', 'Sort Order', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
			$invitation_galleries_file_url_uuid = $this->input->post('invitation_galleries_file_url_uuid');
			$invitation_galleries_file_url_name = $this->input->post('invitation_galleries_file_url_name');
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'media_type' => $this->input->post('media_type'),
				'caption' => $this->input->post('caption'),
				'sort_order' => $this->input->post('sort_order'),
			];

			if (!is_dir(FCPATH . '/uploads/invitation_galleries/')) {
				mkdir(FCPATH . '/uploads/invitation_galleries/');
			}

			if (!empty($invitation_galleries_file_url_uuid)) {
				$invitation_galleries_file_url_name_copy = date('YmdHis') . '-' . $invitation_galleries_file_url_name;

				rename(FCPATH . 'uploads/tmp/' . $invitation_galleries_file_url_uuid . '/' . $invitation_galleries_file_url_name, 
						FCPATH . 'uploads/invitation_galleries/' . $invitation_galleries_file_url_name_copy);

				if (!is_file(FCPATH . '/uploads/invitation_galleries/' . $invitation_galleries_file_url_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_url'] = $invitation_galleries_file_url_name_copy;
			}
		
			
			$save_invitation_galleries = $this->model_invitation_galleries->change($id, $save_data);

			if ($save_invitation_galleries) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/invitation_galleries', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_galleries');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_galleries');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Invitation Galleriess
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('invitation_galleries_delete');

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
            set_message(cclang('has_been_deleted', 'invitation_galleries'), 'success');
        } else {
            set_message(cclang('error_delete', 'invitation_galleries'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Invitation Galleriess
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('invitation_galleries_view');

		$this->data['invitation_galleries'] = $this->model_invitation_galleries->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Invitation Galleries Detail');
		$this->render('backend/standart/administrator/invitation_galleries/invitation_galleries_view', $this->data);
	}
	
	/**
	* delete Invitation Galleriess
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$invitation_galleries = $this->model_invitation_galleries->find($id);

		if (!empty($invitation_galleries->file_url)) {
			$path = FCPATH . '/uploads/invitation_galleries/' . $invitation_galleries->file_url;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_invitation_galleries->remove($id);
	}
	
	/**
	* Upload Image Invitation Galleries	* 
	* @return JSON
	*/
	public function upload_file_url_file()
	{
		if (!$this->is_allowed('invitation_galleries_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'invitation_galleries',
		]);
	}

	/**
	* Delete Image Invitation Galleries	* 
	* @return JSON
	*/
	public function delete_file_url_file($uuid)
	{
		if (!$this->is_allowed('invitation_galleries_delete', false)) {
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
            'table_name'        => 'invitation_galleries',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invitation_galleries/'
        ]);
	}

	/**
	* Get Image Invitation Galleries	* 
	* @return JSON
	*/
	public function get_file_url_file($id)
	{
		if (!$this->is_allowed('invitation_galleries_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$invitation_galleries = $this->model_invitation_galleries->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_url', 
            'table_name'        => 'invitation_galleries',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invitation_galleries/',
            'delete_endpoint'   => 'administrator/invitation_galleries/delete_file_url_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('invitation_galleries_export');

		$this->model_invitation_galleries->export('invitation_galleries', 'invitation_galleries');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('invitation_galleries_export');

		$this->model_invitation_galleries->pdf('invitation_galleries', 'invitation_galleries');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('invitation_galleries_export');

		$table = $title = 'invitation_galleries';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_invitation_galleries->find($id);
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


/* End of file invitation_galleries.php */
/* Location: ./application/controllers/administrator/Invitation Galleries.php */