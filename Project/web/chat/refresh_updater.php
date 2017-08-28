<?php

require_once('../include.php');

session_start();
$currentUsername = $_SESSION['user'];

if (!isset($_POST['hasUpdates'])) {
  returnUpdateResponse(false);
}
$hasUpdates = getPostValueBoolean('hasUpdates');

$db = connectToDb();

try {
  setUserHasUpdatesByUsername($db, $currentUsername, $hasUpdates);
  returnUpdateResponse(true);
} catch (Exception $e) {
  returnUpdateResponse(false);  
}

function returnUpdateResponse($success) {
  $response = array("successfulUpdate"=>$success);
  returnJson($response);
}

?>