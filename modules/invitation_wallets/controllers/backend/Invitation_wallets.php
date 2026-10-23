<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Invitation Wallets Controller
*| --------------------------------------------------------------------------
*| Invitation Wallets site
*|
*/
class Invitation_wallets extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_invitation_wallets');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Invitation Walletss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('invitation_wallets_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['invitation_walletss'] = $this->model_invitation_wallets->get($filter, $field, $this->limit_page, $offset);
		$this->data['invitation_wallets_counts'] = $this->model_invitation_wallets->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/invitation_wallets/index/',
			'total_rows'   => $this->model_invitation_wallets->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Invitation Wallets List');
		$this->render('backend/standart/administrator/invitation_wallets/invitation_wallets_list', $this->data);
	}
	
	/**
	* Add new invitation_walletss
	*
	*/
	public function add()
	{
		$this->is_allowed('invitation_wallets_add');

		$this->template->title('Invitation Wallets New');
		$this->render('backend/standart/administrator/invitation_wallets/invitation_wallets_add', $this->data);
	}

	/**
	* Add New Invitation Walletss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('invitation_wallets_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('wallet_type', 'Wallet Type', 'trim|required');
		$this->form_validation->set_rules('provider_name', 'Provider Name', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('account_holder', 'Account Holder', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('invitation_wallets_qris_image_name', 'Qris Image', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('notes', 'Notes', 'trim|required');
		$this->form_validation->set_rules('sort_order', 'Sort Order', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {
			$invitation_wallets_qris_image_uuid = $this->input->post('invitation_wallets_qris_image_uuid');
			$invitation_wallets_qris_image_name = $this->input->post('invitation_wallets_qris_image_name');
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'wallet_type' => $this->input->post('wallet_type'),
				'provider_name' => $this->input->post('provider_name'),
				'account_number' => $this->input->post('account_number'),
				'account_holder' => $this->input->post('account_holder'),
				'notes' => $this->input->post('notes'),
				'sort_order' => $this->input->post('sort_order'),
			];

			if (!is_dir(FCPATH . '/uploads/invitation_wallets/')) {
				mkdir(FCPATH . '/uploads/invitation_wallets/');
			}

			if (!empty($invitation_wallets_qris_image_name)) {
				$invitation_wallets_qris_image_name_copy = date('YmdHis') . '-' . $invitation_wallets_qris_image_name;

				rename(FCPATH . 'uploads/tmp/' . $invitation_wallets_qris_image_uuid . '/' . $invitation_wallets_qris_image_name, 
						FCPATH . 'uploads/invitation_wallets/' . $invitation_wallets_qris_image_name_copy);

				if (!is_file(FCPATH . '/uploads/invitation_wallets/' . $invitation_wallets_qris_image_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['qris_image'] = $invitation_wallets_qris_image_name_copy;
			}
		
			
			$save_invitation_wallets = $this->model_invitation_wallets->store($save_data);
            

			if ($save_invitation_wallets) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_invitation_wallets;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/invitation_wallets/edit/' . $save_invitation_wallets, 'Edit Invitation Wallets'),
						anchor('administrator/invitation_wallets', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/invitation_wallets/edit/' . $save_invitation_wallets, 'Edit Invitation Wallets')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_wallets');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_wallets');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Invitation Walletss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('invitation_wallets_update');

		$this->data['invitation_wallets'] = $this->model_invitation_wallets->find($id);

		$this->template->title('Invitation Wallets Update');
		$this->render('backend/standart/administrator/invitation_wallets/invitation_wallets_update', $this->data);
	}

	/**
	* Update Invitation Walletss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('invitation_wallets_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('wallet_type', 'Wallet Type', 'trim|required');
		$this->form_validation->set_rules('provider_name', 'Provider Name', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('account_holder', 'Account Holder', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('invitation_wallets_qris_image_name', 'Qris Image', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('notes', 'Notes', 'trim|required');
		$this->form_validation->set_rules('sort_order', 'Sort Order', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
			$invitation_wallets_qris_image_uuid = $this->input->post('invitation_wallets_qris_image_uuid');
			$invitation_wallets_qris_image_name = $this->input->post('invitation_wallets_qris_image_name');
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'wallet_type' => $this->input->post('wallet_type'),
				'provider_name' => $this->input->post('provider_name'),
				'account_number' => $this->input->post('account_number'),
				'account_holder' => $this->input->post('account_holder'),
				'notes' => $this->input->post('notes'),
				'sort_order' => $this->input->post('sort_order'),
			];

			if (!is_dir(FCPATH . '/uploads/invitation_wallets/')) {
				mkdir(FCPATH . '/uploads/invitation_wallets/');
			}

			if (!empty($invitation_wallets_qris_image_uuid)) {
				$invitation_wallets_qris_image_name_copy = date('YmdHis') . '-' . $invitation_wallets_qris_image_name;

				rename(FCPATH . 'uploads/tmp/' . $invitation_wallets_qris_image_uuid . '/' . $invitation_wallets_qris_image_name, 
						FCPATH . 'uploads/invitation_wallets/' . $invitation_wallets_qris_image_name_copy);

				if (!is_file(FCPATH . '/uploads/invitation_wallets/' . $invitation_wallets_qris_image_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['qris_image'] = $invitation_wallets_qris_image_name_copy;
			}
		
			
			$save_invitation_wallets = $this->model_invitation_wallets->change($id, $save_data);

			if ($save_invitation_wallets) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/invitation_wallets', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_wallets');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_wallets');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Invitation Walletss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('invitation_wallets_delete');

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
            set_message(cclang('has_been_deleted', 'invitation_wallets'), 'success');
        } else {
            set_message(cclang('error_delete', 'invitation_wallets'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Invitation Walletss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('invitation_wallets_view');

		$this->data['invitation_wallets'] = $this->model_invitation_wallets->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Invitation Wallets Detail');
		$this->render('backend/standart/administrator/invitation_wallets/invitation_wallets_view', $this->data);
	}
	
	/**
	* delete Invitation Walletss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$invitation_wallets = $this->model_invitation_wallets->find($id);

		if (!empty($invitation_wallets->qris_image)) {
			$path = FCPATH . '/uploads/invitation_wallets/' . $invitation_wallets->qris_image;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_invitation_wallets->remove($id);
	}
	
	/**
	* Upload Image Invitation Wallets	* 
	* @return JSON
	*/
	public function upload_qris_image_file()
	{
		if (!$this->is_allowed('invitation_wallets_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'invitation_wallets',
		]);
	}

	/**
	* Delete Image Invitation Wallets	* 
	* @return JSON
	*/
	public function delete_qris_image_file($uuid)
	{
		if (!$this->is_allowed('invitation_wallets_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'qris_image', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'invitation_wallets',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invitation_wallets/'
        ]);
	}

	/**
	* Get Image Invitation Wallets	* 
	* @return JSON
	*/
	public function get_qris_image_file($id)
	{
		if (!$this->is_allowed('invitation_wallets_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$invitation_wallets = $this->model_invitation_wallets->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'qris_image', 
            'table_name'        => 'invitation_wallets',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invitation_wallets/',
            'delete_endpoint'   => 'administrator/invitation_wallets/delete_qris_image_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('invitation_wallets_export');

		$this->model_invitation_wallets->export('invitation_wallets', 'invitation_wallets');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('invitation_wallets_export');

		$this->model_invitation_wallets->pdf('invitation_wallets', 'invitation_wallets');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('invitation_wallets_export');

		$table = $title = 'invitation_wallets';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_invitation_wallets->find($id);
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


/* End of file invitation_wallets.php */
/* Location: ./application/controllers/administrator/Invitation Wallets.php */