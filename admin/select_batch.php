<?php
include('../db_con.php');
include('sidebar.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department = $_POST['department'];
    $sem = $_POST['sem'];

    // Query to fetch students based on department and semester
    $query = "SELECT * FROM student WHERE department = ? AND sem = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param('ss', $department, $sem);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Batches</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .togglebtn {
            display: block;
            width: 60px;
            padding: 8px;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 auto;
        }
        .togglebtn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mark Batches for Department: <?php echo htmlspecialchars($department); ?>, Semester: <?php echo htmlspecialchars($sem); ?></h1>
        
        <form action="./mark_batches.php" method="post">
            <input type="hidden" name="department" value="<?php echo htmlspecialchars($department); ?>">
            <input type="hidden" name="sem" value="<?php echo htmlspecialchars($sem); ?>">

            <table>
                <tr>
                    <th>RegNo</th>
                    <th>Student Name</th>
                    <th>Batch</th>
                </tr>
<?php
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['RegNo']) . "</td>
                            <td>" . htmlspecialchars($row['Name']) . "</td>
                            <td>
                                <button type='button' class='togglebtn' data-student='" . htmlspecialchars($row['RegNo']) . "' data-current='" . htmlspecialchars($row['batch']) . "'>" . htmlspecialchars($row['batch']) . "</button>
                                <input type='hidden' name='batch[" . htmlspecialchars($row['RegNo']) . "]' value='" . htmlspecialchars($row['batch']) . "'>
                            </td>
                        </tr>";
                }
?>
            </table>
            <input type="submit" value="Save Batches" style="display: block; margin: 0 auto;">
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('.togglebtn');
            
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const studentId = this.getAttribute('data-student');
                    let currentBatch = parseInt(this.getAttribute('data-current'));
                    
                    // Check if currentBatch is a number
                    if (!isNaN(currentBatch)) {
                        // Toggle logic for batches 1, 2, 3
                        currentBatch = (currentBatch % 3) + 1;
                    } else {
                        // Default to 1 if currentBatch is not a number
                        currentBatch = 1;
                    }
                    
                    // Update button text and data attribute
                    this.textContent = currentBatch;
                    this.setAttribute('data-current', currentBatch);

                    // Update hidden input field value
                    const hiddenInput = document.querySelector("input[name='batch[" + studentId + "]']");
                    hiddenInput.value = currentBatch;

                    // Synchronize batches for students above the clicked one
                    synchronizeBatchesAbove(studentId, currentBatch);
                });
            });

            // Function to synchronize batches for students above the clicked one
            function synchronizeBatchesAbove(clickedStudentId, selectedBatch) {
                toggleButtons.forEach(button => {
                    const studentId = button.getAttribute('data-student');
                    const currentBatch = parseInt(button.getAttribute('data-current'));

                    // Update batches only for students above the clicked one
                    if (studentId < clickedStudentId) {
                        // Update button text and data attribute
                        button.textContent = selectedBatch;
                        button.setAttribute('data-current', selectedBatch);

                        // Update hidden input field value
                        const hiddenInput = document.querySelector("input[name='batch[" + studentId + "]']");
                        hiddenInput.value = selectedBatch;
                    }
                });
            }
        });
    </script>
</body>
</html>
<?php
        } else {
            echo "<p>No students found for this department and semester.</p>";
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error;
    }

    $con->close();
} else {
    echo "Invalid request method.";
}
?>
