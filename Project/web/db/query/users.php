<?php

// check if username is unique for registration
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

function insertUserToDb($db, $username, $password, $hint, $answer) {
  $query = 'INSERT INTO users(username, password, hint, answer, reg_date, user_type) VALUES (?, sha1(?), ?, ?, now(), 0)';
  $stmt = $db->prepare($query);

  
  $stmt->bind_param('ssss', $username, $password, $hint, $answer);
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
  $stmt->bind_result($uId, $uUsername, $uPassword, $uHint, $uAnswer, $uRegDate, $uUserType, $uHasUpdates);
  
  $users = array();
  while ($stmt->fetch()) {
    array_push($users, new User($uId, $uUsername, $uPassword, $uHint, $uAnswer, $uRegDate, $uUserType, $uHasUpdates));
  }
  
  return $users;
}

function getUserByUsername($db, $username) {
  $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
  
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($uId, $uUsername, $uPassword, $uHint, $uAnswer, $uRegDate, $uUserType, $uHasUpdates);
  
  $stmt->fetch();
  return new User($uId, $uUsername, $uPassword, $uHint, $uAnswer, $uRegDate, $uUserType, $uHasUpdates);
}

function getUserById($db, $userId) {
  $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
  
  $stmt->bind_param('i', $userId);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($uId, $uUsername, $uPassword, $uHint, $uAnswer, $uRegDate, $uUserType, $uHasUpdates);
  
  $stmt->fetch();
  return new User($uId, $uUsername, $uPassword, $uHint, $uAnswer, $uRegDate, $uUserType, $uHasUpdates);
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