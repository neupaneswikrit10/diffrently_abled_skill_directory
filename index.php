<?php
session_start();
require 'partials/_dbconnect.php';

// Redirect if not logged in
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']!=true){
    header("location: login.php");
    exit;
}

// Redirect Admin to their own panel
if($_SESSION['role'] == 'admin'){
    header("location: admin_dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// --- DATA FETCHING (SQL) ---
// Get All Jobs
$jobs_sql = "SELECT jobs.*, users.full_name as company_name FROM jobs JOIN users ON jobs.company_id = users.user_id";
$jobs_result = mysqli_query($conn, $jobs_sql);

// Get All Applications (Joined for details)
$apps_sql = "SELECT applications.*, jobs.job_title, users.full_name as user_name 
             FROM applications 
             JOIN jobs ON applications.job_id = jobs.job_id 
             JOIN users ON applications.user_id = users.user_id";
$apps_result = mysqli_query($conn, $apps_sql);

// Store apps in array for easy checking
$my_apps = []; 
$all_apps_data = []; // For Company view
while($row = mysqli_fetch_assoc($apps_result)){
    $all_apps_data[] = $row;
    if($row['user_id'] == $user_id){
        $my_apps[] = $row['job_id']; // ID list of jobs I applied to
    }
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <title>Dashboard</title>
    </head>
<body>
    <?php require 'partials/_nav.php' ?>

    <div class="container my-4">
        
        <?php if($role == 'beneficiary'): ?>
            <div class="jumbotron py-4">
                <h1 class="display-4">Find Jobs</h1>
                <p class="lead">Showing jobs suitable for: <strong><?php echo ucfirst($_SESSION['disability_type']); ?></strong></p>
            </div>

            <h3 class="mb-3">Open Vacancies</h3>
            <div class="row">
                <?php 
                $job_found = false;
                while($job = mysqli_fetch_assoc($jobs_result)): 
                    // FILTER: Match Disability AND Job must be Open
                    if($job['target_disability_type'] == $_SESSION['disability_type'] && $job['job_status'] == 'open'):
                        $job_found = true;
                        
                        // Check if already applied
                        $already_applied = in_array($job['job_id'], $my_apps);
                ?>
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $job['job_title']; ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo $job['company_name']; ?></h6>
                                <p class="card-text"><?php echo $job['job_description']; ?></p>
                                <p class="small text-muted">Skills: <?php echo $job['required_skills']; ?></p>
                                
                                <?php if($already_applied): ?>
                                    <button class="btn btn-secondary disabled">Applied</button>
                                <?php else: ?>
                                    <form action="apply_job.php" method="POST">
                                        <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                                        <input type="hidden" name="job_title" value="<?php echo $job['job_title']; ?>">
                                        <button type="submit" class="btn btn-primary">Apply Now</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php 
                    endif; 
                endwhile;
                
                if(!$job_found){
                    echo "<div class='col-12'><div class='alert alert-warning'>No jobs found matching your profile at the moment.</div></div>";
                }
                ?>
            </div>

            <hr class="my-5">

            <h3 class="mb-3">My Applications</h3>
            <table class="table table-bordered">
                <thead class="thead-light"><tr><th>Job Title</th><th>Status</th><th>Notes</th></tr></thead>
                <tbody>
                    <?php foreach($all_apps_data as $app): ?>
                        <?php if($app['user_id'] == $user_id): ?>
                        <tr>
                            <td><?php echo $app['job_title']; ?></td>
                            <td>
                                <?php 
                                    if($app['app_status'] == 'pending_admin') echo '<span class="badge badge-warning">Under Review by Admin</span>';
                                    elseif($app['app_status'] == 'rejected_by_admin') echo '<span class="badge badge-danger">Not Eligible (Admin Rejected)</span>';
                                    elseif($app['app_status'] == 'eligible') echo '<span class="badge badge-info">Verified - Sent to Company</span>';
                                    elseif($app['app_status'] == 'hired') echo '<span class="badge badge-success">HIRED / SELECTED</span>';
                                    elseif($app['app_status'] == 'rejected_by_company') echo '<span class="badge badge-secondary">Company Rejected</span>';
                                ?>
                            </td>
                            <td>
                                <?php if($app['app_status'] == 'pending_admin') echo "Admin is checking your skills."; ?>
                                <?php if($app['app_status'] == 'eligible') echo "Waiting for company decision."; ?>
                                <?php if($app['app_status'] == 'hired') echo "Congratulations!"; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>


        <?php if($role == 'company'): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Company Dashboard</h2>
                <a href="post_job.php" class="btn btn-success">+ Post New Job</a>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-dark text-white">My Job Posts</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead><tr><th>Title</th><th>Target Disability</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php 
                            $my_jobs = [];
                            mysqli_data_seek($jobs_result, 0); // Reset pointer
                            while($job = mysqli_fetch_assoc($jobs_result)): 
                                if($job['company_id'] == $user_id):
                                    $my_jobs[] = $job['job_id']; // Store ID to filter applicants later
                            ?>
                            <tr>
                                <td><?php echo $job['job_title']; ?></td>
                                <td><?php echo $job['target_disability_type']; ?></td>
                                <td>
                                    <?php 
                                    if($job['job_status']=='pending') echo '<span class="badge badge-warning">Pending Admin Approval</span>';
                                    elseif($job['job_status']=='open') echo '<span class="badge badge-success">Live / Open</span>';
                                    elseif($job['job_status']=='closed') echo '<span class="badge badge-secondary">Closed</span>';
                                    ?>
                                </td>
                            </tr>
                            <?php endif; endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">Verified Candidates (Ready for Review)</div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Candidate Name</th><th>Applied For</th><th>Skills Check</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php 
                            $has_candidates = false;
                            foreach($all_apps_data as $app): 
                                // Only show if: 
                                // 1. Job belongs to this company 
                                // 2. Admin has marked them 'eligible'
                                if(in_array($app['job_id'], $my_jobs) && $app['app_status'] == 'eligible'):
                                    $has_candidates = true;
                            ?>
                            <tr>
                                <td><?php echo $app['user_name']; ?></td>
                                <td><?php echo $app['job_title']; ?></td>
                                <td><span class="text-success">✔ Verified by Admin</span></td>
                                <td>
                                    <form action="manage_application.php" method="POST" class="d-inline">
                                        <input type="hidden" name="app_id" value="<?php echo $app['app_id']; ?>">
                                        <button type="submit" name="action" value="hire" class="btn btn-sm btn-success">Hire</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endif; endforeach; ?>
                            
                            <?php if(!$has_candidates) echo "<tr><td colspan='4' class='text-center text-muted'>No verified candidates waiting at the moment.</td></tr>"; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
    
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>