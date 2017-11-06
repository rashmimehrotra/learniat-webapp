<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<link rel="stylesheet" href="<?php echo base_url('assets/css/graph.css');?>" />
<?php  if (!empty($sessionResult)) :?>
<div class="para">
	<table width="100%">
	  <tr>
	    <td colspan="2" class="para-left">
	    	<span class="sessGroupTopic">
				<a href="<?php echo base_url();?>index.php/session/summary/details?sessionId=<?php echo $sessionResult['sessionId']; ?>&sessionDate=<?php echo $sessionDate; ?>">
			    	<?php echo $sessionResult['class_name']; ?>
				</a>
			</span>
	        <br>
	        <div class="sessTopicDetail ">
	        	<?php
                $data=array( 'sessionDetails' => $sessionResult );
                $this->load->view('session/helper/session-information.php', $data);
                ?>
	        </div>
	    </td>
	    <td colspan="1" class="para-right">
		   <table>
		   	<tr>
			   	<td class="topic-bar-box" style="padding-right:15px; font-size: 12px;">Lesson plan</td>
			   	<td>
			   		<?php $uniqueId=$sessionResult[ 'sessionId'] . str_replace( "-", "", $sessionResult[ 'sessionDate']); ?>
		                <div class="lesson-progress-bar" id="progress-<?php echo $uniqueId; ?>">
		                    <a>
		                        <?php $totalCovered=$sessionResult[ 'topicCoveredBefore'] + $sessionResult[ 'topicCovered'] + $sessionResult[ 'toBeCovered']; ?>
		                        <?php if (!empty($sessionResult[ 'topicCoveredBefore'])) : ?>
		                        <div class="covered-before" style="width:<?php echo (($sessionResult['topicCoveredBefore'] / $totalCovered) * 100); ?>%">
		                            <?php echo $sessionResult[ 'topicCoveredBefore']; ?>
		                        </div>
		                        <?php endif; ?>
		                        <?php if (!empty($sessionResult[ 'topicCovered'])) : ?>
		                        <div class="covered-in" style="width:<?php echo (($sessionResult['topicCovered'] / $totalCovered) * 100); ?>%">
		                            <?php echo $sessionResult[ 'topicCovered']; ?>
		                        </div>
		                        <?php endif; ?>
		                        <?php if (!empty($sessionResult[ 'toBeCovered'])) : ?>
		                        <div class="tobe-covered" style="width:<?php echo (($sessionResult['toBeCovered'] / $totalCovered) * 100); ?>%">
		                            <?php echo $sessionResult[ 'toBeCovered']; ?>
		                        </div>
		                        <?php endif; ?>
		                        <div style="clear:both;"></div>
		                        <input id="lessonProgress<?php echo $uniqueId; ?>" type="hidden" value="<div id=''>
											<table class='popupTableLessonPlan'>
											<tr>
											<td width='20'>
											   <div class='square-covered-before'></div>
											</td>
		    									<td class='width170'>
		    									 &nbsp;<?php echo $sessionResult['topicCoveredBefore']; ?> Covered before the session
		    									</td>
											</tr>
											<tr>
											   <td><div class='square-covered-in'></div></td>
											   <td>&nbsp;<?php echo $sessionResult['topicCovered']; ?> Covered in this session</td>
											</tr>
											<tr>
											   <td><div class='square-tobe-covered'></div></td>
											   <td>&nbsp;<?php echo $sessionResult['toBeCovered']; ?> Topics to be covered</td></tr>
											<table>
										</div>">
		                    </a>
		                </div>
			   		</td>
			   	</tr>
		   	</table>
	    </td>
	  </tr>
	  <tr>
	    <td  colspan="3">
	    	<div style="padding-bottom: 2%; padding-top:1%;"><!-- class="para"  -->
		   		<!-- <div class="graphContainer"> -->
		            <?php $data = array( 'studentData' => $studentData ); ?>
		            <!-- <div class="containerIndex"> -->
		                <?php $this->load->view('graph/participation-index-new.php', $data); ?>
		            <!-- </div> -->
		       <!-- </div> -->
	        </div>
	    </td>
	  </tr>
	</table>
</div>
<?php endif; ?>