<?php if ($details->answer_score_percent != NULL) : ?>
    <?php $score = sprintf("%1\$.0f", $details->answer_score_percent); ?>
    <table class="studentScore">
        <tr>
            <td>
                <?php
                $data = array(
                    'answerDetails' => $details
                );
                $hrefBase = '';
                if (isset($sessionId) && isset($studentId)) :
                    $params = 'sessionId=' . $sessionId . '&studentId=' . $studentId . '&questionId=' . $details->question_id ;
                    $hrefBase = base_url('index.php/session/answer/index?' . $params);
                elseif (isset($sessionId) && isset($details->question_id) && isset($details->student_id)) :
                    $params =  'sessionId=' . $sessionId . '&studentId=' . $details->student_id . '&questionId=' . $details->question_id ;
                    $hrefBase = base_url('index.php/session/answer/index?' . $params);
                endif;

                ?>
                <span class="exp-std-reply"
                      style=" <?php echo (empty($hrefBase)) ? 'margin:0;' : ''; ?>"
                      href="<?php echo $hrefBase; ?>">
	         				<?php echo $score; ?>%
                </span>
            </td>
            <td>
                <img alt="evaluated" src="<?php echo base_url('assets/images/exp-view-img.png');?>">
            </td>
            <td>
                <?php $imagePath = ($score >=  66) ? ($score >=  100 ? 'evaluated-img' : 'amber-tick') : 'negative-red'; ?>
                <img alt="evaluated" src="<?php echo base_url("assets/images/$imagePath.png");?>">
            </td>
        </tr>
    </table>

<?php else : ?>
    <span class="exp-std-noreply">No Replied</span>
<?php endif; ?>