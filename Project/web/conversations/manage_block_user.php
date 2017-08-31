<?php

require_once('../include.php');

session_start();
$currentUsername = getSessionValue('user');

$targetUsername = getPostValue('username');
$blockStatus = getPostValue('blockStatus');

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