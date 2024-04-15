<?php
session_start();
include('includes/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL); // Enable error reporting

if(isset($_POST['signup'])) {
    // Code for student ID
    $count_my_page = "studentid.txt";
    $hits = file($count_my_page);
    $hits[0]++;
    $fp = fopen($count_my_page, "w");
    fputs($fp, "$hits[0]");
    fclose($fp); 
    $StudentId = $hits[0];   
    $fname = $_POST['fullname'];
    $mobileno = $_POST['mobileno'];
    $email = $_POST['email']; 
    $password = $_POST['password']; // Don't use md5 here
    $status = 0; // Set status to 0 for inactive users

    // Hash the password using bcrypt
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Generate verification token
    $verification_token = bin2hex(random_bytes(16));

    $sql = "INSERT INTO tblstudents(StudentId,FullName,MobileNumber,EmailId,Password,Status,verification_token) VALUES(:StudentId,:fname,:mobileno,:email,:password,:status,:verification_token)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':StudentId', $StudentId, PDO::PARAM_STR);
    $query->bindParam(':fname', $fname, PDO::PARAM_STR);
    $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR); // Use the hashed password
    $query->bindParam(':status', $status, PDO::PARAM_INT);
    $query->bindParam(':verification_token', $verification_token, PDO::PARAM_STR);

    // Execute the database query
    if($query->execute()) {
        // Log sign-up action
        $userId = $dbh->lastInsertId(); // Get the ID of the inserted user
        $action = 'User signed up';
        logActivity($userId, $action);

        // Send verification email
        // ... Your email sending code here ...

        echo '<script>alert("Your Registration was successful. Please check your email for verification instructions.")</script>';
    } else {
        echo "<script>alert('Something went wrong. Please try again');</script>";
    }
}

// Function to log activity
function logActivity($userId, $action)
{
    global $dbh;
    $sql = "INSERT INTO activity_log (user_id, action) VALUES (:userId, :action)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
    $query->bindParam(':action', $action, PDO::PARAM_STR);
    $query->execute();
}
?>






<!-- Your HTML form goes here -->


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <![endif]-->
    <title>Online Library Management System | Student Signup</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <script type="text/javascript">
    function valid() {
        var password = document.getElementById('password').value;
        var confirmPassword = document.signup.confirmpassword.value;

        // Check if password meets complexity requirements
        var passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;
        if (!passwordRegex.test(password)) {
            document.getElementById('password-message').innerHTML = "Password must be at least 8 characters long, contain at least one uppercase letter, and at least one symbol (!@#$%^&*)";
            document.signup.password.focus();
            return false;
        } else {
            document.getElementById('password-message').innerHTML = "";
        }

        // Check if password and confirm password match
        if (password !== confirmPassword) {
            alert("Password and Confirm Password fields do not match!!");
            document.signup.confirmpassword.focus();
            return false;
        }
        
        return true;
    }
    </script>
    <script>
    function checkAvailability() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "check_availability.php",
            data:'emailid='+$("#emailid").val(),
            type: "POST",
            success:function(data){
                $("#user-availability-status").html(data);
                $("#loaderIcon").hide();
            },
            error:function (){}
        });
    }
    </script>    
</head>
<body>
    <!------MENU SECTION START-->
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">User Signup</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9 col-md-offset-1">
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                           SINGUP FORM
                        </div>
                        <div class="panel-body">
                            <form name="signup" method="post" onSubmit="return valid();">
                                <div class="form-group">
                                    <label>Enter Full Name</label>
                                    <input class="form-control" type="text" name="fullname" pattern="[A-Za-z ]+" title="Full name should only contain letters and spaces" autocomplete="off" required />
                                </div>
                                <div class="form-group">
                                    <label>Mobile Number :</label>
                                    <input class="form-control" type="text" name="mobileno" maxlength="11" autocomplete="off" required />
                                </div>                                     
                                <div class="form-group">
                                    <label>Enter Email</label>
                                    <input class="form-control" type="email" name="email" id="emailid" onBlur="checkAvailability()"  autocomplete="off" required />
                                    <span id="user-availability-status" style="font-size:12px;"></span> 
                                </div>
                                <div class="form-group">
                                    <label>Enter Password</label>
                                    <input class="form-control" type="password" name="password" id="password" autocomplete="off" required />
                                    <div id="password-message" style="font-size: 12px; color: red;"></div>
                                </div>
                                <div class="form-group">
                                    <label>Confirm Password </label>
                                    <input class="form-control" type="password" name="confirmpassword" autocomplete="off" required />
                                </div>
                                <button type="submit" name="signup" class="btn btn-danger" id="submit">Register Now </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CONTENT-WRAPPER SECTION END-->
    <?php include('includes/footer.php');?>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
    <!-- CUSTOM SCRIPTS 
