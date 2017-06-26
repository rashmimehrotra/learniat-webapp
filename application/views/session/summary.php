<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $this->load->view('layouts/header.php'); ?>
<?php $this->load->view('layouts/menu.php'); ?>

<!-- Start right content -->
<div class="content-page">

<!-- ============================================================== -->
	<!-- Start Content here -->
	<!-- ============================================================== -->
	<div class="content">

		<!-- Heading Start -->
		<div class="heading_cont">

			<div class="">


                <div class="session-summary-left"><h2 class="sess-left">Sessions Summary</h2></div>
                <div class="session-summary-right">
                    <div class="sess-bar-right-content" style="margin-right:50px;float:right;">
                        <?php echo form_open(''); ?>
                        <table>
                            <tr>
                                <td>
                                    <span>Class:</span>
                                </td>
                                <td>
                                    <div class="myselect2">
                                        <?php
                                        $options = array(''  => 'All');
                                        foreach ($classesData AS $record) :
                                            $options[$record->class_id] = $record->class_name;
                                        endforeach;
                                        $attributes = array(
                                            'id' => 'sessionClassId',
                                            'onchange' => 'this.form.submit();',
                                            'style' => 'width:180px'
                                        );
                                        $sessionClassId = $this->input->post('sessionClassId');
                                        echo form_dropdown('sessionClassId', $options, $sessionClassId, $attributes);
                                        ?>
                                    </div>
                                </td>

                                <td>
                                    <span>Date:</span>
                                </td>
                                <td>
                                    <div class="myselect2">
                                        <?php
                                        $options = array(
                                            ''  => 'All',
                                            '7' => 'Last week',
                                            '14' => 'Last two week',
                                            '28' => 'Last four week',
                                        );

                                        $selectDuration = $this->input->post('sessionDuration');
                                        $attributes = array(
                                            'id' => 'sessionDuration',
                                            'onchange' => 'this.form.submit();',
                                            'style' => 'width:120px'
                                        );
                                        echo form_dropdown('sessionDuration', $options, $selectDuration, $attributes);
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
            <div class="clear"></div>
		</div>


		<!-- Heading Ends -->
		<!-- Accordion Start -->
		<div class="accordion_container">
		    <?php foreach ($sessionDates AS $recordKey => $record) :?>
    		    <?php
    		    	$keyDate = str_replace("/", "", $recordKey);
    		    	$sessionDate = str_replace("/", "-", $recordKey);
    		    	$res = explode("-", $sessionDate);
    		    	$sessionDate = $res[2]."-".$res[0]."-".$res[1];
    		    	$sessionProcess[$keyDate] = $sessionDate;
    		    ?>
		    	<div class="accordion_head"
                     id="<?php echo $keyDate; ?>"
                     date="<?php echo $sessionDate; ?>">
			        <span class="plusMinus" id="expand-plus"></span><?php echo $record[0]->day; ?> (<?php echo $recordKey; ?>)
			        <span class="session">Session<?php echo (count($record) > 1) ? 's' : ''; ?>:
			        	<?php echo count($record); ?>
			        </span>
			    </div>
			     <!-- Contant of accordion -->
			     <div class="accordion_body" id="" style="display: none;">
					<div style="width:100%">
						<?php foreach ($record AS $key => $classDetails) :?>
							<?php
								$sessionIdentity[$keyDate][$key] = $classDetails->class_session_id;
							?>
							<div id="session-<?php echo $keyDate . $classDetails->class_session_id; ?>" class="sess-expand-box">

							</div>
						<?php endforeach; ?>
					</div>
				</div>
			    <!-- end accordion content -->
			    <br/>
		    <?php endforeach; ?>
		</div>
		<div id='popupDivShort' class="popupDiv"></div>
		<div id='popupDivFull' class="popupDivIndex">
			<a class="close" onclick="popupClose('popupDivFull')">
				<img src="<?php echo base_url('assets/images/close.png');?>" align=right>
			</a>
			<div id="popUpDivContent" style=""></div>
		</div>
		<!-- Accordion Ends -->
	</div>
	<!-- ============================================================== -->
	<!-- End content here -->
	<!-- ============================================================== -->


</div>
<!-- End right content -->

<script>

    var sessionDetails = <?php echo json_encode($sessionIdentity); ?>;
    var sessionProcess = <?php echo json_encode($sessionProcess); ?>;
    var spinnerImage = '<br><div class="centerDiv"><img src="' + imagePath + 'spinner-1.gif"></div>';

    function makeAjaxCall(date, classSessionId, sessionDate)
    {
        var content = $.trim($( "#session-" + date + classSessionId ).html());
        if (content.length == 0) {
            $( "#session-" + date + classSessionId ).html(spinnerImage);
	        var urlParam = "sessionId=" + classSessionId + "&sessionDate=" + sessionDate;
			$.ajax({
				type: "get",
				url: "<?php echo base_url(); ?>index.php/session/summary/overview?" + urlParam,
				cache: true,
				success: function(response){
					try{
						$( "#session-" + date + classSessionId ).html(response);
					}catch(e) {
						alert('Exception while request..');
					}
				},
				error: function(xhr){
					validateAjaxError(xhr);
				}
			});
        }
	}

	function sessionClassDetails(sessionDate)
	{
		var sessionData = sessionDetails[sessionDate];
		var finalDate = sessionProcess[sessionDate];
		jQuery.each(sessionData, function(key, value) {
			makeAjaxCall(sessionDate, value, finalDate);
		})
	}

	$(document).ready(function(){
		//toggle the component with class accordion_body
		$(".accordion_head").click(function() {
			if ($('.accordion_body').is(':visible')) {
				$(".accordion_body").slideUp(600);
				$(".plusMinus").attr('id', 'expand-plus');
			}
			if (!$(this).next('.accordion_body').is(':visible')) {
    			$(this).next(".accordion_body").slideDown(600);
    			$(this).children(".plusMinus").attr('id', 'expand-minus');
    			sessionDate = $(this).attr('id');
    			sessionClassDetails(sessionDate);
			}
		});

        <?php if (!empty($lastSessionDate) && !empty($lastSessionId)) : ?>

            <?php $lastSessionDateId = date("mdY", strtotime($lastSessionDate)); ?>
            var lastSessionDateId = '<?php echo $lastSessionDateId; ?>';
            var lastSessionId = '<?php echo $lastSessionId; ?>';
            sessionClassDetails('<?php echo $lastSessionDateId; ?>');
            $('#' + lastSessionDateId).next(".accordion_body").slideDown(400);
            $('#' + lastSessionDateId).children(".plusMinus").attr('id', 'expand-minus');

            getFocus('session-' + lastSessionDateId + lastSessionId);
        <?php endif; ?>

        function getFocus(elementById) {
            $('html,body').animate({ scrollTop: $('#' + elementById).offset().top - 100 }, 500);
        }

        $('body').on('mouseover', '.lesson-progress-bar', function(){
	        var id =  $(this).attr('id').split('-');
	        var divContent = $('#lessonProgress'+id[1]).val();
	        var position = $(this).offset();
	        var left = position.left - 288;
	        var top = position.top - 150;
	        $('#popupDivShort').show();
	        $('#popupDivShort').css({top: top, left: left, position:'absolute'});
	        $('#popupDivShort').html(divContent);
	    });

	    $('body').on('mouseleave mouseout', '.lesson-progress-bar', function() {
	        $('#popupDivShort').hide();
	    });

		///person index
		$('body').on('mouseover', '[id^=participationPersonInfo-]', function() {
	  	  $(this).attr('class', 'participationPersonOver');
	    });

	    $('body').on('mouseleave mouseout', '[id^=participationPersonInfo-]', function() {
	    	$(this).attr('class', 'participationPerson');
	    });
	});
</script>

	<!-- Tool tip css and js -->


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
$(document).on('mouseover', '[id^=participationPersonInfo-]', function(event) {
    $(this).qtip({
        overwrite: false,
        style: {
	           tip: {
	               corner: true,
	               width: 15,
	               height: 10,
	               border: 1,
	               padding :10
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
  			viewport: $(window)
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
