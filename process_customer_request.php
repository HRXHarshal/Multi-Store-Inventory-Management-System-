<?php
  require_once('includes/load.php');
  require_once('includes/email_functions.php');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if this is an OTP verification submission
    if(isset($_POST['verify_otp'])) {
      // Verify OTP
      $session_otp = $_SESSION['purchase_otp'];
      $entered_otp = $_POST['otp'];
      $request_data = $_SESSION['temp_purchase_request'];
      
      // Check if OTP has expired (15 minutes)
      $otp_time = $_SESSION['otp_time'];
      $current_time = time();
      $time_diff = $current_time - $otp_time;
      
      if($time_diff > 900) { // 900 seconds = 15 minutes
        $_SESSION['error'] = "Verification code has expired. Please request a new one.";
        redirect('verify_purchase.php', false);
        exit;
      }
      
      if($entered_otp == $session_otp) {
        // OTP is correct, process the actual purchase request
        $product_id = $request_data['product_id'];
        $warehouse_id = $request_data['warehouse_id'];
        $quantity = $request_data['quantity'];
        $customer_name = $request_data['customer_name'];
        $email = $request_data['email'];
        $phone = $request_data['phone'];
        $address = $request_data['address'];
        $notes = $request_data['notes'];
        
        // Create or find customer
        $customer_id = find_or_create_customer($customer_name, $email, $phone, $address);
        
        if (!$customer_id) {
          $_SESSION['error'] = "Failed to process customer information.";
          redirect('customer_view.php', false);
          exit;
        }
        
        // Create purchase request
        if (create_purchase_request($customer_id, $product_id, $quantity, $notes)) {
          $_SESSION['success'] = "Your purchase request has been verified and submitted successfully! Our team will contact you shortly.";
        } else {
          $_SESSION['error'] = "Failed to submit purchase request. Please try again.";
        }
        
        // Clear temporary session data
        unset($_SESSION['purchase_otp']);
        unset($_SESSION['temp_purchase_request']);
        unset($_SESSION['otp_time']);
        
        redirect('customer_view.php', false);
        exit;
      } else {
        // Invalid OTP
        $_SESSION['error'] = "Invalid verification code. Please try again.";
        redirect('verify_purchase.php', false);
        exit;
      }
    } else {
      // This is the initial form submission
      // Validate required fields
      $required_fields = array('product-id', 'warehouse-id', 'quantity', 'customer-name', 'email');
      $errors = array();
      
      foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
          $errors[] = "The {$field} field is required.";
        }
      }
      
      if (!empty($errors)) {
        // Store errors in session and redirect back
        $_SESSION['errors'] = $errors;
        redirect('customer_view.php', false);
        exit;
      }
      
      // Sanitize and prepare data
      $product_id = (int)$_POST['product-id'];
      $warehouse_id = (int)$_POST['warehouse-id'];
      $quantity = (int)$_POST['quantity'];
      $customer_name = remove_junk($db->escape($_POST['customer-name']));
      $email = remove_junk($db->escape($_POST['email']));
      $phone = remove_junk($db->escape($_POST['phone']));
      $address = remove_junk($db->escape($_POST['address']));
      $notes = remove_junk($db->escape($_POST['notes']));
      
      // Verify product exists and has enough stock
      $sql = "SELECT p.*, w.name as warehouse_name 
              FROM products p 
              JOIN warehouses w ON p.warehouse_id = w.id 
              WHERE p.id = '{$product_id}' AND p.warehouse_id = '{$warehouse_id}'";
      $product = find_by_sql($sql);
      
      if (empty($product)) {
        $_SESSION['error'] = "Product not found.";
        redirect('customer_view.php', false);
        exit;
      }
      
      $product = $product[0];
      
      if ($product['quantity'] < $quantity) {
        $_SESSION['error'] = "Not enough stock available. Only {$product['quantity']} units available.";
        redirect('customer_view.php', false);
        exit;
      }
      
      // In the process_customer_request.php file, find the section where the OTP is sent
      
      // Store request data temporarily in session
      $_SESSION['temp_purchase_request'] = array(
        'product_id' => $product_id,
        'warehouse_id' => $warehouse_id,
        'quantity' => $quantity,
        'customer_name' => $customer_name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'notes' => $notes,
        'product_name' => $product['name'],
        'warehouse_name' => $product['warehouse_name']
      );
      
      // Generate OTP
      $otp = rand(100000, 999999);
      $_SESSION['purchase_otp'] = $otp;
      $_SESSION['otp_time'] = time(); // Store the time when OTP was generated
      
      // Send OTP to customer email with order details
      $order_details = [
        'product_name' => $product['name'],
        'quantity' => $quantity,
        'warehouse_name' => $product['warehouse_name'],
        'notes' => $notes
      ];
      
      if(send_otp_email($email, $customer_name, $otp, $order_details)) {
        // Redirect to OTP verification page
        redirect('verify_purchase.php', false);
      } else {
        $_SESSION['error'] = "Failed to send verification code. Please try again.";
        redirect('customer_view.php', false);
      }
    }
  } else {
    redirect('customer_view.php', false);
  }
?>