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
}

function removeBlockedUserByUsername($db, $username, $usernameToUnblock) {
  $query = 'DELETE FROM users_blocked_users WHERE user_id = ? AND blocked_user_id = ?';
  
  manageBlockedUserByUsername($db, $username, $usernameToUnblock, $query);
}

function manageBlockedUserByUsername($db, $username, $usernameToManage, $query) {
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