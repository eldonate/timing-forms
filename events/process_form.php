<?php
// DB credentials
include_once 'config.php';
$servername = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname =  getenv('DB_NAME');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$eventName = $_POST['eventName'];
$eventLocation = $_POST['eventLocation'];
$eventDate = $_POST['eventDate'];
$eventType = $_POST['eventType'];
$description = $_POST['description'];
$eventWebsite = $_POST['eventWebsite'];
$categories = $_POST['categoryName'];
$costs = $_POST['categoryCost'];

// Prepare and bind SQL statement for event insertion
$stmt = $conn->prepare("INSERT INTO events (event_name, event_location, event_date, event_type, description, event_website) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $eventName, $eventLocation, $eventDate, $eventType, $description, $eventWebsite);

// Execute event insertion
$stmt->execute();

// Get the event ID
$eventId = $conn->insert_id;

// Prepare and bind SQL statement for category insertion
$stmt2 = $conn->prepare("INSERT INTO categories (event_id, category_name, category_cost) VALUES (?, ?, ?)");
$stmt2->bind_param("iss", $eventId, $categoryName, $categoryCost);

// Insert each category
for ($i = 0; $i < count($categories); $i++) {
    $categoryName = $categories[$i];
    $categoryCost = $costs[$i];
    $stmt2->execute();
}

echo "Event added successfully.";

// Close statements and connection
$stmt->close();
$stmt2->close();
$conn->close();
?>
