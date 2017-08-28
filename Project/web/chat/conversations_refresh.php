<?php

require_once('../include.php');

session_start();
$currentUsername = $_SESSION['user'];
// TODO: more appropriate defaults?
$active = isset($_SESSION['active']) ? $_SESSION['active'] : -1;

$db = connectToDb();
$currentUser = getUserByUsername($db, $currentUsername);

echoRegularConversations($db, $currentUser, $active);
echoGroupConversations($db, $currentUser, $active);

function echoRegularConversations($db, $currentUser, $active) {
  
  $conversations = getConversations($db, $currentUser);
  $conversationsToOutput = array();
  $conversationsToOutput["standard"] = array();
  $conversationsToOutput["blocked"] = array();
  $conversationsToOutput["favourite"] = array();
  
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

    // if active class, add class to modify the style of conversation
    $activeClass = $active == $otherUser->username ? 'active' : '';
    
    // if user is blocked, add class to modify the style of conversation
    $userIsBlocked = isUserBlockedForUser($db, $currentUser, $otherUser);
    $blockedClass = $userIsBlocked ? 'blocked' : '';
    
    // if user is favourited, add class to modify the style of conversation
    $userIsFavourited = isUserFavouritedForUser($db, $currentUser, $otherUser);
    $favouritedClass = $userIsFavourited ? 'favourited' : '';
    
    $displayName = $otherUser->username;
    if ($unreadMsgCount > 0) {
      $displayName .= " <div class=\"conversation-number-unread\">&nbsp;$unreadMsgCount&nbsp;</div>";
    }

    $outputHtml = '<a href="#" class="conversation btn btn-default ' . $activeClass . ' ' . $blockedClass . $favouritedClass . '" role="button" id="' . $otherUser->username . '">' . $displayName . '<span class="glyphicon glyphicon-remove"></span></a>';
    
    if ($userIsBlocked) {
      array_push($conversationsToOutput["blocked"], $outputHtml);
    } else if ($userIsFavourited) {
      array_push($conversationsToOutput["favourite"], $outputHtml);
    } else {
      array_push($conversationsToOutput["standard"], $outputHtml);
    }
  }
  
  foreach ($conversationsToOutput["favourite"] as $favouriteConversationHtml) {
    echo $favouriteConversationHtml;
  }
  foreach ($conversationsToOutput["standard"] as $standardConversationHtml) {
    echo $standardConversationHtml;
  }
  foreach ($conversationsToOutput["blocked"] as $blockedConversationHtml) {
    echo $blockedConversationHtml;
  }
}

function echoGroupDivider() {
  echo '<div class="conversations-divider"><b><u><i>Groups</i></u></b></div>';
}

function echoGroupConversations($db, $currentUser, $active) {
  
  $groupConversations = getGroupsForUser($db, $currentUser, true);
  $groupConversationsToOutput = array();
  
  foreach ($groupConversations as $groupConversation) {
    
    $unreadMsgCount = count(getUnreadChatMessagesForUser($db, $groupConversation, true, $currentUser));

    // if active class, add class to modify the style of conversation
    $activeClass = $active == $groupConversation->name ? 'active' : '';
    
    $displayName = $groupConversation->name;
    if ($unreadMsgCount > 0) {
      $displayName .= " <div class=\"conversation-number-unread\">&nbsp;$unreadMsgCount&nbsp;</div>";
    }

    $groupConversationHtml = '<a href="#" class="conversation group-conversation btn btn-default ' . $activeClass . '" role="button" id="' . $groupConversation->name . '">' . $displayName . '<span class="glyphicon glyphicon-remove"></span></div>';
    array_push($groupConversationsToOutput, $groupConversationHtml);
  }
  
  if (!empty($groupConversationsToOutput)) {
    echoGroupDivider();
  }
  foreach ($groupConversationsToOutput as $groupConversationHtml) {
    echo $groupConversationHtml;
  }
}

?>