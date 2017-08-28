<?php

require_once('../include.php');

session_start();
$currentUsername = $_SESSION['user'];

if (!isset($_POST['hasUpdates'])) {
  returnUpdateResponse(false);
}
$hasUpdatesStr = $_POST['hasUpdates'];

// TODO: DRY
// needed because ajax calls pass variable values to PHP as a string... https://stackoverflow.com/questions/7408976/bool-parameter-from-jquery-ajax-received-as-literal-string-false-true-in-php
$hasUpdates;
if ($hasUpdatesStr === 'true') {
  $hasUpdates = true;
} else if ($hasUpdatesStr === 'false') {
  $hasUpdates = false;
} else {
  // must already be boolean - assign directly
  $hasUpdates = $hasUpdatesStr;
}

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