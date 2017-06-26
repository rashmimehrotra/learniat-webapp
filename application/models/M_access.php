<?php
class M_access extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	public function check_user($username,$password)
	{
		$roleID="";
		$query = $this->db->query("SELECT * FROM tbl_auth where user_name='$username' and password='$password'");
		if($query -> num_rows() == 1)
		{
			foreach ($query->result() as $rowImage)
			{
				$roleID=$rowImage->role_id;
				$userId=$rowImage->user_id;
				if($roleID=='4') {
					$this->session->set_userdata( array(
							'user_id'=>$rowImage->user_id,
							'role_id'=> $rowImage->role_id,
							'email_id'=>$rowImage->email_id,
							'isLoggedIn'=>true
					)
					);
				}else if($roleID=='3') {
					$this->session->set_userdata( array(
							'teacher_id'=>$rowImage->user_id,
							'role_id'=> $rowImage->role_id,
							'email_id'=>$rowImage->email_id,
							'isTeacherLoggedIn'=>true
					)
					);
				}
				//get profile image
				//$data['full_path']=$this->config->item('image_url')."sunprofile/".$userId.".jpg";
				$urlForImg = base_url();
				$urlForImg = str_replace("Learniat","",$urlForImg);
                                $data['full_path']=$this->config->item('image_url')."/sunprofile/".$userId.".jpg";
				//$data['full_path']=$urlForImg."sunprofile/".$userId.".jpg";
				$data['fullName']=$rowImage->first_name." ".$rowImage->last_name;
				//store values into session
				$profileData=array('user_id' =>$userId,'fullName'=>$data['fullName'],'imageName'=>$data['full_path']);
				$this->session->set_userdata('profileData',$profileData);
			}
		}
		
		return $roleID;
    }
    
    /**
     * Check email
     * @param string $email
     * @return array $result
     */
    public function check_email($email)
    {
    	$queryText = "SELECT * FROM tbl_auth where email_id='$email'";
    	$queryEmail = $this->db->query($queryText);
    	$username ="";
    	$password ="";
    	$status = "";
    	foreach ($queryEmail->result() as $rowImage) {
    		$username=$rowImage->user_name;
    		$password=$rowImage->password;
    		$status ="success";
    	}	
    	$result = array('username'=>$username,"password" =>$password,"status" =>$status);
    	return $result;
    }
    
    /**
     * Get school id by student id
     * 
     * @param integer $studentId
     * @return integer $schoolId
     */
    public function getSchoolIdByStudentId($studentId)
    {
    	$schoolIdSql = "SELECT t.school_id FROM tbl_auth t where t.user_id='$studentId'";
    	$topic_id_get = $this->db->query($schoolIdSql)->row();
    	$schoolId = $topic_id_get->school_id;
    	
    	return $schoolId;
    }
    
}
