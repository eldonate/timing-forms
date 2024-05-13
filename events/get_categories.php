<?php
// Check if the event ID is set in the query string
if (isset($_GET["eventId"])) {
    // Database credentials
    $servername = "localhost";
    $username = "events_user";
    $password = "Uad0cAm5d008_8_7d8";
    $dbname = "events";

    // Connect to the database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to retrieve categories for the selected event
    $eventId = $_GET["eventId"];
    $sql = "SELECT category_name, category_cost FROM categories WHERE event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventId);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch categories and store them in an array
    $categories = array();
    while ($row = $result->fetch_assoc()) {
        $categories[] = array(
            "category_name" => $row["category_name"],
            "category_cost" => $row["category_cost"]
        );
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Output categories as JSON
    header('Content-Type: application/json');
    echo json_encode($categories);
} else {
    // If the event ID is not set, return an error response
    http_response_code(400);
    echo "Error: Event ID is not set.";
}
?>
