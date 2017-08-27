<?php

include_once('../../db/connection.php');
include_once('../../db/include.php');
include_once('../../utils.php');

$username = getPostValue('username');
if (empty($username)) {
  // should not happen, redirect to fresh recovery
  header("Location: recover.php");
}
$answer = getPostValue('answer');
if (empty($answer)) {
  $response = errorResponse();
  $response['answerError'] = "Answer cannot be empty";
  returnJson($response);
}

$db = connectToDb();
$correctAnswer = getUserByUsername($db, $username)->recoveryA;
// case-insensitive matching
if (strtoupper($answer) != strtoupper($correctAnswer)) {
  $response = errorResponse();
  $response['answerError'] = "Incorrect answer";
  returnJson($response);
}

?>