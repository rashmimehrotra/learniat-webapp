<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function index()
    {
    	$this->load->model('classes');
    	$teacherId = $this->session->profileData['user_id'];
    	
    	$classesList = $this->classes->getListClassWithStudentDetails($teacherId);
    	$data = array('classesList' => $classesList);
    	$this->load->view('student/classes-list', $data);
    }

    public function view()
    {
        $classId = $this->input->get('classId');
        $sortBy = $this->input->get('sortBy');
        $hiddenStudent = $this->input->get('hiddenStudent');

        $this->load->model('student');
        $this->load->model('graph');

        $sortBy = (!empty($sortBy)) ? $sortBy : NULL;
        $hiddenStudent = (!empty($hiddenStudent)) ? $hiddenStudent : 0;

        $studentData = $this->graph->getClassesGraphDataByClassId($classId);
        $studentData = $this->student->sortByIndexKey($studentData, $sortBy, TRUE);

        $data = array(
            'classId' => $classId,
            'hiddenStudent' => (int)$hiddenStudent,
            'studentData' => $studentData
        );
        $this->load->view('student/list', $data);
    }
}