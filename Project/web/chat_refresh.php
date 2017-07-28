<?php

  include_once('db/connection.php');
  include_once('db/include.php');

  $people = array();

  session_start();
  $currentUsername = $_SESSION['user'];

  $db = connectToDb();
  $currentUser = getUserByUsername($db, $currentUsername);

  $active;
  if (isset($_POST['active'])) {
    $active = $_POST['active']; 
  } else if ($_POST['use_same_active']) {
    // used for new message where we're already on the active chat window
    $active = $_SESSION['active'];
  } else {
    $active = 'Nobody right now';
  }

  $_SESSION['active'] = $active;

  $otherUser = getUserByUsername($db, $active);
  $conversation = getConversationByUsers($db, $currentUser, $otherUser);

  // add message if new one sent
  if (!empty($_POST['message'])) {
    $message = $_POST['message'];
    
    // TODO: group message not hardcoded
    insertChatMessageToDb($db, $currentUser, $message, $conversation, false);
  }
  
  // TODO: group message not hardcoded
  $chatMessages = getChatMessages($db, $conversation, false);

  $previousDateStr = null;

  foreach ($chatMessages as $message) {
    
    $date = strtotime($message->date_time);
    $dateStr = date("d/m/y", $date);
    
    // show date if date changed - visually separates messages on different dates
    if ($previousDateStr != $dateStr) {
      echo "<p class=\"chat-date-indicator\">$dateStr</p>";
      
      $previousDateStr = $dateStr;
    }
    
    $time = date("H:i", $date);
    
    // mark element with a class to show which user the message belongs to
    $msgCreator = $message->creator;
    $createdByCurrentUser = $msgCreator->id == $currentUser->id;
    
    $creatorIndicator = $createdByCurrentUser ? 'message-my' : 'message-other';
    
    $messageToDisplay = $message->message;
    $msgContent = "<span class=\"chat-message-content\">$messageToDisplay</span>";
    $timeContent = "<span class=\"chat-message-time-content $creatorIndicator\">$time</span>";
    $timeIcon = '<span class="glyphicon glyphicon-time"></span>';
    
    // display message with time either to right or left depending on message creator
    $messageWithTime = $createdByCurrentUser ? "$msgContent $timeContent $timeIcon" : "$timeIcon $timeContent $msgContent";
    
    echo "<div class=\"chat-message $creatorIndicator\">$messageWithTime</div>";
  }
?>