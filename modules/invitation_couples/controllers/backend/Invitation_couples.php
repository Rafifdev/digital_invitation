<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Invitation Couples Controller
*| --------------------------------------------------------------------------
*| Invitation Couples site
*|
*/
class Invitation_couples extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_invitation_couples');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Invitation Coupless
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('invitation_couples_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['invitation_coupless'] = $this->model_invitation_couples->get($filter, $field, $this->limit_page, $offset);
		$this->data['invitation_couples_counts'] = $this->model_invitation_couples->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/invitation_couples/index/',
			'total_rows'   => $this->model_invitation_couples->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Invitation Couples List');
		$this->render('backend/standart/administrator/invitation_couples/invitation_couples_list', $this->data);
	}
	
	/**
	* Add new invitation_coupless
	*
	*/
	public function add()
	{
		$this->is_allowed('invitation_couples_add');

		$this->template->title('Invitation Couples New');
		$this->render('backend/standart/administrator/invitation_couples/invitation_couples_add', $this->data);
	}

	/**
	* Add New Invitation Coupless
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('invitation_couples_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('couple_order', 'Couple Order', 'trim|required');
		$this->form_validation->set_rules('groom_nickname', 'Groom Nickname', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('groom_full_name', 'Groom Full Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('groom_child_order', 'Groom Child Order', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('groom_father_name', 'Groom Father Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('groom_mother_name', 'Groom Mother Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('groom_instagram', 'Groom Instagram', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('invitation_couples_groom_photo_name', 'Groom Photo', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('bride_nickname', 'Bride Nickname', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('bride_full_name', 'Bride Full Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('bride_child_order', 'Bride Child Order', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('bride_father_name', 'Bride Father Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('bride_mother_name', 'Bride Mother Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('bride_instagram', 'Bride Instagram', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('invitation_couples_bride_photo_name', 'Bride Photo', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
			$invitation_couples_groom_photo_uuid = $this->input->post('invitation_couples_groom_photo_uuid');
			$invitation_couples_groom_photo_name = $this->input->post('invitation_couples_groom_photo_name');
			$invitation_couples_bride_photo_uuid = $this->input->post('invitation_couples_bride_photo_uuid');
			$invitation_couples_bride_photo_name = $this->input->post('invitation_couples_bride_photo_name');
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'couple_order' => $this->input->post('couple_order'),
				'groom_nickname' => $this->input->post('groom_nickname'),
				'groom_full_name' => $this->input->post('groom_full_name'),
				'groom_child_order' => $this->input->post('groom_child_order'),
				'groom_father_name' => $this->input->post('groom_father_name'),
				'groom_mother_name' => $this->input->post('groom_mother_name'),
				'groom_instagram' => $this->input->post('groom_instagram'),
				'bride_nickname' => $this->input->post('bride_nickname'),
				'bride_full_name' => $this->input->post('bride_full_name'),
				'bride_child_order' => $this->input->post('bride_child_order'),
				'bride_father_name' => $this->input->post('bride_father_name'),
				'bride_mother_name' => $this->input->post('bride_mother_name'),
				'bride_instagram' => $this->input->post('bride_instagram'),
			];

			if (!is_dir(FCPATH . '/uploads/invitation_couples/')) {
				mkdir(FCPATH . '/uploads/invitation_couples/');
			}

			if (!empty($invitation_couples_groom_photo_name)) {
				$invitation_couples_groom_photo_name_copy = date('YmdHis') . '-' . $invitation_couples_groom_photo_name;

				rename(FCPATH . 'uploads/tmp/' . $invitation_couples_groom_photo_uuid . '/' . $invitation_couples_groom_photo_name, 
						FCPATH . 'uploads/invitation_couples/' . $invitation_couples_groom_photo_name_copy);

				if (!is_file(FCPATH . '/uploads/invitation_couples/' . $invitation_couples_groom_photo_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['groom_photo'] = $invitation_couples_groom_photo_name_copy;
			}
		
			if (!empty($invitation_couples_bride_photo_name)) {
				$invitation_couples_bride_photo_name_copy = date('YmdHis') . '-' . $invitation_couples_bride_photo_name;

				rename(FCPATH . 'uploads/tmp/' . $invitation_couples_bride_photo_uuid . '/' . $invitation_couples_bride_photo_name, 
						FCPATH . 'uploads/invitation_couples/' . $invitation_couples_bride_photo_name_copy);

				if (!is_file(FCPATH . '/uploads/invitation_couples/' . $invitation_couples_bride_photo_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['bride_photo'] = $invitation_couples_bride_photo_name_copy;
			}
		
			
			$save_invitation_couples = $this->model_invitation_couples->store($save_data);
            

			if ($save_invitation_couples) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_invitation_couples;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/invitation_couples/edit/' . $save_invitation_couples, 'Edit Invitation Couples'),
						anchor('administrator/invitation_couples', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/invitation_couples/edit/' . $save_invitation_couples, 'Edit Invitation Couples')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_couples');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_couples');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Invitation Coupless
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('invitation_couples_update');

		$this->data['invitation_couples'] = $this->model_invitation_couples->find($id);

		$this->template->title('Invitation Couples Update');
		$this->render('backend/standart/administrator/invitation_couples/invitation_couples_update', $this->data);
	}

	/**
	* Update Invitation Coupless
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('invitation_couples_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('couple_order', 'Couple Order', 'trim|required');
		$this->form_validation->set_rules('groom_nickname', 'Groom Nickname', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('groom_full_name', 'Groom Full Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('groom_child_order', 'Groom Child Order', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('groom_father_name', 'Groom Father Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('groom_mother_name', 'Groom Mother Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('groom_instagram', 'Groom Instagram', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('invitation_couples_groom_photo_name', 'Groom Photo', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('bride_nickname', 'Bride Nickname', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('bride_full_name', 'Bride Full Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('bride_child_order', 'Bride Child Order', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('bride_father_name', 'Bride Father Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('bride_mother_name', 'Bride Mother Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('bride_instagram', 'Bride Instagram', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('invitation_couples_bride_photo_name', 'Bride Photo', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
			$invitation_couples_groom_photo_uuid = $this->input->post('invitation_couples_groom_photo_uuid');
			$invitation_couples_groom_photo_name = $this->input->post('invitation_couples_groom_photo_name');
			$invitation_couples_bride_photo_uuid = $this->input->post('invitation_couples_bride_photo_uuid');
			$invitation_couples_bride_photo_name = $this->input->post('invitation_couples_bride_photo_name');
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'couple_order' => $this->input->post('couple_order'),
				'groom_nickname' => $this->input->post('groom_nickname'),
				'groom_full_name' => $this->input->post('groom_full_name'),
				'groom_child_order' => $this->input->post('groom_child_order'),
				'groom_father_name' => $this->input->post('groom_father_name'),
				'groom_mother_name' => $this->input->post('groom_mother_name'),
				'groom_instagram' => $this->input->post('groom_instagram'),
				'bride_nickname' => $this->input->post('bride_nickname'),
				'bride_full_name' => $this->input->post('bride_full_name'),
				'bride_child_order' => $this->input->post('bride_child_order'),
				'bride_father_name' => $this->input->post('bride_father_name'),
				'bride_mother_name' => $this->input->post('bride_mother_name'),
				'bride_instagram' => $this->input->post('bride_instagram'),
			];

			if (!is_dir(FCPATH . '/uploads/invitation_couples/')) {
				mkdir(FCPATH . '/uploads/invitation_couples/');
			}

			if (!empty($invitation_couples_groom_photo_uuid)) {
				$invitation_couples_groom_photo_name_copy = date('YmdHis') . '-' . $invitation_couples_groom_photo_name;

				rename(FCPATH . 'uploads/tmp/' . $invitation_couples_groom_photo_uuid . '/' . $invitation_couples_groom_photo_name, 
						FCPATH . 'uploads/invitation_couples/' . $invitation_couples_groom_photo_name_copy);

				if (!is_file(FCPATH . '/uploads/invitation_couples/' . $invitation_couples_groom_photo_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['groom_photo'] = $invitation_couples_groom_photo_name_copy;
			}
		
			if (!empty($invitation_couples_bride_photo_uuid)) {
				$invitation_couples_bride_photo_name_copy = date('YmdHis') . '-' . $invitation_couples_bride_photo_name;

				rename(FCPATH . 'uploads/tmp/' . $invitation_couples_bride_photo_uuid . '/' . $invitation_couples_bride_photo_name, 
						FCPATH . 'uploads/invitation_couples/' . $invitation_couples_bride_photo_name_copy);

				if (!is_file(FCPATH . '/uploads/invitation_couples/' . $invitation_couples_bride_photo_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['bride_photo'] = $invitation_couples_bride_photo_name_copy;
			}
		
			
			$save_invitation_couples = $this->model_invitation_couples->change($id, $save_data);

			if ($save_invitation_couples) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/invitation_couples', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_couples');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_couples');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Invitation Coupless
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('invitation_couples_delete');

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
            set_message(cclang('has_been_deleted', 'invitation_couples'), 'success');
        } else {
            set_message(cclang('error_delete', 'invitation_couples'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Invitation Coupless
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('invitation_couples_view');

		$this->data['invitation_couples'] = $this->model_invitation_couples->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Invitation Couples Detail');
		$this->render('backend/standart/administrator/invitation_couples/invitation_couples_view', $this->data);
	}
	
	/**
	* delete Invitation Coupless
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$invitation_couples = $this->model_invitation_couples->find($id);

		if (!empty($invitation_couples->groom_photo)) {
			$path = FCPATH . '/uploads/invitation_couples/' . $invitation_couples->groom_photo;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($invitation_couples->bride_photo)) {
			$path = FCPATH . '/uploads/invitation_couples/' . $invitation_couples->bride_photo;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_invitation_couples->remove($id);
	}
	
	/**
	* Upload Image Invitation Couples	* 
	* @return JSON
	*/
	public function upload_groom_photo_file()
	{
		if (!$this->is_allowed('invitation_couples_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'invitation_couples',
		]);
	}

	/**
	* Delete Image Invitation Couples	* 
	* @return JSON
	*/
	public function delete_groom_photo_file($uuid)
	{
		if (!$this->is_allowed('invitation_couples_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'groom_photo', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'invitation_couples',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invitation_couples/'
        ]);
	}

	/**
	* Get Image Invitation Couples	* 
	* @return JSON
	*/
	public function get_groom_photo_file($id)
	{
		if (!$this->is_allowed('invitation_couples_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$invitation_couples = $this->model_invitation_couples->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'groom_photo', 
            'table_name'        => 'invitation_couples',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invitation_couples/',
            'delete_endpoint'   => 'administrator/invitation_couples/delete_groom_photo_file'
        ]);
	}
	
	/**
	* Upload Image Invitation Couples	* 
	* @return JSON
	*/
	public function upload_bride_photo_file()
	{
		if (!$this->is_allowed('invitation_couples_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'invitation_couples',
		]);
	}

	/**
	* Delete Image Invitation Couples	* 
	* @return JSON
	*/
	public function delete_bride_photo_file($uuid)
	{
		if (!$this->is_allowed('invitation_couples_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'bride_photo', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'invitation_couples',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invitation_couples/'
        ]);
	}

	/**
	* Get Image Invitation Couples	* 
	* @return JSON
	*/
	public function get_bride_photo_file($id)
	{
		if (!$this->is_allowed('invitation_couples_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$invitation_couples = $this->model_invitation_couples->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'bride_photo', 
            'table_name'        => 'invitation_couples',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invitation_couples/',
            'delete_endpoint'   => 'administrator/invitation_couples/delete_bride_photo_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('invitation_couples_export');

		$this->model_invitation_couples->export('invitation_couples', 'invitation_couples');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('invitation_couples_export');

		$this->model_invitation_couples->pdf('invitation_couples', 'invitation_couples');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('invitation_couples_export');

		$table = $title = 'invitation_couples';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_invitation_couples->find($id);
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


/* End of file invitation_couples.php */
/* Location: ./application/controllers/administrator/Invitation Couples.php */