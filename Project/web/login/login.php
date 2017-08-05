<?php

include_once('../db/connection.php');
include_once('../db/query/users.php');
include_once('../utils.php');

$username = $_POST['username'];
$password = $_POST['password'];

validateLoginForm($username, $password);
$db = connectToDb();

if (successfulLogin($db, $username, $password)) {
  session_start();
  $_SESSION['user'] = $username;
  
  // send redirect response to let JS handle it because apparently header doesn't work
  $redirectResponse = redirectResponse("../chat.php");
  returnJson($redirectResponse);
} else {
  $errorResponse = errorResponse();
  $errorResponse["validationFailure"] = "Authentication failed";
  returnJson($errorResponse);
}

function validateLoginForm($username, $password) {
  if (empty($username) || empty($password)) {
    $errorResponse = errorResponse();
    if (empty($username)) {
      $errorResponse["usernameError"] = "Username cannot be empty";
    }
    if (empty($password)) {
      $errorResponse["passwordError"] = "Password cannot be empty";
    }
    returnJson($errorResponse);
  }
}

?>