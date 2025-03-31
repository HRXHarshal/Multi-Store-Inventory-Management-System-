<?php
  $page_title = 'Admin Home Page';
  require_once('includes/load.php');
  // Check what level user has permission to view this page
  page_require_level(1);
?>
<?php
  $c_categorie     = count_by_id('categories');
  $c_product       = count_by_id('products');
  $c_sale          = count_by_id('sales');
  $c_user          = count_by_id('users');
  $products_sold   = find_higest_saleing_product('10');
  $recent_products = find_recent_product_added('5');
  $recent_sales    = find_recent_sale_added('5');
  
  // Get warehouse data with sales information
  $warehouse_sales = find_by_sql("SELECT w.id, w.name, w.location, 
                                 COALESCE(SUM(s.price), 0) as total_sales 
                                 FROM warehouses w 
                                 LEFT JOIN products p ON p.warehouse_id = w.id 
                                 LEFT JOIN sales s ON s.product_id = p.id 
                                 GROUP BY w.id");
?>
<?php include_once('layouts/header.php'); ?>

<!-- Add this line to include the new CSS file -->
<link rel="stylesheet" href="libs/css/dashboard-cards.css">
<!-- Include Leaflet.js for map visualization -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-3">
    <div class="card-box users">
      <div class="card-icon users">
        <i class="glyphicon glyphicon-user"></i>
      </div>
      <div class="card-content">
        <h5 class="card-title">Users</h5>
        <h2 class="card-value"><?php echo $c_user['total']; ?></h2>
        <a href="users.php" class="card-link">View Details</a>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card-box categories">
      <div class="card-icon categories">
        <i class="glyphicon glyphicon-indent-left"></i>
      </div>
      <div class="card-content">
        <h5 class="card-title">Categories</h5>
        <h2 class="card-value"><?php echo $c_categorie['total']; ?></h2>
        <a href="categorie.php" class="card-link">View Details</a>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card-box products">
      <div class="card-icon products">
        <i class="glyphicon glyphicon-th-large"></i>
      </div>
      <div class="card-content">
        <h5 class="card-title">Products</h5>
        <h2 class="card-value"><?php echo $c_product['total']; ?></h2>
        <a href="product.php" class="card-link">View Details</a>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card-box sales">
      <div class="card-icon sales">
        <i class="glyphicon glyphicon-usd"></i>
      </div>
      <div class="card-content">
        <h5 class="card-title">Sales</h5>
        <h2 class="card-value"><?php echo $c_sale['total']; ?></h2>
        <a href="sales.php" class="card-link">View Details</a>
      </div>
    </div>
  </div>
</div>

<!-- Rest of your dashboard content -->
<div class="row">
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Highest Selling Products</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-striped table-bordered table-condensed">
          <thead>
            <tr>
              <th>Title</th>
              <th>Total Sold</th>
              <th>Total Quantity</th>
            <tr>
          </thead>
          <tbody>
            <?php foreach ($products_sold as  $product_sold): ?>
              <tr>
                <td><?php echo remove_junk(first_character($product_sold['name'])); ?></td>
                <td><?php echo (int)$product_sold['totalSold']; ?></td>
                <td><?php echo (int)$product_sold['totalQty']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>LATEST SALES</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-striped table-bordered table-condensed">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Product Name</th>
              <th>Date</th>
              <th>Total Sale</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_sales as  $recent_sale): ?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td>
                  <a href="edit_sale.php?id=<?php echo (int)$recent_sale['id']; ?>">
                    <?php echo remove_junk(first_character($recent_sale['name'])); ?>
                  </a>
                </td>
                <td><?php echo remove_junk(ucfirst($recent_sale['date'])); ?></td>
                <td>$<?php echo remove_junk(first_character($recent_sale['price'])); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Recently Added Products</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="list-group">
          <?php foreach ($recent_products as  $recent_product): ?>
            <a class="list-group-item clearfix" href="edit_product.php?id=<?php echo    (int)$recent_product['id'];?>">
              <h4 class="list-group-item-heading">
                <?php if($recent_product['media_id'] === '0'): ?>
                  <img class="img-avatar img-circle" src="uploads/products/no_image.png" alt="">
                <?php else: ?>
                  <img class="img-avatar img-circle" src="uploads/products/<?php echo $recent_product['image'];?>" alt="" />
                <?php endif;?>
                <?php echo remove_junk(first_character($recent_product['name']));?>
                <span class="label label-warning pull-right">
                  $<?php echo (float)$recent_product['sale_price']; ?>
                </span>
              </h4>
              <span class="list-group-item-text pull-right">
                <?php echo remove_junk(first_character($recent_product['categorie'])); ?>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Sales Map Visualization below existing content -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-map-marker"></span>
          <span>Sales by Warehouse Location</span>
        </strong>
      </div>
      <div class="panel-body">
        <div id="sales-map" style="height: 400px;"></div>
      </div>
    </div>
  </div>
</div>

<!-- Add Map Initialization Script -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map
    var map = L.map('sales-map').setView([20.5937, 78.9629], 5); // Default view centered on India
    
    // Add the OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Warehouse data from PHP
    var warehouseData = [
      <?php foreach($warehouse_sales as $warehouse): ?>,
      {
        name: "<?php echo $warehouse['name']; ?>",
        location: "<?php echo $warehouse['location']; ?>",
        sales: <?php echo !empty($warehouse['total_sales']) ? $warehouse['total_sales'] : 0; ?>
      },
      <?php endforeach; ?>
    ];
    
    // Function to geocode addresses and add markers
    function addWarehouseMarkers() {
      if (warehouseData.length === 0) {
        document.getElementById('sales-map').innerHTML = '<div class="alert alert-info">No warehouse data available. Please add warehouses to your database.</div>';
        return;
      }
      
      warehouseData.forEach(function(warehouse) {
        // Use OpenStreetMap Nominatim API to geocode the location
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(warehouse.location)}`)
          .then(response => response.json())
          .then(data => {
            if (data && data.length > 0) {
              var lat = parseFloat(data[0].lat);
              var lng = parseFloat(data[0].lon);
              
              // Add marker
              L.marker([lat, lng])
                .addTo(map)
                .bindPopup(`
                  <div class="map-info">
                    <h4>${warehouse.name}</h4>
                    <p>Location: ${warehouse.location}</p>
                    <p>Total Sales: <span class="sales-value">₹${warehouse.sales.toLocaleString('en-IN', {maximumFractionDigits: 2})}</span></p>
                  </div>
                `);
            }
          })
          .catch(error => console.error('Error geocoding address:', error));
      });
    }
    
    // Add the markers
    addWarehouseMarkers();
  });
</script>

<style>
  #sales-map {
    width: 100%;
    height: 400px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }
  
  .map-info {
    padding: 10px;
    min-width: 200px;
  }
  
  .map-info h4 {
    margin-top: 0;
    color: #4facfe;
    font-weight: 600;
  }
  
  .sales-value {
    font-weight: bold;
    color: #333;
  }
</style>

<?php include_once('layouts/footer.php'); ?>
