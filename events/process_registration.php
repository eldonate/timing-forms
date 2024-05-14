<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $eventId = $_POST["eventId"];
    $categoryName = $_POST["categoryName"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $dob = $_POST["dob"];
    $sex = $_POST["sex"];
    $team = $_POST["team"];
    $phoneNumber = $_POST["phoneNumber"];
    $city = $_POST["city"];
    $safetyNumber = $_POST["safetyNumber"];
    $tShirtSize = $_POST["tShirtSize"];
    $email = $_POST["email"];

    // Additional processing can be done here, such as validation and sanitization of input data

    // Connect to the database
    include_once 'config.php';
    $servername = DB_HOST;
    $username = DB_USER;
    $password = DB_PASSWORD;
    $dbname = DB_NAME;
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute SQL statement to insert registration data into the database
    $stmt = $conn->prepare("INSERT INTO registrations (event_id, category_name, first_name, last_name, dob, sex, team, phone_number, city, safety_number, t_shirt_size, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssssss", $eventId, $categoryName, $firstName, $lastName, $dob, $sex, $team, $phoneNumber, $city, $safetyNumber, $tShirtSize, $email);
    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
} else {
    // If the form is not submitted via POST method, return an error message
    echo "Error: Form submission method not allowed.";
}
?>
