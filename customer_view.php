<?php
  require_once('includes/load.php');
  $page_title = 'Customer View - Inventory';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $page_title; ?></title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="libs/css/main.css" />
  <style>
    body {
      background-color: #1e1e1e;
      color: #fff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .inventory-header {
      background: linear-gradient(to right, #3498db, #2980b9);
      color: white;
      padding: 20px;
      border-radius: 5px;
      margin-bottom: 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .inventory-header h3 {
      margin-top: 0;
      font-weight: 600;
    }
    
    .filter-section {
      background-color: #2c2c2c;
      padding: 20px;
      border-radius: 5px;
      margin-bottom: 30px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .product-card {
      background-color: #2c2c2c;
      border-radius: 5px;
      margin-bottom: 25px;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }
    
    .product-image {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-bottom: 1px solid #3c3c3c;
    }
    
    .product-details {
      padding: 15px;
    }
    
    .product-title {
      font-weight: bold;
      margin-bottom: 8px;
      font-size: 16px;
    }
    
    .warehouse-location {
      color: #aaa;
      font-size: 12px;
      margin-bottom: 12px;
    }
    
    .product-price {
      margin-bottom: 12px;
      font-size: 15px;
      font-weight: 500;
    }
    
    .stock-status {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 15px;
      font-size: 12px;
      font-weight: 500;
    }
    
    .in-stock {
      background-color: rgba(46, 204, 113, 0.2);
      color: #2ecc71;
    }
    
    .low-stock {
      background-color: rgba(241, 196, 15, 0.2);
      color: #f1c40f;
    }
    
    .form-control {
      background-color: #3c3c3c;
      border: 1px solid #555;
      color: #fff;
      height: 40px;
      box-shadow: none;
    }
    
    .form-control:focus {
      background-color: #3c3c3c;
      border-color: #3498db;
      color: #fff;
      box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    }
    
    h4 {
      margin-top: 0;
      margin-bottom: 10px;
      color: #ddd;
    }
    
    /* Add a footer */
    .footer {
      text-align: center;
      padding: 20px 0;
      margin-top: 30px;
      color: #aaa;
      font-size: 12px;
      border-top: 1px solid #3c3c3c;
    }
</style>
</head>
<body>
  <div class="container">
    <div class="inventory-header">
      <div class="row">
        <div class="col-md-12">
          <h3><i class="glyphicon glyphicon-shopping-cart"></i> Inventory Browser</h3>
          <p>Browse our available products across all warehouse locations</p>
        </div>
        <!-- Removed the login button section -->
      </div>
    </div>
    
    <div class="row filter-section">
      <div class="col-md-6">
        <h4>Filter by Warehouse</h4>
        <select id="warehouse-select" class="form-control">
          <option value="all">All Warehouses</option>
          <?php
            $warehouses = find_all('warehouses');
            foreach ($warehouses as $warehouse):
          ?>
            <option value="<?php echo $warehouse['id']; ?>"><?php echo $warehouse['name']; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <h4>Search Products</h4>
        <input type="text" id="product-search" class="form-control" placeholder="Search by product name...">
      </div>
    </div>
    
    <div class="row" id="product-container">
      <?php
        // Get all products with their warehouse information
        $sql = "SELECT p.id, p.name, p.quantity, p.sale_price, p.media_id, p.warehouse_id, w.name as warehouse_name ";
        $sql .= "FROM products p ";
        $sql .= "LEFT JOIN warehouses w ON p.warehouse_id = w.id ";
        $sql .= "WHERE p.quantity > 0 ";
        $sql .= "ORDER BY p.name ASC";
        $products = find_by_sql($sql);
        
        foreach ($products as $product):
          // Get product image
          $media_id = $product['media_id'];
          $img_file = find_by_id('media', $media_id);
          
          // Check if image exists, if not, don't display image tag
          $has_image = ($img_file && file_exists('uploads/products/'.$img_file['file_name']));
          
          // Determine stock status - moved this here to fix the undefined variable error
          $stock_class = ($product['quantity'] > 5) ? 'in-stock' : 'low-stock';
          $stock_text = ($product['quantity'] > 5) ? 'In Stock' : 'Low Stock';
      ?>
      <div class="col-md-3 product-item" data-warehouse="<?php echo $product['warehouse_id']; ?>">
        <div class="product-card">
          <?php if($has_image): ?>
            <img src="uploads/products/<?php echo $img_file['file_name']; ?>" class="product-image" alt="<?php echo $product['name']; ?>">
          <?php else: ?>
            <div class="product-image" style="background-color: #3c3c3c; display: flex; align-items: center; justify-content: center;">
              <span class="glyphicon glyphicon-picture" style="font-size: 40px; color: #555;"></span>
            </div>
          <?php endif; ?>
          <div class="product-details">
            <h4 class="product-title"><?php echo $product['name']; ?></h4>
            <p class="warehouse-location">
              <i class="glyphicon glyphicon-map-marker"></i> 
              <?php echo $product['warehouse_name'] ? $product['warehouse_name'] : 'Main Warehouse'; ?>
            </p>
            <p class="product-price">Price: $<?php echo number_format($product['sale_price'], 2); ?></p>
            <div class="stock-status <?php echo $stock_class; ?>">
              <?php echo $stock_text; ?> (<?php echo $product['quantity']; ?> available)
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    
    <!-- Added footer -->
    <div class="footer">
      <p>© <?php echo date('Y'); ?> Inventory Management System. All products shown are subject to availability.</p>
    </div>
  </div>
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      // Warehouse filter functionality
      $('#warehouse-select').on('change', function() {
        var selectedWarehouse = $(this).val();
        
        if (selectedWarehouse === 'all') {
          $('.product-item').show();
        } else {
          $('.product-item').hide();
          $('.product-item[data-warehouse="' + selectedWarehouse + '"]').show();
        }
      });
      
      // Product search functionality
      $('#product-search').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        
        $('.product-item').each(function() {
          var productName = $(this).find('.product-title').text().toLowerCase();
          
          if (productName.indexOf(searchTerm) > -1) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      });
    });
  </script>
</body>
</html>