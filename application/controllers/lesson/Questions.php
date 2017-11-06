<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Questions extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function type()
	{
		$this->load->helper('form');
		$this->load->model('question_option');
        $this->load->model('question_log');

		$questionTypeId = $this->input->get('questionTypeId');
		$questionId = $this->input->get('questionId');
        $duplicate = filter_var($this->input->get('duplicate'), FILTER_VALIDATE_BOOLEAN);

        $questionAverageScore = 1;
		$questionOptionData = array();
		if (!empty($questionId)) {
			$questionOptionData = $this->question_option->getQuestionOptionDataByTypeId($questionTypeId, $questionId);

            //if ($duplicate) {
                $questionAverageScore = $this->question_log->getClassAverageScore($questionId, $getNumRows = TRUE);
            //}
		}

		$this->load->view('lesson/question/type',
			array(
                'duplicate' => $duplicate,
                'questionTypeId' => $questionTypeId,
                'questionOptionData' => $questionOptionData,
                'questionAverageScore' => $questionAverageScore
			)
        );
	}


    public function removeOption()
    {
        $this->load->model('question_option');
        $questionOptionId = $this->input->get('questionOptionId');
        if (strpos($questionOptionId, ',') > 0) {
            $questionOptionIds = explode(',', $questionOptionId);
        } else {
            $questionOptionIds = array($questionOptionId);
        }
        foreach ($questionOptionIds AS $questionOptionIds) {
            $this->question_option->deleteQuestionOptionById($questionOptionId);
        }
    }
	
	public function saveOption()
	{
		$parentTopicId = $this->input->get('parentTopicId');
		$classId = $this->input->get('classId');
		$schoolId = $this->input->get('schoolId');
        $topicId = $this->input->post('topicId');

		$redirectUrl = '/lesson/questions/parentTopic' . "?parentTopicId=$parentTopicId&classId=$classId&schoolId=$schoolId";

        if (isset($topicId) && $topicId > 0) {
            $redirectUrl .= "&lastSubTopicId=$topicId"  ;
        }

		if (isset($_POST['save'])) {
			$optionData = $this->input->post();
			$this->load->model('question');
			$teacherId = $this->session->profileData['user_id'];

			if ($optionData['topicId'] === 'null' || $optionData['questionType'] === 'null') {
				return false;
			}
			if (in_array($optionData['questionType'], array(1, 2)) && (isset($optionData['corrected'])) ) {
				$optionData['questionType'] = (count($optionData['corrected']) > 1) ? 2 : 1;
			}
			
			try {
				if (isset($optionData['questionType'])) {

					if ((!empty($optionData['questionId']) && $optionData['questionId'] !== 'null')
							&& ($optionData['duplicate'] === 'false' || $optionData['duplicate'] === false)) {
						
						$checkQuestionExist = $this->question->checkQuestionExist($optionData['question'], $optionData['questionId']);
						if ($checkQuestionExist) {
							$redirectUrl .= '&error=Question already exist.';
						} else {
							//update new record
							$this->question->updateQuestionWithOption($optionData['questionId'], $optionData, $teacherId);
							$redirectUrl .= '&success=Question data has been saved successfully.';
						}
					} else {

						$checkQuestionExist = $this->question->checkQuestionExist($optionData['question']);
						if ($checkQuestionExist) {
							$redirectUrl .= '&error=Question already exist.';
						} else {
							//Insert new record
							$this->question->insertQuestionWithOption($optionData, $teacherId);
							$redirectUrl .= '&success=Question data has been saved successfully.';
						}
					}
				}
			} catch (Exception $e) {
				log_message('error', 'Error while save option - ' . $e->getMessage());
			}
		} else {
			$redirectUrl .= '&error=insufficient data provided.';
		}

        echo $redirectUrl;
		//redirect($redirectUrl,  'location');
	}
	
	public function parentTopic()
	{
        $data = $this->getTopicDetails();
        $this->load->view('lesson/question/topic', $data);
	}

    public function parentTopicDetails()
    {
        $data = $this->getTopicDetails();
        $this->load->view('lesson/question/topic-details', $data);
    }
	
	//Delete question
	public function delete()
	{
        $flag = false;
		$questionId = $this->input->get('questionId');
		$this->load->model('question');
		if (isset($questionId)) {
			$this->question->deleteQuestionByQuestionId($questionId);
            $flag = true;
		}
		
		return $flag;
	}

    public function getTopicDetails()
    {
        $this->load->model('classes');
        $this->load->model('topic');
        $this->load->model('question');
        $this->load->helper('form');

        $parentTopicId = $this->input->get('parentTopicId');
        $classId = $this->input->get('classId');
        $schoolId = $this->input->get('schoolId');
        $error = $this->input->get('error');
        $success = $this->input->get('success');
        $lastSubTopicId = $this->input->get('lastSubTopicId');

        $parentTopicData = $this->topic->getParentTopicDetailsByTopicId($parentTopicId);
        $otherDetails = $this->classes->getParentTopicDetailsForLesson($classId, $parentTopicId);
        $subTopicData = $this->classes->getSubTopicList($classId, $parentTopicId);

        return array(
            'parentTopicData' => $parentTopicData[0],
            'subTopicData' => $subTopicData['subTopicList'],
            'otherDetails' => $otherDetails,
            'schoolId' => $schoolId,
            'parentTopicId' => $parentTopicId,
            'lastSubTopicId' => $lastSubTopicId,
            'classId' => $classId,
            'error' => $error,
            'success' => $success
        );
    }
}