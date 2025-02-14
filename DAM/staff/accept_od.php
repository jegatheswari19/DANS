<?php
include("../../db_con.php");

include('../../staff/staff_nav.php');




if (isset($_POST['accept'])) {
    $subject_code = $_POST['Subject_code'];
    $date = $_POST['Date'];
    $hour = $_POST['Hour'];
    $reg_no = $_POST['RegNo'];

    // Format the date to the required format
    $date_format = date('d_m_Y', strtotime($date));
    $column_name = $date_format . '_' . $hour;

    // Check if the column exists in the subject_code table
    $column_check_sql = "SHOW COLUMNS FROM `$subject_code` LIKE '$column_name'";
    $column_check_result = mysqli_query($con, $column_check_sql);

    if (mysqli_num_rows($column_check_result) > 0) {
        // Update the column value for the given RegNo
        $update_sql = "UPDATE `$subject_code` SET `$column_name` = 2 WHERE RegNo = '$reg_no'";
        if (mysqli_query($con, $update_sql)) {
            // Update status column in od table to 'approved'
            $update_od_status_sql = "UPDATE od SET status = 'approved' WHERE RegNo = '$reg_no' AND Subject_code = '$subject_code'";
            if (!mysqli_query($con, $update_od_status_sql)) {
                echo "Error updating OD status: " . mysqli_error($con);
                exit();
            }

            // Fetch all columns from subject_code table except specific non-attendance columns
            $fetch_data_sql = "SELECT * FROM $subject_code WHERE RegNo = '$reg_no'";
            $fetch_result = mysqli_query($con, $fetch_data_sql);

            if ($fetch_result) {
                $days_present = 0;
                $days_absent = 0;
                $total_columns = 0;

                // Loop through each row of fetched data
                while ($row = mysqli_fetch_assoc($fetch_result)) {
                    foreach ($row as $key => $value) {
                        // Exclude non-attendance columns
                        if ($key !== 'RegNo' && $key !== 'Name' && $key !== 'percentage' && $key !== 'daysabsent' && $key !== 'dayspresent') {
                            if ($value == 1 || $value == 2) {
                                $days_present++;
                            } elseif ($value == 0) {
                                $days_absent++;
                            }
                        }
                    }
                }

                // Update dayspresent and daysabsent for the RegNo
                $update_days_sql = "UPDATE $subject_code SET dayspresent = $days_present, daysabsent = $days_absent WHERE RegNo = '$reg_no'";
                if (!mysqli_query($con, $update_days_sql)) {
                    echo "Error updating dayspresent and daysabsent: " . mysqli_error($con);
                    exit();
                }

                // Calculate percentage
                $total_columns = mysqli_num_fields($fetch_result) - 5; // Total columns excluding non-attendance columns
                if ($total_columns > 0) {
                    $percentage = ($days_present / $total_columns) * 100;

                    // Update percentage in the database
                    $update_percentage_sql = "UPDATE $subject_code SET percentage = $percentage WHERE RegNo = '$reg_no'";
                    if (!mysqli_query($con, $update_percentage_sql)) {
                        echo "Error updating percentage: " . mysqli_error($con);
                        exit();
                    }

                    echo "OD request accepted successfully. Attendance percentage updated.";
                } else {
                    echo "Error: No attendance columns found.";
                }
            } else {
                echo "Error fetching data: " . mysqli_error($con);
            }
        } else {
            echo "Error updating record: " . mysqli_error($con);
        }
    }else {
        // Column does not exist, reject the OD request
        echo "Column $column_name does not exist in table $subject_code. Rejecting OD request.";

        // Fetch student information
        $get_student_info_sql = "SELECT Sem, Department FROM student WHERE RegNo = '$reg_no'";
        $student_info_result = mysqli_query($con, $get_student_info_sql);

        if ($student_info_result && mysqli_num_rows($student_info_result) > 0) {
            $student_info = mysqli_fetch_assoc($student_info_result);
            $sem = $student_info['Sem'];
            $department = $student_info['Department'];

            // Insert notification for rejection
            $datetime = date('Y-m-d H:i:s');
            $insert_notification_sql = "INSERT INTO notification (datetime, description, sem, Department, RegNo) VALUES ('$datetime', 'OD rejected  for $subject_code as no class in that $date', '$sem', '$department', '$reg_no')";
            if (mysqli_query($con, $insert_notification_sql)) {
                echo " Notification inserted for rejection.";

                // Update status column in od table to 'rejected'
                $update_od_status_sql = "UPDATE od SET status = 'rejected' WHERE RegNo = '$reg_no' AND Subject_code = '$subject_code'";
                if (!mysqli_query($con, $update_od_status_sql)) {
                    echo "Error updating OD status: " . mysqli_error($con);
                    exit();
                }
            } else {
                echo "Error inserting notification: " . mysqli_error($con);
            }
        } else {
            echo "Error fetching student information: " . mysqli_error($con);
        }
    }

    mysqli_close($con);
} else {
    echo "Invalid request.";
}
?>