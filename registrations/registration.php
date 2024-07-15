<!DOCTYPE html>
<html>
<head>
    <title>Race Time - Registration Form</title>
    <link rel="stylesheet" type="text/css" href="css/registration_style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h2>Race Selection</h2>
    <div id="raceContainer">
        <?php
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

        // Initialize event name variable
        $eventName = "";

        // Retrieve events from the database using prepared statement
        $sql = "SELECT id, event_name, event_logo FROM events WHERE enabled=true";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='race-box' data-event-id='" . htmlspecialchars($row["id"]) . "'>";
                if ($row["event_logo"]) {
                    $logoPath = "../event_logos/" . htmlspecialchars($row["event_logo"]);
                    echo "<img src='" . $logoPath . "' alt='Event Logo' class='race-logo'><br>";
                }
                echo "<a href='registration.php?eventId=" . htmlspecialchars($row["id"]) . "' class='race-link'>";
                echo "<span class='race-name'>" . htmlspecialchars($row["event_name"]) . "</span></a></div>";
            }
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>

    <div id="eventDetailsContainer" class="hidden">
        <h2 id="eventTitle">Registration Form</h2>
        <p id="eventDescription">Select an event to proceed with registration.</p>
    </div>

    <div id="registrationFormContainer" class="hidden">
        <form action="process_registration.php" method="post" id="registrationForm">
            <input type="hidden" id="eventId" name="eventId">
            
            <label for="categoryName">Κατηγορία:</label><br>
            <select id="categoryName" name="categoryName" required disabled>
                <option value="">Επιλέξτε κατηγορία</option>
            </select><br><br>

            <label for="firstName">Όνομα:</label><br>
            <input type="text" id="firstName" name="firstName" required><br><br>

            <label for="lastName">Επίθετο:</label><br>
            <input type="text" id="lastName" name="lastName" required><br><br>

            <label for="dob">Ημ. Γέννησης:</label><br>
            <input type="date" id="dob" name="dob" required><br><br>

            <label for="sex">Φύλο:</label><br>
            <select id="sex" name="sex" required>
                <option value="Male">Άνδρας</option>
                <option value="Female">Γυναίκα</option>
            </select><br><br>

            <label for="team">Ομάδα:</label><br>
            <input type="text" id="team" name="team"><br><br>

            <label for="phoneNumber">Τηλ. Επικοινωνίας:</label><br>
            <input type="tel" id="phoneNumber" name="phoneNumber" pattern="[0-9]{10}" required><br><br>

            <label for="city">Πόλη:</label><br>
            <input type="text" id="city" name="city" required><br><br>

            <label for="safetyNumber">Τηλ. Έκτακτης Ανάγκης:</label><br>
            <input type="text" id="safetyNumber" name="safetyNumber"><br><br>

            <label for="tShirtSize">Μέγεθος Μπλούζας:</label><br>
            <select id="tShirtSize" name="tShirtSize" required>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
            </select><br><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>

            <input type="checkbox" id="acceptTerms" name="acceptTerms" required>
            <label for="acceptTerms">Αποδέχομαι τους <a href="#">όρους χρήσης</a></label><br><br>

            <input type="submit" value="Register" id="submitButton">
        </form>
    </div>

    <script>
        document.querySelectorAll('.race-box').forEach(box => {
            box.addEventListener('click', function() {
                var eventId = this.getAttribute('data-event-id');
                var eventName = this.querySelector('.race-name').textContent;

                // Update event details section
                document.getElementById('eventTitle').textContent = "Registration Form - " + eventName;
                document.getElementById('eventDescription').textContent = "You are registering for the event: " + eventName;

                // Set eventId in hidden input
                document.getElementById('eventId').value = eventId;

                // Fetch categories for the selected event
                populateCategories(eventId);

                // Show the event details and registration form
                document.getElementById('eventDetailsContainer').classList.remove('hidden');
                document.getElementById('registrationFormContainer').classList.remove('hidden');
            });
        });

        function populateCategories(eventId) {
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
                        var optionText = category.category_name + " - " + category.category_cost + "€";
                        categoryNameSelect.innerHTML += "<option value='" + category.category_name + "'>" + optionText + "</option>";
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    categoryNameSelect.innerHTML = "<option value=''>Failed to Load Categories</option>";
                });
        }

        // Disable submit button on form submit to prevent multiple submissions
        document.getElementById('registrationForm').addEventListener('submit', function() {
            document.getElementById('submitButton').disabled = true;
        });

        // Simulate event selection if eventId is passed in URL
        document.addEventListener('DOMContentLoaded', function() {
            var urlParams = new URLSearchParams(window.location.search);
            var eventId = urlParams.get('eventId');
            if (eventId) {
                document.querySelector('.race-box[data-event-id="' + eventId + '"]').click();
            }
        });
    </script>
</body>
</html>
