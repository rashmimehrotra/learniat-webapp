<?php
class Question_type extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get question type
     * @return array $questionTypes
	 */
	public function getQuestionTypes()
	{
		$sql = "SELECT question_type_id, question_type_title
		FROM question_types AS qt";
		$result = $this->db->query($sql)->result();

		$questionTypes = array();
		foreach ($result AS $row) {
			$questionTypes[$row->question_type_id] = $row->question_type_title;
		}
		return $questionTypes;
	}
}