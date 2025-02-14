<?php
include("../../db_con.php");
include("../../staff/staff_nav.php");

// Get the subject code from the URL
$subject_code = isset($_GET['subject_code']) ? $_GET['subject_code'] : '';

if (empty($subject_code)) {
    die("Subject code is required.");
}

// Fetch data from the database
$query = "SELECT * FROM " . mysqli_real_escape_string($con, $subject_code);
$result = mysqli_query($con, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subject Code: <?php echo htmlspecialchars($subject_code); ?></title>
    <script>
    function enableEditing(rowId) {
        var row = document.getElementById(rowId);
        var spans = row.querySelectorAll('span.tick'); // Select spans with class 'tick'
        var inputs = row.querySelectorAll('input[type="text"]');

        spans.forEach(span => span.style.display = 'none');
        inputs.forEach(input => input.style.display = 'inline');
        
        document.getElementById('edit-' + rowId).style.display = 'none';
        document.getElementById('save-' + rowId).style.display = 'inline';
        document.getElementById('cancel-' + rowId).style.display = 'inline';
    }

    function cancelEditing(rowId) {
        var row = document.getElementById(rowId);
        var inputs = row.getElementsByTagName('input');
        var spans = row.getElementsByTagName('span');
        
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].type === 'hidden' || inputs[i].type === 'text') {
                inputs[i].style.display = 'none'; // Hide the input fields
                inputs[i].value = inputs[i].getAttribute('data-original-value'); // Reset to original value
                spans[i].style.display = 'inline'; // Show the span elements
            }
        }
        document.getElementById('edit-' + rowId).style.display = 'inline';
        document.getElementById('save-' + rowId).style.display = 'none';
        document.getElementById('cancel-' + rowId).style.display = 'none';
    }

    function saveEditing(rowId) {
        var row = document.getElementById(rowId);
        var inputs = row.getElementsByTagName('input');
        var formData = new FormData();
        
        for (var i = 0; i < inputs.length; i++) {
            formData.append(inputs[i].name, inputs[i].value);
        }
        formData.append('subject_code', '<?php echo htmlspecialchars($subject_code); ?>');
        formData.append('action', 'update');

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                alert('Row updated successfully.');
                for (var i = 0; i < inputs.length; i++) {
                    inputs[i].setAttribute('data-original-value', inputs[i].value);
                    inputs[i].style.display = 'none';
                    var span = row.getElementsByTagName('span')[i];
                    span.textContent = inputs[i].value;
                    span.style.display = 'inline';
                }
                document.getElementById('edit-' + rowId).style.display = 'inline';
                document.getElementById('save-' + rowId).style.display = 'none';
                document.getElementById('cancel-' + rowId).style.display = 'none';
                location.reload(); // Refresh the page after saving
            } else {
                alert('Failed to update row. Please try again.');
            }
        };
        xhr.onerror = function () {
            alert('An error occurred during the request.');
        };
        xhr.send(formData);
    }
    </script>
</head>
<style>
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 20px;
    background-color: #f8f8f8; /* Light gray background */
}

form {
    height: 10%;
    margin-bottom: 20px;
    background-color: #fff;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1); /* Soft shadow */
}
.form {
    display: flex;
}
form label {
    margin-right: 10px;
    font-weight: bold;
}

form input[type="text"],
form button {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

form button {
    background-color: #4CAF50; /* Green */
    color: white;
    border: none;
    cursor: pointer;
}

form button:hover {
    background-color: #45a049; /* Darker green on hover */
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1); /* Soft shadow */
}

table th,
table td {
    border: 1px solid #ddd;
    padding: 5px;
    text-align: left;
}

table th {
    background-color: #f2f2f2; /* Light gray */
}

table td input[type="text"] {
    width: calc(100% - 20px); /* Adjusted width for input */
    padding: 2px;
    box-sizing: border-box;
    border-radius: 3px;
}

table td button {
    padding: 8px 12px;
    font-size: 12px;
    cursor: pointer;
    margin-right: 5px;
    border: none;
    border-radius: 3px;
}

table td button.edit-btn {
    background-color: #2196F3; /* Blue */
    color: white;
}

table td button.edit-btn:hover {
    background-color: #0b7dda; /* Darker blue on hover */
}

table td button.save-btn {
    background-color: #4CAF50; /* Green */
    color: white;
}

table td button.save-btn:hover {
    background-color: #45a049; /* Darker green on hover */
}

table td button.cancel-btn {
    background-color: #f44336; /* Red */
    color: white;
}

table td button.cancel-btn:hover {
    background-color: #da190b; /* Darker red on hover */
}

.table {
    overflow-x: auto;
}

.status-present {
    background-color: green;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
}

.status-od {
    background-color: grey;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
}

.status-absent {
    background-color: red;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
}

.legend {
    text-align: right;
    margin-bottom: 10px;
    margin-left: 800px;
}

@media(max-width: 800px) {
    .legend {
        margin-left: 50px;
    }
}

.percentage-green {
    background-color: green;
    color: white;
}

.percentage-yellow {
    background-color: yellow;
    color: black;
}

.percentage-red {
    background-color: red;
    color: white;
}
</style>

<body>

<form method="get" class='form' action="">
    <div>
        <label for="regNo">Filter by RegNo:</label>
        <input type="text" id="regNo" name="regNo" value="<?php echo isset($_GET['regNo']) ? htmlspecialchars($_GET['regNo']) : ''; ?>">
        <input type="hidden" name="subject_code" value="<?php echo htmlspecialchars($subject_code); ?>">
        <button type="submit">Filter</button>
    </div>

    <div class="legend">
        <p>1 - <span class="status-present">Present</span>
            2 - <span class="status-od">OD</span>
            0 - <span class="status-absent">Absent</span></p>
    </div>
</form>
<div class="table">
    <table>
        <thead>
            <tr>
                <?php
                // Fetch the column names
                $field_info = mysqli_fetch_fields($result);
                foreach ($field_info as $field) {
                    echo "<th>" . htmlspecialchars($field->name) . "</th>";
                }
                ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Display the rows
        while ($row = mysqli_fetch_assoc($result)) {
            $rowId = htmlspecialchars($row['RegNo']); // Assuming 'RegNo' is a column in your table
            if (isset($_GET['regNo']) && !empty($_GET['regNo']) && $_GET['regNo'] != $rowId) {
                continue;
            }

            echo "<tr id='row-$rowId'>";
            foreach ($row as $key => $value) {
                $editable = !in_array($key, ['percentage', 'Name', 'RegNo', 'dayspresent', 'daysabsent']);
                $disabled = $editable ? '' : ' disabled';
                
                // Check for the status values and add appropriate CSS class and icon
                if ($editable) {
                    $class = '';
                    $displayValue = '';
                    switch ($value) {
                        case '1':
                            $class = 'status-present';
                            $displayValue = '&#10004;'; // Green tick
                            break;
                        case '2':
                            $class = 'status-od';
                            $displayValue = '&#10004;'; // Grey tick
                            break;
                        case '0':
                            $class = 'status-absent';
                            $displayValue = '&#10008;'; // Red cross
                            break;
                        default:
                            $displayValue = htmlspecialchars($value);
                    }
                    echo "<td><span class='$class tick'>$displayValue</span><input type='text' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "' data-original-value='" . htmlspecialchars($value) . "'$disabled style='display:none;'></td>";
                } else {
                    // Conditional styling for percentage column
                    $percentageClass = '';
                    if ($key == 'percentage') {
                        $percentageValue = floatval($value);
                        if ($percentageValue < 50) {
                            $percentageClass = 'percentage-red';
                        } elseif ($percentageValue < 70) {
                            $percentageClass = 'percentage-yellow';
                        } else {
                            $percentageClass = 'percentage-green';
                        }
                    }
                    echo "<td class='$percentageClass'><span>$value</span><input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "' data-original-value='" . htmlspecialchars($value) . "'$disabled></td>";
                }
            }
            echo "<td>";
            echo "<button type='button' class='edit-btn' id='edit-row-$rowId' onclick='enableEditing(\"row-$rowId\")'>Edit</button>";
            echo "<button type='button' class='save-btn' id='save-row-$rowId' style='display:none;' onclick='saveEditing(\"row-$rowId\")'>Save</button>";
            echo "<button type='button' class='cancel-btn' id='cancel-row-$rowId' style='display:none;' onclick='cancelEditing(\"row-$rowId\")'>Cancel</button>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>

<footer>
    <?php include('../../footer.php'); ?>
</footer>
</html>
