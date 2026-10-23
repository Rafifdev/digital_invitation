<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Invitation Stories Controller
*| --------------------------------------------------------------------------
*| Invitation Stories site
*|
*/
class Invitation_stories extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_invitation_stories');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Invitation Storiess
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('invitation_stories_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['invitation_storiess'] = $this->model_invitation_stories->get($filter, $field, $this->limit_page, $offset);
		$this->data['invitation_stories_counts'] = $this->model_invitation_stories->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/invitation_stories/index/',
			'total_rows'   => $this->model_invitation_stories->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Invitation Stories List');
		$this->render('backend/standart/administrator/invitation_stories/invitation_stories_list', $this->data);
	}
	
	/**
	* Add new invitation_storiess
	*
	*/
	public function add()
	{
		$this->is_allowed('invitation_stories_add');

		$this->template->title('Invitation Stories New');
		$this->render('backend/standart/administrator/invitation_stories/invitation_stories_add', $this->data);
	}

	/**
	* Add New Invitation Storiess
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('invitation_stories_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('year_date', 'Year Date', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('story', 'Story', 'trim|required');
		$this->form_validation->set_rules('invitation_stories_image_url_name', 'Image Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('sort_order', 'Sort Order', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {
			$invitation_stories_image_url_uuid = $this->input->post('invitation_stories_image_url_uuid');
			$invitation_stories_image_url_name = $this->input->post('invitation_stories_image_url_name');
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'year_date' => $this->input->post('year_date'),
				'title' => $this->input->post('title'),
				'story' => $this->input->post('story'),
				'sort_order' => $this->input->post('sort_order'),
			];

			if (!is_dir(FCPATH . '/uploads/invitation_stories/')) {
				mkdir(FCPATH . '/uploads/invitation_stories/');
			}

			if (!empty($invitation_stories_image_url_name)) {
				$invitation_stories_image_url_name_copy = date('YmdHis') . '-' . $invitation_stories_image_url_name;

				rename(FCPATH . 'uploads/tmp/' . $invitation_stories_image_url_uuid . '/' . $invitation_stories_image_url_name, 
						FCPATH . 'uploads/invitation_stories/' . $invitation_stories_image_url_name_copy);

				if (!is_file(FCPATH . '/uploads/invitation_stories/' . $invitation_stories_image_url_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['image_url'] = $invitation_stories_image_url_name_copy;
			}
		
			
			$save_invitation_stories = $this->model_invitation_stories->store($save_data);
            

			if ($save_invitation_stories) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_invitation_stories;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/invitation_stories/edit/' . $save_invitation_stories, 'Edit Invitation Stories'),
						anchor('administrator/invitation_stories', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/invitation_stories/edit/' . $save_invitation_stories, 'Edit Invitation Stories')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_stories');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_stories');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Invitation Storiess
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('invitation_stories_update');

		$this->data['invitation_stories'] = $this->model_invitation_stories->find($id);

		$this->template->title('Invitation Stories Update');
		$this->render('backend/standart/administrator/invitation_stories/invitation_stories_update', $this->data);
	}

	/**
	* Update Invitation Storiess
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('invitation_stories_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('year_date', 'Year Date', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('story', 'Story', 'trim|required');
		$this->form_validation->set_rules('invitation_stories_image_url_name', 'Image Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('sort_order', 'Sort Order', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
			$invitation_stories_image_url_uuid = $this->input->post('invitation_stories_image_url_uuid');
			$invitation_stories_image_url_name = $this->input->post('invitation_stories_image_url_name');
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'year_date' => $this->input->post('year_date'),
				'title' => $this->input->post('title'),
				'story' => $this->input->post('story'),
				'sort_order' => $this->input->post('sort_order'),
			];

			if (!is_dir(FCPATH . '/uploads/invitation_stories/')) {
				mkdir(FCPATH . '/uploads/invitation_stories/');
			}

			if (!empty($invitation_stories_image_url_uuid)) {
				$invitation_stories_image_url_name_copy = date('YmdHis') . '-' . $invitation_stories_image_url_name;

				rename(FCPATH . 'uploads/tmp/' . $invitation_stories_image_url_uuid . '/' . $invitation_stories_image_url_name, 
						FCPATH . 'uploads/invitation_stories/' . $invitation_stories_image_url_name_copy);

				if (!is_file(FCPATH . '/uploads/invitation_stories/' . $invitation_stories_image_url_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['image_url'] = $invitation_stories_image_url_name_copy;
			}
		
			
			$save_invitation_stories = $this->model_invitation_stories->change($id, $save_data);

			if ($save_invitation_stories) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/invitation_stories', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_stories');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_stories');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Invitation Storiess
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('invitation_stories_delete');

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
            set_message(cclang('has_been_deleted', 'invitation_stories'), 'success');
        } else {
            set_message(cclang('error_delete', 'invitation_stories'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Invitation Storiess
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('invitation_stories_view');

		$this->data['invitation_stories'] = $this->model_invitation_stories->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Invitation Stories Detail');
		$this->render('backend/standart/administrator/invitation_stories/invitation_stories_view', $this->data);
	}
	
	/**
	* delete Invitation Storiess
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$invitation_stories = $this->model_invitation_stories->find($id);

		if (!empty($invitation_stories->image_url)) {
			$path = FCPATH . '/uploads/invitation_stories/' . $invitation_stories->image_url;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_invitation_stories->remove($id);
	}
	
	/**
	* Upload Image Invitation Stories	* 
	* @return JSON
	*/
	public function upload_image_url_file()
	{
		if (!$this->is_allowed('invitation_stories_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'invitation_stories',
		]);
	}

	/**
	* Delete Image Invitation Stories	* 
	* @return JSON
	*/
	public function delete_image_url_file($uuid)
	{
		if (!$this->is_allowed('invitation_stories_delete', false)) {
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
            'table_name'        => 'invitation_stories',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invitation_stories/'
        ]);
	}

	/**
	* Get Image Invitation Stories	* 
	* @return JSON
	*/
	public function get_image_url_file($id)
	{
		if (!$this->is_allowed('invitation_stories_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$invitation_stories = $this->model_invitation_stories->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'image_url', 
            'table_name'        => 'invitation_stories',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invitation_stories/',
            'delete_endpoint'   => 'administrator/invitation_stories/delete_image_url_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('invitation_stories_export');

		$this->model_invitation_stories->export('invitation_stories', 'invitation_stories');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('invitation_stories_export');

		$this->model_invitation_stories->pdf('invitation_stories', 'invitation_stories');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('invitation_stories_export');

		$table = $title = 'invitation_stories';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_invitation_stories->find($id);
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


/* End of file invitation_stories.php */
/* Location: ./application/controllers/administrator/Invitation Stories.php */