<?php
class Student_class_map extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Get session attended student auth info
     *
     * @param integer $sessionId
     * @param boolean $getNumRows
     * @return array $studentInfo
     */
    public function getSessionAttendedStudentInfo($sessionId, $getNumRows = FALSE)
    {
        $info = "SELECT user.first_name, user.last_name, user.user_id, states.state_description AS user_state
        FROM student_class_map AS map
        INNER JOIN tbl_auth AS user ON user.user_id = map.student_id
        INNER JOIN entity_states AS states ON user.user_state = states.state_id
        INNER JOIN class_sessions AS session ON map.class_id = session.class_id
        WHERE session.class_session_id = $sessionId
        GROUP BY user.user_id
        ORDER BY user.first_name";

        if ($getNumRows === FALSE) {
        	$studentInfo = $this->db->query($info)->result();
        } else {
        	$studentInfo = $this->db->query($info)->num_rows();
        }
        
        return $studentInfo;
    }


    /**
     * Get session attended live student auth info
     *
     * @param integer $sessionId
     * @param boolean $getNumRows
     * @return array $studentInfo
     */
    public function getSessionAttendedLiveStudentInfo($sessionId, $getNumRows = FALSE)
    {
        $info = "SELECT user.first_name, user.last_name, user.user_id, states.state_description AS user_state
        FROM student_class_map AS map
        INNER JOIN state_transitions AS st ON st.entity_id = map.student_id

        INNER JOIN tbl_auth AS user ON user.user_id = map.student_id
        INNER JOIN entity_states AS states ON user.user_state = states.state_id
        INNER JOIN class_sessions AS session ON map.class_id = session.class_id

        WHERE session.class_session_id = $sessionId AND from_state=1 and to_state=7
        GROUP BY user.user_id
        ORDER BY user.first_name";

        if ($getNumRows === FALSE) {
            $studentInfo = $this->db->query($info)->result();
        } else {
            $studentInfo = $this->db->query($info)->num_rows();
        }

        return $studentInfo;
    }

    /**
     * Get registered student count
     * @param integer $sessionId
     * @param string $endOnDate
     * @return integer $registeredSeats
     */
    public function getRegisteredStudentCount($sessionId, $endOnDate)
    {
        $studentsRegisteredSql = "SELECT map.student_id
            FROM student_class_map as map
    		INNER JOIN class_sessions as session on map.class_id = session.class_id
    		WHERE session.class_session_id = '$sessionId' AND session.ends_on >= '$endOnDate'";
        $registeredSeats = $this->db->query($studentsRegisteredSql)->num_rows();

        return $registeredSeats;
    }

    /**
     * Get student score info by question id
     * @param integer $questionId
     * @param integer $sessionId
     * @param boolean $getNumRows
     * @return mixed
     */
    public function getStudentScoreInfoByQuestionId($questionId, $sessionId, $getNumRows = FALSE)
    {
        $info = "SELECT map.student_id, user.first_name, user.last_name, q.question_name,
        ql.question_id, a.answer_score, (a.answer_score * 100) AS answer_score_percent
        FROM student_class_map as map
        INNER JOIN tbl_auth AS user ON user.user_id = map.student_id
        INNER JOIN class_sessions as session on map.class_id = session.class_id
        INNER JOIN question_log as ql on ql.class_session_id = session.class_session_id
        INNER JOIN questions AS q ON q.question_id = ql.question_id
        INNER JOIN assessment_answers AS a ON a.question_log_id = ql.question_log_id AND a.student_id = map.student_id
        WHERE q.question_id = $questionId AND session.class_session_id = $sessionId
        GROUP BY map.student_id
        ORDER BY user.first_name";

        if ($getNumRows === FALSE) {
            $studentInfo = $this->db->query($info)->result();
        } else {
            $studentInfo = $this->db->query($info)->num_rows();
        }

        return $studentInfo;
    }

    /**
     * Get session attended student auth info
     *
     * @param integer $classId
     * @param boolean $getNumRows
     * @return array $studentInfo
     */
    public function getAttendedStudentDetailsByClassId($classId, $getNumRows = FALSE)
    {

        $info = "SELECT user.first_name, user.last_name, user.user_id, states.state_description AS user_state
        FROM student_class_map AS map
        INNER JOIN tbl_auth AS user ON user.user_id = map.student_id
        INNER JOIN entity_states AS states ON user.user_state = states.state_id
        INNER JOIN class_sessions AS session ON map.class_id = session.class_id
        WHERE map.class_id = $classId
        GROUP BY user.user_id
        ORDER BY user.first_name";

        if ($getNumRows === FALSE) {
            $studentInfo = $this->db->query($info)->result();
        } else {
            $studentInfo = $this->db->query($info)->num_rows();
        }

        return $studentInfo;
    }
}