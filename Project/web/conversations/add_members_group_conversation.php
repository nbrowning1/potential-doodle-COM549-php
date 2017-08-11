<?php

include_once('../db/connection.php');
include_once('../db/include.php');

session_start();

$groupName = isset($_POST["groupName"]) ? $_POST["groupName"] : "";
$usernamesToAdd = array();
if (isset($_POST["members"])) {
  $members = $_POST["members"];
  if (is_array($members)) {
    $usernamesToAdd = $members;
  } else {
    array_push($usernamesToAdd, $members);
  }
}

$db = connectToDb();

$currentUser = getUserByUsername($db, $_SESSION['user']);

$usernamesToAdd = removeExistingNamesFromMembers($db, $groupName, $usernamesToAdd);

if (empty($groupName) || count($usernamesToAdd) == 0) {
  $error = errorResponse();
  if (empty($groupName)) {
    $error["groupNameError"] = "Name cannot be empty";
  }
  if (count($usernamesToAdd) == 0) {
    $error["membersError"] = "Must add at least one new group member";
  }
  returnError($error);
}

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

function addMembersToGroupConversation($db, $groupName, $usernamesToAdd, $currentUser) {
  
  $group = getGroupByName($db, $groupName);
  
  $usersToAddToGroup = array();
  foreach ($usernamesToAdd as $usernameToAdd) {
    $userToAdd = getUserByUsername($db, $usernameToAdd);
    array_push($usersToAddToGroup, $userToAdd);
  }
  
  insertGroupUsersForGroup($db, $group->id, $usersToAddToGroup);
  
  // add admin style message to inform members of change
  $usersAsStr = join(", ", $usernamesToAdd);
  $message = "$currentUser->username added $usersAsStr to the conversation";
  
  insertAdminChatMessageToDb($db, $currentUser, $message, $group, true);
}

function errorResponse() {
  return array("error"=>true);
}

function returnError($errorArray) {
  header('Content-type: application/json');
  echo json_encode($errorArray);
  exit;
}

?>