<?php

include_once('../db/connection.php');
include_once('../db/include.php');
include_once('../utils.php');

session_start();

$active = $_SESSION['active'];
$hideName = isset($_POST["hide"]) ? $_POST["hide"] : "";
// TODO: DRY
$isGroupConversationStr = isset($_POST['isGroupConversation']) ? $_POST['isGroupConversation'] : false;
// needed because ajax calls pass variable values to PHP as a string... https://stackoverflow.com/questions/7408976/bool-parameter-from-jquery-ajax-received-as-literal-string-false-true-in-php
$isGroupConversation;
if ($isGroupConversationStr === 'true') {
  $isGroupConversation = true;
} else if ($isGroupConversationStr === 'false') {
  $isGroupConversation = false;
} else {
  // must already be boolean - assign directly
  $isGroupConversation = $isGroupConversationStr;
}

$db = connectToDb();
$currentUser = getUserByUsername($db, $_SESSION['user']);

if ($isGroupConversation) {
  $groupConversation = getGroupByName($db, $hideName);
  updateGroupUserGroupVisibility($db, $groupConversation->id, $currentUser);
} else {
  $otherUser = getUserByUsername($db, $hideName);
  $conversation = getConversationByUsers($db, $currentUser, $otherUser);
  updateConversationVisibility($db, $conversation->id, $currentUser->id);
}

$response = successResponse();
if ($hideName == $active) {
  $response["noChat"] = true;
}
returnJson($response);

?>