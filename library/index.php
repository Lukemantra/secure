<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Set the time zone to Kuala Lumpur (Malaysia)
date_default_timezone_set('Asia/Kuala_Lumpur');

// Maximum number of failed attempts allowed
$maxAttempts = 3;

// Lockout time after too many failed attempts (in seconds)
$lockoutTime = 1 * 60; // 5 minutes

if($_SESSION['login'] != ''){
    $_SESSION['login'] = '';
}

if(isset($_POST['login'])) {
    $email = $_POST['emailid'];
    $password = $_POST['password'];

    // Prevent multiple failed login attempts
    if(isset($_SESSION['failed_login_attempts'])) {
        $_SESSION['failed_login_attempts']++;
    } else {
        $_SESSION['failed_login_attempts'] = 1;
    }

    if($_SESSION['failed_login_attempts'] > $maxAttempts) {
        if(time() - $_SESSION['last_failed_attempt'] < $lockoutTime) {
            echo "<script>alert('Too many failed login attempts. Please try again later.');</script>";
            exit; // or redirect to another page
        } else {
            // Reset the failed login attempt count
            $_SESSION['failed_login_attempts'] = 1;
        }
    }

    // Record the time of this failed attempt
    $_SESSION['last_failed_attempt'] = time();

    // Retrieve the hashed password from the database
    $sql = "SELECT EmailId, Password, StudentId, Status FROM tblstudents WHERE EmailId = :email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if($user) {
        // Verify the password using password_verify function
        if(password_verify($password, $user['Password'])) {
            $_SESSION['stdid'] = $user['StudentId'];
            if($user['Status'] == 1) {
                $_SESSION['login'] = $email;
                // Log successful login attempt with time and email
                $log_message = "[" . date('Y-m-d H:i:s') . "] Successful login for email: $email" . PHP_EOL;
                file_put_contents("successful_login.log", $log_message, FILE_APPEND);
                echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
            } else {
                echo "<script>alert('Your Account Has been blocked. Please contact admin');</script>";
            }
        } else {
            // Log failed login attempt with time and date
            $log_message = "[" . date('Y-m-d H:i:s') . "] Failed login attempt for email: $email" . PHP_EOL;
            file_put_contents("failed_login.log", $log_message, FILE_APPEND);
            echo "<script>alert('Invalid Password');</script>";
        }
    } else {
        // Log failed login attempt with time and date
        $log_message = "[" . date('Y-m-d H:i:s') . "] Failed login attempt for email: $email (User not found)" . PHP_EOL;
        file_put_contents("failed_login.log", $log_message, FILE_APPEND);
        echo "<script>alert('User not found');</script>";
    }
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | </title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
    <!------MENU SECTION START-->
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
        <div class="container">
            <!--Slider---->
            <div class="row">
                <div class="col-md-10 col-sm-8 col-xs-12 col-md-offset-1">
                    <div id="carousel-example" class="carousel slide slide-bdr" data-ride="carousel" >
                        <div class="carousel-inner">
                            <div class="item active">
                                <img src="assets/img/1.jpg" alt="" />
                            </div>
                            <div class="item">
                                <img src="assets/img/2.jpg" alt="" />
                            </div>
                            <div class="item">
                                <img src="assets/img/3.jpg" alt="" /> 
                            </div>
                        </div>
                        <!--INDICATORS-->
                        <ol class="carousel-indicators">
                            <li data-target="#carousel-example" data-slide-to="0" class="active"></li>
                            <li data-target="#carousel-example" data-slide-to="1"></li>
                            <li data-target="#carousel-example" data-slide-to="2"></li>
                        </ol>
                        <!--PREVIUS-NEXT BUTTONS-->
                        <a class="left carousel-control" href="#carousel-example" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left"></span>
                        </a>
                        <a class="right carousel-control" href="#carousel-example" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right"></span>
                        </a>
                    </div>
                </div>
            </div>
            <hr />

            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">USER LOGIN FORM</h4>
                </div>
            </div>
            <a name="ulogin"></a>            
            <!--LOGIN PANEL START-->           
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" >
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            LOGIN FORM
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post">

                                <div class="form-group">
                                    <label>Enter Email id</label>
                                    <input class="form-control" type="text" name="emailid" required autocomplete="off" />
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input class="form-control" type="password" name="password" required autocomplete="off"  />
                                    <p class="help-block"><a href="user-forgot-password.php">Forgot Password</a></p>
                                </div>

                                <button type="submit" name="login" class="btn btn-info">LOGIN </button> | <a href="signup.php">Not Register Yet</a>
