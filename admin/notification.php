<?php
include('../db_con.php');
include('sidebar.php');

// Handle form submission for editing notifications
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit'])) {
        $nid = $_POST['nid'];
        $description = $_POST['description'];

        // Update notification
        $query = "UPDATE notification SET description = ? WHERE nid = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("si", $description, $nid);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Notification updated successfully');</script>";
        echo "<script>window.location.href = 'notification_management.php';</script>"; // To refresh the page after editing
    }
}

// Fetch notifications
$query = "SELECT * FROM notification ORDER BY datetime DESC";
$result = $con->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Management</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            height: 100vh;
        }
        .content {
            display: flex;
            justify-content: center;
           
        }
        .notification {
        
            width: 80%;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .notification h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .notification table {
            width: 100%;
            border-collapse: collapse;
        }
        .notification table th,
        .notification table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .notification table th {
            background-color: #0e2238;
            color: white;
        }
        .notification table td input {
            width: 100%;
            padding: 5px;
            border: none;
            text-align: center;
        }
        .notification table td .edit-mode {
            display: none;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="notification">
            <h2>Notifications</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Description</th>
                        <th>Link</th>
                        <th>Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row-" . $row['nid'] . "'>";
                            echo "<form method='POST' action='notification_management.php'>";
                            echo "<input type='hidden' name='nid' value='" . $row['nid'] . "'>";
                            echo "<td>" . htmlspecialchars($row['datetime']) . "</td>";
                            echo "<td>
                                    <span class='view-mode'>" . htmlspecialchars($row['description']) . "</span>
                                    <input type='text' name='description' class='edit-mode' value='" . htmlspecialchars($row['description']) . "'>
                                  </td>";
                            echo "<td>" . htmlspecialchars($row['link']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sem']) . "</td>";
                            echo "<td>
                                    <button type='button' class='edit-button'>Edit</button>
                                    <button type='submit' name='edit' class='save-button' style='display:none;'>Save</button>
                                    <button type='button' class='cancel-button' style='display:none;'>Cancel</button>
                                  </td>";
                            echo "</form>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No notifications found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            row.querySelector('.view-mode').style.display = 'none';
            row.querySelector('.edit-mode').style.display = 'inline-block';
            row.querySelector('.edit-button').style.display = 'none';
            row.querySelector('.save-button').style.display = 'inline-block';
            row.querySelector('.cancel-button').style.display = 'inline-block';
        });
    });

    document.querySelectorAll('.cancel-button').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            row.querySelector('.view-mode').style.display = 'inline-block';
            row.querySelector('.edit-mode').style.display = 'none';
            row.querySelector('.edit-button').style.display = 'inline-block';
            row.querySelector('.save-button').style.display = 'none';
            row.querySelector('.cancel-button').style.display = 'none';
        });
    });
    </script>
</body>
</html>

<?php
$con->close();
?>
