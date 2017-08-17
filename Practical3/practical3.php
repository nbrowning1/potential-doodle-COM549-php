<?php

$usernameError = $passwordError = $generalError = "";

$username = getPostValueIfPresent('username');
$password = getPostValueIfPresent('password');

// Get a post value, setting its value to empty if not defined at all
function getPostValueIfPresent($name) {
  return isset($_POST[$name]) ? $_POST[$name] : '';
}

// If actually submitting form, perform login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (formValid($username, $password)) {
    $db = connectToDb();

    // Comparing hashed password to database as they're already stored in their hashed version
    $hashedPassword = sha1($password);
    if (!attemptLogin($db, $username, $hashedPassword)) {
      $generalError = 'Authentication failed - re-check your username and password';
    }
  }
}

// Checking form is valid by way of the fields not being empty
function formValid($username, $password) {
  if (empty($username) || empty($password)) {
    global $usernameError;
    global $passwordError;
    if (empty($username)) {
      $usernameError = "Username cannot be empty";
    }
    if (empty($password)) {
      $passwordError = "Password cannot be empty";
    }
    return false;
  }
  return true;
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

function attemptLogin($db, $username, $password) {
  $stmt = $db->prepare('SELECT * FROM UsersPracticals WHERE Username = ? AND Password = ?');

  $stmt->bind_param('si', $username, $password);
  $stmt->execute();
  $stmt->store_result();

  if (!validateCredentials($stmt)) {
    return false;
  }

  $stmt->bind_result($uId, $uFirstName, $uSurname, $uUsername, $uPassword, $uDateReg, $uUserType);
  $stmt->fetch();

  $stmt->free_result();
  
  // Uses binded variables from above to show landing page for user
  include 'landingPage.php';
  exit;
}

// Returns true if no problems found, AKA unexpected errors or no rows returned from query
function validateCredentials($stmt) {
  if ($stmt->error) {
    echo 'Error occurred: ' . $stmt->error;
    exit;
  }

  if ($stmt->num_rows == 0) {
    return false;
  }
  
  return true;
}

?>

<html>
  <head>
    <style>
    *        {font-family: Arial, Helvetica, sans-serif;}
    .error   {color: red;}
    .label   {width: 125px; display: inline-block;}
    </style>
  </head>
  <body>
    <h2>Login Form</h2>
    <form action="practical3.php" method="post">
      <p class="label">Username</p>
      <input name="username" type="text" placeholder="Enter Username" value="<?php echo htmlspecialchars($username);?>"> 
      <span class="error"><?php echo $usernameError;?></span>
      <br>
      
      <p class="label">Password</p>
      <input name="password" type="password" placeholder="Enter Password">
      <span class="error"><?php echo $passwordError;?></span>
      <br>
      <br>
      
      <input type="submit" value="Submit" style="margin-left: 150px;">
      <br>
      <span class="error"><?php echo $generalError;?></span>
    </form>
  </body>
</html>