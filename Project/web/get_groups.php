<?php

include_once('db/connection.php');
include_once('db/include.php');
include_once('utils.php');

$db = connectToDb();

$groups = getAllGroups($db);
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