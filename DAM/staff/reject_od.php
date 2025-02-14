<?php
include("../../db_con.php");

if (isset($_POST['reject'])) {
    $reg_no = $_POST['RegNo'];
    $subject_code = $_POST['Subject_code'];
    $date = $_POST['Date'];
    $hour = $_POST['Hour'];
    $reject_reason = mysqli_real_escape_string($con, $_POST['reject_reason']); // Sanitize the input

    // Update the status in the 'od' table
    $update_od_status_sql = "UPDATE od SET status = 'rejected' WHERE RegNo = '$reg_no' AND Subject_code = '$subject_code' AND Date = '$date' AND Hour = '$hour'";
    if (mysqli_query($con, $update_od_status_sql)) {
        
        // Fetch student information
        $get_student_info_sql = "SELECT Sem, Department FROM student WHERE RegNo = '$reg_no'";
        $student_info_result = mysqli_query($con, $get_student_info_sql);

        if ($student_info_result && mysqli_num_rows($student_info_result) > 0) {
            $student_info = mysqli_fetch_assoc($student_info_result);
            $sem = $student_info['Sem'];
            $department = $student_info['Department'];

            // Insert notification for rejection
            $datetime = date('Y-m-d H:i:s');
            $description = "OD rejected for $subject_code on $date during hour $hour due to $reject_reason";
            $insert_notification_sql = "INSERT INTO notification (datetime, description, sem, Department, RegNo) VALUES ('$datetime', '$description', '$sem', '$department', '$reg_no')";
            
            if (mysqli_query($con, $insert_notification_sql)) {
                echo "OD request rejected successfully. Notification inserted.";
            } else {
                echo "Error inserting notification: " . mysqli_error($con);
            }
        } else {
            echo "Error fetching student information: " . mysqli_error($con);
        }
    } else {
        echo "Error updating OD status: " . mysqli_error($con);
    }

    mysqli_close($con);
} else {
    echo "Invalid request.";
}
?>
