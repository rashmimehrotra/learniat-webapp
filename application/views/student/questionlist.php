<!--
<link rel="stylesheet" href="<?php echo base_url();?>/assets/css/style.css" />
<link rel="stylesheet" href="<?php echo base_url();?>/assets/css/tabs.css" />-->
<?php if (empty($questionDetails)) : ?>
 <div class="topic-bar-box">
    <div class="student_txt" style="padding-left:20px;">
     	<p>No question found</p>
    </div>
 </div>
<?php else : ?>
    <?php foreach ($questionDetails AS $key => $details) : ?>
    <div class="topic-bar-box">
         <table width="99%">
        	<tr>
        		<td>
        			<div class="question_txt">
			            <div class="question_list"><?php echo $key + 1; ?>.&nbsp;&nbsp;<?php echo $details->question_name; ?></div>
                  <?php
                    echo "<br>";
                    echo "<i><span style='font-size: 10px; margin-left:10px; color: #BDBDBD'>(".$details->question_id.")</span></i>";
                  ?>
			        </div>
        		</td>
                <?php if ($details->averageScoreOfClass > 0) : ?>
                    <td style="width:18px;">
                        <?php
                        $this->load->view(
                            'student/helper/class-average-bar.php',
                            array('averageScoreOfClass' => $details->averageScoreOfClass)
                        );

                        ?>
                    </td>
                <?php endif; ?>
				<td class="<?php echo (($details->averageScoreOfClass > 0) ? 'responseTdMin': 'responseTdMax'); ?>">

                        <?php
                        $studentBracket = 'All';
                        if ($details->numberOfResponse != $details->numberOfAttended) :
                            $studentBracket = sprintf('%d of %d', $details->numberOfResponse, $details->numberOfAttended);
                        endif;

                        $questionScore = '';
                        if ($details->averageScoreOfClass > 0) :
                            $questionScore = sprintf(
                                '<span class="ver-slide-desc responseDiv" style="padding-left: 10px;"> %d%% (%s) </span>',
                                $details->averageScoreOfClass,
                                $studentBracket
                            );
                        else :
                            $questionScore = '<span class="exp-std-noreply" style="float:right">No response yet</span>';
                        endif;
                        echo $questionScore;
                        ?>

					</span>
        		</td>
        	</tr>
        </table>
        <div style="margin-left:20px">
            <?php $this->load->view('student/helper/score.php', array('details' => $details, 'sessionId'=> $sessionId)); ?>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

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