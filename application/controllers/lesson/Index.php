<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function view()
	{
		$this->load->model('classes');
		$this->load->helper('form');
		
		$classId = $this->input->get('classId');
		$schoolId = $this->input->get('schoolId');
		$parentTopicData = $this->classes->getLessonTopicDetails($classId, $schoolId);

		$lastParentTopicId = $this->input->get('lastParentTopicId');
		$lastSubTopicId = $this->input->get('lastSubTopicId');
		
		$data = array(
			'parentTopicData' => $parentTopicData,
			'classId' => $classId,
			'schoolId' => $schoolId,
			'lastParentTopicId' => $lastParentTopicId,
			'lastSubTopicId' => $lastSubTopicId
		);
		$this->load->view('lesson/topic/list', $data);
	}
	
	public function subtopic()
	{
		$this->load->model('classes');
		$this->load->helper('form');
	
		$parentTopicId = $this->input->get('parentTopicId');
		if (!isset($parentTopicId) || empty($parentTopicId)) {
			return TRUE;
		}
		
		$classId = $this->input->get('classId');
		$schoolId = $this->input->get('schoolId');
		$lastSubTopicId = $this->input->get('lastSubTopicId');
		$topicData = $this->classes->getSubTopicList($classId, $parentTopicId);

		$data = array(
			'topicData' => $topicData['subTopicList'],
			'parentTopicCheckBoxFlag' => $topicData['parentTopicCheckBoxFlag'],
			'classId' => $classId,
			'schoolId' => $schoolId,
			'parentTopicId'	=> $parentTopicId,
			'lastSubTopicId' => $lastSubTopicId
		);
		$this->load->view('lesson/topic/sub-topic-list', $data);
	}
	
	public function updateTagged()
	{
		$classId = $this->input->get('classId');
		$topicId = $this->input->get('topicId');
		$topicTagged = $this->input->get('topicTagged');
		
		if (isset($topicTagged)) {
			$this->load->model('lesson_plan');
			$teacherId = $this->session->profileData['user_id'];
			$this->lesson_plan->insertOnDuplicateKey($classId, $topicId, $topicTagged, $teacherId);
			$this->lesson_plan->updateSubTopicTaggedByParentTopic($classId, $topicId, $topicTagged, $teacherId);

			//If all subtopic checked/unchecked  then parent topic id also checked/unchecked
            $this->lesson_plan->updateParentTopicTaggedBySubTopic($classId, $topicId, $topicTagged, $teacherId);

			return true;
		}
	}
	
	public function update()
	{
		$topicId = $this->input->get('topicId');
		$topicName = $this->input->get('topicName');
		
		$this->load->model('topic');
		if (isset($topicId) && isset($topicName)) {
			$this->topic->updateTopicName($topicName, $topicId);
			
			return true;
		}
		
		return false;
	}
	
	public function delete()
	{
		$topicId = $this->input->get('topicId');
		$this->load->model('topic');
		if (isset($topicId)) {
			$this->topic->deleteRecordByTopicId($topicId);
				
			return true;
		}
	
		return false;
	}
	
	public function question_view()
	{
		$this->load->helper('form');
		$this->load->model('question');
		$topicId = $this->input->get('topicId');
		$classId = $this->input->get('classId');
		$schoolId = $this->input->get('schoolId');
        $parentTopicId = $this->input->get('parentTopicId');

		$questionDetails = $this->question->getQuestionWithAverageDataByTopicId($classId, $topicId);

		$this->load->view('lesson/question/list',
			array(
				'questionDetails' => $questionDetails,
				'schoolId' => $schoolId,
				'classId' => $classId,
				'topicId' => $topicId,
                'parentTopicId' => $parentTopicId
			));
	}
	
	public function question_edit()
	{
		$this->load->helper('form');
		$this->load->model('question_type');
		$this->load->model('question');
		$duplicate = $this->input->get('duplicate');
		$questionTypes = $this->question_type->getQuestionTypes();
		$questionId = $this->input->get('questionId');
		$topicId = $this->input->get('topicId');
	
		$questionData = array();
		if (!empty($questionId)) {
			$questionData = $this->question->getQuestionDetails($questionId);
		}

		$this->load->view('lesson/question/edit',
			array(
				'questionTypes' => $questionTypes,
				'duplicate' => $duplicate,
				'questionData' => $questionData,
				'questionId' => $questionId,
				'topicId' => $topicId
			));
	}
	
	public function addtopic()
	{
		$this->load->model('classes');
		$this->load->model('topic');
        $this->load->model('lesson_plan');

		$classId = $this->input->get('classId');
		$topicName = $this->input->get('topicName');
		$subjectId = $this->classes->getSubjectId($classId);
		
		if (!empty($topicName)) {
            $topicId = $this->topic->insertParentTopic($subjectId, $topicName);
            $teacherId = $this->session->profileData['user_id'];
            $this->lesson_plan->insertOnDuplicateKey($classId, $topicId, $topicTagged = 1, $teacherId);
		}
	}
	
	public function addsubtopic()
	{
		$this->load->model('classes');
		$this->load->model('topic');
        $this->load->model('lesson_plan');

		$parentTopicId = $this->input->get('parentTopicId');
		$classId = $this->input->get('classId');
		$subTopicName = $this->input->get('subTopicName');
		$subjectId = $this->classes->getSubjectId($classId);
		if (!empty($subTopicName)) {
			$subTopicId = $this->topic->insertSubTopic($parentTopicId, $subjectId, $subTopicName);
            $teacherId = $this->session->profileData['user_id'];
            $this->lesson_plan->insertOnDuplicateKey($classId, $subTopicId, $topicTagged = 1, $teacherId);
			
			return $subTopicId;
		}
	}
}