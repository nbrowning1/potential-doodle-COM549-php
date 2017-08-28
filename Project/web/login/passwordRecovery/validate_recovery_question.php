<?php

require_once('../../include.php');

$username = getPostValue('username');
if (empty($username)) {
  // should not happen, redirect to fresh recovery
  header("Location: recover.php");
}
$answer = getPostValue('answer');
if (empty($answer)) {
  returnErrorResponse('answerError', 'Answer cannot be empty');
}

$db = connectToDb();
$correctAnswer = getUserByUsername($db, $username)->recoveryA;
// case-insensitive matching
if (strtoupper($answer) != strtoupper($correctAnswer)) {
  returnErrorResponse('answerError', 'Incorrect answer');
}

returnSuccessResponse();

?>