<?php
session_start();
require 'partials/_dbconnect.php';

if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'company'){
    header("location: login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $company_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $skills = $_POST['skills'];
    $target = $_POST['target_disability'];

    $sql = "INSERT INTO jobs (company_id, job_title, job_description, required_skills, target_disability_type, job_status) 
            VALUES ('$company_id', '$title', '$desc', '$skills', '$target', 'pending')";
            
    if(mysqli_query($conn, $sql)){
        header("location: index.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <title>Post Job</title>
</head>
<body>
    <?php require 'partials/_nav.php' ?>
    <div class="container my-5">
        <h2>Create New Job Post</h2>
        <form action="post_job.php" method="POST">
            <div class="form-group">
                <label>Job Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label>Required Skills</label>
                <input type="text" name="skills" class="form-control" placeholder="e.g. PHP, Excel" required>
            </div>
            <div class="form-group">
                <label>Target Disability Type (Who is this suitable for?)</label>
                <select name="target_disability" class="form-control">
                    <option value="locomotor">Locomotor Disability</option>
                    <option value="visual">Visual Impairment</option>
                    <option value="hearing">Hearing Impairment</option>
                    <option value="speech">Speech Impairment</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit for Admin Review</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
