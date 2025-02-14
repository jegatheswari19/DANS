<?php
session_start();
include('../../db_con.php'); // Adjust the path as per your file structure

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['uid'])) {
    die("No registration number found in session.");
}
$RegNo = $_SESSION['uid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['elective_choices']) || !is_array($_POST['elective_choices'])) {
        die("No electives selected.");
    }

    $elective_choices = $_POST['elective_choices'];
    $max_choices = intval($_POST['max_choices']);

    if (count($elective_choices) > $max_choices) {
        die("You have selected more than the allowed number of electives.");
    }

    // Begin a transaction
    $con->begin_transaction();

    try {
        // Check if the student has already voted
        $query = "SELECT COUNT(*) as vote_count FROM votes_pe WHERE RegNo = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $RegNo);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['vote_count'] > 0) {
            throw new Exception("You have already voted.");
        }

        // Correct the SQL query to include all required columns
        $query = "INSERT INTO votes_pe (RegNo, Subject_code, Name, Sem, Department) VALUES (?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }

        foreach ($elective_choices as $peid) {
            // Fetch the subject details for the given Peid
            $subject_query = "SELECT Subject_code, Name, Sem, Department FROM pe WHERE Peid = ?";
            $subject_stmt = $con->prepare($subject_query);
            $subject_stmt->bind_param("s", $peid);
            $subject_stmt->execute();
            $subject_result = $subject_stmt->get_result();
            $subject_row = $subject_result->fetch_assoc();

            if (!$subject_row) {
                throw new Exception("No subject found with the given Peid: " . htmlspecialchars($peid));
            }

            $subject_code = $subject_row['Subject_code'];
            $subject_name = $subject_row['Name'];
            $Sem = $subject_row['Sem'];
            $Department = $subject_row['Department'];

            // Bind parameters to the statement
            $stmt->bind_param("sssis", $RegNo, $subject_code, $subject_name, $Sem, $Department);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            // Increment the vote count for the selected course
            $vote_query = "UPDATE pe SET vote = vote + 1 WHERE Peid = ?";
            $vote_stmt = $con->prepare($vote_query);
            if (!$vote_stmt) {
                throw new Exception("Prepare failed for vote update: " . $con->error);
            }
            $vote_stmt->bind_param("s", $peid);
            if (!$vote_stmt->execute()) {
                throw new Exception("Execute failed for vote update: " . $vote_stmt->error);
            }
        }

        // Commit the transaction
        $con->commit();
        $message = "Your votes have been successfully recorded.";
    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        $con->rollback();
        $error = "Failed to record votes: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color:#99ebff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }

        h1 {
            color: #333;
        }

        p {
            color: #666;
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #008060;
            color: white;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
        }

        .error {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #008060;
            color: white;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vote Submission</h1>
        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
