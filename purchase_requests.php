<?php
  $page_title = 'All Purchase Requests';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  
  $all_requests = find_all_purchase_requests();
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
          <span class="glyphicon glyphicon-list"></span>
          <span>Purchase Requests</span>
        </strong>
        <div class="pull-right">
          <a href="request_purchase.php" class="btn btn-primary">New Request</a>
        </div>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th>Customer</th>
              <th>Product</th>
              <th>Warehouse</th>
              <th class="text-center">Quantity</th>
              <th class="text-center">Status</th>
              <th class="text-center">Assigned To</th>
              <th class="text-center">Request Date</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($all_requests as $req): ?>
            <tr>
              <td class="text-center"><?php echo count_id();?></td>
              <td><?php echo remove_junk($req['customer_name']); ?></td>
              <td><?php echo remove_junk($req['product_name']); ?></td>
              <td><?php echo remove_junk($req['warehouse_name']); ?></td>
              <td class="text-center"><?php echo (int)$req['quantity']; ?></td>
              <td class="text-center">
                <?php if($req['status'] === 'new'): ?>
                  <span class="label label-info">New</span>
                <?php elseif($req['status'] === 'assigned'): ?>
                  <span class="label label-primary">Assigned</span>
                <?php elseif($req['status'] === 'processing'): ?>
                  <span class="label label-warning">Processing</span>
                <?php elseif($req['status'] === 'completed'): ?>
                  <span class="label label-success">Completed</span>
                <?php else: ?>
                  <span class="label label-danger">Cancelled</span>
                <?php endif; ?>
              </td>
              <td class="text-center"><?php echo $req['employee_name'] ? remove_junk($req['employee_name']) : 'Not Assigned'; ?></td>
              <td class="text-center"><?php echo read_date($req['request_date']); ?></td>
              <td class="text-center">
                <div class="btn-group">
                  <a href="view_request.php?id=<?php echo (int)$req['id'];?>" class="btn btn-info btn-xs" title="View" data-toggle="tooltip">
                    <span class="glyphicon glyphicon-eye-open"></span>
                  </a>
                  <?php if($req['status'] !== 'completed'): ?>
                  <!-- Look for the section where the action buttons are defined for each request -->
                  <!-- Add or update the approve button link -->
                  <a href="approve_request.php?id=<?php echo $request['id']; ?>" class="btn btn-success btn-xs" title="Approve" data-toggle="tooltip">
                    <span class="glyphicon glyphicon-ok"></span>
                  </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>