 <!-- Topic list -->
<?php 
$topicIntermediateList = array();
foreach ($parentTopicData AS $key => $topicDetails) :
	$otherDetails = $topicDetails->otherDetails;
	$topicId = $topicDetails->topic_id;
	$uniqueId = $classId . '-' . $topicId;
	if ($otherDetails->subTopicTagged === TRUE) {
		$topicIntermediateList[] = $uniqueId;
	}
?>
<div id="topicCompleteDiv-<?php echo $uniqueId; ?>">
	<div class="accordion_sub_head" id="topicLessonId-<?php echo $uniqueId; ?>"
		onclick="getSubtopicDetails(<?php echo $topicId; ?>, <?php echo $classId; ?>, <?php echo $schoolId; ?>, 'undefined', false);">
		<div class="lessonParentTopicList">
	    	<table width="98%">
				<tr>
					<td style="width: 20px;">
						<span class="plus-minus-sub accordion-plusMinus" id="expand-plus"></span>
					</td>
					<td class="checkboxTopicList accordionOff">
						<?php 
						$data = array(
							'name' => 'topicName',
							'id' => 'topicName-' . $uniqueId,
							'checked' => ($otherDetails->topicTagged > 0 ? 'checked="checked"' : '')
						);
						$data['onclick'] = "updateLessonTagged($classId, $topicId, this.id, $schoolId, 'parentTopic');";
						echo form_checkbox(
                            $data,
                            $value = '1',
                            $checked = FALSE,
                            'class="accordionOff checkboxTopic-' . $topicId . '" ' . $checked);
						
						?>
						<label class="accordionOff" for="<?php echo 'topicName-' . $uniqueId; ?>"></label>
					</td>
					

					<td class="titleTopicText">
					    <?php $urlParentTopic = site_url('lesson/questions/parentTopic') . "?parentTopicId=$topicId&classId=$classId&schoolId=$schoolId"; ?>
						<span>
							<a id="editTopicLink-<?php echo $uniqueId; ?>"
								class="accordionOff less-subj-tittle editTopicLinkClass-<?php echo $topicId; ?>"
								href="<?php echo $urlParentTopic; ?>">
								<?php echo $topicDetails->topic_name; ?>
							</a>
						</span>
                        <?php
                            $classTopicInput = 'subtopic-box accordionOff displayNone editTopicTextBoxClass-' . $topicId;
                            echo form_input(
                                array(
                                    'name' => 'topicName',
                                    'id' => "editTopicTextBox-$uniqueId"
                                ),
                                $topicDetails->topic_name,
                                'class="' . $classTopicInput . '" onblur="updateTopic(' . $classId . ',' . $topicId . ');"'
                            );
                        ?>
                        &nbsp;(<?php echo $otherDetails->cumulativeTime; ?>)
                        <?php
                            echo "<br>";
                            echo "<i><span style='font-size: 10px; margin-left:10px; color: #BDBDBD'>(".$topicDetails->topic_id.")</span></i>";
                        ?>
						
						
					</td>
					<td style="width: 10%;">&nbsp;</td>
					<td style="width: 30%;">
					
						<?php
						$graphIndexData = $otherDetails->indexData;
						$data = array(
							'sessionId' => 0,
							'studentData' => $graphIndexData,
							'showParticipate' => TRUE,
							'averageCountText' => FALSE
						);
	                    ?>
	                    <div class="containerIndex">
	                        <?php $this->load->view('graph/grasp-index-new.php', $data); ?>
	                    </div>
						
					</td>
					<td style="width:50px;">
						<?php
						$title = "<table class='settingTopicOptions'>
								<tr onclick='inlineTopicEdit($classId, $topicId)'
									id='editTopic-$topicId'>
									<td><img src='" . base_url('assets/images/lesson-edit-icon.png') ."' class='addTopic'></td>
									<td>Edit</td>
								</tr>";
						if ($otherDetails->progressPercentage == '0.0' & $otherDetails->cumulativeTime == '00:00:00') {
							$title .= "<tr onclick='removeTopicByClassId($classId, $topicId)'>
										<td><img src='" . base_url('assets/images/lesson-del-icon.png') ."' class='addTopic'></td>
										<td>Delete</td>
									</tr>";
						}

						$title .= "</table>";
						?>
						<div class="less-exp-sett-box accordionOff"  style="float:right;">
							<a class="topicSetting accordionOff" title="<?php echo $title; ?>">
								<span class="exp-setting-img accordionOff"></span>
							</a>
						</div>
						
					</td>
				</tr>
				<tr>
					<td> </td>
					<td colspan="6">
						<table width="100%">
							<tr>
								<td class="lessonSubTopicsCount" style="width: 150px;">

                                    <span id="subTopicCount-<?php echo $uniqueId; ?>"
                                          count="<?php echo $otherDetails->subTopicCount; ?>">
                                        <?php echo ($otherDetails->subTopicCount > 0) ? $otherDetails->subTopicCount : 'No'; ?>
                                        <?php echo ($otherDetails->subTopicCount > 1) ? 'Subtopics' : 'Subtopic'; ?>
                                    </span>
                                    |
                                    <span id="topicQuestionCount-<?php echo $uniqueId; ?>"
                                          count="<?php echo $otherDetails->topicQuestionCount; ?>"
                                          class="topicQuestionCountClass-<?php echo $topicId; ?>"
                                          topicId="<?php echo $topicId; ?>"
                                          classId="<?php echo $classId; ?>"
                                        >
                                        <?php echo ($otherDetails->topicQuestionCount > 0) ? $otherDetails->topicQuestionCount : 'No'; ?>
                                        <?php echo ($otherDetails->topicQuestionCount > 1) ? 'Questions' : 'Question'; ?>
                                    </span>
								</td>
								<td>
								
									<div style="padding:2px;">
										 <div>
										 	<span class="plan-student-avg" style="">Overall Progress</span>
			                            	<span class="plan-student-avg" style="float:right;">
                                                Progress:
                                                <b><?php echo $otherDetails->progressPercentage; ?>%</b>
                                            </span>
										 </div>
										<div class="plan-slideBarBlue" style="width: 100%;">
					                        <span class="slideInnerBlue" style="width: <?php echo $otherDetails->progressPercentage; ?>%;"></span>
					                    </div>
									</div>
									
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
	
	</div>
		
		 <div class="accordion_sub_body" id="" style="display: none;">
			 <div class="sub_accordion_padding">

                
                <div class="plan-subque-box"
                     onclick="showTopicDiv(<?php echo $topicId; ?>)">
					<img width="14" height="14" id="addLink-<?php echo $topicId; ?>"
						src="<?php echo base_url('assets/images/expand-button.png'); ?>" alt="expand-img">
					<span class="add-topic-link">Add new sub-topic</span>
				</div>
                <!-- Add topic -->
                 <br/>

                <div class="lessonParentTopicList" id="addTopicDiv-<?php echo $topicId; ?>" style="display:none;">
	                <table>
	                	<tr>
	                        <td>
	                        	<?php
                                echo form_input(array(
                                        'name' => 'subTopicName',
                                        'id' => 'subTopicName-' . $uniqueId),
                                        '',
                                        'class="subtopic-box"'
                                    );
                                echo form_button(
                                        "addSubTopic",
                                        "Add",
                                        'class="button-add" onclick="addSubTopic(' . $topicId . ', ' . $classId . ', ' . $schoolId . ')"'
                                    );
	                            echo form_button(
                                        "cancel",
                                        "Cancel",
                                        'class="button-cancel" onclick="hideTopicDiv(' . $topicId . ')"'
                                    );
                                ?>
	                        </td>
	                    </tr>
	                </table>
              	</div>
                    
		 		<!-- Sub Topic list -->
                <div id="subTopicInfo-<?php echo $uniqueId; ?>"></div>
                <br/>
			 </div>
		 </div>
	</div><!-- topicCompleteDiv- -->
<?php endforeach; ?>
<script type='text/javascript'>

//Create the tooltips only when document ready
$(document).ready(function()
{
    // MAKE SURE YOUR SELECTOR MATCHES SOMETHING IN YOUR HTML!!!
    $('.participationPerson').each(function() {
        $(this).qtip({
        	overwrite: false,
           style: {
	           tip: {
	               corner: true,
	               width: 15,
	               height: 10,
	               border: 1,
	               padding :0
	           },
	           classes: 'qtip-bootstrap'
		   },
           position: {
     			my: 'bottom center', 
     			at: 'top center', 
     			adjust : {
     				screen : true,
     				resize: true
     			},
     			viewport: true	
     		},
	     	hide: {
	     		fixed: true,
	            delay: 300
	     	}
        });
    });


    $('.topicSetting').each(function() {
        $(this).qtip({
        	overwrite: false,
           style: {
	           tip: {
	               corner: true,
	               width: 15,
	               height: 10,
	               border: 1,
	               padding :0
	           },
	           classes: 'qtip-bootstrap'
		   },
		   position: {
			   	my: 'top right', 
    			at: 'bottom center', 
    			adjust : {
    				screen : true,
    				resize: true
    			},
    			viewport: true	
    		},
	     	hide: {
	     		fixed: true,
	            delay: 300
	     	}
        });
    });

	var topicIntermediateList = <?php echo json_encode($topicIntermediateList); ?>;
	$.each(topicIntermediateList, function(k, v) {
		$('#topicName-' + v).prop("indeterminate", true);
	});
});
<?php if (isset($lastParentTopicId) && !empty($lastParentTopicId)) : ?>
$(document).ready(function() {
    getSubtopicDetails(<?php echo $lastParentTopicId;?>, <?php echo $classId; ?>, <?php echo $schoolId; ?>);
	$('#topicLessonId-' + <?php echo $lastParentTopicId . '-' . $classId; ?>).next(".accordion_sub_body").slideDown(400);
	$('#topicLessonId-' + <?php echo $lastParentTopicId . '-' . $classId; ?>).find(".accordion-plusMinus").attr('id', 'expand-minus');
	$('#topicLessonId-' + <?php echo $lastParentTopicId . '-' . $classId; ?>).focus();

	<?php if (isset($lastSubTopicId)) :?>
		$('#topicCompleteDiv-' + <?php echo $lastSubTopicId;?>).focus();
	<?php endif;?>
}); 
<?php endif;?>
</script>