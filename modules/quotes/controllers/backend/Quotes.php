<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Quotes Controller
*| --------------------------------------------------------------------------
*| Quotes site
*|
*/
class Quotes extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_quotes');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Quotess
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('quotes_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['quotess'] = $this->model_quotes->get($filter, $field, $this->limit_page, $offset);
		$this->data['quotes_counts'] = $this->model_quotes->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/quotes/index/',
			'total_rows'   => $this->model_quotes->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Master Kutipan & Ayat');
		$this->render('backend/standart/administrator/quotes/quotes_list', $this->data);
	}
	
	/**
	* Add new quotess
	*
	*/
	public function add()
	{
		$this->is_allowed('quotes_add');

		$this->template->title('Tambah Kutipan & Ayat');
		$this->render('backend/standart/administrator/quotes/quotes_add', $this->data);
	}

	/**
	* Add New Quotess
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('quotes_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('source', 'Source', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('content_original', 'Teks Asli / Lafadz Arab', 'trim');
		$this->form_validation->set_rules('content', 'Content', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Is Active', 'trim|required|max_length[1]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'category' => $this->input->post('category'),
				'source' => $this->input->post('source'),
				'content_original' => $this->input->post('content_original'),
				'content' => $this->input->post('content'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_quotes = $this->model_quotes->store($save_data);
            

			if ($save_quotes) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_quotes;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/quotes/edit/' . $save_quotes, 'Edit Quotes'),
						anchor('administrator/quotes', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/quotes/edit/' . $save_quotes, 'Edit Quotes')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/quotes');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/quotes');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Quotess
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('quotes_update');

		$this->data['quotes'] = $this->model_quotes->find($id);

		$this->template->title('Edit Kutipan & Ayat');
		$this->render('backend/standart/administrator/quotes/quotes_update', $this->data);
	}

	/**
	* Update Quotess
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('quotes_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('source', 'Source', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('content_original', 'Teks Asli / Lafadz Arab', 'trim');
		$this->form_validation->set_rules('content', 'Content', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Is Active', 'trim|required|max_length[1]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'category' => $this->input->post('category'),
				'source' => $this->input->post('source'),
				'content_original' => $this->input->post('content_original'),
				'content' => $this->input->post('content'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_quotes = $this->model_quotes->change($id, $save_data);

			if ($save_quotes) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/quotes', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/quotes');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/quotes');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Quotess
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('quotes_delete');

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
            set_message(cclang('has_been_deleted', 'quotes'), 'success');
        } else {
            set_message(cclang('error_delete', 'quotes'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Quotess
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('quotes_view');

		$this->data['quotes'] = $this->model_quotes->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Detail Kutipan & Ayat');
		$this->render('backend/standart/administrator/quotes/quotes_view', $this->data);
	}
	
	/**
	* delete Quotess
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$quotes = $this->model_quotes->find($id);

		
		
		return $this->model_quotes->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('quotes_export');

		$this->model_quotes->export('quotes', 'quotes');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('quotes_export');

		$this->model_quotes->pdf('quotes', 'quotes');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('quotes_export');

		$table = $title = 'quotes';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_quotes->find($id);
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


/* End of file quotes.php */
/* Location: ./application/controllers/administrator/Quotes.php */