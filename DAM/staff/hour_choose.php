<?php

include("../../db_con.php");


$subject_code = $_GET['subject_code'];


if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$sql = "SELECT * FROM course WHERE subject_code = '$subject_code'";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    $course = $result->fetch_assoc();
} else {
    echo "No course found.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Form</title>
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

form {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

input[type="text"],
input[type="number"] {
    width: calc(100% - 20px);
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

input[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #28a745;
    border: none;
    border-radius: 4px;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #218838;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

    </style>
<body>
    <form action="./save_attendance.php" method="POST">
        <input type="hidden" name="subject_code" value="<?php echo $subject_code; ?>">
        <label>Course Name: </label>
        <input type="text" value="<?php echo $course['Name']; ?>" readonly><br>
        <label>Course Description: </label>
        <input type="text" value="<?php echo $course['Staff']; ?>" readonly><br>
        <label>Date: </label>
        <input type="text" value="<?php echo date('d-m-Y'); ?>" readonly><br>
        <label>Hour: </label>
        <input type="number" name="hour" required><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>


