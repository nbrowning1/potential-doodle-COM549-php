<?php

include_once('../db/connection.php');
include_once('../db/include.php');

session_start();

$groupOwnerUsername = $_SESSION['user'];

$groupName = isset($_POST["groupName"]) ? $_POST["groupName"] : "";
$usernamesToAdd = array();
if (isset($_POST["members"])) {
  array_push($usernamesToAdd, $_POST["members"]);
}

$usernamesToAdd = removeGroupOwnerNameFromMembers($groupOwnerUsername, $usernamesToAdd);

if (empty($groupName) || empty($usernamesToAdd)) {
  $error = errorResponse();
  if (empty($groupName)) {
    $error["groupNameError"] = "Name cannot be empty";
  }
  if (empty($usernamesToAdd)) {
    $error["membersError"] = "Must have at least one group member (aside from yourself)";
  }
  returnError($error);
}

createNewGroupConversation($groupOwnerUsername, $groupName, $usernamesToAdd);

function removeGroupOwnerNameFromMembers($groupOwnerUsername, $usernamesToAdd) {
  $ownerNameIndex = -1;
  for ($i = 0; $i < count($usernamesToAdd); $i++) {
    if ($usernamesToAdd[$i] == $groupOwnerUsername) {
      $ownerNameIndex = $i;
      break;
    }
  }
  // if owner name found in members to add, remove
  if ($ownerNameIndex != -1) {
    array_splice($usernamesToAdd, $ownerNameIndex, 1);
  }
  
  return $usernamesToAdd;
}

function createNewGroupConversation($groupOwnerUsername, $groupName, $usernamesToAddVar) {
  $db = connectToDb();
  
  // needs to be done because the post variable comes in as a single-element array, where the element contains the array passed from the ajax - so if it matches this criteria, set array to this unnecessarily-nested array - php ????
  $usernamesToAdd;
  if (is_array($usernamesToAddVar) && (count($usernamesToAddVar) == 1) && is_array($usernamesToAddVar[0])) {
    $usernamesToAdd = $usernamesToAddVar[0];
  } else {
    $usernamesToAdd = $usernamesToAddVar;
  }
  
  $ownerUser = getUserByUsername($db, $groupOwnerUsername);
  $usersToAddToGroup = array();
  foreach ($usernamesToAdd as $usernameToAdd) {
    // group owner shouldn't be part of members - redundant
    if ($usernameToAdd == $groupOwnerUsername) {
      continue;
    }
    
    $userToAdd = getUserByUsername($db, $usernameToAdd);
    array_push($usersToAddToGroup, $userToAdd);
  }
  
  insertGroupConversationToDb($db, $groupName, $ownerUser, $usersToAddToGroup);
  
  updateGroupsCache($db);
}

function updateGroupsCache($db) {
  $groups = getAllGroups($db);
  
  $groupsToWrite = array();

  foreach ($groups as $group) {
    $memberNames = array();
    foreach ($group->members as $member) {
      array_push($memberNames, $member->user->username);
    }
    
    $groupsToWrite[] = array('groupname'=> $group->name, 'members'=>$memberNames);
  }

  $fp = fopen('../../cache/groups.json', 'w');
  fwrite($fp, json_encode($groupsToWrite));
  fclose($fp);
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