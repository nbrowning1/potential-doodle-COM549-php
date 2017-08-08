<?php
  
include_once('db/connection.php');
include_once('db/include.php');
include_once('utils.php');

session_start();
$currentUsername = $_SESSION['user'];

if (!isset($_POST['hasUpdates'])) {
  returnResponse(false);
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
  returnResponse(true);
} catch (Exception $e) {
  returnResponse(false);  
}

function returnResponse($success) {
  $response = array("successfulUpdate"=>$success);
  returnJson($response);
}

?>