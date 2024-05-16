<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/var/www/vhosts/racetime.gr/PHPMailer/src/Exception.php';
require '/var/www/vhosts/racetime.gr/PHPMailer/src/PHPMailer.php';
require '/var/www/vhosts/racetime.gr/PHPMailer/src/SMTP.php';
require_once 'config.php'; // Include the configuration file

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

    // Connect to the database using credentials from config.php
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute SQL statement to insert registration data into the database
    $stmt = $conn->prepare("INSERT INTO registrations (event_id, category_name, first_name, last_name, dob, sex, team, phone_number, city, safety_number, t_shirt_size, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssssss", $eventId, $categoryName, $firstName, $lastName, $dob, $sex, $team, $phoneNumber, $city, $safetyNumber, $tShirtSize, $email);
    if ($stmt->execute()) {
        // Prepare and execute SQL statement to fetch event name and website from the database
        $fetchStmt = $conn->prepare("SELECT event_name, event_website FROM events WHERE Id=?");
        $fetchStmt->bind_param("i", $eventId);
        $fetchStmt->execute();
        $fetchStmt->bind_result($eventName, $eventWebsite);
        $fetchStmt->fetch();
        $fetchStmt->close();
        
        // Close the statement
        $stmt->close();
        // Close the database connection
        $conn->close();
        
        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = SMTP_HOST; // Use SMTP host from config.php
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME; // Use SMTP username from config.php
            $mail->Password = SMTP_PASSWORD; // Use SMTP password from config.php
            $mail->SMTPSecure = 'tls';
            $mail->Port = SMTP_PORT; // Use SMTP port from config.php

            //Recipients
            $mail->setFrom('info@racetime.gr', 'Race Time');
            $mail->addAddress($email); // Add a recipient

            //Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Επιβεβαίωση Εγγραφής';
            $mail->Body    = "Ευχαριστούμε για την εγγραφή σας στον '$eventName'!";

            $mail->send();
            echo "<center><h1>Registration successful! <br> Please wait...</h1></center>";
            echo "<script>setTimeout(function() { window.location.href = '{$eventWebsite}'; }, 3000);</script>";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error: " . $stmt->error;
        // Close the statement and database connection
        $stmt->close();
        $conn->close();
    }
} else {
    // If the form is not submitted via POST method, return an error message
    echo "Error: Form submission method not allowed.";
}
?>
