<?php
  $page_title = 'View Warehouse';
  require_once('includes/load.php');
  // Check user permission
  page_require_level(1);
  
  $warehouse_id = (int)$_GET['id'];
  if(empty($warehouse_id)){
    redirect('warehouse.php');
  }
  
  $warehouse = find_by_warehouse_id($warehouse_id);
  if(!$warehouse){
    $session->msg("d","Missing warehouse id.");
    redirect('warehouse.php');
  }
  
  // Modify the query to properly join with categories table
  $products = find_products_by_warehouse($warehouse_id);
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
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Warehouse: <?php echo remove_junk($warehouse['name']); ?></span>
        </strong>
        <div class="pull-right">
          <a href="warehouse.php" class="btn btn-primary">Back to Warehouses</a>
        </div>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <tbody>
            <tr>
              <td class="text-right" style="width: 20%;">Warehouse Name:</td>
              <td><?php echo remove_junk($warehouse['name']); ?></td>
            </tr>
            <tr>
              <td class="text-right">Location:</td>
              <td><?php echo remove_junk($warehouse['location']); ?></td>
            </tr>
            <tr>
              <td class="text-right">Description:</td>
              <td><?php echo remove_junk($warehouse['description']); ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Products in this Warehouse</span>
        </strong>
        <div class="pull-right">
          <a href="add_product.php" class="btn btn-primary">Add New Product</a>
        </div>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Photo</th>
              <th>Product Name</th>
              <th>Category</th>
              <th class="text-center" style="width: 10%;">Quantity</th>
              <th class="text-center" style="width: 10%;">Buy Price</th>
              <th class="text-center" style="width: 10%;">Sale Price</th>
              <th class="text-center" style="width: 10%;">Added Date</th>
              <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if(empty($products)): ?>
              <tr>
                <td colspan="9" class="text-center">No products found in this warehouse</td>
              </tr>
            <?php else: ?>
              <?php foreach ($products as $product): ?>
                <tr>
                  <td class="text-center"><?php echo count_id();?></td>
                  <td>
                    <?php if($product['media_id'] === '0'): ?>
                      <img class="img-avatar img-circle" src="uploads/products/no_image.png" alt="">
                    <?php else: ?>
                      <img class="img-avatar img-circle" src="uploads/products/<?php echo $product['image']; ?>" alt="">
                    <?php endif; ?>
                  </td>
                  <!-- In the table where the error occurs, modify this part: -->
                                  <td><?php echo remove_junk($product['name']); ?></td>
                                  <td>
                                    <?php 
                                    // Check if category key exists before trying to access it
                                    echo isset($product['category']) ? remove_junk($product['category']) : 'N/A'; 
                                    ?>
                                  </td>
                                  <td class="text-center"><?php echo remove_junk($product['quantity']); ?></td>
                  <td class="text-center"><?php echo remove_junk($product['buy_price']); ?></td>
                  <td class="text-center"><?php echo remove_junk($product['sale_price']); ?></td>
                  <td class="text-center"><?php echo read_date($product['date']); ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                      <a href="edit_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-info btn-xs" title="Edit" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>
                      <a href="delete_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-danger btn-xs" title="Delete" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-trash"></span>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>