<?php
  ob_start();
  require_once('includes/load.php');
  if($session->isUserLoggedIn(true)) { redirect('home.php', false);}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Inventory Management System</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="libs/css/brand.css" />
</head>
<body class="login-page-body">

<div class="login-container">
  <div class="login-info">
    <h1>Welcome to Inventory System</h1>
    <p>Streamline your inventory management with our comprehensive solution. Track products, manage warehouses, and optimize your business operations.</p>
  </div>
  
  <div class="login-form">
    <h2>USER LOGIN</h2>
    
    <?php echo display_msg($msg); ?>
    
    <form method="post" action="auth.php" class="clearfix">
      <div class="form-group">
        <input type="text" class="form-control" name="username" placeholder="Username">
      </div>
      
      <div class="form-group">
        <input type="password" name="password" class="form-control" placeholder="Password">
      </div>
      
      <button type="submit" class="btn-login">LOGIN</button>
    </form>
    
    <!-- View as Customer option -->
    <div style="text-align: center; margin-top: 15px;">
      <a href="customer_view.php" style="color: #4facfe; text-decoration: none;">View as Customer</a>
    </div>
  </div>
</div>

</body>
</html>
