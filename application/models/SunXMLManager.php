<?php

require_once 'SunXMLConst.php';

class SunXMLManager
{
    public $event_log_id;
    public $tdb2,$xmldata2;
    public   function __construct()
    {
    }

    function createXML($array)
    {
        $xml='';
        $xml=$xml."<".SunXMLConst::$ROOTTAG.">";
        $xml=$xml."<".SunXMLConst::$MAINTAG.">";
        $xml=$xml."<".SunXMLConst::$ACTIONTAG.">";

        while (list($key, $value) = each($array)) 
        {
            if(is_array($value))
            {
                $xml=$xml."<".$key.">";
                while (list($k, $v) = each($value)) 
                {
                    if(is_array($v)) 
                    {
                        while (list($k1, $v1) = each($v)) 
                        {
                            if(is_array($v1))
                            {
                                $xml=$xml."<".$k.">";
                                while (list($k2, $v2) = each($v1)) 
                                    $xml=$xml."<".$k2.">".$v2."</".$k2.">";
                                $xml=$xml."</".$k.">";
                            }
                            else
                                $xml=$xml."<".$k.">".$v1."</".$k.">";
                        }
                    }
                    else
                        $xml=$xml."<".$k.">".$v."</".$k.">";
                }
                $xml=$xml."</".$key.">";
            }
            else
                $xml=$xml."<".$key.">".$value."</".$key.">";
        }
        $xml=$xml."</".SunXMLConst::$ACTIONTAG.">";
        $xml=$xml."</".SunXMLConst::$MAINTAG.">";
        $xml=$xml."</".SunXMLConst::$ROOTTAG.">";
        return $xml;
    }

    function parseXML($data)
    {
            function parseObject($mvalues) 
            {
                for ($i=0; $i < count($mvalues); $i++) {
                    $obj[$mvalues[$i]["tag"]] = $mvalues[$i]["value"];
                }
                return $obj;
            }
    
            $parser = xml_parser_create();
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
            xml_parse_into_struct($parser, $data, $values, $tags);
            xml_parser_free($parser);

            foreach ($tags as $key=>$val) {
                if ($key == SunXMLConst::$ACTIONTAG) {
                        $ObjRanges = $val;
                        for ($i=0; $i < count($ObjRanges); $i+=2) {
                            $offset = $ObjRanges[$i] + 1;
                            $len = $ObjRanges[$i + 1] - $offset;
                            $tdb[] = parseObject(array_slice($values, $offset, $len));
                        }
                } 
            else {
                        continue;
                }
            }
        return $tdb;
    }
    
            public function GetParticipationIndex($session_id=null,$student_id=null)
                {
                    try
                        {
                          if($student_id == null)
                          {
                            
                          }
                          else
                          {

                                
                                $get_exists_id = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,sim.weight_value FROM student_index si,tbl_auth t,school_index_map sim WHERE si.student_id=t.user_id and t.school_id=sim.school_id and sim.index_type=2 and si.transaction_id=sim.transaction_type and si.class_session_id='$session_id' and si.student_id='$student_id'";
                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                if ($exists_rows > 0)
                                {
                                    $sumofweights = 0;
                                    $weightedscore = 0;
                                    while($topic_id_get2 = mysql_fetch_assoc($exists_id_get))
                                    {
                                        
                                        $total_count = $topic_id_get2['subtotal_of_count'];                                        
                                        $weight = $topic_id_get2['weight_value']; 
                                        $sumofweights += ($total_count*$weight);
                                        
                                    }
                                    $PI = $sumofweights;
                                    return $PI;
                                
                                } 
                                else
                                {   
                                    return 0;                               
                                }                                
                        
                            
                          }
                        }
                    catch(Exception $e)
                        {
                            return "error";
                        }
                }           
 
            public function GetMainGraspIndex($schoolid=null,$session_id=null,$student_id=null,$topic_id=null,$subject_id=null)
                {
                    try
                        {
                          if($student_id == null)
                          {
                            
                            if($topic_id != null)
                            {
                                $GI =0;
                                $sub_topics = "select topic_id from topic where parent_topic_id = '$topic_id' and subject_id = '$subject_id'";
                                $sub_topic_list = SunDataBaseManager::getSingleton()->QueryDB($sub_topics);    
                                $sumofweights = 0;
                                $topicwscore = 0;                            
                                 while($subtopic_id_get = mysql_fetch_assoc($sub_topic_list))
                                 {
                                    $subtopic = $subtopic_id_get['topic_id'];
                                
                                    $get_exists_id = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,si.subtotal_of_score,sim.weight_value FROM student_index si,school_index_map sim WHERE sim.school_id='$schoolid' and sim.index_type=1 and si.transaction_id=sim.transaction_type and si.topic_id = '$subtopic'";
                                   
                                    $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                    $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                    if ($exists_rows > 0)
                                    {
    
                                        while($topic_id_get2 = mysql_fetch_assoc($exists_id_get))
                                        {
                                            
                                            $total_count = $topic_id_get2['subtotal_of_count'];
                                            $total_score = $topic_id_get2['subtotal_of_score'];                                        
                                            $weight = $topic_id_get2['weight_value']; 
                                            $topicwscore += ($total_score*$weight);
                                            $sumofweights += ($total_count*$weight);
                                            
                                        }
                                    }
                                
                                 
                                }   
                                if($sumofweights == 0)
                                {
                                   $GI = 0; 
                                }
                                else
                                {
                                   $GI = ($topicwscore/$sumofweights)*100;
                                }  
                                return $GI;                            
                                                      
                            }
                        }
                    }
                catch(Exception $e)
                        {
                            return "error";
                        }
                }
 
    
            public function GetGraspIndex($schoolid=null,$session_id=null,$student_id=null,$topic_id=null)
                {
                    try
                        {
                          if($student_id == null)
                          {
                            
                            if($topic_id == null)
                            {
                                if($schoolid == null && $session_id !=null)
                                {
                                     //grasp for all topics for all students fir the session    
                                    $schoolidget = "SELECT t.school_id FROM class_sessions s,tbl_auth t where t.user_id=s.teacher_id and s.class_session_id='$session_id'";
                                    $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($schoolidget);
                                    $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                    $schoolid = $topic_id_get2['school_id'];                                   
                                }

                                $get_exists_id = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,si.subtotal_of_score,sim.weight_value FROM student_index si,school_index_map sim WHERE sim.school_id='$schoolid' and sim.index_type=1 and si.transaction_id=sim.transaction_type ";
                                if($session_id != null)
                                {
                                   $get_exists_id.= "and si.class_session_id='$session_id'";
                                }                                
                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                if ($exists_rows > 0)
                                {
                                    $sumofweights = 0;
                                    $topicwscore = 0;
                                    while($topic_id_get2 = mysql_fetch_assoc($exists_id_get))
                                    {
                                        
                                        $total_count = $topic_id_get2['subtotal_of_count'];
                                        $total_score = $topic_id_get2['subtotal_of_score'];                                        
                                        $weight = $topic_id_get2['weight_value']; 
                                        $topicwscore += ($total_score*$weight);
                                        $sumofweights += ($total_count*$weight);
                                        
                                    }
                                    $GI = ($topicwscore/$sumofweights)*100;
                                    
                                    return $GI;
                                
                                } 
                                else
                                {   
                                    
                                    return 0;                               
                                }                                
                                                      
                            }
                            else
                            {
                                //return grasp for all students in topic
                                //grasp for all topics for all students fir the session    
                                if($schoolid == null && $session_id !=null)
                                {
                                    $schoolidget = "SELECT t.school_id FROM class_sessions s,tbl_auth t where t.user_id=s.teacher_id and s.class_session_id='$session_id'";
                                    $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($schoolidget);
                                    $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                    $schoolid = $topic_id_get2['school_id'];                                   
                                }
                                $get_exists_id = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,si.subtotal_of_score,sim.weight_value FROM student_index si,school_index_map sim WHERE sim.school_id='$schoolid' and sim.index_type=1 and si.transaction_id=sim.transaction_type and si.topic_id = '$topic_id' ";
                                if($session_id != null)
                                {
                                   $get_exists_id.= "and si.class_session_id='$session_id'";
                                }
                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                if ($exists_rows > 0)
                                {
                                    $sumofweights = 0;
                                    $topicwscore = 0;
                                    while($topic_id_get2 = mysql_fetch_assoc($exists_id_get))
                                    {
                                        
                                        $total_count = $topic_id_get2['subtotal_of_count'];
                                        $total_score = $topic_id_get2['subtotal_of_score'];                                        
                                        $weight = $topic_id_get2['weight_value']; 
                                        $topicwscore += ($total_score*$weight);
                                        $sumofweights += ($total_count*$weight);
                                        
                                    }
                                    $GI = ($topicwscore/$sumofweights)*100;
                                    
                                    return $GI;
                                
                                } 
                                else
                                {   
                                    
                                    return 0;                               
                                }                                
                            }
                            
                            
                          }
                          else
                          {
                            if($topic_id == null)
                            {
                                //grasp for all topics for $student_id     
                                $schoolidget = "SELECT t.school_id FROM tbl_auth t where t.user_id='$student_id'";
                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($schoolidget);
                                $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                $schoolid = $topic_id_get2['school_id'];
                                $get_exists_id = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,si.subtotal_of_score,sim.weight_value FROM student_index si,school_index_map sim WHERE sim.school_id='$schoolid' and sim.index_type=1 and si.transaction_id=sim.transaction_type and si.student_id='$student_id' ";
                                if($session_id != null)
                                {
                                   $get_exists_id.= "and si.class_session_id='$session_id'";
                                }                                
                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                if ($exists_rows > 0)
                                {
                                    $sumofweights = 0;
                                    $topicwscore = 0;
                                    while($topic_id_get2 = mysql_fetch_assoc($exists_id_get))
                                    {
                                        
                                        $total_count = $topic_id_get2['subtotal_of_count'];
                                        $total_score = $topic_id_get2['subtotal_of_score'];                                        
                                        $weight = $topic_id_get2['weight_value']; 
                                        $topicwscore += ($total_score*$weight);
                                        $sumofweights += ($total_count*$weight);
                                        
                                    }
                                    $GI = ($topicwscore/$sumofweights)*100;
                                    
                                    return $GI;
                                
                                } 
                                else
                                {   
                                    
                                    return 0;                               
                                }                                                                               
                            }
                            else
                            {
                                //return grasp for $topic_id + $student_id 
                                
                                
                                $get_exists_id = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,si.subtotal_of_score,sim.weight_value FROM student_index si,tbl_auth t,school_index_map sim WHERE si.student_id=t.user_id and t.school_id=sim.school_id and sim.index_type=1 and si.transaction_id=sim.transaction_type and si.topic_id = '$topic_id' and si.student_id='$student_id' ";
                                if($session_id != null)
                                {
                                   $get_exists_id.= "and si.class_session_id='$session_id'";
                                }                                 
                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                if ($exists_rows > 0)
                                {
                                    $sumofweights = 0;
                                    $topicwscore = 0;
                                    while($topic_id_get2 = mysql_fetch_assoc($exists_id_get))
                                    {
                                        
                                        $total_count = $topic_id_get2['subtotal_of_count'];
                                        $total_score = $topic_id_get2['subtotal_of_score'];                                        
                                        $weight = $topic_id_get2['weight_value']; 
                                        $topicwscore += ($total_score*$weight);
                                        $sumofweights += ($total_count*$weight);
                                        
                                    }
                                    $GI = ($topicwscore/$sumofweights)*100;
                                    
                                    return $GI;
                                
                                } 
                                else
                                {   
                                    
                                    return 0;                               
                                }                                
                                
                                
                            }                            
                            
                          }
                        }
                    catch(Exception $e)
                        {
                            return "error";
                        }
                }                   
    
    
   public function ReturnTimeOffset($start_time=null,$school_id=2)
           {
                            $get_time = "SELECT TIME_TO_SEC(timezone) as time FROM schools where school_id='$school_id'";
                                    $get = SunDataBaseManager::getSingleton()->QueryDB($get_time);
                                    $time = mysql_fetch_assoc($get);
                                    $offset = $time['time'];                           
                                     $php_formatted_date = strtotime($start_time);
                                    $php_formatted_date = $php_formatted_date + $offset;
                                    $mysql_formatted_date = strftime("%Y-%m-%d %H:%M:%S", $php_formatted_date) ;
                                    return $mysql_formatted_date;             
                }    
                
      public function SendEmail($message, $to, $cc)
      {
        $mail = new PHPMailer(); // create a new object
        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = SunXMLConst::$EMAIL1;
        $mail->Password = SunXMLConst::$PASS1;
        $mail->SetFrom(SunXMLConst::$EMAIL1);
        $mail->Subject = "New Registration details";
        $mail->Body = $message;
        $mail->AddAddress($to);
        $mail->AddAddress($cc); 
         if(!$mail->Send())
            {
            $message=$mail->ErrorInfo;
            echo $message;
            }        
        
        
        
      }      
} 
?>
