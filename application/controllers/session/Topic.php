<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Topic extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function query()
    {
        $this->load->model('student_query');
        $topicId = $this->input->get('topicId');
        $sessionId = $this->input->get('sessionId');
        $studentQueryDetails = $this->student_query->getStudentQueryDataByTopic($topicId, $sessionId);
        $data = array(
            'studentQueryDetails' => $studentQueryDetails,
        	'sessionId' => $sessionId
        );
        $this->load->view('topic/querylist', $data);
        //echo '<pre>';print_r($studentQueryDetails); exit;
    }
    
    public function question()
    {
        $this->load->model('question_log');
        $topicId = $this->input->get('topicId');
        $sessionId = $this->input->get('sessionId');
        $questionDetails = $this->question_log->getQuestionWithAverageData( $sessionId, $topicId);
        $data = array(
            'sessionId' => $sessionId,
            'questionDetails' => $questionDetails
        );
        $this->load->view('topic/questionlist', $data);
    }
    
    public function result()
    {
    	$this->load->model('student_class_map');
    	$this->load->model('query_volunteer');
    	$queryId = $this->input->get('queryId');
    	$sessionId = $this->input->get('sessionId');
    	
    	$queryDetails = $this->query_volunteer->getVolunteerListByQueryId($queryId);
    	$volunteerSelectedQueryDetails = $this->query_volunteer->getAnsweredList($queryId);
    	$studentAttendedNumber = $this->student_class_map->getSessionAttendedStudentInfo($sessionId, $getNumRows = TRUE);
    	
    	$data = array(
    			'queryDetails' => $queryDetails,
    			'volunteerSelectedQueryDetails' => $volunteerSelectedQueryDetails,
    			'studentAttendedNumber' => $studentAttendedNumber
    		);
    	$this->load->view('topic/resultdetails', $data);
    }
    
    
    public function resultquestion()
    {
        $sessionId = $this->input->get('sessionId');
    	$questionId = $this->input->get('questionId');
    	
    	$this->load->model('question');
        $this->load->model('student_class_map');
    	$questionDetails = $this->question->getQuestionWithAverageDataByQuestionId($questionId);
        $studentScoreInfo = $this->student_class_map->getStudentScoreInfoByQuestionId($questionId, $sessionId);

    	$data = array(
            'questionDetails' => $questionDetails,
            'sessionId' => $sessionId,
            'studentScoreInfo' => $studentScoreInfo
        );
    	$this->load->view('topic/resultquestion', $data);
    }
}