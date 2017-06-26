<?php
class Topic_log extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
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
     * Get cumulative time for sub topic id
     * @param integer $classId
     * @param integer $topicId
     * @return int|string $mainCumulativeTime
     */
    public function getCumulativeTimeForSubTopicId($classId, $topicId)
    {
    	$sql = "SELECT cumulative_time
    	FROM topic_log
    	WHERE class_id = '$classId' and topic_id = '$topicId' ";
    	$sql .= " ORDER BY transition_time desc limit 1";

    	$mainCumulativeTime = 0;
    	$rowCount = $this->db->query($sql)->num_rows();
    	if ($rowCount > 0 ) {
    		$topicLogData = $this->db->query($sql)->row();
    		$seconds = $topicLogData->cumulative_time;
    		$cumulativeTime = gmdate("H:i:s", $seconds);
    	} else {
    		$cumulativeTime = "00:00:00";
    	}

    	$mainCumulativeTime = strtotime($mainCumulativeTime)+strtotime($cumulativeTime);
    	$mainCumulativeTime = gmdate("H:i:s", $mainCumulativeTime);
    
    	return $mainCumulativeTime;
    }

    /**
     * Get cumulative time for parent topic id
     * @param integer $classId
     * @param integer $topicId
     * @return int|string $mainCumulativeTime
     */
    public function getCumulativeTimeForParentTopicId($classId, $topicId)
    {
        $topicModel = $this->getTopicModel();
        $subTopicData = $topicModel->getSubTopicDataByParentTopicId($topicId);

        $mainCumulativeTime = 0;
        foreach ($subTopicData AS $subTopicDetails) {
            $subTopicTime = $this->getCumulativeTimeForSubTopicId($classId, $subTopicDetails->topic_id);
            $mainCumulativeTime += strtotime($subTopicTime);
        }

        $mainCumulativeTime = gmdate("H:i:s", $mainCumulativeTime);

        return $mainCumulativeTime;
    }
}