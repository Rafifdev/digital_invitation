<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Packages Controller
*| --------------------------------------------------------------------------
*| Packages site
*|
*/
class Packages extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_packages');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Packagess
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('packages_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['packagess'] = $this->model_packages->get($filter, $field, $this->limit_page, $offset);
		$this->data['packages_counts'] = $this->model_packages->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/packages/index/',
			'total_rows'   => $this->model_packages->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Packages List');
		$this->render('backend/standart/administrator/packages/packages_list', $this->data);
	}
	
	/**
	* Add new packagess
	*
	*/
	public function add()
	{
		$this->is_allowed('packages_add');

		$this->template->title('Packages New');
		$this->render('backend/standart/administrator/packages/packages_add', $this->data);
	}

	/**
	* Add New Packagess
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('packages_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('price', 'Price', 'trim|required|max_length[12]');
		$this->form_validation->set_rules('admin_assist_fee', 'Admin Assist Fee', 'trim|required|max_length[12]');
		$this->form_validation->set_rules('includes_admin_assist', 'Includes Admin Assist', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('max_photos', 'Max Photos', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('max_videos', 'Max Videos', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('max_guests', 'Max Guests', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('allow_custom_music', 'Allow Custom Music', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('allow_rsvp', 'Allow Rsvp', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('allow_digital_gift', 'Allow Digital Gift', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('has_watermark', 'Has Watermark', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('active_days', 'Active Days', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('is_active', 'Is Active', 'trim|required|max_length[1]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'price' => $this->input->post('price'),
				'admin_assist_fee' => $this->input->post('admin_assist_fee'),
				'includes_admin_assist' => $this->input->post('includes_admin_assist'),
				'max_photos' => $this->input->post('max_photos'),
				'max_videos' => $this->input->post('max_videos'),
				'max_guests' => $this->input->post('max_guests'),
				'allow_custom_music' => $this->input->post('allow_custom_music'),
				'allow_rsvp' => $this->input->post('allow_rsvp'),
				'allow_digital_gift' => $this->input->post('allow_digital_gift'),
				'has_watermark' => $this->input->post('has_watermark'),
				'active_days' => $this->input->post('active_days'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_packages = $this->model_packages->store($save_data);
            

			if ($save_packages) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_packages;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/packages/edit/' . $save_packages, 'Edit Packages'),
						anchor('administrator/packages', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/packages/edit/' . $save_packages, 'Edit Packages')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/packages');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/packages');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Packagess
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('packages_update');

		$this->data['packages'] = $this->model_packages->find($id);

		$this->template->title('Packages Update');
		$this->render('backend/standart/administrator/packages/packages_update', $this->data);
	}

	/**
	* Update Packagess
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('packages_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('price', 'Price', 'trim|required|max_length[12]');
		$this->form_validation->set_rules('admin_assist_fee', 'Admin Assist Fee', 'trim|required|max_length[12]');
		$this->form_validation->set_rules('includes_admin_assist', 'Includes Admin Assist', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('max_photos', 'Max Photos', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('max_videos', 'Max Videos', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('max_guests', 'Max Guests', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('allow_custom_music', 'Allow Custom Music', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('allow_rsvp', 'Allow Rsvp', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('allow_digital_gift', 'Allow Digital Gift', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('has_watermark', 'Has Watermark', 'trim|required|max_length[1]');
		$this->form_validation->set_rules('active_days', 'Active Days', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('is_active', 'Is Active', 'trim|required|max_length[1]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'price' => $this->input->post('price'),
				'admin_assist_fee' => $this->input->post('admin_assist_fee'),
				'includes_admin_assist' => $this->input->post('includes_admin_assist'),
				'max_photos' => $this->input->post('max_photos'),
				'max_videos' => $this->input->post('max_videos'),
				'max_guests' => $this->input->post('max_guests'),
				'allow_custom_music' => $this->input->post('allow_custom_music'),
				'allow_rsvp' => $this->input->post('allow_rsvp'),
				'allow_digital_gift' => $this->input->post('allow_digital_gift'),
				'has_watermark' => $this->input->post('has_watermark'),
				'active_days' => $this->input->post('active_days'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_packages = $this->model_packages->change($id, $save_data);

			if ($save_packages) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/packages', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/packages');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/packages');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Packagess
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('packages_delete');

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
            set_message(cclang('has_been_deleted', 'packages'), 'success');
        } else {
            set_message(cclang('error_delete', 'packages'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Packagess
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('packages_view');

		$this->data['packages'] = $this->model_packages->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Packages Detail');
		$this->render('backend/standart/administrator/packages/packages_view', $this->data);
	}
	
	/**
	* delete Packagess
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$packages = $this->model_packages->find($id);

		
		
		return $this->model_packages->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('packages_export');

		$this->model_packages->export('packages', 'packages');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('packages_export');

		$this->model_packages->pdf('packages', 'packages');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('packages_export');

		$table = $title = 'packages';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_packages->find($id);
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


/* End of file packages.php */
/* Location: ./application/controllers/administrator/Packages.php */