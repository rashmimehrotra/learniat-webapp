<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script> -->
<script type="text/javascript" src="<?php echo base_url('assets/js/sly.js');?>"></script>


<!--
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/thumbnail/styles.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/thumbnail/stylesheet.css'); ?>">
-->

<?php

$countVolunteerSelectedQueryDetails = count($volunteerSelectedQueryDetails);
$countQueryDetails = count($queryDetails);

$width = 765;
switch ($countVolunteerSelectedQueryDetails) {
    case 0 :
    case 1 :
        $width = 255;
        break;
    case 2:
        $width = 520;
        break;
}
?>

<div>
    <div class="warpper_cnt" style="width: <?php echo $width; ?>px;">
        <div class="heading_top">
            <span class="heading_top_span">Query Details and Results</span>
        </div>
        <!--thumbnails slide show begin-->
        <div class="heading2">
             <span class="heading2_span">Volunteers (<?php echo count($queryDetails); ?>)</span>
        </div>

        <div style="width: 100%;" id="gallery_container">
            <?php if (!empty($queryDetails)) : ?>
	            <div class="frame" id="basic" style="padding-bottom: 3px;margin-top:-3px;">
	            	<ul class="clearfix">
	                	<?php foreach ($queryDetails AS $data) :?>
	                    <li>
		                    <img
			                    src="<?php echo LEARNIAT_IMAGE_PATH . $data->student_id; ?>_79px.jpg"
			                    class="profilePic47" style="padding:4px;border-radius:10px;"
			                    height="47" width="47">
		               </li>
	                    <?php endforeach;  ?>
	
	                </ul>
	            </div>
	             <button class="prevPage scrollerButton">
					<i class="icon-angle-left"></i>
				</button>
				<button class="nextPage scrollerButton">
					<i class="icon-angle-right"></i>
				</button>
            <?php endif; ?>
        </div>
        <!--thumbnails slide show end-->
        <div class="heading2">
            <span class="heading2_span">Answered (<?php echo count($volunteerSelectedQueryDetails); ?>)</span>
        </div>
        <!--thumbnails slide show begin-->
        <div style="width: 100% !important;" id="gallery_container2">
        	<div style="width: 100%;" id="thumb_container2" class="frame thumbs2">
            	<ul class="clearfix" >
            		<?php foreach ($volunteerSelectedQueryDetails AS $assessmentAnswerData) : ?>
	            		<li>
	                	
	            			<div class="thumbnail">
	            			
			                        <a rel="lightbox[galerie]"><img
			                        	src="<?php echo LEARNIAT_IMAGE_PATH . $assessmentAnswerData->student_id; ?>_79px.jpg"
			                        	class="profilePic40"
			                        	height="40" width="40"></a>
			                        <div class="thumb_details">
			                            <span class="thumb_details_span"><?php echo $assessmentAnswerData->first_name; ?></span>
			                            
			                            
			                            <center>
											<?php
											$starCount = (int) $assessmentAnswerData->Rating;
											for ($star = 1;$star <= 5; $star ++) :
											?>
												<?php if ($star <= $starCount) : ?>
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
										<div style="width:94%;margin-left:0%;padding-top:7px;">
										
											<div style="height:5px; background-color:#e5e5e5;">
												<div style="height:5px; width:<?php echo $thumbsUp; ?>%;background-color:#4cd964;">
												</div>
											</div>
										</div>
										
										<div style="width:94%;margin-left:0%;padding-top:7px;">
											<div style="height:5px; background-color:#e5e5e5;">
												<div style="height:5px; width:<?php echo $thumbsDown; ?>%;background-color:#ff3b30;">
												</div>
											</div>
										</div>
			                        
			                        </div>
								</div>
							</li>
            			<?php endforeach; ?>
            		</ul>
            </div>
            
             <button class="prevPage scrollerButton2" style="border-bottom-left-radius:10px;">
				<i class="icon-angle-left"></i>
			</button>
			<button class="nextPage scrollerButton2" style="border-bottom-right-radius:10px;">
				<i class="icon-angle-right"></i>
			</button>
        </div>


    </div>
</div>

<script type="text/javascript">
	
	jQuery(function($){
		'use strict';

		// -------------------------------------------------------------
		//   Basic Navigation
		// -------------------------------------------------------------
		(function () {
			var $frame  = $('#basic');
			var $slidee = $frame.children('ul').eq(0);
			var $wrap   = $frame.parent();

			// Call Sly on frame
			$frame.sly({
				horizontal: 1,
				itemNav: 'basic',
				smart: 1,
				activateOn: 'click',
				mouseDragging: 1,
				touchDragging: 1,
				releaseSwing: 1,
				startAt: 0,
				scrollBar: $wrap.find('.scrollbar'),
				scrollBy: 1,
				pagesBar: $wrap.find('.pages'),
				activatePageOn: 'click',
				speed: 300,
				elasticBounds: 1,
				//easing: 'easeOutExpo',
				dragHandle: 1,
				dynamicHandle: 1,
				clickBar: 1,

				// Buttons
				forward: $wrap.find('.forward'),
				backward: $wrap.find('.backward'),
				prev: $wrap.find('.prev'),
				next: $wrap.find('.next'),
				prevPage: $wrap.find('.prevPage'),
				nextPage: $wrap.find('.nextPage')
			});

			// To Start button
			$wrap.find('.toStart').on('click', function () {
				var item = $(this).data('item');
				// Animate a particular item to the start of the frame.
				// If no item is provided, the whole content will be animated.
				$frame.sly('toStart', item);
			});

			// To Center button
			$wrap.find('.toCenter').on('click', function () {
				var item = $(this).data('item');
				// Animate a particular item to the center of the frame.
				// If no item is provided, the whole content will be animated.
				$frame.sly('toCenter', item);
			});

			// To End button
			$wrap.find('.toEnd').on('click', function () {
				var item = $(this).data('item');
				// Animate a particular item to the end of the frame.
				// If no item is provided, the whole content will be animated.
				$frame.sly('toEnd', item);
			});

			// Add item
			$wrap.find('.add').on('click', function () {
				$frame.sly('add', '<li>' + $slidee.children().length + '</li>');
			});

			// Remove item
			$wrap.find('.remove').on('click', function () {
				$frame.sly('remove', -1);
			});
		}());



		// -------------------------------------------------------------
		//   Basic Navigation
		// -------------------------------------------------------------
		(function () {
			var $frame  = $('#thumb_container2');
			var $slidee = $frame.children('ul').eq(0);
			var $wrap   = $frame.parent();

			// Call Sly on frame
			$frame.sly({
				horizontal: 1,
				itemNav: 'basic',
				smart: 1,
				activateOn: 'click',
				mouseDragging: 1,
				touchDragging: 1,
				releaseSwing: 1,
				startAt: 0,
				scrollBar: $wrap.find('.scrollbar'),
				scrollBy: 1,
				pagesBar: $wrap.find('.pages'),
				activatePageOn: 'click',
				speed: 300,
				elasticBounds: 1,
				//easing: 'easeOutExpo',
				dragHandle: 1,
				dynamicHandle: 1,
				clickBar: 1,

				// Buttons
				forward: $wrap.find('.forward'),
				backward: $wrap.find('.backward'),
				prev: $wrap.find('.prev'),
				next: $wrap.find('.next'),
				prevPage: $wrap.find('.prevPage'),
				nextPage: $wrap.find('.nextPage')
			});

			// To Start button
			$wrap.find('.toStart').on('click', function () {
				var item = $(this).data('item');
				// Animate a particular item to the start of the frame.
				// If no item is provided, the whole content will be animated.
				$frame.sly('toStart', item);
			});

			// To Center button
			$wrap.find('.toCenter').on('click', function () {
				var item = $(this).data('item');
				// Animate a particular item to the center of the frame.
				// If no item is provided, the whole content will be animated.
				$frame.sly('toCenter', item);
			});

			// To End button
			$wrap.find('.toEnd').on('click', function () {
				var item = $(this).data('item');
				// Animate a particular item to the end of the frame.
				// If no item is provided, the whole content will be animated.
				$frame.sly('toEnd', item);
			});

			// Add item
			$wrap.find('.add').on('click', function () {
				$frame.sly('add', '<li>' + $slidee.children().length + '</li>');
			});

			// Remove item
			$wrap.find('.remove').on('click', function () {
				$frame.sly('remove', -1);
			});
		}());
	});	
</script>
