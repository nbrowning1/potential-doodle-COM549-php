<?php

function insertConversationToDb($db, $user1, $user2) {
  $query = 'INSERT INTO conversations(user_1_id, user_2_id) VALUES (?, ?)';
  $stmt = $db->prepare($query);
  
  $stmt->bind_param('ii', $user1->id, $user2->id);
  $stmt->execute();
  if ($stmt->error) {
    throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
  }

  $stmt->free_result();
}

function getConversations($db, $user) {
  $stmt = $db->prepare('SELECT * FROM conversations WHERE (user_1_id = ? OR user_2_id = ?) AND visible = true');
  
  $stmt->bind_param('ii', $user->id, $user->id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($cId, $cUser1Id, $cUser2Id, $cVisible);
  
  $conversations = array();
  while ($stmt->fetch()) {
    $user1 = getUserById($db, $cUser1Id);
    $user2 = getUserById($db, $cUser2Id);
    
    array_push($conversations, new Conversation($cId, $user1, $user2, $cVisible));
  }
  
  // use ($user) to pass external variable into comparator function
  usort($conversations, function($a, $b) use ($user) {
    // find comparison username for first conversation being compared - we want the OTHER user in the conversation to compare
    $aCompareUsername = $user->id == $a->user_1->id ? $a->user_2->username : $a->user_1->username;
    // same for the second comparison conversation
    $bCompareUsername = $user->id == $b->user_1->id ? $b->user_2->username : $b->user_1->username;
    
    // returns int value (-1, 0, 1) indicating comparison status
    return strcmp($aCompareUsername, $bCompareUsername);
  });
  
  return $conversations;
}

function getConversationById($db, $conversationId) {
  $stmt = $db->prepare('SELECT * FROM conversations WHERE id = ?');
  
  $stmt->bind_param('i', $conversationId);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($cId, $cUser1Id, $cUser2Id, $cVisible);
  
  $stmt->fetch();
  
  $user1 = getUserById($db, $cUser1Id);
  $user2 = getUserById($db, $cUser2Id);
  return new Conversation($cId, $user1, $user2, $cVisible);
}

function getConversationByUsers($db, $user1, $user2) {
  $stmt = $db->prepare('SELECT * FROM conversations WHERE ((user_1_id = ? AND user_2_id = ?) OR (user_2_id = ? AND user_1_id = ?))');
  
  $stmt->bind_param('iiii', $user1->id, $user2->id, $user1->id, $user2->id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($cId, $cUser1Id, $cUser2Id, $cVisible);
  
  $stmt->fetch();
  
  $user1 = getUserById($db, $cUser1Id);
  $user2 = getUserById($db, $cUser2Id);
  return new Conversation($cId, $user1, $user2, $cVisible);
}

function updateConversationVisibility($db, $conversationId) {
  $conversation = getConversationById($db, $conversationId);
  
  // invert visibility
  $newVisible = $conversation->visible ? 0 : 1;
  
  $sql = "UPDATE conversations SET visible = $newVisible WHERE id = $conversationId";
  
  $db->query($sql);
}



?>