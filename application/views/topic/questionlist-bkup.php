
<?php if (empty($questionDetails)) : ?>
 <div class="topic-bar-box sess-expand-box">
    <div class="student_icon">
     	<p>No question found</p>
    </div>
 </div>
<?php else : ?>
    <?php foreach ($questionDetails AS $key => $details) : ?>
    <div class="topic-bar-box sess-expand-box">
        
        <table width="98%">
        	<tr>
        		<td width="84%">
        			<div class="question_txt">
			            <div class="question_list">
                             <span class="cursorPointer"
                                 onclick="getStudentQuestionPopupScreen(<?php echo $details->question_id; ?>, <?php echo $sessionId; ?>)">
                                <?php echo $key + 1; ?>.&nbsp;&nbsp;<?php echo $details->question_name; ?>
                             </span>
                        </div>
			        </div>
        		</td>
        		<td width="7%">
        			<?php $this->load->view('student/helper/class-average-bar.php',
        					array('averageScoreOfClass' => $details->averageScoreOfClass,
        							'classAverageDataRows' => $details->classAverageDataRows)); ?>
				</td>
				<td width="10%">
					<span class="ver-slide-desc" style="padding-left:20%;">
						<?php echo $details->averageScoreOfClass; ?>%
						(<?php echo $details->classAverageDataRows; ?>)
					</span>
        		</td>
        	</tr>
        </table>
        
    </div>
    <?php endforeach; ?>
<?php endif; ?>

