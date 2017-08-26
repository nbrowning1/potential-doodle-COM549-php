<?php

include_once('../db/connection.php');
include_once('../db/include.php');

session_start();

$groupCreatorUsername = $_SESSION['user'];

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

$usernamesToAdd = addGroupCreatorNameToMembers($groupCreatorUsername, $usernamesToAdd);

if (empty($groupName) || count($usernamesToAdd) < 2) {
  $error = errorResponse();
  if (empty($groupName)) {
    $error["groupNameError"] = "Name cannot be empty";
  }
  if (count($usernamesToAdd) < 2) {
    $error["membersError"] = "Must have at least one group member (aside from yourself)";
  }
  returnError($error);
}

createNewGroupConversation($groupName, $usernamesToAdd);

function addGroupCreatorNameToMembers($groupCreatorUsername, $usernamesToAdd) {
  if (!in_array($groupCreatorUsername, $usernamesToAdd)) {
    array_push($usernamesToAdd, $groupCreatorUsername);
  }
  return $usernamesToAdd;
}

function createNewGroupConversation($groupName, $usernamesToAdd) {
  $db = connectToDb();
  
  if (!groupNameIsAvailable($db, $groupName)) {
    $error = errorResponse();
    $error["groupNameError"] = "A group already exists with this name";
    returnError($error);
  }
  
  $usersToAddToGroup = array();
  foreach ($usernamesToAdd as $usernameToAdd) {
    $userToAdd = getUserByUsername($db, $usernameToAdd);
    array_push($usersToAddToGroup, $userToAdd);
  }
  
  insertGroupConversationToDb($db, $groupName, $usersToAddToGroup);
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