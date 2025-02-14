
<?php include('sidebar.php'); ?>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department = $_POST['department'];
    $sem = $_POST['sem'];
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Timetable Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
           
            background-color: #f5f5f5;
            color: #333;
        }
        form {
            margin-bottom: 20px;
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 600px; /* Adjust form width */
            margin-left: auto;
            margin-right: auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"], select {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 60px;
            margin-left:200px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 100%; /* Adjust table width */
        }
        table, th, td {
            border: 1px solid #ddd;
            text-align: center;
        }
        th, td {
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        h1, h2 {
            margin-top: 0;
            text-align:center;
            margin:20px;
        }
    </style>
</head>
<body>
    <h1>Insert Timetable Data for Department: <?php echo htmlspecialchars($department); ?>, Semester: <?php echo htmlspecialchars($sem); ?></h1>
    
    <form action="insert_timetable_process.php" method="post">
        <input type="hidden" name="department" value="<?php echo htmlspecialchars($department); ?>">
        <input type="hidden" name="sem" value="<?php echo htmlspecialchars($sem); ?>">

        <label for="day">Day:</label>
        <select id="day" name="day" required>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
            <option value="Saturday">Saturday</option>
        </select>
        <br>

        <?php for ($i = 1; $i <= 8; $i++): ?>
            <label for="period_<?php echo $i; ?>">Period <?php echo $i; ?>:</label>
            <input type="text" id="period_<?php echo $i; ?>" name="period_<?php echo $i; ?>" required>
            <br>
        <?php endfor; ?>

        <input type="submit" value="Insert Timetable">
    </form>

    <h2>Current Timetable for Department: <?php echo htmlspecialchars($department); ?>, Semester: <?php echo htmlspecialchars($sem); ?></h2>
    <?php
    include('../db_con.php');
    $query = "SELECT * FROM timetable WHERE Department = ? AND Sem = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param('si', $department, $sem);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>Day</th>";
            for ($i = 1; $i <= 8; $i++) {
                echo "<th>Period $i</th>";
            }
            echo "</tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['day']) . "</td>";
                for ($i = 1; $i <= 8; $i++) {
                    echo "<td>" . htmlspecialchars($row["period_$i"]) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No timetable found for this department and semester.</p>";
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error;
    }

    $con->close();
    ?>
</body>

<footer><?php include('footer.php'); ?></footer>
</html>
