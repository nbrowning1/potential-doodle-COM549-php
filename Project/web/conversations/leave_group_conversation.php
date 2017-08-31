<?php

require_once('../include.php');

session_start();
$currentUsername = getSessionValue('user');
$groupName = getPostValue('groupName');

$db = connectToDb();
$currentUser = getUserByUsername($db, $currentUsername);
removeSelfFromGroupConversation($db, $groupName, $currentUser);

function removeSelfFromGroupConversation($db, $groupName, $currentUser) {
  $group = getGroupByName($db, $groupName);
  
  // will also delete group if this is the last member leaving
  removeUserFromGroup($db, $group->id, $currentUser->id);
  
  // add admin style message to inform members of change
  $message = "$currentUser->username left the conversation";
  insertAdminChatMessageToDb($db, $currentUser, $message, $group, true);
}

?>