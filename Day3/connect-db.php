<?php
  $host = 'localhost';
  $username = 'root';
  $password = '';
  $database = 'bookorama';
  $db = new mysqli($host, $username, $password, $database);
  if (mysqli_connect_errno()) {
  echo "<p>Could not connect to database</p>";
  } else {
  echo "<p>Connected to " . $database . "</p>";
  }
?>