<?php
$conn = new mysqli("localhost", "root", "", "rentverse");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// INSERT
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $rating = $_POST['rating'];
    $message = $_POST['message'];

    $conn->query("INSERT INTO reviews (name, rating, message) 
                  VALUES ('$name','$rating','$message')");
}

// FETCH
$result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");
$total = $result->num_rows;
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rentverse | Customer Reviews</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="Review.css">
  </head>
  <body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom border-danger-subtle py-3">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
          <div class="brand-logo">
            <img src="logo_favicon.jpeg" alt="RentVerse Logo" class="me-2" style="height: 120px; width: 120px;">    
        </div>
          <span class="brand-text">RENTVERSE</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto fw-bold text-uppercase small tracking-widest">
            <li class="nav-item"><a class="nav-link px-3" href="property_listings.php">Properties</a></li>
            <li class="nav-item"><a class="nav-link px-3 text-danger active" href="Review.php">Reviews</a></li>
            <li class="nav-item"><a class="nav-link px-3" href="Contact-Us.html">Contact</a></li>
          </ul>
          <a href="login.php" class="btn btn-danger rounded-pill px-4 fw-bold ms-lg-4 shadow-sm">Login</a>
        </div>
      </div>
    </nav>

    <main class="container py-5 mt-lg-4">
      <div class="row g-5">
        <!-- Left Column: Hero & Form -->
        <div class="col-lg-6">
          <div class="hero-section mb-5">
            <h1 class="display-1 fw-black lh-1 mb-4">
              YOUR VOICE <br>
              <span class="text-danger">MATTERS.</span>
            </h1>
            <p class="lead text-secondary mb-5">
              At Rentverse, we strive to provide the best rental experience. 
              Share your journey with us and help others find their perfect home.
            </p>
          </div>

          <div class="card border-danger border-2 rounded-4 shadow-lg p-4 p-md-5 review-form-card">
            <h2 class="h3 fw-bold mb-4 d-flex align-items-center">
              <i class="bi bi-chat-left-text text-danger me-2"></i>
              Leave a Review
            </h2>
            
            <form id="reviewForm" method="POST" action="">
              <div class="mb-4">
                <label class="form-label small fw-bold text-uppercase text-muted tracking-widest">Full Name</label>
                <input type="text" name="name" class="form-control border-0 border-bottom rounded-0 px-0" placeholder="Your Name" required>
              </div>

              <div class="mb-4">
                <label class="form-label small fw-bold text-uppercase text-muted tracking-widest">Rating</label>
                <div class="star-rating h2 text-secondary">
                  <i class="bi bi-star-fill star-btn" data-value="1"></i>
                  <i class="bi bi-star-fill star-btn" data-value="2"></i>
                  <i class="bi bi-star-fill star-btn" data-value="3"></i>
                  <i class="bi bi-star-fill star-btn" data-value="4"></i>
                  <i class="bi bi-star-fill star-btn" data-value="5"></i>
                </div>
                <input type="hidden" id="ratingInput" name="rating" value="0">
              </div>

              <div class="mb-4">
                <label class="form-label small fw-bold text-uppercase text-muted tracking-widest">Your Experience</label>
                <textarea name="message" class="form-control border-2 rounded-3" rows="4" placeholder="Tell us about your stay..." required></textarea>
              </div>

              <button type="submit" name="submit" class="btn btn-danger w-100 py-3">
                Submit Review
                <i class="bi bi-send-fill"></i>
              </button>
            </form>

            <!-- Success Message (Hidden by default) -->
            <div id="successMessage" class="text-center py-5 d-none">
              <i class="bi bi-check-circle-fill text-danger display-1 mb-3"></i>
              <h2 class="fw-black">THANK YOU!</h2>
              <p class="text-muted">Your review has been successfully submitted.</p>
              <button class="btn btn-outline-danger mt-3 rounded-pill" onclick="location.reload()">Write another</button>
            </div>
          </div>
        </div>

        <!-- Right Column: Reviews List -->
        <div class="col-lg-6">
          <div class="d-flex justify-content-between align-items-end mb-4">
            <h2 class="fw-black tracking-tight mb-0">RECENT <span class="text-danger border-bottom border-4 border-danger">REVIEWS</span></h2>
            <span class="small fw-bold text-muted text-uppercase tracking-widest">
            <?php echo $total; ?> Total
            </span>
          </div>

          <div class="reviews-container pe-2">
            <?php while($row = $result->fetch_assoc()) { ?>

            <div class="card border-light shadow-sm rounded-4 p-4 mb-4 review-card">
                
                <div class="d-flex justify-content-between align-items-start mb-3">
                
                <div class="d-flex align-items-center gap-3">
                    
                    <div class="avatar-circle bg-danger-subtle text-danger">
                    <i class="bi bi-person-fill"></i>
                    </div>

                    <div>
                    <h5 class="fw-bold mb-0"><?php echo $row['name']; ?></h5>

                    <div class="text-danger small">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $row['rating']) {
                            echo '<i class="bi bi-star-fill"></i>';
                            } else {
                            echo '<i class="bi bi-star"></i>';
                            }
                        }
                        ?>
                    </div>
                    </div>

                </div>

                <span class="small text-muted fw-bold">
                    <?php echo date("F d, Y", strtotime($row['created_at'])); ?>
                </span>

                </div>

                <p class="text-secondary fst-italic mb-3">
                "<?php echo $row['message']; ?>"
                </p>

            </div>

            <?php } ?>

            </div>
        </div>
      </div>
    </main>

    <footer class="bg-light border-top py-5 mt-5">
      <div class="container">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="d-flex align-items-center mb-3">
             <div class="brand-logo">
            <img src="logo_favicon.jpeg" alt="RentVerse Logo" class="me-2" style="height: 120px; width: 120px;">    
        </div>
              <span class="brand-text-sm">RentVerse</span>
            </div>
            <p class="text-muted small max-w-300">
              The ultimate platform for modern tenants and property managers. 
              Simplifying living, one home at a time.
            </p>
          </div>
          <div class="col-6 col-md-3">
            <h6 class="text-uppercase small fw-bold text-muted tracking-widest mb-4">Quick Links</h6>
            <ul class="list-unstyled small fw-bold text-secondary">
              <li class="mb-2"><a href="about-us.html" class="text-decoration-none text-reset hover-danger">About Us</a></li>
              <li class="mb-2"><a href="Privacy_Policy.html" class="text-decoration-none text-reset hover-danger">Privacy Policy</a></li>
              <li class="mb-2"><a href="Terms&Conditions.html" class="text-decoration-none text-reset hover-danger">Terms of Service</a></li>
            </ul>
          </div>
          <div class="col-6 col-md-3">
            <h6 class="text-uppercase small fw-bold text-muted tracking-widest mb-4">Social</h6>
            <div class="d-flex gap-3">
              <div class="social-icon">In</div>
              <div class="social-icon">Tw</div>
              <div class="social-icon">Ig</div>
            </div>
          </div>
        </div>
        <hr class="my-5 opacity-10">
        <div class="text-center small fw-bold text-muted text-uppercase tracking-widest">
          © 2026 Rentverse Management System. All rights reserved.
        </div>
      </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Logic -->
    <script>
      // Star Rating Logic
      const stars = document.querySelectorAll('.star-btn');
      const ratingInput = document.getElementById('ratingInput');

      stars.forEach(star => {
        star.addEventListener('mouseover', function() {
          const val = this.dataset.value;
          highlightStars(val);
        });

        star.addEventListener('mouseout', function() {
          highlightStars(ratingInput.value);
        });

        star.addEventListener('click', function() {
          ratingInput.value = this.dataset.value;
          highlightStars(this.dataset.value);
        });
      });

      function highlightStars(count) {
        stars.forEach(s => {
          if (parseInt(s.dataset.value) <= parseInt(count)) {
            s.classList.add('text-danger');
            s.classList.remove('text-secondary');
          } else {
            s.classList.remove('text-danger');
            s.classList.add('text-secondary');
          }
        });
      }

      // Form Submission
      document.getElementById('reviewForm').addEventListener('submit', function() {
        document.getElementById('successMessage').classList.remove('d-none');
        });
    </script>
  </body>
</html>