<?php
class Student extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get class question log model
	 */
	public function getQuestionLogModel()
	{
		$CI = &get_instance();
		$CI->load->model('question_log');
		return $CI->question_log;
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
	 * Get class student class map model
	 */
	public function getStudentClassMapModel()
	{
	    $CI = &get_instance();
	    $CI->load->model('student_class_map');
	    return $CI->student_class_map;
	}
	
	/**
	 * Get Graph model
	 */
	public function getGraphModel()
	{
		$CI = &get_instance();
		$CI->load->model('graph');
		return $CI->graph;
	}
	
	/**
	 * Get Student query model
	 */
	public function getStudentQueryModel()
	{
		$CI = &get_instance();
		$CI->load->model('student_query');
		return $CI->student_query;
	}

    /**
     * Get question and query count sort
     * @param integer $sessionId
     * @param string $sortBy
     * @return array
     */
	public function getQuestionAndQueryCount($sessionId, $sortBy = NULL)
	{
		$graphModel = $this->getGraphModel();
		$studentQueryModel = $this->getStudentQueryModel();
		$questionLogModel = $this->getQuestionLogModel();
		
		$studentData = $graphModel->getAllStudentIndex($sessionId, null, Graph::STUDENT_LIVE);
        $studentData = $this->sortByIndexKey($studentData, $sortBy);
		//'studentLastName'
        $participationIndexData = $graspIndexData = array();
		foreach ($studentData AS $position => $student) {
            $participationIndexData[] = $student['participationIndex'];
            $graspIndexData[] = $student['graspIndex'];

			$studentData[$position]['queryCount']  = $studentQueryModel->getStudentQueryDataByStudent($student['studentId'], $sessionId, $rowCount = TRUE);
			$studentData[$position]['questionCount']  = $questionLogModel->getQuestionAnswerDataByStudent(
					$student['studentId'],
					$sessionId,
					$topicId = NULL,
					$getCount = TRUE
					);
		}

        $maxParticipationIndex = (count($participationIndexData) > 0 ) ? max($participationIndexData) : 0;
        $maxGraspIndex = (count($graspIndexData) > 0 ) ? max($graspIndexData) : 0;

        return array(
            'studentData' => $studentData,
            'maxParticipationIndex' => $maxParticipationIndex,
            'maxGraspIndex' => $maxGraspIndex
        );
	}

    /**
     * Sort by index key
     * @param array $studentData
     * @param string $sortBy
     * @return array $studentData
     */
    public function sortByIndexKey($studentData, $sortBy= NULL)
    {
        if (!empty($sortBy)) {
            switch ($sortBy) {
                case 'grasp-index':
                    $studentData = $this->sortArray($studentData,  array('graspIndex', 'studentFirstName'), $descendingSort = false);
                    break;
                case 'grasp-index-desc':
                    $studentData = $this->sortArray($studentData,  array('graspIndex', 'studentFirstName'), $descendingSort = true);
                    break;
                case 'first-name-desc':
                    $studentData = $this->sortArray($studentData,  'studentFirstName', $descendingSort = true);
                    break;
                case 'first-name':
                    $studentData = $this->sortArray($studentData,  'studentFirstName', $descendingSort = false);
                    break;
                case 'participation-index-desc':
                    $studentData = $this->sortArray($studentData, array('participationIndex', 'studentFirstName'), $descendingSort = true);
                    break;
                default:
                    //participation-index
                    $studentData = $this->sortArray($studentData, array('participationIndex', 'studentFirstName'), $descendingSort = false);
            }
        }

        return $studentData;
    }
	
	/**
	 * Sort using array
	 * @param array $data
	 * @param array|string $field
     * @param boolean $descendingSort
	 * @return array $data
	 */
	public function sortArray($data, $field, $descendingSort = false)
	{
		if(!is_array($field)) $field = array($field);
        usort($data, function($a, $b) use($field) {
			$retVal = 0;
			foreach($field as $fieldName) {
				if($retVal == 0) $retVal = strnatcmp($a[$fieldName],$b[$fieldName]);
			}
			return $retVal;
		});

        if ($descendingSort === true) {
            krsort($data);
        }
		
		return $data;
	}
}