<?php
  $page_title = 'Add Product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  $all_categories = find_all('categories');
  $all_warehouses = find_all('warehouses');
  $all_photo = find_all('media'); // Add this line to get all media
?>
<?php
 if(isset($_POST['add_product'])){
   $req_fields = array('product-title','product-categorie','product-quantity','buying-price', 'saleing-price', 'warehouse' );
   validate_fields($req_fields);
   if(empty($errors)){
     $p_name  = remove_junk($db->escape($_POST['product-title']));
     $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
     $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
     $p_buy   = remove_junk($db->escape($_POST['buying-price']));
     $p_sale  = remove_junk($db->escape($_POST['saleing-price']));
     $p_warehouse = remove_junk($db->escape($_POST['warehouse']));
     if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
       $media_id = '0';
     } else {
       $media_id = remove_junk($db->escape($_POST['product-photo']));
     }
     $date    = make_date();
     $query  = "INSERT INTO products (";
     $query .=" name,quantity,buy_price,sale_price,categorie_id,warehouse_id,media_id,date";
     $query .=") VALUES (";
     $query .=" '{$p_name}', '{$p_qty}', '{$p_buy}', '{$p_sale}', '{$p_cat}', '{$p_warehouse}', '{$media_id}', '{$date}'";
     $query .=")";
     $query .=" ON DUPLICATE KEY UPDATE name='{$p_name}'";
     
     if($db->query($query)){
       $product_id = $db->insert_id();
       
       // Handle image upload
       if(isset($_FILES['product-image']) && !empty($_FILES['product-image']['name'])) {
         $upload_dir = 'uploads/products/';
         if(!is_dir($upload_dir)) {
           mkdir($upload_dir, 0777, true);
         }
         
         $file_temp = $_FILES['product-image']['tmp_name'];
         $file_name = $_FILES['product-image']['name'];
         $file_type = $_FILES['product-image']['type'];
         
         // Generate unique filename
         $new_file_name = time() . '_' . $file_name;
         
         if(move_uploaded_file($file_temp, $upload_dir . $new_file_name)) {
           // Insert into media table
           $sql = "INSERT INTO media (file_name, file_type) VALUES ('{$new_file_name}', '{$file_type}')";
           $db->query($sql);
           $media_id = $db->insert_id();
           
           // Update product with media_id
           $sql = "UPDATE products SET media_id = '{$media_id}' WHERE id = '{$product_id}'";
           $db->query($sql);
         }
       }
       
       $session->msg('s',"Product added successfully");
       redirect('add_product.php', false);
     } else {
       $session->msg('d',' Sorry failed to add product!');
       redirect('add_product.php', false);
     }

   } else{
     $session->msg("d", $errors);
     redirect('add_product.php',false);
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
            <span>Add New Product</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_product.php" enctype="multipart/form-data">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="product-title" placeholder="Product Title">
               </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <select class="form-control" name="product-categorie">
                      <option value="">Select Product Category</option>
                    <?php  foreach ($all_categories as $cat): ?>
                      <option value="<?php echo (int)$cat['id'] ?>">
                        <?php echo $cat['name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <select class="form-control" name="warehouse">
                      <option value="">Select Warehouse</option>
                    <?php  foreach ($all_warehouses as $w): ?>
                      <option value="<?php echo (int)$w['id'] ?>">
                        <?php echo $w['name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
               <div class="row">
                 <div class="col-md-4">
                   <div class="input-group">
                     <span class="input-group-addon">
                      <i class="glyphicon glyphicon-shopping-cart"></i>
                     </span>
                     <input type="number" class="form-control" name="product-quantity" placeholder="Product Quantity">
                  </div>
                 </div>
                 <div class="col-md-4">
                   <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="number" class="form-control" name="buying-price" placeholder="Buying Price">
                     <span class="input-group-addon">.00</span>
                  </div>
                 </div>
                  <div class="col-md-4">
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-usd"></i>
                      </span>
                      <input type="number" class="form-control" name="saleing-price" placeholder="Selling Price">
                      <span class="input-group-addon">.00</span>
                   </div>
                  </div>
               </div>
              </div>
              
              <div class="form-group">
                <div class="row">
                  <div class="col-md-12">
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <strong>
                          <span class="glyphicon glyphicon-picture"></span>
                          <span>Product Image</span>
                        </strong>
                      </div>
                      <div class="panel-body">
                        <input type="file" name="product-image" class="form-control">
                        <p class="help-block">Upload an image for this product.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <button type="submit" name="add_product" class="btn btn-danger">Add product</button>
          </form>
         </div>
        </div>
      </div>
    </div>
  </div>

<?php include_once('layouts/footer.php'); ?>
