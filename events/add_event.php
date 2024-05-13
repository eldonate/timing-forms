<!DOCTYPE html>
<html>
<head>
    <title>Add Running Race Event</title>
    <link rel="stylesheet" type="text/css" href="css/add_event_style.css">
</head>
<body>
    <h2>Add Running Race Event</h2>
    <form action="process_form.php" method="post" id="eventForm">
        <label for="eventName">Event Name:</label><br>
        <input type="text" id="eventName" name="eventName" required><br>

        <label for="eventLocation">Event Location:</label><br>
        <input type="text" id="eventLocation" name="eventLocation" required><br>

        <label for="eventDate">Event Date:</label><br>
        <input type="date" id="eventDate" name="eventDate" required><br>

        <label for="eventType">Event Type:</label><br>
        <select id="eventType" name="eventType" required>
            <option value="road_running">Road Running</option>
            <option value="trail_running">Trail Running</option>
            <option value="other">Other</option>
        </select><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4" cols="50"></textarea><br>

        <label for="eventWebsite">Event Website:</label><br>
        <input type="url" id="eventWebsite" name="eventWebsite"><br>

        <div id="categoryFields">
            <!-- JavaScript will add category fields dynamically -->
        </div>

        <button type="button" onclick="addCategoryFields()">Add Category</button><br><br>

        <input type="submit" value="Submit">
    </form>

    <script>
        function addCategoryFields() {
            var categoryFields = document.getElementById('categoryFields');
            var categoryIndex = categoryFields.children.length / 2 + 1; // Number of categories + 1
            var categoryDiv = document.createElement('div');
            categoryDiv.innerHTML = '<h3>Category ' + categoryIndex + '</h3>' +
                                    '<label for="categoryName' + categoryIndex + '">Category Name:</label><br>' +
                                    '<input type="text" id="categoryName' + categoryIndex + '" name="categoryName[]" required><br>' +
                                    '<label for="categoryCost' + categoryIndex + '">Category Cost:</label><br>' +
                                    '<input type="number" id="categoryCost' + categoryIndex + '" name="categoryCost[]" step="0.01" required><br>' +
                                    '<button type="button" onclick="removeCategoryFields(this)">Remove</button><br><br>';
            categoryFields.appendChild(categoryDiv);
        }

        function removeCategoryFields(button) {
            var categoryDiv = button.parentNode;
            var categoryFields = document.getElementById('categoryFields');
            categoryFields.removeChild(categoryDiv);
            updateCategoryIndexes();
        }

        function updateCategoryIndexes() {
            var categoryFields = document.getElementById('categoryFields');
            var categories = categoryFields.getElementsByTagName('div');
            for (var i = 0; i < categories.length; i++) {
                var index = i + 1;
                var category = categories[i];
                category.children[0].innerText = 'Category ' + index; // Update category heading
                var inputs = category.getElementsByTagName('input');
                for (var j = 0; j < inputs.length; j++) {
                    inputs[j].id = 'categoryName' + index;
                    inputs[j].name = 'categoryName[]';
                }
                var labels = category.getElementsByTagName('label');
                for (var k = 0; k < labels.length; k++) {
                    labels[k].setAttribute('for', 'categoryName' + index);
                }
            }
        }
    </script>
</body>
</html>
