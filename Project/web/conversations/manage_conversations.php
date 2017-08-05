<?php

include_once('../db/connection.php');
include_once('../db/include.php');

class AddStatus {
  public $success;
  public $msg;

  public function __construct($success, $msg) {
    $this->success = $success;
    $this->msg = $msg; 
  }
}

function addConversationToActiveConversations($userToAddUsername) {
  $currentUsername = $_SESSION['user'];

  $db = connectToDb();

  $currentUser = getUserByUsername($db, $currentUsername);
  $userToAdd = getUserByUsername($db, $userToAddUsername);

  if (empty($currentUser->id) || empty($userToAdd->id)) {
    return new AddStatus(false, "Couldn't find user");
  }
  
  if ($currentUser->id == $userToAdd->id) {
    return new AddStatus(false, "That's you, silly");
  }
  
  $potentialConversation = getConversationByUsers($db, $currentUser, $userToAdd);
  
  if ($potentialConversation->id != null) {
    $conversationAlreadyVisibleForUser = (($currentUser->id == $potentialConversation->user_1->id && $potentialConversation->user_1_visibility == 1) || ($currentUser->id == $potentialConversation->user_2->id && $potentialConversation->user_2_visibility == 1));
    
    if ($conversationAlreadyVisibleForUser) {
      return new AddStatus(false, "Conversation already exists");
    } else {
      // make visible again
      updateConversationVisibility($db, $potentialConversation->id, $currentUser->id);
    }
  } else {
    // create new conversation between users
    insertConversationToDb($db, $currentUser, $userToAdd);
  }
  return new AddStatus(true, null);
}

function addGroupConversationToActiveConversations($groupToAddName) {
  $currentUsername = $_SESSION['user'];

  $db = connectToDb();

  if (empty($groupToAddName)) {
    return new AddStatus(false, "Enter a group name");
  }
  
  $potentialConversation = getGroupByName($db, $groupToAddName);
  
  if ($potentialConversation->id != null) {
    if ($potentialConversation->visible) {
      return new AddStatus(false, "Conversation already exists");
    } else {
      // make visible again
      updateGroupConversationVisibility($db, $potentialConversation->id);
    }
  } else {
    return new AddStatus(false, "Couldn't find group");
  }
  return new AddStatus(true, null);
}

?>