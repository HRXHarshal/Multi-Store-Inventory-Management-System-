<?php
  $page_title = 'Add Stock';
  require_once('includes/load.php');
  // Check user permission
  page_require_level(2);
  
  $product = find_product_by_id((int)$_GET['id']);
  if(!$product) {
    $session->msg("d", "Missing product id.");
    redirect('product.php');
  }
  
  if(isset($_POST['add_stock'])) {
    $req_fields = array('quantity');
    validate_fields($req_fields);
    
    if(empty($errors)) {
      $p_id = (int)$product[0]['id'];
      $quantity = (int)$_POST['quantity'];
      
      if($quantity <= 0) {
        $session->msg("d", "Quantity must be greater than zero.");
        redirect('add_stock.php?id='.$p_id, false);
      }
      
      $current_qty = (int)$product[0]['quantity'];
      $new_qty = $current_qty + $quantity;
      
      $sql = "UPDATE products SET quantity = '{$new_qty}' WHERE id = '{$p_id}'";
      $result = $db->query($sql);
      
      if($result && $db->affected_rows() === 1) {
        // Log the stock addition
        $date = make_date();
        $sql = "INSERT INTO stock_history (product_id, quantity_added, date, user_id)";
        $sql .= " VALUES ('{$p_id}', '{$quantity}', '{$date}', '{$_SESSION['user_id']}')";
        $db->query($sql);
        
        $session->msg("s", "Stock updated successfully.");
        redirect('product.php', false);
      } else {
        $session->msg("d", "Failed to update stock.");
        redirect('add_stock.php?id='.$p_id, false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('add_stock.php?id='.$p_id, false);
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
          <span class="glyphicon glyphicon-plus"></span>
          <span>Add Stock</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="add_stock.php?id=<?php echo (int)$product[0]['id']; ?>" class="clearfix">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-shopping-cart"></i>
                </span>
                <input type="text" class="form-control" name="product-name" value="<?php echo remove_junk($product[0]['name']); ?>" readonly>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-home"></i>
                </span>
                <input type="text" class="form-control" name="warehouse" value="<?php echo remove_junk($product[0]['warehouse']); ?>" readonly>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-list"></i>
                </span>
                <input type="text" class="form-control" name="current-stock" value="Current Stock: <?php echo (int)$product[0]['quantity']; ?>" readonly>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-plus"></i>
                </span>
                <input type="number" class="form-control" name="quantity" placeholder="Quantity to Add" min="1">
              </div>
            </div>
            <button type="submit" name="add_stock" class="btn btn-primary">Add Stock</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>