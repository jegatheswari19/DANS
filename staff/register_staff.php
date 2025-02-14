<?php
error_reporting(E_ALL);
include('../db_con.php');
include('../config.php');

if (isset($_POST['submit'])) {
    $staff_id = $_POST['staff_id'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $ret = mysqli_query($con, "SELECT username FROM staff_login WHERE username='$email' OR staff_id='$staff_id'");
    $result = mysqli_fetch_array($ret);

    if ($result > 0) {
        echo "<script>alert('You are already registered');</script>";
        header('Location: ./login_staff.php');
    } else {

        $query = mysqli_prepare($con, "INSERT INTO staff_login (staff_id, username, password_hash) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($query, "sss", $staff_id, $email, $password);
        $execute = mysqli_stmt_execute($query);

        if ($execute) {
            echo "<script>alert('You have successfully registered.');</script>";
        } else {
            echo "<script>alert('Something Went Wrong. Please try again.');</script>";
            echo "Error: " . mysqli_error($con);
        }

        mysqli_stmt_close($query);
    }

    mysqli_close($con);
}
?>-

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/images/ptu-logo.png">

    <title>staff Registration</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            font-size: 16px;
            font-weight: 400;
            color: #666666;
            background: #eaeff4;
        }

        .wrapper_sign {
            margin: 0 auto;
            width: 100%;
            max-width: 1140px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .s_container {
            position: relative;
            width: 100%;
            max-width: 600px;
            height: auto;
            display: flex;
            background: #ffffff;
            box-shadow: 0 10px 10px #999999;
        }

        .sign_left,
        .sign_right {
            padding: 30px;
            display: flex;
        }

        .sign_left {
            width: 40%;
            clip-path: inset (10px 50px #a12a96);
            background: #a12a96;
        }

        .sign_right {
            padding: 60px 30px;
            width: 50%;
            margin-left: -10%;
            margin-left: 30px
        }

        .sign_text {
            position: relative;
            width: 100%;
            color: #ffffff;
        }

        .sign_text h2 {
            margin: 0 0 15px 0;
            font-size: 30px;
            font-weight: 700;
        }

        .sign_text p {
            margin: 0 0 20px 0;
            font-size: 16px;
            font-weight: 500;
            line-height: 22px;
        }

        .btn {
            display: inline-block;
            font-family: 'Poppins';
            padding: 7px 20px;
            font-size: 16px;
            letter-spacing: 1px;
            text-decoration: none;
            border-radius: 30px;
            color: #ffffff;
            outline: none;
            border: 1px solid #ffffff;
            box-shadow: inset 0 0 0 0 #ffffff;
            transition: .3s;
        }

        .btn:hover {
            color: #a12a96;
            border: 1px solid #ffffff;
            box-shadow: inset 150px 0 0 0 #ffffff;
            cursor: pointer;
        }

        .sign-form {
            position: relative;
            width: 100%;
        }

        .sign-form h2 {
            margin: 0 0 15px 0;
            font-size: 22px;
            font-weight: 700;
        }

        .sign-form p {
            margin: 0 0 10px 0;
            text-align: left;
            color: #666666;
            font-size: 15px;
        }

        .sign-form p:last-child {
            margin: 0;
            padding-top: 3px;
        }

        .sign-form p a {
            color: #a12a96;
            font-size: 14px;
            text-decoration: none;
        }

        .sign-form label {
            display: block;
            width: 100%;
            margin-bottom: 2px;
            letter-spacing: .5px;
        }

        .sign-form p:last-child label {
            width: 60%;
            float: left;
        }

        .sign-form label span {
            color: #ff574e;
            padding-left: 2px;
        }

        .sign-form input {
            display: block;
            width: 100%;
            height: 35px;
            padding: 0 10px;
            outline: none;
            border: 1px solid #cccccc;
            border-radius: 30px;
        }

        .sign-form input:focus {
            border-color: #ff574e;
        }

        .sign-form button,
        .sign-form input[type=submit] {
            display: inline-block;
            width: 100%;
            margin-top: 5px;
            color: #a12a96;
            font-size: 16px;
            letter-spacing: 1px;
            cursor: pointer;
            background: transparent;
            border: 1px solid #a12a96;
            border-radius: 30px;
            box-shadow: inset 0 0 0 0 #a12a96;
            transition: .3s;
        }

        .sign-form button:hover,
        .sign-form input[type=submit]:hover {
            color: #ffffff;
            box-shadow: inset 250px 0 0 0 #a12a96;
        }

        @media(max-width: 575px) {
            .container {
                flex-direction: column;
                box-shadow: none;
            }

            .sign_left,
            .sign_right {
                width: 100%;
                margin: 0;
                clip-path: none;
            }

            .sign_right {
                padding: 30px;
            }
        }
    </style>
</head>

<body>
    <script type="text/javascript">
        function checkpass() {
            if (document.signup.password.value != document.signup.confirm_password.value) {
                alert('passwords does not match');
                document.signup.confirm_password.focus();
                return false;
            }
            return true;
        }
    </script>

    <div class="wrapper_sign">
        <div class="s_container">
            <div class="sign_left">
                <div class="sign_text">
                    <h2>Already have an account?</h2>

                    <a class='btn' href="./login_staff.php">Log in</a>
                </div>
            </div>
            <div class="sign_right">
                <div class="sign-form">
                    <h2>staff Registration</h2>

                    <form method="post" name="signup" onsubmit=" return checkpass();">
                        <p>
                            <label>staff ID<span>*</span></label>
                            <input type=" text" name="staff_id" placeholder="staff id" required />
                        </p>

                        <p>
                            <label>Username/Email address<span>*</span></label>
                            <input type="email" name="email" placeholder="Username or Email" required />
                        </p>

                        <p>
                            <label>Password<span>*</span></label>
                            <input type="password" name="password" placeholder="Password" required />
                        </p>
                        <p>
                            <label>Password<span>*</span></label>
                            <input type="password" name="confirm_password" placeholder="Confirm Password" required />
                        </p>
                        <p>
                            <input type="submit" name="submit" value="Sign Up" />
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>


</body>

</html>