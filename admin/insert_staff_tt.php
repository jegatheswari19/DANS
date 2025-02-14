<?php
// insert_timetable_process.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('../db_con.php'); // Include your database connection script

    // Retrieve and sanitize inputs
    $Staff_id = isset($_POST['Staff_id']) ? $_POST['Staff_id'] : '';
    $day = isset($_POST['day']) ? $_POST['day'] : '';
    $periods = [];

    // Debug statement to check Staff_id
    echo "<p>Debug: Staff ID is " . htmlspecialchars($Staff_id) . "</p>";

    // Collect all period inputs
    for ($i = 1; $i <= 8; $i++) {
        $periods["period_$i"] = isset($_POST["period_$i"]) ? $_POST["period_$i"] : '';
    }

    // Prepare SQL statement
    $query = "INSERT INTO timetable (Department, Sem, Staff_id, day, period_1, period_2, period_3, period_4, period_5, period_6, period_7, period_8) 
              VALUES (NULL, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $con->prepare($query)) {
        // Bind parameters
        $stmt->bind_param('ssssssssss', $Staff_id, $day, 
                          $periods['period_1'], $periods['period_2'], $periods['period_3'], 
                          $periods['period_4'], $periods['period_5'], $periods['period_6'], 
                          $periods['period_7'], $periods['period_8']);
        
        // Execute statement
        if ($stmt->execute()) {
            echo "Timetable data inserted successfully.";
        } else {
            echo "Error inserting data: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error;
    }

    // Close connection
    $con->close();
} else {
    echo "Invalid request.";
}
?>
