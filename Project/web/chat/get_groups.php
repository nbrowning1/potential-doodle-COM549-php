<?php

require_once('../include.php');

$db = connectToDb();

session_start();
$currentUser = getUserByUsername($db, getSessionValue('user'));

$groups = getGroupsForUser($db, $currentUser, false);
$groupsToWrite = array();

foreach ($groups as $group) {
  $memberNames = array();
  foreach ($group->members as $member) {
    array_push($memberNames, $member->user->username);
  }

  $groupsToWrite[] = array('groupname'=> $group->name, 'members'=>$memberNames);
}

$successResponse = successResponse();
$successResponse["data"] = $groupsToWrite;
returnJson($successResponse);

?>