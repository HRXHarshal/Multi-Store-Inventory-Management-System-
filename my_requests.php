<?php
  $page_title = 'My Assigned Requests';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
  
  $current_user = current_user();
  $my_requests = find_purchase_requests_by_employee($current_user['id']);
  
  // Process status update
  if(isset($_GET['id']) && isset($_GET['status'])) {
    $request_id = (int)$_GET['id'];
    $status = $_GET['status'];
    
    if($status === 'processing') {
      $sql = "UPDATE purchase_requests SET status = 'processing' WHERE id = '{$request_id}' AND assigned_to = '{$current_user['id']}'";
      if($db->query($sql)) {
        $session->msg('s', "Request status updated to Processing");
      } else {
        $session->msg('d', "Failed to update request status");
      }
    } elseif($status === 'complete') {
      if(complete_purchase_request($request_id)) {
        $session->msg('s', "Request marked as completed");
      } else {
        $session->msg('d', "Failed to complete request");
      }
    }
    redirect('my_requests.php', false);
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
          <span class="glyphicon glyphicon-list"></span>
          <span>My Assigned Requests</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th>Customer</th>
              <th>Contact</th>
              <th>Product</th>
              <th class="text-center">Quantity</th>
              <th class="text-center">Status</th>
              <th class="text-center">Request Date</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($my_requests as $req): ?>
            <tr>
              <td class="text-center"><?php echo count_id();?></td>
              <td><?php echo remove_junk($req['customer_name']); ?></td>
              <td>
                <?php if($req['customer_phone']): ?>
                  <i class="glyphicon glyphicon-phone"></i> <?php echo remove_junk($req['customer_phone']); ?><br>
                <?php endif; ?>
                <?php if($req['customer_email']): ?>
                  <i class="glyphicon glyphicon-envelope"></i> <?php echo remove_junk($req['customer_email']); ?>
                <?php endif; ?>
              </td>
              <td><?php echo remove_junk($req['product_name']); ?></td>
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
              <td class="text-center"><?php echo read_date($req['request_date']); ?></td>
              <td class="text-center">
                <div class="btn-group">
                  <a href="view_request.php?id=<?php echo (int)$req['id'];?>" class="btn btn-info btn-xs" title="View" data-toggle="tooltip">
                    <span class="glyphicon glyphicon-eye-open"></span>
                  </a>
                  
                  <?php if($req['status'] === 'assigned'): ?>
                    <a href="my_requests.php?id=<?php echo (int)$req['id'];?>&status=processing" class="btn btn-warning btn-xs" title="Start Processing" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-play"></span>
                    </a>
                  <?php elseif($req['status'] === 'processing'): ?>
                    <a href="my_requests.php?id=<?php echo (int)$req['id'];?>&status=complete" class="btn btn-success btn-xs" title="Mark Complete" data-toggle="tooltip">
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