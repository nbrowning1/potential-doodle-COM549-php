<?php

include_once('../db/connection.php');
include_once('../db/include.php');

session_start();

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

renameGroupConversation($oldName, $newName);

function renameGroupConversation($oldName, $newName) {
  $db = connectToDb();
  
  updateGroupName($db, $oldName, $newName);
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