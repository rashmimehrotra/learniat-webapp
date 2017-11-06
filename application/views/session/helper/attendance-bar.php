<?php
$attendancePercent = 0;
if ($studentsPresent > 0) {
    $attendancePercent = ($studentsPresent / $total) * 100;
    $attendancePercent= sprintf("%1\$.2f",$attendancePercent);
}

if ($attendancePercent <= 60) :
    $color = '#ff3b30';
elseif ($attendancePercent > 60 && $attendancePercent <= 90) :
    $color = '#ffcc00';
elseif ($attendancePercent > 90) :
    $color = '#4cd964';
endif;

$firstBar = ($attendancePercent >= 30 ) ? 30 : $attendancePercent;
$secondBar = ($attendancePercent >= 60 ) ? 60 : (($attendancePercent <= 30 ) ? 0 : $attendancePercent);
$thirdBar = ($attendancePercent >= 100 ) ? 100 : (($attendancePercent <= 60 ) ? 0 : $attendancePercent);
?>

<div class="container-bar">
    <div class="bar" style="height: <?php echo $firstBar;?>%;background: <?php echo $color; ?>;"></div>
    <div class="bar" style="height: <?php echo $secondBar;?>%;background: <?php echo $color; ?>;"></div>
    <div class="bar" style="height: <?php echo $thirdBar;?>%;background: <?php echo $color; ?>;"></div>
</div>