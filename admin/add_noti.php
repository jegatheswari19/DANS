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

function add_notification($con, $description, $link, $sem, $dept) {
    $datetime = date('Y-m-d H:i:s'); // Get current date and time

    $query = mysqli_prepare($con, "SELECT description FROM notification WHERE description=?");
    mysqli_stmt_bind_param($query, "s", $description);
    mysqli_stmt_execute($query);
    mysqli_stmt_store_result($query);
    $rowCount = mysqli_stmt_num_rows($query);

    if ($rowCount > 0) {
        echo "<script>alert('Notification already exists');</script>";
    } else {
        mysqli_stmt_close($query);

        $query = mysqli_prepare($con, "INSERT INTO notification (datetime, description, link, sem, Department) VALUES (?, ?, ?, ?, ?)");
        if ($query) {
            mysqli_stmt_bind_param($query, "sssis", $datetime, $description, $link, $sem, $dept);
            $execute = mysqli_stmt_execute($query);

            if ($execute) {
                echo "<script>alert('Notification added.');</script>";
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

if (isset($_POST['add_notification'])) {
    $description = $_POST['description'];
    $link = $_POST['link'];
    $sem = $_POST['sem'];
    $dept = $_POST['dept'];

    add_notification($con1, $description, $link, $sem, $dept);

    mysqli_close($con1);
}

if (isset($_POST['commit_notification'])) {
    $description = $_POST['description'];
    $link = $_POST['link'];
    $sem = $_POST['sem'];
    $dept = $_POST['dept'];

    add_notification($con1, $description, $link, $sem, $dept);
    add_notification($con2, $description, $link, $sem, $dept);

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
    <title>Add Notification</title>
</head>

<body>
    <div class="content">
        <h1>Add Notification</h1>
        <form method="post">
            <p>
                <label>Description<span>*</span></label>
                <input type="text" name="description" placeholder="Description" required />
            </p>
            <p>
                <label>Link</label>
                <input type="text" name="link" placeholder="Link" />
            </p>
            <p>
                <label>Semester<span>*</span></label>
                <input type="number" name="sem" placeholder="Semester" required />
            </p>
            <p>
                <label>Department<span>*</span></label>
                <input type="text" name="dept" placeholder="Department" required />
            </p>
            <p>
                <input type="submit" name="add_notification" value="Add Notification" />
                <input type="submit" name="commit_notification" value="Commit" />
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
