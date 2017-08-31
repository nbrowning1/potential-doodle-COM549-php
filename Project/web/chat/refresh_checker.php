<?php

require_once('../include.php');

session_start();
$currentUsername = getSessionValue('user');

$db = connectToDb();

$currentUser = getUserByUsername($db, $currentUsername);

$hasUpdatesBoolean = $currentUser->has_updates == 1 ? true : false;
returnRefreshNeededResponse($hasUpdatesBoolean);

function returnRefreshNeededResponse($refreshNeeded) {
  $response = array("refreshNeeded"=>$refreshNeeded);
  returnJson($response);
}

?>