<?php
  require_once('includes/load.php');
  require_once('includes/email_functions.php');
  
  // Check if there's a pending verification
  if(!isset($_SESSION['temp_purchase_request'])) {
    redirect('customer_view.php', false);
  }
  
  $request_data = $_SESSION['temp_purchase_request'];
  
  // Generate new OTP
  $otp = rand(100000, 999999);
  $_SESSION['purchase_otp'] = $otp;
  $_SESSION['otp_time'] = time(); // Reset the OTP time
  
  // Send OTP to customer email
  if(send_otp_email($request_data['email'], $request_data['customer_name'], $otp)) {
    $_SESSION['success'] = "A new verification code has been sent to your email.";
  } else {
    $_SESSION['error'] = "Failed to send verification code. Please try again.";
  }
  
  redirect('verify_purchase.php', false);
?>