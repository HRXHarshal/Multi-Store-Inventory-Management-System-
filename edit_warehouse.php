<?php
  $page_title = 'Edit Warehouse';
  require_once('includes/load.php');
  // Check user permission
  page_require_level(1);
  
  $warehouse = find_by_warehouse_id((int)$_GET['id']);
  if(!$warehouse){
    $session->msg("d","Missing warehouse id.");
    redirect('warehouse.php');
  }

  if(isset($_POST['update'])){
    $req_fields = array('warehouse-name','warehouse-location');
    validate_fields($req_fields);
    if(empty($errors)){
      $name = remove_junk($db->escape($_POST['warehouse-name']));
      $location = remove_junk($db->escape($_POST['warehouse-location']));
      $description = remove_junk($db->escape($_POST['warehouse-description']));
      
      $query  = "UPDATE warehouses SET ";
      $query .= "name='{$name}',location='{$location}',description='{$description}' ";
      $query .= "WHERE id='{$warehouse['id']}'";
      
      $result = $db->query($query);
      if($result && $db->affected_rows() === 1){
        $session->msg('s',"Warehouse updated ");
        redirect('warehouse.php', false);
      } else {
        $session->msg('d',' Sorry failed to update warehouse!');
        redirect('edit_warehouse.php?id='.$warehouse['id'], false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('edit_warehouse.php?id='.$warehouse['id'], false);
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
          <span>Edit Warehouse</span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-12">
          <form method="post" action="edit_warehouse.php?id=<?php echo (int)$warehouse['id']; ?>" class="clearfix">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
                </span>
                <input type="text" class="form-control" name="warehouse-name" value="<?php echo remove_junk($warehouse['name']); ?>">
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-map-marker"></i>
                </span>
                <input type="text" class="form-control" name="warehouse-location" value="<?php echo remove_junk($warehouse['location']); ?>">
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-list-alt"></i>
                </span>
                <textarea class="form-control" name="warehouse-description"><?php echo remove_junk($warehouse['description']); ?></textarea>
              </div>
            </div>
            <button type="submit" name="update" class="btn btn-primary">Update Warehouse</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>