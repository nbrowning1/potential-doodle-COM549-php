<?php

require_once('../include.php');

session_start();
$currentUsername = $_SESSION['user'];

$groupName = getPostValue('groupName');
$usernamesToAdd = getPostValueArray('members');

$db = connectToDb();
$currentUser = getUserByUsername($db, $currentUsername);
$usernamesToAdd = removeExistingNamesFromMembers($db, $groupName, $usernamesToAdd);
validateData($groupName, $usernamesToAdd);
addMembersToGroupConversation($db, $groupName, $usernamesToAdd, $currentUser);

function removeExistingNamesFromMembers($db, $groupName, $usernamesToAdd) {
  $group = getGroupByName($db, $groupName);
  
  // get existing group member names
  $existingNames = array();
  foreach ($group->members as $member) {
    array_push($existingNames, $member->user->username);
  }
  
  // build up new usernames not already in group
  $newUsernamesToAdd = array();
  foreach ($usernamesToAdd as $usernameToAdd) {
    if (!in_array($usernameToAdd, $existingNames)) {
        array_push($newUsernamesToAdd, $usernameToAdd);
    }
  }
  
  return $newUsernamesToAdd;
}

function validateData($groupName, $usernamesToAdd) {
  if (empty($groupName) || count($usernamesToAdd) == 0) {
    $response = errorResponse();
    if (empty($groupName)) {
      $response['groupNameError'] = 'Name cannot be empty';
    }
    if (count($usernamesToAdd) == 0) {
      $response['membersError'] = 'Must add at least one new group member';
    }
    returnJson($response);
  }
}

function addMembersToGroupConversation($db, $groupName, $usernamesToAdd, $currentUser) {
  $group = getGroupByName($db, $groupName);
  
  $usersToAddToGroup = array();
  foreach ($usernamesToAdd as $usernameToAdd) {
    $userToAdd = getUserByUsername($db, $usernameToAdd);
    array_push($usersToAddToGroup, $userToAdd);
  }
  
  insertGroupUsersForGroup($db, $group->id, $usersToAddToGroup);
  
  // add admin style message to inform members of change to group
  $usersAsStr = join(', ', $usernamesToAdd);
  $message = "$currentUser->username added $usersAsStr to the conversation";
  insertAdminChatMessageToDb($db, $currentUser, $message, $group, true);
}

?>