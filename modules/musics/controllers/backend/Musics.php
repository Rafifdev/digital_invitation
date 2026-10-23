<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Musics Controller
*| --------------------------------------------------------------------------
*| Musics site
*|
*/
class Musics extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_musics');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Musicss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('musics_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['musicss'] = $this->model_musics->get($filter, $field, $this->limit_page, $offset);
		$this->data['musics_counts'] = $this->model_musics->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/musics/index/',
			'total_rows'   => $this->model_musics->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Master Lagu');
		$this->render('backend/standart/administrator/musics/musics_list', $this->data);
	}
	
	/**
	* Add new musicss
	*
	*/
	public function add()
	{
		$this->is_allowed('musics_add');

		$this->template->title('Tambah Master Lagu');
		$this->render('backend/standart/administrator/musics/musics_add', $this->data);
	}

	/**
	* Add New Musicss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('musics_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('artist', 'Artist', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('musics_file_url_name', 'File Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('is_active', 'Status Aktif', 'trim|max_length[1]');
		
		

		if ($this->form_validation->run()) {
			$musics_file_url_uuid = $this->input->post('musics_file_url_uuid');
			$musics_file_url_name = $this->input->post('musics_file_url_name');
		
			$save_data = [
				'title' => $this->input->post('title'),
				'artist' => $this->input->post('artist'),
				'category' => $this->input->post('category'),
				'is_active' => $this->input->post('is_active'),
				'created_at' => date('Y-m-d H:i:s'),
			];

			if (!is_dir(FCPATH . '/uploads/musics/')) {
				mkdir(FCPATH . '/uploads/musics/');
			}

			if (!empty($musics_file_url_name)) {
				$musics_file_url_name_copy = date('YmdHis') . '-' . $musics_file_url_name;

				rename(FCPATH . 'uploads/tmp/' . $musics_file_url_uuid . '/' . $musics_file_url_name, 
						FCPATH . 'uploads/musics/' . $musics_file_url_name_copy);

				if (!is_file(FCPATH . '/uploads/musics/' . $musics_file_url_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_url'] = $musics_file_url_name_copy;
			}
		
			
			$save_musics = $this->model_musics->store($save_data);
            

			if ($save_musics) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_musics;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/musics/edit/' . $save_musics, 'Edit Musics'),
						anchor('administrator/musics', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/musics/edit/' . $save_musics, 'Edit Musics')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/musics');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/musics');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Musicss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('musics_update');

		$this->data['musics'] = $this->model_musics->find($id);

		$this->template->title('Edit Master Lagu');
		$this->render('backend/standart/administrator/musics/musics_update', $this->data);
	}

	/**
	* Update Musicss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('musics_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('artist', 'Artist', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('musics_file_url_name', 'File Url', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('is_active', 'Status Aktif', 'trim|max_length[1]');
		
		
		if ($this->form_validation->run()) {
			$musics_file_url_uuid = $this->input->post('musics_file_url_uuid');
			$musics_file_url_name = $this->input->post('musics_file_url_name');
		
			$save_data = [
				'title' => $this->input->post('title'),
				'artist' => $this->input->post('artist'),
				'category' => $this->input->post('category'),
				'is_active' => $this->input->post('is_active'),
				'created_at' => date('Y-m-d H:i:s'),
			];

			if (!is_dir(FCPATH . '/uploads/musics/')) {
				mkdir(FCPATH . '/uploads/musics/');
			}

			if (!empty($musics_file_url_uuid)) {
				$musics_file_url_name_copy = date('YmdHis') . '-' . $musics_file_url_name;

				rename(FCPATH . 'uploads/tmp/' . $musics_file_url_uuid . '/' . $musics_file_url_name, 
						FCPATH . 'uploads/musics/' . $musics_file_url_name_copy);

				if (!is_file(FCPATH . '/uploads/musics/' . $musics_file_url_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_url'] = $musics_file_url_name_copy;
			}
		
			
			$save_musics = $this->model_musics->change($id, $save_data);

			if ($save_musics) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/musics', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/musics');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/musics');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Musicss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('musics_delete');

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
            set_message(cclang('has_been_deleted', 'musics'), 'success');
        } else {
            set_message(cclang('error_delete', 'musics'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Musicss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('musics_view');

		$this->data['musics'] = $this->model_musics->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Detail Master Lagu');
		$this->render('backend/standart/administrator/musics/musics_view', $this->data);
	}
	
	/**
	* delete Musicss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$musics = $this->model_musics->find($id);

		if (!empty($musics->file_url)) {
			$path = FCPATH . '/uploads/musics/' . $musics->file_url;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_musics->remove($id);
	}
	
	/**
	* Upload Image Musics	* 
	* @return JSON
	*/
	public function upload_file_url_file()
	{
		if (!$this->is_allowed('musics_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'musics',
		]);
	}

	/**
	* Delete Image Musics	* 
	* @return JSON
	*/
	public function delete_file_url_file($uuid)
	{
		if (!$this->is_allowed('musics_delete', false)) {
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
            'table_name'        => 'musics',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/musics/'
        ]);
	}

	/**
	* Get Image Musics	* 
	* @return JSON
	*/
	public function get_file_url_file($id)
	{
		if (!$this->is_allowed('musics_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$musics = $this->model_musics->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_url', 
            'table_name'        => 'musics',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/musics/',
            'delete_endpoint'   => 'administrator/musics/delete_file_url_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('musics_export');

		$this->model_musics->export('musics', 'musics');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('musics_export');

		$this->model_musics->pdf('musics', 'musics');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('musics_export');

		$table = $title = 'musics';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_musics->find($id);
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


/* End of file musics.php */
/* Location: ./application/controllers/administrator/Musics.php */