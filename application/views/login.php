<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href='http://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
<title>Teacher-Admin</title>
<link href="<?php echo base_url();?>assets/css/student-admin-style.css" rel="stylesheet" type="text/css"  />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script type="text/javascript">
function validateForm() {
	var userName=$("#userName").val();
	var password=$("#password").val();
	if(userName=="") {
		alert("Please enter username");
		return false;
	}else if(password==""){
		alert("Please enter password");
		return false;
	}else{
		//2 Submit Form using Form's Name
		$("form[name='myForm']").submit();
		return true;
	}
}
</script>
</head>
<body>
<?php if (isset($error) && $error): ?>
  <script type="text/javascript">
      alert("<?php echo $error;?>");
  </script>
<?php endif;?>
<img src="<?php echo base_url();?>assets/images/Login-bg.jpg" alt="" id="login-bg"  />
<!--wrapper starts here-->
<div class="login-one" >
	 <div class="login-content transparent">
	  <h1>Learniat</h1>
	  <img src="<?php echo base_url();?>assets/images/student-icon.png" alt=""  class="studet" />
	  <img src="<?php echo base_url();?>assets/images/teacher-icon.png" alt=""  />
	  <div class="clear"></div>
	  <form  method="post" action="<?php echo base_url('index.php/login/ajax_check');?>" name="myForm">
		   <input type="text" placeholder="Username" class="log-input" name="username" id="userName"/> <br />
		   <input type="password" placeholder="Password" class="log-input" name="password" id="password"/>
		   <p class="form-txt">Forgot your<br />
               <span>
                   <a href="<?php echo base_url('index.php/forgot/index');?>" class="forgotpw" style="color: #00aeef!important;">password</a>
               </span>
               or
               <span>
                   <a href="<?php echo base_url('index.php/forgot/index');?>" class="forgotpw" style="color:#00aeef!important;">username</a>
               </span>
           </p>
		   <input type="button" class="log-submit" value="Sign In" name="signin" onclick="validateForm();"/>
	  </form>
	 </div>
</div>
<!--wrapper ends here-->
</body>
</html>
