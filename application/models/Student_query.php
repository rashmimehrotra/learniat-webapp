<?php
class Student_query extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
    
    /**
     * Get student query details by topic id
     * 
     * @param integer $topicId
     * @param integer $sessionId
     * @return array $studentQueryInfo
     */
    public function getStudentQueryDataByTopic($topicId, $sessionId)
    {
        $sql = "SELECT sq.*, user.first_name, user.last_name,user.user_id,
        IF (qv.volunteer > 0, qv.volunteer, 0) AS volunteer,
        IF (qv1.answered > 0, qv1.answered, 0) AS answered,
        (SELECT count(*) 
            FROM query_volunteer
            WHERE thumbs_up = null AND thumbs_down = null
				AND query_id = sq.query_id) AS dismissedCount
        
        FROM student_query AS sq
		INNER JOIN tbl_auth AS user ON user.user_id = sq.student_id
		LEFT JOIN (SELECT count(student_id) AS volunteer, query_id
                    FROM query_volunteer
                     WHERE (state=6 or state=24) GROUP BY query_id
                  ) AS qv ON qv.query_id = sq.query_id
        LEFT JOIN (SELECT count(student_id) AS answered, query_id
                    FROM query_volunteer
                     WHERE (state=24) GROUP BY query_id
                  ) AS qv1 ON qv1.query_id = sq.query_id
        WHERE sq.topic_id = $topicId AND sq.class_session_id=$sessionId";
        $studentQueryInfo = $this->db->query($sql)->result();
        
        return $studentQueryInfo;
    }

    /**
     * Get count student query by topic
     * @param integer $topicId
     * @param integer $sessionId
     * @return integer $queryCount->countQuery
     */
    public function getCountStudentQueryByTopic($topicId, $sessionId)
    {
    	$sql = "SELECT count(*) AS countQuery
    	FROM student_query AS sq    	
    	WHERE sq.topic_id = $topicId AND sq.class_session_id = $sessionId";
    	$queryCount = $this->db->query($sql)->row();
    
    	if (empty($queryCount)) {
    		return 0;
    	}
    	return $queryCount->countQuery;
    }
    
    /**
     * Get student query details by student
     *
     * @param integer $studentId
     * @param integer $sessionId
     * @param boolean $rowCount
     * @return array $studentQueryInfo
     */
    public function getStudentQueryDataByStudent($studentId, $sessionId, $rowCount = FALSE)
    {
    	$sql = "SELECT sq.*, t.topic_name, tp.topic_name AS parent_topic_name,
    	(SELECT count(volunteer_id) FROM query_volunteer WHERE query_id=sq.query_id) AS volunteer,
    	(SELECT count(votes_received) FROM student_query WHERE query_id=sq.query_id) AS meToo
    	FROM student_query AS sq
    	INNER JOIN topic AS t ON t.topic_id = sq.topic_id
    	INNER JOIN topic AS tp ON t.parent_topic_id = tp.topic_id
    	WHERE student_id = $studentId AND class_session_id=$sessionId";

    	if ($rowCount === TRUE) {
    		$studentQueryInfo = $this->db->query($sql)->num_rows();
    	} else {
    		$studentQueryInfo = $this->db->query($sql)->result();
    	}
    
    	return $studentQueryInfo;
    }
}