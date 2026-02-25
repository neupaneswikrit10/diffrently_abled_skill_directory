<?php
require 'partials/_dbconnect.php';
$login = false;
$showError = false;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        
        // Gatekeeper Check
        if($row['account_status'] == 'pending'){
            $showError = "Your account is still pending Admin approval.";
        } elseif($row['account_status'] == 'rejected'){
            $showError = "Your account has been rejected.";
        } else {
            // Success
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $row['role'];
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['full_name'] = $row['full_name'];
            
            if($row['role'] == 'beneficiary'){
                $_SESSION['disability_type'] = $row['disability_type'];
            }

            if($row['role'] == 'admin'){
                header("location: admin_dashboard.php");
            } else {
                header("location: index.php");
            }
            exit;
        }
    } else {
        $showError = "Invalid Credentials";
    }
}
?>

<!doctype html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <title>Login</title>
    </head>
<body>
    <?php require 'partials/_nav.php' ?>

    <?php
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
        <h1 class="text-center">Login to System</h1>
        <form action="login.php" method="post" style="max-width: 500px; margin: auto;">
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
            <br>
            <p class="text-center"><small class="text-muted">Admin credentials managed by database</small></p>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>