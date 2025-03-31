<?php
  $page_title = 'Sales by Warehouse';
  require_once('includes/load.php');
  // Check user permission
  page_require_level(3);
  
  // Get all warehouses for the current user
  $all_warehouses = find_all_warehouses_by_user($_SESSION['user_id']);
  
  // Default to current month if no date range is specified
  $year = date('Y');
  $month = date('m');
  $start_date = $year.'-'.$month.'-01';
  $end_date = date('Y-m-t', strtotime($start_date));
  
  if(isset($_POST['submit'])) {
    $start_date = $_POST['start-date'];
    $end_date = $_POST['end-date'];
  }
  
  // Prepare data for chart
  $warehouse_sales = array();
  $warehouse_labels = array();
  $warehouse_data = array();
  
  foreach($all_warehouses as $warehouse) {
    $sql = "SELECT SUM(s.price * s.qty) as total ";
    $sql .= "FROM sales s ";
    $sql .= "JOIN products p ON s.product_id = p.id ";
    $sql .= "WHERE p.warehouse_id = '{$warehouse['id']}' ";
    $sql .= "AND s.date BETWEEN '{$start_date}' AND '{$end_date}' ";
    
    $result = find_by_sql($sql);
    $total = $result[0]['total'] ? $result[0]['total'] : 0;
    
    $warehouse_sales[] = array(
      'id' => $warehouse['id'],
      'name' => $warehouse['name'],
      'total' => $total
    );
    
    $warehouse_labels[] = $warehouse['name'];
    $warehouse_data[] = $total;
  }
  
  // Get detailed sales data for each warehouse
  $detailed_sales = array();
  
  foreach($all_warehouses as $warehouse) {
    $sql = "SELECT s.id, s.qty, s.price, s.date, p.name ";
    $sql .= "FROM sales s ";
    $sql .= "JOIN products p ON s.product_id = p.id ";
    $sql .= "WHERE p.warehouse_id = '{$warehouse['id']}' ";
    $sql .= "AND s.date BETWEEN '{$start_date}' AND '{$end_date}' ";
    $sql .= "ORDER BY s.date DESC";
    
    $sales = find_by_sql($sql);
    
    $detailed_sales[$warehouse['id']] = array(
      'name' => $warehouse['name'],
      'sales' => $sales
    );
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
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-signal"></span>
          <span>Sales by Warehouse</span>
        </strong>
      </div>
      <div class="panel-body">
        <form class="clearfix" method="post" action="warehouse_sales.php">
          <div class="form-group">
            <div class="row">
              <div class="col-md-4">
                <label class="control-label">Start Date</label>
                <input type="date" class="form-control" name="start-date" value="<?php echo $start_date; ?>">
              </div>
              <div class="col-md-4">
                <label class="control-label">End Date</label>
                <input type="date" class="form-control" name="end-date" value="<?php echo $end_date; ?>">
              </div>
              <div class="col-md-4">
                <label class="control-label">&nbsp;</label>
                <button type="submit" name="submit" class="btn btn-primary form-control">Generate Report</button>
              </div>
            </div>
          </div>
        </form>
        
        <div class="row">
          <div class="col-md-12">
            <canvas id="warehouseSalesChart" width="800" height="400"></canvas>
          </div>
        </div>
        
        <hr>
        
        <div class="row">
          <div class="col-md-12">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Warehouse</th>
                  <th class="text-center">Total Sales</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($warehouse_sales as $sale): ?>
                <tr>
                  <td><?php echo remove_junk($sale['name']); ?></td>
                  <td class="text-center"><?php echo number_format($sale['total'], 2); ?></td>
                  <td class="text-center">
                    <button class="btn btn-xs btn-primary" onclick="toggleDetails(<?php echo $sale['id']; ?>)">
                      Show Details
                    </button>
                  </td>
                </tr>
                <tr id="details-<?php echo $sale['id']; ?>" style="display: none;">
                  <td colspan="3">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Product</th>
                          <th class="text-center">Quantity</th>
                          <th class="text-center">Total</th>
                          <th class="text-center">Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if(empty($detailed_sales[$sale['id']]['sales'])): ?>
                          <tr>
                            <td colspan="5" class="text-center">No sales found for this warehouse</td>
                          </tr>
                        <?php else: ?>
                          <?php foreach($detailed_sales[$sale['id']]['sales'] as $s): ?>
                            <tr>
                              <td><?php echo count_id(); ?></td>
                              <td><?php echo remove_junk($s['name']); ?></td>
                              <td class="text-center"><?php echo (int)$s['qty']; ?></td>
                              <td class="text-center"><?php echo number_format($s['price'] * $s['qty'], 2); ?></td>
                              <td class="text-center"><?php echo date('F j, Y', strtotime($s['date'])); ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Function to toggle details
  function toggleDetails(id) {
    var detailsRow = document.getElementById('details-' + id);
    if (detailsRow.style.display === 'none') {
      detailsRow.style.display = 'table-row';
    } else {
      detailsRow.style.display = 'none';
    }
  }
  
  // Chart data
  var ctx = document.getElementById('warehouseSalesChart').getContext('2d');
  var warehouseSalesChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($warehouse_labels); ?>,
      datasets: [{
        label: 'Sales by Warehouse',
        data: <?php echo json_encode($warehouse_data); ?>,
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
          'rgba(255, 99, 132, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
<?php include_once('layouts/footer.php'); ?>