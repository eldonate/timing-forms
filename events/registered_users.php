<!DOCTYPE html>
<html>
<head>
    <title>Event Registration</title>
    <link rel="stylesheet" type="text/css" href="css/view_registration_style.css">
</head>
<body>
    <h2>View Registered Users</h2>
    <form action="process_view_registration.php" method="post">
        <label for="eventId">Select Event:</label><br>
        <select id="eventId" name="eventId" required>
            <?php
            // Connect to the database
            $servername = "localhost";
            $username = "events_user";
            $password = "Uad0cAm5d008_8_7d8";
            $dbname = "events";
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Retrieve events from the database
            $sql = "SELECT id, event_name FROM events";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["id"] . "'>" . $row["event_name"] . "</option>";
                }
            }
            $conn->close();
            ?>
        </select><br><br>
        <input type="submit" value="View Registered Users">
    </form>

    <!-- Container to display registered users -->
    <div id="registeredUsers">
        <!-- Users will be displayed here after form submission -->
    </div>
</body>
</html>
