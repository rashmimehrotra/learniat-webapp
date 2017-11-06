<?php $score = sprintf("%1\$.0f", $answerDetails->answer_score_percent); ?>
<table class='exp-popup' width='300px'>
	<tr>
	    <td colspan='2' style='border: none;'>
	    	<div class='popup-std-reply'>
	    		<p>Student Reply</p>
	    		<span><?php echo $score; ?>%</span>
	    	</div>
	   </td>
  	</tr>
    <?php 
  	$optionData = $assessmentAnswerData->Options->Option;
  	$isAnswer = $assessmentAnswerData->IsAnswer;
  	foreach ($questionOptionData AS $key => $option) :
        $tdStyle = '';
        if($key > 0) {
            $tdStyle = "style='border: none;'";
        }
  	?>
	    <tr>
	    	<td width='20px;' <?php echo $tdStyle; ?>>
	    		<div class='popup_mid'>
					<?php
					if ($optionData->OptionText == $option->question_option ) :
						echo "<center><img src='". base_url('assets/images/' . $isAnswer . '.png') . "' width='15' height='15'></center>";
					endif;
					
					?>
				</div>
			</td>
			<td width='250px;' <?php echo $tdStyle; ?>>
				<div class='popup_mid'>
					<p><?php echo $option->question_option;?></p>
				</div>
			</td>
	    </tr>
    <?php endforeach; ?>
    <!-- <tr>
		<td colspan='2' class ='pop-bottom-bg'>
			<?php
            $data = array(
          		'assessmentAnswerData' => $assessmentAnswerData
            );
             $this->load->view('answer/rating.php', $data);
            ?>
		</td>
    </tr>-->
</table>