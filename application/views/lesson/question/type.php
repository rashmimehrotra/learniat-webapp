<?php
$data = array(
    'duplicate' => $duplicate,
    'questionOptionData' => $questionOptionData,
    'questionAverageScore' => $questionAverageScore
);
switch ($questionTypeId) :
	case 1 :
	case 2 :
		$this->load->view('lesson/question/multiple-choice.php', $data);
	break;

	case 3 :
		$this->load->view('lesson/question/multiple-type.php', $data);
	break;

	case 4 :
		$this->load->view('lesson/question/overlay-type.php');
	break;
endswitch;