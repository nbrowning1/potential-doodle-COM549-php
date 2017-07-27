<html>
  <head>
  </head>
  <body>
    <?php
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    validateLoginForm($username, $password);
    $db = connectToDb();
    
    // needed for int(20) constraint on DB table
    $hashedPassword = hexdec( substr(sha1($password), 0, 7) );
    attemptLogin($db, $username, $hashedPassword);
    
    
    function validateLoginForm($username, $password) {
      if (empty($username) || empty($password)) {
        echo 'Username or password cannot be empty.';
        exit;
      }
    }
    
    function connectToDb() {
      $host = 'localhost';
      $username = 'root';
      $password = 'root';
      $database = 'practical2';
      $db = new mysqli($host, $username, $password, $database);
      if (mysqli_connect_errno()) {
        echo 'Could not connect to database';
        exit;
      } else {
        return $db;
      }
    }
    
    function attemptLogin($db, $username, $password) {
      $stmt = $db->prepare('SELECT * FROM users WHERE Username = ? AND Password = ?');
      
      $stmt->bind_param('si', $username, $password);
      $stmt->execute();
      $stmt->store_result();
      
      validateCredentials($stmt);
      
      $stmt->bind_result($uId, $uFirstName, $uSurname, $uUsername, $uPassword, $uDateReg, $uUserType);
      $stmt->fetch();
      
      // uses binded variables from above to show landing page for user
      include 'landingPage.php';
      
      $stmt->free_result();
    }
    
    function validateCredentials($stmt) {
      if ($stmt->error) {
        echo 'Error occurred: ' . $stmt->error;
        exit;
      }
      
      if ($stmt->num_rows == 0) {
        echo 'Authentication failed - re-check your username and password.';
        exit;
      }
    }
    
    ?>
  </body>
</html>