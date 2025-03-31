<?php
  $page_title = 'Sales Report';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
  $all_warehouses = find_all_warehouses_by_user($_SESSION['user_id']);
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Sales Report</span>
        </strong>
      </div>
      <div class="panel-body">
        <form class="clearfix" method="post" action="sales_report.php">
          <div class="form-group">
            <div class="input-group">
              <input type="text" class="datepicker form-control" name="start-date" placeholder="From">
              <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
              <input type="text" class="datepicker form-control" name="end-date" placeholder="To">
              <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
              <span class="input-group-btn">
                <button type="submit" class="btn btn-primary" name="submit_report">Generate Report</button>
              </span>
            </div>
          </div>
        </form>
        
        <?php
        // Initialize variables to avoid undefined variable errors
        $start_date = '';
        $end_date = '';
        $sales = array();
        
        if(isset($_POST['submit_report'])):
          // Set the start and end dates from the form
          $start_date = $_POST['start-date'];
          $end_date = $_POST['end-date'];
          
          // Validate dates
          if(empty($start_date) || empty($end_date)) {
            $session->msg("d", "Please select both start and end dates.");
            redirect('sales_report.php', false);
          }
          
          // Query sales for the date range
          $sql = "SELECT s.id, s.qty, s.price, s.date, p.name, p.sale_price, p.buy_price, ";
          $sql .= "c.name AS categorie, w.name AS warehouse ";
          $sql .= "FROM sales s ";
          $sql .= "LEFT JOIN products p ON s.product_id = p.id ";
          $sql .= "LEFT JOIN categories c ON p.categorie_id = c.id ";
          $sql .= "LEFT JOIN warehouses w ON p.warehouse_id = w.id ";
          $sql .= "WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}' ";
          $sql .= "ORDER BY s.date DESC";
          
          $sales = find_by_sql($sql);
        endif;
        ?>
        
        <?php if(isset($_POST['submit_report'])): ?>
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Sales from <?php echo $start_date; ?> to <?php echo $end_date; ?></span>
          </strong>
        </div>
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Product Name</th>
              <th>Category</th>
              <th>Warehouse</th>
              <th class="text-center" style="width: 15%;">Quantity</th>
              <th class="text-center" style="width: 15%;">Total</th>
              <th class="text-center" style="width: 15%;">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $total_qty = 0;
            $total_sale = 0;
            
            foreach($sales as $sale): 
              $total_qty += $sale['qty'];
              $total_sale += $sale['price'];
            ?>
            <tr>
              <td class="text-center"><?php echo count_id(); ?></td>
              <td><?php echo remove_junk($sale['name']); ?></td>
              <td><?php echo remove_junk($sale['categorie']); ?></td>
              <td><?php echo remove_junk($sale['warehouse']); ?></td>
              <td class="text-center"><?php echo (int)$sale['qty']; ?></td>
              <td class="text-center">₹<?php echo remove_junk($sale['price']); ?></td>
              <td class="text-center"><?php echo $sale['date']; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr class="text-right">
              <td colspan="4"></td>
              <td class="text-center"><?php echo $total_qty; ?></td>
              <td class="text-center">₹<?php echo number_format($total_sale, 2); ?></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
        <?php endif; ?>
        
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
