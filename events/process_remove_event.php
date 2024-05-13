<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if eventId is set and not empty
    if (isset($_POST["eventId"]) && !empty($_POST["eventId"])) {
        // DB credentials
        $servername = "localhost";
        $username = "events_user";
        $password = "Uad0cAm5d008_8_7d8";
        $dbname = "events";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare SQL statement to delete associated categories first
        $eventId = $_POST["eventId"];
        $sqlDeleteCategories = "DELETE FROM categories WHERE event_id = ?";

        // Prepare and bind parameterized statement for deleting categories
        $stmtDeleteCategories = $conn->prepare($sqlDeleteCategories);
        $stmtDeleteCategories->bind_param("i", $eventId);

        // Execute the statement to delete associated categories
        if ($stmtDeleteCategories->execute() === TRUE) {
            // Prepare SQL statement to delete the event
            $sqlDeleteEvent = "DELETE FROM events WHERE id = ?";

            // Prepare and bind parameterized statement for deleting event
            $stmtDeleteEvent = $conn->prepare($sqlDeleteEvent);
            $stmtDeleteEvent->bind_param("i", $eventId);

            // Execute the statement to delete the event
            if ($stmtDeleteEvent->execute() === TRUE) {
                echo "Event removed successfully.";
            } else {
                echo "Error removing event: " . $conn->error;
            }

            // Close statement for deleting event
            $stmtDeleteEvent->close();
        } else {
            echo "Error removing associated categories: " . $conn->error;
        }

        // Close statement for deleting categories and connection
        $stmtDeleteCategories->close();
        $conn->close();
    } else {
        echo "Please select an event to remove.";
    }
} else {
    // If the form is not submitted, redirect to index.php
    header("Location: index.php");
    exit();
}
?>
