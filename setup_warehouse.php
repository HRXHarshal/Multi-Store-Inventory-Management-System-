<?php
  $page_title = 'Setup Warehouse';
  require_once('includes/load.php');
  // Check user permission
  page_require_level(1);
  
  // Check if user already has warehouses
  $existing_warehouses = find_all_warehouses_by_user($_SESSION['user_id']);
  if(!empty($existing_warehouses)) {
    $session->msg("d", "You already have warehouses set up. Go to warehouse management to add more.");
    redirect('warehouse.php');
  }
  
  if(isset($_POST['setup_warehouse'])) {
    $req_fields = array('warehouse-name', 'warehouse-location');
    validate_fields($req_fields);
    
    if(empty($errors)) {
      $name = remove_junk($db->escape($_POST['warehouse-name']));
      $location = remove_junk($db->escape($_POST['warehouse-location']));
      $description = remove_junk($db->escape($_POST['warehouse-description']));
      $user_id = $_SESSION['user_id'];
      
      $query  = "INSERT INTO warehouses (";
      $query .=" name,location,description,user_id";
      $query .=") VALUES (";
      $query .=" '{$name}', '{$location}', '{$description}', {$user_id}";
      $query .=")";
      
      if($db->query($query)) {
        // Get the new warehouse ID
        $warehouse_id = $db->insert_id();
        
        // Update existing products to associate with this warehouse
        $update_query = "UPDATE products SET warehouse_id = '{$warehouse_id}' WHERE warehouse_id = 0";
        $db->query($update_query);
        
        $session->msg('s', "Warehouse setup complete! Your existing products have been assigned to this warehouse.");
        redirect('warehouse.php', false);
      } else {
        $session->msg('d', 'Sorry, failed to set up warehouse!');
        redirect('setup_warehouse.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('setup_warehouse.php', false);
    }
  }
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Initial Warehouse Setup</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="alert alert-info">
          <p>Welcome to the warehouse management system! To get started, please set up your first warehouse. All existing products will be assigned to this warehouse.</p>
        </div>
        <div class="col-md-12">
          <form method="post" action="setup_warehouse.php" class="clearfix">
            <div class="form-group">
              <label for="warehouse-name">Warehouse Name</label>
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
                </span>
                <input type="text" class="form-control" name="warehouse-name" placeholder="Warehouse Name">
              </div>
            </div>
            <div class="form-group">
              <label for="warehouse-location">Location</label>
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-map-marker"></i>
                </span>
                <input type="text" class="form-control" name="warehouse-location" placeholder="Location">
              </div>
            </div>
            <div class="form-group">
              <label for="warehouse-description">Description</label>
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-list-alt"></i>
                </span>
                <textarea class="form-control" name="warehouse-description" placeholder="Description"></textarea>
              </div>
            </div>
            <button type="submit" name="setup_warehouse" class="btn btn-primary">Set Up Warehouse</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>