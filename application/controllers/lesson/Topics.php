<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Topics extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function index()
	{

		$this->load->model('classes');
		$this->load->helper('form');

		$userId = $this->session->profileData['user_id'];

		$lastParentTopicId = $this->input->get('lastParentTopicId');
		$lastClassId = $this->input->get('lastClassId');
		$lastSchoolId = $this->input->get('lastSchoolId');
		$lastParentTopicId = (isset($lastParentTopicId)) ? $lastParentTopicId : 0;
		
		$lastSubTopicId = $this->input->get('lastSubTopicId');
		$lastSubTopicId = (isset($lastSubTopicId)) ? $lastSubTopicId : 0;
				
		$classesList = $this->classes->getListLessonDetails($userId);
		$data = array(
			'classesList' => $classesList,
			'lastClassId' => $lastClassId,
			'lastSchoolId' => $lastSchoolId,
			'lastParentTopicId' => $lastParentTopicId,
			'lastSubTopicId' => $lastSubTopicId
			);
		$this->load->view('lesson/topic-list', $data);
	}
}