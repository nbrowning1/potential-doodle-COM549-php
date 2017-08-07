<?php

include_once('../db/connection.php');
include_once('../db/include.php');

session_start();
$db = connectToDb();
  
$currentUser = getUserByUsername($db, $_SESSION['user']);

$oldName = isset($_POST["oldName"]) ? $_POST["oldName"] : "";
$newName = isset($_POST["newName"]) ? $_POST["newName"] : "";

// shouldn't need validation for old name because it's not form input - maybe put it for safety but cba rn
if (empty($newName)) {
  $error = errorResponse();
  if (empty($newName)) {
    $error["groupNameError"] = "New name cannot be empty";
  }
  returnError($error);
}

renameGroupConversation($db, $oldName, $newName, $currentUser);

function renameGroupConversation($db, $oldName, $newName, $currentUser) {
  
  updateGroupName($db, $oldName, $newName);
  
  $conversation = getGroupByName($db, $newName);
  $message = "$currentUser->username renamed this conversation from '$oldName' to '$newName'";
  
  insertAdminChatMessageToDb($db, $currentUser, $message, $conversation, true);
}

function errorResponse() {
  return array("error"=>true);
}

// TODO: DRY
function returnError($errorArray) {
  header('Content-type: application/json');
  echo json_encode($errorArray);
  exit;
}

?>