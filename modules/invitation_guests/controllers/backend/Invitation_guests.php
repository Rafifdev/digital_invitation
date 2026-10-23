<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Invitation Guests Controller
*| --------------------------------------------------------------------------
*| Invitation Guests site
*|
*/
class Invitation_guests extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_invitation_guests');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Invitation Guestss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('invitation_guests_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['invitation_guestss'] = $this->model_invitation_guests->get($filter, $field, $this->limit_page, $offset);
		$this->data['invitation_guests_counts'] = $this->model_invitation_guests->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/invitation_guests/index/',
			'total_rows'   => $this->model_invitation_guests->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Invitation Guests List');
		$this->render('backend/standart/administrator/invitation_guests/invitation_guests_list', $this->data);
	}
	
	/**
	* Add new invitation_guestss
	*
	*/
	public function add()
	{
		$this->is_allowed('invitation_guests_add');

		$this->template->title('Invitation Guests New');
		$this->render('backend/standart/administrator/invitation_guests/invitation_guests_add', $this->data);
	}

	/**
	* Add New Invitation Guestss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('invitation_guests_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('guest_code', 'Guest Code', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('guest_name', 'Guest Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('group_category', 'Group Category', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('seat_allocation', 'Seat Allocation', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('is_sent', 'Is Sent', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('checked_in_at', 'Checked In At', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'guest_code' => $this->input->post('guest_code'),
				'guest_name' => $this->input->post('guest_name'),
				'group_category' => $this->input->post('group_category'),
				'phone_number' => $this->input->post('phone_number'),
				'seat_allocation' => $this->input->post('seat_allocation'),
				'is_sent' => $this->input->post('is_sent'),
				'checked_in_at' => $this->input->post('checked_in_at'),
			];

			
			$save_invitation_guests = $this->model_invitation_guests->store($save_data);
            

			if ($save_invitation_guests) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_invitation_guests;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/invitation_guests/edit/' . $save_invitation_guests, 'Edit Invitation Guests'),
						anchor('administrator/invitation_guests', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/invitation_guests/edit/' . $save_invitation_guests, 'Edit Invitation Guests')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_guests');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_guests');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Invitation Guestss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('invitation_guests_update');

		$this->data['invitation_guests'] = $this->model_invitation_guests->find($id);

		$this->template->title('Invitation Guests Update');
		$this->render('backend/standart/administrator/invitation_guests/invitation_guests_update', $this->data);
	}

	/**
	* Update Invitation Guestss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('invitation_guests_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('guest_code', 'Guest Code', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('guest_name', 'Guest Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('group_category', 'Group Category', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('seat_allocation', 'Seat Allocation', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('is_sent', 'Is Sent', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('checked_in_at', 'Checked In At', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'guest_code' => $this->input->post('guest_code'),
				'guest_name' => $this->input->post('guest_name'),
				'group_category' => $this->input->post('group_category'),
				'phone_number' => $this->input->post('phone_number'),
				'seat_allocation' => $this->input->post('seat_allocation'),
				'is_sent' => $this->input->post('is_sent'),
				'checked_in_at' => $this->input->post('checked_in_at'),
			];

			
			$save_invitation_guests = $this->model_invitation_guests->change($id, $save_data);

			if ($save_invitation_guests) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/invitation_guests', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_guests');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_guests');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Invitation Guestss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('invitation_guests_delete');

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
            set_message(cclang('has_been_deleted', 'invitation_guests'), 'success');
        } else {
            set_message(cclang('error_delete', 'invitation_guests'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Invitation Guestss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('invitation_guests_view');

		$this->data['invitation_guests'] = $this->model_invitation_guests->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Invitation Guests Detail');
		$this->render('backend/standart/administrator/invitation_guests/invitation_guests_view', $this->data);
	}
	
	/**
	* delete Invitation Guestss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$invitation_guests = $this->model_invitation_guests->find($id);

		
		
		return $this->model_invitation_guests->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('invitation_guests_export');

		$this->model_invitation_guests->export('invitation_guests', 'invitation_guests');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('invitation_guests_export');

		$this->model_invitation_guests->pdf('invitation_guests', 'invitation_guests');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('invitation_guests_export');

		$table = $title = 'invitation_guests';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_invitation_guests->find($id);
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


/* End of file invitation_guests.php */
/* Location: ./application/controllers/administrator/Invitation Guests.php */