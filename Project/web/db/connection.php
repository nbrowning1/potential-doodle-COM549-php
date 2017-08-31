<?php

function connectToDb() {
  $host = 'localhost';
  $username = 'B00652112';
  $password = 'pleaseleavealone1';
  $database = 'b00652112';
  $db = new mysqli($host, $username, $password, $database);
  if (mysqli_connect_errno()) {
    throw new RuntimeException('Could not connect to the database');
  }
  
  return $db;
}

?>