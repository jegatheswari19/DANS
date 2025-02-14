<?php
include('../db_con.php');

if (isset($_POST['batch'])) {
    $batchAssignments = $_POST['batch'];

    $query = mysqli_prepare($con, "UPDATE students SET batch = ? WHERE id = ?");
    foreach ($batchAssignments as $studentId => $batch) {
        mysqli_stmt_bind_param($query, "ii", $batch, $studentId);
        mysqli_stmt_execute($query);
    }

    mysqli_stmt_close($query);
    echo "Batches assigned successfully.";
}
?>
