<?php
include("../../db_con.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_code = isset($_POST['subject_code']) ? mysqli_real_escape_string($con, $_POST['subject_code']) : '';
    if (empty($subject_code)) {
        die("Subject code is required.");
    }

    $regNo = isset($_POST['RegNo']) ? mysqli_real_escape_string($con, $_POST['RegNo']) : '';
    if (empty($regNo)) {
        die("RegNo is required.");
    }

    // Fields that should not be updated
    $non_editable_fields = ['percentage', 'Name', 'RegNo', 'dayspresent', 'daysabsent'];

    // Create the base query for updating
    $query = "UPDATE " . mysqli_real_escape_string($con, $subject_code) . " SET ";

    // Build the query dynamically
    $updates = [];
    foreach ($_POST as $key => $value) {
        if (!in_array($key, $non_editable_fields) && $key != 'subject_code' && $key != 'action') {
            $updates[] = $key . "='" . mysqli_real_escape_string($con, $value) . "'";
        }
    }

    if (empty($updates)) {
        die("No fields to update.");
    }

    $query .= implode(", ", $updates);
    $query .= " WHERE RegNo='" . mysqli_real_escape_string($con, $regNo) . "'";

    // Execute the query
    if (mysqli_query($con, $query)) {
        echo "Row updated successfully.";

        // Calculate dayspresent and daysabsent
        $present = 0;
        $absent = 0;

        foreach ($_POST as $key => $value) {
            if ($key != 'RegNo' && $key != 'subject_code' && !in_array($key, $non_editable_fields)) {
                if ($value == 1 || $value == 2) {
                    $present++;
                } elseif ($value == 0) {
                    $absent++;
                }
            }
        }

        // Update dayspresent and daysabsent
        $update_days_query = "UPDATE " . mysqli_real_escape_string($con, $subject_code) . " 
                              SET dayspresent = $present, daysabsent = $absent 
                              WHERE RegNo='" . mysqli_real_escape_string($con, $regNo) . "'";

        if (mysqli_query($con, $update_days_query)) {
            echo " Days present and absent updated successfully.";

            // Calculate and update percentage
            $total_columns_query = "SELECT COUNT(*) AS total_columns FROM INFORMATION_SCHEMA.COLUMNS 
                                   WHERE TABLE_NAME = '" . mysqli_real_escape_string($con, $subject_code) . "'";
            $result_total_columns = mysqli_query($con, $total_columns_query);
            $row_total_columns = mysqli_fetch_assoc($result_total_columns);
            $total_columns = $row_total_columns['total_columns'];

            if ($total_columns !== false && $total_columns > 5) {
                $percentage = (($present / ($total_columns - 5)) * 100);
                $update_percentage_query = "UPDATE " . mysqli_real_escape_string($con, $subject_code) . " 
                                           SET percentage = $percentage 
                                           WHERE RegNo='" . mysqli_real_escape_string($con, $regNo) . "'";
                
                if (mysqli_query($con, $update_percentage_query)) {
                    echo " Percentage updated successfully.";
                } else {
                    echo " Error updating percentage: " . mysqli_error($con);
                }
            } else {
                echo " Error calculating percentage: Total columns count incorrect.";
            }

        } else {
            echo " Error updating days present and absent: " . mysqli_error($con);
        }
    } else {
        echo "Error updating record: " . mysqli_error($con);
    }
}
?>
