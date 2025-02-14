<?php
include('../../student/student_nav.php');
include('../../db_con.php'); // Adjust the path as per your file structure

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
    <title>Notifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: url('../../images/back2.jpg');
            background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        }
        h1, h2 {
            text-align: center;
            color: #002a5d;
            margin: 20px;
            font-family: 'Montserrat', sans-serif;
        }

        .container {
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .notifications {
            width: 80%;
            padding: 20px;
            background-color: white;
            color: #35424a;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
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
            font-family: 'Montserrat', sans-serif;
        }

        .notification-card p {
            font-size: 1.1em;
            line-height: 1.6;
            font-family: 'Roboto', sans-serif;
        }

        .highlight {
            background-color: #FFD700;
            padding: 0 5px;
            border-radius: 3px;
            color: #002a5d;
        }

        .go-now-btn {
            background-color: #FFD700;
            color: #002a5d;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }

        footer {
            margin-top: 20px;
            color: white;
            text-align: center;
            width: 100%;
            bottom: 0;
            position:fixed;
        }
    </style>
</head>

<body>
    <div class="container">
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
    </div>

    <footer>
        <?php include('../../student/student_footer.php'); ?>
    </footer>
</body>

</html>
