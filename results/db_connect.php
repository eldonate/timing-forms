<?php
include_once 'api_config.php';
$servername = DB_HOST;
$username = DB_USER;
$password = DB_PASSWORD;
$dbname = DB_NAME;

function connect_to_db() {
  global $host, $dbname, $username, $password;

  $conn = mysqli_connect($host, $username, $password, $dbname);

  if (!$conn) {
    echo "Error: Could not connect to database. " . mysqli_connect_error();
    exit;
  }

  return $conn;
}
