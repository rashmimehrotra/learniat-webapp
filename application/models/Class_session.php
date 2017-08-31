<?php

class Class_session extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get class student query
	 */
	public function getStudentQueryModel()
	{
		$CI = &get_instance();
		$CI->load->model('student_query');
		return $CI->student_query;
	}
	
	/**
	 * Get class question log
	 */
	public function getQuestionLogModel()
	{
		$CI = &get_instance();
		$CI->load->model('question_log');
		return $CI->question_log;
	}
	
	/**
	 * Get class student class map
	 */
	public function getStudentClassMapModel()
	{
		$CI = &get_instance();
		$CI->load->model('student_class_map');
		return $CI->student_class_map;
	}

	/**
	 * Get all session id by user id
	 * @param integer $userId
	 * @param string | integer $intervalDays
	 * @param string | integer $classId
	 * @param string | integer $sessionState
	 * @return associated array $result
	 */
	public function getAllSessionIdByUserId($userId, $intervalDays = NULL, $classId = NULL, $sessionState = NULL)
	{
		$where = '';
		if (!empty($sessionState)) {
			$where = " AND term.session_state = $sessionState ";
		}

		if (!empty($intervalDays)) {
			$where = " AND term.ends_on < NOW() AND term.ends_on > (NOW() - INTERVAL $intervalDays DAY) ";
		}

		if (!empty($classId)) {
			$where = " AND term.class_id = $classId";
		}

		$queryText = "select term.class_session_id, term.ends_on, class.class_name
			from class_sessions as term
			inner join tbl_auth as user on term.teacher_id = user.user_id
			inner join classes as class on term.teacher_id = class.teacher_id
			inner join rooms as room on term.room_id = room.room_id
			inner join subjects as subject on subject.subject_id = class.subject_id
			where term.teacher_id = '$userId' AND term.class_id = class.class_id
			$where
			order by term.starts_on desc";

		$query = $this->db->query($queryText);
		$result = $query->result();

		return $result;
	}

	/**
	 * Get session summary details
	 * @param integer $sessionId
	 * @param string $sessionDate
	 * @return associated array $result
	 */
	public function getSessionSummaryDetails($sessionId, $sessionDate)
	{
		$classInfoSql = "SELECT cs.class_id, cs.starts_on, cs.ends_on,
		cs.session_state, rm.room_name, class.class_name
		FROM class_sessions AS cs
		INNER JOIN classes as class on cs.class_id = class.class_id
		INNER JOIN rooms as rm on rm.room_id = cs.room_id
		WHERE cs.class_session_id = '$sessionId' AND cs.ends_on >= '$sessionDate'";
		$classInfoDetails = $this->db->query($classInfoSql)->row();

		$sessionSummaryDetails = array();
		if (!empty($classInfoDetails)) {
		    $classId = $classInfoDetails->class_id;

    		$preAllocatedSeatsSql = "SELECT count(map.student_id) AS totalCount
                FROM student_class_map AS map
                INNER JOIN class_sessions AS
                session ON map.class_id = session.class_id
                WHERE session.class_session_id =  $sessionId";

    		$studentClassMapModel = $this->getStudentClassMapModel();
    		$occupiedSeats = $studentClassMapModel->getSessionAttendedLiveStudentInfo($sessionId, $getNumRows = TRUE);
    		$preAllocatedSeats = $this->db->query($preAllocatedSeatsSql)->row()->totalCount;

    		//Registered seats
            $registeredSeats = $studentClassMapModel->getRegisteredStudentCount($sessionId, $sessionDate);

    		$topicsTaggedSql = "SELECT plan.topic_id FROM lesson_plan as plan
    		INNER JOIN topic on plan.topic_id = topic.topic_id and topic.parent_topic_id is not null
    		WHERE plan.class_id = '$classId' and plan.topic_tagged = '1'";

    		$taggedCount = $this->db->query($topicsTaggedSql)->num_rows();
    		$resultTopics = $this->db->query($topicsTaggedSql)->result();

    		$topic = array();
    		foreach ($resultTopics AS $topics) {
    			$topic[] = $topics->topic_id;
    		}
    		$topic_list = implode(",",$topic);
    		$question_count = 0;

    		if ($topic_list != null) {
    			$questions = "select question_id from questions where topic_id in ($topic_list)";
    			$questions_configured = $this->db->query($questions);
    			$question_count = $questions_configured->num_rows();
    		}

    		//covered topics
    		$topicSql = "SELECT DISTINCT topic_id
            	FROM topic_log,class_sessions
            	WHERE (state=22 or state=23 or state=24)
            		AND topic_log.class_id=class_sessions.class_id
            		AND class_sessions.class_session_id='$sessionId' ";
    		$topicCoveredPastSql = $topicSql . " AND (topic_log.transition_time < class_sessions.starts_on)";
    		$topicCoveredSql = $topicSql . " AND (topic_log.transition_time > class_sessions.starts_on)
		              AND (topic_log.transition_time > class_sessions.ends_on)";

    		$toBeCoveredSql = "SELECT count(topic_id)  AS totalCount FROM lesson_plan,class_sessions
            	WHERE topic_tagged=1 and lesson_plan.class_id = class_sessions.class_id
            	AND class_session_id = '$sessionId'";

    		$topicCoveredBefore = $this->db->query($topicCoveredPastSql)->num_rows();
    		$topicCovered = $this->db->query($topicCoveredSql)->num_rows();
    		$toBeCovered = $this->db->query($toBeCoveredSql)->row()->totalCount;
    		$toBeCovered -= ($topicCoveredBefore + $topicCovered);
    		$sessionSummaryDetails["total_students"] = $registeredSeats;
    		$sessionSummaryDetails["students_present"] = $occupiedSeats;
    		$sessionSummaryDetails["topics_count"] = $taggedCount;
    		$sessionSummaryDetails["question_count"] = $question_count;
    		$sessionSummaryDetails["start_time"] = $this->getTimeFormat($classInfoDetails->starts_on);
    		$sessionSummaryDetails["end_time"] = $this->getTimeFormat($classInfoDetails->ends_on);
    		//$sessionSummaryDetails["end_time"] = $this->getTimeFormat($this->ReturnTimeOffset($classInfoDetails->ends_on));
    		$sessionSummaryDetails["room_name"] = $classInfoDetails->room_name;
    		$sessionSummaryDetails["session_state"] = $classInfoDetails->session_state;
    		$sessionSummaryDetails["class_id"] = $classId;
    		$sessionSummaryDetails["class_name"] = $classInfoDetails->class_name;
    		$sessionSummaryDetails["topicCoveredBefore"] = $topicCoveredBefore;
    		$sessionSummaryDetails["topicCovered"] = $topicCovered;
    		$sessionSummaryDetails["toBeCovered"] = abs($toBeCovered);
    		$sessionSummaryDetails["sessionId"] = $sessionId;
    		$sessionSummaryDetails["sessionDate"] = $sessionDate;
		}

		return $sessionSummaryDetails;
	}

	/**
	 * Get time in AM or PM
	 * @param string $dateTime
	 * @return string $formattedDate
	 */
	public function getTimeFormat($dateTime)
	{
		$formattedDate = date("H:i A", strtotime($dateTime));
		return $formattedDate;
	}

	//TODO : NOT USED ANY WHERE
	public function ReturnTimeOffset($startTime=null, $schoolId = 2)
	{
		$get_time = "SELECT TIME_TO_SEC(timezone) as time FROM schools where school_id=2 or school_id=3";
		$time = $this->db->query($get_time)->row();
		$offset = $time->time;
		$phpFormattedDate = strtotime($startTime);
		$phpFormattedDate = $phpFormattedDate + $offset;
		$formattedDate = strftime("%Y-%m-%d %H:%M:%S", $phpFormattedDate) ;

		return $formattedDate;
	}

	/**
	 * Get school id by session id
	 *
	 * @param integer $sessionId
	 * @return integer $topic_id_get->school_id
	 */
	public function getSchoolIdBySessionId($sessionId)
	{
		$sql = "SELECT t.school_id
		FROM class_sessions s,tbl_auth t
		WHERE t.user_id=s.teacher_id and s.class_session_id='$sessionId'";
    	$topic_id_get = $this->db->query($sql)->row();

    	return $topic_id_get->school_id;
	}

	/**
	 * Get classes list
	 *
	 * @param integer $userId
	 * @return array
	 */
	public function getClassesData($userId)
	{
		$classesSql = "SELECT DISTINCT cs.class_id,c.class_name
		FROM class_sessions cs, classes c
		WHERE cs.class_id=c.class_id and cs.teacher_id='$userId'";
		$classesData = $this->db->query($classesSql)->result();

		return $classesData;
	}

	/**
	 * Get topic list by session id
	 *
	 * @param integer $sessionId
	 * @return array $classesData
	 */
	public function getTopicList($sessionId)
	{
	    $classesSql = "SELECT DISTINCT t.topic_id, t.topic_name, cs.class_session_id,
	    t1.topic_id AS parent_topic_id, t1.topic_name AS parent_topic_name
        FROM class_sessions AS cs
        INNER JOIN lesson_plan AS lp ON lp.class_id=cs.class_id
        INNER JOIN topic AS t ON t.topic_id=lp.topic_id AND t.parent_topic_id IS NOT NULL
        LEFT JOIN topic AS t1 ON t1.topic_id=t.parent_topic_id
        INNER JOIN student_index AS si ON si.topic_id=t.topic_id AND si.class_session_id='$sessionId'
        WHERE cs.class_session_id = '$sessionId'";
	    $classesData = $this->db->query($classesSql)->result();

	    return $classesData;
	}
	
	/**
	 * Get topic data with count details
	 * @param integer $sessionId
	 * @return array $topicList
	 */
	public function getTopicListWithQuestionAndQueryCount($sessionId)
	{
		$topicList = $this->getTopicList($sessionId);
		$studentQueryModel = $this->getStudentQueryModel();
		$questionLogModel = $this->getQuestionLogModel();
		
		foreach ($topicList AS $key => $topicData) {
			$studentQueryCount = $studentQueryModel->getCountStudentQueryByTopic($topicData->topic_id, $sessionId);
			$topicList[$key]->topic_query_count = $studentQueryCount;
			
			$studentQuestionCount = $questionLogModel->getQuestionData($sessionId, $topicData->topic_id, $getCount =TRUE);
			$topicList[$key]->topic_question_count = $studentQuestionCount;
		}
		
		return $topicList;
	}
	
	/**
	 * Get time table details 
	 * @param integer $week
	 * @param integer $year
	 * @param integer $teacherId
	 */
	public function getTimeTableDetails($week, $year, $teacherId)
	{
		$dto = new DateTime();
		$dto->setISODate($year, $week);
		$fromDate = $dto->format('Y-m-d');
		$dto->modify('+' . CALENDER_DAYS . ' days');
		$toDate = $dto->format('Y-m-d');
		
		$classesSql = "SELECT sub.subject_name, class.class_name, term.class_session_id, term.starts_on,
		 term.ends_on, term.teacher_id, term.class_id, class.subject_id, room.room_name,
		 room.room_id, term.session_state 
		FROM class_sessions as term 
		INNER JOIN classes as class on term.class_id=class.class_id
		INNER JOIN rooms as room on term.room_id=room.room_id
		INNER JOIN subjects as sub on class.subject_id=sub.subject_id
		WHERE term.starts_on between '$fromDate 00:00:00' and '$toDate 23:59:59' and term.teacher_id='$teacherId'";
		
		$classesData = $this->db->query($classesSql)->result();
		
		return $classesData;
	}
	
	/**
	 * Get Parent topic list by class id
	 *
	 * @param integer $classId
	 * @param boolean $rowCount
	 * @return array $classesData
	 */
	public function getParentTopicListByTeacherId($classId, $rowCount = FALSE)
	{
		$classesSql = "SELECT t.topic_id, t.topic_name, t.topic_info, TIME(term.starts_on), TIME(term.ends_on)
		FROM topic AS t
		INNER JOIN classes AS c ON c.subject_id=t.subject_id 
		INNER JOIN class_sessions AS term ON term.teacher_id=c.teacher_id 
		WHERE t.parent_topic_id IS NULL AND c.class_id=$classId ";
		$classesSql .= " GROUP BY t.topic_id";
		
		if ($rowCount) {
			$classesData = $this->db->query($classesSql)->num_rows();
		} else {
			$classesData = $this->db->query($classesSql)->result();
		}
	
		return $classesData;
	}
	
	/**
	 * Get sub topic list by topic id
	 *
	 * @param integer $classId
	 * @param integer $topicId
	 * @return array|integer $classesData
	 */
	public function getSubTopicList($classId, $topicId, $rowCount = FALSE)
	{
		$classesSql = "SELECT DISTINCT t.topic_id, t.topic_name, lp.topic_tagged
		FROM topic AS t
		INNER JOIN classes AS c ON c.subject_id=t.subject_id AND class_id=$classId
		LEFT JOIN lesson_plan AS lp ON lp.class_id = c.class_id AND lp.topic_id = t.topic_id
		WHERE t.parent_topic_id=$topicId ";		
		
		if ($rowCount) {
			$classesData = $this->db->query($classesSql)->num_rows();
		} else {
			$classesData = $this->db->query($classesSql)->result();
		}
	
		return $classesData;
	}
	
	/**
	 * Get sub topic ids by topic id
	 *
	 * @param integer $classId
	 * @param integer $topicId
	 * @return array|integer $classesData
	 */
	public function getSubTopicIdsList($classId, $topicId)
	{
		$classesSql = "SELECT DISTINCT t.topic_id
		FROM topic AS t
		INNER JOIN classes AS c ON c.subject_id=t.subject_id AND class_id=$classId
		WHERE t.parent_topic_id=$topicId ";
	
		$classesData = $this->db->query($classesSql)->result();
	
		return $classesData;
	}
}
