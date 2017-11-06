<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href='http://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
    <title>Student-Admin-Forgot Password</title>
    <link href="<?php echo base_url();?>assets/css/student-admin-style.css" rel="stylesheet" type="text/css"  />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script type="text/javascript">
function validateForm() {
	var emailId=$("#emailID").val();
	if(emailId==""){
		alert("Please enter email");
		return false;
		}else{
			//2 Submit Form using Form's Name
			if(!validateEmail(emailId)){
				alert("Please enter valid email");
				return false;
				}else{
					$("form[name='myForm']").submit();
					return true;
					}
			
			}
}
function validateEmail(elementValue){    
	   var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
	   return emailPattern.test(elementValue); 
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
  <form  method="post" action="<?php echo base_url('index.php/forgot/password');?>" name="myForm">
   <input type="text" placeholder="Enter Email" class="log-input" name="emailID" id="emailID"/> <br />
   <div style="margin-left: 109px;">
   <input type="button" class="log-submit" value="Submit" name="signin" onclick="validateForm();"/>
   </div>
  </form>
 </div>
</div>
<!--wrapper ends here-->
</body>
</html>
