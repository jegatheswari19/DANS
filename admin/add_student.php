<?php

include('../db_con.php');

if (isset($_POST['add_students'])) {
    $students = $_POST['students'];

    // Prepare the SQL query with the correct column names
    $query = mysqli_prepare($con, "INSERT INTO student (RegNo, Name, Sem, Department, Year, email_id, Programme) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($query) {
        foreach ($students as $student) {
            mysqli_stmt_bind_param($query, "ssisiss", $student['RegNo'], $student['Name'], $student['Sem'], $student['Department'], $student['Year'], $student['Email'], $student['Programme']);
            mysqli_stmt_execute($query);
        }
        echo "<script>alert('Students added successfully.');</script>";
        mysqli_stmt_close($query);
    } else {
        echo "<script>alert('Failed to prepare the statement.');</script>";
        echo "Error: " . mysqli_error($con);
    }

    mysqli_close($con);
}

include('sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='./style.css'>
    <title>Add Students</title>
    <style>
        
        form {
            max-width: 1200px;
            width: 100%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .student-entry {
            display: flex;
            flex-wrap: wrap;
            border-bottom: 1px solid #ccc;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .student-entry p {
            flex: 1;
            min-width: 150px;
            margin-right: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

       

        .add-student-btn,
        .remove-student-btn {
            background-color: #28a745;
            color: white;
            padding: 10px;
            cursor: pointer;
            display: inline-block;
            margin-top: 10px;
        }

        .remove-student-btn {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="content">
        <h1>Add Students</h1>
        <form method="post" id="studentForm">
            <div id="studentEntries">
                <div class="student-entry">
                    <p>
                        <label>Reg No<span>*</span></label>
                        <input type="text" name="students[0][RegNo]" placeholder="Registration Number" required />
                    </p>
                    <p>
                        <label>Name<span>*</span></label>
                        <input type="text" name="students[0][Name]" placeholder="Name" required />
                    </p>
                    <p>
                        <label>Semester<span>*</span></label>
                        <input type="number" name="students[0][Sem]" placeholder="Semester" required />
                    </p>
                    <p>
                        <label>Department<span>*</span></label>
                        <input type="text" name="students[0][Department]" placeholder="Department" required />
                    </p>
                    <p>
                        <label>Year<span>*</span></label>
                        <input type="number" name="students[0][Year]" placeholder="Year" required />
                    </p>
                    <p>
                        <label>Email<span>*</span></label>
                        <input type="email" name="students[0][Email]" placeholder="Email" required />
                    </p>
                    <p>
                        <label>Programme<span>*</span></label>
                        <input type="text" name="students[0][Programme]" placeholder="Programme" required />
                    </p>
                </div>
            </div>
            <p>
                <input type="submit" name="add_students" value="Add Students" />
            </p>
            <p>
                <span class="add-student-btn" onclick="addStudentEntry()">Add Another Student</span>
                <span class="remove-student-btn" onclick="removeStudentEntry()">Remove Last Student</span>
            </p>
        </form>
    </div>
    <footer>
        <?php include('footer.php'); ?>
    </footer>
    <script>
        let studentCount = 1;

        function addStudentEntry() {
            const studentEntries = document.getElementById('studentEntries');
            const newEntry = document.createElement('div');
            newEntry.classList.add('student-entry');
            newEntry.innerHTML = `
                <p>
                    <label>Reg No<span>*</span></label>
                    <input type="text" name="students[${studentCount}][RegNo]" placeholder="Registration Number" required />
                </p>
                <p>
                    <label>Name<span>*</span></label>
                    <input type="text" name="students[${studentCount}][Name]" placeholder="Name" required />
                </p>
                <p>
                    <label>Semester<span>*</span></label>
                    <input type="number" name="students[${studentCount}][Sem]" placeholder="Semester" required />
                </p>
                <p>
                    <label>Department<span>*</span></label>
                    <input type="text" name="students[${studentCount}][Department]" placeholder="Department" required />
                </p>
                <p>
                    <label>Year<span>*</span></label>
                    <input type="number" name="students[${studentCount}][Year]" placeholder="Year" required />
                </p>
                <p>
                    <label>Email<span>*</span></label>
                    <input type="email" name="students[${studentCount}][Email]" placeholder="Email" required />
                </p>
                <p>
                    <label>Programme<span>*</span></label>
                    <input type="text" name="students[${studentCount}][Programme]" placeholder="Programme" required />
                </p>
            `;
            studentEntries.appendChild(newEntry);
            studentCount++;
        }

        function removeStudentEntry() {
            const studentEntries = document.getElementById('studentEntries');
            if (studentEntries.children.length > 0) {
                studentEntries.removeChild(studentEntries.lastChild);
                studentCount--;
            }
        }
    </script>
</body>

</html>