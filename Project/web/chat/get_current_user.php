<?php

require_once('../include.php');

session_start();

if ($_SESSION['user']) {
  $userResponse = array("user"=>$_SESSION['user']);
  returnJson($userResponse);
}

?>