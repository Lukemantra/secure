<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Check if admin is logged in
if(strlen($_SESSION['alogin']) == 0) { 
    header('location:index.php');
} else {
    // Check if a specific action is performed
    if(isset($_GET['action'])) {
        $action = $_GET['action'];
        
        // Log the admin activity
        $adminId = $_SESSION['alogin'];
        logAdminActivity($adminId, $action);
    }
?>
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
    <title>Online Library Management System | Admin Dash Board</title>
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
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">ADMIN DASHBOARD</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <a href="manage-books.php?action=view_books">
                        <div class="alert alert-success back-widget-set text-center">
                            <i class="fa fa-book fa-5x"></i>
                            <?php 
                            // Retrieve number of books listed
                            $sql ="SELECT id FROM tblbooks";
                            $query = $dbh->prepare($sql);
                            $query->execute();
                            $listdbooks = $query->rowCount();
                            ?>
                            <h3><?php echo htmlentities($listdbooks);?></h3>
                            Books Listed
                        </div>
                    </a>
                </div>

                <div class="col-md-3 col-sm-3 col-xs-6">
                    <a href="reg-students.php?action=view_registered_students">
                        <div class="alert alert-danger back-widget-set text-center">
                            <i class="fa fa-users fa-5x"></i>
                            <?php 
                            // Retrieve number of registered students
                            $sql3 ="SELECT id FROM tblstudents";
                            $query3 = $dbh->prepare($sql3);
                            $query3->execute();
                            $regstds = $query3->rowCount();
                            ?>
                            <h3><?php echo htmlentities($regstds);?></h3>
                            Registered Users
                        </div>
                    </a>
                </div>

                <div class="col-md-3 col-sm-3 col-xs-6">
                    <a href="manage-authors.php?action=view_authors">
                        <div class="alert alert-success back-widget-set text-center">
                            <i class="fa fa-user fa-5x"></i>
                            <?php 
                            // Retrieve number of authors listed
                            $sql4 ="SELECT id FROM tblauthors";
                            $query4 = $dbh->prepare($sql4);
                            $query4->execute();
                            $listdathrs = $query4->rowCount();
                            ?>
                            <h3><?php echo htmlentities($listdathrs);?></h3>
                            Authors Listed
                        </div>
                    </a>
                </div>

                <div class="col-md-3 col-sm-3 rscol-xs-6">
                    <a href="manage-categories.php?action=view_categories">
                        <div class="alert alert-info back-widget-set text-center">
                            <i class="fa fa-file-archive-o fa-5x"></i>
                            <?php 
                            // Retrieve number of listed categories
                            $sql5 ="SELECT id FROM tblcategory";
                            $query5 = $dbh->prepare($sql5);
                            $query5->execute();
                            $listdcats = $query5->rowCount();
                            ?>
                            <h3><?php echo htmlentities($listdcats);?></h3>
                            Listed Categories
                        </div>
                    </a>
                </div>
            </div>             
        </div>
    </div>

    <!-- CONTENT-WRAPPER SECTION END -->
    <?php include('includes/footer.php');?>
    <!-- FOOTER SECTION END -->

    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME -->
    <!-- CORE JQUERY -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.js"></script>
    <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
</body>
</html>
<?php 
}

// Function to log admin activity
function logAdminActivity($adminId, $action) {
    global $dbh;
    $sql = "INSERT INTO admin_activity_log (admin_id, action) VALUES (:adminId, :action)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':adminId', $adminId, PDO::PARAM_INT);
    $query->bindParam(':action', $action, PDO::PARAM_STR);
    $query->execute();
}


?>
