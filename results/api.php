<?php

require_once('db_connect.php');

function get_all_events_with_results() {
    $conn = connect_to_db();

    $sql = "SELECT * FROM events";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo "Error: " . mysqli_error($conn);
        exit;
    }

    $events = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $event_id = $row['id'];
        $categories = get_categories_for_event($conn, $event_id);
        $row['categories'] = [];

        foreach ($categories as $category) {
            $results = get_results_for_event_and_category($conn, $event_id, $category);
            $row['categories'][$category] = $results;
        }

        $events[] = $row;
    }

    mysqli_close($conn);

    return $events;
}

function get_categories_for_event($conn, $event_id) {
    $sql = "SELECT DISTINCT RaceCategory FROM results WHERE RaceID = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        echo "Error: " . mysqli_error($conn);
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'i', $event_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row['RaceCategory'];
    }

    mysqli_stmt_close($stmt);

    return $categories;
}

function get_results_for_event_and_category($conn, $event_id, $category) {
    $sql = "SELECT * FROM results WHERE RaceID = ? AND RaceCategory = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        echo "Error: " . mysqli_error($conn);
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'is', $event_id, $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $results = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $results[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $results;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $all_events = get_all_events_with_results();

    http_response_code(200);
    // Set UTF-8 encoding for JSON output
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['events' => $all_events]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
