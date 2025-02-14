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

function add_department($con, $Name) {
    $query = mysqli_prepare($con, "SELECT id FROM department WHERE Name=?");
    mysqli_stmt_bind_param($query, "s", $Name);
    mysqli_stmt_execute($query);
    mysqli_stmt_store_result($query);
    $rowCount = mysqli_stmt_num_rows($query);

    if ($rowCount > 0) {
        echo "<script>alert('Department already exists');</script>";
    } else {
        mysqli_stmt_close($query);

        $query = mysqli_prepare($con, "INSERT INTO department (Name) VALUES (?)");
        if ($query) {
            mysqli_stmt_bind_param($query, "s", $Name);
            $execute = mysqli_stmt_execute($query);

            if ($execute) {
                echo "<script>alert('Department added.');</script>";
            } else {
                echo "<script>alert('Something Went Wrong. Please try again.');</script>";
                echo "Error: " . mysqli_error($con);
            }

            mysqli_stmt_close($query);
        } else {
            echo "<script>alert('Failed to prepare the statement.');</script>";
            echo "Error: " . mysqli_error($con);
        }
    }
}

if (isset($_POST['add_department'])) {
    $Name = $_POST['dept'];

    add_department($con1, $Name);

    mysqli_close($con1);
}

if (isset($_POST['commit_department'])) {
    $Name = $_POST['dept'];

    add_department($con1, $Name);
    add_department($con2, $Name);

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
    <title>Add Department</title>
</head>

<body>
    <div class="content">
        <h1>Add Department</h1>
        <form method="post">
            <p>
                <label>Department<span>*</span></label>
                <input type="text" name="dept" placeholder="Department" required />
            </p>
            <p>
                <input type="submit" name="add_department" value="Add Department" />
                <input type="submit" name="commit_department" value="Commit" />
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
