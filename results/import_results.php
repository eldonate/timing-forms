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
function insertDataFromCSV($filename, $conn, $raceID, $elevation, $distance) {
    // Open CSV file for reading
    if (($handle = fopen($filename, "r")) !== FALSE) {
        // Skip the header line
        fgetcsv($handle);

        // Read data from CSV and insert into database
        while (($data = fgetcsv($handle)) !== FALSE) {
            // Prepare SQL statement to insert data
            $sql = "INSERT INTO results (ParticipantID, FirstName, LastName, RFID, Birthdate, Age, FinishTime, Gender, RaceCategory, RaceID, Elevation, Distance)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            // Prepare and bind parameters
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("isssssssssdd", $participantID, $firstName, $lastName, $rfid, $birthdate, $age, $finishTime, $gender, $raceCategory, $raceID, $elevation, $distance);

                // Set parameters
                list($participantID, $firstName, $lastName, $rfid, $birthdate, $age, $finishTime, $gender, $raceCategory) = $data;
                $birthdate = date('Y-m-d', strtotime($birthdate)); // Convert birthdate to MySQL format

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
    $elevation = isset($_POST['elevation']) ? floatval($_POST['elevation']) : 0.0;
    $distance = isset($_POST['distance']) ? floatval($_POST['distance']) : 0.0;

    if (!empty($filename) && $raceID > 0 && $distance > 0) {
        insertDataFromCSV($filename, $conn, $raceID, $elevation, $distance);
    } else {
        echo "Please provide a valid filename, select a race, and enter valid elevation and distance values.";
    }
} else {
    // Display form to enter filename, elevation, distance and select race from dropdown
    echo "<form method='post' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "'>";
    echo "Filename: <input type='text' name='filename' required><br>";
    echo "Elevation: <input type='number' step='0.01' name='elevation' required><br>";
    echo "Distance: <input type='number' step='0.01' name='distance' required><br>";
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
