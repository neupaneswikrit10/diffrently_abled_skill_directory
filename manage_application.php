<?php
session_start();
require 'partials/_dbconnect.php';

if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'company'){
    header("location: login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $app_id = $_POST['app_id'];
    $action = $_POST['action']; 
    
    $status = ($action == 'hire') ? 'hired' : 'rejected_by_company';
    
    $sql = "UPDATE applications SET app_status='$status' WHERE app_id='$app_id'";
    mysqli_query($conn, $sql);
}

header("location: index.php");
exit;
?>