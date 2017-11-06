<div class="accordion_container">
    <input type="hidden" name="studentSessionId" id = "studentSessionId" value="<?php echo $sessionId; ?>">              
    <?php foreach ($studentData AS $student): ?>
    <div class="accordion_head accordion_head_question"
        id="<?php echo $student['studentId']; ?>">
        <div class="containerAccordTitle">
            <div class="rowAccordTitle">
        
              	<div class="leftAccordTitle">
              		<span class="plusMinus-question plusMinus plusMinus-<?php echo $student['studentId']; ?>"
                          id="expand-plus" style="margin-top:11px;"></span>

                    <div class="profile_img_student">
                        <img src="<?php echo LEARNIAT_IMAGE_PATH . '/' . $student['studentId']; ?>_79px.jpg" class="profilePic40">
                    </div>
                    <label style="margin-top:11px;padding-left:10px;"><?php echo $student['studentFirstName']; ?></label>
              	</div>
            
              	<div class="middleAccordTitle">
              	    <?php
              	         $graphIndexData = array($student);
              	         $data = array(
          	                 'graphIndexData' => $graphIndexData,
          	                 'showParticipate' => FALSE,
              	         	 'averageCountText' => FALSE,
              	         	 'maxGraspIndex' => (isset($maxGraspIndex)) ? $maxGraspIndex : 0,
            	         	 'maxParticipationIndex' => (isset($maxParticipationIndex)) ? $maxParticipationIndex : 0,
              	         );
              	    ?>
              		<div class="containerIndex" style="padding-top:10px;">
              		    <?php $this->load->view('graph/index.php', $data); ?>
              		</div>
              	</div>
            
              	<div class="rightAccordTitle">
              	    <div class="containerIndex" style="padding-top:10px;">
                	   <?php  $this->load->view('graph/grasp-index.php', $data); ?>
                	</div>
              	</div>
        
        	</div>
        </div>
    </div>
    
    <div class="accordion_body accordion_body_question" style="display: none;">
        <div class="switch_top">
            <div class="topic-exp-top">
            	<span class="switchActive topic-exp-quer " id="tabQuestion-1<?php echo $student['studentId']; ?>">
            	   <a class="link-student-tab topic-exp-active" href="javascript:void(0);">Queries  (<?php echo $student['queryCount']; ?>)</a>
            	</span>
            	<span class="topic-top-breaker">|</span>
            	<span class="switchActive topic-exp-numCount"  id="tabQuestion-2<?php echo $student['studentId']; ?>">
            	   <a class="link-student-tab" href="javascript:void(0);">Questions (<?php echo $student['questionCount']; ?>)</a>
            	</span>
            </div>
        </div>
        <div class="switch_question_cnt" id="success">
            <!-- Filled by jquery method-->
            <div style="display:block;" id="tabQuestionDetails-1<?php echo $student['studentId']; ?>">
                
            </div>
            <!-- Filled by jquery method-->
            <div style="display:none;"  id="tabQuestionDetails-2<?php echo $student['studentId']; ?>">
                
            </div>
        </div>
    </div>
    <br/>
    <?php endforeach; ?>
        
</div>

<script type="text/javascript">

    $("[id^=tabQuestion]").bind( "click", function() {
		var studentIdData = $(this).attr('id').split('-');
		var studentId = studentIdData[1];
		$("[id^=tabQuestionDetails]").attr('style',  'display:none;');
		$("#tabQuestionDetails-" + studentId).attr('style',  'display:block;');
	});


    $(".link-student-tab").bind( "click", function() {
    	$( ".link-student-tab" ).attr("class", "link-student-tab");
    	$(this).attr("class", "link-student-tab topic-exp-active");
    	
    });
    
	//toggle the component with class accordion_body
	//$(".accordion_head_question").click(function() {
	$(".accordion_head_question").bind( "click", function() {
		if ($('.accordion_body_question').is(':visible')) {
			$(".accordion_body_question").slideUp(600);
			$(".plusMinus-question").attr('id', 'expand-plus');
		}
		if (!$(this).next('.accordion_body_question').is(':visible')) {
			$(this).next(".accordion_body_question").slideDown(600);
			var thisId = $(this).attr('id');
			$(".plusMinus-" + thisId).attr('id', 'expand-minus');
			//$(this).children(".plusMinus").attr('id', 'expand-minus');
			var studentId = $(this).attr('id');
			
			getStudentQueryDetails(studentId);
			getStudentQuestionDetails(studentId);
		}
	});
</script>