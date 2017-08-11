<?php

include_once('../db/connection.php');
include_once('../db/include.php');

session_start();

$groupName = isset($_POST["groupName"]) ? $_POST["groupName"] : "";

$db = connectToDb();

$currentUser = getUserByUsername($db, $_SESSION['user']);

removeSelfFromGroupConversation($db, $groupName, $currentUser);


function removeSelfFromGroupConversation($db, $groupName, $currentUser) {
  
  $group = getGroupByName($db, $groupName);
  
  // will also delete group if this is the last member leaving
  removeUserFromGroup($db, $group->id, $currentUser->id);
  
  // add admin style message to inform members of change
  $message = "$currentUser->username left the conversation";
  
  insertAdminChatMessageToDb($db, $currentUser, $message, $group, true);
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