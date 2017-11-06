<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_student_session extends CI_Model
{
    public function __construct()
    {
        parent:: __construct();
    }
    function getYesterDaySessions($classID,$dateSearchStatus)
    {
    	if( $this->session->userdata('isLoggedIn') ) {
			$userId = $this->session->userdata( 'user_id' );
		if(!empty($userId)) {
			$yesterDayDate = date('Y-m-d',strtotime("-1 days"));
			$queryForClass = "select sclassmap.student_id, term.class_session_id, term.starts_on, term.ends_on,
						user.first_name, user.user_id,  state.state_description,class.class_id, class.class_name,
						room.room_name, room.room_id, subject.subject_id, subject.subject_name,
						term.class_id
						from class_sessions as term inner join student_class_map sclassmap on sclassmap.class_id = term.class_id
						inner join tbl_auth as user on user.user_id=sclassmap.student_id
						inner join entity_states as state on term.session_state = state.state_id
						inner join classes as class on sclassmap.class_id = class.class_id
						inner join rooms as room on term.room_id = room.room_id
						inner join subjects as subject on subject.subject_id = class.subject_id
						where sclassmap.student_id = '$userId' and term.starts_on between '$yesterDayDate 00:00:00' and '$yesterDayDate 23:59:59' and term.session_state != 6";
    	}
    	if(!empty($classID)){
    		$queryForClass .= " and sclassmap.class_id='$classID'";
    	}
    	if(!empty($dateSearchStatus) && $dateSearchStatus == "thisweek") {
    		$from_date = date('Y-m-d',strtotime('monday this week'));
    		$to_date = date('Y-m-d');
    		$queryForClass .= " and term.starts_on between '$from_date 00:00:00' and '$to_date 23:59:59' ";
    	}
    	$queryForClass .= " order by term.starts_on asc";
    	$queryClassSessions = $this->db->query($queryForClass);
    	$data[] = array();
    	if ($queryClassSessions->num_rows() > 0)
    	{
    		foreach ($queryClassSessions->result() as $row)
    		{
    			$data[] = $row;
    		}
    	}
      }
      return $data;
    }
    function getAllSessions($classID,$dateSearchStatus) {
    	if( $this->session->userdata('isLoggedIn') ) {
    		$userId = $this->session->userdata( 'user_id' );
    		if(!empty($userId)) {
    			$previousDate = date('Y-m-d',strtotime("-2 days"));
    			$queryForClass = "select sclassmap.student_id, term.class_session_id, term.starts_on, term.ends_on,
					    			user.first_name, user.user_id,  state.state_description,class.class_id, class.class_name,
					    			room.room_name, room.room_id, subject.subject_id, subject.subject_name,
					    			term.class_id
					    			from class_sessions as term inner join student_class_map sclassmap on sclassmap.class_id = term.class_id
					    			inner join tbl_auth as user on user.user_id=sclassmap.student_id
					    			inner join entity_states as state on term.session_state = state.state_id
					    			inner join classes as class on sclassmap.class_id = class.class_id
					    			inner join rooms as room on term.room_id = room.room_id
					    			inner join subjects as subject on subject.subject_id = class.subject_id
					    			where sclassmap.student_id = '$userId' and term.starts_on <='$previousDate' and term.session_state != 6 ";
    		}
    		if(!empty($classID)){
    			$queryForClass .= " and sclassmap.class_id='$classID'";
    		}
    		if(!empty($dateSearchStatus) && $dateSearchStatus == "lastweek") {
    			$monday = date('Y-m-d',strtotime('monday this week'));
    			$friday = date('Y-m-d',strtotime('friday this week'));
    			$from_date = date('Y-m-d',strtotime($monday.'-7 day'));
    			$to_date = date('Y-m-d',strtotime($friday.'-7 day'));
    			$queryForClass .= " and term.starts_on between '$from_date 00:00:00' and '$to_date 23:59:59' ";
    		}
    		if(!empty($dateSearchStatus) && $dateSearchStatus == "thisweek") {
    			$from_date = date('Y-m-d',strtotime('monday this week'));
    			$to_date = date('Y-m-d');
    			$queryForClass .= " and term.starts_on between '$from_date 00:00:00' and '$to_date 23:59:59' ";
    		}
    		$queryForClass .= " order by term.starts_on desc";
    		$queryClassSessions = $this->db->query($queryForClass);
    		$data[] = array();
    		if ($queryClassSessions->num_rows() > 0)
    		{
    			foreach ($queryClassSessions->result() as $row)
    			{
    				$data[] = $row;
    			}
    		}
    	}
    	return $data;
    }
    
}