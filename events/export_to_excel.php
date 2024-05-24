<?php
// Check if eventId is set in the POST data
if (isset($_POST["eventId"])) {
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

    if ($result->num_rows > 0) {
        // Set headers for Excel file download
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=registered_users_event_" . $eventId . ".xls");

        // Output Excel file header
        echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Date of Birth</th><th>Sex</th><th>Team</th><th>Phone Number</th><th>City</th><th>Safety Number</th><th>T-Shirt Size</th><th>Email</th><th>Registration Date</th><th>Category</th><th>Category Cost</th></tr>";

        // Output Excel file data
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
                echo "<td colspan='2'>Category cost not found</td>";
            }
            echo "</tr>";
        }

        // Close the Excel file
        echo "</table>";

        // Close the statement and database connection
        $stmt->close();
        $conn->close();
    } else {
        echo "No users registered for this event.";
    }
} else {
    echo "Error: Event ID not provided.";
}
?>
