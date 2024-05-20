<?php

require_once('db_connect.php');

function get_data($id = null) {
  $conn = connect_to_db();

  $sql = "SELECT r.*, e.event_name FROM results r INNER JOIN events e ON r.RaceID = e.id WHERE r.RaceID = ?";
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
    // Decode Unicode escape sequences
    $row['FirstName'] = json_decode('"' . $row['FirstName'] . '"');
    $row['LastName'] = json_decode('"' . $row['LastName'] . '"');
    // Add decoded row to data array
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
    http_response_code(404);
    echo json_encode(['error' => 'race not found']);
  } else {
    http_response_code(200);
    // Set UTF-8 encoding for JSON output
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
  }
} else {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
}
?>
