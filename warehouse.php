<?php
  $page_title = 'All Warehouses';
  require_once('includes/load.php');
  // Check user permission
  page_require_level(1);
  $all_warehouses = find_all_warehouses_by_user($_SESSION['user_id']);
  
  // Get warehouse statistics
  $warehouse_stats = array();
  foreach ($all_warehouses as $warehouse) {
    $sql = "SELECT COUNT(id) as total_items, SUM(quantity * sale_price) as total_value ";
    $sql .= "FROM products ";
    $sql .= "WHERE warehouse_id = '{$warehouse['id']}'";
    $result = find_by_sql($sql);
    
    $warehouse_stats[] = array(
      'id' => $warehouse['id'],
      'name' => $warehouse['name'],
      'location' => $warehouse['location'],
      'description' => $warehouse['description'],
      'total_items' => $result[0]['total_items'] ? $result[0]['total_items'] : 0,
      'total_value' => $result[0]['total_value'] ? $result[0]['total_value'] : 0
    );
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
          <span class="glyphicon glyphicon-th"></span>
          <span>Warehouses</span>
        </strong>
        <div class="pull-right">
          <a href="add_warehouse.php" class="btn btn-primary">Add New Warehouse</a>
        </div>
      </div>
      <div class="panel-body">
        <?php if(empty($warehouse_stats)): ?>
          <div class="alert alert-info">
            <p>No warehouses found. Please add a warehouse to get started.</p>
          </div>
        <?php else: ?>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Warehouse Name</th>
              <th>Location</th>
              <th>Description</th>
              <th class="text-center">Total Items</th>
              <th class="text-center">Total Value</th>
              <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($warehouse_stats as $warehouse):?>
            <tr>
              <td class="text-center"><?php echo count_id();?></td>
              <td><?php echo remove_junk(ucfirst($warehouse['name'])); ?></td>
              <td><?php echo remove_junk($warehouse['location']); ?></td>
              <td><?php echo remove_junk($warehouse['description']); ?></td>
              <td class="text-center"><?php echo $warehouse['total_items']; ?></td>
              <td class="text-center"><?php echo number_format($warehouse['total_value'], 2); ?></td>
              <td class="text-center">
                <div class="btn-group">
                  <a href="view_warehouse.php?id=<?php echo (int)$warehouse['id'];?>" class="btn btn-xs btn-primary" title="View" data-toggle="tooltip">
                    <span class="glyphicon glyphicon-eye-open"></span>
                  </a>
                  <a href="edit_warehouse.php?id=<?php echo (int)$warehouse['id'];?>" class="btn btn-xs btn-warning" title="Edit" data-toggle="tooltip">
                    <span class="glyphicon glyphicon-edit"></span>
                  </a>
                  <a href="delete_warehouse.php?id=<?php echo (int)$warehouse['id'];?>" class="btn btn-xs btn-danger" title="Delete" data-toggle="tooltip">
                    <span class="glyphicon glyphicon-trash"></span>
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>