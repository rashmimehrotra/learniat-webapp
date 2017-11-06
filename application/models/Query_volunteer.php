<?php
class Query_volunteer extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get volunteer not selected query details
	 * @param integer $studentId
     * @param integer $sessionId
	 * @return array $queryInfo
	 */
	public function getVolunteerNotSelectedQueryByStudentId($studentId, $sessionId)
	{
		$sql = "SELECT qv.*, sq.query_text, t.topic_name, tp.topic_name AS parent_topic_name
		FROM query_volunteer AS qv
		INNER JOIN student_query AS sq ON qv.query_id = sq.query_id
		INNER JOIN topic AS t ON t.topic_id = sq.topic_id
    	INNER JOIN topic AS tp ON t.parent_topic_id = tp.topic_id
		WHERE (qv.state=6 or qv.state=24) AND sq.class_session_id=$sessionId
			AND qv.student_id = $studentId";

		$queryInfo = $this->db->query($sql)->result();
		
		return $queryInfo;
	}
	
	/**
	 * Get volunteer and selected query details
	 * @param integer $studentId
     * @param integer $sessionId
	 * @return array $queryInfo
	 */
	public function getVolunteerSelectedQueryByStudentId($studentId, $sessionId)
	{
		$sql = "SELECT sq.class_session_id, qv.volunteer_id, qv.query_id, qv.teacher_score AS Rating,
             qv.start_time, qv.close_time, qv.badge_id AS BadgeId, qv.state,
             sq.query_text, t.topic_name, tp.topic_name AS parent_topic_name,
             (SELECT sum(thumbs_up) FROM query_volunteer WHERE query_id=qv.query_id) AS thumbs_up,
             (SELECT sum(thumbs_down) FROM query_volunteer WHERE query_id=qv.query_id) AS thumbs_down
        FROM query_volunteer AS qv
        INNER JOIN student_query AS sq ON qv.query_id = sq.query_id
        INNER JOIN topic AS t ON t.topic_id = sq.topic_id
        INNER JOIN topic AS tp ON t.parent_topic_id = tp.topic_id
        WHERE qv.start_time is NOT NULL AND qv.close_time is NOT NULL
            AND qv.student_id = $studentId AND sq.class_session_id = $sessionId
        ORDER BY qv.close_time";

		$queryInfo = $this->db->query($sql)->result();
	
		return $queryInfo;
	}

	/**
	 * Get volunteer list
	 * @param integer $queryId
	 * @return array $queryInfo
	 */
	public function getVolunteerListByQueryId($queryId)
	{
		$sql = "SELECT student_id
				FROM query_volunteer
				WHERE (state=6 or state=24) AND query_id = $queryId";
		
		$queryInfo = $this->db->query($sql)->result();
		
		return $queryInfo;
	}

	/**
	 * Get answered list
	 * @param integer $queryId
	 * @return array $queryInfo
	 */
	public function getAnsweredList($queryId)
	{
		$sql = "SELECT qv.student_id, qv.thumbs_up, qv.thumbs_down,
					qv.teacher_score AS Rating, qv.badge_id AS BadgeId,
					user.first_name
				FROM query_volunteer AS qv
				INNER JOIN tbl_auth AS user ON user.user_id = qv.student_id
				WHERE (state=24)  AND query_id = $queryId";
		$queryInfo = $this->db->query($sql)->result();
		
		return $queryInfo;
	}
}