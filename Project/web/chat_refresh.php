<?php

include_once('db/connection.php');
include_once('db/include.php');
include_once('utils.php');

session_start();
$currentUsername = $_SESSION['user'];

$db = connectToDb();
$currentUser = getUserByUsername($db, $currentUsername);

$active;
if (isset($_POST['active'])) {
  $active = $_POST['active']; 
} else if ($_POST['use_same_active'] && !empty($_SESSION['active'])) {
  // used for new message where we're already on the active chat window
  $active = $_SESSION['active'];
} else {
  $response = array();
  $response['noChat'] = true;
  returnJson($response);
  exit;
}

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

$_SESSION['active'] = $active;

$conversation;
$chatTitle;
if ($isGroupConversation) {
  $conversation = getGroupByName($db, $active);
  $memberNames = array();
  foreach ($conversation->members as $member) {
    array_push($memberNames, $member->user->username);
  }
  $chatTitle = '<b>' . $conversation->name . '</b>: <i>' . implode(", ", $memberNames) . '</i>';
} else {
  $otherUser = getUserByUsername($db, $active);
  $conversation = getConversationByUsers($db, $currentUser, $otherUser);
  $chatTitle = '<b>' . $otherUser->username . '</b>';
}

// add message if new one sent
if (!empty($_POST['message'])) {
  $message = htmlspecialchars($_POST['message']);

  insertChatMessageToDb($db, $currentUser, $message, $conversation, $isGroupConversation);
}

$userChatMessages = getAllChatMessagesForUser($db, $conversation, $isGroupConversation, $currentUser);

$chatHtml = '';

$previousDateStr = null;
foreach ($userChatMessages as $userChatMessage) {
  $message = $userChatMessage->message;
  
  $date = strtotime($message->date_time);
  $dateStr = date("d/m/y", $date);

  // show date if date changed - visually separates messages on different dates
  if ($previousDateStr != $dateStr) {
    $chatHtml .= "<p class=\"chat-date-indicator\">$dateStr</p>";

    $previousDateStr = $dateStr;
  }

  $time = date("H:i", $date);

  // mark element with a class to show which user the message belongs to
  $msgCreator = $message->creator;
  $createdByCurrentUser = $msgCreator->id == $currentUser->id;

  $finalMessage;
  $creatorIndicator;
  if ($message->is_admin_message) {
    $creatorIndicator = 'message-admin';
    
    $finalMessage = "<span class=\"chat-message-content\">$message->message</span>";
  } else {
    $creatorIndicator = $createdByCurrentUser ? 'message-my' : 'message-other'; 
    
    $messageToDisplay = $message->message;
    $msgContent = "<span class=\"chat-message-content\">$messageToDisplay</span>";
    $timeContent = "<span class=\"chat-message-time-content $creatorIndicator\">$time</span>";
    $timeIcon = '<span class="glyphicon glyphicon-time"></span>';

    $finalMessage;
    // display message with time either to right or left depending on message creator. If group conversation, display names of other users beside message
    if ($isGroupConversation) {
      $msgCreator = "<span class=\"chat-message-creator\">$msgCreator->username</span>:";
      
      $finalMessage = $createdByCurrentUser ? "$msgContent $timeContent $timeIcon" : "$timeIcon $timeContent $msgCreator $msgContent";
    } else {
      $finalMessage = $createdByCurrentUser ? "$msgContent $timeContent $timeIcon" : "$timeIcon $timeContent $msgContent";
    }
  }
  
  $unreadMsgClass = $userChatMessage->read ? '' : 'unread-message'; 
    
  $chatHtml .= "<div class=\"chat-message $unreadMsgClass $creatorIndicator \">$finalMessage</div>";
}

// since user clicked on chat window, mark messages as read
markMessagesAsReadForUser($db, $currentUser, $userChatMessages);

$response = array();
$response['chatContent'] = $chatHtml;
$response['chatTitle'] = $chatTitle;

returnJson($response);
?>