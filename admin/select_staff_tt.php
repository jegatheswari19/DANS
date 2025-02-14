<!-- select_staff.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Staff Member</title>
</head>
<body>
    <h1>Select Staff Member</h1>
    
    <form action="add_staff_tt.php" method="get">
        <label for="staff_name">Select Staff:</label>
        <select id="staff_name" Name="staff_name" required>
            <?php
            include('../db_con.php'); // Include your database connection script

            // Fetch staff Names from the database
            $query = "SELECT * FROM staff";
            $result = $con->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value=\"" . htmlspecialchars($row['Name']) . "\">" . htmlspecialchars($row['Name']) . "</option>";
                }
            } else {
                echo "<option value=\"\">No staff found</option>";
            }

            $con->close();
            ?>
        </select>
        <br>
        <input type="submit" value="Select">
    </form>
</body>
</html>
