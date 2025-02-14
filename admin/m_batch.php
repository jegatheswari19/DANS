<?php

include('../db_con.php');
include('sidebar.php');


// Delete operation
if (isset($_GET['delid'])) {
    $RegNo = $_GET['delid'];
    mysqli_query($con, "DELETE FROM student WHERE RegNo ='$RegNo'");
    echo "<script>alert('Student Deleted');</script>";
    echo "<script>window.location.href = 'm_student.php';</script>"; // To refresh the page after deletion
}

// Edit operation
if (isset($_POST['edit'])) {
    $RegNo = $_POST['RegNo'];
    $name = $_POST['name'];
    $sem = $_POST['sem'];
    $department = $_POST['department'];
    $year = $_POST['year'];
    $batch = $_POST['batch'];
    $programme = $_POST['programme'];
    mysqli_query($con, "UPDATE student SET Name='$name', Sem='$sem', Department='$department', year='$year', batch='$batch', Programme='$programme' WHERE RegNo='$RegNo'");
    echo "<script>alert('Student Updated');</script>";
    echo "<script>window.location.href = 'm_student.php';</script>"; // To refresh the page after editing
}

// Search operation
$department = isset($_POST['department']) ? $_POST['department'] : '';
$RegNo = isset($_POST['RegNo']) ? $_POST['RegNo'] : '';
$sem = isset($_POST['sem']) ? $_POST['sem'] : '';
$year = isset($_POST['year']) ? $_POST['year'] : '';

$query = "SELECT * FROM student WHERE 1=1";

if ($department != '') {
    $query .= " AND Department LIKE '%$department%'";
}
if ($RegNo != '') {
    $query .= " AND RegNo LIKE '%$RegNo%'";
}
if ($sem != '') {
    $query .= " AND Sem LIKE '%$sem%'";
}
if ($year != '') {
    $query .= " AND year LIKE '%$year%'";
}

$ret = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
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

    @media (max-width: 430px) {
        .content {
           width:100%;
           align-items: normal;
          
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
        <h1>Manage Students</h1>
        <div class="search-container">
            <form method="POST" action="m_student.php">
                <p>
                    <label>Department</label>
                    <input type="text" name="department" placeholder="Department"
                        value="<?php echo htmlspecialchars($department); ?>" />
                </p>
                <p>
                    <label>Reg No</label>
                    <input type="text" name="RegNo" placeholder="Registration Number"
                        value="<?php echo htmlspecialchars($RegNo); ?>" />
                </p>
                <p>
                    <label>Semester</label>
                    <input type="number" name="sem" placeholder="Semester"
                        value="<?php echo htmlspecialchars($sem); ?>" />
                </p>
                <p>
                    <label>year</label>
                    <input type="number" name="year" placeholder="year"
                        value="<?php echo htmlspecialchars($year); ?>" />
                </p>
                <p style="align-self: center; flex: 0 0 auto;">
                    <input type="submit" value="Search" />
                </p>
            </form>
        </div>

        <div class="tables">
            <h3 class="title1">Student List</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Reg No</th>
                            <th>Name</th>
                            <th>Semester</th>
                            <th>Department</th>
                            <th>year</th>
                            <th>batch</th>
                            <th>Programme</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($ret)) {
                        ?>
                        <tr id="row-<?php echo $row['RegNo']; ?>">
                            <form method="POST" action="m_student.php">
                                <input type="hidden" name="RegNo" value="<?php echo $row['RegNo']; ?>">
                                <th scope="row"><?php echo $cnt; ?></th>
                                <td>
                                    <span class="view-mode"><?php echo htmlspecialchars($row['RegNo']); ?></span>
                                    <input type="text" name="RegNo" class="edit-mode"
                                        value="<?php echo htmlspecialchars($row['RegNo']); ?>" style="display:none;">
                                </td>
                                <td>
                                    <span class="view-mode"><?php echo htmlspecialchars($row['Name']); ?></span>
                                    <input type="text" name="name" class="edit-mode"
                                        value="<?php echo htmlspecialchars($row['Name']); ?>" style="display:none;">
                                </td>
                                <td>
                                    <span class="view-mode"><?php echo htmlspecialchars($row['Sem']); ?></span>
                                    <input type="text" name="sem" class="edit-mode"
                                        value="<?php echo htmlspecialchars($row['Sem']); ?>" style="display:none;">
                                </td>
                                <td>
                                    <span class="view-mode"><?php echo htmlspecialchars($row['Department']); ?></span>
                                    <input type="text" name="department" class="edit-mode"
                                        value="<?php echo htmlspecialchars($row['Department']); ?>"
                                        style="display:none;">
                                </td>
                                <td>
                                    <span class="view-mode"><?php echo htmlspecialchars($row['year']); ?></span>
                                    <input type="text" name="year" class="edit-mode"
                                        value="<?php echo htmlspecialchars($row['year']); ?>" style="display:none;">
                                </td>
                                <td>
                                    <span class="view-mode"><?php echo htmlspecialchars($row['batch']); ?></span>
                                    <input type="text" name="batch" class="edit-mode"
                                        value="<?php echo htmlspecialchars($row['batch']); ?>" style="display:none;">
                                </td>
                            
                                <td>
                                    <button type="button" class="btn btn-primary edit-button">Edit</button>
                                    <button type="submit" name="edit" class="btn btn-success save-button"
                                        style="display:none;">Save</button>
                                    <a href="m_student.php?delid=<?php echo $row['RegNo']; ?>" class="btn btn-danger"
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
            row.querySelectorAll('.view-mode').forEach(el => {
                el.style.display = 'none';
            });
            row.querySelectorAll('.edit-mode').forEach(el => {
                el.style.display = 'inline-block';
            });
            row.querySelector('.edit-button').style.display = 'none';
            row.querySelector('.save-button').style.display = 'inline-block';
            row.querySelector('.cancel-button').style.display = 'inline-block';
        });
    });

    document.querySelectorAll('.cancel-button').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            row.querySelectorAll('.view-mode').forEach(el => {
                el.style.display = 'inline-block';
            });
            row.querySelectorAll('.edit-mode').forEach(el => {
                el.style.display = 'none';
            });
            row.querySelector('.edit-button').style.display = 'inline-block';
            row.querySelector('.save-button').style.display = 'none';
            row.querySelector('.cancel-button').style.display = 'none';
        });
    });
    </script>
</body>
<footer><?php include('footer.php'); ?></footer>

</html>