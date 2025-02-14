<?php

include('../db_con.php');

// Database connections
$servername = "127.0.0.1";
$username = "root";
$password = "Jega@2004";
$dbname1 = "dans";
$dbname2 = "dans2";

// Connect to both databases
$con1 = mysqli_connect($servername, $username, $password, $dbname1);
$con2 = mysqli_connect($servername, $username, $password, $dbname2);

if (mysqli_connect_errno()) {
    echo "Connection failed: " . mysqli_connect_error();
    exit();
}

function add_course($con, $code, $Name, $staff, $sem, $dept, $staff_id) {
    $query = mysqli_prepare($con, "SELECT Subject_code FROM Course WHERE Subject_code=?");
    mysqli_stmt_bind_param($query, "s", $code);
    mysqli_stmt_execute($query);
    mysqli_stmt_store_result($query);
    $rowCount = mysqli_stmt_num_rows($query);

    if ($rowCount > 0) {
        echo "<script>alert('Course already exists');</script>";
    } else {
        mysqli_stmt_close($query);

        $query = mysqli_prepare($con, "INSERT INTO Course (Subject_code, Name, Staff, Sem, Department, Staff_id) VALUES (?, ?, ?, ?, ?, ?)");
        if ($query) {
            mysqli_stmt_bind_param($query, "sssiss", $code, $Name, $staff, $sem, $dept, $staff_id);
            $execute = mysqli_stmt_execute($query);

            if ($execute) {
                $tableName = $code;
                $procedure = mysqli_prepare($con, "CALL insert_course_row(?,?,?)");

                if ($procedure) {
                    mysqli_stmt_bind_param($procedure, "sis", $tableName, $sem, $dept);
                    mysqli_stmt_execute($procedure);
                    mysqli_stmt_close($procedure);

                    echo "<script>alert('Course added and table created.');</script>";
                } else {
                    echo "<script>alert('Course added, but failed to create table.');</script>";
                    echo "Error: " . mysqli_error($con);
                }
            } else {
                echo "<script>alert('Something went wrong. Please try again.');</script>";
                echo "Error: " . mysqli_error($con);
            }

            mysqli_stmt_close($query);
        } else {
            echo "<script>alert('Failed to prepare the statement.');</script>";
            echo "Error: " . mysqli_error($con);
        }
    }
}

if (isset($_POST['add_course'])) {
    $code = $_POST['code'];
    $sem = $_POST['sem'];
    $staff = $_POST['staff'];
    $Name = $_POST['name'];
    $dept = $_POST['dept'];
    $staff_id = $_POST['Staff_id'];

    add_course($con1, $code, $Name, $staff, $sem, $dept, $staff_id);

    mysqli_close($con1);
}

if (isset($_POST['commit_course'])) {
    $code = $_POST['code'];
    $sem = $_POST['sem'];
    $staff = $_POST['staff'];
    $Name = $_POST['name'];
    $dept = $_POST['dept'];
    $staff_id = $_POST['Staff_id'];

    add_course($con1, $code, $Name, $staff, $sem, $dept, $staff_id);
    add_course($con2, $code, $Name, $staff, $sem, $dept, $staff_id);

    mysqli_close($con1);
    mysqli_close($con2);
}

include('sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='./style.css'>
    <title>Add Course</title>
</head>

<body>
    <div class="content">
        <h1>Add Course</h1>
        <form method="post">
            <p>
                <label>Subject code<span>*</span></label>
                <input type="text" name="code" placeholder="Subject code" required />
            </p>
            <p>
                <label>Subject Name<span>*</span></label>
                <input type="text" name="name" placeholder="Subject Name" required />
            </p>
            <p>
                <label>Staff<span>*</span></label>
                <input type="text" name="staff" placeholder="Staff Name" required />
            </p>
            <p>
                <label>Semester<span>*</span></label>
                <input type="text" name="sem" placeholder="Semester" required />
            </p>
            <p>
                <label>Department<span>*</span></label>
                <input type="text" name="dept" placeholder="Department" required />
            </p>
            <p>
                <label>Staff id<span>*</span></label>
                <input type="text" name="Staff_id" placeholder="Staff_id" required />
            </p>
            <p>
                <input type="submit" name="add_course" value="Add Course" />
                <input type="submit" name="commit_course" value="Commit" />
            </p>
        </form>
    </div>

    <script>
        const hamBurger = document.querySelector(".toggle-btn");

        hamBurger.addEventListener("click", function() {
            document.querySelector("#sidebar").classList.toggle("expand");
            document.querySelector(".content").classList.toggle("expand");
        });
    </script>
</body>
<footer>
    <?php include('footer.php'); ?>
</footer>

</html>
