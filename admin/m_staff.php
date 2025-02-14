<?php

include('../db_con.php');
include('sidebar.php');

// Delete operation
if (isset($_POST['delid'])) {
    $sid = $_POST['delid'];
    mysqli_query($con, "DELETE FROM Staff WHERE Staff_id ='$sid'");
    echo "<script>alert('Staff Deleted');</script>";
    echo "<script>window.location.href = 'm_staff.php';</script>"; // To refresh the page after deletion
}

if (isset($_POST['edit'])) {
    $eid = $_POST['id'];
    $name = $_POST['name'];
    $department = $_POST['department'];
    $email = $_POST['email'];
    mysqli_query($con, "UPDATE Staff SET Name='$name', Department='$department', email='$email' WHERE Staff_id='$eid'");
    echo "<script>alert('Staff Updated');</script>";
    echo "<script>window.location.href = 'm_staff.php';</script>"; // To refresh the page after editing
}


$department = isset($_POST['department']) ? $_POST['department'] : '';
$Staff_id  = isset($_POST['staff_id']) ? $_POST['staff_id'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';


$query = "SELECT * FROM staff WHERE 1=1";

if ($department != '') {
    $query .= " AND Department LIKE '%$department%'";
}

if ($name != '') {
    $query .= " AND Name LIKE '%$name%'";
}
if ($Staff_id != '') {
    $query .= " AND Staff_id LIKE '%$Staff_id%'";
}
$ret = mysqli_query($con, $query);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
    <!-- <link rel='stylesheet' href='./style.css'> -->
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



    @media (max-width: 1024px) {
        .content {
            width: 100%;
            align-items: normal;
0
        }
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
    input[type="number"],
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

            <form method="POST" action="m_staff.php">
                <p>
                    <label>Name</label>
                    <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>" />
                </p>
                <p>
                    <label>Staff ID</label>
                    <input type="text" name="staff_id" placeholder="Staff ID"
                        value="<?php echo htmlspecialchars($Staff_id); ?>" />
                </p>
                <p>
                    <label>Department</label>
                    <input type="text" name="department" placeholder="Department"
                        value="<?php echo htmlspecialchars($department); ?>" />
                </p>
                <p style="align-self: center; flex: 0 0 auto;">
                    <input type="submit" name="search" value="Search" />
                </p>
            </form>
        </div>

        <div class="tables">
            <h3 class="title1">Manage Staff</h3>
            <div class="table-responsive bs-example widPOST-shadow">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Staff ID</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($ret)) {
                        ?>
                        <tr id="row-<?php echo $row['Staff_id']; ?>">
                            <form method="POST" action="m_staff.php">
                                <input type="hidden" name="id" value="<?php echo $row['Staff_id']; ?>">
                                <th scope="row"><?php echo $cnt; ?></th>
                                <td>
                                    <span class="view-mode"><?php echo htmlspecialchars($row['Name']); ?></span>
                                    <input type="text" name="name" class="edit-mode"
                                        value="<?php echo htmlspecialchars($row['Name']); ?>" style="display:none;">
                                </td>
                                <td><?php echo htmlspecialchars($row['Staff_id']); ?></td>
                                <td>
                                    <span class="view-mode"><?php echo htmlspecialchars($row['Department']); ?></span>
                                    <input type="text" name="department" class="edit-mode"
                                        value="<?php echo htmlspecialchars($row['Department']); ?>"
                                        style="display:none;">
                                </td>
                                <td>
                                    <span class="view-mode"><?php echo htmlspecialchars($row['email']); ?></span>
                                    <input type="text" name="email" class="edit-mode"
                                        value="<?php echo htmlspecialchars($row['email']); ?>" style="display:none;">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary edit-button">Edit</button>
                                    <button type="submit" name="edit" class="btn btn-success save-button"
                                        style="display:none;">Save</button>
                                    <a href="m_staff.php?delid=<?php echo $row['Staff_id']; ?>" class="btn btn-danger"
                                        onClick="return confirm('Are you sure you want to delete?')">Delete</a>
                                    <button type="button" class="btn btn-secondary cancel-button"
                                        style="display:none;">Cancel</button>
                                </td>
                            </form>
                        </tr>
                        <?php
                            $cnt++;
                        }

                        mysqli_close($con);
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