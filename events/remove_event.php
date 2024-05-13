<!DOCTYPE html>
<html>
<head>
    <title>Remove Running Race Event</title>
	    <link rel="stylesheet" type="text/css" href="css/remove_event_style.css">
</head>
<body>
    <h2>Remove Running Race Event</h2>
    <form action="process_remove_event.php" method="post">
        <label for="eventId">Select Event:</label><br>
        <select id="eventId" name="eventId" required>
            <?php
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

            // Retrieve events
            $sql = "SELECT id, event_name FROM events ORDER BY event_date DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["id"] . "'>" . $row["event_name"] . "</option>";
                }
            } else {
                echo "<option value=''>No events found</option>";
            }
            $conn->close();
            ?>
        </select><br><br>
        <input type="submit" value="Remove Event">
    </form>
</body>
</html>
