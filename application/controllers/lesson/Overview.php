<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Overview extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function index()
	{
		echo 'avi';exit;
	}
}