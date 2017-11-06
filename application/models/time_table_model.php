<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Time_table_model extends CI_Model
{
    public function __construct()
    {
        parent:: __construct();
    }

    public function timeTable()
    {
     /*     $today =date('D');
            if($today == "Mon")
            {
                 $from_date=date('Y-m-d');
                 $to_date=date('Y-m-d',strtotime('+ 4 day'));
            }
            if($today == "Tue")
            {
                 $from_date=date('Y-m-d', strtotime('-1 day'));
                 $to_date=date('Y-m-d',strtotime('+ 3 day'));
            }
            if($today == "Wed")
            {
                 $from_date=date('Y-m-d', strtotime('-2 day'));
                 $to_date=date('Y-m-d',strtotime('+ 2 day'));
            }
            if($today == "Thu")
            {
                 $from_date=date('Y-m-d', strtotime('-3 day'));
                 $to_date=date('Y-m-d',strtotime('+ 1 day'));
            }
            if($today == "Fri")
            {
                 $from_date=date('Y-m-d', strtotime('-4 day'));
                 $to_date=date('Y-m-d');
            }
            if($today == "Sat")
            {
                 $from_date=date('Y-m-d', strtotime('+2 day'));
                 $to_date=date('Y-m-d',strtotime('+ 6 day'));
            }
           if($today == "Sun")
            {
                 $from_date=date('Y-m-d', strtotime('+1 day'));
                 $to_date=date('Y-m-d',strtotime('+ 5 day'));
            }
     */
      $from_date = date('Y-m-d',strtotime('monday this week'));
      $to_date=date('Y-m-d',strtotime('friday this week'));
     $userId=$this->session->userdata('user_id');
            
//     	echo "query:select sub.subject_name,sclassmap.student_id, term.class_session_id, term.starts_on, term.ends_on,term.teacher_id,user.first_name,user.	middle_name,user.last_name, user.user_id, term.class_id,class.subject_id,room.room_name from class_sessions as term inner join student_class_map sclassmap on sclassmap.class_id = term.class_id inner join tbl_auth as user on user.user_id=sclassmap.student_id inner join classes as class on sclassmap.class_id=class.class_id inner join rooms as room on term.room_id=room.room_id inner join subjects as sub on class.subject_id=sub.subject_id where sclassmap.student_id = '538' and term.starts_on between '2014-11-08 00:00:00' and '2014-11-08 23:59:59'";
    	$query=$this->db->query("select sub.subject_name,class.class_name,sclassmap.student_id, term.class_session_id, term.starts_on, term.ends_on,term.teacher_id,user.first_name,user.middle_name,user.last_name, user.user_id, term.class_id,class.subject_id,room.room_name,room.room_id from class_sessions as term inner join student_class_map sclassmap on sclassmap.class_id = term.class_id inner join tbl_auth as user on user.user_id=sclassmap.student_id inner join classes as class on sclassmap.class_id=class.class_id inner join rooms as room on term.room_id=room.room_id inner join subjects as sub on class.subject_id=sub.subject_id where sclassmap.student_id = '$userId' and term.starts_on between '$from_date 00:00:00' and '$to_date 23:59:59' and term.session_state != 6");  //and date(term.starts_on)='2014-05-10'
        //echo "select sub.subject_name,sclassmap.student_id, term.class_session_id, term.starts_on, term.ends_on,term.teacher_id,user.first_name,user.	middle_name,user.last_name, user.user_id, term.class_id,class.subject_id,room.room_name from class_sessions as term inner join student_class_map sclassmap on sclassmap.class_id = term.class_id inner join tbl_auth as user on user.user_id=sclassmap.student_id inner join classes as class on sclassmap.class_id=class.class_id inner join rooms as room on term.room_id=room.room_id inner join subjects as sub on class.subject_id=sub.subject_id where sclassmap.student_id = '510' and term.starts_on between '$from_date 00:00:00' and '$to_date 23:59:59'";
        return $query->result();                 	
    }
    function add_sevendays()
    {
        $sesssionFromDate=$this->session->userdata('from_date');
        $sessionToDate=$this->session->userdata('to_date');
        $userId=$this->session->userdata('user_id');
         
             $monday = date('Y-m-d',strtotime('monday this week'));
             $friday = date('Y-m-d',strtotime('friday this week'));

        if($sesssionFromDate=='')
        {
        $from_date=date('Y-m-d',strtotime($monday.'+7 day'));
        $to_date= date('Y-m-d',strtotime($friday.'+7 day'));

            $this->session->set_userdata('process_add','add');
            $this->session->unset_userdata('process_sub');
            $this->session->set_userdata('from_date',$from_date);
            $this->session->set_userdata('to_date',$to_date);
        }
        else
        {
            if($this->session->userdata('process_sub')=='sub')
            {
                $from_date=date('Y-m-d',strtotime($sesssionFromDate.'+14 day'));
                $to_date= date('Y-m-d',strtotime($sessionToDate.'+14 day'));
            }
            if($this->session->userdata('process_add')=='add')
            {
                 $from_date=$sesssionFromDate;
                 $to_date= $sessionToDate;
            }

            $this->session->set_userdata('process_add','add');
            $this->session->unset_userdata('process_sub');
            $this->session->set_userdata('from_date',$from_date);
            $this->session->set_userdata('to_date',$to_date);
        }   
              
        $query=$this->db->query("select sub.subject_name,class.class_name,sclassmap.student_id, term.class_session_id, term.starts_on, term.ends_on,term.teacher_id,user.first_name,user.	middle_name,user.last_name, user.user_id, term.class_id,class.subject_id,room.room_name,room.room_id from class_sessions as term inner join student_class_map sclassmap on sclassmap.class_id = term.class_id inner join tbl_auth as user on user.user_id=sclassmap.student_id inner join classes as class on sclassmap.class_id=class.class_id inner join rooms as room on term.room_id=room.room_id inner join subjects as sub on class.subject_id=sub.subject_id where sclassmap.student_id = '$userId' and term.starts_on between '$from_date 00:00:00' and '$to_date 23:59:59' and term.session_state != 6");  //and date(term.starts_on)='2014-05-10'
       //echo "select sub.subject_name,sclassmap.student_id, term.class_session_id, term.starts_on, term.ends_on,term.teacher_id,user.first_name,user.	middle_name,user.last_name, user.user_id, term.class_id,class.subject_id,room.room_name from class_sessions as term inner join student_class_map sclassmap on sclassmap.class_id = term.class_id inner join tbl_auth as user on user.user_id=sclassmap.student_id inner join classes as class on sclassmap.class_id=class.class_id inner join rooms as room on term.room_id=room.room_id inner join subjects as sub on class.subject_id=sub.subject_id where sclassmap.student_id = '510' and term.starts_on between '$from_date 00:00:00' and '$to_date 23:59:59'";
        return $query->result();
    }
    function sub_sevendays()
    {
        $session_FromDate=$this->session->userdata('from_date');
        $session_ToDate=$this->session->userdata('to_date');
        $userId=$this->session->userdata('user_id');

             $monday = date('Y-m-d',strtotime('monday this week'));
             $friday = date('Y-m-d',strtotime('friday this week'));
        
        if($session_FromDate=='' && $session_ToDate=='')
        {
            $from_date=date('Y-m-d',strtotime($monday.'-7 day'));
            $to_date=date('Y-m-d',strtotime($friday.'-7 day'));
            $this->session->unset_userdata('process_add');
            $this->session->set_userdata('process_sub','sub');
            $this->session->set_userdata('from_date',$from_date);
            $this->session->set_userdata('to_date',$to_date);
        }
        else
        {
            if($this->session->userdata('process_add')=="add")
            {
                $from_date=date('Y-m-d',strtotime($session_FromDate.'-14 day'));
                $to_date=date('Y-m-d',strtotime($session_ToDate.'-14 day'));
            }
            if($this->session->userdata('process_sub')=="sub")
            {
               $from_date=$session_FromDate;
               $to_date=$session_ToDate;
            }
                $this->session->unset_userdata('process_add');
                $this->session->set_userdata('process_sub','sub');
                $this->session->set_userdata('from_date',$from_date);
                $this->session->set_userdata('to_date',$to_date);
         }
//          echo "select sub.subject_name,class.class_name,sclassmap.student_id, term.class_session_id, term.starts_on, term.ends_on,term.teacher_id,user.first_name,user.	middle_name,user.last_name, user.user_id, term.class_id,class.subject_id,room.room_name,room.room_id from class_sessions as term inner join student_class_map sclassmap on sclassmap.class_id = term.class_id inner join tbl_auth as user on user.user_id=sclassmap.student_id inner join classes as class on sclassmap.class_id=class.class_id inner join rooms as room on term.room_id=room.room_id inner join subjects as sub on class.subject_id=sub.subject_id where sclassmap.student_id = '$userId' and term.starts_on between '$from_date 00:00:00' and '$to_date 23:59:59'";
    $query=$this->db->query("select sub.subject_name,class.class_name,sclassmap.student_id, term.class_session_id, term.starts_on, term.ends_on,term.teacher_id,user.first_name,user.	middle_name,user.last_name, user.user_id, term.class_id,class.subject_id,room.room_name,room.room_id from class_sessions as term inner join student_class_map sclassmap on sclassmap.class_id = term.class_id inner join tbl_auth as user on user.user_id=sclassmap.student_id inner join classes as class on sclassmap.class_id=class.class_id inner join rooms as room on term.room_id=room.room_id inner join subjects as sub on class.subject_id=sub.subject_id where sclassmap.student_id = '$userId' and term.starts_on between '$from_date 00:00:00' and '$to_date 23:59:59' and term.session_state != 6");  //and date(term.starts_on)='2014-05-10'
    //echo "select sub.subject_name,sclassmap.student_id, term.class_session_id, term.starts_on, term.ends_on,term.teacher_id,user.first_name,user.	middle_name,user.last_name, user.user_id, term.class_id,class.subject_id,room.room_name from class_sessions as term inner join student_class_map sclassmap on sclassmap.class_id = term.class_id inner join tbl_auth as user on user.user_id=sclassmap.student_id inner join classes as class on sclassmap.class_id=class.class_id inner join rooms as room on term.room_id=room.room_id inner join subjects as sub on class.subject_id=sub.subject_id where sclassmap.student_id = '510' and term.starts_on between '$from_date 00:00:00' and '$to_date 23:59:59'";
    return $query->result();   
    }

}