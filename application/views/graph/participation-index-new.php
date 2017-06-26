<?php
    $totalParticipationIndex = 0 ;
    $showParticipate = (isset($showParticipate)) ? $showParticipate : TRUE;
    $maxParticipationIndex = (isset($studentData['maxParticipationIndex'])) ? $studentData['maxParticipationIndex'] : 0;
    $minParticipationIndex = (isset($studentData['minParticipationIndex'])) ? $studentData['minParticipationIndex'] : 0;

    //Min add
    $maxParticipationIndex -= $minParticipationIndex;

    $averageParticipationIndex = (isset($studentData['averageParticipationIndex'])) ? $studentData['averageParticipationIndex'] : 0;
    $averageParticipationIndex = sprintf("%1\$.1f",$averageParticipationIndex) ;
    //Flag to disable below average and above average
    $averageCountText = (isset($averageCountText)) ? $averageCountText : TRUE;
    //Always show line greater than participation index.
?>
<!-- SHOW PARTICIPATION INDEX -->

	<div class="titleBarParticipationIndexLine">

		<div class="centerIndex">

		<?php
		//Allow only show participate
		if($showParticipate === TRUE) :
		?>
					<?php

				    $belowAverage = 0;
				    $aboveAverage = 0;
					foreach ($studentData['participationIndexData'] AS $participationIndex => $studentData) :

						$positionStudent = $participationIndex;

                        //Min add
                        $positionStudent -= $minParticipationIndex;

						//calculate position on index bar method is
						if ($maxParticipationIndex > 0) :
							$positionStudent = ($positionStudent * 100) / $maxParticipationIndex;
						endif;

						$studentIdentification = $sessionId.$participationIndex;
						$stringPopup = "<table class=''><tr>";
						$shortPopup = "<table class='popupTable'><tr>";

						foreach ($studentData AS $key => $student) :

							if ($student['participationIndex'] > 0) :
								$student['participationIndex'] = sprintf("%1\$.1f",$student['participationIndex']) ;
							endif;
							if (($key != 0) && ($key % 3) ==  0) :
								$stringPopup .= "</tr><tr>";
							endif;

							$string = "<td>";
							$string .= "<table class='popupTable'>
								<tr>
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
										<br><a class='sessionPopDetails'  style='color: #016699;' href='#'>see details </a>
									</td>
								</tr>
							</table>";

							$string .= "</td>";


							if ($student['participationIndex'] >=  $averageParticipationIndex) :
								$aboveAverage++;
							else :
								$belowAverage++;
							endif;

							$stringPopup .= $string;

							if($key < 3) :
								$shortPopup .= $string . "</tr><tr>";
							endif;

						endforeach;
						$stringPopup .= "</tr></table>";
						$shortPopup .= "</tr></table>";
						?>
						<!-- SHOW PARTICIPATE FLAG -->
						<?php if($showParticipate === TRUE) : ?>
							<div id="participationPersonInfo-<?php echo $studentIdentification; ?>"
								title="<?php echo $stringPopup; ?>"
								class="participationPerson" style="left:<?php echo $positionStudent; ?>%;">

							</div>

						<?php endif; ?>
					<?php endforeach; ?>
			<?php
			//End only show participate
			endif; ?>
			<?php if ($averageParticipationIndex > 0) :
	    			if ($maxParticipationIndex > 0) :
	    				$averageParticipationIndexPosition = $averageParticipationIndex - $minParticipationIndex;
	    			    $averageParticipationIndexPosition = ($averageParticipationIndexPosition * 100) / ($maxParticipationIndex ) ;
						
	    			    $averageParticipationIndexPosition = sprintf("%1\$.1f",$averageParticipationIndexPosition) ;
	    			endif;
				?>
			 	<!-- SHOW STUDENT INDEX -->
	    			<div class="averageDiamond" style="left: <?php echo $averageParticipationIndexPosition; ?>%;"></div>
			<?php endif; ?>
		</div>
		<?php if ($averageParticipationIndex > 0) : ?>

		     <div class="titleBarParticipationIndexLineCopy">

        		<?php if ($averageParticipationIndex > 0 && $averageCountText === TRUE) : ?>
			    	<div style="padding-left:0%">
	    				<div class="<?php echo ($averageParticipationIndexPosition > 20) ? 'averageText' : 'averageOverflowText'; ?>">
	    				<?php echo $belowAverage; ?> below average</div>
	    			</div>
	    			<div style="padding-left:91%">
	    				<div class="averageText"><?php echo $aboveAverage; ?> above average</div>
	    			</div>
				<?php endif; ?>

				<?php if ( $averageCountText === FALSE) : ?>
	    			<div style="padding-left:0%">
	    				<div class="titleBarIndexText">Participation Index</div>
	    			</div>
				<?php endif; ?>


				<div class="centerIndex">

					<?php if ( $averageCountText === TRUE) : ?>
		        		<div class="<?php echo ($showParticipate) ? 'averageText' : '	'; ?>"
		        			 style="padding-left:<?php echo ($averageParticipationIndexPosition -2); ?>%;">
		        			 <?php echo ($showParticipate) ? 'Average' : 'Result'; ?>
		        			 <?php echo $averageParticipationIndex; ?>
		        		</div>
		        	<?php else: ?>
		        		<div style="float:right; text-align:right;">
		    				<div class="titlebarIndexHeadTextRight">
		    					<span class="titlebarIndexHeadNormalTextRight">Average: </span>
		    					<?php echo $averageParticipationIndex; ?>
		    				</div>
		    			</div>
	        		<?php endif; ?>
        		</div>
        	</div>
			<!-- SHOW STUDENT INDEX -->
			<div  class="averageText" style="padding-left:<?php echo $averageParticipationIndexPosition; ?>%" class="average-diamond">
			</div>
		<?php endif; ?>

	</div>