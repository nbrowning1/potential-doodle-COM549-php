<?php

require_once('../../db/connection.php');
require_once('../../../include.php');
require_once('../../utils.php');

$username = getPostValue('username');
if (empty($username)) {
  $response = errorResponse();
  $response['usernameError'] = "Username cannot be empty";
  returnJson($response);
}

$db = connectToDb();
if (usernameIsAvailable($db, $username)) {
  $response = errorResponse();
  $response['usernameError'] = "Username does not exist";
  returnJson($response);
}

?>