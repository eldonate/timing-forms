<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Race Results</title>
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        /* Form styles (default for larger screens) */
        form {
            width: 60%; /* Adjust this value as needed for larger screens */
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Input and button styles (default for larger screens) */
        label {
            font-weight: bold;
            font-size: 15px; /* Set font size to 20px */
            text-align: center; /* Center labels horizontally */
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        select,
        input[type="submit"] {
            width: 100%; /* Full width for larger screens */
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        /* Media query for smaller screens */
        @media screen and (max-width: 400px) {
            /* Adjust styles for smaller screens here */
            label {
                font-size: 15px; /* Set font size to 15px for mobile */
            }

            /* Form styles for mobile */
            form {
                width: 100%; /* Full width on mobile */
            }

            /* Input and button styles for mobile */
            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="date"],
            select {
                width: calc(100% - 20px); /* Adjust padding value as needed */
            }

            input[type="submit"] {
                width: 100%; /* Full width on mobile */
            }
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .hidden {
            display: none;
        }

        .toggle-button {
            cursor: pointer;
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            transition-duration: 0.4s;
            border-radius: 5px;
        }

        .toggle-button:hover {
            background-color: #3e8e41; /* Dark green */
        }
    </style>
</head>
<body>
    <h1>Race Results</h1>
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
        let eventsData = [];

        // Fetch data from the API
        fetch('https://racetime.gr/results/api.php')
            .then(response => response.json())
            .then(data => {
                eventsData = data.events;
                populateEventSelect();
            })
            .catch(error => {
                console.error('Error fetching data from API:', error);
            });

        function populateEventSelect() {
            const eventSelect = document.getElementById('eventSelect');
            eventSelect.innerHTML = '<option value="">--Select Event--</option>';

            eventsData.forEach(event => {
                const option = document.createElement('option');
                option.value = event.id;
                option.textContent = event.event_name;
                eventSelect.appendChild(option);
            });
        }

        const categorySelect = document.getElementById('categorySelect');
        const resultsDiv = document.getElementById('results');

        eventSelect.addEventListener('change', function() {
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

                    const headers = ['General Position', 'Participant', 'Finish Time', 'Gender', 'Age', 'Gender Position', 'Speed', 'Pace', 'Splits'];
                    const tr = document.createElement('tr');
                    headers.forEach(header => {
                        const th = document.createElement('th');
                        th.textContent = header;
                        tr.appendChild(th);
                    });
                    thead.appendChild(tr);
                    table.appendChild(thead);

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

        function formatSplits(splits) {
            let html = '<ul>';
            splits.forEach(split => {
                const splitPosition = split.position / 1000;
                html += `<li>Split ${splitPosition.toFixed(1)}k: ${split.split_time}</li>`;
            });
            html += '</ul>';
            return html;
        }
    </script>
</body>
</html>
