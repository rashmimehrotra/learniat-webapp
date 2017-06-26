<?php
class MY_Controller extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        if(!$this->session->userdata('isLoggedIn') && !$this->session->userdata('isTeacherLoggedIn')) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(401)
                    ->_display();
                exit;
            } else {
                redirect('/login');
            }
        }
    }
}
