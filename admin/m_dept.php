<?php
include('../db_con.php');
include('sidebar.php');

// Connect to the second database
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

// Function to check if a department exists in the second database
function department_exists_in_dans2($con2, $Name) {
    $query = mysqli_prepare($con2, "SELECT id FROM department WHERE Name=?");
    mysqli_stmt_bind_param($query, "s", $Name);
    mysqli_stmt_execute($query);
    mysqli_stmt_store_result($query);
    $rowCount = mysqli_stmt_num_rows($query);
    mysqli_stmt_close($query);
    return $rowCount > 0;
}

// Function to add a department to the second database
function add_department_to_dans2($con2, $Name) {
    $query = mysqli_prepare($con2, "INSERT INTO department (Name) VALUES (?)");
    mysqli_stmt_bind_param($query, "s", $Name);
    $execute = mysqli_stmt_execute($query);
    mysqli_stmt_close($query);
    return $execute;
}

// Delete operation
if (isset($_GET['delid'])) {
    $sid = $_GET['delid'];
    mysqli_query($con, "DELETE FROM department WHERE ID ='$sid'");
    echo "<script>alert('Data Deleted');</script>";
    echo "<script>window.location.href = 'm_dept.php';</script>"; // To refresh the page after deletion
}

// Edit operation
if (isset($_POST['edit'])) {
    $eid = $_POST['id'];
    $name = $_POST['name'];
    mysqli_query($con, "UPDATE department SET Name='$name' WHERE ID='$eid'");
    echo "<script>alert('Data Updated');</script>";
    echo "<script>window.location.href = 'm_dept.php';</script>"; // To refresh the page after editing
}

// Commit operation
if (isset($_POST['commit'])) {
    $Name = $_POST['dept_name'];
    
    if (add_department_to_dans2($con2, $Name)) {
        echo "<script>alert('Department committed to dans2');</script>";
    } else {
        echo "<script>alert('Failed to commit department to dans2');</script>";
    }
    
    echo "<script>window.location.href = 'm_dept.php';</script>"; // To refresh the page after commit
}

// Fetch department data for display
$ret = mysqli_query($con, "SELECT * FROM department");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Department</title>
    <link rel='stylesheet' href='./style.css'>
    <style>
    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
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

    .edit-form input[type="text"] {
        width: calc(100% - 10px);
    }

    @media (max-width: 768px) {
        .main-page {
            margin-left: 250px;
        }
    }
    </style>
</head>

<body>
    <div class="content">
        <div class="tables">
            <h3 class="title1">Manage Department</h3>
            <div class="table-responsive bs-example widget-shadow">
                <h4>Manage Department:</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($ret)) {
                            $is_committed = department_exists_in_dans2($con2, $row['Name']);
                        ?>
                        <tr id="row-<?php echo $row['ID']; ?>">
                            <form method="POST" action="m_dept.php">
                                <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                <input type="hidden" name="dept_name" value="<?php echo htmlspecialchars($row['Name']); ?>">
                                <th scope="row"><?php echo $cnt; ?></th>
                                <td>
                                    <span class="view-mode"><?php echo htmlspecialchars($row['Name']); ?></span>
                                    <input type="text" name="name" class="edit-mode" value="<?php echo htmlspecialchars($row['Name']); ?>" style="display:none;">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary edit-button">Edit</button>
                                    <button type="submit" name="edit" class="btn btn-success save-button" style="display:none;">Save</button>
                                    <a href="m_dept.php?delid=<?php echo $row['ID']; ?>" class="btn btn-danger" onClick="return confirm('Are you sure you want to delete?')">Delete</a>
                                    <button type="button" class="btn btn-secondary cancel-button" style="display:none;">Cancel</button>
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
                        }
                        ?>
                    </tbody>
                </table>
            </div>
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

<footer>
    <?php include('footer.php'); ?>
</footer>

</html>

<?php
mysqli_close($con);
mysqli_close($con2);
?>
