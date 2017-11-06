
<table class='exp-popup' width='300px'>
	<tr>
	    <td style='border: none;'>
	    	<div class='popup-std-reply'>
	    		<p>Evaluation</p>
	    	</div>
	   </td>
  	</tr>

    
    <tr>
		<td>
			<?php
            $data = array(
          		'assessmentAnswerData' => $assessmentAnswerData
            );
             $this->load->view('answer/rating.php', $data);
            ?>
		</td>
    </tr>
    
    <tr>
		<td style='border: none;'>
			 <?php
			 	$thumbsDown = round(($assessmentAnswerData->thumbs_down/$studentAttendedNumber) * 100);
			  	$thumbsUp = round(($assessmentAnswerData->thumbs_up/$studentAttendedNumber) * 100);
			  	if ($thumbsDown > 100) {
			  		$thumbsDown = 100;
			  	}
			  	
			  	if ($thumbsUp > 100) {
			  		$thumbsUp = 100;
			  	}
			  ?>
			<div style="width:90%;margin-left:5%;">
			
				<div style="height:5px; background-color:#e5e5e5;">
					<div style="height:5px; width:<?php echo $thumbsUp; ?>%;background-color:#4cd964;">
					</div>
				</div>
			</div>
			<br>
			<div style="width:90%;margin-left:5%;">
				<div style="height:5px; background-color:#e5e5e5;">
					<div style="height:5px; width:<?php echo $thumbsDown; ?>%;background-color:#ff3b30;">
					</div>
				</div>
			</div>
			<br>
		</td>
    </tr>
</table>