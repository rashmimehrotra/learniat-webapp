<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Answer extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Answer details
	 */
	public function index()
	{
		$this->load->database();
		$sessionId = $this->input->get('sessionId');
		$questionId = $this->input->get('questionId');
		$studentId = $this->input->get('studentId');
		$this->load->model('question_log');
		$answerDetails = $this->question_log->getQuestionAnswerData($questionId, $studentId, $sessionId);

        if (empty($answerDetails)) {
            echo 'No answer details found.';
            exit;
        }
		$this->load->library('services/External', '','externalService');
		$assessmentAnswerId = $answerDetails->assessment_answer_id;
		$pageUrl = $this->externalService->getAssessmentAnswerData($assessmentAnswerId);

		$this->load->library('curl');
		$result = $this->curl->simple_get($pageUrl);

		$assessmentAnswerData = $this->externalService->xmlToObject($result);

		$this->load->model('question_option');
		$questionOptionData = $this->question_option->getQuestionOptionDataByType($assessmentAnswerData->QuestionType, $questionId);

		//Only used for scribble question
		$this->load->model('question');
		$questionScribble = $this->question->getQuestionScribble($questionId);
		//echo'<pre>'; print_r($questionOptionData);exit;
		$data = array(
			'answerDetails' => $answerDetails,
			'assessmentAnswerData' => $assessmentAnswerData,
			'questionOptionData' => $questionOptionData,
			'questionScribble' => $questionScribble
		);
		
		$this->load->view('answer/index', $data);
		
	}
}
