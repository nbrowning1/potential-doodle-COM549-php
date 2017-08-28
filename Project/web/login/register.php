<?php

require_once('../include.php');

$username = getPostValue('username');
$password = getPostValue('password');
$confirmPassword = getPostValue('confirmPassword');
$question = getPostValue('recoveryQuestion');
$answer = getPostValue('recoveryAnswer');

$db = connectToDb();
validateForm($username, $password, $confirmPassword, $question, $answer);

if (!usernameIsAvailable($db, $username)) {
  returnErrorResponse('validationFailure', 'Username taken - please choose another');
}

// TODO: add validation check (false if DB constraints fail)
insertUserToDb($db, $username, $password, $question, $answer);

$db->close();

// workaround in JS as PHP header() wasn't working for some reason
$redirectResponse = redirectResponse("login.html");
returnJson($redirectResponse);

// TODO: add length checks
function validateForm($username, $password, $confirmPassword, $question, $answer) {
  if (anyEmpty($username, $password, $confirmPassword, $question, $answer) || !isValidQuestion($question)) {
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
    returnErrorResponse('confirmPasswordError', 'Passwords do not match');
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