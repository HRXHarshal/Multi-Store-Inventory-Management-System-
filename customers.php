<?php
  $page_title = 'All Customers';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  
  $all_customers = find_all_customers();
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
          <span class="glyphicon glyphicon-user"></span>
          <span>Customers</span>
        </strong>
        <div class="pull-right">
          <a href="add_customer.php" class="btn btn-primary">Add New Customer</a>
        </div>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th class="text-center">Date Added</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($all_customers as $customer): ?>
            <tr>
              <td class="text-center"><?php echo count_id();?></td>
              <td><?php echo remove_junk($customer['name']); ?></td>
              <td><?php echo remove_junk($customer['email'] ? $customer['email'] : 'N/A'); ?></td>
              <td><?php echo remove_junk($customer['phone'] ? $customer['phone'] : 'N/A'); ?></td>
              <td class="text-center"><?php echo read_date($customer['date_added']); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>