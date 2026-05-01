<?php
require 'config.php'; // Database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentVerse | Premium Property Grid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --rv-red: #dc3545;
            --rv-red-dark: #b91c1c;
            --rv-white: #ffffff;
            --rv-gray: #f9fafb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--rv-white);
            color: #111827;
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            background-color: var(--rv-red);
            border-bottom: 1px solid #e5e7eb;
            padding: 1.25rem 0;
        }
        .navbar-brand {
            font-weight: 800;
            color: var(--rv-white) !important;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .brand-icon {
            background: var(--rv-red);
            color: white;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1.2rem;
            overflow: hidden;
        }
        .brand-icon img {
            max-width: 100%;
            height: auto;
        }

        .nav-link {
            color: #ffffff;
            font-weight: 600;
            color: #ffffff !important;
            margin: 0 12px;
            font-size: 0.95rem;
        }
        .nav-link:hover { color: #fa8080 !important; }

        /* Hero */
        .hero {
            padding: 100px 0 80px;
            background: var(--rv-gray);
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }
        .hero h1 { font-weight: 800; font-size: 3.5rem; margin-bottom: 1rem; }
        .hero h1 span { color: var(--rv-red); }
        .hero p { color: #6b7280; font-size: 1.25rem; max-width: 600px; margin: 0 auto; }

        /* Grid & Cards */
        .property-card {
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            height: 100%;
        }
        .property-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border-color: var(--rv-red);
        }
        .card-img-wrapper { position: relative; height: 240px; overflow: hidden; }
        .card-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .property-card:hover .card-img-wrapper img { transform: scale(1.05); }
        .property-badge {
            position: absolute; top: 15px; left: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 6px 14px; border-radius: 50px;
            font-size: 0.75rem; font-weight: 700;
        }
        .price-text { color: var(--rv-red); font-weight: 800; font-size: 1.5rem; }
        .property-info { padding: 24px; }
        .property-specs {
            display: flex; justify-content: space-between;
            padding-top: 20px; margin-top: 20px;
            border-top: 1px solid #f3f4f6; color: #6b7280;
            font-size: 0.875rem; font-weight: 600;
        }
        .property-specs i { color: var(--rv-red); margin-right: 6px; }

        .btn-rv {
            background-color: var(--rv-red); color: white; border: none;
            padding: 14px 28px; border-radius: 12px; font-weight: 700; width: 100%;
        }
        .btn-rv:hover { background-color: var(--rv-red-dark); color: white; }

        footer { background-color: #111827; color: #9ca3af; padding: 80px 0 40px; }
        footer h6 { color: white; text-transform: uppercase; letter-spacing: 1px; }
        footer a { color: #9ca3af; text-decoration: none; transition: color 0.2s; }
        footer a:hover { color: var(--rv-red); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <div class="brand-icon"><img src="logo_favicon.jpeg" alt="logo"></div>
                RentVerse
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="property_listings.php">Listings</a></li>
                    <li class="nav-item"><a class="nav-link" href="about-us.html">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <h1>Explore <span>Premium</span> Living</h1>
            <p>Direct from the database. A curated collection of the finest properties.</p>
        </div>
    </header>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4">
                
                <?php
                // FETCH DYNAMIC DATA
                $result = $conn->query("SELECT * FROM properties ORDER BY id DESC");
                if ($result->num_rows > 0):
                    while($row = $result->fetch_assoc()):
                        $img = !empty($row['image']) ? "uploads/properties/".$row['image'] : "https://via.placeholder.com/400x250?text=RentVerse+Property";
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="property-card">
                        <div class="card-img-wrapper">
                            <img src="<?= $img ?>" alt="Property Image">
                            <div class="property-badge">FOR RENT</div>
                        </div>
                        <div class="property-info">
                            <div class="price-text mb-2">₹ <?= number_format($row['price']) ?> <span class="fs-6 fw-normal text-muted">/mo</span></div>
                            <h4 class="fw-bold mb-1"><?= htmlspecialchars($row['title']) ?></h4>
                            <p class="text-muted small"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?= htmlspecialchars($row['location']) ?></p>
                            
                            <button class="btn btn-rv mt-3 view-btn" 
                                    data-title="<?= htmlspecialchars($row['title']) ?>"
                                    data-price="<?= number_format($row['price']) ?>"
                                    data-loc="<?= htmlspecialchars($row['location']) ?>"
                                    data-desc="<?= htmlspecialchars($row['description']) ?>"
                                    data-img="<?= $img ?>"
                                    data-bs-toggle="modal" data-bs-target="#propertyModal">
                                View Details
                            </button>

                            <div class="property-specs">
                                <span><i class="fas fa-bed"></i> 3 Beds</span>
                                <span><i class="fas fa-bath"></i> 2 Baths</span>
                                <span><i class="fas fa-expand"></i> 1200 ft²</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; else: ?>
                    <div class="col-12 text-center py-5"><h3>No properties found in the dimension.</h3></div>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <a class="navbar-brand text-white" href="#">
                        <div class="brand-icon"><i class="fas fa-home"></i></div>
                        RentVerse
                    </a>
                    <p class="mt-3">Redefining modern living with bold design and premium services.</p>
                </div>
                <div class="col-lg-8 text-end">
                    <p class="small">&copy; 2026 RentVerse Inc. Red & White Modern Series.</p>
                </div>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="propertyModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content overflow-hidden shadow-lg border-0">
                <div class="modal-body p-0">
                    <img src="" id="modalImg" class="w-100" style="height: 400px; object-fit: cover;">
                    <div class="p-4">
                        <h2 id="modalTitle" class="fw-bold"></h2>
                        <h4 id="modalPrice" class="text-danger fw-bold"></h4>
                        <p id="modalLoc" class="text-muted"></p>
                        <hr>
                        <p id="modalDesc"></p>
                        <div class="mt-4">
                            <a href="login.php" class="btn btn-rv">Login to Inquire</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal Logic to update content dynamically
        document.querySelectorAll('.view-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modalTitle').innerText = this.getAttribute('data-title');
                document.getElementById('modalPrice').innerText = '₹ ' + this.getAttribute('data-price') + ' / month';
                document.getElementById('modalLoc').innerText = this.getAttribute('data-loc');
                document.getElementById('modalDesc').innerText = this.getAttribute('data-desc');
                document.getElementById('modalImg').src = this.getAttribute('data-img');
            });
        });
    </script>
</body>
</html>