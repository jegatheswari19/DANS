<?php
include('../../db_con.php'); // Adjust the path as per your file structure

session_start();

if (!isset($_SESSION['uid'])) {
    die("No registration number found in session.");
}

$RegNo = $_SESSION['uid'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $choices = $_POST['choices'];
    $subject_codes = $_POST['subject_code'];

    // Delete existing choices for the student
    $delete_query = "DELETE FROM oec_choices WHERE RegNo = ?";
    $delete_stmt = $con->prepare($delete_query);
    $delete_stmt->bind_param('s', $RegNo);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Fetch the student's semester, department, and name
    $query = "SELECT Name, Sem, Department FROM student WHERE RegNo=?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $RegNo);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject_row = $result->fetch_assoc();
    $stmt->close();

    // Check if the query returned a valid result
    if (!$subject_row) {
        die("Error fetching student details.");
    }

    $name = $subject_row['Name'];
    $Sem = $subject_row['Sem'];
    $Department = $subject_row['Department'];

    // Insert new choices
    $insert_query = "INSERT INTO oec_choices (RegNo, Name, Sem, Department, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $con->prepare($insert_query);

    // Initialize columns array
    $columns = array_fill(0, 8, null);
    foreach ($choices as $index => $choice) {
        if ($choice > 0 && $choice <= 8) {
            $columns[$choice - 1] = $subject_codes[$index];
        }
    }

    $insert_stmt->bind_param('ssisssssssss', $RegNo, $name, $Sem, $Department, ...$columns);
    $insert_stmt->execute();

    // Check for successful insertion
    if ($insert_stmt->affected_rows > 0) {
        echo "<script>alert('Choices saved successfully');</script>";
        
        // Call the stored procedure insert_oec_choices_procedure
        $call_proc_query = "CALL insert_oec_choices_procedure(?, ?, ?)";
        $call_proc_stmt = $con->prepare($call_proc_query);
        $call_proc_stmt->bind_param('sss', $RegNo, $name, $Department);
        $call_proc_stmt->execute();
        $call_proc_stmt->close();
        
    } else {
        echo "<script>alert('Error saving choices');</script>";
    }

    $insert_stmt->close();
    echo "<script>window.location.href = 'view_choices.php';</script>";
}

$con->close();
?>
