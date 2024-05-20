<?php

$host = 'localhost';
$dbname = 'dbname';
$username = 'dbusername';
$password = 'password';

function connect_to_db() {
  global $host, $dbname, $username, $password;

  $conn = mysqli_connect($host, $username, $password, $dbname);

  if (!$conn) {
    echo "Error: Could not connect to database. " . mysqli_connect_error();
    exit;
  }

  return $conn;
}
