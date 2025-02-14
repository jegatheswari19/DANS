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
// Fetch allotted courses for the current semester and department
$query = "SELECT Subject_code, Name FROM course WHERE Sem=? AND Department=? AND type='pe'";
$stmt = $con->prepare($query);
$stmt->bind_param('is', $sem, $department);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
$stmt->close();
$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allotted Professional Electives</title>
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

        .body{
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top:200px;
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
        footer {
            margin-top: 20px;
            color: white;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        .content h1 {
            margin-bottom: 20px;
        }
        .course-list {
            list-style: none;
            padding: 0;
        }
        .course-item {
            background-color:#e6ffff;
            margin: 10px 0;
            color:black;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="body">
    <div class="content">
        <h1>Allotted Professional Electives for This Semester</h1>
        <?php if (count($courses) > 0): ?>
            <ul class="course-list">
                <?php foreach ($courses as $course): ?>
                    <li class="course-item">
                        <strong><?php echo htmlspecialchars($course['Subject_code']); ?></strong>: 
                        <?php echo htmlspecialchars($course['Name']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No professional electives allotted for this semester.</p>
        <?php endif; ?>
        </div>
    </div>
    <footer>
        <?php include('../../student/student_footer.php'); ?>
    </footer>
</body>
</html>
