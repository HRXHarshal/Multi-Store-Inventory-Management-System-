<?php include_once('includes/load.php'); ?>
<?php
$req_fields = array('username','password' );
validate_fields($req_fields);
$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);

if(empty($errors)){
  // Add the authenticate function if it's missing
  if(!function_exists('authenticate')) {
    function authenticate($username='', $password='') {
      global $db;
      $username = $db->escape($username);
      $password = $db->escape($password);
      $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
      $result = $db->query($sql);
      if($db->num_rows($result)){
        $user = $db->fetch_assoc($result);
        $password_request = sha1($password);
        if($password_request === $user['password'] ){
          return $user;
        }
      }
      return false;
    }
  }
  
  $user = authenticate($username, $password);
  
  if($user){
    //create session with id
    $session->login($user['id']);
    //Update Sign in time
    updateLastLogIn($user['id']);
    // redirect user to group home page by user level
    if($user['user_level'] === '1'){
      $session->msg("s", "Hello ".$user['username'].", Welcome to Inventory Management System.");
      redirect('admin.php',false);
    } elseif ($user['user_level'] === '2') {
      $session->msg("s", "Hello ".$user['username'].", Welcome to Inventory Management System.");
      redirect('special.php',false);
    } else {
      $session->msg("s", "Hello ".$user['username'].", Welcome to Inventory Management System.");
      redirect('home.php',false);
    }
  } else {
    $session->msg("d", "Sorry Username/Password incorrect.");
    redirect('index.php',false);
  }
} else {
  $session->msg("d", $errors);
  redirect('index.php',false);
}
?>
