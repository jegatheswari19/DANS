<!-- insert_timetable.php -->

<?php include('sidebar.php'); ?>
<?php
include('../db_con.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Timetable Data</title>
</head>
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
<body>
    <?php
    // Retrieve selected staff name from the URL query parameter
    $staff_name = $_GET['staff_name'];

    // Retrieve Staff_id based on staff name
    $query = "SELECT Staff_id FROM staff WHERE Name = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param('s', $staff_name);
        $stmt->execute();
        $stmt->bind_result($Staff_id);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error;
        exit();
    }

    echo "<h1>Insert Timetable Data for Staff: " . htmlspecialchars($staff_name) . "</h1>";
    ?>
    
    <form action="insert_staff_tt.php" method="post">
        <input type="hidden" name="Staff_id" value="<?php echo htmlspecialchars($Staff_id); ?>">
        <input type="hidden" name="staff_name" value="<?php echo htmlspecialchars($staff_name); ?>">

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
            <input type="text" id="period_<?php echo $i; ?>" name="period_<?php echo $i; ?>" >
            <br>
        <?php endfor; ?>

        <input type="submit" value="Insert Timetable">
    </form>

    <h2>Current Timetable for Staff: <?php echo htmlspecialchars($staff_name); ?></h2>
    <?php
    // Display current timetable for the selected staff member
    $query = "SELECT * FROM timetable WHERE Staff_id = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param('s', $Staff_id);
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
            echo "<p>No timetable found for this staff member.</p>";
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error;
    }

    $con->close();
    ?>
</body>
</html>
