<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Session summery details
	 */
	public function index()
	{
		$this->load->database();
		$this->load->model('class_session');
		$this->load->helper('form');
		$userId = $this->session->profileData['user_id'];
		$classId = $this->input->post('sessionClassId');
		$selectDuration = $this->input->post('sessionDuration');
		$sessionResult = $this->class_session->getAllSessionIdByUserId($userId, $selectDuration, $classId, 5);
		
		$this->load->library('services/External', '','externalService');
		$sessionDates = $this->externalService->getSessionIdByDate($sessionResult);
		$classesData = $this->class_session->getClassesData($userId);

		$data = array(
			'sessionDates' => $sessionDates,
			'classesData' => $classesData
		);
		$this->load->view('session/summary', $data);
	}
	
	/*
	 * Session summary page
	 */
	public function summary()
	{
		$this->load->database();
		$this->load->model('class_session');
		$userId = $this->session->profileData['user_id'];
		$sessionId = $this->input->get('sessionId');
		$sessionDate = $this->input->get('sessionDate');
		$sessionResult = $this->class_session->getSessionSummaryDetails($sessionId, $sessionDate);
		
		$this->load->model('student');
		$studentData = $this->student->getAllStudentIndex($sessionId);
		
		$data = array(
			'sessionId' => $sessionId,
			'sessionResult' => $sessionResult,
			'studentData' => $studentData
		);
		$this->load->view('session/class_details', $data);
		
	}
	
	//Details of session
	public function details()
	{
		$this->load->database();
		$this->load->model('class_session');
		$sessionId = $this->input->get('sessionId');
		$topicList = $this->class_session->getTopicList($sessionId);
		
		$data = array(
	        'sessionId' => $sessionId,
	        'topicList' => $topicList
		);
		$this->load->view('session/topic_summary', $data);
	}
	
}
