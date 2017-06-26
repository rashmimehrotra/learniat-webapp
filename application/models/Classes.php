<?php
class Classes extends CI_Model
{
    public function __construct()
    {
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
     * Get question model
     */
    public function getQuestionModel()
    {
        $CI = &get_instance();
        $CI->load->model('question');
        return $CI->question;
    }

    /**
     * Get graph model
     */
    public function getGraphModel()
    {
        $CI = &get_instance();
        $CI->load->model('graph');
        return $CI->graph;
    }

    /**
     * Get question log model
     */
    public function getQuestionLogModel()
    {
        $CI = &get_instance();
        $CI->load->model('question_log');
        return $CI->question_log;
    }

    /**
     * Get topic log model
     */
    public function getTopicLogModel()
    {
        $CI = &get_instance();
        $CI->load->model('topic_log');
        return $CI->topic_log;
    }

    /**
     * Get lesson plan model
     */
    public function getLessonPlanModel()
    {
        $CI = &get_instance();
        $CI->load->model('lesson_plan');
        return $CI->lesson_plan;
    }

    /**
     * Get class list by teacher id
     * @param integer $teacherId
     * @return array $classesData
     */
    public function getClassListBy($teacherId)
    {
        $classesSql = "SELECT cs.class_session_id, c.class_name, c.class_id,
    	cs.starts_on, cs.ends_on, sub.school_id
    	FROM classes AS c
		INNER JOIN class_sessions AS cs on cs.teacher_id = c.teacher_id
		INNER JOIN tbl_auth AS tb on cs.teacher_id = tb.user_id
		INNER JOIN subjects AS sub on sub.subject_id = c.subject_id
		
		WHERE cs.teacher_id = $teacherId AND cs.class_id = c.class_id AND date(cs.starts_on) <= curdate()
		GROUP BY c.class_id
    	ORDER BY cs.class_session_id DESC";
        $classesData = $this->db->query($classesSql)->result();

        return $classesData;
    }

    /**
     * Get list lesson list
     * @param integer $teacherId
     * @return array $classList
     */
    public function getListLessonDetails($teacherId)
    {
        $questionModel = $this->getQuestionModel();
        $questionLogModel = $this->getQuestionLogModel();
        //classes list
        $classesList = $this->getClassListForLessonPlan($teacherId);
        foreach ($classesList AS $classKey => $classDetails) {

            $classId = $classDetails->class_id;
            //Question count
            $questionCount = $questionModel->getTopicQuestionData($classId, NULL, TRUE);
            $classesList[$classKey]->questionCount = $questionCount;

            //Covered Question count
            $classesList[$classKey]->coveredQuestionCount = $questionLogModel->getTopicQuestionIds(
                $classId,
                NULL,
                NULL,
                $getCount = TRUE
            );

            //progress percentage
            $classesList[$classKey]->progressPercentage = $this->getProgressPercentage(
                $classesList[$classKey]->coveredQuestionCount,
                $classesList[$classKey]->questionCount
            );
        }

        return $classesList;
    }

    /**
     * Get lesson topic details
     * @param integer $classId
     * @param integer $schoolId
     * @return array $parentTopicData
     */
    public function getLessonTopicDetails($classId, $schoolId)
    {
        $classSessionModel = $this->getClassSessionModel();
        $parentTopicData = $classSessionModel->getParentTopicListByTeacherId($classId);

        foreach ($parentTopicData AS $topicKey => $topicDetails) {
            $parentTopicData[$topicKey]->otherDetails = $this->getParentTopicDetailsForLesson($classId, $topicDetails->topic_id);
        }

        return $parentTopicData;
    }

    /**
     * Get Parent topic other details
     * @param integer $classId
     * @param integer $topicId
     * @return stdClass
     */
    public function getParentTopicDetailsForLesson($classId, $topicId)
    {
        $classSessionModel = $this->getClassSessionModel();
        $questionModel = $this->getQuestionModel();
        $graphModel = $this->getGraphModel();
        $questionLogModel = $this->getQuestionLogModel();
        $topicLogModel = $this->getTopicLogModel();
        $lessonPlanModel = $this->getLessonPlanModel();

        $object = new stdClass();
        $object->subTopicCount = $classSessionModel->getSubTopicList($classId, $topicId, TRUE);

        //Question count
        $object->topicQuestionCount = $questionModel->getParentQuestionData($classId, $topicId, TRUE);

        //Covered Question count
        $object->topicCoveredQuestionCount = $questionLogModel->getTopicQuestionIds(
            $classId,
            $topicId,
            NULL,
            $getCount = TRUE
        );

        //Cumulative time
        $object->cumulativeTime = $topicLogModel->getCumulativeTimeForParentTopicId($classId, $topicId);

        //tagged
        $object->topicTagged = $lessonPlanModel->getTagged($classId, $topicId);

        //sub topic tagged list
        $subTopicIds = $lessonPlanModel->getSubTopicTaggedList($classId, $topicId);

        $subTopicTaggedIds = array();
        foreach ($subTopicIds AS $data) {
            $subTopicTaggedIds[] = $data->topic_tagged;
        }

        $count = count(array_unique($subTopicTaggedIds));
        if ($object->subTopicCount > 0 && $object->subTopicCount == count($subTopicIds)) {
            $object->subTopicTagged = ($count === 1) ? FALSE : TRUE;
        } else {
            $object->subTopicTagged = FALSE;
        }

        //progress percentage
        $object->progressPercentage = $this->getProgressPercentage(
            $object->topicCoveredQuestionCount,
            $object->topicQuestionCount
        );

        //GI index dataa
        $object->indexData = $graphModel->getParticipationIndexGraphData(
            NULL,
            $topicId,
            $getParticipationIndex = FALSE,
            $getGraspIndex = TRUE
        );

        return $object;
    }

    /**
     * Get sub topic list
     * @param integer $classId
     * @param integer $parentTopicId
     * @return array
     */
    public function getSubTopicList($classId, $parentTopicId)
    {
        $classSessionModel = $this->getClassSessionModel();
        $subTopicList = $classSessionModel->getSubTopicList($classId, $parentTopicId);

        $lessonPlanModel = $this->getLessonPlanModel();
        $parentTopicCheckBoxFlag = $lessonPlanModel->getTagged($classId, $parentTopicId);

        $subTopicUnmatchedTaggedIds = array();
        foreach ($subTopicList AS $topicKey => $subTopic) {

            $subTopicList[$topicKey]->otherDetails = $this->getTopicDetailsForLesson($classId, $subTopic->topic_id);

            if ($parentTopicCheckBoxFlag != $subTopic->topic_tagged) {
                $subTopicUnmatchedTaggedIds[$subTopic->topic_id] = $subTopic->topic_tagged;
            }
        }

        $unMatchedCount = count($subTopicUnmatchedTaggedIds);
        if ($unMatchedCount > 0 && $unMatchedCount != count($subTopicList) ) {
            $parentTopicCheckBoxFlag = FALSE;
        }

        return array(
            'parentTopicCheckBoxFlag' => $parentTopicCheckBoxFlag,
            'subTopicList' => $subTopicList
        );
    }

    /**
     * Get topic details for lesson
     * @param integer $classId
     * @param integer $topicId
     * @return stdClass
     */
    public function getTopicDetailsForLesson($classId, $topicId)
    {
        $classSessionModel = $this->getClassSessionModel();
        $questionLogModel = $this->getQuestionLogModel();
        $questionModel = $this->getQuestionModel();
        $graphModel = $this->getGraphModel();
        $topicLogModel = $this->getTopicLogModel();
        $lessonPlanModel = $this->getLessonPlanModel();

        $object = new stdClass();

        //Sub topic count
        $object->subTopicCount = $classSessionModel->getSubTopicList($classId, $topicId, TRUE);

        //Question count
        $object->topicQuestionCount = $questionModel->getTopicQuestionData($classId, $topicId, TRUE);

        //Covered Question count
        $object->topicCoveredQuestionCount = $questionLogModel->getTopicQuestionIds(
            $classId,
            NULL,
            $topicId,
            $getCount = TRUE
        );

        //progress percentage
        $object->progressPercentage = $this->getProgressPercentage(
            $object->topicCoveredQuestionCount,
            $object->topicQuestionCount
        );

        //Cumulative time
        $object->cumulativeTime = $topicLogModel->getCumulativeTimeForSubTopicId($classId, $topicId);

        //tagged
        $object->topicTagged = $lessonPlanModel->getTagged($classId, $topicId);

        //GI index data
        $object->indexData = $graphModel->getParticipationIndexGraphData(
            NULL,
            $topicId,
            $getParticipationIndex = FALSE,
            $getGraspIndex = TRUE
        );

        return $object;
    }

    /**
     * Get pogress percent
     * @param float|integer $numerator
     * @param float|integer $denominator
     * @return float $progressPercent
     */
    public function getProgressPercentage($numerator, $denominator)
    {
        if ($denominator > 0) {
            $progressPercent = (($numerator/$denominator) * 100);
        } else {
            $progressPercent = 0;
        }
        $progressPercent = sprintf("%01.1f", $progressPercent);

        return $progressPercent;
    }

    /**
     * Get subject id
     * @param integer $classId
     * @return integer $subjectId
     */
    public function getSubjectId($classId)
    {
        $sql = "SELECT subject_id FROM `classes` where class_id=$classId";
        $classesData =$this->db->query($sql)->row();

        if (!isset($classesData->subject_id)) {
            $subjectId = 0;
        } else {
            $subjectId = $classesData->subject_id;
        }

        return $subjectId;
    }
    /**
     * Get class list for lesson plan
     * @param integer $teacherId
     * @return array $classesData
     */
    public function getClassListForLessonPlan($teacherId)
    {
        $classesSql = "SELECT cs.class_session_id, c.class_name, c.class_id,
    	cs.starts_on, cs.ends_on, sub.school_id
    	FROM classes AS c
    	INNER JOIN class_sessions AS cs on cs.teacher_id = c.teacher_id
    	INNER JOIN subjects AS sub on sub.subject_id = c.subject_id
    	WHERE c.teacher_id = $teacherId
    	GROUP BY c.class_id";
        $classesData = $this->db->query($classesSql)->result();

        return $classesData;
    }

    /**
     * Get live list student
     * @param integer $teacherId
     * @return array $classList
     */
    public function getListClassWithStudentDetails($teacherId)
    {
        //classes list
        $classesList = $this->getClassListForLessonPlan($teacherId);

        return $classesList;
    }
}