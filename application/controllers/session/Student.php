<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student extends MY_Controller {

    function __construct()
    {
        parent::__construct();
    }
    
    public function query()
    {
        $this->load->database();
        $this->load->model('student_query');
        $this->load->model('table_auth');
        $this->load->model('query_volunteer');
        $this->load->model('student_class_map');
        
        $studentId = $this->input->get('studentId');
        $sessionId = $this->input->get('sessionId');
        
        $studentDetails = $this->table_auth->getUserInfo($studentId);
        $studentQueryDetails = $this->student_query->getStudentQueryDataByStudent($studentId, $sessionId);
        $volunteerNotSelectedQueryDetails = $this->query_volunteer->getVolunteerNotSelectedQueryByStudentId($studentId, $sessionId);
        $volunteerSelectedQueryDetails = $this->query_volunteer->getVolunteerSelectedQueryByStudentId($studentId, $sessionId);
        $studentAttendedNumber = $this->student_class_map->getSessionAttendedStudentInfo($sessionId, $getNumRows = TRUE);
        
        $data = array(
        	'studentDetails' => $studentDetails,
            'studentQueryDetails' => $studentQueryDetails,
        	'volunteerNotSelectedQueryDetails' => $volunteerNotSelectedQueryDetails,
        	'volunteerSelectedQueryDetails' => $volunteerSelectedQueryDetails,
        	'studentAttendedNumber' => $studentAttendedNumber,
        	'sessionId' => $sessionId
        );
        $this->load->view('student/querylist', $data);
    }
    
    public function question()
    {
        $this->load->database();
        $this->load->model('question_log');
        $topicId = $this->input->get('topicId');
        $studentId = $this->input->get('studentId');
        $sessionId = $this->input->get('sessionId');
        $questionDetails = $this->question_log->getQuestionListWithClassAverageScore($studentId, $sessionId);
        
        $data = array(
        	'questionDetails' => $questionDetails,
        	'studentId' => $studentId,
       		'sessionId' => $sessionId,
        );
        $this->load->view('student/questionlist', $data);
    }
}