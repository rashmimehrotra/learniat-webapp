
<?php
$countStudentQueryDetails= count($studentQueryDetails);
if($countStudentQueryDetails > 0) : ?>
	<div class="topic-bar-header-box">
        
	    <div class="question_txt">
	        <h4>
                Queries by <?php echo ucfirst($studentDetails->first_name); ?>
                (<?php echo count($studentQueryDetails); ?>)
            </h4>
	    </div>
	</div>
		
	<?php foreach ($studentQueryDetails as $studentQuery) : ?>
		<div class="topic-bar-box">
		    <div class="question_txt">
		    
		    	 <table class="evaluated" width="98%">
			     	<tr>
				     	<td colspan="5">
	                        <div class="student_txt">
				                <p><?php echo $studentQuery->query_text; ?></p>
				            </div>
	                    </td>
		        	</tr>
			        <!-- <div class="question_list">  -->
				       
	           		<tr>
	           			<td> 
	           				<?php echo $studentQuery->topic_name; ?>
	           				<img alt="evaluated"
	                            src="<?php echo base_url('assets/images/exp-view-img.png');?>"
	                            class="top-exp-viewed">
	                        <?php echo $studentQuery->parent_topic_name; ?>
	           			</td>
	           			<td> </td>
	           			<td> </td>
	                    
	                    <td> </td>
	                    <td class="query-right">
	                     	<?php if ($studentQuery->badge_id == 1) : ?>
	                     	
	                     		<?php 
	                     			$titleView = "<table width='180px;'  style='margin:5px;'>
				                    	<tr>
				                    		<td style='width=50%;text-align:center; border-right: 1px solid #e5e5e5;padding:0;'>
						                        <span class='meToo'>Volunteer</span><br>
						                        <span class='top-right-btm'>" . $studentQuery->volunteer . "</span>
						                    </td>
						                    <td  style='width=50%;text-align:center;padding:0;'>
						                    	 <span class='meToo'>&nbsp;Me-too&nbsp;</span><br>
						                        <span class='top-right-btm'> " . $studentQuery->meToo . " </span>
						                    </td>
				                    	</tr>
			                    	</table>";
	                     		?>
		                    	<table>
			                    	<tr>
			                    		<td>
					                        <img alt="evaluated"
					                            src="<?php echo base_url('assets/images/evaluated-img.png');?>"
					                            class="topic-std-exp-img">
					                         Good question
					                    </td>
					                    <td>
					                    	<img alt="evaluated"
					                            src="<?php echo base_url('assets/images/exp-view-img.png');?>"
					                            class="top-exp-viewed">
					                        <a href="javascript:void(0);"
					                        	class="evaluated-view"
					                        	title="<?php echo $titleView; ?>"
					                        >View</a>
					                    </td>
			                    	</tr>
		                    	</table>
	                        <?php endif;?>
	                    </td>
	                </tr>
				           
				        
			        <!-- </div> -->
		         </table>
		    </div>
		</div>
	<?php endforeach; ?>
<?php else: ?>
<div class="topic-bar-box">
    <div class="question_txt">
         <div class="student_query_txt">
         	<p>No query by <?php echo $studentDetails->first_name; ?></p>
         </div>
    </div>
</div>
<?php endif; ?>

<?php
$countVolunteerNotSelectedQuery = count($volunteerNotSelectedQueryDetails);
if($countVolunteerNotSelectedQuery > 0) : ?>
<div class="topic-bar-header-box">
    <div class="question_txt">
        <h4>Volunteered but not selected (<?php echo $countVolunteerNotSelectedQuery; ?>)</h4>
    </div>
</div>
<?php endif; ?>
<?php foreach ($volunteerNotSelectedQueryDetails as $queryDetails) : ?>

	
	
<div class="topic-bar-box">
    <div class="question_txt">
	    
    	 <table class="evaluated" width="98%">
	     	<tr>
		     	<td colspan="5">
                    <div class="student_txt">
		                <p><?php echo $queryDetails->query_text; ?></p>
		            </div>
               </td>
	        </tr>
			       
           	<tr>
           		<td> 
           			<?php echo $queryDetails->topic_name; ?>
           				<img alt="evaluated"
                            src="<?php echo base_url('assets/images/exp-view-img.png');?>"
                            class="top-exp-viewed">
                        <?php echo $queryDetails->parent_topic_name; ?>
           		</td>
           		<td> </td>
           		<td> </td>
           		<td> </td>
           		<td class="query-right"></td>
           	</tr>    
		       
		</table>
	</div>
</div>
<?php endforeach; ?>


<?php
$countVolunteerSelectedQuery = count($volunteerSelectedQueryDetails);
if($countVolunteerSelectedQuery > 0) : ?>
<div class="topic-bar-header-box">
    <div class="question_txt">
        <h4>Volunteered and selected (<?php echo $countVolunteerSelectedQuery; ?>)</h4>
    </div>
</div>
<?php endif; ?>

<?php foreach ($volunteerSelectedQueryDetails as $queryDetails) : ?>
	<div class="topic-bar-box">
	    <div class="question_txt">
		    
	    	 <table class="evaluated" width="98%">
		     	<tr>
			     	<td colspan="5">
	                    <div class="student_txt">
			                <p><?php echo $queryDetails->query_text; ?></p>
			            </div>
	               </td>
		        </tr>
				       
	           	<tr>
	           		<td> 
	           			<?php echo $queryDetails->topic_name; ?>
	           				<img alt="evaluated"
	                            src="<?php echo base_url('assets/images/exp-view-img.png');?>"
	                            class="top-exp-viewed">
	                        <?php echo $queryDetails->parent_topic_name; ?>
	           		</td>
	           		<td> </td>
	           		<td> </td>
	           		<td> </td>
	           		<td class="query-right">
	           			<table class="evaluated" >
			                <tr>
			                	<td>
			                        <img alt="like"
			                            src="<?php echo base_url();?>/assets/images/exp-like-count-img.png"
			                            class="topic-std-exp-img">
			                        <?php echo $queryDetails->thumbs_up; ?>
			                    </td>
			                    <td>
			                        <img alt="dislike"
			                            src="<?php echo base_url('assets/images/exp-dislike-count-img.png');?>"
			                            class="topic-std-exp-img">
			                            
			                        <?php echo $queryDetails->thumbs_down; ?>
			                    </td>
			                    <td><div style="border-right: 1px solid #e5e5e5;">&nbsp;</div></td>
			                    
			                    <td>
			                        <img alt="evaluated"
			                            src="<?php echo base_url('assets/images/evaluated-img.png');?>"
			                            class="topic-std-exp-img">
			                    </td>
			                    <td>Evaluated</td>
			                    
			                    <td>
			                        <img alt="view"
			                            src="<?php echo base_url('assets/images/exp-view-img.png');?>"
			                            class="top-exp-viewed">
			                    </td>
			                    <td>
				                     <?php $evaluatedString = $this->load->view('answer/evaluated.php',
			        					array(
			        						'assessmentAnswerData' => $queryDetails,
			        						'studentAttendedNumber' => $studentAttendedNumber
			        						),
					            		true);
			                    	 ?>
			                    	<a href="javascript:void(0);"
						               class="evaluated-view"
			                    		title="<?php echo htmlspecialchars($evaluatedString); ?>"
				                    >
			                    		View
			                    	</a>
			                    </td>
			                </tr>
			            </table>
			            
	           		</td>
	           	</tr>    
			       
			</table>
		</div>
	</div>
<?php endforeach; ?>