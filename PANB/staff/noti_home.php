<?php
// Start the session to access session variables
include("../../db_con.php"); // Include the database connection
include("../../staff/staff_nav.php"); // Include the staff navigation

// Fetch staff ID from the session
$staff_id = $_SESSION['uid'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Courses</title>
    <link rel="stylesheet" href="path/to/your/css/file.css"> <!-- Link to your CSS file -->
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

      

        h2 {
            text-align: center;
            color: #003366; /* Dark Blue */
            margin-top: 20px;
        }

        /* Courses Container */
        .courses {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            padding: 20px;
        }

        /* Course Link */
        .course-link {
            text-decoration: none;
            color: inherit;
            margin: 10px;
        }

        /* Course Card */
        .course-card {
            background-color: #003366;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 10px;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            width: 300px;
            text-align: center;
            
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        /* Course Card Title */
        .course-card h3 {
            margin: 0;
            color: white; 
            font-size:22px; /* Darker Blue */
            /* Medium Blue */
        }

        /* Course Card Paragraph */
        .course-card p {
            margin: 10px 0;
            color:#fff;
            font-size:20px; /* Darker Blue */
        }

        footer {
            margin-top: 20px;
            color: white;
            text-align: center;
            width: 100%;
            bottom: 0;
            position:fixed;
        }


        /* Responsive Design */
        @media (max-width: 600px) {
            .course-card {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="courses">
        <h2>Courses</h2>
        <?php
        // Query to fetch courses assigned to the staff member
        $query = "SELECT subject_code, Name, Sem, Department FROM course WHERE Staff_id = ?";
        if ($stmt = $con->prepare($query)) {
            $stmt->bind_param("s", $staff_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Output data for each row
                while ($row = $result->fetch_assoc()) {
                    echo '<a href="noti_student.php?subject_code=' . urlencode($row['subject_code']) . '" class="course-link">';
                    echo '<div class="course-card">';
                    echo '<h3>' . htmlspecialchars($row['Name']) . '</h3>';
                    echo '<p>Subject Code: ' . htmlspecialchars($row['subject_code']) . '</p>';
                    echo '<p>Department: ' . htmlspecialchars($row['Department']) . '</p>';
                    echo '</div>';
                    echo '</a>';
                }
            } else {
                echo '<p>No courses assigned.</p>';
            }

            $stmt->close();
        } else {
            echo '<p>Error fetching courses.</p>';
        }
        ?>
    </div>
</body>

<footer>
    <?php include('../../footer.php'); ?>
</footer>

</html>
