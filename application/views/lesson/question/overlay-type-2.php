<?php if (empty($questionOptionData)) :
    $scribble = '';
    //$scribble = base_url('assets/images/no-foto.png');
else :
    $scribble = LEARNIAT_IMAGE_SCRIBBLE . $questionOptionData->image_path;
endif; ?>



<table class="editQuestionMatchList">
	<tr>
		<td class="alignTop">

			<div style="margin: 0 auto; padding: 30px 0; width: 80%;">
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


					<tr>
						<td colspan="1">
							
							<div class="image-editor"  style="margin-left:2%; padding-top: 20px;">
							      <!-- .cropit-image-preview-container is needed for background image to work -->
							      <div class="cropit-image-preview-container">
							        <div class="cropit-image-preview"></div>
							      </div>
							      <div class="image-size-label">
							      </div>
							      <input type="range" class="cropit-image-zoom-input">
							      <textarea name="image-data" class="hidden-image-data displayNone"  ></textarea>
						     </div>
							
						</td>

                        <td style="vertical-align: top;float: right;">
                            <table>
                                <tr>
                                    <td class="que-tittle" style="padding-top:10px;">
                                        Image Size
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="button"
                                               class="button-pic width100"
                                               id="size-1"
                                               value="780x520" name="size-1" disabled>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 8px;">
                                        <input type="button"
                                               class="button-pic width100"
                                               id="size-2"
                                               value="1500x1000"
                                               name="size-2">
                                    </td>
                                </tr>
                            </table>
                        </td>

					</tr>
				</table>
			</div>

		</td>
	</tr>
</table>



<script>
    var widthImage = 780;
    var heightImage = 520;
    var imageSizes = {
        "size-1":{"widthImage": 780, "heightImage": 520},
        "size-2":{"widthImage": 1500, "heightImage": 1000}
    };

    $(".button-pic").click(function(){
        addCropResize();
        $(".button-pic").prop('disabled', false);
        $(this).prop('disabled', true);
        var currentId = $(this).attr('id');
        if (typeof currentId !== 'undefined') {
            widthImage =imageSizes[currentId]['widthImage'];
            heightImage =imageSizes[currentId]['heightImage'];
            validateScribbleImage();
        }

    });


    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var image = new Image();
                image.src = e.target.result;

                image.onload = function() {
                    var imageWidth = this.width;
                    var imageHeight = this.height;

                    if (imageWidth < widthImage || imageHeight < heightImage) {
                        alert('Image should be more than ' + widthImage + ' X ' + heightImage);
                        $('.cropit-image-background').prop('src', '');
                        $('.cropit-image-preview').css('background', '');
                        $(input).val('');
                    }
                }
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgInp").change(function(){
        $( ".errorLabel" ).remove();
        var ext = $(this).val().split('.').pop().toLowerCase();
        if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
            alert('invalid extension!');
        } else {
            validateScribbleImage();
        }
    });

    function validateScribbleImage()
    {
        var input = document.getElementById('imgInp');
        readURL(input);
        $('#checkScribbleAvailable').val('');
    }
</script>

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
</style>

<script src="<?php echo base_url('assets/resource/cropit/jquery.cropit.js'); ?>"></script>
<script>


    $(function() {
        addCropResize();
    });

    function addCropResize()
    {
        $('.editQuestionMatchList').cropit({
            exportZoom: 1,
            imageBackground: true,
            imageBackgroundBorderWidth: 0,
            width: widthImage,
            height: heightImage,
            imageState: {
                src: '<?php echo $scribble;?>'
            }
        });
    }

    $('#save').click(function() {
        var imageData = $('.editQuestionMatchList').cropit('export');
        $('.hidden-image-data').text(imageData);
    });
    </script>


<?php
$scribble = '';
if (!empty($questionOptionData) && property_exists($questionOptionData, 'image_path')) :
    $scribble = LEARNIAT_IMAGE_SCRIBBLE . $questionOptionData->image_path;
endif; ?>

<input type="hidden" name='checkScribbleAvailable' id='checkScribbleAvailable' value="<?php echo $scribble; ?>">