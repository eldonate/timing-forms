<?php

require_once('db_connect.php'); 

function get_data($id = null) {
  $conn = connect_to_db();

  $sql = "SELECT * FROM users WHERE id = ?";
  $stmt = mysqli_prepare($conn, $sql);

  if (!$stmt) {
    echo "Error: Could not prepare statement. " . mysqli_error($conn);
    exit;
  }

  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);

  $data = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
  }

  mysqli_stmt_close($stmt);
  mysqli_close($conn);

  return $data;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

  if ($id === null) {
    http_response_code(400);
    echo json_encode(['error' => 'ID parameter is required']);
    exit;
  }

  $data = get_data($id);

  if (empty($data)) {
    // not found code (4004) code setup
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
  } else {
    // success code setup (200)
    http_response_code(200);
    echo json_encode($data);
  }
} else {
  //  unsupported method cide setup
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
}
