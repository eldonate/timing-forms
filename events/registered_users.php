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
            include_once 'config.php';
            $servername = DB_HOST;
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = DB_NAME;
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
