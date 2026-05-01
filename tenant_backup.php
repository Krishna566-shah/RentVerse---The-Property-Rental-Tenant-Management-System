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
    $stmt = $conn->prepare("INSERT INTO maintenance_requests (property_id, tenant_id, issue) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $prop['id'], $tenant_id, $issue);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tenant Dashboard | RentVerse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<style>
    .logo {
            border-radius : 100px;
        }

        .sidebar{
        width:280px;
        height:100vh;
        background:#dc3545;
        position:fixed;
        padding:20px;
        box-shadow:0 4px 20px rgba(0,0,0,0.05);
        }

        .sidebar a{
        display:block;
        padding:12px;
        margin-bottom:8px;
        border-radius:10px;
        font-weight:600;
        color:#fafafa;
        text-decoration:none;
        }

        .sidebar a:hover{
        background:white;
        color:#dc3545;
        }
    </style>
<body>
    <nav class="navbar navbar-dark bg-danger p-3">
        <div class="d-flex align-items-center">
            <img class="logo" src="logo_favicon.jpeg" width="120" height="120" alt="RentVerse Logo" onerror="this.src='https://via.placeholder.com/50?text=RV'">
            <h2 class="ms-2 rentverse-title fw-bold text-white mb-0">RentVerse</h2>
        </div>
        <span class="text-white me-3 fw-bold">Welcome, <?php echo $_SESSION['name']; ?></span>
        <a href="logout.php" class="btn btn-light btn-sm text-danger">Logout</a>
    </nav>

    <div class="sidebar">

        <a href="#dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="#property"><i class="bi bi-house"></i> My Property</a>
        <a href="#rent"><i class="bi bi-wallet2"></i> Rent Tracking</a>
        <a href="#maintenance"><i class="bi bi-tools"></i> Maintenance</a>

        <a href="login.html" class="bg-danger text-white">
        <i class="bi bi-box-arrow-right"></i> Logout
        </a>

        </div>
    

    <div class="container mt-4">
        
        <?php if($prop): ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-danger mb-4">
                    <div class="card-header bg-danger text-white fw-bold">My Property</div>
                    <img src="uploads/properties/<?= $prop['image'] ?>" class="card-img-top" alt="Property">
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-danger"><?= $prop['title'] ?></h5>
                        <p class="mb-1"><strong>Owner:</strong> <?= $prop['owner_name'] ?></p>
                        <p class="mb-1"><strong>Rent:</strong> $<?= $prop['price'] ?>/mo</p>
                        <p class="mb-3"><strong>Location:</strong> <?= $prop['location'] ?></p>
                        <a href="agreement.php?assignment_id=<?= $prop['assignment_id'] ?>" target="_blank" class="btn btn-outline-danger w-100">Download Agreement</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white fw-bold">Pay Rent</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="number" name="amount" class="form-control mb-2" placeholder="Amount" value="<?= $prop['price'] ?>" required>
                            <input type="date" name="date" class="form-control mb-2" required>
                            <label class="form-label small">Upload Screenshot</label>
                            <input type="file" name="screenshot" class="form-control mb-3" required>
                            <button type="submit" name="pay_rent" class="btn btn-danger w-100">Submit Payment</button>
                        </form>
                    </div>
                </div>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white fw-bold">Raise Maintenance</div>
                    <div class="card-body">
                        <form method="POST">
                            <textarea name="issue" class="form-control mb-2" placeholder="Describe issue..." required></textarea>
                            <button type="submit" name="raise_req" class="btn btn-warning w-100">Submit Request</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white fw-bold">Payment History</div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php
                            $pays = $conn->query("SELECT * FROM payments WHERE tenant_id=$tenant_id");
                            while($p = $pays->fetch_assoc()): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>$<?= $p['amount'] ?> (<?= $p['payment_date'] ?>)</span>
                                    <?php if($p['status'] == 'Approved'): ?>
                                        <a href="invoice.php?payment_id=<?= $p['id'] ?>" target="_blank" class="badge bg-success text-decoration-none">View Invoice</a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= $p['status'] ?></span>
                                    <?php endif; ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">You have not been assigned a property yet.</div>
        <?php endif; ?>

    </div>
</body>
</html>