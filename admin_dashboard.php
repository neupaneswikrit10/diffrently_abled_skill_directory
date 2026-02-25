<?php
session_start();
require 'partials/_dbconnect.php';

// Security Check: Only Admin Allowed
if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin'){
    header("location: login.php");
    exit;
}

// --- ACTION HANDLER (POST) ---
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    // 1. User Approval
    if(isset($_POST['approve_user_id'])){
        $id = $_POST['approve_user_id'];
        mysqli_query($conn, "UPDATE users SET account_status='active' WHERE user_id=$id");
    }
    if(isset($_POST['reject_user_id'])){
        $id = $_POST['reject_user_id'];
        mysqli_query($conn, "DELETE FROM users WHERE user_id=$id");
    }

    // 2. Job Approval
    if(isset($_POST['publish_job_id'])){
        $id = $_POST['publish_job_id'];
        mysqli_query($conn, "UPDATE jobs SET job_status='open' WHERE job_id=$id");
    }
    if(isset($_POST['reject_job_id'])){
        $id = $_POST['reject_job_id'];
        mysqli_query($conn, "DELETE FROM jobs WHERE job_id=$id");
    }
    if(isset($_POST['close_job_id'])){
        $id = $_POST['close_job_id'];
        mysqli_query($conn, "UPDATE jobs SET job_status='closed' WHERE job_id=$id");
    }

    // 3. Application Vetting
    if(isset($_POST['verify_app_id'])){
        $id = $_POST['verify_app_id'];
        mysqli_query($conn, "UPDATE applications SET app_status='eligible' WHERE app_id=$id");
    }
    if(isset($_POST['reject_app_id'])){
        $id = $_POST['reject_app_id'];
        mysqli_query($conn, "DELETE FROM applications WHERE app_id=$id");
    }
    
    header("Location: admin_dashboard.php");
    exit;
}

// --- DATA FETCHING (SQL) ---
$users_sql = "SELECT * FROM users";
$users_result = mysqli_query($conn, $users_sql);

$jobs_sql = "SELECT jobs.*, users.full_name as company_name FROM jobs JOIN users ON jobs.company_id = users.user_id";
$jobs_result = mysqli_query($conn, $jobs_sql);

$apps_sql = "SELECT applications.*, users.full_name as user_name, jobs.job_title 
            FROM applications 
            JOIN users ON applications.user_id = users.user_id 
            JOIN jobs ON applications.job_id = jobs.job_id";
$apps_result = mysqli_query($conn, $apps_sql);
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <title>Admin Dashboard</title>
    </head>
<body>
    <?php require 'partials/_nav.php' ?>

    <div class="container-fluid my-4">
        <h2 class="mb-4">Admin Control Center</h2>

        <ul class="nav nav-tabs" id="adminTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="users-tab" data-toggle="tab" href="#users" role="tab">New Users (Pending)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="jobs-tab" data-toggle="tab" href="#jobs" role="tab">Job Approvals</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="apps-tab" data-toggle="tab" href="#apps" role="tab">Application Vetting</a>
            </li>
        </ul>

        <div class="tab-content my-3" id="adminTabContent">

            <div class="tab-pane fade show active" id="users" role="tabpanel">
                <h4>Pending Beneficiaries</h4>
                <table class="table table-bordered">
                    <thead class="thead-dark"><tr><th>Name</th><th>Email</th><th>Disability</th><th>Proof</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php while($u = mysqli_fetch_assoc($users_result)): ?>
                            <?php if($u['account_status'] == 'pending' && $u['role'] == 'beneficiary'): ?>
                            <tr>
                                <td><?php echo $u['full_name']; ?></td>
                                <td><?php echo $u['email']; ?></td>
                                <td><?php echo $u['disability_type']; ?></td>
                                <td><a href="<?php echo $u['disability_card_url']; ?>" target="_blank" class="btn btn-sm btn-info">View Card</a></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="approve_user_id" value="<?php echo $u['user_id']; ?>">
                                        <button class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="reject_user_id" value="<?php echo $u['user_id']; ?>">
                                        <button class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <h4 class="mt-4">Pending Companies</h4>
                <table class="table table-bordered">
                    <thead class="thead-light"><tr><th>Company Name</th><th>Email</th><th>Location</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php 
                        // Reset pointer for companies
                        mysqli_data_seek($users_result, 0);
                        while($u = mysqli_fetch_assoc($users_result)): ?>
                            <?php if($u['account_status'] == 'pending' && $u['role'] == 'company'): ?>
                            <tr>
                                <td><?php echo $u['full_name']; ?></td>
                                <td><?php echo $u['email']; ?></td>
                                <td><?php echo $u['company_location']; ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="approve_user_id" value="<?php echo $u['user_id']; ?>">
                                        <button class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="jobs" role="tabpanel">
                <h4>Jobs Awaiting Publication</h4>
                <table class="table table-bordered">
                    <thead class="thead-dark"><tr><th>Company</th><th>Job Title</th><th>Target Disability</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php while($j = mysqli_fetch_assoc($jobs_result)): ?>
                            <?php if($j['job_status'] == 'pending'): ?>
                            <tr>
                                <td><?php echo $j['company_name']; ?></td>
                                <td><?php echo $j['job_title']; ?></td>
                                <td><span class="badge badge-warning"><?php echo $j['target_disability_type']; ?></span></td>
                                <td>Pending</td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="publish_job_id" value="<?php echo $j['job_id']; ?>">
                                        <button class="btn btn-primary btn-sm">Publish Job</button>
                                    </form>
                                    <form method="POST" style="display:inline; margin-left:6px;">
                                        <input type="hidden" name="reject_job_id" value="<?php echo $j['job_id']; ?>">
                                        <button class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="apps" role="tabpanel">
                <h4>Verify Candidates (The Filter)</h4>
                <div class="alert alert-info">Only approve if the user's skills match the job description.</div>
                
                <table class="table table-bordered">
                    <thead class="thead-dark"><tr><th>Applicant</th><th>Job Title</th><th>Current Status</th><th>Admin Decision</th></tr></thead>
                    <tbody>
                        <?php while($a = mysqli_fetch_assoc($apps_result)): ?>
                            <?php if($a['app_status'] == 'pending_admin'): ?>
                            <tr>
                                <td><?php echo $a['user_name']; ?></td>
                                <td><?php echo $a['job_title']; ?></td>
                                <td>Under Review</td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="verify_app_id" value="<?php echo $a['app_id']; ?>">
                                        <button class="btn btn-success btn-sm">Verify & Forward</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="reject_app_id" value="<?php echo $a['app_id']; ?>">
                                        <button class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="closure" role="tabpanel">
                <h4>Jobs Requested for Closure</h4>
                <table class="table table-bordered">
                    <thead class="thead-dark"><tr><th>Job Title</th><th>Company</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php 
                        // Reset pointer for closure jobs
                        mysqli_data_seek($jobs_result, 0);
                        while($j = mysqli_fetch_assoc($jobs_result)): ?>
                            <?php if($j['job_status'] == 'closure_requested'): ?>
                            <tr>
                                <td><?php echo $j['job_title']; ?></td>
                                <td><?php echo $j['company_name']; ?></td>
                                <td>Requested to Close</td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="close_job_id" value="<?php echo $j['job_id']; ?>">
                                        <button class="btn btn-danger btn-sm">Archive / Close</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>


