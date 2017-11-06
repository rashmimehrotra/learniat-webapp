<?php
class Topic extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
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
     * Get question model
     */
    public function getQuestionModel()
    {
        $CI = &get_instance();
        $CI->load->model('question');
        return $CI->question;
    }
    
    /**
     * Get all index by topic ids
     * @param integer $sessionId,
     * @param array $topicData
     * @return array $allIndexes
     */
    public function getAllIndexByTopicIds($sessionId, $topicData)
    {
        $graphModel = $this->getGraphModel();
        $allIndexes = array();

        foreach ($topicData AS $data) {
            $topicId = $data->topic_id;
            $allIndexes[$topicId] = $graphModel->getParticipationIndexGraphData($sessionId, $topicId);
        }
         
        return $allIndexes;
    }
        
    /**
     * Get Parent topic by topic id
     *
     * @param integer $topicId
     * @param boolean $rowCount
     * @return array $topicData
     */
    public function getParentTopicDetailsByTopicId($topicId, $rowCount = FALSE)
    {
    	$classesSql = "SELECT t.topic_id, t.topic_name, t.topic_info, TIME(term.starts_on), TIME(term.ends_on)
    	FROM topic AS t
    	INNER JOIN classes AS c ON c.subject_id=t.subject_id
    	INNER JOIN class_sessions AS term ON term.teacher_id=c.teacher_id
    	WHERE t.parent_topic_id IS NULL AND t.topic_id=$topicId ";
    	$classesSql .= " GROUP BY t.topic_id";
    
    	if ($rowCount) {
    		$topicData = $this->db->query($classesSql)->num_rows();
    	} else {
    		$topicData = $this->db->query($classesSql)->result();
    	}
    
    	return $topicData;
    }
    
    /**
     * Insert sub topic 
     * @param integer $parentTopicId
     * @param integer $subjectId
     * @param string $topicName
     * @return integer $lastTopicId
     */
    public function insertSubTopic($parentTopicId, $subjectId, $topicName)
    {
    	$data = array(
    		'parent_topic_id' => $parentTopicId ,
    		'subject_id' => $subjectId ,
    		'topic_name' => $topicName,
    		'topic_info' => NULL,
    	);
    	
    	$this->db->insert('topic', $data);
    	return $this->db->insert_id();
    }
    
    /**
     * Insert parent topic
     * @param integer $subjectId
     * @param string $topicName
     */
    public function insertParentTopic($subjectId, $topicName)
    {
    	$data = array(
    		'parent_topic_id' => NULL,
    		'subject_id' => $subjectId ,
    		'topic_name' => $topicName,
    		'topic_info' => NULL
    	);
    	 
    	$this->db->insert('topic', $data);
    	
    	return $this->db->insert_id();
    }
    
    /**
     * Update topic name
     * @param string $topicName
     * @param integer $topicId
     */
    public function updateTopicName($topicName, $topicId)
    {
    	$data = array('topic_name' => $topicName);
    	$where = "topic_id = '$topicId'";
    	$this->db->update('topic', $data, $where);
    }
    
    /**
     * Delete record by topic id
     * @param integer $topicId
     */
    public function deleteRecordByTopicId($topicId)
    {
        //Move question to dummy topic
        $questionModel = $this->getQuestionModel();
        $updatedFlag = $questionModel->assignQuestionToDummyTopic($topicId);
        if ($updatedFlag == TRUE) {
            //Lesson plan table contains foreign key
            $this->db->delete(array('lesson_plan','topic'), array('topic_id' => $topicId));
        }
    }
    
    /**
     * Delete record by parent topic id
     * @param integer $parentTopicId
     */
    public function deleteRecordByParentTopicId($parentTopicId)
    {
    	$this->db->delete('topic', array('parent_topic_id' => $parentTopicId));
    }

    /**
     * Get sub topic data
     * @param integer $topicId
     * @return array $topicData
     */
    public function getSubTopicData($topicId)
    {
        $classesSql = "SELECT *
    	FROM topic AS t
    	WHERE t.topic_id=$topicId ";
        $classesSql .= " GROUP BY t.topic_id";
        $topicData = $this->db->query($classesSql)->row();

        return $topicData;
    }

    /**
     * Get sub topic data by parent topic id
     * @param integer $parentTopicId
     * @return array $topicData
     */
    public function getSubTopicDataByParentTopicId($parentTopicId)
    {
        $classesSql = "SELECT *
    	FROM topic AS t
    	WHERE t.parent_topic_id=$parentTopicId ";
        $classesSql .= " GROUP BY t.topic_id";
        $topicData = $this->db->query($classesSql)->result();

        return $topicData;
    }
}