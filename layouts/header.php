<?php $user = current_user(); ?>
<!DOCTYPE html>
  <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title><?php if (!empty($page_title))
           echo remove_junk($page_title);
            elseif(!empty($user))
           echo ucfirst($user['name']);
            else echo "Inventory Management System";?>
    </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <link rel="stylesheet" href="libs/css/main.css" />
    <!-- Custom CSS for compact dashboard -->
    <style>
      .panel-box {
        height: auto;
        display: flex;
        margin-bottom: 15px;
      }
      .panel-icon {
        width: 85px;
        height: 85px;
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .panel-icon i {
        font-size: 32px;
      }
      .panel-value {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 10px;
      }
      .panel-value h2 {
        margin: 0;
        font-size: 28px;
      }
      .panel-value p {
        margin: 0;
      }
      /* Make dashboard rows display horizontally on wider screens */
      @media (min-width: 992px) {
        .dashboard-stats .row {
          display: flex;
          flex-wrap: wrap;
        }
      }
    </style>
    <!-- Product Page Action Buttons Styling -->
    <style>
      /* Specific styling for product table action buttons */
      .table td.text-center .btn-group {
        display: flex;
        justify-content: center;
      }
      
      .table td.text-center .btn {
        margin: 0 2px;
        padding: 4px 8px;
        border-radius: 3px;
      }
      
      /* Match the styling of the category page buttons */
      .table td.text-center .btn-info {
        background-color: #4facfe;
        border-color: #4facfe;
      }
      
      /* Make the edit icon more visible */
      .table td.text-center .btn-info .glyphicon {
        color: white;
      }
      
      .table td.text-center .btn-danger {
        background-color: #ff5e62;
        border-color: #ff5e62;
      }
      
      /* Make the delete icon more visible */
      .table td.text-center .btn-danger .glyphicon {
        color: white;
      }
    </style>
    <!-- Header Styling -->
    <style>
      /* Top-left logo section styling */
      .logo.pull-left {
        background-color: #242939; /* Match sidebar color */
        padding: 0;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 250px; /* Increased width further */
      }
      
      .logo.pull-left img {
        height: 100%;
        width: 100%;
        object-fit: cover;
      }
      
      /* Adjust header date position */
      .header-date.pull-left {
        margin-left: 35px; /* Add more space to shift time to the right */
      }
      
      /* Ensure profile section stays at extreme right */
      .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: calc(100% - 250px); /* Adjust based on new logo width */
      }
    </style>
    
    <!-- Dashboard Sections Styling -->
    <style>
      /* Styling for the three main dashboard sections */
      .panel-default {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        margin-bottom: 25px;
        overflow: hidden;
      }
      
      .panel-default:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        transform: translateY(-5px);
      }
      
      /* Custom headers for each section */
      .panel-heading {
        padding: 15px 20px;
        border: none;
        font-weight: 600;
        border-radius: 10px 10px 0 0;
      }
      
      /* Highest Selling Products */
      .panel:nth-of-type(1) .panel-heading {
        background: linear-gradient(to right, #00b09b, #96c93d);
        color: white;
      }
      
      /* Latest Sales */
      .panel:nth-of-type(2) .panel-heading {
        background: linear-gradient(to right, #ff9966, #ff5e62);
        color: white;
      }
      
      /* Recently Added Products */
      .panel:nth-of-type(3) .panel-heading {
        background: linear-gradient(to right, #4facfe, #00f2fe);
        color: white;
      }
      
      .panel-heading strong {
        display: flex;
        align-items: center;
      }
      
      .panel-heading .glyphicon {
        margin-right: 10px;
        font-size: 16px;
      }
      
      .panel-body {
        padding: 20px;
        background-color: white;
      }
      
      /* Table styling */
      .table {
        margin-bottom: 0;
      }
      
      .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
      }
      
      .table-bordered {
        border: 1px solid #f0f0f0;
      }
      
      .table-bordered > thead > tr > th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #eee;
        color: #555;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
      }
      
      .table-bordered > tbody > tr > td {
        border: 1px solid #f0f0f0;
        padding: 12px 15px;
      }
      
      /* Product list styling */
      .list-group {
        border-radius: 4px;
      }
      
      .list-group-item {
        border: none;
        border-bottom: 1px solid #f0f0f0;
        padding: 15px;
        transition: all 0.2s;
      }
      
      .list-group-item:last-child {
        border-bottom: none;
      }
      
      .list-group-item:hover {
        background-color: rgba(0, 0, 0, 0.02);
      }
      
      .list-group-item-heading {
        font-weight: 500;
        display: flex;
        align-items: center;
      }
      
      .img-avatar {
        width: 40px;
        height: 40px;
        margin-right: 15px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #eee;
      }
      
      .label-warning {
        background-color: #ff9966;
        font-weight: 400;
        padding: 5px 10px;
        border-radius: 20px;
      }
      
      /* Links in tables */
      .panel-body a {
        color: #4facfe;
        transition: all 0.2s;
      }
      
      .panel-body a:hover {
        color: #00b09b;
        text-decoration: none;
      }
    </style>
    <!-- Add this line right before the closing </head> tag -->
    <link rel="stylesheet" href="libs/css/form-fixes.css">
    </head>
    <link rel="stylesheet" href="libs/css/brand.css">
  </head>
  <body>
  <?php  if ($session->isUserLoggedIn(true)): ?>
    <header id="header">
      <div class="logo pull-left">
        <img src="uploads/logo/inventory-management-processes.jpg" alt="Inventory System Logo">
      </div>
      <div class="header-content">
      <div class="header-date pull-left">
        <strong><?php 
        // Set timezone to IST
        date_default_timezone_set('Asia/Kolkata');
        echo date("F j, Y, g:i a"); 
        ?></strong>
      </div>
      <div class="pull-right clearfix">
        <ul class="info-menu list-inline list-unstyled">
          <li class="profile">
            <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
              <?php 
              // Check if user has an image, if not use default
              $user_image = !empty($user['image']) ? $user['image'] : 'admin.jpg';
              ?>
              <img src="uploads/users/<?php echo $user_image; ?>" alt="user-image" class="img-circle img-inline">
              <span><?php echo remove_junk(ucfirst($user['name'])); ?> <i class="caret"></i></span>
            </a>
            <ul class="dropdown-menu">
              <li>
                  <a href="profile.php?id=<?php echo (int)$user['id'];?>">
                      <i class="glyphicon glyphicon-user"></i>
                      Profile
                  </a>
              </li>
             <li>
                 <a href="edit_account.php" title="edit account">
                     <i class="glyphicon glyphicon-cog"></i>
                     Settings
                 </a>
             </li>
             <li class="last">
                 <a href="logout.php">
                     <i class="glyphicon glyphicon-off"></i>
                     Logout
                 </a>
             </li>
           </ul>
          </li>
        </ul>
      </div>
     </div>
    </header>
    <div class="sidebar">
      <?php if($user['user_level'] === '1'): ?>
        <!-- admin menu -->
      <?php include_once('admin_menu.php');?>

      <?php elseif($user['user_level'] === '2'): ?>
        <!-- Special user -->
      <?php include_once('special_menu.php');?>

      <?php elseif($user['user_level'] === '3'): ?>
        <!-- User menu -->
      <?php include_once('user_menu.php');?>

      <?php endif;?>

   </div>
<?php endif;?>

<div class="page">
  <div class="container-fluid">
