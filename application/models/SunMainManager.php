<?php
require_once 'SunManagerConst.php';
require_once 'SunXMLManager.php';
require_once 'commonAPI.php';

class SunMainManager
{
	private $mWorker;
	private $mXMLManager;

	public function __construct()
  	{
    		$this->mXMLManager = new SunXMLManager();
		$this->mWorker = new commonAPI();
  	}
  
  	public function Process($getdata,$postdata)
  	{

		//Check and assign the xmldata variable to POST or GET request data.
		if($postdata != null)
			$xmldata = $postdata;
		else
			$xmldata = $getdata;
		//Convert the XML into a multidimensional array using the parseXML function which is defined in SunXMLManager.php file.                    
		$array = $this->mXMLManager->parseXML($xmldata);
		//Here the for loop goes through the array and matches the array value with the pre-defined service requests(all of them are listed in sunManagerConst.php file) and runs the required service request function listed in the commonAPI.php file.
 		for($i=0;$i<sizeof($array);$i++) {
			while (list($key, $value) = each($array[$i])) {
				
				if($key==SMC::$SERVICE)
				{
        				$service_name=$array[$i][SMC::$SERVICE];
        				$user_id=$array[$i][SMC::$USERID];
        				$uuid=$array[$i][SMC::$UUID];
						$input_time="select current_timestamp";
						$input_time_query=SunDataBaseManager::getSingleton()->QueryDB($input_time);
						$input_time_fetch=mysql_fetch_assoc($input_time_query);
						$xml_input_time=$input_time_fetch['current_timestamp'];
        				$insert_input="insert into event_log(service_name,user_id,UUID,xml_input,request_time) values('$service_name','$user_id','$uuid','$xmldata','$xml_input_time')";
						$input_query=SunDataBaseManager::getSingleton()->QueryDB($insert_input);
        				$e_id=SunDataBaseManager::getSingleton()->getLastInsertId();
			  		switch ($value) {
			  			case SMC::$SERVICE_LOGIN:
							$result = $this->mWorker->Login($array[$i][SMC::$USERNAME], $array[$i][SMC::$PASSWORD],$array[$i][SMC::$APPVERSION],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID],$array[$i][SMC::$APPID],$array[$i][SMC::$LATITUDE],$array[$i][SMC::$LONGITUDE]);
							break;

			  			case SMC::$SERVICE_GETMYINFO :
							$result = $this->mWorker->GetMyInfo($array[$i][SMC::$USERID],$array[$i][SMC::$UUID]);
							break;

						case SMC::$SERVICE_LOGOUT:
							$result = $this->mWorker->Logout($array[$i][SMC::$USERID],$array[$i][SMC::$UUID]);
							break;
							
						case SMC::$SERVICE_GETMYCURRENTSESSION:
							$result = $this->mWorker->GetMyCurrentSession($array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
							
						case SMC::$SERVICE_GETMYNEXTSESSION:
							$result = $this->mWorker->GetMyNextSession($array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_GETMYTODAYSSESSIONS:
							$result = $this->mWorker->GetMyTodaysSessions($array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_UPDATESESSIONSTATE:
							$result = $this->mWorker->UpdateSessionState($array[$i][SMC::$SESSIONID],$array[$i][SMC::$STATUSID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_CONFIGUREGRID:
							$result = $this->mWorker->ConfigureGrid($array[$i][SMC::$ROOMID], $array[$i][SMC::$ROWS], $array[$i][SMC::$COLUMNS], $array[$i][SMC::$SEATSREMOVED],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_GETSTUDENTSSESSIONINFO:
							$result = $this->mWorker->GetStudentsSessionInfo($array[$i][SMC::$SESSIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_RETRIEVESTUDENTQUERY :
							$result = $this->mWorker->RetrieveStudentQuery($array[$i][SMC::$QUERYID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_STUDENTSEATASSIGNMENT:
							$result = $this->mWorker->StudentSeatAssignment($array[$i][SMC::$SESSIONID],$array[$i][SMC::$SEATIDLIST],$array[$i][SMC::$STUDENTIDLIST],$array[$i][SMC::$STATUSID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_RETRIEVEGRIDDESIGN :
							$result = $this->mWorker->RetrieveGridDesign($array[$i][SMC::$ROOMID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_RETRIEVESEATASSIGNMENTS :
							$result = $this->mWorker->RetrieveSeatAssignments($array[$i][SMC::$SESSIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_CLASSSESSIONSUMMARY :
							$result = $this->mWorker->ClassSessionSummary($array[$i][SMC::$SESSIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
                            
						case SMC::$SERVICE_GETAllSTUDENTSTATES :
							$result = $this->mWorker->GetAllStudentStates($array[$i][SMC::$SESSIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
                            
						case SMC::$SERVICE_NEWXMPP :
							$result = $this->mWorker->NewXMPP($array[$i][SMC::$MESSAGETYPE],$array[$i][SMC::$MESSAGECONTENT],$array[$i][SMC::$SENDERID],$array[$i][SMC::$RECEIVERID]); 
							break;
                                                        
						case SMC::$SERVICE_RESETSEATASSIGNMENT:
							$result = $this->mWorker->ResetSeatAssignment($array[$i][SMC::$SESSIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_EXTENDSESSIONTIME:
							$result = $this->mWorker->ExtendSessionTime($array[$i][SMC::$SESSIONID],$array[$i][SMC::$MINUTESEXTENDED],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_GETALLNODES:
							$result = $this->mWorker->GetAllNodes($array[$i][SMC::$CLASSID], $array[$i][SMC::$SUBJECTID],$array[$i][SMC::$TOPICID],$array[$i][SMC::$TYPE],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_RETRIEVESTUDENTANSWER:
							$result = $this->mWorker->RetrieveStudentAnswer($array[$i][SMC::$ASSESSMENTANSWERID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_BROADCASTQUESTION:
							$result = $this->mWorker->BroadcastQuestion($array[$i][SMC::$SESSIONID], $array[$i][SMC::$QUESTIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_SETCURRENTTOPIC:
							$result = $this->mWorker->SetCurrentTopic($array[$i][SMC::$SESSIONID], $array[$i][SMC::$TOPICID],$array[$i][SMC::$STUDENTID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_STOPTOPIC:
							$result = $this->mWorker->StopTopic($array[$i][SMC::$SESSIONID], $array[$i][SMC::$TOPICID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_SETCURRENTQUESTION:
							$result = $this->mWorker->SetCurrentQuestion($array[$i][SMC::$SESSIONID], $array[$i][SMC::$QUESTIONID]); 
							break;

						case SMC::$SERVICE_OPENQUERYFORVOTING:
							$result = $this->mWorker->OpenQueryForVoting($array[$i][SMC::$QUERYID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_CLOSEQUESTIONRESPONSE:
							$result = $this->mWorker->CloseQuestionResponse($array[$i][SMC::$QUESTIONLOGID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_ENDQUESTIONINSTANCE:
							$result = $this->mWorker->EndQuestionInstance($array[$i][SMC::$QUESTIONLOGID],$array[$i][SMC::$TOPICID],$array[$i][SMC::$STUDENTID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_APPROVEVOLUNTEER:
							$result = $this->mWorker->ApproveVolunteer($array[$i][SMC::$VOLUNTEERID],$array[$i][SMC::$STOPPEDFLAG],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_RETRIEVEVOLUNTEERVOTES:
							$result = $this->mWorker->RetrieveVolunteerVotes($array[$i][SMC::$VOLUNTEERID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_RECORDLESSONPLAN:
							$result = $this->mWorker->RecordLessonPlan($array[$i][SMC::$CLASSID],$array[$i][SMC::$TEACHERID],$array[$i][SMC::$TOPICIDLIST],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_RECORDMODELANSWER:
							$result = $this->mWorker->RecordModelAnswer($array[$i][SMC::$ASSESSMENTANSWERID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_UPLOADTEACHERSCRIBBLE:
							$result = $this->mWorker->UploadTeacherScribble($array[$i][SMC::$IMAGEPATH],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_RECORDQUESTION:
							$result = $this->mWorker->RecordQuestion($array[$i][SMC::$SESSIONID],$array[$i][SMC::$QUESTIONTYPE],$array[$i][SMC::$TOPICID],$array[$i][SMC::$TEACHERID],$array[$i][SMC::$SCRIBBLEID],$array[$i][SMC::$QUESTIONTITLE],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_GETGRASPINDEX:
							$result = $this->mWorker->GetGraspIndex($array[$i][SMC::$SESSIONID],$array[$i][SMC::$STUDENTID],$array[$i][SMC::$TOPICID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_GETALLSTUDENTINDEX:
							$result = $this->mWorker->GetAllStudentIndex($array[$i][SMC::$SESSIONID],$array[$i][SMC::$TOPICID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
                            
						case SMC::$SERVICE_UPDATERECORDEDQUESTION:
							$result = $this->mWorker->UpdateRecordedQuestion($array[$i][SMC::$QUESTIONID],$array[$i][SMC::$QUESTIONTITLE],$array[$i][SMC::$ELEMENTID],$array[$i][SMC::$ISANSWER],$array[$i][SMC::$COLUMN],$array[$i][SMC::$SEQUENCE],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_RESPONDTOQUERY:
							$result = $this->mWorker->RespondToQuery($array[$i][SMC::$QUERYID], $array[$i][SMC::$TEACHERREPLYTEXT], $array[$i][SMC::$BADGEID], $array[$i][SMC::$DISMISSFLAG],$array[$i][SMC::$STUDENTID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_MUTESTUDENT:
							$result = $this->mWorker->MuteStudent($array[$i][SMC::$STUDENTID], $array[$i][SMC::$MUTESTATUS],$array[$i][SMC::$SESSIONID],$array[$i][SMC::$TOPICID]); 
							break;

						case SMC::$SERVICE_SAVESELECTEDQUERIES:
							$result = $this->mWorker->SaveSelectedQueries($array[$i][SMC::$QUERYIDLIST], $array[$i][SMC::$ALLOWVOLUNTEERFLAG],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_SAVEVOLUNTEERFEEDBACK:
							$result = $this->mWorker->SaveVolunteerFeedback($array[$i][SMC::$VOLUNTEERID],$array[$i][SMC::$RATING],$array[$i][SMC::$BADGEID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;

						case SMC::$SERVICE_SENDFEEDBACK:
							$result = $this->mWorker->SendFeedback($array[$i][SMC::$ASSESSMENTANSWERIDLIST], $array[$i][SMC::$TEACHERID], $array[$i][SMC::$URL], $array[$i][SMC::$RATING], $array[$i][SMC::$TEXTRATING], $array[$i][SMC::$BADGEID],$array[$i][SMC::$STUDENTID],$array[$i][SMC::$MODELANSWERFLAG],$array[$i][SMC::$UUID]); 
							break;
							
						case SMC::$SERVICE_GETVOLUNTEERLIST:
							$result = $this->mWorker->GetVolunteerList($array[$i][SMC::$QUERYID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
					    case SMC::$SERVICE_UPDATEUSERSTATE:
							$result = $this->mWorker->UpdateUserState($array[$i][SMC::$USERID],$array[$i][SMC::$STATUSID],$array[$i][SMC::$SESSIONID],$array[$i][SMC::$UUID]); 
							break;
                            
					    case SMC::$SERVICE_GETSESSIONINFO:
							$result = $this->mWorker->GetSessionInfo($array[$i][SMC::$SESSIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;                            
                        case SMC::$SERVICE_FETCHQUESTION:
							$result = $this->mWorker->FetchQuestion($array[$i][SMC::$QUESTIONID],$array[$i][SMC::$QUESTIONLOGID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
                        case SMC::$SERVICE_SENDANSWER:
							$result = $this->mWorker->SendAnswer($array[$i][SMC::$IMAGEPATH],$array[$i][SMC::$STUDENTID],$array[$i][SMC::$TEXTANSWER],$array[$i][SMC::$OPTIONTEXT], $array[$i][SMC::$SEQUENCE],$array[$i][SMC::$COLUMN],$array[$i][SMC::$SESSIONID],$array[$i][SMC::$QUESTIONLOGID],$array[$i][SMC::$QUESTIONTYPE]); 
							break;                            
                        case SMC::$SERVICE_GETFEEDBACK:
							$result = $this->mWorker->GetFeedback($array[$i][SMC::$ASSESSMENTANSWERID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
                        case SMC::$SERVICE_RETRIEVEBADGES:
							$result = $this->mWorker->RetrieveBadges($array[$i][SMC::$USERID],$array[$i][SMC::$UUID]);
							break;    
                            
						case SMC::$SERVICE_GETTHISSTUDENTSESSIONS:
							$result = $this->mWorker->GetThisStudentSessions($array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;                                                     

						case SMC::$SERVICE_GETMYSTATE:
							$result = $this->mWorker->GetMyState($array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;  
                            
  						case SMC::$SERVICE_SAVESTUDENTQUERY:
							$result = $this->mWorker->SaveStudentQuery($array[$i][SMC::$STUDENTID],$array[$i][SMC::$SESSIONID],$array[$i][SMC::$QUERYTEXT],$array[$i][SMC::$ANONYMOUS]); 
							break;    
                                                    
						case SMC::$SERVICE_GETQUERYRESPONSE:
							$result = $this->mWorker->GetQueryResponse($array[$i][SMC::$QUERYID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;  
                           
 						case SMC::$SERVICE_SAVEMETOOVOTES:
							$result = $this->mWorker->SaveMeTooVotes($array[$i][SMC::$QUERYID],$array[$i][SMC::$STUDENTID]); 
							break;                           
                            
 						case SMC::$SERVICE_FETCHSRQ:
							$result = $this->mWorker->FetchSRQ($array[$i][SMC::$SESSIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;     
                            
 						case SMC::$SERVICE_VOLUNTEERREGISTER:
							$result = $this->mWorker->VolunteerRegister($array[$i][SMC::$STUDENTID],$array[$i][SMC::$QUERYID]); 
							break;      
                                                    
   						case SMC::$SERVICE_GETVOLUNTEERSELECTED:
							$result = $this->mWorker->GetVolunteerSelected($array[$i][SMC::$SESSIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;    
   						case SMC::$SERVICE_WITHDRAWSTUDENTSUBMISSION:
							$result = $this->mWorker->WithdrawStudentSubmission($array[$i][SMC::$ASSESSMENTANSWERID],$array[$i][SMC::$QUERYID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;    
   						case SMC::$SERVICE_RETRIEVEAGGREGATEDRILLDOWN:
							$result = $this->mWorker->RetrieveAggregateDrillDown($array[$i][SMC::$QUESTIONLOGID],$array[$i][SMC::$OPTIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;  
   						case SMC::$SERVICE_GETALLROOMS:
							$result = $this->mWorker->GetAllRooms($array[$i][SMC::$SCHOOLID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;     
   						case SMC::$SERVICE_GETMODELANSWER:
							$result = $this->mWorker->GetModelAnswer($array[$i][SMC::$QUESTIONLOGID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;          
   						case SMC::$SERVICE_GETALLMODELANSWERS:
							$result = $this->mWorker->GetAllModelAnswers($array[$i][SMC::$QUESTIONLOGID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;          
   						case SMC::$SERVICE_ENDVOLUNTEERINGSESSION:
							$result = $this->mWorker->EndVolunteeringSession($array[$i][SMC::$SESSIONID],$array[$i][SMC::$QUERYIDLIST],$array[$i][SMC::$METOOLIST],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;    
   						case SMC::$SERVICE_STOPVOLUNTEERING:
							$result = $this->mWorker->StopVolunteering($array[$i][SMC::$VOLUNTEERID],$array[$i][SMC::$THUMBSUPVOTES],$array[$i][SMC::$THUMBSDOWNVOTES],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;                                                                                                                                                                                                
   						case SMC::$SERVICE_SENDSIGNUPINFO:
							$result = $this->mWorker->SendSignupInfo($array[$i][SMC::$DEVICEID],$array[$i][SMC::$INSTITUTIONNAME],$array[$i][SMC::$EMAILID],$array[$i][SMC::$CONTACTPERSON],$array[$i][SMC::$PHONENO],$array[$i][SMC::$IPADFLAG],$array[$i][SMC::$ADDRESS],$array[$i][SMC::$CITY],$array[$i][SMC::$LONGITUDE],$array[$i][SMC::$LATITUDE],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;    
   						case SMC::$SERVICE_GETMAXSTUDENTSREGISTERED:
							$result = $this->mWorker->GetMaxStudentsRegistered($array[$i][SMC::$ROOMID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;  
   						case SMC::$SERVICE_GETSTATE:
							$result = $this->mWorker->GetState($array[$i][SMC::$ENTITYTYPE],$array[$i][SMC::$ENTITYID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;  
   						case SMC::$SERVICE_GETQUESTIONDETAILS:
							$result = $this->mWorker->GetQuestionDetails($array[$i][SMC::$QUESTIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break; 
   						case SMC::$SERVICE_GETPARTICIPATIONINDEX:
							$result = $this->mWorker->GetParticipationIndex($array[$i][SMC::$SESSIONID],$array[$i][SMC::$STUDENTID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;   
   						case SMC::$SERVICE_RECOVERFROMCRASH:
							$result = $this->mWorker->RecoverFromCrash($array[$i][SMC::$SESSIONID],$array[$i][SMC::$TYPE],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;      
   						case SMC::$SERVICE_RECORDSUGGESTION:
							$result = $this->mWorker->RecordSuggestion($array[$i][SMC::$STUDENTID],$array[$i][SMC::$SUGGESTTXT],$array[$i][SMC::$CATEGORYID],$array[$i][SMC::$SESSIONID],$array[$i][SMC::$TOPICID],$array[$i][SMC::$UUID]); 
							break;   
   						case SMC::$SERVICE_CLEARQUESTION:
							$result = $this->mWorker->ClearQuestion($array[$i][SMC::$QUESTIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;     
						case SMC::$SERVICE_FETCHCATEGORY:
							$result = $this->mWorker->FetchCategory($array[$i][SMC::$INPUTCATEGORY],$array[$i][SMC::$TOPICID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
						case SMC::$SERVICE_GETINDEX:
							$result = $this->mWorker->GetIndex($array[$i][SMC::$INDEXTYPE],$array[$i][SMC::$STUDENTID],$array[$i][SMC::$CLASSID],$array[$i][SMC::$SESSIONID],$array[$i][SMC::$TOPICID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
						case SMC::$SERVICE_CREATECATEGORY:
							$result = $this->mWorker->CreateCategory($array[$i][SMC::$CATEGORYTITLE],$array[$i][SMC::$TOPICID],$array[$i][SMC::$CATEGORYDESCRIPTION],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
						case SMC::$SERVICE_SAVESUGGESTIONSTATE:
							$result = $this->mWorker->SaveSuggestionState($array[$i][SMC::$SUGGESTIONID],$array[$i][SMC::$SUGGESTIONSTATE],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
						case SMC::$SERVICE_SELECTCATEGORY:
							$result = $this->mWorker->SelectCategory($array[$i][SMC::$CATEGORYID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
						case SMC::$SERVICE_UPDATEBEHAVIOURINDEX:
							$result = $this->mWorker->UpdateBehaviourIndex($array[$i][SMC::$SESSIONID],$array[$i][SMC::$STUDENTID],$array[$i][SMC::$TRANSACTIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
						case SMC::$SERVICE_SAVEMYLOCATION:
							$result = $this->mWorker->SaveMyLocation($array[$i][SMC::$LATITUDE],$array[$i][SMC::$LONGITUDE],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
						case SMC::$SERVICE_GETSTUDENTSSTATE:
							$result = $this->mWorker->GetStudentsState($array[$i][SMC::$SESSIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
						case SMC::$SERVICE_GETLIVESTATUS:
							$result = $this->mWorker->GetLiveStatus($array[$i][SMC::$SESSIONID],$array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
						case SMC::$SERVICE_GETLIKERTSCALES:
							$result = $this->mWorker->GetLikertScales($array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
						case SMC::$SERVICE_GETERRORMESSAGES:
							$result = $this->mWorker->GetErrorMessages($array[$i][SMC::$USERID],$array[$i][SMC::$UUID]); 
							break;
						/*case SMC::$SERVICE_:
							$result = $this->mWorker->($array[$i][SMC::$]);
							break;*/
						

				  		default: 
				  			break;
				  	}
				  	$output_time="select current_timestamp";
					$output_time_query=SunDataBaseManager::getSingleton()->QueryDB($output_time);
					$output_time_fetch=mysql_fetch_assoc($output_time_query);
					$xml_output_time=$output_time_fetch['current_timestamp'];
					$update_op="update event_log set xml_output='$result',return_time='$xml_output_time' where event_log_id='$e_id'";
					$update_q=SunDataBaseManager::getSingleton()->QueryDB($update_op);
					return $result;
		  		}
	  		}
  		}	
  		
  		
  	}
} 
?>
