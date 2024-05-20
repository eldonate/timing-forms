<?php

$host = 'localhost';
$dbname = 'testapi';
$username = 'testapi';
$password = '2iu25?Xn2'; // **Keep this confidential!**

function connect_to_db() {
  global $host, $dbname, $username, $password;

  $conn = mysqli_connect($host, $username, $password, $dbname);

  if (!$conn) {
    echo "Error: Could not connect to database. " . mysqli_connect_error();
    exit;
  }

  return $conn;
}
