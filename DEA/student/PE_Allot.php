<?php
include('../../student/student_nav.php');
include('../../db_con.php'); // Adjust the path as per your file structure

if (!isset($_SESSION['uid'])) {
    die("No registration number found in session.");
}
$RegNo = $_SESSION['uid'];

// Check if the student has already voted
$query = "SELECT COUNT(*) as vote_count FROM votes_pe WHERE RegNo = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $RegNo);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['vote_count'] > 0) {
    die("You have already voted.");
}

// Fetch semester and department for the student
$query = "SELECT Sem, Department FROM student WHERE RegNo = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $RegNo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No student found with the given registration number.");
}

$row = $result->fetch_assoc();
$sem = $row['Sem'];
$department = $row['Department'];

// Fetch elective details
$query = "SELECT Peid, Name, Staff, Subject_code FROM pe WHERE Sem = ? AND Department = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ss", $sem, $department);
$stmt->execute();
$result = $stmt->get_result();

$electives = [];
while ($row = $result->fetch_assoc()) {
    $electives[] = $row;
}

// Fetch the number of electives the student can choose
$query = "SELECT elective_no FROM pe_count WHERE Sem = ? AND Department = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ss", $sem, $department);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No data found in pe_count for the given semester and department.");
}

$row = $result->fetch_assoc();
$elective_no = $row['elective_no'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Elective Voting</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            background-image: url('../../images/back2.jpg');
            background-size: cover;
            background-position: center;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        h1, h2, h3 {
            text-align: center;
            color: #35424a;
        }
        .info {
            text-align: center;
            font-size: 18px;
            color: #35424a;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
            color: rgb(6, 57, 112);
        }
        .elective {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .elective input[type="checkbox"] {
            margin-right: 10px;
        }
        .submit-button {
            display: block;
            width: 100%;
            padding: 7px;
            background: #002a5d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        .submit-button:hover {
            background: rgba(52, 164, 196, 255);
            color: black;
        }
        footer {
            margin-top: 20px;
            color: white;
            text-align: center;
            /* position: fixed; */
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Elective Voting</h1>
        <div class="info">
            <h2>Semester: <?php echo htmlspecialchars($sem); ?></h2>
            <h2>Department: <?php echo htmlspecialchars($department); ?></h2>
            <h3>You can select up to <?php echo htmlspecialchars($elective_no); ?> electives</h3>
        </div>
        <form id="electiveForm" action="submit_vote.php" method="post">
            <?php foreach ($electives as $elective): ?>
                <div class="elective">
                    <input type="checkbox" id="elective_<?php echo htmlspecialchars($elective['Peid']); ?>" name="elective_choices[]" value="<?php echo htmlspecialchars($elective['Peid']); ?>">
                    <label for="elective_<?php echo htmlspecialchars($elective['Peid']); ?>">
                        <strong>Subject Code:</strong> <?php echo htmlspecialchars($elective['Subject_code']); ?><br>
                        <strong>Subject Name:</strong> <?php echo htmlspecialchars($elective['Name']); ?><br>
                        <strong>Staff:</strong> <?php echo htmlspecialchars($elective['Staff']); ?>
                    </label>
                </div>
            <?php endforeach; ?>
            <input type="hidden" name="max_choices" value="<?php echo htmlspecialchars($elective_no); ?>">
            <input type="submit" class="submit-button" value="Vote">
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const maxChoices = <?php echo $elective_no; ?>;
            const form = document.getElementById('electiveForm');
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', (e) => {
                    if (document.querySelectorAll('input[type="checkbox"]:checked').length > maxChoices) {
                        e.target.checked = false;
                        alert(`You can select up to ${maxChoices} electives.`);
                    }
                });
            });

            form.addEventListener('submit', (e) => {
                if (document.querySelectorAll('input[type="checkbox"]:checked').length < maxChoices) {
                    e.preventDefault();
                    alert(`You must select exactly ${maxChoices} electives.`);
                }
            });
        });
    </script>

    <footer>
        <?php include('../../student/student_footer.php'); ?>
    </footer>
</body>
</html>
