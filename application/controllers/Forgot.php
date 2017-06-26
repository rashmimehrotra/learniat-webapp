<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forgot extends CI_Controller {

    function __construct()
    {
       
    	parent::__construct();
        $this->load->database();
        $this->load->model('m_access');
        $this->load->helper('url');
    	$email_config = Array(
    			'protocol'  => 'smtp',
    			'smtp_host' => 'ssl://smtp.googlemail.com',
    			'smtp_port' => '465',
    			'smtp_user' => 'info@mindshiftapps.com',
    			'smtp_pass' => 'msas1234',
    			'mailtype'  => 'html',
    			'starttls'  => true,
    			'newline'   => "\r\n"
    	);
    	$this->load->library('email', $email_config);

    }

    function index()
    {
        $this->load->view('forgot_password');
	}
	function password()
    {
		$email = $this->input->post("emailID");
        if (empty($email)) {
            redirect('/login/index');
        }
		$user = $this->m_access->check_email($email);
		if($user['status']=="success"){
			$this->email->from('info@mindshiftapps.com', 'Admin-Learniat');
			$this->email->to($email);
			$this->email->subject('Forgot Username or Password');
			$body_email="<p style='color:#000;'>Dear Sir,<br/><br/>
						Greeting from <a href='http://www.http://54.251.104.13/webadmin/'>Learniat</a><br/><br/><br/>
						<br/><br/>Please find the Login details: <br/><br/><p>";
			$body_email.="<b>User Name:</b>&nbsp;&nbsp;&nbsp;".$user['username'];
			$body_email.="<br/><b>Password:</b>&nbsp;&nbsp;&nbsp;".$user['password'];
			$body_email.="<br/>Please login by clicking the link http://54.251.104.13/learniat/";
			$body_email.="<p style='color:gray;'><br/><br/>Thanks & Regards<br/>Admin-Learniat<br/></p>";
			$this->email->message($body_email);
			if($this->email->send()) {
                $form['error']="Your username and password are sent to your mail.Check and login again.";
                $this->load->view('login',$form);
            } else {
                $form['error']="Email error";
                $this->load->view('forgot_password',$form);
            }
		} else {
            $this->email->from('info@mindshiftapps.com', 'Admin-Learniat');
            $this->email->to($email);
            $this->email->subject('Forgot Username or Password');
			 $body_email="<p style='color:#000;'>Dear Sir,<br/><br/>
                Greeting from <a href='http://54.251.104.13/learniat/'>Learniat</a><br/><br/><br/>
                <br/><br/>Your details doesn't exist in our database.Please contact the School Admin</b>.<br/><br/><p>";
			$body_email.="<p style='color:gray;'><br/><br/>Thanks & Regards<br/>Admin-Learniat<br/></p>";
			$this->email->message($body_email);
			if ($this->email->send()) {
				$form['error']="Your details doesn't exist in our database.Please contact the School Admin";
				$this->load->view('login',$form);
			} else {
				$form['error']="Email error";
				$this->load->view('forgot_password',$form);
			}
		}
		
	}
}
