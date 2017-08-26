<?php

include_once('../../db/connection.php');
include_once('../../db/include.php');
include_once('../../utils.php');

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
if ($answer != $correctAnswer) {
  // should not happen, redirect to fresh recovery
  header("Location: recover.php");
}

$password = getPostValue('password');
$confirmPassword = getPostValue('confirmPassword');
if (empty($password) || empty($confirmPassword)) {
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
  $response = errorResponse();
  $response['confirmPasswordError'] = "Passwords do not match";
  returnJson($response);
}

updateUserPassword($db, $userToUpdate->username, $password);

?>