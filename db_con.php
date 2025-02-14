<?php
$servername = "127.0.0.1";
$username = "root";
$password = "Jega@2004";
$dbname = "dans";

$con = mysqli_connect($servername, $username, $password, $dbname);
if (mysqli_connect_errno()) {
    echo "connection Fail" . mysqli_connect_error();
}
