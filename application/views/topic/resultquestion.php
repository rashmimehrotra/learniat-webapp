<!--<link rel="stylesheet" href="<?php echo base_url();?>/assets/css/style.css" />
<link rel="stylesheet" href="<?php echo base_url();?>/assets/css/tabs.css" />
-->
<?php 
$label = sprintf('%s%% (%s)', $questionDetails->averageScoreOfClass, $questionDetails->classAverageDataRows);

?>
<!-- Modal popup Start -->
<a class="close" onclick="popupClose('modal_popup_no_bg')">
    <img src="<?php echo base_url('assets/images/close-t.png');?>" align=right>
</a>
<div class="modalpop_wrapper_full">
   <div class="modal_header">
      
      	<table width="99%">
        	<tr>
        		<td >
        			<span class="modal_title"><p><?php echo $questionDetails->question_name; ?></p> </span>
        		</td>
        		<td width="70px;">
        			<?php $this->load->view('student/helper/class-average-bar.php',
        					array('averageScoreOfClass' => $questionDetails->averageScoreOfClass,
        							'classAverageDataRows' => $questionDetails->classAverageDataRows)); ?>
				</td>
				<td width="<?php echo (strlen($label) * 7); ?>px;">
					<span class="ver-slide-desc"
						style="float: right;padding-top:1px;font-size:12px;">
						<?php echo $label; ?>
					</span>
        		</td>
        	</tr>
        </table>
   </div>
   <!------------->
    <?php
    if (!empty($studentScoreInfo)) :
        foreach ($studentScoreInfo AS $student) :
    ?>
        <div class="topic-btm-left">
            <img src="<?php echo LEARNIAT_IMAGE_PATH . '/' . $student->student_id; ?>_79px.jpg" class="profilePic47 topic-std-exp-img">
            <p class="topic-exp-std-name"><?php echo $student->first_name; ?></p>
            <?php $this->load->view('student/helper/score.php', array('details' => $student, 'sessionId' => $sessionId)); ?>
        </div>

    <?php
        endforeach;
    endif;
    ?>
   <!------------->
   <div style="clear:both;"></div>
</div>


<script type='text/javascript'>

    //Create the tooltips only when document ready
    $(document).ready(function()
    {
        // MAKE SURE YOUR SELECTOR MATCHES SOMETHING IN YOUR HTML!!!
        $('.exp-std-reply').each(function() {
            $(this).qtip({
                content: {
                    text: function(event, api) {
                        $.ajax({
                            url: api.elements.target.attr('href') // Use href attribute as URL
                        })
                            .then(function(content) {
                                // Set the tooltip content upon successful retrieval
                                api.set('content.text', content);
                            }, function(xhr, status, error) {
                                // Upon failure... set the tooltip content to error
                                api.set('content.text', status + ': ' + error);
                            });

                        return '<img src="<?php echo base_url('assets/images/spinner-1.gif'); ?>" width="18" height="18" alt="Loading..."/>'; // Set some initial text
                    }
                },
                overwrite: true,
                style: {
                    tip: {
                        corner: true,
                        width: 15,
                        height: 10,
                        border: 1,
                        padding :0
                    },
                    widget:true,
                    border: {radius: 15},
                    classes: 'qtip-bootstrap'
                },
                position: {
                    my: 'top center',
                    at: 'bottom center',
                    adjust : {
                        screen : true,
                        resize: true
                    }
                    //,viewport: $(window)
                },
                hide: {
                    fixed: true,
                    delay: 300
                }
            });
        });
    });
</script>
<style>

    .qtip-bootstrap {
        border-radius: 12px !important;
        -webkit-border-radius: 12px !important;
        -moz-border-radius: 12px !important;
    }
</style>
