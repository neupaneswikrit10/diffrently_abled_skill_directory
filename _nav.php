<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true;
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">DisabilityApp</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <?php if(!$loggedin): ?>
      <li class="nav-item">
        <a class="nav-link" href="login.php">Login</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="register.php">Register</a>
      </li>
      <?php endif; ?>
      
      <?php if($loggedin): ?>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">Logout</a>
      </li>
      <?php endif; ?>
    </ul>
    
    <?php if($loggedin): ?>
    <span class="navbar-text text-white">
      Welcome, <?php echo $_SESSION['email']; ?> (<?php echo ucfirst($_SESSION['role']); ?>)
    </span>
    <?php endif; ?>
  </div>
</nav>