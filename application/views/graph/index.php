<?php
    $totalParticipationIndex = 0 ;
    $showParticipate = (isset($showParticipate)) ? $showParticipate : TRUE;
    $maxParticipationIndex = (isset($maxParticipationIndex)) ? $maxParticipationIndex : 0;
    
    //Flag to disable below average and above average
    $averageCountText = (isset($averageCountText)) ? $averageCountText : TRUE;
    
    //Alwas show line greater than participation index.
    //$maxParticipationIndex +=10;
?>
<!-- SHOW PARTICIPATION INDEX -->
<?php
     //$maxParticipationIndex = 0;
     
     $maxParticipationIndexFlag = FALSE;
     if ($maxParticipationIndex > 0 ) {
         $maxParticipationIndexFlag = TRUE;
     }
     foreach ($graphIndexData AS $student) :
         $totalParticipationIndex += $student['participationIndex'];
        
         if ($maxParticipationIndexFlag === FALSE) {   
            $maxParticipationIndex = ($student['participationIndex'] > $maxParticipationIndex) ? $student['participationIndex'] : $maxParticipationIndex;
         }
     endforeach;
     
     $averageParticipationIndex = 0;
     if ($totalParticipationIndex > 0 ) {
         $averageParticipationIndex = sprintf("%1\$.1f",($totalParticipationIndex/count($graphIndexData))) ;
     }
?>
	
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
		    
		    if ($student['participationIndex'] >=  $averageParticipationIndex) :
		        $aboveAverage++;
		    else :
		        $belowAverage++;
		    endif;
			?>
			<!-- SHOW PARTICIPATE FLAG -->
			<?php if($showParticipate === TRUE) : ?>
			<a>
    			<div id="participationPersonInfo"
    				title="<div id='ParticipationIndexLeft'>
    						<img src='<?php echo LEARNIAT_IMAGE_PATH . $student['studentId'] . '_' . IMAGE_SIZE_79;?>.jpg' width='47' height='47' /></div>
    						<div id='ParticipationIndexRight'>
    						<span class='sessionPopName'
    							id='studentIndex'>
    							<?php echo $student['studentFirstName'] . " " . $student['studentLastName']; ?>
    							</span><br>
    						<span class='sessionPopIndex'>Participation Index: <?php echo $student['participationIndex']; ?></span><br>
    						<a class='sessionPopDetails' href='#'>see details </a>
    						</div>"
    				class="participationPerson" style="left: <?php echo $positionStudent; ?>%">
    			
    			
    			</div>
			</a>
			<?php endif; ?>
			
			
			<?php endforeach; ?>
			<?php if ($averageParticipationIndex > 0) :
    			if ($maxParticipationIndex > 0) {
    			    $averageParticipationIndexPosition = ($averageParticipationIndex * 100) / $maxParticipationIndex;
    			    $averageParticipationIndexPosition = sprintf("%1\$.1f",$averageParticipationIndexPosition);
    			    
    			    //Alwas show line greater than participation index.
    			    //If $averageParticipationIndexPosition is  more than 95
    			    $averageParticipationIndexPosition = ($averageParticipationIndexPosition > 99) ? 99 : $averageParticipationIndexPosition;
    			}
			?>
			     <div class="averageDiamond" style="left: <?php echo $averageParticipationIndexPosition; ?>%;"></div>
			<?php endif; ?>
			
			<?php if ( $averageCountText === FALSE) : ?>
    			<div style="padding-left:0%">
    				<div class="titleBarIndexText">Participation Index</div>
    			</div>
			<?php endif; ?>
		</div>
		
		
		
		<?php if ($averageParticipationIndex > 0) : ?>
		     
		     <div class="titleBarParticipationIndexLineCopy" style="width:105%;">
        		<div class="centerIndex">
        			
        			<?php if ( $averageCountText === TRUE) : ?>
		        		<div class="averageText"
		        			 style="padding-left:<?php echo $averageParticipationIndexPosition; ?>%">
		        			 Average <?php echo $averageParticipationIndex; ?>
		        		</div>
		        	<?php else: ?>
		        		<div style="padding-left:73%;width:100px;text-align:right;">
		    				<div class="titleBarIndexText" style="text-align:right;width:100px;">
                                Result: <b><?php echo $averageParticipationIndex; ?></b>
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