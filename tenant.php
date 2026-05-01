<?php
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Tenant') { header("Location: login.php"); exit(); }
$tenant_id = $_SESSION['user_id'];

// Get assigned property
$stmt = $conn->prepare("SELECT a.id as assignment_id, p.*, u.name as owner_name FROM property_assignments a JOIN properties p ON a.property_id=p.id JOIN users u ON p.owner_id=u.id WHERE a.tenant_id=?");
$stmt->bind_param("i", $tenant_id);
$stmt->execute();
$prop = $stmt->get_result()->fetch_assoc();

// Pay Rent
if (isset($_POST['pay_rent']) && $prop) {
    $amount = $_POST['amount']; $date = $_POST['date'];
    $img = $_FILES['screenshot']['name'];
    $target = "uploads/payments/" . basename($img);
    move_uploaded_file($_FILES['screenshot']['tmp_name'], $target);

    $stmt = $conn->prepare("INSERT INTO payments (tenant_id, property_id, amount, payment_date, screenshot_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iidss", $tenant_id, $prop['id'], $amount, $date, $img);
    $stmt->execute();
}

// Raise Maintenance
if (isset($_POST['raise_req']) && $prop) {
    $issue = $_POST['issue'];
    
    // FIX 1: Explicitly insert 'Pending' as the status so the database doesn't reject it
    $stmt = $conn->prepare("INSERT INTO maintenance_requests (property_id, tenant_id, issue, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("iis", $prop['id'], $tenant_id, $issue);
    
    if($stmt->execute()) {
        // FIX 2: Redirect back to the exact same section with a success flag
        header("Location: tenant.php?msg=ticket_raised#maintenance");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tenant Dashboard | RentVerse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { scroll-behavior: smooth; background-color: #f8f9fa; }
        .logo { border-radius: 100px; margin-bottom: 20px; }
        
        .sidebar {
            width: 280px;
            height: 100vh;
            background: #dc3545;
            position: fixed;
            padding: 20px;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            font-weight: 600;
            color: #fafafa;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar a i { margin-right: 12px; font-size: 1.2rem; }

        .sidebar a:hover {
            background: white;
            color: #dc3545;
            transform: translateX(5px);
        }

        /* Active Section Offset */
        .main-content {
            margin-left: 280px;
            padding: 40px;
        }

        .section-padding {
            padding-top: 80px; /* Offset for fixed header if needed */
            margin-bottom: 50px;
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            transition: 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

    <div class="sidebar text-center">
        <img class="logo" src="logo_favicon.jpeg" width="100" height="100" alt="RentVerse Logo" onerror="this.src='https://via.placeholder.com/100?text=RV'">
        <h3 class="text-white fw-bold mb-4">RentVerse</h3>
        <hr class="text-white opacity-50">
        
        <div class="text-start mt-4">
            <a href="#dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="#property"><i class="bi bi-house-heart"></i> My Property</a>
            <a href="#rent"><i class="bi bi-credit-card"></i> Rent Tracking</a>
            <a href="#maintenance"><i class="bi bi-tools"></i> Maintenance</a>
            
            <a href="logout.php" class="mt-5 bg-dark text-white">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold">Welcome, <span class="text-danger"><?php echo $_SESSION['name']; ?></span></h2>
                <p class="text-muted">Manage your stay and payments from one dimension.</p>
            </div>
            <div class="badge bg-danger p-3 rounded-pill shadow-sm">
                Tenant Portal Active
            </div>
        </div>

        <?php if($prop): ?>
            
            <section id="dashboard" class="section-padding">
                <h4 class="fw-bold mb-4"><i class="bi bi-layers text-danger"></i> Quick Overview</h4>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card stat-card shadow-sm bg-white p-4 border-start border-danger border-5">
                            <h6 class="text-muted fw-bold">Active Property</h6>
                            <h4 class="fw-bold"><?= htmlspecialchars($prop['title']) ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card shadow-sm bg-white p-4 border-start border-danger border-5">
                            <h6 class="text-muted fw-bold">Monthly Rent</h6>
                            <h4 class="fw-bold text-danger">$<?= number_format($prop['price'], 2) ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card shadow-sm bg-white p-4 border-start border-danger border-5">
                            <h6 class="text-muted fw-bold">Payment Status</h6>
                            <?php
                                $last_pay = $conn->query("SELECT status FROM payments WHERE tenant_id=$tenant_id ORDER BY id DESC LIMIT 1")->fetch_assoc();
                                $status = $last_pay['status'] ?? 'No Records';
                                $color = ($status == 'Approved') ? 'success' : (($status == 'Pending') ? 'warning' : 'secondary');
                            ?>
                            <h4 class="fw-bold text-<?= $color ?>"><?= $status ?></h4>
                        </div>
                    </div>
                </div>
            </section>

            <section id="property" class="section-padding">
                <h4 class="fw-bold mb-4"><i class="bi bi-house-heart text-danger"></i> Property Details</h4>
                <div class="card shadow border-0 overflow-hidden" style="border-radius: 20px;">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <img src="uploads/properties/<?= $prop['image'] ?>" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="Property">
                        </div>
                        <div class="col-md-6 p-5 bg-white">
                            <h2 class="fw-bold text-danger mb-3"><?= $prop['title'] ?></h2>
                            <p class="fs-5 text-muted mb-4"><i class="bi bi-geo-alt"></i> <?= $prop['location'] ?></p>
                            <hr>
                            <div class="row mb-4 mt-4">
                                <div class="col-6">
                                    <p class="text-muted mb-0 small uppercase fw-bold">LANDLORD</p>
                                    <p class="fw-bold"><?= $prop['owner_name'] ?></p>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted mb-0 small uppercase fw-bold">MONTHLY RENT</p>
                                    <p class="fw-bold text-danger">$<?= $prop['price'] ?></p>
                                </div>
                            </div>
                            <a href="agreement.php?assignment_id=<?= $prop['assignment_id'] ?>" target="_blank" class="btn btn-danger btn-lg w-100 py-3 fw-bold shadow">
                                <i class="bi bi-file-pdf"></i> Download Rental Agreement
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section id="rent" class="section-padding">
                <div class="row">
                    <div class="col-md-5">
                        <h4 class="fw-bold mb-4"><i class="bi bi-credit-card text-danger"></i> Pay Your Rent</h4>
                        <div class="card shadow-sm border-0 p-4" style="border-radius: 15px;">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Amount to Pay ($)</label>
                                    <input type="number" name="amount" class="form-control form-control-lg bg-light" value="<?= $prop['price'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Payment Date</label>
                                    <input type="date" name="date" class="form-control form-control-lg bg-light" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Screenshot of Proof</label>
                                    <input type="file" name="screenshot" class="form-control" required>
                                </div>
                                <button type="submit" name="pay_rent" class="btn btn-danger btn-lg w-100 fw-bold">Submit Payment Request</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h4 class="fw-bold mb-4"><i class="bi bi-clock-history text-danger"></i> Recent History</h4>
                        <div class="card shadow-sm border-0" style="border-radius: 15px;">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="p-3">Date</th>
                                            <th class="p-3">Amount</th>
                                            <th class="p-3 text-center">Status</th>
                                            <th class="p-3">Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $pays = $conn->query("SELECT * FROM payments WHERE tenant_id=$tenant_id ORDER BY id DESC");
                                        while($p = $pays->fetch_assoc()): ?>
                                        <tr>
                                            <td class="p-3"><?= $p['payment_date'] ?></td>
                                            <td class="p-3 fw-bold">$<?= $p['amount'] ?></td>
                                            <td class="p-3 text-center">
                                                <span class="badge rounded-pill bg-<?= ($p['status'] == 'Approved') ? 'success' : (($p['status'] == 'Pending') ? 'warning' : 'danger') ?> px-3">
                                                    <?= $p['status'] ?>
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <?php if($p['status'] == 'Approved'): ?>
                                                    <a href="invoice.php?payment_id=<?= $p['id'] ?>" target="_blank" class="text-danger fw-bold"><i class="bi bi-download"></i> PDF</a>
                                                <?php else: ?>
                                                    <span class="text-muted small">Locked</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="maintenance" class="section-padding">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0 p-5 text-center" style="border-radius: 20px; background: white;">
                            <i class="bi bi-tools text-danger display-4 mb-3"></i>
                            <h3 class="fw-bold">Report an Issue</h3>
                            <p class="text-muted mb-4">Broken pipe? Electrical issues? Let our technician handle it.</p>
                            
                            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'ticket_raised'): ?>
                                <div class="alert alert-success fw-bold">
                                    <i class="bi bi-check-circle-fill me-2"></i> Your maintenance ticket has been sent to the technician!
                                </div>
                            <?php endif; ?>

                            <form method="POST" class="text-start">
                                <div class="mb-3">
                                    <textarea name="issue" class="form-control" rows="4" placeholder="Briefly describe what needs fixing..." required></textarea>
                                </div>
                                <button type="submit" name="raise_req" class="btn btn-warning btn-lg w-100 fw-bold">Raise Maintenance Ticket</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        <?php else: ?>
            <div class="alert alert-danger p-5 shadow text-center rounded-4">
                <i class="bi bi-exclamation-triangle display-1 mb-3"></i>
                <h2 class="fw-bold">Access Restricted</h2>
                <p class="fs-5">You are currently registered as a Tenant, but you have not been assigned to a property by an owner yet.</p>
                <hr>
                <p class="mb-0 small">Please contact your landlord to link your account to your rental property.</p>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>