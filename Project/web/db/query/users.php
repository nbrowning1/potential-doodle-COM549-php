<?php

// check if username is unique for registration or available for password recovery
function usernameIsAvailable($db, $username) {
  $query = 'SELECT * FROM users WHERE username = ?';
  $stmt = $db->prepare($query);
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->error) {
    throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
  }

  if ($stmt->num_rows > 0) {
    return false;
  }

  $stmt->free_result();
  
  return true;
}

function insertUserToDb($db, $username, $password, $recoveryQ, $recoveryA) {
  $query = 'INSERT INTO users(username, password, recovery_question, recovery_answer, reg_date, user_type) VALUES (?, sha1(?), ?, ?, now(), 0)';
  $stmt = $db->prepare($query);

  
  $stmt->bind_param('ssss', $username, $password, $recoveryQ, $recoveryA);
  $stmt->execute();
  if ($stmt->error) {
    throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
  }

  $stmt->free_result();
}

function successfulLogin($db, $username, $password) {
  $stmt = $db->prepare('SELECT * FROM users WHERE Username = ? AND Password = sha1(?)');

  $stmt->bind_param('ss', $username, $password);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->error) {
    throw new RuntimeException('Unexpected error occurred: ' . $stmt->error);
  }

  if ($stmt->num_rows == 0) {
    return false;
  }

  $stmt->free_result(); 
  
  return true;
}

function updateUserPassword($db, $username, $newPassword) {
  $stmt = $db->prepare('UPDATE users SET password = sha1(?) WHERE username = ?');
  
  $stmt->bind_param('ss', $newPassword, $username);
  $stmt->execute();
}

// gets the username as defined when they created the account, as login is case-insensitive
function getUsernameProperCase($db, $usernameAnyCase) {
  $stmt = $db->prepare('SELECT username FROM users WHERE username = ?');
  
  $stmt->bind_param('s', $usernameAnyCase);
  $stmt->execute();
  $stmt->bind_result($usernameProperCase);
  
  $stmt->fetch();
  
  return $usernameProperCase;
}

function getAllUsers($db) {
  $stmt = $db->prepare('SELECT * FROM users');
  
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($uId, $uUsername, $uPassword, $uRecoveryQ, $uRecoveryA, $uRegDate, $uUserType, $uHasUpdates);
  
  $users = array();
  while ($stmt->fetch()) {
    array_push($users, new User($uId, $uUsername, $uPassword, $uRecoveryQ, $uRecoveryA, $uRegDate, $uUserType, $uHasUpdates));
  }
  
  return $users;
}

// perhaps this should replace some usages of getAllUsers()
function getAllUsersForSearch($db, $currentUser) {
  // exclude current user
  $query = "SELECT  *
            FROM    users
            WHERE 
            NOT     (id = ?)";
  $stmt = $db->prepare($query);
  
  $stmt->bind_param('i', $currentUser->id);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($uId, $uUsername, $uPassword, $uRecoveryQ, $uRecoveryA, $uRegDate, $uUserType, $uHasUpdates);
  
  $users = array();
  while ($stmt->fetch()) {
    array_push($users, new User($uId, $uUsername, $uPassword, $uRecoveryQ, $uRecoveryA, $uRegDate, $uUserType, $uHasUpdates));
  }
  
  return $users;
}

function getUserByUsername($db, $username) {
  $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
  
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($uId, $uUsername, $uPassword, $uRecoveryQ, $uRecoveryA, $uRegDate, $uUserType, $uHasUpdates);
  
  $stmt->fetch();
  return new User($uId, $uUsername, $uPassword, $uRecoveryQ, $uRecoveryA, $uRegDate, $uUserType, $uHasUpdates);
}

function getUserById($db, $userId) {
  $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
  
  $stmt->bind_param('i', $userId);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($uId, $uUsername, $uPassword, $uRecoveryQ, $uRecoveryA, $uRegDate, $uUserType, $uHasUpdates);
  
  $stmt->fetch();
  return new User($uId, $uUsername, $uPassword, $uRecoveryQ, $uRecoveryA, $uRegDate, $uUserType, $uHasUpdates);
}

function setUserHasUpdatesByUsername($db, $username, $value) {
  $updateValue = $value ? 1 : 0;
  $stmt = $db->prepare('UPDATE users SET has_updates = ? WHERE username = ?');
  
  $stmt->bind_param('is', $updateValue, $username);
  $stmt->execute();
}

function setUserHasUpdatesById($db, $id, $value) {
  $updateValue = $value ? 1 : 0;
  $stmt = $db->prepare('UPDATE users SET has_updates = ? WHERE id = ?');
  
  $stmt->bind_param('ii', $updateValue, $id);
  $stmt->execute();
}

?>