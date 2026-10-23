<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Invitation Events Controller
*| --------------------------------------------------------------------------
*| Invitation Events site
*|
*/
class Invitation_events extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_invitation_events');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Invitation Eventss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('invitation_events_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['invitation_eventss'] = $this->model_invitation_events->get($filter, $field, $this->limit_page, $offset);
		$this->data['invitation_events_counts'] = $this->model_invitation_events->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/invitation_events/index/',
			'total_rows'   => $this->model_invitation_events->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Invitation Events List');
		$this->render('backend/standart/administrator/invitation_events/invitation_events_list', $this->data);
	}
	
	/**
	* Add new invitation_eventss
	*
	*/
	public function add()
	{
		$this->is_allowed('invitation_events_add');

		$this->template->title('Invitation Events New');
		$this->render('backend/standart/administrator/invitation_events/invitation_events_add', $this->data);
	}

	/**
	* Add New Invitation Eventss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('invitation_events_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('event_name', 'Event Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('event_date', 'Event Date', 'trim|required');
		$this->form_validation->set_rules('time_start', 'Time Start', 'trim|required');
		$this->form_validation->set_rules('time_end', 'Time End', 'trim|required');
		$this->form_validation->set_rules('timezone', 'Timezone', 'trim|required');
		$this->form_validation->set_rules('venue_name', 'Venue Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('address', 'Address', 'trim|required');
		$this->form_validation->set_rules('latitude', 'Latitude', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('longitude', 'Longitude', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('google_maps_url', 'Google Maps Url', 'trim|required');
		$this->form_validation->set_rules('live_streaming_url', 'Live Streaming Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('sort_order', 'Sort Order', 'trim|required|max_length[11]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'event_name' => $this->input->post('event_name'),
				'event_date' => $this->input->post('event_date'),
				'time_start' => $this->input->post('time_start'),
				'time_end' => $this->input->post('time_end'),
				'timezone' => $this->input->post('timezone'),
				'venue_name' => $this->input->post('venue_name'),
				'address' => $this->input->post('address'),
				'latitude' => $this->input->post('latitude'),
				'longitude' => $this->input->post('longitude'),
				'google_maps_url' => $this->input->post('google_maps_url'),
				'live_streaming_url' => $this->input->post('live_streaming_url'),
				'sort_order' => $this->input->post('sort_order'),
			];

			
			$save_invitation_events = $this->model_invitation_events->store($save_data);
            

			if ($save_invitation_events) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_invitation_events;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/invitation_events/edit/' . $save_invitation_events, 'Edit Invitation Events'),
						anchor('administrator/invitation_events', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/invitation_events/edit/' . $save_invitation_events, 'Edit Invitation Events')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_events');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_events');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Invitation Eventss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('invitation_events_update');

		$this->data['invitation_events'] = $this->model_invitation_events->find($id);

		$this->template->title('Invitation Events Update');
		$this->render('backend/standart/administrator/invitation_events/invitation_events_update', $this->data);
	}

	/**
	* Update Invitation Eventss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('invitation_events_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('invitation_id', 'Invitation Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('event_name', 'Event Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('event_date', 'Event Date', 'trim|required');
		$this->form_validation->set_rules('time_start', 'Time Start', 'trim|required');
		$this->form_validation->set_rules('time_end', 'Time End', 'trim|required');
		$this->form_validation->set_rules('timezone', 'Timezone', 'trim|required');
		$this->form_validation->set_rules('venue_name', 'Venue Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('address', 'Address', 'trim|required');
		$this->form_validation->set_rules('latitude', 'Latitude', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('longitude', 'Longitude', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('google_maps_url', 'Google Maps Url', 'trim|required');
		$this->form_validation->set_rules('live_streaming_url', 'Live Streaming Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('sort_order', 'Sort Order', 'trim|required|max_length[11]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'invitation_id' => $this->input->post('invitation_id'),
				'event_name' => $this->input->post('event_name'),
				'event_date' => $this->input->post('event_date'),
				'time_start' => $this->input->post('time_start'),
				'time_end' => $this->input->post('time_end'),
				'timezone' => $this->input->post('timezone'),
				'venue_name' => $this->input->post('venue_name'),
				'address' => $this->input->post('address'),
				'latitude' => $this->input->post('latitude'),
				'longitude' => $this->input->post('longitude'),
				'google_maps_url' => $this->input->post('google_maps_url'),
				'live_streaming_url' => $this->input->post('live_streaming_url'),
				'sort_order' => $this->input->post('sort_order'),
			];

			
			$save_invitation_events = $this->model_invitation_events->change($id, $save_data);

			if ($save_invitation_events) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/invitation_events', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invitation_events');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invitation_events');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Invitation Eventss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('invitation_events_delete');

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
            set_message(cclang('has_been_deleted', 'invitation_events'), 'success');
        } else {
            set_message(cclang('error_delete', 'invitation_events'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Invitation Eventss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('invitation_events_view');

		$this->data['invitation_events'] = $this->model_invitation_events->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Invitation Events Detail');
		$this->render('backend/standart/administrator/invitation_events/invitation_events_view', $this->data);
	}
	
	/**
	* delete Invitation Eventss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$invitation_events = $this->model_invitation_events->find($id);

		
		
		return $this->model_invitation_events->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('invitation_events_export');

		$this->model_invitation_events->export('invitation_events', 'invitation_events');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('invitation_events_export');

		$this->model_invitation_events->pdf('invitation_events', 'invitation_events');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('invitation_events_export');

		$table = $title = 'invitation_events';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_invitation_events->find($id);
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


/* End of file invitation_events.php */
/* Location: ./application/controllers/administrator/Invitation Events.php */