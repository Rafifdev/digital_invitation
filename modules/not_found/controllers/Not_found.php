<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section
class Not_found extends Front
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->library('cc_app');
	}

	public function index()
	{
		$this->cc_app->eventListen('show_error_404');
		$this->template->build('error_404');
	}
}


/* End of file Page.php */
/* Location: ./application/controllers/Page.php */