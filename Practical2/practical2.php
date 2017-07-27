<html>
  <head>
  </head>
  <body>
    <?php
    
    $firstName = $_POST['firstname'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $userType = $_POST['user_type'];
    
    $db = connectToDb();
    validateForm($firstName, $surname, $email, $password, $confirmPassword);
    validateForDb($db, $email);
    
    $userTypeId = getUserTypeId($userType);
    insertUserToDb($db, $firstName, $surname, $email, $password, $userTypeId);
    showUsersFromDb($db);
    
    $db->close();
    
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
    
    function validateForm($firstName, $surname, $email, $password, $confirmPassword) {
      // check all form fields filled in
      if (!filled_out($_POST)) {
        echo 'You have not filled the form out correctly - please go back and try again.';
        exit;
      }
      // check name is only letters and -
      if (!valid_name($firstName) || !valid_name($surname)) {
        echo 'That is not a valid name. Please go back and try again.';
        exit;
      }
      // check email address is valid
      if (!valid_email($email)) {
        echo 'That is not a valid email address. Please go back and try again.';
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

    function valid_name($name) {
      return (preg_match('/^[a-zA-Z\-]+$/', $name));
    }
    
    function valid_email($address) {
      // check an email address is possibly valid
      return (preg_match('/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/', $address));
    }
        
    function validateForDb($db, $username) {
      // check if username is unique
      $query = 'SELECT * FROM users WHERE Username = ?';
      $stmt = $db->prepare($query);
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $stmt->store_result();

      if ($stmt->error) {
        echo 'Error occurred: ' . $stmt->error;
        exit;
      }
      
      if ($stmt->num_rows > 0) {
        echo 'That username is taken - go back and choose another one.';
        exit;
      }
      
      $stmt->free_result();
    }
    
    function getUserTypeId($userType) {
      if ($userType == 'Super Admin') {
        return 0;
      } else if ($userType == 'Regular') {
        return 1;
      } else {
        echo 'Invalid user type specified.';
        exit;
      }
    }

    function insertUserToDb($db, $firstName, $surname, $username, $password, $userType) {
      $query = 'INSERT INTO users(Firstname, Surname, Username, Password, DateRegistered, UserType) VALUES (?, ?, ?, ?, now(), ?)';
      $stmt = $db->prepare($query);
      
      // needed for int(20) constraint on DB table
      $hashedPassword = hexdec( substr(sha1($password), 0, 7) );
      
      $stmt->bind_param('sssii', $firstName, $surname, $username, $hashedPassword, $userType);
      $stmt->execute();
      if ($stmt->error) {
        echo 'Error occurred: ' . $stmt->error;
        exit;
      }
      
      $stmt->free_result();
    }
    
    function showUsersFromDb($db) {
      $stmt = $db->prepare('SELECT * FROM users');
      if ($stmt->error) {
        echo('Could not get users: ' . $stmt->error);
        exit;
      }
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($uId, $uFirstName, $uSurname, $uUsername, $uPassword, $uDateReg, $uUserType);
      
      $borderStyle = "border: 1px solid black;";
      $styleBorderStyle = "style=\"$borderStyle\"";
      
      echo "<table style=\"$borderStyle border-collapse: collapse; text-align: center;\">";
      echo "<tr style=\"$borderStyle font-weight: bold;\">
        <td $styleBorderStyle>User ID</td>
        <td $styleBorderStyle>First Name</td>
        <td $styleBorderStyle>Surname</td>
        <td $styleBorderStyle>Username</td>
        <td $styleBorderStyle>Password Hash</td>
        <td $styleBorderStyle>Registration Date</td>
        <td $styleBorderStyle>User Type ID</td>
      </tr>";
      while ($stmt->fetch()) {
        $tdFunc = "tableData";
        echo "<tr $styleBorderStyle>
          <td $styleBorderStyle>$uId</td>
          <td $styleBorderStyle>$uFirstName</td>
          <td $styleBorderStyle>$uSurname</td>
          <td $styleBorderStyle>$uUsername</td>
          <td $styleBorderStyle>$uPassword</td>
          <td $styleBorderStyle>$uDateReg</td>
          <td $styleBorderStyle>$uUserType</td>
        </tr>"; 
      }
      echo "</table>";
      
      $stmt->free_result();
    }
    
    ?>
  </body>
</html>