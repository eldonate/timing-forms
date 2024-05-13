<!DOCTYPE html>
<html>
<head>
    <title>Event Registration</title>
    <link rel="stylesheet" type="text/css" href="css/registration_style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h2>Event Registration</h2>
    <form action="process_registration.php" method="post" id="registrationForm">
        <label for="eventId">Select Event:</label><br>
        <select id="eventId" name="eventId" required onchange="populateCategories()">
            <option value="">Select Event</option>
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

        <label for="categoryName">Select Category:</label><br>
        <select id="categoryName" name="categoryName" required disabled>
            <option value="">Select Event First</option>
        </select><br><br>

        <label for="firstName">First Name:</label><br>
        <input type="text" id="firstName" name="firstName" required><br><br>

        <label for="lastName">Last Name:</label><br>
        <input type="text" id="lastName" name="lastName" required><br><br>

        <label for="dob">Date of Birth:</label><br>
        <input type="date" id="dob" name="dob" required><br><br>

        <label for="sex">Sex:</label><br>
        <select id="sex" name="sex" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select><br><br>

        <label for="team">Team:</label><br>
        <input type="text" id="team" name="team"><br><br>

        <label for="phoneNumber">Phone Number:</label><br>
        <input type="tel" id="phoneNumber" name="phoneNumber" pattern="[0-9]{10}" required><br><br>

        <label for="city">City:</label><br>
        <input type="text" id="city" name="city" required><br><br>

        <label for="safetyNumber">Safety Number:</label><br>
        <input type="text" id="safetyNumber" name="safetyNumber"><br><br>

        <label for="tShirtSize">T-Shirt Size:</label><br>
        <select id="tShirtSize" name="tShirtSize" required>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
        </select><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <input type="checkbox" id="acceptTerms" name="acceptTerms" required>
        <label for="acceptTerms">I accept the terms</label><br><br>

        <input type="submit" value="Register">
    </form>

    <script>
        function populateCategories() {
            var eventId = document.getElementById("eventId").value;
            var categoryNameSelect = document.getElementById("categoryName");

            // Enable category dropdown menu
            categoryNameSelect.disabled = false;
            categoryNameSelect.innerHTML = "<option value=''>Loading...</option>";

            // Fetch categories for the selected event from the database
            fetch("get_categories.php?eventId=" + eventId)
                .then(response => response.json())
                .then(categories => {
                    // Populate category dropdown menu with retrieved categories
                    categoryNameSelect.innerHTML = "<option value=''>Select Category</option>";
                    categories.forEach(category => {
                        categoryNameSelect.innerHTML += "<option value='" + category.category_name + "'>" + category.category_name + " - " + category.category_cost + "</option>";
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    categoryNameSelect.innerHTML = "<option value=''>Failed to Load Categories</option>";
                });
        }
    </script>


</body>
</html>
