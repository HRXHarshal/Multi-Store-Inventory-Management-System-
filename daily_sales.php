<?php
  $page_title = 'Daily Sales';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
  
  // Get year and month from URL or use current date
  $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
  $month = isset($_GET['month']) ? $_GET['month'] : date('m');
  
  // Get daily sales data
  $sales = dailySales($year, $month);
  
  // Get month name
  $month_name = date('F', strtotime($year . '-' . $month . '-01'));
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
          <span>Daily Sales for <?php echo $month_name . ' ' . $year; ?></span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Date</th>
              <th class="text-center" style="width: 15%;">Quantity</th>
              <th class="text-center" style="width: 15%;">Total Sales</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $total_qty = 0;
            $total_sales = 0;
            
            if(!empty($sales)):
              foreach($sales as $sale): 
                $date_formatted = date('d F, Y', strtotime($sale['date']));
                $total_qty += $sale['total_qty'];
                $total_sales += $sale['total_sales'];
            ?>
            <tr>
              <td class="text-center"><?php echo count_id(); ?></td>
              <td><?php echo $date_formatted; ?></td>
              <td class="text-center"><?php echo (int)$sale['total_qty']; ?></td>
              <td class="text-center">₹<?php echo number_format($sale['total_sales'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
              <td colspan="4" class="text-center">No sales found for <?php echo $month_name . ' ' . $year; ?></td>
            </tr>
            <?php endif; ?>
          </tbody>
          <tfoot>
            <tr class="text-right">
              <td colspan="2"></td>
              <td class="text-center"><?php echo $total_qty; ?></td>
              <td class="text-center">₹<?php echo number_format($total_sales, 2); ?></td>
            </tr>
          </tfoot>
        </table>
        <div class="text-center">
          <a href="monthly_sales.php?year=<?php echo $year; ?>" class="btn btn-primary">Back to Monthly Sales</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
