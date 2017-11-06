<?php
class Table_auth extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get user info by user id.
	 * @param integer $userId
	 * @return row_object $userInfo
	 */
	public function getUserInfo($userId)
	{
		$sql = "SELECT user.*
		FROM tbl_auth AS user
		WHERE user.user_id = $userId";
		$userInfo = $this->db->query($sql)->row();
		
		return $userInfo;
	}
}