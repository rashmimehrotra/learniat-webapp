<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct()
    {
       
    	parent::__construct();
	
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->library('Session/session');

    }

    function index()
    {
    	if( !$this->session->userdata('isLoggedIn') && !$this->session->userdata('isTeacherLoggedIn')) {
    		$this->load->view('login');
    	} else {
    		if($this->session->userdata('isLoggedIn')) {
    			redirect('/session/summary/index');
    		}else if($this->session->userdata('isTeacherLoggedIn')) {
    			redirect('/session/summary/index');
    		}
    	}
	}

	//AJAX LOGIN
	function ajax_check()
	{
		$this->load->database();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');

		$this->form_validation->set_message('required', 'Please fill in the fields');

        if($this->form_validation->run() == FALSE) {
           	$form['error'] = "Please fill all fields";
			$this->load->view('login', $form);
        } else {
        	$this->load->model('m_access');
			$user = $this->m_access->check_user($this->input->post('username'),$this->input->post('password'));
			if($user == '4') {
				redirect('/session/summary/index');
			} else if($user == '3'){
				redirect('/session/summary/index');
			}else{
				$form['error']="Username does not exists, please try again.";
				$this->load->view('login',$form);
			}
        }
	}
	
	function logout()
	{
		$user_data = $this->session->all_userdata();
		foreach ($user_data as $key => $value) {
			if ($key != 'session_id' && $key != 'ip_address' && $key != 'user_agent' && $key != 'last_activity') {
				$this->session->unset_userdata($key);
			}
		}
		$this->session->sess_destroy();
		redirect('login/index');
	}
      
}

?>