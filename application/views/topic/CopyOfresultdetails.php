
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/thumbnail/stylesheet.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('assets/js/thumbnail/mootools-1_002.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/thumbnail/mootools-1.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/thumbnail/SlideItMoo.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/thumbnail/load.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/thumbnail/styles.css'); ?>">

<div>
    <div class="warpper_cnt">
        <div class="heading_top">
            <h5>Query Details and Results</h5>
        </div>
        <!--thumbnails slideshow begin-->
        <div class="heading2">
            <h6>Volunteers (<?php echo count($queryDetails); ?>)</h6>
        </div>

        <div style="width: 100%;" id="gallery_container">
            <?php if (!empty($queryDetails)) : ?>
	            <div style="width: 94%;" id="thumb_container">
	                <div id="thumbs" style="padding-left:3px;">
	                	<?php 
	                	for ($i = 1;$i <= 10; $i ++) :
	                	foreach ($queryDetails AS $data) :?>
	                    <a rel="lightbox[galerie]"><img
		                    src="<?php echo LEARNIAT_IMAGE_PATH . $data->student_id; ?>_79px.jpg"
		                    class="profilePic40" style="padding:3px;"
		                    height="40" width="40"></a>
	                    <?php endforeach;endfor;  ?>
	
	                </div>
	            </div>
	            <div class="addfwd"></div>
	            <div class="addbkwd"></div>
            <?php endif; ?>
        </div>
        <!--thumbnails slideshow end-->
        <div class="heading2">
            <h6>Answered (<?php echo count($volunteerSelectedQueryDetails); ?>)</h6>
        </div>
        <!--thumbnails slideshow begin-->
        <div style="width: 100% !important;" id="gallery_container2">
        	<div style="width: 94%;" id="thumb_container2">
            	<div id="thumbs2">
            
            		<?php foreach ($volunteerSelectedQueryDetails AS $assessmentAnswerData) : ?>
            	
                	
            			<div class="thumbnail">
            			
							<div class="thumbnail">
		                        <a rel="lightbox[galerie]"><img
		                        	src="<?php echo LEARNIAT_IMAGE_PATH . $assessmentAnswerData->student_id; ?>_79px.jpg"
		                        	class="profilePic40"
		                        	height="40" width="40"></a>
		                        <div class="thumb_details">
		                            <h6>Mathias</h6>
		                            
		                            
		                            <center>
										<?php
										$starCount = (int) $assessmentAnswerData->Rating;
										for ($i = 1;$i <= 5; $i ++) :
										?>
											<?php if ($i <= $starCount) : ?>
												<img alt='rate' src='<?php echo base_url('assets/images/star.png');?>'>
											<?php else : ?>
												<img alt='rate' src='<?php echo base_url('assets/images/star-outline.png');?>'>
											<?php endif; ?>
										<?php endfor; ?>	
									</center>
		                        </div>
		                        
		                        
		                        <div class="thumb_details">
		                        	<div class='pop_bottom_icon'>
										<?php if (!empty($assessmentAnswerData->BadgeId)) : ?>
										   <center>
										   		<img width='25' height='25'
										   			class="profilePic25"
										   			src='<?php echo LEARNIAT_IMAGE_BADGES_PATH . $assessmentAnswerData->BadgeId . '.png'; ?>'>
										   		
										   </center>
									    <?php endif; ?>
									</div>
		                        </div>
		                        
		                        <div class="bar22">
		                        	<?php
									 	$thumbsDown = round(($assessmentAnswerData->thumbs_down/$studentAttendedNumber) * 100);
									  	$thumbsUp = round(($assessmentAnswerData->thumbs_up/$studentAttendedNumber) * 100);
									  	if ($thumbsDown > 100) {
									  		$thumbsDown = 100;
									  	}
									  	
									  	if ($thumbsUp > 100) {
									  		$thumbsUp = 100;
									  	}
									  ?>
									<div style="width:90%;margin-left:5%;">
									
										<div style="height:5px; background-color:#e5e5e5;">
											<div style="height:5px; width:<?php echo $thumbsUp; ?>%;background-color:#4cd964;">
											</div>
										</div>
									</div>
									<br>
									<div style="width:90%;margin-left:5%;">
										<div style="height:5px; background-color:#e5e5e5;">
											<div style="height:5px; width:<?php echo $thumbsDown; ?>%;background-color:#ff3b30;">
											</div>
										</div>
									</div>
		                        
		                        </div>
		                    </div>
						</div>
					
            		<?php endforeach; ?>
            	</div>
            </div>
            
            <div class="addfwd"></div>
            <div class="addbkwd"></div>
        </div>


    </div>
</div>