<?php
include 'db.php';
require('phpmailer/PHPMailerAutoload.php');
if(!empty($_POST))
{
  extract($_POST);
  if($who_posted==1)
    header('Content-Type: application/json');
  $match = "SELECT * FROM `User` WHERE `username` = '$username' OR `email` = '$email'";
  $result = $cxn->query($match);
  $row = $result->fetch_assoc();
  $password = md5($password);
  if($result->num_rows>0)
  {
     $jresp[]=array('error_code' => "username/email/sap already registered");
     if($who_posted==0)
    echo "<html><body><h1>Username or email or sap already registered</h1><body></html>";
  else
    echo json_encode($jresp);
}
  else
  {
  $mail = new PHPMailer();  // create a new object
  $mail->IsSMTP(); // enable SMTP
  $mail->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only
  $mail->SMTPAuth = true;  // authentication enabled
  $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
  $mail->Host = 'smtp.gmail.com';
  $message = "
  
  Thanks $username for signing up!
  Your account has been created!
  Please click this link to activate your account:
  http://localhost/acm_test/verify.php?e=$email&h=$password

  If this email is delivered to your spam box,please mark this as 'Not Spam'.
  
  ";
  $mail->Timeout = 3600; 
  $mail->Username = 'rakeshsemlani9@gmail.com';  
  $mail->Password = 'goobypls';           
  $mail->SetFrom('noreply@djacm.in', 'DJSCE ACM');
  $mail->Subject = "ACM ACCOUNT VERIFICATION";
  $mail->Port       = 465;                   
  $mail->SMTPSecure = "ssl"; 
  $mail->Body = $message;
  $mail->AddAddress($email);
  if(!$mail->Send()) {
    $error = 'Mail error: '.$mail->ErrorInfo; 
    $jresp[]=array('error_code' => "Please try again.Make sure email is valid");
    if($who_posted==0)
      echo "<html><body><h1>Something Went Wrong while sending you the Verification Email!,Please try Registering again</h1><body></html>";
    else
      echo json_encode($jresp);
    return false;
  } else {
    $query="INSERT INTO `u357445461_acm`.`User` (`id`,`username`,`hash`,`email`,`sap`,`active`,`dept`,`year`) VALUES (NULL, '$username','$password','$email','$sap','0','$department','$year')";    
    $res=$cxn->query($query);
    $jresp[]=array('error_code' => "Registeration successful.Activation Link has been sent to your email");
    if($who_posted==0)
      echo "<html><body><h1>Registeration successful</h1><br>A Verification link has been sent to your Email.Please click on the link to Verify your account<body></html>";
    else
      echo json_encode($jresp);
    return true;
  }
}
}
else
{
  $jresp[]=array('error_code' => "Please Try Again");
  if($who_posted==0)
    echo "<html><body><h1>Something Went Wrong,Please try Again!</h1><body></html>";
  else
    echo json_encode($jresp);
}
?>




