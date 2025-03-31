<?php
  $page_title = 'All Product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  
  // Get all warehouses - Fix: use find_all() instead of find_all_warehouses()
  $all_warehouses = find_all('warehouses');
  
  // Filter by warehouse if selected
  $warehouse_id = isset($_GET['warehouse']) ? (int)$_GET['warehouse'] : 0;
  
  if($warehouse_id > 0) {
    $products = find_products_by_warehouse($warehouse_id);
  } else {
    $products = join_product_table();
  }
?>
<?php include_once('layouts/header.php'); ?>
  <div class="row">
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
         <div class="pull-right">
           <a href="add_product.php" class="btn btn-primary">Add New</a>
         </div>
        </div>
        <div class="panel-body">
          <!-- Warehouse filter -->
          <div class="form-group">
            <select class="form-control" id="warehouse-filter" onchange="filterByWarehouse(this.value)">
              <option value="">All Warehouses</option>
              <?php foreach ($all_warehouses as $warehouse): ?>
                <option value="<?php echo (int)$warehouse['id']; ?>" <?php if($warehouse_id === (int)$warehouse['id']): echo "selected"; endif; ?>>
                  <?php echo remove_junk($warehouse['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th class="text-center">Photo</th>
                <th>Product Title</th>
                <th>Categories</th>
                <th>Warehouse</th>
                <th class="text-center">In-Stock</th>
                <th class="text-center">Buying Price</th>
                <th class="text-center">Selling Price</th>
                <th class="text-center">Product Added</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $product):?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td class="text-center">
                  <?php if($product['media_id'] === '0'): ?>
                    <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                  <?php else: ?>
                    <?php 
                    // Get the media information
                    $media_id = (int)$product['media_id'];
                    $media = find_by_id('media', $media_id);
                    if($media): 
                    ?>
                      <img class="img-avatar img-circle" src="uploads/products/<?php echo $media['file_name']; ?>" alt="">
                    <?php else: ?>
                      <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
                <td><?php echo remove_junk($product['name']); ?></td>
                <td><?php echo isset($product['categorie']) ? remove_junk($product['categorie']) : 'N/A'; ?></td>
                <td><?php echo isset($product['warehouse']) ? remove_junk($product['warehouse']) : 'N/A'; ?></td>
                <td class="text-center"><?php echo remove_junk($product['quantity']); ?></td>
                <td class="text-center"><?php echo remove_junk($product['buy_price']); ?></td>
                <td class="text-center"><?php echo remove_junk($product['sale_price']); ?></td>
                <td class="text-center"><?php echo read_date($product['date']); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-info btn-xs"  title="Edit" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-danger btn-xs"  title="Delete" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </div>
                </td>
              </tr>
             <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    function filterByWarehouse(warehouseId) {
      if(warehouseId) {
        window.location.href = 'product.php?warehouse=' + warehouseId;
      } else {
        window.location.href = 'product.php';
      }
    }
  </script>
  
<?php include_once('layouts/footer.php'); ?>
