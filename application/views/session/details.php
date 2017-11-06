<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('form');
?>
<?php $this->load->view('layouts/header.php'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/css/session.css');?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/tabs.css');?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/graph.css');?>" />
<?php $this->load->view('layouts/menu.php'); ?>

<!-- Start right content -->
<div class="content-page">

<!-- ============================================================== -->
	<!-- Start Content here -->
	<!-- ============================================================== -->
	<div class="content">

		<!-- Heading Start -->
		<div class="heading_cont">

			<div class="sess-bar-left">

				<div class="">
					<div class="back-arrow">
                        <?php $params = "?lastSessionId=$sessionId&lastSessionDate=$sessionDate"; ?>
						<a href="<?php echo site_url('session/summary/index') . $params; ?>">
                            <button>
							    <img src="<?php echo base_url('assets/images/back-arrow.png'); ?>">
						    </button>
						</a>
	                    <h2 class="newh2"><?php echo $sessionDetails['class_name']; ?></h2>
                    </div>
                    <div class="clear"></div>
                    <span class="session-detail-span">

                    	<?php
		              	 $data = array(
		          	     	'sessionDetails' => $sessionDetails
		              	 );
		             	 $this->load->view('session/helper/session-information.php', $data);
		             	?>
                    </span>
                    <div class="clear"></div>
            	</div>

            </div>
            <div class="sess-bar-right">
            	<div style="margin-right:9%; width:75%; padding-top:2%; float: right;">
            		<div class = "sess-bar-right-content">
                    <!-- Right part -->
                  	<?php
                     $data = array(
                     	'studentData' => $sessionGraphIndex,
                        'showParticipate' => TRUE,
                        'averageCountText' => FALSE
                     );
                    ?>
                    	<div class="containerIndex">
                        	<?php $this->load->view('graph/participation-index-new.php', $data); ?>
                        </div>
                    </div>
            	</div>
            </div>
            <div class="clear"></div>
		</div>


		<!-- Heading Ends -->
        <!-- Tab Start -->
        <div class="mid_cont">
            <!-- tab "panes" -->
            <div id="tabs_wrapper">
                <div id="tabs_container">
                    <div style="width:300px;">
                        <ul id="tabs">
                            <li class="active"><a href="#tab1">Topics</a></li>
                            <li><a href="#tab2">Students</a></li>
                        </ul>
                    </div>
                    <div style="float:right;width:380px;margin-top:-28px;">

                        <table width="100%" id="sortStudentBy" cellpadding="10" style="display: none;">
                            <tr>
                                <td>
                                    Sort by:
                                    <div class="sortStudentPadding"></div>
                                </td>
                                <td>
                                    <span id="participation-index-<?php echo $sessionId; ?>"
                                          class="cursorPointer getStudentDetails"
                                          sort="participation-index"
                                          sessionId="<?php echo $sessionId; ?>">
                                        Participation Index
                                    </span>
                                    <div id="participation-index-sort" class="dropDownSortBy">
                                        <span class="dropDownAscSortBy"></span>
                                    </div>
                                    <div class="sortStudentPadding sortStudentRedBorder"></div>
                                </td>
                                <td>
                                    <span id="grasp-index-<?php echo $sessionId; ?>"
                                          class="cursorPointer getStudentDetails"
                                          sort="grasp-index"
                                          sessionId="<?php echo $sessionId; ?>">
                                        Grasp Index
                                    </span>
                                    <div id="grasp-index-sort" class="dropDownSortBy"></div>
                                    <div class="sortStudentPadding"></div>
                                </td>
                                <td>
                                    <span id="first-name-<?php echo $sessionId; ?>"
                                          class="cursorPointer getStudentDetails"
                                          sort="first-name"
                                          sessionId="<?php echo $sessionId; ?>">
                                        ABC
                                    </span>
                                    <div id="first-name-sort" class="dropDownSortBy"></div>
                                    <div class="sortStudentPadding"></div>
                                </td>
                            </tr>
                        </table>


	                </div>
                </div>

                <div id="tabs_content_container">
                    <div id="tab1" class="tab_content" style="display: block;">
                        <p>
                        <div class="accordion_container">
                        	<input type="hidden" name="sessionId" id = "sessionId" value="<?php echo $sessionId; ?>">
                            <?php foreach ($topicList AS $topicData): ?>
                            <div class="accordion_head accordion_head_query"
                                id="<?php echo $topicData->topic_id; ?>">
                                <div class="containerAccordTitle">
                                    <div class="rowAccordTitle">

                                      	<div class="leftAccordTitle">
                                      		<span class="plusMinus-query plusMinus plusMinus-<?php echo $topicData->topic_id; ?>"
                                      		id="expand-plus"></span>
                                            <?php echo $topicData->topic_name; ?>
                                            <span style="padding-left: 10px;" class="subtopic-heading">
                                            (<?php echo $topicData->parent_topic_name; ?>)</span>
                                      	</div>

                                      	<div class="middleAccordTitle">
                                      	    <?php
                                      	         $graphIndexData = $graphIndexAllData[$topicData->topic_id];
                                      	         $data = array(
                                  	                 'studentData' => $graphIndexData,
                                  	                 'showParticipate' => TRUE,
              	     								 'averageCountText' => FALSE
                                      	         );
                                      	    ?>
                                      		<div class="containerIndex">
                                      		    <?php $this->load->view('graph/participation-index-new.php', $data); ?>
                                      		</div>
                                      	</div>

                                      	<div class="rightAccordTitle">
                                      	    <div class="containerIndex">
                                        	   <?php $this->load->view('graph/grasp-index-new.php', $data); ?>
                                        	</div>
                                      	</div>

                                	</div>
                                </div>
                            </div>

                            <div class="accordion_body accordion_body_query" style="display: none;">
                                <div class="switch_top">
                                    <div class="topic-exp-top">
                                    	<span class="switchActive topic-exp-quer" id="tabQuery-1<?php echo $topicData->topic_id; ?>">
                                    	    <a class="link-topic-tab topic-exp-active" href="javascript:void(0);">
                                    	   		Queries (<?php echo $topicData->topic_query_count; ?>)
                                    	   	</a>
                                    	</span>
                                    	<span class="topic-top-breaker">|</span>
                                    	<span class="switchActive topic-exp-numCount" id="tabQuery-2<?php echo $topicData->topic_id; ?>">
                                    	   <a class="link-topic-tab" href="javascript:void(0);">
                                    	   		Questions (<?php echo $topicData->topic_question_count; ?>)
                                    	   </a>
                                    	</span>
                                    </div>
                                </div>
                                <div class="switch_question_cnt" id="success">
                                    <!-- Filled by jquery method-->
                                    <div style="display:block;" id="tabQueryDetails-1<?php echo $topicData->topic_id; ?>">

                                    </div>
                                    <!-- Filled by jquery method-->
                                    <div style="display:none;"  id="tabQueryDetails-2<?php echo $topicData->topic_id; ?>">

                                    </div>
                                </div>
                            </div>
                            <br/>
                            <?php endforeach; ?>

                        </div>
                        </p>
                    </div>
                    <div id="tab2" class="tab_content">
                        <p>

                            <?php
                            	if (!empty($studentData['studentData'])) :

	                                $maxParticipationIndex = (!empty($studentData['maxParticipationIndex'])) ? $studentData['maxParticipationIndex'] : 0;
	                                $maxGraspIndex =  (!empty($studentData['maxGraspIndex'])) ? $studentData['maxGraspIndex'] : 0;
	                                $data =  array(
	                                    'studentData' => $studentData['studentData'],
	                                    'maxParticipationIndex' => $maxParticipationIndex,
	                                	'maxGraspIndex' => $maxGraspIndex,
	                                	'sessionId' => $sessionId
	                                );
                            ?>
                            	<?php $this->load->view('session/student-list.php', $data); ?>
                            <?php else: ?>
                            	<div style="padding:15px">No Record found</div>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <!-- Accordion Start -->
            <!-- Accordion Ends -->
        </div>
		<div class='popupDiv' style=""></div>
		<!-- Accordion Ends -->

	</div>
	<!-- ============================================================== -->
	<!-- End content here -->
	<!-- ============================================================== -->
</div>

<div id='popupDivShort' class="popupDiv"></div>
<div id='popupDivFull' class="popupDivIndex">
	<a class="close" onclick="popupClose('popupDivFull')">
		<img src="<?php echo base_url('assets/images/close.png');?>" align=right>
	</a>
	<div id="popUpDivContent" style=""></div>
</div>

<?php $this->load->view('session/js.php'); ?>
<script type="text/javascript">


    $('.getStudentDetails').click(function(event) {
        var sortBy = $(this).attr('sort');
        var sessionId = $(this).attr('sessionId');
        var nextImage = $.trim($(this).next().html());
        $('.sortStudentPadding').attr('class', 'sortStudentPadding');
        $('.dropDownSortBy').html('');
        $(this).next().next().attr('class', 'sortStudentPadding sortStudentRedBorder');

        if (nextImage === "") {
            sortBy += '';
            $(this).next().html('<span class="dropDownAscSortBy"></span>');
        } else if (nextImage.indexOf('dropDownDescSortBy') >= 0) {
            sortBy = sortBy.replace('-desc', '');
            $(this).next().html('<span class="dropDownAscSortBy"></span>');
        }  else if (nextImage.indexOf('dropDownAscSortBy') >= 0) {
            sortBy += '-desc';
            $(this).next().html('<span class="dropDownDescSortBy"></span>');
        }

        getStudentSortDetails(sessionId, sortBy);
        $(this).attr('sort', sortBy);

    });

    $(".link-topic-tab").click(function() {
        $(".link-topic-tab").attr("class", "link-topic-tab");
        $(this).attr("class", "link-topic-tab topic-exp-active");
    });

    /* <![CDATA[ */
    $(document).ready(function(){
        $("#tabs li").click(function() {
            //	First remove class "active" from currently active tab
            $("#tabs li").removeClass('active');

            //	Now add class "active" to the selected/clicked tab
            $(this).addClass("active");

            //	Hide all tab content
            $(".tab_content").hide();

            //	Here we get the href value of the selected tab
            var selected_tab = $(this).find("a").attr("href");

            if (selected_tab === '#tab2') {
                $('#sortStudentBy').show();
            } else {
                $('#sortStudentBy').hide();
            }

            //	Show the selected tab content
            $(selected_tab).fadeIn();

            //	At the end, we add return false so that the click on the link is not executed
            return false;
        });
    });
    /* ]]> */

    //toggle the component with class accordion_body
    $(".accordion_head_query").bind( "click", function() {
    //$(".accordion_head_query").click(function() {
        if ($('.accordion_body_query').is(':visible')) {
            $(".accordion_body_query").slideUp(300);
            $(".plusMinus-query").attr('id', 'expand-plus');
        }
        if (!$(this).next('.accordion_body_query').is(':visible')) {
            $(this).next(".accordion_body_query").slideDown(300);
            var thisId = $(this).attr('id');
            $(".plusMinus-" + thisId).attr('id', 'expand-minus');
            topicId = $(this).attr('id');

            getTopicQueryDetails(topicId);
            getTopicQuestionDetails(topicId);
        }
    });


	$('body').on('mouseover', '[id^=participationPersonInfo-]', function() {
  	  $(this).attr('class', 'participationPersonOver');
    });

    $('body').on('mouseout mouseleave', '[id^=participationPersonInfo-]', function() {
    	$(this).attr('class', 'participationPerson');
    });
</script>



<link rel="stylesheet" href="<?php echo base_url('assets/css/tooltips.css');?>" />

<div id="modal_popup_no_bg" ></div>
<div id="fade" class="black_overlay"></div>



<?php $this->load->view('layouts/footer.php'); ?>


<!-- CSS file -->
<link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/css/qtip/jquery.qtip.css');?>" />

<!-- jQuery FIRST i.e. before qTip (and all other scripts too usually) -->
<script type="text/javascript" src="<?php echo base_url('assets/js/qtip/jquery.min.js');?>"></script>

<!-- Include either the minifed or production version, NOT both!! -->
<script type="text/javascript" src="<?php echo base_url('assets/js/qtip/jquery.qtip.js');?>"></script>

<!-- Optional: imagesLoaded script to better support images inside your tooltips -->
<script type="text/javascript" src="<?php echo base_url('assets/js/qtip/imagesloaded.pkg.min.js');?>"></script>


<script type='text/javascript'>

$("[id^=tabQuery]").click( function() {
	var topicIdData = $(this).attr('id').split('-');
	var topicId = topicIdData[1];
	$("[id^=tabQueryDetails]").attr('style', 'display:none;');
	$("#tabQueryDetails-" + topicId).attr('style', 'display:block;');
});

//Create the tooltips only when document ready
$(document).ready(function()
{
    // MAKE SURE YOUR SELECTOR MATCHES SOMETHING IN YOUR HTML!!!
    $('.participationPerson').each(function() {
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

});


$(document).on('mouseover', '.evaluated-view', function(event) {
    $(this).qtip({
        overwrite: true,
        style: {
            tip: {
                corner: true,
                mimic: 'center',
                offset: 5 ,//Shift corner ---position
                width: 15,
                height: 10,
                border: 1,
                padding :10
            },
            classes: 'qtip-bootstrap'
		   },
        position: {
            my: 'top right',
            at: 'bottom right',
            adjust : {
  				 screen : true,
  				resize: true
  			},
 			viewport: true
  		},
     	hide: {
     		fixed: true,
            delay: 300
     	},
        show: {
            event: event.type,
            ready: true
        }
    }, event);
})
.each(function(i) {
    $.attr(this, 'oldtitle', $.attr(this, 'title'));
    this.removeAttribute('title');
});


</script>
<script type="text/javascript" src="<?php echo base_url('assets/js/common/session.js?v=20151010');?>"></script>
 <!-- Query view result tooltip css -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/thumbnail/styles.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/thumbnail/stylesheet.css'); ?>">
    
    
    
   