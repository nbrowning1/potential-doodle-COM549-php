<?php

session_start();
$newActive = isset($_POST['newActive']) ? $_POST['newActive'] : '';
$_SESSION['active'] = $newActive;

?>