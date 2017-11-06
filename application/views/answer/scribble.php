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
				<center>
					<div style="position: relative; left: 0; top: 0;">
					
					<?php if(!empty($questionScribble->image_path)):
					    $questionScribbleImage = LEARNIAT_IMAGE_SCRIBBLE . $questionScribble->image_path;
					    $fileHeaders = @get_headers($questionScribbleImage);
						if ($fileHeaders[0] != 'HTTP/1.1 404 Not Found') :
					?>
						<img src="<?php echo $questionScribbleImage; ?>"
							 tyle="position: relative; top: 0; left: 0;"
						 	 width="250" height="150">
					<?php 
						endif;
					endif;

					$absoluteStyle = (!empty($questionScribble->image_path)) ? 'position:absolute;' : 'position: relative;';
					$scribble = LEARNIAT_IMAGE_SCRIBBLE . $assessmentAnswerData->Scribble;

					$fileHeaders = @get_headers($scribble);
                    if ($fileHeaders[0] != 'HTTP/1.1 404 Not Found' && (!empty($assessmentAnswerData->Scribble))) :

                        //$fileGetContent = file_get_contents($scribble);
                        //if (!empty($fileGetContent)) :

					?>
						<img src="<?php echo $scribble; ?>"
							 style="<?php echo $absoluteStyle; ?>;left: 0; top: 0;"
							width="250" height="150">
					<?php
                        //endif;
                    endif;

					if(!empty($assessmentAnswerData->TeacherScribble)):
					    $teacherScribble = LEARNIAT_IMAGE_SCRIBBLE . $assessmentAnswerData->TeacherScribble;

					    $fileHeaders = @get_headers($teacherScribble);
						if ($fileHeaders[0] != 'HTTP/1.1 404 Not Found') :

                            //$fileGetContent = file_get_contents($teacherScribble);
                            //if (!empty($fileGetContent)) :
					?>
						<img src="<?php echo $teacherScribble; ?>"
							 style="position:absolute;left: 0; top: 0;"
							width="250" height="150">
					<?php
                            //endif;
						endif;
					endif;
					?>
					</div>
				</center>
			</div>
		</td>
    </tr>
    
    <tr>
		<td class = 'pop-bottom-bg'>
			<?php
            $data = array(
          		'assessmentAnswerData' => $assessmentAnswerData
            );
             $this->load->view('answer/rating.php', $data);
            ?>
		</td>
    </tr>
</table>