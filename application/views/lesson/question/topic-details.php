<!-- Start right content -->
<div class="content-page">
    <!-- ============================================================== -->
    <!-- Start Content here -->
    <!-- ============================================================== -->
    <div class="content">

        <div class="error alert-box"></div>
        <div class="success alert-box"></div>
        <!-- Heading Start -->
        <div class="heading_cont" style="padding: 10px;">
            <div class="" style="padding: 2px 10px 10px 0px;">

                <table width="98%" style="vertical-align:middle;">
                    <tr>
                        <td style="width: 50px;" rowspan="1" class="back-arrow">

                            <?php
                            $uniqueId = $classId . '-' . $parentTopicData->topic_id;
                            $topicId = $parentTopicData->topic_id;
                            $urlParam = "?&lastClassId=$classId&lastSchoolId=$schoolId";
                            ?>
                            <a href="<?php echo site_url('lesson/topics/index') . $urlParam; ?>">
                                <button>
                                    <img src="<?php echo base_url('assets/images/back-arrow.png'); ?>">
                                </button>
                            </a>
                        </td>
                        <td class="checkboxTopicList" style="vertical-align:middle;">

                            <?php
                            $topicIntermediateList = array();
                            if ($otherDetails->subTopicTagged === TRUE) {
                                $topicIntermediateList[] = $uniqueId;
                            }
                            $data = array(
                                'name' => 'parentTopicName',
                                'id' => 'topicName-' . $uniqueId,
                                'checked' => ($otherDetails->topicTagged > 0) ? 'checked="checked"' : '',
                                'onclick' => "updateLessonTagged($classId, " . $parentTopicData->topic_id  . ", this.id, $schoolId, 'parentTopic');"
                            );

                            echo form_checkbox($data, $value = '', $checked = FALSE, 'class="accordionOff"');
                            ?>
                            <label class="accordionOff"
                                   style="vertical-align:middle;"
                                   for="<?php echo 'topicName-' . $uniqueId; ?>"></label>
                        </td>
                        <td class="titleTopicText" style="vertical-align:middle;">
                            <span class="less-subj-tittle"><?php echo $parentTopicData->topic_name; ?></span>
                            &nbsp;<span class="lesson-sub-cmt">(<?php echo $otherDetails->cumulativeTime; ?>)</span>
                            <?php
                                echo "<br>";
                                echo "<i><span style='font-size: 10px; margin-left:10px; color: #BDBDBD'>(".$parentTopicData->topic_id.")</span></i>";
                            ?>
                        </td>
                        <td style="width: 10%;"> <td>
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


                        </td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="7" >
                            <table width="100%">
                                <tr>
                                    <td colspan="1" class="lessonSubTopicsCount" style="width:150px;" >


                                        <span id="subTopicCount-<?php echo $uniqueId; ?>"
                                              count="<?php echo $otherDetails->subTopicCount; ?>">
                                            <?php echo ($otherDetails->subTopicCount > 0) ? $otherDetails->subTopicCount : 'No'; ?>
                                            <?php echo ($otherDetails->subTopicCount > 1) ? 'Subtopics' : 'Subtopic'; ?>
                                        </span>
                                        |
                                        <span id="topicQuestionCount-<?php echo $uniqueId; ?>"
                                            count="<?php echo $otherDetails->topicQuestionCount; ?>"
                                            class="topicQuestionCountClass-<?php echo $topicId; ?>"
                                            topicId="<?php echo $topicId; ?>"
                                            classId="<?php echo $classId; ?>">
                                            <?php echo ($otherDetails->topicQuestionCount > 0) ? $otherDetails->topicQuestionCount : 'No'; ?>
                                            <?php echo ($otherDetails->topicQuestionCount > 1) ? 'Questions' : 'Question'; ?>
                                        </span>


                                    </td>
                                    <td colspan="4">

                                        <div style="padding:2px;">
                                            <div>
                                                <span class="plan-student-avg" style="">Overall Progress</span>
                                                <span class="plan-student-avg" style="float:right;">
                                                    Progress:
                                                    <b><?php echo $otherDetails->progressPercentage; ?>%</b></span>
                                            </div>
                                            <div class="plan-slideBarBlue" style="width: 100%;">
                                                <span class="slideInnerBlue" style="width: <?php echo $otherDetails->progressPercentage; ?>%;"></span>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            </div>

            <div class="clear"></div>

        </div>
        <!-- Heading Ends -->
        <!-- Accordion Start -->
        <div class="accordion_container">

            <div style="width:100%; padding:10px;">

                <a id="addLink-<?php echo $parentTopicData->topic_id; ?>"
                   onclick="showTopicDiv(<?php echo $parentTopicData->topic_id; ?>)">
                    <span class="add-topic-span plusMinus" id="expand-plus"></span>
                    <span class="add-topic">Add new sub-topic</span>
                </a>

                <!-- Add topic -->
                <br>

                <div class="lessonParentTopicList"
                     id="addTopicDiv-<?php echo $parentTopicData->topic_id; ?>"
                     style="display:none;">
                    <table>
                        <tr>
                            <td>
                                <?php
                                echo form_input(array(
                                        'name' => 'subTopicName',
                                        'id' => 'subTopicName-' . $classId . '-' . $parentTopicData->topic_id
                                    ),
                                    '',
                                    'class="subtopic-box"'
                                );

                                echo form_button(
                                    "addSubTopic",
                                    "Add",
                                    'class="button-add" onclick="addSubTopicForQuestion(' . $parentTopicData->topic_id . ', ' . $classId . ', ' . $schoolId . ')"'
                                );

                                echo form_button(
                                    'cancel',
                                    'Cancel',
                                    'class="button-cancel" onclick="hideTopicDiv(' . $parentTopicData->topic_id . ')"'
                                );
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <br>
                <!-- Sub Topic list -->
                <div id="parentTopicSubTopicList"></div>
                <div id="subTopicInfo-<?php echo $parentTopicData->topic_id; ?>">
                    <?php
                    $data = array(
                        'parentTopicData' => $parentTopicData,
                        'topicData' => $subTopicData,
                        'schoolId' => $schoolId,
                        'classId' => $classId,
                        'parentTopicId' => $parentTopicId,
                    );
                    $this->load->view('lesson/question/sub-topic-list.php', $data);
                    ?>
                </div>
            </div>


            <!-- end accordion content -->
        </div>

        <!-- Accordion Ends -->
    </div>
    <!-- ============================================================== -->
    <!-- End content here -->
    <!-- ============================================================== -->

</div>

<?php
$attributes = array('name' => 'answerData', 'id' => 'answerData');
$url = site_url('lesson/questions/saveOption') ;
$url .=  "?parentTopicId=$parentTopicData->topic_id&classId=$classId&schoolId=$schoolId";
echo form_open_multipart($url, $attributes);
?>
<div id="modal_popup" ></div>
<?php echo form_close(); ?>
<div id="fade" class="black_overlay"></div>







<div class="progress-file">
    <div class="bar-file"></div >
    <div class="percent-file">Processing ... 0%</div >
</div>



<!-- End right content -->

<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.2.0.0.min.js'); ?>"></script>

<script>

    //callback handler for form submit
    $("#answerData").submit(function(e) {

        var validateFlag = validateQuestionData(e);

        if (validateFlag === false) {
            return false;
        }


        var postData = $(this).serialize() + "&save="+ $('#save').attr("value");
        var formURL = $(this).attr("action");
        var questionEditTopicId = $('#questionEditTopicId').attr("value");

        var bar = $('.bar-file');
        var percent = $('.percent-file');
        var parentTopicUniqueId = "<?php echo $classId; ?>-<?php echo $parentTopicData->topic_id; ?>";
        var totalParentTopicQuestionCount = parseInt($('#topicQuestionCount-' + parentTopicUniqueId).attr('count'));

        $.ajax(
            {
                url : formURL,
                type: "POST",
                data : postData,
                success:function(data, textStatus, jqXHR) {
                    //data: return data from server
                    var error = getParameterByName( 'error', data );
                    var success = getParameterByName( 'success', data );
                    if (error !== "") {
                        setErrorGrowlDiv(error);
                    } else if (success !== "")  {
                        setSuccessGrowlDiv(success);
                    } else {
                        setErrorGrowlDiv('Insufficient data provided.');
                    }

                    getQuestionRefresh(questionEditTopicId);
                    //Add question count + 1
                    questionCountStatistics((totalParentTopicQuestionCount + 1), parentTopicUniqueId);
                },
                beforeSend: function(){
                    // Handle the beforeSend event
                    var percentVal = '0%';
                    bar.width(percentVal)
                    percent.html(percentVal);

                    $("#modal_popup").html('');
                    $("#modal_popup").hide();
                    $(".progress-file").show();
                },
                complete: function(){
                    // Handle the complete event
                    bar.width("100%");
                    percent.html("100%");

                    $(".progress-file").hide();
                    $(".black_overlay").hide();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //if fails
                },

                //Work around #3
                xhr: function() {
                    var myXhr = new window.XMLHttpRequest();
                    if(myXhr.upload){
                        myXhr.upload.addEventListener('progress',showProgress, false);
                    } else {
                        console.log("Uploadress is not supported.");
                    }
                    return myXhr;
                }
            });
        e.preventDefault(); //STOP default action
        //e.unbind(); //unbind. to stop multiple form submit.
    });

    /**
     * Show progress bar
     * @param evt
     */
    function showProgress(evt) {
        if (evt.lengthComputable) {
            var percentComplete = (evt.loaded / evt.total) * 100;
            var percentVal = Math.round(percentComplete);
            percentVal += '%';
            $('.bar-file').width(percentVal);
            $('.percent-file').html(percentVal);

        }
    }

    /**
     * Get question data refresh
     **/
    function getQuestionRefresh(topicId)
    {
        var classId  = <?php echo $classId; ?>;
        var schoolId = <?php echo $schoolId; ?>;
        var parentTopicId = <?php echo $parentTopicData->topic_id; ?>;

        $("#topicQuestionReference-" + topicId).html('');
        getQuestionDetails(topicId, classId, schoolId, parentTopicId);

        var countQuestion = $("#topicQuestionListCount-" + topicId).attr('count');
        updateQuestionCountByTopicId(topicId, countQuestion);
    }

    $(document).ready(function(){
        //var h = window.innerHeight;
        //$(".black_overlay").css({"min-height": h});

        //toggle the component with class accordion_body
        $(".accordion_head").click(function(event) {
            if(!$(event.target).hasClass('accordionOff')) {
                if ($('.accordion_body').is(':visible')) {
                    $(".accordion_body").slideUp(600);
                    $(".plusMinus").attr('id', 'expand-plus');
                }
                if (!$(this).next('.accordion_body').is(':visible')) {
                    $(this).next(".accordion_body").slideDown(600);
                    $(this).find(".accordion-plusMinus").attr('id', 'expand-minus');
                }
            }
        });

        var topicIntermediateList = <?php echo json_encode($topicIntermediateList); ?>;
        $.each(topicIntermediateList, function(k, v) {
            $('#topicName-' + v).prop("indeterminate", true);
        });

        <?php if(isset($error) && !empty($error)) :?>
            setErrorMessage('<?php echo ucfirst($error); ?>');
        <?php endif; ?>

        <?php if(isset($success) && !empty($success)) :?>
            setSuccessMessage('<?php echo ucfirst($success); ?>');
        <?php endif; ?>

        <?php if(isset($lastSubTopicId) && !empty($lastSubTopicId)) :?>
            var lastSubTopicId = <?php echo $lastSubTopicId; ?>;
            var parentTopicId = <?php echo $parentTopicData->topic_id; ?>;
            getQuestionDetails(lastSubTopicId,  <?php echo $classId; ?>, <?php echo $schoolId; ?>, parentTopicId);
            $('#subTopicId-' + lastSubTopicId).next(".accordion_body").slideDown(400);
            $('#subTopicId-' + lastSubTopicId).find(".accordion-plusMinus").attr('id', 'expand-minus');
        <?php endif; ?>

    });


    /**
     * Load page without refresh
     * @param successMsg
     */
    function getSelfPageWithoutRedirect(successMsg)
    {
        var classId  = <?php echo $classId; ?>;
        var schoolId = <?php echo $schoolId; ?>;
        var parentTopicId = <?php echo $parentTopicData->topic_id; ?>;
        var urlParam = "parentTopicId=" + parentTopicId + "&classId=" + classId + "&schoolId=" + schoolId ;
        if (typeof successMsg !== 'undefined') {
            urlParam += '&success=' + successMsg;
        }

        $.ajax({
            type: "get",
            url: "<?php echo base_url(); ?>index.php/lesson/questions/parentTopicDetails?" + urlParam,
            cache: true,
            success: function(response) {
                $('#topicDetailsComplete' + parentTopicId).html(response)
            },
            error: function(xhr){
                validateAjaxError(xhr);
            }
        });
    }
</script>
<script type="text/javascript" src="<?php echo base_url('assets/js/common/topic.js');?>"></script>


<!-- CSS file -->
<link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/css/qtip/jquery.qtip.css');?>" />
<!-- jQuery FIRST i.e. before qTip (and all other scripts too usually) -->
<script type="text/javascript" src="<?php echo base_url('assets/js/qtip/jquery.min.js');?>"></script>
<!-- Include either the minifed or production version, NOT both!! -->
<script type="text/javascript" src="<?php echo base_url('assets/js/qtip/jquery.qtip.js');?>"></script>
<!-- Optional: imagesLoaded script to better support images inside your tooltips -->
<script type="text/javascript" src="<?php echo base_url('assets/js/qtip/imagesloaded.pkg.min.js');?>"></script>

<script type='text/javascript'>

    //Create the tooltips only when document ready
    $(document).ready(function()
    {
        // MAKE SURE YOUR SELECTOR MATCHES SOMETHING IN YOUR HTML!!!
        $('.participationPerson').each(function() {
            $(this).qtip({
                overwrite: false,
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
                position: {
                    my: 'bottom center',
                    at: 'top center',
                    adjust : {
                        screen : true,
                        resize: true
                    },
                    viewport: true
                },
                hide: {
                    fixed: true,
                    delay: 300
                }
            });
        });


        $('.topicSetting').each(function() {
            $(this).qtip({
                overwrite: false,
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
                    adjust : {
                        screen : false,
                        resize: false
                    },
                    viewport: true
                },
                hide: {
                    fixed: true,
                    delay: 300
                }
            });
        });

    });
</script>

<div id="dialog-confirm"></div>
<link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.css');?>">
<script src="<?php echo base_url('assets/js/jquery-ui.js');?>"></script>

