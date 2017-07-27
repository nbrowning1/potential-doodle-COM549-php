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
}

function getChatMessages($db, $conversation, $isGroupConversation) {
  
  $columnName;
  if ($isGroupConversation) {
    $columnName = 'group_conversation_id';
  } else {
    $columnName = 'conversation_id';
  }
  
  $stmt = $db->prepare("SELECT * FROM chat_messages WHERE $columnName = ?");
  
  $stmt->bind_param('i', $conversation->id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($cmId, $cmCreatorId, $cmMessage, $cmConversationId, $cmGroupConversationId, $cmDateTime);
  
  $chatMessages = array();
  while ($stmt->fetch()) {
    $creator = getUserById($db, $cmCreatorId);
    
    if ($isGroupConversation) {
      // TODO: implement
      $groupConversation = getGroupConversation($db, $cmGroupConversationId);
      
      array_push($chatMessages, new ChatMessage($cmId, $creator, $cmMessage, null, $groupConversation, $cmDateTime));
    } else {
      $conversation = getConversationById($db, $cmConversationId);
      
      array_push($chatMessages, new ChatMessage($cmId, $creator, $cmMessage, $conversation, null, $cmDateTime));
    }
  }
  
  return $chatMessages;
}

?>