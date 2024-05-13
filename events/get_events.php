<?php
// Database connection
$servername = "localhost";
$username = "events_user";
$password = "Uad0cAm5d008_8_7d8";
$dbname = "events";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch events for the selected date
$date = $_GET['date'];
$sql = "SELECT * FROM events WHERE event_date = '$date'";
$result = $conn->query($sql);

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

// Close connection
$conn->close();
?>
