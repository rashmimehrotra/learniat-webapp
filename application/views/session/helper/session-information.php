<table>
	<tr>
		<td>
			<?php echo $sessionDetails['start_time']; ?> - <?php echo $sessionDetails['end_time']; ?>
			| <?php echo str_replace("Room", "Room: ", $sessionDetails['room_name']); ?>
			| Students: <?php echo $sessionDetails['students_present']; ?> of <?php echo $sessionDetails['total_students']; ?>
			<?php
			$attendancePercent = 0;
			if ($sessionDetails['students_present'] > 0) {
                $attendancePercent = ($sessionDetails['students_present'] / $sessionDetails['total_students']) * 100;
                $attendancePercent= sprintf("%1\$.2f",$attendancePercent);
			}
			?>
		</td>
		<td>&nbsp;(</td>

		<td>
            <?php
                $data=array(
                    'studentsPresent' => $sessionDetails['students_present'],
                    'total' => $sessionDetails['total_students']
                );
                $this->load->view('session/helper/attendance-bar.php', $data);
            ?>
		</td>
		<td><?php echo $attendancePercent; ?>%)</td>
	</tr>
</table>
