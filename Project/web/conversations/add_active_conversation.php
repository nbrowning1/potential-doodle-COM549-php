<?php

include_once('../db/connection.php');
include_once('../db/include.php');
include_once('manage_conversations.php');

session_start();

$nameToAdd = isset($_POST["nameToAdd"]) ? $_POST["nameToAdd"] : "";
$groupToAdd = isset($_POST["groupToAdd"]) ? $_POST["groupToAdd"] : "";

if (empty($nameToAdd) && empty($groupToAdd)) {
  $error = errorResponse();
  $error["searchError"] = "Name cannot be empty";
  returnError($error);
}

addActiveConversation($nameToAdd, $groupToAdd);

function addActiveConversation($nameToAdd, $groupToAdd) {
  // either add regular conversation or group conversation
  $conversationAddStatus = empty($groupToAdd) ?addConversationToActiveConversations($nameToAdd) :
  addGroupConversationToActiveConversations($groupToAdd);
  
  if (!$conversationAddStatus->success) {
    $error = errorResponse();
    $error["searchError"] = $conversationAddStatus->msg;
    returnError($error);
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