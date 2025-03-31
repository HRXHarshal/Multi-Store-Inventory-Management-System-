<?php
  require_once('includes/load.php');
  // Check user permission
  page_require_level(1);
  
  $warehouse = find_by_warehouse_id((int)$_GET['id']);
  if(!$warehouse){
    $session->msg("d","Missing warehouse id.");
    redirect('warehouse.php');
  }

  // Check if warehouse has products
  $products = find_products_by_warehouse($warehouse['id']);
  if(!empty($products)) {
    $session->msg("d","Cannot delete warehouse. Remove all products from this warehouse first.");
    redirect('warehouse.php');
  }

  $delete_id = delete_by_id('warehouses',(int)$warehouse['id']);
  if($delete_id){
    $session->msg("s","Warehouse deleted.");
    redirect('warehouse.php');
  } else {
    $session->msg("d","Warehouse deletion failed.");
    redirect('warehouse.php');
  }
?>