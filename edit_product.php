<?php
  $page_title = 'Edit product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
?>
<?php
$product = find_by_id('products',(int)$_GET['id']);
$all_categories = find_all('categories');
$all_warehouses = find_all('warehouses');
$all_photo = find_all('media');
if(!$product){
  $session->msg("d","Missing product id.");
  redirect('product.php');
}
?>
<?php
 if(isset($_POST['product'])){
    $req_fields = array('product-title','product-categorie','product-quantity','buying-price', 'saleing-price', 'product-warehouse' );
    validate_fields($req_fields);

   if(empty($errors)){
       $p_name  = remove_junk($db->escape($_POST['product-title']));
       $p_cat   = (int)$_POST['product-categorie'];
       $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
       $p_buy   = remove_junk($db->escape($_POST['buying-price']));
       $p_sale  = remove_junk($db->escape($_POST['saleing-price']));
       $p_warehouse = (int)$_POST['product-warehouse'];
       
       // Check if product name already exists (excluding current product)
       $check_sql = "SELECT * FROM products WHERE name = '{$p_name}' AND id != '{$product['id']}'";
       $check_result = find_by_sql($check_sql);
       
       if(!empty($check_result)) {
         $session->msg('d', "Product name '{$p_name}' already exists. Please choose a different name.");
         redirect('edit_product.php?id='.$product['id'], false);
         exit;
       }
       
       if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
         $media_id = $product['media_id']; // Keep existing media_id
       } else {
         $media_id = remove_junk($db->escape($_POST['product-photo']));
       }
       
       // Fix the query to properly handle the warehouse_id
       $query   = "UPDATE products SET";
       $query  .=" name ='{$p_name}', quantity ='{$p_qty}',";
       $query  .=" buy_price ='{$p_buy}', sale_price ='{$p_sale}', categorie_id ='{$p_cat}',";
       $query  .=" warehouse_id ='{$p_warehouse}', media_id ='{$media_id}'";
       $query  .=" WHERE id ='{$product['id']}'";
       $result = $db->query($query);
       
       if($result && $db->affected_rows() === 1 || $result){
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
             $sql = "UPDATE products SET media_id = '{$media_id}' WHERE id = '{$product['id']}'";
             $db->query($sql);
           }
         }
         
         $session->msg('s',"Product updated successfully");
         redirect('product.php', false);
       } else {
         $session->msg('d',' Sorry failed to update product!');
         redirect('edit_product.php?id='.$product['id'], false);
       }

   } else{
       $session->msg("d", $errors);
       redirect('edit_product.php?id='.$product['id'], false);
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
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Edit Product</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-7">
           <form method="post" action="edit_product.php?id=<?php echo (int)$product['id'] ?>" enctype="multipart/form-data">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="product-title" value="<?php echo remove_junk($product['name']);?>">
               </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <select class="form-control" name="product-categorie">
                    <option value=""> Select a categorie</option>
                   <?php  foreach ($all_categories as $cat): ?>
                     <option value="<?php echo (int)$cat['id']; ?>" <?php if($product['categorie_id'] === $cat['id']): echo "selected"; endif; ?> >
                       <?php echo remove_junk($cat['name']); ?></option>
                   <?php endforeach; ?>
                 </select>
                  </div>
                  <div class="col-md-6">
                    <select class="form-control" name="product-photo">
                      <option value=""> No image</option>
                      <?php  foreach ($all_photo as $photo): ?>
                        <option value="<?php echo (int)$photo['id'];?>" <?php if($product['media_id'] === $photo['id']): echo "selected"; endif; ?> >
                          <?php echo $photo['file_name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Add warehouse selection here -->
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <select class="form-control" name="product-warehouse">
                      <option value="">Select Warehouse</option>
                      <?php foreach ($all_warehouses as $warehouse): ?>
                        <option value="<?php echo (int)$warehouse['id'] ?>" <?php if($product['warehouse_id'] == $warehouse['id']): echo "selected"; endif; ?>>
                          <?php echo $warehouse['name'] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="file" name="product-image" class="form-control">
                      <p class="help-block">Upload a new image for this product.</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
               <div class="row">
                 <div class="col-md-4">
                  <div class="form-group">
                    <label for="qty">Quantity</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                       <i class="glyphicon glyphicon-shopping-cart"></i>
                      </span>
                      <input type="number" class="form-control" name="product-quantity" value="<?php echo remove_junk($product['quantity']); ?>">
                   </div>
                  </div>
                 </div>
                 <div class="col-md-4">
                  <div class="form-group">
                    <label for="qty">Buying price</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-usd"></i>
                      </span>
                      <input type="number" class="form-control" name="buying-price" value="<?php echo remove_junk($product['buy_price']);?>">
                      <span class="input-group-addon">.00</span>
                   </div>
                  </div>
                 </div>
                  <div class="col-md-4">
                   <div class="form-group">
                     <label for="qty">Selling price</label>
                     <div class="input-group">
                       <span class="input-group-addon">
                         <i class="glyphicon glyphicon-usd"></i>
                       </span>
                       <input type="number" class="form-control" name="saleing-price" value="<?php echo remove_junk($product['sale_price']);?>">
                       <span class="input-group-addon">.00</span>
                    </div>
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
                          <span>Current Product Image</span>
                        </strong>
                      </div>
                      <div class="panel-body">
                        <?php if($product['media_id'] === '0'): ?>
                          <img class="img-thumbnail" src="uploads/products/no_image.jpg" alt="">
                        <?php else: ?>
                          <?php $media = find_by_id('media', $product['media_id']); ?>
                          <img class="img-thumbnail" src="uploads/products/<?php echo $media['file_name']; ?>" alt="">
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <button type="submit" name="product" class="btn btn-danger">Update</button>
          </form>
         </div>
        </div>
      </div>
  </div>

<?php include_once('layouts/footer.php'); ?>
