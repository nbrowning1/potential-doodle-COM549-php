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
    echo 'Authentication failed - re-check your username and password.';
    return false;
  }

  $stmt->free_result(); 
  
  return true;
}

function getAllUsers($db) {
  $stmt = $db->prepare('SELECT * FROM users');
  
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($uId, $uUsername, $uPassword, $uHint, $uAnswer, $uRegDate, $uUserType);
  
  $users = array();
  while ($stmt->fetch()) {
    array_push($users, new User($uId, $uUsername, $uPassword, $uHint, $uAnswer, $uRegDate, $uUserType));
  }
  
  return $users;
}

function getUserByUsername($db, $username) {
  $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
  
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($uId, $uUsername, $uPassword, $uHint, $uAnswer, $uRegDate, $uUserType);
  
  $stmt->fetch();
  return new User($uId, $uUsername, $uPassword, $uHint, $uAnswer, $uRegDate, $uUserType);
}

function getUserById($db, $userId) {
  $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
  
  $stmt->bind_param('i', $userId);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($uId, $uUsername, $uPassword, $uHint, $uAnswer, $uRegDate, $uUserType);
  
  $stmt->fetch();
  return new User($uId, $uUsername, $uPassword, $uHint, $uAnswer, $uRegDate, $uUserType);
}

?>