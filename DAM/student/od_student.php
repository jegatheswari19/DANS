<?php
include('../../student/student_nav.php');
include('../../db_con.php');

// Assuming that the registration number is stored in the session as 'uid'
$RegNo = $_SESSION['uid'];

// Fetch the student's semester and department
$student_sql = "SELECT Sem, Department FROM student WHERE RegNo = '$RegNo'";
$student_result = mysqli_query($con, $student_sql);
$student_data = mysqli_fetch_assoc($student_result);
$student_sem = $student_data['Sem'];
$student_dept = $student_data['Department'];

// Fetch the Subject_code from the course table
$course_sql = "SELECT Subject_code FROM course WHERE Sem = '$student_sem' AND Department = '$student_dept' AND (type IS NULL OR type = 'pe')";
$course_result = mysqli_query($con, $course_sql);

// Fetch the Subject_code from the oec_choices table
$oec_sql = "SELECT allotted FROM oec_choices WHERE RegNo = '$RegNo'";
$oec_result = mysqli_query($con, $oec_sql);

$subject_codes = [];
while ($course_row = mysqli_fetch_assoc($course_result)) {
    $subject_codes[] = $course_row['Subject_code'];
}
while ($oec_row = mysqli_fetch_assoc($oec_result)) {
    $subject_codes[] = $oec_row['allotted'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Subject_code = $_POST['Subject_code'];
    $Date = $_POST['Date'];
    $Hour = $_POST['Hour'];
    $img = file_get_contents($_FILES['img']['tmp_name']);

    // Prepare and bind
    $stmt = $con->prepare("INSERT INTO od (RegNo, Subject_code, Date, Hour, img) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $RegNo, $Subject_code, $Date, $Hour, $img);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            background-size: cover;
            background-position: center;
            background-image: url('../../images/back2.jpg');
            height: 100vh;
        }
        .content {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 80px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="date"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
       
        footer {
            margin-top: 20px;
            color: white;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        
    }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="content">
        <form action="" method="post" enctype="multipart/form-data">
            <label for="Subject_code">Subject Code:</label>
            <select id="Subject_code" name="Subject_code" required>
                <option value="">Select Subject Code</option>
                <?php
                foreach ($subject_codes as $code) {
                    echo "<option value=\"$code\">$code</option>";
                }
                ?>
            </select><br><br>

            <label for="Date">Date:</label>
            <input type="date" id="Date" name="Date" required><br><br>

            <label for="Hour">Hour:</label>
            <input type="text" id="Hour" name="Hour" required><br><br>

            <label for="img">Upload Image:</label>
            <input type="file" id="img" name="img" accept="image/*" required><br><br>

            <input type="submit" value="Submit">
        </form>
    </div>
</body>
<footer>
    <?php include('../../student/student_footer.php'); ?>
</footer>
</html>
