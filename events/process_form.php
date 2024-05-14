<?php
// DB credentials
include_once 'config.php';
$servername = DB_HOST;
$username = DB_USER;
$password = DB_PASSWORD;
$dbname = DB_NAME;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize and validate form data
function sanitizeInput($input) {
    // Remove leading and trailing whitespace
    $input = trim($input);
    // Convert special characters to HTML entities
    $input = htmlspecialchars($input, ENT_QUOTES);
    return $input;
}

// Retrieve and sanitize form data
$eventName = sanitizeInput($_POST['eventName']);
$eventLocation = sanitizeInput($_POST['eventLocation']);
$eventDate = $_POST['eventDate']; // Assuming date format is validated on the client-side
$eventType = sanitizeInput($_POST['eventType']);
$description = sanitizeInput($_POST['description']);
$eventWebsite = filter_var($_POST['eventWebsite'], FILTER_SANITIZE_URL);
$categories = $_POST['categoryName']; // Assuming category names are validated on the client-side
$costs = $_POST['categoryCost']; // Assuming costs are validated on the client-side

// Prepare and bind SQL statement for event insertion
$stmt = $conn->prepare("INSERT INTO events (event_name, event_location, event_date, event_type, description, event_website) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $eventName, $eventLocation, $eventDate, $eventType, $description, $eventWebsite);

// Execute event insertion
$stmt->execute();

// Get the event ID
$eventId = $conn->insert_id;

// Prepare and bind SQL statement for category insertion
$stmt2 = $conn->prepare("INSERT INTO categories (event_id, category_name, category_cost) VALUES (?, ?, ?)");

// Bind parameters
$stmt2->bind_param("iss", $eventId, $categoryName, $categoryCost);

// Insert each category
for ($i = 0; $i < count($categories); $i++) {
    // Retrieve category data and sanitize
    $categoryName = sanitizeInput($categories[$i]);
    $categoryCost = sanitizeInput($costs[$i]);

    // Execute category insertion
    $stmt2->execute();
}

echo "Event added successfully.";

// Close statements and connection
$stmt->close();
$stmt2->close();
$conn->close();
?>
