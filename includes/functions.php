<?php
 $errors = array();

 /*--------------------------------------------------------------*/
 /* Function for Remove escapes special
 /* characters in a string for use in an SQL statement
 /*--------------------------------------------------------------*/
function real_escape($str){
  global $con;
  $escape = mysqli_real_escape_string($con,$str);
  return $escape;
}
/*--------------------------------------------------------------*/
/* Function for Remove html characters
/*--------------------------------------------------------------*/
function remove_junk($str){
  $str = nl2br($str);
  $str = htmlspecialchars(strip_tags($str, ENT_QUOTES));
  return $str;
}
/*--------------------------------------------------------------*/
/* Function for Uppercase first character
/*--------------------------------------------------------------*/
function first_character($str){
  $val = str_replace('-'," ",$str);
  $val = ucfirst($val);
  return $val;
}
/*--------------------------------------------------------------*/
/* Function for Checking input fields not empty
/*--------------------------------------------------------------*/
function validate_fields($var){
  global $errors;
  foreach ($var as $field) {
    $val = remove_junk($_POST[$field]);
    if(isset($val) && $val==''){
      $errors = $field ." can't be blank.";
      return $errors;
    }
  }
}
/*--------------------------------------------------------------*/
/* Function for Display Session Message
   Ex echo displayt_msg($message);
/*--------------------------------------------------------------*/
function display_msg($msg =''){
   $output = array();
   if(!empty($msg)) {
      if(is_array($msg) || is_object($msg)) {
        foreach ($msg as $key => $value) {
          $output  = "<div class=\"alert alert-{$key}\">";
          $output .= "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>";
          $output .= remove_junk(first_character($value));
          $output .= "</div>";
        }
      } else {
        $output = "<div class=\"alert alert-success\">";
        $output .= "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>";
        $output .= remove_junk($msg);
        $output .= "</div>";
      }
      return $output;
   } else {
     return "";
   }
}
/*--------------------------------------------------------------*/
/* Function for redirect
/*--------------------------------------------------------------*/
function redirect($url, $permanent = false)
{
    if (headers_sent() === false)
    {
      header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
    }

    exit();
}
/*--------------------------------------------------------------*/
/* Function for find out total saleing price, buying price and profit
/*--------------------------------------------------------------*/
function total_price($totals){
   $sum = 0;
   $sub = 0;
   foreach($totals as $total ){
     $sum += $total['total_saleing_price'];
     $sub += $total['total_buying_price'];
     $profit = $sum - $sub;
   }
   return array($sum,$profit);
}
/*--------------------------------------------------------------*/
/* Function for Readable date time
/*--------------------------------------------------------------*/
function read_date($str){
     if($str)
      return date('F j, Y, g:i:s a', strtotime($str));
     else
      return null;
  }
/*--------------------------------------------------------------*/
/* Function for  Readable Make date time
/*--------------------------------------------------------------*/
function make_date(){
  return strftime("%Y-%m-%d %H:%M:%S", time());
}
/*--------------------------------------------------------------*/
/* Function for Finding all warehouses by user ID
/*--------------------------------------------------------------*/
/* Function for Finding all warehouses by user ID
/*--------------------------------------------------------------*/
function find_all_warehouses_by_user($user_id){
  global $db;
  
  // Check if user is admin (level 1) - admins can see all warehouses
  $user_level = find_user_level($user_id);
  
  if($user_level == 1) {
    // Admin can see all warehouses
    return find_all_warehouses();
  } else {
    // Regular users only see warehouses assigned to them
    $sql = "SELECT * FROM warehouses WHERE user_id = '{$user_id}'";
    return find_by_sql($sql);
  }
}

/*--------------------------------------------------------------*/
/* Function for Finding user level
/*--------------------------------------------------------------*/
function find_user_level($user_id){
  global $db;
  $user_id = (int)$user_id;
  $sql = "SELECT user_level FROM users WHERE id = '{$user_id}' LIMIT 1";
  $result = find_by_sql($sql);
  return $result ? $result[0]['user_level'] : 0;
}
/*--------------------------------------------------------------*/
/* Function for Finding warehouse by ID
/*--------------------------------------------------------------*/
function find_by_warehouse_id($id){
  global $db;
  $id = (int)$id;
  $sql = "SELECT * FROM warehouses WHERE id = '{$id}' LIMIT 1";
  $result = find_by_sql($sql);
  return $result ? $result[0] : null;
}

/*--------------------------------------------------------------*/
/* Function for Finding all products by warehouse ID
/*--------------------------------------------------------------*/
function find_products_by_warehouse($warehouse_id) {
  global $db;
  $sql = "SELECT p.id, p.name, p.quantity, p.buy_price, p.sale_price, p.media_id, p.date, 
          c.name AS categorie, w.name AS warehouse 
          FROM products p 
          LEFT JOIN categories c ON c.id = p.categorie_id 
          LEFT JOIN warehouses w ON w.id = p.warehouse_id 
          WHERE p.warehouse_id = '{$warehouse_id}'
          ORDER BY p.id DESC";
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function for  Readable date time
/*--------------------------------------------------------------*/
function count_id(){
  static $count = 1;
  return $count++;
}
/*--------------------------------------------------------------*/
/* Function for Creting random string
/*--------------------------------------------------------------*/
function randString($length = 5)
{
  $str='';
  $cha = "0123456789abcdefghijklmnopqrstuvwxyz";

  for($x=0; $x<$length; $x++)
   $str .= $cha[mt_rand(0,strlen($cha))];
  return $str;
}


/*--------------------------------------------------------------*/
/* Function for Finding all product join with category and warehouse
/*--------------------------------------------------------------*/
function join_product_table() {
  global $db;
  $sql  =" SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.media_id,p.date,c.name";
  $sql  .=" AS categorie,m.file_name AS image,w.name AS warehouse";
  $sql  .=" FROM products p";
  $sql  .=" LEFT JOIN categories c ON c.id = p.categorie_id";
  $sql  .=" LEFT JOIN media m ON m.id = p.media_id";
  $sql  .=" LEFT JOIN warehouses w ON w.id = p.warehouse_id";
  $sql  .=" ORDER BY p.id ASC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Finding product by id with category and warehouse
/*--------------------------------------------------------------*/
function find_product_by_id($id){
  global $db;
  $id = (int)$id;
  $sql = "SELECT p.*, c.name AS categorie, w.name AS warehouse";
  $sql .= " FROM products p";
  $sql .= " LEFT JOIN categories c ON c.id = p.categorie_id";
  $sql .= " LEFT JOIN warehouses w ON w.id = p.warehouse_id";
  $sql .= " WHERE p.id = '{$id}'";
  $sql .= " LIMIT 1";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Finding all transfer history
/*--------------------------------------------------------------*/
function find_all_transfer_history(){
  global $db;
  $sql = "SELECT t.*, p.name AS product_name, ";
  $sql .= "w1.name AS source_warehouse, w2.name AS destination_warehouse, ";
  $sql .= "u.name AS transferred_by ";
  $sql .= "FROM product_transfers t ";
  $sql .= "LEFT JOIN products p ON p.id = t.product_id ";
  $sql .= "LEFT JOIN warehouses w1 ON w1.id = t.source_warehouse_id ";
  $sql .= "LEFT JOIN warehouses w2 ON w2.id = t.destination_warehouse_id ";
  $sql .= "LEFT JOIN users u ON u.id = t.user_id ";
  $sql .= "ORDER BY t.transfer_date DESC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Finding transfer history by user ID
/*--------------------------------------------------------------*/
function find_transfer_history_by_user($user_id){
  global $db;
  $sql = "SELECT t.*, p.name AS product_name, ";
  $sql .= "w1.name AS source_warehouse, w2.name AS destination_warehouse, ";
  $sql .= "u.name AS transferred_by ";
  $sql .= "FROM product_transfers t ";
  $sql .= "LEFT JOIN products p ON p.id = t.product_id ";
  $sql .= "LEFT JOIN warehouses w1 ON w1.id = t.source_warehouse_id ";
  $sql .= "LEFT JOIN warehouses w2 ON w2.id = t.destination_warehouse_id ";
  $sql .= "LEFT JOIN users u ON u.id = t.user_id ";
  $sql .= "WHERE t.user_id = '{$user_id}' ";
  $sql .= "ORDER BY t.transfer_date DESC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Finding transfer history by warehouse ID
/*--------------------------------------------------------------*/
function find_transfer_history_by_warehouse($warehouse_id){
  global $db;
  $sql = "SELECT t.*, p.name AS product_name, ";
  $sql .= "w1.name AS source_warehouse, w2.name AS destination_warehouse, ";
  $sql .= "u.name AS transferred_by ";
  $sql .= "FROM product_transfers t ";
  $sql .= "LEFT JOIN products p ON p.id = t.product_id ";
  $sql .= "LEFT JOIN warehouses w1 ON w1.id = t.source_warehouse_id ";
  $sql .= "LEFT JOIN warehouses w2 ON w2.id = t.destination_warehouse_id ";
  $sql .= "LEFT JOIN users u ON u.id = t.user_id ";
  $sql .= "WHERE t.source_warehouse_id = '{$warehouse_id}' OR t.destination_warehouse_id = '{$warehouse_id}' ";
  $sql .= "ORDER BY t.transfer_date DESC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Getting warehouse statistics
/*--------------------------------------------------------------*/
function get_warehouse_stats($warehouse_id){
  global $db;
  $stats = array();
  
  // Get total products
  $sql = "SELECT COUNT(id) as total_products FROM products WHERE warehouse_id = '{$warehouse_id}'";
  $result = find_by_sql($sql);
  $stats['total_products'] = $result[0]['total_products'];
  
  // Get total value
  $sql = "SELECT SUM(quantity * sale_price) as total_value FROM products WHERE warehouse_id = '{$warehouse_id}'";
  $result = find_by_sql($sql);
  $stats['total_value'] = $result[0]['total_value'];
  
  // Get low stock items
  $sql = "SELECT COUNT(id) as low_stock FROM products WHERE warehouse_id = '{$warehouse_id}' AND quantity <= 10";
  $result = find_by_sql($sql);
  $stats['low_stock'] = $result[0]['low_stock'];
  
  return $stats;
}
/*--------------------------------------------------------------*/
/* Function for Getting low stock products by warehouse
/*--------------------------------------------------------------*/
function find_low_stock_by_warehouse($warehouse_id, $threshold = 10){
  global $db;
  $sql = "SELECT p.*, c.name AS categorie, w.name AS warehouse ";
  $sql .= "FROM products p ";
  $sql .= "LEFT JOIN categories c ON c.id = p.categorie_id ";
  $sql .= "LEFT JOIN warehouses w ON w.id = p.warehouse_id ";
  $sql .= "WHERE p.warehouse_id = '{$warehouse_id}' AND p.quantity <= {$threshold} ";
  $sql .= "ORDER BY p.quantity ASC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Transferring products between warehouses
/*--------------------------------------------------------------*/
function transfer_product($product_id, $source_warehouse_id, $destination_warehouse_id, $quantity, $user_id){
  global $db;
  $product_id = (int)$product_id;
  $source_warehouse_id = (int)$source_warehouse_id;
  $destination_warehouse_id = (int)$destination_warehouse_id;
  $quantity = (int)$quantity;
  $user_id = (int)$user_id;
  $date = make_date();
  
  // Start transaction
  $db->query("START TRANSACTION");
  
  // Check if product exists in source warehouse with enough quantity
  $sql = "SELECT * FROM products WHERE id = '{$product_id}' AND warehouse_id = '{$source_warehouse_id}'";
  $result = find_by_sql($sql);
  
  if(!$result || $result[0]['quantity'] < $quantity) {
    $db->query("ROLLBACK");
    return false;
  }
  
  // Get the product details from source
  $source_product = $result[0];
  
  // Reduce quantity in source warehouse
  $sql = "UPDATE products SET quantity = quantity - {$quantity} WHERE id = '{$product_id}' AND warehouse_id = '{$source_warehouse_id}'";
  $result = $db->query($sql);
  
  if(!$result) {
    $db->query("ROLLBACK");
    return false;
  }
  
  // Check if product exists in destination warehouse by exact name match
  $sql = "SELECT id FROM products WHERE name = '{$db->escape($source_product['name'])}' AND warehouse_id = '{$destination_warehouse_id}'";
  $result = find_by_sql($sql);
  
  if($result) {
    // Update existing product in destination warehouse
    $dest_product_id = $result[0]['id'];
    $sql = "UPDATE products SET quantity = quantity + {$quantity} WHERE id = '{$dest_product_id}'";
    $result = $db->query($sql);
  } else {
    // Copy product to destination warehouse with exact same name
    $sql = "INSERT INTO products (name, quantity, buy_price, sale_price, categorie_id, warehouse_id, media_id, date) ";
    $sql .= "VALUES ('{$db->escape($source_product['name'])}', {$quantity}, '{$source_product['buy_price']}', ";
    $sql .= "'{$source_product['sale_price']}', '{$source_product['categorie_id']}', '{$destination_warehouse_id}', ";
    $sql .= "'{$source_product['media_id']}', '{$date}')";
    $result = $db->query($sql);
  }
  
  if(!$result) {
    $db->query("ROLLBACK");
    return false;
  }
  
  // Record the transfer
  $sql = "INSERT INTO product_transfers (product_id, source_warehouse_id, destination_warehouse_id, quantity, transfer_date, user_id) ";
  $sql .= "VALUES ('{$product_id}', '{$source_warehouse_id}', '{$destination_warehouse_id}', '{$quantity}', '{$date}', '{$user_id}')";
  $result = $db->query($sql);
  
  if(!$result) {
    $db->query("ROLLBACK");
    return false;
  }
  
  // Commit transaction
  $db->query("COMMIT");
  return true;
}

/*--------------------------------------------------------------*/
/* Function for Finding all warehouses
/*--------------------------------------------------------------*/
function find_all_warehouses(){
  global $db;
  $sql = "SELECT * FROM warehouses ORDER BY name ASC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Creating a new warehouse
/*--------------------------------------------------------------*/
function create_warehouse($name, $location, $description, $user_id){
  global $db;
  $name = remove_junk($db->escape($name));
  $location = remove_junk($db->escape($location));
  $description = remove_junk($db->escape($description));
  $user_id = (int)$user_id;
  
  $sql = "INSERT INTO warehouses (name, location, description, user_id) ";
  $sql .= "VALUES ('{$name}', '{$location}', '{$description}', '{$user_id}')";
  
  if($db->query($sql)){
    return true;
  } else {
    return false;
  }
}

/*--------------------------------------------------------------*/
/* Function for Updating a warehouse
/*--------------------------------------------------------------*/
function update_warehouse($id, $name, $location, $description){
  global $db;
  $id = (int)$id;
  $name = remove_junk($db->escape($name));
  $location = remove_junk($db->escape($location));
  $description = remove_junk($db->escape($description));
  
  $sql = "UPDATE warehouses SET ";
  $sql .= "name='{$name}', location='{$location}', description='{$description}' ";
  $sql .= "WHERE id='{$id}'";
  
  if($db->query($sql)){
    return true;
  } else {
    return false;
  }
}
/*--------------------------------------------------------------*/
/* Function for Finding products below specified quantity
/*--------------------------------------------------------------*/
/*--------------------------------------------------------------*/
/* Function for Finding products below specified quantity
/*--------------------------------------------------------------*/
function find_products_below_quantity($qty) {
  global $db;
  $sql = "SELECT p.id, p.name, p.quantity, p.buy_price, p.sale_price, p.categorie_id, p.media_id, p.warehouse_id,";
  $sql .= " c.name AS categorie, m.file_name AS image, w.name AS warehouse";
  $sql .= " FROM products p";
  $sql .= " LEFT JOIN categories c ON c.id = p.categorie_id";
  $sql .= " LEFT JOIN media m ON m.id = p.media_id";
  $sql .= " LEFT JOIN warehouses w ON w.id = p.warehouse_id";
  $sql .= " WHERE CAST(p.quantity AS UNSIGNED) < '{$qty}'";
  $sql .= " ORDER BY p.quantity ASC";
  
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function for Finding product by ID and warehouse ID
/*--------------------------------------------------------------*/
/*--------------------------------------------------------------*/
/* Function for Finding product by ID and warehouse ID
/*--------------------------------------------------------------*/
/*--------------------------------------------------------------*/
/* Function for Finding product by ID and warehouse ID
/*--------------------------------------------------------------*/
function find_product_by_id_and_warehouse($product_id, $warehouse_id){
  global $db;
  $product_id = (int)$product_id;
  $warehouse_id = (int)$warehouse_id;
  
  $sql = "SELECT * FROM products WHERE id = '{$product_id}' AND warehouse_id = '{$warehouse_id}' LIMIT 1";
  $result = find_by_sql($sql);
  return $result ? $result[0] : null;
}

/*--------------------------------------------------------------*/
/* Function for Finding product by name and warehouse ID
/*--------------------------------------------------------------*/
function find_product_by_name_and_warehouse($product_name, $warehouse_id){
  global $db;
  $product_name = $db->escape($product_name);
  $warehouse_id = (int)$warehouse_id;
  
  $sql = "SELECT * FROM products WHERE name = '{$product_name}' AND warehouse_id = '{$warehouse_id}' LIMIT 1";
  $result = find_by_sql($sql);
  return $result ? $result[0] : null;
}

/*--------------------------------------------------------------*/
/* Function for Finding all products with warehouse info
/*--------------------------------------------------------------*/
function find_all_products(){
  global $db;
  $sql = "SELECT p.*, c.name AS categorie, w.name AS warehouse 
          FROM products p 
          LEFT JOIN categories c ON c.id = p.categorie_id 
          LEFT JOIN warehouses w ON w.id = p.warehouse_id 
          ORDER BY p.name ASC";
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function for Finding all sales with warehouse information
/*--------------------------------------------------------------*/
/*--------------------------------------------------------------*/
/* Function for Finding all sales with warehouse information
/*--------------------------------------------------------------*/
function find_all_sale_with_warehouse(){
  global $db;
  $sql  = "SELECT s.id, p.name, w.name as warehouse_name, s.qty, s.price, s.date ";
  $sql .= "FROM sales s ";
  $sql .= "LEFT JOIN products p ON s.product_id = p.id ";
  $sql .= "LEFT JOIN warehouses w ON p.warehouse_id = w.id ";
  $sql .= "ORDER BY s.date DESC";
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function for finding or creating a customer
/*--------------------------------------------------------------*/
function find_or_create_customer($name, $email = '', $phone = '', $address = '') {
  global $db;
  
  // Check if customer exists
  $sql = "SELECT id FROM customers WHERE ";
  if(!empty($email)) {
    $sql .= "email = '{$email}' OR ";
  }
  if(!empty($phone)) {
    $sql .= "phone = '{$phone}' OR ";
  }
  $sql .= "name = '{$name}' LIMIT 1";
  
  $result = find_by_sql($sql);
  
  if(!empty($result)) {
    return $result[0]['id'];
  } else {
    // Create new customer
    $date = make_date();
    $sql = "INSERT INTO customers (name, email, phone, address, date_added) VALUES (";
    $sql .= "'{$name}', '{$email}', '{$phone}', '{$address}', '{$date}'";
    $sql .= ")";
    
    if($db->query($sql)) {
      return $db->insert_id();
    }
  }
  return false;
}

/*--------------------------------------------------------------*/
/* Function for creating a new purchase request
/*--------------------------------------------------------------*/
function create_purchase_request($customer_id, $product_id, $quantity, $notes = '') {
  global $db;
  $date = make_date();
  
  $sql = "INSERT INTO purchase_requests (";
  $sql .= "customer_id, product_id, quantity, status, request_date, notes";
  $sql .= ") VALUES (";
  $sql .= "'{$customer_id}', '{$product_id}', '{$quantity}', 'new', '{$date}', '{$notes}'";
  $sql .= ")";
  
  if($db->query($sql)) {
    $request_id = $db->insert_id();
    // Automatically assign to an employee
    assign_purchase_request($request_id);
    return true;
  } else {
    return false;
  }
}

/*--------------------------------------------------------------*/
/* Function for assigning purchase request to employee with lowest workload
/*--------------------------------------------------------------*/
function assign_purchase_request($request_id) {
  global $db;
  
  // Get all active level 3 users (employees)
  $sql = "SELECT id FROM users WHERE user_level = 3 AND status = 1";
  $employees = find_by_sql($sql);
  
  if(empty($employees)) {
    // No employees available
    return false;
  }
  
  // Find employee with lowest workload
  $selected_employee = null;
  $lowest_workload = PHP_INT_MAX;
  
  foreach($employees as $employee) {
    // Check if employee exists in workload table
    $sql = "SELECT active_assignments FROM employee_workload WHERE user_id = '{$employee['id']}'";
    $result = find_by_sql($sql);
    
    if(empty($result)) {
      // Employee not in workload table, add them
      $sql = "INSERT INTO employee_workload (user_id, active_assignments) VALUES ('{$employee['id']}', 0)";
      $db->query($sql);
      $current_workload = 0;
    } else {
      $current_workload = $result[0]['active_assignments'];
    }
    
    if($current_workload < $lowest_workload) {
      $lowest_workload = $current_workload;
      $selected_employee = $employee['id'];
    }
  }
  
  if($selected_employee) {
    // Assign request to selected employee
    $sql = "UPDATE purchase_requests SET status = 'assigned', assigned_to = '{$selected_employee}' ";
    $sql .= "WHERE id = '{$request_id}'";
    $db->query($sql);
    
    // Update employee workload
    $sql = "UPDATE employee_workload SET active_assignments = active_assignments + 1, ";
    $sql .= "total_assignments = total_assignments + 1 WHERE user_id = '{$selected_employee}'";
    $db->query($sql);
    
    return $selected_employee;
  }
  
  return false;
}

/*--------------------------------------------------------------*/
/* Function for completing a purchase request
/*--------------------------------------------------------------*/
function complete_purchase_request($request_id) {
  global $db;
  $date = make_date();
  
  // Get request details
  $sql = "SELECT r.*, p.name as product_name, p.warehouse_id, p.sale_price,
          c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
          w.name as warehouse_name
          FROM purchase_requests r 
          JOIN products p ON p.id = r.product_id 
          JOIN customers c ON c.id = r.customer_id
          JOIN warehouses w ON w.id = p.warehouse_id
          WHERE r.id = '{$request_id}'";
  $result = find_by_sql($sql);
  
  if(empty($result)) {
    return false;
  }
  
  $request = $result[0];
  
  // Check if request is already completed
  if($request['status'] === 'completed') {
    return false;
  }
  
  $employee_id = $request['assigned_to'];
  $product_id = $request['product_id'];
  $quantity = $request['quantity'];
  $price = $request['sale_price'];
  
  // Begin transaction to ensure data integrity
  $db->query("START TRANSACTION");
  
  // 1. Update product quantity in warehouse
  $sql = "UPDATE products SET quantity = quantity - {$quantity} 
          WHERE id = '{$product_id}'";
  $result1 = $db->query($sql);
  
  // 2. Add entry to sales table
  $sql = "INSERT INTO sales (product_id, qty, price, date) 
          VALUES ('{$product_id}', '{$quantity}', '{$price}', '{$date}')";
  $result2 = $db->query($sql);
  
  // 3. Update request status
  $sql = "UPDATE purchase_requests SET status = 'completed', completion_date = '{$date}' 
          WHERE id = '{$request_id}'";
  $result3 = $db->query($sql);
  
  // 4. Update employee workload - use GREATEST to prevent negative values
  $sql = "UPDATE employee_workload SET 
          active_assignments = GREATEST(0, active_assignments - 1), 
          completed_assignments = completed_assignments + 1 
          WHERE user_id = '{$employee_id}'";
  $result4 = $db->query($sql);
  
  // Commit or rollback transaction based on success
  if($result1 && $result2 && $result3 && $result4) {
    $db->query("COMMIT");
    
    // Send confirmation email to customer
    require_once('email_functions.php');
    
    $order_details = [
      'request_id' => $request_id,
      'product_name' => $request['product_name'],
      'quantity' => $quantity,
      'warehouse_name' => $request['warehouse_name'],
      'notes' => $request['notes']
    ];
    
    send_order_confirmation_email(
      $request['customer_email'],
      $request['customer_name'],
      $order_details
    );
    
    return true;
  } else {
    $db->query("ROLLBACK");
    return false;
  }
}

/*--------------------------------------------------------------*/
/* Function for finding all purchase requests
/*--------------------------------------------------------------*/
/*--------------------------------------------------------------*/
/* Function for finding all purchase requests
/*--------------------------------------------------------------*/
function find_all_purchase_requests() {
  global $db;
  $sql = "SELECT r.*, p.name as product_name, c.name as customer_name, 
          c.email as customer_email, u.name as employee_name,
          w.name as warehouse_name
          FROM purchase_requests r 
          LEFT JOIN products p ON p.id = r.product_id 
          LEFT JOIN customers c ON c.id = r.customer_id 
          LEFT JOIN users u ON u.id = r.assigned_to 
          LEFT JOIN warehouses w ON w.id = p.warehouse_id
          ORDER BY r.request_date DESC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for finding purchase requests by employee
/*--------------------------------------------------------------*/
function find_purchase_requests_by_employee($employee_id) {
  global $db;
  $sql = "SELECT r.*, p.name as product_name, c.name as customer_name, 
          c.email as customer_email, c.phone as customer_phone 
          FROM purchase_requests r 
          LEFT JOIN products p ON p.id = r.product_id 
          LEFT JOIN customers c ON c.id = r.customer_id 
          WHERE r.assigned_to = '{$employee_id}' 
          ORDER BY r.request_date DESC";
  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for finding all customers
/*--------------------------------------------------------------*/
function find_all_customers() {
  global $db;
  $sql = "SELECT * FROM customers ORDER BY name ASC";
  return find_by_sql($sql);
}
?>
