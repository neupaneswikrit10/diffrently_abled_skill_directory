<?php
session_start();
require 'partials/_dbconnect.php';

// Security Checks
if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'beneficiary'){
    header("location: login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $job_id = $_POST['job_id'];
    $user_id = $_SESSION['user_id'];
    
    $sql = "INSERT INTO applications (job_id, user_id, app_status) VALUES ('$job_id', '$user_id', 'pending_admin')";
    mysqli_query($conn, $sql);
}

header("location: index.php");
exit;
?>
