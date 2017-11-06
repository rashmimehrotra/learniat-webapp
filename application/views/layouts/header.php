<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="icon" type="image/png" href="<?php echo base_url('assets/images/favicon.png');?>">
<!-- <meta http-equiv="content-type" content="text/html; charset=UTF-8"> -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="UTF-8">
	<title>Learniat</title>   
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="description" content="">
	<meta name="keywords" content="coco bootstrap template, coco admin, bootstrap,admin template, bootstrap admin,">
	<meta name="author" content="Huban Creative">
	<!-- Base Css Files -->
	
	<?php $this->load->helper('url'); ?>
	
	<link rel="stylesheet" href="<?php echo base_url('assets/css/tooltips.css');?>" />
	<!-- Extra CSS Libraries Start -->
	<link rel="stylesheet" href="<?php echo base_url('assets/files/bootstrap-new.css');?>" />
	<!-- <link rel="stylesheet" href="<?php echo base_url('assets/files/animate.css');?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/files/component.css');?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/files/magnific-popup.css');?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/files/ios7-switch.css');?>" />
	 -->
	<!-- Extra CSS Libraries End -->
	
	
	<link rel="stylesheet" href="<?php echo base_url('assets/css/style-responsive.css');?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-learniant.css');?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/css/style.css?v=16092016');?>" />
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.1.3.2.min.js');?>"></script>
	<script>
		var baseUrl = '<?php echo base_url(); ?>index.php';
		var imagePath = '<?php echo base_url(); ?>/assets/images/';
	</script>
</head>
	
<body class="fixed-left  widescreen pace-done">
	<div class="pace  pace-inactive">
		<div data-progress="99" data-progress-text="100%" style="width: 100%;" class="pace-progress">
			<div class="pace-progress-inner"></div>
		</div>
		<div class="pace-activity"></div>
	</div>
		
	<!-- Begin page --><!-- End is footer.php file -->
	<div id="wrapper">
		
		<!-- Top Bar Start -->
		<div class="topbar">
			<div class="topbar-left">
				<div class="logo">
				   <a ><img src="<?php echo base_url();?>/assets/images/logo.png" alt="Logo"></a>
				</div>
				<!--<button class="button-menu-mobile open-left">
				<i class="fa fa-bars"></i>
				</button>
				 -->
			</div>
			<!-- Button mobile view to collapse sidebar menu -->
			<div class="navbar navbar-default" role="navigation">
				<div class="container">
					<!-- Profile toggle Starts -->

					<div class="onclick-menu" tabindex="0">
						<div class="profile_img">
							<a href="javascript:void(0);" class="profileOption">
								<img class="profilePic40"
								 	src="<?php echo LEARNIAT_IMAGE_PATH .  $this->session->profileData['user_id'] . '_' . IMAGE_SIZE_79;?>.jpg">
							</a>
						</div>
						<div class="profile_name">
						<a class="p1 profileOption" href="javascript:void(0);"><?php echo $this->session->profileData['fullName']; ?></a>

							<div id="dd" class="wrapper-dropdown-5" tabindex="1">
								<a style="border-bottom:0;">
								<span class="p2">Edit Profile
    								<span class="knoch">
    								    <img src="<?php echo base_url();?>/assets/images/knoch.png">
    								</span>
								</span>
								</a>
								<ul class="dropdown">
									<li><a href="<?php echo base_url(); ?>index.php/setting/index/profile">Profile</a></li>
									<li><a >Settings</a></li>
									<li><a href="<?php echo base_url(); ?>index.php/login/logout">Log out</a></li>
								</ul>
							</div>
						</div>
					</div>

					<!-- Profile toggle Ends -->
					
				</div>
			</div>
		</div>
		<!-- Top Bar End -->