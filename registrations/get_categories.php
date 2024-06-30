<?php
// Check if the event ID is set in the query string and it's a valid integer
if (isset($_GET["eventId"]) && ctype_digit($_GET["eventId"])) {
    // Database credentials
    include_once 'config.php';
    $servername = DB_HOST;
    $username = DB_USER;
    $password = DB_PASSWORD;
    $dbname = DB_NAME;

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
            "category_name" => htmlspecialchars($row["category_name"]), // Sanitize output
            "category_cost" => htmlspecialchars($row["category_cost"]) // Sanitize output
        );
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Output categories as JSON
    header('Content-Type: application/json');
    echo json_encode($categories);
} else {
    // If the event ID is not set or not a valid integer, return an error response
    http_response_code(400);
    echo "Error: Event ID is not valid.";
}
?>
