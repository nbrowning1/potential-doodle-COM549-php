<?php
  
require_once('db/connection.php');
require_once('include.php');
require_once('utils.php');

session_start();
$currentUsername = $_SESSION['user'];

$db = connectToDb();

$currentUser = getUserByUsername($db, $currentUsername);

$hasUpdatesBoolean = $currentUser->has_updates == 1 ? true : false;
returnRefreshNeededResponse($hasUpdatesBoolean);

function returnRefreshNeededResponse($refreshNeeded) {
  $response = array("refreshNeeded"=>$refreshNeeded);
  returnJson($response);
}

?>