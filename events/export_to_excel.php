<?php
// Check if the form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if eventId is set in the POST data
    if (isset($_POST["eventId"])) {
        // Retrieve the selected event ID from the POST data
        $eventId = $_POST["eventId"];

        // Connect to the database
        $servername = "localhost";
        $username = "events_user";
        $password = "Uad0cAm5d008_8_7d8";
        $dbname = "events";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare and execute SQL statement to retrieve registered users for the selected event
        $stmt = $conn->prepare("SELECT registrations.*, categories.category_name, categories.category_cost FROM registrations INNER JOIN categories ON registrations.category_id = categories.id WHERE registrations.event_id = ?");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if any users are registered for the selected event
        if ($result->num_rows > 0) {
            // Define file name for the exported Excel file
            $filename = "registered_users_event_" . $eventId . ".csv";

            // Set appropriate headers for CSV file download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            // Open file handle for writing
            $file = fopen('php://output', 'w');

            // Write CSV header row
            fputcsv($file, array('ID', 'First Name', 'Last Name', 'Date of Birth', 'Sex', 'Team', 'Phone Number', 'City', 'Safety Number', 'T-Shirt Size', 'Email', 'Registration Date', 'Category', 'Category Cost'));

            // Fetch and write each row of data to the CSV file
            while ($row = $result->fetch_assoc()) {
                fputcsv($file, $row);
            }

            // Close the file handle
            fclose($file);
        } else {
            // If no users are registered for the selected event, display a message
            echo "No users registered for this event.";
        }

        // Close the statement and database connection
        $stmt->close();
        $conn->close();
    } else {
        // If eventId is not set in the POST data, display an error message
        echo "Error: Event ID not provided.";
    }
} else {
    // If the form is not submitted via POST method, display an error message
    echo "Error: Form submission method not allowed.";
}
?>
