<?php
// Database connection parameters
include_once 'results_config.php';
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

// Function to retrieve events from database
function getEvents($conn) {
    $events = array();
    $sql = "SELECT id, event_name FROM events";
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $events[$row['id']] = $row['event_name'];
        }
    } else {
        echo "Error retrieving events: " . $conn->error;
    }
    return $events;
}

// Function to parse CSV file and insert data into database
function insertDataFromCSV($filename, $conn, $raceID, $splitNumber, $position) {
    // Open CSV file for reading
    if (($handle = fopen($filename, "r")) !== FALSE) {
        // Skip the header line
        fgetcsv($handle);

        // Read data from CSV and insert into database
        while (($data = fgetcsv($handle)) !== FALSE) {
            // Prepare SQL statement to insert data
            $sql = "INSERT INTO splits (race_id, split_number, position, rfid, split_time)
                    VALUES (?, ?, ?, ?, ?)";

            // Prepare and bind parameters
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("iidsd", $raceID, $splitNumber, $position, $rfid, $splitTime);

                // Set parameters
                list($participantID, $firstName, $lastName, $rfid, $birthdate, $age, $finishTime, $gender, $raceCategory) = $data;
                $splitTime = $finishTime; // Assuming Finish Time is used for Split Time

                // Execute SQL statement
                if (!$stmt->execute()) {
                    echo "Error inserting data: " . $stmt->error . "<br>";
                } else {
                    echo "Data inserted successfully.<br>";
                }
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error . "<br>";
            }
        }

        // Close the file
        fclose($handle);
    } else {
        echo "Error opening file: " . htmlspecialchars($filename) . "<br>";
    }
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filename = isset($_POST['filename']) ? trim($_POST['filename']) : '';
    $raceID = isset($_POST['raceID']) ? intval($_POST['raceID']) : 0;
    $splitNumber = isset($_POST['splitNumber']) ? intval($_POST['splitNumber']) : 0;
    $position = isset($_POST['position']) ? floatval($_POST['position']) : 0.0;

    if (!empty($filename) && $raceID > 0 && $splitNumber > 0 && $position > 0) {
        insertDataFromCSV($filename, $conn, $raceID, $splitNumber, $position);
    } else {
        echo "Please provide a valid filename, select a race, and enter valid split number and position values.";
    }
} else {
    // Display form to enter filename, split number, position and select race from dropdown
    echo "<form method='post' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "'>";
    echo "Filename: <input type='text' name='filename' required><br>";
    echo "Split Number: <input type='number' name='splitNumber' required><br>";
    echo "Position: <input type='number' step='0.01' name='position' required><br>";
    echo "Race ID: <select name='raceID' required>";

    // Populate dropdown with event names and IDs
    $events = getEvents($conn);
    foreach ($events as $id => $eventName) {
        echo "<option value='" . htmlspecialchars($id) . "'>" . htmlspecialchars($eventName) . "</option>";
    }

    echo "</select><br>";
    echo "<input type='submit' value='Submit'>";
    echo "</form>";
}

// Close connection
$conn->close();
?>
