<?php
  include_once('../db/connection.php');
  include_once('../db/include.php');
?>

<html>
  <head>
  </head>
  <body>
    <?php
    
    // TODO: add hint and answer
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    $db = connectToDb();
    validateForm($username, $password, $confirmPassword);
    // TODO: redirect back if failure
    if (!usernameIsAvailable($db, $username)) {
      echo 'Username not available';
      exit;
    }
    
    insertUserToDb($db, $username, $password, 'hint', 'password');
    
    updateUsersCache($db);
    
    $db->close();
    
    session_start();
    $_SESSION['user'] = $username;
    // redirects to chat and exits this file
    header("Location: ../chat.php");
    exit;
    
    // TODO: add length checks, redirect back on failure
    function validateForm($username, $password, $confirmPassword) {
      // check all form fields filled in
      if (!filled_out($_POST)) {
        echo 'You have not filled the form out correctly - please go back and try again.';
        exit;
      }
      
      // check that password / confirmation matches
      if ($password != $confirmPassword) {
        echo 'The passwords you entered do not match - please go back and try again.';
        exit;
      }
    }
    
    function filled_out($form_vars) {
      // test that each variable has a value
      foreach ($form_vars as $key => $value) {
        if ((!isset($key)) || ($value == '')) {
          return false;
        }
      }
      return true;
    }
    
    function updateUsersCache($db) {
      $users = getAllUsers($db);
      $usersToWrite = array();
      
      foreach ($users as $user) {
        $usersToWrite[] = array('username'=> $user->username);
      } 

      $fp = fopen('../../cache/users.json', 'w');
      fwrite($fp, json_encode($usersToWrite));
      fclose($fp);
    }
    
    ?>
  </body>
</html>