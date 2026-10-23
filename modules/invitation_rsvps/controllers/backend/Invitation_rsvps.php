<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Invitation Rsvps Controller
*| --------------------------------------------------------------------------
*| Invitation Rsvps site
*|
*/
class Invitation_rsvps extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_invitation_rsvps');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Invitation Rsvpss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('invitation_rsvps_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['invitation_rsvpss'] = $this->model_invitation_rsvps->get($filter, $field, $this->limit_page, $offset);
		$this->data['invitation_rsvps_counts'] = $this->model_invitation_rsvps->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/invitation_rsvps/index/',
			'total_rows'   => $this->model_invitation_rsvps->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Invitation Rsvps List');
		$this->render('backend/standart/administrator/invitation_rsvps/invitation_rsvps_list', $this->data);
	}
	
	/**
	* Add new invitation_rsvpss
	*
	*/
	public function add()
	{
		$this->is_allowed('invitation_rsvps_add');

		$this->template->title('Invitation Rsvps New');
		$this->render('backend/standart/administrator/invitation_rsvps/invitation_rsvps_add', $this->data);
	}

	/**
	* Add New Invitation Rsvpss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('invitation_rsvps_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('guest_id', 'Guest Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('sender_name', 'Sender Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('attendance_status', 'Attendance Status', 'trim|required');
		$this->form_validation->set_rules('pax', 'Pax', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('message', 'Message', 'trim|required');
		$this->form_validation->set_rules('gift_amount', 'Gift Amount', 'trim|required|max_length[12]');
		$this->form_validation->set_rules('reply_message', 'Reply Message', 'trim|required');
		$this->form_validation->set_rules('is_approved', 'Is Approved', 'trim|required|max_length[1]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'guest_id' => $this->input->post('guest_id'),
				'sender_name' => $this->input->post('sender_name'),
				'attendance_status' => $this->input->post('attendance_status'),
				'pax' => $this->input->post('pax'),
				'message' => $this->input->post('message'),
				'gift_amount' => $this->input->post('gift_amount'),
				'reply_message' => $this->input->post('reply_message'),
				'is_approved' => $this->input->post('is_approved'),
			];

			
			$save_invitation_rsvps = $this->model_invitation_rsvps->store($save_data);
            

			if ($save_invitation_rsvps) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_invitation_rsvps;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/invitation_rsvps/edit/' . $save_invitation_rsvps, 'Edit Invitation Rsvps'),
						anchor('administrator/invitation_rsvps', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/invitation_rsvps/edit/' . $save_invitation_rsvps, 'Edit Invitation Rsvps')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_rsvps');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_rsvps');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Invitation Rsvpss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('invitation_rsvps_update');

		$this->data['invitation_rsvps'] = $this->model_invitation_rsvps->find($id);

		$this->template->title('Invitation Rsvps Update');
		$this->render('backend/standart/administrator/invitation_rsvps/invitation_rsvps_update', $this->data);
	}

	/**
	* Update Invitation Rsvpss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('invitation_rsvps_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('guest_id', 'Guest Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('sender_name', 'Sender Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('attendance_status', 'Attendance Status', 'trim|required');
		$this->form_validation->set_rules('pax', 'Pax', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('message', 'Message', 'trim|required');
		$this->form_validation->set_rules('gift_amount', 'Gift Amount', 'trim|required|max_length[12]');
		$this->form_validation->set_rules('reply_message', 'Reply Message', 'trim|required');
		$this->form_validation->set_rules('is_approved', 'Is Approved', 'trim|required|max_length[1]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'guest_id' => $this->input->post('guest_id'),
				'sender_name' => $this->input->post('sender_name'),
				'attendance_status' => $this->input->post('attendance_status'),
				'pax' => $this->input->post('pax'),
				'message' => $this->input->post('message'),
				'gift_amount' => $this->input->post('gift_amount'),
				'reply_message' => $this->input->post('reply_message'),
				'is_approved' => $this->input->post('is_approved'),
			];

			
			$save_invitation_rsvps = $this->model_invitation_rsvps->change($id, $save_data);

			if ($save_invitation_rsvps) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/invitation_rsvps', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_rsvps');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_rsvps');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Invitation Rsvpss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('invitation_rsvps_delete');

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
            set_message(cclang('has_been_deleted', 'invitation_rsvps'), 'success');
        } else {
            set_message(cclang('error_delete', 'invitation_rsvps'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Invitation Rsvpss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('invitation_rsvps_view');

		$this->data['invitation_rsvps'] = $this->model_invitation_rsvps->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Invitation Rsvps Detail');
		$this->render('backend/standart/administrator/invitation_rsvps/invitation_rsvps_view', $this->data);
	}
	
	/**
	* delete Invitation Rsvpss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$invitation_rsvps = $this->model_invitation_rsvps->find($id);

		
		
		return $this->model_invitation_rsvps->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('invitation_rsvps_export');

		$this->model_invitation_rsvps->export('invitation_rsvps', 'invitation_rsvps');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('invitation_rsvps_export');

		$this->model_invitation_rsvps->pdf('invitation_rsvps', 'invitation_rsvps');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('invitation_rsvps_export');

		$table = $title = 'invitation_rsvps';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_invitation_rsvps->find($id);
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


/* End of file invitation_rsvps.php */
/* Location: ./application/controllers/administrator/Invitation Rsvps.php */