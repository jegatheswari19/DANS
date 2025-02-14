<?php
include('../db_con.php');
include('sidebar.php');

// Delete operation
if (isset($_GET['delid'])) {
    $sid = $_GET['delid'];
    
    // Delete the pe from the pe table
    if (mysqli_query($con, "DELETE FROM pe WHERE Subject_code ='$sid'")) {
        // Drop the corresponding table
        $dropTableQuery = "DROP TABLE IF EXISTS $sid";
        if (mysqli_query($con, $dropTableQuery)) {
            echo "<script>alert('pe and associated table deleted');</script>";
        } else {
            echo "<script>alert('pe deleted but failed to delete associated table');</script>";
        }
    } else {
        echo "<script>alert('Failed to delete pe');</script>";
    }
    
    echo "<script>window.location.href = 'm_pe.php';</script>"; // To refresh the page after deletion
}

if (isset($_POST['edit'])) {
    $eid = $_POST['id'];
    $subject_code = $_POST['subject_code'];
    $name = $_POST['name'];
    $staff = $_POST['staff'];
    $sem = $_POST['sem'];
    $department = $_POST['department'];
    mysqli_query($con, "UPDATE pe SET Subject_code='$subject_code', Name='$name', Staff='$staff', Sem='$sem', Department='$department' WHERE Subject_code='$eid'");
    echo "<script>alert('Data Updated');</script>";
    echo "<script>window.location.href = 'm_pe.php';</script>"; // To refresh the page after editing
}

// Search operation
$subject_code = isset($_POST['subject_code']) ? $_POST['subject_code'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$staff = isset($_POST['staff']) ? $_POST['staff'] : '';
$sem = isset($_POST['sem']) ? $_POST['sem'] : '';
$department = isset($_POST['department']) ? $_POST['department'] : '';

$query = "SELECT * FROM pe WHERE 1=1";

if ($subject_code != '') {
    $query .= " AND Subject_code LIKE '%$subject_code%'";
}

if ($name != '') {
    $query .= " AND Name LIKE '%$name%'";
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
    <title>Manage pe</title>
    <!-- <link rel="stylesheet" href="./style.css"> -->

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
            <form method="POST" action="m_pe.php">
                <p>
                    <label>Subject Code</label>
                    <input type="text" name="subject_code" placeholder="Subject Code" value="<?php echo htmlspecialchars($subject_code); ?>" />
                </p>
                <p>
                    <label>Name</label>
                    <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>" />
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
            <h3 class="title1">Manage pe</h3>
            <div class="table-responsive bs-example widget-shadow">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Staff</th>
                            <th>Semester</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($ret)) {
                        ?>
                            <tr id="row-<?php echo $row['Subject_code']; ?>">
                                <form method="POST" action="m_pe.php">
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
                                        <a href="m_pe.php?delid=<?php echo $row['Subject_code']; ?>" class="btn btn-danger" onClick="return confirm('Are you sure you want to delete?')">Delete</a>
                                        <button type="button" class="btn btn-secondary cancel-button" style="display:none;">Cancel</button>
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
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
                row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'inline-block');
                row.querySelector('.edit-button').style.display = 'none';
                row.querySelector('.save-button').style.display = 'inline-block';
                row.querySelector('.cancel-button').style.display = 'inline-block';
            });
        });

        document.querySelectorAll('.cancel-button').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'inline-block');
                row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'none');
                row.querySelector('.edit-button').style.display = 'inline-block';
                row.querySelector('.save-button').style.display = 'none';
                row.querySelector('.cancel-button').style.display = 'none';
            });
        });
    </script>
</body>

<footer><?php include('footer.php'); ?></footer>

</html>
