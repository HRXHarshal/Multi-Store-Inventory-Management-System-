<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  
  $request_id = (int)$_GET['id'];
  
  if(empty($request_id)) {
    $_SESSION['error'] = "Missing request ID.";
    redirect('purchase_requests.php', false);
  }
  
  // Get request details
  $sql = "SELECT r.*, p.name as product_name, c.name as customer_name 
          FROM purchase_requests r 
          LEFT JOIN products p ON p.id = r.product_id 
          LEFT JOIN customers c ON c.id = r.customer_id 
          WHERE r.id = '{$request_id}' LIMIT 1";
  $request = find_by_sql($sql);
  
  if(empty($request)) {
    $_SESSION['error'] = "Request not found.";
    redirect('purchase_requests.php', false);
  }
  
  $request = $request[0];
  
  // Check if request is already completed
  if($request['status'] === 'completed') {
    $_SESSION['error'] = "This request has already been completed.";
    redirect('purchase_requests.php', false);
  }
  
  // Complete the request
  if(complete_purchase_request($request_id)) {
    $_SESSION['success'] = "Request approved and completed successfully. A confirmation email has been sent to the customer.";
  } else {
    $_SESSION['error'] = "Failed to approve request. Please try again.";
  }
  
  redirect('purchase_requests.php', false);
?>