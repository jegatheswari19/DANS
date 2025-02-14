<?php

include('../db_con.php');

if (isset($_POST['add_course'])) {
    $code = $_POST['code'];
    $sem = $_POST['sem'];
    $staff = $_POST['staff'];
    $Name = $_POST['name'];
    $dept = $_POST['dept'];
    $staff_id = $_POST['Staff_id'];
    $limit_oec = $_POST['limit_oec']; 

    $query = mysqli_prepare($con, "SELECT Subject_code FROM oec WHERE Subject_code=?");
    mysqli_stmt_bind_param($query, "s", $code);
    mysqli_stmt_execute($query);
    mysqli_stmt_store_result($query);
    $rowCount = mysqli_stmt_num_rows($query);

    if ($rowCount > 0) {
        echo "<script>alert('OEC already exists');</script>";
    } else {
        mysqli_stmt_close($query);

        // Escape `limit_oec` with backticks
        $query = mysqli_prepare($con, "INSERT INTO oec (Subject_code, Name, Staff, Sem, Department, Staff_id, `limit_oec`) VALUES (?, ?, ?, ?, ?, ?, ?)");
       
        if ($query) {
            mysqli_stmt_bind_param($query, "sssissi", $code, $Name, $staff, $sem, $dept, $staff_id, $limit_oec);
            $execute = mysqli_stmt_execute($query);

            if ($execute) {
                echo "<script>alert('Open  elective added.');</script>";
            } else {
                echo "<script>alert('Failed to execute the statement.');</script>";
                echo "Error: " . mysqli_error($con);
            }

            mysqli_stmt_close($query);
        } else {
            echo "<script>alert('Failed to prepare the statement.');</script>";
            echo "Error: " . mysqli_error($con);
        }
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
                <input type="text" name="staff" placeholder="Staff Name" />
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
                <label>limit_oec<span>*</span></label> <!-- Adjusted field name -->
                <input type="number" name="limit_oec" placeholder="limit_oec" required />
            </p>
            <p>
                <label>Staff id<span>*</span></label>
                <input type="text" name="Staff_id" placeholder="Staff_id" />
            </p>
            <p>
                <input type="submit" name="add_course" value="Add Course" />
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
