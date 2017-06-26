<?php
    $totalGraspIndex = 0 ;
    $showParticipate = (isset($showParticipate)) ? $showParticipate : TRUE;
    $maxGraspIndex = (isset($studentData['maxGraspIndex'])) ? $studentData['maxGraspIndex'] : 0;

    $minGraspIndex = (isset($studentData['minGraspIndex '])) ? $studentData['minGraspIndex '] : 0;
    //Min add
    $maxGraspIndex -= $minGraspIndex;

    $averageGraspIndex = (isset($studentData['averageGraspIndex'])) ? $studentData['averageGraspIndex'] : 0;
    
    //Flag to disable below average and above average
    $averageCountText = (isset($averageCountText)) ? $averageCountText : TRUE;
?>
<!-- SHOW PARTICIPATION INDEX -->

	<div class="titleBarGraspIndexLine">

		<div class="centerIndex">
			<?php ;
		    $belowAverage = 0;
		    $aboveAverage = 0;
			foreach ($studentData['graspIndexData'] AS $graspIndex => $studentData) :
			
				$positionStudent = $graspIndex;
                //Min add
                $positionStudent -= $minGraspIndex;

				//calculate position on index bar method is
				if ($maxGraspIndex > 0) {
					$positionStudent = ($positionStudent * 100) / $maxGraspIndex;
				}
				
				$studentIdentification = $sessionId.$graspIndex;
				$stringPopup = "<table class=''><tr>";
				$shortPopup = "<table class='popupTable'><tr>";
				
				foreach ($studentData AS $key => $student) :
					if ($student['graspIndex'] > 0) :
						$student['graspIndex'] = round($student['graspIndex']);
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
							<span class='sessionPopIndex'>Grasp Index: " . $student['graspIndex'] ."%</span>
							<br><a class='sessionPopDetails' style='color: #016699;' href='#'>see details </a>
						</td>
					</tr>
					</table>";
					
					$string .= "</td>";
						
						
					if ($student['graspIndex'] >=  $averageGraspIndex) :
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
			
			<?php if ($averageGraspIndex > 0) :
	    			if ($maxGraspIndex > 0) :
	    				$averageGraspIndexPosition = $averageGraspIndex - $minGraspIndex;
	    			    $averageGraspIndexPosition = round(($averageGraspIndexPosition * 100) / $maxGraspIndex);
                        $positionStudent = ($positionStudent * 100) / $maxGraspIndex;
						$averageGraspIndexPosition = round($averageGraspIndexPosition);
	    			endif;
				?>
			 	<!-- SHOW STUDENT INDEX -->
	    			<div class="averageGraspDiamond" style="left: <?php echo $averageGraspIndexPosition; ?>%;"></div>
			<?php endif; ?>
		</div>
		<?php if ($averageGraspIndex > 0) : ?>
		     
		     <div class="titleBarParticipationIndexLineCopy">
        		
        		<?php if ($averageGraspIndex > 0 && $averageCountText === TRUE) : ?>
			    	<div style="padding-left:0%">
	    				<div class="<?php echo ($averageGraspIndexPosition > 10) ? 'averageText' : 'averageOverflowText'; ?>">
	    					<?php echo $belowAverage; ?> below average</div>
	    			</div>
	    			<div style="padding-left:95%">
	    				<div class="averageText"><?php echo $aboveAverage; ?> above average</div>
	    			</div>
				<?php endif; ?>
				
				
				<?php if ( $averageCountText === FALSE) : ?>
	    			<div style="padding-left:0%">
	    				<div class="titleBarIndexText">Grasp Index</div>
	    			</div>
				<?php endif; ?>
				
				
				<div class="centerIndex">
				
	        		<?php if ( $averageCountText === TRUE) : ?>
		        		<div class="<?php echo ($showParticipate) ? 'averageText' : '	'; ?>"
		        			 style="padding-left:<?php echo $averageGraspIndexPosition; ?>%">
		        			 <?php echo ($showParticipate) ? 'Average' : 'Result'; ?>
		        			 <?php echo $averageGraspIndex; ?>
		        		</div>
		        	<?php else: ?>
		        		<div style="float:right; text-align:right;">
		    				<div class="titlebarIndexHeadTextRight">
		    					<span class="titlebarIndexHeadNormalTextRight">Average: </span>
		    					<?php echo $averageGraspIndex; ?>%
		    				</div>
		    			</div>
	        		<?php endif; ?>
	        		
        		</div>
        	</div>
        	
			<!-- SHOW STUDENT INDEX -->
			<div  class="averageText" style="padding-left:<?php echo $averageGraspIndexPosition; ?>%" class="average-diamond">
				
			</div>
		<?php endif; ?>
		
	</div>
