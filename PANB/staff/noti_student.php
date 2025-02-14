<?php
// Start the session to access session variables
include("../../db_con.php"); // Include the database connection
include("../../staff/staff_nav.php");
$staff_id = $_SESSION['uid'];
 // Include the staff navigation

// Get subject_code from URL

$subject_code = $_GET['subject_code'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link = $_POST['link'] ?? null;
    $instructions = $_POST['instructions'] ?? null;
    $batch = $_POST['batch'] ?? null;
    
    // Fetch staff information using session uid
    $staff_id = $_SESSION['uid'];
    $stmt = $con->prepare("SELECT * FROM staff WHERE Staff_id = ?");
    $stmt->bind_param("s", $staff_id);
    $stmt->execute();
    $staff_info = $stmt->get_result()->fetch_assoc();

    
    // Fetch subject information
    $stmt = $con->prepare("SELECT * FROM course WHERE Subject_code = ?");
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();
    $subject_info = $stmt->get_result()->fetch_assoc();
    $description = "Staff: " . $staff_info['Name'] . " (ID: " . $staff_info['Staff_id'] . "), Subject: " . $subject_info['Name'] . " (Code: " . $subject_code . ")";
   
    if ($instructions) {
        $description .= ", Instructions: " . $instructions;
    }
    
    // Fetch batch information
    $stmt = $con->prepare("SELECT Sem, Department FROM course WHERE subject_code = ?");
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();
    $batch_info = $stmt->get_result()->fetch_assoc();

    $sem = $batch_info['Sem'];
    $department = $batch_info['Department'];
    $datetime = date('Y-m-d H:i:s');
    
    // Insert into notification table
    $stmt = $con->prepare("INSERT INTO notification (datetime, description, link, sem, Department, RegNo, Batch) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $reg_no = null; // Assuming RegNo is null, you can modify this if you have a value for it
    $stmt->bind_param("ssssssi", $datetime, $description, $link, $sem, $department, $reg_no, $batch);
    $stmt->execute();
    
    echo "Notification inserted successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notification Form</title>
    <style>
          body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
        background-image: url('../../images/back2.jpg'); 
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
  .content{
    display:flex;
    justify-content: center;
    align-items:center;
    margin-top:60px;
  }
        .form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .form-container label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .form-container input[type="url"],
        .form-container input[type="text"],
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container button {
            background-color: #5cb85c;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #4cae4c;
        }
       
        footer {
            margin-top: 20px;
            color: white;
            text-align: center;
            width: 100%;
            bottom: 0;
            position:fixed;
        }
    </style>
</head>
<body>
    <div class="content">
    <div class="form-container">
        <h2>Notification Form</h2>
        <form method="POST">
            <label for="instructions">Instructions:</label>
            <input type="text" name="instructions" id="instructions" required>
            
            <label for="link">Link (optional):</label>
            <input type="url" name="link" id="link">
            
            <label for="batch">Batch:</label>
            <select name="batch" id="batch">
                <option value="">Select a batch</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
            </select>
            
            <button type="submit">Submit</button>
        </form>
    </div>

    <footer>
        <?php include('../../student/student_footer.php'); ?>
    </footer>
</body>
</div>
</html>
