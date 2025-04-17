<?php
  $page_title = 'Request Purchase';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
  
  // Get all warehouses
  $all_warehouses = find_all('warehouses');
  
  // Get all products with stock
  $all_products = find_by_sql("SELECT p.*, w.name as warehouse_name 
                              FROM products p 
                              JOIN warehouses w ON w.id = p.warehouse_id 
                              WHERE p.quantity > 0 
                              ORDER BY p.name ASC");
  
  if(isset($_POST['request_purchase'])){
    $req_fields = array('customer-name', 'product-id', 'quantity');
    validate_fields($req_fields);
    
    if(empty($errors)){
      $c_name  = remove_junk($db->escape($_POST['customer-name']));
      $c_email = remove_junk($db->escape($_POST['email']));
      $c_phone = remove_junk($db->escape($_POST['phone']));
      $c_address = remove_junk($db->escape($_POST['address']));
      $p_id     = remove_junk($db->escape($_POST['product-id']));
      $quantity = remove_junk($db->escape($_POST['quantity']));
      $notes    = remove_junk($db->escape($_POST['notes']));
      
      // Create or find customer
      $customer_id = find_or_create_customer($c_name, $c_email, $c_phone, $c_address);
      
      if($customer_id && create_purchase_request($customer_id, $p_id, $quantity, $notes)){
        $session->msg('s',"Purchase request submitted successfully!");
        redirect('request_purchase.php', false);
      } else {
        $session->msg('d','Sorry, failed to submit purchase request!');
        redirect('request_purchase.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('request_purchase.php',false);
    }
  }
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-shopping-cart"></span>
          <span>REQUEST PURCHASE</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="request_purchase.php">
          <h4>Customer Information</h4>
          <div class="form-group">
            <label>Customer Name</label>
            <input type="text" class="form-control" name="customer-name" placeholder="Customer Name" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" placeholder="Email Address">
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input type="text" class="form-control" name="phone" placeholder="Phone Number">
          </div>
          <div class="form-group">
            <label>Address</label>
            <textarea class="form-control" name="address" placeholder="Customer Address"></textarea>
          </div>
          
          <h4>Product Information</h4>
          <div class="form-group">
            <label>Product</label>
            <select class="form-control" name="product-id" required>
              <option value="">Select Product</option>
              <?php foreach ($all_products as $prod): ?>
                <option value="<?php echo (int)$prod['id'] ?>">
                  <?php echo $prod['name'] ?> - <?php echo $prod['warehouse_name'] ?> (In Stock: <?php echo (int)$prod['quantity'] ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Quantity</label>
            <input type="number" class="form-control" name="quantity" placeholder="Quantity" required>
          </div>
          <div class="form-group">
            <label>Notes</label>
            <textarea class="form-control" name="notes" placeholder="Additional notes or special instructions"></textarea>
          </div>
          <button type="submit" name="request_purchase" class="btn btn-primary">Submit Request</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>