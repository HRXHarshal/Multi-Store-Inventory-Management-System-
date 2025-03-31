<?php
$page_title = 'Sales Report';
$results = '';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(3);
?>
<?php
  $page_title = 'Sale Report Process';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
  
  if(isset($_POST['submit'])){
    $req_dates = array('start-date','end-date');
    validate_fields($req_dates);

    if(empty($errors)):
      $start_date   = remove_junk($db->escape($_POST['start-date']));
      $end_date     = remove_junk($db->escape($_POST['end-date']));
      $warehouse_id = isset($_POST['warehouse']) ? (int)$_POST['warehouse'] : 0;

      if($warehouse_id > 0) {
        // Modify the sales query to filter by warehouse
        $sql = "SELECT s.id, s.qty, s.price, s.date, p.name, p.warehouse_id, w.name as warehouse ";
        $sql .= "FROM sales s ";
        $sql .= "LEFT JOIN products p ON s.product_id = p.id ";
        $sql .= "LEFT JOIN warehouses w ON p.warehouse_id = w.id ";
        $sql .= "WHERE p.warehouse_id = '{$warehouse_id}' ";
        $sql .= "AND s.date BETWEEN '{$start_date}' AND '{$end_date}' ";
        $sql .= "ORDER BY s.date DESC";
      } else {
        // Original query without warehouse filter
        $sql = "SELECT s.id, s.qty, s.price, s.date, p.name, w.name as warehouse ";
        $sql .= "FROM sales s ";
        $sql .= "LEFT JOIN products p ON s.product_id = p.id ";
        $sql .= "LEFT JOIN warehouses w ON p.warehouse_id = w.id ";
        $sql .= "WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}' ";
        $sql .= "ORDER BY s.date DESC";
      }
      
      $sales = find_by_sql($sql);
      
    else:
      $session->msg("d", $errors);
      redirect('sales_report.php', false);
    endif;
  } else {
    $session->msg("d", "Select dates");
    redirect('sales_report.php', false);
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
          <span class="glyphicon glyphicon-th"></span>
          <span>Sales Report</span>
        </strong>
        <div class="pull-right">
          <a href="sales_report.php" class="btn btn-primary">Back</a>
        </div>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Product Name</th>
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
              $total_sale += $sale['price'] * $sale['qty'];
            ?>
            <tr>
              <td class="text-center"><?php echo count_id(); ?></td>
              <td><?php echo remove_junk($sale['name']); ?></td>
              <td><?php echo remove_junk($sale['warehouse']); ?></td>
              <td class="text-center"><?php echo (int)$sale['qty']; ?></td>
              <td class="text-center"><?php echo remove_junk($sale['price'] * $sale['qty']); ?></td>
              <td class="text-center"><?php echo $sale['date']; ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
              <td class="text-center" colspan="3"><strong>Total</strong></td>
              <td class="text-center"><strong><?php echo $total_qty; ?></strong></td>
              <td class="text-center"><strong><?php echo number_format($total_sale, 2); ?></strong></td>
              <td class="text-center"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
