<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Invitations Controller
*| --------------------------------------------------------------------------
*| Invitations site
*|
*/
class Invitations extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_invitations');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Invitationss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('invitations_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['invitationss'] = $this->model_invitations->get($filter, $field, $this->limit_page, $offset);
		$this->data['invitations_counts'] = $this->model_invitations->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/invitations/index/',
			'total_rows'   => $this->model_invitations->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Semua Undangan');
		$this->render('backend/standart/administrator/invitations/invitations_list', $this->data);
	}
	
	/**
	* Add new invitationss
	*
	*/
	public function add()
	{
		$this->is_allowed('invitations_add');

		$this->template->title('Tambah Undangan Baru');
		$this->render('backend/standart/administrator/invitations/invitations_add', $this->data);
	}

	/**
	* Add New Invitationss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('invitations_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('user_id', 'Pemilik Undangan', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('template_id', 'Template', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('slug', 'Slug', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('title', 'Judul Undangan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');
		

		if ($this->form_validation->run()) {
			$invitations_cover_photo_uuid = $this->input->post('invitations_cover_photo_uuid');
			$invitations_cover_photo_name = $this->input->post('invitations_cover_photo_name');
		
			$save_data = [
				'user_id' => $this->input->post('user_id'),
				'created_by' => get_user_data('id') ?: $this->input->post('user_id'),
				'template_id' => $this->input->post('template_id'),
				'slug' => $this->input->post('slug'),
				'title' => $this->input->post('title'),
				'status' => $this->input->post('status'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			];

			if (!is_dir(FCPATH . '/uploads/invitations/')) {
				mkdir(FCPATH . '/uploads/invitations/');
			}

			if (!empty($invitations_cover_photo_name)) {
				$invitations_cover_photo_name_copy = date('YmdHis') . '-' . $invitations_cover_photo_name;

				rename(FCPATH . 'uploads/tmp/' . $invitations_cover_photo_uuid . '/' . $invitations_cover_photo_name, 
						FCPATH . 'uploads/invitations/' . $invitations_cover_photo_name_copy);

				if (!is_file(FCPATH . '/uploads/invitations/' . $invitations_cover_photo_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['cover_photo'] = $invitations_cover_photo_name_copy;
			}
		
			$save_invitations = $this->model_invitations->store($save_data);
            

			if ($save_invitations) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_invitations;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/invitations/edit/' . $save_invitations, 'Edit Invitations'),
						anchor('administrator/invitations', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/invitations/edit/' . $save_invitations, 'Edit Invitations')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitations');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitations');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Invitationss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('invitations_update');

		$this->data['invitations'] = $this->model_invitations->find($id);

		$this->template->title('Edit Undangan');
		$this->render('backend/standart/administrator/invitations/invitations_update', $this->data);
	}

	/**
	* Update Invitationss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('invitations_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('user_id', 'Pemilik Undangan', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('template_id', 'Template', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('slug', 'Slug', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('title', 'Judul Undangan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');
		
		if ($this->form_validation->run()) {
			$invitations_cover_photo_uuid = $this->input->post('invitations_cover_photo_uuid');
			$invitations_cover_photo_name = $this->input->post('invitations_cover_photo_name');
		
			$save_data = [
				'user_id' => $this->input->post('user_id'),
				'template_id' => $this->input->post('template_id'),
				'slug' => $this->input->post('slug'),
				'title' => $this->input->post('title'),
				'status' => $this->input->post('status'),
				'last_edited_by' => get_user_data('id'),
				'updated_at' => date('Y-m-d H:i:s'),
			];

			if (!is_dir(FCPATH . '/uploads/invitations/')) {
				mkdir(FCPATH . '/uploads/invitations/');
			}

			if (!empty($invitations_cover_photo_uuid)) {
				$invitations_cover_photo_name_copy = date('YmdHis') . '-' . $invitations_cover_photo_name;

				rename(FCPATH . 'uploads/tmp/' . $invitations_cover_photo_uuid . '/' . $invitations_cover_photo_name, 
						FCPATH . 'uploads/invitations/' . $invitations_cover_photo_name_copy);

				if (!is_file(FCPATH . '/uploads/invitations/' . $invitations_cover_photo_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['cover_photo'] = $invitations_cover_photo_name_copy;
			}
		
			$save_invitations = $this->model_invitations->change($id, $save_data);

			if ($save_invitations) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/invitations', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitations');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitations');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Invitationss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('invitations_delete');

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
            set_message(cclang('has_been_deleted', 'invitations'), 'success');
        } else {
            set_message(cclang('error_delete', 'invitations'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Invitationss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('invitations_view');

		$this->data['invitations'] = $this->model_invitations->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Detail Undangan');
		$this->render('backend/standart/administrator/invitations/invitations_view', $this->data);
	}
	
	/**
	* delete Invitationss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$invitations = $this->model_invitations->find($id);

		if (!empty($invitations->cover_photo)) {
			$path = FCPATH . '/uploads/invitations/' . $invitations->cover_photo;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		return $this->model_invitations->remove($id);
	}
	
	/**
	* Upload Image Invitations	* 
	* @return JSON
	*/
	public function upload_cover_photo_file()
	{
		if (!$this->is_allowed('invitations_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'invitations',
		]);
	}

	/**
	* Delete Image Invitations	* 
	* @return JSON
	*/
	public function delete_cover_photo_file($uuid)
	{
		if (!$this->is_allowed('invitations_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'cover_photo', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'invitations',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invitations/'
        ]);
	}

	/**
	* Get Image Invitations	* 
	* @return JSON
	*/
	public function get_cover_photo_file($id)
	{
		if (!$this->is_allowed('invitations_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$invitations = $this->model_invitations->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'cover_photo', 
            'table_name'        => 'invitations',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invitations/',
            'delete_endpoint'   => 'administrator/invitations/delete_cover_photo_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('invitations_export');

		$this->model_invitations->export('invitations', 'invitations');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('invitations_export');

		$this->model_invitations->pdf('invitations', 'invitations');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('invitations_export');

		$table = $title = 'invitations';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_invitations->find($id);
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


/* End of file invitations.php */
/* Location: ./application/controllers/administrator/Invitations.php */