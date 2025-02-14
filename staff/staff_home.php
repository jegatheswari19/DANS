<?php
include('../db_con.php');
include('./staff_nav.php');

$staff_id = $_SESSION['uid'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Base Styles */
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
            color: #333333;
            margin: 20px;
        }

        .row {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            margin: 20px;
        }

        .timetable, .staff-details {
            
            margin: 10px;
            padding: 40px;
            border-radius: 8px;
        }

        .timetable {
           
            margin: 10px;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: #ffffff;
        }

        .staff-details {
            width: 30%;
            display: flex;
            flex-direction: column;
            align-items: center;
          
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        .staff-details img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
            border: 3px solid #007bff; /* Blue border */
        }

        .staff-details p {
            margin: 10px 0;
            color: #333333;
            font-size: 18px;
        }

        .timetable {
            width: 65%;
            transition: transform 0.3s ease;
        }

        .timetable:hover {
            transform: scale(1.02);
            overflow-x: auto;
        }

        .timetable table {
            width: 100%;
            border-collapse: collapse;
        }

        .timetable th, .timetable td {
            border: 1px solid #003366;
            padding: 10px;
            text-align: center;
        }

        .timetable th {
            background-color: #003366;
            color: #ffffff;
        }

        .timetable tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .timetable tbody tr:nth-child(even) {
            background-color: #ececec;
        }

        .vertical-text {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
            padding: 10px;
            background-color: #f4f4f9;
            color: #003366;
        }

        footer {
            color: white;
            text-align: center;
            margin-top: 80px;
            width: 100%;
            bottom: 0;
            position: fixed;
        }

        @media only screen and (max-width: 1024px) {
            .row {
                flex-direction: column;
                align-items: center;
            }

            .timetable, .staff-details {
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
        <div class="staff-details">
            <h2>Staff Details</h2>
            <?php
            // Fetch staff details from the database
            $query = "SELECT Name, Staff_id, Department, email, img FROM Staff WHERE Staff_id = ?";
            if ($stmt = $con->prepare($query)) {
                $stmt->bind_param('s', $staff_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $img_src = htmlspecialchars($row['img']);
                    // Check if image exists
                    if (!file_exists($img_src) || empty($img_src)) {
                        $img_src = '../images/staff1.jpg'; 
                    }
                    echo '<img src="' . $img_src . '" alt="Staff Image">';
                    echo '<div class="staff-info">';
                    echo '<p>' . htmlspecialchars($row['Name']) . '</p>';
                    echo '<p>Staff id: '. htmlspecialchars($row['Staff_id']) . '</p>';
                    echo '<p>' . htmlspecialchars($row['email']) . '</p>';
                    echo '<p>Department: ' . htmlspecialchars($row['Department']) . '</p>';
                    echo '</div>';
                } else {
                    echo '<p>No staff details found.</p>';
                }

                $stmt->close();
            } else {
                echo '<p>Error fetching staff details.</p>';
            }
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
                    // Fetch timetable data from the database
                    $query = "SELECT * FROM timetable WHERE Staff_id = ?";
                    if ($stmt = $con->prepare($query)) {
                        $stmt->bind_param('s', $staff_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($row['day']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['period_1']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['period_2']) . '</td>';

                                // Only add Break rowspan once per day
                                if ($row['day'] == 'Monday') {
                                    echo '<td class="vertical-text" rowspan="6">Break</td>';
                                }

                                echo '<td>' . htmlspecialchars($row['period_3']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['period_4']) . '</td>';

                                // Only add Lunch Break rowspan once per day
                                if ($row['day'] == 'Monday') {
                                    echo '<td class="vertical-text" rowspan="6">Lunch Break</td>';
                                }

                                echo '<td>' . htmlspecialchars($row['period_5']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['period_6']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['period_7']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['period_8']) . '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="11">No timetable available.</td></tr>';
                        }

                        $stmt->close();
                    } else {
                        echo '<tr><td colspan="11">Error fetching timetable: ' . $con->error . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

<footer>
    <?php include('../footer.php'); ?>
</footer>

</html>
