<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Race Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .event, .category, .results {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .toggle-button {
            cursor: pointer;
            color: blue;
            text-decoration: underline;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <h1>Race Results</h1>
    <?php
    // The URL to your API endpoint
    $api_url = 'https://racetime.gr/results/api.php';  // Replace with your actual API URL

    // Fetch data from the API
    $response = file_get_contents($api_url);
    if ($response === FALSE) {
        echo '<p>Error fetching data from the API.</p>';
        exit;
    }

    // Decode the JSON response
    $data = json_decode($response, true);
    if ($data === NULL) {
        echo '<p>Error decoding JSON response.</p>';
        exit;
    }

    // Check if 'events' key exists in the response
    if (!isset($data['events'])) {
        echo '<p>No events data found in the API response.</p>';
        exit;
    }

    // Store the events data in a JavaScript variable
    echo '<script>';
    echo 'const eventsData = ' . json_encode($data['events']) . ';';
    echo '</script>';
    ?>

    <div>
        <label for="eventSelect">Select Event:</label>
        <select id="eventSelect">
            <option value="">--Select Event--</option>
        </select>
    </div>

    <div>
        <label for="categorySelect">Select Category:</label>
        <select id="categorySelect" disabled>
            <option value="">--Select Category--</option>
        </select>
    </div>

    <div id="results"></div>

    <script>
        // Populate event selection dropdown
        const eventSelect = document.getElementById('eventSelect');
        const categorySelect = document.getElementById('categorySelect');
        const resultsDiv = document.getElementById('results');

        eventsData.forEach(event => {
            const option = document.createElement('option');
            option.value = event.id;
            option.textContent = event.event_name;
            eventSelect.appendChild(option);
        });

        eventSelect.addEventListener('change', function() {
            // Clear previous category options and results
            categorySelect.innerHTML = '<option value="">--Select Category--</option>';
            resultsDiv.innerHTML = '';
            categorySelect.disabled = true;

            const selectedEventId = this.value;
            if (selectedEventId) {
                const selectedEvent = eventsData.find(event => event.id == selectedEventId);
                if (selectedEvent && selectedEvent.categories) {
                    Object.keys(selectedEvent.categories).forEach(category => {
                        const option = document.createElement('option');
                        option.value = category;
                        option.textContent = category;
                        categorySelect.appendChild(option);
                    });
                    categorySelect.disabled = false;
                }
            }
        });

        categorySelect.addEventListener('change', function() {
            // Clear previous results
            resultsDiv.innerHTML = '';

            const selectedEventId = eventSelect.value;
            const selectedCategory = this.value;

            if (selectedEventId && selectedCategory) {
                const selectedEvent = eventsData.find(event => event.id == selectedEventId);
                const results = selectedEvent.categories[selectedCategory];

                if (results && results.length > 0) {
                    const table = document.createElement('table');
                    const thead = document.createElement('thead');
                    const tbody = document.createElement('tbody');

                    // Create table headers
                    const headers = ['General Position', 'Participant', 'Finish Time', 'Gender', 'Age', 'Gender Position', 'Speed', 'Pace', 'Splits'];
                    const tr = document.createElement('tr');
                    headers.forEach(header => {
                        const th = document.createElement('th');
                        th.textContent = header;
                        tr.appendChild(th);
                    });
                    thead.appendChild(tr);
                    table.appendChild(thead);

                    // Create table rows
                    results.forEach(result => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${result.general_position}</td>
                            <td>${result.FirstName} ${result.LastName}</td>
                            <td>${result.FinishTime}</td>
                            <td>${result.Gender}</td>
                            <td>${result.Age}</td>
                            <td>${result.gender_position}</td>
                            <td>${result.speed}</td>
                            <td>${result.pace}</td>
                            <td><button class="toggle-button">Show Splits</button>
                                <div class="hidden splits">${formatSplits(result.splits)}</div></td>
                        `;
                        tbody.appendChild(tr);
                    });
                    table.appendChild(tbody);
                    resultsDiv.appendChild(table);

                    // Add event listeners for toggle buttons
                    const toggleButtons = document.querySelectorAll('.toggle-button');
                    toggleButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const splitsDiv = this.nextElementSibling;
                            splitsDiv.classList.toggle('hidden');
                            if (splitsDiv.classList.contains('hidden')) {
                                this.textContent = 'Show Splits';
                            } else {
                                this.textContent = 'Hide Splits';
                            }
                        });
                    });
                } else {
                    resultsDiv.innerHTML = '<p>No results available for this category.</p>';
                }
            }
        });

        // Function to format splits for display
        function formatSplits(splits) {
            let html = '<ul>';
            splits.forEach(split => {
                html += `<li>Split ${split.split_number}: ${split.split_time}</li>`;
            });
            html += '</ul>';
            return html;
        }
    </script>
</body>
</html>
