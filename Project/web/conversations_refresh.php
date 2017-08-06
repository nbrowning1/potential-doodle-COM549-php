<?php
  
include_once('db/connection.php');
include_once('db/include.php');

session_start();
$currentUsername = $_SESSION['user'];
// TODO: more appropriate defaults?
$active = isset($_SESSION['active']) ? $_SESSION['active'] : -1;
$hideId = isset($_POST['hide_id']) ? $_POST['hide_id'] : -1;

$db = connectToDb();
$currentUser = getUserByUsername($db, $currentUsername);

echoRegularConversations($db, $currentUser, $active, $hideId);
echoGroupConversations($db, $currentUser, $active, $hideId);

function echoRegularConversations($db, $currentUser, $active, $hideId) {
  
  $conversations = getConversations($db, $currentUser);
  foreach ($conversations as $conversation) {
    
    $unreadMsgCount = count(getUnreadChatMessagesForUser($db, $conversation, false, $currentUser));
    
    $user1 = $conversation->user_1;
    $user2 = $conversation->user_2;
    $otherUser;
    if ($currentUser->id == $user1->id) {
      $otherUser = $user2;
    } else {
      $otherUser = $user1;
    }

    // hide conversation if refresh request desires it
    if ($otherUser->username == $hideId) {
      updateConversationVisibility($db, $conversation->id, $currentUser->id);
      continue;
    }

    // if active class, add class to modify the style of conversation
    $activeClass = $active == $otherUser->username ? 'active' : '';
    
    $displayName = $otherUser->username;
    if ($unreadMsgCount > 0) {
      $displayName .= " <div class=\"conversation-number-unread\">&nbsp;$unreadMsgCount&nbsp;</div>";
    }

    echo '<a href="#" class="conversation btn btn-default ' . $activeClass . '" role="button" id=' . $otherUser->username . '>' . $displayName . '<span class="glyphicon glyphicon-remove"></span></div>';
  }
}

function echoGroupConversations($db, $currentUser, $active, $hideId) {
  
  $groupConversations = getGroupsForUser($db, $currentUser);
  foreach ($groupConversations as $groupConversation) {
    
    $unreadMsgCount = count(getUnreadChatMessagesForUser($db, $groupConversation, true, $currentUser));
    
    // hide conversation if refresh request desires it
    if ($groupConversation->name == $hideId) {
      updateGroupUserGroupVisibility($db, $groupConversation->id, $currentUser);
      continue;
    }

    // if active class, add class to modify the style of conversation
    $activeClass = $active == $groupConversation->name ? 'active' : '';
    
    $displayName = $groupConversation->name;
    if ($unreadMsgCount > 0) {
      $displayName .= " <div class=\"conversation-number-unread\">&nbsp;$unreadMsgCount&nbsp;</div>";
    }

    echo '<a href="#" class="conversation group-conversation btn btn-default ' . $activeClass . '" role="button" id="' . $groupConversation->name . '">' . $displayName . '<span class="glyphicon glyphicon-remove"></span></div>';
  }
}

?>