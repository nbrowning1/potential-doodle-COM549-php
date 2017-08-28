<?php

require_once('../../include.php');

$username = getPostValue('username');
if (empty($username)) {
  returnErrorResponse('usernameError', 'Username cannot be empty');
}

$db = connectToDb();
if (usernameIsAvailable($db, $username)) {
  returnErrorResponse('usernameError', 'Username does not exist');
}

returnSuccessResponse();

?>