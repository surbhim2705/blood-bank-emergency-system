<?php
session_start();
include('includes/config.php');

// Protect page
if(!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

if(isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    $donor_id = $_SESSION['donor_id'];

    // Ensure the request belongs to the logged-in donor
    $sql = "UPDATE blood_requests SET status = :status WHERE id = :id AND donor_id = :donor_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->bindParam(':donor_id', $donor_id, PDO::PARAM_INT);
    
    if($query->execute()) {
        header("Location: donor-dashboard.php?msg=Status updated successfully");
    } else {
        header("Location: donor-dashboard.php?error=Failed to update status");
    }
} else {
    header("Location: donor-dashboard.php");
}
exit();
?>
