<?php
include('../db_con.php');
include('sidebar.php');

// Delete operation
if (isset($_GET['delid'])) {
    $id = $_GET['delid'];
    $delete_query = "DELETE FROM pe_count WHERE id = '$id'";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('Record deleted successfully');</script>";
    } else {
        echo "<script>alert('Failed to delete record');</script>";
    }
    echo "<script>window.location.href = 'pe_count.php';</script>"; // To refresh the page after deletion
}

// Edit operation
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $sem = $_POST['sem'];
    $department = $_POST['department'];
    $elective_no = $_POST['elective_no'];
    $update_query = "UPDATE pe_count SET Sem = '$sem', Department = '$department', Elective_No = '$elective_no' WHERE id = '$id'";
    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('Data Updated');</script>";
    } else {
        echo "<script>alert('Failed to update data');</script>";
    }
    echo "<script>window.location.href = 'pe_count.php';</script>"; // To refresh the page after editing
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $sem = $_POST['sem'];
    $department = $_POST['department'];
    $elective_no = $_POST['elective_no'];

    // Insert the new record into the pe_count table
    $insert_query = "INSERT INTO pe_count (Sem, Department, Elective_No) VALUES ('$sem', '$department', '$elective_no')";
    if (mysqli_query($con, $insert_query)) {
        echo "<script>alert('New record added successfully');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
    }
}

// Fetch all records from the pe_count table
$fetch_query = "SELECT id, Sem, Department, Elective_No FROM pe_count";
$result = mysqli_query($con, $fetch_query);
if (!$result) {
    echo "Error: " . mysqli_error($con);
    exit();
}

// Start the HTML output
echo "<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: white;
        height: 100vh;
        margin: 0;
    }
    .content {
        margin-left: 70px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column; 
    }
    .form-container, .table-container {
        width: 80%;
        max-width: 800px;
        margin: 20px auto;
        font-size: 20px;
        padding: 10px;
    }
    .form-container {
        margin-bottom: 40px;
        background-color:#0e2242;
        padding: 20px;
        border-radius: 10px;
        color:white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .table-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        height: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    th, td {
        padding: 15px;
        border: 1px solid #ddd;
        text-align: center;
    }
    th {
        background-color: #1D2951;
        color: white;
    }
    tr {
        background-color: #f2f2f2;
    }
    tr:hover {
        background-color: #ddd;
    }
    .btn {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn-primary {
        background-color: #007bff;
        color: white;
    }
    .btn-success {
        background-color: #28a745;
        color: white;
    }
    .btn-danger {
        background-color: #dc3545;
        color: white;
    }
    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }

    input[type='submit']{
    background:#FFD700;
    color:black;
    }

     input[type='submit']:hover{
    color:#FFD700;
    background:white;
    }

    input[type='text'] {
        width: 100%;
        padding: 8px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    h1 {
        text-align: center;
        color: white;
    }
</style>";

echo "<div class='content'>";
echo "<div class='form-container'>";
echo "<h1>Add New Record to PE Count Table</h1>";
echo "<form action='' method='POST'>
        <label for='sem'>Semester:</label>
        <input type='text' id='sem' name='sem' required><br>
        <label for='department'>Department:</label>
        <input type='text' id='department' name='department' required><br>
        <label for='elective_no'>Elective No:</label>
        <input type='text' id='elective_no' name='elective_no' required><br>
        <input type='submit' name='add' value='Add Record' class='btn btn-primary'>
      </form>";
echo "</div>";

echo "<div class='table-container'>";
echo "<h1>Current Records in PE Count Table</h1>";
echo "<table>";
echo "<tr><th>Semester</th><th>Department</th><th>Elective No</th><th>Action</th></tr>";

// Display the records fetched from the pe_count table
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr id='row-{$row['id']}'>
            <form method='POST' action='pe_count.php'>
                <input type='hidden' name='id' value='{$row['id']}'>
                <td>
                    <span class='view-mode'>{$row['Sem']}</span>
                    <input type='text' name='sem' class='edit-mode' value='{$row['Sem']}' style='display:none;'>
                </td>
                <td>
                    <span class='view-mode'>{$row['Department']}</span>
                    <input type='text' name='department' class='edit-mode' value='{$row['Department']}' style='display:none;'>
                </td>
                <td>
                    <span class='view-mode'>{$row['Elective_No']}</span>
                    <input type='text' name='elective_no' class='edit-mode' value='{$row['Elective_No']}' style='display:none;'>
                </td>
                <td>
                    <button type='button' class='btn btn-primary edit-button'>Edit</button>
                    <button type='submit' name='edit' class='btn btn-success save-button' style='display:none;'>Save</button>
                    <a href='pe_count.php?delid={$row['id']}' class='btn btn-danger' onClick=\"return confirm('Are you sure you want to delete?')\">Delete</a>
                    <button type='button' class='btn btn-secondary cancel-button' style='display:none;'>Cancel</button>
                </td>
            </form>
          </tr>";
}

echo "</table>";
echo "</div>";
echo "</div>";

// Close the database connection
mysqli_close($con);

echo "<footer>";
include('./footer.php');
echo "</footer>";

echo "<script>
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
</script>";
?>
