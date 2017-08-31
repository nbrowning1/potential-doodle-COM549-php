<?php

function isUserBlockedForUser($db, $currentUser, $targetUser) {
  $query = 'SELECT * FROM users_blocked_users WHERE user_id = ? AND blocked_user_id = ?';
  
  $stmt = $db->prepare($query);
  
  $stmt->bind_param('ii', $currentUser->id, $targetUser->id);
  $stmt->execute();
  $stmt->store_result();
  
  if ($stmt->num_rows > 0) {
    return true;
  }
  
  $stmt->free_result();
  
  return false;
}

function addBlockedUserByUsername($db, $username, $usernameToBlock) {
  $query = 'INSERT INTO users_blocked_users(user_id, blocked_user_id) VALUES (?, ?)';
  
  manageBlockedUserByUsername($db, $username, $usernameToBlock, $query);
  
  $message = "$username has blocked $usernameToBlock. Neither user will be able to send messages until this block has been removed";
  addAdminMessageForBlockStatus($db, $username, $usernameToBlock, $message);
}

function removeBlockedUserByUsername($db, $username, $usernameToUnblock) {
  $query = 'DELETE FROM users_blocked_users WHERE user_id = ? AND blocked_user_id = ?';
  
  manageBlockedUserByUsername($db, $username, $usernameToUnblock, $query);
  
  $message = "$username has unblocked $usernameToUnblock";
  addAdminMessageForBlockStatus($db, $username, $usernameToUnblock, $message);
}

function addAdminMessageForBlockStatus($db, $username, $otherUsername, $message) {
  $username = htmlspecialchars($username);
  $otherUsername = htmlspecialchars($otherUsername);
  
  // show some message to make clear what's happening. Toyed with the idea of making the blocking user invisible to the blocked user but if both users block eachother then it becomes a deadlock
  $currentUser = getUserByUsername($db, $username);
  $otherUser = getUserByUsername($db, $otherUsername);
  $conversation = getConversationByUsers($db, $currentUser, $otherUser);
  
  insertAdminChatMessageToDb($db, $currentUser, $message, $conversation, false);
}

function manageBlockedUserByUsername($db, $username, $usernameToManage, $query) {
  $username = htmlspecialchars($username);
  $usernameToManage = htmlspecialchars($usernameToManage);
  
  $currentUser = getUserByUsername($db, $username);
  $targetUser = getUserByUsername($db, $usernameToManage);
  
  $stmt = $db->prepare($query);
  
  $stmt->bind_param('ii', $currentUser->id, $targetUser->id);
  $stmt->execute();
  if ($stmt->error) {
    throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
  }

  $stmt->free_result();
}

?>