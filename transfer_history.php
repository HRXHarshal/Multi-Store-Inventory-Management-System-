<?php
  $page_title = 'Transfer History';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
?>
<?php
  // Get all transfers
  $sql = "SELECT t.*, p.name AS product_name, 
          w1.name AS from_warehouse_name, 
          w2.name AS to_warehouse_name,
          u.name AS transferred_by
          FROM product_transfers t 
          JOIN products p ON t.product_id = p.id 
          JOIN warehouses w1 ON t.source_warehouse_id = w1.id 
          JOIN warehouses w2 ON t.destination_warehouse_id = w2.id 
          JOIN users u ON t.user_id = u.id
          ORDER BY t.transfer_date DESC";
  
  $transfers = find_by_sql($sql);
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
          <span class="glyphicon glyphicon-transfer"></span>
          <span>Transfer History</span>
        </strong>
      </div>
      <div class="panel-body">
        <?php if(!empty($transfers)): ?>
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Product</th>
              <th class="text-center">Quantity</th>
              <th class="text-center">From Warehouse</th>
              <th class="text-center">To Warehouse</th>
              <th class="text-center">Transferred By</th>
              <th class="text-center">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($transfers as $transfer): ?>
            <tr>
              <td class="text-center"><?php echo count_id();?></td>
              <td><?php echo remove_junk($transfer['product_name']); ?></td>
              <td class="text-center"><?php echo remove_junk($transfer['quantity']); ?></td>
              <td class="text-center"><?php echo remove_junk($transfer['from_warehouse_name']); ?></td>
              <td class="text-center"><?php echo remove_junk($transfer['to_warehouse_name']); ?></td>
              <td class="text-center"><?php echo remove_junk($transfer['transferred_by']); ?></td>
              <td class="text-center"><?php echo remove_junk($transfer['transfer_date']); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div class="alert alert-info">No transfer history found.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>