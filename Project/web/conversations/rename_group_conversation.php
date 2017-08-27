<?php

require_once('../include.php');

session_start();
$db = connectToDb();  
$currentUser = getUserByUsername($db, $_SESSION['user']);

$oldName = getPostValue('oldName');
$newName = getPostValue('newName');

validateData($db, $oldName, $newName);

function validateData($db, $oldName, $newName) {
  if ($oldName == $newName) {
    exit;
  }

  if (empty($newName)) {
    returnErrorResponse('groupNameError', 'New name cannot be empty');
  }

  if (!groupNameIsAvailable($db, $newName)) {
    returnErrorResponse('groupNameError', 'A group already exists with this name');
  }
}

renameGroupConversation($db, $oldName, $newName, $currentUser);

function renameGroupConversation($db, $oldName, $newName, $currentUser) {
  updateGroupName($db, $oldName, $newName);
  
  // show admin message to inform group of change
  $conversation = getGroupByName($db, $newName);
  $message = "$currentUser->username renamed this conversation from '$oldName' to '$newName'";
  insertAdminChatMessageToDb($db, $currentUser, $message, $conversation, true);
}

?>