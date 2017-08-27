<?php

require_once('../include.php');

session_start();
$currentUsername = $_SESSION['user'];

$groupName = getPostValue('groupName');
$usernamesToAdd = getPostValueArray('members');
$usernamesToAdd = addCurrentUserToMembers($currentUsername, $usernamesToAdd);

validateData($groupName, $usernamesToAdd);
createNewGroupConversation($groupName, $usernamesToAdd);

function addCurrentUserToMembers($currentUsername, $usernamesToAdd) {
  if (!in_array($currentUsername, $usernamesToAdd)) {
    array_push($usernamesToAdd, $currentUsername);
  }
  return $usernamesToAdd;
}

function validateData($groupName, $usernamesToAdd) {
  // < 2 check because usernames to add already includes the current user
  if (empty($groupName) || count($usernamesToAdd) < 2) {
    $response = errorResponse();
    if (empty($groupName)) {
      $response['groupNameError'] = 'Name cannot be empty';
    }
    if (count($usernamesToAdd) < 2) {
      $response['membersError'] = 'Must have at least one group member (aside from yourself)';
    }
    returnJson($response);
  }
}

function createNewGroupConversation($groupName, $usernamesToAdd) {
  $db = connectToDb();
  
  if (!groupNameIsAvailable($db, $groupName)) {
    returnErrorResponse('groupNameError', 'A group already exists with this name');
  }
  
  $usersToAddToGroup = array();
  foreach ($usernamesToAdd as $usernameToAdd) {
    $userToAdd = getUserByUsername($db, $usernameToAdd);
    array_push($usersToAddToGroup, $userToAdd);
  }
  
  insertGroupConversationToDb($db, $groupName, $usersToAddToGroup);
}

?>