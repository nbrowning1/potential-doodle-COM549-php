<?php

require_once('../../include.php');

$username = getPostValue('username');
$answer = getPostValue('answer');
if (empty($username) || empty($answer)) {
  // should not happen, redirect to fresh recovery
  header("Location: recover.php");
}

$db = connectToDb();
$userToUpdate = getUserByUsername($db, $username);
$correctAnswer = $userToUpdate->recoveryA;
// re-validate to be sure
if (strtoupper($answer) != strtoupper($correctAnswer)) {
  // should not happen, redirect to fresh recovery
  header("Location: recover.php");
}

$password = getPostValue('password');
$confirmPassword = getPostValue('confirmPassword');
if (anyEmpty($password, $confirmPassword)) {
  $response = errorResponse();
  if (empty($password)) {
    $response['passwordError'] = "Password cannot be empty";
  }
  if (empty($confirmPassword)) {
    $response['confirmPasswordError'] = "Confirm Password cannot be empty";
  }
  returnJson($response);
}

if ($password != $confirmPassword) {
  returnErrorResponse('confirmPasswordError', 'Passwords do not match');
}

if (!isValidPassword($password)) {
  returnErrorResponse('passwordError', 'Password does not meet requirements. Password must be at least 6 characters, contain 1 lowercase, 1 uppercase, and 1 numeric character');
}

updateUserPassword($db, $userToUpdate->username, $password);

returnSuccessResponse();

?>