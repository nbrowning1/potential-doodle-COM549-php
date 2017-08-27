<?php

require_once('../include.php');

// TODO: change to json response to be consistent with everything else
class AddStatus {
  public $success;
  public $msg;

  public function __construct($success, $msg) {
    $this->success = $success;
    $this->msg = $msg; 
  }
}

function addConversationToActiveConversations($userToAddUsername) {
  $db = connectToDb();
  $currentUsername = $_SESSION['user'];
  $currentUser = getUserByUsername($db, $currentUsername);
  $userToAdd = getUserByUsername($db, $userToAddUsername);

  if (anyEmpty($currentUser->id, $userToAdd->id)) {
    return new AddStatus(false, "Couldn't find user");
  }
  
  if ($currentUser->id == $userToAdd->id) {
    return new AddStatus(false, "That's you!");
  }
  
  $potentialConversation = getConversationByUsers($db, $currentUser, $userToAdd);
  
  if ($potentialConversation->id != null) {
    $currentUserIsUser1AndSetVisible = $currentUser->id == $potentialConversation->user_1->id && $potentialConversation->user_1_visibility == 1;
    $currentUserIsUser2AndSetVisible = $currentUser->id == $potentialConversation->user_2->id && $potentialConversation->user_2_visibility == 1;
    
    $conversationAlreadyVisibleForUser = $currentUserIsUser1AndSetVisible || $currentUserIsUser2AndSetVisible;
    
    if ($conversationAlreadyVisibleForUser) {
      return new AddStatus(true, null);
    } else {
      // make visible again
      updateConversationVisibility($db, $potentialConversation->id, $currentUser->id);
    }
  } else {
    // create new conversation between users - default visible for this user and hidden for other user
    insertConversationToDb($db, $currentUser, $userToAdd);
  }
  return new AddStatus(true, null);
}

function addGroupConversationToActiveConversations($groupToAddName) {
  $db = connectToDb();
  $currentUsername = $_SESSION['user'];
  $currentUser = getUserByUsername($db, $currentUsername);

  if (empty($groupToAddName)) {
    return new AddStatus(false, 'Enter a group name');
  }
  
  $potentialConversation = getGroupByName($db, $groupToAddName);
  
  if ($potentialConversation->id != null) {
    if (groupVisibleForUser($db, $potentialConversation->id, $currentUser)) {
      return new AddStatus(true, null);
    } else {
      // make visible again
      updateGroupUserGroupVisibility($db, $potentialConversation->id, $currentUser);
    }
  } else {
    return new AddStatus(false, "Couldn't find group");
  }
  return new AddStatus(true, null);
}

?>