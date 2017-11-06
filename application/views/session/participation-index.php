<?php $totalParticipationIndex = 0 ; ?>
<!-- SHOW PARTICIPATION INDEX -->
<?php
     $maxParticipationIndex = 0;
     foreach ($graphIndexData AS $student) :
         $totalParticipationIndex += $student['participationIndex'];
         $maxParticipationIndex = ($student['participationIndex'] > $maxParticipationIndex) ? $student['participationIndex'] : $maxParticipationIndex;
     endforeach;
     
     $averageParticipationIndex = 0;
     if ($totalParticipationIndex > 0 ) {
         $averageParticipationIndex = sprintf("%1\$.2f",($totalParticipationIndex/count($graphIndexData))) ;
     }
?>
<div class="containerIndex">
	
	<div class="titleBarParticipationIndexLine">

		<div class="centerIndex">
		<?php 
				
	    $belowAverage = 0;
	    $aboveAverage = 0;
		foreach ($graphIndexData AS $student) :
		    $positionStudent = $student['participationIndex'];
		    //caluclate position on index bar method is
		    if ($maxParticipationIndex > 0) {
		        $positionStudent = ($positionStudent * 100) / $maxParticipationIndex;
		    }
		    
		    if ($student['participationIndex'] >=  $maxParticipationIndex) :
		        $aboveAverage++;
		    else :
		        $belowAverage++;
		    endif;
			?>
			<a><div id="participationPersonInfo" class="participationPerson" style="left: <?php echo $positionStudent; ?>%">
			
				<input class="studentInfo" type="hidden"
					value="<div id='ParticipationIndexLeft'>
						<img src='<?php echo LEARNIAT_IMAGE_PATH . $student['studentId'] . '_' . IMAGE_SIZE_79;?>.jpg' width='47' height='47' /></div>
						<div id='ParticipationIndexRight'>
						<span class='sessionPopName'
							id='studentIndex'>
							<?php echo $student['studentFirstName'] . " " . $student['studentLastName']; ?>
							</span><br>
						<span class='sessionPopIndex'>Participation Index: <?php echo $student['participationIndex']; ?></span><br>
						<a class='sessionPopDetails' href='#'>see details </a>
						</div>"
					>
			
			</div></a>
			<?php endforeach; ?>
			<div class="averageDiamond" style="left: <?php echo $averageParticipationIndex; ?>%;">
			 </div>
			</div>
			<?php if ($averageParticipationIndex > 0) : ?>
			     <?php 
			    
    			     if ($maxParticipationIndex > 0) {
    			         $averageParticipationIndexPosition = ($averageParticipationIndex * 100) / $maxParticipationIndex;
    			         $averageParticipationIndexPosition = sprintf("%1\$.2f",$averageParticipationIndexPosition) ;
    			     }
			     ?>
			     <div class="titleBarParticipationIndexLineCopy">
            		<div class="centerIndex">
            			<div class="averageText" style="padding-left:<?php echo $averageParticipationIndexPosition; ?>%">
            			     Average <?php echo $averageParticipationIndex; ?>
            			</div>
            		</div>
            	</div>
    			<!-- SHOW STUDENT INDEX -->
    			
    			
    			<div  class="averageText" style="padding-left:<?php echo $averageParticipationIndexPosition; ?>%" class="average-diamond">
    				
    			</div>
			<?php endif; ?>
		
	</div>
</div>