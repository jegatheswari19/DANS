<?php
// Include necessary PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/Exception.php';
require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';

// Include database connection and sidebar (if needed)
include('../db_con.php');
include('sidebar.php');

// Function to send email notification to students in the same semester
function send_notification_email($con, $description, $semester) {
    // Replace with your email credentials
    $email_username = 'dansseproject@gmail.com';
    $email_password = 'lbvj xslr gzjm xcgn';

    // Instantiate PHPMailer

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $email_username;
        $mail->Password = $email_password;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Recipients - Fetching emails from student table for the selected semester
        $recipient_emails = [];
        $stmt = $con->prepare("SELECT email_id FROM student WHERE Sem = ?");
        $stmt->bind_param("i", $semester);
        $stmt->execute();
        $stmt->bind_result($email);

        while ($stmt->fetch()) {
            $recipient_emails[] = $email;
        }

        $stmt->close();

        // Email content
        $mail->setFrom($email_username, 'DANS');
        foreach ($recipient_emails as $recipient_email) {
            $mail->addAddress($recipient_email);
        }

        $mail->isHTML(false);
        $mail->Subject = 'Professional Elective Portal Notification';
        $mail->Body = "Notification: $description";

        // Send the email
        $mail->send();
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}

// Handle form submission for Professional Elective
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['Sem'])) {
        $Sem = $_POST['Sem'];
        $action = $_POST['action'];

        if ($action == 'open') {
            // Check if the portal is already open
            $query = "SELECT * FROM notification WHERE description LIKE ? AND sem=?";
            $description_like = '%Professional Elective portal is open%';
            $stmt = $con->prepare($query);
            $stmt->bind_param('si', $description_like, $Sem);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<script>alert('Portal is already open');</script>";
            } else {
                // Delete all data of the selected Sem from votes_pe table
                $query = "DELETE FROM votes_pe WHERE Sem=?";
                $stmt = $con->prepare($query);
                $stmt->bind_param('i', $Sem);
                $stmt->execute();
                $stmt->close();

                // Set vote column to 0 in pe table for the selected Sem
                $query = "UPDATE pe SET vote=0 WHERE Sem=?";
                $stmt = $con->prepare($query);
                $stmt->bind_param('i', $Sem);
                $stmt->execute();
                $stmt->close();

                $query = "DELETE FROM notification WHERE description LIKE ? AND sem=?";
                $description_like = '%Professional Elective is alloted%';
                $stmt = $con->prepare($query);
                $stmt->bind_param('si', $description_like, $Sem);
                $stmt->execute();
                $stmt->close();

                $stmt = $con->prepare("SELECT Subject_code FROM pe where Sem = ?");
                $stmt->bind_param("i", $Sem);
                $stmt->execute();
                $stmt->bind_result($subject_code);

                $subject_codes = [];
                while ($stmt->fetch()) {
                    $subject_codes[] = $subject_code;
                }
                $stmt->close();

                // Drop all tables for the fetched subject codes
                foreach ($subject_codes as $code) {
                    $query = "DROP TABLE IF EXISTS `$code`"; // Note the backticks around the table name
                    if (!$con->query($query)) {
                        echo "Error dropping table $code: " . $con->error;
                    }
                }

                $query = "DELETE FROM course WHERE type = ? AND Sem=?";
                $type = 'pe';
                $stmt = $con->prepare($query);
                $stmt->bind_param('si', $type, $Sem);
                $stmt->execute();
                $stmt->close();

                // Insert notification
                $description = 'Professional Elective portal is open for Sem ' . $Sem . '. Give your preferences. Remember first come first serve.';
                $link = BASE_URL . "/DEA/student/PE_Allot.php";

                $query = "INSERT INTO notification(datetime, description, link, sem) VALUES (NOW(), ?, ?, ?)";
                $stmt = $con->prepare($query);
                $stmt->bind_param("ssi", $description, $link, $Sem);
                $stmt->execute();
                $stmt->close();

                send_notification_email($con, $description, $Sem);

                echo "<script>alert('Portal opened successfully');</script>";
            }
        } elseif ($action == 'close') {
            // Check if the portal is open
            $query = "SELECT * FROM notification WHERE description LIKE ? AND sem=?";
            $description_like = '%Professional Elective portal is open%';
            $stmt = $con->prepare($query);
            $stmt->bind_param('si', $description_like, $Sem);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                echo "<script>alert('The portal is not opened yet.');</script>";
            } else {
                // Delete open portal notifications
                $query = "DELETE FROM notification WHERE description LIKE ? AND sem=?";
                $description_like = '%Professional Elective portal is open%';
                $stmt = $con->prepare($query);
                $stmt->bind_param('si', $description_like, $Sem);
                $stmt->execute();
                $stmt->close();

                // Insert notification for closing the portal
                $description = 'Professional Elective is alloted. Go and see which you\'re going to learn this sem.';
                $link = '/DAMNS/DEA/student/Pe_result.php';

                $query = "INSERT INTO notification(datetime, description, link, sem) VALUES (NOW(), ?, ?, ?)";
                $stmt = $con->prepare($query);
                $stmt->bind_param("ssi", $description, $link, $Sem);
                $stmt->execute();
                $stmt->close();

                // Fetch departments for the given semester
                $query = "SELECT DISTINCT Department FROM pe WHERE Sem=?";
                $stmt = $con->prepare($query);
                $stmt->bind_param('i', $Sem);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($dept_row = $result->fetch_assoc()) {
                    $department = $dept_row['Department'];

                    // Get the number of electives for the department and semester
                    $query = "SELECT elective_no FROM pe_count WHERE Sem=? AND Department=?";
                    $stmt = $con->prepare($query);
                    $stmt->bind_param('is', $Sem, $department);
                    $stmt->execute();
                    $result_count = $stmt->get_result();

                    if ($result_count->num_rows > 0) {
                        $count_row = $result_count->fetch_assoc();
                        $elective_no = $count_row['elective_no'];

                        // Fetch top electives based on votes
                        $query = "SELECT Subject_code, Name FROM pe WHERE Sem=? AND Department=? ORDER BY vote DESC LIMIT ?";
                        $stmt = $con->prepare($query);
                        $stmt->bind_param('isi', $Sem, $department, $elective_no);
                        $stmt->execute();
                        $result_electives = $stmt->get_result();

                        while ($elective_row = $result_electives->fetch_assoc()) {
                            $subject_code = $elective_row['Subject_code'];
                            $subject_Name = $elective_row['Name'];

                            // Insert into course table
                            $query = "INSERT INTO course (Subject_code, Name, Sem, Department, type) VALUES (?, ?, ?, ?, 'pe')";
                            $stmt = $con->prepare($query);
                            $stmt->bind_param('ssis', $subject_code, $subject_Name, $Sem, $department);
                            $stmt->execute();
                            $stmt->close();

                            // Call stored procedure
                            $procedure = mysqli_prepare($con, "CALL insert_course_row(?, ?, ?)");
                            mysqli_stmt_bind_param($procedure, 'sis', $subject_code, $Sem, $department);
                            mysqli_stmt_execute($procedure);
                            mysqli_stmt_close($procedure);
                        }
                    }
                }
                send_notification_email($con, $description, $Sem);

                echo "<script>alert('Portal closed and courses allocated successfully');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Elective Portal Management</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
        }
        .content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            width: 50%;
            background-color: #0e2242;
            padding: 20px;
            border-radius: 8px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
        }
        .form-container form label {
            margin-bottom: 10px;
        }
        .form-container form select,
        .form-container form button {
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 4px;
        }
        .form-container form button {
            background-color: #FFD700;
            color: white;
            cursor: pointer;
        }
        .form-container form button:hover {
            background-color: white;
            color: #1976d2;
        }
        .form-container .button-group {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="form-container">
            <h1>Professional Elective Portal Management</h1>
            <form method="POST">
                <label for="Sem">Select Sem:</label>
                <select name="Sem" id="Sem" required>
                    <?php
                    for ($sem = 3; $sem <= 8; $sem++) {
                        echo "<option value='$sem'>Sem $sem</option>";
                    }
                    ?>
                </select>
                <div class="button-group">
                    <button type="submit" name="action" value="open">Open Portal</button>
                    <button type="submit" name="action" value="close">Close Portal</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php
$con->close();
?>
