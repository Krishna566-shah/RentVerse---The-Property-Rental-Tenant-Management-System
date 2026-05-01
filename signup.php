<?php
ob_start(); // This catches all output and prevents "Headers already sent" errors
require 'config.php';

$error = null; // Initialize error variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Check if email exists first (Good practice)
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "Email already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        
        if ($stmt->execute()) {
            // SUCCESS: Redirecting now
            header("Location: login.php?msg=registered");
            exit(); 
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RentVerse | Sign Up</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="sign-up.css">
  <style>
    
    .signup-banner {
      background-image: url("signup-banner.png");
      background-size: cover;
      background-position: center;
    }
    .link-red { color: #d10000; text-decoration: none; font-weight: 600; }
  </style>
</head>
<body>

<div class="container-fluid vh-100">
  <div class="row h-100">

    <div class="col-lg-6 col-md-12 d-flex flex-column justify-content-between p-4 p-md-5 bg-white">

      <div>
        <div class="d-flex align-items-center mb-3">
          <img src="logo_favicon.jpeg" width="150" height="150" alt="Logo" onerror="this.src='https://via.placeholder.com/100?text=RV'">
          <h2 class="ms-2 rentverse-title">RentVerse</h2>
        </div>
      </div>

      <div style="max-width: 480px; width: 100%; margin: 0 auto;">
        <h3 class="fw-bold mb-4" style="color: #d10000;"> Join Our Dimension </h3>

        <?php if(isset($error)): ?>
          <div class="alert alert-danger py-2 small shadow-sm"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="signup.php" method="POST" onsubmit="return validateSignUp()">
          
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control custom-input" placeholder="Your Name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" id="email" name="email" class="form-control custom-input" placeholder="name@example.com" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control custom-input" placeholder="Create a strong password" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" id="Cpassword" name="Cpassword" class="form-control custom-input" placeholder="Re-enter your password" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Select Role</label>
            <select class="form-select custom-input" id="role" name="role" required>
              <option value="" selected disabled>Select your role</option>
              <option value="Agent">Agent</option>
              <option value="Owner">Owner</option>
              <option value="Tenant">Tenant</option>
              <option value="Technician">Technician</option>
            </select>
          </div>

          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="terms" required>
            <label class="form-check-label small text-muted">
              I agree to the <a href="Terms&Condition.html" class="link-red">Terms of Service</a> and <a href="Privacy_Policy.html" class="link-red">Privacy Policy</a>
            </label>
          </div>

          <button type="submit" name = 'msg' value='registered'  class="btn btn-danger w-100 py-3 fw-bold">REGISTER</button>

          <p class="text-center mt-3 small">
            Already have an account? <a href="login.php" class="link-red">Log in here</a>
          </p>
        </form>
      </div>

      <div class="mt-4">
        <p class="footer-text">
          We value your privacy. Your information is safe with us.<br>
          ©2026 RentVerse. All rights reserved.
        </p>
      </div>

    </div>

    <div class="col-lg-6 banner signup-banner d-none d-lg-block"></div>

  </div>
</div>

<script src="sign-up.js"></script>

</body>
</html>