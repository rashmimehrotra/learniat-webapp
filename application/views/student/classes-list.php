<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('layouts/header.php'); ?>
<?php $this->load->view('layouts/menu.php', array('selectedLink' => 'Students')); ?>
<link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/css/student-list.css');?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/tabs.css');?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/graph.css');?>" />

<!-- Start right content -->
	<div class="content-page">
	    <!-- ============================================================== -->
	    <!-- Start Content here -->
	    <!-- ============================================================== -->
	    <div class="content">
	        <!-- Heading Start -->
	        <div class="heading_cont" style="padding: 19px;">
	            <div class="lesson-bar-left">
	                <h2 class="sess-left">Students</h2>
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
                                 onclick="showStudentDetails(<?php echo $classesDetails->class_id; ?>)"
				        		id="classId-<?php echo $classesDetails->class_id; ?>"
				        		>
				        		<table width="98%">
			                        <tr>
			                            <td style="width:38%;">
			                            	<?php echo $classesDetails->class_name; ?>
			                            	<span class="plusMinus accordion-plusMinus" id="expand-plus"></span>
			                            </td>
			                            
			                        </tr>
			                    </table>
				        		
				            </div>
				            <!-- Constant of accordion -->
				            <div class="accordion_body" id="" style="display: none;">
				                <div class="accordion_padding">
				                
				                	<!-- Add topic -->

					                <div class="plan-subque-box" >

                                        <div class="topic-exp-top">
                                            <div style="float:right;width:380px;">

                                                <table width="100%" id="sortStudentBy" cellpadding="10"
                                                       style="display: block; padding-bottom:7px">
                                                    <tr>
                                                        <td>
                                                            Sort by:
                                                            <div class="sortStudentListPadding"></div>
                                                        </td>
                                                        <td>
                                                            <span id="participation-index-<?php echo $classesDetails->class_id; ?>"
                                                                  class="cursorPointer getStudentDetails"
                                                                  sort="participation-index"
                                                                  classId="<?php echo $classesDetails->class_id; ?>">
                                                                Participation Index
                                                            </span>
                                                            <div id="participation-index-sort" class="dropDownSortBy">
                                                                <span class="dropDownAscSortBy"></span>
                                                            </div>

                                                            <div class="sortStudentListPadding sortStudentRedBorder"></div>

                                                        </td>
                                                        <td>
                                                            <span id="grasp-index-<?php echo $classesDetails->class_id; ?>"
                                                                  class="cursorPointer getStudentDetails"
                                                                  sort="grasp-index"
                                                                  classId="<?php echo $classesDetails->class_id; ?>">
                                                                Grasp Index
                                                            </span>
                                                            <div id="grasp-index-sort" class="dropDownSortBy"></div>
                                                            <div class="sortStudentListPadding"></div>
                                                        </td>
                                                        <td>
                                                            <span id="first-name-<?php echo $classesDetails->class_id; ?>"
                                                                  class="cursorPointer getStudentDetails"
                                                                  sort="first-name"
                                                                  classId="<?php echo $classesDetails->class_id; ?>">
                                                                ABC
                                                            </span>
                                                            <div id="first-name-sort" class="dropDownSortBy"></div>
                                                            <div class="sortStudentListPadding"></div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

									</div>
				                   <!-- Topic list -->

                                    <div id="classInfo-<?php echo $classesDetails->class_id; ?>"
                                         hiddenStudent="0"></div>

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

<?php $this->load->view('layouts/footer.php'); ?>
<script>
    $(document).ready(function(){
    	//toggle the component with class accordion_body
    	$(".accordion_head").click(function(event) {

            hideAllHiddenStudentDiv();
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
     * Get student details
     * @param integer classId
     */
    function showStudentDetails(classId, sortBy)
    {
        var content = $.trim($( "#classInfo-" + classId).html());
        if (content.length == 0) {

            getStudentDetails(classId, sortBy);
        }
    }


    $('.getStudentDetails').click(function(event) {
        var classId = $(this).attr('classId');
        var sortBy = $(this).attr('sort');
        var nextImage = $.trim($(this).next().html());
        $('.sortStudentListPadding').attr('class', 'sortStudentListPadding');
        $('.dropDownSortBy').html('');
        $(this).next().next().attr('class', 'sortStudentListPadding sortStudentRedBorder');

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

        getStudentDetails(classId, sortBy);
        $(this).attr('sort', sortBy);

    });

    function getStudentDetails(classId, sortBy)
    {
        if (typeof sortBy === 'undefined') {
            sortBy = 'participation-index';
        }
        var classDiv = '#classInfo-' + classId;
        var hiddenStudent = $(classDiv).attr('hiddenStudent');

        $.ajax({
            type: "get",
            url: "<?php echo base_url(); ?>index.php/student/index/view?classId=" + classId + "&sortBy=" + sortBy + "&hiddenStudent=" + hiddenStudent ,
            cache: true,
            success: function(response){
                try{
                    $(classDiv).html(response);
                }catch(e) {
                    //alert('Exception while request..');
                }
            },
            error: function(){
                //alert('Error while request..');
            }
        });
    }

    /**
     * Show hidden student div
     * @param integer classId
     */
    function showHiddenStudentDiv(classId)
    {
        $('#hiddenStudentLink-' + classId).hide();
        $('#hiddenStudentDiv-' + classId).show();
        var classDiv = '#classInfo-' + classId;
        var hiddenStudent = $(classDiv).attr('hiddenStudent');
        hiddenStudent = ((hiddenStudent == 0) ? 1 : 0);
        $(classDiv).attr('hiddenStudent', hiddenStudent);
    }

    /**
     * Hide hidden student div
     * @param integer classId
     */
    function hideAllHiddenStudentDiv()
    {
        $('[id^=hiddenStudentLink]').show();
        $('[id^=hiddenStudentDiv]').hide();
        //var hiddenStudent = $(classDiv).attr('hiddenStudent');
        //hiddenStudent = ((hiddenStudent == 0) ? 1 : 0);
        $('[id^=classInfo-]').attr('hiddenStudent', 0);
    }

</script>