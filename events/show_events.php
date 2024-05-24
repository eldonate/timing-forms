<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Running Race Events</title>
    <link rel="stylesheet" href="css/show_event_style.css">
</head>
<body>
    <div class="container">
        <h2>Select a Running Race Event</h2>

        <form method="post">
            <label for="eventId">Select Event:</label>
            <select name="eventId" id="eventId">
                <?php
                // DB credentials
                include_once 'config.php';
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

                // Retrieve events
                $sql = "SELECT id, event_name FROM events ORDER BY event_date DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["id"] . "'>" . $row["event_name"] . "</option>";
                    }
                }
                $conn->close();
                ?>
            </select>
            <input type="submit" value="Show Details">
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["eventId"]) && ctype_digit($_POST["eventId"])) {
                $selectedEventId = $_POST["eventId"];

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Prepare SQL statement to retrieve event details
                $sql = "SELECT e.*, c.category_name, c.category_cost
                        FROM events e
                        LEFT JOIN categories c ON e.id = c.event_id
                        WHERE e.id = ?
                        ORDER BY e.event_date DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $selectedEventId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Output event details
                    echo "<div class='event-details'>";
                    echo "<h2>Event Details</h2>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<h3>" . $row["event_name"] . "</h3>";
                        echo "<p><strong>Location:</strong> " . $row["event_location"] . "</p>";
                        echo "<p><strong>Date:</strong> " . $row["event_date"] . "</p>";
                        echo "<p><strong>Type:</strong> " . ucfirst(str_replace("_", " ", $row["event_type"])) . "</p>";
                        if (!empty($row["category_name"])) {
                            echo "<p><strong>Categories:</strong></p>";
                            echo "<ul>";
                            do {
                                echo "<li><strong>Name:</strong> " . htmlspecialchars($row["category_name"]) . ", <strong>Cost:</strong> €" . htmlspecialchars($row["category_cost"]) . "</li>";
                            } while ($row = $result->fetch_assoc() and $row["id"] == $selectedEventId);
                            echo "</ul>";
                        }
                    }
                    echo "</div>";

                    // Output event description
                    echo "<div class='description'>";
                    echo "<h2>Description</h2>";
                    // Move the cursor back to the beginning of the result set
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()) {
                        echo "<p>" . htmlspecialchars($row["description"]) . "</p>";
                    }
                    echo "</div>";
                } else {
                    echo "Event not found.";
                }

                // Close statement and connection
                $stmt->close();
                $conn->close();
            } else {
                echo "Invalid event ID.";
            }
        }
        ?>
    </div>
</body>
</html>
