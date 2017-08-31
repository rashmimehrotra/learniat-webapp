 <!-- Sub Topic list -->
<?php foreach ($topicData AS $key => $topicDetails) :
	$otherDetails = $topicDetails->otherDetails;
	$topicId = $topicDetails->topic_id;
    $uniqueId = $classId . '-' . $topicId;
?>
    <div id="topicCompleteDiv-<?php echo $uniqueId; ?>">
        <div class="accordion_head"
             id="subTopicId-<?php echo $topicId; ?>"
             onclick="getQuestionDetails(<?php echo $topicId; ?>, <?php echo $classId; ?>, <?php echo $schoolId; ?>, <?php echo $parentTopicId; ?>)">
            <table width="98%">
                <tr>
                    <td style="width: 20px;" rowspan="1">
                        <span class="plusMinus accordion-plusMinus" id="expand-plus"></span>
                    </td>
                    <td class="accordionOff checkboxTopicList">
                        <?php
                        $data = array(
                            'name' => 'topicName',
                            'id' => 'topicName-' . $uniqueId,
                            'checked' => ($otherDetails->topicTagged > 0) ? 'checked="checked"' : ''
                            );
                        $data['onclick'] = "updateLessonTagged($classId, $topicId, this.id, $schoolId, 'subParentTopic', $parentTopicId);";
                        $extra = "class='linkToParent-$classId-$parentTopicId  accordionOff'";
                        echo form_checkbox($data, $value = '', $checked = FALSE,  $extra);
                        ?>
                        <label for="<?php echo 'topicName-' . $uniqueId; ?>"
                               class="accordionOff cursorPointer"
                               style="vertical-align: middle"></label>
                    </td>
                    <td class="titleTopicText">
                        <span class="less-subj-tittle">
                            <a id="editTopicLink-<?php echo $uniqueId; ?>"
                                class="less-subj-tittle">
                                <?php echo $topicDetails->topic_name; ?>
                            </a>
                        </span>

                        <?php
                        $classTopicInput = 'subtopic-box accordionOff displayNone editTopicTextBoxClass-' . $topicId;
                        echo form_input(
                            array(
                                'name' => 'topicName',
                                'id' => "editTopicTextBox-$uniqueId"
                            ),
                            $topicDetails->topic_name,
                            'class="' . $classTopicInput . '" onblur="updateTopic(' . $classId . ',' . $topicId . ');"'
                        );
                        ?>
                        &nbsp;<span class="lesson-sub-cmt" style="font-weight:400">(<?php echo $otherDetails->cumulativeTime; ?>)</span>
                                <?php
                                echo "<br>";
                                echo "<i><span style='font-size: 10px; margin-left:10px; color: #BDBDBD'>(".$topicDetails->topic_id.")</span></i>";
                                ?>
                    </td>

                    <td style="width: 11%;vertical-align:top;"> <td>
                    <td style="width: 30%;">

                        <?php
                        $graphIndexData = $otherDetails->indexData;
                        $data = array(
                            'sessionId' => 0,
                            'studentData' => $graphIndexData,
                            'showParticipate' => TRUE,
                            'averageCountText' => FALSE
                        );
                        ?>
                        <div class="containerIndex">
                            <?php $this->load->view('graph/grasp-index-new.php', $data); ?>
                        </div>

                    </td>
                    <td style="width:50px;">
                        <?php
                        $title = "<table class='settingTopicOptions'>
                                <tr onclick='inlineTopicEdit($classId, $topicId)'
                                    id='editTopic-$uniqueId'>
                                    <td><img src='" . base_url('assets/images/lesson-edit-icon.png') ."' class='addTopic'></td>
                                    <td>Edit</td>
                                </tr>
                                <tr onclick='removeSubTopicId($classId, $topicId, $parentTopicId)'>
                                    <td><img src='" . base_url('assets/images/lesson-del-icon.png') ."' class='addTopic'></td>
                                    <td>Delete</td>
                                </tr>
                            </table>";
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
                <tr>
                    <td> </td>
                    <td colspan="8" >
                        <table width="100%">
                            <tr>
                                <td colspan="2" class="lessonSubTopicsCount"  style="width: 7%;">

                                    <span id="topicQuestionCount-<?php echo $uniqueId; ?>"
                                          count="<?php echo $otherDetails->topicQuestionCount; ?>"
                                          class="topicQuestionCountClass-<?php echo $topicId; ?>"
                                          topicId="<?php echo $topicId; ?>"
                                          parentTopicId="<?php echo $parentTopicId; ?>"
                                          classId="<?php echo $classId; ?>"
                                        >
                                        <?php echo ($otherDetails->topicQuestionCount > 0) ? $otherDetails->topicQuestionCount : 'No'; ?>
                                        <?php echo ($otherDetails->topicQuestionCount > 1) ? 'Questions' : 'Question'; ?>
                                    </span>
                                </td>
                                <td colspan="6">
                                    <div style="padding:2px;">
                                         <div>
                                            <span class="plan-student-avg" style="">Overall Progress</span>
                                                <span class="plan-student-avg" style="float:right;">
                                                Progress: <b><?php echo $otherDetails->progressPercentage; ?>%</b>
                                                </span>
                                         </div>
                                        <div class="plan-slideBarBlue"
                                             style="width: 100%;">
                                            <span class="slideInnerBlue"
                                                  style="width:<?php echo $otherDetails->progressPercentage; ?>%;">
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        </table>

                    </td>
                </tr>
            </table>
        </div>

        <div class="accordion_body accordion_body_query"
             style="display: none;">
            <div style="width:100%;padding-bottom:2px;">

                <!-- Add question start -->
                <div id="showtopic" class="plan-newtopic-bar1">
                    <div class="plan-subque-box" onclick="showQuestionDiv(<?php echo $topicId; ?>, null, false);">
                        <img width="12" height="12"
                             id="addQuestionLink-<?php echo $topicId; ?>"
                             class="cursorPointer"
                                src="<?php echo base_url('assets/images/expand-button.png'); ?>" alt="expand-img">
                        <span class="plan-subque-text cursorPointer">Add new question</span>
                    </div>
                </div>
                <!-- Add question end -->

                <div id="topicQuestionReference-<?php echo $topicId; ?>"></div>
            </div>
        </div>
        <br>
	</div>
<?php endforeach; ?>
 <script>
 $('input[id^="editTopicTextBox-"]').keypress(function (e) {
     var currentId = $(this).attr('id');
     var key = e.which;
     processEnterEvent(currentId, key);
 });
 </script>