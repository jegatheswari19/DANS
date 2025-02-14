<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $department = $_POST['department'];
    $sem = $_POST['sem'];
    $day = $_POST['day'];
    
    // Prepare an array to store periods data
    $periods = [];
    for ($i = 1; $i <= 8; $i++) {
        $periods[] = $_POST["period_$i"];
    }
    
    // Database connection
    include('../db_con.php');
    
    // Prepare the SQL query
    $query = "INSERT INTO timetable (Department, Sem, day, period_1, period_2, period_3, period_4, period_5, period_6, period_7, period_8) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $con->prepare($query)) {
        // Bind parameters
        $stmt->bind_param('sisssssssss', $department, $sem, $day, ...$periods);
        
        // Execute the query
        if ($stmt->execute()) {
            echo "Timetable inserted successfully.";
        } else {
            echo "Error executing query: " . $stmt->error;
        }
        
        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error;
    }
    
    // Close the database connection
    $con->close();
}
?>
