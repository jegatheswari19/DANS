<?php
include('../../student/student_nav.php');
include('../../db_con.php'); // Adjust the path as per your file structure

// Retrieve RegNo from session
$RegNo = $_SESSION['uid'];

// Query student table to get Sem, Department, and Name
$result_student = mysqli_query($con, "SELECT Sem, Department, Name FROM student WHERE RegNo = '$RegNo'");
if (!$result_student) {
    echo "Error: " . mysqli_error($con);
    exit();
}

// Fetch Sem, Department, and Name
$row_student = mysqli_fetch_assoc($result_student);
$sem = $row_student['Sem'];
$department = $row_student['Department'];
$name = $row_student['Name'];

// Query course table to get subject_code and name for the student's Sem and Department
$result_courses = mysqli_query($con, "SELECT subject_code, name FROM course WHERE Sem = '$sem' AND Department = '$department'");
if (!$result_courses) {
    echo "Error: " . mysqli_error($con);
    exit();
}

// Start the HTML table with CSS styles
echo "<style>
     body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            background-image: url('../../images/back2.jpg'); /* Replace 'path_to_your_background_image.jpg' with your image path */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    .info, .table-container {
        width: 80%;
        max-width: 800px;
        margin: 20px auto;
        font-size: 20px;
        padding: 10px;
    }
    .info {
        display: flex;
        justify-content: space-between;
        align-items: center;
       
        flex-wrap: wrap;
    }
    .table-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        height: 80%;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    th, td {
        padding: 15px;
        border: 1px solid #ddd;
        text-align: center;
    }
    th {
        background-color: #002a5d; /* Green background for headers */
        color: white;
    }
    tr {
        background-color: #f2f2f2;
    }
    tr:hover {
        background-color: #ddd;
    }
    td.low-attendance {
        color: red; /* Red color for attendance percentages below 75% */
        font-weight: bold;
    }
    td.good-attendance {
        color: green; /* Green color for attendance percentages 75% and above */
        font-weight: bold;
    }
   
        footer {
            margin-top: 20px;
            color: white;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        
    }
</style>";


echo "<div class='table-container'>";

echo "<div class='info'>";
echo "<p><strong>RegNo:</strong> $RegNo</p>";
echo "<p><strong>Name:</strong> $name</p>";
echo "<p><strong>Sem:</strong> $sem</p>";
echo "<p><strong>Department:</strong> $department</p>";
echo "</div>";

echo "<table>";
echo "<tr><th>Name</th><th>Days Present</th><th>Days Absent</th><th>Percentage Attendance</th></tr>";

$total_subjects = 0;
$total_percentage = 0;
$total_days_present = 0;
$total_days_absent = 0;

// Fetch subject_codes and names
while ($row_course = mysqli_fetch_assoc($result_courses)) {
    $subject_code = $row_course['subject_code'];
    $name = $row_course['name'];

    // Query the attendance percentage for the student in this subject_code's table
    $table_name = $subject_code; // Adjust if table names have a prefix or suffix
    $result_attendance = mysqli_query($con, "SELECT percentage, dayspresent, daysabsent FROM $table_name WHERE RegNo = '$RegNo'");
    if (!$result_attendance) {
        echo "<tr><td colspan='4'>Error: " . mysqli_error($con) . "</td></tr>";
        continue; // Skip to the next subject_code if there's an error
    }

    // Fetch attendance percentage, days present, and days absent
    $row_attendance = mysqli_fetch_assoc($result_attendance);
    $percentage = $row_attendance['percentage'];
    $days_present = $row_attendance['dayspresent'];
    $days_absent = $row_attendance['daysabsent'];

    // Calculate overall percentage
    $total_percentage += $percentage;
    $total_days_present += $days_present;
    $total_days_absent += $days_absent;
    $total_subjects++;

    // Determine CSS class based on attendance percentage
    $class = ($percentage < 75) ? 'low-attendance' : 'good-attendance';

    // Display name, days present, days absent, and percentage in table rows
    echo "<tr><td>$name</td><td>$days_present</td><td>$days_absent</td><td class='$class'>$percentage%</td></tr>";
}

// Calculate overall percentage
if ($total_subjects > 0) {
    $overall_percentage = $total_percentage / $total_subjects;
} else {
    $overall_percentage = 0; // Default to 0 if no subjects found (shouldn't normally happen)
}

// Display overall percentage, total days present, and total days absent
echo "<tr class='overall'><td><b>Overall</b></td><td><b>$total_days_present</b></td><td><b>$total_days_absent</b></td><td><b>" . round($overall_percentage, 2) . "%</b></td></tr>";

// Close the table
echo "</table>";
echo "</div>";

// Close the database connection
mysqli_close($con);

// Include the footer
?>

<footer>
<?php
include('../../student/student_footer.php');
?>
</footer>
