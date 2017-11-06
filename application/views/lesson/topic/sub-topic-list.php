 <!-- Sub Topic list -->

<?php if(!empty($topicData)) : ?>
	<?php foreach ($topicData AS $key => $topicDetails) :
		$otherDetails = $topicDetails->otherDetails;
		$topicId = $topicDetails->topic_id;
        $uniqueId = $classId . '-' . $topicId;
	?>

		<div class="lessonSubTopicList" id="topicCompleteDiv-<?php echo $uniqueId; ?>">
	    	<table width="98%">
				<tr>
					<td class="checkboxTopicList">
						<?php
						$data = array(
							'name' => 'topicName',
							'id' => 'topicName-' . $uniqueId,
							'checked' => ($otherDetails->topicTagged > 0) ? 'checked="checked"' : ''
							);
						$data['onclick'] = "updateLessonTagged($classId, $topicId, this.id, $schoolId, 'subParentTopic', $parentTopicId);";
                        $extra = "class='linkToParent-$classId-$parentTopicId accordionOff'";
						echo form_checkbox($data, $value = '', $checked = FALSE, $extra);
						?>
						<label for="<?php echo 'topicName-' . $uniqueId; ?>"></label>
					</td>
					<td class="titleTopicText">


						<span class="less-subj-tittle">
							<a id="editTopicLink-<?php echo $uniqueId; ?>"
								class="accordionOff less-subj-tittle editTopicLinkClass-<?php echo $topicId; ?>">
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

					<?php
					$title = "<table class='settingTopicOptions'>
							<tr onclick='inlineTopicEdit($classId, $topicId)'
								id='editTopic-$uniqueId'>
								<td><img src='" . base_url('assets/images/lesson-edit-icon.png') ."' class='addTopic'></td>
								<td>Edit</td>
							</tr>
							<tr onclick='removeSubTopicId($classId, $topicId, $parentTopicId);'>
								<td><img src='" . base_url('assets/images/lesson-del-icon.png') ."' class='addTopic'></td>
								<td>Delete</td>
							</tr>
						</table>";
					?>
					<td style="width:50px;">
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
								<td class="lessonSubTopicsCount"
                                    style="width:100px;">
                                    <span id="topicQuestionCount-<?php echo $uniqueId; ?>"
                                          count="<?php echo $otherDetails->topicQuestionCount; ?>"
                                          class="topicQuestionCountClass-<?php echo $topicId; ?>"
                                          topicId="<?php echo $topicId; ?>"
                                          parentTopicId="<?php echo $parentTopicId; ?>"
                                          classId="<?php echo $classId; ?>"
                                        >
									    <?php echo ($otherDetails->topicQuestionCount > 0) ? $otherDetails->topicQuestionCount : 'No'; ?>
                                        <?php echo ($otherDetails->topicQuestionCount > 1) ? 'Questions' : 'Question'; ?>
                                    </span>
								</td>
								<td >
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



	<?php endforeach; ?>
<?php endif; ?>
 <script type='text/javascript'>
     $('input[id^="editTopicTextBox-"]').keypress(function (e) {
         var currentId = $(this).attr('id');
         var key = e.which;
         processEnterEvent(currentId, key);
     });
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

});

</script>