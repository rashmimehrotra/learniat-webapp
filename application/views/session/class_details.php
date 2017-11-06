<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php 
 if (!empty($sessionResult)) :
?>
<div>
	<div class="para">
		<div class="para-left">
			<span class="sessGroupTopic "><?php echo $sessionResult['class_name']; ?></span><br> 
			<div class="sessTopicDetail ">
    		     <table>
    		          <tr>
        		          <td>
                            <?php echo $sessionResult['start_time']; ?> - <?php echo $sessionResult['end_time']; ?> | 
                            <?php echo str_replace("Room", "Room: ", $sessionResult['room_name']); ?> | Students: 
                            <?php echo $sessionResult['students_present']; ?> of <?php echo $sessionResult['total_students']; ?>
                            <?php
                            	$attendancePercent = 0;
                            	if ($sessionResult['students_present'] > 0) {
                            		$attendancePercent = ($sessionResult['students_present'] / $sessionResult['total_students']) * 100;
                            		$attendancePercent= sprintf("%1\$.2f",$attendancePercent);
                            	}
                            ?>
                            (<?php
                                $firstBar = ($attendancePercent >= 30 ) ? 30 : $attendancePercent;
                                $secondBar = ($attendancePercent > 30 && $attendancePercent <= 60 ) ? $attendancePercent : 60;
                                $secondBar = ($attendancePercent < 30 ) ? 0 : $secondBar;
                                $thirdBar = ($attendancePercent > 60 && $attendancePercent <= 100 ) ? $attendancePercent : 100;
                                $thirdBar = ($attendancePercent < 60 ) ? 0 : $thirdBar;
                            ?>
                            <?php echo $attendancePercent; ?>%)
        		          </td>
        		          <td>
        		              <div class="container-bar">
                			    <div class="bar" style="height: <?php echo $firstBar;?>%"></div>
                			    <div class="bar" style="height: <?php echo $secondBar;?>%"></div>
                			    <div class="bar" style="height: <?php echo $thirdBar;?>%"></div>
                			</div>
        		          </td>
    		          </tr>
    		     </table>
				
				
			</div>
		</div>
		<!-- Para right-->
		<div class="para-right">
			<div style="float:left;margin: 13px 0 13px 0;">Lesson plan</div>
			<div style="float:left; margin-left:20%;">
			<?php $uniqueId = $sessionResult['sessionId'] . str_replace("-", "", $sessionResult['sessionDate']); ?>
				<div class="lesson-progress-bar"
				    id="progress-<?php echo $uniqueId; ?>">
					<a>
						<div class="covered-before" style="width:30%"><?php echo $sessionResult['topicCoveredBefore']; ?></div>
						<div class="covered-in" style="width:50%"><?php echo $sessionResult['topicCovered']; ?></div>
						<div class="tobe-covered" style="width:20%"><?php echo $sessionResult['toBeCovered']; ?></div>
						<div style="clear:both;"></div>
						<input id="lessonProgress<?php echo $uniqueId; ?>"
						    type="hidden"
							value="<div id='ParticipationIndexLesson'>
									<table>
									<tr><td width='20'><div class='square-covered-before'></div></td>
    									<td class='width170'>
    									   <?php echo $sessionResult['topicCoveredBefore']; ?> Covered before the session
    									</td>
									</tr>
									<tr>
									   <td><div class='square-covered-in'></div></td>
									   <td> <?php echo $sessionResult['topicCovered']; ?> Covered in this session</td>
									</tr>
									<tr>
									   <td><div class='square-tobe-covered'></div></td>
									   <td> <?php echo $sessionResult['toBeCovered']; ?> Topics to be covered</td></tr>
									<table>
								</div>"
								>
					</a>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
		 <!-- End Para right-->
	</div>
	<!-- End Para-->
	
   <!-- Start chart--> 
	<div class="clear"></div>
	<div class="para"  style="padding-bottom: 4%; padding-top:2%;">
		<div class="ParticipationIndexcopy">
		    <div class="participationIndexLine">
				
			</div>
			<!-- <div class="RoundedRectangleBar">
				<img width="102%" height="9" src="<?php echo base_url();?>/assets/images/RoundedRectangleBar.png">
			</div> -->
			<?php $totalParticipationIndex = 0 ; ?>
			<!-- SHOW PARTICIPATION INDEX -->
			<?php
			     foreach ($studentData AS $student) :
			         $totalParticipationIndex += $student['participationIndex'];
			     endforeach;
			     
			     $averageParticipationIndex = 0;
			     if ($totalParticipationIndex > 0 ) {
			         $averageParticipationIndex = sprintf("%1\$.1f",($totalParticipationIndex/count($studentData))) - 2 ;
			     }
			?>
			<?php 
				
			    $belowAverage = 0;
			    $aboveAverage = 0;
				foreach ($studentData AS $student) :
				    $positionStudent = $student['participationIndex'];
				    $studentIdentification = $sessionId . $student['studentId'];
				    if ($student['participationIndex'] >=  $averageParticipationIndex) :
				        $aboveAverage++;
				    else :
				        $belowAverage++;
				    endif;
			?>
			<div class="student-index" style="padding-left:<?php echo $positionStudent; ?>%;">
				<a>
					<img class="student-info" id="student-<?php echo $studentIdentification; ?>" width="7" height="7"
					 src="<?php echo base_url();?>/assets/images/student-average.png">
				</a>
				<input id='studentInfo<?php echo $studentIdentification; ?>' type="hidden"
						value="<div id='ParticipationIndexLeft'>
							<img src='<?php echo LEARNIAT_IMAGE_PATH . $student['studentId'] . '_' . IMAGE_SIZE_79;?>.jpg' width='47' height='47' /></div>
							<div id='ParticipationIndexRight'>
							<span class='sessionPopName'
								id='studentIndex-<?php echo $studentIdentification; ?>'>
								<?php echo $student['studentFirstName'] . " " . $student['studentLastName']; ?>
								</span><br>
							<span class='sessionPopIndex'>Participation Index: <?php echo $student['participationIndex']; ?></span><br>
							<a class='sessionPopDetails' href='#'>see details </a>
							</div>"
						>
			</div>
			<?php endforeach; ?>
			
			<?php if ($averageParticipationIndex > 0) : ?>
    			<!-- SHOW STUDENT INDEX -->
    			<div style="padding-left:<?php echo $averageParticipationIndex; ?>%">
    				<div class="average-text">Average <?php echo $averageParticipationIndex; ?></div>
    			</div>
    			<div style="padding-left:<?php echo $averageParticipationIndex; ?>%" class="average-diamond">
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
	</div>
	<!-- End chart-->
</div>
</table>
<?php endif; ?>