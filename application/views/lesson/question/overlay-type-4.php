<!--<script src="<?php echo base_url('assets/imagecrop/js/jquery.min.js'); ?>"></script>-->
<script src="<?php echo base_url('assets/imagecrop/js/jquery.Jcrop.js'); ?>"></script>

<link rel="stylesheet" href="<?php echo base_url('assets/imagecrop/demo_files/main.css'); ?>" type="text/css" />
<link rel="stylesheet" href="<?php echo base_url('assets/imagecrop/css/jquery.Jcrop.css'); ?>" type="text/css" />


<?php if (empty($questionOptionData)) :
    $scribble = '';
//$scribble = base_url('assets/images/no-foto.png');
else :
    $scribble = LEARNIAT_IMAGE_SCRIBBLE . $questionOptionData->image_path;
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
                                <?php if (!empty($scribble)) :
                                    $randomId = date("YmdHis");;
                                    echo "<img src='$scribble?randomId=$randomId' id='targetImg' alt='Kindly wait]'>";
                                endif; ?>

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

    /*.edit_wrapper{
        max-height: 82%;
        max-height: 82vh;
        overflow-y: auto;
        padding: 15px;
    }

    #modal_popup {
        background-color: white;
        display: none;
        left: 5%;
        position: absolute;
        top: 5%;
        width: 90%;
        z-index: 1002;
    }*/


</style>



<script>

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

                    if (imageWidth < imageWidthMin && imageHeight < imageHeightMin) {
                        alert('Image should be more than <?php echo CROP_MIN_WIDTH; ?> X <?php echo CROP_MIN_HEIGHT; ?>');

                        $('#targetImg').remove();
                        return false;
                    }
                    if (imageWidth > <?php echo CROP_MAX_WIDTH; ?> || imageHeight > <?php echo CROP_MAX_HEIGHT; ?>) {
                        alert('Image should be more than <?php echo CROP_MAX_WIDTH; ?>  X <?php echo CROP_MAX_HEIGHT; ?>');
                        $('#targetImg').remove();

                        return false;
                    } else {

                        if (imageWidth > 1000) {
                            var percentImageWidth = ((imageWidth - 1000)%10);
                            percentImageWidth += 100;
                            $('.editQuestionMatchList').attr('style', 'max-width:' + percentImageWidth + '%;width:' + percentImageWidth + '%' );
                        }

                        if (imageWidth < imageWidthMin || imageHeight < imageHeightMin) {
                            if (imageWidth < imageHeightMin && imageHeight > imageHeightMin) {
                                $('#targetImg').Jcrop({
                                    minSize : [imageWidth, (imageWidth/(1.5)) ],
                                    maxSize : [1500, 1000],
                                    setSelect:   [ 0, 0, imageWidth, (imageWidth/(1.5))],
                                    aspectRatio: 3 / 2,
                                    bgColor: '',
                                    onSelect: updateCords
                                });
                            } else if (imageHeight < imageHeightMin) {
                                $('#targetImg').Jcrop({
                                    minSize : [imageHeight * 1.5, imageHeight],
                                    maxSize : [1500, 1000],
                                    setSelect:   [ 0, 0, imageHeight * 1.5, imageHeight ],
                                    aspectRatio: 3 / 2,
                                    bgColor: '',
                                    onSelect: updateCords
                                });
                            }


                        } else {
                            $('#targetImg').Jcrop({
                                minSize : [imageWidthMin, imageHeightMin],
                                maxSize : [1500, 1000],
                                setSelect:   [ ((imageWidth - imageWidthMin)/2), ((imageHeight - imageHeightMin)/2), imageWidthMin , imageHeightMin ],
                                aspectRatio: 3 / 2,
                                bgColor: '',
                                onSelect: updateCords
                            });
                        }
                    }
                }
            }

            reader.readAsDataURL(input.files[0]);
        }
    }


    $("#imgInp").change(function(){

        $('#targetImgDiv').html('');
        $('#targetImgDiv').html('<img src="" id="targetImg" alt="Kindly wait">');


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