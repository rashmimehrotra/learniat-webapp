<!-- <form id="answerData" accept-charset="utf-8" method="post" enctype="multipart/form-data" name="answerData">
<link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/css/style.css');?>" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url('assets/css/lesson.css');?>" />
-->
<?php
$subTextMessage = array(
	1 => 'Has exactly one correct answer',
	2 => 'Has more than one correct answer',
	3 => 'Has more than one match',
	4 => '',
	5 => '',
	6 => ''			
	);
$subTextOptionMessage = $subTextMessage[1];
$duplicate = isset($duplicate) ? $duplicate : false;
echo form_hidden('duplicate', $duplicate);

$questionId = (isset($questionId) && !empty($questionId)) ? $questionId : '';
echo form_hidden('questionId', $questionId);

$topicId = (isset($topicId) && !empty($topicId)) ? $topicId : '';
echo form_hidden('topicId', $topicId);
?>
<div id="questionEditTopicId" value="<?php echo $topicId; ?>"></div>
<div class="modal_header">

	<span class="modal_title">
		<h2><?php echo ($questionId == 'null') ? 'Add' : 'Edit'; ?> Question</h2>
	</span>
	<div class="modal_per">
		<table>
			<tbody>
				 <tr>
				 	<td><?php echo form_button("cancel","Cancel", 'class="button-cancel" onclick="hideQuestionDiv();"'); ?></td>
				 	<td style="padding-left:10px;">
				 		<?php
						$dataOptions = array(
							'id' => 'save',
							'name' => 'save'
						);
						echo form_submit($dataOptions, "Save changes", 'class="button-changes"');
						?>
					</td>
				 	
				 </tr>
			</tbody>
		</table>
	</div>

	<div style="clear:both;"></div>
</div>

<div class="edit_wrapper">		 
	<table>
	 	<tr>
			<td class="que-tittle">Question:</td>
			<td class="que-tittle">Select Question Type:</td>
		</tr>
	 	<tr>
			<td>
				<?php
				$questionName = (isset($questionData->question_name)) ? $questionData->question_name : '';
				$data = array('name' => 'question', 'cols' => 80, 'rows' => 3);
				echo form_textarea($data, $questionName, 'class="select99 textOptions"');
				?>
			</td>
			<td>
				<div class="select_question">
					<div class="styled_select">
					<?php
					$questionTypeId = (isset($questionData->question_type_id)) ? $questionData->question_type_id : '';
					$data = array('id' => 'questionType', 'name' => 'questionType');
					
					
					if (!empty($questionTypeId)) :
						$subTextOptionMessage = $subTextMessage[$questionTypeId];

                        switch ($questionTypeId) :
                            case 1:
                            case 2:
                                    $questionTypes = array(1 => $questionTypes[1], 2 => $questionTypes[2]);
                                break;
                            default:
                                $questionTypes = array($questionTypeId => $questionTypes[$questionTypeId]);
                        endswitch;
					endif;
					echo form_dropdown($data, $questionTypes, $questionTypeId, 'class="styled_select"');
					
					?>
					   
					   <div class="sub_head_option"> <?php echo $subTextOptionMessage; ?></div>
					</div>
					
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" id="question-type-result" style="padding-bottom:10px;"></td>
		</tr>
	</table>
</div>


<!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>-->
<script>

var subQuestionType = <?php echo json_encode($subTextMessage); ?>		
$("#questionType").change(function(event) {
	var questionType = $(this).val();
	var subMessage = subQuestionType[questionType];
	$('.sub_head_option').html(subMessage);
	getQuestionTypeData(questionType);
	
});

function getQuestionTypeData(questionType)
{
	$("#question-type-result").html('');
	var questionId = '<?php echo (isset($questionData->question_id)) ? $questionData->question_id : ''; ?>';
    var urlParam = "questionTypeId=" + questionType + "&questionId=" + questionId + "&duplicate=" + <?php echo $duplicate; ?>;

    $.ajax({
		type: "get",
		url: "<?php echo base_url(); ?>index.php/lesson/questions/type?" + urlParam,
		cache: true,
		success: function(response){						
			try{
				$( "#question-type-result" ).html(response);
			}catch(e) {
				//alert('Exception while request..');
			}		
		},
		error: function(xhr){
			validateAjaxError(xhr)
		}
	});
}
$(document).ready(function(){
	var questionType = $("#questionType :selected").val();
	getQuestionTypeData(questionType);
});

//validation
$(".textOptions").click(function(e){
	$( ".errorLabel" ).remove();
});

function validateQuestionData(e)
{
    $( ".errorLabel" ).remove();
    var flag = true;
    var scribble = $('#checkScribbleAvailable').val();
    //For edit question scribble is not available then reset to empty
    if (typeof scribble === 'undefined') {
        scribble = '';
    }
    var imageDefault = '<?php echo base_url('assets/images/no-foto.png'); ?>';
    $(".textOptions").each(function(nr){
        if($(this).val() === '')  {
            flag = false;
            e.preventDefault();
            if(!$(this).next().is('label')) {
                $(this).after('<label class="errorLabel">This field can not be empty</label>');
            }
        }
    });

    $('.fileOption').find('input[type=file]').each(function() {
        if($(this).val() === '' && scribble === '')  {
            flag = false;
            e.preventDefault();
            if(!$(this).next().is('label')) {
                $(this).parent().after('<label class="errorLabel">This field can not be empty</label>');
            }
        }
    });

    if($("#targetImg").length > 0) {
        var imageSrc = $('#targetImg').attr('src');
        if (imageSrc === '') {
            flag = false;
            e.preventDefault();

            if(!$("#targetImg").next().is('label')) {
                $("#targetImg").parent().after('<label class="errorLabel">This field can not be empty</label>');
            }
        }

    }

    return flag;
}
</script>
<!--</form> -->
