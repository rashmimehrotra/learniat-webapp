<!--  <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css');?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/tabs.css');?>" />-->
<?php if (empty($studentQueryDetails)) : ?>
 <div class="topic-bar-box">
    <div class="student_icon">
     	No query found
    </div>
 </div>

<?php else : ?>
    <?php foreach ($studentQueryDetails AS $details) : ?>
    <div class="topic-bar-box">
        <div class="student_icon">
            <div class="profile_img">
                <img class="profilePic44"
                    src="<?php echo LEARNIAT_IMAGE_PATH .  $details->user_id . '_' . IMAGE_SIZE_79;?>.jpg">
            </div>
        </div>
        <div class="student_txt_cnt">
            <table class="student_txt">
            	<tr>
            		<td>
            			<div class="stud_title">
		                    <h2><?php echo ucfirst($details->first_name) . ' ' . ucfirst($details->last_name); ?></h2>
		                </div>
            		
            		</td>
            		<td class="student_txt_td" style="text-align:right;float:right;">
            		
            			<table class="evaluated">
	                        <tr>
	                        	<td class="width150">
		                        	<?php if ($details->badge_id == 1) : ?>
		                                <img alt="Good question"
		                                    src="<?php echo base_url();?>assets/images/exp-like-count-img.png"
		                                    class="topic-std-exp-img">
		                                 Good Question   
		                            <?php endif; ?>
		                        </td>
	                        	
	                        	
	                        	<?php if ($details->volunteer > 0 && $details->answered > 0) : ?>
	                        		<?php if ($details->badge_id == 1) : ?>
		                        		<td><span class="top-left-Br">|</span></td>
		                        	<?php endif; ?>
		                        	<td>
		                        		<span class="evaluated-link"><?php echo $details->volunteer;?>  volunteers</span>&nbsp;&nbsp;
		                        			<img alt="" src="<?php echo base_url();?>assets/images/exp-view-img.png">&nbsp;&nbsp;
		                        		<span class="evaluated-link"><?php echo $details->answered;?>  answered</span>
		                            </td>
		                            
		                             <td>
				                    	<img alt="evaluated"
				                            src="<?php echo base_url('assets/images/exp-view-img.png');?>"
				                            class="top-exp-viewed">
				                        <a href="javascript:void(0);"
				                        	load="<?php echo base_url();?>index.php/session/topic/result?queryId=<?php echo $details->query_id;?>&sessionId=<?php echo $sessionId; ?>"
				                        	class="resultViewTopicQuery"
				                        >View</a>
				                    </td>
	                            <?php endif; ?>
	                        	
	                        	<?php if ($details->votes_received > 0) : ?>
	                        		
	                        		<?php if ($details->badge_id == 1
	                        				|| ($details->volunteer > 0 && $details->answered > 0)) : ?>
		                        		<td><span class="top-left-Br">|</span></td>
		                        	<?php endif; ?>
		                        	
		                            <td>
		                                <img alt="Voted"
		                                    src="<?php echo base_url();?>assets/images/evaluated-img.png"
		                                    class="topic-std-exp-img">
		                                Voted
		                            </td>
	                       	 	<?php endif; ?>
	                       	 	
	                       	 	<?php if ($details->badge_id == 0 && $details->volunteer == 0
                                        && $details->answered == 0
	                       	 			&& $details->votes_received == 0) : ?>
			                    	<td><div class="topic-btm-right-dismiss">DISMISSED</div></td>
			                	<?php endif; ?>
	                        </tr>
	                    </table>
            		
            		
            		</td>
            		 <?php if ($details->votes_received > 0) : ?>
            			<td rowspan="2" class="width80">
	            			<div class="meToo_cnt">
		            			<span class="meToo">Me-too</span><br>
		            			<span class="top-right-btm"><?php echo sprintf("%02d", $details->votes_received);?></span>
		       				</div>
		       			</td>
            		 <?php endif; ?>
            	</tr>
            	<tr>
            		<td colspan="2">
            			<div class="student_query_txt">
                			<p><?php echo $details->query_text; ?></p>
            			</div>
            		</td>
            	</tr>
            </table>
        </div>
        
    </div>
    <?php endforeach; ?>
<?php endif; ?>


<script type="text/javascript">
//Create the tooltips only when document ready
$(document).ready(function()
{
    // MAKE SURE YOUR SELECTOR MATCHES SOMETHING IN YOUR HTML!!!
    $('.resultViewTopicQuery').each(function() {
        $(this).qtip({
           content: {
               text: function(event, api) {
                   $.ajax({
                       url: api.elements.target.attr('load') // Use href attribute as URL
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
	        	   //corner: 'right top',
	               //mimic: 'right center',
                   corner: true,
 	               mimic: 'center',
                   width: 15,
                   height: 10,
                   border: 1,
                   padding :1
	           },
                widget:true,
                border: {radius: 15},
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
		     	//event: false,
                fixed: true,
	            delay: 300
	     	}
        });
    });
});
</script>

