<?php
  $page_title = 'Sales by Location';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
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
          <span class="glyphicon glyphicon-map-marker"></span>
          <span>Sales by Warehouse Location</span>
        </strong>
      </div>
      <div class="panel-body">
        <div id="sales-map" style="height: 500px;"></div>
      </div>
    </div>
  </div>
</div>

<!-- Include Leaflet.js library -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map
    var map = L.map('sales-map').setView([20.5937, 78.9629], 5); // Default view centered on India
    
    // Add the OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Sample data - replace with your actual warehouse data
    var warehouseData = [
      {
        name: "Mumbai Warehouse",
        location: [19.0760, 72.8777],
        sales: 250000
      },
      {
        name: "Delhi Warehouse",
        location: [28.6139, 77.2090],
        sales: 180000
      },
      {
        name: "Bangalore Warehouse",
        location: [12.9716, 77.5946],
        sales: 210000
      },
      {
        name: "Chennai Warehouse",
        location: [13.0827, 80.2707],
        sales: 160000
      }
    ];
    
    // Add markers for each warehouse
    warehouseData.forEach(function(warehouse) {
      L.marker(warehouse.location)
        .addTo(map)
        .bindPopup(`
          <div class="map-info">
            <h4>${warehouse.name}</h4>
            <p>Total Sales: <span class="sales-value">₹${warehouse.sales.toLocaleString('en-IN')}</span></p>
          </div>
        `);
    });
  });
</script>

<style>
  #sales-map {
    width: 100%;
    height: 500px;
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