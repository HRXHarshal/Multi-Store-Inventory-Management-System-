<?php
  $page_title = 'Add Warehouse';
  require_once('includes/load.php');
  // Check user permission
  page_require_level(1);

  if(isset($_POST['add_warehouse'])){
    $req_fields = array('warehouse-name','warehouse-location');
    validate_fields($req_fields);
    if(empty($errors)){
      $name = remove_junk($db->escape($_POST['warehouse-name']));
      $location = remove_junk($db->escape($_POST['warehouse-location']));
      $description = remove_junk($db->escape($_POST['warehouse-description']));
      $user_id = $_SESSION['user_id'];
      
      $query  = "INSERT INTO warehouses (";
      $query .=" name,location,description,user_id";
      $query .=") VALUES (";
      $query .=" '{$name}', '{$location}', '{$description}', {$user_id}";
      $query .=")";
      
      if($db->query($query)){
        $session->msg('s',"Warehouse added ");
        redirect('warehouse.php', false);
      } else {
        $session->msg('d',' Sorry failed to add warehouse!');
        redirect('warehouse.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('warehouse.php',false);
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
          <span>Add New Warehouse</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="add_warehouse.php" class="clearfix">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
                </span>
                <input type="text" class="form-control" name="warehouse-name" placeholder="Warehouse Name">
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-map-marker"></i>
                </span>
                <input type="text" class="form-control" name="warehouse-location" placeholder="Location">
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-list-alt"></i>
                </span>
                <textarea class="form-control" name="warehouse-description" placeholder="Description"></textarea>
              </div>
            </div>
            <button type="submit" name="add_warehouse" class="btn btn-primary">Add Warehouse</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>