<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class External
{
	protected $_jupiterApi = 'http://54.251.104.13/jupiter_dev/sun.php?api=';
	/**
	 * Get jupiter api url
	 * @return string $pageurl
	 */
	public function getJupiterApi()
	{
		$pageurl = $this->_jupiterApi;
		
		return $pageurl;
	}
	
	/**
	 * Function returns XML string for input associative array.
	 * @param Array $array Input associative array
	 * @param String $wrap Wrapping tag
	 * @param Boolean $upper To set tags in uppercase
	 * @return String $xml
	 */
	public function arrayToXml($array, $wrap='Sunstone', $upper=FALSE) {
		// set initial value for XML string
		$xml = '';
		// wrap XML with $wrap TAG
		if ($wrap != NULL) {
			$xml .= "<$wrap>";
		}
		// main loop
		foreach ($array as $key=>$value) {
				
			if (is_array($value)) {
				$xml .= $this->arrayToXml($value, $key, $upper);
			} else {
				// set tags in uppercase if needed
				if ($upper == TRUE) {
					$key = strtoupper($key);
				}
				// append to XML string
				$xml .= "<$key>" . htmlspecialchars(trim($value)) . "</$key>";
			}
		}
		// close wrap TAG if needed
		if ($wrap != NULL) {
			$xml .= "</$wrap>";
		}
		// return prepared XML string
		return urldecode($xml);
	}
	
	public function getServicePath($data)
	{
		$page = $this->arrayToXml($data); 
		$pageurl = $this->getJupiterApi() . $page;
		
		return $pageurl;
	}
	
	public function callService($pageurl)
	{
		$this->load->library('curl');
		$result = $this->curl->simple_get($pageurl);
	}
	
	/**
	 * Get session by date
	 * 
	 * @param collection|array $sessionResult
	 * @return array $sessionDates
	 */
	public function getSessionIdByDate($sessionResult)
	{
		//get_instance()->load->helper('HELPER_NAME');
		$sessionDates = array();
		if (!empty($sessionResult)) {
			foreach ($sessionResult AS $data) {
				$formatedDate = date("m/d/Y", strtotime($data->ends_on));
				$data->day = $this->getDayName($formatedDate);				
				$sessionDates[$formatedDate][] = $data;
			}
		}
		
		return $sessionDates;	
	}
	
	/**
	 * Get day name
	 * @param string $date
	 * @return string $dayName
	 */
	function getDayName($date)
	{
		$today = new DateTime();
		$yesterday = new DateTime('-1 day');
		
		if($date == $today->format('m/d/Y')) {
			$dayName = 'Today';
		} else if($date == $yesterday->format('m/d/Y')) {
			$dayName = 'Yesterday';
		} else {
			$dayName = date("l", strtotime($date));
		}
		return $dayName;
	}
	
	/**
	 * Xml to object
	 * @param object $result stdClass
	 */
	public function xmlToObject($result)
	{   
		//var_dump($result);
		$xml = simplexml_load_string($result);
		$fp=fopen('xml.txt','w');
		fwrite($fp,print_r($xml,true));
		return $xml->SunStone->Action;
	}
	
	/**
	 * Xml to json
	 * @param string $result
	 * @return string
	 */
    public function xmlStringToJson($result)
    {
        $xml = $this->xmlToObject(result);
        $json = json_encode($xml);
        
        return $json;
    }
    
    /**
     * xml to string
     * @param string $result
     * @return array $array
     */
    public function xmlStringToArray($result)
    {
        $json = $this->xmlStringToJson($result);
        $array = json_decode($json,TRUE);
        
        return $array;
    }
    

    /**
     * Get class session summery
     *
     * @param integer $sessionId
     * @return array|boolean
     */
    public function getClassSessionSummary($sessionId)
    {
    	$data = array (
    		'Action' => array (
    			'Service' => 'ClassSessionSummary',
    			'SessionId' => $sessionId
    		)
    	);
    	
    	return $this->getServicePath($data);
    }
    

    /**
     * Get all student index
     *
     * @param integer $sessionId
     * @param integer $topicId
     * @return array|boolean
     */
    public function getAllStudentIndex($sessionId = NULL, $topicId = NULL)
    {
    	$data = array (
			'Action' => array (
				'Service' => 'GetAllStudentIndex',
				'SessionId' => $sessionId,
				'TopicId' => $topicId
			)
    	);
    	return $this->getServicePath($data);
    }
    
    /**
     * Get user info
     *
     * @param integer $sessionId
     * @return array|boolean
     */
    public function getUserInfo($userId)
    {
    	$data = array (
    		'Action' => array (
    			'Service' => 'GetMyInfo',
    			'UserId' => $userId
    		)
    	);
    	
    	return $this->getServicePath($data);
    }
    
    /**
     * Get assessment answer data
     * @param integer $assessmentAnswerId
     * @return string $pageurl
     */
    public function getAssessmentAnswerData($assessmentAnswerId)
    {
    	$data = array (
    		'Action' => array (
    			'Service' => 'RetrieveStudentAnswer',
    			'AssessmentAnswerId' => $assessmentAnswerId
    		)
    	);

    	$pageurl = $this->getServicePath($data);
    	
    	return $pageurl;
    }
}
