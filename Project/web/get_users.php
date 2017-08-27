<?php

include_once('db/connection.php');
include_once('db/include.php');
include_once('utils.php');

$db = connectToDb();

session_start();
$currentUser = getUserByUsername($db, $_SESSION['user']);

$users = getAllUsersForSearch($db, $currentUser);
$usersToWrite = array();

foreach ($users as $user) {
  $usersToWrite[] = array('username'=> $user->username);
} 

$successResponse = successResponse();
$successResponse["data"] = $usersToWrite;
returnJson($successResponse);

?>