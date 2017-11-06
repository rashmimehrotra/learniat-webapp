<?php $score = sprintf("%1\$.0f", $answerDetails->answer_score_percent); ?>
<table class='exp-popup' style='max-width: 550px;padding:5px;'>
	<tr>
	    <td colspan='3' style='border: none;'>
	    	<div class='popup-std-reply'>
	    		<p>Student Reply</p>
	    		<span><?php echo $score; ?>%</span>
	    	</div>
	   </td>
  	</tr>
  	
  	<?php 
  	$optionData = $assessmentAnswerData->Options->Option;
  	foreach ($questionOptionData['firstColumn'] AS  $key => $option) :
  	?>
        <tr>
            <td style='max-width: 240px;' >
                <div class='popup_mid'>

                    <p><?php echo $option->question_option;?></p>
                </div>
            </td>

            <td style='max-width: 40px;'>
                <div class='popup_mid'>
                    <center>
                        <?php

                        foreach ($optionData AS $answerData) :
                            $isAnswer = NULL;
                            if ($answerData->OptionText == $questionOptionData['secondColumn'][$key]->question_option) :
                                $sequence = (int) $answerData->Sequence;
                                $oldSequence = (int) $answerData->OldSequence;
                                $isAnswer = ($sequence == $oldSequence) ? '1' : '0';
                                    echo "<center><img src='". base_url('assets/images/' . $isAnswer . '.png') . "' width='15' height='15'></center>";
                                break;
                            endif;
                        endforeach;

                        ?>
                    </center>
                </div>
            </td>
            <td style='max-width: 240px;'>
                <div class='popup_mid'>

                    <p><?php echo $questionOptionData['secondColumn'][$key]->question_option;?></p>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <!-- <tr>
		<td colspan='3' class ='pop-bottom-bg'>
			<?php
            $data = array(
          		'assessmentAnswerData' => $assessmentAnswerData
            );
             $this->load->view('answer/rating.php', $data);
            ?>
		</td>
    </tr>-->
</table>