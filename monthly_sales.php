<?php
  $page_title = 'Monthly Sales';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
  
  // Get current year or use the one from the form
  $year = isset($_POST['year']) ? $_POST['year'] : date('Y');
  
  // Get monthly sales data
  $sales = monthlySales($year);
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
          <span>Monthly Sales for <?php echo $year; ?></span>
        </strong>
      </div>
      <div class="panel-body">
        <form class="clearfix" method="post" action="monthly_sales.php">
          <div class="form-group">
            <div class="input-group">
              <input type="text" class="form-control" name="year" placeholder="Year" value="<?php echo $year; ?>">
              <span class="input-group-btn">
                <button type="submit" class="btn btn-primary">Show Sales</button>
              </span>
            </div>
          </div>
        </form>
        
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Month</th>
              <th class="text-center" style="width: 15%;">Quantity</th>
              <th class="text-center" style="width: 15%;">Total Sales</th>
              <th class="text-center" style="width: 15%;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $total_qty = 0;
            $total_sales = 0;
            
            if(!empty($sales)):
              foreach($sales as $sale): 
                $month_name = date('F', strtotime($sale['month'] . '-01'));
                $month_num = date('m', strtotime($sale['month'] . '-01'));
                $total_qty += $sale['total_qty'];
                $total_sales += $sale['total_sales'];
            ?>
            <tr>
              <td class="text-center"><?php echo count_id(); ?></td>
              <td><?php echo $month_name; ?></td>
              <td class="text-center"><?php echo (int)$sale['total_qty']; ?></td>
              <td class="text-center">₹<?php echo number_format($sale['total_sales'], 2); ?></td>
              <td class="text-center">
                <a href="daily_sales.php?year=<?php echo $year; ?>&month=<?php echo $month_num; ?>" class="btn btn-xs btn-primary">
                  View Daily Sales
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
              <td colspan="5" class="text-center">No sales found for <?php echo $year; ?></td>
            </tr>
            <?php endif; ?>
          </tbody>
          <tfoot>
            <tr class="text-right">
              <td colspan="2"></td>
              <td class="text-center"><?php echo $total_qty; ?></td>
              <td class="text-center">₹<?php echo number_format($total_sales, 2); ?></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
