<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }
    
    public function calendar()
    {
    	$this->load->database();
    	$this->load->model('class_session');
    	
    	$week = $this->input->get('week');
    	$year = $this->input->get('year');
    	$week = (!empty($week)) ? $week : date('W');
    	$year = (!empty($year)) ? $year : date('Y');
    	$timeTableData = $this->class_session->getTimeTableDetails($week, $year, $this->session->profileData['user_id']);
    	
    	$this->load->model('graph');
    	$timeTableData = $this->graph->getCalenderWithPIDetails($timeTableData);

        //$timeTableData ['data'][] = $timeTableData['data'][5];
        //$timeTableData['data'][5]->starts_on = '2015-08-23 06:36:41';
        //$timeTableData['data'][5]->ends_on = '2015-08-23 07:24:44';
    	//echo '<pre>'; print_r($timeTableData);exit;

    	$data = array(
            'timeTableData' => $timeTableData['data'],
            'formatData' => $timeTableData['formatData'],
            'week' => $week,
            'year' => $year
        );
    	$this->load->view('calendar/cal', $data);
    }

    public function record()
    {
        $this->load->database();
        $this->load->model('class_session');

        $week = $this->input->get('week');
        $year = $this->input->get('year');
        $week = (!empty($week)) ? $week : date('W');
        $year = (!empty($year)) ? $year : date('Y');
        $timeTableData = $this->class_session->getTimeTableDetails($week, $year, $this->session->profileData['user_id']);

        $this->load->model('graph');
        $timeTableData = $this->graph->getCalenderWithPIDetails($timeTableData);
        //echo '<pre>'; print_r($timeTableData);exit;

        $data = array(
            'timeTableData' => $timeTableData,
            'week' => $week,
            'year' => $year
        );
        $this->load->view('calendar/record', $data);
    }
}
