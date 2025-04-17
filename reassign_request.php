<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  
  $request_id = (int)$_GET['id'];
  
  // Get request details with customer and product information
  $sql = "SELECT r.*, p.name as product_name, c.name as customer_name, 
          u.name as employee_name 
          FROM purchase_requests r 
          LEFT JOIN products p ON p.id = r.product_id 
          LEFT JOIN customers c ON c.id = r.customer_id 
          LEFT JOIN users u ON u.id = r.assigned_to 
          WHERE r.id = '{$request_id}' LIMIT 1";
  $request = find_by_sql($sql);
  
  if(empty($request)) {
    $_SESSION['error'] = "Request not found.";
    redirect('purchase_requests.php', false);
  }
  
  $request = $request[0];
  
  // Get all active employees
  // Get all active employees with their current workload
  $sql = "SELECT u.id, u.name, 
          IFNULL(w.active_assignments, 0) as active_assignments 
          FROM users u 
          LEFT JOIN employee_workload w ON u.id = w.user_id 
          WHERE u.user_level = 3 AND u.status = 1 
          ORDER BY active_assignments ASC";
  $employees = find_by_sql($sql);
  
  if(isset($_POST['reassign'])) {
    $employee_id = (int)$_POST['employee_id'];
    
    // Update request assignment
    $sql = "UPDATE purchase_requests SET assigned_to = '{$employee_id}' WHERE id = '{$request_id}'";
    $result = $db->query($sql);
    
    if($result) {
      // Update workload - decrease for old employee
      if(!empty($request['assigned_to'])) {
        // First check current active assignments to avoid underflow
        $sql = "SELECT active_assignments FROM employee_workload WHERE user_id = '{$request['assigned_to']}'";
        $current_workload = find_by_sql($sql);
        
        if(!empty($current_workload) && $current_workload[0]['active_assignments'] > 0) {
          $sql = "UPDATE employee_workload SET active_assignments = active_assignments - 1 
                  WHERE user_id = '{$request['assigned_to']}' AND active_assignments > 0";
          $db->query($sql);
        }
      }
      
      // Update workload - increase for new employee
      $sql = "SELECT user_id FROM employee_workload WHERE user_id = '{$employee_id}'";
      $result = find_by_sql($sql);
      
      if(empty($result)) {
        // Create new workload record
        $sql = "INSERT INTO employee_workload (user_id, active_assignments, total_assignments) 
                VALUES ('{$employee_id}', 1, 1)";
      } else {
        // Update existing workload
        $sql = "UPDATE employee_workload SET active_assignments = active_assignments + 1, 
                total_assignments = total_assignments + 1 WHERE user_id = '{$employee_id}'";
      }
      $db->query($sql);
      
      $_SESSION['success'] = "Request reassigned successfully.";
      redirect('purchase_requests.php', false);
    } else {
      $_SESSION['error'] = "Failed to reassign request.";
    }
  }
  
  include_once('layouts/header.php');
?>

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
          <span class="glyphicon glyphicon-transfer"></span>
          <span>REASSIGN PURCHASE REQUEST</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered">
          <tbody>
            <tr>
              <td class="text-right" style="width: 30%;">Request ID:</td>
              <td><?php echo $request['id']; ?></td>
            </tr>
            <tr>
              <td class="text-right">Customer:</td>
              <td>
                <?php 
                  if(isset($request['customer_name']) && !empty($request['customer_name'])) {
                    echo $request['customer_name']; 
                  } else {
                    echo "Unknown Customer";
                  }
                ?>
              </td>
            </tr>
            <tr>
              <td class="text-right">Product:</td>
              <td>
                <?php 
                  if(isset($request['product_name']) && !empty($request['product_name'])) {
                    echo $request['product_name']; 
                  } else {
                    echo "Unknown Product";
                  }
                ?>
              </td>
            </tr>
            <tr>
              <td class="text-right">Quantity:</td>
              <td><?php echo $request['quantity']; ?></td>
            </tr>
            <tr>
              <td class="text-right">Current Status:</td>
              <td><span class="label label-info"><?php echo ucfirst($request['status']); ?></span></td>
            </tr>
            <tr>
              <td class="text-right">Currently Assigned To:</td>
              <td><?php echo $request['employee_name'] ? $request['employee_name'] : 'Not Assigned'; ?></td>
            </tr>
          </tbody>
        </table>
        
        <form method="post" action="reassign_request.php?id=<?php echo $request_id; ?>">
          <div class="form-group">
            <label for="employee_id">Reassign To</label>
            <select class="form-control" name="employee_id" required>
              <option value="">Select Employee</option>
              <?php foreach($employees as $employee): ?>
                <option value="<?php echo $employee['id']; ?>">
                  <?php echo $employee['name']; ?> 
                  (Current Requests: <?php echo $employee['active_assignments']; ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <button type="submit" name="reassign" class="btn btn-primary">Reassign</button>
            <a href="purchase_requests.php" class="btn btn-default">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>