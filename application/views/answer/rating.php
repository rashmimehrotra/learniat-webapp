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
<br>
<div class='pop_bottom_icon'>
	<?php if (!empty($assessmentAnswerData->BadgeId)) : ?>
	   <center>
	   		<img width='25' height='25' src='<?php echo LEARNIAT_IMAGE_BADGES_PATH . $assessmentAnswerData->BadgeId . '.png'; ?>'>
	   		<!--  <img width='17' height='25' src='<?php echo base_url('assets/images/popup-medal.png');?>'>-->
	   </center>
    <?php endif; ?>
</div>
<br>
<?php if (!empty($assessmentAnswerData->TextRating) && $assessmentAnswerData->TextRating != '(null)') : ?>
	<div class='pop_bottom'>
		<center><?php echo $assessmentAnswerData->TextRating; ?></center>
	</div> 
 <?php endif; ?>