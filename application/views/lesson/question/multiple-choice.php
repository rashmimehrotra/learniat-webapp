<?php $questionRowCount = 1; ?>
<table class="editQuestionAnswerList">
	<tr>
		<td class="que-tittle" style="padding: 15px 0 0 15px; width:50%;">
			<img src="<?php echo base_url('assets/images/1.png');?>" width="15" height="15" style="vertical-align:bottom;">&nbsp;
			Correct Answer(s)
			<br>
			<div class="sub_head_black">At least one needs to be correct</div>
		</td>
		<td class="que-tittle" style="padding: 15px 0 0 15px; width:50%;">
			<img src="<?php echo base_url('assets/images/0.png');?>" width="15" height="15" style="vertical-align:bottom;">&nbsp;
			Incorrect Answer(s)
			<br>
			<div class="sub_head_black">At least one needs to be incorrect</div>
		</td>
	</tr>
	
	<tr>
		<td class="alignTop" style="padding: 20px 0 0 0;">
            <table id="correctedTable">
			    <?php if (empty($questionOptionData)) : ?>

					<tr>
						<td>
							<?php
							$data = array('name' => 'corrected[]', 'cols' => 40, 'rows' => 3);
							echo form_textarea($data, '', 'class="select99 textOptions"');
							?>
						</td>
						<td class="width50"> </td>
					</tr>

			    <?php
                else :
                    $rowCount = 0;
                    foreach ($questionOptionData AS $key => $optionCorrected) :
					    if ($optionCorrected->is_answer == 1) :
                ?>

                            <tr id="questionRowCount-<?php echo $questionRowCount; ?>">
                                <td>
                                    <?php
                                    $data = array('name' => 'corrected[]', 'cols' => 40, 'rows' => 3);
                                    echo form_textarea($data, $optionCorrected->question_option, 'class="select99 textOptions"');
                                    ?>
                                </td>
                                <td class="width50">
                                    <?php if ($questionAverageScore == 0 && $duplicate === false && $rowCount != 0) : ?>
                                        <img onclick="removeQuestionOptionAlreadyExist(<?php echo $optionCorrected->question_option_id; ?>, <?php echo $questionRowCount; ?>);"
                                             src="<?php echo base_url('assets/images/negative-red.png');?>"
                                             alt="">
                                    <?php elseif ($duplicate === true && $rowCount != 0) : ?>
                                        <img onclick="removeQuestionRow(<?php echo $questionRowCount++;; ?>);"
                                             src="<?php echo base_url('assets/images/negative-red.png');?>"
                                             alt="">
                                    <?php endif; ?>
                                </td>
                            </tr>
                <?php
                            $questionRowCount++;
                            $rowCount++;
                        endif;
                    endforeach;
                endif;
                ?>
            </table>
			<table>
				<tr>
					<td colspan="2">
						<?php
						$dataOptions = array(
							'id' => 'addCorrectOption',
							'name' => 'addCorrectOption'
						);
						echo form_button($dataOptions, "Add", 'class="button-add"'); ?>
					</td>
				</tr>
			</table>
			
		</td>
		<td class="alignTop" style="padding: 20px 0 0 0;">
            <table id="inCorrectedTable">
			    <?php if (empty($questionOptionData)) : ?>

					<tr>
						<td>
							<?php
							$data = array('name' => 'inCorrected[]', 'cols' => 40, 'rows' => 3);
							echo form_textarea($data, '', 'class="select99 textOptions"');
							?>
						</td>
						<td class="width50"> </td>
					</tr>
			    <?php
                else :
                    $rowCount = 0;
                    foreach ($questionOptionData AS $key => $optionCorrected) :
                        if ($optionCorrected->is_answer == 0) :
                ?>
                            <tr id="questionRowCount-<?php echo $questionRowCount; ?>">
                                <td>
                                    <?php
                                    $data = array('name' => 'inCorrected[]', 'cols' => 40, 'rows' => 3);
                                    echo form_textarea($data, $optionCorrected->question_option, 'class="select99 textOptions"');
                                    ?>
                                </td>
                                <td class="width50">
                                    <?php if ($questionAverageScore == 0 && $duplicate === false && $rowCount != 0) : ?>
                                        <img onclick="removeQuestionOptionAlreadyExist(<?php echo $optionCorrected->question_option_id; ?>, <?php echo $questionRowCount; ?>);"
                                             src="<?php echo base_url('assets/images/negative-red.png');?>"
                                             alt="">
                                    <?php elseif ($duplicate === true && $rowCount != 0) : ?>
                                        <img onclick="removeQuestionRow(<?php echo $questionRowCount++;; ?>);"
                                             src="<?php echo base_url('assets/images/negative-red.png');?>"
                                             alt="">
                                    <?php endif; ?>
                                </td>
                            </tr>
                <?php
                            $questionRowCount++;
                            $rowCount++;
                        endif;
				    endforeach;
			    endif;
                ?>
            </table>
			<table>
				<tr>
					<td colspan="2">
						<?php
						$dataOptions = array(
							'id' => 'addInCorrectOption',
							'name' => 'addInCorrectOption'
							);
						echo form_button($dataOptions, "Add", 'class="button-add"'); ?>	
					</td>
				</tr>
				
			</table>
		
		</td>
	</tr>
	
</table>
<script>
    var questionRowCount = <?php echo $questionRowCount; ?>;
    $("#addCorrectOption").click(function(event) {
        addNextRows('corrected', 'correctedTable');
        decideQuestionType();
    });
    $("#addInCorrectOption").click(function(event) {
        addNextRows('inCorrected', 'inCorrectedTable');
    });

    /**
     * Add next rows
     *
     * @param integer typeId
     * @param integer tableId
     */
    function addNextRows(typeId, tableId)
    {
        var rowId = "questionRowCount-" + questionRowCount;
        var newRow = '<tr id="' + rowId + '">';
        newRow += '<td ><textarea rows="3" class="select99 textOptions" cols="40" id="' + typeId + questionRowCount + '" name="' + typeId + '[]"></textarea></td>';
        newRow += '<td style="text-align:left;"><img alt="" src="<?php echo base_url('assets/images/negative-red.png');?>" onclick="removeQuestionRow(\'' + questionRowCount + '\');"></td>';
        newRow += '</tr>';
        questionRowCount ++;
        $('#' + tableId + ' tr:last').after(newRow);
    }

    /**
     * Decide question type
     *
     */
    function decideQuestionType()
    {
        var n = $( "[id^=corrected]" ).size();
        if (n > 1) {
            $( "#questionType" ).val(2);
            var subMessage = subQuestionType[2];
            $('.sub_head_option').html(subMessage);
        } else {
            $( "#questionType" ).val(1);
            var subMessage = subQuestionType[1];
            $('.sub_head_option').html(subMessage);
        }
    }

    /**
     * Remove question Row
     * @param integer rowId
     */
    function removeQuestionRow(rowId)
    {
        $('#questionRowCount-' + rowId).remove();
        decideQuestionType();
    }

    /**
     * Remove question option already exist
     * @param mixed questionOptionId
     * @param integer questionRowCountId
     */
    function removeQuestionOptionAlreadyExist(questionOptionId, questionRowCountId)
    {
        var urlParam = "questionOptionId=" + questionOptionId;

        $.ajax({
            type: "get",
            url: "<?php echo base_url(); ?>index.php/lesson/questions/removeOption?" + urlParam,
            success: function(response){
                try{
                    removeQuestionRow(questionRowCountId);
                }catch(e) {
                    //alert('Exception while request..');
                }
            },
            error: function(xhr){
                validateAjaxError(xhr)
            }
        });
    }

</script>
