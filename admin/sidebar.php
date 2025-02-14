<?php
include('../config.php');
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location:login.php');
exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/images/ptu-logo.png">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

a {
    text-decoration: none;
}

li {
    list-style: none;
}

h1 {
    font-weight: 600;
    font-size: 1.5rem;
}

body {
    font-family: 'Poppins', sans-serif;
}

.wrapper {
    display: flex;
}

#sidebar {
    position: fixed;
    width: 70px;
    min-width: 70px;
    z-index: 1000;
    height: 100%;
    transition: all .25s ease-in-out;
    background-color: #0e2238;
    display: flex;
    flex-direction: column;
}

#sidebar.expand {
    width: 260px;
    min-width: 260px;
}

.toggle-btn {
    background-color: transparent;
    cursor: pointer;
    border: 0;
    padding: 1rem 1.5rem;
}

.toggle-btn i {
    font-size: 1.5rem;
    color: #FFF;
}

.sidebar-logo {
    margin: auto 0;
}

.sidebar-logo a {
    color: #FFF;
    font-size: 1.15rem;
    font-weight: 600;
}

#sidebar:not(.expand) .sidebar-logo,
#sidebar:not(.expand) a.sidebar-link span {
    display: none;
}

.sidebar-nav {
    padding: 2rem 0;
    flex: 1 1 auto;
}

a.sidebar-link {
    padding: .625rem 1.625rem;
    color: #FFF;
    display: block;
    font-size: 0.9rem;
    white-space: nowrap;
    border-left: 3px solid transparent;
}

.sidebar-link i {
    font-size: 1.1rem;
    margin-right: .75rem;
}

a.sidebar-link:hover {
    background-color: rgba(255, 255, 255, .075);
    border-left: 3px solid #3b7ddd;
}

.sidebar-item {
    position: relative;
}

#sidebar:not(.expand) .sidebar-item .sidebar-dropdown {
    position: absolute;
    top: 0;
    left: 70px;
    background-color: #0e2238;
    padding: 0;
    min-width: 15rem;
    display: none;
}

#sidebar:not(.expand) .sidebar-item:hover .has-dropdown+.sidebar-dropdown {
    display: block;
    max-height: 15em;
    width: 100%;
    opacity: 1;
}

#sidebar.expand .sidebar-link[data-bs-toggle="collapse"]::after {
    border: solid;
    border-width: 0 .075rem .075rem 0;
    content: "";
    display: inline-block;
    padding: 2px;
    position: absolute;
    right: 1.5rem;
    top: 1.4rem;
    transform: rotate(-135deg);
    transition: all .2s ease-out;
}

#sidebar.expand .sidebar-link[data-bs-toggle="collapse"].collapsed::after {
    transform: rotate(45deg);
    transition: all .2s ease-out;
}
</style>

<body>
    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button"><i class="lni lni-grid-alt"></i></button>
                <div class="sidebar-logo"><a href="#">DANS</a></div>
            </div>
            <ul class="sidebar-nav">


                <li class="sidebar-item">
                    <a href="./add_course.php" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#courses" aria-expanded="false" aria-controls="courses">
                        <i class="lni lni-book"></i><span>Courses</span>
                    </a>
                    <ul id="courses" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item"><a href="./add_course.php" class="sidebar-link">Add Courses</a></li>
                        <li class="sidebar-item"><a href="./m_course.php" class="sidebar-link">Manage Courses</a></li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="./add_dept.php" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#departments" aria-expanded="false" aria-controls="departments">
                        <i class="lni lni-grid-alt"></i><span>Departments</span>
                    </a>
                    <ul id="departments" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item"><a href="./add_dept.php" class="sidebar-link">Add Departments</a></li>
                        <li class="sidebar-item"><a href="m_dept.php" class="sidebar-link">Manage Departments</a></li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#students" aria-expanded="false" aria-controls="students">
                        <i class="lni lni-user"></i><span>Student</span>
                    </a>
                    <ul id="students" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item"><a href="./add_student.php" class="sidebar-link">Add Student</a></li>
                        <li class="sidebar-item"><a href="./m_student.php" class="sidebar-link">Manage Student</a></li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#staff" aria-expanded="false" aria-controls="staff">
                        <i class="lni lni-users"></i><span>Staff</span>
                    </a>
                    <ul id="staff" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item"><a href="./add_staff.php" class="sidebar-link">Add Staff</a></li>
                        <li class="sidebar-item"><a href="./m_staff.php" class="sidebar-link">Manage Staff</a></li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="./add_course.php" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#portal" aria-expanded="false" aria-controls="portal">
                        <i class="lni lni-alarm"></i><span>Portal</span>
                    </a>
                    <ul id="portal" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item"><a href="./notification.php" class="sidebar-link">Notifications</a></li>
                        <li class="sidebar-item"><a href="./add_noti.php" class="sidebar-link"> Add Notifications</a></li>
                       
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#timetable" aria-expanded="false" aria-controls="timetable">
                        <i class="lni lni-calendar"></i><span>Timetable</span>
                    </a>
                    <ul id="timetable" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item"><a href="./add_tt.php" class="sidebar-link">Add Timetable</a></li>
                        <li class="sidebar-item"><a href="./select_staff_tt.php" class="sidebar-link">Add staff Timetable</a></li>

                        
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#oec" aria-expanded="false" aria-controls="oec">
                        <i class="lni lni-certificate"></i><span>OEC</span>
                    </a>
                    <ul id="oec" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item"><a href="./add_oec.php" class="sidebar-link">Add OEC</a></li>
                        <li class="sidebar-item"><a href="./m_oec.php" class="sidebar-link">Manage OEC</a></li>
                        <li class="sidebar-item"><a href="./oec_portal.php" class="sidebar-link">Portal Control</a></li>
                        <li class="sidebar-item"><a href="./dept_choose_oec.php" class="sidebar-link">Allotment</a></li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#pe" aria-expanded="false" aria-controls="pe">
                        <i class="lni lni-target"></i><span>Professional Elective</span>

                    </a>
                    <ul id="pe" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item"><a href="./add_pe.php" class="sidebar-link">Add PE</a></li>
                        <li class="sidebar-item"><a href="./m_pe.php" class="sidebar-link">Manage PE</a></li>
                        <li class="sidebar-item"><a href="./peportal.php" class="sidebar-link">Portal Control</a></li>
                       
                        <li class="sidebar-item"><a href="./pe_count.php" class="sidebar-link"> PE Count</a></li>

                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#batch" aria-expanded="false" aria-controls="batch">
                        <i class="lni lni-graduation"></i><span>Batch</span>
                    </a>
                    <ul id="batch" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item"><a href="./add_batch.php" class="sidebar-link">Add Batch</a></li>
                        <li class="sidebar-item"><a href="./m_batch.php" class="sidebar-link">Manage Batch</a></li>
                    </ul>
                </li>
            </ul>
            <div class="sidebar-footer"><a href="./logout.php" class="sidebar-link"><i class="lni lni-exit"></i><span>Logout</span></a></div>
        </aside>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script>
    const hamBurger = document.querySelector(".toggle-btn");

    hamBurger.addEventListener("click", function() {
            document.querySelector("#sidebar").classList.toggle("expand");
        }
    );
    </script>
</body>

</html>
