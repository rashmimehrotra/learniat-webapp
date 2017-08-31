<?php
class Question_log extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get class student class map model
	 */
	public function getStudentClassMapModel()
	{
		$CI = &get_instance();
		$CI->load->model('student_class_map');
		return $CI->student_class_map;
	}
	
	/**
	 * Get question details 
	 *
	 * @param integer $sessionId
	 * @param integer $topicId
	 * @param boolean $getCount
	 * @return array $studentQueryInfo
	 */
	public function getQuestionData($sessionId, $topicId = NULL, $getCount = FALSE)
	{
		$sql = "SELECT q.question_name,ql.question_id
			FROM question_log AS ql
			INNER JOIN questions AS q ON q.question_id = ql.question_id
			WHERE ql.class_session_id=$sessionId ";
		
		if (!empty($topicId)) {
			$sql .= " AND q.topic_id = $topicId  ";
		}
		
		$sql .= "GROUP BY ql.question_id";
		
		if ($getCount === TRUE) {
			$questionInfo = $this->db->query($sql)->num_rows();
		} else {
			$questionInfo = $this->db->query($sql)->result();
		}
		
		return $questionInfo;
	}
	
	/**
	 * Get question with average data
	 * @param integer $sessionId
	 * @param integer $topicId
	 * @return array $questionData
	 */
	public function getQuestionWithAverageData($sessionId, $topicId = NULL)
	{
		$questionData = $this->getQuestionData($sessionId, $topicId, $getCount = FALSE);
		
		foreach ($questionData AS $key => $data) {
			$classAverageData =  $this->getClassAverageScore($data->question_id);
			$classAverageDataRows =  $this->getClassAverageScore($data->question_id, $getNumRows = TRUE);

			$questionData[$key]->averageScoreOfClass = 0;
			$questionData[$key]->classAverageDataRows = $classAverageDataRows;
			if ($classAverageDataRows > 0) {
				$questionData[$key]->averageScoreOfClass = round(($classAverageData->averageScoreOfClass/ $classAverageDataRows) * 100);
			}
		}
		
		return $questionData;
	}
	
	/**
	 * Get question and answer details by student
	 *
	 * @param integer $studentId
	 * @param integer $sessionId
	 * @param integer $topicId
	 * @param boolean $getCount
	 * @return array $questionInfo
	 */
	public function getQuestionAnswerDataByStudent($studentId, $sessionId = NULL, $topicId = NULL, $getCount = FALSE)
	{
		$sql = "SELECT q.question_name, ql.question_id, a.answer_score, (a.answer_score * 100) AS answer_score_percent
		FROM question_log AS ql
		INNER JOIN questions AS q ON q.question_id = ql.question_id
		LEFT JOIN assessment_answers AS a ON a.question_log_id = ql.question_log_id  AND a.student_id = $studentId
		WHERE 1 ";
	
		if (!empty($sessionId)) {
			$sql .= " AND ql.class_session_id = $sessionId  ";
		}
		if (!empty($topicId)) {
			$sql .= " AND q.topic_id = $topicId  ";
		}
		$sql .= " GROUP BY ql.question_id";
		
		if ($getCount === TRUE) {
			$questionInfo = $this->db->query($sql)->num_rows();
		} else {
			$questionInfo = $this->db->query($sql)->result();
		}
	
		return $questionInfo;
	}
	
	
	/**
	 * Get count of answer on question
	 * @param integer $questionId
	 * @param integer $sessionId
	 * @param boolean $getCount
	 * @return integer $rows
	 */
	public function getCountAnswerScoreQuestion($questionId, $sessionId, $getCount = FALSE)
	{
		$sql = "SELECT answer_score FROM assessment_answers WHERE question_log_id in
				(SELECT question_log_id FROM question_log
					WHERE question_id='$questionId' AND class_session_id = $sessionId) ";
		
		if ($getCount === TRUE) {
			$questionInfo = $this->db->query($sql)->num_rows();
		} else {
			$questionInfo = $this->db->query($sql)->result();
		}
	
		return $questionInfo;
	}
	
	/**
	 * Get count of answer on question
	 * @param integer $questionId
	 * @return array $row
	 */
	public function getClassAverageScore($questionId, $getCount = FALSE)
	{

		$sql = "SELECT a.answer_score
            FROM student_class_map as map
            INNER JOIN tbl_auth AS user ON user.user_id = map.student_id
            INNER JOIN class_sessions as session on map.class_id = session.class_id
            INNER JOIN question_log as ql on ql.class_session_id = session.class_session_id
            INNER JOIN questions AS q ON q.question_id = ql.question_id
            INNER JOIN assessment_answers AS a ON a.question_log_id = ql.question_log_id AND a.student_id = map.student_id
            WHERE q.question_id = $questionId
            GROUP BY map.student_id";

		if ($getCount === TRUE) {
			$questionInfo = $this->db->query($sql)->num_rows();
		} else {
			$questionInfo = $this->db->query($sql)->result();

            $averageScoreOfClass = 0;
            if (!empty($questionInfo)) {
                foreach ($questionInfo AS $question) {
                    $averageScoreOfClass += $question->answer_score;
                }
            }
            $questionInfo = new stdClass();
            $questionInfo->averageScoreOfClass = $averageScoreOfClass;
		}
	
		return $questionInfo;
	}
	
	/**
	 * Get question list with class average scores
	 * @param integer $studentId
	 * @param integer $sessionId
	 * @return array $questionInfo
	 */
	public function getQuestionListWithClassAverageScore($studentId, $sessionId)
	{
		$questionInfo = $this->getQuestionAnswerDataByStudent($studentId, $sessionId);
		
		$studentClassMapModel = $this->getStudentClassMapModel();
		$studentAttended = $studentClassMapModel->getSessionAttendedStudentInfo($sessionId, $getNumRows = TRUE);
		
		foreach ($questionInfo AS $key => $question) {
			$questionInfo[$key]->numberOfResponse = $this->getCountAnswerScoreQuestion($question->question_id, $sessionId, $getCount = TRUE);
			$questionInfo[$key]->numberOfAttended= $studentAttended;
			$classAverageData =  $this->getClassAverageScore($question->question_id);
			$classAverageDataRows =  $this->getClassAverageScore($question->question_id, $getNumRows = TRUE);
			$questionInfo[$key]->averageScoreOfClass = 0;
			if ($classAverageDataRows > 0) {
				$questionInfo[$key]->averageScoreOfClass = round(($classAverageData->averageScoreOfClass/ $classAverageDataRows) * 100);
			}
			
		}
		
		return $questionInfo;
	}
	
	/**
	 * Get question answer data
	 * @param integer $questionId
	 * @param integer $studentId
	 * @param integer $sessionId
	 * @return array $questionInfo
	 */
	public function getQuestionAnswerData($questionId, $studentId, $sessionId = NULL)
	{
		$sql = "SELECT q.question_name, ql.question_id, a.answer_score, (a.answer_score * 100) AS answer_score_percent,
		a.assessment_answer_id, a.student_id, ql.question_log_id
		FROM question_log AS ql
		INNER JOIN questions AS q ON q.question_id = ql.question_id
		INNER JOIN assessment_answers AS a ON a.question_log_id = ql.question_log_id
		WHERE ql.question_id = $questionId  AND a.student_id = $studentId";
		
		if (!empty($sessionId)) {
			$sql .= " AND ql.class_session_id=$sessionId";
		}

		$questionInfo = $this->db->query($sql)->row();
	
		return $questionInfo;
	}
	
	/**
	 * Get topic question
	 * @param integer $classId
	 * @param integer $parentTopicId
	 * @param integer $topicId
	 * @param boolean $getCount
	 */
	public function getTopicQuestionIds($classId, $parentTopicId = NULL, $topicId = NULL, $getCount = FALSE)
	{
		$sql = "SELECT DISTINCT q.question_id 
		FROM questions AS q
		INNER JOIN question_log AS ql ON  ql.question_id=q.question_id
		INNER JOIN topic AS t ON t.topic_id=q.topic_id
		INNER JOIN classes AS c ON c.subject_id=t.subject_id
		WHERE c.class_id=$classId ";
		
		if (!empty($parentTopicId)) {
			$sql .= "  AND t.parent_topic_id=$parentTopicId";
		}
		
		if (!empty($topicId)) {
			$sql .= "  AND t.topic_id=$topicId";
		}
		
		if ($getCount) {
			$questionInfo = $this->db->query($sql)->num_rows();
		} else {
			$questionInfo = $this->db->query($sql)->result();
		}
		
		return $questionInfo;
	}
}