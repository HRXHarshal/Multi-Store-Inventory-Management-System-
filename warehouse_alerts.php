<?php
  $page_title = 'Warehouse Alerts';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  
  // Get the filter value from POST or use default
  $threshold = isset($_POST['threshold']) ? (int)$_POST['threshold'] : 10;
  $warehouse_id = isset($_POST['warehouse']) ? (int)$_POST['warehouse'] : 0;
  
  // Get all warehouses
  $all_warehouses = find_all_warehouses();
  
  // Get low stock products based on filters
  if($warehouse_id > 0) {
    $low_stock = find_low_stock_by_warehouse($warehouse_id, $threshold);
  } else {
    // Get all low stock products across all warehouses
    $low_stock = find_products_below_quantity($threshold);
  }
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-warning-sign"></span>
          <span>LOW STOCK ALERTS</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Warehouse:</label>
                <select class="form-control" name="warehouse">
                  <option value="0" <?php if($warehouse_id === 0) echo 'selected'; ?>>All Warehouses</option>
                  <?php foreach($all_warehouses as $warehouse): ?>
                    <option value="<?php echo (int)$warehouse['id']; ?>" <?php if($warehouse_id === (int)$warehouse['id']) echo 'selected'; ?>>
                      <?php echo remove_junk($warehouse['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Alert when stock is below:</label>
                <div class="input-group">
                  <input type="number" class="form-control" name="threshold" value="<?php echo $threshold; ?>">
                  <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit">Apply Filter</button>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </form>
        
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Product Name</th>
              <th>Category</th>
              <th>Warehouse</th>
              <th class="text-center">Current Stock</th>
              <th class="text-center">Buy Price</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($low_stock)): ?>
              <?php foreach($low_stock as $product): ?>
                <tr>
                  <td><?php echo count_id(); ?></td>
                  <td><?php echo remove_junk($product['name']); ?></td>
                  <td><?php echo isset($product['categorie']) ? remove_junk($product['categorie']) : 'N/A'; ?></td>
                  <td>
                    <?php 
                      // Fix for undefined array key 'warehouse'
                      echo isset($product['warehouse']) ? remove_junk($product['warehouse']) : 'N/A'; 
                    ?>
                  </td>
                  <td class="text-center"><?php echo remove_junk($product['quantity']); ?></td>
                  <td class="text-center">₹<?php echo remove_junk($product['buy_price']); ?></td>
                  <td class="text-center">
                    <a href="edit_product.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-info btn-xs">Edit</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center">No low stock items found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>