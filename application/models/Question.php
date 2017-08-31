<?php
class Question extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get question log
	 */
	public function getQuestionLogModel()
	{
		$CI = &get_instance();
		$CI->load->model('question_log');
		return $CI->question_log;
	}
	
	/**
	 * Get question option
	 */
	public function getQuestionOptionModel()
	{
		$CI = &get_instance();
		$CI->load->model('question_option');
		return $CI->question_option;
	}

    /**
     * Get uploaded images
     */
    public function getUploadedImagesModel()
    {
        $CI = &get_instance();
        $CI->load->model('uploaded_images');
        return $CI->uploaded_images;
    }

	/**
	 * Get question scribble path by question id
	 * @param integer $questionId
	 * @return array $pathInfo
	 */
	public function getQuestionScribble($questionId)
	{
		$sql = "SELECT image.image_path, q.scribble_id
		FROM questions AS q
		INNER JOIN uploaded_images as image on image.image_id = q.scribble_id
		WHERE q.question_id = $questionId";
		$pathInfo = $this->db->query($sql)->row();
		
		return $pathInfo;
	}
	
	/**
	 * Get question details
	 *
	 * @param integer $classId
	 * @param integer $topicId
	 * @param boolean $getCount
	 * @return array $questionInfo
	 */
	public function getParentQuestionData($classId, $topicId, $getCount = FALSE)
	{
		$sql = "SELECT DISTINCT question_name, question_id
		FROM questions AS q
		INNER JOIN topic AS t ON t.topic_id=q.topic_id
		INNER JOIN classes AS c ON c.subject_id=t.subject_id
		WHERE t.parent_topic_id = $topicId AND  class_id=$classId ";
		
		if ($getCount === TRUE) {
			$questionInfo = $this->db->query($sql)->num_rows();
		} else {
			$questionInfo = $this->db->query($sql)->result();
		}
	
		return $questionInfo;
	}
	
	/**
	 * Get topic question details
	 *
	 * @param integer $classId
	 * @param integer $topicId
	 * @param boolean $getCount
	 * @return array $questionInfo
	 */
	public function getTopicQuestionData($classId, $topicId = NULL, $getCount = FALSE)
	{
		/*$sql = "SELECT DISTINCT question_name, question_id
		FROM questions AS q
		INNER JOIN topic AS t ON t.topic_id=q.topic_id
		INNER JOIN classes AS c ON c.subject_id=t.subject_id
		WHERE class_id=$classId ";*/
		$sql = "SELECT DISTINCT question_name, question_id, qtype.question_type_title
		FROM questions AS q
		INNER JOIN topic AS t ON t.topic_id=q.topic_id
		INNER JOIN classes AS c ON c.subject_id=t.subject_id
		INNER JOIN question_types AS qtype ON qtype.question_type_id = q.question_type_id
		WHERE class_id=$classId";
	
		if (!empty($topicId)) {
			$sql .= " AND t.topic_id = $topicId ";
		}
		
		if ($getCount === TRUE) {
			$questionInfo = $this->db->query($sql)->num_rows();
		} else {
			$questionInfo = $this->db->query($sql)->result();
		}
		//print_r($questionInfo);
		return $questionInfo;
	}

	public function checkQuestionlog($questionId)
	{
		$checkQuestionlog="select question_log_id from question_log where question_id='$questionId'";
		$QuestionlogQueryRes=$this->db->query($checkQuestionlog)->result();
		$logExists=$this->db->query($checkQuestionlog)->num_rows();
		//$QuestionlogQueryNum=new stdClass();
		//$QuestionlogQueryNum->logExists=$logExists;
		return $logExists;
	}
	
	/**
	 * Get question with average data by topic id
	 * @param integer $classId
	 * @param integer $topicId
	 * @return array $questionData
	 */
	public function getQuestionWithAverageDataByTopicId($classId, $topicId)
	{
		$questionData = $this->getTopicQuestionData($classId, $topicId);
		$questionLogModel = $this->getQuestionLogModel();
		
		foreach ($questionData AS $key => $data) {
			$classAverageData =  $questionLogModel->getClassAverageScore($data->question_id);
			$classAverageDataRows =  $questionLogModel->getClassAverageScore($data->question_id, $getNumRows = TRUE);
			$logExistsNum=$this->checkQuestionlog($data->question_id);
			$questionData[$key]->logExists=$logExistsNum;

			$questionData[$key]->averageScoreOfClass = 0;
			$questionData[$key]->classAverageDataRows = $classAverageDataRows;
			if ($classAverageDataRows > 0) {
				$questionData[$key]->averageScoreOfClass = round(($classAverageData->averageScoreOfClass/ $classAverageDataRows) * 100);
			}
		}
	
		return $questionData;
	}
	
	/**
	 * Get question details
	 * @param integer $questionId
	 */
	public function getQuestionDetails($questionId)
	{
		$sql = "SELECT question_name, question_id, question_type_id
		FROM questions AS q
		WHERE q.question_id=$questionId ";
	
		$questionInfo = $this->db->query($sql)->row();
	
		return $questionInfo;
	}

	/**
	 * Get question with average data
	 * @param integer $questionId
	 * @return array $questionData
	 */
	public function getQuestionWithAverageDataByQuestionId($questionId)
	{
		$questionLogModel = $this->getQuestionLogModel();
		$questionData = $this->getQuestionDetails($questionId);
		$questionData->averageScoreOfClass = 0;
		$questionData->classAverageDataRows = 0;
		$questionData->averageScoreOfClass = 0;
		
		if (!empty($questionData)) {
			$classAverageData =  $questionLogModel->getClassAverageScore($questionData->question_id);
			$classAverageDataRows =  $questionLogModel->getClassAverageScore($questionData->question_id, $getNumRows = TRUE);
			
			$questionData->averageScoreOfClass = 0;
			$questionData->classAverageDataRows = $classAverageDataRows;
			if ($classAverageDataRows > 0) {
				$questionData->averageScoreOfClass = round(($classAverageData->averageScoreOfClass/ $classAverageDataRows) * 100);
			}
		}

		return $questionData;
	}
	
	/**
	 * Save question
	 * @param integer $questionTypeId
	 * @param integer $topicId
	 * @param integer $teacherId
	 * @param string $questionName
	 * @throws Exception
	 */
    public function saveQuestion($questionTypeId, $topicId, $teacherId, $questionName)
	{
		if (empty($questionTypeId) || empty($topicId) || empty($teacherId) || empty($questionName)) {
			throw new Exception("While saving question data,  data provided is empty in question model");
		}
		$data = array(
			'question_type_id' => $questionTypeId ,
			'topic_id' => $topicId ,
			'teacher_id' => $teacherId,
			'question_name' => $questionName
		);
		$fp=fopen('saving.txt','w');
		fwrite($fp,print_r($data));
		$this->db->insert('questions', $data);
		
		return $this->db->insert_id();
	}
	
	/**
	 * Insert question with options
	 * @param array $postData
	 * @param integer $teacherId
	 */
	public function insertQuestionWithOption($postData, $teacherId)
	{
		$this->db->trans_start();
		try {
            $questionId = $this->saveQuestion(
                $postData['questionType'],
                $postData['topicId'],
                $teacherId,
                $postData['question']
			);

            $questionOptionModel = $this->getQuestionOptionModel();

            if (($postData['duplicate'] === 'false' || $postData['duplicate'] === false)) {
                $questionOptionModel->insertQuestionOption(
                    $postData['questionType'],
                    $questionId,
                    $postData,
                    $teacherId
                );
            } else {
                $oldQuestionId = $postData['questionId'];
                $questionOptionModel->copyQuestionOptionForDuplicate(
                    $postData['questionType'],
                    $questionId,
                    $oldQuestionId,
                    $teacherId,
                    $postData
                );
            }

            $this->db->trans_complete();

            return $questionId;
        } catch (Exception $ex) {
            $this->db->trans_rollback();
        }


	}

	/**
	 * Update question with options
     * @param integer $questionId
	 * @param array $postData
	 * @param integer $teacherId
     * @return integer $questionId
	 */
	public function updateQuestionWithOption($questionId, $postData, $teacherId)
	{
		$this->db->trans_start();
		$this->question->updateQuestion(
				$questionId,
				$teacherId,
				$postData['question'],
				$postData['questionType']
			);

        //If old scribble is available then do not update same scribble again
        if (!isset($postData['checkScribbleAvailable']) || empty($postData['checkScribbleAvailable'])) {
            $questionOptionModel = $this->getQuestionOptionModel();
            $questionOptionModel->updateQuestionOption($postData['questionType'], $questionId, $postData, $teacherId);
        }
	
		$this->db->trans_complete();
		return $questionId;
	}
	
	/**
	 * Update question
	 * 
	 * @param integer $questionId
	 * @param integer $teacherId
	 * @param string $questionName
	 * @param integer $questionTypeId
	 * @throws Exception
	 */
	public function updateQuestion($questionId, $teacherId, $questionName, $questionTypeId)
	{
		if (empty($questionId) || empty($teacherId) || empty($questionName)) {
			throw new Exception("While updating question data,  data provided is empty in question model");
		}
		$data = array(
			'teacher_id' => $teacherId,
			'question_name' => $questionName,
			'question_type_id' => $questionTypeId
		);
		$where = "question_id = '$questionId'";
		$this->db->update('questions', $data, $where);
	}
	
	/**
	 * Update question scribble
	 * @param integer $questionId
	 * @param integer $scribbleId
	 */
	public function updateQuestionScribble($questionId, $scribbleId)
	{
		$data = array('scribble_id' => $scribbleId);
		$where = "question_id = '$questionId'";
		$this->db->update('questions', $data, $where);
	}
	
	/**
	 * Delete question
	 * @param integer $questionId
	 */
	public function deleteQuestionByQuestionId($questionId)
	{
        $questionScribble = $this->getQuestionScribble($questionId);
        if (!empty($questionScribble) && $questionScribble->scribble_id >0) {
            $uploadedImagesModel = $this->getUploadedImagesModel();
            $uploadedImagesModel->delete($questionScribble->scribble_id);
        }
		$this->db->delete(
            array('question_log','question_options','questions'),
            array('question_id' => $questionId)
        );
	}

	/**
	 * Check question exist or not
	 * @param string $questionName
     * @param integer $questionId
	 * @return boolean
	 */
	public function checkQuestionExist($questionName, $questionId = NULL)
	{
		$classesSql = "SELECT question_id
		FROM questions
		WHERE question_name = '$questionName'";

		if (!empty($questionId)) {
            $classesSql .= " AND question_id <> $questionId";
		}
		
		$rowCount = $this->db->query($classesSql)->num_rows();

		if ($rowCount > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

    /**
     * Assign questions to dummy topic
     * @param integer $topicId
     * @return boolean
     */
    public function assignQuestionToDummyTopic($topicId)
    {
        $data = array('topic_id' => DUMMY_TOPIC_ID);
        $where = "topic_id = '$topicId'";
        $this->db->update('questions', $data, $where);

        return TRUE;
    }
}