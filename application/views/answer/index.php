<?php
$data = array(
	'answerDetails' => $answerDetails,
	'assessmentAnswerData' => $assessmentAnswerData,
	'questionOptionData' => $questionOptionData,
	'questionScribble' => $questionScribble
);
$view = "";
if($assessmentAnswerData->QuestionType)
{
	switch ($assessmentAnswerData->QuestionType) :
		case 'Fresh Scribble' :
		case 'Overlay Scribble' :
			$view = 'scribble';
			break;
		case 'Text':
			$view = 'text';
			break;
		case 'Multiple Choice' :
			$view = 'mcq';	
			break;
		case 'Multiple Response' :
			$view = 'mrq';
			break;
		case 'Match Columns':
			$view = 'mtc';
			break;
		
	endswitch;
}

if (!empty($view))
{
	$this->load->view("answer/$view", $data);
}
else 
	echo "Student didn't answer this question yet.";
	