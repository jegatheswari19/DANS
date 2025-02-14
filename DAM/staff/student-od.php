<?php
include("../../db_con.php");
include("../../staff/staff_nav.php");

$uid = $_SESSION['uid']; // Get the user ID from the session

// Fetch the courses taught by the staff
$course_sql = "SELECT Subject_code FROM course WHERE Staff_id = '$uid'";
$course_result = mysqli_query($con, $course_sql);

$subject_codes = [];
while ($course_row = mysqli_fetch_assoc($course_result)) {
    $subject_codes[] = $course_row['Subject_code'];
}

$subject_codes_str = "'" . implode("','", $subject_codes) . "'";

// Fetch the data from the 'od' table for the subjects taught by the staff
$sql = "SELECT RegNo, Subject_code, Date, Hour, img FROM od WHERE Subject_code IN ($subject_codes_str) and status is null";
$result = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OD Requests</title>
    <style>
           body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
        background-image: url('../../images/back2.jpg'); 
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .image-modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }
        .image-modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }
        .image-modal-content, #caption {
            animation-name: zoom;
            animation-duration: 0.6s;
        }
        @keyframes zoom {
            from {transform: scale(0)}
            to {transform: scale(1)}
        }
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }
        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        select {
            padding: 10px;
            margin: 4px 2px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            cursor: pointer;
        }
        footer {
            margin-top: 20px;
            color: white;
            text-align: center;
            width: 100%;
            bottom: 0;
            position:fixed;
        }

    </style>
</head>
<body>

<?php
if (mysqli_num_rows($result) > 0) {
    echo '<table>';
    echo '<tr><th>RegNo</th><th>Subject Code</th><th>Date</th><th>Hour</th><th>Image</th><th>Action</th></tr>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $row["RegNo"] . '</td>';
        echo '<td>' . $row["Subject_code"] . '</td>';
        echo '<td>' . $row["Date"] . '</td>';
        echo '<td>' . $row["Hour"] . '</td>';
        echo '<td><img src="data:image/jpeg;base64,' . base64_encode($row["img"]) . '" width="100" height="100" onclick="showImage(this)"/></td>';
        echo '<td>';
        echo '<form method="post" action="accept_od.php" style="display:inline-block;">';
        echo '<input type="hidden" name="RegNo" value="' . $row["RegNo"] . '">';
        echo '<input type="hidden" name="Subject_code" value="' . $row["Subject_code"] . '">';
        echo '<input type="hidden" name="Date" value="' . $row["Date"] . '">';
        echo '<input type="hidden" name="Hour" value="' . $row["Hour"] . '">';
        echo '<button type="submit" name="accept">Accept</button>';
        echo '</form>';
        echo '<form method="post" action="reject_od.php" style="display:inline-block;">';
        echo '<input type="hidden" name="RegNo" value="' . $row["RegNo"] . '">';
        echo '<input type="hidden" name="Subject_code" value="' . $row["Subject_code"] . '">';
        echo '<input type="hidden" name="Date" value="' . $row["Date"] . '">';
        echo '<input type="hidden" name="Hour" value="' . $row["Hour"] . '">';
        echo '<select name="reject_reason" onchange="enableRejectButton(this)">';
        echo '<option value="">Select reason</option>';
        echo '<option value="OD Date doesn\'t match">OD Date doesn\'t match</option>';
        echo '<option value="OD limit exceeded">OD limit exceeded</option>';
        echo '<option value="Invalid OD">Invalid OD</option>';
        echo '</select>';
        echo '<button type="submit" name="reject" disabled>Reject</button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
} else {
    echo "0 results";
}

mysqli_close($con);
?>

<!-- Modal for displaying image -->
<div id="myModal" class="image-modal">
    <span class="close" onclick="closeImage()">&times;</span>
    <img class="image-modal-content" id="img01">
</div>

<script>
function showImage(img) {
    var modal = document.getElementById("myModal");
    var modalImg = document.getElementById("img01");
    modal.style.display = "block";
    modalImg.src = img.src;
}

function closeImage() {
    var modal = document.getElementById("myModal");
    modal.style.display = "none";
}

function enableRejectButton(selectElement) {
    var rejectButton = selectElement.nextElementSibling;
    if (selectElement.value) {
        rejectButton.disabled = false;
    } else {
        rejectButton.disabled = true;
    }
}
</script>

</body>
<footer>
    <?php include('../../footer.php'); ?>
</footer>
</html>
