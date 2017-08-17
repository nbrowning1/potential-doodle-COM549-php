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
  // If actually submitting form, perform registration
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Success assumed until error hit
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

// Get a post value, setting its value to empty if not defined at all
function getPostValueIfPresent($name) {
  return isset($_POST[$name]) ? $_POST[$name] : '';
}

function connectToDb() {
  $host = 'localhost';
  $username = 'B00652112';
  $password = 'pleaseleavealone1';
  $database = 'b00652112';
  $db = new mysqli($host, $username, $password, $database);
  if (mysqli_connect_errno()) {
    echo 'Could not connect to database';
    exit;
  } else {
    return $db;
  }
}

/* Checks that the form is valid - fields aren't empty and some stricter validation checks for fields
    like name / email address fields. If validation fails, specific error messages will be set to feed
    back to the user */
function formValid($firstName, $surname, $email, $password, $confirmPassword, $userType) {
  
  // For accessing & modifying these variables from outside the function
  global $fnameError;
  global $snameError;
  global $emailError;
  global $passwordError;
  global $cPasswordError;
  global $userTypeError;
  
  $formEmpty = empty($firstName) || empty($surname) || empty($email) || empty($password) || empty($confirmPassword) || empty($userType);
  
  // Set specific error messages for empty form fields
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
  
  // Check that name meets validation
  if (!validName($firstName) || !validName($surname)) {
    if (!validName($firstName)) {
      $fnameError = "Invalid name";
    }
    
    if (!validName($surname)) {
      $snameError = "Invalid name";
    }
    
    return false;
  }
  
  // Check that email address meets validation
  if (!validEmail($email)) {
    $emailError = "Invalid email address";
    return false;
  }
  
  // Check that password / confirmation matches
  if ($password != $confirmPassword) {
    $cPasswordError = "Passwords do not match";
    return false;
  }
  
  return true;
}

// Check name is in form [alphanumeric with -]
function validName($name) {
  return (preg_match('/^[a-zA-Z\-]+$/', $name));
}

// Check email address is in form [alphanumeric with _ . -]@[alphanumeric with -].[alphanumeric with - .]
function validEmail($address) {
  return (preg_match('/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/', $address));
}

// Check that username is valid for the DB - includes a check that the username isn't already in use
function validForDb($db, $username) {
  global $generalError;
  
  $query = 'SELECT * FROM UsersPracticals WHERE Username = ?';
  $stmt = $db->prepare($query);
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->error) {
    $generalError = 'Error occurred: ' . $stmt->error;
    return false;
  }

  // Check if username is unique - if any rows come back, there was a match with an existing user
  if ($stmt->num_rows > 0) {
    $generalError = 'Username taken! Please choose another';
    return false;
  }

  $stmt->free_result();
  
  return true;
}

/* Gets user type as an int, to meet data type defined by DB for this field. If unrecognised,
    an exception will be thrown */
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

// Inserts user to database, hashing password before insert
function insertUserToDb($db, $firstName, $surname, $username, $password, $userType) {
  // Uses MySQL function now() to get current dateTime without having to handle it in PHP
  $query = 'INSERT INTO UsersPracticals(Firstname, Surname, Username, Password, DateRegistered, UserType) VALUES (?, ?, ?, ?, now(), ?)';
  $stmt = $db->prepare($query);

  $hashedPassword = sha1($password);

  $stmt->bind_param('ssssi', $firstName, $surname, $username, $hashedPassword, $userType);
  $stmt->execute();
  if ($stmt->error) {
    throw new Exception('Error occurred: ' . $stmt->error);
  }

  $stmt->free_result();
}

// Shows users from the database in table format
function showUsersFromDb($db) {
  global $successTable;
  
  $stmt = $db->prepare('SELECT * FROM UsersPracticals');
  if ($stmt->error) {
    throw new Exception('Could not get users: ' . $stmt->error);
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
        <!-- Re-sets selected attribute if was previously selected (AKA if form failed validation) -->
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