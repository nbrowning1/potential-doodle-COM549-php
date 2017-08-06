<?php

function insertChatMessageToDb($db, $creator, $message, $conversation, $isGroupConversation) {
  $query = 'INSERT INTO chat_messages(creator_id, message, conversation_id, group_conversation_id) VALUES (?, ?, ?, ?)';
  $stmt = $db->prepare($query);
  
  $conversationId;
  $groupConversationId;
  
  if ($isGroupConversation) {
    $conversationId = null;
    $groupConversationId = $conversation->id;
  } else {
    $conversationId = $conversation->id;
    $groupConversationId = null;
  }
  
  $stmt->bind_param('isii', $creator->id, $message, $conversationId, $groupConversationId);
  $stmt->execute();
  if ($stmt->error) {
    throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
  }

  $stmt->free_result();
  
  // insert user chat messages
  $messageId = mysqli_insert_id($db);
  
  if ($isGroupConversation) {
    
    $groupConversation = getGroupById($db, $groupConversationId);
    
    foreach ($groupConversation->members as $member) {
      $userId = $member->user->id;
      // set 'read' as true for creator of message
      $readStatus = ($creator->id == $userId) ? 1 : 0;
      
      insertUserChatMessageToDb($db, $userId, $messageId, $readStatus);
    }
    
  } else {
    
    $conversation = getConversationById($db, $conversationId);
    $user1Id = $conversation->user_1->id;
    $user2Id = $conversation->user_2->id;
    
    // set 'read' as true for creator of message
    $user1ReadStatus = ($creator->id == $user1Id) ? 1 : 0;
    $user2ReadStatus = $user1ReadStatus ? 0 : 1;
    
    insertUserChatMessageToDb($db, $user1Id, $messageId, $user1ReadStatus);
    insertUserChatMessageToDb($db, $user2Id, $messageId, $user2ReadStatus);
  }
}

function getAllChatMessagesForUser($db, $conversation, $isGroupConversation, $user) {
  
  $columnName;
  if ($isGroupConversation) {
    $columnName = 'group_conversation_id';
  } else {
    $columnName = 'conversation_id';
  }
  
  $query = "SELECT chat_messages.*, users_chat_messages.read_status
  FROM chat_messages
  INNER JOIN users_chat_messages ON chat_messages.id = users_chat_messages.message_id WHERE $columnName = ? AND users_chat_messages.user_id = ?";
  
  return getChatMessagesForUser($query, $db, $conversation, $isGroupConversation, $user);
}

function getUnreadChatMessagesForUser($db, $conversation, $isGroupConversation, $user) {
  
  $columnName;
  if ($isGroupConversation) {
    $columnName = 'group_conversation_id';
  } else {
    $columnName = 'conversation_id';
  }
  
  $query = "SELECT chat_messages.*, users_chat_messages.read_status
  FROM chat_messages
  INNER JOIN users_chat_messages ON chat_messages.id = users_chat_messages.message_id WHERE $columnName = ? AND users_chat_messages.user_id = ? AND users_chat_messages.read_status = 0";
  
  return getChatMessagesForUser($query, $db, $conversation, $isGroupConversation, $user);
}

function getChatMessagesForUser($query, $db, $conversation, $isGroupConversation, $user) {
  
  $stmt = $db->prepare($query);
  
  $stmt->bind_param('ii', $conversation->id, $user->id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($cmId, $cmCreatorId, $cmMessage, $cmConversationId, $cmGroupConversationId, $cmDateTime, $ucmRead);
  
  $userChatMessages = array();
  while ($stmt->fetch()) {
    $creator = getUserById($db, $cmCreatorId);
    
    if ($isGroupConversation) {
      $groupConversation = getGroupById($db, $cmGroupConversationId);
      
      $chatMessage = new ChatMessage($cmId, $creator, $cmMessage, null, $groupConversation, $cmDateTime);
      
      // null id - not getting from DB, just constructing it from chat message
      array_push($userChatMessages, new UserChatMessage(null, $user, $chatMessage, $ucmRead));
    } else {
      $conversation = getConversationById($db, $cmConversationId);
      
      $chatMessage = new ChatMessage($cmId, $creator, $cmMessage, $conversation, null, $cmDateTime);
      
      // null id - not getting from DB, just constructing it from chat message
      array_push($userChatMessages, new UserChatMessage(null, $user, $chatMessage, $ucmRead));
    }
  }
  
  return $userChatMessages;
}

function getChatMessageById($db, $id) {
  
  $stmt = $db->prepare("SELECT * FROM chat_messages WHERE id = ?");
  
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($cmId, $cmCreatorId, $cmMessage, $cmConversationId, $cmGroupConversationId, $cmDateTime);
  
  $stmt->fetch();
  $creator = getUserById($db, $cmCreatorId);
  
  $isGroupConversation = $cmGroupConversationId != null;
  if ($isGroupConversation) {
    $groupConversation = getGroupById($db, $cmGroupConversationId);

    return new ChatMessage($cmId, $creator, $cmMessage, null, $groupConversation, $cmDateTime);
  } else {
    $conversation = getConversationById($db, $cmConversationId);

    return new ChatMessage($cmId, $creator, $cmMessage, $conversation, null, $cmDateTime);
  }
}

?>