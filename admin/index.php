<?php
include('../db_con.php');
include('sidebar.php');

// Your database queries
$sql_students = "SELECT COUNT(*) AS student_count FROM student";
$result_students = $con->query($sql_students);
$row_students = $result_students->fetch_assoc();
$student_count = $row_students['student_count'];

$sql_courses = "SELECT COUNT(*) AS course_count FROM course";
$result_courses = $con->query($sql_courses);
$row_courses = $result_courses->fetch_assoc();
$course_count = $row_courses['course_count'];

$sql_staff = "SELECT COUNT(*) AS staff_count FROM staff";
$result_staff = $con->query($sql_staff);
$row_staff = $result_staff->fetch_assoc();
$staff_count = $row_staff['staff_count'];

$sql_departments = "SELECT COUNT(*) AS department_count FROM department";
$result_departments = $con->query($sql_departments);
$row_departments = $result_departments->fetch_assoc();
$department_count = $row_departments['department_count'];

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            margin-bottom: 20px;
        }
        .card-header {
            font-size: 1.25rem;
            font-weight: bold;
            color: #fff;
        }
        .bg-primary { background-color: #007bff !important; }
        .bg-success { background-color: #28a745 !important; }
        .bg-warning { background-color: #ffc107 !important; }
        .bg-danger { background-color: #dc3545 !important; }
        .count {
            font-size: 2rem;
            font-weight: bold;
        }
        .chart-container {
            position: relative;
            margin: auto;
            height: 40vh;
            width: 80vw;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Admin Dashboard</h1>

        <div class="row">
            <!-- Students Section -->
            <div class="col-md-6 col-lg-3">
                <div class="card text-white bg-primary">
                    <div class="card-header">Students</div>
                    <div class="card-body">
                        <div class="count"><?php echo $student_count; ?></div>
                    </div>
                </div>
            </div>

            <!-- Courses Section -->
            <div class="col-md-6 col-lg-3">
                <div class="card text-white bg-success">
                    <div class="card-header">Courses</div>
                    <div class="card-body">
                        <div class="count"><?php echo $course_count; ?></div>
                    </div>
                </div>
            </div>

            <!-- Staff Section -->
            <div class="col-md-6 col-lg-3">
                <div class="card text-white bg-warning">
                    <div class="card-header">Staff</div>
                    <div class="card-body">
                        <div class="count"><?php echo $staff_count; ?></div>
                    </div>
                </div>
            </div>

            <!-- Departments Section -->
            <div class="col-md-6 col-lg-3">
                <div class="card text-white bg-danger">
                    <div class="card-header">Departments</div>
                    <div class="card-body">
                        <div class="count"><?php echo $department_count; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Doughnut Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Doughnut Chart</div>
                    <div class="card-body">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bar Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Bar Chart</div>
                    <div class="card-body">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <?php include('footer.php'); ?>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        const doughnutData = {
            labels: ['Students', 'Courses', 'Staff', 'Departments'],
            datasets: [{
                label: 'Counts',
                data: [<?php echo $student_count; ?>, <?php echo $course_count; ?>, <?php echo $staff_count; ?>, <?php echo $department_count; ?>],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
            }]
        };

        const doughnutConfig = {
            type: 'doughnut',
            data: doughnutData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        };

        const barData = {
            labels: ['Students', 'Courses', 'Staff', 'Departments'],
            datasets: [{
                label: 'Counts',
                data: [<?php echo $student_count; ?>, <?php echo $course_count; ?>, <?php echo $staff_count; ?>, <?php echo $department_count; ?>],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
            }]
        };

        const barConfig = {
            type: 'bar',
            data: barData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        window.onload = function() {
            const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
            new Chart(ctxDoughnut, doughnutConfig);

            const ctxBar = document.getElementById('barChart').getContext('2d');
            new Chart(ctxBar, barConfig);
        };
    </script>
</body>
</html>
