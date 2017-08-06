<?php

include_once('../db/connection.php');
include_once('../db/include.php');

session_start();

$oldName = isset($_POST["oldName"]) ? $_POST["oldName"] : "";
$newName = isset($_POST["newName"]) ? $_POST["newName"] : "";

// shouldn't need validation for old name because it's not form input - maybe put it for safety but cba rn
if (empty($newName)) {
  $error = errorResponse();
  if (empty($newName)) {
    $error["groupNameError"] = "New name cannot be empty";
  }
  returnError($error);
}

renameGroupConversation($oldName, $newName);

function renameGroupConversation($oldName, $newName) {
  $db = connectToDb();
  
  updateGroupName($db, $oldName, $newName);
  
  updateGroupsCache($db);
}

// TODO: DRY - also used in create_group_conversation
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

// TODO: DRY
function returnError($errorArray) {
  header('Content-type: application/json');
  echo json_encode($errorArray);
  exit;
}

?>