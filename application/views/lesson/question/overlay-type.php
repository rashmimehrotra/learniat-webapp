<!--<script src="<?php echo base_url('assets/imagecrop/js/jquery.min.js'); ?>"></script>-->
<script src="<?php echo base_url('assets/imagecrop/js/jquery.Jcrop.js'); ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assets/imagecrop/demo_files/main.css'); ?>" type="text/css" />
<link rel="stylesheet" href="<?php echo base_url('assets/imagecrop/css/jquery.Jcrop.css'); ?>" type="text/css" />


<?php if (empty($questionOptionData)) :
    $scribble = $relativePath = '';
//$scribble = base_url('assets/images/no-foto.png');
else :
    $scribble = LEARNIAT_IMAGE_SCRIBBLE . $questionOptionData->image_path;
    $relativePath = LEARNIAT_IMAGE_RELATIVE_PATH . $questionOptionData->image_path;
endif; ?>

<input type="hidden" name='checkScribbleAvailable' id='checkScribbleAvailable' value="<?php echo $scribble; ?>">
<textarea name="image-data" class="hidden-image-data displayNone"  ></textarea>
<table class="editQuestionMatchList">
    <tr>
        <td class="alignTop">

            <div style="margin: 0 auto; padding: 30px;">
                <table>
                    <tr>
                        <td class="que-tittle" style="padding:10px;">Preset Image</td>
                        <td style="float: right;">

                            <table>
                                <tr>
                                    <td class="que-tittle" style="padding:10px;">Change image</td>
                                    <td class="que-tittle">  </td>
                                    <td>
                                        <div class="button-add fileOption" style="height: 37px; width: 90px;">
                                            Browse
                                            <input type='file' id="imgInp" name='test'  size='40' class="upload cropit-image-input" />
                                        </div>


                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

					<tr colspan="2">
						<div id="canvasDiv" style="margin-top:15px;text-align: center;">
							<canvas id="canvas" width='1' height='1' style="border: 1px solid black" />
						</div>
					</tr>
                    <tr>
                        <td colspan="2">

                            <div id="targetImgDiv" style="margin-top:15px;text-align: center;">
                                <?php
                                if (!empty($scribble) && file_exists($relativePath)) :
                                    $randomId = date("YmdHis");
                                    echo "<img src='$scribble?randomId=$randomId' id='targetImg' alt='Kindly wait]'>";
                                elseif (!file_exists($relativePath)) :
                                    echo '<span class="image-noreply">Image Not Available</span>';
                                endif;

                                ?>
								
                            </div>

                            <input type="hidden" id="x" name="x" />
                            <input type="hidden" id="y" name="y" />
                            <input type="hidden" id="w" name="w" />
                            <input type="hidden" id="h" name="h" />

                        </td>
                    </tr>
					
                </table>
            </div>

        </td>
    </tr>
</table>

<style>

    .cropit-image-preview {
        background-color: #f8f8f8;
        background-size: cover;
        border: 5px solid #ccc;
        border-radius: 3px;
        margin-top: 7px;
        cursor: move;
    }
    .image-editor {
        width: 780px;
    }
    .cropit-image-background {
        opacity: .2;
        cursor: auto;
    }
    .cropit-image-zoom-input{
    }
    .image-size-label {
        margin-top: 15px;
    }

    input {
        /* Use relative position to prevent from being covered by image background */
        position: relative;
        z-index: 10;
        display: block;
    }

    .export {
        margin-top: 10px;
    }


    .black_overlay {
        z-index: 90;
    }
    #modal_popup{
        z-index: 100;
    }

</style>

<div id="dialog-question-crop" style="display: none;">
    <span id="dialog-question-crop-text"></span>
    <br>
    <span class="ui-icon ui-icon-alert" style="float:left;margin:2px 3px 0px 0px;"></span>
    <span id="dialog-question-crop-warning"></span>
</div>
<link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.css');?>">
<script src="<?php echo base_url('assets/js/jquery-ui.js');?>"></script>

<script type="text/javascript">



	var canvas = document.getElementById('canvas');
	var canvasFlag=0;
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
			$("#save").prop('disabled', false);
			
            reader.onload = function (e) {
                var image = new Image();
                image.src = e.target.result;

                $('#targetImg').attr('src', image.src );
				
                image.onload = function() {
                    var imageWidth = this.width;
                    var imageHeight = this.height;

                    var imageWidthMin= <?php echo CROP_MIN_WIDTH; ?>;
                    var imageHeightMin = <?php echo CROP_MIN_HEIGHT; ?>;

                    var imageWidthMinRetina = <?php echo CROP_MIN_WIDTH_RETINA; ?>;
                    var imageHeightMinRetina = <?php echo CROP_MIN_HEIGHT_RETINA; ?>;
					//alert(imageWidth+' '+imageHeight+' '+imageWidthMin+' '+imageHeightMin);
					//1024 576 1500 1000 780 520
					//900 900 1500 1000 780 520
					var dialogText = '';
                    var dialogWarning = '';
					var aspectRatio =3/2;
					
					/*var image = new Image();
					image.src = 'http://placehold.it/300x550';
					image.onload = function () {
						var canvasContext = canvas.getContext('2d');
						var wrh = image.width / image.height;
						var newWidth = canvas.width;
						var newHeight = newWidth / wrh;
						if (newHeight > canvas.height) {
									newHeight = canvas.height;
							newWidth = newHeight * wrh;
						}
						var xOffset = newWidth < canvas.width ? ((canvas.width - newWidth) / 2) : 0;
						var yOffset = newHeight < canvas.height ? ((canvas.height - newHeight) / 2) : 0;

						canvasContext.drawImage(image, xOffset, yOffset, newWidth, newHeight);
					  };
					  
					*/
                    if (imageWidth < imageWidthMin || imageHeight < imageHeightMin) {
                        //If width of pic less than min required width
						
						
						if(imageWidth>imageHeight*aspectRatio)
						{
					//		alert('the case');
							dialogText = 'The height of this picture is smaller than the lowest size permitted i.e. ' + imageHeightMin +'.';
							dialogWarning = 'If you upload it, it will <b>NOT</b> fill the entire width of a normal iPad screen';
							document.getElementById('targetImgDiv').style.display='none';
							canvasFlag=1;
							var canvasWidth = imageWidth;
							var canvasHeight = imageWidth / aspectRatio;
						//	alert( canvasWidth+' '+canvas.width+' '+canvasHeight+' '+canvas.height);
							canvas.width=canvasWidth;
							canvas.height=canvasHeight;
							var canvasContext = canvas.getContext('2d');
							var yOffset = imageHeight < canvas.height ? ((canvas.height - imageHeight) / 2) : 0;
							//alert( canvasWidth+' '+canvas.width+' '+canvasHeight+' '+canvas.height);
							//alert(yOffset);
							
							
							canvasContext.drawImage(image,x=0, y=yOffset, width=imageWidth, height=imageHeight);
							canvasContext.fillStyle = "black";
							canvasContext.fillRect(0, 0, canvas.width,yOffset);
							canvasContext.fillRect(0, yOffset+imageHeight, canvas.width,yOffset);
						}else if (imageWidth < imageHeight)
						{
						
						
						
						}
                        dialogText = 'The width of this picture is smaller than the lowest size permitted i.e. ' + imageWidthMin +'.';
                        dialogWarning = 'If you upload it, it will <b>NOT</b> fill the entire width of a normal iPad screen';
					// ahme edits
						/*var canvasWidth = imageHeight * aspectRatio;
						var canvasHeight = imageHeight;
						var canvasContext = canvas.getContext('2d');
						var xOffset = imageWidth < canvas.width ? ((canvas.width - imageWidth) / 2) : 0;
						canvasContext.drawImage(image, xOffset, 0 , canvasWidth, canvasHeight);*/
					// end ahmed edit
                    } 
					else if ((imageWidth > imageWidthMin)  && (imageWidth <imageWidthMinRetina)) {
						document.getElementById('canvasDiv').style.display='none';
						document.getElementById('targetImgDiv').style.display='block';
                        dialogText = 'The width of this picture is smaller that needed for a full size iPad retina screen i.e. ' + imageWidthMinRetina +'.';
                        dialogWarning = 'If you upload it, it will NOT fill the entire width of an iPad retina screen. However will be fine for a normal iPad.';
                    } 
					else if (imageHeight < imageHeightMin) {
                        //If height of pic less than min required height
					//	alert('the case');
                        dialogText = 'The height of this picture is smaller than the lowest size permitted i.e. ' + imageHeightMin +'.';
                        dialogWarning = 'If you upload it, it will <b>NOT</b> fill the entire width of a normal iPad screen';
						var canvasWidth = imageHeight;
						var canvasHeight = imageHeight / aspectRatio;
						canvas.Width=canvasWidth;
						canvas.Height=canvasHeight;
						var canvasContext = canvas.getContext('2d');
						var yOffset = imageHeight < canvas.Height ? ((canvas.Height - imageHeight) / 2) : 0;
						//alert(yoffset);
						canvasContext.drawImage(image,x=0, y=yOffset, width=imageWidth, height=imageHeight);
                    } 
					else if ((imageHeight > imageHeightMin)  && (imageHeight <imageHeightMinRetina)) {
                        dialogText = 'The height of this picture is smaller than that needed for a full size iPad retina screen i.e. ' + imageWidthMinRetina +'.';
                        dialogWarning = 'If you upload it, it will NOT fill the entire height of an iPad retina screen. However will be fine for a normal iPad.';
                    }


                    if (dialogText.length > 0 ) {

                        $('#dialog-question-crop-text').html(dialogText);
                        $('#dialog-question-crop-warning').html(dialogWarning);
                        var dialog = $("#dialog-question-crop").dialog({
                            resizable: true,
                            modal: true,
                            draggable: false,
                            closeOnEscape: true,
                            title: "Warning:",
                            async: false,
                            height: 250,
                            width: 550,
                            close: function( event, ui ) {
                                $("body").css("overflow", "auto");
                            },
                            create: function( event, ui ) {
                                $("body").css("overflow", "hidden");
                            },
                            buttons: {
                                "Yes": function () {
                                    $(this).dialog('close');
                                },
                                "No": function () {

                                    $(this).dialog('close');
                                    $('#targetImg').remove();
									$("#save").prop('disabled', true);
									//document.getElementById('save').disabled=true;
                                    return false;
                                }
                            }
                        });

                        dialog.dialog( "open" );
                    }

                    applyCropOnImage(imageWidthMin, imageHeightMin, imageWidth, imageHeight);


                }
				
					
				
			}

            reader.readAsDataURL(input.files[0]);
			
			
        }
		
    }

    /**
     * Apply image crop
     * @param {integer} imageWidthMin
     * @param {integer} imageHeightMin
     * @param {integer} imageWidth
     * @param {integer} imageHeight
     */
    function applyCropOnImage(imageWidthMin, imageHeightMin, imageWidth, imageHeight)
    {
		
        if (imageWidth > 1000) {
            var percentImageWidth = ((imageWidth - 1000)%10);
            percentImageWidth += 100;
            $('.editQuestionMatchList').attr('style', 'max-width:' + percentImageWidth + '%;width:' + percentImageWidth + '%' );
        }

        var minCropSize= [imageWidthMin, imageHeightMin];
        var cropSelect = [((imageWidth - imageWidthMin)/2), ((imageHeight - imageHeightMin)/2), imageWidthMin , imageHeightMin];


        if (imageWidth < imageWidthMin || imageHeight < imageHeightMin) {
            if (imageWidth < imageWidthMin && imageHeight > imageHeightMin) {

                minCropSize = [imageWidth, (imageWidth/(1.5))];
                cropSelect = [ 0, 0, imageWidth, (imageWidth/(1.5))];
            } else if (imageWidth > imageWidthMin && imageHeight < imageHeightMin) {
                minCropSize = [imageHeight * 1.5, imageHeight];
                cropSelect =  [0, 0, imageHeight * 1.5, imageHeight ];
            } else {
				//512 35 1500 1000 780 520
				minCropSize = [imageWidth, imageHeight];
				//alert(minCropSize);
				//Ahmed Montasser start (code doesn't change anthing
				var max= Math.max(imageWidth,imageHeight);
				var minHeightAspect = imageHeight;
				var minWidthAspect = imageWidth;
				if(imageWidth>imageHeight)
				{
					minHeightAspect = imageWidth * (2/3);
				}
				else if(imageWidth<imageHight)
				{
					minWidthAspect = imageHeight * (3/2);
				}
				//Ahmed Montasser end
                cropSelect =  [0,0, imageWidth, imageHeight ];
				//alert(cropSelect);
            }

		// aspect ratio and min size is conflicting 
        }
        $('#targetImg').Jcrop({
            minSize : minCropSize,		
            setSelect: cropSelect,
            aspectRatio: 3 / 2,
            bgColor: '',
            onSelect: updateCords
        });
    }


    $("#imgInp").change(function(){

        var imageValue = $(this).val();
        if (imageValue.length > 0) {
            $('#targetImgDiv').html('');
            $('#targetImgDiv').html('<img src="" id="targetImg" alt="Kindly wait">');

            $( ".errorLabel" ).remove();
            var ext = $(this).val().split('.').pop().toLowerCase();
            if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
                alert('invalid extension!');
            } else {
                validateScribbleImage();
            }
        }

    });

    function validateScribbleImage()
    {
		//alert('validate');
        var input = document.getElementById('imgInp');
        readURL(input);
		
        $('#checkScribbleAvailable').val('');
    }

    $('#save').click(function() {
         
		if(canvasFlag==1)
		{
			var imageData = canvas.toDataURL();
			console.log(imageData);
		}else
			var imageData = $('.jcrop-holder > img').attr('src');
		$('.hidden-image-data').text(imageData);
    });

</script>

<script type="text/javascript">

    function updateCords(c)
    {
        $('#x').val(c.x);
        $('#y').val(c.y);
        $('#w').val(c.w);
        $('#h').val(c.h);
    };
</script>