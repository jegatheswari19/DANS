<?php

include("../../db_con.php");
// include("../../staff/staff_nav.php"); // Include the staff navigation

$subject_code = $_GET['subject_code'];
$column_name = $_GET['column_name'];
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$sql = "SELECT RegNo, Name FROM $subject_code WHERE RegNo LIKE '%$search_query%' OR Name LIKE '%$search_query%'";
$result = $con->query($sql);

echo "<form action='' method='GET' class='search-form'>";
echo "<input type='hidden' name='subject_code' value='$subject_code'>";
echo "<input type='hidden' name='column_name' value='$column_name'>";
echo "<input type='text' name='search' placeholder='Search by RegNo or Name' value='$search_query'>";
echo "<input type='submit' value='Search'>";
echo "</form>";

if ($result->num_rows > 0) {
    echo "<form action='submit.php' method='POST' class='attendance-form'>";
    echo "<input type='hidden' name='subject_code' value='$subject_code'>";
    echo "<input type='hidden' name='column_name' value='$column_name'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='student-row'>";
        echo "<label class='student-regno'>{$row['RegNo']}</label>";
        echo "<label class='student-name'>{$row['Name']}</label>";
        echo "<input type='hidden' name='students[{$row['RegNo']}]' value='1'>";
        echo "<button type='button' class='attendance-button present' onclick='toggleAttendance(this)'>Present</button>";
        echo "</div>";
    }
    echo "<input type='submit' value='Save' class='submit-button'>";
    echo "</form>";
} else {
    echo "<p>No students found.</p>";
}

$con->close();

?>

<footer>
    <?php include('../../footer.php'); ?>
</footer>

<script>
function toggleAttendance(button) {
    const statuses = ['Present', 'Absent', 'OD'];
    const values = ['1', '0', '2'];
    let currentStatus = statuses.indexOf(button.innerHTML);
    currentStatus = (currentStatus + 1) % statuses.length;
    button.innerHTML = statuses[currentStatus];
    button.previousElementSibling.value = values[currentStatus];
    button.className = 'attendance-button ' + statuses[currentStatus].toLowerCase();
}
</script>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 20px;
}

.search-form {
    margin-bottom: 20px;
    text-align: center;
}
footer {
            margin-top: 20px;
            color: #003366;
            text-align: center;
            width: 100%;
            bottom: 0;
            position:fixed;
        }

.search-form input[type='text'] {
    padding: 10px;
    font-size: 16px;
    width: 300px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.search-form input[type='submit'] {
    padding: 10px 15px;
    font-size: 16px;
    color: #fff;
    background-color: #007bff; /* Blue for Submit */
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.attendance-form {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: auto;
}

.student-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.student-regno, .student-name {
    font-size: 16px;
}

.attendance-button {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    color: #fff;
    width: 115px;
}

.attendance-button.present {
    background-color: #28a745; /* Green for Present */
}

.attendance-button.absent {
    background-color: #dc3545; /* Red for Absent */
}

.attendance-button.od {
    background-color: #6c757d; /* Grey for OD */
}

.submit-button {
    padding: 10px 20px;
    font-size: 16px;
    color: #fff;
    background-color: #007bff; /* Blue for Submit */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: block;
    margin: 20px auto 0;
}

.submit-button:hover,
.attendance-button:hover {
    opacity: 0.9;
}
</style>
