<?php
// Database connection
include_once 'config.php';
$servername = DB_HOST;
$username = DB_USER;
$password = DB_PASSWORD;
$dbname = DB_NAME;

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch events for the selected date using parameterized query
$date = $_GET['date'];
$sql = "SELECT * FROM events WHERE event_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Output events
    echo "<h3>Events for $date:</h3>";
    echo "<ul>";
    while($row = $result->fetch_assoc()) {
        echo "<li>".$row['event_name']."</li>";
    }
    echo "</ul>";
} else {
    echo "No events found for $date";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
