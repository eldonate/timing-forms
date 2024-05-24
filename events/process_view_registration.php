<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registered Users</title>
    <link rel="stylesheet" href="css/view_registration_style.css">
</head>
<body>
    <?php
    // Check if the form is submitted via POST method
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if eventId is set in the POST data
        if (isset($_POST["eventId"])) {
            // Retrieve the selected event ID from the POST data
            $eventId = $_POST["eventId"];

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

            // Prepare and execute SQL statement to retrieve registered users for the selected event
            $stmt = $conn->prepare("SELECT * FROM registrations WHERE event_id = ?");
            $stmt->bind_param("i", $eventId);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if any users are registered for the selected event
            if ($result->num_rows > 0) {
                // Display registered users in a table
                echo "<table border='1'>";
                echo "<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Date of Birth</th><th>Sex</th><th>Team</th><th>Phone Number</th><th>City</th><th>Safety Number</th><th>T-Shirt Size</th><th>Email</th><th>Registration Date</th><th>Category</th><th>Category Cost</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["first_name"] . "</td>";
                    echo "<td>" . $row["last_name"] . "</td>";
                    echo "<td>" . $row["dob"] . "</td>";
                    echo "<td>" . $row["sex"] . "</td>";
                    echo "<td>" . $row["team"] . "</td>";
                    echo "<td>" . $row["phone_number"] . "</td>";
                    echo "<td>" . $row["city"] . "</td>";
                    echo "<td>" . $row["safety_number"] . "</td>";
                    echo "<td>" . $row["t_shirt_size"] . "</td>";
                    echo "<td>" . $row["email"] . "</td>";
                    echo "<td>" . $row["registration_date"] . "</td>";

                    // Retrieve category_cost from categories table based on category_name
                    $categoryName = $row["category_name"];
                    $categoryCostQuery = $conn->prepare("SELECT category_cost FROM categories WHERE category_name = ?");
                    $categoryCostQuery->bind_param("s", $categoryName);
                    $categoryCostQuery->execute();
                    $categoryCostResult = $categoryCostQuery->get_result();

                    if ($categoryCostResult->num_rows > 0) {
                        $categoryCostRow = $categoryCostResult->fetch_assoc();
                        $categoryCost = $categoryCostRow["category_cost"];
                        echo "<td>" . $categoryName . "</td>";
                        echo "<td>" . $categoryCost . "</td>";
                    } else {
                        // If no category cost found, display a message or handle it accordingly
                        echo "<td colspan='2'>Category cost not found</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";

                // Add export button
                echo "<form action='export_to_excel.php' method='post'>";
                echo "<input type='hidden' name='eventId' value='" . $eventId . "'>";
                echo "<input type='submit' value='Export to Excel'>";
                echo "</form>";
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
</body>
</html>
