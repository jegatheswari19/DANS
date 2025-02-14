<?php
include('../db_con.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department = $_POST['department'];
    $sem = $_POST['sem'];
    $batches = $_POST['batch']; 

    // Update batches in the database
    foreach ($batches as $studentId => $batch) {
        // Perform SQL update query
        $updateQuery = "UPDATE student SET batch = ? WHERE RegNo = ?";
        if ($stmt = $con->prepare($updateQuery)) {
            $stmt->bind_param('ss', $batch, $studentId);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error updating batches: " . $con->error;
        }
    }

    // Redirect to a success page or back to the marking page
    echo "batch marked";
  
    exit();

} else {
    echo "Invalid request method.";
}
?>
