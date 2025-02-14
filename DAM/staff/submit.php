<?php
include("../../db_con.php");
include("../../staff/staff_nav.php");
 // Include the staff navigation


$subject_code = $_POST['subject_code'];
$column_name = $_POST['column_name'];
$students = $_POST['students'];

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

foreach ($students as $RegNo => $status) {
    // Retrieve current dayspresent and daysabsent values
    $result = $con->query("SELECT dayspresent, daysabsent FROM $subject_code WHERE RegNo = '$RegNo'");
    if ($result) {
        $row = $result->fetch_assoc();
        $days_present = $row['dayspresent'];
        $days_absent = $row['daysabsent'];

        // Determine which value to increment
        if ($status == '1') {
            $days_present += 1;
        } elseif ($status == '0') {
            $days_absent += 1;
        } else {
            $days_present += 1;
        }

        // Update dayspresent and daysabsent values
        $sql_update_days = "UPDATE $subject_code SET dayspresent = $days_present, daysabsent = $days_absent WHERE RegNo = '$RegNo'";
        if (!$con->query($sql_update_days)) {
            echo "Error: " . $con->error;
            exit();
        }

        // Update the column with the status value
        $sql_update_status = "UPDATE $subject_code SET $column_name = $status WHERE RegNo = '$RegNo'";
        if (!$con->query($sql_update_status)) {
            echo "Error: " . $con->error;
            exit();
        }
    } else {
        echo "Error: " . $con->error;
        exit();
    }
}

// Calculate total days
$result = $con->query("SHOW COLUMNS FROM $subject_code");
$total_columns = $result->num_rows;
$total_days = $total_columns - 5;

if ($total_days <= 0) {
    echo "Error: Not enough columns to calculate total days.";
    $con->close();
    exit();
}

// Update percentage for each student
$result = $con->query("SELECT RegNo, dayspresent FROM $subject_code");
while ($row = $result->fetch_assoc()) {
    $RegNo = $row['RegNo'];
    $days_present = $row['dayspresent'];
    $percentage = ($days_present / $total_days) * 100;

    $sql_update_percentage = "UPDATE $subject_code SET percentage = $percentage WHERE RegNo = '$RegNo'";
    if (!$con->query($sql_update_percentage)) {
        echo "Error: " . $con->error;
        exit();
    }
}

$con->close();

?>

<script>
    alert('Attendance and percentages saved successfully.');
    window.location.href = '../../staff/staff_home.php';
</script>

<footer>
    <?php include('../../footer.php'); ?>
</footer>
