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
										$seat_info = "select seat.seat_id, assign.seat_state from seats as seat 
										inner join class_sessions as session on session.room_id = seat.room_id 
										left join seat_assignments as assign on seat.seat_id = assign.seat_id and assign.class_session_id = '$session_id' 
										left join entity_states as state on session.session_state = state.state_id 
										where session.class_session_id = '$session_id'";
										
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

										$arr[SMC::$STATUS] = "Success";
										$arr[SMC::$SEATSCONFIGURED] = $configured_count;
										$arr[SMC::$STUDENTSREGISTERED] = $registered_count;
										$arr[SMC::$PREALLOCATEDSEATS] = $preallocated_count;
										$arr[SMC::$OCCUPIEDSEATS] = $occupied_count;
										$arr[SMC::$TOPICSCONFIGURED] = $tagged_count;
										
	}
					
					
					
					


public function GetMyTodaysSessions($user_id=null)
{
		try
				{
//if end time<now(), end it
$session_details5 = "select term.class_session_id 
		from class_sessions as term 
		inner join tbl_auth as user on term.teacher_id = user.user_id 
		inner join classes as class on term.teacher_id = class.teacher_id 
		inner join rooms as room on term.room_id = room.room_id 
		inner join subjects as subject on subject.subject_id = class.subject_id 
		where term.teacher_id = '$user_id' and term.ends_on < now() and term.session_state=1 AND term.class_id = class.class_id order by term.starts_on desc";
		
		
$details9=SunDataBaseManager::getSingleton()->QueryDB($session_details5);
$count7 = SunDataBaseManager::getSingleton()->getnoOfrows($details9);
if($count7 > 0)
{
	while($details7 = mysql_fetch_assoc($details9))
	{
		$sess= $details7['class_session_id'];
		$update_session_state = "update class_sessions set session_state = '5' where class_session_id = '$sess'";
		$updated_session_state = SunDataBaseManager::getSingleton()->QueryDB($update_session_state);
		if($updated_session_state)
			{
					$log_session_transition = "insert into state_transitions(entity_type_id, entity_id, from_state, to_state) values('2','$sess','1','5')";
					$logged_session_transition = SunDataBaseManager::getSingleton()->QueryDB($log_session_transition);
			}
	}
}
				  //older sessions to cancelled
$session_details = "select term.class_session_id, term.starts_on, term.ends_on, state.state_id, user.first_name, user.user_id, class.class_id, class.class_name,
room.room_name, room.room_id, subject.subject_id, subject.subject_name from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id inner join classes as class on term.teacher_id = class.teacher_id inner join rooms as room on term.room_id = room.room_id inner join subjects as subject on subject.subject_id = class.subject_id inner join entity_states as state on term.session_state = state.state_id where term.teacher_id = '$user_id' and date(term.starts_on) < curdate() AND term.class_id = class.class_id";
$details=SunDataBaseManager::getSingleton()->QueryDB($session_details);
			$count = SunDataBaseManager::getSingleton()->getnoOfrows($details);
			if($count>0)
					{
							$arr[SMC::$STATUS] = "Success";
							$i = 0;
							while($detail = mysql_fetch_assoc($details))
									{
$new_state = $detail['state_id'];
$session_id = $detail['class_session_id'];
//change state to cancelled
//change state to cancelled
   if(($detail['state_id'] =="4") || ($detail['state_id'] =="2"))
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
		}
}
//today's tobe cancelled'
						$session_details = "select term.class_session_id, term.starts_on, term.ends_on, state.state_id, user.first_name, user.user_id, class.class_id, class.class_name, room.room_name, room.room_id, subject.subject_id, subject.subject_name from class_sessions as term inner join tbl_auth as user on term.teacher_id = user.user_id inner join classes as class on term.teacher_id = class.teacher_id inner join rooms as room on term.room_id = room.room_id inner join subjects as subject on subject.subject_id = class.subject_id inner join entity_states as state on term.session_state = state.state_id where term.teacher_id = '$user_id' and date(term.starts_on) = curdate() AND term.class_id = class.class_id";
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
														$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$SESSIONID] = $detail['class_session_id'];
														$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$STARTTIME] = $this->mXMLManager->ReturnTimeOffset($detail['starts_on']);
														$arr[SMC::$SESSIONS][SMC::$SESSION][$i][SMC::$ENDTIME] = $this->mXMLManager->ReturnTimeOffset($detail['ends_on']);
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