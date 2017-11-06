<div class="ParticipationIndexcopy">
		    <div class="participationIndexLine">
				
			</div>
			<!-- <div class="RoundedRectangleBar">
				<img width="102%" height="9" src="<?php echo base_url();?>/assets/images/RoundedRectangleBar.png">
			</div> -->
			
			<?php 
				$averageParticipationIndex = $studentData['averageParticipationIndex'];
				$maxParticipationIndex = $studentData['maxParticipationIndex'];
			    $belowAverage = 0;
			    $aboveAverage = 0;
				foreach ($studentData['participationIndexData'] AS $participationIndex => $studentData) :
				    $positionStudent = $participationIndex;
				    //caluclate position on index bar method is
				    if ($maxParticipationIndex > 0) {
				        $positionStudent = ($positionStudent * 100) / $maxParticipationIndex;
				    }
				    
				    //$studentIdentification = $sessionId . $student['studentId'];
				    
				    $studentIdentification = $sessionId.$participationIndex;
				    $stringPopup = "<table class='popupTable'><tr>";
				    $shortPopup = "";
				    
					    foreach ($studentData AS $key => $student) :
					    
						    if (($key != 0) && ($key % 3) ==  0) :
						    	$stringPopup .= "</tr><tr>";
						    endif;
					    	
						    $string = "<td>";
							$string .= "<table class='popupTable'>
								<td rowspan='3'>
										<img class='profilePic47' src='".LEARNIAT_IMAGE_PATH . $student['studentId'] . '_' . IMAGE_SIZE_79.".jpg' width='47' height='47' />
									</td>
								</tr>
								<tr>
									<td>
									<span class='sessionPopName'>". $student['studentFirstName'] . " " . $student['studentLastName']. "</span>
									</td>
								</tr>
								<tr>
									<td>
										<span class='sessionPopIndex'>Participation Index: " . $student['participationIndex'] ."</span>
										<br><a class='sessionPopDetails' href='#'>see details </a>
									</td>
								</tr></table>";
					    	
							$string .= "</td>";
							
							
					    	if ($student['participationIndex'] >=  $averageParticipationIndex) :
					    		$aboveAverage++;
					    	else :
					    		$belowAverage++;
					    	endif;
					    	
					    	$stringPopup .= $string;
					    	
					    	if($key < 3) :
					    		$shortPopup .= $string;
					    	endif;
						
					    endforeach;
				    $stringPopup .= "</tr></table>";
				    $shortPopup .= "</tr></table>";
			?>
			<div class="student-index" style="padding-left:<?php echo $positionStudent; ?>%;">
				<a>
					<img class="student-info" id="student-<?php echo $studentIdentification; ?>" width="7" height="7"
					 src="<?php echo base_url();?>/assets/images/student-average.png">
				</a>
				<div id='studentInfo<?php echo $studentIdentification; ?>' class="popupDivIndex" style="display: none;">
					<?php echo $stringPopup; ?>
				</div>
				<div id='studentInfoShort<?php echo $studentIdentification; ?>' class="popupDivIndex" style="display: none;">
					<?php echo $shortPopup; ?>
				</div>
			</div>
			<?php endforeach; ?>
			
			<?php if ($averageParticipationIndex > 0) : ?>
			     <?php 
			    
    			     if ($maxParticipationIndex > 0) :
    			         $averageParticipationIndexPosition = ($averageParticipationIndex * 100) / $maxParticipationIndex;
    			         $averageParticipationIndexPosition = sprintf("%1\$.2f",$averageParticipationIndexPosition) ;
    			     endif;
			     ?>
    			<!-- SHOW STUDENT INDEX -->
    			<div style="padding-left:<?php echo $averageParticipationIndexPosition; ?>%">
    				<div class="average-text">Average <?php echo $averageParticipationIndex; ?></div>
    			</div>
    			<div style="padding-left:<?php echo $averageParticipationIndexPosition; ?>%" class="average-diamond">
    				<img width="14" height="14" src="<?php echo base_url();?>/assets/images/average-diamond.png">
    			</div>
			<?php endif; ?>
		</div>
		
		<?php if ($averageParticipationIndex > 0) : ?>
    		<div class="ParticipationIndexcopy2">
    		    <div style="padding-left:2%">
    				<div class="average-text"><?php echo $belowAverage; ?> below average</div>
    			</div>
    			<div style="padding-left:91%">
    				<div class="average-text"><?php echo $aboveAverage; ?> above average</div>
    			</div>
    		</div>
		<?php endif; ?>