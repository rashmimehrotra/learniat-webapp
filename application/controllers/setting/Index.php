<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }


    /**
     * Profile details
     */
    public function profile()
    {
        $this->load->helper('form');
        $this->load->database();
        $this->load->view('setting/profile');
    }
}