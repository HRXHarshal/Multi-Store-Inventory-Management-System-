<?php
  $page_title = 'Home Page';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(3);
?>
<?php
 // Get all stock quantity
 $c_stock      = count_by_id('products');
 // Get all categories 
 $c_category   = count_by_id('categories');
 // Get all user except Admin
 $c_user       = count_by_id('users');
 // Get all sales
 $c_sale       = count_by_id('sales');
 // Get all products
 $products_sold   = find_higest_saleing_product('10');
 // Get all sales
 $recent_sales    = find_recent_sale_added('5');

 // Store the filter value in session to persist it
 if(isset($_POST['filter_quantity'])) {
   $_SESSION['low_stock_filter'] = (int)$_POST['filter_quantity'];
 }
 
 // Use the session value or default to 10
 $filter_quantity = isset($_SESSION['low_stock_filter']) ? $_SESSION['low_stock_filter'] : 10;
 
 // Get low stock products using the filter value
 $low_stock_products = find_products_below_quantity($filter_quantity);
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
 <div class="col-md-12">
    <div class="panel">
      <div class="jumbotron text-center">
         <h1>Welcome User <hr> Inventory Management System</h1>
         <p>Browes around to find out the pages that you can access!</p>
      </div>
    </div>
 </div>
</div>

<?php
  // Count warehouses - Fix the function call to use only one parameter
  $c_warehouse = count_by_id('warehouses');
  
  // Get warehouse statistics
  $warehouse_stats = array();
  $warehouses = find_all_warehouses_by_user($_SESSION['user_id']);
  foreach($warehouses as $warehouse) {
    $products = find_products_by_warehouse($warehouse['id']);
    $total_items = count($products);
    $total_value = 0;
    foreach($products as $product) {
      $total_value += $product['buy_price'] * $product['quantity'];
    }
    $warehouse_stats[] = array(
      'id' => $warehouse['id'],
      'name' => $warehouse['name'],
      'total_items' => $total_items,
      'total_value' => $total_value
    );
  }
?>

<!-- Add warehouse statistics to dashboard -->
<div class="row">
  <div class="col-md-4">
    <div class="panel panel-box clearfix">
      <div class="panel-icon pull-left bg-green">
        <i class="glyphicon glyphicon-home"></i>
      </div>
      <div class="panel-value pull-right">
        <h2 class="margin-top"><?php echo $c_warehouse['total']; ?></h2>
        <p class="text-muted">Warehouses</p>
      </div>
    </div>
  </div>
</div>

<!-- Add warehouse breakdown section -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Warehouse Overview</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
            <th>Warehouse</th>
              <th class="text-center">Total Items</th>
              <th class="text-center">Total Value</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($warehouse_stats as $stat): ?>
            <tr>
              <td><?php echo remove_junk($stat['name']); ?></td>
              <td class="text-center"><?php echo $stat['total_items']; ?></td>
              <td class="text-center"><?php echo number_format($stat['total_value'], 2); ?></td>
              <td class="text-center">
                <a href="view_warehouse.php?id=<?php echo (int)$stat['id']; ?>" class="btn btn-primary btn-xs">View Details</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- In the low stock section, update the form to show the current filter value -->
<div class="panel panel-default">
  <div class="panel-heading">
    <strong>
      <span class="glyphicon glyphicon-th"></span>
      <span>LOW STOCK PRODUCTS</span>
    </strong>
  </div>
  <div class="panel-body">
    <form method="post" action="">
      <div class="form-group">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="glyphicon glyphicon-filter"></i>
          </span>
          <input type="number" class="form-control" name="filter_quantity" placeholder="Filter by quantity" value="<?php echo $filter_quantity; ?>">
          <span class="input-group-btn">
            <button class="btn btn-primary" type="submit">Filter</button>
          </span>
        </div>
      </div>
    </form>
    <table class="table table-striped table-bordered table-condensed">
      <thead>
        <tr>
          <th>Product Name</th>
          <th>Quantity</th>
          <th>Alert Level</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($low_stock_products as $low_stock): ?>
          <tr>
            <td><?php echo remove_junk($low_stock['name']); ?></td>
            <td><?php echo remove_junk($low_stock['quantity']); ?></td>
            <td><?php echo remove_junk($filter_quantity); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
