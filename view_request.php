<?php
  $page_title = 'View Purchase Request';
  require_once('includes/load.php');
  
  // Check user level - both admins and employees can view
  page_require_level(3);
  
  if(!isset($_GET['id']) || empty($_GET['id'])) {
    $session->msg("d", "Missing request ID.");
    redirect(current_user()['user_level'] <= 1 ? 'purchase_requests.php' : 'my_requests.php');
  }
  
  $request_id = (int)$_GET['id'];
  
  // Get request details with product and warehouse info
  $sql = "SELECT r.*, p.name as product_name, p.quantity as available_qty, 
          p.sale_price, w.name as warehouse_name, u.name as employee_name,
          c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
          c.address as customer_address
          FROM purchase_requests r 
          LEFT JOIN products p ON p.id = r.product_id 
          LEFT JOIN warehouses w ON w.id = p.warehouse_id 
          LEFT JOIN users u ON u.id = r.assigned_to 
          LEFT JOIN customers c ON c.id = r.customer_id
          WHERE r.id = '{$request_id}'";
  $request = find_by_sql($sql);
  
  if(empty($request)) {
    $session->msg("d", "Request not found.");
    redirect(current_user()['user_level'] <= 1 ? 'purchase_requests.php' : 'my_requests.php');
  }
  $request = $request[0];
  
  // For employees, verify they can only view their assigned requests
  $current_user = current_user();
  if($current_user['user_level'] > 1 && $request['assigned_to'] != $current_user['id']) {
    $session->msg("d", "You don't have permission to view this request.");
    redirect('my_requests.php');
  }
  
  // Process approval (admin only)
  if(isset($_GET['approve']) && $_GET['approve'] == 'true') {
    // Check if user is admin
    if($current_user['user_level'] <= 1) {
      // Check if request is in a state that can be approved
      if($request['status'] === 'completed' || $request['status'] === 'cancelled') {
        $session->msg("d", "This request has already been " . $request['status'] . ".");
      } 
      // Check if enough stock is available
      elseif($request['quantity'] <= $request['available_qty']) {
        // Check if complete_purchase_request function exists
        if(function_exists('complete_purchase_request')) {
          if(complete_purchase_request($request_id)) {
            $session->msg("s", "Purchase approved and processed successfully.");
            redirect('purchase_requests.php');
          } else {
            $session->msg("d", "Failed to process purchase.");
          }
        } else {
          $session->msg("d", "System error: Required function not found.");
        }
      } else {
        $session->msg("d", "Insufficient stock to complete this purchase.");
      }
    } else {
      $session->msg("d", "You don't have permission to approve purchases.");
    }
    redirect('view_request.php?id='.$request_id);
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
          <span class="glyphicon glyphicon-info-sign"></span>
          <span>Purchase Request Details</span>
        </strong>
        <div class="pull-right">
          <?php if($current_user['user_level'] <= 1): ?>
            <a href="purchase_requests.php" class="btn btn-primary btn-xs">Back to All Requests</a>
          <?php else: ?>
            <a href="my_requests.php" class="btn btn-primary btn-xs">Back to My Requests</a>
          <?php endif; ?>
        </div>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <h4>Customer Information</h4>
            <table class="table table-bordered">
              <tr>
                <td class="text-right" style="width: 40%;">Name:</td>
                <td><?php echo remove_junk($request['customer_name']); ?></td>
              </tr>
              <?php if(!empty($request['customer_email'])): ?>
              <tr>
                <td class="text-right">Email:</td>
                <td><?php echo remove_junk($request['customer_email']); ?></td>
              </tr>
              <?php endif; ?>
              <?php if(!empty($request['customer_phone'])): ?>
              <tr>
                <td class="text-right">Phone:</td>
                <td><?php echo remove_junk($request['customer_phone']); ?></td>
              </tr>
              <?php endif; ?>
              <?php if(!empty($request['customer_address'])): ?>
              <tr>
                <td class="text-right">Address:</td>
                <td><?php echo nl2br(remove_junk($request['customer_address'])); ?></td>
              </tr>
              <?php endif; ?>
            </table>
          </div>
          <div class="col-md-6">
            <h4>Request Information</h4>
            <table class="table table-bordered">
              <tr>
                <td class="text-right" style="width: 40%;">Request ID:</td>
                <td><?php echo $request['id']; ?></td>
              </tr>
              <tr>
                <td class="text-right">Status:</td>
                <td>
                  <?php if($request['status'] === 'new'): ?>
                    <span class="label label-info">New</span>
                  <?php elseif($request['status'] === 'assigned'): ?>
                    <span class="label label-primary">Assigned</span>
                  <?php elseif($request['status'] === 'processing'): ?>
                    <span class="label label-warning">Processing</span>
                  <?php elseif($request['status'] === 'completed'): ?>
                    <span class="label label-success">Completed</span>
                  <?php else: ?>
                    <span class="label label-danger">Cancelled</span>
                  <?php endif; ?>
                </td>
              </tr>
              <tr>
                <td class="text-right">Assigned To:</td>
                <td><?php echo !empty($request['employee_name']) ? remove_junk($request['employee_name']) : 'Not Assigned'; ?></td>
              </tr>
              <tr>
                <td class="text-right">Request Date:</td>
                <td><?php echo read_date($request['request_date']); ?></td>
              </tr>
              <?php if(!empty($request['completion_date'])): ?>
              <tr>
                <td class="text-right">Completion Date:</td>
                <td><?php echo read_date($request['completion_date']); ?></td>
              </tr>
              <?php endif; ?>
            </table>
          </div>
        </div>
        
        <h4>Product Information</h4>
        <table class="table table-bordered">
          <tr>
            <td class="text-right" style="width: 20%;">Product:</td>
            <td><?php echo remove_junk($request['product_name']); ?></td>
          </tr>
          <tr>
            <td class="text-right">Warehouse:</td>
            <td><?php echo remove_junk($request['warehouse_name']); ?></td>
          </tr>
          <tr>
            <td class="text-right">Quantity Requested:</td>
            <td><?php echo (int)$request['quantity']; ?></td>
          </tr>
          <tr>
            <td class="text-right">Available Quantity:</td>
            <td>
              <?php echo (int)$request['available_qty']; ?>
              <?php if($request['quantity'] > $request['available_qty']): ?>
                <span class="label label-danger">Insufficient Stock</span>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td class="text-right">Unit Price:</td>
            <td><?php echo "$".number_format($request['sale_price'], 2); ?></td>
          </tr>
          <tr>
            <td class="text-right">Total Price:</td>
            <td><?php echo "$".number_format($request['sale_price'] * $request['quantity'], 2); ?></td>
          </tr>
        </table>
        
        <?php if(!empty($request['notes'])): ?>
        <h4>Notes</h4>
        <div class="well">
          <?php echo nl2br(remove_junk($request['notes'])); ?> 
        </div>
        <?php endif; ?>
        
        <?php 
        // Show approval button for admins if request is not completed and has sufficient stock
        if($current_user['user_level'] <= 1 && 
           $request['status'] !== 'completed' && 
           $request['status'] !== 'cancelled' && 
           $request['quantity'] <= $request['available_qty']): 
        ?>
        <div class="text-center">
          <a href="view_request.php?id=<?php echo (int)$request_id; ?>&approve=true" 
             class="btn btn-success" 
             onclick="return confirm('Are you sure you want to approve this purchase? This will reduce inventory and create a sales record.');">
            <i class="glyphicon glyphicon-ok"></i> Approve Purchase
          </a>
        </div>
        <?php endif; ?>
      </div>
      <div class="panel-footer">
            <!-- Find the section with the action buttons at the bottom -->
            <!-- Replace or update the Approve Request button with this code -->
            <div class="form-group text-center">
              <a href="purchase_requests.php" class="btn btn-default">Back</a>
              <a href="approve_request.php?id=<?php echo $request_id; ?>" class="btn btn-success">Approve Request</a>
              <a href="reassign_request.php?id=<?php echo $request_id; ?>" class="btn btn-primary">Reassign</a>
            </div>
          </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>