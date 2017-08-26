<?php

function isUserFavouritedForUser($db, $currentUser, $targetUser) {
  $query = 'SELECT * FROM users_favourite_users WHERE user_id = ? AND favourite_user_id = ?';
  
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

function addFavouritedUserByUsername($db, $username, $usernameToFavourite) {
  $query = 'INSERT INTO users_favourite_users(user_id, favourite_user_id) VALUES (?, ?)';
  
  manageFavouritedUserByUsername($db, $username, $usernameToFavourite, $query);
}

function removeFavouritedUserByUsername($db, $username, $usernameToUnfavourite) {
  $query = 'DELETE FROM users_favourite_users WHERE user_id = ? AND favourite_user_id = ?';
  
  manageFavouritedUserByUsername($db, $username, $usernameToUnfavourite, $query);
}

function manageFavouritedUserByUsername($db, $username, $usernameToManage, $query) {
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