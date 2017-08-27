<?php

include_once('db/connection.php');
include_once('db/include.php');
include_once('utils.php');

$db = connectToDb();

session_start();
$currentUser = getUserByUsername($db, $_SESSION['user']);

$groups = getAllGroupsForSearch($db, $currentUser);
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