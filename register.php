<?php
require 'partials/_dbconnect.php';
$showAlert = false;
$showError = false;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];
    $role = $_POST["role"];
    $full_name = $_POST['full_name'];

    // Check if email exists
    $existSql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $existSql);
    
    if(mysqli_num_rows($result) > 0){
        $showError = "Email already exists!";
    } elseif($password == $cpassword){
        
        $sql = "";
        
        if($role == 'beneficiary'){
            $disability_type = $_POST['disability_type'];
            $skills = $_POST['skills'];
            
            // --- FILE UPLOAD LOGIC ---
            $target_dir = "uploads/";
            // Create unique name to prevent overwriting
            $file_name = time() . "_" . basename($_FILES["disability_card"]["name"]);
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["disability_card"]["tmp_name"], $target_file)) {
                // Success: Insert into DB
                $sql = "INSERT INTO users (full_name, email, password, role, disability_type, skills, disability_card_url, account_status) 
                        VALUES ('$full_name', '$email', '$password', 'beneficiary', '$disability_type', '$skills', '$target_file', 'pending')";
            } else {
                $showError = "Failed to upload file.";
            }

        } elseif($role == 'company'){
            $company_location = $_POST['company_location'];
            $sql = "INSERT INTO users (full_name, email, password, role, company_location, account_status) 
                    VALUES ('$full_name', '$email', '$password', 'company', '$company_location', 'pending')";
        }

        if($sql != ""){
            $result = mysqli_query($conn, $sql);
            if($result){
                $showAlert = true;
            } else {
                $showError = "Database Error: " . mysqli_error($conn);
            }
        }
        
    } else {
        $showError = "Passwords do not match";
    }
}
?>

<!doctype html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <title>Register</title>
    </head>
<body>
    <?php require 'partials/_nav.php' ?>
    
    <?php
    if($showAlert){
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Your account is created and is <b>Pending Admin Approval</b>.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
    }
    if($showError){
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> '. $showError .'
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
    }
    ?>

    <div class="container my-4">
        <h1 class="text-center">Sign Up</h1>
        
        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="ben-tab" data-toggle="tab" href="#beneficiary" role="tab">As Beneficiary</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="comp-tab" data-toggle="tab" href="#company" role="tab">As Company</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            
            <div class="tab-pane fade show active" id="beneficiary" role="tabpanel">
                <form action="register.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="role" value="beneficiary">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control" name="cpassword" required>
                    </div>
                    <div class="form-group">
                        <label>Disability Type</label>
                        <select class="form-control" name="disability_type" required>
                            <option value="locomotor">Locomotor Disability</option>
                            <option value="visual">Visual Impairment</option>
                            <option value="hearing">Hearing Impairment</option>
                            <option value="speech">Speech Impairment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Skills (comma separated)</label>
                        <input type="text" class="form-control" name="skills" placeholder="e.g. PHP, Data Entry" required>
                    </div>
                    <div class="form-group">
                        <label>Upload Disability Card (PDF/Image)</label>
                        <input type="file" class="form-control-file" name="disability_card" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
            </div>

            <div class="tab-pane fade" id="company" role="tabpanel">
                <form action="register.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="role" value="company">
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Company Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control" name="cpassword" required>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" class="form-control" name="company_location" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Register Company</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>