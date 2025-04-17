<?php
  require_once('includes/load.php');
  $page_title = 'Verify Purchase Request';
  
  // Check if there's a pending verification
  if(!isset($_SESSION['purchase_otp']) || !isset($_SESSION['temp_purchase_request'])) {
    redirect('customer_view.php', false);
  }
  
  $request_data = $_SESSION['temp_purchase_request'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $page_title; ?></title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="libs/css/main.css" />
  <style>
    body {
      background-color: #1e1e1e;
      color: #fff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .verification-container {
      max-width: 500px;
      margin: 50px auto;
      background-color: #2c2c2c;
      padding: 30px;
      border-radius: 5px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .verification-header {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .verification-header h3 {
      margin-top: 0;
      color: #3498db;
    }
    
    .form-control {
      background-color: #3c3c3c;
      border: 1px solid #555;
      color: #fff;
      height: 40px;
      box-shadow: none;
    }
    
    .form-control:focus {
      background-color: #3c3c3c;
      border-color: #3498db;
      color: #fff;
      box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    }
    
    .btn-primary {
      background-color: #3498db;
      border-color: #2980b9;
    }
    
    .btn-primary:hover {
      background-color: #2980b9;
    }
    
    .order-summary {
      background-color: #3c3c3c;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    
    .order-summary h4 {
      margin-top: 0;
      color: #3498db;
    }
    
    .order-detail {
      margin-bottom: 5px;
    }
    
    .order-detail strong {
      color: #ddd;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="verification-container">
      <div class="verification-header">
        <h3><i class="glyphicon glyphicon-lock"></i> Verify Your Purchase</h3>
        <p>We've sent a verification code to your email address. Please enter it below to complete your purchase request.</p>
      </div>
      
      <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
          ?>
        </div>
      <?php endif; ?>
      
      <div class="order-summary">
        <h4>Order Summary</h4>
        <div class="order-detail">
          <strong>Product:</strong> <?php echo $request_data['product_name']; ?>
        </div>
        <div class="order-detail">
          <strong>Warehouse:</strong> <?php echo $request_data['warehouse_name']; ?>
        </div>
        <div class="order-detail">
          <strong>Quantity:</strong> <?php echo $request_data['quantity']; ?>
        </div>
        <div class="order-detail">
          <strong>Customer:</strong> <?php echo $request_data['customer_name']; ?>
        </div>
        <div class="order-detail">
          <strong>Email:</strong> <?php echo $request_data['email']; ?>
        </div>
      </div>
      
      <form method="post" action="process_customer_request.php">
        <div class="form-group">
          <label for="otp">Verification Code</label>
          <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter the 6-digit code" required>
        </div>
        <input type="hidden" name="verify_otp" value="1">
        <button type="submit" class="btn btn-primary btn-block">Verify & Complete Purchase</button>
      </form>
      
      <div class="text-center" style="margin-top: 20px;">
        <p>Didn't receive the code? <a href="resend_otp.php">Resend Code</a></p>
        <p><a href="customer_view.php">Cancel and return to products</a></p>
      </div>
    </div>
  </div>
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</body>
</html>