<?php
  $page_title = 'Transfer Product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  
  // Get all warehouses
  $all_warehouses = find_all_warehouses();
  
  // Get all products
  $all_products = find_all_products();
  
  // Process form
  if(isset($_POST['transfer_product'])){
    $req_fields = array('product-id', 'source-warehouse', 'destination-warehouse', 'quantity');
    validate_fields($req_fields);
    
    if(empty($errors)){
      $p_id      = (int)$_POST['product-id'];
      $s_w_id    = (int)$_POST['source-warehouse'];
      $d_w_id    = (int)$_POST['destination-warehouse'];
      $quantity  = (int)$_POST['quantity'];
      
      // Check if source and destination are different
      if($s_w_id === $d_w_id){
        $session->msg('d', 'Source and destination warehouses must be different');
        redirect('transfer_product.php', false);
      }
      
      // Check if product exists in source warehouse
      $product = find_product_by_id_and_warehouse($p_id, $s_w_id);
      if(!$product){
        $session->msg('d', 'Product not found in source warehouse');
        redirect('transfer_product.php', false);
      }
      
      // Check if quantity is valid
      if($quantity <= 0 || $quantity > $product['quantity']){
        $session->msg('d', 'Invalid quantity. Available: '.$product['quantity']);
        redirect('transfer_product.php', false);
      }
      
      // Check if product already exists in destination warehouse
      $dest_product = find_product_by_name_and_warehouse($product['name'], $d_w_id);
      
      // Start transaction
      global $db;
      $db->query("START TRANSACTION");
      
      // Reduce quantity in source warehouse
      $sql = "UPDATE products SET quantity = quantity - {$quantity} WHERE id = '{$p_id}' AND warehouse_id = '{$s_w_id}'";
      $result = $db->query($sql);
      
      if(!$result) {
        $db->query("ROLLBACK");
        $session->msg('d', 'Failed to update source warehouse');
        redirect('transfer_product.php', false);
      }
      
      // Transfer to destination warehouse
      if($dest_product) {
        // Update existing product in destination warehouse
        $sql = "UPDATE products SET quantity = quantity + {$quantity} WHERE id = '{$dest_product['id']}'";
        $result = $db->query($sql);
      } else {
        // Create new product in destination warehouse with the same name
        $date = make_date();
        
        // Properly escape the product name to prevent SQL injection
        $escaped_name = $db->escape($product['name']);
        
        // Create a new product with the same name
        $sql = "INSERT INTO products (name, quantity, buy_price, sale_price, categorie_id, warehouse_id, media_id, date) ";
        $sql .= "VALUES ('{$escaped_name}', '{$quantity}', '{$product['buy_price']}', '{$product['sale_price']}', ";
        $sql .= "'{$product['categorie_id']}', '{$d_w_id}', '{$product['media_id']}', '{$date}')";
        
        $result = $db->query($sql);
      }
      
      if(!$result) {
        $db->query("ROLLBACK");
        $session->msg('d', 'Failed to update destination warehouse');
        redirect('transfer_product.php', false);
      }
      
      // Record the transfer
      $user_id = (int)$_SESSION['user_id'];
      $sql = "INSERT INTO product_transfers (product_id, source_warehouse_id, destination_warehouse_id, quantity, transfer_date, user_id) ";
      $sql .= "VALUES ('{$p_id}', '{$s_w_id}', '{$d_w_id}', '{$quantity}', NOW(), '{$user_id}')";
      $result = $db->query($sql);
      
      if(!$result) {
        $db->query("ROLLBACK");
        $session->msg('d', 'Failed to record transfer');
        redirect('transfer_product.php', false);
      }
      
      // Commit transaction
      $db->query("COMMIT");
      $session->msg('s', "Product transferred successfully");
      redirect('transfer_product.php', false);
    } else {
      $session->msg('d', $errors);
      redirect('transfer_product.php', false);
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
          <span class="glyphicon glyphicon-transfer"></span>
          <span>Transfer Product</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="transfer_product.php">
          <div class="form-group">
            <label>Product</label>
            <select class="form-control" name="product-id" id="product-select" required>
              <option value="">Select Product</option>
              <?php foreach ($all_products as $product): ?>
                <option value="<?php echo (int)$product['id']; ?>" data-quantity="<?php echo (int)$product['quantity']; ?>" data-warehouse="<?php echo (int)$product['warehouse_id']; ?>">
                  <?php echo remove_junk($product['name']); ?> (Warehouse: <?php echo remove_junk($product['warehouse']); ?>, Quantity: <?php echo (int)$product['quantity']; ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Source Warehouse</label>
            <select class="form-control" name="source-warehouse" id="source-warehouse" required>
              <option value="">Select Source Warehouse</option>
              <?php foreach ($all_warehouses as $warehouse): ?>
                <option value="<?php echo (int)$warehouse['id']; ?>">
                  <?php echo remove_junk($warehouse['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Destination Warehouse</label>
            <select class="form-control" name="destination-warehouse" required>
              <option value="">Select Destination Warehouse</option>
              <?php foreach ($all_warehouses as $warehouse): ?>
                <option value="<?php echo (int)$warehouse['id']; ?>">
                  <?php echo remove_junk($warehouse['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Quantity <span id="available-quantity" class="text-info"></span></label>
            <input type="number" class="form-control" name="quantity" id="quantity-input" placeholder="Quantity" required>
          </div>
          <button type="submit" name="transfer_product" class="btn btn-primary">Transfer</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product-select');
    const sourceWarehouse = document.getElementById('source-warehouse');
    const quantityInput = document.getElementById('quantity-input');
    const availableQuantity = document.getElementById('available-quantity');
    
    // Function to update available quantity display
    function updateAvailableQuantity() {
      const selectedOption = productSelect.options[productSelect.selectedIndex];
      const productWarehouse = selectedOption.getAttribute('data-warehouse');
      const selectedWarehouse = sourceWarehouse.value;
      
      if (selectedOption.value && selectedWarehouse && productWarehouse == selectedWarehouse) {
        const quantity = selectedOption.getAttribute('data-quantity');
        availableQuantity.textContent = `(Available: ${quantity})`;
        quantityInput.max = quantity;
      } else {
        availableQuantity.textContent = '';
      }
    }
    
    // Add event listeners
    productSelect.addEventListener('change', updateAvailableQuantity);
    sourceWarehouse.addEventListener('change', updateAvailableQuantity);
  });
</script>

<?php include_once('layouts/footer.php'); ?>