<?php
  include_once('../db/connection.php');
  include_once('../db/query/users.php');
?>

<html>
  <head>
  </head>
  <body>
    <?php
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    validateLoginForm($username, $password);
    $db = connectToDb();
    
    if(successfulLogin($db, $username, $password)) {
      session_start();
      $_SESSION['user'] = $username;
      // redirect to chat page
      header("Location: ../chat.php");
      exit;
    } else {
      // TODO: validation on login page
      // redirects to login page again
      header("Location: login.html");
      exit;
    }
    
    function validateLoginForm($username, $password) {
      if (empty($username) || empty($password)) {
        // redirects to login page again
        header("Location: login.html");
        exit;
      }
    }
    
    ?>
  </body>
</html>