<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Harga Pelanggan Controller
*| --------------------------------------------------------------------------
*| Harga Pelanggan site
*|
*/
class Harga_pelanggan extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_harga_pelanggan');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Harga Pelanggans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('harga_pelanggan_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['harga_pelanggans'] = $this->model_harga_pelanggan->get($filter, $field, $this->limit_page, $offset);
		$this->data['harga_pelanggan_counts'] = $this->model_harga_pelanggan->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/harga_pelanggan/index/',
			'total_rows'   => $this->model_harga_pelanggan->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Harga Pelanggan List');
		$this->render('backend/standart/administrator/harga_pelanggan/harga_pelanggan_list', $this->data);
	}
	
	/**
	* Add new harga_pelanggans
	*
	*/
	public function add()
	{
		$this->is_allowed('harga_pelanggan_add');

		$this->template->title('Harga Pelanggan New');
		$this->render('backend/standart/administrator/harga_pelanggan/harga_pelanggan_add', $this->data);
	}

	/**
	* Add New Harga Pelanggans
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('harga_pelanggan_add', false)) {
			$this->response([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]); return;
		}

		$this->form_validation->set_rules('produk_id', 'Produk Id', 'trim|required');
		$this->form_validation->set_rules('tipe_pelanggan', 'Tipe Pelanggan', 'trim|required');
		$this->form_validation->set_rules('min_qty', 'Min Qty', 'trim|required');
		$this->form_validation->set_rules('harga', 'Harga', 'trim|required|max_length[18]');
		$this->form_validation->set_rules('diskon_persen', 'Diskon Persen', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('created_at', 'Created At', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'produk_id' => $this->input->post('produk_id'),
				'tipe_pelanggan' => $this->input->post('tipe_pelanggan'),
				'min_qty' => $this->input->post('min_qty'),
				'harga' => $this->input->post('harga'),
				'diskon_persen' => $this->input->post('diskon_persen'),
				'created_at' => $this->input->post('created_at'),
			];

			
			$save_harga_pelanggan = $this->model_harga_pelanggan->store($save_data);
            

			if ($save_harga_pelanggan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_harga_pelanggan;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/harga_pelanggan/edit/' . $save_harga_pelanggan, 'Edit Harga Pelanggan'),
						anchor('administrator/harga_pelanggan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/harga_pelanggan/edit/' . $save_harga_pelanggan, 'Edit Harga Pelanggan')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/harga_pelanggan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/harga_pelanggan');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		$this->response($this->data);
	}
	
		/**
	* Update view Harga Pelanggans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('harga_pelanggan_update');

		$this->data['harga_pelanggan'] = $this->model_harga_pelanggan->find($id);

		$this->template->title('Harga Pelanggan Update');
		$this->render('backend/standart/administrator/harga_pelanggan/harga_pelanggan_update', $this->data);
	}

	/**
	* Update Harga Pelanggans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('harga_pelanggan_update', false)) {
			$this->response([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]); return;
		}
		
		$this->form_validation->set_rules('produk_id', 'Produk Id', 'trim|required');
		$this->form_validation->set_rules('tipe_pelanggan', 'Tipe Pelanggan', 'trim|required');
		$this->form_validation->set_rules('min_qty', 'Min Qty', 'trim|required');
		$this->form_validation->set_rules('harga', 'Harga', 'trim|required|max_length[18]');
		$this->form_validation->set_rules('diskon_persen', 'Diskon Persen', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('created_at', 'Created At', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'produk_id' => $this->input->post('produk_id'),
				'tipe_pelanggan' => $this->input->post('tipe_pelanggan'),
				'min_qty' => $this->input->post('min_qty'),
				'harga' => $this->input->post('harga'),
				'diskon_persen' => $this->input->post('diskon_persen'),
				'created_at' => $this->input->post('created_at'),
			];

			
			$save_harga_pelanggan = $this->model_harga_pelanggan->change($id, $save_data);

			if ($save_harga_pelanggan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/harga_pelanggan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/harga_pelanggan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/harga_pelanggan');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		$this->response($this->data);
	}
	
	/**
	* delete Harga Pelanggans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('harga_pelanggan_delete');

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
            set_message(cclang('has_been_deleted', 'harga_pelanggan'), 'success');
        } else {
            set_message(cclang('error_delete', 'harga_pelanggan'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Harga Pelanggans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('harga_pelanggan_view');

		$this->data['harga_pelanggan'] = $this->model_harga_pelanggan->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Harga Pelanggan Detail');
		$this->render('backend/standart/administrator/harga_pelanggan/harga_pelanggan_view', $this->data);
	}
	
	/**
	* delete Harga Pelanggans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$harga_pelanggan = $this->model_harga_pelanggan->find($id);

		
		
		return $this->model_harga_pelanggan->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('harga_pelanggan_export');

		$this->model_harga_pelanggan->export('harga_pelanggan', 'harga_pelanggan');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('harga_pelanggan_export');

		$this->model_harga_pelanggan->pdf('harga_pelanggan', 'harga_pelanggan');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('harga_pelanggan_export');

		$table = $title = 'harga_pelanggan';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_harga_pelanggan->find($id);
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


/* End of file harga_pelanggan.php */
/* Location: ./application/controllers/administrator/Harga Pelanggan.php */