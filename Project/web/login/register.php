<?php

include_once('../db/connection.php');
include_once('../db/include.php');
include_once('../utils.php');

$username = $_POST['username'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];
$question = isset($_POST['recoveryQuestion']) ? $_POST['recoveryQuestion'] : '';
$answer = $_POST['recoveryAnswer'];

$db = connectToDb();
validateForm($username, $password, $confirmPassword, $question, $answer);

if (!usernameIsAvailable($db, $username)) {
  $errorResponse = errorResponse();
  $errorResponse["validationFailure"] = "Username taken - please choose another";
  returnJson($errorResponse);
}

// TODO: add validation check (false if DB constraints fail)
insertUserToDb($db, $username, $password, $question, $answer);

$db->close();

$redirectResponse = redirectResponse("login.html");
returnJson($redirectResponse);

// TODO: add length checks
function validateForm($username, $password, $confirmPassword, $question, $answer) {
  if (empty($username) || empty($password) || empty($confirmPassword) || empty($question) || !isValidQuestion($question) || empty($answer)) {
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
    if (empty($question)) {
      $errorResponse["recoveryQuestionError"] = "Recovery Question cannot be empty";
    } else if (!isValidQuestion($question)) {
      $errorResponse["recoveryQuestionError"] = "Invalid Recovery Question";
    }
    if (empty($answer)) {
      $errorResponse["recoveryAnswerError"] = "Recovery Answer cannot be empty";
    }
    returnJson($errorResponse);
  }
  
  if ($password != $confirmPassword) {
    $errorResponse = errorResponse();
    $errorResponse["confirmPasswordError"] = "Passwords do not match";
    returnJson($errorResponse);
  }
}

function isValidQuestion($question) {
  return 
    $question == "What is your mother's maiden name?" ||
    $question == "What was the name of your first teacher?" ||
    $question == "What is your pet's name?" ||
    $question == "Where were you born?";
}

?>