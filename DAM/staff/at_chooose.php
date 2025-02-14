<?php
include("../../db_con.php");
include('../../staff/staff_nav.php');

$staff_id = $_SESSION['uid'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
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

        

        h1, h2 {
            text-align: center;
            color: #333333;
            margin: 20px;
        }

        .row {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            margin: 20px;
        }

        .courses {
            margin: 10px;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: #ffffff;
        }

       
      
        .vertical-text {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
            padding: 10px;
            background-color: #f4f4f9;
            color:white;
        }

        .courses {
            width: 30%;
            background-color:white;
            color: #ffffff;
            text-align: center;
            overflow-y: auto;
        }

        .course-link {
            color: #003366;
            text-decoration: none;
        }

                footer {
            margin-top: 20px;
            color: #003366;
            text-align: center;
            width: 100%;
            bottom: 0;
            position:fixed;
        }


        .course-card {
            background-color: #003366;
            color: white;
            border: 1px solid white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        @media only screen and (max-width: 1024px) {
            .row {
                flex-direction: column;
                align-items: center;
            }
            
         
            .courses {
                width: 90%;
                margin: 10px 0;
            }

          
        }
    </style>
</head>

<body>
    <div class="row">

   
        <div class="courses">
            <h2>Courses</h2>
            <?php
            // Query to fetch courses assigned to the staff member
            $query = "SELECT Subject_code, Name, Sem, Department FROM course WHERE Staff_id = ?";
            if ($stmt = $con->prepare($query)) {
                $stmt->bind_param("s", $staff_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Output data for each row
                    while ($row = $result->fetch_assoc()) {
                        echo '<a href="../staff/hour_choose.php?subject_code=' . urlencode($row['Subject_code']) . '" class="course-link">';
                        echo '<div class="course-card">';
                        echo '<h3>' . htmlspecialchars($row['Name']) . '</h3>';
                        echo '<p>Subject Code: ' . htmlspecialchars($row['Subject_code']) . '</p>';
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
    </div>
</body>

<footer>
    <?php include('../../footer.php'); ?>
</footer>

</html>
