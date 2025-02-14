<?php
include('../../student/student_nav.php');
include('../../db_con.php'); // Adjust the path as per your file structure

// Assuming `$_SESSION['uid']` is set and represents the student's `regNo`
if (!isset($_SESSION['uid'])) {
    echo "User not logged in.";
    exit;
}

$regNo = $_SESSION['uid'];

// Fetch the student's semester and department
$query = "SELECT Sem, Department FROM student WHERE regNo=?";
$stmt = $con->prepare($query);
$stmt->bind_param('s', $regNo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Student not found.";
    exit;
}

$student = $result->fetch_assoc();
$sem = $student['Sem'];
$department = $student['Department'];
$stmt->close();

// Fetch allotted course for the current semester from the oec_choices table
$query = "SELECT allotted FROM oec_choices WHERE regNo=?";
$stmt = $con->prepare($query);
$stmt->bind_param('s', $regNo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "No OEC allotted.";
    exit;
}

$oec = $result->fetch_assoc();
$allottedCourseCode = $oec['allotted'];
$stmt->close();

// Fetch course details from the course table
$query = "SELECT Subject_code, Name FROM course WHERE Subject_code=?";
$stmt = $con->prepare($query);
$stmt->bind_param('s', $allottedCourseCode);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Course details not found.";
    exit;
}

$course = $result->fetch_assoc();
$stmt->close();
$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allotted Open Elective Course</title>
    <style>
         body {
            font-family: 'Poppins', sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('../../images/back2.jpg'); 
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        }

        footer {
            margin-top: 20px;
            color: white;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        .body {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 200px;
        }
        .content {
            
            background-color: #004d99;
            padding: 20px;
            border-radius: 8px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 50%;
            text-align: center;
        }
        .content h1 {
            margin-bottom: 20px;
        }
        .course-details {
            background-color:#e6ffff;
            color: black;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="body">
        <div class="content">
            <h1>Allotted Open Elective Course for This Semester</h1>
            <div class="course-details">
                <strong><?php echo htmlspecialchars($course['Subject_code']); ?></strong>: 
                <?php echo htmlspecialchars($course['Name']); ?>
            </div>
        </div>
    </div>
    <footer>
        <?php include('../../student/student_footer.php'); ?>
    </footer>
</body>
</html>
