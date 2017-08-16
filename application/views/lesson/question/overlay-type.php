

<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>-->
<!--<script src="<?php echo base_url('assets/imagecrop/js/jquery.min.js'); ?>"></script>-->
<script src="<?php echo base_url('assets/imagecrop/js/jquery.cropit.js'); ?>"></script>
<script src="<?php echo base_url('assets/imagecrop/js/jquery.Jcrop.js'); ?>"></script>
<script src="https://use.fontawesome.com/7c6fb8299d.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
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
	<style>
	
    </style>
	<div class="container-fluid" style="
    background-color: #ededed;
    padding: 20px;
">
		<div class="row">
		
		
		</div>
		<div class="row">
			<div class="col-md-12 image-editor">
				<div class="row" style="margin:10px;background-color:white;padding: 10px;border-radius:5px;">
						<div class="col-md-2 col-xs-2">
							<button  type="button" class="btn  pull-left" id="js-edit" style="background:#016da0;color:white;<?php echo (!empty($scribble) && file_exists($relativePath))?'':'display:none;'?>" onclick="editImage();return false;" >Change Image</button>
							<button  type="button" class="btn  pull-left" id="js-cancel" style="background:#016da0;display:none;color:white;" onclick	="cancelEdit();return false;" >Cancel</button>
							
						</div>
						<div class="col-md-3 col-xs-3 col-md-offset-1" style="margin-top: 3px;">
							<span class="edit-control" style="display:none;">
								<i class="fa fa-picture-o" aria-hidden="true" style="display: inline-block;vertical-align: middle;color:#016da0;"></i> 
								<div style="display: inline-block;vertical-align: middle;"><input type="range" class="cropit-image-zoom-input slider"></div>
								<i class="fa fa-2x fa-picture-o" aria-hidden="true" style="display: inline-block;vertical-align: middle;margin-top:-5px;color:#016da0;"></i> 
							</span>
						</div>
						<div class="col-md-1 col-xs-1">
							<span class="edit-control" style="display:none;">
								<button class="rotation rotate-ccw slider" onclick="rotateLeft();return false;" style="height: 30px; width: 30px;margin-left:10px;margin-right:10px;">
									<i class="fa fa-undo" aria-hidden="true"></i>
								</button>
							</span>
						</div>
						
						<div class="col-md-1 col-xs-1 edit-control" style="display:none;">
							<span class="edit-control" style="display:none;">
								<button class=" rotation rotate-cw slider" onclick="rotateRight();return false;" style="height: 30px; width: 30px;margin-left:10px;margin-right:10px;">
									<i class="fa fa-repeat" aria-hidden="true"></i>
								</button>
							</span>
						</div>
						<div class="col-md-2 col-xs-2">
							<span class="edit-control" style="display:none;">
								<button  type="button" class="btn  pull-left zoomFill" id="" style="background:#016da0;color:white;" onclick="fill();return false;" >Fill Image</button>
								<button  type="button" class="btn  pull-left zoomFit" id="" style="background:#016da0;color:white;display:none;" onclick="fit();return false;" >Fit Image</button>
							</span>
						</div>
						<div class="col-md-1 col-xs-1 col-md-offset-1 button-add fileOption slider pull-right image-editor" style="cursor:pointer;<?php echo (!empty($scribble) && file_exists($relativePath))?'display:none;':''?>">
							<span id="browse-header"style="ursor:pointer;text-align: center;display: block;margin: 0 auto;">Browse</span>
							<input type="file" class="btn cropit-image-input" style="cursor:pointer;">
							
						</div>
				</div>
				<div class="row" style="margin-top:80px;">

					<div class="cropit-preview" style="display:none;"></div>

				</div>
			</div>
		</div>
		<div class="row" style="margin-top:50px;">
			 <div class="col-md-12" id="targetImgDiv" style="<?php echo (!empty($scribble) && file_exists($relativePath))?'':'display:none;'?>">
				<?php
				if (!empty($scribble) && file_exists($relativePath)) :
					$randomId = date("YmdHis");
					echo "<img src='$scribble?randomId=$randomId' id='targetImg' alt='Kindly wait]'>";
				elseif (!file_exists($relativePath)) :
					echo '<span class="image-noreply">Image Not Available</span>';
				endif;

				?>
				
			</div>
			<input type="hidden" id='editScribble' name='editScribble' value="0">
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />
			<input type="hidden" id="option" value='0'/>
		
		
		</div>
	</div>
</table>

<style>

	.editQuestionMatchList {
    width: 100%;
    background: none;
    border: none;
    border-radius: 6px;
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    border-collapse: separate;
    padding: 10px;
    margin-top: 10px;
}

		.rotation
		{
		background-color: Transparent;
		background-repeat:no-repeat;
		border: none;
		cursor:pointer;
		overflow: hidden;
		outline:none;
		}
      .cropit-preview {
        
        border: 5px solid #ccc;
        border-radius: 3px;
        m
		float: none;
		margin: 0 auto;"
      }

      .cropit-preview-image-container {
        cursor: move;
      }

      .cropit-preview-background {
        opacity: .4;
        cursor: auto;
      }

      .image-size-label {
        margin-top: 10px;
      }

      input, .export .rotate{
        /* Use relative position to prevent from being covered by image background */
        position: relative;
        z-index: 10;
        display: block;
      }

      
	 .slider	{
			display: table-cell;
			margin-left:10px;
			margin-right:10px;
			}
	#slider-wrap	{
		margin-bottom:20px;
		display: table;
		width:100%;
	}
    .cropit-image-preview {
        background-color: #f8f8f8;
        background-size: cover;
        border: 5px solid #ccc;
        border-radius: 3px;
        margin-top: 7px;
        cursor: move;
    }
    .image-editor {
        
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



	$(function() {
        $('.image-editor').cropit({
         // exportZoom: 1.25,
          imageBackground: true,
         imageBackgroundBorderWidth: 40,
		  width:1024,
		  height:768,
		  exportZoom:1,
		  allowDragNDrop:true,
		  minZoom:'fit',
		  maxZoom:3,
		  initialZoom:'min',
		  smallImage:'reject',
		  onImageError: function(error) {
			alert(error.message+', min. image dimensions accepted are 342 (width) x 256 (height). Please try again!' );
		  },
		  onFileChange: function(){
			$('.edit-control').css('display','block');
			$('.cropit-preview').css('display','block');
		  
		  },
		  onImageLoaded: function(){
			document.getElementById('browse-header').innerHTML = 'Change';
            $('.fileOption').css('background-color','#da4f49');
            $('.fileOption').css('background','#da4f49');
            //$('#browse-header').css('color','#da4f49');
		  
		  }
        });
		//342 , 256
       
		
        $('.export').click(function() {
          var imageData = $('.image-editor').cropit('export',{ type:'image/jpeg',
		  fillBg:'#000',width:1024,quality: 1,
		  height:768,});
          window.open(imageData);
        });
      });
	   function rotateRight() {
          $('.image-editor').cropit('rotateCW');
        }
        function rotateLeft(){
          $('.image-editor').cropit('rotateCCW');
        }

	$('#js-reset').click(function() {
         
		
		/* var image_x = document.getElementById('targetImg');
		image_x.parentNode.removeChild(image_x);
		image_x = document.getElementById('canvas');
		image_x.parentNode.removeChild(image_x); */
    });
	function editImage()
		{
			$('#editScribble').val(1);
			$('#js-edit').css('display','none');
			$('#targetImgDiv').css('display','none');
			$('#js-cancel').css('display','block');
			//$('.image-editor').css('display','block');
			$('.button-add').css('display','block');
		
		}
		function cancelEdit()
		{
			$('#editScribble').val(0);
			$('#js-edit').css('display','block');
			$('#targetImgDiv').css('display','block');
			$('#js-cancel').css('display','none');
			//$('.image-editor').css('display','none');
			$('.button-add').css('display','none');
		
		}
	function fill()
	{
		$('.image-editor').cropit('minZoom', 'fill');
		$('.image-editor').cropit({
         // exportZoom: 1.25,
          imageBackground: true,
         imageBackgroundBorderWidth: 40,
		  width:1024,
		  height:768,
		  exportZoom:1,
		  allowDragNDrop:true,
		  minZoom:'fill',
		  maxZoom:3,
		  initialZoom:'min',
		  smallImage:'reject'
        });
		$('.zoomFit').css('display','block');
		$('.zoomFill').css('display','none');
	
	}
	function fit()
	{
		//$('.cropit-preview-image').attr("src",'');
		$('.image-editor').cropit('minZoom', 'fit');
		$('.image-editor').cropit({
         // exportZoom: 1.25,
          imageBackground: true,
         imageBackgroundBorderWidth: 40,
		  width:1024,
		  height:768,
		  exportZoom:1,
		  allowDragNDrop:true,
		  minZoom:'fit',
		  maxZoom:3,
		  initialZoom:'min',
		  smallImage:'reject'
        });
		$('.zoomFit').css('display','none');
		$(".cropit-image-zoom-input").val(0);
		//alert($(".cropit-image-zoom-input").val());
		//$(this).off('click');
		$('.zoomFill').css('display','block');
		
	
	}
    $('#save').click(function() {
         
		/* if(canvasFlag==1)
		{
			 */
			 var imageData = $('.image-editor').cropit('export',{ type:'image/png',
		  fillBg:'black',width:1024,
		  height:768,});
			 $('#x').val(0);
			$('#y').val(0);
			$('#w').val(1024);
			$('#h').val(768);
			 
				//console.log(imageData);
		/* }else
			var imageData = $('.jcrop-holder > img').attr('src'); */
		$('.jcrop-holder > img').attr('src');
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
		console.log(c.x + ' ' +c.y + ' ' + c.w+' '+c.h);
    };
</script>