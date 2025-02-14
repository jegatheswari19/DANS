<?php
include('../db_con.php');
include('sidebar.php');

if (!isset($_GET['semester'])) {
    echo "Semester not specified.";
    exit;
}

$semester = $_GET['semester'];

// Fetch OEC choices for the specified semester
$query = "SELECT RegNo, Name, Department, Status, allotted, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8` FROM oec_choices WHERE Sem=? AND status LIKE '%waiting%'";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $semester);
$stmt->execute();
$result = $stmt->get_result();

$oec_choices = [];
while ($row = $result->fetch_assoc()) {
    $oec_choices[] = $row;
}
$stmt->close();

// Load course data from JSON file
$json_data = file_get_contents('course_data.json');
$course_data = json_decode($json_data, true);

// Determine the minimum number of choices required
$min_choices = isset($course_data[$semester]['course_choices']) ? intval($course_data[$semester]['course_choices']) : 8;

$query_subjects = "SELECT subject_code, name FROM oec where sem=$semester";
$result_subjects = $con->query($query_subjects);

$subjects = [];
while ($row_subjects = $result_subjects->fetch_assoc()) {
    $subjects[$row_subjects['subject_code']] = $row_subjects['name'];
}

// Handle form submission for moving a student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['move'])) {
    $regNo = $_POST['regNo'];
    $new_subject = $_POST['new_subject'];

    // Update the oec_choices table
    $update_query = "UPDATE oec_choices SET Status='selected', allotted=? WHERE RegNo=?";
    $stmt = $con->prepare($update_query);
    $stmt->bind_param('ss', $new_subject, $regNo);
    $stmt->execute();
    $stmt->close();

    // Insert data into the new subject table
    $insert_query = "INSERT INTO $new_subject (RegNo, Name) SELECT RegNo, Name FROM oec_choices WHERE RegNo=?";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param('s', $regNo);
    $stmt->execute();
    $stmt->close();

    // Refresh the page using JavaScript
    echo "<script>location.reload();</script>";
    exit;
}

// Handle form submission for deleting a student from a subject table
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_subject'])) {
    $regNo = $_POST['regNo'];
    $subject_code = $_POST['subject_code'];

    // Update oec_choices table status and allotted fields
    $update_status_query = "UPDATE oec_choices SET Status='waiting_p', allotted=NULL WHERE RegNo=?";
    $stmt = $con->prepare($update_status_query);
    $stmt->bind_param('s', $regNo);
    $stmt->execute();
    $stmt->close();

    // Delete subject table
    $delete_subject_query = "DROP TABLE IF EXISTS $subject_code";
    $con->query($delete_subject_query);

    // Assuming $subject_code is a valid and sanitized variable
    $delete_subject_query = "DELETE FROM oec WHERE Subject_code = ?";
    $stmt = $con->prepare($delete_subject_query);
    $stmt->bind_param('s', $subject_code); // Assuming Subject_code is a string (adjust 's' if it's an integer)
    $stmt->execute();
    $stmt->close();

    // Refresh the page using JavaScript
    echo "<script>location.reload();</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OEC Allotment</title>
    <style>
        body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    height: 100vh;
    background-color: #f4f6f9;
}

.body {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 50px;
    flex-direction: column;
}

.content {
    padding: 20px;
    border-radius: 8px;
    color: #333;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 1200px;
    text-align: center;
    background-color: #fff;
}

.content h1 {
    font-size: 2rem;
    margin-bottom: 20px;
    color: #1976d2;
}

.content table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    table-layout: fixed;
}

.content table, th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

.content th {
    background-color: #1976d2;
    color: #fff;
    font-weight: bold;
}

.content td {
    background-color: #f9f9f9;
}

.content td:nth-child(odd) {
    background-color: #f4f6f9;
}

.content .subjects {
    margin: 20px 0;
    padding: 20px;
    border-radius: 8px;
    color: #333;
    background-color: #e0e7ff;
}

.subjects h2 {
    font-size: 1.5rem;
    color: #1976d2;
    margin-bottom: 10px;
}

.subjects h3 {
    font-size: 1.25rem;
    margin-top: 10px;
    color: #333;
}

button {
    background-color: #1976d2;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #125a9e;
}

select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

form {
    display: inline-block;
}

    </style>
</head>
<body>
    <div class="body">
        <div class="content">
            <h1>OEC Allotment for Semester <?php echo htmlspecialchars($semester); ?></h1>
            <table>
                <thead>
                    <tr>
                        <th>RegNo</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Allotted</th>
                        <th>Allotted course name</th>
                        <?php for ($i = 1; $i <= $min_choices; $i++): ?>
                            <th>Choice <?php echo $i; ?></th>
                        <?php endfor; ?>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($oec_choices as $choice): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($choice['RegNo']); ?></td>
                            <td><?php echo htmlspecialchars($choice['Name']); ?></td>
                            <td><?php echo htmlspecialchars($choice['Department']); ?></td>
                            <td><?php echo htmlspecialchars($choice['Status']); ?></td>
                            <td><?php echo htmlspecialchars($choice['allotted']); ?></td>
                            <td><?php echo htmlspecialchars(isset($subjects[$choice['allotted']]) ? $subjects[$choice['allotted']] : 'N/A'); ?></td>
                            <?php for ($i = 1; $i <= $min_choices; $i++): ?>
                                <td><?php echo htmlspecialchars($choice[$i]); ?></td>
                            <?php endfor; ?>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="regNo" value="<?php echo htmlspecialchars($choice['RegNo']); ?>">
                                    <select name="new_subject" required>
                                        <option value="">Select Subject</option>
                                        <?php foreach ($subjects as $subject_code => $subject_name): ?>
                                            <option value="<?php echo htmlspecialchars($subject_code); ?>"><?php echo htmlspecialchars($subject_name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="move">Move</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="subjects">
                <h2>Available OEC Subjects</h2>
                <?php foreach ($subjects as $subject_code => $subject_name): ?>
                    <h3><?php echo htmlspecialchars($subject_code); ?>: <?php echo htmlspecialchars($subject_name); ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>RegNo</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Assuming your table structure has columns RegNo and Name, adjust as per your actual table structure
                            $query_subject_table = "SELECT * FROM $subject_code";
                            $result_subject_table = $con->query($query_subject_table);

                            if ($result_subject_table->num_rows > 0) {
                                while ($row_subject_table = $result_subject_table->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row_subject_table['RegNo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_subject_table['Name']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2'>No data available</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php $con->close(); ?>
</body>
</html>
