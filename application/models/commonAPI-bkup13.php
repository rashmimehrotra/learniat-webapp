<?php
	require_once ('SunXMLManager.php');
	require_once ('SunDataBaseManager.php');
	require('phpmailer/PHPMailerAutoload.php');
	class commonAPI
		{
			private $mXMLManager;

			public function  __construct()
				{
					$this->mXMLManager = new SunXMLManager();
				}
			
			public function Login($username = null,$password=null,$app_version = null,$device_id=null,$is_teacher=0,$user_id=null,$uuid=null)
				{
					try
						{
							if($app_version == 1.6)
								{

                                    	$get_class = "select role_id from tbl_auth where user_name = '$username'";
    									$class_info = SunDataBaseManager::getSingleton()->QueryDB($get_class);
    									$class = mysql_fetch_assoc($class_info);
    									$exists = $class['role_id'];
                                        if(($exists == 3 && $is_teacher == 1) || ($exists == 4 && $is_teacher == 0))//teacher cant login into student and vice versa
                                        {
									$user_exists = "select user_id from tbl_auth where user_name = '$username'";
									$validate = SunDataBaseManager::getSingleton()->QueryDB($user_exists);
									$count=SunDataBaseManager::getSingleton()->getnoOfrows($validate);
									if ($count>0)
										{
											$user = mysql_fetch_assoc($validate);
											$uid = $user['user_id'];
											$user_active = "select role_id from tbl_auth where user_id = '$uid' and user_active";
											$validate = SunDataBaseManager::getSingleton()->QueryDB($user_active);
											$count=SunDataBaseManager::getSingleton()->getnoOfrows($validate);
											if ($count>0)
												{
													$user_role = mysql_fetch_assoc($validate);
													$role = $user_role['role_id'];
													if($role=='3')
														{
															$validate_password = "select school_id from tbl_auth where user_name = '$username' and password = '$password'";
															$validate = SunDataBaseManager::getSingleton()->QueryDB($validate_password);
															$count=SunDataBaseManager::getSingleton()->getnoOfrows($validate);
															if($count > 0)
																{
																	$user = mysql_fetch_assoc($validate);
																	$school_id = $user['school_id'];
																	$validate_state = "select user_name,user_state from tbl_auth where user_id = '$uid'";// and user_state = '8'";
																	$validate = SunDataBaseManager::getSingleton()->QueryDB($validate_state);
																	$count=SunDataBaseManager::getSingleton()->getnoOfrows($validate);
																	if($count > 0)
																		{
																			$save_device = "insert into devices(user_id,device_id) values('$uid','$device_id')";
																			$save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
																			if ($save)
																				{
                                                                                    $user = mysql_fetch_assoc($validate);
																                	$state = $user['user_state'];																				    
																				    if($state == '1')
                                                                                    {
                                                                                        //app crash:dont change state
                                                                                        $change_state = "update tbl_auth set connected_device_id = '$device_id' where user_id = '$uid'";
    																					$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
    																					if ($change)
    																						{
    																						
																									$arr[SMC::$STATUS] = "Success";
																									$arr[SMC::$USERID] = $uid;
																									$arr[SMC::$SCHOOLID] = $school_id;
                                                                                            }
    	                                                                                        
                                                                                        
                                                                                    }
                                                                                    else
                                                                                    {

																					$change_state = "update tbl_auth set user_state = '7', connected_device_id = '$device_id' where user_id = '$uid'";
																					$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
																					if ($change)
																						{
																							$log_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$uid','8','7')";
																							$logged = SunDataBaseManager::getSingleton()->QueryDB($log_transition);
																							if($logged)
																								{
																									$arr[SMC::$STATUS] = "Success";
																									$arr[SMC::$USERID] = $uid;
																									$arr[SMC::$SCHOOLID] = $school_id;
																								}
																						}                                                                                        
                                                                                        
                                                                                    }
																				}
																		}
																	else
																		{
																			$arr[SMC::$STATUS] = "The user has already logged in on a different device.";
																		}
																}
															else
																{
																	$arr[SMC::$STATUS] = "The password entered is incorrect, please try again.";
																}
														}
													if($role=='4')
														{
															$validate_password = "select school_id from tbl_auth where user_name = '$username' and password = '$password'";
															$validate = SunDataBaseManager::getSingleton()->QueryDB($validate_password);
															$count=SunDataBaseManager::getSingleton()->getnoOfrows($validate);
															if($count > 0)
																{
																	$user = mysql_fetch_assoc($validate);
																	$school_id = $user['school_id'];
																	//$validate_state = "select user_name from tbl_auth where user_id = '$uid' and user_state = '8'";
																        $validate_state = "select user_name from tbl_auth where user_id = '$uid'";
																	$validate = SunDataBaseManager::getSingleton()->QueryDB($validate_state);
																	$count=SunDataBaseManager::getSingleton()->getnoOfrows($validate);
																	if($count > 0)
																		{
																			$save_device = "insert into devices(user_id,device_id) values('$uid','$device_id')";
																			$save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
																			if ($save)
																				{
																					$change_state = "update tbl_auth set user_state = '7', connected_device_id = '$device_id' where user_id = '$uid'";
																					$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
																					if ($change)
																						{
																						    $log_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$uid','8','7')";
																							$logged = SunDataBaseManager::getSingleton()->QueryDB($log_transition);
																							$arr[SMC::$STATUS] = "Success";
																							$arr[SMC::$USERID] = $uid;
																							$arr[SMC::$SCHOOLID] = $school_id;
																						}
																				}
																		}
																	else
																		{
																			$arr[SMC::$STATUS] = "The user has already logged in on a different device.";
																		}
																}
															else
																{
																	$arr[SMC::$STATUS] = "The password entered is incorrect, please try again.";
																}
														}
												}
											else
												{
													$arr[SMC::$STATUS] = "This username is no longer active, please contact your school admin.";
												}
										}
									else
										{
											$arr[SMC::$STATUS] = "Username does not exists, please try again.";
										}
                                        }
                                        else
                                        {
                                            $arr[SMC::$STATUS] = "This Username cant login from this app";
                                        }
								}
							else
								{
									$arr[SMC::$STATUS] = "You are using a different version of app, please update your app to 1.6";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;	
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			
			public function GetMyInfo($user_id=null,$device_id=null,$uuid=null)
				{
					try
						{
							$user_name = "select first_name, last_name from tbl_auth where user_id = '$user_id'";
							$result = SunDataBaseManager::getSingleton()->QueryDB($user_name);
							if($result) 
								{
									$count=SunDataBaseManager::getSingleton()->getnoOfrows($result);
									if($count>0)
										{
											$arr[SMC::$STATUS] = "Success";
											$user_details = mysql_fetch_assoc($result);
											$arr[SMC::$FIRSTNAME] = $user_details['first_name'];
											$arr[SMC::$LASTNAME] = $user_details['last_name'];
											$arr[SMC::$USERID] = $user_id;
										}
									else
										{
											$arr[SMC::$STATUS] = "Could not retrieve info, please try again";
										}
									
									
								}
							else
								{
									$arr[SMC::$STATUS] = "User Id sent is not valid.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;	
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function Logout($user_id=null)
				{
					try
						{
							$validate_state = "select user_name from tbl_auth where user_id = '$user_id' and user_state = '7'";
							$validate = SunDataBaseManager::getSingleton()->QueryDB($validate_state);
							$count=SunDataBaseManager::getSingleton()->getnoOfrows($validate);
							if($count > 0)
								{
									$change_state = "update tbl_auth set user_state = '8', connected_device_id = NULL where user_id = '$user_id'";
									$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
									if ($change)
										{
											$log_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$user_id','7','8')";
											$logged = SunDataBaseManager::getSingleton()->QueryDB($log_transition);
											if($logged)
												{
													$arr[SMC::$STATUS] = "Success";
												}
										}
								}
							else
								{
									$arr[SMC::$STATUS] = "Sorry, you are not eligible to logout yet";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;	
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
		
			public function GetMyCurrentSession($user_id=null)
				{
					try
						{
						  
                          
                            //if cancelled or ended, remove from live_sessions
                            $session_details51 = "select term.class_session_id from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id where term.ends_on < now() and (term.session_state=5 or term.session_state=6) order by term.starts_on desc";                            
                            $details9=SunDataBaseManager::getSingleton()->QueryDB($session_details51);
                            $count71 = SunDataBaseManager::getSingleton()->getnoOfrows($details9);
                            if($count71 > 0)
                            {
                                while($details7 = mysql_fetch_assoc($details9))
                                {
                                    $sess1= $details7['class_session_id'];
                                         //delete live_session
													$delete = "delete from live_session_status where session_id = '$sess1'";
													$deleted = SunDataBaseManager::getSingleton()->QueryDB($delete);                                       
                                    
                                }
                                
                                
                            }                          
                            //if end time<now(), end it
                            $session_details5 = "select term.class_session_id from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id where term.ends_on < now() and term.session_state=1 and TIME_TO_SEC(TIMEDIFF(term.ends_on,now())+0) < 3600 order by term.starts_on desc";                            
                            $details9=SunDataBaseManager::getSingleton()->QueryDB($session_details5);
                            $count7 = SunDataBaseManager::getSingleton()->getnoOfrows($details9);
                            if($count7 > 0)
                            {
                                while($details7 = mysql_fetch_assoc($details9))
                                {
                                    $sess= $details7['class_session_id'];
                                                    //change student
                                                    $getstudents = "select map.student_id,tbl_auth.user_state from student_class_map as map inner join class_sessions as session on map.class_id = session.class_id inner join tbl_auth on tbl_auth.user_id=map.student_id  where session.class_session_id = '$sess'";                                                    
                									$s_state = SunDataBaseManager::getSingleton()->QueryDB($getstudents);
                									while($cis_state = mysql_fetch_assoc($s_state))
                                                    {
                      									$s_id = $cis_state['student_id'];   
                                                        $s_state = $cis_state['user_state'];
                                                        if($s_state != null && ($s_state == '1' || $s_state == '9' || $s_state == '11' || $s_state == '21'))
                                                        {
                                                            if($s_state != '7')
                                                            {
                                     							$update = "update tbl_auth set user_state = '7' where user_id = '$s_id'";
                               							        $updated = SunDataBaseManager::getSingleton()->QueryDB($update);         
                        	                                    $log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$s_id',$s_state,'7')";
                                                                $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);                                                                                                                              
                                                            }
                                                        }                                            
                                                    }                            
                                                    //close questions
                                                    $getstudents = "select question_log_id from question_log where class_session_id = '$sess'";                                                    
                									$s_state = SunDataBaseManager::getSingleton()->QueryDB($getstudents);
                									while($cis_state = mysql_fetch_assoc($s_state))
                                                    {      
                                                        $question_log_id = $cis_state['question_log_id']; 
                            							$change_state = "update question_log set active_question = '0', end_time = NOW() where question_log_id = '$question_log_id'";
                            							$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);                                                        
                                                        
                                                     } 
                                                  
                                                    //query to 17
                                                    $getstudents = "select query_id,state from student_query where class_session_id = '$sess'";                                                    
                									$s_state = SunDataBaseManager::getSingleton()->QueryDB($getstudents);
                									while($cis_state = mysql_fetch_assoc($s_state))
                                                    {
                      									$query_id = $cis_state['query_id'];   
                                                        $s_state = $cis_state['state'];
                                                        if($s_state != null && ($s_state == '16' || $s_state == '18'))
                                                        {
                                                            if($s_state != '17')
                                                            {           
                                     							$change_state = "update student_query set state = '17' where query_id = '$query_id'";
                                    							$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
                                    							if ($change)
                                    								{
                                    									$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$query_id',$s_state,'17')";
                                    									$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
                                    								} 
                                                                    
                                                                }                                                               
                                                                
                                                        }
                                                    }                                                             
                        							$update_session_state = "update class_sessions set session_state = '5' where class_session_id = '$sess'";
                        							$updated_session_state = SunDataBaseManager::getSingleton()->QueryDB($update_session_state);
                                                
                    							    if($updated_session_state)
                    								{
                    									$log_session_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('2','$sess','1','5')";
                    									$logged_session_transition = SunDataBaseManager::getSingleton()->QueryDB($log_session_transition);
                    								}   
                                                    
                                         //delete live_session
													$delete = "delete from live_session_status where session_id = '$sess'";
													$deleted = SunDataBaseManager::getSingleton()->QueryDB($delete); 
                                                                                                         
                                        //teacher to ended as well
       									$s_state = "select class_sessions.teacher_id,tbl_auth.user_state  from class_sessions, tbl_auth where user_id = class_sessions.teacher_id and class_sessions.class_session_id ='$sess'";
    									$r_state = SunDataBaseManager::getSingleton()->QueryDB($s_state);        
                                        $cs_state = mysql_fetch_assoc($r_state);
                                        $o_tid = $cs_state['teacher_id'];   
                                        $o_tstate = $cs_state['user_state'];  
                                        if($o_tstate != '7')
                                        {
                    							$update = "update tbl_auth set user_state = '7' where user_id = '$o_tid'";
               							        $updated = SunDataBaseManager::getSingleton()->QueryDB($update);         
        	                                    $log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$o_tid',$o_tstate,'7')";
                                                $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);                                                                       
                                            
                                        }                                                                                       
                                    
                                }      
                            }
						  //older sessions to cancelled
                            $session_details = "select term.class_session_id,term.session_state from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id where term.ends_on < now() and (term.session_state=2 or term.session_state=4) and TIME_TO_SEC(TIMEDIFF(term.ends_on,now())+0) < 3600 order by term.starts_on desc";
                           // $session_details = "select term.class_session_id, term.starts_on, term.ends_on, state.state_id, user.first_name, user.user_id, class.class_id, class.class_name, room.room_name, room.room_id, subject.subject_id, subject.subject_name from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id inner join classes as class on term.teacher_id = class.teacher_id inner join rooms as room on term.room_id = room.room_id inner join subjects as subject on subject.subject_id = class.subject_id inner join entity_states as state on term.session_state = state.state_id where term.teacher_id = '$user_id' and date(term.starts_on) < curdate() AND term.class_id = class.class_id";
							$details=SunDataBaseManager::getSingleton()->QueryDB($session_details);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($details);
							if($count>0)
								{
								    
									$arr[SMC::$STATUS] = "Success";
									$i = 0;
									while($detail = mysql_fetch_assoc($details))
										{
										  
                                          $new_state = $detail['session_state'];
                                          $session_id = $detail['class_session_id'];
                                            //change state to cancelled
                                                
                                            //change state to cancelled
                                               if(($detail['session_state'] =="4") || ($detail['session_state'] =="2"))
                                                {
                                                    $new_state = "6";
                        							$update_session_state = "update class_sessions set session_state = '6' where class_session_id = '$session_id'";
                        							$updated_session_state = SunDataBaseManager::getSingleton()->QueryDB($update_session_state);
                                                    $state = "";
                                                    if($detail['session_state'] == "4")
                                                    {
                                                        $state = "4";
                                                    }
                                                    else
                                                    {
                                                        $state = "2";
                                                    }
                                                
                    							    if($updated_session_state)
                    								{
                    									$log_session_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('2','$session_id','$state','6')";
                    									$logged_session_transition = SunDataBaseManager::getSingleton()->QueryDB($log_session_transition);
                    								}
                                                }
                                        }
                                }	                            
                            $session_details1 = "select term.starts_on, term.session_state, term.class_session_id from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id inner join classes as class on term.teacher_id = class.teacher_id inner join rooms as room on term.room_id = room.room_id inner join subjects as subject on subject.subject_id = class.subject_id where term.teacher_id = '$user_id' and term.starts_on > now() AND date(term.starts_on) =DATE(NOW()) AND term.class_id = class.class_id and term.session_state !=6 order by term.starts_on asc limit 0,1";
						    $details2=SunDataBaseManager::getSingleton()->QueryDB($session_details1);
                            $details1 = mysql_fetch_assoc($details2);                          
							$session_details = "select term.class_session_id, term.starts_on, user.first_name, class.class_id,room.room_id, class.class_name, room.room_name, subject.subject_id, subject.subject_name,term.session_state from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id inner join classes as class on term.teacher_id = class.teacher_id inner join rooms as room on term.room_id = room.room_id inner join subjects as subject on subject.subject_id = class.subject_id where term.teacher_id = '$user_id' and term.starts_on < now() and term.ends_on > now() AND term.class_id = class.class_id AND term.session_state !=6 order by term.starts_on desc limit 0,1";
							$details=SunDataBaseManager::getSingleton()->QueryDB($session_details);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($details);
                            $nosession = 0;
								    //get school
															$validate_password = "select school_id from tbl_auth where user_id = '$user_id'";
															$validate = SunDataBaseManager::getSingleton()->QueryDB($validate_password);
															
																	$user = mysql_fetch_assoc($validate);
																	$school_id = $user['school_id'];  
							if($count>0)
								{
                                  
                                    
                                    
    									    $arr[SMC::$STATUS] = "Success";								    
									        $detail = mysql_fetch_assoc($details);
        									$arr[SMC::$SESSIONID] = $detail['class_session_id'];
        									$arr[SMC::$STARTTIME] = $this->mXMLManager->ReturnTimeOffset($detail['starts_on'],$school_id);
        									$arr[SMC::$TEACHERNAME] = $detail['first_name'];
        									$arr[SMC::$CLASSID] = $detail['class_id'];
        									$arr[SMC::$CLASSNAME] = $detail['class_name'];
                                            $arr[SMC::$ROOMID] = $detail['room_id'];
        									$arr[SMC::$ROOMNAME] = $detail['room_name'];
        									$arr[SMC::$SUBJECTID] = $detail['subject_id'];
                                            $arr[SMC::$SUBJECTNAME] = $detail['subject_name'];
                                            $arr[SMC::$SESSIONSTATE] = $detail['session_state'];
                                            $session_id = $detail['class_session_id'];
                 							$seat_info = "select seat.seat_id, assign.seat_state, state.state_description, session.starts_on from seats as seat inner join class_sessions as session on session.room_id = seat.room_id left join seat_assignments as assign on seat.seat_id = assign.seat_id and assign.class_session_id = '$session_id' left join entity_states as state on session.session_state = state.state_id where session.class_session_id = '$session_id'";
                							$info = SunDataBaseManager::getSingleton()->QueryDB($seat_info);
                							$configured_count = SunDataBaseManager::getSingleton()->getnoOfrows($info);
                							$preallocated_count = 0;
                                            $occupied_count = 0;
                							while($details3 = mysql_fetch_assoc($info))
                								{
                									$state = $details3['seat_state'];
                									$session_status = $details3['state_description'];
                									$start_time = $details3['starts_on'];
                									if ($state == '9')
                										{
                											$preallocated_count++;
                										}
                									if ($state == '10')
                										{
                											$occupied_count++;
                										}                                                        
                								}
                							$students_registered = "select map.student_id from student_class_map as map inner join class_sessions as session on map.class_id = session.class_id where session.class_session_id = '$session_id'";
                							$get_registered = SunDataBaseManager::getSingleton()->QueryDB($students_registered);
                							$registered_count = SunDataBaseManager::getSingleton()->getnoOfrows($get_registered);
                							
                
                							$arr[SMC::$SEATSCONFIGURED] = $configured_count;
                							$arr[SMC::$STUDENTSREGISTERED] = $registered_count;
                							$arr[SMC::$PREALLOCATEDSEATS] = $preallocated_count; 
                                            $arr[SMC::$OCCUPIEDSEATS] = $occupied_count;                                          
                                            
                                            
                                            
                                        
								}
							else
								{
								    $nosession = 1;
									$arr[SMC::$STATUS] = "There are no active sessions.";
								}
                                if(empty($details1))
                                {
                                    $arr[SMC::$NEXTSESSIONTIME] = null;
                                    $arr[SMC::$NEXTSESSIONSTATE] = null;
                                    $arr[SMC::$NEXTSESSIONID] = null;                                    
                                    
                                }
                                else
                                {
                                    $arr[SMC::$NEXTSESSIONTIME] = $this->mXMLManager->ReturnTimeOffset($details1['starts_on'],$school_id);
                                    $arr[SMC::$NEXTSESSIONSTATE] = $details1['session_state'];
                                    $arr[SMC::$NEXTSESSIONID] = $details1['class_session_id'];
                                    
                                }

                                if($nosession == 1 && !empty($details1))
                                {
                                    $session_id =$details1['class_session_id'];
                 							$seat_info = "select seat.seat_id, assign.seat_state, state.state_description, session.starts_on from seats as seat inner join class_sessions as session on session.room_id = seat.room_id left join seat_assignments as assign on seat.seat_id = assign.seat_id and assign.class_session_id = '$session_id' left join entity_states as state on session.session_state = state.state_id where session.class_session_id = '$session_id'";
                							$info = SunDataBaseManager::getSingleton()->QueryDB($seat_info);
                							$configured_count = SunDataBaseManager::getSingleton()->getnoOfrows($info);
                							$preallocated_count = 0;
                                            $occupied_count = 0;
                							while($details3 = mysql_fetch_assoc($info))
                								{
                									$state = $details3['seat_state'];
                									$session_status = $details3['state_description'];
                									$start_time = $details3['starts_on'];
                									if ($state == '9')
                										{
                											$preallocated_count++;
                										}
                									if ($state == '10')
                										{
                											$occupied_count++;
                										}                                                           
                								}
                							$students_registered = "select map.student_id from student_class_map as map inner join class_sessions as session on map.class_id = session.class_id where session.class_session_id = '$session_id'";
                							$get_registered = SunDataBaseManager::getSingleton()->QueryDB($students_registered);
                							$registered_count = SunDataBaseManager::getSingleton()->getnoOfrows($get_registered);
                							
                
                							$arr[SMC::$SEATSCONFIGURED] = $configured_count;
                							$arr[SMC::$STUDENTSREGISTERED] = $registered_count;
                							$arr[SMC::$PREALLOCATEDSEATS] = $preallocated_count;   
                                            $arr[SMC::$OCCUPIEDSEATS] = $occupied_count;                                      
                                    
                                }                                            
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function GetMyNextSession($user_id=null)
				{
					try
						{
							$session_details = "select term.class_session_id, term.starts_on, term.ends_on, user.first_name, class.class_id, class.class_name, room.room_name, subject.subject_id, subject.subject_name from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id inner join classes as class on term.teacher_id = class.teacher_id inner join rooms as room on term.room_id = room.room_id inner join subjects as subject on subject.subject_id = class.subject_id where term.teacher_id = '$user_id' and term.starts_on > now() and term.session_state !=6 order by term.starts_on limit 1";
							$details=SunDataBaseManager::getSingleton()->QueryDB($session_details);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($details);
							if($count>0)
								{
								    //get school
															$validate_password = "select school_id from tbl_auth where user_id = '$user_id'";
															$validate = SunDataBaseManager::getSingleton()->QueryDB($validate_password);
															
																	$user = mysql_fetch_assoc($validate);
																	$school_id = $user['school_id'];      								    
									$detail = mysql_fetch_assoc($details);
									$arr[SMC::$STATUS] = "Success";
									$arr[SMC::$SESSIONID] = $detail['class_session_id']; 
									$arr[SMC::$STARTTIME] = $this->mXMLManager->ReturnTimeOffset($detail['starts_on'],$school_id);                                   
									$arr[SMC::$ENDTIME] = $this->mXMLManager->ReturnTimeOffset($detail['ends_on'],$school_id);
									$arr[SMC::$TEACHERNAME] = $detail['first_name'];
									$arr[SMC::$CLASSID] = $detail['class_id'];
									$arr[SMC::$CLASSNAME] = $detail['class_name'];
									$arr[SMC::$ROOMNAME] = $detail['room_name'];
									$arr[SMC::$SUBJECTID] = $detail['subject_id'];
									$arr[SMC::$SUBJECTNAME] = $detail['subject_name'];
								}
							else
								{
									$arr[SMC::$STATUS] = "There are no more sessions for the day.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function GetMyTodaysSessions($user_id=null)
				{
					try
						{	
                            //if end time<now(), end it
                            $session_details51 = "select term.class_session_id from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id where term.ends_on < now() and (term.session_state=5 or term.session_state=6) order by term.starts_on desc";                            
                            $details9=SunDataBaseManager::getSingleton()->QueryDB($session_details51);
                            $count71 = SunDataBaseManager::getSingleton()->getnoOfrows($details9);
                            if($count71 > 0)
                            {
                                while($details7 = mysql_fetch_assoc($details9))
                                {
                                    $sess1= $details7['class_session_id'];
                                         //delete live_session
													$delete = "delete from live_session_status where session_id = '$sess1'";
													$deleted = SunDataBaseManager::getSingleton()->QueryDB($delete);                                       
                                    
                                }
                                
                                
                            }

                            //if end time<now(), end it
                            $session_details5 = "select term.class_session_id from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id where term.ends_on < now() and term.session_state=1 and TIME_TO_SEC(TIMEDIFF(term.ends_on,now())+0) < 3600 order by term.starts_on desc";                            
                            $details9=SunDataBaseManager::getSingleton()->QueryDB($session_details5);
                            $count7 = SunDataBaseManager::getSingleton()->getnoOfrows($details9);
                            if($count7 > 0)
                            {
                                while($details7 = mysql_fetch_assoc($details9))
                                {
                                    $sess= $details7['class_session_id'];
                                                    //change student
                                                    $getstudents = "select map.student_id,tbl_auth.user_state from student_class_map as map inner join class_sessions as session on map.class_id = session.class_id inner join tbl_auth on tbl_auth.user_id=map.student_id  where session.class_session_id = '$sess'";                                                    
                									$s_state = SunDataBaseManager::getSingleton()->QueryDB($getstudents);
                									while($cis_state = mysql_fetch_assoc($s_state))
                                                    {
                      									$s_id = $cis_state['student_id'];   
                                                        $s_state = $cis_state['user_state'];
                                                        if($s_state != null && ($s_state == '1' || $s_state == '9' || $s_state == '11' || $s_state == '21'))
                                                        {
                                                            if($s_state != '7')
                                                            {
                                     							$update = "update tbl_auth set user_state = '7' where user_id = '$s_id'";
                               							        $updated = SunDataBaseManager::getSingleton()->QueryDB($update);         
                        	                                    $log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$s_id',$s_state,'7')";
                                                                $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);                                                                                                                              
                                                            }
                                                        }                                            
                                                    }           
                                                    //close questions
                                                    $getstudents = "select question_log_id from question_log where class_session_id = '$sess'";                                                    
                									$s_state = SunDataBaseManager::getSingleton()->QueryDB($getstudents);
                									while($cis_state = mysql_fetch_assoc($s_state))
                                                    {      
                                                        $question_log_id = $cis_state['question_log_id']; 
                            							$change_state = "update question_log set active_question = '0', end_time = NOW() where question_log_id = '$question_log_id'";
                            							$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);                                                        
                                                        
                                                     }                                                       
                                                    //query to 17
                                                    $getstudents = "select query_id,state from student_query where class_session_id = '$sess'";                                                    
                									$s_state = SunDataBaseManager::getSingleton()->QueryDB($getstudents);
                									while($cis_state = mysql_fetch_assoc($s_state))
                                                    {
                      									$query_id = $cis_state['query_id'];   
                                                        $s_state = $cis_state['state'];
                                                        if($s_state != null && ($s_state == '16' || $s_state == '18'))
                                                        {
                                                            if($s_state != '17')
                                                            {           
                                     							$change_state = "update student_query set state = '17' where query_id = '$query_id'";
                                    							$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
                                    							if ($change)
                                    								{
                                    									$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$query_id',$s_state,'17')";
                                    									$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
                                    								} 
                                                                    
                                                                }                                                               
                                                                
                                                        }
                                                    }   
                                                                                                         

                                                    
                                                   
                                                                               
                        							$update_session_state = "update class_sessions set session_state = '5' where class_session_id = '$sess'";
                        							$updated_session_state = SunDataBaseManager::getSingleton()->QueryDB($update_session_state);
                                                
                    							    if($updated_session_state)
                    								{
                    									$log_session_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('2','$sess','1','5')";
                    									$logged_session_transition = SunDataBaseManager::getSingleton()->QueryDB($log_session_transition);
                    								}        
                                                    
                                                    
                                         //delete live_session
													$delete = "delete from live_session_status where session_id = '$sess'";
													$deleted = SunDataBaseManager::getSingleton()->QueryDB($delete);                                                    
                                        //teacher to ended as well
       									$s_state = "select class_sessions.teacher_id,tbl_auth.user_state  from class_sessions, tbl_auth where user_id = class_sessions.teacher_id and class_sessions.class_session_id ='$sess'";
    									$r_state = SunDataBaseManager::getSingleton()->QueryDB($s_state);        
                                        $cs_state = mysql_fetch_assoc($r_state);
                                        $o_tid = $cs_state['teacher_id'];   
                                        $o_tstate = $cs_state['user_state'];  
                                        if($o_tstate != '7')
                                        {
                    							$update = "update tbl_auth set user_state = '7' where user_id = '$o_tid'";
               							        $updated = SunDataBaseManager::getSingleton()->QueryDB($update);         
        	                                    $log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$o_tid',$o_tstate,'7')";
                                                $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);                                                                       
                                            
                                        }                                                                               
                                    
                                }      
                            }						  
						  //older sessions to cancelled
                            $session_details = "select term.class_session_id,term.session_state from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id where term.ends_on < now() and (term.session_state=2 or term.session_state=4) and TIME_TO_SEC(TIMEDIFF(term.ends_on,now())+0) < 3600 order by term.starts_on desc";
                           // $session_details = "select term.class_session_id, term.starts_on, term.ends_on, state.state_id, user.first_name, user.user_id, class.class_id, class.class_name, room.room_name, room.room_id, subject.subject_id, subject.subject_name from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id inner join classes as class on term.teacher_id = class.teacher_id inner join rooms as room on term.room_id = room.room_id inner join subjects as subject on subject.subject_id = class.subject_id inner join entity_states as state on term.session_state = state.state_id where term.teacher_id = '$user_id' and date(term.starts_on) < curdate() AND term.class_id = class.class_id";
							$details=SunDataBaseManager::getSingleton()->QueryDB($session_details);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($details);
							if($count>0)
								{
								    
									$arr[SMC::$STATUS] = "Success";
									$i = 0;
									while($detail = mysql_fetch_assoc($details))
										{
										  
                                          $new_state = $detail['session_state'];
                                          $session_id = $detail['class_session_id'];
                                            //change state to cancelled
                                                
                                            //change state to cancelled
                                               if(($detail['session_state'] =="4") || ($detail['session_state'] =="2"))
                                                {
                                                    $new_state = "6";
                        							$update_session_state = "update class_sessions set session_state = '6' where class_session_id = '$session_id'";
                        							$updated_session_state = SunDataBaseManager::getSingleton()->QueryDB($update_session_state);
                                                    $state = "";
                                                    if($detail['session_state'] == "4")
                                                    {
                                                        $state = "4";
                                                    }
                                                    else
                                                    {
                                                        $state = "2";
                                                    }
                                                
                    							    if($updated_session_state)
                    								{
                    									$log_session_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('2','$session_id','$state','6')";
                    									$logged_session_transition = SunDataBaseManager::getSingleton()->QueryDB($log_session_transition);
                    								}
                                                }
                                        }
                                }										  
						  
                          //today's tobe cancelled'
							$session_details = "select term.class_session_id, term.starts_on, term.ends_on, state.state_id, user.first_name, user.user_id, class.class_id, class.class_name, room.room_name, room.room_id, subject.subject_id, subject.subject_name,schools.timezone from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id inner join classes as class on term.teacher_id = class.teacher_id inner join rooms as room on term.room_id = room.room_id inner join subjects as subject on subject.subject_id = class.subject_id inner join entity_states as state on term.session_state = state.state_id inner join schools on user.school_id=schools.school_id where term.teacher_id = '$user_id' and date(term.starts_on) = curdate() AND term.class_id = class.class_id";
							$details=SunDataBaseManager::getSingleton()->QueryDB($session_details);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($details);
							if($count>0)
								{
							$session_details1 = "SELECT now() as time from dual";
							$details1=SunDataBaseManager::getSingleton()->QueryDB($session_details1);
                            $detail1 = mysql_fetch_assoc($details1);
                            $time = $detail1['time'];
								    
									$arr[SMC::$STATUS] = "Success";
									$i = 0;
									while($detail = mysql_fetch_assoc($details))
										{
										  
                                          $new_state = $detail['state_id'];
                                          $session_id = $detail['class_session_id'];
                                            //change state to cancelled
                                                
                                                // first DateTime object created based on the MySQL datetime format
                                            $dt1 = DateTime::createFromFormat('Y-m-d H:i:s', $detail['ends_on']);
                                            
                                            // second DateTime object created based on the MySQL datetime format
                                            $dt2 = DateTime::createFromFormat('Y-m-d H:i:s', $time);
                                               //change state to cancelled
                                               if(($dt1->format('U') < $dt2->format('U')) && (($detail['state_id'] =="4") || ($detail['state_id'] =="2")))
                                                {
                                                    $new_state = "6";
                        							$update_session_state = "update class_sessions set session_state = '6' where class_session_id = '$session_id'";
                        							$updated_session_state = SunDataBaseManager::getSingleton()->QueryDB($update_session_state);
                                                    $state = "";
                                                    if($detail['state_id'] == "4")
                                                    {
                                                        $state = "4";
                                                    }
                                                    else
                                                    {
                                                        $state = "2";
                                                    }
                                                
                    							    if($updated_session_state)
                    								{
                    									$log_session_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('2','$session_id','$state','6')";
                    									$logged_session_transition = SunDataBaseManager::getSingleton()->QueryDB($log_session_transition);
                    								}
                                                }						
								    //get school
															$validate_password = "select school_id from tbl_auth where user_id = '$user_id'";
															$validate = SunDataBaseManager::getSingleton()->QueryDB($validate_password);
															
																	$user = mysql_fetch_assoc($validate);
																	$school_id = $user['school_id'];                                                 				  
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID] = $detail['class_session_id'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$STARTTIME] = $this->mXMLManager->ReturnTimeOffset($detail['starts_on'],$school_id);
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$ENDTIME] = $this->mXMLManager->ReturnTimeOffset($detail['ends_on'],$school_id);
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONSTATE] = $new_state;
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$TEACHERNAME] = $detail['first_name'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$TEACHERID] = $detail['user_id'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$CLASSID] = $detail['class_id'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$CLASSNAME] = $detail['class_name'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$ROOMID] = $detail['room_id'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$ROOMNAME] = $detail['room_name'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SUBJECTID] = $detail['subject_id'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SUBJECTNAME] = $detail['subject_name'];
                                            $session_id = $detail['class_session_id'];
                 							$seat_info = "select seat.seat_id, assign.seat_state, state.state_description, session.starts_on from seats as seat inner join class_sessions as session on session.room_id = seat.room_id left join seat_assignments as assign on seat.seat_id = assign.seat_id and assign.class_session_id = '$session_id' left join entity_states as state on session.session_state = state.state_id where session.class_session_id = '$session_id'";
                							$info = SunDataBaseManager::getSingleton()->QueryDB($seat_info);
                							$configured_count = SunDataBaseManager::getSingleton()->getnoOfrows($info);
                							$preallocated_count = 0;
                                            $occupied_count = 0;
                							while($details2 = mysql_fetch_assoc($info))
                								{
                									$state = $details2['seat_state'];
                									$session_status = $details2['state_description'];
                									$start_time = $details2['starts_on'];
                									if ($state == '9')
                										{
                											$preallocated_count++;
                										}
                									if ($state == '10')
                										{
                											$occupied_count++;
                										}                                                        
                								}
                							$students_registered = "select map.student_id from student_class_map as map inner join class_sessions as session on map.class_id = session.class_id where session.class_session_id = '$session_id'";
                							$get_registered = SunDataBaseManager::getSingleton()->QueryDB($students_registered);
                							$registered_count = SunDataBaseManager::getSingleton()->getnoOfrows($get_registered);

                							$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SEATSCONFIGURED] = $configured_count;
                							$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$STUDENTSREGISTERED] = $registered_count;
                							$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$PREALLOCATEDSEATS] = $preallocated_count;
                                            $arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$OCCUPIEDSEATS] = $occupied_count;
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$TIMEZONE] = $detail['timezone'];                                             
											$i++;
										}
								}
							else
								{
									$arr[SMC::$STATUS] = "You do not have any sessions today.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function UpdateSessionState($session_id=null,$status_id=null)
				{
					try
						{
							$session_state = "select session_state from class_sessions where class_session_id = '$session_id'";
							$retrieve_session_state = SunDataBaseManager::getSingleton()->QueryDB($session_state);
							$current_session_state = mysql_fetch_assoc($retrieve_session_state);
							$old_session_state = $current_session_state['session_state'];
                            
                            if($status_id == "2") //if state is opened
                            {
       									$s_state = "select seat_assignments.seat_id, seat_assignments.seat_state,tbl_auth.user_state, tbl_auth.user_id  from seat_assignments,tbl_auth where tbl_auth.user_id = seat_assignments.student_id and class_session_id = '$session_id'";
    									$r_state = SunDataBaseManager::getSingleton()->QueryDB($s_state);
    									while($cs_state = mysql_fetch_assoc($r_state))
                                        {
          									$o_seat = $cs_state['seat_id'];   
                                            $o_state = $cs_state['seat_state']; 
                                            $st_state = $cs_state['user_state']; 
                                            $st_id = $cs_state['user_id']; 
                                            if($o_seat != null && $st_state == '7')
                                            {
                    							$update = "update tbl_auth set user_state = '9' where user_id = '$st_id'";
               							        $updated = SunDataBaseManager::getSingleton()->QueryDB($update);         
        	                                    $log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$st_id',$st_state,'9')";
                                                $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);                                                                       
                                            }                                            
                                        }
                              
                            }
                            if($status_id == "4") //if state is scheduled
                            {
                                //PUSH notification
                              
                            }                            
							if ($status_id == "1" or $status_id == "5" or $status_id == "6")
								{
									$get_teacher = "select teacher_id from class_sessions where class_session_id = '$session_id'";
									$teacher = SunDataBaseManager::getSingleton()->QueryDB($get_teacher);
									$user = mysql_fetch_assoc($teacher);
									$user_id = $user['teacher_id'];
									$get_room = "select room_id from class_sessions where class_session_id = '$session_id'";
									$rooms = SunDataBaseManager::getSingleton()->QueryDB($get_room);
									$room = mysql_fetch_assoc($rooms);
									$room_id = $room['room_id'];
									if ($status_id == "5" or $status_id == "6")
										{
											$state = 7;
                                            if($status_id == "6")
                                            {
                                               $free_seats = "delete from seat_assignments where class_session_id = '$session_id'";
                                               $seat_freed = SunDataBaseManager::getSingleton()->QueryDB($free_seats);    
                                                
                                            }
											if ($status_id == "5")
												{
													$delete = "delete from live_session_status where session_id = '$session_id'";
													$deleted = SunDataBaseManager::getSingleton()->QueryDB($delete);
													$get_class = "select class_id from class_sessions where class_session_id = '$session_id'";
													$class_info = SunDataBaseManager::getSingleton()->QueryDB($get_class);
													$class = mysql_fetch_assoc($class_info);
													$class_id = $class['class_id'];
                                                    $getstudents = "select map.student_id,tbl_auth.user_state from student_class_map as map inner join class_sessions as session on map.class_id = session.class_id inner join tbl_auth on tbl_auth.user_id=map.student_id  where session.class_session_id = '$session_id'";                                                    
                									$s_state = SunDataBaseManager::getSingleton()->QueryDB($getstudents);
                									while($cis_state = mysql_fetch_assoc($s_state))
                                                    {
                      									$s_id = $cis_state['student_id'];
                                                        $s_state = $cis_state['user_state'];
                                                        if($s_state != null && ($s_state == '1' || $s_state == '9' || $s_state == '11' || $s_state == '21'))
                                                        {
                                                            if($s_state != '7')
                                                            {
                                     							$update = "update tbl_auth set user_state = '7' where user_id = '$s_id'";
                               							        $updated = SunDataBaseManager::getSingleton()->QueryDB($update);         
                        	                                    $log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$s_id',$s_state,'7')";
                                                                $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);                                                                                                                              
                                                            }
                                                        }                                            
                                                    } 
                                                    //close questions
                                                    $getstudents = "select question_log_id from question_log where class_session_id = '$session_id'";                                                    
                									$s_state = SunDataBaseManager::getSingleton()->QueryDB($getstudents);
                									while($cis_state = mysql_fetch_assoc($s_state))
                                                    {      
                                                        $question_log_id = $cis_state['question_log_id']; 
                            							$change_state = "update question_log set active_question = '0', end_time = NOW() where question_log_id = '$question_log_id'";
                            							$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);                                                        
                                                        
                                                     }                                                     
                                                    //query to 17
                                                    $getstudents = "select query_id,state from student_query where class_session_id = '$session_id'";                                                    
                									$s_state = SunDataBaseManager::getSingleton()->QueryDB($getstudents);
                									while($cis_state = mysql_fetch_assoc($s_state))
                                                    {
                      									$query_id = $cis_state['query_id'];   
                                                        $s_state = $cis_state['state'];
                                                        if($s_state != null && ($s_state == '16' || $s_state == '18'))
                                                        {
                                                            if($s_state != '17')
                                                            {           
                                     							$change_state = "update student_query set state = '17' where query_id = '$query_id'";
                                    							$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
                                    							if ($change)
                                    								{
                                    									$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$query_id',$s_state,'17')";
                                    									$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
                                    								} 
                                                                    
                                                                }                                                               
                                                                
                                                        }
                                                    }                                                    
													
                                                    $get_oldtime = "select topic_id, cumulative_time, transition_time, state from topic_log where class_id = '$class_id' order by transition_time desc limit 1";
													$got_oldtime = SunDataBaseManager::getSingleton()->QueryDB($get_oldtime);
													$oldrecord = mysql_fetch_assoc($got_oldtime);
													$topic_id = $oldrecord['topic_id'];
													$cumulative_time = $oldrecord['cumulative_time'];
													$start_time = $oldrecord['transition_time'];
													$topic_state = $oldrecord['state'];
													$select_tst="select topic_session_time,transition_time from topic_log where topic_id='$topic_id' and class_session_id='$session_id' order by transition_time desc limit 1";
													$tst_query=SunDataBaseManager::getSingleton()->QueryDB($select_tst);
													$tst_fetch=mysql_fetch_assoc($tst_query);
													$tpc_session_time= $tst_fetch['topic_session_time'];
													$trans_time2=$tst_fetch['transition_time'];
													if ($topic_state == "22")
														{
															$select_curr_time="select current_timestamp";
															$curr_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_curr_time);
															$curr_time_fetch=mysql_fetch_assoc($curr_time_query);
															$curr_time=$curr_time_fetch['current_timestamp'];
															$started_time = strtotime($start_time);
															$current_time = strtotime($curr_time);
															$time_difference = $current_time - $started_time;
															$cumulative_time += $time_difference;
															$topic_session_time=$tpc_session_time+(strtotime($curr_time)-strtotime($trans_time2));
															$log_topic = "insert into topic_log (class_id, topic_id, class_session_id, cumulative_time,topic_session_time, state) values ('$class_id','$topic_id','$session_id','$cumulative_time','$topic_session_time','23')";
															$logged_topic = SunDataBaseManager::getSingleton()->QueryDB($log_topic);
														}
                                                        $attendsc = 0;
													$get_attend = "SELECT distinct `entity_id` as student_id FROM `state_transitions` WHERE `from_state`=1 and `to_state`=7 and `entity_id` in (select map.student_id from student_class_map as map inner join class_sessions as session on map.class_id = session.class_id  where session.class_session_id = '$session_id') and transition_time > (SELECT `starts_on` FROM `class_sessions` WHERE `class_session_id`='$session_id') and transition_time < (SELECT `ends_on` FROM `class_sessions` WHERE `class_session_id`='$session_id')";
													$attend= SunDataBaseManager::getSingleton()->QueryDB($get_attend);
													$attends = mysql_fetch_assoc($attend);
													$attendsc = $attends['attend'];
													$reset_time = "update class_sessions set ends_on = now(),stud_attended='$attendsc' where class_session_id = '$session_id'";
   							                		$reset = SunDataBaseManager::getSingleton()->QueryDB($reset_time);

												/*	$retrieve_student_seats = "select seat_id, seat_state from seat_assignments where class_session_id = '$session_id'";
													$student_seats_retrieved = SunDataBaseManager::getSingleton()->QueryDB($retrieve_student_seats);
													while($seats = mysql_fetch_assoc($student_seats_retrieved))
														{
															$seat = $seats['seat_id'];
															$seat_state = $seats['seat_state'];
															$log_seat_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('4','$seat','$seat_state','7')";
															$logged_seat_transition = SunDataBaseManager::getSingleton()->QueryDB($log_seat_transition);
														}*/
													$free_seats = "delete from seat_assignments where class_session_id = '$session_id'";
													$seat_freed = SunDataBaseManager::getSingleton()->QueryDB($free_seats);
                                                    
                                                    //update if new state is ended
       									$s_state = "select seat_assignments.seat_id, seat_assignments.seat_state,tbl_auth.user_state, tbl_auth.user_id  from seat_assignments,tbl_auth where tbl_auth.user_id = seat_assignments.student_id and class_session_id = '$session_id'";
    									$r_state = SunDataBaseManager::getSingleton()->QueryDB($s_state);
    									while($cs_state = mysql_fetch_assoc($r_state))
                                        {
          									$o_seat = $cs_state['seat_id'];   
                                            $o_state = $cs_state['seat_state']; 
                                            $st_state = $cs_state['user_state']; 
                                            $st_id = $cs_state['user_id']; 
                                            if($o_seat != null && ($st_state == '1' || $st_state == '9'))
                                            {
                                                if($st_state != '7')
                                                {
                         							$update = "update tbl_auth set user_state = '7' where user_id = '$st_id'";
                   							        $updated = SunDataBaseManager::getSingleton()->QueryDB($update);         
            	                                    $log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$st_id',$st_state,'7')";
                                                    $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);                                                                                                                              
                                                }
                                            }                                            
                                        }         
                                        //teacher to ended as well
       									$s_state = "select class_sessions.teacher_id,tbl_auth.user_state  from class_sessions, tbl_auth where user_id = class_sessions.teacher_id and class_sessions.class_session_id ='$session_id'";
    									$r_state = SunDataBaseManager::getSingleton()->QueryDB($s_state);        
                                        $cs_state = mysql_fetch_assoc($r_state);
                                        $o_tid = $cs_state['teacher_id'];   
                                        $o_tstate = $cs_state['user_state'];  
                                        if($o_tstate != '7')
                                        {
                    							$update = "update tbl_auth set user_state = '7' where user_id = '$o_tid'";
               							        $updated = SunDataBaseManager::getSingleton()->QueryDB($update);         
        	                                    $log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$o_tid',$o_tstate,'7')";
                                                $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);                                                                       
                                            
                                        }
                                                     
                                                    
												}
										}
									else
										{
											$state = 1;
											$get_class = "select class_id from class_sessions where class_session_id = '$session_id'";
											$class_info = SunDataBaseManager::getSingleton()->QueryDB($get_class);
											$class = mysql_fetch_assoc($class_info);
											$class_id = $class['class_id'];
											$get_oldtime = "select topic_id, cumulative_time, state from topic_log where class_id = '$class_id' order by transition_time desc limit 1";
											$got_oldtime = SunDataBaseManager::getSingleton()->QueryDB($get_oldtime);
											$oldtime_count = SunDataBaseManager::getSingleton()->getnoOfrows($got_oldtime);
											if ($oldtime_count > 0)
												{
													$oldrecord = mysql_fetch_assoc($got_oldtime);
													$topic_id = $oldrecord['topic_id'];
													$cumulative_time = $oldrecord['cumulative_time'];
													$topic_state = $oldrecord['state'];
															if ($topic_state == "23")
																{
																	$log_topic = "insert into topic_log (class_id, topic_id, cumulative_time, state) values ('$class_id','$topic_id','$cumulative_time','22')";
																	$logged_topic = SunDataBaseManager::getSingleton()->QueryDB($log_topic);
																	$create = "insert into live_session_status(session_id,current_topic) values('$session_id','$topic_id')";
																	$created = SunDataBaseManager::getSingleton()->QueryDB($create);
																}
												}
											$create = "insert into live_session_status(session_id) values('$session_id')";
											$created = SunDataBaseManager::getSingleton()->QueryDB($create);
											/********************* changes ************************/
											$get_class = "select class_id from class_sessions where class_session_id = '$session_id'";
											$get_class_query=SunDataBaseManager::getSingleton()->QueryDB($get_class);
											$class_id_fetch=mysql_fetch_assoc($get_class_query);
											$class_id=$class_id_fetch['class_id'];
											$students="SELECT student_id FROM student_class_map, class_sessions INNER JOIN tbl_auth WHERE tbl_auth.user_state=1 AND student_class_map.student_id=tbl_auth.user_id AND student_class_map.class_id='$class_id' AND class_sessions.class_session_id='$session_id'";
											$students_query=SunDataBaseManager::getSingleton()->QueryDB($students);
											while ($row=mysql_fetch_array($students_query)) {
												$student=$row['student_id'];
												$check_student="select student_id from stud_session_time where student_id='$student' and class_session_id='$session_id'";
												$check_student_query=SunDataBaseManager::getSingleton()->QueryDB($check_student);
												$student_entries=SunDataBaseManager::getSingleton()->getnoOfrows($check_student_query);
												if($student_entries>0)
												{
													$last_seen_time="select current_timestamp";
													$last_seen_query=SunDataBaseManager::getSingleton()->QueryDB($last_seen_time);
													$last_seen_fetch=mysql_fetch_assoc($last_seen_query);
													$last_seen=$last_seen_fetch['current_timestamp'];
													$update_stud="update stud_session_time set last_seen='$last_seen' where student_id='$student' and class_session_id='$session_id'";
													$update_stud_query=SunDataBaseManager::getSingleton()->QueryDB($update_stud);
												}
												elseif ($student_entries==0) {
													$last_seen_time="select current_timestamp";
													$last_seen_query=SunDataBaseManager::getSingleton()->QueryDB($last_seen_time);
													$last_seen_fetch=mysql_fetch_assoc($last_seen_query);
													$last_seen=$last_seen_fetch['current_timestamp'];
													$insert_stud="insert into stud_session_time(student_id,class_session_id,stud_time,present,last_seen) values('$student','$session_id',0,0,'$last_seen')";
													$insert_stud_query=SunDataBaseManager::getSingleton()->QueryDB($insert_stud);
												}
											}
										/********************* end of changes ************************/
										}
									//$teacher_state = "select user_state from tbl_auth where user_id = '$user_id'";
									//$retrieve_teacher_state = SunDataBaseManager::getSingleton()->QueryDB($teacher_state);
									//$current_teacher_state = mysql_fetch_assoc($retrieve_teacher_state);
									//$old_teacher_state = $current_teacher_state['user_state'];
									//$change_teacher_state = "update tbl_auth set user_state = '$state' where user_id = '$user_id'";
									//$teacher_state_change = SunDataBaseManager::getSingleton()->QueryDB($change_teacher_state);
									//$log_teacher_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$user_id','$old_teacher_state','$state')";
									//$logged_teacher_transition = SunDataBaseManager::getSingleton()->QueryDB($log_teacher_transition);
									$room_state = "select state from rooms where room_id = '$room_id'";
									$retrieve_room_state = SunDataBaseManager::getSingleton()->QueryDB($room_state);
									$current_room_state = mysql_fetch_assoc($retrieve_room_state);
									$old_room_state = $current_room_state['state'];
									if($state!=$old_room_state)
									{
										$change_room_state = "update rooms set state = '$state' where room_id = '$room_id'";
										$room_state_change = SunDataBaseManager::getSingleton()->QueryDB($change_room_state);
										$log_room_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('3','$room_id','$old_room_state','$state')";
										$logged_room_transition = SunDataBaseManager::getSingleton()->QueryDB($log_room_transition);
									}
                                    if($status_id == '1' && $old_session_state != '1')
                                    {
                                         
                                        //extend session time constraints
                                        $session_details1 = "select term.starts_on, term.session_state, term.class_session_id from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id inner join classes as class on term.teacher_id = class.teacher_id inner join rooms as room on term.room_id = room.room_id inner join subjects as subject on subject.subject_id = class.subject_id where term.teacher_id = '$user_id' and term.starts_on < now() AND term.class_id = class.class_id AND term.session_state !=5 and term.session_state != 6 and term.class_session_id != '$session_id' order by term.starts_on asc limit 0,1";
            						    $details2=SunDataBaseManager::getSingleton()->QueryDB($session_details1);
                                        $count = SunDataBaseManager::getSingleton()->getnoOfrows($details2);
                                        if(empty($count))
                                        {
                                            //next session start time to current time if no current sessions are found
                                            $reset_time = "update class_sessions set starts_on = now() where class_session_id = '$session_id'";
   							                $reset = SunDataBaseManager::getSingleton()->QueryDB($reset_time);
                                        }
                                        else
                                        {
                                            $details1 = mysql_fetch_assoc($details2);     
                                            $prev_session_state = $details1['session_state'];
                                            $prev_session_id = $details1['class_session_id'];                                            
                                        }

                                        
                                        if($prev_session_state == '4' || $prev_session_state == '2')
                                        {
                                           
                                            //cancel current session
  							                $update_session_state = "update class_sessions set session_state = '6' where class_session_id = '$prev_session_id'";
                							$updated_session_state = SunDataBaseManager::getSingleton()->QueryDB($update_session_state);
                							if($updated_session_state)
                								{
                									$log_session_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('2','$prev_session_id','$prev_session_state','6')";
                									$logged_session_transition = SunDataBaseManager::getSingleton()->QueryDB($log_session_transition);
                								}
                                            //next session start time to current time
                                            $reset_time = "update class_sessions set starts_on = now() where class_session_id = '$session_id'";
   							                $reset = SunDataBaseManager::getSingleton()->QueryDB($reset_time);
								            $delete = "delete from live_session_status where session_id = '$prev_session_id'";
                                            $deleted = SunDataBaseManager::getSingleton()->QueryDB($delete);
                                                   
                                        }
                                        if($prev_session_state == '1')
                                        {
                                            //end current session
  							                $update_session_state = "update class_sessions set session_state = '5' where class_session_id = '$prev_session_id'";
                							$updated_session_state = SunDataBaseManager::getSingleton()->QueryDB($update_session_state);
                							if($updated_session_state)
                								{
                									$log_session_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('2','$prev_session_id','$prev_session_state','5')";
                									$logged_session_transition = SunDataBaseManager::getSingleton()->QueryDB($log_session_transition);
                								}
                                            //next session start time to current time
                                            $reset_time = "update class_sessions set starts_on = now() where class_session_id = '$session_id'";
   							                $reset = SunDataBaseManager::getSingleton()->QueryDB($reset_time);
                                            //current session end time to current time
                                            $reset_time = "update class_sessions set ends_on = now() where class_session_id = '$prev_session_id'";
  							                $reset = SunDataBaseManager::getSingleton()->QueryDB($reset_time);
								            $delete = "delete from live_session_status where session_id = '$prev_session_id'";
                                            $deleted = SunDataBaseManager::getSingleton()->QueryDB($delete);
                                            
                                        }
                                        
                                        //extend session time constraints end
                                        
                                      //if new state is live
       									$s_state = "select seat_assignments.seat_id, seat_assignments.seat_state,tbl_auth.user_state, tbl_auth.user_id  from seat_assignments,tbl_auth where tbl_auth.user_id = seat_assignments.student_id and class_session_id = '$session_id'";
    									$r_state = SunDataBaseManager::getSingleton()->QueryDB($s_state);
    									while($cs_state = mysql_fetch_assoc($r_state))
                                        {
          									$o_seat = $cs_state['seat_id'];   
                                            $o_state = $cs_state['seat_state']; 
                                            $st_state = $cs_state['user_state']; 
                                            $st_id = $cs_state['user_id']; 
                                            if($o_seat != null && $st_state == '10')
                                            {
                    							$update = "update tbl_auth set user_state = '1' where user_id = '$st_id'";
               							        $updated = SunDataBaseManager::getSingleton()->QueryDB($update);         
        	                                    $log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$st_id',$st_state,'1')";
                                                $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);                                                                       
                                            }                                            
                                        }         
                                        //teacher to live
       									$s_state = "select class_sessions.teacher_id,tbl_auth.user_state  from class_sessions, tbl_auth where user_id = class_sessions.teacher_id and class_sessions.class_session_id ='$session_id'";
    									$r_state = SunDataBaseManager::getSingleton()->QueryDB($s_state);        
                                        $cs_state = mysql_fetch_assoc($r_state);
                                        $o_tid = $cs_state['teacher_id'];   
                                        $o_tstate = $cs_state['user_state'];  
                    							$update = "update tbl_auth set user_state = '1' where user_id = '$o_tid'";
               							        $updated = SunDataBaseManager::getSingleton()->QueryDB($update);         
               							        if($o_tstate!='1' || $o_tstate!=1)
               							        {
        	                                    $log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$o_tid',$o_tstate,'1')";
                                                $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition); 
                                                }                                                                      
                                                                             
                                    }

                                                                                                                                    
								}
								$update_session_state = "update class_sessions set session_state = '$status_id' where class_session_id = '$session_id'";
								$updated_session_state = SunDataBaseManager::getSingleton()->QueryDB($update_session_state);
								if($updated_session_state)
								{
									if($old_session_state!=$status_id)
									{
										$log_session_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('2','$session_id','$old_session_state','$status_id')";
										$logged_session_transition = SunDataBaseManager::getSingleton()->
											QueryDB($log_session_transition);
										$arr[SMC::$STATUS] = "Success";
									}
								}
								else
								{
									$arr[SMC::$STATUS] = "Session status could not be updated, please try again.";
								}
								/********************* changes ************************/
								if($status_id == 5)
								{
									
									//Update session_time:
									$select_time="select starts_on,ends_on from class_sessions where class_session_id='$session_id'";
									$time_query=SunDataBaseManager::getSingleton()->QueryDB($select_time);
									$time_fetch2=mysql_fetch_assoc($time_query);
									$start_time2=$time_fetch2['starts_on'];
									$end_time2=$time_fetch2['ends_on'];
									$start_time3 = strtotime($start_time2);
									$end_time3 = strtotime($end_time2);
									$session_time3=$end_time3-$start_time3;
									$update_session_time2="update class_sessions set session_time='$session_time3' where class_session_id='$session_id'";
									$session_time_query2=SunDataBaseManager::getSingleton()->QueryDB($update_session_time2);
									// For selecting different transanctions for GI for that school
									$select_trans_types_gi="SELECT DISTINCT transaction_type
									FROM school_index_map
									WHERE index_type=1
									AND school_id=(SELECT DISTINCT school_id
									FROM student_class_map
									INNER JOIN class_sessions
									INNER JOIN tbl_auth
									ON class_sessions.class_id=student_class_map.class_id
									AND tbl_auth.user_id=student_class_map.student_id
									AND class_session_id='$session_id')
									ORDER BY transaction_type ASC";
									$trans_type_query_gi=SunDataBaseManager::getSingleton()->QueryDB($select_trans_types_gi);
									$t=0;
									while ($row=mysql_fetch_array($trans_type_query_gi))
									{
										$trans_type_gi[$t]=$row['transaction_type'];
										$t++;
									}
									// For selecting different transanctions for PI for that school
									$select_trans_types_pi="SELECT DISTINCT transaction_type
									FROM school_index_map
									WHERE index_type=2
									AND school_id=(SELECT DISTINCT school_id
									FROM student_class_map
									INNER JOIN class_sessions
									INNER JOIN tbl_auth
									ON class_sessions.class_id=student_class_map.class_id
									AND tbl_auth.user_id=student_class_map.student_id
									AND class_session_id='$session_id')
									ORDER BY transaction_type ASC";
									$trans_type_query_pi=SunDataBaseManager::getSingleton()->QueryDB($select_trans_types_pi);
									$t=0;
									while ($row=mysql_fetch_array($trans_type_query_pi))
									{
										$trans_type_pi[$t]=$row['transaction_type'];
										$t++;
									}
									//Selects all live students who attended this session
									$select_students="SELECT student_id, school_id
									FROM stud_session_time
									INNER JOIN tbl_auth
									ON stud_session_time.student_id = tbl_auth.user_id
									WHERE class_session_id ='$session_id'
									AND tbl_auth.user_state =1";
									$select_students_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
									while ($row=mysql_fetch_array($select_students_query))
									{
										$student=$row['student_id'];
										$school_id=$row['school_id'];
										$duration="select stud_time,last_seen from stud_session_time where student_id='$student' and class_session_id='$session_id'";
										$duration_query=SunDataBaseManager::getSingleton()->QueryDB($duration);
										$duration_fetch=mysql_fetch_assoc($duration_query);
										$stud_time=$duration_fetch['stud_time'];
										$last_seen=$duration_fetch['last_seen'];
										$select_curr_time="select current_timestamp";
										$curr_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_curr_time);
										$curr_time_fetch=mysql_fetch_assoc($curr_time_query);
										$curr_time=$curr_time_fetch['current_timestamp'];
										$current_time=strtotime($curr_time);
										$last_seen_time=strtotime($last_seen);
										$stud_time+=$current_time-$last_seen_time;
										$update_stud="update stud_session_time set stud_time='$stud_time', last_seen='$curr_time' where student_id='$student' and class_session_id='$session_id'";
										$update_stud_query=SunDataBaseManager::getSingleton()->QueryDB($update_stud);
									}
									// Change state of each live or background student to 7, i.e. free
									$select_live_students="SELECT student_id,user_state
									FROM tbl_auth
									INNER JOIN student_class_map
									INNER JOIN class_sessions
									ON tbl_auth.user_id=student_class_map.student_id
									WHERE tbl_auth.user_state!=7
									AND tbl_auth.user_state!=8
									AND student_class_map.class_id=class_sessions.class_id
									AND class_sessions.class_session_id='$session_id'";
									$select_students_query=SunDataBaseManager::getSingleton()->QueryDB($select_live_students);
									while($row=mysql_fetch_array($select_students_query))
									{
										$student=$row['student_id'];
										$stud_state=$row['user_state'];
										$update_stud_state="update tbl_auth set user_state=7 where user_id='$student'";
										$update_state_query=SunDataBaseManager::getSingleton()->QueryDB($update_stud_state);
										$stud_state_trans="insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$student','$stud_state','7')";
										$state_trans_query=SunDataBaseManager::getSingleton()->QueryDB($stud_state_trans);
									}
									// Select all open or signed out students
									$select_students="SELECT student_id, school_id
									FROM stud_session_time
									INNER JOIN tbl_auth
									ON stud_session_time.student_id = tbl_auth.user_id
									WHERE class_session_id ='$session_id'
									AND (tbl_auth.user_state =7
										OR tbl_auth.user_state=8)";
									$select_students_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
									$sess_gi_num=0;
									$sess_gi_den=0;
									$sess_pi=0;
									while ($row=mysql_fetch_array($select_students_query))
									{
										$student=$row['student_id'];
										$school_id=$row['school_id'];
										$select_topics="SELECT DISTINCT topic.topic_id, topic_name
										FROM topic
										INNER JOIN topic_log
										WHERE topic.topic_id=topic_log.topic_id
										AND class_session_id='$session_id'";
										$topic_query=SunDataBaseManager::getSingleton()->QueryDB($select_topics);
										$session_gi[$student]=0;
										$session_weights_gi[$student]=0;
										$session_weights_pi[$student]=0;
										$session_pi[$student]=0;
										while ($row2=mysql_fetch_array($topic_query))
										{
											$topic_id=$row2['topic_id'];
											$select_transactions="select transaction_id,subtotal_of_score,subtotal_of_count from student_index where student_id='$student' and topic_id='$topic_id' and class_session_id='$session_id'";
											$transactions_query=SunDataBaseManager::getSingleton()->QueryDB($select_transactions);
											$gi=0;
											$pi=0;
											$weight_gi=0;
											$weight_pi=0;
											while ($row3=mysql_fetch_array($transactions_query))
											{
												$transaction_id=$row3['transaction_id'];
												$score=$row3['subtotal_of_score'];
												$sub_count=$row3['subtotal_of_count'];
												if(in_array($transaction_id,$trans_type_gi))
												{
													$select_weight="select weight_value from school_index_map where transaction_type='$transaction_id' and school_id='$school_id' and index_type=1";
													$weight_query=SunDataBaseManager::getSingleton()->QueryDB($select_weight);
													$weight_fetch=mysql_fetch_assoc($weight_query);
													$weight_value=$weight_fetch['weight_value'];
													$gi=$gi+($weight_value*$score);
													$weight_gi=$weight_gi+($weight_value*$sub_count);
												}
												if (in_array($transaction_id,$trans_type_pi))
												{
													$select_weight2="select weight_value from school_index_map where transaction_type='$transaction_id' and school_id='$school_id' and index_type=2";
													$weight_query2=SunDataBaseManager::getSingleton()->QueryDB($select_weight2);
													$weight_fetch2=mysql_fetch_assoc($weight_query2);
													$weight_value2=$weight_fetch2['weight_value'];
													$pi=$pi+($weight_value2*$sub_count);
													$weight_pi=$weight_pi+($weight_value2*$sub_count);
												}
											}
											$update_gi_pi="update stud_topic_time set student_gi_num='$gi',student_gi_den='$weight_gi', student_pi='$pi' where student_id='$student' and topic_id='$topic_id' and class_session_id='$session_id'";
											$update_gi_pi_query=SunDataBaseManager::getSingleton()->QueryDB($update_gi_pi);
											$session_gi[$student]+=$gi;
											$session_pi[$student]+=$pi;
											$session_weights_gi[$student]+=$weight_gi;
											//$session_weights_pi[$student]+=$weight_pi;
										}
										//$session_gi[$student]/=$session_weights_gi[$student];
										//$session_pi[$student]/=$session_weights_pi[$student];
										$s_gi=$session_gi[$student];
										$w_gi=$session_weights_gi[$student];
										$s_pi=$session_pi[$student];
										$sess_gi_num+=$s_gi;
										$sess_gi_den+=$w_gi;
										$sess_pi+=$s_pi;
										$update_gi_pi2="update stud_session_time set student_gi_num='$s_gi',student_gi_den='$w_gi', student_pi='$s_pi' where student_id='$student' and class_session_id='$session_id'";
										$update_gi_pi_query2=SunDataBaseManager::getSingleton()->QueryDB($update_gi_pi2);
									}
									$class_ginum=$sess_gi_num;
									$class_giden=$sess_gi_den;
									$classpi=$sess_pi;
									//Updating gi, pi for this session in class_sessions
									$select_old_index="select gi_num,gi_den,pi from class_sessions where class_session_id='$session_id'";
									$old_index_query=SunDataBaseManager::getSingleton()->QueryDB($select_old_index);
									$old_index_fetch=mysql_fetch_assoc($old_index_query);
									$old_gi_num=$old_index_fetch['gi_num'];
									$old_gi_den=$old_index_fetch['gi_den'];
									$old_pi=$old_index_fetch['pi'];
									$sess_gi_num+=$old_gi_num;
									$sess_gi_den+=$old_gi_den;
									$sess_pi+=$old_pi;
									$update_sess_index="update class_sessions set gi_num='$sess_gi_num',gi_den='$sess_gi_den',pi='$sess_pi' where class_session_id='$session_id'";
									$sess_index_query=SunDataBaseManager::getSingleton()->QueryDB($update_sess_index);
									//Updating gi, pi for this class_id in class_sessions
									$select_old_index2="select classes.class_id,classes.gi_num,classes.gi_den,classes.pi from classes inner join class_sessions on class_sessions.class_id=classes.class_id where class_sessions.class_session_id='$session_id'";
									$old_index_query2=SunDataBaseManager::getSingleton()->QueryDB($select_old_index2);
									$old_index_fetch2=mysql_fetch_assoc($old_index_query2);
									$old_gi_num2=$old_index_fetch2['gi_num'];
									$old_gi_den2=$old_index_fetch2['gi_den'];
									$old_pi2=$old_index_fetch2['pi'];
									$gi_pi_class=$old_index_fetch2['class_id'];
									$class_ginum+=$old_gi_num2;
									$class_giden+=$old_gi_den2;
									$classpi+=$old_pi2;
									$update_class_index="update classes set gi_num='$class_ginum',gi_den='$class_giden',pi='$classpi' where class_id='$gi_pi_class'";
									$class_index_query=SunDataBaseManager::getSingleton()->QueryDB($update_class_index);
									//Find every topic covered in that session
									$select_topics2="select distinct topic_id from topic_log where class_session_id='$session_id'";
									$topics_q=SunDataBaseManager::getSingleton()->QueryDB($select_topics2);
									//Update gi and pi in topic table for each topic in this session
									while ($tpc_row=mysql_fetch_array($topics_q))
									{
										$topic_id=$tpc_row['topic_id'];
										$tpc_gi_num=0;
										$tpc_gi_den=0;
										$tpc_pi=0;
										$select_stud2="select student_id from stud_topic_time where topic_id='$topic_id' and class_session_id='$session_id'";
										$stud_query2=SunDataBaseManager::getSingleton()->QueryDB($select_stud2);
										while ($stud_row=mysql_fetch_array($stud_query2))
										{
											$stud_tpc=$stud_row['student_id'];
											$select_tpc_index="select student_gi_num,student_gi_den,student_pi from stud_topic_time where student_id='$stud_tpc' and topic_id='$topic_id' and class_session_id='$session_id'";
											$tpc_ind_q=SunDataBaseManager::getSingleton()->QueryDB($select_tpc_index);
											$tpc_ind_f=mysql_fetch_assoc($tpc_ind_q);
											$stud_gi_num2=$tpc_ind_f['student_gi_num'];
											$stud_gi_den2=$tpc_ind_f['student_gi_den'];
											$stud_pi2=$tpc_ind_f['student_pi'];
											$tpc_gi_num+=$stud_gi_num2;
											$tpc_gi_den+=$stud_gi_den2;
											$tpc_pi+=$stud_pi2;
										}
										$less_gi_num=$tpc_gi_num;
										$less_gi_den=$tpc_gi_den;
										$less_pi=$tpc_pi;
										$select_old_index3="select topic.gi_num,topic.gi_den,topic.pi from topic where topic_id='$topic_id'";
										$old_index_query3=SunDataBaseManager::getSingleton()->QueryDB($select_old_index3);
										$old_index_fetch3=mysql_fetch_assoc($old_index_query3);
										$old_gi_num3=$old_index_fetch3['gi_num'];
										$old_gi_den3=$old_index_fetch3['gi_den'];
										$old_pi3=$old_index_fetch3['pi'];
										$tpc_gi_num+=$old_gi_num3;
										$tpc_gi_den+=$old_gi_den3;
										$tpc_pi+=$old_pi3;
										$update_tpc_index="update topic set gi_num='$tpc_gi_num',gi_den='$tpc_gi_den',pi='$tpc_pi' where topic_id='$topic_id'";
										$tpc_index_query=SunDataBaseManager::getSingleton()->QueryDB($update_tpc_index);
										//Update gi and pi in lesson plan table
										$select_c_id="select class_id from class_sessions where class_session_id='$session_id'";
										$c_id_query=SunDataBaseManager::getSingleton()->QueryDB($select_c_id);
										$c_id_fetch=mysql_fetch_assoc($c_id_query);
										$c_id=$c_id_fetch['class_id'];
										$select_old_index4="select lesson_plan.gi_num,lesson_plan.gi_den,lesson_plan.pi from lesson_plan where topic_id='$topic_id' and class_id='$c_id'";
										$old_index_query4=SunDataBaseManager::getSingleton()->QueryDB($select_old_index4);
										$old_index_fetch4=mysql_fetch_assoc($old_index_query4);
										$old_gi_num4=$old_index_fetch4['gi_num'];
										$old_gi_den4=$old_index_fetch4['gi_den'];
										$old_pi4=$old_index_fetch4['pi'];
										$less_gi_num+=$old_gi_num4;
										$less_gi_den+=$old_gi_den4;
										$less_pi+=$old_pi4;
										$update_less_index="update lesson_plan set gi_num='$less_gi_num',gi_den='$less_gi_den',pi='$less_pi' where topic_id='$topic_id' and class_id='$c_id'";
										$less_index_query=SunDataBaseManager::getSingleton()->QueryDB($update_less_index);
									}
									//Update student GI and PI on class level
									$select_students="SELECT student_class_map.student_id,class_sessions.class_id
									FROM student_class_map
									INNER JOIN class_sessions
									INNER JOIN stud_session_time
									WHERE class_sessions.class_id=student_class_map.class_id
									AND stud_session_time.class_session_id=class_sessions.class_session_id
									AND stud_session_time.student_id=student_class_map.student_id
									AND stud_session_time.class_session_id='$session_id'";
									$select_students_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
									while ($row=mysql_fetch_array($select_students_query))
									{
										$student=$row['student_id'];
										$class_id=$row['class_id'];
										$select_prev="select student_gi_num,student_gi_den,student_pi,total_sessions from student_class_map where student_id='$student' and class_id='$class_id'";
										$select_prev_query=SunDataBaseManager::getSingleton()->QueryDB($select_prev);
										$stud_class_pi_fetch=mysql_fetch_assoc($select_prev_query);
										$stud_class_pi=$stud_class_pi_fetch['student_pi'];
										$stud_class_gi_num=$stud_class_pi_fetch['student_gi_num'];
										$stud_class_gi_den=$stud_class_pi_fetch['student_gi_den'];
										$select_gipi="select student_gi_num,student_gi_den,student_pi from stud_session_time where class_session_id='$session_id' and student_id='$student'";
										$gipi_query=SunDataBaseManager::getSingleton()->QueryDB($select_gipi);
										$gipi_fetch=mysql_fetch_assoc($gipi_query);
										$s_gi_num=$gipi_fetch['student_gi_num'];
										$s_gi_den=$gipi_fetch['student_gi_den'];
										$s_pi=$gipi_fetch['student_pi'];
										$class_gi_num=$stud_class_gi_num+$s_gi_num;
										$class_gi_den=$stud_class_gi_den+$s_gi_den;
										$class_pi=$stud_class_pi+$s_pi;
										$update_gi_pi3="update student_class_map set student_gi_num='$class_gi_num',student_gi_den='$class_gi_den', student_pi='$class_pi' where student_id='$student' and class_id='$class_id'";
										$update_gi_pi_query3=SunDataBaseManager::getSingleton()->QueryDB($update_gi_pi3);
									}
									$select_students="select student_id from stud_session_time where class_session_id='$session_id'";
									$select_students_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
									//*******EMAIL config********
										$mail = new PHPMailer();
        								$mail->IsSMTP();
        								$mail->SMTPDebug = 1;
        								$mail->SMTPAuth = true;
        								$mail->SMTPSecure = 'tls';
        								$mail->Host = "smtp.gmail.com";
  										$mail->Username = "learniat@mindshiftapps.com";
  										$mail->Password = "begin2win";
  										$mail->SetFrom('learniat@mindshiftapps.com', 'Learniat');
        								$mail->Port = 587;
        								$mail->SMTPSecure = "tls";
        								$mail->IsHTML(true);
        								set_time_limit(100000); // sets php execution time to 12000 seconds.
        							//*******END OF EMAIL config********
        							// Find out class name for email
        							$select_class_name="select class_name from classes inner join class_sessions on classes.class_id=class_sessions.class_id where class_session_id='$session_id'";
									$select_class_query=SunDataBaseManager::getSingleton()->QueryDB($select_class_name);
									$class_name_fetch=mysql_fetch_assoc($select_class_query);
											$class_name=$class_name_fetch['class_name'];
									while ($row=mysql_fetch_array($select_students_query))
									{
										$student=$row['student_id'];
										$stud_time_fetch="select stud_time,session_time from stud_session_time inner join class_sessions on stud_session_time.class_session_id=class_sessions.class_session_id where student_id='$student' and class_sessions.class_session_id='$session_id'";
										$stud_time_query=SunDataBaseManager::getSingleton()->QueryDB($stud_time_fetch);
										$get_stud_time=mysql_fetch_assoc($stud_time_query);
										$stud_time=$get_stud_time['stud_time'];
										$session_time=$get_stud_time['session_time'];
										$percentage_time=$stud_time/$session_time;
										//Update 'present' and send email
										if($percentage_time>=0.5)
										{
											$update_present="update stud_session_time set present=1 where student_id='$student' and class_session_id='$session_id'";
											$update_present_query=SunDataBaseManager::getSingleton()->QueryDB($update_present);
										}
									}
									// This commented part is for sending emails to those who registered but didn't attend.
									/*$select_reg_stud="SELECT student_class_map.student_id
									FROM student_class_map
									INNER JOIN class_sessions
									ON student_class_map.class_id=class_sessions.class_id
									WHERE class_sessions.class_session_id='$session_id'";
									$reg_stud_query=SunDataBaseManager::getSingleton()->QueryDB($select_reg_stud);
									while ($row=mysql_fetch_array($reg_stud_query))
									{
										$student=$row['student_id'];
										$check_stud="SELECT DISTINCT student_class_map.student_id
										FROM student_class_map
										INNER JOIN stud_session_time
										WHERE stud_session_time.student_id=student_class_map.student_id
										AND class_session_id='$session_id'
										AND student_class_map.student_id='$student'";
										$check_stud_query=SunDataBaseManager::getSingleton()->QueryDB($check_stud);
										$check_stud_entries=SunDataBaseManager::getSingleton()->getnoOfrows($check_stud_query);
										if($check_stud_entries==0)
										{
											//*******EMAIL PART******
											$select_email="select notif_email_id,first_name,last_name from tbl_auth where user_id='$student'";
											$email_query=SunDataBaseManager::getSingleton()->QueryDB($select_email);
											$email_fetch=mysql_fetch_assoc($email_query);
											$email_id=$email_fetch['notif_email_id'];
											$first_name=$email_fetch['first_name'];
											$last_name=$email_fetch['last_name'];
											$select_class_name="select class_name from classes inner join class_sessions on classes.class_id=class_sessions.class_id where class_session_id='$session_id'";
											$select_class_query=SunDataBaseManager::getSingleton()->QueryDB($select_class_name);
											$class_name_fetch=mysql_fetch_assoc($select_class_query);
											$class_name=$class_name_fetch['class_name'];
											$percentage_time2=$percentage_time*100;
											$select_start_end="select starts_on,ends_on from class_sessions where class_session_id='$session_id'";
											$start_end_query=SunDataBaseManager::getSingleton()->QueryDB($select_start_end);
											$start_end_fetch=mysql_fetch_assoc($start_end_query);
											$starts_on=$start_end_fetch['starts_on'];
											$ends_on=$start_end_fetch['ends_on'];
											$starts_on=substr($starts_on, 11, 5);
											$ends_on=substr($ends_on, 11, 5);
        									$message="<!DOCTYPE html>
        									<html>
        									<head>
        									</head>
        									<body>
        										<div style='font-size: 18px;'>Dear parent of <span style='font-size: 22px; font-style:italic;'>".$first_name." ".$last_name."</span>, your child was absent in the session <b>".$class_name."</b> that started at ".$starts_on." and ended at ".$ends_on.".
        										</div>
        									</body>
        									</html>";
        									$mail->Subject = "Attendance of ".$first_name;
        									$mail->Body = $message;
        									$mail->AddAddress($email_id);
        									$mail->Send();
        									$mail->clearAddresses();
										}
									}*/
									$select_students="select student_id,stud_time,session_time from stud_session_time inner join class_sessions on stud_session_time.class_session_id=class_sessions.class_session_id where class_sessions.class_session_id='$session_id'";
									$student_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
									while ($row=mysql_fetch_array($student_query)) {
										$student=$row['student_id'];
										$select_school="select school_id from tbl_auth where user_id='$student'";
										$school_query=SunDataBaseManager::getSingleton()->QueryDB($select_school);
										$school_fetch=mysql_fetch_assoc($school_query);
										$school_id=$school_fetch['school_id'];
										$select_topics="SELECT DISTINCT topic.topic_id, topic_name
										FROM topic
										INNER JOIN topic_log
										WHERE topic.topic_id=topic_log.topic_id
										AND class_session_id='$session_id'";
										$topic_query=SunDataBaseManager::getSingleton()->QueryDB($select_topics);
										while ($row2=mysql_fetch_array($topic_query))
										{
											$topic_id=$row2['topic_id'];
											$topic_name=$row2['topic_name'];
											$index=0;
											$thead[$index][$student][$topic_id]=$topic_name;
											$select_stud_time="SELECT stud_time
											FROM stud_topic_time
											WHERE class_session_id='$session_id'
											AND topic_id='$topic_id'
											AND student_id='$student'";
											$stud_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_stud_time);
											$stud_time_entries=SunDataBaseManager::getSingleton()->getnoOfrows($stud_time_query);
											if($stud_time_entries==0)
											{
												$index=1;
												$thead[$index][$student][$topic_id]="--";
											}
											else
											{
												$stud_time_fetch=mysql_fetch_assoc($stud_time_query);
												$stud_time=$stud_time_fetch['stud_time'];
												$index=1;
												$thead[$index][$student][$topic_id]=(int)($stud_time/60);
											}
											$select_topic_time="SELECT topic_session_time
											FROM topic_log
											WHERE class_session_id='$session_id'
											AND topic_id='$topic_id'
											ORDER BY transition_time DESC
											LIMIT 1";
											$topic_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_topic_time);
											$topic_time_fetch=mysql_fetch_assoc($topic_time_query);
											$topic_time=$topic_time_fetch['topic_session_time'];
											$index=2;
											$thead[$index][$student][$topic_id]=(int)($topic_time/60);
											$select_transactions="select transaction_id,subtotal_of_score from student_index where student_id='$student' and topic_id='$topic_id' and class_session_id='$session_id'";
											$transactions_query=SunDataBaseManager::getSingleton()->QueryDB($select_transactions);
											$good_question_badges=0;
											$raised_hand=0;
											$answered_publicly=0;
											while ($row3=mysql_fetch_array($transactions_query)) {
												$transaction_id=$row3['transaction_id'];
												if($transaction_id==29) {
													$good_question_badges++;
												}
												else if($transaction_id==30) {
													$raised_hand++;
												}
												else if($transaction_id==31) {
													$answered_publicly++;
												}
											}
											$select_gi="select student_gi_num,student_gi_den from stud_topic_time where student_id='$student' and topic_id='$topic_id' and class_session_id='$session_id'";
											$select_gi_query=SunDataBaseManager::getSingleton()->QueryDB($select_gi);
											$grasp_fetch=mysql_fetch_assoc($select_gi_query);
											$grasp_num=$grasp_fetch['student_gi_num'];
											$grasp_den=$grasp_fetch['student_gi_den'];
											$grasp=$grasp_num/$grasp_den;
											$topic_gi_num[$student][$topic_id]=$grasp_num;
											$topic_gi_den[$student][$topic_id]=$grasp_den;
											$index=4;
											$thead[$index][$student][$topic_id]=(int)($grasp*100);
											$select_stud_queries="SELECT count(student_id) AS queries
											FROM student_query
											WHERE topic_id='$topic_id'
											AND class_session_id='$session_id'
											AND student_id='$student'
											GROUP BY student_id";
											$stud_queries_query=SunDataBaseManager::getSingleton()->QueryDB($select_stud_queries);
											$num_stud_queries=SunDataBaseManager::getSingleton()->getnoOfrows($stud_queries_query);
											if($num_stud_queries!=0)
											{
												$stud_queries_fetch=mysql_fetch_assoc($stud_queries_query);
												$stud_queries=$stud_queries_fetch['queries'];
											}
											else
											{
												$stud_queries=0;
											}
											$index=6;
											if($stud_queries==0)
											{
												$thead[$index][$student][$topic_id]="--";
											}
											else
											{
												$thead[$index][$student][$topic_id]=$stud_queries;
											}
											$index=7;
											if($good_question_badges!=0)
											{
												$thead[$index][$student][$topic_id]=$good_question_badges;
											}
											else
											{
												$thead[$index][$student][$topic_id]="--";
											}
											$index=9;
											if($raised_hand!=0)
											{
												$thead[$index][$student][$topic_id]=$raised_hand;
											}
											else
											{
												$thead[$index][$student][$topic_id]="--";
											}
											$index=10;
											if($answered_publicly!=0)
											{
												$thead[$index][$student][$topic_id]=$answered_publicly;
											}
											else
											{
												$thead[$index][$student][$topic_id]="--";
											}
										}
									}
									$select_topics="SELECT DISTINCT topic.topic_id, topic_name
										FROM topic
										INNER JOIN topic_log
										WHERE topic.topic_id=topic_log.topic_id
										AND class_session_id='$session_id'";
									$topic_query=SunDataBaseManager::getSingleton()->QueryDB($select_topics);
									while ($row=mysql_fetch_array($topic_query))
									{
										$topic_id=$row['topic_id'];
										$select_tot_queries="SELECT count(query_id) AS queries
										FROM student_query
										WHERE topic_id='$topic_id'
										AND class_session_id='$session_id'
										GROUP BY topic_id";
										$tot_queries_query=SunDataBaseManager::getSingleton()->QueryDB($select_tot_queries);
										$tot_queries_entries=SunDataBaseManager::getSingleton()->getnoOfrows($tot_queries_query);
										if($tot_queries_entries==0)
										{
											$total_queries=0;
										}
										else
										{
											$tot_queries_fetch=mysql_fetch_assoc($tot_queries_query);
											$total_queries=$tot_queries_fetch['queries'];
										}
										$select_allowed="SELECT COUNT(allow_volunteer) as allowed
										FROM student_query
										WHERE topic_id='$topic_id'
										AND class_session_id='$session_id'
										AND allow_volunteer=1
										GROUP BY topic_id";
										$allowed_query=SunDataBaseManager::getSingleton()->QueryDB($select_allowed);
										$allowed_entries=SunDataBaseManager::getSingleton()->getnoOfrows($allowed_query);
										if($allowed_entries==0)
										{
											$allowed=0;
										}
										else
										{
											$allowed_fetch=mysql_fetch_assoc($allowed_query);
											$allowed=$allowed_fetch['allowed'];
										}
										$avg_grasp_num=0;
										$avg_grasp_den=0;
										$select_students="select student_id,stud_time,session_time from stud_session_time inner join class_sessions on stud_session_time.class_session_id=class_sessions.class_session_id where class_sessions.class_session_id='$session_id'";
										$student_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
										$index=4;
										while ($row2=mysql_fetch_array($student_query)) {
											$student=$row2['student_id'];
											if($thead[$index][$student][$topic_id]!="--")
											{
												$avg_grasp_num=$avg_grasp_num+$topic_gi_num[$student][$topic_id];
												$avg_grasp_den=$avg_grasp_den+$topic_gi_den[$student][$topic_id];
												$thead[$index][$student][$topic_id]=$thead[$index][$student][$topic_id]."%";
											}
										}
										if($avg_grasp_den!=0)
										{
											$avg_grasp=$avg_grasp_num/$avg_grasp_den;
										}
										else
										{
											$avg_grasp=0;
										}
										$avg_grasp=$avg_grasp*100;
										//OR part in queryfor GI
										/*$trans_types_gi=$trans_type_gi[0];
										$query_or_part=$trans_types_gi;
										$i=1;
										while ($i<count($trans_type_gi)) {
											$query_or_part=$query_or_part." or transaction_id=".$trans_type_gi[$i];
											$i++;
										}
										$query_or_part=$query_or_part.")";
										$select_tot_students="SELECT DISTINCT student_id as total_students
										FROM student_index
										WHERE (transaction_id=".$query_or_part."
										topic_id='$topic_id'
										AND class_session_id='$session_id'";
										$tot_students_query=SunDataBaseManager::getSingleton()->QueryDB($select_tot_students);
										$tot_students_entries=SunDataBaseManager::getSingleton()->getnoOfrows($tot_students_query);
										$tot_students_fetch=mysql_fetch_assoc($tot_students_query);
										$total_students=$tot_students_fetch['total_students'];
										$flag=0;
										if($tot_students_entries==0)
										{
											//$total_students=0;
											$avg_grasp=0;
											$flag=1;
										}
										else
										{
											//$avg_grasp=$avg_grasp/$total_students;

											$flag=2;
										}*/
										$select_students="select student_id,stud_time,session_time from stud_session_time inner join class_sessions on stud_session_time.class_session_id=class_sessions.class_session_id where class_sessions.class_session_id='$session_id'";
										$student_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
										while ($row2=mysql_fetch_array($student_query))
										{
											$student=$row2['student_id'];
											$index=3;
											if($avg_grasp==0)
											{
												$thead[$index][$student][$topic_id]="--";
											}
											else
											{
												$thead[$index][$student][$topic_id]=(int)($avg_grasp)."%";
											}
											$index=5;
											if($total_queries==0)
											{
												$thead[$index][$student][$topic_id]="--";
											}
											else
											{
												$thead[$index][$student][$topic_id]=$total_queries;
											}
											$index=8;
											if($allowed==0)
											{
												$thead[$index][$student][$topic_id]="--";
											}
											else
											{
												$thead[$index][$student][$topic_id]=$allowed;
											}
										}
									}
									$select_students="select student_id,stud_time,session_time from stud_session_time inner join class_sessions on stud_session_time.class_session_id=class_sessions.class_session_id where class_sessions.class_session_id='$session_id'";
									$student_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
									while ($row=mysql_fetch_array($student_query))
									{
										$student=$row['student_id'];
										$stud_time=$row['stud_time'];
										$session_time=$row['session_time'];
										$per_time=$stud_time/$session_time;
										$select_email="select notif_email_id,first_name,last_name from tbl_auth where user_id='$student'";
										$email_query=SunDataBaseManager::getSingleton()->QueryDB($select_email);
										$email_fetch=mysql_fetch_assoc($email_query);
										$email_id=$email_fetch['notif_email_id'];
										$stud_name=$email_fetch['first_name'];
										$last_name=$email_fetch['last_name'];
										$select_class="SELECT class_name,starts_on,ends_on
										FROM classes
										INNER JOIN class_sessions
										WHERE class_sessions.class_id=classes.class_id
										AND class_sessions.class_session_id='$session_id'";
										$class_name_query=SunDataBaseManager::getSingleton()->QueryDB($select_class);
										$class_name_fetch=mysql_fetch_assoc($class_name_query);
										$class_name=$class_name_fetch['class_name'];
										$starts_on=$class_name_fetch['starts_on'];
										$ends_on=$class_name_fetch['ends_on'];
										$starts_on=substr($starts_on, 11, 5);
										$ends_on=substr($ends_on, 11, 5);
										if($per_time>=0.5)
										{
											$per_time2=(int)($per_time*100);
											$session_time2=(int)($session_time/60);
											$message1="<html>
											<body>
											<div style='font-size: 16px;'>
												Dear parent,<br>
												Your child <span style='font-size: 20px; font-style:italic;'>".$stud_name." ".$last_name."</span> just finished the class session <b>".$class_name."</b> successfully with ".$per_time2."% attentiveness using Learniat.<br><br>The class started at ".$starts_on." and lasted for ".$session_time2." minutes overall.<br><br>The following topics were covered in the class with following statistics specific to your child:<br><br>
											</div>
											<table border='1'>";
										}
										else
										{
											$per_time2=(int)($per_time*100);
											$session_time2=(int)($session_time/60);
											$stud_time2=(int)($stud_time/60);
											$message1="<html>
											<body>
											<div style='font-size: 16px;'>
												Dear parent,<br>
												Your child <span style='font-size: 20px; font-style:italic;'>".$stud_name." ".$last_name."</span> has attended the class session of <b>".$class_name."</b> that started at ".$starts_on." and ended at ".$ends_on." but his attendance was partial as he attended only ".$stud_time2." minutes out of the total ".$session_time2." minutes.<br><br> The following topics were covered in the class with following statistics specific to your child:<br><br>
											</div>
											<table border='1'>";
										}
										$table_heads=array('Topic name','Time for which '.$stud_name.' was attentive (minutes)','Total time covered (minutes)','Total grasp
										index of class (percentage)','Grasp index of '.$stud_name,'Total queries asked by all students','Total queries asked by '.$stud_name,'Total good question badges received','Number of times teacher requested volunteers for answering publicly','Number of times '.$stud_name.' raised hand to volunteer.','Number of times '.$stud_name.' actually answered publicly.');
										$i=0;
										$tr="<tr>";
										$tbl="";
										while ($i<11) {
											$th="<th>".$table_heads[$i]."</th>";
											$tbl=$tbl.$th;
											$i++;
										}
										$tbl=$tr.$tbl."</tr>";
										$select_topics="SELECT DISTINCT topic.topic_id, topic_name
										FROM topic
										INNER JOIN topic_log
										WHERE topic.topic_id=topic_log.topic_id
										AND class_session_id='$session_id'";
										$topic_query=SunDataBaseManager::getSingleton()->QueryDB($select_topics);
										while ($row2=mysql_fetch_array($topic_query))
										{
											$topic_id=$row2['topic_id'];
											$i=0;
											$tr="<tr>";
											$tbl2="";
											while ($i<11) {
												if($thead[$i][$student][$topic_id]==0 && ($i==3 || $i==4))
												{
													$thead[$i][$student][$topic_id]=$thead[$i][$student][$topic_id]."%";
												}
												if($i==0)
												{
													$td="<td style='text-align:center;'>".$thead[$i][$student][$topic_id]."</td>";
												}
												else if($thead[$i][$student][$topic_id]!="--")
												{
													$td="<td style='text-align:right;'>".$thead[$i][$student][$topic_id]."</td>";
												}
												else
												{
													$td="<td style='text-align:center;'>".$thead[$i][$student][$topic_id]."</td>";
												}
												$tbl2=$tbl2.$td;
												$i++;
											}
											$tbl2=$tr.$tbl2."</tr>";
											$tbl=$tbl.$tbl2;
										}
										$message2="</table>
										</body>
										</html>";
										$message=$message1.$tbl.$message2;
										$mail->Subject = "Attendance of ".$stud_name;
        								$mail->Body = $message;
        								$mail->AddAddress($email_id);
        								$mail->Send();
        								$mail->clearAddresses();
									}
									//*******END OF NEW EMAIL PART******
									//For updating sessions_attended and total_sessions for student_class_map
									$select_students="SELECT student_class_map.student_id AS stud_id,sessions_attended,student_class_map.class_id
									FROM student_class_map
									INNER JOIN class_sessions
									INNER JOIN stud_session_time
									ON student_class_map.class_id=class_sessions.class_id
									WHERE class_sessions.class_session_id='$session_id'
									AND stud_session_time.student_id=student_class_map.student_id
									AND stud_session_time.class_session_id=class_sessions.class_session_id
									AND present=1";
									$select_students_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
									while ($row=mysql_fetch_array($select_students_query)) {
										$student=$row['stud_id'];
										$sessions_attended=$row['sessions_attended'];
										$class_id=$row['class_id'];
										$sessions_attended++;
										$update_sessions="update student_class_map set sessions_attended='$sessions_attended' where student_id='$student' and class_id='$class_id'";
										$update_sessions_query=SunDataBaseManager::getSingleton()->QueryDB($update_sessions);
									}
									$select_sessions="SELECT total_sessions
									FROM student_class_map
									WHERE class_id=(SELECT class_id
									FROM class_sessions
									WHERE class_session_id='$session_id')
									LIMIT 1";
									$select_sessions_query=SunDataBaseManager::getSingleton()->QueryDB($select_sessions);
									$total_sessions_fetch=mysql_fetch_assoc($select_sessions_query);
									$total_sessions=$total_sessions_fetch['total_sessions'];
									$total_sessions++;
									$update_total_sessions="UPDATE student_class_map
									SET total_sessions='$total_sessions'
									WHERE class_id=(SELECT class_id
									FROM class_sessions
									WHERE class_session_id='$session_id')";
									$update_total_query=SunDataBaseManager::getSingleton()->QueryDB($update_total_sessions);
									// To update stud_attended from class_sessions
									$update_stud_attended="UPDATE class_sessions
									set stud_attended=(SELECT COUNT(student_id)
									FROM stud_session_time
									WHERE present=1
									AND class_session_id='$session_id'
									GROUP BY class_session_id)
									WHERE class_sessions.class_session_id='$session_id'";
									$stud_attended_query=SunDataBaseManager::getSingleton()->QueryDB($update_stud_attended);
									// To update total_stud_registered from class_sessions
									$select_class_id="select student_class_map.class_id from student_class_map inner join class_sessions on student_class_map.class_id=class_sessions.class_id where class_sessions.class_session_id='$session_id'";
									$select_class_query=SunDataBaseManager::getSingleton()->QueryDB($select_class_id);
									$select_class=mysql_fetch_assoc($select_class_query);
									$class_id=$select_class['class_id'];
									$update_stud_registered="UPDATE class_sessions
									set total_stud_registered=(SELECT COUNT(student_id)
									FROM student_class_map
									WHERE class_id='$class_id'
									 GROUP BY class_id)
									WHERE class_sessions.class_session_id='$session_id'";
									$update_registered_query=SunDataBaseManager::getSingleton()->QueryDB($update_stud_registered);
									// *****Email for school ending*****
									$select_students="SELECT student_id, concat(first_name,' ',last_name) as student_name
									FROM student_class_map
									INNER JOIN class_sessions
									INNER JOIN tbl_auth
									ON class_sessions.class_id=student_class_map.class_id
									AND tbl_auth.user_id=student_class_map.student_id
									AND class_session_id='$session_id'";
									$students_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
									$avg_stud_attendance=0;
									while ($row=mysql_fetch_array($students_query))
									{
										$student=$row['student_id'];
										$select_school="select school_id from tbl_auth where user_id='$student'";
										$school_query=SunDataBaseManager::getSingleton()->QueryDB($select_school);
										$school_fetch=mysql_fetch_assoc($school_query);
										$school_id=$school_fetch['school_id'];
										$select_sessions="SELECT class_session_id
										FROM student_class_map
										INNER JOIN class_sessions
										ON student_class_map.class_id=class_sessions.class_id
										WHERE student_id='$student'
										AND session_state!=5
										AND session_state!=6
										AND date(starts_on)=CURDATE()";
										$sessions_query=SunDataBaseManager::getSingleton()->QueryDB($select_sessions);
										$number_of_sessions=SunDataBaseManager::getSingleton()->getnoOfrows($sessions_query);
										if($number_of_sessions==0) // Last session of the day
										{
											$tdata2[$student]=0;
											$student_name=$row['student_name'];
											$tdata1[$student]=$student_name;
											/*To send emails to all parents irrespective of the fact that the students did or didn't attend a single session in the day: (Commented for time being, should be uncommented actually)
											*****Uncomment this query and comment the next query for actual app.*****
											$select_todays_classes="SELECT class_name, concat(first_name,' ',last_name) as teacher_name,class_sessions.class_session_id
											FROM student_class_map
											INNER JOIN class_sessions
											INNER JOIN classes
											INNER JOIN tbl_auth
											WHERE student_class_map.class_id=class_sessions.class_id
											AND  tbl_auth.user_id=class_sessions.teacher_id
											AND student_class_map.class_id=classes.class_id
											AND student_class_map.student_id='$student'
											AND session_state=5
											AND date(starts_on)=CURDATE()";*/
											//To send emails to only those, who attended at least one session:
											$select_todays_classes="SELECT class_name, concat(first_name,' ',last_name) as teacher_name,class_sessions.class_session_id
											FROM student_class_map
											INNER JOIN class_sessions
											INNER JOIN classes
                                            INNER JOIN stud_session_time
											INNER JOIN tbl_auth
											WHERE student_class_map.class_id=class_sessions.class_id
											AND  tbl_auth.user_id=class_sessions.teacher_id
											AND student_class_map.class_id=classes.class_id
											AND student_class_map.student_id='$student'
                                            AND stud_session_time.student_id='$student'
                                            AND stud_session_time.class_session_id=class_sessions.class_session_id
											AND session_state=5
											AND date(starts_on)=CURDATE()";
											$todays_classes_query=SunDataBaseManager::getSingleton()->QueryDB($select_todays_classes);
											$number_of_classes=SunDataBaseManager::getSingleton()->getnoOfrows($todays_classes_query);
											$tdata3[$student]=$number_of_classes; ////////////////////
											$stud_attended="SELECT COUNT(student_id) as tot_attended
											FROM stud_session_time
											INNER JOIN class_sessions
											WHERE stud_session_time.class_session_id=class_sessions.class_session_id
											AND date(starts_on)=CURDATE()
											AND stud_session_time.student_id='$student'
											AND session_state=5
											GROUP BY student_id";
											$stud_attended_query=SunDataBaseManager::getSingleton()->QueryDB($stud_attended);
											$stud_attended_entries=SunDataBaseManager::getSingleton()->getnoOfrows($stud_attended_query);
											if($stud_attended_entries==0)
											{
												$tdata2[$student]=0;
											}
											else
											{
												$stud_attended_fetch=mysql_fetch_assoc($stud_attended_query);
												$tot_stud_attended=$stud_attended_fetch['tot_attended'];
												$tdata2[$student]=$tot_stud_attended; ///////////
											}
											$stud_gi_num[$student]=0;
											$stud_gi_den[$student]=0;
											$stud_gi[$student]=0;
											$stud_pi[$student]=0;
											$tot_sessions[$student]=0;
											$student_time[$student]=0;
											$sess_time[$student]=0;
											while ($row2=mysql_fetch_array($todays_classes_query))
											{
												$class_session_id=$row2['class_session_id'];
												$grasp_ind[$student][$class_session_id]=0;
												$part_ind[$student][$class_session_id]=0;
												$tot_sessions[$class_session_id]=0;
												$class_name=$row2['class_name'];
												$teacher_name=$row2['teacher_name'];
												$index=0;
												$thead[$index][$student][$class_session_id]=$class_name;
												$index=1;
												$thead[$index][$student][$class_session_id]=$teacher_name;
												$select_session_time="select session_time from class_sessions where class_session_id='$class_session_id'";
												$session_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_session_time);
												$session_time_fetch=mysql_fetch_assoc($session_time_query);
												$session_time=$session_time_fetch['session_time'];
												$session_time2=(int)($session_time/60);
												$sess_time[$student]+=$session_time2;
												$index=2;
												$thead[$index][$student][$class_session_id]=$session_time2." minutes";
												$check_if_student="select student_id,stud_time from stud_session_time where class_session_id='$class_session_id' and student_id='$student'";
												$check_student_query=SunDataBaseManager::getSingleton()->QueryDB($check_if_student);
												$check_stud_entries=SunDataBaseManager::getSingleton()->getnoOfrows($check_student_query);
												if($check_stud_entries==0)
												{
													$index=3;
													$thead[$index][$student][$class_session_id]="--";
												}
												else
												{
													$check_student_fetch=mysql_fetch_assoc($check_student_query);
													$stud_time=$check_student_fetch['stud_time'];
													$stud_time2=(int)($stud_time/60);
													$index=3;
													$thead[$index][$student][$class_session_id]=$stud_time2." minutes";
													$student_time[$student]+=$stud_time2;
												}
												$select_sess_gipi="select student_gi_num,student_gi_den,student_pi from stud_session_time where student_id='$student' and class_session_id='$class_session_id'";
												$select_sess_gipi_query=SunDataBaseManager::getSingleton()->QueryDB($select_sess_gipi);
												$sess_gipi_fetch=mysql_fetch_assoc($select_sess_gipi_query);
												$grasp_ind_num[$student][$class_session_id]=$sess_gipi_fetch['student_gi_num'];
												$grasp_ind_den[$student][$class_session_id]=$sess_gipi_fetch['student_gi_den'];
												$part_ind[$student][$class_session_id]=$sess_gipi_fetch['student_pi'];
												//$tot_sessions[$student]++;
												$stud_gi_num[$student]+=$grasp_ind_num[$student][$class_session_id];
												$stud_gi_den[$student]+=$grasp_ind_den[$student][$class_session_id];
												$stud_pi[$student]+=$part_ind[$student][$class_session_id];
											}
											if($stud_gi_den[$student]!=0)
											{
												$stud_gi[$student]=$stud_gi_num[$student]/$stud_gi_den[$student];
											}
											$tdata4[$student]=(int)(($student_time[$student]*100)/$sess_time[$student]);
											$tdata7[$student]=$stud_gi[$student]*100;
											$tdata10[$student]=(int)($stud_pi[$student]);
										}
									}
									$select_students="SELECT student_id, concat(first_name,' ',last_name) as student_name
									FROM student_class_map
									INNER JOIN class_sessions
									INNER JOIN tbl_auth
									ON class_sessions.class_id=student_class_map.class_id
									AND tbl_auth.user_id=student_class_map.student_id
									AND class_session_id='$session_id'";
									$students_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
									while ($row=mysql_fetch_array($students_query))
									{
										$student=$row['student_id'];
										$select_sessions="SELECT class_session_id
										FROM student_class_map
										INNER JOIN class_sessions
										ON student_class_map.class_id=class_sessions.class_id
										WHERE student_id='$student'
										AND session_state!=5
										AND session_state!=6
										AND date(starts_on)=CURDATE()";
										$sessions_query=SunDataBaseManager::getSingleton()->QueryDB($select_sessions);
										$number_of_sessions=SunDataBaseManager::getSingleton()->getnoOfrows($sessions_query);
										if($number_of_sessions==0) // Last session of the day
										{
											/*To send emails to all parents irrespective of the fact that the students did or didn't attend a single session in the day: (Commented for time being, should be uncommented actually)
											*****Uncomment this query and comment the next query for actual app.*****
											$select_todays_classes="SELECT class_name, concat(first_name,' ',last_name) as teacher_name,class_sessions.class_session_id
											FROM student_class_map
											INNER JOIN class_sessions
											INNER JOIN classes
											INNER JOIN tbl_auth
											WHERE student_class_map.class_id=class_sessions.class_id
											AND  tbl_auth.user_id=class_sessions.teacher_id
											AND student_class_map.class_id=classes.class_id
											AND student_class_map.student_id='$student'
											AND session_state=5
											AND date(starts_on)=CURDATE()";*/
											//To send emails to only those, who attended at least one session:
											$select_todays_classes="SELECT class_name, concat(first_name,' ',last_name) as teacher_name,class_sessions.class_session_id
											FROM student_class_map
											INNER JOIN class_sessions
											INNER JOIN classes
                                            INNER JOIN stud_session_time
											INNER JOIN tbl_auth
											WHERE student_class_map.class_id=class_sessions.class_id
											AND  tbl_auth.user_id=class_sessions.teacher_id
											AND student_class_map.class_id=classes.class_id
											AND student_class_map.student_id='$student'
                                            AND stud_session_time.student_id='$student'
                                            AND stud_session_time.class_session_id=class_sessions.class_session_id
											AND session_state=5
											AND date(starts_on)=CURDATE()";
											$todays_classes_query=SunDataBaseManager::getSingleton()->QueryDB($select_todays_classes);
											$number_of_classes=SunDataBaseManager::getSingleton()->getnoOfrows($todays_classes_query);
											$avg_gi[$student]=0;
											$avg_gi_num[$student]=0;
											$avg_gi_den[$student]=0;
											$avg_pi[$student]=0;
											$tot_stud_sessions[$student]=$number_of_classes;
											$cum_class_attd[$student]=0;
											while ($row2=mysql_fetch_array($todays_classes_query))
											{
												$class_session_id=$row2['class_session_id'];
												$select_topics="SELECT DISTINCT topic.topic_id, topic_name
												FROM topic
												INNER JOIN topic_log
												WHERE topic.topic_id=topic_log.topic_id
												AND class_session_id='$class_session_id'";
												$topic_query=SunDataBaseManager::getSingleton()->QueryDB($select_topics);
												$average_gi[$class_session_id]=0;
												$average_gi_num[$class_session_id]=0;
												$average_gi_den[$class_session_id]=0;
												$average_pi[$class_session_id]=0;
												$total_topics[$class_session_id]=0;
												while ($row3=mysql_fetch_array($topic_query))
												{
													$topic_id=$row3['topic_id'];
													$avg_grasp2[$class_session_id][$topic_id]=0;
													$avg_grasp2_num[$class_session_id][$topic_id]=0;
													$avg_grasp2_den[$class_session_id][$topic_id]=0;
													$avg_part[$class_session_id][$topic_id]=0;
													$select_students="select student_id from stud_session_time where class_session_id='$class_session_id'";
													$student_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
													while ($row4=mysql_fetch_array($student_query))
													{
														$student2=$row4['student_id'];
														$select_topic_gipi="select student_gi_num,student_gi_den,student_pi from stud_topic_time where student_id='$student2' and topic_id='$topic_id' and class_session_id='$class_session_id'";
														$gipi_query=SunDataBaseManager::getSingleton()->QueryDB($select_topic_gipi);
														$gipi_fetch=mysql_fetch_assoc($gipi_query);
														$avg_grasp2_num[$class_session_id][$topic_id]+=$gipi_fetch['student_gi_num'];
														$avg_grasp2_den[$class_session_id][$topic_id]+=$gipi_fetch['student_gi_den'];
														$avg_part[$class_session_id][$topic_id]+=$gipi_fetch['student_pi'];
													}
													//OR part in queryfor GI
													/*$trans_types_gi=$trans_type_gi[0];
													$query_or_part=$trans_types_gi;
													$i=1;
													while ($i<count($trans_type_gi)) {
														$query_or_part=$query_or_part." or transaction_id=".$trans_type_gi[$i];
														$i++;
													}
													$query_or_part=$query_or_part.")";
													$select_tot_students_gi="SELECT COUNT(DISTINCT student_id) as total_students
													FROM student_index
													WHERE (transaction_id=".$query_or_part."
													AND topic_id='$topic_id'
													AND class_session_id='$class_session_id'";
													$tot_students_query_gi=SunDataBaseManager::getSingleton()->QueryDB($select_tot_students_gi);
													$tot_students_entries_gi=SunDataBaseManager::getSingleton()->getnoOfrows($tot_students_query_gi);
													$tot_students_fetch_gi=mysql_fetch_assoc($tot_students_query_gi);
													$total_students_gi=$tot_students_fetch_gi['total_students'];
													if($tot_students_entries_gi==0 || $total_students_gi==0)
													{
														$total_students_gi=0;
														$avg_grasp2[$class_session_id][$topic_id]=0;
													}
													else
													{
														$avg_grasp2[$class_session_id][$topic_id]/=$total_students_gi;
													}*/
													if($avg_grasp2_den[$class_session_id][$topic_id]==0)
													{
														$avg_grasp2[$class_session_id][$topic_id]=$avg_grasp2_num[$class_session_id][$topic_id]/$avg_grasp2_den[$class_session_id][$topic_id];
													}
													$average_gi_num[$class_session_id]+=$avg_grasp2_num[$class_session_id][$topic_id];
													$average_gi_den[$class_session_id]+=$avg_grasp2_den[$class_session_id][$topic_id];
													//$total_topics[$class_session_id]++;
													//OR part in query for PI
													/*$trans_types_pi=$trans_type_pi[0];
													$query_or_part=$trans_types_pi;
													$i=1;
													while ($i<count($trans_type_pi)) {
														$query_or_part=$query_or_part." or transaction_id=".$trans_type_pi[$i];
														$i++;
													}
													$query_or_part=$query_or_part.")";
													$select_tot_students_pi="SELECT COUNT(DISTINCT student_id) as total_students
													FROM student_index
													WHERE (transaction_id=".$query_or_part."
													AND topic_id='$topic_id'
													AND class_session_id='$class_session_id'";
													$tot_students_query_pi=SunDataBaseManager::getSingleton()->QueryDB($select_tot_students_pi);
													$tot_students_entries_pi=SunDataBaseManager::getSingleton()->getnoOfrows($tot_students_query_pi);
													$tot_students_fetch_pi=mysql_fetch_assoc($tot_students_query_pi);
													$total_students_pi=$tot_students_fetch_pi['total_students'];
													if($tot_students_entries_pi==0 || $total_students_pi==0)
													{
														$total_students_pi=0;
														$avg_part[$class_session_id][$topic_id]=0;
													}
													else
													{
														$avg_part[$class_session_id][$topic_id]/=$total_students_pi;
													}*/
													$average_pi[$class_session_id]+=$avg_part[$class_session_id][$topic_id];
												}// topic loop ends here
												$avg_gi_num[$student]+=$average_gi_num[$class_session_id];
												$avg_gi_den[$student]+=$average_gi_den[$class_session_id];
												$no_of_stud="select count(distinct student_id) as stud_att from stud_session_time where class_session_id='$class_session_id'";
												$stud_att_query=SunDataBaseManager::getSingleton()->QueryDB($no_of_stud);
												$stud_att_fetch=mysql_fetch_assoc($stud_att_query);
												$stud_att=$stud_att_fetch['stud_att'];
												$avg_pi[$student]=$avg_pi[$student]+(($average_pi[$class_session_id])/$stud_att);
												$select_session_time="select session_time from class_sessions where class_session_id='$class_session_id'";
												$session_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_session_time);
												$session_time_fetch=mysql_fetch_assoc($session_time_query);
												$session_time=$session_time_fetch['session_time'];
												$session_time2=$session_time/60;
												$select_stud_att="select stud_time from stud_session_time where class_session_id='$class_session_id'";
												$stud_att_query=SunDataBaseManager::getSingleton()->QueryDB($select_stud_att);
												$tot_stud=SunDataBaseManager::getSingleton()->getnoOfrows($stud_att_query);
												$avg_attd[$class_session_id]=0;
												while ($row3=mysql_fetch_array($stud_att_query)) {
													$stud_time=$row3['stud_time'];
													$stud_time2=$stud_time/60;
													$avg_attd[$class_session_id]=$avg_attd[$class_session_id]+(($stud_time2*100)/$session_time2);
												}
												$avg_attd[$class_session_id]/=$tot_stud;
												$cum_class_attd[$student]+=$avg_attd[$class_session_id];
											}
											// Attendance
											$cum_class_attd[$student]/=$number_of_classes;
											if((int)($tdata4[$student]-$cum_class_attd[$student])>0)
											{
												$tdata5[$student]=(int)($tdata4[$student]-$cum_class_attd[$student]);
												$tdata6[$student]="% more than";
											}
											else if((int)($cum_class_attd[$student]-$tdata4[$student])>0)
											{
												$tdata5[$student]=(int)($cum_class_attd[$student]-$tdata4[$student]);
												$tdata6[$student]="% less than";
											}
											else if((int)($cum_class_attd[$student]-$tdata4[$student])==0)
											{
												$tdata5[$student]="";
												$tdata6[$student]="the same as";
											}
											// GI
											if($avg_gi_den[$student]!=0)
											{
												$avg_gi[$student]=($avg_gi_num[$student]*100)/$avg_gi_den[$student];
											}
											if((int)($tdata7[$student]-$avg_gi[$student])>0)
											{
												$tdata8[$student]=(int)($tdata7[$student]-$avg_gi[$student]);
												$tdata9[$student]="% more than";
											}
											else if((int)($avg_gi[$student]-$tdata7[$student])>0)
											{
												$tdata8[$student]=(int)($avg_gi[$student]-$tdata7[$student]);
												$tdata9[$student]="% less than";
											}
											else if((int)($avg_gi[$student]-$tdata7[$student])==0)
											{
												$tdata8[$student]="";
												$tdata9[$student]="the same as";
											}
											// PI
											if((int)($tdata10[$student]-$avg_pi[$student])>0)
											{
												$tdata11[$student]=(int)((($tdata10[$student]-$avg_pi[$student])/$tdata10[$student])*100);
												$tdata12[$student]="% more than";
											}
											else if((int)($avg_pi[$student]-$tdata10[$student])>0)
											{
												$tdata11[$student]=(int)((($avg_pi[$student]-$tdata10[$student])/$avg_pi[$student])*100);
												$tdata12[$student]="% less than";
											}
											else if((int)($avg_pi[$student]-$tdata10[$student])==0)
											{
												$tdata11[$student]="";
												$tdata12[$student]="the same as";
											}
										}
									}
									// HTML part for school ending
									$select_stud="SELECT student_id
									FROM student_class_map
									INNER JOIN class_sessions
									ON student_class_map.class_id=class_sessions.class_id
									AND class_sessions.class_session_id='$session_id'";
									$students_query=SunDataBaseManager::getSingleton()->QueryDB($select_students);
									$stud_query=SunDataBaseManager::getSingleton()->QueryDB($select_stud);
									while ($row=mysql_fetch_array($stud_query))
									{
										$student=$row['student_id'];
										$select_sessions="SELECT class_session_id
										FROM student_class_map
										INNER JOIN class_sessions
										ON student_class_map.class_id=class_sessions.class_id
										WHERE student_id='$student'
										AND session_state!=5
										AND session_state!=6
										AND date(starts_on)=CURDATE()";
										$sessions_query=SunDataBaseManager::getSingleton()->QueryDB($select_sessions);
										$number_of_sessions=SunDataBaseManager::getSingleton()->getnoOfrows($sessions_query);
										if($number_of_sessions==0) // Last session of the day
										{
											$select_email="select notif_email_id from tbl_auth where user_id='$student' and notif_email_id is not null";
											$email_query=SunDataBaseManager::getSingleton()->QueryDB($select_email);
											$email_fetch=mysql_fetch_assoc($email_query);
											$email_id=$email_fetch['notif_email_id'];
											$tdata7[$student]=(int)($tdata7[$student]);
											$message1="
											<html>
												<head></head>
												<body>
													<div style='font-size: 16px;'>
														Dear parent,<br><br>Your child <span style='font-size: 20px; font-style:italic;'>".$tdata1[$student]."</span> just finished the school day attending ".$tdata2[$student]." of ".$tdata3[$student]." class sessions successfully attaining ".$tdata4[$student]."% cumulative attentiveness on the Learniat platform. This is ".$tdata5[$student]."".$tdata6[$student]." the average of all students of his/her class. Your child attained an average grasp index of ".$tdata7[$student]."% which is ".$tdata8[$student]."".$tdata9[$student]." the average class grasp. Your child’s participation index was ".$tdata10[$student]." which is ".$tdata11[$student]."".$tdata12[$student]." the entire class average.<br><br>The following classes were completed today:
													</div>
													<table border=1>";
											$message2="
													</table>
												</body>
											</html>";
											$table_heads=array('Class name','Class teacher name','Session duration','Attendance of '.$tdata1[$student]);
											$table="";
											$i=0;
											while ($i<count($table_heads))
											{
												$table=$table."<th>".$table_heads[$i]."</th>";
												$i++;
											}
											$table="<tr>".$table."</tr>";
											/*To send emails to all parents irrespective of the fact that the students did or didn't attend a single session in the day: (Commented for time being, should be uncommented actually)
											*****Uncomment this query and comment the next query for actual app.*****
											$select_todays_classes="SELECT class_name, concat(first_name,' ',last_name) as teacher_name,class_sessions.class_session_id
											FROM student_class_map
											INNER JOIN class_sessions
											INNER JOIN classes
											INNER JOIN tbl_auth
											WHERE student_class_map.class_id=class_sessions.class_id
											AND  tbl_auth.user_id=class_sessions.teacher_id
											AND student_class_map.class_id=classes.class_id
											AND student_class_map.student_id='$student'
											AND session_state=5
											AND date(starts_on)=CURDATE()";*/
											//To send emails to only those, who attended at least one session:
											$select_todays_classes="SELECT class_name, concat(first_name,' ',last_name) as teacher_name,class_sessions.class_session_id
											FROM student_class_map
											INNER JOIN class_sessions
											INNER JOIN classes
                                            INNER JOIN stud_session_time
											INNER JOIN tbl_auth
											WHERE student_class_map.class_id=class_sessions.class_id
											AND  tbl_auth.user_id=class_sessions.teacher_id
											AND student_class_map.class_id=classes.class_id
											AND student_class_map.student_id='$student'
                                            AND stud_session_time.student_id='$student'
                                            AND stud_session_time.class_session_id=class_sessions.class_session_id
											AND session_state=5
											AND date(starts_on)=CURDATE()";
											$todays_classes_query=SunDataBaseManager::getSingleton()->QueryDB($select_todays_classes);
											$flag=0;
											while ($row2=mysql_fetch_array($todays_classes_query)) {
												$class_session_id=$row2['class_session_id'];
												$i=0;
												$table2="";
												while ($i<count($table_heads))
												{
													$table2=$table2."<td>".$thead[$i][$student][$class_session_id]."</td>";
													$i++;
												}
												$table2="<tr>".$table2."</tr>";
												$table=$table.$table2;
												$flag=1;
											}
											if($flag==1)
											{
												$message=$message1.$table.$message2;
												$mail->Subject = "Attendance of ".$tdata1[$student];
        										$mail->Body = $message;
        										$mail->AddAddress($email_id);
        										$mail->Send();
        										$mail->clearAddresses();
        									}
										}
									}
									//end of email for school ending
								}
								/********************* end of changes ************************/
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function ConfigureGrid($room_id=null, $rows=null, $columns=null, $seats_removed=null)
				{
					try
						{
							$check = "select grid_id from seating_grids where room_id ='$room_id'";
							$validate = SunDataBaseManager::getSingleton()->QueryDB($check);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($validate);
							if($count == 1)
								{
									$update = "update seating_grids set seat_rows = '$rows', seat_columns = '$columns', seats_removed = '$seats_removed' where room_id ='$room_id'";
									$change = SunDataBaseManager::getSingleton()->QueryDB($update);
									if ($change)
										{
											$unassign = "delete from seat_assignments where seat_id in (select seat_id from seats where room_id = '$room_id')";
											$truncate = SunDataBaseManager::getSingleton()->QueryDB($unassign);
											$delete = "delete from seats where room_id ='$room_id'";
											$clear = SunDataBaseManager::getSingleton()->QueryDB($delete);
											if ($clear)
												{
													if (!empty($seats_removed))
														{
															$count = substr_count($seats_removed, ',');
															if ($count>=1)
																{
																	$removed = explode(",",$seats_removed);
																}
															else
																{
																	$removed = $seats_removed;
																}
														}
													else
														{
															$removed = null;
														}
													$letter = "A";
													$max = $rows * $columns;
													for($i=1;$i<=$max;$i++)
														{
															if ($removed == null)
																{
																	$label[] = $letter.$i;
																}
															else if (!is_array($removed))
																{
																	$new_label = $letter.$i;
																	if ($new_label != $removed)
																		{
																			$label[] = $new_label;
																		}
																}
															else
																{
																	$new_label = $letter.$i;
																	if (!in_array($new_label, $removed))
																		{
																			$label[] = $new_label;
																		}
																}
														}
													foreach($label as $seat)
														{
															$add_seat = "insert into seats (room_id,seat_number) values ('$room_id','$seat')";
															$add = SunDataBaseManager::getSingleton()->QueryDB($add_seat);
														}
													$get_seats = "select seat_id, seat_number from seats where room_id = '$room_id'";
													$get = SunDataBaseManager::getSingleton()->QueryDB($get_seats);
													while($seat = mysql_fetch_assoc($get))
														{
															$seat_id[] = $seat['seat_id'];
															$seat_label[] = $seat['seat_number'];
														}
													$seat_ids = implode(",",$seat_id);
													$seat_labels = implode(",",$seat_label);
													$arr[SMC::$STATUS] = "Success";
													$arr[SMC::$SEATIDLIST] = $seat_ids;
													$arr[SMC::$SEATLABELLIST] = $seat_labels;
												}
										}
								}
							else
								{
									$add = "insert into seating_grids (room_id,seat_rows,seat_columns,seats_removed) values ('$room_id','$rows','$columns','$seats_removed')";
									$insert = SunDataBaseManager::getSingleton()->QueryDB($add);
									if ($insert)
										{													
											if (!empty($seats_removed))
												{
													$count = substr_count($seats_removed, ',');
													if ($count>=1)
														{
															$removed = explode(",",$seats_removed);
														}
													else
														{
															$removed = $seats_removed;
														}
												}
											else
												{
													$removed = null;
												}
											$letter = "A";
											$max = $rows * $columns;
											for($i=1;$i<=$max;$i++)
												{
													if ($removed == null)
														{
															$label[] = $letter.$i;
														}
													else if (!is_array($removed))
														{
															$new_label = $letter.$i;
															if ($new_label != $removed)
																{
																	$label[] = $new_label;
																}
														}
													else
														{
															$new_label = $letter.$i;
															if (!in_array($new_label, $removed))
																{
																	$label[] = $new_label;
																}
														}
												}
											foreach($label as $seat)
												{
													$add_seat = "insert into seats (room_id,seat_number) values ('$room_id','$seat')";
													$add = SunDataBaseManager::getSingleton()->QueryDB($add_seat);
												}
											$get_seats = "select seat_id, seat_number from seats where room_id = '$room_id'";
											$get = SunDataBaseManager::getSingleton()->QueryDB($get_seats);
											while($seat = mysql_fetch_assoc($get))
												{
													$seat_id[] = $seat['seat_id'];
													$seat_label[] = $seat['seat_number'];
												}
											$seat_ids = implode(",",$seat_id);
											$seat_labels = implode(",",$seat_label);
											$arr[SMC::$STATUS] = "Success";
											$arr[SMC::$SEATIDLIST] = $seat_ids;
											$arr[SMC::$SEATLABELLIST] = $seat_labels;

										}
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}

			public function GetStudentsSessionInfo($session_id=null)
				{
					try
						{
					  
							$current_session = "select questions.question_name as current_question, topic.topic_name as current_topic, session.question_instance_id from live_session_status as session inner join topic on topic.topic_id = session.current_topic inner join questions on questions.question_id = session.current_question where session_id = '$session_id'";
							$session_details = SunDataBaseManager::getSingleton()->QueryDB($current_session);
							$session_info = mysql_fetch_assoc($session_details);
							$current_question = $session_info['current_question'];
							$current_topic = $session_info['current_topic'];
							$current_question_instance = $session_info['question_instance_id'];
						    //$info = "select user.first_name, user.user_id, states.state_description as user_state, seat.seat_number, assign.seat_id, state.state_description as seat_state from tbl_auth as user inner join entity_states as states on user.user_state = states.state_id inner join student_class_map as map on map.student_id = user.user_id inner join class_sessions as session on session.class_id = map.class_id left join seat_assignments as assign on assign.student_id = user.user_id and assign.class_session_id = '$session_id' left join seats as seat on seat.seat_id = assign.seat_id inner join entity_states as state on assign.seat_state = state.state_id where session.class_session_id = '$session_id' order By user.first_name";
                            $info = "SELECT user.first_name, user.user_id, states.state_description AS user_state
FROM tbl_auth AS user
INNER JOIN entity_states AS states ON user.user_state = states.state_id
INNER JOIN student_class_map AS map ON map.student_id = user.user_id
INNER JOIN class_sessions AS session ON session.class_id = map.class_id
WHERE session.class_session_id =  '$session_id'
ORDER BY user.first_name";
							
							//$info = "select user.first_name, user.user_id, states.state_description as user_state, seat.seat_number, assign.seat_id, state.state_description as seat_state from tbl_auth as user inner join entity_states as states on user.user_state = states.state_id inner join student_class_map as map on map.student_id = user.user_id inner join class_sessions as session on session.class_id = map.class_id left join seat_assignments as assign on assign.student_id = user.user_id and assign.class_session_id = '$session_id' left join seats as seat on seat.seat_id = assign.seat_id inner join entity_states as state on assign.seat_state = state.state_id where session.class_session_id = '$session_id'";
							$student = SunDataBaseManager::getSingleton()->QueryDB($info);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($student);
							if($count>0)
								{
									$arr[SMC::$STATUS] = "Success";
									$i = 0;
									while($session = mysql_fetch_assoc($student))
										{
											$user_id = $session['user_id'];
//return rows for students
											
											$check1 = "select seat.seat_number, assign.seat_id, state.state_description as seat_state from tbl_auth as user inner join entity_states as states on user.user_state = states.state_id inner join student_class_map as map on map.student_id = user.user_id inner join class_sessions as session on session.class_id = map.class_id left join seat_assignments as assign on assign.student_id = user.user_id left join seats as seat on seat.seat_id = assign.seat_id inner join entity_states as state on assign.seat_state = state.state_id where assign.class_session_id = '$session_id' and user.user_id='$user_id' order By user.first_name";
											$check_type1 = SunDataBaseManager::getSingleton()->QueryDB($check1);
											$session1 = mysql_fetch_assoc($check_type1);
                                                                                        
											$check = "select answer.assessment_answer_id, answer.rating, types.question_type_title from questions inner join question_types as types using (question_type_id) inner join question_log using(question_id) inner join assessment_answers as answer using(question_log_id) where answer.student_id = '$user_id' and answer.class_session_id = '$session_id' and answer.question_log_id = '$current_question_instance'";
											$check_type = SunDataBaseManager::getSingleton()->QueryDB($check);
											$answer_count = SunDataBaseManager::getSingleton()->getnoOfrows($check_type);
											//echo "Answer count is $answer_count for Student $user_id and question instance is $current_question_instance and session id is $session_id";
											if($answer_count > 0)
												{
													$assessment = mysql_fetch_assoc($check_type);
													$type = $assessment['question_type_title'];
													$answer_id = $assessment['assessment_answer_id'];
													//echo "Answer id is $answer_id";
													if ($type == "Multiple Response")
														{
															$options = "select option_text from answer_options as options inner join assessment_answers as answer on answer.assessment_answer_id = options.assessment_answer_id where options.assessment_answer_id = '$answer_id'";
															$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
															$j = 0;
															while($option = mysql_fetch_assoc($option_list))
																{
																	$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTANSWER][$j][SMC::$OPTIONTEXT] = $option['option_text'];
																	$j++;
																}
														}
													else if ($type == "Multiple Choice")
														{
															$options = "select option_text from answer_options as options inner join assessment_answers as answer on answer.assessment_answer_id = options.assessment_answer_id where options.assessment_answer_id = '$answer_id'";
															$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
															$option = mysql_fetch_assoc($option_list);
															$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTANSWER][SMC::$OPTIONTEXT] = $option['option_text'];
														}
													else if ($type == "Fresh Scribble" or $type == "Overlay Scribble")
														{
															$retrieve = "select image.image_path as teacher_scribble, images.image_path as scribble from assessment_answers as answer left join uploaded_images as image on answer.teacher_scribble_id = image.image_id left join answer_options as options on options.assessment_answer_id = answer.assessment_answer_id left join uploaded_images as images on options.scribble_id = images.image_id where options.assessment_answer_id = '$answer_id'";
															$answer = SunDataBaseManager::getSingleton()->QueryDB($retrieve);
															$option = mysql_fetch_assoc($answer);
															$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTANSWER][SMC::$SCRIBBLE] = $option['scribble'];
															$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTANSWER][SMC::$TEACHERSCRIBBLE] = $option['teacher_scribble'];
														}
													else if ($type == "Text")
														{
															$retrieve = "select answer_text from answer_options as options inner join assessment_answers as answer on answer.assessment_answer_id = options.assessment_answer_id where options.assessment_answer_id = '$answer_id'";
															$answer = SunDataBaseManager::getSingleton()->QueryDB($retrieve);
															$option = mysql_fetch_assoc($answer);
                                                            //echo $option['answer_text'];
															$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTANSWER][SMC::$TEXTANSWER] = $option['answer_text'];
														}
													else if ($type == "Match Columns")
														{
															$options = "select option_text, mtc_column, mtc_sequence from answer_options as options inner join assessment_answers as answer on answer.assessment_answer_id = options.assessment_answer_id where options.assessment_answer_id = '$answer_id'";
															$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
															$j = 0;
															while($option = mysql_fetch_assoc($option_list))
																{
																	$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTANSWER][$j][SMC::$OPTIONTEXT] = $option['option_text'];
																	$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTANSWER][$j][SMC::$COLUMN] = $option['mtc_column'];
																	$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTANSWER][$j][SMC::$SEQUENCE] = $option['mtc_sequence'];
																	$j++;
																}
														}
												}
											else
												{
													$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTANSWER] = "";
												}
											if (!empty($current_question))
												{
													$arr[SMC::$CURRENTQUESTION] = $session_info['current_question'];
												}
											if (!empty($current_topic))
												{
													$arr[SMC::$CURRENTTOPIC] = $session_info['current_topic'];
												}
                                                $seatsid = $session1['seat_id'];
                                                $seatsno = $session1['seat_number'];
                                                $seatstate = $session1['seat_state'];
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$NAME] = $session['first_name'];
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID] = $user_id;                                            
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTSTATE] = $session['user_state'];
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$SEATID] = $seatsid;
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$SEATLABEL] = $seatsno;
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$SEATSTATE] = $seatstate;
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$GRASPINDEX] = $this->mXMLManager->GetGraspIndex(null,$session_id,$user_id,$current_topic);
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$PARTICIPATIONINDEX] = $this->mXMLManager->GetParticipationIndex($session_id,$user_id);
											$i++;
										}
								}
							else
								{
								    
     							    $info = "select user.first_name, user.user_id, seat.seat_number from tbl_auth as user inner join student_class_map as map on map.student_id = user.user_id inner join class_sessions as session on session.class_id = map.class_id left join seat_assignments as assign on assign.student_id = user.user_id and assign.class_session_id = '$session_id' left join seats as seat on seat.seat_id = assign.seat_id where session.class_session_id = '$session_id' order by user.first_name";
        							$student1 = SunDataBaseManager::getSingleton()->QueryDB($info);
        							$count1 = SunDataBaseManager::getSingleton()->getnoOfrows($student1);
        							if($count1>0)
        							{
        									$arr[SMC::$STATUS] = "Success";
        									$i = 0;
        									while($session = mysql_fetch_assoc($student1))
        										{
        											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$NAME] = $session['first_name'];
        											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID] = $session['user_id'];                                            
        											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTSTATE] = NULL;
        											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$SEATID] = NULL;
        											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$SEATLABEL] = $session['seat_number'];
        											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$SEATSTATE] = NULL;
        											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$GRASPINDEX] = $this->mXMLManager->GetGraspIndex(null,$session_id,$session['user_id'],$current_topic);
        											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$PARTICIPATIONINDEX] = $this->mXMLManager->GetParticipationIndex($session_id,$session['user_id']);                                                   
        											$i++;
        										}
        							}
        							else
        							{
        						      $arr[SMC::$STATUS] = "There are no students in the current session.";
        							}
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
					/*The above API returns parameters WRT scenario, it can be called after App has crashed or in general. It helps populate the class view of an 
					  on-going session by showing current answer of all students, their state, their seat state/id/label, query, topic and question.*/
				}
			public function RetrieveStudentQuery($query_id=null)
				{
					try
						{
							$detail = "select user.first_name, query.student_id, query.query_text, query.start_time, query.anonymous from student_query as query inner join tbl_auth as user on query.student_id = user.user_id where query.query_id = '$query_id'";
							$student = SunDataBaseManager::getSingleton()->QueryDB($detail);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($student);
                            
							if($count>0)
								{
									$arr[SMC::$STATUS] = "Success";
									$query = mysql_fetch_assoc($student);
								    //get school
                                    $user_id = $query['student_id'];
															$validate_password = "select school_id from tbl_auth where user_id = '$user_id'";
															$validate = SunDataBaseManager::getSingleton()->QueryDB($validate_password);
															
																	$user = mysql_fetch_assoc($validate);
																	$school_id = $user['school_id'];                                     
                                                                        $arr[SMC::$QUERY][SMC::$QUERYID] = $query_id;
									$arr[SMC::$QUERY][SMC::$STUDENTID] = $query['student_id'];
									$arr[SMC::$QUERY][SMC::$STUDENTNAME] = $query['first_name'];
									$arr[SMC::$QUERY][SMC::$QUERYTEXT] = $query['query_text'];
									$arr[SMC::$QUERY][SMC::$STARTTIME] = $this->mXMLManager->ReturnTimeOffset($query['start_time'],$school_id);
									$arr[SMC::$QUERY][SMC::$ANONYMOUS] = $query['anonymous'];
                                    $arr[SMC::$QUERY][SMC::$QUERYID] = $query_id;
								}
							else
								{
									$arr[SMC::$STATUS] = "There are no queries with this ID, please try again.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function StudentSeatAssignment($session_id=null,$seat_id=null,$student_id=null,$status_id=null)
				{
					try
						{
							$count = substr_count($seat_id, ',');
							if ($count>=1)
								{
									$seats = explode(",",$seat_id);
									$students = explode(",",$student_id);
								}
							else
								{
									$seats = $seat_id;
									$students = $student_id;
								}

							if (is_array($seats))
								{
									foreach ($seats as $key=>$seat)
										{
											$check = "select seat_id from seat_assignments where class_session_id = '$session_id' and student_id = '$students[$key]'"; 
											$verify = SunDataBaseManager::getSingleton()->QueryDB($check);
											$count = SunDataBaseManager::getSingleton()->getnoOfrows($verify);
											if ($count>0)
												{
													$update = "update seat_assignments set seat_id = '$seat',seat_state='$status_id' where student_id = '$students[$key]' and class_session_id = '$session_id' ;";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
												}
											else
												{
													$add = "insert into seat_assignments (seat_id, student_id, class_session_id, seat_state) values ('$seat', '$students[$key]', '$session_id', '$status_id');";
													$assign = SunDataBaseManager::getSingleton()->QueryDB($add);
												}
											$log_seat_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('4','$seat','7','9')";
											$logged_seat_transition = SunDataBaseManager::getSingleton()->QueryDB($log_seat_transition);
										}
									$arr[SMC::$STATUS] = "Success";
								}
							else
								{
									$check = "select seat_id from seat_assignments where class_session_id = '$session_id' and student_id = '$students'"; 
									$verify = SunDataBaseManager::getSingleton()->QueryDB($check);
									$count = SunDataBaseManager::getSingleton()->getnoOfrows($verify);
									if ($count>0)
										{
											$update = "update seat_assignments set seat_id = '$seats',seat_state='$status_id' where student_id = '$students' and class_session_id = '$session_id' ;";
											$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
										}
									else
										{
											$add = "insert into seat_assignments (seat_id, student_id, class_session_id, seat_state) values ('$seats', '$students', '$session_id', '$status_id');";
											$assign = SunDataBaseManager::getSingleton()->QueryDB($add);
										}
									$log_seat_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('4','$seats','7','9')";
									$logged_seat_transition = SunDataBaseManager::getSingleton()->QueryDB($log_seat_transition);
									$arr[SMC::$STATUS] = "Success";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function RetrieveGridDesign($room_id=null)
				{
					try
						{
							$detail = "select seat_rows, seat_columns, seats_removed from seating_grids where room_id = '$room_id';";
							$student = SunDataBaseManager::getSingleton()->QueryDB($detail);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($student);
							if($count>0)
								{
									$get_seats = "select seat_id, seat_number from seats where room_id = '$room_id'";
									$get = SunDataBaseManager::getSingleton()->QueryDB($get_seats);
									while($seat = mysql_fetch_assoc($get))
										{
											$seat_id[] = $seat['seat_id'];
											$seat_label[] = $seat['seat_number'];
										}
									$seat_ids = implode(",",$seat_id);
									$seat_labels = implode(",",$seat_label);
									$arr[SMC::$STATUS] = "Success";
									$query = mysql_fetch_assoc($student);
									$arr[SMC::$ROWS] = $query['seat_rows'];
									$arr[SMC::$COLUMNS] = $query['seat_columns'];
									$arr[SMC::$SEATSREMOVED] = $query['seats_removed'];
									$arr[SMC::$SEATIDLIST] = $seat_ids;
									$arr[SMC::$SEATLABELLIST] = $seat_labels;
								}
							else
								{
									$arr[SMC::$STATUS] = "There are no grids designed for this room.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function RetrieveSeatAssignments($session_id=null)
				{
					try
						{
							$detail = "select assign.seat_id, seat.seat_number, assign.student_id, user.first_name, state.state_description as user_state, states.state_description as seat_state from seat_assignments as assign inner join seats as seat on assign.seat_id = seat.seat_id inner join tbl_auth as user on assign.student_id = user.user_id inner join entity_states as state on user.user_state = state.state_id inner join entity_states as states on assign.seat_state = states.state_id where assign.class_session_id = '$session_id'";
							$assignment = SunDataBaseManager::getSingleton()->QueryDB($detail);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($assignment);
							if($count>0)
								{
									$arr[SMC::$STATUS] = "Success";
									$i = 0;
									while($seat = mysql_fetch_assoc($assignment))
										{
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$SEATID] = $seat['seat_id'];
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$SEATLABEL] = $seat['seat_number'];
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID] = $seat['student_id'];
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTNAME] = $seat['first_name'];
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTSTATE] = $seat['user_state'];
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$SEATSTATE] = $seat['seat_state'];
											$i++;
										}
								}
							else
								{
									$arr[SMC::$STATUS] = "No student is assigned seat yet, please wait for your teacher to configure seats.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function ClassSessionSummary($session_id=null)
				{
					try
						{
							$state_info = "SELECT starts_on,session_state FROM class_sessions WHERE class_session_id = '$session_id'";
							$info1 = SunDataBaseManager::getSingleton()->QueryDB($state_info);
							while($details123 = mysql_fetch_assoc($info1))
								{
									$session_status = $details123['session_state'];
									$start_time = $details123['starts_on'];
								}						  
							$seat_info = "select seat.seat_id, assign.seat_state from seats as seat inner join class_sessions as session on session.room_id = seat.room_id left join seat_assignments as assign on seat.seat_id = assign.seat_id and assign.class_session_id = '$session_id' left join entity_states as state on session.session_state = state.state_id where session.class_session_id = '$session_id'";
							$info = SunDataBaseManager::getSingleton()->QueryDB($seat_info);
							$configured_count = SunDataBaseManager::getSingleton()->getnoOfrows($info);
							$preallocated_count = 0;
							$occupied_count = 0;
							while($details = mysql_fetch_assoc($info))
								{
									$state = $details['seat_state'];
									if ($state == '9')
										{
											$preallocated_count++;
										}
									if ($state == '10')
										{
											$occupied_count++;
										}
								}
							$students_registered = "select map.student_id from student_class_map as map inner join class_sessions as session on map.class_id = session.class_id where session.class_session_id = '$session_id'";
							$get_registered = SunDataBaseManager::getSingleton()->QueryDB($students_registered);
							$registered_count = SunDataBaseManager::getSingleton()->getnoOfrows($get_registered);
							$get_class = "select class_id from class_sessions where class_session_id = '$session_id'";
							$class_info = SunDataBaseManager::getSingleton()->QueryDB($get_class);
							$class = mysql_fetch_assoc($class_info);
							$class_id = $class['class_id'];
							$topics_tagged = "select plan.topic_id from lesson_plan as plan inner join topic on plan.topic_id = topic.topic_id and topic.parent_topic_id is not null where plan.class_id = '$class_id' and plan.topic_tagged = '1'";
							$get_tagged = SunDataBaseManager::getSingleton()->QueryDB($topics_tagged);
							$tagged_count = SunDataBaseManager::getSingleton()->getnoOfrows($get_tagged);
							while($topics = mysql_fetch_assoc($get_tagged))
								{
									$topic[] = $topics['topic_id'];
								}
							$topic_list = implode(",",$topic);
                            $question_count = 0;
                            if($topic_list != null)
                            {
 							$questions = "select question_id from questions where topic_id in ($topic_list)";
							$questions_configured = SunDataBaseManager::getSingleton()->QueryDB($questions);
							$question_count = SunDataBaseManager::getSingleton()->getnoOfrows($questions_configured);                               
                            }
															$validate_password = "SELECT school_id FROM subjects,classes where subjects.subject_id=classes.subject_id and class_id= '$class_id'";
															$validate = SunDataBaseManager::getSingleton()->QueryDB($validate_password);
															
																	$user = mysql_fetch_assoc($validate);
																	$school_id = $user['school_id'];         
							$arr[SMC::$STATUS] = "Success";
							$arr[SMC::$SEATSCONFIGURED] = $configured_count;
							$arr[SMC::$STUDENTSREGISTERED] = $registered_count;
							$arr[SMC::$PREALLOCATEDSEATS] = $preallocated_count;
							$arr[SMC::$OCCUPIEDSEATS] = $occupied_count;
							$arr[SMC::$TOPICSCONFIGURED] = $tagged_count;
							$arr[SMC::$QUESTIONSCONFIGURED] = $question_count;
                            $arr[SMC::$STARTTIME] = $this->mXMLManager->ReturnTimeOffset($start_time,$school_id);
							$arr[SMC::$SESSIONSTATE] = $session_status;

                            $retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function ResetSeatAssignment($session_id=null)
				{
					try
						{
							$reset_seats = "delete from seat_assignments where class_session_id = '$session_id'";
							$reset = SunDataBaseManager::getSingleton()->QueryDB($reset_seats);
							if ($reset)
								{
									$arr[SMC::$STATUS] = "Success";
								}
							else
								{
									$arr[SMC::$STATUS] = "Could not clear seat assignments, please try again.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function ExtendSessionTime($session_id=null, $extended_time=null)
				{
					try
						{
							$get_time = "select starts_on,ends_on, session_state from class_sessions where class_session_id = '$session_id'";
							$get = SunDataBaseManager::getSingleton()->QueryDB($get_time);
							$time = mysql_fetch_assoc($get);
							$state = $time['session_state'];
                            if($state == '1' || $state == 1)
                            {
                                $curr_time = $time['ends_on'];
    							$new_time = strtotime($curr_time." + ".$extended_time." minute");
    							$set_time = date("Y-m-d H:i:s", $new_time);
    							$reset_time = "update class_sessions set ends_on = '$set_time' where class_session_id = '$session_id'";
    							$reset = SunDataBaseManager::getSingleton()->QueryDB($reset_time);
    							if ($reset)
    								{
    									$arr[SMC::$STATUS] = "Success";
    								}
    							else
    								{
    									$arr[SMC::$STATUS] = "Could not extend class time, please try again.";
    								}
    							$retval = $this->mXMLManager->createXML($arr);
    							return $retval;                                
                            }
                            else
                            {
                                $curr_time = $time['starts_on'];
    							$new_time = strtotime($curr_time." + ".$extended_time." minute");
    							$set_time = date("Y-m-d H:i:s", $new_time);
    							$reset_time = "update class_sessions set starts_on = '$set_time' where class_session_id = '$session_id' and session_state !=6";
    							$reset = SunDataBaseManager::getSingleton()->QueryDB($reset_time);
    							if ($reset)
    								{
    									$arr[SMC::$STATUS] = "Success";
    								}
    							else
    								{
    									$arr[SMC::$STATUS] = "Could not extend class time, please try again.";
    								}
    							$retval = $this->mXMLManager->createXML($arr);
    							return $retval;                                
                            }

						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function GetAllNodes($class_id=null, $subject_id=null,$main_topic_id=null,$type=null)
				{
					try
						{
						  if($main_topic_id == null && $type == null)
                          {
                            
                        
							$tagged_topics = "select topic_id from lesson_plan where class_id = '$class_id'";
							$tagged_topic_list = SunDataBaseManager::getSingleton()->QueryDB($tagged_topics);
							$tagged_count = SunDataBaseManager::getSingleton()->getnoOfrows($tagged_topic_list);
							if ($tagged_count > 0)
								{
									unset($tagged_list1);
									while($tagged_topic = mysql_fetch_assoc($tagged_topic_list))
										{
											$tagged_list1[] = $tagged_topic['topic_id'];
										}
								    $str = implode (", ", $tagged_list1);
                                }
                                else
                                {
                                    $str = "";
                                }
                                
                                
							$tagged_topics = "select topic_id from lesson_plan where topic_tagged = '1' and class_id = '$class_id'";
							$tagged_topic_list = SunDataBaseManager::getSingleton()->QueryDB($tagged_topics);
							$tagged_count = SunDataBaseManager::getSingleton()->getnoOfrows($tagged_topic_list);
							if ($tagged_count > 0)
								{
									unset($tagged_list);
									while($tagged_topic = mysql_fetch_assoc($tagged_topic_list))
										{
											$tagged_list[] = $tagged_topic['topic_id'];
										}
								}
                                
							$retval = "<Root><SunStone><Action>";
							$topics = "select topic_id, topic_name, topic_info from topic where parent_topic_id is null and subject_id = '$subject_id'";
                            if($str != "")
                            {
                              $topics.= " and topic_id in($str)";  
                            } 
                           //echo $topics;
							$topic_list = SunDataBaseManager::getSingleton()->QueryDB($topics);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($topic_list);
							if($count > 0)
								{
									$retval .= "<Status>Success</Status>";
									$retval .= "<MainTopics>";
									while($topic = mysql_fetch_assoc($topic_list))
										{
										  $main_cumulative_time = 0;
											$topic_id = $topic['topic_id'];
											$topic_name = $topic['topic_name'];
                                            $topic_info = $topic['topic_info'];
											$retval .= "<MainTopic>";
											if ($tagged_list)
												{
													$check = in_array($topic_id, $tagged_list);
													if ($check)
														{
														  //echo "tagged ".$topic_id;
															$retval .= "<Tagged>1</Tagged>";
														}
													else
														{
															$retval .= "<Tagged>0</Tagged>";
														}
												}
											else
												{
													$retval .= "<Tagged>0</Tagged>";
												}
											$initiated_topic = "select log.topic_id from topic_log as log inner join topic on log.topic_id = topic.topic_id and log.cumulative_time > 0 where topic.parent_topic_id = '$topic_id' and log.class_id = '$class_id' group by log.topic_id";
											$got_initiated_topic = SunDataBaseManager::getSingleton()->QueryDB($initiated_topic);
											$initiated_topic_count = SunDataBaseManager::getSingleton()->getnoOfrows($got_initiated_topic);
											$sub_topic_tagged = "select plan.topic_id from lesson_plan as plan inner join topic on plan.topic_id = topic.topic_id where topic.parent_topic_id = '$topic_id' and plan.class_id = '$class_id'";
											$got_tagged_sub_topic = SunDataBaseManager::getSingleton()->QueryDB($sub_topic_tagged );
											$tagged_sub_topic_count = SunDataBaseManager::getSingleton()->getnoOfrows($got_tagged_sub_topic );

$parent_tagged = "SELECT topic_tagged FROM lesson_plan,topic WHERE lesson_plan.topic_id=topic.topic_id and topic_tagged = '1' and topic.parent_topic_id='$topic_id' and topic.subject_id='$subject_id' and lesson_plan.class_id ='$class_id'";
$parent_tagged_topic = SunDataBaseManager::getSingleton()->QueryDB($parent_tagged);
$parent_tagged_topic_count = SunDataBaseManager::getSingleton()->getnoOfrows($parent_tagged_topic );
							$topics5 = "select topic_id, topic_name, topic_info from topic where parent_topic_id = '$topic_id' and subject_id = '$subject_id'";
							$topic_list5 = SunDataBaseManager::getSingleton()->QueryDB($topics5);
							$count5 = SunDataBaseManager::getSingleton()->getnoOfrows($topic_list5);
if($parent_tagged_topic_count != 0){	
 if($parent_tagged_topic_count > $count5)
 {
    $parent_tagged_topic_count = $count5;
 }   
    
$topic_percentage_tagged = (($parent_tagged_topic_count/$count5)*100)."%";} else{$topic_percentage_tagged = 0;}

if($tagged_sub_topic_count != 0){											
$sub_topic_percentage_started = (($initiated_topic_count/$tagged_sub_topic_count)*100)."%";} else{$sub_topic_percentage_started = 0;}
$school = SunDataBaseManager::getSingleton()->QueryDB("SELECT school_id FROM subjects WHERE subject_id='$subject_id'");
$question = mysql_fetch_assoc($school);
$school_id = $question['school_id'];
$grasp_index = $this->mXMLManager->GetMainGraspIndex($school_id,null,null,$topic_id,$subject_id);   
//$grasp_index = $this->mXMLManager->GetGraspIndex($school_id,null,null,$topic_id);
											$retval .= "<Id>$topic_id</Id><Name>$topic_name</Name><Info>$topic_info</Info><PercentageStarted>$sub_topic_percentage_started </PercentageStarted><PercentageTagged>$topic_percentage_tagged</PercentageTagged><GraspIndex>$grasp_index</GraspIndex>";
											$sub_topics = "select topic_id, topic_name, topic_info from topic where parent_topic_id = '$topic_id' and subject_id = '$subject_id'";
											$sub_topic_list = SunDataBaseManager::getSingleton()->QueryDB($sub_topics);
											$sub_topic_count = SunDataBaseManager::getSingleton()->getnoOfrows($sub_topic_list);
											if($sub_topic_count > 0)
												{
													$retval .= "<SubTopics>";
													while($sub_topic = mysql_fetch_assoc($sub_topic_list))
														{
															$retval .= "<SubTopic>";
															$sub_id = $sub_topic['topic_id'];
															$sub_name = $sub_topic['topic_name'];
                                                            $sub_info = $sub_topic['topic_info'];
															$saved_time = "select cumulative_time from topic_log where class_id = '$class_id' and topic_id = '$sub_id' order by transition_time desc limit 1";
															$get_time = SunDataBaseManager::getSingleton()->QueryDB($saved_time);
															$time_count = SunDataBaseManager::getSingleton()->getnoOfrows($get_time);
															if ($time_count > 0)
																{
																	$time = mysql_fetch_assoc($get_time);
																	$seconds = $time['cumulative_time'];
																	$cumulative_time = gmdate("H:i:s", $seconds);
																}
															else
																{
																	$cumulative_time = "00:00:00";
																}
                                                                $main_cumulative_time = strtotime($main_cumulative_time)+strtotime($cumulative_time);
                                                                $main_cumulative_time = gmdate("H:i:s", $main_cumulative_time);
                                                          
															//$percentage_tagged = "66%";
/*$topic_tagged = "SELECT topic_tagged FROM lesson_plan,topic WHERE lesson_plan.topic_id=topic.topic_id and topic_tagged = '1' and topic.parent_topic_id='$topic_id' and topic.subject_id='$subject_id'";
$topic_tagged_topic = SunDataBaseManager::getSingleton()->QueryDB($topic_tagged);
$topic_tagged_topic_count = SunDataBaseManager::getSingleton()->getnoOfrows($topic_tagged_topic );
if($topic_tagged_topic_count != 0){	
 if($topic_tagged_topic_count > $sub_topic_count)
 {
    $topic_tagged_topic_count = $sub_topic_count;
 }   
    
$percentage_tagged = (($topic_tagged_topic_count/$sub_topic_count)*100)."%";} else{$percentage_tagged = 0;}

*/

															if ($tagged_list)
																{
																	$check = in_array($sub_id, $tagged_list);
																	if ($check)
																		{
																			$retval .= "<Tagged>1</Tagged>";
																		}
																	else
																		{
																			$retval .= "<Tagged>0</Tagged>";
																		}
																}
															else
																{
																	$retval .= "<Tagged>0</Tagged>";
																}
															$initiated_questions = "select log.question_id from question_log as log inner join questions as question on question.question_id = log.question_id where question.topic_id = '$sub_id' group by log.question_id";
															$got_initiated_questions = SunDataBaseManager::getSingleton()->QueryDB($initiated_questions);
															$initiated_question_count = SunDataBaseManager::getSingleton()->getnoOfrows($got_initiated_questions);
															$sub_topic_questions = "select questions.question_id from questions inner join topic on questions.topic_id = topic.topic_id where questions.topic_id = '$sub_id'";
															$got_sub_topic_questions = SunDataBaseManager::getSingleton()->QueryDB($sub_topic_questions);
															$sub_topic_question_count = SunDataBaseManager::getSingleton()->getnoOfrows($got_sub_topic_questions);
if($sub_topic_question_count != 0){															
$sub_topic_percentage_started = (($initiated_question_count/$sub_topic_question_count)*100)."%";} else { $sub_topic_percentage_started =0;}
$school = SunDataBaseManager::getSingleton()->QueryDB("SELECT school_id FROM subjects WHERE subject_id='$subject_id'");
$question = mysql_fetch_assoc($school);
$school_id = $question['school_id'];

$grasp_index = $this->mXMLManager->GetGraspIndex($school_id,null,null,$sub_id);

															$retval .= "<Id>$sub_id</Id><Name>$sub_name</Name><Info>$sub_info</Info><CumulativeTime>$cumulative_time</CumulativeTime><PercentageStarted>$sub_topic_percentage_started</PercentageStarted><GraspIndex>$grasp_index</GraspIndex>";
															$questions = "select question.question_id, type.question_type_title, question_name, image.image_path from questions as question inner join question_types as type on question.question_type_id = type.question_type_id left join uploaded_images as image on image.image_id = question.scribble_id where question.topic_id = '$sub_id'";
															$question_list = SunDataBaseManager::getSingleton()->QueryDB($questions);
															$question_count = SunDataBaseManager::getSingleton()->getnoOfrows($question_list);
															if($question_count > 0)
																{
																	$retval .= "<Questions>";
																	while($question = mysql_fetch_assoc($question_list))
																		{
																			$question_id = $question['question_id'];
																			$question_type = $question['question_type_title'];
																			$question_title = $question['question_name'];
																			$scribble = $question['image_path'];

//over the whole school                                                                            
$ques_scores = "SELECT answer_score FROM assessment_answers WHERE question_log_id in (select question_log_id from question_log where question_id='$question_id')";                                                                            
$scores_list = SunDataBaseManager::getSingleton()->QueryDB($ques_scores);                                                                            
$scores_count = SunDataBaseManager::getSingleton()->getnoOfrows($scores_list);
$sum_of_scores = 0;
while($scores = mysql_fetch_assoc($scores_list))
{
    $thescore = $scores['answer_score'];
    if($thescore == "" || $thescore == null)
    {
        $thescore = 0;
    }
    $sum_of_scores += $thescore;
}
if($scores_count == 0)
{
    $avg_score = 0;
}
else
{
    $avg_score = $sum_of_scores/$scores_count;    
}


																			$retval .= "<Question><Id>$question_id</Id><Type>$question_type</Type><Name>$question_title</Name><QuestonAvgScore>$avg_score</QuestonAvgScore><NumberOfResponses>$scores_count</NumberOfResponses>";
																			if ($question_type == "Multiple Choice" or $question_type == "Multiple Response")
																				{
																					$options = "select question_option_id,question_option, is_answer from question_options where question_id = '$question_id'";
																					$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
																					$retval .= "<Options>";
																					while($option = mysql_fetch_assoc($option_list))
																						{
																						  $option_id = $option['question_option_id'];
																							$option_text = $option['question_option'];
																							$answer = $option['is_answer'];
																							$retval .= "<Option><OptionId>$option_id</OptionId><OptionText>$option_text</OptionText><IsAnswer>$answer</IsAnswer></Option>";
																						}
																					$retval .= "</Options></Question>";
																				}
																			else if ($question_type == "Fresh Scribble" or $question_type == "Text")
																				{
																					$retval .= "</Question>";
																				}
																			else if ($question_type == "Overlay Scribble")
																				{
																					$retval .= "<Scribble>$scribble</Scribble></Question>";
																				}
																				else if ($question_type == "One string")
																				{
																					$retval .= "</Question>";
																				}
																			else if ($question_type == "Match Columns")
																				{
																					$options = "select question_option_id,question_option, mtc_column, mtc_sequence from question_options where question_id = '$question_id'";
																					$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
																					$retval .= "<Options>";
																					while($option = mysql_fetch_assoc($option_list))
																						{
																							$option_text = $option['question_option'];
                                                                                            $option_id = $option['question_option_id'];
																							$column = $option['mtc_column'];
																							$sequence = $option['mtc_sequence'];
																							$retval .= "<Option><OptionId>$option_id</OptionId><OptionText>$option_text</OptionText><Column>$column</Column><Sequence>$sequence</Sequence></Option>";
																						}
																					$retval .= "</Options></Question>";
																				}
                                                                            else 
																				{
																					$retval .= "</Question>";
																				}
																		}
																	$retval .= "</Questions></SubTopic>";
																}
															else
																{
																	$retval .= "</SubTopic>";
																}
														}
													$retval .= "</SubTopics><CumulativeTime>$main_cumulative_time</CumulativeTime></MainTopic>";
												}
											else
												{
													$retval .= "</MainTopic>";
												}
										}
									$retval .= "</MainTopics>";
								}
							else
								{
									$retval .= "<Status>There are no topics for this subject currently.</Status>";
								}
							$retval .= "</Action></SunStone></Root>";
							return $retval;
                         } 
                         else if($main_topic_id == null && $type == "Only MainTopics")
                         {
                            
                        
							$tagged_topics = "select topic_id from lesson_plan where class_id = '$class_id'";
							$tagged_topic_list = SunDataBaseManager::getSingleton()->QueryDB($tagged_topics);
							$tagged_count = SunDataBaseManager::getSingleton()->getnoOfrows($tagged_topic_list);
							if ($tagged_count > 0)
								{
									unset($tagged_list1);
									while($tagged_topic = mysql_fetch_assoc($tagged_topic_list))
										{
											$tagged_list1[] = $tagged_topic['topic_id'];
										}
								    $str = implode (", ", $tagged_list1);
                                }
                                else
                                {
                                    $str = "";
                                }
                                
                                
							$tagged_topics = "select topic_id from lesson_plan where topic_tagged = '1' and class_id = '$class_id'";
							$tagged_topic_list = SunDataBaseManager::getSingleton()->QueryDB($tagged_topics);
							$tagged_count = SunDataBaseManager::getSingleton()->getnoOfrows($tagged_topic_list);
							if ($tagged_count > 0)
								{
									unset($tagged_list);
									while($tagged_topic = mysql_fetch_assoc($tagged_topic_list))
										{
											$tagged_list[] = $tagged_topic['topic_id'];
										}
								}
                                
							$retval = "<Root><SunStone><Action>";
							$topics = "select topic_id, topic_name, topic_info from topic where parent_topic_id is null and subject_id = '$subject_id'";
                            if($str != "")
                            {
                              $topics.= " and topic_id in($str)";  
                            } 
                           //echo $topics;
							$topic_list = SunDataBaseManager::getSingleton()->QueryDB($topics);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($topic_list);
							if($count > 0)
								{
									$retval .= "<Status>Success</Status>";
									$retval .= "<MainTopics>";
									while($topic = mysql_fetch_assoc($topic_list))
										{
											$topic_id = $topic['topic_id'];
											$topic_name = $topic['topic_name'];
                                            $topic_info = $topic['topic_info'];
											$retval .= "<MainTopic>";
											if ($tagged_list)
												{
													$check = in_array($topic_id, $tagged_list);
													if ($check)
														{
														  //echo "tagged ".$topic_id;
															$retval .= "<Tagged>1</Tagged>";
														}
													else
														{
															$retval .= "<Tagged>0</Tagged>";
														}
												}
											else
												{
													$retval .= "<Tagged>0</Tagged>";
												}
											$initiated_topic = "select log.topic_id from topic_log as log inner join topic on log.topic_id = topic.topic_id and log.cumulative_time > 0 where topic.parent_topic_id = '$topic_id' and log.class_id = '$class_id' group by log.topic_id";
											$got_initiated_topic = SunDataBaseManager::getSingleton()->QueryDB($initiated_topic);
											$initiated_topic_count = SunDataBaseManager::getSingleton()->getnoOfrows($got_initiated_topic);
											$sub_topic_tagged = "select plan.topic_id from lesson_plan as plan inner join topic on plan.topic_id = topic.topic_id where topic.parent_topic_id = '$topic_id' and plan.class_id = '$class_id'";
											$got_tagged_sub_topic = SunDataBaseManager::getSingleton()->QueryDB($sub_topic_tagged );
											$tagged_sub_topic_count = SunDataBaseManager::getSingleton()->getnoOfrows($got_tagged_sub_topic );


$parent_tagged = "SELECT topic_tagged FROM lesson_plan,topic WHERE lesson_plan.topic_id=topic.topic_id and topic_tagged = '1' and topic.parent_topic_id='$topic_id' and topic.subject_id='$subject_id' and lesson_plan.class_id ='$class_id'";
$parent_tagged_topic = SunDataBaseManager::getSingleton()->QueryDB($parent_tagged);
$parent_tagged_topic_count = SunDataBaseManager::getSingleton()->getnoOfrows($parent_tagged_topic );
							$topics5 = "select topic_id, topic_name, topic_info from topic where parent_topic_id = '$topic_id' and subject_id = '$subject_id'";
							$topic_list5 = SunDataBaseManager::getSingleton()->QueryDB($topics5);
							$count5 = SunDataBaseManager::getSingleton()->getnoOfrows($topic_list5);
if($parent_tagged_topic_count != 0){	
 if($parent_tagged_topic_count > $count5)
 {
    $parent_tagged_topic_count = $count5;
 }   
    
$topic_percentage_tagged = (($parent_tagged_topic_count/$count5)*100)."%";} else{$topic_percentage_tagged = 0;}

if($tagged_sub_topic_count != 0){											
$sub_topic_percentage_started = (($initiated_topic_count/$tagged_sub_topic_count)*100)."%";} else{$sub_topic_percentage_started = 0;}

									$sub_topics = "select topic_id from topic where parent_topic_id = '$topic_id' and subject_id = '$subject_id'";
											$sub_topic_list = SunDataBaseManager::getSingleton()->QueryDB($sub_topics);
											$sub_topic_count = SunDataBaseManager::getSingleton()->getnoOfrows($sub_topic_list);
                                            $main_cumulative_time =0;
											if($sub_topic_count > 0)
												{
													
													while($sub_topic = mysql_fetch_assoc($sub_topic_list))
														{
															
															$sub_id = $sub_topic['topic_id'];
															$saved_time = "select cumulative_time from topic_log where class_id = '$class_id' and topic_id = '$sub_id' order by transition_time desc limit 1";
															$get_time = SunDataBaseManager::getSingleton()->QueryDB($saved_time);
															$time_count = SunDataBaseManager::getSingleton()->getnoOfrows($get_time);
															if ($time_count > 0)
																{
																	$time = mysql_fetch_assoc($get_time);
																	$seconds = $time['cumulative_time'];
																	$cumulative_time = gmdate("H:i:s", $seconds);
																}
															else
																{
																	$cumulative_time = "00:00:00";
																}
                                                                $main_cumulative_time = strtotime($main_cumulative_time)+strtotime($cumulative_time);
                                                                $main_cumulative_time = gmdate("H:i:s", $main_cumulative_time);
                                                        }
                                                }
											$retval .= "<Id>$topic_id</Id><Name>$topic_name</Name><Info>$topic_info</Info><PercentageStarted>$sub_topic_percentage_started </PercentageStarted><PercentageTagged>$topic_percentage_tagged</PercentageTagged>";
											$sub_topics = "select topic_id, topic_name, topic_info from topic where parent_topic_id = '$topic_id' and subject_id = '$subject_id'";
											$sub_topic_list = SunDataBaseManager::getSingleton()->QueryDB($sub_topics);
											$sub_topic_count = SunDataBaseManager::getSingleton()->getnoOfrows($sub_topic_list);
                                            
                                            $school = SunDataBaseManager::getSingleton()->QueryDB("SELECT school_id FROM subjects WHERE subject_id='$subject_id'");
                                            $question = mysql_fetch_assoc($school);
                                            $school_id = $question['school_id'];
                                            $grasp_index = $this->mXMLManager->GetMainGraspIndex($school_id,null,null,$topic_id,$subject_id);   
                                                                                     
                                            $retval .= "<GraspIndex>$grasp_index</GraspIndex><TaggedSubTopicCount>$parent_tagged_topic_count</TaggedSubTopicCount><SubTopicCount>$tagged_sub_topic_count</SubTopicCount><CumulativeTime>$main_cumulative_time</CumulativeTime></MainTopic>";
										}
									$retval .= "</MainTopics>";
								}
							else
								{
									$retval .= "<Status>There are no topics for this subject currently.</Status>";
								}
							$retval .= "</Action></SunStone></Root>";
							return $retval;
                         }
						 else if($main_topic_id != null && $type == "Only SubTopics")
                          {
                            
                        
							$tagged_topics = "select topic_id from lesson_plan where class_id = '$class_id'";
							$tagged_topic_list = SunDataBaseManager::getSingleton()->QueryDB($tagged_topics);
							$tagged_count = SunDataBaseManager::getSingleton()->getnoOfrows($tagged_topic_list);
							if ($tagged_count > 0)
								{
									unset($tagged_list1);
									while($tagged_topic = mysql_fetch_assoc($tagged_topic_list))
										{
											$tagged_list1[] = $tagged_topic['topic_id'];
										}
								    $str = implode (", ", $tagged_list1);
                                }
                                else
                                {
                                    $str = "";
                                }
                                
                                
							$tagged_topics = "select topic_id from lesson_plan where topic_tagged = '1' and class_id = '$class_id'";
							$tagged_topic_list = SunDataBaseManager::getSingleton()->QueryDB($tagged_topics);
							$tagged_count = SunDataBaseManager::getSingleton()->getnoOfrows($tagged_topic_list);
							if ($tagged_count > 0)
								{
									unset($tagged_list);
									while($tagged_topic = mysql_fetch_assoc($tagged_topic_list))
										{
											$tagged_list[] = $tagged_topic['topic_id'];
										}
								}
                                
							$retval = "<Root><SunStone><Action>";
									$retval .= "<Status>Success</Status>";
											$sub_topics = "select topic_id, topic_name, topic_info from topic where parent_topic_id = '$main_topic_id' and subject_id = '$subject_id'";
											$sub_topic_list = SunDataBaseManager::getSingleton()->QueryDB($sub_topics);
											$sub_topic_count = SunDataBaseManager::getSingleton()->getnoOfrows($sub_topic_list);
                                            
											if($sub_topic_count > 0)
												{
													$retval .= "<SubTopics>";
													while($sub_topic = mysql_fetch_assoc($sub_topic_list))
														{
															$retval .= "<SubTopic>";
															$sub_id = $sub_topic['topic_id'];
															$sub_name = $sub_topic['topic_name'];
                                                            $sub_info = $sub_topic['topic_info'];
															$saved_time = "select cumulative_time from topic_log where class_id = '$class_id' and topic_id = '$sub_id' order by transition_time desc limit 1";
															$get_time = SunDataBaseManager::getSingleton()->QueryDB($saved_time);
															$time_count = SunDataBaseManager::getSingleton()->getnoOfrows($get_time);
															if ($time_count > 0)
																{
																	$time = mysql_fetch_assoc($get_time);
																	$seconds = $time['cumulative_time'];
																	$cumulative_time = gmdate("H:i:s", $seconds);
																}
															else
																{
																	$cumulative_time = "00:00:00";
																}
															//$percentage_tagged = "66%";
/*$topic_tagged = "SELECT topic_tagged FROM lesson_plan,topic WHERE lesson_plan.topic_id=topic.topic_id and topic_tagged = '1' and topic.parent_topic_id='$topic_id' and topic.subject_id='$subject_id'";
$topic_tagged_topic = SunDataBaseManager::getSingleton()->QueryDB($topic_tagged);
$topic_tagged_topic_count = SunDataBaseManager::getSingleton()->getnoOfrows($topic_tagged_topic );
if($topic_tagged_topic_count != 0){	
 if($topic_tagged_topic_count > $sub_topic_count)
 {
    $topic_tagged_topic_count = $sub_topic_count;
 }   
    
$percentage_tagged = (($topic_tagged_topic_count/$sub_topic_count)*100)."%";} else{$percentage_tagged = 0;}

*/
$school = SunDataBaseManager::getSingleton()->QueryDB("SELECT school_id FROM subjects WHERE subject_id='$subject_id'");
$question = mysql_fetch_assoc($school);
$school_id = $question['school_id'];

															$grasp_index = $this->mXMLManager->GetGraspIndex($school_id,null,null,$sub_id);
															if ($tagged_list)
																{
																	$check = in_array($sub_id, $tagged_list);
																	if ($check)
																		{
																			$retval .= "<Tagged>1</Tagged>";
																		}
																	else
																		{
																			$retval .= "<Tagged>0</Tagged>";
																		}
																}
															else
																{
																	$retval .= "<Tagged>0</Tagged>";
																}
															$initiated_questions = "select log.question_id from question_log as log inner join questions as question on question.question_id = log.question_id where question.topic_id = '$sub_id' group by log.question_id";
															$got_initiated_questions = SunDataBaseManager::getSingleton()->QueryDB($initiated_questions);
															$initiated_question_count = SunDataBaseManager::getSingleton()->getnoOfrows($got_initiated_questions);
															$sub_topic_questions = "select questions.question_id from questions inner join topic on questions.topic_id = topic.topic_id where questions.topic_id = '$sub_id'";
															$got_sub_topic_questions = SunDataBaseManager::getSingleton()->QueryDB($sub_topic_questions);
															$sub_topic_question_count = SunDataBaseManager::getSingleton()->getnoOfrows($got_sub_topic_questions);
if($sub_topic_question_count != 0){															
$sub_topic_percentage_started = (($initiated_question_count/$sub_topic_question_count)*100)."%";} else { $sub_topic_percentage_started =0;}
															$retval .= "<Id>$sub_id</Id><Name>$sub_name</Name><Info>$sub_info</Info><CumulativeTime>$cumulative_time</CumulativeTime><PercentageStarted>$sub_topic_percentage_started</PercentageStarted><GraspIndex>$grasp_index</GraspIndex>";
															$questions = "select question.question_id, type.question_type_title, question_name, image.image_path from questions as question inner join question_types as type on question.question_type_id = type.question_type_id left join uploaded_images as image on image.image_id = question.scribble_id where question.topic_id = '$sub_id'";
															$question_list = SunDataBaseManager::getSingleton()->QueryDB($questions);
															$question_count = SunDataBaseManager::getSingleton()->getnoOfrows($question_list);
															$retval .= "<QuestionCount>$question_count</QuestionCount></SubTopic>";
														  
														}
													$retval .= "</SubTopics>";
												}else
								{
									$retval .= "<Status>There are no subtopics for this topic currently.</Status>";
								}
							$retval .= "</Action></SunStone></Root>";
							return $retval;
                         }
						  if($main_topic_id != null && $type == "Only Questions")
                          {
                            
                        
                                
							$retval = "<Root><SunStone><Action>";

									$retval .= "<Status>Success</Status>";

															$questions = "select question.question_id, type.question_type_title, question_name, image.image_path from questions as question inner join question_types as type on question.question_type_id = type.question_type_id left join uploaded_images as image on image.image_id = question.scribble_id where question.topic_id = '$main_topic_id'";
															$question_list = SunDataBaseManager::getSingleton()->QueryDB($questions);
															$question_count = SunDataBaseManager::getSingleton()->getnoOfrows($question_list);
															if($question_count > 0)
																{
																	$retval .= "<Questions>";
																	while($question = mysql_fetch_assoc($question_list))
																		{
																			$question_id = $question['question_id'];
																			$question_type = $question['question_type_title'];
																			$question_title = $question['question_name'];
																			$scribble = $question['image_path'];

//over the whole school                                                                            
$ques_scores = "SELECT answer_score FROM assessment_answers WHERE question_log_id in (select question_log_id from question_log where question_id='$question_id')";                                                                            
$scores_list = SunDataBaseManager::getSingleton()->QueryDB($ques_scores);                                                                            
$scores_count = SunDataBaseManager::getSingleton()->getnoOfrows($scores_list);
$sum_of_scores = 0;
while($scores = mysql_fetch_assoc($scores_list))
{
    $thescore = $scores['answer_score'];
    if($thescore == "" || $thescore == null)
    {
        $thescore = 0;
    }
    $sum_of_scores += $thescore;
}
if($scores_count == 0)
{
    $avg_score = 0;
}
else
{
    $avg_score = $sum_of_scores/$scores_count;    
}



																			$retval .= "<Question><Id>$question_id</Id><Type>$question_type</Type><Name>$question_title</Name><QuestonAvgScore>$avg_score</QuestonAvgScore><NumberOfResponses>$scores_count</NumberOfResponses>";
																			if ($question_type == "Multiple Choice" or $question_type == "Multiple Response")
																				{
																					$options = "select question_option_id,question_option, is_answer from question_options where question_id = '$question_id'";
																					$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
																					$retval .= "<Options>";
																					while($option = mysql_fetch_assoc($option_list))
																						{
																							$option_text = $option['question_option'];
                                                                                            $option_id = $option['question_option_id'];
																							$answer = $option['is_answer'];
																							$retval .= "<Option><OptionId>$option_id</OptionId><OptionText>$option_text</OptionText><IsAnswer>$answer</IsAnswer></Option>";
																						}
																					$retval .= "</Options></Question>";
																				}
																			else if ($question_type == "Fresh Scribble" or $question_type == "Text")
																				{
																					$retval .= "</Question>";
																				}
																			else if ($question_type == "Overlay Scribble")
																				{
																					$retval .= "<Scribble>$scribble</Scribble></Question>";
																				}
																				else if ($question_type == "One string")
																				{
																					$retval .= "</Question>";
																				}
																			else if ($question_type == "Match Columns")
																				{
																					$options = "select question_option_id,question_option, mtc_column, mtc_sequence from question_options where question_id = '$question_id'";
																					$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
																					$retval .= "<Options>";
																					while($option = mysql_fetch_assoc($option_list))
																						{
																							$option_text = $option['question_option'];
                                                                                            $option_id = $option['question_option_id'];
																							$column = $option['mtc_column'];
																							$sequence = $option['mtc_sequence'];
																							$retval .= "<Option><OptionId>$option_id</OptionId><OptionText>$option_text</OptionText><Column>$column</Column><Sequence>$sequence</Sequence></Option>";
																						}
																					$retval .= "</Options></Question>";
																				}
																		}
																	$retval .= "</Questions>";
																}



							else
								{
									$retval .= "<Status>There are no questions for this subtopic currently.</Status>";
								}
							$retval .= "</Action></SunStone></Root>";
							return $retval;
                         }                                                                           
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function SetCurrentTopic($session_id=null,$topic_id=null,$student_id=null)
				{
					try
						{
							$update = "update live_session_status set current_topic = '$topic_id' where session_id = '$session_id'";
							$verify = SunDataBaseManager::getSingleton()->QueryDB($update);
							if ($verify)
								{
									$get_class = "select class_id from class_sessions where class_session_id = '$session_id'";
									$class_info = SunDataBaseManager::getSingleton()->QueryDB($get_class);
									$class = mysql_fetch_assoc($class_info);
									$class_id = $class['class_id'];
									$get_oldtime = "select cumulative_time, state from topic_log where class_id = '$class_id' and topic_id = '$topic_id' order by transition_time desc limit 1";
									$got_oldtime = SunDataBaseManager::getSingleton()->QueryDB($get_oldtime);
									$oldtime_count = SunDataBaseManager::getSingleton()->getnoOfrows($got_oldtime);
									if ($oldtime_count > 0)
										{
											$oldrecord = mysql_fetch_assoc($got_oldtime);
											$cumulative_time = $oldrecord['cumulative_time'];
											//$current_time3=date('Y-m-d H:i:s');
											$state = $oldrecord['state'];
											if ($state == "24")
												{
													$log_topic = "insert into topic_log (class_id, topic_id, class_session_id, cumulative_time, state) values ('$class_id','$topic_id','$session_id','$cumulative_time','22')";
													$logged_topic = SunDataBaseManager::getSingleton()->QueryDB($log_topic);
												}
										}
									else
										{
											$log_topic = "insert into topic_log (class_id, topic_id, class_session_id, state) values ('$class_id','$topic_id', '$session_id','22')";
											$logged_topic = SunDataBaseManager::getSingleton()->QueryDB($log_topic);
										}
									$arr[SMC::$STATUS] = "Success";
									/********************* changes ************************/
									$select_old_time="select topic_session_time from topic_log where topic_id='$topic_id' and class_session_id='$session_id' and state=24 order by transition_time desc limit 1";
									$old_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_old_time);
									$check_time_entries=SunDataBaseManager::getSingleton()->getnoOfrows($old_time_query);
									if($check_time_entries>0)
									{
										$old_time_fetch=mysql_fetch_assoc($old_time_query);
										$old_tpc_time=$old_time_fetch['topic_session_time'];
										$update_tpc_time="update topic_log set topic_session_time='$old_tpc_time' where topic_id='$topic_id' and class_session_id='$session_id' and state=22 order by transition_time desc limit 1";
										$tpc_time_query2=SunDataBaseManager::getSingleton()->QueryDB($update_tpc_time);
									}
									//Selects live students mapped to the current session
									$students="SELECT student_id FROM student_class_map, class_sessions INNER JOIN tbl_auth WHERE tbl_auth.user_state=1 AND student_class_map.student_id=tbl_auth.user_id AND student_class_map.class_id='$class_id' AND class_sessions.class_session_id='$session_id'";
									$student_ids=SunDataBaseManager::getSingleton()->QueryDB($students);
									while ($row=mysql_fetch_array($student_ids))
									{
										$student=$row['student_id'];
										$check_entry="select student_id from stud_topic_time where student_id='$student' and topic_id='$topic_id' and class_session_id='$session_id'";
										$check_entries=SunDataBaseManager::getSingleton()->QueryDB($check_entry);
										$check_student_count=SunDataBaseManager::getSingleton()->getnoOfrows($check_entries);
										if ($check_student_count==0) {
											$last_seen_time="select current_timestamp";
											$last_seen_query=SunDataBaseManager::getSingleton()->QueryDB($last_seen_time);
											$last_seen_fetch=mysql_fetch_assoc($last_seen_query);
											$last_seen=$last_seen_fetch['current_timestamp'];
											$insert_stud="insert into stud_topic_time(student_id,class_session_id,topic_id,last_seen, stud_time) values('$student','$session_id','$topic_id', '$last_seen',0)";
											$insert_query=SunDataBaseManager::getSingleton()->QueryDB($insert_stud);
										}
										elseif($check_student_count>0)
										{
											$last_seen_time="select current_timestamp";
											$last_seen_query=SunDataBaseManager::getSingleton()->QueryDB($last_seen_time);
											$last_seen_fetch=mysql_fetch_assoc($last_seen_query);
											$last_seen=$last_seen_fetch['current_timestamp'];
											$update_last_seen="update stud_topic_time set last_seen='$last_seen' where student_id='$student' and topic_id='$topic_id' and class_session_id='$session_id'";
											$update_last_seen_query=SunDataBaseManager::getSingleton()->QueryDB($update_last_seen);
										}
									}
									/********************* end of changes ************************/
                                  					if (!empty($student_id))
														{
														  
															$count = substr_count($student_id, ';;;');
															if ($count>=1)
																{
																	$removed = explode(";;;",$student_id);
																}
															else
																{
																	$removed = $student_id;
																}
														}
													else
														{
															$removed = null;
														}
                                                        
                                                        
													if ($removed != null)
													{
													   if(is_array($removed))
                                                       {
													        $i = 0;
    													    foreach($removed as $student)
    														{
    														  
    
                                                                $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID] = $student;
                                                                $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$GRASPINDEX] = $this->mXMLManager->GetGraspIndex(null,$session_id,$student,$topic_id);
                                                                              
                                                                $i++;
                                                            }                                                        
                                                       }
                                                       else
                                                       {
                                                                $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID] = $removed;
                                                                $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$GRASPINDEX] = $this->mXMLManager->GetGraspIndex(null,$session_id,$removed,$topic_id);
                                                       
                                                       }

                                                    }                                                                             
								}
							else
								{
									$arr[SMC::$STATUS] = "Current topic could not be set, please try again.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function StopTopic($session_id=null,$topic_id=null)
				{
					try
						{
							$update = "update live_session_status set current_topic = null where session_id = '$session_id'";
							$verify = SunDataBaseManager::getSingleton()->QueryDB($update);
							if ($verify)
								{
									$get_class = "select class_id from class_sessions where class_session_id = '$session_id'";
									$class_info = SunDataBaseManager::getSingleton()->QueryDB($get_class);
									$class = mysql_fetch_assoc($class_info);
									$class_id = $class['class_id'];
									$get_oldtime = "select cumulative_time, transition_time from topic_log where class_id = '$class_id' and topic_id = '$topic_id' and state = '22' order by transition_time desc limit 1";
									$got_oldtime = SunDataBaseManager::getSingleton()->QueryDB($get_oldtime);
									$oldtime = mysql_fetch_assoc($got_oldtime);
									$cumulative_time = $oldtime['cumulative_time'];
									$old_cum_time=$cumulative_time;
									$start_time = $oldtime['transition_time'];
									$started_time = strtotime($start_time);
									$select_curr_time="select current_timestamp";
									$curr_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_curr_time);
									$curr_time_fetch=mysql_fetch_assoc($curr_time_query);
									$curr_time=$curr_time_fetch['current_timestamp'];
									$current_time = strtotime($curr_time);
									$time_difference = $current_time - $started_time;
									$cumulative_time += $time_difference;
									$log_topic = "insert into topic_log (class_id, topic_id,class_session_id, cumulative_time, state) values ('$class_id','$topic_id','$session_id','$cumulative_time','24')";
									$logged_topic = SunDataBaseManager::getSingleton()->QueryDB($log_topic);
									$arr[SMC::$STATUS] = "Success";
									/********************* changes ************************/
									//To update topic_session_time in topic_log
									$select_trans_time="select time(transition_time) as transition_time_2,topic_session_time from topic_log where topic_id='$topic_id' and class_session_id='$session_id' and state=22 order by transition_time desc limit 1";
									$trans_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_trans_time);
									$trans_time_fetch=mysql_fetch_assoc($trans_time_query);
									$trans_time=$trans_time_fetch['transition_time_2'];
									$tpc_session_time2=$trans_time_fetch['topic_session_time'];
									$select_curr_time="select time(current_timestamp) as time3";
									$curr_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_curr_time);
									$curr_time_fetch=mysql_fetch_assoc($curr_time_query);
									$curr_time2=$curr_time_fetch['time3'];
									$topic_session_time=$tpc_session_time2+(strtotime($curr_time2)-strtotime($trans_time));
									$update_tpc_time="update topic_log set topic_session_time='$topic_session_time' where topic_id='$topic_id' and class_session_id='$session_id' and state=24 order by transition_time desc limit 1";
									$tpc_time_query=SunDataBaseManager::getSingleton()->QueryDB($update_tpc_time);
									//To update stud_topic_time
									$students="select student_id from stud_topic_time inner join tbl_auth on stud_topic_time.student_id=tbl_auth.user_id where topic_id='$topic_id' and class_session_id='$session_id' and tbl_auth.user_state=1";
									$student_ids=SunDataBaseManager::getSingleton()->QueryDB($students);
									while ($row=mysql_fetch_array($student_ids))
									{
										$student=$row['student_id'];
										$duration_query="select stud_time,last_seen from stud_topic_time where student_id='$student' and topic_id='$topic_id' and class_session_id='$session_id'";
										$duration_fetch=SunDataBaseManager::getSingleton()->QueryDB($duration_query);
										$duration=mysql_fetch_assoc($duration_fetch);
										$stud_time=$duration['stud_time'];
										$last_seen=$duration['last_seen'];
										$select_curr_time="select current_timestamp";
										$curr_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_curr_time);
										$curr_time_fetch=mysql_fetch_assoc($curr_time_query);
										$curr_time=$curr_time_fetch['current_timestamp'];
										$current_time=strtotime($curr_time);
										$last_seen_time=strtotime($last_seen);
										$stud_time+=$current_time-$last_seen_time;
										$update_cum_time="update stud_topic_time set stud_time='$stud_time', last_seen='$curr_time' where student_id='$student' and topic_id='$topic_id' and class_session_id='$session_id'";
										$update_query=SunDataBaseManager::getSingleton()->QueryDB($update_cum_time);
									}
									/********************* end of changes ************************/
								}
							else
								{
									$arr[SMC::$STATUS] = "Current topic could not be set, please try again.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function OpenQueryForVoting($query_id=null)
				{
					try
						{
							$change_state = "update student_query set state = '18' where query_id = '$query_id'";
							$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
							if ($change)
								{
									$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$query_id','16','18')";
									$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
									$arr[SMC::$STATUS] = "Success";
								}
							else
								{
									$arr[SMC::$STATUS] = "Query could not be opened for voting, please try again.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function BroadcastQuestion($session_id=null, $question_id=null)
				{
					try
						{
							$log = "insert into question_log (question_id, class_session_id) values ('$question_id', '$session_id')";
							$verify = SunDataBaseManager::getSingleton()->QueryDB($log);
							if ($verify)
								{
									$id = SunDataBaseManager::getSingleton()->getLastInsertId();
									$update_question = "update live_session_status set current_question = '$question_id', question_instance_id = '$id' where session_id = '$session_id'";
									$updated_question = SunDataBaseManager::getSingleton()->QueryDB($update_question);
									$arr[SMC::$STATUS] = "Success";
									$arr[SMC::$QUESTIONLOGID] = $id;
						         	$retrieve_votes = "select qt.question_type_title from questions q inner join question_types qt where q.question_id = '$question_id' and qt.question_type_id=q.question_type_id";
						        	$retrieve = SunDataBaseManager::getSingleton()->QueryDB($retrieve_votes);
									$votes = mysql_fetch_assoc($retrieve);
                                    $type = $votes['question_type_title'];
                                    $arr[SMC::$QUESTIONTYPE] = $type;                         
								}
							else
								{
									$arr[SMC::$STATUS] = "Could not broadcast question, please try again.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function CloseQuestionResponse($question_log_id=null)
				{
					try
						{
							$change_state = "update question_log set freeze_time = NOW() where question_log_id = '$question_log_id'";
							$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
							if ($change)
								{
									$arr[SMC::$STATUS] = "Success";                   
                                                         
								}
							else
								{
									$arr[SMC::$STATUS] = "Question could not be closed, please try again.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function EndQuestionInstance($question_log_id=null,$topic_id=null, $student_id=null)
				{
					try
						{
							$change_state = "update question_log set active_question = '0', end_time = NOW() where question_log_id = '$question_log_id'";
							$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
							if ($change)
								{
									$get_class = "select class_session_id from question_log where question_log_id = '$question_log_id'";
									$class_info = SunDataBaseManager::getSingleton()->QueryDB($get_class);
									$class = mysql_fetch_assoc($class_info);
									$session_id = $class['class_session_id'];								    
									$update_question = "update live_session_status set current_question = null, question_instance_id = null where session_id = '$session_id'";
									$updated_question = SunDataBaseManager::getSingleton()->QueryDB($update_question);
									$arr[SMC::$STATUS] = "Success";
                                   					if (!empty($student_id))
														{
														  
															$count = substr_count($student_id, ';;;');
															if ($count>=1)
																{
																	$removed = explode(";;;",$student_id);
																}
															else
																{
																	$removed = $student_id;
																}
														}
													else
														{
															$removed = null;
														}
                                                        
                                                        
													if ($removed != null)
													{
													   if(is_array($removed))
                                                       {
     													     $i = 0;
    													    foreach($removed as $student)
    														{
    														  
    
                                                                $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID] = $student;
                                                                $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$GRASPINDEX] = $this->mXMLManager->GetGraspIndex(null,$session_id,$student,$topic_id);
                                                                              
                                                                $i++;
                                                            }                                                       
                                                       }
                                                       else
                                                       {
                                                                $arr[SMC::$STUDENTS][SMC::$STUDENT][SMC::$STUDENTID] = $removed;
                                                                $arr[SMC::$STUDENTS][SMC::$STUDENT][SMC::$GRASPINDEX] = $this->mXMLManager->GetGraspIndex(null,$session_id,$removed,$topic_id);
                                                        
                                                       }

                                                    }                                                     
                                    
                                    
								}
							else
								{
									$arr[SMC::$STATUS] = "Question could not be closed, please try again.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function ApproveVolunteer($volunteer_id=null, $stoppedflag=null)
				{
					try
						{
						  if($volunteer_id != null)
                          {
                                    $state = 20;//LiveAnswering 
                                    if($stoppedflag == 1)
                                    {
                                        $state = 24;//Stopped
                                    }
                            		$get_class = "select state from query_volunteer where volunteer_id = '$volunteer_id'";
									$class_info = SunDataBaseManager::getSingleton()->QueryDB($get_class);
									$class = mysql_fetch_assoc($class_info);
									$exists = $class['state'];
                                    if($exists != null)
                                    {
                                        if($exists != '24' && $exists != '6')
                                        {
                 							$change_state = "update query_volunteer set state = '$state' where volunteer_id = '$volunteer_id'";
                							$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
                							if ($change)
                								{
                									if($exists!=$state)
                									{
                									$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('6','$volunteer_id','$exists','$state')";
                									$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
                									}
                									$arr[SMC::$STATUS] = "Success";
                                                    if($state == '24')
                                                    {
                                                        //pi
                            							$volunteer_state1 = "SELECT sq.class_session_id,qv.student_id,sq.topic_id FROM query_volunteer qv, student_query sq WHERE qv.query_id = sq.query_id and qv.volunteer_id='$volunteer_id'";
                            							$retrieve_volunteer_state1 = SunDataBaseManager::getSingleton()->QueryDB($volunteer_state1);
                            							$current_volunteer_state1 = mysql_fetch_assoc($retrieve_volunteer_state1);     
                                                        $session_id = $current_volunteer_state1['class_session_id'];
                                                        $student_id = $current_volunteer_state1['student_id'];          
                                                        $topic_id = $current_volunteer_state1['topic_id'];
                                                        
                                                        //check if row exists 
                                                                        
                                                        $get_exists_id = "SELECT auto_id,subtotal_of_count FROM student_index WHERE class_session_id='$session_id' and student_id='$student_id' and transaction_id=31";
                                                        $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                        $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                        if ($exists_rows > 0)
                                                        {
                                                            $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                            $auto_id = $topic_id_get2['auto_id'];
                                                            $total_count = $topic_id_get2['subtotal_of_count'];
                                                            $total_count++;
                                                            //update if yes
                                                            $save_device = "update student_index set subtotal_of_count= '$total_count' where auto_id = '$auto_id'";
                                                            $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                        
                                                        } else
                                                        {
                                                            //insert if no
                                                            $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count) values ('$session_id','$topic_id','31','$student_id','1')";
                                                            $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                        
                                                        }                                      
                                                                                                                  
                                                        
                                                                                                            
                                                  }                                      
                                                    
                								}
                							else
                								{
                									$arr[SMC::$STATUS] = "Question could not be closed, please try again.";
                								}                                               
                                        }
                                      
                                    }
                                    else
                                    {
                                        $arr[SMC::$STATUS] = "Volunteer ID is invalid.";
                                    }
                      
                          }
                          else
                          {
                            $arr[SMC::$STATUS] = "Volunteer ID is not passed.";
                          }

							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function RetrieveVolunteerVotes($volunteer_id=null)
				{
					try
						{
							$retrieve_votes = "select thumbs_up, thumbs_down from query_volunteer where volunteer_id = '$volunteer_id'";
							$retrieve = SunDataBaseManager::getSingleton()->QueryDB($retrieve_votes);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($retrieve);
							if ($count>0)
								{
									$votes = mysql_fetch_assoc($retrieve);
									$arr[SMC::$STATUS] = "Success";
									$arr[SMC::$THUMBSUP] = $votes['thumbs_up'];
									$arr[SMC::$THUMBSDOWN] = $votes['thumbs_down'];
								}
							else
								{
									$arr[SMC::$STATUS] = "There are no votes for this volunteer yet, please try again later.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function RecordLessonPlan($class_id=null, $teacher_id=null, $topic_id=null)
				{
					try
						{
							$reset_tags = "update lesson_plan set topic_tagged = '0' where class_id = '$class_id'";
							$tags_reset = SunDataBaseManager::getSingleton()->QueryDB($reset_tags);
							$count = substr_count($topic_id, ',');
                            
							if ($count>=1)
								{
									$topics = explode(",",$topic_id);
								}
							else
								{
									$topics = $topic_id;
								}
							if (is_array($topics))
								{
									foreach($topics as $topic)
										{
											$update = "update lesson_plan set tagged_by = '$teacher_id', topic_tagged = '1' where class_id = '$class_id' and topic_id = '$topic'";
											$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
										}
									$arr[SMC::$STATUS] = "Success";
								}
							else
								{
									$update = "update lesson_plan set tagged_by = '$teacher_id', topic_tagged = '1' where class_id = '$class_id' and topic_id = '$topics'";
									$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
									$arr[SMC::$STATUS] = "Success";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function RecordModelAnswer($answer_id=null)
				{
					try
						{
							$record = "update assessment_answers set model_answer = '1' where assessment_answer_id = '$answer_id'";
							$recorded = SunDataBaseManager::getSingleton()->QueryDB($record);
							$arr[SMC::$STATUS] = "Success";
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function UploadTeacherScribble($image_path=null)
				{
					try
						{
							$upload = "select image_id from uploaded_images where image_path='$image_path'";
							$uploaded = SunDataBaseManager::getSingleton()->QueryDB($upload);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($uploaded);
							if ($count>0)
								{
									$votes = mysql_fetch_assoc($uploaded);
									$arr[SMC::$STATUS] = "Success";
									$arr[SMC::$SCRIBBLEID] = $votes['image_id'];
                                }
                                else
                                {
                                    $arr[SMC::$STATUS] = "Image path is not found";
                                }
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function RecordQuestion($session_id=null, $type_id=null, $topic_id=null, $teacher_id=null, $scribble_id=null, $question_title=null)
				{
					try
						{
							if (empty($scribble_id) or empty($question_title))
								{
									if (empty($scribble_id) and empty($question_title))
										{
											$add = "insert into questions(question_type_id, topic_id, teacher_id, on_the_fly) values ('$type_id', '$topic_id', '$teacher_id', '1')";
										}
									else if (empty($scribble_id))
										{
											$add = "insert into questions (question_type_id, topic_id, teacher_id, question_name, on_the_fly) values ('$type_id', '$topic_id', '$teacher_id', '$question_title', '1')";
										}
									else if (empty($question_title))
										{
											$add = "insert into questions (question_type_id, topic_id, teacher_id, scribble_id, on_the_fly) values ('$type_id', '$topic_id', '$teacher_id', '$scribble_id', '1')";
										}
								}
							else
								{
									$add = "insert into questions (question_type_id, topic_id, teacher_id, question_name, scribble_id, on_the_fly) values ('$type_id', '$topic_id', '$teacher_id', '$question_title', '$scribble_id', '1')";
								}
							$added = SunDataBaseManager::getSingleton()->QueryDB($add);
							$question_id = SunDataBaseManager::getSingleton()->getLastInsertId();
							if($added)
							{
								$arr[SMC::$STATUS] = "Success";
								$arr[SMC::$QUESTIONID] = $question_id;
							}
							else
							{
								$arr[SMC::$STATUS] = "Could not add the question, please try again.";
							}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
				// Change name of question_options to element_id
				//state 30, suggestion_options
			/*public function UpdateRecordedQuestion($question_id=null, $question_title=null,$element_id=null,$is_ans=null,$col_no=null,$seqno=null)
				{
					try
						{
							$update = "update questions set question_name = '$question_title' where question_id = '$question_id'";
							$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                            if (!empty($element_id))
							{
								$metooremoved = array();
                                $is_answerd = array();
                                $seqq1 = array();
                                $colnooo1 = array();
								$count2 = substr_count($element_id, ';;;');
								if ($count2>=1)
								{
									$metooremoved = explode(";;;",$element_id);
                                    $is_answerd = explode(";;;",$is_ans);
                                    $seqq1 = explode(";;;",$seqno);
                                    $colnooo1 = explode(";;;",$col_no);
								}
								else
								{
									$metooremoved = $element_id;
                                    $is_answerd = $is_ans;
                                    $seqq1 = $seqno;
                                    $colnooo1 = $col_no;
                                }
                                for($p = 0;$p < count($metooremoved);$p++)
                                {
                                	$state = $is_answerd[$p];
                                    $suggest_id = $metooremoved[$p];
                                    $seqq = $seqq1[$p];
                                    $colnooo = $colnooo1[$p];
                                    if(!empty($suggest_id))
                                    {
                                    	$isanswer = 0;
                                        if($state == '1')
                                        {
                                            $isanswer = 1;
                                        }
                                        $get_class = "select suggestion_txt,session_id,topic_id,student_id from suggestion_options inner join category on suggestion_options.cat_id=category.category_id where suggestion_id='$suggest_id'";
                                        $class_info = SunDataBaseManager::getSingleton()->QueryDB($get_class);
                                        $class = mysql_fetch_assoc($class_info);
                                       	$new_option_txt = $class['suggestion_txt'];
                                        $session_id = $class['session_id'];
                                        $topic_id = $class['topic_id'];
                                        $student_id = $class['student_id'];
                                        $add = "insert into question_options (question_id, question_option, is_answer, mtc_column, mtc_sequence) values ('$question_id', '$new_option_txt', '$isanswer', '$colnooo', '$seqq')";
                                    	$added = SunDataBaseManager::getSingleton()->QueryDB($add);
                                    	$question_option_id = SunDataBaseManager::getSingleton()->getLastInsertId();
                                    	//$update_state_q=""
                                    	$arr[SMC::$OPTIONS][SMC::$OPTIONID][$p]=$question_option_id;
					                }
					            }
					        }
					        $arr[SMC::$STATUS] = "Success";
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}*/
				public function UpdateRecordedQuestion($question_id=null, $question_title=null,$element_id=null,$is_ans=null,$col_no=null,$seqno=null,$user_id=null,$device_id=null)
				{
					try
					{
							$update = "update questions set question_name = '$question_title' where question_id = '$question_id'";
							$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
							if(!empty($element_id))
							{
								$count_elem=substr_count($element_id, ';;;');
								if($count_elem>=1)
								{
									$elements=explode(';;;', $element_id);
									$is_answers=explode(';;;', $is_ans);
									$columns=explode(';;;', $col_no);
									$sequences=explode(';;;', $seqno);
									$i=0;
									$option_id="";
									while ($i<count($elements))
									{
										$elem_id=$elements[$i];
										$isanswer=$is_answers[$i];
										$col_number=$columns[$i];
										$seq_number=$sequences[$i];
										$get_elem="select element_text,suggestion_options.suggestion_id,suggestion_state,session_id,topic_id,student_id
										from elements
										inner join suggestion_options
										on suggestion_options.suggestion_id=elements.suggestion_id
										inner join category
										on suggestion_options.cat_id=category.category_id
										where element_id='$elem_id'";
										$elem_q=SunDataBaseManager::getSingleton()->QueryDB($get_elem);
										$elem_f=mysql_fetch_assoc($elem_q);
										$elem_text=$elem_f['element_text'];
										$sess_id=$elem_f['session_id'];
										$topic_id=$elem_f['topic_id'];
										$suggestion_id=$elem_f['suggestion_id'];
										$suggestion_state=$elem_f['suggestion_state'];
										$insert_opt="insert into question_options (question_id, question_option, is_answer, mtc_column, mtc_sequence) values ('$question_id', '$elem_text', '$isanswer', '$col_number', '$seq_number')";
										$opt_q=SunDataBaseManager::getSingleton()->QueryDB($insert_opt);
										$opt_id=SunDataBaseManager::getSingleton()->getLastInsertId();
										if($i==0)
										{
											$option_id=$opt_id;
										}
										else
										{
											$option_id=$option_id.";;;".$opt_id;
										}
										if($suggestion_state!=30)
										{
											$current_time_select="select current_timestamp";
											$select_time_q=SunDataBaseManager::getSingleton()->QueryDB($current_time_select);
											$fetch_time=mysql_fetch_assoc($select_time_q);
											$curr_time=$fetch_time['current_timestamp'];
											$update_state="update suggestion_options set suggestion_state=30,last_updated='$curr_time' where suggestion_id='$suggestion_id'";
											$update_st_q=SunDataBaseManager::getSingleton()->QueryDB($update_state);
											$state_trans="insert into state_transitions values(7,'$suggestion_id','$suggestion_state',30,'$curr_time')";
											$update_state_trans=SunDataBaseManager::getSingleton()->QueryDB($state_trans);
										}
										$i++;
									}
								}
								else
								{
									$elements=$element_id;
									$is_answers=$is_ans;
									$columns=$col_no;
									$sequences=$seqno;
									$get_elem="select element_text,suggestion_options.suggestion_id,suggestion_state,session_id,topic_id,student_id
									from elements
									inner join suggestion_options
									on suggestion_options.suggestion_id=elements.suggestion_id
									inner join category
									on suggestion_options.cat_id=category.category_id
									where element_id='$elements'";
									$elem_q=SunDataBaseManager::getSingleton()->QueryDB($get_elem);
									$elem_f=mysql_fetch_assoc($elem_q);
									$elem_text=$elem_f['element_text'];
									$sess_id=$elem_f['session_id'];
									$topic_id=$elem_f['topic_id'];
									$suggestion_id=$elem_f['suggestion_id'];
									$suggestion_state=$elem_f['suggestion_state'];
									$insert_opt="insert into question_options (question_id, question_option, is_answer, mtc_column, mtc_sequence) values ('$question_id', '$elem_text', '$is_answers', '$columns', '$sequences')";
									$opt_q=SunDataBaseManager::getSingleton()->QueryDB($insert_opt);
									$option_id=SunDataBaseManager::getSingleton()->getLastInsertId();
									if($suggestion_state!=30)
									{
										$current_time_select="select current_timestamp";
										$select_time_q=SunDataBaseManager::getSingleton()->QueryDB($current_time_select);
										$fetch_time=mysql_fetch_assoc($select_time_q);
										$curr_time=$fetch_time['current_timestamp'];
										$update_state="update suggestion_options set suggestion_state=30, last_updated='$curr_time' where suggestion_id='$suggestion_id'";
										$update_st_q=SunDataBaseManager::getSingleton()->QueryDB($update_state);
										$state_trans="insert into state_transitions values(7,'$suggestion_id','$suggestion_state',30,'$curr_time')";
										$update_state_trans=SunDataBaseManager::getSingleton()->QueryDB($state_trans);
									}
								}
							}
							$arr[SMC::$STATUS] = "Success";
							$arr[SMC::$OPTIONID]=$option_id;
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
					}
					catch (Exception $e)
					{

					}
				}
			public function RetrieveStudentAnswer($assessment_answer_id=null)
				{
					try
						{
						  
                            if($assessment_answer_id != null)
                            {
                                
                            
							$check = "select questions.topic_id, question_log.class_session_id,question_log.question_id,answer.rating, answer.text_rating, answer.badge_id, types.question_type_title, answer.student_id from questions inner join question_types as types using (question_type_id) inner join question_log using(question_id) inner join assessment_answers as answer using(question_log_id) where answer.assessment_answer_id = '$assessment_answer_id'";
							$check_type = SunDataBaseManager::getSingleton()->QueryDB($check);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($check_type);
							if($count > 0)
								{
								    
									$assessment = mysql_fetch_assoc($check_type);
									$arr[SMC::$STATUS] = "Success";
									$topicid = $assessment['topic_id'];
									$sessionid = $assessment['class_session_id'];
									$studentid = $assessment['student_id'];                                                                                                            
                                    $QuestionId = $assessment['question_id'];
									$arr[SMC::$RATING] = $assessment['rating'];
									$arr[SMC::$TEXTRATING] = $assessment['text_rating'];
									$arr[SMC::$BADGEID] = $assessment['badge_id'];
									$arr[SMC::$GRASPINDEX] = $this->mXMLManager->GetGraspIndex(null,$sessionid,$studentid,$topicid);
									$arr[SMC::$PARTICIPATIONINDEX] = $this->mXMLManager->GetParticipationIndex($sessionid,$studentid);
									$type = $assessment['question_type_title'];
                                    $arr[SMC::$QUESTIONTYPE] = $type;
                                    $arr[SMC::$STUDENTID] = $assessment['student_id'];
                                    $arr[SMC::$ASSESSMENTANSWERID] = $assessment_answer_id;
									if ($type == "Multiple Response")
										{									  
											$options = "select option_text from answer_options where assessment_answer_id = '$assessment_answer_id'";
											$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
											$i = 0;
											while($option = mysql_fetch_assoc($option_list))
												{
												 $options1 = "select question_option, is_answer from question_options where question_id = '$QuestionId'";
					    	                    $option_list1 = SunDataBaseManager::getSingleton()->QueryDB($options1);
        										while($option1 = mysql_fetch_assoc($option_list1))
        											{
        												$option_text = $option1['question_option'];
                                                        $answer = $option1['is_answer'];
                                                     if(trim($option['option_text']) == trim($option_text) && $answer == 1)
                                                    {
                                                        $arr[SMC::$OPTIONS][SMC::$OPTION][$i][SMC::$ISANSWER] = 1;
                                                        break;
                                                    }
                                                    else
                                                    {
                                                        $arr[SMC::$OPTIONS][SMC::$OPTION][$i][SMC::$ISANSWER] = 0;
                                                    }
  
		                                            }
													$arr[SMC::$OPTIONS][SMC::$OPTION][$i][SMC::$OPTIONTEXT] = $option['option_text'];

                                                    
													$i++;
												}
										}
									else if ($type == "Multiple Choice")
										{
										  $i = 0;
											$options = "select option_text from answer_options where assessment_answer_id = '$assessment_answer_id'";
											$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
										//	$option = mysql_fetch_assoc($option_list);
                                            $combo = "";
                                            $count3 = SunDataBaseManager::getSingleton()->getnoOfrows($option_list);
                                            while($option = mysql_fetch_assoc($option_list))
                                            {
                                              
                                             $options1 = "select question_option, is_answer from question_options where question_id = '$QuestionId'";
					    	                    $option_list1 = SunDataBaseManager::getSingleton()->QueryDB($options1);
        										while($option1 = mysql_fetch_assoc($option_list1))
        											{
        												$option_text = $option1['question_option'];
                                                        $answer = $option1['is_answer'];
        												
                                                         if(trim($option['option_text']) == trim($option_text) && $answer == 1)
                                                            {
                                                                $arr[SMC::$ISANSWER] = 1;
                                                                break;
                                                            }
                                                            else
                                                            {
                                                                $arr[SMC::$ISANSWER] = 0;
                                                                
                                                            }
                                                       }
                                             $combo.= $option['option_text'];
                                             if($count3 > 1)
                                             {
                                                $combo.= ";;;";
                                             }           
                                             
                                             $count3--;                                     
                                            }
                                            $arr[SMC::$OPTIONS][SMC::$OPTION][$i][SMC::$OPTIONTEXT] = $combo;
											     
                                                            
										}
									else if ($type == "Fresh Scribble" or $type == "Overlay Scribble")
										{
											$retrieve = "select image.image_path as teacher_scribble, images.image_path as scribble from assessment_answers as answer left join uploaded_images as image on answer.teacher_scribble_id = image.image_id left join answer_options as options on options.assessment_answer_id = answer.assessment_answer_id left join uploaded_images as images on options.scribble_id = images.image_id where options.assessment_answer_id = '$assessment_answer_id'";
											$answer = SunDataBaseManager::getSingleton()->QueryDB($retrieve);
											$option = mysql_fetch_assoc($answer);
											$arr[SMC::$SCRIBBLE] = $option['scribble'];
											$arr[SMC::$TEACHERSCRIBBLE] = $option['teacher_scribble'];
										}
									else if ($type == "Text")
										{
											$retrieve = "select answer_text from answer_options where assessment_answer_id = '$assessment_answer_id'";
											$answer = SunDataBaseManager::getSingleton()->QueryDB($retrieve);
											$option = mysql_fetch_assoc($answer);
											$arr[SMC::$TEXTANSWER] = $option['answer_text'];
										}
									else if ($type == "Match Columns")
										{
											$options = "select option_text, mtc_column, old_sequence, mtc_sequence from answer_options where assessment_answer_id = '$assessment_answer_id'";
											$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
											$i = 0;
											while($option = mysql_fetch_assoc($option_list))
												{
													$arr[SMC::$OPTIONS][SMC::$OPTION][$i][SMC::$OPTIONTEXT] = $option['option_text'];
													$arr[SMC::$OPTIONS][SMC::$OPTION][$i][SMC::$COLUMN] = $option['mtc_column'];
													$arr[SMC::$OPTIONS][SMC::$OPTION][$i][SMC::$SEQUENCE] = $option['mtc_sequence'];
                                                    $arr[SMC::$OPTIONS][SMC::$OPTION][$i][SMC::$OLDSEQ] = $option['old_sequence'];
													$i++;
												}
										}
								}
							else
								{
									$arr[SMC::$STATUS] = "This student has not answered the question yet, please try again later.";
								}
                                }
                                else
                                {
                                    $arr[SMC::$STATUS] = "No assessment answerid passed";
                                }
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function SendFeedback($answer_id=null, $user_id=null, $url=null, $rating=null, $text_rating=null, $badge_id=null,$student_id=null, $model=null)
				{
					try
						{
						    $marked_student_index = 0;
							$count = substr_count($answer_id, ',');
							if ($count>=1)
								{
									$answers = explode(",",$answer_id);
								}
							else
								{
									$answer = $answer_id;
								}
							if (empty($url) or empty($rating) or empty($text_rating) or empty($badge_id))
								{
									if(empty($url) and empty($rating) and empty($text_rating))
										{
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
														}
												}
											else
												{
													$update = "update assessment_answers set badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
												}
										}
									else if(empty($rating) and empty($text_rating) and empty($badge_id))
										{
											$upload = "insert into uploaded_images (image_type_id, image_path, uploaded_by) values ('1', '$url', '$user_id')";
											$uploaded = SunDataBaseManager::getSingleton()->QueryDB($upload);
											$id = SunDataBaseManager::getSingleton()->getLastInsertId();
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set teacher_scribble_id = '$id', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
														}
												}
											else
												{
													$update = "update assessment_answers set teacher_scribble_id = '$id', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
												}
										}
									else if(empty($text_rating) and empty($badge_id) and empty($url))
										{
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set rating = '$rating', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                            //grasp index
                                                            
                                                            $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                            $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                            $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                            $topic_id = $topic_id_get1['topic_id'];     
    
    
                                                            $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                            $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                            $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                            $session_id = $session_id_get1['class_session_id'];  
                                                            //check if row exists 
                                                            /***pqrstuvwxyz****/
                                                            $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                            $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                            $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                            $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                            if($exists_rows > 0)
                                                            {
                                                                 $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                 $auto_id = $topic_id_get2['auto_id'];
                                                                 $total_count = $topic_id_get2['subtotal_of_count'];
                                                                 $total_score = $topic_id_get2['subtotal_of_score'];
                                                                 $total_count++;
                                                                 $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                            
                                                            }
                                                            else
                                                            {
                                                                //insert if no
                                     				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
    					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                
                                                            }                                                            
														}
												}
											else
												{
													$update = "update assessment_answers set rating = '$rating', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                            //grasp index
                                                            
                                                            $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                            $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                            $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                            $topic_id = $topic_id_get1['topic_id'];     
    
    
                                                            $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                            $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                            $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                            $session_id = $session_id_get1['class_session_id'];  
                                                            //check if row exists 
                                                            $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                            $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                            $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                            $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                            if($exists_rows > 0)
                                                            {
                                                                 $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                 $auto_id = $topic_id_get2['auto_id'];
                                                                 $total_count = $topic_id_get2['subtotal_of_count'];
                                                                 $total_score = $topic_id_get2['subtotal_of_score'];
                                                                 $total_count++;
                                                                 $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                            
                                                            }
                                                            else
                                                            {
                                                                //insert if no
                                     				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
    					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                
                                                            }                                              /***** close pqrstuvwxyz ****/              
                                                                                                                
												}
                                                $marked_student_index = 1;
										}
									else if(empty($badge_id) and empty($url) and empty($rating))
										{
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set text_rating = '$text_rating', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
														}
												}
											else
												{
													$update = "update assessment_answers set text_rating = '$text_rating', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
												}
										}
									else if(empty($url) and empty($rating))
										{
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set text_rating = '$text_rating', badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
														}
												}
											else
												{
													$update = "update assessment_answers set text_rating = '$text_rating', badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
												}
										}
									else if(empty($url) and empty($text_rating))
										{
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set rating = '$rating', badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);      
                                                            //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }
														}
												}
											else
												{
													$update = "update assessment_answers set rating = '$rating', badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                            
                                                            //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                
												}
                                                $marked_student_index = 1;
										}
									else if(empty($url) and empty($badge_id))
										{
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set text_rating = '$text_rating', rating = '$rating', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                            //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                         
														}
												}
											else
												{
													$update = "update assessment_answers set text_rating = '$text_rating', rating = '$rating', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                            //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                 
												}
                                                $marked_student_index = 1;
										}
									else if(empty($rating) and empty($text_rating))
										{
											$upload = "insert into uploaded_images (image_type_id, image_path, uploaded_by) values ('1', '$url', '$user_id')";
											$uploaded = SunDataBaseManager::getSingleton()->QueryDB($upload);
											$id = SunDataBaseManager::getSingleton()->getLastInsertId();
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set teacher_scribble_id = '$id', badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
														}
												}
											else
												{
													$update = "update assessment_answers set teacher_scribble_id = '$id', badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
												}
										}
									else if(empty($rating) and empty($badge_id))
										{
											$upload = "insert into uploaded_images (image_type_id, image_path, uploaded_by) values ('1', '$url', '$user_id')";
											$uploaded = SunDataBaseManager::getSingleton()->QueryDB($upload);
											$id = SunDataBaseManager::getSingleton()->getLastInsertId();
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set teacher_scribble_id = '$id', text_rating = '$text_rating', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
														}
												}
											else
												{
													$update = "update assessment_answers set teacher_scribble_id = '$id', text_rating = '$text_rating', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
												}
										}
									else if(empty($text_rating) and empty($badge_id))
										{
											$upload = "insert into uploaded_images (image_type_id, image_path, uploaded_by) values ('1', '$url', '$user_id')";
											$uploaded = SunDataBaseManager::getSingleton()->QueryDB($upload);
											$id = SunDataBaseManager::getSingleton()->getLastInsertId();
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set teacher_scribble_id = '$id', rating = '$rating', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                           //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                                                                                                                                                 
														}
												}
											else
												{
													$update = "update assessment_answers set teacher_scribble_id = '$id', rating = '$rating', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                                                                                                                                                 
                                                                                                                
												}
                                                $marked_student_index = 1;
										}
									else if (empty($url))
										{
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set text_rating = '$text_rating', rating = '$rating', badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                         
														}
												}
											else
												{
													$update = "update assessment_answers set text_rating = '$text_rating', rating = '$rating', badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                 
												}
                                                $marked_student_index = 1;
										}
									else if (empty($rating))
										{
											$upload = "insert into uploaded_images (image_type_id, image_path, uploaded_by) values ('1', '$url', '$user_id')";
											$uploaded = SunDataBaseManager::getSingleton()->QueryDB($upload);
											$id = SunDataBaseManager::getSingleton()->getLastInsertId();
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set teacher_scribble_id = '$id', text_rating = '$text_rating', badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
														}
												}
											else
												{
													$update = "update assessment_answers set teacher_scribble_id = '$id', text_rating = '$text_rating', badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
												}
										}
									else if (empty($text_rating))
										{
											$upload = "insert into uploaded_images (image_type_id, image_path, uploaded_by) values ('1', '$url', '$user_id')";
											$uploaded = SunDataBaseManager::getSingleton()->QueryDB($upload);
											$id = SunDataBaseManager::getSingleton()->getLastInsertId();
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set teacher_scribble_id = '$id', rating = '$rating', badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                            //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                         
														}
												}
											else
												{
													$update = "update assessment_answers set teacher_scribble_id = '$id', rating = '$rating', badge_id = '$badge_id', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                 
												}
                                                $marked_student_index = 1;
										}
									else if (empty($badge_id))
										{
											$upload = "insert into uploaded_images (image_type_id, image_path, uploaded_by) values ('1', '$url', '$user_id')";
											$uploaded = SunDataBaseManager::getSingleton()->QueryDB($upload);
											$id = SunDataBaseManager::getSingleton()->getLastInsertId();
											if (is_array($answers))
												{
													foreach ($answers as $answer)
														{
															$update = "update assessment_answers set text_rating = '$text_rating', rating = '$rating', teacher_scribble_id = '$id', model_answer = '$model' where assessment_answer_id = '$answer'";
															$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                         
														}
												}
											else
												{
													$update = "update assessment_answers set text_rating = '$text_rating', rating = '$rating', teacher_scribble_id = '$id', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                 
												}
                                                $marked_student_index = 1;
										}
								}
							else
								{
									$upload = "insert into uploaded_images (image_type_id, image_path, uploaded_by) values ('1', '$url', '$user_id')";
									$uploaded = SunDataBaseManager::getSingleton()->QueryDB($upload);
									$id = SunDataBaseManager::getSingleton()->getLastInsertId();
									if (is_array($answers))
										{
											foreach ($answers as $answer)
												{
													$update = "update assessment_answers set badge_id = '$badge_id', text_rating = '$text_rating', rating = '$rating', teacher_scribble_id = '$id', model_answer = '$model' where assessment_answer_id = '$answer'";
													$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                        //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                 
                                                                                                                
												}
										}
									else
										{
											$update = "update assessment_answers set badge_id = '$badge_id', text_rating = '$text_rating', rating = '$rating', teacher_scribble_id = '$id', model_answer = '$model' where assessment_answer_id = '$answer'";
											$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
                                                            $answer_score =($rating/5);                                                        // echo "ans:".$answer_score;
                                                            if($answer_score < 0)
                                                            {
                                                                $answer_score = 0;
                                                            }
                                      				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$answer'";
    					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                //grasp index
                                                            if($marked_student_index == 0)
                                                            {
                                                                $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (SELECT question_id FROM assessment_answers, question_log WHERE assessment_answer_id='$answer' and assessment_answers.question_log_id=question_log.question_log_id)";
                                                                $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                                $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                                $topic_id = $topic_id_get1['topic_id'];     
        
        
                                                                $get_session_id = "SELECT class_session_id FROM assessment_answers WHERE assessment_answer_id='$answer'";
                                                                $session_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_session_id);
                                                                $session_id_get1 = mysql_fetch_assoc($session_id_get);
                                                                $session_id = $session_id_get1['class_session_id'];  
                                                                //check if row exists 
                                                                $get_transaction_id="select transaction_id
                                                            from assessment_answers
                                                            inner join question_log
                                                            on assessment_answers.question_log_id=question_log.question_log_id
                                                            inner join questions
                                                            on question_log.question_id=questions.question_id
                                                            inner join question_types
                                                            on questions.question_type_id=question_types.question_type_id
                                                            inner join transaction_types
                                                            on transaction_types.transaction_id=question_types.question_type_id
                                                            where assessment_answer_id='$answer'";
                                                            $trans_id_query=SunDataBaseManager::getSingleton()->QueryDB($get_transaction_id);
                                                            $trans_id_fetch=mysql_fetch_assoc($trans_id_query);
                                                            $transaction_id2=$trans_id_fetch['transaction_id'];
                                                                $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id='$transaction_id2'";
                                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                                if($exists_rows > 0)
                                                                {
                                                                     $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                                     $auto_id = $topic_id_get2['auto_id'];
                                                                     $total_count = $topic_id_get2['subtotal_of_count'];
                                                                     $total_score = $topic_id_get2['subtotal_of_score'];
                                                                     $total_count++;
                                                                     $total_score +=$answer_score;
                                                                //update if yes
                                         				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                                
                                                                }
                                                                else
                                                                {
                                                                    //insert if no
                                         				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','$transaction_id2','$student_id','1','$answer_score')";
        					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                                    
                                                                }                                                                                                                                                                              
                                                                
                                                                
                                                            }                                                                                                                 
                                                                                                        
										}
                                        $marked_student_index = 1;
								}
							$arr[SMC::$STATUS] = "Success";
                            $arr[SMC::$ASSESSMENTANSWERID] = $answer_id;
							$count = substr_count($student_id, ';;;');
							if ($count>=1)
								{
									$students = explode(";;;",$student_id);
								}
							else
								{
									$student = $student_id;
								}        
                                
                                           $j =0;
											if (is_array($students))
												{
												    
													foreach ($students as $student)
														{
														  
                                                            $arr[SMC::$STUDENTS][SMC::$STUDENT][$j][SMC::$STUDENTID] = $student;                                                                                                                    
                                                            $j++;
														}
												}
											else
												{
												    
                                                    $arr[SMC::$STUDENTS][SMC::$STUDENT][$j][SMC::$STUDENTID] = $student;    
                                                }                           
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
//4 June 2014
			public function StopVolunteering($volunteer_id=null, $thumbs_up=null, $thumbs_down=null)
				{
					try
						{
                                                        
							$volunteer_state = "select state from query_volunteer where volunteer_id = '$volunteer_id'";
							$retrieve_volunteer_state = SunDataBaseManager::getSingleton()->QueryDB($volunteer_state);
							$current_volunteer_state = mysql_fetch_assoc($retrieve_volunteer_state);
							if($current_volunteer_state)
								{
									$change_state = "update query_volunteer set thumbs_up = '$thumbs_up', thumbs_down = '$thumbs_down', close_time = NOW() where volunteer_id = '$volunteer_id'";
									$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
                                    
									if ($change)
										{
											$arr[SMC::$STATUS] = "Success";
            							}                                
                                                                     
								}
							else
								{
									$arr[SMC::$STATUS] = "Volunteer not found";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function SaveVolunteerFeedback($volunteer_id=null, $rating=null, $badge_id=null)
				{
					try
						{
							$state = "5";
							$volunteer_state = "select state from query_volunteer where volunteer_id = '$volunteer_id'";
							$retrieve_volunteer_state = SunDataBaseManager::getSingleton()->QueryDB($volunteer_state);
							$current_volunteer_state = mysql_fetch_assoc($retrieve_volunteer_state);
							if($current_volunteer_state)
								{
									$old_volunteer_state = $current_volunteer_state['state'];
									$change_state = "update query_volunteer set teacher_score = '$rating', badge_id = '$badge_id' where volunteer_id = '$volunteer_id'";
									$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
									if ($change)
										{
											//$log_volunteer_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('6','$volunteer_id','$old_volunteer_state','$state')";
											//$logged_volunteer_transition = SunDataBaseManager::getSingleton()->QueryDB($log_volunteer_transition);
											$arr[SMC::$STATUS] = "Success";
										}   
                                        
								}
							else
								{
									$arr[SMC::$STATUS] = "Volunteer not found";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function RespondToQuery($query_id=null, $response_text=null, $badge_id=null, $dismiss=null, $student_id=null)
				{
					try
						{
						    if($dismiss == "(null)" || $dismiss == "%28null%29" || $dismiss == null)
                            {
                                unset($dismiss);
                            }
							if ($dismiss=="1")
								{
									$state = "17";
									$query_state = "select state from student_query where query_id = '$query_id'";
									$retrieve_query_state = SunDataBaseManager::getSingleton()->QueryDB($query_state);
									$current_query_state = mysql_fetch_assoc($retrieve_query_state);
									$old_query_state = $current_query_state['state'];
								}
							if (empty($badge_id) or empty($response_text) or empty($dismiss))
								{
									if (empty($response_text) and empty($badge_id))
										{
											if($old_query_state!=$state)
											{
											$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$query_id','$old_query_state','$state')";
											$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
											}
											$update = "update student_query set state = '$state' where query_id = '$query_id'";
										}
									else if (empty($badge_id) and empty($dismiss))
										{
										  $response_text = addslashes($response_text);
											$update = "update student_query set reply_text = '$response_text' where query_id = '$query_id'";
										}
									else if (empty($response_text) and empty($dismiss))
										{
											$update = "update student_query set badge_id = '$badge_id' where query_id = '$query_id'";
										}
									else if (empty($response_text))
										{
											$update = "update student_query set badge_id = '$badge_id', state = '$state' where query_id = '$query_id'";
											if($old_query_state!=$state)
											{
											$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$query_id','$old_query_state','$state')";
											$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
											}
										}
									else if (empty($badge_id))
										{
										  $response_text = addslashes($response_text);
											$update = "update student_query set reply_text = '$response_text', state = '$state' where query_id = '$query_id'";
											if($old_query_state!=$state)
											{
											$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$query_id','$old_query_state','$state')";
											$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
											}
										}
									else if (empty($dismiss))
										{
										  $response_text = addslashes($response_text);
											$update = "update student_query set reply_text = '$response_text', badge_id = '$badge_id' where query_id = '$query_id'";
										}
								}
							else
								{
								    $response_text = addslashes($response_text);
									$update = "update student_query set reply_text = '$response_text', badge_id = '$badge_id', state = '$state' where query_id = '$query_id'";
                                   // echo $update;
                                    if($old_query_state!=$state)
                                    {
 											$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$query_id','$old_query_state','$state')";
											$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);                                       
                                    }
                                    
								}
                                        if(!empty($badge_id) && $badge_id == 1 && empty($dismiss))
                                        {
                                            
                                        
                                    //pi
                                    $volunteer_state1 = "SELECT `topic_id`,`class_session_id` FROM `student_query` WHERE `query_id`='$query_id'";
                                    $retrieve_volunteer_state1 = SunDataBaseManager::getSingleton()->QueryDB($volunteer_state1);
                                    $current_volunteer_state1 = mysql_fetch_assoc($retrieve_volunteer_state1);
                                    $session_id = $current_volunteer_state1['class_session_id'];
                                    //$student_id = $current_volunteer_state1['student_id'];
                                    $topic_id = $current_volunteer_state1['topic_id'];
                                    
                                    //check if row exists
                                    
                                    $get_exists_id = "SELECT auto_id,subtotal_of_count FROM student_index WHERE class_session_id='$session_id' and student_id='$student_id' and transaction_id=29";
                                    $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                    $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                    if ($exists_rows > 0)
                                    {
                                        $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                        $auto_id = $topic_id_get2['auto_id'];
                                        $total_count = $topic_id_get2['subtotal_of_count'];
                                        $total_count++;
                                        //update if yes
                                        $save_device = "update student_index set subtotal_of_count= '$total_count' where auto_id = '$auto_id'";
                                        $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                    
                                    } else
                                    {
                                        //insert if no
                                        $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count) values ('$session_id','$topic_id','29','$student_id','1')";
                                        $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                    
                                    }
                                        
                                     }                                
							$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
							$arr[SMC::$STATUS] = "Success";
                            $arr[SMC::$QUERYID] = $query_id;
                            $arr[SMC::$STUDENTID] = $student_id;
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function MuteStudent($user_id=null, $mute=null,$session_id=null,$topic_id=null)
				{
					try
						{
							if ($mute=="1")
								{
									$state = "21";
								}
							else
								{
									$state = "1";
								}
							$student_state = "select user_state,role_id from tbl_auth where user_id = '$user_id'";
							$retrieve_student_state = SunDataBaseManager::getSingleton()->QueryDB($student_state);
							$current_student_state = mysql_fetch_assoc($retrieve_student_state);
                        $old_student_state = 0;
                        $user_role = 0;

										 $old_student_state = $current_student_state['user_state'];
										  $user_role = $current_student_state['role_id'];                      

                            if($user_role == "4")
                            {
                                if($old_student_state != $state)
                                {
        							$change_student_state = "update tbl_auth set user_state = '$state' where user_id = '$user_id'";
    			     				$student_state_change = SunDataBaseManager::getSingleton()->QueryDB($change_student_state);
    			     				if($old_student_state!=$state)
    			     				{
    				    				$log_student_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$user_id','$old_student_state','$state')";
    				    				$logged_student_transition = SunDataBaseManager::getSingleton()->QueryDB($log_student_transition);
    				    			}
                                    if($state == '21')
                                    {
                                        //pi
                                        $student_id = $user_id;          

                                        
                                        //check if row exists 
                                                        
                                        $get_exists_id = "SELECT auto_id,subtotal_of_count FROM student_index WHERE class_session_id='$session_id' and student_id='$student_id' and transaction_id=32";
                                        $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                        $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                        if ($exists_rows > 0)
                                        {
                                            $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                            $auto_id = $topic_id_get2['auto_id'];
                                            $total_count = $topic_id_get2['subtotal_of_count'];
                                            $total_count++;
                                            //update if yes
                                            $save_device = "update student_index set subtotal_of_count= '$total_count' where auto_id = '$auto_id'";
                                            $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                        
                                        } else
                                        {
                                            //insert if no
                                            $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count) values ('$session_id','$topic_id','32','$student_id','1')";
                                            $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                        
                                        }                                         
                                    }
                                   
                                }
    							$arr[SMC::$STATUS] = "Success";                             
                            }
                            else
                            {
                                $arr[SMC::$STATUS] = "This user cannot be muted"; 
                            }
                            

							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function SaveSelectedQueries($query_id=null, $volunteer=null)
				{
					try
						{
							$state = "18";
							$count = substr_count($query_id, ',');
							if ($count>=1)
								{
									$queries = explode(",",$query_id);
									$volunteers = explode(",",$volunteer);
								}
							else
								{
									$queries = $query_id;
									$volunteers = $volunteer;
								}
							if (is_array($queries))
								{
									foreach($queries as $key=>$query)
										{
											$query_state = "select state from student_query where query_id = '$query'";
											$retrieve_query_state = SunDataBaseManager::getSingleton()->QueryDB($query_state);
											$current_query_state = mysql_fetch_assoc($retrieve_query_state);
											$old_query_state = $current_query_state['state'];
											if($old_query_state!=$state)
											{
												$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$query','$old_query_state','$state')";
												$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
											}
											$update = "update student_query set state = '$state', allow_volunteer = '$volunteers[$key]',selected_for_voting=1 where query_id = '$query'";
											$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
										}
									$arr[SMC::$STATUS] = "Success";
								}
							else
								{
									$query_state = "select state from student_query where query_id = '$queries'";
									$retrieve_query_state = SunDataBaseManager::getSingleton()->QueryDB($query_state);
									$current_query_state = mysql_fetch_assoc($retrieve_query_state);
									$old_query_state = $current_query_state['state'];
									if($old_query_state!=$state)
									{
										$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$queries','$old_query_state','$state')";
										$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
									}
									$update = "update student_query set state = '$state', allow_volunteer = '$volunteers',selected_for_voting=1 where query_id = '$queries'";
									$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
									$arr[SMC::$STATUS] = "Success";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function GetVolunteerList($query_id=null)
				{
					try
						{
							$volunteer_students = "select query.student_id, user.first_name from query_volunteer as query inner join tbl_auth as user on query.student_id = user.user_id where query.query_id = '$query_id'";
							$volunteered_students = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($volunteered_students);
							if ($count > 0)
								{
									$arr[SMC::$STATUS] = "Success";
									$i = 0;
									while($students = mysql_fetch_assoc($volunteered_students))
										{
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID] = $students['student_id'];
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTNAME] = $students['first_name'];
											$i++;
										}
								}
							else
								{
									$arr[SMC::$STATUS] = "There are no volunteers for this query yet, please try again.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
    			public function UpdateUserState($user_id=null,$status_id=null,$session_id=null)
    				{
    					try
    						{
    						    if ($status_id)
                                {
 									$student_state = "select user_state,role_id from tbl_auth where user_id = '$user_id'";
									$retrieve_student_state = SunDataBaseManager::getSingleton()->QueryDB($student_state);
									$current_student_state = mysql_fetch_assoc($retrieve_student_state);
									$old_student_state = $current_student_state['user_state'];       
                                    $role = $current_student_state['role_id'];  
                                    //using state of session , new state of student 10, assign the seat state 10, if 7, 9      
                                    if($role == '4' && $status_id == '10') 
                                    {
     									$s_state = "select seat_id, seat_state from seat_assignments where student_id = '$user_id' and class_session_id = '$session_id'";
    									$r_state = SunDataBaseManager::getSingleton()->QueryDB($s_state);
    									$cs_state = mysql_fetch_assoc($r_state);
    									$o_seat = $cs_state['seat_id'];   
                                        $o_state = $cs_state['seat_state']; 
                                        if($o_seat != null && $o_state != '10')
                                        {
                							$update = "update seat_assignments set seat_state = '10' where student_id = '$user_id' and class_session_id = '$session_id'";
           							        $updated = SunDataBaseManager::getSingleton()->QueryDB($update);         
    	                                    $log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('4','$o_seat',$o_state,'10')";
                                            $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);                                                                       
                                        }      
                                        //change room state
                                        $get_room = "select room_id from class_sessions where class_session_id = '$session_id'";
    									$rooms = SunDataBaseManager::getSingleton()->QueryDB($get_room);
    									$room = mysql_fetch_assoc($rooms);
    									$room_id = $room['room_id'];     
    									$room_state = "select state from rooms where room_id = '$room_id'";
    									$retrieve_room_state = SunDataBaseManager::getSingleton()->QueryDB($room_state);
    									$current_room_state = mysql_fetch_assoc($retrieve_room_state);
    									$old_room_state = $current_room_state['state'];
                                        if($old_room_state != '1')
                                        {
         									$change_room_state = "update rooms set state = '1' where room_id = '$room_id'";
        									$room_state_change = SunDataBaseManager::getSingleton()->QueryDB($change_room_state);
        									$log_room_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('3','$room_id','$old_room_state','1')";
        									$logged_room_transition = SunDataBaseManager::getSingleton()->QueryDB($log_room_transition);                                                                        
                                             
                                        }
                                      
                                    }           
                                    if($role == '4' && $status_id == '7')
                                    {
     									$s_state = "select seat_id, seat_state from seat_assignments where student_id = '$user_id' and class_session_id = '$session_id'";
    									$r_state = SunDataBaseManager::getSingleton()->QueryDB($s_state);
    									$cs_state = mysql_fetch_assoc($r_state);
    									$o_seat = $cs_state['seat_id'];   
                                        $o_state = $cs_state['seat_state']; 
                                        if($o_seat != null && $o_state != '9')
                                        {
                							$update = "update seat_assignments set seat_state = '9' where student_id = '$user_id' and class_session_id = '$session_id'";
           							        $updated = SunDataBaseManager::getSingleton()->QueryDB($update);         
    	                                    $log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('4','$o_seat',$o_state,'9')";
                                            $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);                                                                       
                                        }                                           
                                        
                                    }
                                    /********************* changes ************************/
                                    if($role == '4' && $status_id == '1')   
                                    {
                                    	$check_session_state="select session_state from class_sessions where class_session_id='$session_id'";
                                    	$check_state_query=SunDataBaseManager::getSingleton()->QueryDB($check_session_state);
                                    	$get_state=mysql_fetch_assoc($check_state_query);
                                    	$session_state=$get_state['session_state'];
                                    	if($session_state == 5)
                                    	{
                                    		$status_id='7';
                                    	}
                                    	elseif($session_state == 1)
                                    	{
                                    		$check_current_topic="select current_topic from live_session_status where session_id='$session_id' and current_topic is not null";
                                    		$check_topic_query=SunDataBaseManager::getSingleton()->QueryDB($check_current_topic);
                                    		$check_topic_entries=SunDataBaseManager::getSingleton()->getnoOfrows($check_topic_query);
                                    		if($check_topic_entries>0)
                                    		{
                                    			$check_topic=mysql_fetch_assoc($check_topic_query);
                                    			$current_topic=$check_topic['current_topic'];
                                    			$check_stud="select student_id from stud_topic_time where topic_id='$current_topic' and class_session_id='$session_id' and student_id='$user_id'";
                                    			$check_stud_query=SunDataBaseManager::getSingleton()->QueryDB($check_stud);
                                    			$check_stud_entries=SunDataBaseManager::getSingleton()->getnoOfrows($check_stud_query);
                                    			if($check_stud_entries>0)
                                    			{
                                    				$last_seen_time="select current_timestamp";
													$last_seen_query=SunDataBaseManager::getSingleton()->QueryDB($last_seen_time);
													$last_seen_fetch=mysql_fetch_assoc($last_seen_query);
													$last_seen=$last_seen_fetch['current_timestamp'];
                                    				$update_stud="update stud_topic_time set last_seen='$last_seen' where student_id='$user_id' and topic_id='$current_topic' and class_session_id='$session_id'";
                                    				$update_stud_query=SunDataBaseManager::getSingleton()->QueryDB($update_stud);
                                    			}
                                    			elseif($check_stud_entries==0)
                                    			{
                                    				$last_seen_time="select current_timestamp";
													$last_seen_query=SunDataBaseManager::getSingleton()->QueryDB($last_seen_time);
													$last_seen_fetch=mysql_fetch_assoc($last_seen_query);
													$last_seen=$last_seen_fetch['current_timestamp'];
                                    				$insert_stud="insert into stud_topic_time(student_id,class_session_id,topic_id,last_seen,stud_time) values('$user_id','$session_id','$current_topic','$last_seen',0)";
                                    				$insert_stud_query=SunDataBaseManager::getSingleton()->QueryDB($insert_stud);
                                    			}
                                    		}
                                    		$check_students="select student_id from stud_session_time where class_session_id='$session_id' and student_id='$user_id'";
                                    		$check_student_query=SunDataBaseManager::getSingleton()->QueryDB($check_students);
                                    		$check_stud_entries=SunDataBaseManager::getSingleton()->getnoOfrows($check_student_query);
                                    		if($check_stud_entries>0)
                                    		{
                                    			$last_seen_time="select current_timestamp";
												$last_seen_query=SunDataBaseManager::getSingleton()->QueryDB($last_seen_time);
												$last_seen_fetch=mysql_fetch_assoc($last_seen_query);
												$last_seen=$last_seen_fetch['current_timestamp'];
                                    			$update_stud="update stud_session_time set last_seen='$last_seen' where student_id='$user_id' and class_session_id='$session_id'";
                                    			$update_stud_query=SunDataBaseManager::getSingleton()->QueryDB($update_stud);
                                    		}
                                    		elseif ($check_stud_entries==0)
                                    		{
                                    			$last_seen_time="select current_timestamp";
												$last_seen_query=SunDataBaseManager::getSingleton()->QueryDB($last_seen_time);
												$last_seen_fetch=mysql_fetch_assoc($last_seen_query);
												$last_seen=$last_seen_fetch['current_timestamp'];
												$insert_stud="insert into stud_session_time(student_id,class_session_id,stud_time,present,last_seen) values('$user_id','$session_id',0,0,'$last_seen')";
												$insert_stud_query=SunDataBaseManager::getSingleton()->QueryDB($insert_stud);
                                    		}
                                    	}
                                    }
                                    if($role == '4' && ($status_id == '7' || 	$status_id == '11'))
                                    {
                                    	$check_current_topic="select current_topic from live_session_status where session_id='$session_id'";
                                    	$check_topic_query=SunDataBaseManager::getSingleton()->QueryDB($check_current_topic);
                                    	$check_topic_entries=SunDataBaseManager::getSingleton()->getnoOfrows($check_topic_query);
                                    	if($check_topic_entries>0)
                                    	{
                                    		$check_topic_fetch=mysql_fetch_assoc($check_topic_query);
                                    		$current_topic=$check_topic_fetch['current_topic'];
                                    		$calculate_stud_time="select stud_time,last_seen from stud_topic_time where student_id='$user_id' and topic_id='$current_topic' and class_session_id='$session_id'";
                                    		$duration_query=SunDataBaseManager::getSingleton()->QueryDB($calculate_stud_time);
                                    		$duration_fetch=mysql_fetch_assoc($duration_query);
                                    		$stud_time=$duration_fetch['stud_time'];
                                    		$last_seen=$duration_fetch['last_seen'];
                                    		$select_curr_time="select current_timestamp";
											$curr_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_curr_time);
											$curr_time_fetch=mysql_fetch_assoc($curr_time_query);
											$curr_time=$curr_time_fetch['current_timestamp'];
                                    		$current_time=strtotime($curr_time);
                                    		$last_seen_time=strtotime($last_seen);
                                    		$stud_time+=$current_time-$last_seen_time;
                                    		$update_stud="update stud_topic_time set stud_time='$stud_time', last_seen='$curr_time' where student_id='$user_id' and topic_id='$current_topic' and class_session_id='$session_id'";
                                    		$update_stud_query=SunDataBaseManager::getSingleton()->QueryDB($update_stud);
                                    	}
                                    	$check_session="select session_state from class_sessions where class_session_id='$session_id'";
                                    	$check_session_query=SunDataBaseManager::getSingleton()->QueryDB($check_session);
                                    	$fetch_session_state=mysql_fetch_assoc($check_session_query);
                                    	$session_state=$fetch_session_state['session_state'];
                                    	if($session_state==1)
                                    	{
                                    		$calculate_stud_time="select stud_time,last_seen from stud_session_time where student_id='$user_id' and class_session_id='$session_id'";
                                    		$duration_query=SunDataBaseManager::getSingleton()->QueryDB($calculate_stud_time);
                                    		$duration_fetch=mysql_fetch_assoc($duration_query);
                                    		$stud_time=$duration_fetch['stud_time'];
                                    		$last_seen=$duration_fetch['last_seen'];
                                    		$select_curr_time="select current_timestamp";
                                    		$curr_time_query=SunDataBaseManager::getSingleton()->QueryDB($select_curr_time);
											$curr_time_fetch=mysql_fetch_assoc($curr_time_query);
											$curr_time=$curr_time_fetch['current_timestamp'];
                                    		$current_time=strtotime($curr_time);
                                    		$last_seen_time=strtotime($last_seen);
                                    		$stud_time+=$current_time-$last_seen_time;
                                    		$update_stud="update stud_session_time set stud_time='$stud_time', last_seen='$curr_time' where student_id='$user_id' and class_session_id='$session_id'";
                                    		$update_stud_query=SunDataBaseManager::getSingleton()->QueryDB($update_stud);
                                    	}
                                    }
                                    /********************* end of changes ************************/
                                    $arr[SMC::$STATUS] = "Success";
                                    if($status_id != $old_student_state)
                                    {
         							    $update = "update tbl_auth set user_state = '$status_id' where user_id = '$user_id'";
        						      	$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
        						      	if ($updated)
        								{
        									if($old_student_state!=$status_id)
        									{
									        	$log_live_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$user_id',$old_student_state,$status_id)";
												$logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);
											}
        							 	}
        						      	else
        								{
        									$arr[SMC::$STATUS] = "No user found.";
        								}                               
                                        
                                    }                                          
                                }
                                else
                                {
                                    $arr[SMC::$STATUS] = "User state is not passed.";
                                }

    							$retval = $this->mXMLManager->createXML($arr);
    							return $retval;
    						}
    					catch(Exception $e)
    						{
    							echo "error";
    						}
    				}
                    
                public function GetSessionInfo($session_id=null)
				{
					try
						{
							$session_state = "select session_state,teacher_id from class_sessions where class_session_id = '$session_id'";
							$retrieve_session_state = SunDataBaseManager::getSingleton()->QueryDB($session_state);
                            $count = SunDataBaseManager::getSingleton()->getnoOfrows($retrieve_session_state);
  							if ($count > 0)
								{
									$arr[SMC::$STATUS] = "Success";
									while($teacher= mysql_fetch_assoc($retrieve_session_state))
										{
										  $arr[SMC::$TEACHERID] = $teacher['teacher_id'];
										  $arr[SMC::$SESSIONSTATE] = $teacher['session_state'];
                                          $tid = $teacher['teacher_id'];
                                          $check = "select * from tbl_auth where user_id = '$tid'";
                                          $getname = SunDataBaseManager::getSingleton()->QueryDB($check);
                                          if($getname)
                                          {
                                            $current_teacher_name = mysql_fetch_assoc($getname);                                         
                                            $arr[SMC::$TEACHERNAME] = $current_teacher_name['first_name']. " ".$current_teacher_name['last_name'];
                                          }
			        	                  
										}
								}
							else
								{
									$arr[SMC::$STATUS] = "There are no results for this sessionid.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;                     

						}
    					catch(Exception $e)
   						{
  							echo "error";
   						}
  				}         

		public function FetchQuestion($question_id=null,$question_log_id=null)
				{
					try
						{
							$retval = "<Root><SunStone><Action>";
						if(!empty($question_id))
						{
							$questions = "select question.question_id,type.question_type_title, question_name, image.image_path from questions as question inner join question_types as type on question.question_type_id = type.question_type_id left join uploaded_images as image on image.image_id = question.scribble_id where question_id = '$question_id'";
						}
						else
						{
						$questions = "select question.question_id,type.question_type_title, question_name, image.image_path from questions as question inner join question_types as type on question.question_type_id = type.question_type_id left join uploaded_images as image on image.image_id = question.scribble_id inner join question_log as questionlog on questionlog.question_id=question.question_id where questionlog.question_log_id = '$question_log_id'";
						}
					$question_list = SunDataBaseManager::getSingleton()->QueryDB($questions);
			     	$question_count = SunDataBaseManager::getSingleton()->getnoOfrows($question_list);
                    if($question_count > 0)
					{
					   $retval .= "<Status>Success</Status>";
						while($question = mysql_fetch_assoc($question_list))
						{
		
						$question_type = $question['question_type_title'];
								$question_title = $question['question_name'];
				$scribble = $question['image_path'];
                $question_id = $question['question_id'];
				$retval .= "<Question><QuestionType>$question_type</QuestionType><Name>$question_title</Name><Scribble>$scribble</Scribble>";
																			if ($question_type == "Multiple Choice" or $question_type == "Multiple Response")
																				{
																					$options = "select question_option_id,question_option, is_answer from question_options where question_id = '$question_id'";
																					$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
																					$retval .= "<Options>";
																					while($option = mysql_fetch_assoc($option_list))
																						{
																						   $option_id = $option['question_option_id'];
																							$option_text = $option['question_option'];
																							$answer = $option['is_answer'];
																							$retval .= "<Option><OptionId>$option_id</OptionId><OptionText>$option_text</OptionText><IsAnswer>$answer</IsAnswer></Option>";
																						}
																					$retval .= "</Options></Question>";
																				}
																			else if ($question_type == "Fresh Scribble" or $question_type == "Text")
																				{
																					$retval .= "</Question>";
																				}
																			else if ($question_type == "Overlay Scribble")
																				{
																					$retval .= "</Question>";
																				}
																			else if ($question_type == "Match Columns")
																				{
																					$options = "select question_option_id,question_option, mtc_column, mtc_sequence from question_options where question_id = '$question_id'";
																					$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
																					$retval .= "<Options>";
																					while($option = mysql_fetch_assoc($option_list))
																						{
																							$option_text = $option['question_option'];
                                                                                            $option_id = $option['question_option_id'];
																							$column = $option['mtc_column'];
																							$sequence = $option['mtc_sequence'];
																							$retval .= "<Option><OptionId>$option_id</OptionId><OptionText>$option_text</OptionText><Column>$column</Column><Sequence>$sequence</Sequence></Option>";
																						}
																					$retval .= "</Options></Question>";
																				}                                                                                
									}

						}
                        else
                        {
                            $retval .= "<Status>There are no topics for this subject currently.</Status>";
                        }
                            $retval .= "</Action></SunStone></Root>";
							return $retval;
                            }
					catch(Exception $e)
						{
							echo "error";
						}
				}
 			public function SendAnswer($url=null,$student_id=null,$answertxt=null,$opttxt=null, $seqno=null,$col=2,$session_id=null, $question_log_id=null, $type=null)
				{
					try
						{
						  $col = 2;
						  if($question_log_id != null && $student_id != null && $session_id != null && $type != null)
                          {
      				       	$save_device = "insert into assessment_answers(question_log_id,class_session_id,student_id) values('$question_log_id','$session_id','$student_id')";
					         $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);    
                             
 							if ($save)
								{
								    
                                    $opttxt = addslashes($opttxt);
                                     $answertxt = addslashes($answertxt);
									$assess_id = SunDataBaseManager::getSingleton()->getLastInsertId();
                                    $current_imageid = null;
                                    if($url !=null && $url !="(null)")
                                    {
    								    $student_state = "SELECT image_id FROM uploaded_images WHERE image_path = '$url'";
    									$retrieve_student_state = SunDataBaseManager::getSingleton()->QueryDB($student_state);
    									$current_imageid1 = mysql_fetch_assoc($retrieve_student_state);
                                        $current_imageid = $current_imageid1['image_id'];
                                    }
                                    
 									if ($type == "Multiple Response")
										{				
                                    					if (!empty($opttxt))
														{
															$count = substr_count($opttxt, ';;;');
															if ($count>=1)
																{
																	$removed = explode(";;;",$opttxt);
																}
															else
																{
																	$removed = $opttxt;
																}
														}
													else
														{
															$removed = null;
														}	
 													if ($removed != null && is_array($removed))
													{
													   
                    									$student_state2 = "select question_id from question_log where question_log_id = '$question_log_id' limit 0,1";
                									$getquesid = SunDataBaseManager::getSingleton()->QueryDB($student_state2);
                									$question_id1 = mysql_fetch_assoc($getquesid);
                                                    $question_id = $question_id1['question_id']; 
                                                    $j = 0;     
                                                    $correctcount = 0;
                                                    $incorrectcount = 0;          
                                                    $attempted = 0;                                    
													foreach($removed as $optiont)
														{
														  
                                                         //answer score logic
     										            // answer_score =(#correct_Selection - #incorrect_Selection - #missed_Selection) / (#Total_correct_answers)
                                                        $attempted++; 
                                                        $totalcorrect = 0; 
                                                        $totaloptions = 0;
                         	                            $options1 = "select question_option, is_answer from question_options where question_id = '$question_id'";                                                      												
        					    	                    $option_list1 = SunDataBaseManager::getSingleton()->QueryDB($options1);
                										while($option1 = mysql_fetch_assoc($option_list1))
                											{
           											            $totaloptions++;
                												$option_text = $option1['question_option'];
                                                                $answer = $option1['is_answer'];
                                                                if($answer == 1)
                                                                {
                                                                   // echo "-tc-";
                                                                    $totalcorrect++;
                                                                }
                                                                 if(trim($optiont) == trim($option_text) && $answer == 1)
                                                                {
                                                                   // echo "-cc-";
                                                                    $correctcount++;
                                                                }
        		                                            }
								                                            
                              				       	        $save_device1 = "insert into answer_options(assessment_answer_id,option_text,mtc_column,mtc_sequence,answer_text,scribble_id) values('$assess_id','$optiont','$col','$seqno','$answertxt','$current_imageid')";
                        					                $save1 = SunDataBaseManager::getSingleton()->QueryDB($save_device1);
                                                            if($save1)
                                                            {
                                                                $answer_id = SunDataBaseManager::getSingleton()->getLastInsertId();
                                                                $arr[SMC::$STATUS] = "Success";
                                                                $arr[SMC::$ASSESSMENTANSWERID] = $assess_id;
                                                            }         
														}
                                                        $missed = $totalcorrect - $correctcount; 
                                                        $wrong = $attempted - $correctcount;
                                                        $answer_score =(($totalcorrect - $wrong - $missed) )/ ($totalcorrect);                                                        // echo "ans:".$answer_score;
                                                        if($answer_score < 0)
                                                        {
                                                            $answer_score = 0;
                                                        }
                                  				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$assess_id'";
					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                        //grasp index
                                                        
                                                        $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (select question_id from question_log where question_log_id='$question_log_id')";
                                                        $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                        $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                        $topic_id = $topic_id_get1['topic_id'];     
                                                        //check if row exists 
                                                        
                                                        $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id=2";
                                                        $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                        $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                        if($exists_rows > 0)
                                                        {
                                                             $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                             $auto_id = $topic_id_get2['auto_id'];
                                                             $total_count = $topic_id_get2['subtotal_of_count'];
                                                             $total_score = $topic_id_get2['subtotal_of_score'];
                                                             $total_count++;
                                                             $total_score +=$answer_score;
                                                        //update if yes
                                 				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                        
                                                        }
                                                        else
                                                        {
                                                            //insert if no
                                 				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','2','$student_id','1','$answer_score')";
					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                            
                                                        }
                                                        
                                                        
                                                                                                            
                                                                                                                    
                                                       }
                                                        else
                                                        {
                            									$student_state2 = "select question_id from question_log where question_log_id = '$question_log_id' limit 0,1";
                        									$getquesid = SunDataBaseManager::getSingleton()->QueryDB($student_state2);
                        									$question_id1 = mysql_fetch_assoc($getquesid);
                                                            $question_id = $question_id1['question_id'];                                                             
                                                            $correctcount = 0;
                                                            $incorrectcount = 0;          
                                                            $attempted = 0;     
                                                             //answer score logic
         										            // answer_score =(#correct_Selection - #incorrect_Selection - #missed_Selection) / (#Total_correct_answers)
                                                            $attempted++; 
                                                            $totalcorrect = 0; 
                                                            $totaloptions = 0;
                             	                            $options1 = "select question_option, is_answer from question_options where question_id = '$question_id'";                                                      												 
            					    	                    $option_list1 = SunDataBaseManager::getSingleton()->QueryDB($options1);
                    										while($option1 = mysql_fetch_assoc($option_list1))
                    											{
               											            $totaloptions++;
                    												$option_text = $option1['question_option'];
                                                                    $answer = $option1['is_answer'];
                                                                    if($answer == 1)
                                                                    {
                                                                        $totalcorrect++;
                                                                    }
                                                                     if(trim($opttxt) == trim($option_text) && $answer == 1)
                                                                    {
                                                                        $correctcount++;
                                                                    }
    
            		                                            }
                                                            
                                                            $save_device1 = "insert into answer_options(assessment_answer_id,option_text,mtc_column,mtc_sequence,answer_text,scribble_id) values('$assess_id','$opttxt','$col','$seqno','$answertxt','$current_imageid')";
                            					                $save1 = SunDataBaseManager::getSingleton()->QueryDB($save_device1); 
                                                                if($save1)
                                                                {
                                                                    $answer_id = SunDataBaseManager::getSingleton()->getLastInsertId();
                                                                    $arr[SMC::$STATUS] = "Success";
                                                                    $arr[SMC::$ASSESSMENTANSWERID] = $assess_id;
                                                                }   
                                                                
                                                                $missed = $totalcorrect - $correctcount; 
                                                                $wrong = $attempted - $correctcount;
                                                                $answer_score =(($totalcorrect - $wrong - $missed) )/ ($totalcorrect);
                                                                if($answer_score < 0)
                                                                {
                                                                    $answer_score = 0;
                                                                }
                                                                $save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$assess_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                                
                                                        //grasp index
                                                        
                                                        $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (select question_id from question_log where question_log_id='$question_log_id')";
                                                        $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                        $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                        $topic_id = $topic_id_get1['topic_id'];     
                                                        //check if row exists 
                                                        
                                                        $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id=2";
                                                        $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                        $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                        if($exists_rows > 0)
                                                        {
                                                             $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                             $auto_id = $topic_id_get2['auto_id'];
                                                             $total_count = $topic_id_get2['subtotal_of_count'];
                                                             $total_score = $topic_id_get2['subtotal_of_score'];
                                                             $total_count++;
                                                             $total_score +=$answer_score;
                                                        //update if yes
                                 				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                        
                                                        }
                                                        else
                                                        {
                                                            //insert if no
                                 				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','2','$student_id','1','$answer_score')";
					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                            
                                                        }                                                                
                                                                    
                                                        }                                                        
                                                        									  

										}
									else if ($type == "Match Columns")
										{
                                    					if (!empty($seqno))
														{
															$count = substr_count($seqno, ';;;');
															if ($count>=1)
																{
																	$removed = explode(";;;",$seqno);
																}
															else
																{
																	$removed = $seqno;
																}
														}
													else
														{
															$removed = null;
														}
                                                        
                                                        
													if ($removed != null && is_array($removed))
													{
													   
                    									$student_state2 = "select question_id from question_log where question_log_id = '$question_log_id' limit 0,1";
                									$getquesid = SunDataBaseManager::getSingleton()->QueryDB($student_state2);
                									$question_id1 = mysql_fetch_assoc($getquesid);
                                                    $question_id = $question_id1['question_id'];
													$i =1;   
                                                    $j = 0;
                                                         $correctcount = 0;
                                                    $attempted = 0; 
                                                    	 $totalcorrect = 0; 
                                                        $totaloptions = 0;
													foreach($removed as $optiont)
														{
														  

  														  $attempted++;
                     									 $newseq = "select question_option from question_options where question_id = '$question_id' and mtc_column='$col' and mtc_sequence='$optiont'";
                									$getnewseq = SunDataBaseManager::getSingleton()->QueryDB($newseq);
                									$newseq_opt1 = mysql_fetch_assoc($getnewseq);  
                                                    $newseq_opt = $newseq_opt1['question_option']; 	
                                                    	$newseq_opt = addslashes($newseq_opt);							                                            

           											            $totaloptions++;
                												$option_text = $option1['question_option'];
                                                                

                                                                 if($optiont == $i)
                                                                {
                                                                    $correctcount++;
                                                                }
        		                                                               
                              				       	        $save_device1 = "insert into answer_options(assessment_answer_id,option_text,mtc_column,old_sequence,mtc_sequence,answer_text,scribble_id) values('$assess_id','$newseq_opt','$col','$optiont','$i','$answertxt','$current_imageid')";
                        					                $save1 = SunDataBaseManager::getSingleton()->QueryDB($save_device1);
                                                            $totalcorrect = $i;
                                                            $i++; 
                                                            
                                                            if($save1)
                                                            {
                                                                $answer_id = SunDataBaseManager::getSingleton()->getLastInsertId();
                                                                $arr[SMC::$STATUS] = "Success";
                                                                $arr[SMC::$ASSESSMENTANSWERID] = $assess_id;
                                                            }         
														} 
                                                                $missed = 0; 
                                                                 
                                                              $wrong = $attempted - $correctcount;
                                                               $answer_score =(($totalcorrect - $wrong - $missed) )/ ($totalcorrect);                                                                 // echo "tc:". $totalcorrect. " to: ".$totaloptions." att:".$attempted." cc:".$correctcount;
                                                                //echo "as:".$answer_score;
                                                                $save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$assess_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);  
                                                                
                                                        //grasp index
                                                        
                                                        $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (select question_id from question_log where question_log_id='$question_log_id')";
                                                        $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                        $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                        $topic_id = $topic_id_get1['topic_id'];     
                                                        //check if row exists 
                                                        
                                                        $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id=3";
                                                        $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                        $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                        if($exists_rows > 0)
                                                        {
                                                             $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                             $auto_id = $topic_id_get2['auto_id'];
                                                             $total_count = $topic_id_get2['subtotal_of_count'];
                                                             $total_score = $topic_id_get2['subtotal_of_score'];
                                                             $total_count++;
                                                             $total_score +=$answer_score;
                                                        //update if yes
                                 				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                        
                                                        }
                                                        else
                                                        {
                                                            //insert if no
                                 				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','3','$student_id','1','$answer_score')";
					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                            
                                                        }                                                                
                                                                  
                                        
                                                                
                                                       }
                                                        else
                                                        {
                                                                 $correctcount = 0;
                                                                $attempted = 1; 
                                                                	 $totalcorrect = 1; 
                                                                    $totaloptions = 1;
                                                               if($seqno == 1)
                                                               {
                                                                $correctcount++;
                                                               }
                                                                $missed = 0; 
                                                         //    $missed = $totalcorrect - $correctcount; 
                                                        $wrong = $attempted - $correctcount;
                                                        $answer_score =(($totalcorrect - $wrong - $missed) )/ ($totalcorrect);                                       
                                                                $save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$assess_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);    
                                                               
                                                        //grasp index
                                                        
                                                        $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (select question_id from question_log where question_log_id='$question_log_id')";
                                                        $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                        $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                        $topic_id = $topic_id_get1['topic_id'];     
                                                        //check if row exists 
                                                        
                                                        $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id=3";
                                                        $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                        $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                        if($exists_rows > 0)
                                                        {
                                                             $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                             $auto_id = $topic_id_get2['auto_id'];
                                                             $total_count = $topic_id_get2['subtotal_of_count'];
                                                             $total_score = $topic_id_get2['subtotal_of_score'];
                                                             $total_count++;
                                                             $total_score +=$answer_score;
                                                        //update if yes
                                 				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                        
                                                        }
                                                        else
                                                        {
                                                            //insert if no
                                 				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','3','$student_id','1','$answer_score')";
					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                            
                                                        }
                                                        
                                                                                                                    
                                   				       	        $save_device1 = "insert into answer_options(assessment_answer_id,option_text,mtc_column,mtc_sequence,answer_text,scribble_id) values('$assess_id','$opttxt','$col','$seqno','$answertxt','$current_imageid')";
                            					                $save1 = SunDataBaseManager::getSingleton()->QueryDB($save_device1); 
                                                                if($save1)
                                                                {
                                                                    $answer_id = SunDataBaseManager::getSingleton()->getLastInsertId();
                                                                    $arr[SMC::$STATUS] = "Success";
                                                                    $arr[SMC::$ASSESSMENTANSWERID] = $assess_id;
                                                                }   
                                                            
                                                            
                                                        }                                                         
                                                        
                                                        
										}
                                        else
                                        {
                                            //multiple choice
                    									$student_state2 = "select question_id from question_log where question_log_id = '$question_log_id' limit 0,1";
                									$getquesid = SunDataBaseManager::getSingleton()->QueryDB($student_state2);
                									$question_id1 = mysql_fetch_assoc($getquesid);
                                                    $question_id = $question_id1['question_id'];                                             
                                                   $correctcount = 0;
                                                    $attempted = 1;     
                                                             //answer score logic
         										            // answer_score =(#correct_Selection - #incorrect_Selection - #missed_Selection) / (#Total_correct_answers)
                      
                                                            $totalcorrect = 0; 
                                                            $totaloptions = 0;
                             	                            $options1 = "select question_option, is_answer from question_options where question_id = '$question_id'";                                                      												
            					    	                    $option_list1 = SunDataBaseManager::getSingleton()->QueryDB($options1);
                    										while($option1 = mysql_fetch_assoc($option_list1))
                    											{
               											            $totaloptions++;
                    												$option_text = $option1['question_option'];
                                                                    $answer = $option1['is_answer'];
                                                                    if($answer == 1)
                                                                    {
                                                                        $totalcorrect++;
                                                                    }
                                                                     if(trim($opttxt) == trim($option_text) && $answer == 1)
                                                                    {
                                                                        $correctcount++;
                                                                    }
    
            		                                            }                                            
                                    				       	     $save_device1 = "insert into answer_options(assessment_answer_id,option_text,mtc_column,mtc_sequence,answer_text,scribble_id) values('$assess_id','$opttxt','$col','$seqno','$answertxt','$current_imageid')";
                            					                $save1 = SunDataBaseManager::getSingleton()->QueryDB($save_device1); 
                                                                if($save1)
                                                                {
                                                                    $answer_id = SunDataBaseManager::getSingleton()->getLastInsertId();
                                                                    $arr[SMC::$STATUS] = "Success";
                                                                    $arr[SMC::$ASSESSMENTANSWERID] = $assess_id;
                                                                }
                                                                //echo "tc:". $totalcorrect. " to: ".$totaloptions." att:".$attempted." cc:".$correctcount;
                                                                //$missed = $totalcorrect - $attempted; 
                                                                //$wrong = $totalcorrect - $correctcount;
                                                                //$answer_score =(($correctcount - $wrong - $missed) )/ ($totalcorrect);
                                                                 $missed = $totalcorrect - $correctcount; 
                                                                $wrong = $attempted - $correctcount;
                                                                 $answer_score =(($totalcorrect - $wrong - $missed) )/ ($totalcorrect);                                                            
                                                                if($answer_score < 0)
                                                                {
                                                                    $answer_score = 0;
                                                                }                                                             
                                          				       	$save_device = "update assessment_answers set answer_score= '$answer_score' where assessment_answer_id = '$assess_id'";
        					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);      
                                                                
                                                         if ($type == "Multiple Choice")  
                                                         {     
                                                        //grasp index
                                                        
                                                        $get_topic_id = "SELECT topic_id FROM questions WHERE question_id in (select question_id from question_log where question_log_id='$question_log_id')";
                                                        $topic_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_topic_id);
                                                        $topic_id_get1 = mysql_fetch_assoc($topic_id_get);
                                                        $topic_id = $topic_id_get1['topic_id'];     
                                                        //check if row exists 
                                                        
                                                        $get_exists_id = "SELECT auto_id,subtotal_of_count,subtotal_of_score FROM student_index WHERE class_session_id='$session_id' and topic_id = '$topic_id' and student_id='$student_id' and transaction_id=1";
                                                        $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                        $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                        if($exists_rows > 0)
                                                        {
                                                             $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                             $auto_id = $topic_id_get2['auto_id'];
                                                             $total_count = $topic_id_get2['subtotal_of_count'];
                                                             $total_score = $topic_id_get2['subtotal_of_score'];
                                                             $total_count++;
                                                             $total_score +=$answer_score;
                                                        //update if yes
                                 				       	$save_device = "update student_index set subtotal_of_count= '$total_count',subtotal_of_score='$total_score' where auto_id = '$auto_id'";
					                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                        
                                                        
                                                        }
                                                        else
                                                        {
                                                            //insert if no
                                 				       	   $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score) values ('$session_id','$topic_id','1','$student_id','1','$answer_score')";
					                                       $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);                                                               
                                                            
                                                        }    
                                                        
                                                        }                                                                                                                                                                    
                                            
                                            
                                        }
                                                                           

                                                                      
                                    
                                 }
                                               
                          }
                          else
                          {
                            $arr[SMC::$STATUS] = "SessionId or QuestionLogId or Studentid is missing.";
                            
                          }

							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}                      
			public function GetFeedback($assessment_answer_id=null)
				{
					try
						{
						  $volunteer_students1 = "SELECT u.image_path FROM assessment_answers a, uploaded_images u WHERE a.assessment_answer_id='$assessment_answer_id' and a.teacher_scribble_id = u.image_id";
                									$getnewseq = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students1);
                									$newseq_opt1 = mysql_fetch_assoc($getnewseq);  
                                                    $newseq_opt = $newseq_opt1['image_path']; 
                                                    if($newseq_opt == null)
                                                    {
                                                        $volunteer_students = "SELECT a.student_id,a.text_rating as text_rating,a.rating as star_rating,a.badge_id as badge_id FROM assessment_answers a WHERE a.assessment_answer_id='$assessment_answer_id'";
                            							$answer = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students);
                                                        $volunteered_students = mysql_fetch_assoc($answer);                                                
                                                    }
                                                    else
                                                    {
                                                           $volunteer_students = "SELECT u.image_path,a.student_id,a.text_rating as text_rating,a.rating as star_rating,a.badge_id as badge_id FROM assessment_answers a, uploaded_images u WHERE a.assessment_answer_id='$assessment_answer_id' and a.teacher_scribble_id = u.image_id";
                            							$answer = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students);
                                                        $volunteered_students = mysql_fetch_assoc($answer);                                                     
                                                    }								

							if ($volunteered_students)
								{
								     //and a.teacher_scribble_id = u.image_id, u.image_path as url,
								    $url = $volunteered_students["image_path"];
                                    $text_rating = $volunteered_students["text_rating"];
                                    $star_rating = $volunteered_students["star_rating"];
                                    $badge_id = $volunteered_students["badge_id"];
                                    $student_id = $volunteered_students["student_id"];
        							$arr[SMC::$STATUS] = "Success";
        							$arr[SMC::$TEXTRATING] = $text_rating;
        							$arr[SMC::$RATING] = $star_rating;
        							$arr[SMC::$BADGEID] = $badge_id;
        							$arr[SMC::$IMAGEPATH] = $url;       
                                    $arr[SMC::$ASSESSMENTANSWERID] = $assessment_answer_id;   
                                    $arr[SMC::$STUDENTID] = $student_id;   
                                                              
								}
							else
								{
									$arr[SMC::$STATUS] = "There is no feedback from the teacher for this.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
			public function RetrieveBadges()
				{
					try
						{
							$volunteer_students = "SELECT u.image_path, u.image_id FROM uploaded_images u WHERE u.image_type_id=3";
							$answer = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students);
                            $count3 = SunDataBaseManager::getSingleton()->getnoOfrows($answer);
                            $combo = "";
                            $combo1 = "";
                            while($volunteered_students = mysql_fetch_assoc($answer))
								{
								     //and a.teacher_scribble_id = u.image_id, u.image_path as url,
								    $url = $volunteered_students["image_path"];
                                    $badge_id = $volunteered_students["image_id"];
                                              $combo.= $url;
                                             if($count3 > 1)
                                             {
                                                $combo.= ";;;";
                                             }           
                                             
                                            
                                             $combo1.= $badge_id;
                                             if($count3 > 1)
                                             {
                                                $combo1.= ";;;";
                                             }           
                                             
                                             $count3--;                                                                               
                                                              
								}
        							$arr[SMC::$STATUS] = "Success";
        							$arr[SMC::$BADGEID] = $combo1;
        							$arr[SMC::$IMAGEPATH] = $combo;    
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
                
 			public function GetThisStudentSessions($user_id=null)
				{
					try
						{
							$session_details = "select  term.class_session_id, term.starts_on, term.ends_on,
user.first_name, user.user_id, state.state_description,class.class_id, class.class_name,
room.room_name, room.room_id, subject.subject_id, subject.subject_name
from class_sessions as term inner join student_class_map sclassmap on sclassmap.class_id = term.class_id
inner join tbl_auth as user on user.user_id=sclassmap.student_id
inner join entity_states as state on term.session_state = state.state_id
inner join classes as class on sclassmap.class_id = class.class_id
inner join rooms as room on term.room_id = room.room_id
inner join subjects as subject on subject.subject_id = class.subject_id
where sclassmap.student_id ='$user_id' and date(term.starts_on) = curdate()";
							$details=SunDataBaseManager::getSingleton()->QueryDB($session_details);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($details);
								    //get school
															$validate_password = "select school_id from tbl_auth where user_id = '$user_id'";
															$validate = SunDataBaseManager::getSingleton()->QueryDB($validate_password);
															
																	$user = mysql_fetch_assoc($validate);
																	$school_id = $user['school_id'];                            
							if($count>0)
								{
									$arr[SMC::$STATUS] = "Success";
									$i = 0;
        							$session_details1 = "SELECT now() as time from dual";
        							$details1=SunDataBaseManager::getSingleton()->QueryDB($session_details1);
                                    $detail1 = mysql_fetch_assoc($details1);
                                    $time = $detail1['time'];                                    
									while($detail = mysql_fetch_assoc($details))
										{
										      $session_id = $detail['class_session_id'];
                                              $new_state = $detail['state_description'];
										       //cancel
                                              // first DateTime object created based on the MySQL datetime format
                                            $dt1 = DateTime::createFromFormat('Y-m-d H:i:s', $detail['ends_on']);
                                            
                                            // second DateTime object created based on the MySQL datetime format
                                            $dt2 = DateTime::createFromFormat('Y-m-d H:i:s', $time);
                                               //change state to cancelled
                                               if(($dt1->format('U') < $dt2->format('U')) && (($detail['state_description'] =="Scheduled") || ($detail['state_description'] =="Opened")))
                                                {
                                                    $new_state = "Cancelled";
                        							$update_session_state = "update class_sessions set session_state = '6' where class_session_id = '$session_id'";
                        							$updated_session_state = SunDataBaseManager::getSingleton()->QueryDB($update_session_state);
                                                    $state = "";
                                                    if($detail['state_description'] == "Scheduled")
                                                    {
                                                        $state = "4";
                                                    }
                                                    else
                                                    {
                                                        $state = "2";
                                                    }
                                                    
                        							if($updated_session_state)
                        								{
                        									$log_session_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('2','$session_id','$state','6')";
                        									$logged_session_transition = SunDataBaseManager::getSingleton()->QueryDB($log_session_transition);
                        								}
                                                }
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID] = $detail['class_session_id'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$STARTTIME] = $this->mXMLManager->ReturnTimeOffset($detail['starts_on'],$school_id);
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$ENDTIME] = $this->mXMLManager->ReturnTimeOffset($detail['ends_on'],$school_id);
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONSTATE] = $new_state;
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$STUDENTNAME] = $detail['first_name'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$STUDENTID] = $detail['user_id'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$CLASSID] = $detail['class_id'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$CLASSNAME] = $detail['class_name'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$ROOMID] = $detail['room_id'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$ROOMNAME] = $detail['room_name'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SUBJECTID] = $detail['subject_id'];
											$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SUBJECTNAME] = $detail['subject_name'];
                                            
											$i++;

    										                                       
										}
								}
							else
								{
									$arr[SMC::$STATUS] = "You do not have any sessions today.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
                
			public function SendSignupInfo($deviceId=null,$institutionName=null,$email=null,$contactPerson=null,$phoneNo=null,$ipadFlag=null, $address=null,$city=null,$longitude=null, $latitude=null)
				{
					try
						{
						    $userOtp ="ToBeIntegrated";
					        $save_device = "INSERT INTO unregistered_users (unregistered_user_id, email_id, phone_number, user_otp, unique_identifier, active, institutionName, contactPerson, ipadFlag, address, city, latitude, longitude) VALUES (NULL, '$email', '$phoneNo', '$userOtp','$deviceId', '0', '$institutionName', '$contactPerson', '$ipadFlag', '$address', '$city', '$latitude', '$longitude')";
							echo $save_device;
                            $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
							$arr[SMC::$STATUS] = "Success";
							$retval = $this->mXMLManager->createXML($arr);
    $message1 = <<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,300,500,700' rel='stylesheet' type='text/css'>
</head>

<body style="background: #e8e8e8;font-size:12px;font-family: 'Raleway', sans-serif;">
<!-- ======================== Top Header ======================== -->
<div class="container" style="max-width:1200px;	width::100%; margin:0 auto;">
	<div class="header" style="	position:relative; background:#fff;padding-bottom:100px;margin-bottom:100px;">

            	<div class="logo" style="background-color: rgb(232, 232, 232); width: 100%;">
                	<a href="http://www.mindshiftapps.com/" style="display:block;" title=""><img src="http://www.mindshiftapps.com/img/logo1.png" alt="" ></a>
                </div>        
<!-- ======================== Bottom Header ======================== -->
        	<div class="hd_bt_wrap" style="	padding: 0 85px;">
            	<div class="hd_bt" style="	margin: 0 auto;	max-width: 932px; padding-top: 60px; text-align: center;	width: 100%; line-height: 95px; margin-bottom:100px;">
                	<h1 style="	color: #000; font-size: 40px; font-weight: 300;">We have received your request to try out Learniat.</h1>
                    <h3 style="	color: #999999;	font-size: 30px; font-weight: 300;">You have entered the following details:</h3>
                </div>
                    <div class="address_wrap">
                    	<form action="#">
                        	<div class="con_sec" style=" border-bottom:1px solid #dfdfdf; padding-bottom:10px; margin-bottom:10px;">
                                <label style="float: left; font-size:14px; font-weight:500; color:#999999;">Contact person:</label>
                                <h2 style="float: right; font-size:14px; font-weight:500; color:#000" >$contactPerson</h2>
                                <div class="clear" style="clear:both;margin:0;padding:0;"></div>
                            </div>
                            
                            <div class="con_sec" style=" border-bottom:1px solid #dfdfdf; padding-bottom:10px; margin-bottom:10px;">
                                <label style="float: left; font-size:14px; font-weight:500; color:#999999;">Email:</label>
                                <h2 style="float: right; font-size:14px; font-weight:500; color:#000" >$email</h2>
                                <div class="clear" style="clear:both;margin:0;padding:0;"></div>
                            </div>
                            
                            <div class="con_sec" style=" border-bottom:1px solid #dfdfdf; padding-bottom:10px; margin-bottom:10px;">
                                <label style="float: left; font-size:14px; font-weight:500; color:#999999;">Institute name:</label>
                                <h2 style="float: right; font-size:14px; font-weight:500; color:#000" >$institutionName</h2>
                                <div class="clear" style="clear:both;margin:0;padding:0;"></div>
                            </div>
                            
                            <div class="con_sec" style=" border-bottom:1px solid #dfdfdf; padding-bottom:10px; margin-bottom:10px;">
                                <label style="float: left; font-size:14px; font-weight:500; color:#999999;">Address:</label>
                                <h2 style="float: right; font-size:14px; font-weight:500; color:#000" >$address</h2>
                                <div class="clear" style="clear:both;margin:0;padding:0;"></div>
                            </div>
                            
                            <div class="con_sec" style=" border-bottom:1px solid #dfdfdf; padding-bottom:10px; margin-bottom:10px;">
                                <label style="float: left; font-size:14px; font-weight:500; color:#999999;">Phone number:</label>
                                <h2 style="float: right; font-size:14px; font-weight:500; color:#000" >$phoneNo</h2>
                                <div class="clear" style="clear:both;margin:0;padding:0;"></div>
                            </div>
                            
                            <div class="con_sec" style=" border-bottom:1px solid #dfdfdf; padding-bottom:10px; margin-bottom:10px;">
                                <label style="float: left; font-size:14px; font-weight:500; color:#999999;">IPads availability:</label>
                                <h2 style="float: right; font-size:14px; font-weight:500; color:#000" >Yes</h2>
                                <div class="clear" style="clear:both;margin:0;padding:0;"></div>
                            </div>
                    	</form>
                        <div class="after_btn" style="background: hsl(209, 49%, 44%) none repeat scroll 0 0;border-radius: 3px;color: hsl(0, 0%, 100%);display: block;font-size: 24px;height: 44px;line-height: 44px;margin: 85px auto 0;max-width: 320px;text-align: center;width: 100%;">Are the above details correct?</div>
                        <div class="bt_hd_text" style="line-height: 95px;margin: 45px auto 0;max-width: 950px;width: 100%;">
                        	<h1 style="font-size: 20px;font-weight: 100;text-align: left;"><span style="color: hsl(209, 49%, 44%);">If not!</span> Please reply to this email with the correct details and we will soon be in touch with you</h1>
                        </div>
                    </div>
            </div><!--hd_bt_wrap-->
    </div><!--header-->
<!-- ======================== Section No 2 ======================== -->
		
        <div class="sec2_wrap" style="margin-bottom:100px;background:#fff;padding-bottom:50px;">
        	<div class="inner_sec2" style="position:relative;border-bottom:1px solid #dfdfdf;padding-bottom:30px;">
                <div class="sec_banner2">
                    <img src="http://www.mindshiftapps.com/img/sec_banner2.png" alt="" width="100%">
                </div>
                <div class="sec_hd" style="left: 35%;position: absolute;top: 75px;">
                    <h2 style="text-align: center;font-size: 60px;font-weight: 300;color: #fff;">Teacher App</h2>
                </div>
                <div class="pra_text" style="padding:0 100px; text-align: center;margin-top: 10px;">
                    <p style="font-size: 20px;font-weight: 200;line-height: 27px;padding-bottom: 10px;">Learniat will be an ALL-IN-ONE Student Response system, Timetable Management System, Seating Management System, Lesson Plan Management System and Auto Alerting System useful for school administrators as well as parents. </p>
                    <p style="font-size: 20px;font-weight: 200;line-height: 27px;padding-bottom: 20px;">Teacher can interact with the student's work including annotating on it, which is fed back instantly to students. We'll show how, in the demo</p>
                </div>
            </div>
            <div class="sec2_image" style="margin: 50px auto;max-width: 931px;width: 100%;">
            	<img src="http://www.mindshiftapps.com/img/sec2_image.png" alt="" width="100%">
            </div>
            <div class="download_image" style="margin: 85px auto 0;max-width: 500px;width: 100%;">
            	<a href="https://itunes.apple.com/us/app/learniat-student/id972350068?ls=1&amp;mt=8" title="" style="display: block;"><img src="http://www.mindshiftapps.com/img/download_btn.png" alt=""  style="max-width: 500px;width: 100%;"></a>
            </div>
        </div><!--sec2_wrap-->
    
<!-- ======================== Section No 3 ======================== -->
		
        <div class="sec3_wrap" style="margin-bottom:45px;background:#fff;padding-bottom:50px;">
        	<div class="inner_sec2" style="position:relative;border-bottom:1px solid #dfdfdf;padding-bottom:30px;">
                <div class="sec_banner2">
                    <img src="http://www.mindshiftapps.com/img/sec_banner_3.png" alt="" style="width:100%;">
                </div>
                <div class="sec_hd" style="left: 35%;position: absolute;top: 75px;">
                    <h2 style="text-align: center;font-size: 60px;font-weight: 300;color: #fff;">Student App</h2>
                </div>
                <div class="pra_text" style="padding:0 100px; text-align: center;margin-top: 30px;">
                    <p style="font-size: 20px;font-weight: 200;line-height: 27px;padding-bottom: 20px;">Teachers and Students can annotate over a picture. Students can collaboratively create question options.   </p>
                    <p style="font-size: 20px;font-weight: 200;line-height: 27px;padding-bottom: 20px;">We will show you how your school will really benefit with Learniat powering up your classes. </p>
                </div>
            </div>
          <!--  <div class="sec2_image" style="margin: 50px auto;max-width: 931px;width: 100%;">
            	<img src="http://www.mindshiftapps.com/img/sec3_image.png" alt="" width="100%">
            </div> -->
            
            <div class="download_image" style="margin: 85px auto 0;max-width: 500px;width: 100%;">
            	<a href="https://itunes.apple.com/us/app/learniat-student/id972350068?ls=1&amp;mt=8" title="" style="display: block;"><img src="http://www.mindshiftapps.com/img/download_btn2.png" alt="" style="max-width: 500px;width: 100%;"></a>
            </div>
        </div><!--sec2_wrap-->
<!-- ======================== Footer Wrap ======================== -->
		<div class="footer_wrap">
        	<div class="bt_text" >
    			<p style="color: #999999;font-size: 20px;font-weight: 500;">If you are have received this email by mistake and you are not the correct recipient please notify us by reply email.</p>
    		</div>
            <div class="ft_bottom" style="margin:50px 0;">
            	<div class="footer_logo" style="float:left;">
                	<a href="http://www.mindshiftapps.com/" title=""><img src="http://www.mindshiftapps.com/img/ft_logo.png" alt=""></a>
                </div>
                <div class="social_icon" style="float:right;">
                	<ul>
                    	<li style="float:left;list-style:none;padding:0 30px;"><a href="http://www.mindshiftapps.com/" title="" style="display:block;"><img src="http://www.mindshiftapps.com/img/fb_icon.png" alt=""></a></li>
                        <li style="float:left;list-style:none;padding:0 30px;"><a href="http://www.mindshiftapps.com/" title="" style="display:block;"><img src="http://www.mindshiftapps.com/img/tw_icon.png" alt=""></a></li>
                        <li class="last2" style="float:left;list-style:none; padding:0 0 0 30px;"><a href="http://www.mindshiftapps.com/" title="" style="display:block;"><img src="http://www.mindshiftapps.com/img/in_icon.png" alt=""></a></li>
                        <div class="clear" style="clear:both;margin:0;padding:0;"></div>
                    </ul>
                </div>
                <div class="clear" style="clear:both;margin:0;padding:0;"></div>
            </div>
        </div><!--footer_wrap-->
</div> <!--container-->
</body>
</html>
EOF;
                            
                            $this->mXMLManager->SendEmail($message1,$email,"info@mindshiftapps.com");
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
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
                                    $arr[SMC::$STATUS] = "Success";
                                    $arr[SMC::$PARTICIPATIONINDEX] = $PI;
                                
                                } 
                                else
                                {   
                                    $arr[SMC::$STATUS] = "Success";
                                    $arr[SMC::$PARTICIPATIONINDEX] = 0;                               
                                }                                
                        
                            
                          }
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;	
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}        

                                     
 			public function GetGraspIndex($session_id=null,$student_id=null,$topic_id=null)
				{
					try
						{
						  if($student_id == null)
                          {
                            
                            if($topic_id == null)
                            {
                                //grasp for all topics for all students for the session    
                                $schoolidget = "SELECT t.school_id FROM class_sessions s,tbl_auth t where t.user_id=s.teacher_id and s.class_session_id='$session_id'";
                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($schoolidget);
                                $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                $schoolid = $topic_id_get2['school_id'];
                                $get_exists_id = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,si.subtotal_of_score,sim.weight_value FROM student_index si,school_index_map sim WHERE sim.school_id='$schoolid' and sim.index_type=1 and si.transaction_id=sim.transaction_type and si.class_session_id='$session_id'";
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
                                    $arr[SMC::$STATUS] = "Success";
                                    $arr[SMC::$GRASPINDEX] = $GI;
                                
                                } 
                                else
                                {   
                                    $arr[SMC::$STATUS] = "Success";
                                    $arr[SMC::$GRASPINDEX] = 0;                               
                                }                                
                                                      
                            }
                            else
                            {
                            	//okokokokokokokokokokoko
                                //return grasp for all students in topic
                                //grasp for all topics for all students fir the session    
                                $schoolidget = "SELECT t.school_id FROM class_sessions s,tbl_auth t where t.user_id=s.teacher_id and s.class_session_id='$session_id'";
                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($schoolidget);
                                $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                $schoolid = $topic_id_get2['school_id'];
                                $get_exists_id = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,si.subtotal_of_score,sim.weight_value FROM student_index si,school_index_map sim WHERE sim.school_id='$schoolid' and sim.index_type=1 and si.transaction_id=sim.transaction_type and si.class_session_id='$session_id' and si.topic_id = '$topic_id'";
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
                                    $arr[SMC::$STATUS] = "Success";
                                    $arr[SMC::$GRASPINDEX] = $GI;
                                
                                } 
                                else
                                {   
                                    $arr[SMC::$STATUS] = "Success";
                                    $arr[SMC::$GRASPINDEX] = 0;           
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
                                $get_exists_id = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,si.subtotal_of_score,sim.weight_value FROM student_index si,school_index_map sim WHERE sim.school_id='$schoolid' and sim.index_type=1 and si.transaction_id=sim.transaction_type and si.class_session_id='$session_id' and si.student_id='$student_id'";
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
                                    $arr[SMC::$STATUS] = "Success";
                                    $arr[SMC::$GRASPINDEX] = $GI;
                                
                                } 
                                else
                                {   
                                    $arr[SMC::$STATUS] = "Success";
                                    $arr[SMC::$GRASPINDEX] = 0;                             
                                }                                                                               
                            }
                            else
                            {
                                //return grasp for $topic_id + $student_id 
                                
                                
                                $get_exists_id = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,si.subtotal_of_score,sim.weight_value FROM student_index si,tbl_auth t,school_index_map sim WHERE si.student_id=t.user_id and t.school_id=sim.school_id and sim.index_type=1 and si.transaction_id=sim.transaction_type and si.class_session_id='$session_id' and si.topic_id = '$topic_id' and si.student_id='$student_id'";
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
                                    $arr[SMC::$STATUS] = "Success";
                                    $arr[SMC::$GRASPINDEX] = $GI;
                                
                                } 
                                else
                                {   
                                    $arr[SMC::$STATUS] = "Success";
                                    $arr[SMC::$GRASPINDEX] = 0;            
                                }                                
                                
                                
                            }                            
                            
                          }
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;	
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}                    
			public function GetMyState($user_id=null)
				{
					try
						{
							$detail = "SELECT e.state_description FROM tbl_auth as t inner join entity_states as e on e.state_id=t.user_state WHERE user_id='$user_id'";
							$student = SunDataBaseManager::getSingleton()->QueryDB($detail);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($student);
							if($count>0)
								{
									$arr[SMC::$STATUS] = "Success";
									$query = mysql_fetch_assoc($student);
									$arr[SMC::$USERSTATE] = $query['state_description'];
								}
							else
								{
									$arr[SMC::$STATUS] = "There are no results found for this user";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
    			public function SaveStudentQuery($student_id=null,$session_id=null,$query=null,$anonymous=null)
    				{
    					try
    						{
    						    if ($query != null)
                                {
                                    $query = mysql_escape_string($query);
        							if ($student_id != null)
        								{
        								    $trans = 26;
        								    if($anonymous == null)
                                            {
                                                $anonymous = 0;
                                                $trans = 28;
                                            }
        								    if($anonymous == 0)
                                            {
                                                $trans = 28;
                                            }                                            
                                            $topic_id = null;
                                            $get_exists_id = "SELECT current_topic FROM live_session_status WHERE session_id='$session_id' limit 0,1";
                                            $exists_id_get1 = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                            $topic_id_get21 = mysql_fetch_assoc($exists_id_get1);
                                            $topic_id = $topic_id_get21['current_topic'];
                                            if($topic_id)
                                            {
             									$arr[SMC::$STATUS] = "Success";
    									        $log_live_transition = "insert into student_query(student_id, class_session_id,topic_id, query_text,anonymous) values('$student_id','$session_id','$topic_id','$query','$anonymous')";
    											$logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);
                                                $id = SunDataBaseManager::getSingleton()->getLastInsertId();
                                                $arr[SMC::$QUERYID] = $id;
                                                $get_exists_id = "SELECT auto_id,subtotal_of_count FROM student_index WHERE class_session_id='$session_id' and student_id='$student_id' and transaction_id='$trans'";
                                                $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                if ($exists_rows > 0)
                                                {
                                                    $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                    $auto_id = $topic_id_get2['auto_id'];
                                                    $total_count = $topic_id_get2['subtotal_of_count'];
                                                    $total_count++;
                                                    //update if yes
                                                    $save_device = "update student_index set subtotal_of_count= '$total_count' where auto_id = '$auto_id'";
                                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                
                                                } else
                                                {
                                                    //insert if no
                                                    $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count) values ('$session_id','$topic_id','$trans','$student_id','1')";
                                                    $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                
                                                }                                                                                           
                                            }
                                            else
                                            {
                                                $arr[SMC::$STATUS] = "No current topic found for this session";
                                            }
                                            

        								}
        							else
        								{
        									$arr[SMC::$STATUS] = "No studentid passed.";
        								}                               
                                }
                                else
                                {
                                    $arr[SMC::$STATUS] = "Query is not passed.";
                                }

    							$retval = $this->mXMLManager->createXML($arr);
    							return $retval;
    						}
    					catch(Exception $e)
    						{
    							echo "error";
    						}
    				}
    			public function GetQueryResponse($query_id=null)
    				{
    					try
    						{
    						  if($query_id != null)
                              {
            							$volunteer_students = "SELECT student_id FROM student_query WHERE query_id = '$query_id'";
            							$answer = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students);
                                        $count3 = SunDataBaseManager::getSingleton()->getnoOfrows($answer);       
                                        if($count3 != 0)
                                        {
                                         $update = "select reply_text, badge_id, state from student_query where query_id = '$query_id'";
                                        $result = SunDataBaseManager::getSingleton()->QueryDB($update);
            							if($result) 
            								{
        										$arr[SMC::$STATUS] = "Success";
        										$user_details = mysql_fetch_assoc($result);
                                                $arr[SMC::$TEACHERREPLYTEXT] =$user_details['reply_text'];
                                                $arr[SMC::$BADGEID] = $user_details['badge_id'];
                                                if($user_details['state'] == '17')
                                                {
                                                    $arr[SMC::$DISMISSFLAG] = 1;
                                                }
                                                else
                                                {
                                                    $arr[SMC::$DISMISSFLAG] = 0;
                                                }
                                            }
                                            else
                                            {
                                                $arr[SMC::$STATUS] = "No results found";
                                            }                                              
                                        }
                                        else
                                        {
                                            $arr[SMC::$STATUS] = "Invalid query passed";
                                        }                         
                               
                              }
                              else
                              {
                                $arr[SMC::$STATUS] = "Query is not passed.";
                              }
                     
    							$retval = $this->mXMLManager->createXML($arr);
    							return $retval;
    						}
    					catch(Exception $e)
    						{
    							echo "error";
    						}
    				}                        
        			public function SaveMeTooVotes($query_id=null, $student_id=null)
        				{
        					try
        						{
        						  
        							if ($student_id != null)
        								{
        								    
           									$student_state = "select user_state,role_id from tbl_auth where user_id = '$student_id'";
        									$retrieve_student_state = SunDataBaseManager::getSingleton()->QueryDB($student_state);
        									$current_student_state = mysql_fetch_assoc($retrieve_student_state);
                                            $user_role = $current_student_state['role_id']; 
                                            if($user_role == 4)
                                            {
                      							$get_question = "update student_query set votes_received= votes_received+1 where query_id = '$query_id' and student_id = '$student_id'";
                    							$got_question = SunDataBaseManager::getSingleton()->QueryDB($get_question);
                    							if ($got_question)
                    								{
                    									$arr[SMC::$STATUS] = "Success";
                                                    }
                                                    else
                                                    {
                                                        $arr[SMC::$STATUS] = "Could not update vote count";
                                                    }                                              
                                            }
                                            else
                                            {
                                                $arr[SMC::$STATUS] = "Invalid Student";
                                            }
                                                
                                       }
                                       else
                                       {
                                          $arr[SMC::$STATUS] = "Student is not passed";
                                       }

        							$retval = $this->mXMLManager->createXML($arr);
        							return $retval;
        						}
        					catch(Exception $e)
        						{
        							echo "error";
        						}
        				}                        
               			public function FetchSRQ($session_id=null)
            				{
            					try
            						{
            						  if($session_id != null)
                                      {
            							$volunteer_students = "SELECT s.query_id as query_id,s.allow_volunteer as allow_volunteer, s.query_text as query_text,t.first_name as name FROM student_query s, tbl_auth t WHERE s.student_id= t.user_id and s.state=18 and s.class_session_id='$session_id'";
            							$answer = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students);
                                        $count3 = SunDataBaseManager::getSingleton()->getnoOfrows($answer);
                                        if($count3 > 0)
                                        {
                                            $combo = "";
                                            $combo1 = "";
                                            $combo2 = "";
                                            $combo3 = "";
                                            while($volunteered_students = mysql_fetch_assoc($answer))
                								{
                								     //and a.teacher_scribble_id = u.image_id, u.image_path as url,
                								    $url = $volunteered_students["query_id"];
                                                    $badge_id = $volunteered_students["allow_volunteer"];
                                                    $qtext = $volunteered_students["query_text"];
                                                    $fname = $volunteered_students["name"];
                                                    
                                                              $combo.= $url;
                                                             if($count3 > 1)
                                                             {
                                                                $combo.= ";;;";
                                                             }           
                                                             
                                                            
                                                             $combo1.= $badge_id;
                                                             if($count3 > 1)
                                                             {
                                                                $combo1.= ";;;";
                                                             }          
                                                             
                                                             $combo2.= $qtext;
                                                             if($count3 > 1)
                                                             {
                                                                $combo2.= ";;;";
                                                             } 
                                                             
                                                             $combo3.= $fname;
                                                             if($count3 > 1)
                                                             {
                                                                $combo3.= ";;;";
                                                             }                                                                                                                            
                                                             
                                                             $count3--;                                                                               
                                                                              
                								}
                        							$arr[SMC::$STATUS] = "Success";
                        							$arr[SMC::$ALLOWVOLUNTEERFLAG] = $combo1;
                        							$arr[SMC::$QUERYIDLIST] = $combo;        
                        							$arr[SMC::$QUERYTEXT] = $combo2;
                        							$arr[SMC::$STUDENTNAME] = $combo3;                                                                      
                                        }
                                        else
                                        {
                                            $arr[SMC::$STATUS] = "No results for the session passed";
                                        }
                                      
                                      }
                                      else
                                      {
                                        $arr[SMC::$STATUS] = "Session is not passed";
                                      }
   
            							$retval = $this->mXMLManager->createXML($arr);
            							return $retval;
            						}
            					catch(Exception $e)
            						{
            							echo "error";
            						}
            				}
    			public function VolunteerRegister($student_id=null,$query=null)
    				{
    					try
    						{
    						    if ($query != null)
                                {                             
        							if ($student_id != null)
        								{
        								    
           									$student_state = "select user_state,first_name,role_id from tbl_auth where user_id = '$student_id'";
        									$retrieve_student_state = SunDataBaseManager::getSingleton()->QueryDB($student_state);
        									$current_student_state = mysql_fetch_assoc($retrieve_student_state);
        									$old_student_state = $current_student_state['user_state']; 
                                            $user_role = $current_student_state['role_id']; 
                                            if($user_role == 4)
                                            {
                                                 if($old_student_state !=null)
                                                {
                                                    
                   									$student_state = "select student_id from query_volunteer where student_id = '$student_id' and query_id='$query'";
                									$retrieve_student_state = SunDataBaseManager::getSingleton()->QueryDB($student_state);
                                                     $count3 = SunDataBaseManager::getSingleton()->getnoOfrows($retrieve_student_state);                                                 
                                                    if($count3 == 0)
                                                    {
                                                        
                                                    
                                                        $student_name = $current_student_state['first_name'];                             
                                              
                                                        
                    									$arr[SMC::$STATUS] = "Success";
            									        $log_live_transition = "insert into query_volunteer(student_id, query_id,state) values('$student_id','$query','19')";
            											$logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);
                                                        $id = SunDataBaseManager::getSingleton()->getLastInsertId();
                                                        $arr[SMC::$VOLUNTEERID] = $id;
                                                        $arr[SMC::$STUDENTNAME] = $student_name;       
                                                        //pi
                            							$volunteer_state1 = "SELECT sq.class_session_id,sq.topic_id FROM query_volunteer qv, student_query sq WHERE qv.query_id = sq.query_id and sq.query_id='$query'";
                            							$retrieve_volunteer_state1 = SunDataBaseManager::getSingleton()->QueryDB($volunteer_state1);
                            							$current_volunteer_state1 = mysql_fetch_assoc($retrieve_volunteer_state1);     
                                                        $session_id = $current_volunteer_state1['class_session_id'];
                                                        $topic_id = $current_volunteer_state1['topic_id'];
                                                       // $student_id = $current_volunteer_state1['student_id'];          
                                                        
                                                        //check if row exists 
                                                                        
                                                        $get_exists_id = "SELECT auto_id,subtotal_of_count FROM student_index WHERE class_session_id='$session_id' and student_id='$student_id' and transaction_id=30";
                                                        $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                        $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                        if ($exists_rows > 0)
                                                        {
                                                            $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                            $auto_id = $topic_id_get2['auto_id'];
                                                            $total_count = $topic_id_get2['subtotal_of_count'];
                                                            $total_count++;
                                                            //update if yes
                                                            $save_device = "update student_index set subtotal_of_count= '$total_count' where auto_id = '$auto_id'";
                                                            $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                            //echo $save_device;
                                                        } else
                                                        {
                                                            //insert if no
                                                            $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count) values ('$session_id','$topic_id','30','$student_id','1')";
                                                            $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                           // echo $save_device;
                                                        }   
                                                    }
                                                    else
                                                    {
                                                        $arr[SMC::$STATUS] = "Student/Query combination exists.";
                                                    }                                                                                         
                                                }
                                                else
                                                {
                                                    $arr[SMC::$STATUS] = "No user found.";
                                                } 
                                              
                                            }
                                            else
                                            {
                                                $arr[SMC::$STATUS] = "Only a student can volunteer";
                                            }
   
                                            
                                            
        								}
        							else
        								{
        									$arr[SMC::$STATUS] = "No studentid passed.";
        								}                               
                                }
                                else
                                {
                                    $arr[SMC::$STATUS] = "Query is not passed.";
                                }

    							$retval = $this->mXMLManager->createXML($arr);
    							return $retval;
    						}
    					catch(Exception $e)
    						{
    							echo "error";
    						}
    				} 
                			public function GetVolunteerSelected($session_id=null)
            				{
            					try
            						{
            						  if($session_id != null)
                                      {
            							$volunteer_students = "SELECT qv.volunteer_id,tb.first_name FROM student_query as sq, query_volunteer as qv, tbl_auth as tb where qv.query_id=sq.query_id and sq.student_id= tb.user_id and sq.class_session_id='$session_id'";
            							$answer = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students);
                                        $count3 = SunDataBaseManager::getSingleton()->getnoOfrows($answer);
                                        if($count3 > 0)
                                        {
                                            $combo = "";
                                            $combo1 = "";
                                            while($volunteered_students = mysql_fetch_assoc($answer))
                								{
                								     //and a.teacher_scribble_id = u.image_id, u.image_path as url,
                								    $url = $volunteered_students["volunteer_id"];
                                                    $badge_id = $volunteered_students["first_name"];
                                                              $combo.= $url;
                                                             if($count3 > 1)
                                                             {
                                                                $combo.= ";;;";
                                                             }           
                                                             
                                                            
                                                             $combo1.= $badge_id;
                                                             if($count3 > 1)
                                                             {
                                                                $combo1.= ";;;";
                                                             }           
                                                             
                                                             $count3--;                                                                               
                                                                              
                								}
                        							$arr[SMC::$STATUS] = "Success";
                        							$arr[SMC::$STUDENTNAME] = $combo1;
                        							$arr[SMC::$VOLUNTEERID] = $combo;                         
                                        }
                                        else
                                        {
                                            $arr[SMC::$STATUS] = "No results for the session passed";
                                        }
                                      
                                      }
                                      else
                                      {
                                        $arr[SMC::$STATUS] = "Session is not passed";
                                      }
   
            							$retval = $this->mXMLManager->createXML($arr);
            							return $retval;
            						}
            					catch(Exception $e)
            						{
            							echo "error";
            						}
            				}
        			public function WithdrawStudentSubmission($AssessmentAnswerId = null,$query_id=null)
        				{
        					try
        						{
        						    if($query_id == "(null)" || $query_id == "%28null%29" || $query_id == null)
                                    {
                                        unset($query_id);
                                    }
        						    if($AssessmentAnswerId == "(null)" || $AssessmentAnswerId == "%28null%29" || $AssessmentAnswerId == null)
                                    {
                                        unset($AssessmentAnswerId);
                                    }                                    


        									if (empty($AssessmentAnswerId) and empty($query_id))
        										{
                                                    $arr[SMC::$STATUS] = "Both params are empty";        										  
        										}
        									else if (empty($AssessmentAnswerId) and !empty($query_id))
        										{
        										  $arr[SMC::$STATUS] = "Success";
   										            $state = '25';
         									        $query_state = "select student_id,class_session_id,state from student_query where query_id = '$query_id'";
                									$retrieve_query_state = SunDataBaseManager::getSingleton()->QueryDB($query_state);
                									$current_query_state = mysql_fetch_assoc($retrieve_query_state);
                									$old_query_state = $current_query_state['state'];
                                                    $student_id = $current_query_state['student_id'];
                                                    $session_id = $current_query_state['class_session_id'];
                                                    if($old_query_state!=$state)
                                                    {
         											$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$query_id','$old_query_state','$state')";
        											$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
        											}
        											$update = "update student_query set state = '$state' where query_id = '$query_id'";
                                                    //pir reduce withdraw
                                                    
                                                    $get_exists_id = "SELECT auto_id,subtotal_of_count FROM student_index WHERE class_session_id='$session_id' and student_id='$student_id' and transaction_id='28'";
                                                    $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                    $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                    if ($exists_rows > 0)
                                                    {
                                                        $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                        $auto_id = $topic_id_get2['auto_id'];
                                                        $total_count = $topic_id_get2['subtotal_of_count'];
                                                        $total_count--;
                                                        //update if yes
                                                        $save_device = "update student_index set subtotal_of_count= '$total_count' where auto_id = '$auto_id'";
                                                        $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                    
                                                    }                                                    
                                                    
                                                    
        										}
        									else if (!empty($AssessmentAnswerId) and empty($query_id))
        										{
        										  $arr[SMC::$STATUS] = "Success";
        											$update = "update assessment_answers set answer_withdrawn = '1' where assessment_answer_id = '$AssessmentAnswerId'";
        										}
                                                else
                                                {
                                                    $arr[SMC::$STATUS] = "Success";
    										            $state = '25';
         									        $query_state = "select student_id,class_session_id,state from student_query where query_id = '$query_id'";
                									$retrieve_query_state = SunDataBaseManager::getSingleton()->QueryDB($query_state);
                									$current_query_state = mysql_fetch_assoc($retrieve_query_state);
                									$old_query_state = $current_query_state['state'];
                                                    $student_id = $current_query_state['student_id'];
                                                    $session_id = $current_query_state['class_session_id'];
                                                    if($old_query_state!=$state)
                                                    { 
         											$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$query_id','$old_query_state','$state')";
        											$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
        											}
        											$update = "update student_query set state = '$state' where query_id = '$query_id'";
           											$update1 = "update assessment_answers set answer_withdrawn = '1' where assessment_answer_id = '$AssessmentAnswerId'";
                                                    $updated1 = SunDataBaseManager::getSingleton()->QueryDB($update1);
                                                    //pir reduce withdraw
                                                    
                                                    $get_exists_id = "SELECT auto_id,subtotal_of_count FROM student_index WHERE class_session_id='$session_id' and student_id='$student_id' and transaction_id='28'";
                                                    $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                                    $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                                    if ($exists_rows > 0)
                                                    {
                                                        $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                                        $auto_id = $topic_id_get2['auto_id'];
                                                        $total_count = $topic_id_get2['subtotal_of_count'];
                                                        $total_count--;
                                                        //update if yes
                                                        $save_device = "update student_index set subtotal_of_count= '$total_count' where auto_id = '$auto_id'";
                                                        $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                                    
                                                    }                                                     
                                                }

        							$updated = SunDataBaseManager::getSingleton()->QueryDB($update);
        							$retval = $this->mXMLManager->createXML($arr);
        							return $retval;
        						}
        					catch(Exception $e)
        						{
        							echo "error";
        						}
        				}                            
                 			public function RetrieveAggregateDrillDown($question_log_id=null, $seq=null)
            				{
            					try
            						{
            						  if($question_log_id != null)
                                      {
                                        $asq = "select questions.question_type_id,questions.topic_id, question_log.class_session_id from questions inner join question_types as types using (question_type_id) inner join question_log using(question_id) where question_log.question_log_id = '$question_log_id'";
                                        $questions = SunDataBaseManager::getSingleton()->QueryDB($asq);
                                        $forgi = mysql_fetch_assoc($questions);
                                         $topicid = $forgi["topic_id"];
                                         $class_session_id = $forgi["class_session_id"];
                                        $q_type = $forgi["question_type_id"];
                                        if($seq != null)
                                        {
                                            
                                            if($q_type == 3)
                                            {
                                               
                                            $asq0 = "SELECT question_id,mtc_sequence FROM question_options WHERE question_option_id= '$seq'";
                                            $questions0= SunDataBaseManager::getSingleton()->QueryDB($asq0);
                                            $forgi0 = mysql_fetch_assoc($questions0);
                                             $opttext0 = $forgi0["question_id"];    
                                            $mtcseq0 = $forgi0["mtc_sequence"]; 
                                            $asq1 = "SELECT question_option,mtc_sequence FROM question_options WHERE question_id= '$opttext0' and mtc_column=2 and mtc_sequence='$mtcseq0'";
                                            $questions1 = SunDataBaseManager::getSingleton()->QueryDB($asq1);
                                            $forgi1 = mysql_fetch_assoc($questions1);
                                             $opttext = $forgi1["question_option"];    
                                             $mtcseq = $forgi1["mtc_sequence"];       
                                                                     
                							
               							    $volunteer_students = "select assessment_answers.student_id, tbl_auth.first_name from answer_options,assessment_answers, tbl_auth where assessment_answers.assessment_answer_id = answer_options.assessment_answer_id and tbl_auth.user_id = assessment_answers.student_id and answer_options.old_sequence = answer_options.mtc_sequence and answer_options.option_text = '$opttext' and assessment_answers.question_log_id ='$question_log_id' and answer_options.mtc_sequence ='$mtcseq'";
                                            //echo $volunteer_students;
                                            $answer = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students);
                                            $count3 = SunDataBaseManager::getSingleton()->getnoOfrows($answer);
                                            if($count3 > 0)
                                            {
                                                $combo = "";
                                                $combo1 = "";
                                                $combo2 = "";
                                                $combo3 = "";
                                                while($volunteered_students = mysql_fetch_assoc($answer))
                    								{
                    								     //and a.teacher_scribble_id = u.image_id, u.image_path as url,
                    								    $url = $volunteered_students["student_id"];
                                                        $badge_id = $volunteered_students["first_name"];
                                                        $grasp = $this->mXMLManager->GetGraspIndex(null,$class_session_id,$url,$topicid);
                                                        $pi =$this->mXMLManager->GetParticipationIndex($class_session_id,$url);
                                                                  $combo.= $url;
                                                                 if($count3 > 1)
                                                                 {
                                                                    $combo.= ";;;";
                                                                 }           
                                                                 
                                                                
                                                                 $combo1.= $badge_id;
                                                                 if($count3 > 1)
                                                                 {
                                                                    $combo1.= ";;;";
                                                                 }       
                                                                 
                                                                 $combo2.= $grasp;
                                                                 if($count3 > 1)
                                                                 {
                                                                    $combo2.= ";;;";
                                                                 }       
                                                                 $combo3.= $pi;
                                                                 if($count3 > 1)
                                                                 {
                                                                    $combo3.= ";;;";
                                                                 }                                                                                                                                
                                                                 
                                                                 $count3--;                                                                               
                                                                                  
                    								}
                            							$arr[SMC::$STATUS] = "Success";
                            							$arr[SMC::$STUDENTNAME] = $combo1;
                            							$arr[SMC::$STUDENTID] = $combo;
                                                        $arr[SMC::$GRASPINDEX] = $combo2;
                                                        $arr[SMC::$PARTICIPATIONINDEX] = $combo3;
                                              }                        
                                        

 //incorrect
                							$volunteer_students2 = "select assessment_answers.student_id, tbl_auth.first_name from answer_options,assessment_answers, tbl_auth where assessment_answers.assessment_answer_id = answer_options.assessment_answer_id and tbl_auth.user_id = assessment_answers.student_id and answer_options.old_sequence <> answer_options.mtc_sequence and answer_options.old_sequence ='$mtcseq' and assessment_answers.question_log_id ='$question_log_id'";
                							$answer2 = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students2);
                                            $count32 = SunDataBaseManager::getSingleton()->getnoOfrows($answer2);
                                            if($count32 > 0)
                                            {
                                                $combo = "";
                                                $combo1 = "";
                                                $combo2 = "";
                                                $combo3 = "";
                                                while($volunteered_students2 = mysql_fetch_assoc($answer2))
                    								{
                    								     //and a.teacher_scribble_id = u.image_id, u.image_path as url,
                    								    $url = $volunteered_students2["student_id"];
                                                        $badge_id = $volunteered_students2["first_name"];
                                                        $grasp = $this->mXMLManager->GetGraspIndex(null,$class_session_id,$url,$topicid);
                                                        $pi =$this->mXMLManager->GetParticipationIndex($class_session_id,$url);
                                                                  $combo.= $url;
                                                                 if($count32 > 1)
                                                                 {
                                                                    $combo.= ";;;";
                                                                 }           
                                                                 
                                                                
                                                                 $combo1.= $badge_id;
                                                                 if($count32 > 1)
                                                                 {
                                                                    $combo1.= ";;;";
                                                                 }       
                                                                 
                                                                 $combo2.= $grasp;
                                                                 if($count32 > 1)
                                                                 {
                                                                    $combo2.= ";;;";
                                                                 }   
                                                                 
                                                                 $combo3.= $pi;
                                                                 if($count32 > 1)
                                                                 {
                                                                    $combo3.= ";;;";
                                                                 }                                                                                                                                     
                                                                 
                                                                 $count32--;                                                                               
                                                                                  
                    								}
                            							
                            							$arr[SMC::$STUDENTNAMEWRONG] = $combo1;
                            							$arr[SMC::$STUDENTIDWRONG] = $combo;
                                                        $arr[SMC::$GRASPINDEXWRONG] = $combo2;
                                                        $arr[SMC::$PARTICIPATIONINDEXWRONG] = $combo3;
                                              }                                                       
                                            }
                                            else
                                            {
                                
                                                $asq0 = "SELECT question_option FROM question_options WHERE question_option_id= '$seq'";
                                                $questions0= SunDataBaseManager::getSingleton()->QueryDB($asq0);
                                                $forgi0 = mysql_fetch_assoc($questions0);
                                                 $opttext0 = $forgi0["question_option"];                                                    
                     							$volunteer_students = "select DISTINCT assessment_answers.student_id, tbl_auth.first_name from answer_options,assessment_answers, tbl_auth where assessment_answers.assessment_answer_id = answer_options.assessment_answer_id and tbl_auth.user_id = assessment_answers.student_id and answer_options.option_text = '$opttext0' and assessment_answers.question_log_id ='$question_log_id'";
                    // A CLAUSE DISTINCT ADDED BY SANJAY ON JULY 30, 2015  to get rid of the duplicte name bug.
                    							$answer = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students);
                                                $count3 = SunDataBaseManager::getSingleton()->getnoOfrows($answer);
                                                if($count3 > 0)
                                                {
                                                    $combo = "";
                                                    $combo1 = "";
                                                    $combo2 = "";
                                                    $combo3 = "";
                                                    while($volunteered_students = mysql_fetch_assoc($answer))
                        								{
                        								     //and a.teacher_scribble_id = u.image_id, u.image_path as url,
                        								    $url = $volunteered_students["student_id"];
                                                            $badge_id = $volunteered_students["first_name"];
                                                            $grasp = $this->mXMLManager->GetGraspIndex(null,$class_session_id,$url,$topicid);
                                                            $pi =$this->mXMLManager->GetParticipationIndex($class_session_id,$url);
                                                                      $combo.= $url;
                                                                     if($count3 > 1)
                                                                     {
                                                                        $combo.= ";;;";
                                                                     }           
                                                                     
                                                                    
                                                                     $combo1.= $badge_id;
                                                                     if($count3 > 1)
                                                                     {
                                                                        $combo1.= ";;;";
                                                                     }       
                                                                     
                                                                     $combo2.= $grasp;
                                                                     if($count3 > 1)
                                                                     {
                                                                        $combo2.= ";;;";
                                                                     }            
                                                                     
                                                                     $combo3.= $pi;
                                                                     if($count3 > 1)
                                                                     {
                                                                        $combo3.= ";;;";
                                                                     }                                                                                                                                  
                                                                     
                                                                     $count3--;                                                                               
                                                                                      
                        								}
                                							$arr[SMC::$STATUS] = "Success";
                                							$arr[SMC::$STUDENTNAME] = $combo1;
                                							$arr[SMC::$STUDENTID] = $combo;
                                                            $arr[SMC::$GRASPINDEX] = $combo2;
                                                            $arr[SMC::$PARTICIPATIONINDEX] = $combo3;
                                                  }                                               
                                                    
                                                
                                            }
                 
  	
                                            
                                          }
                                            else
                                      {
                                        $arr[SMC::$STATUS] = "OptionId is not passed";
                                      }  
                                       
                                      }
                                      else
                                      {
                                        $arr[SMC::$STATUS] = "Question is not passed";
                                      }
   
            							$retval = $this->mXMLManager->createXML($arr);
            							return $retval;
            						}
            					catch(Exception $e)
            						{
            							echo "error";
            						}
            				}

                                                                                        
 			public function GetAllRooms($school_id=null)
				{
					try
						{
						    $retval = "<Root><SunStone><Action>";
						    $detail_room = "select r.room_id,r.room_name, s.seat_rows, s.seat_columns,s.seats_removed from rooms as r,seating_grids as s where r.room_id=s.room_id and r.school_id ='$school_id'";					        
							$student = SunDataBaseManager::getSingleton()->QueryDB($detail_room);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($student);
							if($count>0)
								{
								    $retval .= "<Status>Success</Status>";
									$retval .= "<Rooms>";
								    $arr[SMC::$STATUS] = "Success";
									while($seat = mysql_fetch_assoc($student))
										{
										  $roomid = $seat['room_id'];
                                          $roomname= $seat['room_name'];
                                          $rows = $seat['seat_rows'];
                                          $columns = $seat['seat_columns'];
                                          $sremoved = $seat['seats_removed'];
                                          $retval .= "<Room>";
                                          $retval .= "<RoomId>$roomid</RoomId><RoomName>$roomname</RoomName>";
                                          $retval .= "<Rows>$rows</Rows><Columns>$columns</Columns><SeatsRemoved>$sremoved</SeatsRemoved>";
         								 $query_state = "select seat_id, seat_number from seats where room_id = '$roomid'";
 									     $student1 = SunDataBaseManager::getSingleton()->QueryDB($query_state);
      									 	$i = 0;
                                             while($seat1 = mysql_fetch_assoc($student1))
                                             {
                                                $i++;
    
    											$seat_id[] = $seat1['seat_id'];
    											$seat_label[] = $seat1['seat_number'];                                            
                                                
                                             }  
                                             
    		        	                      $seat_ids = implode(";;;",$seat_id);
    				                          $seat_labels = implode(";;;",$seat_label); 
                                              
                                              $students = "SELECT distinct scm.student_id FROM class_sessions cs, student_class_map scm WHERE cs.class_id=scm.class_id and cs.room_id='$roomid'";                                              
        							          $studentc = SunDataBaseManager::getSingleton()->QueryDB($students);
                  							  $sccount = SunDataBaseManager::getSingleton()->getnoOfrows($studentc);                                             
                                              $retval .= "<SeatIdList>$seat_ids</SeatIdList><SeatLabelList>$seat_labels</SeatLabelList>";                                         								  
										      $retval .= "<SeatsConfigured>$i</SeatsConfigured><StudentsRegistered>$sccount</StudentsRegistered>";
                                              $retval .= "</Room>";
                                            
										}
									$retval .= "</Rooms>";
								}
							else
								{
								    $retval .= "<Status>There are no rooms for this school.</Status>";
								}
							$retval .= "</Action></SunStone></Root>";
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
    			public function GetModelAnswer($question_log_id=null)
    				{
    					try
    						{
    							$volunteer_students = "select model_answer from assessment_answers where question_log_id = '$question_log_id'";
    							$volunteered_students = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students);
    							$count = SunDataBaseManager::getSingleton()->getnoOfrows($volunteered_students);
    							if ($count > 0)
    								{
    									$arr[SMC::$STATUS] = "Success";
    									$i = 0;
                                        if($count == 1)
                                        {
                                            $student = mysql_fetch_assoc($volunteered_students);
                                            $arr[SMC::$MODELANSWERFLAG] = $student['model_answer'];
                                        }
                                        else
                                        {
     									  while($students = mysql_fetch_assoc($volunteered_students))
    										{
    											$arr[SMC::$MODELANSWERS][$i][SMC::$MODELANSWERFLAG] = $students['model_answer'];
    											$i++;
    										}                                           
                                        }

    								}
    							else
    								{
    									$arr[SMC::$STATUS] = "There are no model answers for this question.";
    								}
    							$retval = $this->mXMLManager->createXML($arr);
    							return $retval;
    						}
    					catch(Exception $e)
    						{
    							echo "error";
    						}
    				}         
   			public function GetAllModelAnswers($question_log_id=null)
    				{
    					try
    						{
    							$volunteer_students = "SELECT q.question_name,aa.teacher_scribble_id, aa.assessment_answer_id,ao.scribble_id,aa.student_id,tb.first_name,qt.question_type_title  FROM assessment_answers aa,tbl_auth tb,question_types qt,question_log ql, questions q,answer_options ao WHERE aa.student_id=tb.user_id and ql.question_log_id=aa.question_log_id and ql.question_id =q.question_id and qt.question_type_id = q.question_type_id and ao.assessment_answer_id = aa.assessment_answer_id and model_answer = '1' and aa.question_log_id='$question_log_id'";
    							$volunteered_students = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students);
    							$count = SunDataBaseManager::getSingleton()->getnoOfrows($volunteered_students);
    							if ($count > 0)
    								{
    									$arr[SMC::$STATUS] = "Success";
    									$i = 0;



     									  while($students = mysql_fetch_assoc($volunteered_students))
    										{
    										    //student
    										    $scribbleid = $students['scribble_id'];
            						          	$volunteer_students1 = "SELECT image_path FROM uploaded_images WHERE image_id='$scribbleid'";
            						          	$volunteered_students1 = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students1);    								    
            								    $scribble = mysql_fetch_assoc($volunteered_students1);   
                                                //teacher
    										    $tscribbleid = $students['teacher_scribble_id'];
            						          	$volunteer_students2 = "SELECT image_path FROM uploaded_images WHERE image_id='$tscribbleid'";
            						          	$volunteered_students2 = SunDataBaseManager::getSingleton()->QueryDB($volunteer_students2);    								    
            								    $tscribble = mysql_fetch_assoc($volunteered_students2);                                                   
                                                $arr[SMC::$ASSESSMENTANSWERIDLIST][SMC::$ASSESSMENTANSWERID][$i][SMC::$ASSESSMENTANSWERID] = $students['assessment_answer_id'];
                                                $arr[SMC::$ASSESSMENTANSWERIDLIST][SMC::$ASSESSMENTANSWERID][$i][SMC::$IMAGE] = $scribble['image_path'];
                                                $arr[SMC::$ASSESSMENTANSWERIDLIST][SMC::$ASSESSMENTANSWERID][$i][SMC::$TEACHERSCRIBBLE] = $tscribble['image_path'];
                                                $arr[SMC::$ASSESSMENTANSWERIDLIST][SMC::$ASSESSMENTANSWERID][$i][SMC::$STUDENTID] = $students['student_id'];
                                                $arr[SMC::$ASSESSMENTANSWERIDLIST][SMC::$ASSESSMENTANSWERID][$i][SMC::$STUDENTNAME] = $students['first_name'];
                                                $arr[SMC::$ASSESSMENTANSWERIDLIST][SMC::$ASSESSMENTANSWERID][$i][SMC::$QUESTIONTYPE] = $students['question_type_title'];
                                                if($students['question_type_title'] == "Text")
                                                {
                                                    $assessid = $students['assessment_answer_id'];
                                                   	$retrieve = "select answer_text from answer_options where assessment_answer_id = '$assessid'";
        											$answer = SunDataBaseManager::getSingleton()->QueryDB($retrieve);
        											$option = mysql_fetch_assoc($answer);
                                                    $arr[SMC::$ASSESSMENTANSWERIDLIST][SMC::$ASSESSMENTANSWERID][$i][SMC::$TEXTANSWER] = $option['answer_text'];
                                                }
    											$i++;
    										}                                           


    								}
    							else
    								{
    									$arr[SMC::$STATUS] = "There are no model answers for this question.";
    								}
    							$retval = $this->mXMLManager->createXML($arr);
    							return $retval;
    						}
    					catch(Exception $e)
    						{
    							echo "error";
    						}
    				}
    			public function EndVolunteeringSession($session_id=null,$queryidlist=null,$metoolist= null)
    				{
    					try
    						{
                                        $removed = null;
                                        $metooremoved = null;
                                    					if (!empty($queryidlist))
														{
														 //  echo "1isnide";
															$count = substr_count($queryidlist, ';;;');
															if ($count>=1)
																{
																	$removed = explode(";;;",$queryidlist);
																}
															else
																{
																	$removed = $queryidlist;
																}
														}
													else
														{
															$removed = null;
														}
                                                        
                                                        
													if ($removed != null && is_array($removed))
													{
													 //  echo "isnide";
                                    					if (!empty($metoolist))
														{
															$count2 = substr_count($metoolist, ';;;');
															if ($count2>=1)
																{
																	$metooremoved = explode(";;;",$metoolist);
																}
															else
																{
																	$metooremoved = $metoolist;
																}
                                                               
                                                             for($p = 0;$p < count($removed);$p++)
                                                             {
                                                                $queryid = $removed[$p];
                                                                $metoo = $metooremoved[$p];
                                                                if(!empty($queryid))
                                                                {
                                                                    $change_state = "UPDATE  student_query SET  votes_received =  '$metoo' WHERE  query_id ='$queryid'";
																   // echo "sql:".$change_state;
                                                                    $change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
                                                                    
                                                                }
                                                             }   
                                                                
                                                        }													   
													   
													   
                                                    }
													   
                               
    						//	$change_state = "update student_query set allow_volunteer = '0',selected_for_voting='0' where class_session_id = '$session_id'";
    						//	$change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
                                                  $state = '24';
         									        $query_state1 = "select state,query_id,student_id from student_query where class_session_id = '$session_id'";
                								//	echo "q1: ".$query_state1;
                                                    $retrieve_query_state1 = SunDataBaseManager::getSingleton()->QueryDB($query_state1);
                									while($current_query_state1 = mysql_fetch_assoc($retrieve_query_state1))
                                                    {
                    									$old_query_state = $current_query_state1['state'];
                                                        $query_id = $current_query_state1['query_id'];
                                                        $student_id = $current_query_state1['student_id'];
                                                        if($old_query_state == '19')
                                                        {
                  											$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('5','$query_id','$old_query_state','$state')";
                											$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
                											$update = "update student_query set state = '$state' where class_session_id = '$session_id'";
                                                            $updated1 = SunDataBaseManager::getSingleton()->QueryDB($update);                                                       
                                                        }
             									        $query_state = "select state,volunteer_id from query_volunteer where query_id='$query_id'";
                    								//	echo "getting qv for: ".$query_state;
                                                        $retrieve_query_state = SunDataBaseManager::getSingleton()->QueryDB($query_state);
                    									while($current_query_state = mysql_fetch_assoc($retrieve_query_state))
                                                        {
                                                            
                        									$old_query_state1 = $current_query_state['state'];
                                                            $vol_id = $current_query_state['volunteer_id'];
                                                           // echo "vol: ".$vol_id." state: ".$old_query_state1;
                                                            if($old_query_state1 == '19')
                                                            {
                                                                $state = '6';
                      											$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('6','$vol_id','$old_query_state1','$state')";
                    											$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
                    											$update = "update query_volunteer set state = '$state' where volunteer_id='$vol_id'";
                                                                $updated1 = SunDataBaseManager::getSingleton()->QueryDB($update);                                                       
                                                            }
                                                            else if($old_query_state1 == '20')
                                                            {
                                                                $state = '24';
                      											$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('6','$vol_id','$old_query_state1','$state')";
                    											$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
                    											$update = "update query_volunteer set state = '$state' where volunteer_id='$vol_id'";
                                                                $updated1 = SunDataBaseManager::getSingleton()->QueryDB($update);                                                       
                                                            }                                                             
                                                        }
         									            $query_state12 = "SELECT user_state FROM tbl_auth WHERE user_id='$student_id'";
                                                        $retrieve_query_state12 = SunDataBaseManager::getSingleton()->QueryDB($query_state12);                                                        
                                                        $current_query_state12 = mysql_fetch_assoc($retrieve_query_state12);
                                                        $old_mute_state = $current_query_state12['user_state'];
                                                        if($old_mute_state == '21')
                                                        {                                                        
                                                            $recelect="SELECT session_state FROM class_sessions WHERE class_session_id='$session_id'";
                                                            $retrievestate = SunDataBaseManager::getSingleton()->QueryDB($recelect);                                                        
                                                            $current_sess_state12 = mysql_fetch_assoc($retrievestate);
                                                            $sess_state = $current_sess_state12['session_state'];  
                                                            if($sess_state == '1')
                                                            {
                     											$update = "update tbl_auth set user_state = '1' where user_id='$student_id'";
                                                                $updated1 = SunDataBaseManager::getSingleton()->QueryDB($update);     
                      											$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$student_id','21','1')";
                    											$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);                                                                                                                             
                                                            }
                                                            else if($sess_state == '7')
                                                            {
                      											$update = "update tbl_auth set user_state = '7' where user_id='$student_id'";
                                                                $updated1 = SunDataBaseManager::getSingleton()->QueryDB($update);     
                      											$log_query_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('1','$student_id','21','7')";
                    											$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);                                                                                                                                
                                                            }                                                               
                                                        }
                                                    }                                                                                                                                                      

    									$arr[SMC::$STATUS] = "Success";
    
    							$retval = $this->mXMLManager->createXML($arr);
    							return $retval;
    						}
    					catch(Exception $e)
    						{
    							echo "error";
    						}
    				}     
        			public function GetMaxStudentsRegistered($roomId=null)
        				{
        					try
        						{
                                   // old query: $students = "SELECT distinct scm.student_id FROM class_sessions cs, student_class_map scm WHERE cs.class_id=scm.class_id and cs.room_id='$roomId'";     
                                   $students = "SELECT MAX(total_students) as cw from (SELECT count(*) total_students,class_id from student_class_map where class_id in (select distinct class_id from class_sessions where room_id = '$roomId') group by class_id) as T1";                                         
			                        $studentc = SunDataBaseManager::getSingleton()->QueryDB($students);
								    $student_c_details = mysql_fetch_assoc($studentc);
				                    $sccount= $student_c_details['cw'];                                    
    							    // old query: $sccount = SunDataBaseManager::getSingleton()->getnoOfrows($studentc);              
        							if($sccount>0)
        								{
        									$arr[SMC::$STATUS] = "Success";
        									$arr[SMC::$STUDENTSREGISTERED] = $sccount;
        								}
        							else
        								{
        									$arr[SMC::$STATUS] = "There are no results found for this room";
        								}
        							$retval = $this->mXMLManager->createXML($arr);
        							return $retval;
        						}
        					catch(Exception $e)
        						{
        							echo "error";
        						}
        				}
			public function GetState($entity_type=null, $entity_id=null)
				{
					try
						{
						  if($entity_type == "user")
                          {
 							$user_name = "select user_state from tbl_auth where user_id = '$entity_id'";
							$result = SunDataBaseManager::getSingleton()->QueryDB($user_name);
							if($result) 
								{
									$count=SunDataBaseManager::getSingleton()->getnoOfrows($result);
									if($count>0)
										{
											$arr[SMC::$STATUS] = "Success";
											$user_details = mysql_fetch_assoc($result);
											$arr[SMC::$ENTITYSTATE] = $user_details['user_state'];
										}
									else
										{
											$arr[SMC::$STATUS] = "Could not retrieve info, please try again";
										}
									
								}
							else
								{
									$arr[SMC::$STATUS] = "User Id sent is not valid.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;                           
                          }
                          elseif($entity_type == "session")
                          {
 							$user_name = "select session_state from class_sessions where class_session_id = '$entity_id'";
							$result = SunDataBaseManager::getSingleton()->QueryDB($user_name);
							if($result) 
								{
									$count=SunDataBaseManager::getSingleton()->getnoOfrows($result);
									if($count>0)
										{
											$arr[SMC::$STATUS] = "Success";
											$user_details = mysql_fetch_assoc($result);
											$arr[SMC::$ENTITYSTATE] = $user_details['session_state'];
										}
									else
										{
											$arr[SMC::$STATUS] = "Could not retrieve info, please try again";
										}
									
								}
							else
								{
									$arr[SMC::$STATUS] = "Session Id sent is not valid.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;                              
                            
                          }
                          elseif($entity_type == "room")
                          { 							
                            $user_name = "select state from rooms where room_id = '$entity_id'";
							$result = SunDataBaseManager::getSingleton()->QueryDB($user_name);
							if($result) 
								{
									$count=SunDataBaseManager::getSingleton()->getnoOfrows($result);
									if($count>0)
										{
											$arr[SMC::$STATUS] = "Success";
											$user_details = mysql_fetch_assoc($result);
											$arr[SMC::$ENTITYSTATE] = $user_details['state'];
										}
									else
										{
											$arr[SMC::$STATUS] = "Could not retrieve info, please try again";
										}
									
								}
							else
								{
									$arr[SMC::$STATUS] = "Room Id sent is not valid.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;                              
                            }
                          elseif($entity_type == "seat")
                          { 							
                            $user_name = "select seat_state from seat_assignments where seat_id = '$entity_id'";
							$result = SunDataBaseManager::getSingleton()->QueryDB($user_name);
							if($result) 
								{
									$count=SunDataBaseManager::getSingleton()->getnoOfrows($result);
									if($count>0)
										{
											$arr[SMC::$STATUS] = "Success";
											$user_details = mysql_fetch_assoc($result);
											$arr[SMC::$ENTITYSTATE] = $user_details['seat_state'];
										}
									else
										{
											$arr[SMC::$STATUS] = "Could not retrieve info, please try again";
										}
									
								}
							else
								{
									$arr[SMC::$STATUS] = "Seat Id sent is not valid.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;                              
                            }                            
                          elseif($entity_type == "query")
                          { 							
                            $user_name = "select state from student_query where query_id = '$entity_id'";
							$result = SunDataBaseManager::getSingleton()->QueryDB($user_name);
							if($result) 
								{
									$count=SunDataBaseManager::getSingleton()->getnoOfrows($result);
									if($count>0)
										{
											$arr[SMC::$STATUS] = "Success";
											$user_details = mysql_fetch_assoc($result);
											$arr[SMC::$ENTITYSTATE] = $user_details['state'];
										}
									else
										{
											$arr[SMC::$STATUS] = "Could not retrieve info, please try again";
										}
									
								}
							else
								{
									$arr[SMC::$STATUS] = "Query Id sent is not valid.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;                              
                            }  	
                          else
                          { 	
                            //volunteering
                            $user_name = "select state from query_volunteer where volunteer_id = '$entity_id'";
							$result = SunDataBaseManager::getSingleton()->QueryDB($user_name);
							if($result) 
								{
									$count=SunDataBaseManager::getSingleton()->getnoOfrows($result);
									if($count>0)
										{
											$arr[SMC::$STATUS] = "Success";
											$user_details = mysql_fetch_assoc($result);
											$arr[SMC::$ENTITYSTATE] = $user_details['state'];
										}
									else
										{
											$arr[SMC::$STATUS] = "Could not retrieve info, please try again";
										}
									
								}
							else
								{
									$arr[SMC::$STATUS] = "Volunteer Id sent is not valid.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;                              
                            } 
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}                     
                                       
       			public function GetQuestionDetails($question_id=null)
        				{
        					try
        						{
                                    $ques_scores = "SELECT answer_score FROM assessment_answers WHERE question_log_id in (select question_log_id from question_log where question_id='$question_id')";                                                                            
                                    $scores_list = SunDataBaseManager::getSingleton()->QueryDB($ques_scores);                                                                            
                                    $scores_count = SunDataBaseManager::getSingleton()->getnoOfrows($scores_list);
                                    $sum_of_scores = 0;
                                    while($scores = mysql_fetch_assoc($scores_list))
                                    {
                                        $thescore = $scores['answer_score'];
                                        if($thescore == "" || $thescore == null)
                                        {
                                            $thescore = 0;
                                        }
                                        $sum_of_scores += $thescore;
                                    }
                                    if($scores_count == 0)
                                    {
                                        $avg_score = 0;
                                    }
                                    else
                                    {
                                        $avg_score = $sum_of_scores/$scores_count;    
                                    }                   

        									$arr[SMC::$STATUS] = "Success";
        									$arr[SMC::$QUESTIONAVGSCORE] = $avg_score;
                                            $arr[SMC::$NUMBEROFRESPONSES] = $scores_count;
 
        							$retval = $this->mXMLManager->createXML($arr);
        							return $retval;
        						}
        					catch(Exception $e)
        						{
        							echo "error";
        						}
        				}
                        
			public function NewXMPP($message_type=null, $content=null, $from_user_id=null, $to_user_id=null)
				{
					try
						{
             			 if(!empty($message_type) && !empty($content) && !empty($from_user_id) && !empty($to_user_id))
                          {
                              $content = mysql_escape_string($content);
                              $log_live_transition = "insert into message_log(message_type, message_content,sender_id, receiver_id) values('$message_type','$content','$from_user_id','$to_user_id')";
        					  $logged_live_transition = SunDataBaseManager::getSingleton()->QueryDB($log_live_transition);
                              $id = SunDataBaseManager::getSingleton()->getLastInsertId();
                              $arr[SMC::$STATUS] = "Success";
                              $arr[SMC::$MESSAGEID] = $id;                          
                          }
                          else
                          {
                            $arr[SMC::$STATUS] = "All parameters are compulsory.";
                          }	

							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}      
                
			public function GetAllStudentStates($session_id=null)
				{
					try
						{
							$students_registered = "select map.student_id,tbl_auth.user_state from student_class_map as map inner join class_sessions as session on map.class_id = session.class_id inner join tbl_auth on map.student_id=tbl_auth.user_id where session.class_session_id = '$session_id'";
							$get_registered = SunDataBaseManager::getSingleton()->QueryDB($students_registered);
							$registered_count = SunDataBaseManager::getSingleton()->getnoOfrows($get_registered);
							if ($registered_count > 0)
								{
									$arr[SMC::$STATUS] = "Success";
									$i = 0;
									while($students = mysql_fetch_assoc($get_registered))
										{
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID] = $students['student_id'];
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTSTATE] = $students['user_state'];
											$i++;
										}
								}
							else
								{
									$arr[SMC::$STATUS] = "There are no students in this session, please try again.";
								}
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}
                
 			public function GetAllStudentIndex($session_id=null,$topic_id=null)
				{
					try
						{
                            if($topic_id == null)
                            {
                                if($session_id != null)
                                {

                            $info = "SELECT user.first_name, user.user_id, states.state_description AS user_state
FROM tbl_auth AS user
INNER JOIN entity_states AS states ON user.user_state = states.state_id
INNER JOIN student_class_map AS map ON map.student_id = user.user_id
INNER JOIN class_sessions AS session ON session.class_id = map.class_id
WHERE session.class_session_id =  '$session_id'
ORDER BY user.first_name";
							
							//$info = "select user.first_name, user.user_id, states.state_description as user_state, seat.seat_number, assign.seat_id, state.state_description as seat_state from tbl_auth as user inner join entity_states as states on user.user_state = states.state_id inner join student_class_map as map on map.student_id = user.user_id inner join class_sessions as session on session.class_id = map.class_id left join seat_assignments as assign on assign.student_id = user.user_id and assign.class_session_id = '$session_id' left join seats as seat on seat.seat_id = assign.seat_id inner join entity_states as state on assign.seat_state = state.state_id where session.class_session_id = '$session_id'";
							$student = SunDataBaseManager::getSingleton()->QueryDB($info);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($student);
							if($count>0)
								{
									$arr[SMC::$STATUS] = "Success";
									$i = 0;
								    while($session = mysql_fetch_assoc($student))
										{
											$user_id = $session['user_id'];                                    
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID] = $user_id;                                            
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$GRASPINDEX] = $this->mXMLManager->GetGraspIndex(null,$session_id,$user_id,null);
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$PARTICIPATIONINDEX] = $this->mXMLManager->GetParticipationIndex($session_id,$user_id);
											$i++;
                                        }                                    
                                    
                                    
                                    
                                    
                                }
                     
                                }
                                else
                                {
                                    $arr[SMC::$STATUS] = "Both params cant be null";
                                }                                
                                                      
                            }
                            else
                            {
                                if($session_id != null)
                                {
 
                             $info = "SELECT user.first_name, user.user_id, states.state_description AS user_state
FROM tbl_auth AS user
INNER JOIN entity_states AS states ON user.user_state = states.state_id
INNER JOIN student_class_map AS map ON map.student_id = user.user_id
INNER JOIN class_sessions AS session ON session.class_id = map.class_id
WHERE session.class_session_id =  '$session_id'
ORDER BY user.first_name";
							
							//$info = "select user.first_name, user.user_id, states.state_description as user_state, seat.seat_number, assign.seat_id, state.state_description as seat_state from tbl_auth as user inner join entity_states as states on user.user_state = states.state_id inner join student_class_map as map on map.student_id = user.user_id inner join class_sessions as session on session.class_id = map.class_id left join seat_assignments as assign on assign.student_id = user.user_id and assign.class_session_id = '$session_id' left join seats as seat on seat.seat_id = assign.seat_id inner join entity_states as state on assign.seat_state = state.state_id where session.class_session_id = '$session_id'";
							$student = SunDataBaseManager::getSingleton()->QueryDB($info);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($student);
							if($count>0)
								{
									$arr[SMC::$STATUS] = "Success";
									$i = 0;
								    while($session = mysql_fetch_assoc($student))
										{
											$user_id = $session['user_id'];                                    
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID] = $user_id;                                            
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$GRASPINDEX] = $this->mXMLManager->GetGraspIndex(null,$session_id,$user_id,$topic_id);
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$PARTICIPATIONINDEX] = $this->mXMLManager->GetParticipationIndex($session_id,$user_id);
											$i++;
                                        }                                    
                                    
                                    
                                    
                                    
                                }                              
                                }
                                else
                                {
                                    
                                      //return grasp for all sessions
                                    //grasp for all topics for all students fir the session    
                                    $schoolidget = "SELECT s.school_id FROM topic t,subjects s WHERE t.subject_id=s.subject_id and t.topic_id='$topic_id'";
                                    $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($schoolidget);
                                    $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                    $schoolid = $topic_id_get2['school_id'];
                                    $getstudents = "SELECT student_id FROM student_class_map scm, classes c,topic t WHERE c.class_id=scm.class_id and t.subject_id=c.subject_id and t.topic_id='$topic_id'";
							$student = SunDataBaseManager::getSingleton()->QueryDB($getstudents);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($student);
							if($count>0)
								{
									$arr[SMC::$STATUS] = "Success";
									$i = 0;
								    while($session = mysql_fetch_assoc($student))
										{
											$user_id = $session['student_id'];                                    
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID] = $user_id;                                            
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$GRASPINDEX] = $this->mXMLManager->GetGraspIndex(null,null,$user_id,$topic_id);
                                             $get_exists_id = "SELECT si.transaction_id,si.auto_id,si.subtotal_of_count,sim.weight_value FROM student_index si,tbl_auth t,school_index_map sim WHERE si.student_id=t.user_id and t.school_id=sim.school_id and sim.index_type=2 and si.transaction_id=sim.transaction_type and t.school_id='$schoolid' and si.student_id='$user_id'";
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
                                                $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$PARTICIPATIONINDEX] = $PI;            
                                            } 
                                            else
                                            {   
                                                $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$PARTICIPATIONINDEX] = 0;     
                                            } 											
                                            
											$i++;
                                        }                                    
                                                                        
                                  }
                                    
                                }
                                                                                                 
                           }

							$retval = $this->mXMLManager->createXML($arr);
							return $retval;	
						}
					catch(Exception $e)
						{
							echo "error";
						}
				} 
                
			public function RecoverFromCrash($session_id=null,$type=null)
				{
					try
						{
						  if($type == "Only Questions")
                          {
							$check = "SELECT `assessment_answer_id` FROM `assessment_answers` WHERE `class_session_id`= '$session_id'";
							$check_type = SunDataBaseManager::getSingleton()->QueryDB($check);
							$i = 0;
							while($check_type1 = mysql_fetch_assoc($check_type))
								{     
								    $assessment_answer_id = $check_type1['assessment_answer_id'];
                            
                            if($assessment_answer_id != null)
                            {
                                
                            
							$check = "select questions.topic_id, question_log.class_session_id,question_log.question_id,answer.rating, answer.text_rating, answer.badge_id, types.question_type_title, answer.student_id from questions inner join question_types as types using (question_type_id) inner join question_log using(question_id) inner join assessment_answers as answer using(question_log_id) where answer.assessment_answer_id = '$assessment_answer_id'";
							$check_type3 = SunDataBaseManager::getSingleton()->QueryDB($check);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($check_type3);
							if($count > 0)
								{
								    
									$assessment = mysql_fetch_assoc($check_type3);
									$arr[SMC::$STATUS] = "Success";
									$topicid = $assessment['topic_id'];
									$sessionid = $assessment['class_session_id'];
									$studentid = $assessment['student_id'];                                                                                                            
                                    $QuestionId = $assessment['question_id'];
                                   // echo "quesid:".$QuestionId;
									$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$RATING] = $assessment['rating'];
									$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$TEXTRATING] = $assessment['text_rating'];
									$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$BADGEID] = $assessment['badge_id'];
                                    if($assessment['rating'] ==null && $assessment['text_rating']==null)
                                    {
                                        $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$ANSWERSTATE] = "Pending";
                                    }
                                    else
                                    {
                                        $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$ANSWERSTATE] = "Evaluated";   
                                    }
									$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$GRASPINDEX] = $this->mXMLManager->GetGraspIndex(null,$sessionid,$studentid,$topicid);
									$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$PARTICIPATIONINDEX] = $this->mXMLManager->GetParticipationIndex($sessionid,$studentid);
									$type = $assessment['question_type_title'];
                                    $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$QUESTIONTYPE] = $type;
                                    $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID] = $assessment['student_id'];
                                    $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$ASSESSMENTANSWERID] = $assessment_answer_id;
									if ($type == "Multiple Response")
										{									  
											$options = "select option_text from answer_options where assessment_answer_id = '$assessment_answer_id'";
											$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
											$j = 0;
											while($option = mysql_fetch_assoc($option_list))
												{
												 $options1 = "select question_option, is_answer from question_options where question_id = '$QuestionId'";
					    	                    $option_list1 = SunDataBaseManager::getSingleton()->QueryDB($options1);
        										while($option1 = mysql_fetch_assoc($option_list1))
        											{
        												$option_text = $option1['question_option'];
                                                        $answer = $option1['is_answer'];
                                                     if(trim($option['option_text']) == trim($option_text) && $answer == 1)
                                                    {
                                                        $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$OPTIONS][SMC::$OPTION][$j][SMC::$ISANSWER] = 1;
                                                        break;
                                                    }
                                                    else
                                                    {
                                                        $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$OPTIONS][SMC::$OPTION][$j][SMC::$ISANSWER] = 0;
                                                    }
  
		                                            }
													$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$OPTIONS][SMC::$OPTION][$j][SMC::$OPTIONTEXT] = $option['option_text'];

                                                    
													$j++;
												}
										}
									else if ($type == "Multiple Choice")
										{
										  $j = 0;
											$options = "select option_text from answer_options where assessment_answer_id = '$assessment_answer_id'";
											$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
										//	$option = mysql_fetch_assoc($option_list);
                                            $combo = "";
                                            $count3 = SunDataBaseManager::getSingleton()->getnoOfrows($option_list);
                                            while($option = mysql_fetch_assoc($option_list))
                                            {
                                              
                                             $options1 = "select question_option, is_answer from question_options where question_id = '$QuestionId'";
					    	                    $option_list1 = SunDataBaseManager::getSingleton()->QueryDB($options1);
        										while($option1 = mysql_fetch_assoc($option_list1))
        											{
        												$option_text = $option1['question_option'];
                                                        $answer = $option1['is_answer'];
        												
                                                         if(trim($option['option_text']) == trim($option_text) && $answer == 1)
                                                            {
                                                                $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$ISANSWER] = 1;
                                                                break;
                                                            }
                                                            else
                                                            {
                                                                $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$ISANSWER] = 0;
                                                                
                                                            }
                                                       }
                                             $combo.= $option['option_text'];
                                             if($count3 > 1)
                                             {
                                                $combo.= ";;;";
                                             }           
                                             
                                             $count3--;                                     
                                            }
                                            $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$OPTIONS][SMC::$OPTION][$j][SMC::$OPTIONTEXT] = $combo;
											     
                                                            
										}
									else if ($type == "Fresh Scribble" or $type == "Overlay Scribble")
										{
											$retrieve = "select image.image_path as teacher_scribble, images.image_path as scribble from assessment_answers as answer left join uploaded_images as image on answer.teacher_scribble_id = image.image_id left join answer_options as options on options.assessment_answer_id = answer.assessment_answer_id left join uploaded_images as images on options.scribble_id = images.image_id where options.assessment_answer_id = '$assessment_answer_id'";
											$answer = SunDataBaseManager::getSingleton()->QueryDB($retrieve);
											$option = mysql_fetch_assoc($answer);
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$SCRIBBLE] = $option['scribble'];
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$TEACHERSCRIBBLE] = $option['teacher_scribble'];
										}
									else if ($type == "Text")
										{
											$retrieve = "select answer_text from answer_options where assessment_answer_id = '$assessment_answer_id'";
											$answer = SunDataBaseManager::getSingleton()->QueryDB($retrieve);
											$option = mysql_fetch_assoc($answer);
											$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$TEXTANSWER] = $option['answer_text'];
										}
									else if ($type == "Match Columns")
										{
											$options = "select option_text, mtc_column, old_sequence, mtc_sequence from answer_options where assessment_answer_id = '$assessment_answer_id'";
											$option_list = SunDataBaseManager::getSingleton()->QueryDB($options);
											$j = 0;
											while($option = mysql_fetch_assoc($option_list))
												{
													$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$OPTIONS][SMC::$OPTION][$j][SMC::$OPTIONTEXT] = $option['option_text'];
													$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$OPTIONS][SMC::$OPTION][$j][SMC::$COLUMN] = $option['mtc_column'];
													$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$OPTIONS][SMC::$OPTION][$j][SMC::$SEQUENCE] = $option['mtc_sequence'];
                                                    $arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$OPTIONS][SMC::$OPTION][$j][SMC::$OLDSEQ] = $option['old_sequence'];
													$j++;
												}
										}
								}
							else
								{
									$arr[SMC::$STATUS] = "This student has not answered the question yet, please try again later.";
								}
                                }
                                else
                                {
                                    $arr[SMC::$STATUS] = "No assessment answerid passed";
                                }
                                 $i++;
                            
                            }
                            
                          }
                          else
                          {
                            							$check = "SELECT `query_id` FROM `student_query` WHERE state=16 and `class_session_id`= '$session_id'";
							$check_type = SunDataBaseManager::getSingleton()->QueryDB($check);
							$i = 0;
							while($check_type1 = mysql_fetch_assoc($check_type))
								{     
								    $query_id = $check_type1['query_id'];
                            
                            if($query_id != null)
                            {
    
                            
                           	$detail = "select user.first_name, query.student_id, query.query_text,query.reply_text,query.badge_id, query.start_time, query.anonymous from student_query as query inner join tbl_auth as user on query.student_id = user.user_id where query.query_id = '$query_id'";
							$student = SunDataBaseManager::getSingleton()->QueryDB($detail);
							$count = SunDataBaseManager::getSingleton()->getnoOfrows($student);
							if($count>0)
								{
									$arr[SMC::$STATUS] = "Success";
									$query = mysql_fetch_assoc($student);
                                    $user_id = $query['student_id'];
								    //get school
															$validate_password = "select school_id from tbl_auth where user_id = '$user_id'";
															$validate = SunDataBaseManager::getSingleton()->QueryDB($validate_password);
															
																	$user = mysql_fetch_assoc($validate);
																	$school_id = $user['school_id'];                                         
                                                                        $arr[SMC::$STUDENTS][SMC::$QUERY][SMC::$QUERYID] = $query_id;
									$arr[SMC::$STUDENTS][$i][SMC::$QUERY][SMC::$STUDENTID] = $query['student_id'];
									$arr[SMC::$STUDENTS][$i][SMC::$QUERY][SMC::$STUDENTNAME] = $query['first_name'];
									$arr[SMC::$STUDENTS][$i][SMC::$QUERY][SMC::$QUERYTEXT] = $query['query_text'];
									$arr[SMC::$STUDENTS][$i][SMC::$QUERY][SMC::$STARTTIME] = $this->mXMLManager->ReturnTimeOffset($query['start_time'],$school_id);
									$arr[SMC::$STUDENTS][SMC::$QUERY][SMC::$ANONYMOUS] = $query['anonymous'];
                                    $arr[SMC::$STUDENTS][$i][SMC::$QUERY][SMC::$QUERYID] = $query_id;
                                    $st_id = $query['student_id'];
							$student_state = "select user_state from tbl_auth where user_id = '$st_id'";
							$retrieve_student_state = SunDataBaseManager::getSingleton()->QueryDB($student_state);
							$current_student_state = mysql_fetch_assoc($retrieve_student_state);    
                                    if($current_student_state['user_state'] == '21')
                                    {
                                      $arr[SMC::$STUDENTS][$i][SMC::$QUERY][SMC::$MUTESTATE] =   1;
                                    }
                                    else
                                    {
                                        $arr[SMC::$STUDENTS][$i][SMC::$QUERY][SMC::$MUTESTATE] =   0;
                                    }                                                             
                                    if($query['badge_id'] == 1)
                                    {
                                      $arr[SMC::$STUDENTS][$i][SMC::$QUERY][SMC::$GOODQUERYSTATE] =   1;
                                    }
                                    else
                                    {
                                        $arr[SMC::$STUDENTS][$i][SMC::$QUERY][SMC::$GOODQUERYSTATE] =   0;
                                    }                                    
                                    if($query['reply_text'] == null)
                                    {
                                      $arr[SMC::$STUDENTS][$i][SMC::$QUERY][SMC::$TEXTREPLYSTATE] =   0;
                                    }
                                    else
                                    {
                                        $arr[SMC::$STUDENTS][$i][SMC::$QUERY][SMC::$TEXTREPLYSTATE] =   1;
                                    }
								}
							else
								{
									$arr[SMC::$STATUS] = "There are no queries with this ID, please try again.";
								}
                                }
                                $i++;
                                }
                                


                            
                          } 
						  	$retval = $this->mXMLManager->createXML($arr);
							return $retval;


						}
					catch(Exception $e)
						{
							echo "error";
						}
				}    
                
			public function RecordSuggestion($student_id=null,$suggestionText=null,$category_id=null, $session_id=null, $topic_id=null,$device_id=null,$uuid=null)
				{
					try
						{
                                                          // echo "got: ".$suggestionText;
									$current_time_select="select current_timestamp";
									$select_time_q=SunDataBaseManager::getSingleton()->QueryDB($current_time_select);
									$fetch_time=mysql_fetch_assoc($select_time_q);
									$curr_time=$fetch_time['current_timestamp'];
									$log_query_transition = "insert into suggestion_options(suggestion_txt,cat_id, student_id, session_id,last_updated, suggestion_state) values('$suggestionText','$category_id','$student_id','$session_id','$curr_time',27)";
//echo $log_query_transition;									
$logged_query_transition = SunDataBaseManager::getSingleton()->QueryDB($log_query_transition);
									$id = SunDataBaseManager::getSingleton()->getLastInsertId();
                                    
                                    //pi
                                    
                                    //check if row exists
                                    
                                    $get_exists_id = "SELECT auto_id,subtotal_of_count FROM student_index WHERE class_session_id='$session_id' and student_id='$student_id' and transaction_id=33";
                                    $exists_id_get = SunDataBaseManager::getSingleton()->QueryDB($get_exists_id);
                                    $exists_rows = SunDataBaseManager::getSingleton()->getnoOfrows($exists_id_get);
                                    if ($exists_rows > 0)
                                    {
                                        $topic_id_get2 = mysql_fetch_assoc($exists_id_get);
                                        $auto_id = $topic_id_get2['auto_id'];
                                        $total_count = $topic_id_get2['subtotal_of_count'];
                                        $total_count++;
                                        //update if yes
                                        $save_device = "update student_index set subtotal_of_count= '$total_count' where auto_id = '$auto_id'";
                                        $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                    
                                    } else
                                    {
                                        //insert if no
                                        $save_device = "insert into student_index(class_session_id,topic_id,transaction_id,student_id,subtotal_of_count) values ('$session_id','$topic_id','33','$student_id','1')";
                                        $save = SunDataBaseManager::getSingleton()->QueryDB($save_device);
                                    
                                    }                                    
                                    
                                    $arr[SMC::$STATUS] = "Success";
                                    $arr[SMC::$SUGGESTIONID] = $id;
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}                            
    			public function ClearQuestion($queryidlist=null)
    				{
    					try
    						{
													if (!empty($queryidlist))
													{
													   $arr[SMC::$STATUS] = "Success";
            											$retrieve = "SELECT question_log_id FROM question_log WHERE question_id= '$queryidlist'";
            											$answer = SunDataBaseManager::getSingleton()->QueryDB($retrieve);
                                                        $question_log_id = "";
                                                        $i = 0;
            												while($option = mysql_fetch_assoc($answer))
        											         {
        											             if($i > 0)
                                                                 {
                                                                    $question_log_id.=","; 
                                                                 }
            											         $question_log_id.= $option['question_log_id'];
                                                                 $i++;
                                                              }   
                                                        $count = 0;
            											$retrieve1 = "SELECT * FROM  `assessment_answers` WHERE question_log_id IN ( $question_log_id)";
            											 
                                                        $answer1 = SunDataBaseManager::getSingleton()->QueryDB($retrieve1);
                                                        $count = SunDataBaseManager::getSingleton()->getnoOfrows($answer1);
                                                       
                                                        if($count == 0)
                                                        {
                                                            
                                                                    $change_state = "SET FOREIGN_KEY_CHECKS=0";
                                                                    $change = SunDataBaseManager::getSingleton()->QueryDB($change_state);                                                            
                                                            
                                                                    $change_state = "DELETE FROM suggestion_options where question_id =  '$queryidlist'";
                                                                    $change = SunDataBaseManager::getSingleton()->QueryDB($change_state);                                                            


                                                                    $change_state = "DELETE FROM question_log where question_id =  '$queryidlist'";
                                                                    $change = SunDataBaseManager::getSingleton()->QueryDB($change_state);     
                                                                    
                                                                    
                                                                    $change_state = "DELETE FROM questions where question_id =  '$queryidlist'";
                                                                    $change = SunDataBaseManager::getSingleton()->QueryDB($change_state);                                                                         
 
                                                                     $change_state = "DELETE FROM question_options where question_id =  '$queryidlist'";
                                                                    $change = SunDataBaseManager::getSingleton()->QueryDB($change_state);
                                                                    
                                                                    
                                                                    $change_state = "SET FOREIGN_KEY_CHECKS=1";
                                                                    $change = SunDataBaseManager::getSingleton()->QueryDB($change_state);                                                            
                                                                        
                                                            
                                                        }
                                                        else
                                                        {
                                                            $arr[SMC::$NUMBEROFRESPONSES] = $count;
                                                            
                                                        }												                                                    
                                                    }
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
                    }  
            public function FetchCategory($input_category=null,$topic_id=null)
            {
            	try
            	{
            		$arr[SMC::$STATUS]="Success";
            		if($input_category!=null)
            		{
            			$i=0;
            			if($topic_id!=null)
            			{
            				//PRIORITY 1: Category title starts from $input_category (having topic_id)
            				$select_category="SELECT category_title,category_id,popularity,t1.topic_id,t1.topic_name, t1.parent_topic_id, t2.topic_name as parent_topic_name,subject_name
            				FROM category
            				INNER JOIN topic as t1
            				ON category.topic_id=t1.topic_id
            				INNER JOIN subjects
            				ON t1.subject_id=subjects.subject_id
                            LEFT JOIN topic as t2
                            ON t1.parent_topic_id = t2.topic_id
            				WHERE category_title like '$input_category%'
                            AND category.topic_id='$topic_id'";
                            //echo $select_category."<br>";
            				$category_query=SunDataBaseManager::getSingleton()->QueryDB($select_category);
            				while ($row=mysql_fetch_array($category_query))
            				{
            					if($i>=100)
            					{
            						break;
            					}
            					$category_suggestion=$row['category_title'];
            					$cat_suggested[$i]=$row['category_id'];
            					$cat_suggested3[$i]=$row['category_id'];
            					$cat_suggested1[$i]=$category_suggestion;
            					$tpc_name[$i]=$row['topic_name'];
            					$parent_topic_name[$i]=$row['parent_topic_name'];
            					$subject_name[$i]=$row['subject_name'];
            					$popularities[$i]=$row['popularity'];
            					$t=0;
            					$flag=0;
            					while($t<$i)
            					{
            						if($popularities[$i]>$popularities[$t])
            						{
            							$flag=1;
            							break;
            						}
            						$t++;
            					}
            					if($flag==1)
            					{
            						$temp=$popularities[$i];
            						$temp2=$cat_suggested1[$i];
            						$temp3=$tpc_name[$i];
            						$temp4=$cat_suggested3[$i];
            						$temp5=$parent_topic_name[$i];
            						$temp6=$subject_name[$i];
            						$k=$i;
            						while ($k>=$t)
            						{
            							$popularities[($k+1)]=$popularities[$k];
            							$cat_suggested1[($k+1)]=$cat_suggested1[$k];
            							$tpc_name[($k+1)]=$tpc_name[$k];
            							$cat_suggested3[($k+1)]=$cat_suggested3[$k];
            							$parent_topic_name[($k+1)]=$parent_topic_name[$k];
            							$subject_name[($k+1)]=$subject_name[$k];
            							$k--;
            						}
            						$popularities[$t]=$temp;
            						$cat_suggested1[$t]=$temp2;
            						$tpc_name[$t]=$temp3;
            						$cat_suggested3[$t]=$temp4;
            						$parent_topic_name[$t]=$temp5;
            						$subject_name[$t]=$temp6;
            					}
            					$i++;
            				}
            				//PRIORITY 2: Category title contains $input_category (having topic_id)
            				if($i<100)
            				{
            					$select_category="SELECT category_title,category_id,popularity,t1.topic_id,t1.topic_name, t1.parent_topic_id, t2.topic_name as parent_topic_name,subject_name
            						FROM category
            						INNER JOIN topic as t1
            						ON category.topic_id=t1.topic_id
            						INNER JOIN subjects
            						ON t1.subject_id=subjects.subject_id
                            		LEFT JOIN topic as t2
                            		ON t1.parent_topic_id = t2.topic_id
            						WHERE category_title like '%$input_category%'
                            		AND category.topic_id='$topic_id'";	
            					$category_query=SunDataBaseManager::getSingleton()->QueryDB($select_category);
            					$j=0;
            					while ($row=mysql_fetch_array($category_query))
            					{
            						if($i>=100)
            						{
            							break;
            						}
            						$category_suggestion=$row['category_id'];
            						if(!(in_array($category_suggestion,$cat_suggested)))
            						{
            							$cat_suggested2[$j]=$category_suggestion;
										$popularities[$i]=$row['popularity'];
										$tpc_name[$i]=$row['topic_name'];
										$cat_suggested3[$i]=$row['category_id'];
										$cat_suggested1[$i]=$row['category_title'];
										$parent_topic_name[$i]=$row['parent_topic_name'];
            							$subject_name[$i]=$row['subject_name'];
										$t=0;
            							$flag=0;
            							while($t<$i)
            							{
            								if($popularities[$i]>$popularities[$t])
            								{
            									$flag=1;
            									break;
            								}
            								$t++;
            							}
            							if($flag==1)
            							{
            								$temp=$popularities[$i];
            								$temp2=$cat_suggested1[$i];
            								$temp3=$tpc_name[$i];
            								$temp4=$cat_suggested3[$i];
            								$temp5=$parent_topic_name[$i];
            								$temp6=$subject_name[$i];
            								$k=$i;
            								while ($k>=$t)
            								{
            									$popularities[($k+1)]=$popularities[$k];
            									$cat_suggested1[($k+1)]=$cat_suggested1[$k];
            									$tpc_name[($k+1)]=$tpc_name[$k];
            									$cat_suggested3[($k+1)]=$cat_suggested3[$k];
            									$parent_topic_name[($k+1)]=$parent_topic_name[$k];
            									$subject_name[($k+1)]=$subject_name[$k];
            									$k--;
            								}
            								$popularities[$t]=$temp;
            								$cat_suggested1[$t]=$temp2;
            								$tpc_name[$t]=$temp3;
            								$cat_suggested3[$t]=$temp4;
            								$parent_topic_name[$t]=$temp5;
            								$subject_name[$t]=$temp6;
            							}
            							$j++;
            							$i++;
            						}
            					}
            				}
            				$n=0;
            				while ($n<$j)
            				{
            					$cat_suggested[$i-$j+$n]=$cat_suggested2[$n];
            					$n++;
            				}
            				//PRIORITY 3: Category description contains $input_category (having topic_id)
            				if($i<100)
            				{
            					$select_category="SELECT category_title,category_id,popularity,t1.topic_id,t1.topic_name, t1.parent_topic_id, t2.topic_name as parent_topic_name,subject_name
            						FROM category
            						INNER JOIN topic as t1
            						ON category.topic_id=t1.topic_id
            						INNER JOIN subjects
            						ON t1.subject_id=subjects.subject_id
                            		LEFT JOIN topic as t2
                            		ON t1.parent_topic_id = t2.topic_id
            						WHERE cat_descr like '%$input_category%'
                            		AND category.topic_id='$topic_id'";	
            					$category_query=SunDataBaseManager::getSingleton()->QueryDB($select_category);
            					$j=0;
            					while ($row=mysql_fetch_array($category_query))
            					{
            						if($i>=100)
            						{
            							break;
            						}
            						$category_suggestion=$row['category_id'];
            						if(!(in_array($category_suggestion,$cat_suggested)))
            						{
            							$cat_suggested2[$j]=$category_suggestion;
										$popularities[$i]=$row['popularity'];
										$tpc_name[$i]=$row['topic_name'];
										$cat_suggested3[$i]=$row['category_id'];
										$cat_suggested1[$i]=$row['category_title'];
										$parent_topic_name[$i]=$row['parent_topic_name'];
            							$subject_name[$i]=$row['subject_name'];
										$t=0;
            							$flag=0;
            							while($t<$i)
            							{
            								if($popularities[$i]>$popularities[$t])
            								{
            									$flag=1;
            									break;
            								}
            								$t++;
            							}
            							if($flag==1)
            							{
            								$temp=$popularities[$i];
            								$temp2=$cat_suggested1[$i];
            								$temp3=$tpc_name[$i];
            								$temp4=$cat_suggested3[$i];
            								$temp5=$parent_topic_name[$i];
            								$temp6=$subject_name[$i];
            								$k=$i;
            								while ($k>=$t)
            								{
            									$popularities[($k+1)]=$popularities[$k];
            									$cat_suggested1[($k+1)]=$cat_suggested1[$k];
            									$tpc_name[($k+1)]=$tpc_name[$k];
            									$cat_suggested3[($k+1)]=$cat_suggested3[$k];
            									$parent_topic_name[($k+1)]=$parent_topic_name[$k];
            									$subject_name[($k+1)]=$subject_name[$k];
            									$k--;
            								}
            								$popularities[$t]=$temp;
            								$cat_suggested1[$t]=$temp2;
            								$tpc_name[$t]=$temp3;
            								$cat_suggested3[$t]=$temp4;
            								$parent_topic_name[$t]=$temp5;
            								$subject_name[$t]=$temp6;
            							}
            							$j++;
            							$i++;
            						}
            					}
            				}
            				$n=0;
            				while ($n<$j)
            				{
            					$cat_suggested[$i-$j+$n]=$cat_suggested2[$n];
            					$n++;
            				}
            			}
            			$x=0;
            			while ($x<$i)
            			{
            				$arr[SMC::$CATEGORYLIST][SMC::$CATEGORY][$x][SMC::$CATEGORYID]=$cat_suggested3[$x];
            				$arr[SMC::$CATEGORYLIST][SMC::$CATEGORY][$x][SMC::$CATEGORYTITLE]=$cat_suggested1[$x];
            				$arr[SMC::$CATEGORYLIST][SMC::$CATEGORY][$x][SMC::$SUBTOPICNAME]=$tpc_name[$x];
            				$arr[SMC::$CATEGORYLIST][SMC::$CATEGORY][$x][SMC::$TOPICNAME]=$parent_topic_name[$x];
            				$arr[SMC::$CATEGORYLIST][SMC::$CATEGORY][$x][SMC::$SUBJECTNAME]=$subject_name[$x];
            				$x++;
            			}
            			$i1=$i;
            			$i=0;
            			//PRIORITY 1: Category title starts from $input_category (not having topic_id)
            			if($i1<100)
            			{
            				$select_category="SELECT category_title,category_id,popularity,t1.topic_id,t1.topic_name, t1.parent_topic_id, t2.topic_name as parent_topic_name,subject_name
            						FROM category
            						INNER JOIN topic as t1
            						ON category.topic_id=t1.topic_id
            						INNER JOIN subjects
            						ON t1.subject_id=subjects.subject_id
                            		LEFT JOIN topic as t2
                            		ON t1.parent_topic_id = t2.topic_id
            						WHERE category_title like '$input_category%'";
            				$category_query=SunDataBaseManager::getSingleton()->QueryDB($select_category);
            				$j=0;
            				while ($row=mysql_fetch_array($category_query))
            				{
            					if($i>=100)
            					{
            						break;
            					}
            					$category_suggestion=$row['category_id'];
            					if(!(in_array($category_suggestion,$cat_suggested)))
            					{
            						$cat_suggested2[$j]=$category_suggestion;
									$popularities[$i]=$row['popularity'];
									$tpc_name[$i]=$row['topic_name'];
									$cat_suggested3[$i]=$row['category_id'];
									$cat_suggested1[$i]=$row['category_title'];
									$parent_topic_name[$i]=$row['parent_topic_name'];
            						$subject_name[$i]=$row['subject_name'];
									$t=0;
            						$flag=0;
            						while($t<$i)
            						{
            							if($popularities[$i]>$popularities[$t])
            							{
            								$flag=1;
            								break;
            							}
            							$t++;
            						}
            						if($flag==1)
            						{
            							$temp=$popularities[$i];
            							$temp2=$cat_suggested1[$i];
            							$temp3=$tpc_name[$i];
            							$temp4=$cat_suggested3[$i];
            							$temp5=$parent_topic_name[$i];
            							$temp6=$subject_name[$i];
            							$k=$i;
            							while ($k>=$t)
            							{
            								$popularities[($k+1)]=$popularities[$k];
            								$cat_suggested1[($k+1)]=$cat_suggested1[$k];
            								$tpc_name[($k+1)]=$tpc_name[$k];
            								$cat_suggested3[($k+1)]=$cat_suggested3[$k];
            								$parent_topic_name[($k+1)]=$parent_topic_name[$k];
            								$subject_name[($k+1)]=$subject_name[$k];
            								$k--;
            							}
            							$popularities[$t]=$temp;
            							$cat_suggested1[$t]=$temp2;
            							$tpc_name[$t]=$temp3;
            							$cat_suggested3[$t]=$temp4;
            							$parent_topic_name[$t]=$temp5;
            							$subject_name[$t]=$temp6;
            						}
            						$j++;
            						$i++;
            						$i1++;
            					}
            				}
            			}
            			$n=0;
            			while ($n<$j)
            			{
            				$cat_suggested[$i1-$j+$n]=$cat_suggested2[$n];
            				$n++;
            			}
            			//PRIORITY 2: Category title contains $input_category (not having topic_id)
            			if($i1<100)
            			{
            				$select_category="SELECT category_title,category_id,popularity,t1.topic_id,t1.topic_name, t1.parent_topic_id, t2.topic_name as parent_topic_name,subject_name
            						FROM category
            						INNER JOIN topic as t1
            						ON category.topic_id=t1.topic_id
            						INNER JOIN subjects
            						ON t1.subject_id=subjects.subject_id
                            		LEFT JOIN topic as t2
                            		ON t1.parent_topic_id = t2.topic_id
            						WHERE category_title like '%$input_category%'";
            				$category_query=SunDataBaseManager::getSingleton()->QueryDB($select_category);
            				$j=0;
            				while ($row=mysql_fetch_array($category_query))
            				{
            					if($i>=100)
            					{
            						break;
            					}
            					$category_suggestion=$row['category_id'];
            					if(!(in_array($category_suggestion,$cat_suggested)))
            					{
            						$cat_suggested2[$j]=$category_suggestion;
									$popularities[$i]=$row['popularity'];
									$tpc_name[$i]=$row['topic_name'];
									$cat_suggested3[$i]=$row['category_id'];
									$cat_suggested1[$i]=$row['category_title'];
									$parent_topic_name[$i]=$row['parent_topic_name'];
            						$subject_name[$i]=$row['subject_name'];
									$t=0;
            						$flag=0;
            						while($t<$i)
            						{
            							if($popularities[$i]>$popularities[$t])
            							{
            								$flag=1;
            								break;
            							}
            							$t++;
            						}
            						if($flag==1)
            						{
            							$temp=$popularities[$i];
            							$temp2=$cat_suggested1[$i];
            							$temp3=$tpc_name[$i];
            							$temp4=$cat_suggested3[$i];
            							$temp5=$parent_topic_name[$i];
            							$temp6=$subject_name[$i];
            							$k=$i;
            							while ($k>=$t)
            							{
            								$popularities[($k+1)]=$popularities[$k];
            								$cat_suggested1[($k+1)]=$cat_suggested1[$k];
            								$tpc_name[($k+1)]=$tpc_name[$k];
            								$cat_suggested3[($k+1)]=$cat_suggested3[$k];
            								$parent_topic_name[($k+1)]=$parent_topic_name[$k];
            								$subject_name[($k+1)]=$subject_name[$k];
            								$k--;
            							}
            							$popularities[$t]=$temp;
            							$cat_suggested1[$t]=$temp2;
            							$tpc_name[$t]=$temp3;
            							$cat_suggested3[$t]=$temp4;
            							$parent_topic_name[$t]=$temp5;
            							$subject_name[$t]=$temp6;
            						}
            						$j++;
            						$i++;
            						$i1++;
            					}
            				}
            			}
            			$n=0;
            			while ($n<$j)
            			{
            				$cat_suggested[$i1-$j+$n]=$cat_suggested2[$n];
            				$n++;
            			}
            			//PRIORITY 3: Category description contains $input_category (not having topic_id)
            			if($i1<100)
            			{
            				$select_category="SELECT category_title,category_id,popularity,t1.topic_id,t1.topic_name, t1.parent_topic_id, t2.topic_name as parent_topic_name,subject_name
            						FROM category
            						INNER JOIN topic as t1
            						ON category.topic_id=t1.topic_id
            						INNER JOIN subjects
            						ON t1.subject_id=subjects.subject_id
                            		LEFT JOIN topic as t2
                            		ON t1.parent_topic_id = t2.topic_id
            						WHERE cat_descr like '%$input_category%'";
            				$category_query=SunDataBaseManager::getSingleton()->QueryDB($select_category);
            				$j=0;
            				while ($row=mysql_fetch_array($category_query))
            				{
            					if($i>=100)
            					{
            						break;
            					}
            					$category_suggestion=$row['category_id'];
            					if(!(in_array($category_suggestion,$cat_suggested)))
            					{
            						$cat_suggested2[$j]=$category_suggestion;
									$popularities[$i]=$row['popularity'];
									$tpc_name[$i]=$row['topic_name'];
									$cat_suggested3[$i]=$row['category_id'];
									$cat_suggested1[$i]=$row['category_title'];
									$parent_topic_name[$i]=$row['parent_topic_name'];
            						$subject_name[$i]=$row['subject_name'];
									$t=0;
            						$flag=0;
            						while($t<$i)
            						{
            							if($popularities[$i]>$popularities[$t])
            							{
            								$flag=1;
            								break;
            							}
            							$t++;
            						}
            						if($flag==1)
            						{
            							$temp=$popularities[$i];
            							$temp2=$cat_suggested1[$i];
            							$temp3=$tpc_name[$i];
            							$temp4=$cat_suggested3[$i];
            							$temp5=$parent_topic_name[$i];
            							$temp6=$subject_name[$i];
            							$k=$i;
            							while ($k>=$t)
            							{
            								$popularities[($k+1)]=$popularities[$k];
            								$cat_suggested1[($k+1)]=$cat_suggested1[$k];
            								$tpc_name[($k+1)]=$tpc_name[$k];
            								$cat_suggested3[($k+1)]=$cat_suggested3[$k];
            								$parent_topic_name[($k+1)]=$parent_topic_name[$k];
            								$subject_name[($k+1)]=$subject_name[$k];
            								$k--;
            							}
            							$popularities[$t]=$temp;
            							$cat_suggested1[$t]=$temp2;
            							$tpc_name[$t]=$temp3;
            							$cat_suggested3[$t]=$temp4;
            							$parent_topic_name[$t]=$temp5;
            							$subject_name[$t]=$temp6;
            						}
            						$j++;
            						$i++;
            						$i1++;
            					}
            				}
            			}
            			$j=$x;
            			$x=0;
            			while ($x<$i)
            			{
            				$arr[SMC::$CATEGORYLIST][SMC::$CATEGORY][$j+$x][SMC::$CATEGORYID]=$cat_suggested3[$x];
            				$arr[SMC::$CATEGORYLIST][SMC::$CATEGORY][$j+$x][SMC::$CATEGORYTITLE]=$cat_suggested1[$x];
            				$arr[SMC::$CATEGORYLIST][SMC::$CATEGORY][$j+$x][SMC::$SUBTOPICNAME]=$tpc_name[$x];
            				$arr[SMC::$CATEGORYLIST][SMC::$CATEGORY][$j+$x][SMC::$TOPICNAME]=$parent_topic_name[$x];
            				$arr[SMC::$CATEGORYLIST][SMC::$CATEGORY][$j+$x][SMC::$SUBJECTNAME]=$subject_name[$x];
            				$x++;
            			}
            			/*$n=0;
            			while ($n<$i1) {
            			}*/
            		}
            		$retval = $this->mXMLManager->createXML($arr);
					return $retval;
            	}
            	catch (Exception $e)
            	{
            		echo "error";
            	}
            }                                                                                                                                                            
                                                                                                                         
			/*public function ($_id=null, $_=null)
				{
					try
						{
							$ = "";
							$ = SunDataBaseManager::getSingleton()->QueryDB($);
							$ = mysql_fetch_assoc($);
							$arr[SMC::$STATUS] = "Success";
							$arr[SMC::$] = "";
							$retval = $this->mXMLManager->createXML($arr);
							return $retval;
						}
					catch(Exception $e)
						{
							echo "error";
						}
				}*/
				public function GetIndex($index_type=null,$student_id=null,$class_id=null,$session_id=null,$topic_id=null,$user_id=null,$device_id=null,$uuid=null)
				{
					try
					{
						$arr[SMC::$STATUS]="Success";
						// class_id value specified
						if(strcasecmp($class_id, 'Agg_Lev=0') && strcasecmp($class_id, 'Agg_Lev=1'))
						{
							// session_id value specified
							if(strcasecmp($session_id, 'Agg_Lev=0') && strcasecmp($session_id, 'Agg_Lev=1'))
							{
								// topic_id value specified
								if(strcasecmp($topic_id, 'Agg_Lev=0') && strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									// student_id value specified
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										//GI
										if($index_type==1)
										{
											$index1="select substring((select (student_gi_num*100)/student_gi_den from stud_topic_time where student_id='$student_id' and topic_id='$topic_id' and class_session_id='$session_id'),1,(select locate('.',(select (student_gi_num*100)/student_gi_den from stud_topic_time where student_id='$student_id' and topic_id='$topic_id' and class_session_id='$session_id'))+2)) as grasp_index";
											$index_q1=SunDataBaseManager::getSingleton()->QueryDB($index1);
											$index_f1=mysql_fetch_assoc($index_q1);
											$index_v1=$index_f1['grasp_index'];
											if($index_v1==NULL)
											{
												$index_v1=0;
											}
											$arr[SMC::$GRASPINDEX]=$index_v1;
										}
										//PI
										else if($index_type==2)
										{
											$index2="select student_pi from stud_topic_time where student_id='$student_id' and topic_id='$topic_id' and class_session_id='$session_id'";
											$index_q2=SunDataBaseManager::getSingleton()->QueryDB($index2);
											$index_f2=mysql_fetch_assoc($index_q2);
											$index_v2=$index_f2['student_pi'];
											//echo $index_v2;
											$arr[SMC::$PARTICIPATIONINDEX]=$index_v2;
										}
									}
									//student_id has aggregation_level=0
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										$stud1="select student_id from stud_topic_time where class_session_id='$session_id' and topic_id='$topic_id'";
										$stud_q1=SunDataBaseManager::getSingleton()->QueryDB($stud1);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($stud_f1=mysql_fetch_array($stud_q1))
											{
												$stud_v1=$stud_f1['student_id'];
												$index3="select substring((select (student_gi_num*100)/student_gi_den from stud_topic_time where student_id='$stud_v1' and topic_id='$topic_id' and class_session_id='$session_id'),1,(select locate('.',(select (student_gi_num*100)/student_gi_den from stud_topic_time where student_id='$stud_v1' and topic_id='$topic_id' and class_session_id='$session_id'))+2)) as grasp_index";
												$index_q3=SunDataBaseManager::getSingleton()->QueryDB($index3);
												$index_f3=mysql_fetch_assoc($index_q3);
												$index_v3=$index_f3['grasp_index'];
												if($index_v3==NULL)
												{
													$index_v3=0;
												}
												$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID]=$stud_v1;
												$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$GRASPINDEX]=$index_v3;
												$i++;
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($stud_f1=mysql_fetch_array($stud_q1))
											{
												$stud_v1=$stud_f1['student_id'];
												$index4="select student_pi from stud_topic_time where student_id='$stud_v1' and topic_id='$topic_id' and class_session_id='$session_id'";
												$index_q4=SunDataBaseManager::getSingleton()->QueryDB($index4);
												$index_f4=mysql_fetch_assoc($index_q4);
												$index_v4=$index_f4['student_pi'];
												//echo "stud_id: ".$stud_v1." pi: ".$index_v4 ."<br>";
												$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID]=$stud_v1;
												$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$PARTICIPATIONINDEX]=$index_v4;
												$i++;
											}
										}
									}
									//student_id has aggregation_level=1
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										$stud2="select student_id from stud_topic_time where class_session_id='$session_id' and topic_id='$topic_id'";
										$stud_q2=SunDataBaseManager::getSingleton()->QueryDB($stud2);
										//GI
										if($index_type==1)
										{
											$num=0;
											$den=0;
											while ($stud_f2=mysql_fetch_array($stud_q2))
											{
												$stud_v2=$stud_f2['student_id'];
												$index5="select student_gi_num,student_gi_den from stud_topic_time where class_session_id='$session_id' and topic_id='$topic_id' and student_id='$stud_v2'";
												$index_q5=SunDataBaseManager::getSingleton()->QueryDB($index5);
												$index_f5=mysql_fetch_assoc($index_q5);
												$num+=$index_f5['student_gi_num'];
												$den+=$index_f5['student_gi_den'];
											}
											$index_v5=substr((($num*100)/$den),0,strpos((($num*100)/$den),'.')+3);
											$arr[SMC::$GRASPINDEX]=$index_v5;
										}
										//PI
										else if($index_type==2)
										{
											$num=0;
											while ($stud_f2=mysql_fetch_array($stud_q2))
											{
												$stud_v2=$stud_f2['student_id'];
												$index6="select student_pi from stud_topic_time where class_session_id='$session_id' and topic_id='$topic_id' and student_id='$stud_v2'";
												$index_q6=SunDataBaseManager::getSingleton()->QueryDB($index6);
												$index_f6=mysql_fetch_assoc($index_q6);
												$num+=$index_f6['student_pi'];
											}
											$index_v6=$num;
											$arr[SMC::$PARTICIPATIONINDEX]=$index_v6;
										}
									} //end of student_id possibilities
								}
								//topic_id has aggregation_level=0
								else if (!strcasecmp($topic_id, 'Agg_Lev=0'))
								{
									//student_id value specified
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										$topic1="select topic_id from stud_topic_time where student_id='$student_id' and class_session_id='$session_id'";
										$topic_q1=SunDataBaseManager::getSingleton()->QueryDB($topic1);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($topic_f1=mysql_fetch_array($topic_q1))
											{
												$topic_v1=$topic_f1['topic_id'];
												$index7="select substring((select (student_gi_num*100)/student_gi_den from stud_topic_time where student_id='$student_id' and topic_id='$topic_v1' and class_session_id='$session_id'),1,(select locate('.',(select (student_gi_num*100)/student_gi_den from stud_topic_time where student_id='$student_id' and topic_id='$topic_v1' and class_session_id='$session_id'))+2)) as grasp_index";
												$index_q7=SunDataBaseManager::getSingleton()->QueryDB($index7);
												$index_f7=mysql_fetch_assoc($index_q7);
												$index_v7=$index_f7['grasp_index'];
												if($index_v7==NULL)
												{
													$index_v7=0;
												}
												$arr[SMC::$TOPICS][SMC::$TOPIC][$i][SMC::$TOPICID]=$topic_v1;
												$arr[SMC::$TOPICS][SMC::$TOPIC][$i][SMC::$GRASPINDEX]=$index_v7;
												$i++;
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($topic_f1=mysql_fetch_array($topic_q1))
											{
												$topic_v1=$topic_f1['topic_id'];
												$index8="select student_pi from stud_topic_time where topic_id='$topic_v1' and student_id='$student_id' and class_session_id='$session_id'";
												$index_q8=SunDataBaseManager::getSingleton()->QueryDB($index8);
												$index_f8=mysql_fetch_assoc($index_q8);
												$index_v8=$index_f8['student_pi'];
												$arr[SMC::$TOPICS][SMC::$TOPIC][$i][SMC::$TOPICID]=$topic_v1;
												$arr[SMC::$TOPICS][SMC::$TOPIC][$i][SMC::$PARTICIPATIONINDEX]=$index_v8;
												$i++;
											}
										}
									}
									//student_id has aggregation_level=0
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										$topic2="select distinct topic_id from stud_topic_time where class_session_id='$session_id'";
										$topic_q2=SunDataBaseManager::getSingleton()->QueryDB($topic2);
										//GI
										if($index_type==1)
										{
											$j=0;
											while ($topic_f2=mysql_fetch_array($topic_q2))
											{
												$topic_v2=$topic_f2['topic_id'];
												$stud3="select student_id from stud_topic_time where topic_id='$topic_v2' and class_session_id='$session_id'";
												$stud_q3=SunDataBaseManager::getSingleton()->QueryDB($stud3);
												while($stud_f3=mysql_fetch_array($stud_q3))
												{
													$stud_v3=$stud_f3['student_id'];
													$index9="select substring((select (student_gi_num*100)/student_gi_den from stud_topic_time where student_id='$stud_v3' and topic_id='$topic_v2' and class_session_id='$session_id'),1,(select locate('.',(select (student_gi_num*100)/student_gi_den from stud_topic_time where student_id='$stud_v3' and topic_id='$topic_v2' and class_session_id='$session_id'))+2)) as grasp_index";
													$index_q9=SunDataBaseManager::getSingleton()->QueryDB($index9);
													$index_f9=mysql_fetch_assoc($index_q9);
													$index_v9=$index_f9['grasp_index'];
													if($index_v9==NULL)
													{
														$index_v9=0;
													}
													$arr[SMC::$TOPICS][SMC::$TOPIC][$j][SMC::$TOPICID]=$topic_v2;
													$arr[SMC::$TOPICS][SMC::$TOPIC][$j][SMC::$STUDENTID]=$stud_v3;
													$arr[SMC::$TOPICS][SMC::$TOPIC][$j][SMC::$GRASPINDEX]=$index_v9;
													$j++;
												}
											}
										}
										//PI
										else if($index_type==2)
										{
											$j=0;
											while ($topic_f2=mysql_fetch_array($topic_q2))
											{
												$topic_v2=$topic_f2['topic_id'];
												$stud3="select student_id,student_pi from stud_topic_time where topic_id='$topic_v2' and class_session_id='$session_id'";
												$stud_q3=SunDataBaseManager::getSingleton()->QueryDB($stud3);
												while($stud_f3=mysql_fetch_array($stud_q3))
												{
													$stud_v3=$stud_f3['student_id'];
													$index_v10=$stud_f3['student_pi'];
													$arr[SMC::$TOPICS][SMC::$TOPIC][$j][SMC::$TOPICID]=$topic_v2;
													$arr[SMC::$TOPICS][SMC::$TOPIC][$j][SMC::$STUDENTID]=$stud_v3;
													$arr[SMC::$TOPICS][SMC::$TOPIC][$j][SMC::$PARTICIPATIONINDEX]=$index_v10;
													$j++;
												}
											}
										}
									}
									//student_id has aggregation_level=1
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										$topic3="select distinct topic_id from stud_topic_time where class_session_id='$session_id'";
										$topic_q3=SunDataBaseManager::getSingleton()->QueryDB($topic3);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($topic_f3=mysql_fetch_array($topic_q3))
											{
												$topic_v3=$topic_f3['topic_id'];
												$stud4="select student_id,student_gi_num,student_gi_den from stud_topic_time where topic_id='$topic_v3' and class_session_id='$session_id'";
												$stud_q4=SunDataBaseManager::getSingleton()->QueryDB($stud4);
												$num=0;
												$den=0;
												while ($stud_f4=mysql_fetch_array($stud_q4))
												{
													$stud_v4=$stud_f4['student_id'];
													$num+=$stud_f4['student_gi_num'];
													$den+=$stud_f4['student_gi_den'];
												}
												if($den==0)
												{
													$index_v11=0;
												}
												else
												{
													$index_v11=substr((($num*100)/$den),0,strpos((($num*100)/$den),'.')+3);
												}
												$arr[SMC::$TOPICS][SMC::$TOPIC][$i][SMC::$TOPICID]=$topic_v3;
												$arr[SMC::$TOPICS][SMC::$TOPIC][$i][SMC::$GRASPINDEX]=$index_v11;
												$i++;
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($topic_f3=mysql_fetch_array($topic_q3))
											{
												$topic_v3=$topic_f3['topic_id'];
												$stud4="select student_id,student_pi from stud_topic_time where topic_id='$topic_v3' and class_session_id='$session_id'";
												$stud_q4=SunDataBaseManager::getSingleton()->QueryDB($stud4);
												$num=0;
												while ($stud_f4=mysql_fetch_array($stud_q4))
												{
													$stud_v4=$stud_f4['student_id'];
													$num+=$stud_f4['student_pi'];
												}
												$index_v12=$num;
												$arr[SMC::$TOPICS][SMC::$TOPIC][$i][SMC::$TOPICID]=$topic_v3;
												$arr[SMC::$TOPICS][SMC::$TOPIC][$i][SMC::$PARTICIPATIONINDEX]=$index_v12;
												$i++;
											}
										}
									}
								}
								//topic_id has aggregation_level=1
								else if (!strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									//student_id value specified
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										$topic4="select student_gi_num,student_gi_den,student_pi from stud_session_time where class_session_id='$session_id' and student_id='$student_id'";
										$topic_q4=SunDataBaseManager::getSingleton()->QueryDB($topic4);
										$topic_f4=mysql_fetch_assoc($topic_q4);
										if($index_type==1)
										{
											$index_v13=substr((($topic_f4['student_gi_num']*100)/$topic_f4['student_gi_den']),0,strpos((($topic_f4['student_gi_num']*100)/$topic_f4['student_gi_den']),'.')+3);
											if($index_v13==NULL)
											{
												$index_v13=0;
											}
											$arr[SMC::$GRASPINDEX]=$index_v13;
										}
										//PI
										else if($index_type==2)
										{
											$index_v14=$topic_f4['student_pi'];
											$arr[SMC::$PARTICIPATIONINDEX]=$index_v14;
										}
									}
									//student_id has aggregation_level=0
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										$stud5="select student_id,student_gi_num,student_gi_den,student_pi from stud_session_time where class_session_id='$session_id'";
										$stud_q5=SunDataBaseManager::getSingleton()->QueryDB($stud5);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($stud_f5=mysql_fetch_array($stud_q5))
											{
												$stud_v5=$stud_f5['student_id'];
												$index_v15=substr((($stud_f5['student_gi_num']*100)/$stud_f5['student_gi_den']),0,strpos((($stud_f5['student_gi_num']*100)/$stud_f5['student_gi_den']),'.')+3);
												if($index_v15==NULL)
												{
													$index_v15=0;
												}
												$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID]=$stud_v5;
												$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$GRASPINDEX]=$index_v15;
												$i++;
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($stud_f5=mysql_fetch_array($stud_q5))
											{
												$stud_v5=$stud_f5['student_id'];
												$index_v16=$stud_f5['student_pi'];
												$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$STUDENTID]=$stud_v5;
												$arr[SMC::$STUDENTS][SMC::$STUDENT][$i][SMC::$PARTICIPATIONINDEX]=$index_v16;
												$i++;
											}
										}
									}
									//student_id has aggregation_level=1
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										$stud6="select student_id,student_gi_num,student_gi_den,student_pi from stud_session_time where class_session_id='$session_id'";
										$stud_q6=SunDataBaseManager::getSingleton()->QueryDB($stud6);
										//GI
										if($index_type==1)
										{
											$num=0;
											$den=0;
											while ($stud_f6=mysql_fetch_array($stud_q6))
											{
												$stud_v6=$stud_f6['student_id'];
												$num+=$stud_f6['student_gi_num'];
												$den+=$stud_f6['student_gi_den'];
											}
											if($den==0)
											{
												$index_v17=0;
											}
											else
											{
												$index_v17=substr((($num*100)/$den),0,strpos((($num*100)/$den),'.')+3);
											}
											$arr[SMC::$GRASPINDEX]=$index_v17;
										}
										//PI
										else if($index_type==2)
										{
											$num=0;
											while ($stud_f6=mysql_fetch_array($stud_q6))
											{
												$stud_v6=$stud_f6['student_id'];
												$num+=$stud_f6['student_pi'];
											}
											$index_v18=$num;
											$arr[SMC::$PARTICIPATIONINDEX]=$index_v18;
										}
									}
								}
							}
							//session_id has aggregation_level=0
							else if (!strcasecmp($session_id, 'Agg_Lev=0'))
							{
								//topic_id value specified
								if(strcasecmp($topic_id, 'Agg_Lev=0') && strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									//student_id value specified
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										$ses1="select stud_topic_time.class_session_id from stud_topic_time inner join class_sessions on stud_topic_time.class_session_id=class_sessions.class_session_id where topic_id='$topic_id' and student_id='$student_id' and class_id='$class_id'";
										$ses_q1=SunDataBaseManager::getSingleton()->QueryDB($ses1);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($ses_f1=mysql_fetch_array($ses_q1))
											{
												$ses_v1=$ses_f1['class_session_id'];
												$index19="select substring((select (student_gi_num*100)/student_gi_den from stud_topic_time where student_id='$student_id' and topic_id='$topic_id' and class_session_id='$ses_v1'),1,(select locate('.',(select (student_gi_num*100)/student_gi_den from stud_topic_time where student_id='$student_id' and topic_id='$topic_id' and class_session_id='$ses_v1'))+2)) as grasp_index";
												$index_q19=SunDataBaseManager::getSingleton()->QueryDB($index19);
												$index_f19=mysql_fetch_assoc($index_q19);
												$index_v19=$index_f19['grasp_index'];
												if($index_v19==NULL)
												{
													$index_v19=0;
												}
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v1;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$GRASPINDEX]=$index_v19;
												$i++;
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($ses_f1=mysql_fetch_array($ses_q1))
											{
												$ses_v1=$ses_f1['class_session_id'];
												$index20="select student_pi from stud_topic_time where topic_id='$topic_id' and class_session_id='$ses_v1' and student_id='$student_id'";
												$index_q20=SunDataBaseManager::getSingleton()->QueryDB($index20);
												$index_f20=mysql_fetch_assoc($index_q20);
												$index_v20=$index_f20['student_pi'];
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v1;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$PARTICIPATIONINDEX]=$index_v20;
												$i++;
											}
										}
									}
									//student_id has aggregation_level=0
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										$ses2="select distinct class_session_id from stud_topic_time where topic_id='$topic_id'";
										$ses_q2=SunDataBaseManager::getSingleton()->QueryDB($ses2);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($ses_f2=mysql_fetch_array($ses_q2))
											{
												$ses_v2=$ses_f2['class_session_id'];
												$stud7="select student_id,student_gi_num,student_gi_den from stud_topic_time where class_session_id='$ses_v2' and topic_id='$topic_id'";
												$stud_q7=SunDataBaseManager::getSingleton()->QueryDB($stud7);
												while ($stud_f7=mysql_fetch_array($stud_q7))
												{
													$stud_v7=$stud_f7['student_id'];
													$index_v21=substr((($stud_f7['student_gi_num']*100)/$stud_f7['student_gi_den']),0,strpos((($stud_f7['student_gi_num']*100)/$stud_f7['student_gi_den']),'.')+3);
													if($index_v21==NULL)
													{
														$index_v21=0;
													}
													$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v2;
													$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$STUDENTID]=$stud_v7;
													$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$GRASPINDEX]=$index_v21;
													$i++;
												}
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($ses_f2=mysql_fetch_array($ses_q2))
											{
												$ses_v2=$ses_f2['class_session_id'];
												$stud7="select student_id,student_pi from stud_topic_time where class_session_id='$ses_v2' and topic_id='$topic_id'";
												$stud_q7=SunDataBaseManager::getSingleton()->QueryDB($stud7);
												while ($stud_f7=mysql_fetch_array($stud_q7))
												{
													$stud_v7=$stud_f7['student_id'];
													$index_v22=$stud_f7['student_pi'];
													$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v2;
													$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$STUDENTID]=$stud_v7;
													$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$PARTICIPATIONINDEX]=$index_v22;
													$i++;
												}
											}
										}
									}
									//student_id has aggregation_level=1
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										$ses3="select distinct class_session_id from stud_topic_time where topic_id='$topic_id'";
										$ses_q3=SunDataBaseManager::getSingleton()->QueryDB($ses3);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($ses_f3=mysql_fetch_array($ses_q3))
											{
												$ses_v3=$ses_f3['class_session_id'];
												$stud8="select student_id,student_gi_num,student_gi_den from stud_topic_time where topic_id='$topic_id' and class_session_id='$ses_v3'";
												$stud_q8=SunDataBaseManager::getSingleton()->QueryDB($stud8);
												$num=0;
												$den=0;
												while ($stud_f8=mysql_fetch_array($stud_q8))
												{
													$stud_v8=$stud_f8['student_id'];
													$num+=$stud_f8['student_gi_num'];
													$den+=$stud_f8['student_gi_den'];
												}
												if($den==0)
												{
													$index_v23=0;
												}
												else
												{
													$index_v23=substr((($num*100)/$den),0,strpos((($num*100)/$den),'.')+3);
												}
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v3;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$GRASPINDEX]=$index_v23;
												$i++;
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($ses_f3=mysql_fetch_array($ses_q3))
											{
												$ses_v3=$ses_f3['class_session_id'];
												$stud8="select student_id,student_pi from stud_topic_time where topic_id='$topic_id' and class_session_id='$ses_v3'";
												$stud_q8=SunDataBaseManager::getSingleton()->QueryDB($stud8);
												$num=0;
												while ($stud_f8=mysql_fetch_array($stud_q8))
												{
													$stud_v8=$stud_f8['student_id'];
													$num+=$stud_f8['student_pi'];
												}
												$index_v24=$num;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v3;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$PARTICIPATIONINDEX]=$index_v24;
												$i++;
											}
										}
									}
								}
								//topic_id has aggregation_level=0
								else if (!strcasecmp($topic_id, 'Agg_Lev=0'))
								{
									//student_id value specified
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										$stud9="select class_session_id,topic_id,student_gi_num,student_gi_den,student_pi from stud_topic_time where student_id='$student_id'";
										$stud_q9=SunDataBaseManager::getSingleton()->QueryDB($stud9);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($stud_f9=mysql_fetch_array($stud_q9))
											{
												$ses_v4=$stud_f9['class_session_id'];
												$topic_v4=$stud_f9['topic_id'];
												$index_v25=substr((($stud_f9['student_gi_num']*100)/$stud_f9['student_gi_den']),0,strpos((($stud_f9['student_gi_num']*100)/$stud_f9['student_gi_den']),'.')+3);
												if($index_v25==NULL)
												{
													$index_v25=0;
												}
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v4;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$TOPICID]=$topic_v4;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$GRASPINDEX]=$index_v25;
												$i++;
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($stud_f9=mysql_fetch_array($stud_q9))
											{
												$ses_v4=$stud_f9['class_session_id'];
												$topic_v4=$stud_f9['topic_id'];
												$index_v26=$stud_f9['student_pi'];
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v4;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$TOPICID]=$topic_v4;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$PARTICIPATIONINDEX]=$index_v26;
												$i++;
											}
										}
									}
									//student_id has aggregation_level=0
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										$ses5="select student_id,class_sessions.class_session_id,topic_id,student_gi_num,student_gi_den,student_pi from class_sessions inner join stud_topic_time on class_sessions.class_session_id=stud_topic_time.class_session_id where class_id='$class_id'";
										$ses_q5=SunDataBaseManager::getSingleton()->QueryDB($ses5);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($ses_f5=mysql_fetch_assoc($ses_q5))
											{
												$stud_v10=$ses_f5['student_id'];
												$ses_v5=$ses_f5['class_session_id'];
												$topic_v5=$ses_f5['topic_id'];
												$index_v27=substr((($ses_f5['student_gi_num']*100)/$ses_f5['student_gi_den']),0,strpos((($ses_f5['student_gi_num']*100)/$ses_f5['student_gi_den']),'.')+3);
												if($index_v27==NULL)
												{
													$index_v27=0;
												}
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v5;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$TOPICID]=$topic_v5;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$STUDENTID]=$stud_v10;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$GRASPINDEX]=$index_v27;
												$i++;
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($ses_f5=mysql_fetch_assoc($ses_q5))
											{
												$stud_v10=$ses_f5['student_id'];
												$ses_v5=$ses_f5['class_session_id'];
												$topic_v5=$ses_f5['topic_id'];
												$index_v28=$ses_f5['student_pi'];
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v5;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$TOPICID]=$topic_v5;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$STUDENTID]=$stud_v10;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$PARTICIPATIONINDEX]=$index_v28;
												$i++;
											}
										}
									}
									//student_id has aggregation_level=1
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										$ses6="select distinct topic_id,sum(student_gi_num) as student_gi_num,sum(student_gi_den) as student_gi_den,sum(student_pi) as student_pi, stud_topic_time.class_session_id from class_sessions inner join stud_topic_time on class_sessions.class_session_id=stud_topic_time.class_session_id where class_id='$class_id' GROUP by topic_id ,class_session_id order by stud_topic_time.class_session_id desc";
										$ses_q6=SunDataBaseManager::getSingleton()->QueryDB($ses6);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($ses_f6=mysql_fetch_array($ses_q6))
											{
												$ses_v6=$ses_f6['class_session_id'];
												$topic_v6=$ses_f6['topic_id'];
												$index_v29=substr((($ses_f6['student_gi_num']*100)/$ses_f6['student_gi_den']),0,strpos((($ses_f6['student_gi_num']*100)/$ses_f6['student_gi_den']),'.')+3);
												if($index_v29==NULL)
												{
													$index_v29=0;
												}
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v6;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$TOPICID]=$topic_v6;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$GRASPINDEX]=$index_v29;
												$i++;
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($ses_f6=mysql_fetch_array($ses_q6))
											{
												$ses_v6=$ses_f6['class_session_id'];
												$topic_v6=$ses_f6['topic_id'];
												$index_v30=$ses_f6['student_pi'];
												if($index_v30==NULL)
												{
													$index_v30=0;
												}
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v6;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$TOPICID]=$topic_v6;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$PARTICIPATIONINDEX]=$index_v30;
												$i++;
											}
										}
									}
								}
								//topic_id has aggregation_level=1
								else if (!strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									//student_id value specified
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										$ses7="select student_gi_num,student_gi_den,student_pi,class_session_id from stud_session_time where student_id='$student_id'";
										$ses_q7=SunDataBaseManager::getSingleton()->QueryDB($ses7);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($ses_f7=mysql_fetch_array($ses_q7))
											{
												$ses_v7=$ses_f7['class_session_id'];
												$index_v31=substr((($ses_f7['student_gi_num']*100)/$ses_f7['student_gi_den']),0,strpos((($ses_f7['student_gi_num']*100)/$ses_f7['student_gi_den']),'.')+3);
												if($index_v31==NULL)
												{
													$index_v31=0;
												}
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v7;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$GRASPINDEX]=$index_v31;
												$i++;
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($ses_f7=mysql_fetch_array($ses_q7))
											{
												$ses_v7=$ses_f7['class_session_id'];
												$index_v32=$ses_f7['student_pi'];
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v7;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$PARTICIPATIONINDEX]=$index_v32;
												$i++;
											}
										}
									}
									//student_id has aggregation_level=0
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										$stud11="select student_id,stud_session_time.class_session_id,student_gi_num,student_gi_den,student_pi from stud_session_time inner join class_sessions on stud_session_time.class_session_id=class_sessions.class_session_id where class_id='$class_id'";
										$stud_q11=SunDataBaseManager::getSingleton()->QueryDB($stud11);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($stud_f11=mysql_fetch_array($stud_q11))
											{
												$stud_v11=$stud_f11['student_id'];
												$ses_v8=$stud_f11['class_session_id'];
												$index_v33=substr((($stud_f11['student_gi_num']*100)/$stud_f11['student_gi_den']),0,strpos((($stud_f11['student_gi_num']*100)/$stud_f11['student_gi_den']),'.')+3);
												if($index_v33==NULL)
												{
													$index_v33=0;
												}
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v8;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$STUDENTID]=$stud_v11;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$GRASPINDEX]=$index_v33;
												$i++;
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($stud_f11=mysql_fetch_array($stud_q11))
											{
												$stud_v11=$stud_f11['student_id'];
												$ses_v8=$stud_f11['class_session_id'];
												$index_v34=$stud_f11['student_pi'];
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v8;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$STUDENTID]=$stud_v11;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$PARTICIPATIONINDEX]=$index_v34;
												$i++;
											}
										}
									}
									//student_id has aggregation_level=1
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										$ses9="select class_session_id,gi_num,gi_den,pi from class_sessions where class_id='$class_id'";
										$ses_q9=SunDataBaseManager::getSingleton()->QueryDB($ses9);
										//GI
										if($index_type==1)
										{
											$i=0;
											while ($ses_f9=mysql_fetch_array($ses_q9))
											{
												$ses_v9=$ses_f9['class_session_id'];
												$index_v35=substr((($ses_f9['gi_num']*100)/$ses_f9['gi_den']),0,strpos((($ses_f9['gi_num']*100)/$ses_f9['gi_den']),'.')+3);
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v9;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$GRASPINDEX]=$index_v35;
												$i++;
											}
										}
										//PI
										else if($index_type==2)
										{
											$i=0;
											while ($ses_f9=mysql_fetch_array($ses_q9))
											{
												$ses_v9=$ses_f9['class_session_id'];
												$index_v36=$ses_f9['pi'];
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID]=$ses_v9;
												$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$PARTICIPATIONINDEX]=$index_v36;
												$i++;
											}
										}
									}
								}
							}
							//session_id has aggregation_level=1
							else if (!strcasecmp($session_id, 'Agg_Lev=1'))
							{
								//topic_id value specified
								if(strcasecmp($topic_id, 'Agg_Lev=0') && strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									//student_id value specified
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										$ses10="select stud_topic_time.class_session_id,student_gi_num,student_gi_den,student_pi from stud_topic_time inner join class_sessions on stud_topic_time.class_session_id=class_sessions.class_session_id where student_id='$student_id' and topic_id='$topic_id' and class_id='$class_id'";
										$ses_q10=SunDataBaseManager::getSingleton()->QueryDB($ses10);
										//GI
										if($index_type==1)
										{
											$num=0;
											$den=0;
											$i=0;
											while ($ses_f10=mysql_fetch_array($ses_q10))
											{
												$num+=$ses_f10['student_gi_num'];
												$den+=$ses_f10['student_gi_den'];
											}
											if($den==0)
											{
												$index_v37=0;
											}
											else
											{
												$index_v37=substr((($num*100)/$den),0,strpos((($num*100)/$den),'.')+3);
											}
											$arr[SMC::$GRASPINDEX]=$index_v37;
										}
										//PI
										else if($index_type==2)
										{
											$num=0;
											while ($ses_f10=mysql_fetch_array($ses_q10))
											{
												$num+=$ses_f10['student_pi'];
											}
											$index_v38=$num;
											$arr[SMC::$PARTICIPATIONINDEX]=$index_v38;
										}
									}
									//student_id has aggregation_level=0
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=0'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
							}
						}
						elseif (!strcasecmp($class_id, 'Agg_Lev=0'))
						{
							if(strcasecmp($session_id, 'Agg_Lev=0') && strcasecmp($session_id, 'Agg_Lev=1'))
							{
								if(strcasecmp($topic_id, 'Agg_Lev=0') && strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=0'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
							}
							else if (!strcasecmp($session_id, 'Agg_Lev=0'))
							{
								if(strcasecmp($topic_id, 'Agg_Lev=0') && strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=0'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
							}
							else if (!strcasecmp($session_id, 'Agg_Lev=1'))
							{
								if(strcasecmp($topic_id, 'Agg_Lev=0') && strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=0'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{

									}
								}
							}
						}
						elseif (!strcasecmp($class_id, 'Agg_Lev=1'))
						{
							if(strcasecmp($session_id, 'Agg_Lev=0') && strcasecmp($session_id, 'Agg_Lev=1'))
							{
								if(strcasecmp($topic_id, 'Agg_Lev=0') && strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=0'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
							}
							else if (!strcasecmp($session_id, 'Agg_Lev=0'))
							{
								if(strcasecmp($topic_id, 'Agg_Lev=0') && strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=0'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
							}
							else if (!strcasecmp($session_id, 'Agg_Lev=1'))
							{
								if(strcasecmp($topic_id, 'Agg_Lev=0') && strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=0'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
								else if (!strcasecmp($topic_id, 'Agg_Lev=1'))
								{
									if(strcasecmp($student_id, 'Agg_Lev=0') && strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=0'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
									else if (!strcasecmp($student_id, 'Agg_Lev=1'))
									{
										if($index_type==1)
										{

										}
										else if($index_type==2)
										{
											
										}
									}
								}
							}
						}
						$retval = $this->mXMLManager->createXML($arr);
						return $retval;
					}
					catch(Exception $e)
					{
						echo "error";
					}
				}
				public function CreateCategory($category_title=null,$topic_id=null,$category_description=null,$user_id=null,$device_id=null,$uuid=null)
				{
					try
					{
						$current_time_select="select current_timestamp";
						$select_time_q=SunDataBaseManager::getSingleton()->QueryDB($current_time_select);
						$fetch_time=mysql_fetch_assoc($select_time_q);
						$curr_time=$fetch_time['current_timestamp'];
						$insert_cat="insert into category(topic_id,category_title,cat_descr,popularity,created_by,last_updated) values('$topic_id','$category_title','$category_description',0,$user_id,'$curr_time')";
						$cat_ins_q=SunDataBaseManager::getSingleton()->QueryDB($insert_cat);
						$cat_ins_id=SunDataBaseManager::getSingleton()->getLastInsertId();
						$arr[SMC::$STATUS]="Success";
						$arr[SMC::$CATEGORYID]=$cat_ins_id;
						$retval = $this->mXMLManager->createXML($arr);
						return $retval;
					}
					catch(Exception $e)
					{
						echo "error";
					}
				}
				public function SaveSuggestionState($suggestion_id=null,$suggestion_state=null,$user_id=null,$device_id=null,$uuid=null)
				{
					try
					{
						$count_ids=substr_count($suggestion_id, ';;;');
						if($count_ids>0)
						{
							$suggestion_ids=explode(';;;', $suggestion_id);
						}
						else
						{
							$suggestion_ids=$suggestion_id;
						}
						$count_states=substr_count($suggestion_state, ';;;');
						if($count_states>0)
						{
							$suggestion_states=explode(';;;', $suggestion_state);
						}
						else
						{
							$suggestion_states=$suggestion_state;
						}
						$current_time_select="select current_timestamp";
						$select_time_q=SunDataBaseManager::getSingleton()->QueryDB($current_time_select);
						$fetch_time=mysql_fetch_assoc($select_time_q);
						$curr_time=$fetch_time['current_timestamp'];
						//If multiple suggestion_states and suggestion_ids are passed
						if(is_array($suggestion_ids) && is_array($suggestion_states))
						{
							$i=0;
							// Incase the number of ids and states do not match, the loop will consider the array having lower number of values
							while ($i<((count($suggestion_ids)<count($suggestion_states)) ? count($suggestion_ids) : count($suggestion_states)))
							{
								$sugg_id=$suggestion_ids[$i];
								$sugg_state=$suggestion_states[$i];
								$old_state_q="select suggestion_state from suggestion_options where suggestion_id='$sugg_id'";
								$state_q1=SunDataBaseManager::getSingleton()->QueryDB($old_state_q);
								$state_f1=mysql_fetch_assoc($state_q1);
								$old_state_id=$state_f1['suggestion_state'];
								if($old_state_id!=$sugg_state)
								{
									$state_trans="insert into state_transitions(entity_type_id,entity_id,from_state,to_state,transition_time) values(7,'$sugg_id','$old_state_id','$sugg_state','$curr_time')";
									$ins_state_trans=SunDataBaseManager::getSingleton()->QueryDB($state_trans);
								}
								if($sugg_state==29)
								{
									$sugg_txt="select suggestion_txt,cat_id,student_id from suggestion_options where suggestion_id='$sugg_id'";
									$sugg_txt_q=SunDataBaseManager::getSingleton()->QueryDB($sugg_txt);
									$sugg_txt_f=mysql_fetch_assoc($sugg_txt_q);
									$suggestion_txt=$sugg_txt_f['suggestion_txt'];
									$category_id=$sugg_txt_f['cat_id'];
									$student_id=$sugg_txt_f['student_id'];
									$current_time_select="select current_timestamp";
									$select_time_q=SunDataBaseManager::getSingleton()->QueryDB($current_time_select);
									$fetch_time=mysql_fetch_assoc($select_time_q);
									$curr_time=$fetch_time['current_timestamp'];
									$ins_elem="insert into elements(element_text,cat_id,suggestion_id,popularity,contributed_by,last_updated) values('$suggestion_txt','$category_id','$sugg_id',0,'$student_id','$curr_time')";
									$ins_elem_q=SunDataBaseManager::getSingleton()->QueryDB($ins_elem);
									$rec_elem=SunDataBaseManager::getSingleton()->getLastInsertId();
									$arr[SMC::$SUGGESTIONSLIST][SMC::$SUGGESTIONS][$i][SMC::$SUGGESTIONID]=$sugg_id;
									$arr[SMC::$SUGGESTIONSLIST][SMC::$SUGGESTIONS][$i][SMC::$SUGGESTIONTEXT]=$suggestion_txt;
									$arr[SMC::$SUGGESTIONSLIST][SMC::$SUGGESTIONS][$i][SMC::$ELEMENTID]=$rec_elem;
									$arr[SMC::$SUGGESTIONSLIST][SMC::$SUGGESTIONS][$i][SMC::$ELEMENTFLAG]=1;
								}
								if($old_state_id==29 && $sugg_state==28)
								{
									$sel_elem="select element_id,element_text from elements where suggestion_id='$sugg_id'";
									$sel_elem_q=SunDataBaseManager::getSingleton()->QueryDB($sel_elem);
									$sel_elem_f=mysql_fetch_assoc($sel_elem_q);
									$sel_elem_v=$sel_elem_f['element_id'];
									$suggestion_txt=$sel_elem_f['element_text'];
									$del_elem="delete from elements where suggestion_id='$sugg_id'";
									$del_elem_q=SunDataBaseManager::getSingleton()->QueryDB($del_elem);
									$arr[SMC::$SUGGESTIONSLIST][SMC::$SUGGESTIONS][$i][SMC::$SUGGESTIONID]=$sugg_id;
									$arr[SMC::$SUGGESTIONSLIST][SMC::$SUGGESTIONS][$i][SMC::$SUGGESTIONTEXT]=$suggestion_txt;
									$arr[SMC::$SUGGESTIONSLIST][SMC::$SUGGESTIONS][$i][SMC::$ELEMENTID]=$sel_elem_v;
									$arr[SMC::$SUGGESTIONSLIST][SMC::$SUGGESTIONS][$i][SMC::$ELEMENTFLAG]=0;
								}
								$update_sugg_state="update suggestion_options set suggestion_state='$sugg_state' where suggestion_id='$sugg_id'";
								$state_q=SunDataBaseManager::getSingleton()->QueryDB($update_sugg_state);
								$i++;
							}
						}
						else if(!(is_array($suggestion_ids)) || !(is_array($suggestion_states)))
						{
							// If just one sugestion_id is passed and multiple state_ids are passed
							if(!(is_array($suggestion_ids)) && is_array($suggestion_states))
							{
								$sugg_state1=$suggestion_states[0];
								$old_state_q="select suggestion_state from suggestion_options where suggestion_id='$suggestion_ids'";
								$state_q1=SunDataBaseManager::getSingleton()->QueryDB($old_state_q);
								$state_f1=mysql_fetch_assoc($state_q1);
								$old_state_id=$state_f1['suggestion_state'];
								if($old_state_id!=$sugg_state1)
								{
									$state_trans="insert into state_transitions(entity_type_id,entity_id,from_state,to_state,transition_time) values(7,'$suggestion_ids','$old_state_id','$sugg_state1','$curr_time')";
									$ins_state_trans=SunDataBaseManager::getSingleton()->QueryDB($state_trans);	
								}
								if($sugg_state1==29)
								{
									$sugg_txt="select suggestion_txt,cat_id,student_id from suggestion_options where suggestion_id='$suggestion_ids'";
									$sugg_txt_q=SunDataBaseManager::getSingleton()->QueryDB($sugg_txt);
									$sugg_txt_f=mysql_fetch_assoc($sugg_txt_q);
									$suggestion_txt=$sugg_txt_f['suggestion_txt'];
									$category_id=$sugg_txt_f['cat_id'];
									$student_id=$sugg_txt_f['student_id'];
									$current_time_select="select current_timestamp";
									$select_time_q=SunDataBaseManager::getSingleton()->QueryDB($current_time_select);
									$fetch_time=mysql_fetch_assoc($select_time_q);
									$curr_time=$fetch_time['current_timestamp'];
									$ins_elem="insert into elements(element_text,cat_id,suggestion_id,popularity,contributed_by,last_updated) values('$suggestion_txt','$category_id','$suggestion_ids',0,'$student_id','$curr_time')";
									$ins_elem_q=SunDataBaseManager::getSingleton()->QueryDB($ins_elem);
									$rec_elem=SunDataBaseManager::getSingleton()->getLastInsertId();
									$arr[SMC::$SUGGESTIONID]=$suggestion_ids;
									$arr[SMC::$SUGGESTIONTEXT]=$suggestion_txt;
									$arr[SMC::$ELEMENTID]=$rec_elem;
									$arr[SMC::$ELEMENTFLAG]=1;
								}
								if($old_state_id==29 && $sugg_state1==28)
								{
									$sel_elem="select element_id,element_text from elements where suggestion_id='$sugg_id'";
									$sel_elem_q=SunDataBaseManager::getSingleton()->QueryDB($sel_elem);
									$sel_elem_f=mysql_fetch_assoc($sel_elem_q);
									$sel_elem_v=$sel_elem_f['element_id'];
									$suggestion_txt=$sel_elem_f['element_text'];
									$del_elem="delete from elements where suggestion_id='$suggestion_ids'";
									$del_elem_q=SunDataBaseManager::getSingleton()->QueryDB($del_elem);
									$arr[SMC::$SUGGESTIONID]=$suggestion_ids;
									$arr[SMC::$ELEMENTID]=$sel_elem_v;
									$arr[SMC::$SUGGESTIONTEXT]=$suggestion_txt;
									$arr[SMC::$ELEMENTFLAG]=0;
								}
								$update_sugg_state="update suggestion_options set suggestion_state='$sugg_state1' where suggestion_id='$suggestion_ids'";
								$state_q=SunDataBaseManager::getSingleton()->QueryDB($update_sugg_state);
							}
							// If just one sugestion_state is passed and multiple suggestion_ids are passed
							else if(is_array($suggestion_ids) && !(is_array($suggestion_states)))
							{
								$sugg_id1=$suggestion_ids[0];
								$old_state_q="select suggestion_state from suggestion_options where suggestion_id='$sugg_id1'";
								$state_q1=SunDataBaseManager::getSingleton()->QueryDB($old_state_q);
								$state_f1=mysql_fetch_assoc($state_q1);
								$old_state_id=$state_f1['suggestion_state'];
								if($old_state_id!=$suggestion_states)
								{
									$state_trans="insert into state_transitions(entity_type_id,entity_id,from_state,to_state,transition_time) values(7,'$sugg_id1','$old_state_id','$suggestion_states','$curr_time')";
									$ins_state_trans=SunDataBaseManager::getSingleton()->QueryDB($state_trans);
								}
								if($suggestion_states==29)
								{
									$sugg_txt="select suggestion_txt,cat_id,student_id from suggestion_options where suggestion_id='$sugg_id1'";
									$sugg_txt_q=SunDataBaseManager::getSingleton()->QueryDB($sugg_txt);
									$sugg_txt_f=mysql_fetch_assoc($sugg_txt_q);
									$suggestion_txt=$sugg_txt_f['suggestion_txt'];
									$category_id=$sugg_txt_f['cat_id'];
									$student_id=$sugg_txt_f['student_id'];
									$current_time_select="select current_timestamp";
									$select_time_q=SunDataBaseManager::getSingleton()->QueryDB($current_time_select);
									$fetch_time=mysql_fetch_assoc($select_time_q);
									$curr_time=$fetch_time['current_timestamp'];
									$ins_elem="insert into elements(element_text,cat_id,suggestion_id,popularity,contributed_by,last_updated) values('$suggestion_txt','$category_id','$sugg_id1',0,'$student_id','$curr_time')";
									$ins_elem_q=SunDataBaseManager::getSingleton()->QueryDB($ins_elem);
									$rec_elem=SunDataBaseManager::getSingleton()->getLastInsertId();
									$arr[SMC::$SUGGESTIONID]=$sugg_id1;
									$arr[SMC::$SUGGESTIONTEXT]=$suggestion_txt;
									$arr[SMC::$ELEMENTID]=$rec_elem;
									$arr[SMC::$ELEMENTFLAG]=1;
								}
								if($old_state_id==29 && $suggestion_states==28)
								{
									$sel_elem="select element_id,element_text from elements where suggestion_id='$sugg_id1'";
									$sel_elem_q=SunDataBaseManager::getSingleton()->QueryDB($sel_elem);
									$sel_elem_f=mysql_fetch_assoc($sel_elem_q);
									$sel_elem_v=$sel_elem_f['element_id'];
									$suggestion_txt=$sel_elem_f['element_text'];
									$del_elem="delete from elements where suggestion_id='$sugg_id1'";
									$del_elem_q=SunDataBaseManager::getSingleton()->QueryDB($del_elem);
									$arr[SMC::$SUGGESTIONID]=$sugg_id1;
									$arr[SMC::$ELEMENTID]=$sel_elem_v;
									$arr[SMC::$SUGGESTIONTEXT]=$suggestion_txt;
									$arr[SMC::$ELEMENTFLAG]=0;
								}
								$update_sugg_state="update suggestion_options set suggestion_state='$suggestion_states' where suggestion_id='$sugg_id1'";
								$state_q=SunDataBaseManager::getSingleton()->QueryDB($update_sugg_state);
							}
							// If just one suggestion_id and suggestion_state are passed
							else if(!(is_array($suggestion_ids)) && !(is_array($suggestion_states)))
							{
								$old_state_q="select suggestion_state from suggestion_options where suggestion_id='$suggestion_ids'";
								$state_q1=SunDataBaseManager::getSingleton()->QueryDB($old_state_q);
								$state_f1=mysql_fetch_assoc($state_q1);
								$old_state_id=$state_f1['suggestion_state'];
								if($old_state_id!=$suggestion_states)
								{
									$state_trans="insert into state_transitions(entity_type_id,entity_id,from_state,to_state,transition_time) values(7,'$suggestion_ids','$old_state_id','$suggestion_states','$curr_time')";
									$ins_state_trans=SunDataBaseManager::getSingleton()->QueryDB($state_trans);
								}
								if($suggestion_states==29)
								{
									$sugg_txt="select suggestion_txt,cat_id,student_id from suggestion_options where suggestion_id='$suggestion_ids'";
									$sugg_txt_q=SunDataBaseManager::getSingleton()->QueryDB($sugg_txt);
									$sugg_txt_f=mysql_fetch_assoc($sugg_txt_q);
									$suggestion_txt=$sugg_txt_f['suggestion_txt'];
									$category_id=$sugg_txt_f['cat_id'];
									$student_id=$sugg_txt_f['student_id'];
									$current_time_select="select current_timestamp";
									$select_time_q=SunDataBaseManager::getSingleton()->QueryDB($current_time_select);
									$fetch_time=mysql_fetch_assoc($select_time_q);
									$curr_time=$fetch_time['current_timestamp'];
									$ins_elem="insert into elements(element_text,cat_id,suggestion_id,popularity,contributed_by,last_updated) values('$suggestion_txt','$category_id','$suggestion_ids',0,'$student_id','$curr_time')";
									$ins_elem_q=SunDataBaseManager::getSingleton()->QueryDB($ins_elem);
									$rec_elem=SunDataBaseManager::getSingleton()->getLastInsertId();
									$arr[SMC::$SUGGESTIONID]=$suggestion_ids;
									$arr[SMC::$SUGGESTIONTEXT]=$suggestion_txt;
									$arr[SMC::$ELEMENTID]=$rec_elem;
									$arr[SMC::$ELEMENTFLAG]=1;
								}
								if($old_state_id==29 && $suggestion_states==28)
								{
									$sel_elem="select element_id,element_text from elements where suggestion_id='$suggestion_ids'";
									$sel_elem_q=SunDataBaseManager::getSingleton()->QueryDB($sel_elem);
									$sel_elem_f=mysql_fetch_assoc($sel_elem_q);
									$sel_elem_v=$sel_elem_f['element_id'];
									$suggestion_txt=$sel_elem_f['element_text'];
									$del_elem="delete from elements where suggestion_id='$suggestion_ids'";
									$del_elem_q=SunDataBaseManager::getSingleton()->QueryDB($del_elem);
									$arr[SMC::$SUGGESTIONID]=$suggestion_ids;
									$arr[SMC::$SUGGESTIONTEXT]=$suggestion_txt;
									$arr[SMC::$ELEMENTID]=$sel_elem_v;
									$arr[SMC::$ELEMENTFLAG]=0;
								}
								$update_sugg_state="update suggestion_options set suggestion_state='$suggestion_states' where suggestion_id='$suggestion_ids'";
								$state_q=SunDataBaseManager::getSingleton()->QueryDB($update_sugg_state);
							}
						}
						$arr[SMC::$STATUS]="Success";
						$retval = $this->mXMLManager->createXML($arr);
						return $retval;
					}
					catch(Exception $e)
					{
						echo "error";
					}
				}
				public function SelectCategory($category_id=null,$user_id=null,$device_id=null,$uuid=null)
				{
					try
					{
						$arr[SMC::$STATUS]="Success";
						$select_elems="select element_id,element_text,suggestion_id from elements where cat_id='$category_id'";
						$elems_q=SunDataBaseManager::getSingleton()->QueryDB($select_elems);
						$i=0;
						while ($row=mysql_fetch_array($elems_q))
						{
							$elem_id=$row['element_id'];
							$elem_text=$row['element_text'];
							$sugg_id=$row['suggestion_id'];
							$arr[SMC::$ELEMENTLIST][SMC::$ELEMENT][$i][SMC::$ELEMENTID]=$elem_id;
            				$arr[SMC::$ELEMENTLIST][SMC::$ELEMENT][$i][SMC::$ELEMENTTEXT]=$elem_text;
            				$arr[SMC::$ELEMENTLIST][SMC::$ELEMENT][$i][SMC::$SUGGESTIONID]=$sugg_id;
							$i++;
						}
						$retval = $this->mXMLManager->createXML($arr);
						return $retval;
					}
					catch (Exception $e)
					{
						echo "error";
					}
				}
				public function UpdateBehaviourIndex($session_id=null,$student_id=null,$transaction_id=null,$user_id=null,$device_id=null,$uuid=null)
				{
					try
					{
						$arr[SMC::$STATUS]="Success";
						$success=0;
						if($student_id==null)
						{
							//Select all live students attending this session
							$sel_stud="select user_id from tbl_auth inner join student_class_map on student_class_map.student_id=tbl_auth.user_id inner join class_sessions on class_sessions.class_id=student_class_map.class_id where user_state=1 and class_session_id='$session_id'";
							$sel_stud_q=SunDataBaseManager::getSingleton()->QueryDB($sel_stud);
							while ($row=mysql_fetch_array($sel_stud_q))
							{
								$student_id1=$row['user_id'];
								$sel_old_count="select subtotal_of_count from student_index where student_id='$student_id1' and class_session_id='$session_id' and transaction_id='$transaction_id' and topic_id is null";
								$old_count_q=SunDataBaseManager::getSingleton()->QueryDB($sel_old_count);
								$exists_old_c=SunDataBaseManager::getSingleton()->getnoOfrows($old_count_q);
								if($exists_old_c>0)
								{
									$old_c_f=mysql_fetch_assoc($old_count_q);
									$old_subtotal=$old_c_f['subtotal_of_count'];
									$old_subtotal++;
									$update_c="update student_index set subtotal_of_count='$old_subtotal' where student_id='$student_id1' and class_session_id='$session_id' and transaction_id='$transaction_id' and topic_id is null";
									$update_c_q=SunDataBaseManager::getSingleton()->QueryDB($update_c);
								}
								if($exists_old_c==0)
								{
									$current_time_select="select current_timestamp";
									$select_time_q=SunDataBaseManager::getSingleton()->QueryDB($current_time_select);
									$fetch_time=mysql_fetch_assoc($select_time_q);
									$curr_time=$fetch_time['current_timestamp'];
									$insert_trans="insert into student_index(class_session_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score,last_updated) values('$session_id','$transaction_id','$student_id1',1,0,'$curr_time')";
									$insert_c_q=SunDataBaseManager::getSingleton()->QueryDB($insert_trans);
								}
								if((!($update_c_q)) && (!($insert_c_q)))
								{
									$success=1;
								}
							}
							if($success!=0)
							{
								$arr[SMC::$STATUS]="Failure";
							}
						}
						else if($student_id!=null)
						{
							$count_stud=substr_count($student_id,';;;');
							if($count_stud>0)
							{
								$student_ids=explode(';;;', $student_id);
								$i=0;
								while ($i<count($student_ids))
								{
									$student_id1=$student_ids[$i];
									$sel_old_count="select subtotal_of_count from student_index where student_id='$student_id1' and class_session_id='$session_id' and transaction_id='$transaction_id' and topic_id is null";
									$old_count_q=SunDataBaseManager::getSingleton()->QueryDB($sel_old_count);
									$exists_old_c=SunDataBaseManager::getSingleton()->getnoOfrows($old_count_q);
									if($exists_old_c>0)
									{
										$old_c_f=mysql_fetch_assoc($old_count_q);
										$old_subtotal=$old_c_f['subtotal_of_count'];
										$old_subtotal++;
										$update_c="update student_index set subtotal_of_count='$old_subtotal' where student_id='$student_id1' and class_session_id='$session_id' and transaction_id='$transaction_id' and topic_id is null";
										$update_c_q=SunDataBaseManager::getSingleton()->QueryDB($update_c);
									}
									if($exists_old_c==0)
									{
										$current_time_select="select current_timestamp";
										$select_time_q=SunDataBaseManager::getSingleton()->QueryDB($current_time_select);
										$fetch_time=mysql_fetch_assoc($select_time_q);
										$curr_time=$fetch_time['current_timestamp'];
										$insert_trans="insert into student_index(class_session_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score,last_updated) values('$session_id','$transaction_id','$student_id1',1,0,'$curr_time')";
										$insert_c_q=SunDataBaseManager::getSingleton()->QueryDB($insert_trans);
									}
									if((!($update_c_q)) && (!($insert_c_q)))
									{
										$success=1;
									}
									$i++;
								}
								if($success!=0)
								{
									$arr[SMC::$STATUS]="Failure";
								}
							}
							else
							{
								$sel_old_count="select subtotal_of_count from student_index where student_id='$student_id' and class_session_id='$session_id' and transaction_id='$transaction_id' and topic_id is null";
								$old_count_q=SunDataBaseManager::getSingleton()->QueryDB($sel_old_count);
								$exists_old_c=SunDataBaseManager::getSingleton()->getnoOfrows($old_count_q);
								if($exists_old_c>0)
								{
									$old_c_f=mysql_fetch_assoc($old_count_q);
									$old_subtotal=$old_c_f['subtotal_of_count'];
									$old_subtotal++;
									$update_c="update student_index set subtotal_of_count='$old_subtotal' where student_id='$student_id' and class_session_id='$session_id' and transaction_id='$transaction_id' and topic_id is null";
									$update_c_q=SunDataBaseManager::getSingleton()->QueryDB($update_c);
								}
								if($exists_old_c==0)
								{
									$current_time_select="select current_timestamp";
									$select_time_q=SunDataBaseManager::getSingleton()->QueryDB($current_time_select);
									$fetch_time=mysql_fetch_assoc($select_time_q);
									$curr_time=$fetch_time['current_timestamp'];
									$insert_trans="insert into student_index(class_session_id,transaction_id,student_id,subtotal_of_count,subtotal_of_score,last_updated) values('$session_id','$transaction_id','$student_id',1,0,'$curr_time')";
									$insert_c_q=SunDataBaseManager::getSingleton()->QueryDB($insert_trans);
								}
								if((!($update_c_q)) && (!($insert_c_q)))
								{
									$arr[SMC::$STATUS]="Failure";
								}
							}
						}
						else
						{
							$arr[SMC::$STATUS]="Failure";
						}
						$retval = $this->mXMLManager->createXML($arr);
						return $retval;
					}
					catch (Exception $e)
					{
						echo "error";
					}
				}
				public function SaveMyLocation($latitude=null,$longitude=null,$user_id=null,$uuid=null)
				{
					try
					{
						$update_loc="update tbl_auth set latitude='$latitude',longitude='$longitude' where user_id='$user_id'";	
						$loc_q=SunDataBaseManager::getSingleton()->QueryDB($update_loc);
						if($loc_q)
						{
							$arr[SMC::$STATUS]="Success";
						}
						else
						{
							$arr[SMC::$STATUS]="Failure";
						}
						$retval = $this->mXMLManager->createXML($arr);
						return $retval;
					}
					catch (Exception $e)
					{
						echo "error";
					}
				}
				public function GetStudentsState($session_id=null,$user_id=null,$uuid=null)
				{
					try
					{
						$sel_states="select user_state,user_id from tbl_auth inner join student_class_map on tbl_auth.user_id=student_class_map.student_id inner join class_sessions on class_sessions.class_id=student_class_map.class_id where class_session_id='$session_id'";
						$states_q=SunDataBaseManager::getSingleton()->QueryDB($sel_states);
						if($states_q)
						{
							$arr[SMC::$STATUS]="Success";
						}
						else
						{
							$arr[SMC::$STATUS]="Failure";
						}
						$i=0;
						while ($row=mysql_fetch_array($states_q))
						{
							$arr[SMC::$STUDENTIDLIST][SMC::$STUDENTS][$i][SMC::$STUDENTID]=$row['user_id'];
							$arr[SMC::$STUDENTIDLIST][SMC::$STUDENTS][$i][SMC::$STUDENTSTATE]=$row['user_state'];
							$i++;
						}
						$retval = $this->mXMLManager->createXML($arr);
						return $retval;
					}
					catch (Exception $e)
					{
						echo "error";
					}
				}
		}
?>