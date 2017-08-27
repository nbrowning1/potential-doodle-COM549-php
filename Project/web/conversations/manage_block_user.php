<?php

require_once('../include.php');

session_start();
$currentUsername = $_SESSION['user'];

$targetUsername = getPostValue('username');
$blockStatus = getPostValue('blockStatus');
// TODO: change to boolean from AJAX
if (!($blockStatus == 'block' || $blockStatus == 'unblock')) {
  returnErrorResponse('invalidOption', 'Invalid block status');
}
$shouldBlock = $blockStatus == 'block' ? true : false;

$db = connectToDb();
manageTargetUserBlockStatus($db, $currentUsername, $targetUsername, $shouldBlock);

function manageTargetUserBlockStatus($db, $currentUsername, $targetUsername, $shouldBlock) {
  if ($shouldBlock) {
    addBlockedUserByUsername($db, $currentUsername, $targetUsername);
  } else {
    removeBlockedUserByUsername($db, $currentUsername, $targetUsername);
  }
}

?>