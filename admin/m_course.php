<?php
include('../db_con.php');
include('sidebar.php');

// Database connections
$servername = "127.0.0.1";
$username = "root";
$password = "Jega@2004";
$dbname2 = "dans2";

// Connect to the second database
$con2 = mysqli_connect($servername, $username, $password, $dbname2);
if (mysqli_connect_errno()) {
    echo "Connection failed: " . mysqli_connect_error();
    exit();
}

// Function to check if a course exists in the second database
function course_exists_in_dans2($con2, $subject_code) {
    $query = mysqli_prepare($con2, "SELECT Subject_code FROM Course WHERE Subject_code=?");
    mysqli_stmt_bind_param($query, "s", $subject_code);
    mysqli_stmt_execute($query);
    mysqli_stmt_store_result($query);
    $rowCount = mysqli_stmt_num_rows($query);
    mysqli_stmt_close($query);
    return $rowCount > 0;
}

// Function to add a course to the second database
function add_course_to_dans2($con2, $subject_code, $name, $staff, $sem, $department, $staff_id) {
    $query = mysqli_prepare($con2, "INSERT INTO Course (Subject_code, Name, Staff, Sem, Department, Staff_id) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($query, "sssiss", $subject_code, $name, $staff, $sem, $department, $staff_id);
    $execute = mysqli_stmt_execute($query);
    mysqli_stmt_close($query);
    return $execute;
}
// Function to delete a course from both databases
function delete_course($con, $con2, $subject_code) {
    $query1 = "DELETE FROM Course WHERE Subject_code ='$subject_code'";
    $query2 = "DELETE FROM Course WHERE Subject_code ='$subject_code'";
    
    $delete1 = mysqli_query($con, $query1);
    $delete2 = mysqli_query($con2, $query2);
    
    return $delete1 && $delete2;
}

// Delete operation
if (isset($_GET['delid'])) {
    $sid = $_GET['delid'];
    
    // Delete the course from both databases
    if (delete_course($con, $con2, $sid)) {
        // Drop the corresponding table
        $dropTableQuery = "DROP TABLE IF EXISTS $sid";
        if (mysqli_query($con, $dropTableQuery)) {
            echo "<script>alert('Course and associated table deleted');</script>";
        } else {
            echo "<script>alert('Course deleted but failed to delete associated table');</script>";
        }
    } else {
        echo "<script>alert('Failed to delete course');</script>";
    }
    
    echo "<script>window.location.href = 'm_course.php';</script>"; // To refresh the page after deletion
}

// Edit operation
if (isset($_POST['edit'])) {
    $eid = $_POST['id'];
    $subject_code = $_POST['subject_code'];
    $name = $_POST['name'];
    $staff = $_POST['staff'];
    $staff_id = $_POST['staff_id'];
    $sem = $_POST['sem'];
    $department = $_POST['department'];
    
    // Update in dans2 database
    $updateQueryDans2 = mysqli_prepare($con2, "UPDATE Course SET Subject_code=?, Name=?, Staff=?, Staff_id=?, Sem=?, Department=? WHERE Subject_code=?");
    mysqli_stmt_bind_param($updateQueryDans2, "sssssss", $subject_code, $name, $staff, $staff_id, $sem, $department, $eid);
    $executeDans2 = mysqli_stmt_execute($updateQueryDans2);
    mysqli_stmt_close($updateQueryDans2);
    
    // Check if course exists in dans1
    $checkQueryDans1 = mysqli_prepare($con2, "SELECT Subject_code FROM Course WHERE Subject_code=?");
    mysqli_stmt_bind_param($checkQueryDans1, "s", $eid);
    mysqli_stmt_execute($checkQueryDans1);
    mysqli_stmt_store_result($checkQueryDans1);
    $rowCountDans1 = mysqli_stmt_num_rows($checkQueryDans1);
    mysqli_stmt_close($checkQueryDans1);
    
    // Update in dans1 database if course exists
    if ($rowCountDans1 > 0) {
        $updateQueryDans1 = mysqli_prepare($con, "UPDATE Course SET Subject_code=?, Name=?, Staff=?, Staff_id=?, Sem=?, Department=? WHERE Subject_code=?");
        mysqli_stmt_bind_param($updateQueryDans1, "sssssss", $subject_code, $name, $staff, $staff_id, $sem, $department, $eid);
        $executeDans1 = mysqli_stmt_execute($updateQueryDans1);
        mysqli_stmt_close($updateQueryDans1);
    }
    
    if ($executeDans2) {
        echo "<script>alert('Data Updated');</script>";
    } else {
        echo "<script>alert('Failed to update data');</script>";
    }
    
    echo "<script>window.location.href = 'm_course.php';</script>"; // To refresh the page after editing
}


// Commit operation
if (isset($_POST['commit'])) {
    $subject_code = $_POST['subject_code'];
    $name = $_POST['name'];
    $staff = $_POST['staff'];
    $sem = $_POST['sem'];
    $department = $_POST['department'];
    $staff_id = $_POST['staff_id'];
    
    if (add_course_to_dans2($con2, $subject_code, $name, $staff, $sem, $department, $staff_id)) {
        echo "<script>alert('Course committed to dans2');</script>";
    } else {
        echo "<script>alert('Failed to commit course to dans2');</script>";
    }
    
    echo "<script>window.location.href = 'm_course.php';</script>"; // To refresh the page after commit
}

// Search operation
$department = isset($_POST['department']) ? $_POST['department'] : '';
$subject_code = isset($_POST['subject_code']) ? $_POST['subject_code'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$staff = isset($_POST['staff']) ? $_POST['staff'] : '';
$staff_id = isset($_POST['staff_id']) ? $_POST['staff_id'] : '';
$sem = isset($_POST['sem']) ? $_POST['sem'] : '';

$query = "SELECT * FROM Course WHERE 1=1";

if ($subject_code != '') {
    $query .= " AND Subject_code LIKE '%$subject_code%'";
}

if ($name != '') {
    $query .= " AND Name LIKE '%$name%'";
}

if ($staff != '') {
    $query .= " AND Staff LIKE '%$staff%'";
}

if ($staff_id != '') {
    $query .= " AND Staff_id LIKE '%$staff_id%'";
}

if ($sem != '') {
    $query .= " AND Sem LIKE '%$sem%'";
}

if ($department != '') {
    $query .= " AND Department LIKE '%$department%'";
}

$ret = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F0F8FF;
            height: 100vh;
            margin-bottom: 20px;
        }

        .content {
            margin-left: 70px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column; 
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            overflow-x: auto;
            overflow-y: auto;
        }

        @media (max-width: 1024px) {
            .content {
                width: 100%;
                align-items: normal;
            }
        }

        th,
        td {
            padding: 7px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .search-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .search-container form {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
        }

        .search-container p {
            display: flex;
            flex-direction: column;
            margin-right: 20px;
            flex: 1;
            min-width: 150px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: #0e2238;
            color: white;
            border: none;
            width: auto;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="search-container">
            <form method="POST" action="m_course.php">
                <p>
                    <label>Subject Code</label>
                    <input type="text" name="subject_code" placeholder="Subject Code" value="<?php echo htmlspecialchars($subject_code); ?>" />
                </p>
                <p>
                    <label>Name</label>
                    <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>" />
                </p>
                <p>
                    <label>Staff</label>
                    <input type="text" name="staff" placeholder="Staff" value="<?php echo htmlspecialchars($staff); ?>" />
                </p>
                <p>
                    <label>Staff ID</label>
                    <input type="text" name="staff_id" placeholder="Staff ID" value="<?php echo htmlspecialchars($staff_id); ?>" />
                </p>
                <p>
                    <label>Semester</label>
                    <input type="text" name="sem" placeholder="Semester" value="<?php echo htmlspecialchars($sem); ?>" />
                </p>
                <p>
                    <label>Department</label>
                    <input type="text" name="department" placeholder="Department" value="<?php echo htmlspecialchars($department); ?>" />
                </p>
                <p style="align-self: center; flex: 0 0 auto;">
                    <input type="submit" name="search" value="Search" />
                </p>
            </form>
        </div>

        <div class="tables">
            <h3 class="title1">Manage Course</h3>
            <div class="table-responsive bs-example widget-shadow">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Staff</th>
                            <th>Staff ID</th>
                            <th>Semester</th>
                            <th>Department</th>
                            <th>Action</th>
                            <th>Commit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($ret)) {
                            $is_committed = course_exists_in_dans2($con2, $row['Subject_code']);
                        ?>
                            <tr id="row-<?php echo $row['Subject_code']; ?>">
                                <form method="POST" action="m_course.php">
                                    <input type="hidden" name="id" value="<?php echo $row['Subject_code']; ?>">
                                    <input type="hidden" name="subject_code" value="<?php echo htmlspecialchars($row['Subject_code']); ?>">
                                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($row['Name']); ?>">
                                    <input type="hidden" name="staff" value="<?php echo htmlspecialchars($row['Staff']); ?>">
                                    <input type="hidden" name="staff_id" value="<?php echo htmlspecialchars($row['Staff_id']); ?>">
                                    <input type="hidden" name="sem" value="<?php echo htmlspecialchars($row['Sem']); ?>">
                                    <input type="hidden" name="department" value="<?php echo htmlspecialchars($row['Department']); ?>">
                                    <th scope="row"><?php echo $cnt; ?></th>
                                    <td>
                                        <span class="view-mode"><?php echo htmlspecialchars($row['Subject_code']); ?></span>
                                        <input type="text" name="subject_code" class="edit-mode" value="<?php echo htmlspecialchars($row['Subject_code']); ?>" style="display:none;">
                                    </td>
                                    <td>
                                        <span class="view-mode"><?php echo htmlspecialchars($row['Name']); ?></span>
                                        <input type="text" name="name" class="edit-mode" value="<?php echo htmlspecialchars($row['Name']); ?>" style="display:none;">
                                    </td>
                                    <td>
                                        <span class="view-mode"><?php echo htmlspecialchars($row['Staff']); ?></span>
                                        <input type="text" name="staff" class="edit-mode" value="<?php echo htmlspecialchars($row['Staff']); ?>" style="display:none;">
                                    </td>
                                    <td>
                                        <span class="view-mode"><?php echo htmlspecialchars($row['Staff_id']); ?></span>
                                        <input type="text" name="staff_id" class="edit-mode" value="<?php echo htmlspecialchars($row['Staff_id']); ?>" style="display:none;">
                                    </td>
                                    <td>
                                        <span class="view-mode"><?php echo htmlspecialchars($row['Sem']); ?></span>
                                        <input type="text" name="sem" class="edit-mode" value="<?php echo htmlspecialchars($row['Sem']); ?>" style="display:none;">
                                    </td>
                                    <td>
                                        <span class="view-mode"><?php echo htmlspecialchars($row['Department']); ?></span>
                                        <input type="text" name="department" class="edit-mode" value="<?php echo htmlspecialchars($row['Department']); ?>" style="display:none;">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary edit-button">Edit</button>
                                        <button type="submit" name="edit" class="btn btn-success save-button" style="display:none;">Save</button>
                                        <a href="m_course.php?delid=<?php echo $row['Subject_code']; ?>" class="btn btn-danger" onClick="return confirm('Are you sure you want to delete?')">Delete</a>
                                        <button type="button" class="btn btn-secondary cancel-button" style="display:none;">Cancel</button>
                                    </td>
                                    <td>
                                        <?php if ($is_committed) { ?>
                                            <button type="button" class="btn btn-info" disabled>Committed</button>
                                        <?php } else { ?>
                                            <button type="submit" name="commit" class="btn btn-warning">Commit</button>
                                        <?php } ?>
                                    </td>
                                </form>
                            </tr>
                        <?php
                            $cnt++;
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', () => {
                const row = button.closest('tr');
                row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
                row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'block');
                row.querySelectorAll('.edit-button').forEach(el => el.style.display = 'none');
                row.querySelectorAll('.save-button').forEach(el => el.style.display = 'inline-block');
                row.querySelectorAll('.cancel-button').forEach(el => el.style.display = 'inline-block');
            });
        });

        document.querySelectorAll('.cancel-button').forEach(button => {
            button.addEventListener('click', () => {
                const row = button.closest('tr');
                row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'block');
                row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'none');
                row.querySelectorAll('.edit-button').forEach(el => el.style.display = 'inline-block');
                row.querySelectorAll('.save-button').forEach(el => el.style.display = 'none');
                row.querySelectorAll('.cancel-button').forEach(el => el.style.display = 'none');
            });
        });
    </script>
</body>

</html>
