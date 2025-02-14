<?php
include('../db_con.php');
include('sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Semester</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
            height: 100vh;
        }

        .body {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 200px;
        }

        .content {
            background-color: #0e2242;
            padding: 20px;
            border-radius: 8px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 50%;
            text-align: center;
        }

        .content select {
            padding: 10px;
            font-size: 16px;
            border-radius: 4px;
            border: none;
            outline: none;
        }

        .content button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            border: none;
            background-color: #1976d2;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="body">
        <div class="content">
            <h1>Select Semester</h1>
            <form action="oec_allotment.php" method="GET">
                <select name="semester" required>
                    <option value="">Select Semester</option>
                    <?php
                    // Assuming you have semesters from 1 to 8
                    for ($i = 1; $i <= 8; $i++) {
                        echo "<option value='$i'>Semester $i</option>";
                    }
                    ?>
                </select>
                <br>
                <button type="submit">View OEC Process</button>
            </form>
        </div>
    </div>
</body>
</html>
