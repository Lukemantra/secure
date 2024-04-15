<?php
session_start();
include('includes/config.php');

if(isset($_GET['token'])) {
    $verification_token = $_GET['token'];

    // Check if the verification token exists in the database
    $sql = "SELECT * FROM tblstudents WHERE verification_token = :verification_token";
    $query = $dbh->prepare($sql);
    $query->bindParam(':verification_token', $verification_token, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if($result) {
        // Update the user's status to active
        $sql = "UPDATE tblstudents SET Status = :status WHERE verification_token = :verification_token";
        $query = $dbh->prepare($sql);
        $status = 1; // Set status to active
        $query->bindParam(':status', $status, PDO::PARAM_INT);
        $query->bindParam(':verification_token', $verification_token, PDO::PARAM_STR);
        $query->execute();

        echo "Your email has been successfully verified. You can now login.";
    } else {
        echo "Invalid verification link.";
    }
} else {
    echo "Verification link not provided.";
}
?>
