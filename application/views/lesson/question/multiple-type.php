<?php $questionRowCount = 1; ?>
<table class="editQuestionMatchList">

	<tr>
		<td class="alignTop">
			<div class="que-tittle" style="padding: 15px 0 15px  43%;">Add the matches</div>
		</td>
	</tr>
	<tr>
		<td class="alignTop">
		
			<table id="multipleTypeTable">
				<?php
                if (empty($questionOptionData)) :
					$questionRowCount = 2;
					for ($i=0; $i < $questionRowCount; $i++) :
                ?>
                        <tr>
							<td>
								<?php
								$data = array('name' => 'matchColumnA[]', 'cols' => 40, 'rows' => 3);
								echo form_textarea($data, '', 'class="select99 floatRight textOptions"');
								?>
							</td>
							<td class="width50">
								<div style="width:110%; border: 1px solid #e5e5e5;margin-left:-7%;z-index:-100;"></div>
							</td>
							
							<td>
								<?php
								$data = array('name' => 'matchColumnB[]', 'cols' => 40, 'rows' => 3);
								echo form_textarea($data, '', 'class="select99 textOptions"');
								?>
							</td>
							<td class="width50">

                            </td>
						</tr>
                <?php
                    endfor;
				else :
					$questionRowCount = count($questionOptionData['firstColumn']);
					 foreach ($questionOptionData['firstColumn'] AS $position => $optionColumnFirst) :
                ?>
						<tr id="questionRowCount-<?php echo $questionRowCount; ?>">
							<td>
								<?php
								$data = array('name' => 'matchColumnA[]', 'cols' => 40, 'rows' => 3);
								echo form_textarea($data, $optionColumnFirst->question_option, 'class="select99 floatRight textOptions"');
								?>
							</td>
							<td class="width50">
								<div style="width:110%; border: 1px solid #e5e5e5;margin-left:-7%;z-index:-100;"></div>
							</td>
							
							<td>
								<?php
								$data = array('name' => 'matchColumnB[]', 'cols' => 40, 'rows' => 3);
								echo form_textarea($data, $questionOptionData['secondColumn'][$position]->question_option, 'class="select99 textOptions"');
								?>
							</td>
							<td class="width50">
                                <?php
                                $idArray = $optionColumnFirst->question_option_id . ',' . $questionOptionData['secondColumn'][$position]->question_option_id;
                                if ($questionAverageScore == 0 && $duplicate === false && $position >= 2) : ?>
                                    <img onclick="removeQuestionOptionAlreadyExist('<?php echo $idArray; ?>', <?php echo $questionRowCount; ?>);"
                                         src="<?php echo base_url('assets/images/negative-red.png');?>"
                                         alt="">
                                <?php elseif ($duplicate === true && $position >= 2) : ?>
                                    <img onclick="removeQuestionRow(<?php echo $questionRowCount++;; ?>);"
                                         src="<?php echo base_url('assets/images/negative-red.png'); ?>"
                                         alt="">
                                <?php endif; ?>
                            </td>
						</tr>
                <?php
                        $questionRowCount++;
                    endforeach;
				endif;
                ?>
			</table>
		</td>
	</tr>
	
	<tr>
		<td class="alignTop">
			<table id="multipleAddColumn">
				<tr>
					<td> </td>
					<td class="width50">
						<?php
						$dataOptions = array(
							'id' => 'addMultipleType',
							'name' => 'addMultipleType'
							);
						echo form_button($dataOptions, "Add", 'class="button-add"');
						 ?>	
					</td>
					
					<td class=""> </td>
					<td class="width50"> </td>
				</tr>
			</table>
			
		</td>
	</tr>
	<tr>
		<td style="padding-bottom:15px;"> </td>
	</tr>
</table>
<script>
    var questionRowCount = <?php echo $questionRowCount; ?>;
    var maxRowAllowed = 6;
    var rowStart = <?php echo $questionRowCount; ?>;
    $("#addMultipleType").click(function(event) {
        addNextRows('multipleTypeTable');
    });
    function addNextRows(tableId)
    {
        if (rowStart < maxRowAllowed) {
            var rowId = "questionRowCount-" + questionRowCount;
            var newRow = '<tr id="' + rowId + '">';
            newRow += '<td ><textarea rows="3" class="select99 floatRight textOptions" cols="40" name="matchColumnA[]"></textarea></td>';
            newRow += '<td class="width50"><div style="width:110%; border: 1px solid #e5e5e5;margin-left:-7%;z-index:-100;"></div></td>';
            newRow += '<td ><textarea rows="3" class="select99 textOptions" cols="40" name="matchColumnB[]"></textarea></td>';
            newRow += '<td style="text-align:left;"><img alt="" src="<?php echo base_url('assets/images/negative-red.png');?>" onclick="removeQuestionRow(\'' + questionRowCount + '\');"></td>';
            newRow += '</tr>';
            questionRowCount ++;
            rowStart ++;
            $('#' + tableId + ' tr:last').after(newRow);
        }
    }

    function removeQuestionRow(rowId)
    {
        $('#questionRowCount-' + rowId).remove();
        rowStart --;
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
