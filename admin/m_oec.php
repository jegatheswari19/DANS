<?php
include('../db_con.php');
include('sidebar.php');

// Delete operation
if (isset($_GET['delid'])) {
    $sid = $_GET['delid'];
    
    // Delete the oec from the oec table
    if (mysqli_query($con, "DELETE FROM oec WHERE Subject_code ='$sid'")) {
        // Drop the corresponding table
        $dropTableQuery = "DROP TABLE IF EXISTS $sid";
        if (mysqli_query($con, $dropTableQuery)) {
            echo "<script>alert('oec and associated table deleted');</script>";
        } else {
            echo "<script>alert('oec deleted but failed to delete associated table');</script>";
        }
    } else {
        echo "<script>alert('Failed to delete oec');</script>";
    }
    
    echo "<script>window.location.href = 'm_oec.php';</script>"; // To refresh the page after deletion
}

if (isset($_POST['edit'])) {
    $eid = $_POST['id'];
    $subject_code = $_POST['subject_code'];
    $name = $_POST['name'];
    $staff = $_POST['staff'];
    $staff_id = $_POST['staff_id'];
    $sem = $_POST['sem'];
    $limit_oec = isset($_POST['limit_oec']) ? $_POST['limit_oec'] : ''; // Add check for limit_oec key
    $department = $_POST['department'];
    
    $query = "UPDATE oec SET Subject_code='$subject_code', Name='$name', Staff='$staff', Staff_id='$staff_id', Sem='$sem', limit_oec='$limit_oec', Department='$department' WHERE Subject_code='$eid'";
    if (mysqli_query($con, $query)) {
        echo "<script>alert('Data Updated');</script>";
    } else {
        echo "<script>alert('Data Update Failed');</script>";
    }
    echo "<script>window.location.href = 'm_oec.php';</script>"; // To refresh the page after editing
}

// Search operation
$department = isset($_POST['department']) ? $_POST['department'] : '';
$subject_code = isset($_POST['subject_code']) ? $_POST['subject_code'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$staff = isset($_POST['staff']) ? $_POST['staff'] : '';
$staff_id = isset($_POST['staff_id']) ? $_POST['staff_id'] : '';
$sem = isset($_POST['sem']) ? $_POST['sem'] : '';
$limit_oec = isset($_POST['limit_oec']) ? $_POST['limit_oec'] : '';

$query = "SELECT * FROM oec WHERE 1=1";

if ($subject_code != '') {
    $query .= " AND Subject_code LIKE '%$subject_code%'";
}
if ($limit_oec != '') {
    $query .= " AND limit_oec LIKE '%$limit_oec%'";
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
    <title>Manage oec</title>
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
            <form method="POST" action="m_oec.php">
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
                <p>
                    <label>limit_oec</label>
                    <input type="text" name="limit_oec" placeholder="limit_oec" value="<?php echo htmlspecialchars($limit_oec); ?>" />
                </p>
                <p style="align-self: center; flex: 0 0 auto;">
                    <input type="submit" name="search" value="Search" />
                </p>
            </form>
        </div>

        <div class="tables">
            <h3 class="title1">Manage oec</h3>
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
                            <th>limit_oec</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($ret)) {
                        ?>
                            <tr id="row-<?php echo $row['Subject_code']; ?>">
                                <form method="POST" action="m_oec.php">
                                    <input type="hidden" name="id" value="<?php echo $row['Subject_code']; ?>">
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
                                        <span class="view-mode"><?php echo htmlspecialchars($row['limit_oec']); ?></span>
                                        <input type="text" name="limit_oec" class="edit-mode" value="<?php echo htmlspecialchars($row['limit_oec']); ?>" style="display:none;">
                                    </td>
                                    <td>
                                        <button type="button" class="edit-button">Edit</button>
                                        <input type="submit" name="edit" class="save-button" value="Save" style="display:none;">
                                        <a href="m_oec.php?delid=<?php echo $row['Subject_code']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this oec?');">Delete</a>
                                    </td>
                                </form>
                            </tr>
                        <?php
                            $cnt++;
                        }
                        ?>
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
                button.style.display = 'none';
                row.querySelector('.save-button').style.display = 'block';
            });
        });
    </script>
</body>

</html>
