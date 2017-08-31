<?php if (empty($questionDetails)) : ?>
    <div class="topic-bar-box sess-expand-box">
        <p>No question found</p>
    </div>
<?php else : ?>

    <?php foreach ($questionDetails AS $key => $details) : ?>
        <div class="topic-bar-box-question sess-expand-box" id="questionList-<?php echo $details->question_id; ?>">

            <table width="98%" >
                <tr>
                    <td>
                        <div class="question_txt">
                            <div class="question_list"><?php echo $key + 1; ?>.&nbsp;&nbsp;<?php echo $details->question_name;
                            echo '<br>';
                            echo '<div class="topic-answer">'.$details->question_type_title.'</div>';
                             ?>
                            
                            </div>
                        </div>
                    </td>

                    <?php if ($details->averageScoreOfClass > 0) : ?>
                        <td style="width:18px;">
                        <?php
                            $this->load->view(
                                    'student/helper/class-average-bar.php',
                                    array(
                                        'averageScoreOfClass' => $details->averageScoreOfClass,
                                        'classAverageDataRows' => $details->classAverageDataRows
                                    )
                                );

                        ?>
                        </td>
                    <?php endif; ?>
                    <td class="<?php echo (($details->averageScoreOfClass > 0) ? 'responseTdMin': 'responseTdMax'); ?>">

                            <?php
                            $questionScore = '';
                            if ($details->averageScoreOfClass > 0) :
                                $questionScore = sprintf(
                                    '<span class="ver-slide-desc responseDiv" style=""> %d%% (%d) </span>',
                                    $details->averageScoreOfClass,
                                    $details->classAverageDataRows
                                );
                            else :
                                $questionScore = '<span class="exp-std-noreply" style="float:right">No response yet</span>';
                            endif;
                            ?>
                            <?php echo $questionScore; ?>

                    </td>
                    <td style="width:18px;">
                        <?php
                        $title = "<table class='settingTopicOptions'>
                                <tr onclick='showQuestionDiv($topicId, " . $details->question_id. ", true);'>
                                    <td>
                                        <img src='" . base_url('assets/images/plus.png') ."' class='addTopic'>
                                    </td>
                                    <td >Duplicate</td>
                                </tr>
                                <tr onclick='showQuestionDiv($topicId, " . $details->question_id . ", false);'>
                                    <td>
                                        <img src='" . base_url('assets/images/lesson-edit-icon.png') ."' class='addTopic'>
                                    </td>
                                    <td>Edit</td>
                                </tr>";

                        if ($details->classAverageDataRows == 0) :
                            $title .= "<tr onclick='deleteQuestion(" . $topicId . ", " . $classId . ", " . $schoolId . ", " . $details->question_id . ", " . $parentTopicId . ");'>
                                    <td><img src='" . base_url('assets/images/lesson-del-icon.png') ."' class='addTopic'></td>
                                    <td>Delete</td>
                                </tr>";
                        endif;

                        $title .= "</table>";
                        ?>
                        <div class="less-exp-sett-box accordionOff"  style="float:right;">
                            <a class="topicSetting accordionOff"
                                title="<?php echo $title; ?>"
                             >
                                <span class="exp-setting-img accordionOff"></span>
                            </a>
                        </div>

                    </td>
                </tr>
            </table>

        </div>
    <?php endforeach; ?>
<?php endif; ?>
<div id="topicQuestionListCount-<?php echo $topicId; ?>"  count="<?php echo count($questionDetails); ?>"></div>


<script type='text/javascript'>

//Create the tooltips only when document ready
$(document).ready(function()
{
    $('.topicSetting').each(function() {
        $(this).qtip({
        	overwrite: true,
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
		   show: {
		   		//event: 'click'
		   },
            position: {
                my: 'top right',
                at: 'bottom center',
                viewport: $(window)

            },
            hide: {
                fixed: true,
                delay: 300
            }
        });
    });

});
</script>