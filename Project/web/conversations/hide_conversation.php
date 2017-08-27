<?php

require_once('../include.php');

session_start();
$active = $_SESSION['active'];
$currentUsername = $_SESSION['user'];

$hideName = getPostValue('hide');
$isGroupConversation = getPostValueBoolean('isGroupConversation');

$db = connectToDb();
$currentUser = getUserByUsername($db, $currentUsername);

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
  $response['noChat'] = true;
}
returnJson($response);

?>