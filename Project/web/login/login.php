<?php

require_once('../include.php');

$username = getPostValue('username');
$password = getPostValue('password');

validateLoginForm($username, $password);
$db = connectToDb();

if (successfulLogin($db, $username, $password)) {
  session_start();
  // get username in the case that the user registered as - allows session to be set properly while allowing case-insensitive login
  $_SESSION['user'] = getUsernameProperCase($db, $username);
  
  // send redirect response to let JS handle it because apparently header doesn't work - tried multiple workarounds e.g. ob_start but nothing worked
  $redirectResponse = redirectResponse('../chat.php');
  returnJson($redirectResponse);
} else {
  returnErrorResponse('validationFailure', 'Authentication failed');
}

function validateLoginForm($username, $password) {
  if (anyEmpty($username, $password)) {
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