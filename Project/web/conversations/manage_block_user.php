<?php

include_once('../db/connection.php');
include_once('../db/include.php');

session_start();

$targetUsername = isset($_POST["username"]) ? $_POST["username"] : "";
$blockStatus = isset($_POST["blockStatus"]) ? $_POST["blockStatus"] : "";
if (!($blockStatus == 'block' || $blockStatus == 'unblock')) {
  $response = errorResponse();
  $response['invalidOption'] = 'Invalid block status';
  returnError($response);
}
$shouldBlock = $blockStatus == 'block' ? true : false;

$db = connectToDb();

$currentUsername = $_SESSION['user'];

manageTargetUserBlockStatus($db, $currentUsername, $targetUsername, $shouldBlock);

function manageTargetUserBlockStatus($db, $currentUsername, $targetUsername, $shouldBlock) {
  if ($shouldBlock) {
    addBlockedUserByUsername($db, $currentUsername, $targetUsername);
  } else {
    removeBlockedUserByUsername($db, $currentUsername, $targetUsername);
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