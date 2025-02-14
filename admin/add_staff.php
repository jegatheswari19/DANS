<?php

include('../db_con.php');

if (isset($_POST['add_staff'])) {
    $name = $_POST['name'];
    $staff_id = $_POST['staff_id'];
    $department = $_POST['department'];
    $email = $_POST['email'];
    $img = '';

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        if (in_array($file_ext, $allowed_types)) {
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_dir = 'uploads/staff_images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $img = $upload_dir . $new_file_name;

            if (!move_uploaded_file($file_tmp, $img)) {
                echo "<script>alert('Failed to upload image.');</script>";
                exit;
            }
        } else {
            echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.');</script>";
            exit;
        }
    } else {
        echo "<script>alert('Image upload error.');</script>";
        exit;
    }

    // Check if staff already exists
    $query = mysqli_prepare($con, "SELECT Staff_id FROM Staff WHERE Staff_id=?");
    mysqli_stmt_bind_param($query, "s", $staff_id);
    mysqli_stmt_execute($query);
    mysqli_stmt_store_result($query);
    $rowCount = mysqli_stmt_num_rows($query);

    if ($rowCount > 0) {
        echo "<script>alert('Staff already exists');</script>";
    } else {
        mysqli_stmt_close($query);

        // Insert new staff
        $query = mysqli_prepare($con, "INSERT INTO Staff (Name, Staff_id, Department, email, img) VALUES (?, ?, ?, ?, ?)");
        if ($query) {
            mysqli_stmt_bind_param($query, "sssss", $name, $staff_id, $department, $email, $img);
            $execute = mysqli_stmt_execute($query);

            if ($execute) {
                echo "<script>alert('Staff added successfully.');</script>";
            } else {
                echo "<script>alert('Something went wrong. Please try again.');</script>";
                echo "Error: " . mysqli_error($con);
            }

            mysqli_stmt_close($query);
        } else {
            echo "<script>alert('Failed to prepare the statement.');</script>";
            echo "Error: " . mysqli_error($con);
        }
    }

    mysqli_close($con);
}

include('sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='./style.css'>
    <title>Add Staff</title>
</head>

<body>
    <div class="content">
        <h1>Add Staff</h1>
        <form method="post" enctype="multipart/form-data">
            <p>
                <label>Name<span>*</span></label>
                <input type="text" name="name" placeholder="Name" required />
            </p>
            <p>
                <label>Staff ID<span>*</span></label>
                <input type="text" name="staff_id" placeholder="Staff ID" required />
            </p>
            <p>
                <label>Department<span>*</span></label>
                <input type="text" name="department" placeholder="Department" required />
            </p>
            <p>
                <label>Email<span>*</span></label>
                <input type="email" name="email" placeholder="Email" required />
            </p>
            <p>
                <label>Image<span>*</span></label>
                <input type="file" name="image" accept="image/*" required />
            </p>
            <p>
                <input type="submit" name="add_staff" value="Add Staff" />
            </p>
        </form>
    </div>
    <footer>
        <?php include('footer.php'); ?>
    </footer>
    <script>
        const hamBurger = document.querySelector(".toggle-btn");

        hamBurger.addEventListener("click", function() {
            document.querySelector("#sidebar").classList.toggle("expand");
            document.querySelector(".content").classList.toggle("expand");
        });
    </script>
</body>

</html>
