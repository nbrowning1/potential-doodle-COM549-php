<?php

require_once('../include.php');

session_start();
$currentUsername = $_SESSION['user'];

$targetUsername = getPostValue('username');
$favouriteStatus = getPostValue('favouriteStatus');
// TODO: change to boolean from AJAX
if (!($favouriteStatus == 'favourite' || $favouriteStatus == 'unfavourite')) {
  returnErrorResponse('invalidOption', 'Invalid favourite status');
}
$shouldFavourite = $favouriteStatus == 'favourite' ? true : false;

$db = connectToDb();
manageTargetUserFavouriteStatus($db, $currentUsername, $targetUsername, $shouldFavourite);

function manageTargetUserFavouriteStatus($db, $currentUsername, $targetUsername, $shouldFavourite) {
  if ($shouldFavourite) {
    addFavouritedUserByUsername($db, $currentUsername, $targetUsername);
  } else {
    removeFavouritedUserByUsername($db, $currentUsername, $targetUsername);
  }
}

?>