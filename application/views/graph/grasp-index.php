<?php
    $totalGraspIndex = 0 ;
    $showParticipate = (isset($showParticipate)) ? $showParticipate : TRUE;
    $maxGraspIndex = (isset($maxGraspIndex)) ? $maxGraspIndex : 0;
    
    //Flag to disable below average and above average
    $averageCountText = (isset($averageCountText)) ? $averageCountText : TRUE;
    
    //Always show line greater than participation index.
    //$maxGraspIndex +=10;
?>
<!-- SHOW PARTICIPATION INDEX -->
<?php
     //$maxGraspIndex = 0;
     
     $maxGraspIndexFlag = FALSE;
     if ($maxGraspIndex > 0 ) {
         $maxGraspIndexFlag = TRUE;
     }
     foreach ($graphIndexData AS $student) :
         $totalGraspIndex += $student['graspIndex'];
        
         if ($maxGraspIndexFlag === FALSE) {   
            $maxGraspIndex = ($student['graspIndex'] > $maxGraspIndex) ? $student['graspIndex'] : $maxGraspIndex;
         }
     endforeach;
     
     $averageGraspIndex = 0;
     if ($totalGraspIndex > 0 ) {
         //$averageGraspIndex = sprintf("%1\$.2f",($totalGraspIndex/count($graphIndexData))) ;
     	$averageGraspIndex = round($totalGraspIndex/count($graphIndexData));
     }
?>
	
	<div class="titleBarGraspIndexLine">

		<div class="centerIndex">
		<?php 
				
	    $belowAverage = 0;
	    $aboveAverage = 0;
		foreach ($graphIndexData AS $student) :
		    $positionStudent = $student['graspIndex'];
		    //caluclate position on index bar method is
		    if ($maxGraspIndex > 0) {
		        $positionStudent = ($positionStudent * 100) / $maxGraspIndex;
		    }
		    
		    if ($student['graspIndex'] >=  $averageGraspIndex) :
		        $aboveAverage++;
		    else :
		        $belowAverage++;
		    endif;
			?>
			<!-- SHOW PARTICIPATE FLAG -->
			<?php if($showParticipate === TRUE) : ?>
			<a>
    			<div id="participationPersonInfo"
    				class="participationPerson"
    				style="left: <?php echo $positionStudent; ?>%"
    				title="<div id='ParticipationIndexLeft'>
    						<img src='<?php echo LEARNIAT_IMAGE_PATH . $student['studentId'] . '_' . IMAGE_SIZE_79;?>.jpg' width='47' height='47' /></div>
    						<div id='ParticipationIndexRight'>
    						<span class='sessionPopName'
    							id='studentIndex'>
    							<?php echo $student['studentFirstName'] . " " . $student['studentLastName']; ?>
    							</span><br>
    						<span class='sessionPopIndex'>Grasp Index: <?php echo $student['graspIndex']; ?>%</span>
    						<br>
    						<a class='sessionPopDetails' href='#'>see details </a>
    						</div>"
    			>
    			
    			
    			</div>
			</a>
			<?php endif; ?>
			
			
			<?php endforeach; ?>
			<?php if ($averageGraspIndex > 0) :
    			if ($maxGraspIndex > 0) {
    			    $averageGraspIndexPosition = ($averageGraspIndex * 100) / $maxGraspIndex;
    			    $averageGraspIndexPosition = sprintf("%1\$.2f",$averageGraspIndexPosition);
    			    
    			    //Alwas show line greater than participation index.
    			    //If $averageGraspIndexPosition is  more than 90
    			    $averageGraspIndexPosition = ($averageGraspIndexPosition > 99) ? 99 : $averageGraspIndexPosition;
    			}
			?>
			     <div class="averageGraspDiamond" style="left: <?php echo $averageGraspIndexPosition; ?>%;"></div>
			<?php endif; ?>
			
			<?php if ( $averageCountText === FALSE) : ?>
    			<div style="padding-left:0%">
    				<div class="titleBarIndexText">Grasp Index</div>
    			</div>
			<?php endif; ?>
		</div>
		
		
		
		<?php if ($averageGraspIndex > 0) : ?>
		     
		     <div class="titleBarParticipationIndexLineCopy" style="width:105%;">
        		<div class="centerIndex">
        			
        			<?php if ( $averageCountText === TRUE) : ?>
		        		<div class="averageText"
		        			 style="padding-left:<?php echo $averageGraspIndexPosition; ?>%">
		        			 Average <?php echo $averageGraspIndex; ?>
		        		</div>
		        	<?php else: ?>
		        		<div style="padding-left:71%;width:100px;text-align:right;">
		    				<div class="titleBarIndexText" style="text-align:right;width:100px;">
		    				 	Result: <b><?php echo $averageGraspIndex; ?>%</b>
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