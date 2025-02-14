<?php
include('../db_con.php');
include('./student_nav.php');

// Check if RegNo is set in session
if(isset($_SESSION['uid'])) {
    $RegNo = $_SESSION['uid'];

    // Query student table to get Sem, Department, and batch
    $result_student = mysqli_query($con, "SELECT Sem, Department, batch FROM student WHERE RegNo = '$RegNo'");
    if (!$result_student) {
        echo "Error: " . mysqli_error($con);
        exit();
    }
    
    // Fetch Sem, Department, and batch
    $row_student = mysqli_fetch_assoc($result_student);
    $sem = $row_student['Sem'];
    $department = $row_student['Department'];
    $batch = $row_student['batch'];
    
    ?>
    

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
        <style>
           body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
        background-image: url('../images/back2.jpg'); 
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    h1, h2 {
        text-align: center;
        color:  #002a5d;
        margin: 20px;
    }

    .row {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        justify-content: center;
        flex-grow: 1;
        margin: 20px;
    }

    .timetable, .notifications {
        margin: 10px;
        padding: 40px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        background-color: #ffffff;
    }

    .timetable {
        width: 80%;
        padding: 20px;

    }

    .timetable table {
        width: 100%;
        border-collapse: collapse;
    }

    .timetable th, .timetable td {
        border: 1px solid #002a5d;
        padding: 10px; /* Increase padding to make columns larger */
        text-align: center;
        width: 100px; /* Adjust the width to your preference */
        font-size: 16px;
        height:50%; /* Increase font size for better readability */
    }

    .timetable th {
        background-color: #002a5d;
        color: #ffffff;
    }

    .timetable tbody tr:nth-child(odd) {
        background-color: #ffffff;
    }

    .timetable tbody tr:nth-child(even) {
        background-image: url('../images/back2.jpg'); 
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .vertical-text {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        white-space: nowrap;
        padding: 10px;
        margin: 0px;
        background-image: url('../images/back2.jpg'); /* Replace 'path_to_your_background_image.jpg' with your image path */
        background-size: cover;
        color: #002a5d;
    }

    .notifications {
        width: 35%;
        padding: 10px;
        background-color: white;
        color: #002a5d;
        overflow-y: auto;
        text-align: center;
        max-height: 600px; /* Set a maximum height for scrolling */
    }

    .notification-card {
        background-color: #002a5d;
        color: white;
        border: 1px solid #66bb6a;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .notification-card h3 {
        margin-top: 0;
    }

    .go-now-btn {
        background-color: #FFD700;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        display: inline-block;
        margin-top: 10px;
    }
 footer {
            margin-top: 40px;
            color: white;
            text-align: center;
            width: 100%;
            bottom: 0;
            position:fixed;
        }

    @media only screen and (max-width: 1024px) {
        .row {
            flex-direction: column;
            align-items: center;
        }

        .timetable, .notifications {
            width: 90%;
            margin: 10px 0;
        }

        .timetable table {
            overflow-y: auto;
            overflow-x: auto;
        }

        .timetable th, .timetable td {
            padding: 8px;
        }
    }

        </style>
    </head>

    <body>
        <div class="row">
            <div class="notifications">
                <h2>Notifications</h2>
                <?php

    // Step 1: Retrieve notifications based on Sem and RegNo
    $query = "SELECT datetime, description, link, Department FROM notification 
    WHERE (Sem = ? AND (Department is null or Department=?) AND(RegNo IS NULL OR RegNo = ?) AND (batch IS NULL OR batch = ?))";
if ($stmt = $con->prepare($query)) {
$stmt->bind_param('issi', $sem,$department, $RegNo, $batch);
$stmt->execute();
$result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Array to store notifications
            $notifications = [];

            // Fetch all notifications matching Sem and RegNo
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }

            // Step 2: Filter notifications based on Department (NULL or specific)
            echo '<div class="notifications-container">';
            foreach ($notifications as $notification) {
                // Check if Department is NULL or matches $department
                if ($notification['Department'] === null || $notification['Department'] === $department) {
                    echo '<div class="notification-card">';
                    echo '<h3>' . htmlspecialchars($notification['datetime']) . '</h3>';
                    echo '<p>' . htmlspecialchars($notification['description']) . '</p>';
                    if (!empty($notification['link'])) {
                        echo '<a href="' . htmlspecialchars($notification['link']) . '" class="go-now-btn">Go Now</a>';
                    }
                    echo '</div>';
                }
            }
            echo '</div>';
        } else {
            echo '<p>No notifications found.</p>';
        }

        $stmt->close();
    } else {
        echo '<p>Error fetching notifications: ' . $con->error . '</p>';
    }

    $con->close(); // Close the database connection
    ?>

                
            </div>
            <div class="timetable">
                <h1>TIME TABLE</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>9:20 - 10:10</th>
                            <th>10:10 - 11:00</th>
                            <th>11:00 - 11:10</th>
                            <th>11:10 - 12:00</th>
                            <th>12:00 - 12:50</th>
                            <th>12:50 - 1:40</th>
                            <th>1:40 - 2:30</th>
                            <th>2:30 - 3:20</th>
                            <th>3:20 - 4:10</th>
                            <th>4:10 - 5:00</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    include('../db_con.php');

    // Check if RegNo is set in session
    if(isset($_SESSION['uid'])) {
        $RegNo = $_SESSION['uid'];

        // Query student table to get Sem, Department
        $result_student = mysqli_query($con, "SELECT Sem, Department FROM student WHERE RegNo = '$RegNo'");
        if (!$result_student) {
            echo "Error: " . mysqli_error($con);
            exit();
        }

        // Fetch Sem, Department
        $row_student = mysqli_fetch_assoc($result_student);
        $sem = $row_student['Sem'];
        $department = $row_student['Department'];

        // Query timetable based on Sem and Department
        $query = "SELECT * FROM timetable WHERE Sem=? AND Department=?";
        if ($stmt = $con->prepare($query)) {
            $stmt->bind_param('is', $sem, $department);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result) {
                echo "Error: " . mysqli_error($con);
                exit();
            }

            $days = [
                'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
            ];

            $day_count = 0;

            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['day']) . '</td>';
                echo '<td>' . htmlspecialchars($row['period_1']) . '</td>';
                echo '<td>' . htmlspecialchars($row['period_2']) . '</td>';
                
                // Only add Break rowspan once
                if ($day_count === 0) {
                    echo '<td class="vertical-text" rowspan="6">Break</td>';
                }

                echo '<td>' . htmlspecialchars($row['period_3']) . '</td>';
                echo '<td>' . htmlspecialchars($row['period_4']) . '</td>';
                
                // Only add Lunch Break rowspan once
                if ($day_count === 0) {
                    echo '<td class="vertical-text" rowspan="6">Lunch Break</td>';
                }

                echo '<td>' . htmlspecialchars($row['period_5']) . '</td>';
                echo '<td>' . htmlspecialchars($row['period_6']) . '</td>';
                echo '<td>' . htmlspecialchars($row['period_7']) . '</td>';
                echo '<td>' . htmlspecialchars($row['period_8']) . '</td>';
                echo '</tr>';
                
                $day_count++;
            }

            $stmt->close();
        } else {
            echo "Error preparing statement: " . $con->error;
        }

        mysqli_close($con);
    } else {
        // Handle case where RegNo is not set in session
        echo "RegNo is not set in session.";
    }
    ?>
</tbody>

                </table>
            </div>
        </div>
    </body>

    <footer>
        <?php include('./student_footer.php'); ?>
    </footer>

    </html>

    <?php
} else {
    // Handle case where RegNo is not set in session
    echo "RegNo is not set in session.";
}
?>
