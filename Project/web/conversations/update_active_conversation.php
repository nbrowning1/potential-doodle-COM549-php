<?php

require_once('../include.php');

session_start();
$newActive = getPostValue('newActive');

// make sure it's an existing user/group first and get correct case to set active (if user)
$db = connectToDb();
if (!usernameIsAvailable($db, $newActive)) {
  $_SESSION['active'] = getUsernameProperCase($db, $newActive);
} else if (!groupNameIsAvailable($db, $newActive)) {
  $_SESSION['active'] = $newActive;
}

?>