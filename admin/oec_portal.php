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
        $mail->Subject = 'Open Elective Portal Notification';
        $mail->Body = "Notification: $description";

        // Send the email
        $mail->send();
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}

// Path to the JSON file
$json_file_path = 'course_data.json';

// Function to read data from JSON file
function read_json_file($file_path) {
    if (file_exists($file_path)) {
        $json_data = file_get_contents($file_path);
        return json_decode($json_data, true);
    }
    return [];
}

// Function to write data to JSON file
function write_json_file($file_path, $data) {
    $json_data = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($file_path, $json_data);
}

// Function to handle notification insertion
function insert_notification($con, $description, $link, $semester) {
    // First delete existing "OEC ALLOTMENT" notifications for the same semester
    $delete_query = "DELETE FROM notification WHERE description = ? AND sem = ?";
    $delete_stmt = $con->prepare($delete_query);
    $delete_stmt->bind_param('si', $description, $semester);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Then insert new notification
    $insert_query = "INSERT INTO notification(datetime, description, link, sem) VALUES (NOW(), ?, ?, ?)";
    $insert_stmt = $con->prepare($insert_query);
    $insert_stmt->bind_param("ssi", $description, $link, $semester);
    $insert_stmt->execute();
    $insert_stmt->close();
}

// Function to handle notification deletion
function delete_notification($con, $description_like, $semester) {
    $query = "DELETE FROM notification WHERE description LIKE ? AND sem=?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('si', $description_like, $semester);
    $stmt->execute();
    $stmt->close();
}

// Handle form submission for Open Elective
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['semester'])) {
        $semester = intval($_POST['semester']);
        $action = $_POST['action'];

        // Read current data from JSON file
        $course_data = read_json_file($json_file_path);

        if ($action == 'open') {
            $course_choices = filter_var($_POST['course_choices'], FILTER_SANITIZE_STRING);
            $min_students = intval($_POST['min_students']);

            // Check if the portal is already open
            $description_like = '%Open Elective portal is open%';
            $query = "SELECT * FROM notification WHERE description LIKE ? AND sem=?";
            $stmt = $con->prepare($query);
            $stmt->bind_param('si', $description_like, $semester);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<script>alert('Portal is already open');</script>";
            } else {
                // Prepare and execute the statement to fetch `Subject_code`
                $stmt = $con->prepare("SELECT Subject_code FROM oec WHERE Sem = ?");
                $stmt->bind_param("i", $semester);
                $stmt->execute();
                $stmt->bind_result($subject_code);

                $subject_codes = [];
                while ($stmt->fetch()) {
                    $subject_codes[] = $subject_code;
                }
                $stmt->close();

                // Drop all tables for the fetched subject codes
                foreach ($subject_codes as $code) {
                    $query = "DROP TABLE IF EXISTS `$code`";
                    if (!$con->query($query)) {
                        echo "Error dropping table $code: " . $con->error;
                    }
                }

                $delete_stmt = $con->prepare("DELETE FROM course WHERE type = 'oec' AND Sem = ?");
                $delete_stmt->bind_param("i", $semester);
                $delete_stmt->execute();
                $delete_stmt->close();

                // Fetch all Subject codes for the semester from oec table
                $stmt = $con->prepare("SELECT Subject_code FROM oec WHERE Sem = ?");
                $stmt->bind_param("i", $semester);
                $stmt->execute();
                $stmt->bind_result($subject_code);

                $subject_codes = [];

                while ($stmt->fetch()) {
                    $subject_codes[] = $subject_code;
                }

                $stmt->close();

                // Create tables for each Subject_code found
                foreach ($subject_codes as $subject_code) {
                    $create_query = "CREATE TABLE IF NOT EXISTS `$subject_code` (
                        RegNo VARCHAR(255) NOT NULL,
                        Name VARCHAR(255) NOT NULL,
                        percentage DECIMAL(5,2) NOT NULL DEFAULT 100,        
                        dayspresent INT NOT NULL DEFAULT 0, 
                        daysabsent INT NOT NULL DEFAULT 0, 
                        PRIMARY KEY (RegNo)
                    )";
                    $con->query($create_query);
                }

                $description = 'Open Elective portal is open for semester ' . $semester . '.  Remember first come first serve.';
                $link = BASE_URL . "/DEA/student/OEC_Allot.php";

                // Insert notification
                insert_notification($con, $description, $link, $semester);

                // Truncate the oec_choices table
                $truncate_query = "TRUNCATE TABLE oec_choices";
                $con->query($truncate_query);

                // Delete notifications
                $description_like = 'OEC ALLOTMENT!';
                delete_notification($con, $description_like, $semester);

                // Store course choices and minimum number of students in JSON file
                $course_data[$semester] = [
                    'course_choices' => $course_choices,
                    'min_students' => $min_students
                ];
                write_json_file($json_file_path, $course_data);
                send_notification_email($con, $description, $semester);

                echo "<script>alert('Portal opened successfully');</script>";
            }
        } elseif ($action == 'close') {
            // Check if the portal is already closed
            if (!isset($course_data[$semester])) {
                echo "<script>alert('Portal is already closed');</script>";
            } else {
                // Delete notifications
                $description_like = '%Open Elective portal is open%';
                delete_notification($con, $description_like, $semester);

                echo "<script>alert('Portal closed successfully');</script>";
            }
        } elseif ($action == 'announce') {
            // Check if the portal is closed
            if (!isset($course_data[$semester])) {
                echo "<script>alert('Please close the portal before announcing the results');</script>";
            } else {
                $description = 'OEC ALLOTMENT!';
                $link = BASE_URL . "/DEA/student/Oec_result.php";

                // Insert notification
                insert_notification($con, $description, $link, $semester);

                // Insert data from oec to course
                $insert_query = "INSERT INTO course (Subject_code, Name, Staff, Staff_id, Department, Sem ,type) SELECT Subject_code, Name, Staff, Staff_id, Department,Sem, 'oec' FROM oec WHERE Sem = ?";
                $insert_stmt = $con->prepare($insert_query);
                $insert_stmt->bind_param("i", $semester);
                $insert_stmt->execute();
                $insert_stmt->close();

                // Remove course data for the semester from JSON file
                unset($course_data[$semester]);
                write_json_file($json_file_path, $course_data);

                send_notification_email($con, $description, $semester);

                echo "<script>alert('Result announced successfully');</script>";
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
    <title>Open Elective Portal Management</title>
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
            width: 60%;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .form-container h1 {
            text-align: center;
            color: #0e2242;
            margin-bottom: 30px;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
        }
        .form-container form label {
            margin-bottom: 10px;
            color: #333333;
        }
        .form-container form input,
        .form-container form select,
        .form-container form button {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #cccccc;
            border-radius: 5px;
        }
        .form-container form button {
            background-color: #0e2242;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container form button:hover {
            background-color: #1976d2;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="form-container">
            <h1>Open Elective Portal Management</h1>
            <form method="POST">
                <label for="semester">Select Semester:</label>
                <select name="semester" id="semester" required>
                    <?php
                    for ($sem = 3; $sem <= 8; $sem++) {
                        echo "<option value='$sem'>Semester $sem</option>";
                    }
                    ?>
                </select>
                <div id="open-fields">
                    <label for="course_choices">Course Choices:</label>
                    <input type="number" name="course_choices" id="course_choices" required>
                    <label for="min_students">Minimum Students:</label>
                    <input type="number" name="min_students" id="min_students" required>
                </div>
                <div class="button-group">
                    <button type="submit" name="action" value="open" onclick="showFields()">Open Portal</button>
                    <button type="submit" name="action" value="close" onclick="hideFields()">Close Portal</button>
                    <button type="submit" name="action" value="announce" onclick="hideFields()">Announce Result</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function showFields() {
            document.getElementById('course_choices').required = true;
            document.getElementById('min_students').required = true;
        }

        function hideFields() {
            document.getElementById('course_choices').required = false;
            document.getElementById('min_students').required = false;
        }
    </script>
</body>
</html>
<?php
$con->close();
?>
