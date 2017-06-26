<?php
require_once 'SunManagerConst.php';
require_once ('SunXMLManager.php');
class Graph extends CI_Model
{
    const STUDENT_ATTENDED = 'attended';
    const STUDENT_LIVE = 'live';
    const STUDENT_DEFAULT = 'default';
    private $mXMLManager;

	public function __construct()
	{
		$this->mXMLManager = new SunXMLManager();
		parent::__construct();
	}
	
	/**
	 * Get class session model
	 */
	public function getClassSessionModel()
	{
		$CI = &get_instance();
		$CI->load->model('class_session');
		return $CI->class_session;
	}
	
	/**
	 * Get access model
	 */
	public function getAccessModel()
	{
		$CI = &get_instance();
		$CI->load->model('m_access');
		return $CI->m_access;
	}
	
	/**
	 * Get student class map model
	 */
	public function getStudentClassMapModel()
	{
	    $CI = &get_instance();
	    $CI->load->model('student_class_map');
	    return $CI->student_class_map;
	}

    /**
     * Get student case wise student list
     * @param integer $sessionId
     * @param string $studentCase
     * @return mixed $student
     */
    public function getStudentCaseWiseStudent($sessionId, $studentCase = self::STUDENT_DEFAULT)
    {
        $studentClassMap = $this->getStudentClassMapModel();
        switch ($studentCase) {
            case self::STUDENT_LIVE :
                $student = $studentClassMap->getSessionAttendedLiveStudentInfo($sessionId);
                break;
            default :
                $student = $studentClassMap->getSessionAttendedStudentInfo($sessionId);
                break;
        }

        return $student;
    }

	/**
	 * Get all student index.
	 * 
	 * @param integer $sessionId
	 * @param integer $topicId
     * @param string $studentCase
	 * @return array $studentIndexDetails
	 */
	public function getAllStudentIndex($sessionId=null, $topicId=null, $studentCase = self::STUDENT_DEFAULT)
	{
		try
		{
            //$attendedStudent = FALSE, $liveStudent = FALSE
			$studentIndexDetails = array();
			if($topicId == null) {
				if($sessionId != null) {
                    $student = $this->getStudentCaseWiseStudent($sessionId, $studentCase);

					if(count($student) > 0) {
						foreach ($student AS $key => $studentRow)
						{
							$userId = $studentRow->user_id;
							$studentIndexDetails[$key]['studentId'] = $userId;
							$studentIndexDetails[$key]['studentFirstName'] = $studentRow->first_name;
							$studentIndexDetails[$key]['studentLastName'] = $studentRow->last_name;
							$studentIndexDetails[$key]['graspIndex'] = $this->getGraspIndex(null, $sessionId, $userId, null);
							$studentIndexDetails[$key]['participationIndex'] = $this->getParticipationIndex2($sessionId, $userId,null,null);
						}
					}

				} /*else {
					$studentIndexDetails[STATUS] = "Both params cant be null";
				}*/

			} else {
				if($sessionId != null) {
				    //Select student all or attended
                    $student = $this->getStudentCaseWiseStudent($sessionId, $studentCase);
				    
					if (count($student)) {
						foreach ($student AS $key => $studentRow) {
							$userId = $studentRow->user_id;
							$studentIndexDetails[$key]['studentId'] = $userId;
							$studentIndexDetails[$key]['studentFirstName'] = $studentRow->first_name;
							$studentIndexDetails[$key]['studentLastName'] = $studentRow->last_name;
							$studentIndexDetails[$key]['graspIndex'] = $this->getGraspIndex(null, $sessionId, $userId, $topicId);
							$studentIndexDetails[$key]['participationIndex'] = $this->getParticipationIndex2($sessionId, $userId,$topicId,null);
						}
					}
				} else {
					//return grasp for all sessions
					//grasp for all topics for all students fir the session
					$schoolIdSql = "SELECT s.school_id FROM topic t,subjects s WHERE t.subject_id=s.subject_id and t.topic_id='$topicId'";
					$exists_id_get = $this->db->query($schoolIdSql)->row();
					$schoolId = $exists_id_get->school_id;
					
					$studentsSql = "SELECT student_id, user.first_name, user.last_name
					FROM student_class_map scm
					INNER JOIN classes AS c ON c.class_id=scm.class_id
					INNER JOIN topic AS t ON t.subject_id=c.subject_id
					INNER JOIN  tbl_auth AS user ON user.user_id=scm.student_id
					WHERE  t.topic_id='$topicId'
					GROUP BY student_id";
					$count = $this->db->query($studentsSql)->num_rows();
					
					if($count>0) {
						$student = $this->db->query($studentsSql)->result();
						
						foreach ($student AS $key => $studentRow) {
							$userId = $studentRow->student_id;
							$studentIndexDetails[$key]['studentId'] = $userId;
							$studentIndexDetails[$key]['studentFirstName'] = $studentRow->first_name;
							$studentIndexDetails[$key]['studentLastName'] = $studentRow->last_name;
							$studentIndexDetails[$key]['graspIndex'] = $this->getGraspIndex(null, null, $userId, $topicId);
							//$studentIndexDetails[$key]['graspIndex'] = $this->getGraspIndex($schoolId, null, $userId, $topicId);
							$studentIndexSql = "SELECT si.transaction_id, si.auto_id,
							si.subtotal_of_count, sim.weight_value
							FROM student_index si,tbl_auth t,school_index_map sim
							WHERE si.student_id=t.user_id and t.school_id=sim.school_id and sim.index_type=2
							and si.transaction_id=sim.transaction_type and t.school_id='$schoolId' and si.student_id='$userId'";
							$exists_rows = $this->db->query($studentIndexSql)->num_rows();
							
							if ($exists_rows > 0) {
								$sumOfWeight = 0;
								$weightedScore = 0;
								$topicData = $this->db->query($studentIndexSql)->result();
								foreach ($topicData AS $topicKey => $topicRow) {

									$total_count = $topicRow->subtotal_of_count;
									$weight = $topicRow->weight_value;
									$sumOfWeight += ($total_count * $weight);

								}
								$PI = $sumOfWeight;
								$studentIndexDetails[$key]['participationIndex'] = $PI;
							} else {
								$studentIndexDetails[$key]['participationIndex'] = 0;
							}
						}

					}

				}
			}

			return $studentIndexDetails;
		} catch(Exception $e) {
			echo "error" . $e->getMessage();
		}
	}

	/**
	 * Get participation index
	 * 
	 * @param string | integer $sessionId
	 * @param string | integer $studentId
	 * @return number|string
	 */
    public function getParticipationIndex($sessionId=null, $studentId=null)
	{
		try
		{
            $studentParticipationIndexSql = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,sim.weight_value
				FROM student_index AS si
				INNER JOIN tbl_auth AS t ON si.student_id=t.user_id
				INNER JOIN school_index_map AS sim ON t.school_id=sim.school_id AND sim.index_type=2 AND  si.transaction_id=sim.transaction_type
				WHERE  si.class_session_id='$sessionId' ";
			if($studentId != null) {
                $studentParticipationIndexSql .= " AND si.student_id='$studentId'";
			}

			$exists_rows = $this->db->query($studentParticipationIndexSql)->num_rows(); 
			if ($exists_rows > 0) {
				$sumOfWeight = 0;
				$weightedScore = 0;
				$topicData = $this->db->query($studentParticipationIndexSql)->result();
				foreach ($topicData AS $topicKey => $topicRow) {
					
					$total_count = $topicRow->subtotal_of_count;
					$weight = $topicRow->weight_value;
					$sumOfWeight += ($total_count * $weight);
				}
				$PI = $sumOfWeight;
				return $PI;

			} else {
				return 0;
			}
			
		} catch(Exception $e) {
			return "error" . $e->getMessage();
		}
	}
	
	public function getCommonAPImodel()
    {
        $CI = &get_instance();
        $CI->load->model('commonapi');
        return $CI->commonapi;
    }

	public function getParticipationIndex2($sessionId = null, $studentId = null, $topicId = null, $classId = null)
	{
		//$commonapiModel=$this->getCommonAPImodel();
		/*$class_s="select class_id from class_sessions where class_session_id='$sessionId'";
		$class_q=$this->db->query($class_s);
		$class_f=$class_q->row();
		$class_id=$class_f->class_id;
		echo $class_id;*/
		if($sessionId!=null && $studentId!=null)
		{
			if($topicId!=null)
			{
				$index2="select student_pi from stud_topic_time where student_id='$studentId' and topic_id='$topicId' and class_session_id='$sessionId'";
				$index_q2=$this->db->query($index2);
				$index_f2=$index_q2->row();
				if($index_f2)
				{
					$student_pi=$index_f2->student_pi;
				}
				else
				{
					$student_pi=0;
				}
				return $student_pi;
			}
			if($topicId==null)
			{
				$index2="select student_pi from stud_session_time where student_id='$studentId' and class_session_id='$sessionId'";
				$index_q2=$this->db->query($index2);
				$index_f2=$index_q2->row();
				if($index_f2)
				{
					$student_pi=$index_f2->student_pi;
				}
				else
				{
					$student_pi=0;
				}
				return $student_pi;
			}
		}
		if($studentId==null && $sessionId!=null)
		{
			if($topicId==null)
			{
				$index2="select pi from class_sessions where class_session_id='$sessionId'";
				$index_q2=$this->db->query($index2);
				$index_f2=$index_q2->row();
				if($index_f2)
				{
					$sess_pi=$index_f2->pi;
				}
				else
				{
					$sess_pi=0;
				}
				return $sess_pi;
			}
			else
			{
				$index2="SELECT sum(student_pi) as topic_pi from stud_topic_time where class_session_id='$sessionId' and topic_id='$topicId' group by topic_id";
				$index_q2=$this->db->query($index2);
				$index_f2=$index_q2->row();
				if($index_f2)
				{
					$topic_pi=$index_f2->topic_pi;
				}
				else
				{
					$topic_pi=0;
				}
				return $topic_pi;
			}
		}
		if($studentId!=null && $topicId==null && $sessionId==null && $classId!=null)
		{
			$stud_s="select max(pi_class) as pi_class from stud_session_time inner join class_sessions on class_sessions.class_session_id=stud_session_time.class_session_id where student_id='$studentId' and class_id='$classId'";
			$stud_q=$this->db->query($stud_s);
			$stud_f=$stud_q->row();
			if($stud_f)
			{
				$index2=$stud_f->pi_class;
			}
			else
			{
				$index2=0;
			}
			return $index2;
		}
		//$arr[SMC::$PARTICIPATIONINDEX]=$index_v2;
		/*$participationIndex=$commonapiModel->GetIndex(2,$studentId,$class_id,$sessionId,$topicId,null,null);
		$array2 = $this->mXMLManager->parseXML($participationIndex);
		echo $array2[0][SMC::$PARTICIPATIONINDEX];*/
		return 0;
	}

	/**
	 * Get grasp index.
	 * 
	 * @param string | integer $schoolId
	 * @param string | integer $sessionId
	 * @param string | integer $studentId
	 * @param string | integer $topicId
	 * @return number|string
	 */
	public function getGraspIndex($schoolId = null, $sessionId = null, $studentId = null, $topicId = null)
	{
		try {
			$classSession = $this->getClassSessionModel();
			$accessModel = $this->getAccessModel();
			if($studentId == null) {
				if($topicId == null) {
					//case 1
					//grasp for all topics for all students fir the session
					if($schoolId == null && $sessionId !=null) {
						$schoolId = $classSession->getSchoolIdBySessionId($sessionId);
					}
					
					return $this->getGraspIndexBy($schoolId, $sessionId);
				}
				else {
					//case 2
					//return grasp for all students in topic
					//grasp for all topics for all students fir the session
					if($schoolId == null && $sessionId !=null) {
						$schoolId = $this->getStudentId($sessionId);
					}
					
					return $this->getStudentGraspIndex($schoolId, $sessionId, $topicId);
				}

			}
			else {
				if($topicId == null) {
					//case 3
					$schoolId = $accessModel->getSchoolIdByStudentId($studentId);
					return $this->getStudentGraspIndex($schoolId, $sessionId, NULL, $studentId);
				} else {
					//case 4
					return $this->getStudentGraspIndex(NULL, $sessionId, $topicId, $studentId);
				}

			}
		} catch(Exception $e) {
			return "error";
		}
	}
	
	/**
	 * Get student grasp index
	 * 
	 * @param string | integer $schoolId
	 * @param string | integer $sessionId
	 * @param string | integer $topicId
	 * @param string | integer $studentId
	 * @return float | integer
	 */
	public function getStudentGraspIndex($schoolId = NULL, $sessionId = NULL, $topicId = NULL, $studentId = NULL)
	{
		if ($schoolId != NULL ) {
			$studentGraspIndexSql = "SELECT si.transaction_id, si.auto_id, si.subtotal_of_count,
			si.subtotal_of_score,sim.weight_value
			FROM student_index si,school_index_map sim
			WHERE sim.school_id='$schoolId' and sim.index_type=1
			AND si.transaction_id=sim.transaction_type ";
			
		} else {
			$studentGraspIndexSql = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,
			si.subtotal_of_score,sim.weight_value
			FROM student_index si,tbl_auth t,school_index_map sim
			WHERE si.student_id=t.user_id and t.school_id=sim.school_id
			AND sim.index_type=1 and si.transaction_id=sim.transaction_type
			AND si.topic_id = '$topicId' and si.student_id='$studentId' ";
		}
		
		if($studentId != null) {
			$studentGraspIndexSql .= " AND si.student_id='$studentId'";
		}			
		if ($topicId != NULL) {
			 $studentGraspIndexSql .= " AND si.topic_id = '$topicId' ";
		}
		if($sessionId != NULL) {
			$studentGraspIndexSql .= " AND si.class_session_id='$sessionId'";
		}
		
		$exists_rows = $this->db->query($studentGraspIndexSql)->num_rows();
		if ($exists_rows > 0) {
			$sumOfWeight = 0;
            $topicScore = 0;
			
			$schoolIndexData = $this->db->query($studentGraspIndexSql)->result();
			foreach ($schoolIndexData AS $schoolKey => $schoolData) {

				$total_count = $schoolData->subtotal_of_count;
				$total_score = $schoolData->subtotal_of_score;
				$weight = $schoolData->weight_value;
				$topicScore += ($total_score * $weight);
				$sumOfWeight += ($total_count * $weight);

			}
			$GI = ($topicScore/$sumOfWeight)*100;

			return $GI;

		} else {

			return 0;
		}
	}
	
	/**
	 * Get participation index data
	 * @param integer $sessionId
	 * @param integer $topicId
	 * @param boolean $getParticipationIndex
	 * @param boolean $getGraspIndex
	 * @return array $graphIndexData
	 */
	public function getParticipationIndexGraphData($sessionId = NULL, $topicId = NULL, $getParticipationIndex = TRUE, $getGraspIndex = TRUE)
	{
		$studentData = $this->getAllStudentIndex($sessionId, $topicId, self::STUDENT_LIVE);
        $graphIndexData = $this->getGraphDataByStudentList($studentData, $getParticipationIndex, $getGraspIndex);
		return $graphIndexData;
	}

    /**
     * Get graph student list
     *
     * @param array $studentData
     * @param boolean $getParticipationIndex
     * @param boolean $getGraspIndex
     * @return array $graphIndexData
     */
    public function getGraphDataByStudentList($studentData, $getParticipationIndex = TRUE, $getGraspIndex = TRUE)
    {
        $graphIndexData = array(
            'participationIndexData' => array(),
            'graspIndexData' => array(),
            'totalParticipationIndex' => 0,
            'totalGraspIndex' => 0,
        );

        $maxParticipationIndex = 0;
        $minParticipationIndex = 0;

        $maxGraspIndex = 0;
        $minGraspIndex = 0;

        foreach ($studentData AS $student) {
            if ($getParticipationIndex === TRUE) {
                //Calculate Participation graph data
                $participationIndex = $student['participationIndex'];
                $graphIndexData['participationIndexData'][$participationIndex][] = $student;
                $graphIndexData['totalParticipationIndex'] += $student['participationIndex'];
                $maxParticipationIndex = ($student['participationIndex'] > $maxParticipationIndex) ? $student['participationIndex'] : $maxParticipationIndex;
                $minParticipationIndex = ($student['participationIndex'] < $minParticipationIndex) ? $student['participationIndex'] : $minParticipationIndex;
            }

            if ($getGraspIndex === TRUE) {
                //Calculate grasp graph data
                $graspIndex = $student['graspIndex'];
                $graphIndexData['graspIndexData'][$graspIndex][] = $student;
                $graphIndexData['totalGraspIndex'] += $student['graspIndex'];
                $maxGraspIndex = ($student['graspIndex'] > $maxGraspIndex) ? $student['graspIndex'] : $maxGraspIndex;
                $minGraspIndex = ($student['graspIndex'] < $minGraspIndex) ? $student['graspIndex'] : $minGraspIndex;
            }

        }

        $graphIndexData['maxParticipationIndex'] = $maxParticipationIndex;
        $graphIndexData['maxGraspIndex'] = $maxGraspIndex;

        $graphIndexData['minParticipationIndex'] = $minParticipationIndex;
        $graphIndexData['minGraspIndex'] = $minGraspIndex;

        $averageParticipationIndex = 0;
        if ($graphIndexData['totalParticipationIndex'] > 0 ) {
            $averageParticipationIndex = sprintf("%1\$.1f",($graphIndexData['totalParticipationIndex']/count($studentData))) ;
        }

        $averageGraspIndex = 0;
        if ($graphIndexData['totalGraspIndex'] > 0 ) {
            $averageGraspIndex = sprintf("%1\$.1f",($graphIndexData['totalGraspIndex']/count($studentData))) ;
        }
        $graphIndexData['averageParticipationIndex'] = $averageParticipationIndex;
        $graphIndexData['averageGraspIndex'] = $averageGraspIndex;

        return $graphIndexData;
    }
	
	/**
	 * Get calendar details with PI
	 * @param array $timeTableData
	 * @return array $timeTableData
	 */
	public function getCalenderWithPIDetails($timeTableData)
	{
        $minStartTime = array();
        $maxEndTime = array();
		if (!empty($timeTableData) && is_array($timeTableData)) {
			foreach ($timeTableData AS $key => $data) {

                $minStartTime[] = (int)(date('H', strtotime($data->starts_on)));
                $maxEndTime[] =  (int) (date('H', strtotime($data->ends_on)));


				$PI = $this->getParticipationIndex2($data->class_session_id,null,null,null);
				$timeTableData[$key]->averagePI = $PI;

                //Occupied seats
                $studentClassMapModel = $this->getStudentClassMapModel();
                $timeTableData[$key]->occupiedSeats = $studentClassMapModel->getSessionAttendedLiveStudentInfo($data->class_session_id, $getNumRows = TRUE);

                //Registered seats
                $timeTableData[$key]->registeredSeats = $studentClassMapModel->getRegisteredStudentCount($data->class_session_id, $data->ends_on);
			}
		}

        $formatData['minStartTime'] = (!empty($minStartTime)) ? min($minStartTime) : 8;
        $formatData['maxEndTime'] = (!empty($minStartTime)) ?max($maxEndTime) + 2 : 16;

		return array(
            'data' => $timeTableData,
            'formatData' => $formatData
        );
	}
	
	/**
	 * Get lesson grasp index
	 * @param integer $schoolId
	 * @param integer $topicId
     * @return integer $GI
	 */
	public function getLessonGraspIndexData($schoolId, $topicId)
	{
		$studentGraspIndexSql = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,si.subtotal_of_score,sim.weight_value
		FROM student_index si,school_index_map sim WHERE sim.school_id='$schoolId'
		and sim.index_type=1 and si.transaction_id=sim.transaction_type and si.topic_id = $topicId";
		
		
		$exists_rows = $this->db->query($studentGraspIndexSql)->num_rows();
		if ($exists_rows > 0) {
			$sumOfWeight = 0;
            $topicScore = 0;
				
			$schoolIndexData = $this->db->query($studentGraspIndexSql)->result();
			foreach ($schoolIndexData AS $schoolKey => $schoolData) {
			
			$total_count = $schoolData->subtotal_of_count;
			$total_score = $schoolData->subtotal_of_score;
			$weight = $schoolData->weight_value;
            $topicScore += ($total_score * $weight);
			$sumOfWeight += ($total_count * $weight);
			
			}
			$GI = ($topicScore/$sumOfWeight)*100;
		
		} else {

            $GI = 0;
		}

        return $GI;
	}

    /**
     * Get classes graph data by class id
     * @param integer $classId
     * @return array $studentIndexDetails
     */
    public function getClassesGraphDataByClassId($classId)
    {
        $studentClassMapModel = $this->getStudentClassMapModel();
        $students = $studentClassMapModel->getAttendedStudentDetailsByClassId($classId, $getNumRows = FALSE);
        $studentIndexDetails = array();
        foreach ($students AS $key => $studentRow) {
            $userId = $studentRow->user_id;
            $studentIndexDetails[$key]['studentId'] = $userId;
            $studentIndexDetails[$key]['studentFirstName'] = ucfirst($studentRow->first_name);
            $studentIndexDetails[$key]['studentLastName'] = ucfirst($studentRow->last_name);
            $studentIndexDetails[$key]['graspIndex'] = $this->getGraspIndex(null, null, $userId, null);
            $studentIndexDetails[$key]['participationIndex'] = $this->getParticipationIndex2(null, $userId,null,$classId);
        }

        return $studentIndexDetails;
    }
}