<div id="completeLessonPlan">

    <?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <?php $this->load->view('layouts/header.php'); ?>
    <?php $this->load->view('layouts/menu.php', array('selectedLink' => 'Lesson Plan')); ?>
    <link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/graph.css');?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/lesson.css');?>">
    <!-- Start right content -->
        <div class="content-page">
            <!-- ============================================================== -->
            <!-- Start Content here -->
            <!-- ============================================================== -->
            <div class="content">

                <div class="error alert-box"></div>
                <div class="success alert-box"></div>
                <!-- Heading Start -->
                <div class="heading_cont" style="padding: 19px;">
                    <div class="lesson-bar-left">
                        <h2 class="sess-left">Lesson Plan</h2>
                    </div>
                    <div class="lesson-bar-right">
                        <div class = "sess-bar-right-content" style="vertical-align: middle;">

                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <!-- Heading Ends -->
                <!-- Accordion Start -->
                <div class="accordion_container">

                    <?php if (empty($classesList)) : ?>
                        No Record founds
                    <?php else : ?>
                        <?php foreach ($classesList AS $key => $classesDetails) : ?>
                                <div class="accordion_head"
                                    id="classId-<?php echo $classesDetails->class_id; ?>"
                                    onclick="getClassDetails(<?php echo $classesDetails->class_id; ?>, <?php echo $classesDetails->school_id; ?>);"
                                    >
                                    <table width="98%">
                                        <tr>
                                            <td style="width:38%;">
                                                <?php echo $classesDetails->class_name; ?>
                                                <span class="plusMinus accordion-plusMinus" id="expand-plus"></span>
                                            </td>
                                            <td style="text-align:right; width:60%;">
                                                <div style="padding:2px;">
                                                     <div>
                                                        <span class="plan-student-avg" style="">Overall Progress</span>
                                                        <span class="plan-student-avg" style="float:right;">
                                                            Progress: <b><?php echo $classesDetails->progressPercentage; ?>%</b>
                                                        </span>
                                                     </div>
                                                    <div class="plan-slideBarBlue" style="width: 100%;">
                                                        <span class="slideInnerBlue"
                                                              style="width: <?php echo $classesDetails->progressPercentage; ?>%;"></span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                </div>
                                <!-- Content of accordion -->
                                <div class="accordion_body" id="" style="display: none;">
                                    <div class="accordion_padding">

                                        <!-- Add topic -->

                                        <div class="plan-subque-box" onclick="showTopicDiv(<?php echo $classesDetails->class_id; ?>)">
                                            <a>
                                            <img width="14" height="14"
                                                 class="cursorPointer"
                                                 id="addLink-<?php echo $classesDetails->class_id; ?>"
                                                src="<?php echo base_url('assets/images/expand-button.png'); ?>" alt="expand-img">
                                            <span class="add-topic-link">Add new topic</span>
                                            </a>
                                        </div>


                                        <br/>

                                        <div class="lessonParentTopicList"
                                             id="addTopicDiv-<?php echo $classesDetails->class_id; ?>"
                                             style="display:none;">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <?php
                                                            echo form_input(
                                                                array(
                                                                    'name' => 'topicName',
                                                                    'id' => 'addTopicName-' . $classesDetails->class_id
                                                                ),
                                                                '',
                                                                'class="subtopic-box"'
                                                                );

                                                            echo form_button(
                                                                    "addTopic",
                                                                    "Add",
                                                                    'class="button-add" onclick="addTopic(' .$classesDetails->class_id . ', ' . $classesDetails->school_id . ')"'
                                                                );
                                                            echo form_button(
                                                                    "cancel",
                                                                    "Cancel",
                                                                    'class="button-cancel" onclick="hideTopicDiv(' . $classesDetails->class_id . ')"'
                                                                );
                                                        ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>


                                       <!-- Topic list -->
                                       <div id="classInfo-<?php echo $classesDetails->class_id; ?>"></div>

                                         <br/>
                                    </div>
                                <!-- End accordion_body -->
                                </div>

                                 <br/>
                        <?php endforeach; ?>
                    <?php endif; ?>


                <!-- end accordion content -->
                </div>

                <!-- Accordion Ends -->
            </div>
            <!-- ============================================================== -->
            <!-- End content here -->
            <!-- ============================================================== -->
        </div>
    <!-- End right content -->
    <script type="text/javascript" src="<?php echo base_url('assets/js/qtip/jquery.min.js');?>"></script>
    <script>
        var spinnerImage = '<br><div class="centerDiv"><img src="' + imagePath + 'spinner-1.gif"></div>';
        $(document).ready(function(){
            //toggle the component with class accordion_body
            $(".accordion_head").click(function(event) {

                if ($('.accordion_body').is(':visible')) {
                    $(".accordion_body").slideUp(400);
                    $(".plusMinus").attr('id', 'expand-plus');
                }
                if (!$(this).next('.accordion_body').is(':visible')) {
                    $(this).next(".accordion_body").slideDown(400);
                    $(this).find(".accordion-plusMinus").attr('id', 'expand-minus');
                }
            });
        });

        /**
         * Get class details
         * @param integer classId
         * @param integer schoolId
         */
        function getClassDetails(classId, schoolId)
        {
            var content = $.trim($( "#classInfo-" + classId ).html());
            if (content.length == 0) {
                $( "#classInfo-" + classId ).html(spinnerImage);
                var urlParam = "classId=" + classId + "&schoolId=" + schoolId;
                $.ajax({
                    type: "get",
                    url: "<?php echo base_url(); ?>index.php/lesson/index/view?" + urlParam,
                    cache: true,
                    success: function(response){
                        try{
                            $( "#classInfo-" + classId ).html(response);
                        }catch(e) {
                            //alert('Exception while request..');
                        }
                    },
                    error: function(xhr){
                        validateAjaxError(xhr);
                    }
                });
            }
        }

        function showTopicDiv(id)
        {
            $('#addLink-' + id).attr('src', '<?php echo base_url('assets/images/minus.png'); ?>');
            $('#addLink-' + id).attr('height', '4');
            $('#addTopicDiv-' + id).show();
        }
        function hideTopicDiv(id)
        {
            $('#addLink-' + id).attr('src', '<?php echo base_url('assets/images/expand-button.png'); ?>');
            $('#addLink-' + id).attr('height', '14');
            $('#addTopicDiv-' + id).hide();
        }


        function getSelfPage(classId, lastParentTopicId, schoolId, lastSubTopicId)
        {
            var urlParam = "lastParentTopicId=" + lastParentTopicId + "&classId=" + classId + "&schoolId=" + schoolId ;
            if (typeof lastSubTopicId !== 'undefined') {
                // variable is undefined
                urlParam = urlParam + "&lastSubTopicId=" + lastSubTopicId ;
            }

            var url = "<?php echo base_url(); ?>index.php/lesson/topics/index?" + urlParam;
            window.location.href = url;
        }

        <?php if (isset($lastClassId)) : ?>
            $(document).ready(function() {
                var lastClassId = <?php echo $lastClassId; ?>;
                var lastSchoolId = <?php echo $lastSchoolId; ?>;
                var lastParentTopicId = <?php echo $lastParentTopicId; ?>;
                var lastSubTopicId = <?php echo $lastSubTopicId; ?>;

                getClassDetailsWithLastData(lastClassId, lastSchoolId, lastParentTopicId, lastSubTopicId);
                $('#classId-' + lastClassId).next(".accordion_body").slideDown(400);
                $('#classId-' + lastClassId).find(".accordion-plusMinus").attr('id', 'expand-minus');
            });
        <?php endif;?>



        $('body').on('mouseover', '[id^=participationPersonInfo-]', function() {
            $(this).attr('class', 'participationPerson participationPersonOver');
        });

        $('body').on('mouseleave mouseout', '[id^=participationPersonInfo-]', function() {
            $(this).attr('class', 'participationPerson');
        });

        $(document).on("click",".accordion_sub_head",function(event) {
            if(!$(event.target).hasClass('accordionOff')) {
                if ($('.accordion_sub_body').is(':visible')) {
                    $(".accordion_sub_body").slideUp(400);
                    $(".plus-minus-sub").attr('id', 'expand-plus');
                }
                if (!$(this).next('.accordion_sub_body').is(':visible')) {
                    $(this).next(".accordion_sub_body").slideDown(400);
                    $(this).find(".accordion-plusMinus").attr('id', 'expand-minus');
                }
            }
        });
    </script>
    <?php $this->load->view('layouts/footer.php'); ?>

    <div id='popupDivShort' class="popupDiv"></div>
    <div id='popupDivFull' class="popupDivIndex">
        <a class="close" onclick="popupClose('popupDivFull')">
            <img src="<?php echo base_url('assets/images/close.png');?>" align=right>
        </a>
        <div id="popUpDivContent" style=""></div>
    </div>

    <script type="text/javascript" src="<?php echo base_url('assets/js/common/lesson.js');?>?v=123"></script>
    <!-- CSS file -->
    <link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/css/qtip/jquery.qtip.css');?>" />

    <!-- jQuery FIRST i.e. before qTip (and all other scripts too usually) -->
    <script type="text/javascript" src="<?php echo base_url('assets/js/qtip/jquery.min.js');?>"></script>

    <!-- Include either the minifed or production version, NOT both!! -->
    <script type="text/javascript" src="<?php echo base_url('assets/js/qtip/jquery.qtip.js');?>"></script>

    <!-- Optional: imagesLoaded script to better support images inside your tooltips -->
    <script type="text/javascript" src="<?php echo base_url('assets/js/qtip/imagesloaded.pkg.min.js');?>"></script>



    <div id="dialog-confirm"></div>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.css');?>">
    <script src="<?php echo base_url('assets/js/jquery-ui.js');?>"></script>


</div>