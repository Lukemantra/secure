<?php
session_start();
include('includes/config.php');
error_reporting(0);
if(strlen($_SESSION['login'])==0)
{   
    header('location:index.php');
}
else
{ 
    if(isset($_POST['change']))
    {
        $password = $_POST['password'];
        $newpassword = $_POST['newpassword'];
        $confirmpassword = $_POST['confirmpassword'];
        $email = $_SESSION['login'];

        // Fetching the user's current password hash from the database
        $sql = "SELECT Password FROM tblstudents WHERE EmailId = :email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if($user)
        {
            // Verify if the entered current password matches the stored hash
            if(password_verify($password, $user['Password']))
            {
                // Check if new password and confirm password match
                if($newpassword === $confirmpassword)
                {
                    // Hash the new password
                    $hashed_password = password_hash($newpassword, PASSWORD_DEFAULT);
                    
                    // Update the password in the database
                    $sql = "UPDATE tblstudents SET Password = :password WHERE EmailId = :email";
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                    $stmt->execute();
                    
                    $msg = "Your password has been changed successfully.";
                }
                else
                {
                    $error = "New password and confirm password do not match.";
                }
            }
            else
            {
                $error = "Your current password is incorrect.";
            }
        }
        else
        {
            $error = "User not found.";
        }
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
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap{
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
    </style>
</head>
<script type="text/javascript">
    function valid()
    {
        if(document.chngpwd.newpassword.value != document.chngpwd.confirmpassword.value)
        {
            alert("New Password and Confirm Password do not match!");
            document.chngpwd.confirmpassword.focus();
            return false;
        }
        return true;
    }
</script>

<body>
    <!------MENU SECTION START-->
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">User Change Password</h4>
                </div>
            </div>
            <?php 
            if($error)
            {
                echo '<div class="errorWrap"><strong>ERROR</strong>: ' . htmlentities($error) . '</div>';
            } 
            elseif($msg)
            {
                echo '<div class="succWrap"><strong>SUCCESS</strong>: ' . htmlentities($msg) . '</div>';
            }
            ?>            
            <!--LOGIN PANEL START-->           
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Change Password
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post" onSubmit="return valid();" name="chngpwd">

                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input class="form-control" type="password" name="password" autocomplete="off" required  />
                                </div>

                                <div class="form-group">
                                    <label>Enter Password</label>
                                    <input class="form-control" type="password" name="newpassword" autocomplete="off" required  />
                                </div>

                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input class="form-control" type="password" name="confirmpassword" autocomplete="off" required  />
                                </div>

                                <button type="submit" name="change" class="btn btn-info">Change</button> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>  
            <!---LOGIN PANEL END-->            
        </div>
    </div>
    <!-- CONTENT-WRAPPER SECTION END-->
    <?php include('includes/footer.php');?>
    <!-- FOOTER SECTION END-->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
    <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>
</body>
</html>
