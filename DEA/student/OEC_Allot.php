<?php

include('../../student/student_nav.php');
include('../../db_con.php'); // Adjust the path as per your file structure

if (!isset($_SESSION['uid'])) {
    die("No registration number found in session.");
}

$RegNo = $_SESSION['uid'];

// Check if the student has already voted
$query = "SELECT COUNT(*) as vote_count FROM oec_choices WHERE RegNo = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $RegNo);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['vote_count'] > 0) {
    header("Location: view_choices.php");
    exit();
}

// Fetch student semester from the database
$query = "SELECT Sem FROM student WHERE RegNo = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('s', $RegNo);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("No student found with the provided registration number.");
}

$student = $result->fetch_assoc();
$semester = $student['Sem'];

// Fetch courses for the student's semester from the 'oec' table
$query = "SELECT * FROM oec WHERE Sem = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $semester);
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Path to the JSON file
$json_file_path = '../../admin/course_data.json';

// Function to read data from JSON file
function read_json_file($file_path) {
    if (file_exists($file_path)) {
        $json_data = file_get_contents($file_path);
        return json_decode($json_data, true);
    }
    return [];
}

// Read course choices and minimum number of student from JSON file
$course_data = read_json_file($json_file_path);
$course_choices = isset($course_data[$semester]['course_choices']) ? intval($course_data[$semester]['course_choices']) : 0;
$min_students = isset($course_data[$semester]['min_students']) ? $course_data[$semester]['min_students'] : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Elective Courses</title>
    <style>
        footer {
            margin-top: 20px;
            color: white;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            background-image: url('../../images/back2.jpg');
            background-size: cover;
            background-position: center;
        }
        .table-container {
            width: 80%;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #0e2242;
            color: white;
        }
        .button {
            padding: 5px 10px;
            background-color: #FFD700;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .button:hover {
            background-color: white;
            color: #1976d2;
        }
        .submit-container {
            text-align: center;
        }
        .submit-button {
            padding: 10px 20px;
            background-color: #FFD700;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
        .submit-button:hover {
            background-color: white;
            color: #1976d2;
        }
    </style>
    <script>
        function toggleChoice(button, maxChoices) {
            let currentChoice = parseInt(button.innerHTML);
            let nextChoice = (currentChoice + 1) % (maxChoices + 1);
            
            // Ensure that each choice from 1 to maxChoices is unique
            let otherButtons = document.querySelectorAll('.choice-button');
            while (nextChoice !== 0 && Array.from(otherButtons).some(btn => parseInt(btn.innerHTML) === nextChoice && btn !== button)) {
                nextChoice = (nextChoice + 1) % (maxChoices + 1);
            }
            
            button.innerHTML = nextChoice;
            button.nextElementSibling.value = nextChoice;
            reorderTable();
        }

        function reorderTable() {
            let rows = Array.from(document.querySelectorAll('tbody tr'));
            rows.sort((a, b) => {
                let aChoice = parseInt(a.querySelector('.choice-button').innerHTML);
                let bChoice = parseInt(b.querySelector('.choice-button').innerHTML);
                if (aChoice === 0) return 1;
                if (bChoice === 0) return -1;
                return aChoice - bChoice;
            });
            let tbody = document.querySelector('tbody');
            rows.forEach(row => tbody.appendChild(row));
        }

        function validateAndSubmit(maxChoices) {
            let choices = Array.from(document.querySelectorAll('.choice-button'));
            let choiceValues = choices.map(button => parseInt(button.innerHTML));
            let uniqueChoices = new Set(choiceValues.filter(choice => choice > 0));

            if (uniqueChoices.size !== maxChoices) {
                alert(`Please select exactly ${maxChoices} unique choices.`);
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="table-container">
        <h1>Open Elective Courses for Semester <?php echo $semester; ?></h1>
        <p>Total number of choices: <?php echo $course_choices; ?></p>
        <p>Minimum number of students required: <?php echo $min_students; ?></p>
        <form action="submit.php" method="POST" onsubmit="return validateAndSubmit(<?php echo $course_choices; ?>)">
            <table>
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Choice</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course) { ?>
                        <tr>
                            <td><?php echo $course['Subject_code']; ?></td>
                            <td><?php echo $course['Name']; ?></td>
                            <td>
                                <button type="button" class="button choice-button" onclick="toggleChoice(this, <?php echo $course_choices; ?>)">0</button>
                                <input type="hidden" name="choices[]" value="0">
                                <input type="hidden" name="subject_code[]" value="<?php echo $course['Subject_code']; ?>">
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="submit-container">
                <button type="submit" class="submit-button">Save Choices</button>
            </div>
        </form>
    </div>
    
    <footer>
        <?php include('../../student/student_footer.php'); ?>
    </footer>
</body>
</html>
<?php
$con->close();
?>
