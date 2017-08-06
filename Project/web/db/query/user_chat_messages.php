<?php

function insertUserChatMessageToDb($db, $userId, $messageId, $read) {
  
  $readInsertVal = $read ? 1 : 0;
  
  $query = "INSERT INTO users_chat_messages(user_id, message_id, read_status) VALUES (?, ?, ?)";
  $stmt = $db->prepare($query);
  
  $stmt->bind_param('iii', $userId, $messageId, $readInsertVal);
  $stmt->execute();
  if ($stmt->error) {
    throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
  }

  $stmt->free_result();
}

function getUserChatMessagesForUser($db, $user) {
  $stmt = $db->prepare('SELECT * FROM users_chat_messages WHERE user_id = ?');
  
  $stmt->bind_param('i', $user->id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($ucmId, $ucmUserId, $ucmMessageId, $ucmRead);
  
  $userChatMessages = array();
  while ($stmt->fetch()) {
    $message = getChatMessageById($db, $ucmMessageId);
    
    array_push($userChatMessages, new UserChatMessage($ucmId, $user, $message, $ucmRead));
  }
  
  return $userChatMessages;
}

function markMessagesAsReadForUser($db, $user, $messages) {
  
  if (empty($messages)) {
    return;
  }
  
  // build IN clause because PHP prepared statements don't support this array-like parameter type
  $inClause = '';
  
  if (is_array($messages)) {
    foreach ($messages as $message) {
      $messageId = $message->message->id;
      
      // bit of safety since we're raw querying here
      if (!is_numeric($messageId)) {
        continue;
      }

      if (empty($inClause)) {
        $inClause .= $messageId;
      } else {
        $inClause .= ", $messageId";
      }
    }
  } else {
    // bit of safety since we're raw querying here
    if (!is_numeric($messageId)) {
      return;
    }

    $inClause .= $message->id;
  }
  
  $stmt = $db->prepare("UPDATE users_chat_messages SET read_status = 1 WHERE user_id = ? AND message_id IN ($inClause)");
  
  $stmt->bind_param('i', $user->id);
  $stmt->execute();
}

?>