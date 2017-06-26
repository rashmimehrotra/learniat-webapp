    <?php
class Lesson_plan extends CI_Model
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
     * Get topic model
     */
    public function getTopicModel()
    {
        $CI = &get_instance();
        $CI->load->model('topic');
        return $CI->topic;
    }
    
    /**
     * Get tagged
     * @param integer $classId
     * @param integer $topicId
     * @return array|string $topicTagged
     */
    public function getTagged($classId, $topicId)
    {
    	$sql = "SELECT topic_tagged
    	FROM lesson_plan
    	WHERE class_id = '$classId' and topic_id = '$topicId' ";

    	$rowCount = $this->db->query($sql)->num_rows();
    	if ($rowCount > 0 ) {
    		$taggedData = $this->db->query($sql)->row();
    		$topicTagged = $taggedData->topic_tagged;
    	} else {
    		$topicTagged = NULL;
    	}
    	
    	return $topicTagged;
    }
    
    /**
     * Update tagged
     * @param integer $classId
     * @param integer $topicId
     * @param integer $tagged
     * @param integer $teacherId
     */
    public function updateTagged($classId, $topicId, $tagged, $teacherId)
    {
    	$data = array('topic_tagged' => $tagged);
    	$where = "class_id = '$classId' AND topic_id = '$topicId' AND tagged_by = '$teacherId'";
    	$this->db->update('lesson_plan', $data, $where);
    }
    
    /**
     * Insert or update
     * @param integer $classId
     * @param integer $topicId
     * @param integer $tagged
     * @param integer $teacherId
     */
    public function insertOnDuplicateKey($classId, $topicId, $tagged, $teacherId)
    {
    	$data = array(
    		'tagged_by' => $teacherId,
    		'topic_tagged' => $tagged,
    		'class_id' => $classId,
    		'topic_id' => $topicId
    	);
    	
    	$sql = $this->db->insert_string('lesson_plan', $data) . " ON DUPLICATE KEY UPDATE tagged_by=$teacherId, topic_tagged=$tagged ";
    	$this->db->query($sql);
    }
    
    /**
     * Update sub-topic using parent topic id
     * @param integer $classId
     * @param integer $topicId
     * @param integer $tagged
     * @param integer $teacherId
     */
    public function updateSubTopicTaggedByParentTopic($classId, $topicId, $tagged, $teacherId)
    {
    	$classSessionModel = $this->getClassSessionModel();
    	$subTopicList = $classSessionModel->getSubTopicIdsList($classId, $topicId);
    	
    	foreach ($subTopicList AS $subTopic) {
    		$this->insertOnDuplicateKey($classId, $subTopic->topic_id, $tagged, $teacherId);
    	}
    }
    
    /**
     * Insert tag
     * @param integer $classId
     * @param integer $topicId
     * @param integer $tagged
     * @param integer $teacherId
     */
    public function insert($classId, $topicId, $tagged, $teacherId)
    {
    	$data = array(
    		'tagged_by' => $teacherId,
    		'topic_tagged' => $tagged,
    		'class_id' => $classId,
    		'topic_id' => $topicId
    	);
    	 
    	$this->db->insert('lesson_plan', $data);
    }
    
    /**
     * Get sub topic tagged
     *
     * @param integer $classId
     * @param integer $topicId
     * @return array $classesData
     */
    public function getSubTopicTaggedList($classId, $topicId)
    {
    	$classesSql = "SELECT lp.topic_tagged
    	FROM topic AS t
    	INNER JOIN classes AS c ON c.subject_id=t.subject_id
    	LEFT JOIN lesson_plan AS lp ON lp.class_id = c.class_id AND lp.topic_id = t.topic_id
    	WHERE t.parent_topic_id=$topicId AND c.class_id=$classId";
    
    	$classesData = $this->db->query($classesSql)->result();
    
    	return $classesData;
    }

    /**
     * Get sub-topic distinct tagged list
     * @param integer $classId
     * @param integer $topicId
     * @return array $classesData
     */
    public function getSubTopicDistinctTaggedList($classId, $topicId)
    {
        $classesSql = "SELECT DISTINCT lp.topic_tagged
    	FROM topic AS t
    	INNER JOIN classes AS c ON c.subject_id=t.subject_id
    	LEFT JOIN lesson_plan AS lp ON lp.class_id = c.class_id AND lp.topic_id = t.topic_id
    	WHERE t.parent_topic_id=$topicId AND c.class_id=$classId";

        $classesData = $this->db->query($classesSql)->result();

        return $classesData;
    }

    /**
     * Update parent topic tag by sub-topic
     * @param integer $classId
     * @param integer $topicId
     * @param integer $tagged
     * @param integer $teacherId
     */
    public function updateParentTopicTaggedBySubTopic($classId, $topicId, $tagged, $teacherId)
    {
        $topicModel = $this->getTopicModel();
        $subTopicDetails = $topicModel->getSubTopicData($topicId);
        if (isset($subTopicDetails->parent_topic_id) && !empty($subTopicDetails->parent_topic_id)) {

            $subTopicList = $this->getSubTopicDistinctTaggedList($classId, $subTopicDetails->parent_topic_id);

            //Update parent topic only when all sub-topic has same tagging.
            if (count($subTopicList) == 1) {
                $this->insertOnDuplicateKey($classId, $subTopicDetails->parent_topic_id, $subTopicList[0]->topic_tagged, $teacherId);
            }
        }
    }
}