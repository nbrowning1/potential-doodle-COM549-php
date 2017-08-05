<?php
  
include_once('db/connection.php');
include_once('db/include.php');

session_start();
$currentUsername = $_SESSION['user'];
$active = isset($_SESSION['active']) ? $_SESSION['active'] : -1;
$hideId = isset($_POST['hide_id']) ? $_POST['hide_id'] : -1;

$db = connectToDb();
$currentUser = getUserByUsername($db, $currentUsername);

echoRegularConversations($db, $currentUser, $active, $hideId);
echoGroupConversations($db, $currentUser, $active, $hideId);

function echoRegularConversations($db, $currentUser, $active, $hideId) {
  
  $conversations = getConversations($db, $currentUser);
  foreach ($conversations as $conversation) {
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

    echo '<a href="#" class="conversation btn btn-default ' . $activeClass . '" role="button" id=' . $otherUser->username . '>' . $otherUser->username . '<span class="glyphicon glyphicon-remove"></span></div>';
  }
}

function echoGroupConversations($db, $currentUser, $active, $hideId) {
  
  $groupConversations = getGroupsForUser($db, $currentUser);
  foreach ($groupConversations as $groupConversation) {
    
    // hide conversation if refresh request desires it
    if ($groupConversation->name == $hideId) {
      updateGroupUserGroupVisibility($db, $groupConversation->id, $currentUser);
      continue;
    }

    // if active class, add class to modify the style of conversation
    $activeClass = $active == $groupConversation->name ? 'active' : '';

    echo '<a href="#" class="conversation group-conversation btn btn-default ' . $activeClass . '" role="button" id=' . $groupConversation->name . '>' . $groupConversation->name . '<span class="glyphicon glyphicon-remove"></span></div>';
  }
}

?>