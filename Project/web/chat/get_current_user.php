<?php

require_once('../include.php');

session_start();

if ($_SESSION['user']) {
  $userResponse = array("user"=>getSessionValue('user'));
  returnJson($userResponse);
}

?>