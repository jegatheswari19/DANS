<?php

include("../../db_con.php");
include("../../staff/staff_nav.php"); 

// Include the staff navigation


$subject_code = $_POST['subject_code'];
$hour = $_POST['hour'];
$date = date('d_m_Y');
$column_name = $date . '_' . $hour;

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Function to check if the column already exists
function columnExists($con, $subject_code, $column_name) {
    $checkColumnSql = "SHOW COLUMNS FROM `$subject_code` LIKE '$column_name'";
    $result = $con->query($checkColumnSql);
    return $result->num_rows > 0;
}

if (columnExists($con, $subject_code, $column_name)) {
    echo "Attendance for this date and hour has already been marked.";
} else {
    $sql = "CALL create_attendance_column('$subject_code', '$column_name')";
    if ($con->query($sql) === TRUE) {
        header("Location: at_marking.php?subject_code=$subject_code&column_name=$column_name");
    } else {
        echo "Error: " . $con->error;
    }
}

$con->close();


?>
<footer>
    <?php include('../../footer.php'); ?>
</footer>
<style>
footer{
    margin-top: 580px;
}
    </style>