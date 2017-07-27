<?php

function connectToDb() {
  $host = 'localhost';
  $username = 'root';
  $password = 'root';
  $database = 'project';
  $db = new mysqli($host, $username, $password, $database);
  if (mysqli_connect_errno()) {
    throw new RuntimeException('Could not connect to the database');
  }
  
  return $db;
}

?>