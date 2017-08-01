<?php

include_once('../db/connection.php');
include_once('../db/query/users.php');

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
  $errorResponse = errorResponse();
  $errorResponse["validationFailure"] = "Authentication failed";
  returnError($errorResponse);
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
    returnError($errorResponse);
  }
}

function errorResponse() {
  return array("error"=>true);
}

function returnError($errorArray) {
  header('Content-type: application/json');
  echo json_encode($errorArray);
  exit;
}

?>