<?php

include_once('../db/connection.php');
include_once('../db/include.php');

session_start();

$targetUsername = isset($_POST["username"]) ? $_POST["username"] : "";
$favouriteStatus = isset($_POST["favouriteStatus"]) ? $_POST["favouriteStatus"] : "";
if (!($favouriteStatus == 'favourite' || $favouriteStatus == 'unfavourite')) {
  $response = errorResponse();
  $response['invalidOption'] = 'Invalid favourite status';
  returnError($response);
}
$shouldFavourite = $favouriteStatus == 'favourite' ? true : false;

$db = connectToDb();

$currentUsername = $_SESSION['user'];

manageTargetUserFavouriteStatus($db, $currentUsername, $targetUsername, $shouldFavourite);

function manageTargetUserFavouriteStatus($db, $currentUsername, $targetUsername, $shouldFavourite) {
  if ($shouldFavourite) {
    addFavouritedUserByUsername($db, $currentUsername, $targetUsername);
  } else {
    removeFavouritedUserByUsername($db, $currentUsername, $targetUsername);
  }
}

function errorResponse() {
  return array("error"=>true);
}

function returnError($errorArray) {
  header('Content-type: application/json');
  echo json_encode($errorArray);
  exit;
}

?>