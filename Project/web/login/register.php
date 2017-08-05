<?php
//ob_start();

//header("Location: login.html");
//echo "<script type='text/javascript'>window.top.location='http://example.com/';</script>";
//exit;

include_once('../db/connection.php');
include_once('../db/include.php');
include_once('../utils.php');

$username = $_POST['username'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

$db = connectToDb();
validateForm($username, $password, $confirmPassword);

if (!usernameIsAvailable($db, $username)) {
  $errorResponse = errorResponse();
  $errorResponse["validationFailure"] = "Username taken - please choose another";
  returnJson($errorResponse);
}

// TODO: add validation check (false if DB constraints fail)
insertUserToDb($db, $username, $password, 'hint', 'password');

updateUsersCache($db);

$db->close();

$redirectResponse = redirectResponse("login.html");
returnJson($redirectResponse);

// TODO: add length checks
function validateForm($username, $password, $confirmPassword) {
  if (empty($username) || empty($password) || empty($confirmPassword)) {
    $errorResponse = errorResponse();
    if (empty($username)) {
      $errorResponse["usernameError"] = "Username cannot be empty";
    }
    if (empty($password)) {
      $errorResponse["passwordError"] = "Password cannot be empty";
    }
    if (empty($confirmPassword)) {
      $errorResponse["confirmPasswordError"] = "Confirm Password cannot be empty";
    }
    returnJson($errorResponse);
  }
  
  if ($password != $confirmPassword) {
    $errorResponse = errorResponse();
    $errorResponse["confirmPasswordError"] = "Passwords do not match";
    returnJson($errorResponse);
  }
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