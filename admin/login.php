<?php
session_start();

if (isset($_SESSION['uid'])) {
    header("Location: index.php");
    exit();
}


include('../db_con.php');

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $stmt = $con->prepare("SELECT id FROM admin WHERE email=? AND password=?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();

    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id);
        $stmt->fetch();
        $_SESSION['loggedin'] = true;
        $_SESSION['uid'] = $id;
        session_regenerate_id();
        echo "<script>alert('Login success');</script>";
        header('Location: index.php');
        exit();
    } else {
        echo "<script>alert('Invalid details');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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

        .wrapper {
            margin: 0 auto;
            width: 100%;
            max-width: 1140px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .container {
            position: relative;
            width: 100%;
            max-width: 600px;
            height: auto;
            display: flex;
            background: #ffffff;
            box-shadow: 0 10px 10px #999999;
        }

        .login .col-left,
        .login .col-right {
            padding: 30px;
            display: flex;
        }

        .login .col-left {
            width: 60%;
            clip-path: polygon(25% 0%, 75% 0%, 10% 50%, 75% 100%, 25% 100%, 0% 50%);
            background: #002855;
        }


        .login .col-right {
            padding: 60px 30px;
            width: 50%;
            margin-left: -10%;
        }


        .login .login-form {
            position: relative;
            width: 100%;
        }

        .login .login-form h2 {
            margin: 0 0 15px 0;
            font-size: 22px;
            font-weight: 700;
        }

        .login .login-form p {
            margin: 0 0 10px 0;
            text-align: left;
            color: #666666;
            font-size: 15px;
        }

        .login .login-form p:last-child {
            margin: 0;
            padding-top: 3px;
        }

        .login .login-form p a {
            color: #002855;
            font-size: 14px;
            text-decoration: none;
        }

        .login .login-form label {
            display: block;
            width: 100%;
            margin-bottom: 2px;
            letter-spacing: .5px;
        }

        .login .login-form p:last-child label {
            width: 60%;
            float: left;
        }

        .login .login-form label span {
            color: #002855;
            padding-left: 2px;
        }

        .login .login-form input {
            display: block;
            width: 100%;
            height: 35px;
            padding: 0 10px;
            outline: none;
            border: 1px solid #cccccc;
            border-radius: 30px;
        }

        .login .login-form input:focus {
            border-color: #ff574e;
        }

        .login .login-form button,
        .login .login-form input[type=submit] {
            display: inline-block;
            width: 100%;
            margin-top: 5px;
            color: #002855;
            font-size: 16px;
            letter-spacing: 1px;
            cursor: pointer;
            background: transparent;
            border: 1px solid #002855;
            border-radius: 30px;
            box-shadow: inset 0 0 0 0 #002855;
            transition: .3s;
        }

        .login .login-form button:hover,
        .login .login-form input[type=submit]:hover {
            color: #ffffff;
            box-shadow: inset 250px 0 0 0 #002855;
        }

        @media(max-width: 575px) {
            .login .container {
                flex-direction: column;
                box-shadow: none;
            }

            .login .col-left,
            .login .col-right {
                width: 100%;
                margin: 0;
                clip-path: none;
            }

            .login .col-right {
                padding: 30px;
            }


            .col-left .text {
                color: white;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper login">
        <div class="container">
            <div class="col-left">
            </div>
            <div class="col-right">
                <div class="login-form">
                    <h2> Admin Login</h2>
                    <form method="post">
                        <p>
                            <label>Username/Email address<span>*</span></label>
                            <input type="text" name="email" placeholder="Username or Email" required />
                        </p>
                        <p>
                            <label>Password<span>*</span></label>
                            <input type="password" name="password" placeholder="Password" required />
                        </p>
                        <p>
                            <input type="submit" name="login" value=" Sign in" />
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>