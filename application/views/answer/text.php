<?php $score = sprintf("%1\$.0f", $answerDetails->answer_score_percent); ?>
<table class='exp-popup' width='300px'>
	<tr>
	    <td style='border: none;'>
	    	<div class='popup-std-reply'>
	    		<p>Student Reply</p>
	    		<span><?php echo $score; ?>%</span>
	    	</div>
	   </td>
  	</tr>
    <tr>
		<td>
			<div class='popup_mid'>
				
				<p class="italic-text"><?php echo $assessmentAnswerData->TextAnswer;?></p>
			</div>
		</td>
    </tr>
    
    <tr>
		<td class ='pop-bottom-bg'>
			<?php
            $data = array(
          		'assessmentAnswerData' => $assessmentAnswerData
            );
             $this->load->view('answer/rating.php', $data);
            ?>
		</td>
    </tr>
</table>