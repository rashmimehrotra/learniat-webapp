<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apicontroller extends CI_Controller {

    function __construct()
    {
       
    	parent::__construct();
	
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->library('session');

    }
	function loadcommonapi()
	{
		$this->load->model('sun');
		//$getxml='<Sunstone><Action><Service>GetMyInfo</Service><UserId>532</UserId><DeviceId>532</DeviceId><UUID>532</UUID></Action></Sunstone>';
		//$postxml='';
		error_reporting(0);
		$getxml = $_GET['api'];
		$postxml = $_POST['data'];
		//$data=array('getxml' => $getxml,'postxml' => $postxml);
		$this->sun->callManager($getxml,$postxml);
	}
      
}

?>