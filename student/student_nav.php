<?php
session_start();
include(realpath(dirname(__FILE__) . '/../config.php'));

if (!isset($_SESSION['uid']) && !isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/images/ptu-logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Student Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
        }

        nav {
            background-color: #003366;
            
            box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 10px 20px;
        }

        nav li {
            height: 60px;
        }

        nav a {
            height: 100%;
            padding: 0 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            color: white;
            font-family: 'Montserrat', sans-serif;
        }

        nav a:hover {
            background-color:##003366;
            color: white;
            transition: background-color 0.3s ease;
        }

        nav li:first-child {
            margin-right: auto;
        }

        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            height: 100vh;
            width: 250px;
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            box-shadow: -10px 0 10px rgba(0, 0, 0, 0.1);
            list-style: none;
            display: none;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            padding: 20px;
        }

        .sidebar li {
            width: 100%;
            margin: 10px 0;
        }

        .sidebar a {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            color: #003366;
            font-family: 'Roboto', sans-serif;
        }

        .sidebar a:hover {
            background-color:##003366;
            color: white;
        }

        .menu-button {
            display: none;
            cursor: pointer;
        }

        @media(max-width: 800px) {
            .hideOnMobile {
                display: none;
            }

            .menu-button {
                display: block;
            }

            .sidebar {
                display: none;
            }
        }

        @media(max-width: 400px) {
            .sidebar {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <script>
        function showSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.display = 'flex';
        }

        function hideSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.display = 'none';
        }
    </script>
    <nav>
        <ul class="sidebar">
            <li onclick="hideSidebar()"><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="26" viewBox="0 96 960 960" width="26">
                        <path d="m249 849-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z" />
                    </svg></a></li>
                    <li ><a href="<?php echo BASE_URL; ?>/student/student_home.php">Home</a></li>
            <li ><a href="<?php echo BASE_URL; ?>/DAM/student/atte_percentage.php">Attendance</a></li>
            <li ><a href="<?php echo BASE_URL; ?>/PANB/student/noti.php">Notifications</a></li>
            <li ><a href="<?php echo BASE_URL; ?>/DAM/student/od_student.php">Od</a></li>
            <li ><a href="<?php echo BASE_URL; ?>/logout.php">Logout</a></li>
        </ul>
        <ul>
            <li><a href="#"><span class="sideheading">DANS | Student Portal</span></a></li>
            <li class="hideOnMobile"><a href="<?php echo BASE_URL; ?>/student/student_home.php">Home</a></li>
            <li class="hideOnMobile"><a href="<?php echo BASE_URL; ?>/DAM/student/atte_percentage.php">Attendance</a></li>
            <li class="hideOnMobile"><a href="<?php echo BASE_URL; ?>/PANB/student/noti.php">Notifications</a></li>
            <li class="hideOnMobile"><a href="<?php echo BASE_URL; ?>/DAM/student/od_student.php">Od</a></li>
            <li class="hideOnMobile"><a href="<?php echo BASE_URL; ?>/logout.php">Logout</a></li>
            <li class="menu-button" onclick="showSidebar()"><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="26" viewBox="0 96 960 960" width="26">
                        <path d="M120 816v-60h720v60H120Zm0-210v-60h720v60H120Zm0-210v-60h720v60H120Z" />
                    </svg></a></li>
        </ul>
    </nav>
</body>

</html>
