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

// Function to convert time string to hours
function timeToHours($time) {
    list($hours, $minutes, $seconds) = explode(':', $time);
    return $hours + ($minutes / 60) + ($seconds / 3600);
}

// Function to update positions, speed, and pace for a specific race and category
function updatePositionsSpeedAndPace($conn, $raceid, $racecategory) {
    // Retrieve all results for the specific race and category sorted by FinishTime
    $sql = "SELECT id, FinishTime, Distance, Gender FROM results WHERE raceid = ? AND racecategory = ? ORDER BY FinishTime ASC";
    $results = [];
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("is", $raceid, $racecategory);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            $stmt->close();
        } else {
            echo "Error retrieving results: " . $stmt->error;
            return;
        }
    } else {
        echo "Error preparing results retrieval statement: " . $conn->error;
        return;
    }

    // Update general_position, speed, and calculate pace
    foreach ($results as $index => $row) {
        $generalPosition = $index + 1;
        $id = $row['id'];
        $finishTime = $row['FinishTime'];
        $distance = $row['Distance'];
        $timeInHours = timeToHours($finishTime);
        $speed = $distance / 1000 / $timeInHours;  // Convert distance to kilometers
        $paceMinutes = floor(60 / $speed);
        $paceSeconds = round((60 / $speed - $paceMinutes) * 60);
        $pace = sprintf("%02d:%02d", $paceMinutes, $paceSeconds); // Format pace as MM:SS

        $updateSql = "UPDATE results SET general_position = ?, speed = ?, pace = ? WHERE id = ?";
        if ($stmt = $conn->prepare($updateSql)) {
            $stmt->bind_param("issi", $generalPosition, $speed, $pace, $id);
            if (!$stmt->execute()) {
                echo "Error updating general_position, speed, and pace: " . $stmt->error . "<br>";
            }
            $stmt->close();
        } else {
            echo "Error preparing general_position, speed, and pace statement: " . $conn->error . "<br>";
        }
    }

    // Update gender_position
    $genderPositions = ['male' => 1, 'female' => 1];
    foreach ($results as $row) {
        $id = $row['id'];
        $gender = strtolower($row['Gender']);
        $genderPosition = $genderPositions[$gender];

        $genderPositionLabel = ($gender === 'male' ? 'M' : 'F') . $genderPosition;
        $updateSql = "UPDATE results SET gender_position = ? WHERE id = ?";
        if ($stmt = $conn->prepare($updateSql)) {
            $stmt->bind_param("si", $genderPositionLabel, $id);
            if (!$stmt->execute()) {
                echo "Error updating gender_position: " . $stmt->error . "<br>";
            }
            $stmt->close();
        } else {
            echo "Error preparing gender_position statement: " . $conn->error . "<br>";
        }

        $genderPositions[$gender]++;
    }
}

// Retrieve distinct raceid and racecategory combinations
$raceQuery = "SELECT DISTINCT raceid, racecategory FROM results";
if ($result = $conn->query($raceQuery)) {
    while ($row = $result->fetch_assoc()) {
        $raceid = $row['raceid'];
        $racecategory = $row['racecategory'];
        updatePositionsSpeedAndPace($conn, $raceid, $racecategory);
    }
} else {
    echo "Error retrieving race categories: " . $conn->error;
}

// Close connection
$conn->close();

echo "Positions, speed, and pace updated successfully.";
?>
