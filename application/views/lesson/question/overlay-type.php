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




    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

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

                    var dialogText = '';
                    var dialogWarning = '';


                    if (imageWidth < imageWidthMin) {
                        //If width of pic less than min required width
                        dialogText = 'The width of this picture is smaller than the lowest size permitted i.e. ' + imageWidthMin +'.';
                        dialogWarning = 'If you upload it, it will <b>NOT</b> fill the entire width of a normal iPad screen';
                    } else if ((imageWidth > imageWidthMin)  && (imageWidth <imageWidthMinRetina)) {
                        dialogText = 'The width of this picture is smaller that needed for a full size iPad retina screen i.e. ' + imageWidthMinRetina +'.';
                        dialogWarning = 'If you upload it, it will NOT fill the entire width of an iPad retina screen. However will be fine for a normal iPad.';
                    } else if (imageHeight < imageHeightMin) {
                        //If height of pic less than min required height
                        dialogText = 'The height of this picture is smaller than the lowest size permitted i.e. ' + imageHeightMin +'.';
                        dialogWarning = 'If you upload it, it will <b>NOT</b> fill the entire width of a normal iPad screen';
                    } else if ((imageHeight > imageHeightMin)  && (imageHeight <imageHeightMinRetina)) {
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
                minCropSize = [imageWidth, imageHeight];
                cropSelect =  [0, 0, imageWidth, imageHeight ];
            }


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
        var input = document.getElementById('imgInp');
        readURL(input);
        $('#checkScribbleAvailable').val('');
    }

    $('#save').click(function() {
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