<?php
  require_once('includes/load.php');
  
  // Check if warehouse_id is set
  if(isset($_POST['warehouse_id'])) {
    $warehouse_id = (int)$_POST['warehouse_id'];
    
    // Get products from the selected warehouse
    $sql = "SELECT id, name, quantity FROM products WHERE warehouse_id = '{$warehouse_id}' AND quantity > 0 ORDER BY name ASC";
    $products = find_by_sql($sql);
    
    // Generate HTML options
    echo '<option value="">Select Product</option>';
    foreach($products as $product) {
      echo '<option value="'.$product['id'].'">'.$product['name'].' (In Stock: '.$product['quantity'].')</option>';
    }
  } else {
    echo '<option value="">Select Product</option>';
  }
?>