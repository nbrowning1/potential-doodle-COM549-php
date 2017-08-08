<?php

$fnameError = $snameError = $emailError = $passwordError = $cPasswordError = $userTypeError = $generalError = "";

$success = "";
$successTable = "";

$firstName = getPostValueIfPresent('firstname');
$surname = getPostValueIfPresent('surname');
$email = getPostValueIfPresent('email');
$password = getPostValueIfPresent('password');
$confirmPassword = getPostValueIfPresent('confirm_password');
$userType = getPostValueIfPresent('user_type');

try {
  // if actually submitting form
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $successStatus = true;
    $successTable = "";

    if (formValid($firstName, $surname, $email, $password, $confirmPassword, $userType)) {
      $db = connectToDb();

      if (validForDb($db, $email)) {
        $userTypeId = getUserTypeId($userType);
        insertUserToDb($db, $firstName, $surname, $email, $password, $userTypeId);
        showUsersFromDb($db);

        $db->close();

      } else {
        $successStatus = false;
      }
    } else {
      $successStatus = false;
    }

    if ($successStatus) {
      $success = "Added user $firstName $surname - email [$email] as a $userType user";

      // clear down error values because it was a success
      $fnameError = $snameError = $emailError = $passwordError = $cPasswordError = $userTypeError = $generalError = "";
    } else {
      // clear down any previous success message if present
      $success = "";
    }
  }
} catch (Exception $e) {
  $generalError = $e->getMessage();
}

function getPostValueIfPresent($name) {
  return isset($_POST[$name]) ? $_POST[$name] : "";
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

function formValid($firstName, $surname, $email, $password, $confirmPassword, $userType) {
  
  global $fnameError;
  global $snameError;
  global $emailError;
  global $passwordError;
  global $cPasswordError;
  global $userTypeError;
  
  $formEmpty = empty($firstName) || empty($surname) || empty($email) || empty($password) || empty($confirmPassword) || empty($userType);
    
  if ($formEmpty) {
    
    if (empty($firstName)) {
      $fnameError = "First name cannot be empty";
    }
    
    if (empty($surname)) {
      $snameError = "Surname cannot be empty";
    }
    
    if (empty($email)) {
      $emailError = "Email cannot be empty";
    }
    
    if (empty($password)) {
      $passwordError = "Password cannot be empty";
    }
    
    if (empty($confirmPassword)) {
      $cPasswordError = "Confirm Password cannot be empty";
    }
    
    if (empty($userType)) {
      $userTypeError = "User type cannot be empty";
    }
    
    return false;
  }
  
  // check name is only letters and -
  if (!valid_name($firstName) || !valid_name($surname)) {
    if (!valid_name($firstName)) {
      $fnameError = "Invalid name";
    }
    
    if (!valid_name($surname)) {
      $snameError = "Invalid name";
    }
    
    return false;
  }
  
  // check email address is valid
  if (!valid_email($email)) {
    $emailError = "Invalid email address";
    return false;
  }
  
  // check that password / confirmation matches
  if ($password != $confirmPassword) {
    $cPasswordError = "Passwords do not match";
    return false;
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

function validForDb($db, $username) {
  global $generalError;
  
  // check if username is unique
  $query = 'SELECT * FROM users WHERE Username = ?';
  $stmt = $db->prepare($query);
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->error) {
    $generalError = 'Error occurred: ' . $stmt->error;
    return false;
  }

  if ($stmt->num_rows > 0) {
    $generalError = 'That username is taken - go back and choose another one.';
    return false;
  }

  $stmt->free_result();
  
  return true;
}

function getUserTypeId($userType) {
  if ($userType == 'Super Admin') {
    return 0;
  } else if ($userType == 'Admin') {
    return 1;
  } else if ($userType == 'Coach') {
    return 2;
  } else {
    throw new Exception('Invalid user type [' . $userType . '] specified.');
  }
}

function insertUserToDb($db, $firstName, $surname, $username, $password, $userType) {
  $query = 'INSERT INTO users(Firstname, Surname, Username, Password, DateRegistered, UserType) VALUES (?, ?, ?, ?, now(), ?)';
  $stmt = $db->prepare($query);

  $hashedPassword = sha1($password);

  $stmt->bind_param('ssssi', $firstName, $surname, $username, $hashedPassword, $userType);
  $stmt->execute();
  if ($stmt->error) {
    echo 'Error occurred: ' . $stmt->error;
    exit;
  }

  $stmt->free_result();
}

function showUsersFromDb($db) {
  global $successTable;
  
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

  $successTable .= "<table style=\"$borderStyle border-collapse: collapse; text-align: center;\">";
  $successTable .= "<tr style=\"$borderStyle font-weight: bold;\">
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
    $successTable .= "<tr $styleBorderStyle>
      <td $styleBorderStyle>$uId</td>
      <td $styleBorderStyle>$uFirstName</td>
      <td $styleBorderStyle>$uSurname</td>
      <td $styleBorderStyle>$uUsername</td>
      <td $styleBorderStyle>$uPassword</td>
      <td $styleBorderStyle>$uDateReg</td>
      <td $styleBorderStyle>$uUserType</td>
    </tr>"; 
  }
  $successTable .= "</table>";

  $stmt->free_result();
}

?>

<html>
  <head>
    <style>
    *        {font-family: Arial, Helvetica, sans-serif;}
    .success {color: green;}
    .error   {color: red;}
    .label   {width: 125px; display: inline-block;}
    </style>
  </head>
  <body>
    <h2>Registration Form</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
      
      <p class="label">Firstname</p> 
      <input name="firstname" type="text" value="<?php echo htmlspecialchars($firstName);?>">
      <span class="error"><?php echo $fnameError;?></span>
      <br>
      
      <p class="label">Surname</p> 
      <input name="surname" type="text" value="<?php echo htmlspecialchars($surname);?>">
      <span class="error"><?php echo $snameError;?></span>
      <br>
      
      <p class="label">Email</p> 
      <input name="email" type="text" value="<?php echo htmlspecialchars($email);?>">
      <span class="error"><?php echo $emailError;?></span>
      <br>
      
      <p class="label">Password</p> 
      <input name="password" type="password" value="<?php echo htmlspecialchars($password);?>">
      <span class="error"><?php echo $passwordError;?></span>
      <br>
      
      <p class="label">Confirm Password</p> 
      <input name="confirm_password" type="password" value="<?php echo htmlspecialchars($confirmPassword);?>">
      <span class="error"><?php echo $cPasswordError;?></span>
      <br>
      
      <p class="label">User Type</p> 
      <select name="user_type">
        <option <?php if($userType == 'Super Admin') echo "selected"; ?>>Super Admin</option>
        <option <?php if($userType == 'Admin') echo "selected"; ?>>Admin</option>
        <option <?php if($userType == 'Coach') echo "selected"; ?>>Coach</option>
      </select>
      <span class="error"><?php echo $userTypeError;?></span>
      <br>
      <br>
      
      <input style="margin-left: 150px;" type="submit" value="Submit">
      <br>
      <br>
      <span class="error"><?php echo $generalError;?></span>
      <span class="success"><?php echo $success;?></span>
      <?php echo $successTable; ?>
    </form>
  </body>
</html>