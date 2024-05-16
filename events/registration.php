<!DOCTYPE html>
<html>
<head>
    <title>Race Time - Registration Form</title>
    <link rel="stylesheet" type="text/css" href="css/registration_style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h2>Registration Form</h2>
    <form action="process_registration.php" method="post" id="registrationForm">
        <label for="eventId">Επιλογή αγώνα:</label><br>
        <select id="eventId" name="eventId" required onchange="populateCategories()">
            <option value="">Επιλέξτε αγώνα</option>
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

// Retrieve events from the database using prepared statement
$sql = "SELECT id, event_name FROM events";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . htmlspecialchars($row["id"]) . "'>" . htmlspecialchars($row["event_name"]) . "</option>";
    }
}

$stmt->close();
$conn->close();
?>

        </select><br><br>

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
