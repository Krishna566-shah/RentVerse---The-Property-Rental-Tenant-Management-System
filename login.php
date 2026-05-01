<?php
// 1. MUST BE THE VERY FIRST LINE
ob_start(); 
require 'config.php'; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify the hashed password against the database
        if (password_verify($password, $row['password'])) {
            // Set Session Variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name']    = $row['name'];
            $_SESSION['role']    = $row['role'];

            // Role-based Redirection
            if ($row['role'] == 'Owner') {
                header("Location: owner.php");
            } elseif ($row['role'] == 'Tenant') {
                header("Location: tenant.php");
            } elseif ($row['role'] == 'Technician') {
                header("Location: tech.php");
            } else {
                header("Location: admin.php"); // For Agent/Admin
            }
            exit(); 
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RentVerse | Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="login.css">
  <link rel="stylesheet" href="assets/style.css">
  <style>
    .login-banner {
        background: linear-gradient(rgba(220, 53, 69, 0.1), rgba(0, 0, 0, 0.5)), 
                    url('login-banner.png');
        background-size: cover;
        background-position: center;
    }
    .link-red { color: #dc3545; text-decoration: none; font-weight: 600; }
    .link-red:hover { text-decoration: underline; }
  </style>
</head>
<body>

<div class="container-fluid vh-100">
  <div class="row h-100">

    <div class="col-lg-6 col-md-12 d-flex flex-column justify-content-between p-4 p-md-5 bg-white">

      <div>
        <div class="d-flex align-items-center mb-3">
          <img src="logo_favicon.jpeg" width="150" height="150" alt="RentVerse Logo" onerror="this.src='https://via.placeholder.com/100?text=RV'">
          <h2 class="ms-2 rentverse-title fw-bold text-danger">RentVerse</h2>
        </div>
      </div>

      <div style="max-width: 450px; width: 100%; margin: 0 auto;">
        <h3 class="mb-4 fw-bold">Welcome Back</h3>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
            <div class="alert alert-success shadow-sm border-0 mb-4">Account created! You can now login.</div>
        <?php endif; ?>

        <?php if(!empty($error)): ?>
          <div class="alert alert-danger shadow-sm border-0 mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label fw-bold">Email Address</label>
            <input name="email" type="email" class="form-control custom-input py-2" placeholder="name@example.com" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Password</label>
            <input name="password" type="password" class="form-control custom-input py-2" placeholder="Enter your password" required>
          </div>

          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="termsCheck" required>
            <label class="form-check-label small text-muted" for="termsCheck">
              I agree to the <a href="Terms&Condition.html" class="link-red">Terms of Service</a> and <a href="Privacy_Policy.html" class="link-red">Privacy Policy</a>
            </label>
          </div>

          <button type="submit" class="btn btn-danger w-100 py-3 mb-3 fw-bold shadow-sm">LOGIN</button>

          <p class="text-center mt-3 small">
            <a href="#" class="link-red">Forgot Password?</a>
          </p>

          <p class="text-center mt-2 small">
            Don't have an account? <a href="signup.php" class="link-red">Sign up now</a>
          </p>
        </form>
      </div>

      <p class="footer-text text-center mt-4 text-muted small">
        We value your privacy. Your information is safe with us.<br>
        ©2026 RentVerse. All rights reserved.
      </p>

    </div>

    <div class="col-lg-6 banner login-banner d-none d-lg-block"></div>

  </div>
</div>

</body>
</html>