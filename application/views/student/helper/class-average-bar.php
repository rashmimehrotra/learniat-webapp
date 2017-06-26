<?php 
if ($averageScoreOfClass <= 60) :
	$color = 'red';
elseif ($averageScoreOfClass > 60 && $averageScoreOfClass <= 90) :
	$color = 'yellow';
elseif ($averageScoreOfClass > 90) :
	$color = 'green';
endif;

?>

<div class = "temperature temperature-<?php echo $color; ?>">
	<div class = "temperature-bar temperature-bar-<?php echo $color; ?>"></div>
</div>