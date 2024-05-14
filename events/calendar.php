<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <!-- Link to the CSS file -->
    <link rel="stylesheet" href="css/calendar_style.css">
</head>
<body>
	<?php
// Database connection
include_once 'config.php';
$servername = DB_HOST;
$username = DB_USER;
$password = DB_PASSWORD;
$dbname = DB_NAME;

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch events for a given date
function getEvents($date, $conn) {
    $sql = "SELECT * FROM events WHERE event_date = '$date'";
    $result = $conn->query($sql);
    
    $events = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
    }
    
    return $events;
}

// Get current month and year
$month = isset($_GET['month']) ? $_GET['month'] : date('n');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Calculate previous and next month
$prev_month = ($month == 1) ? 12 : $month - 1;
$prev_year = ($prev_month == 12) ? $year - 1 : $year;
$next_month = ($month == 12) ? 1 : $month + 1;
$next_year = ($next_month == 1) ? $year + 1 : $year;

// Get number of days in the current month
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Create a container div for centering
echo "<div class='container'>";

// Add navigation buttons
echo "<div style='text-align: center; margin-bottom: 20px;'>";
echo "<a href='?month=$prev_month&year=$prev_year'>Previous Month</a> | ";
echo "<a href='?month=$next_month&year=$next_year'>Next Month</a>";
echo "</div>";

// Create a calendar table
echo "<table class='calendar'>";
echo "<tr><th colspan='7'>".date('F Y', mktime(0, 0, 0, $month, 1, $year))."</th></tr>";
echo "<tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr>";

// Get the day of the week the first day of the month falls on
$first_day_of_month = date('N', mktime(0, 0, 0, $month, 1, $year));

echo "<tr>";

// Add empty cells for the days before the first day of the month
for ($i = 1; $i < $first_day_of_month; $i++) {
    echo "<td></td>";
}

// Loop through each day of the month
for ($day = 1; $day <= $days_in_month; $day++) {
    $date = "$year-$month-$day";
    $events = getEvents($date, $conn);
    
    // Check if there are events for this date
    if (!empty($events)) {
        echo "<td style='background-color: #45a049;'><a href='#' onclick='showEvents(\"$date\");'>$day</a></td>";
    } else {
        echo "<td>$day</td>";
    }
    
    // Start a new row after Sunday
    if (date('N', strtotime($date)) == 7) {
        echo "</tr><tr>";
    }
}

// Fill in remaining empty cells
$last_day_of_month = date('N', mktime(0, 0, 0, $month, $days_in_month, $year));
for ($i = $last_day_of_month; $i < 7; $i++) {
    echo "<td></td>";
}

echo "</tr>";
echo "</table>";

// Display event details box
echo "<div id='eventList' class='event-details'>";
echo "<h3>Event Details</h3>";
echo "<div id='eventContent'>Click on a date to view events</div>";
echo "</div>";

// Close container div
echo "</div>";

// Close connection
$conn->close();
?>


<script>
function showEvents(date) {
    // Fetch events for the selected date via AJAX
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("eventList").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "get_events.php?date=" + date, true);
    xhttp.send();
}
</script>
	</body>
</html>
