<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_student_session_details extends CI_Model
{
    public function __construct()
    {
        parent:: __construct();
    }
    function getStudentGraspIndex($userId,$classID,$subjectId,$roomId,$topicId)
    {$GI = 0;
			if(!empty($topicId)) {
				$queryForGsp ="SELECT si.transaction_id,si.subtotal_of_count,si.subtotal_of_score,sim.weight_value FROM
				student_index si,tbl_auth t,school_index_map sim WHERE si.student_id=t.user_id
				and t.school_id=sim.school_id and sim.index_type=1 and si.transaction_id=sim.transaction_type
				and si.class_session_id='$classID' and si.topic_id ='$topicId' and si.student_id='$userId'";
				$queryGp = $this->db->query($queryForGsp);
				if ($queryGp->num_rows() > 0)
				{
					$sumofweights = 0;
					$weightedscore = 0;
					$topicwscore = 0;
					$sumofweights = 0;
				foreach ($queryGp->result() as $rowGraspRow)
				{
						$total_count = $rowGraspRow->subtotal_of_count;
					$total_score = $rowGraspRow->subtotal_of_score;
					$weight = $rowGraspRow->weight_value;
					$topicwscore += ($total_score*$weight);
					$sumofweights += $total_count*$weight;
				}
				$GI = ($topicwscore/$sumofweights)*100;
				}
				}
				return $GI;
    }
    
    function getPIIndex($userId,$classID,$subjectId,$roomId) {
    	$PI = 0;
    	$queryForGrspIndex = "select sclassmap.student_id, term.class_session_id, term.starts_on, term.ends_on,
						    	user.first_name, user.user_id,  state.state_description,class.class_id, class.class_name,
						    	room.room_name, room.room_id, subject.subject_id, subject.subject_name,
						    	term.class_id,term.teacher_id
						    	from class_sessions as term inner join student_class_map sclassmap on sclassmap.class_id = term.class_id
						    	inner join tbl_auth as user on user.user_id=sclassmap.student_id
						    	inner join entity_states as state on term.session_state = state.state_id
						    	inner join classes as class on sclassmap.class_id = class.class_id
						    	inner join rooms as room on term.room_id = room.room_id
						    	inner join subjects as subject on subject.subject_id = class.subject_id
						    	where sclassmap.student_id = '$userId' and sclassmap.class_id='$classID' and term.room_id='$roomId'
						    	and class.subject_id='$subjectId' and term.session_state != 6";
    	$queryClassSessionsDetails = $this->db->query($queryForGrspIndex);
    	if ($queryClassSessionsDetails->num_rows() > 0)
    	{
    		foreach ($queryClassSessionsDetails->result() as $row)
    		{
    			$studentId = $row->student_id;
    			$classSessionId =$row->class_session_id;
    			if(!empty($studentId) && !empty($classSessionId)) {
    				$queryForStudentIndex = $this->db->query("select topic_id from student_index where student_id = '$studentId' and class_session_id ='$classSessionId'");
    				if ($queryForStudentIndex->num_rows() > 0)
    				{
    					foreach ($queryForStudentIndex->result() as $rowGrasp)
    					{
    						$topicId = $rowGrasp->topic_id;
    						if(!empty($topicId)) {
    							$queryForGsp ="SELECT si.transaction_id,si.subtotal_of_count,sim.weight_value FROM student_index si,tbl_auth t,
    										school_index_map sim WHERE si.student_id=t.user_id and t.school_id=sim.school_id and sim.index_type=2 
    										and si.transaction_id=sim.transaction_type and si.class_session_id='$classSessionId' and 
    										si.student_id='$studentId'";
    							$queryGp = $this->db->query($queryForGsp);
    							if ($queryGp->num_rows() > 0)
    							{
	    							$sumofweights = 0;
	    							$weightedscore = 0;
    							foreach ($queryGp->result() as $rowGraspRow)
    							{
	    							$total_count = $rowGraspRow->subtotal_of_count;
	    							$weight = $rowGraspRow->weight_value;
	    							$sumofweights += ($total_count*$weight);
    							}
    							$PI = $sumofweights;
    							}
    							}
    							}
    							}else{
    							//other code goes here
    							$queryForGsp ="SELECT si.transaction_id,si.subtotal_of_count,sim.weight_value FROM student_index si,tbl_auth t,
    										school_index_map sim WHERE si.student_id=t.user_id and t.school_id=sim.school_id and sim.index_type=2 
    										and si.transaction_id=sim.transaction_type and si.class_session_id='$classSessionId' and 
    										si.student_id='$studentId'";
    								$queryGp = $this->db->query($queryForGsp);
    								if ($queryGp->num_rows() > 0)
    								{
    								$sumofweights = 0;
	    							$weightedscore = 0;
    							foreach ($queryGp->result() as $rowGraspRow)
    							{
	    							$total_count = $rowGraspRow->subtotal_of_count;
	    							$weight = $rowGraspRow->weight_value;
	    							$sumofweights += ($total_count*$weight);
    							}
    							$PI = $sumofweights;
    								}
    								}
    								}
    								}
    								}else{
    								$queryForGsp ="SELECT si.transaction_id,si.subtotal_of_count,sim.weight_value FROM student_index si,tbl_auth t,
    										school_index_map sim WHERE si.student_id=t.user_id and t.school_id=sim.school_id and sim.index_type=2 
    										and si.transaction_id=sim.transaction_type and 
    										si.student_id='$userId'";
    								$queryGp = $this->db->query($queryForGsp);
    								if ($queryGp->num_rows() > 0)
    								{
    								$sumofweights = 0;
	    							$weightedscore = 0;
    							foreach ($queryGp->result() as $rowGraspRow)
    							{
	    							$total_count = $rowGraspRow->subtotal_of_count;
	    							$weight = $rowGraspRow->weight_value;
	    							$sumofweights += ($total_count*$weight);
    							}
    							$PI = $sumofweights;
    								}
    								}
    								return $PI;
    }
}
