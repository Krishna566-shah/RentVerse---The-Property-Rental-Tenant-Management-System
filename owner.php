<?php
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Owner') { header("Location: login.php"); exit(); }
$owner_id = $_SESSION['user_id'];

// Add Property
if (isset($_POST['add_property'])) {
    $title = $_POST['title']; $desc = $_POST['description'];
    $price = $_POST['price']; $loc = $_POST['location'];
    
    $image = $_FILES['image']['name'];
    $target = "uploads/properties/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $conn->prepare("INSERT INTO properties (title, description, price, location, owner_id, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsss", $title, $desc, $price, $loc, $owner_id, $image);
    $stmt->execute();
}

// Assign Tenant
if (isset($_POST['assign_tenant'])) {
    $prop_id = $_POST['property_id']; $tenant_id = $_POST['tenant_id'];
    $stmt = $conn->prepare("INSERT INTO property_assignments (property_id, owner_id, tenant_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $prop_id, $owner_id, $tenant_id);
    $stmt->execute();
}

// Update Payment Status
if (isset($_POST['update_payment'])) {
    $pay_id = $_POST['payment_id']; $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE payments SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $pay_id);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Owner Dashboard | RentVerse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* CRITICAL STRICT SIZING */
        body {
            background-color: #f8f9fa;
            overflow: hidden; /* completely prevents horizontal and full-page vertical scroll */
            height: 100vh;
            width: 100vw;
            margin: 0;
            padding: 0;
        }
        
        .sidebar {
            width: 280px;
            height: 100vh;
            background-color: #dc3545;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.05);
        }
        
        .main-content {
            position: absolute;
            left: 280px;
            top: 0;
            width: calc(100vw - 280px); /* Exactly calculates remaining width */
            height: 100vh;
            padding: 20px 30px;
            overflow-y: auto; /* Only this section scrolls if needed */
            overflow-x: hidden; /* Kills horizontal scroll */
            box-sizing: border-box;
        }

        /* Hide scrollbar for a cleaner look */
        .main-content::-webkit-scrollbar { width: 6px; }
        .main-content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .logo { border-radius: 100px; border: 3px solid rgba(255,255,255,0.2); padding: 3px; }
        .nav-menu { list-style: none; padding: 0 15px; }
        
        .nav-link-custom {
            display: flex; align-items: center; padding: 10px 16px;
            color: rgba(255,255,255,0.85); text-decoration: none;
            border-radius: 10px; font-weight: 600; margin-bottom: 6px;
            transition: 0.2s; cursor: pointer; font-size: 0.95rem;
        }
        .nav-link-custom i { margin-right: 12px; font-size: 1.1rem; }
        .nav-link-custom:hover, .nav-link-custom.active-tab {
            background: white; color: #dc3545; transform: translateX(4px);
        }
        
        /* DASHBOARD CARDS & COMPACTNESS */
        .welcome-banner {
            background: linear-gradient(135deg, #dc3545 0%, #9c1522 100%);
            color: white; border-radius: 12px;
            height : 160px;
        }
        .stat-card {
            border: none; border-radius: 12px; transition: 0.3s;
        }
        .stat-icon-wrapper {
            width: 50px; height: 50px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
        }
        .quick-action-btn {
            border: 2px dashed #dc3545; color: #dc3545;
            background: rgba(220, 53, 69, 0.05); transition: 0.3s; font-size: 0.9rem;
        }
        .quick-action-btn:hover { background: #dc3545; color: white; }
        
        /* Table scroll container to keep it on one screen */
        .table-scroll-container {
            max-height: calc(100vh - 180px); /* Leaves room for headers */
            overflow-y: auto;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="text-center mb-3 pb-3 border-bottom border-light border-opacity-25 mx-3">
            <img class="logo mb-2" src="logo_favicon.jpeg" width="60" height="60" alt="RentVerse Logo" onerror="this.src='https://via.placeholder.com/60?text=RV'">
            <h5 class="fw-bold text-white mb-1">RentVerse</h5>
            <div class="mt-2">
                <span class="badge bg-white text-danger px-3 py-1 rounded-pill fw-bold shadow-sm" style="font-size: 0.8rem;">
                    <i class="bi bi-person-circle me-1"></i> Hi, <?php echo explode(' ', trim($_SESSION['name']))[0]; ?>
                </span>
            </div>
        </div>

        <ul class="nav-menu mt-2">
            <li><a class="nav-link-custom active-tab" onclick="showSection('dashboard', this)"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
            <li><a class="nav-link-custom" onclick="showSection('properties', this)"><i class="bi bi-houses"></i> Manage Properties</a></li>
            <li><a class="nav-link-custom" onclick="showSection('onboarding', this)"><i class="bi bi-person-plus-fill"></i> Add Tenant</a></li>
            <li><a class="nav-link-custom" onclick="showSection('payments', this)"><i class="bi bi-receipt"></i> Rent Payments</a></li>
            <li><a class="nav-link-custom" onclick="showSection('agreements', this)"><i class="bi bi-file-earmark-check-fill"></i> Agreements</a></li>
            <li class="mt-4"><a href="logout.php" class="nav-link-custom text-white bg-dark"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        
        <section id="dashboard" class="content-section">
            
            <div class="card border-0 shadow-sm welcome-banner mb-3">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1">Welcome to your Dimension! </h4>
                        <p class="mb-0 opacity-75 small">Here is a quick overview of your properties today.</p>
                    </div>
                    <div class="d-none d-md-block text-end bg-white bg-opacity-10 px-3 py-2 rounded-3 backdrop-blur">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-calendar3 me-2"></i><?= date('F j, Y') ?></h6>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <?php
                    $total_props = $conn->query("SELECT COUNT(*) FROM properties WHERE owner_id=$owner_id")->fetch_row()[0];
                    $active_tenants = $conn->query("SELECT COUNT(*) FROM property_assignments WHERE owner_id=$owner_id")->fetch_row()[0];
                    $pending_pays = $conn->query("SELECT COUNT(*) FROM payments p JOIN properties pr ON p.property_id=pr.id WHERE pr.owner_id=$owner_id AND p.status='Pending'")->fetch_row()[0];
                ?>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3 shadow-sm h-100 border-start border-danger border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted fw-bold text-uppercase small mb-1">Total Properties</h6>
                                <h2 class="fw-bold text-dark mb-0"><?= $total_props ?></h2>
                            </div>
                            <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger"><i class="bi bi-building"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3 shadow-sm h-100 border-start border-success border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted fw-bold text-uppercase small mb-1">Active Tenants</h6>
                                <h2 class="fw-bold text-dark mb-0"><?= $active_tenants ?></h2>
                            </div>
                            <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success"><i class="bi bi-people"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-white p-3 shadow-sm h-100 border-start border-warning border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted fw-bold text-uppercase small mb-1">Pending Payments</h6>
                                <h2 class="fw-bold text-danger mb-0"><?= $pending_pays ?></h2>
                            </div>
                            <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning"><i class="bi bi-exclamation-circle"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-3 pb-0">
                            <h6 class="fw-bold text-dark m-0"><i class="bi bi-lightning-charge text-warning me-2"></i>Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <button onclick="document.querySelectorAll('.nav-link-custom')[1].click()" class="btn quick-action-btn w-100 py-2 mb-2 fw-bold rounded-3">
                                <i class="bi bi-plus-circle me-2"></i> List Property
                            </button>
                            <button onclick="document.querySelectorAll('.nav-link-custom')[2].click()" class="btn quick-action-btn w-100 py-2 fw-bold rounded-3">
                                <i class="bi bi-person-plus me-2"></i> Assign Tenant
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-dark m-0"><i class="bi bi-clock-history text-danger me-2"></i>Recent Activity</h6>
                            <button onclick="document.querySelectorAll('.nav-link-custom')[3].click()" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-2 py-0" style="font-size: 0.8rem;">View All</button>
                        </div>
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle mb-0">
                                    <tbody>
                                        <?php
                                        $recent_pays = $conn->query("SELECT p.*, u.name as t_name, pr.title FROM payments p JOIN users u ON p.tenant_id=u.id JOIN properties pr ON p.property_id=pr.id WHERE pr.owner_id=$owner_id ORDER BY p.id DESC LIMIT 3");
                                        if($recent_pays->num_rows > 0):
                                            while($r_pay = $recent_pays->fetch_assoc()): 
                                        ?>
                                        <tr class="border-bottom">
                                            <td class="py-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-circle p-1 me-2"><i class="bi bi-person text-muted fs-6"></i></div>
                                                    <div>
                                                        <p class="mb-0 fw-bold small"><?= $r_pay['t_name'] ?></p>
                                                        <small class="text-muted" style="font-size: 0.75rem;"><?= $r_pay['title'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-2 text-end fw-bold text-dark small">$<?= number_format($r_pay['amount'], 2) ?></td>
                                            <td class="py-2 text-end">
                                                <span class="badge bg-<?php echo ($r_pay['status'] == 'Approved') ? 'success' : 'warning text-dark'; ?> rounded-pill" style="font-size: 0.75rem;">
                                                    <?= $r_pay['status'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endwhile; else: ?>
                                        <tr><td colspan="3" class="text-center text-muted py-3 small">No recent payment activity.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="properties" class="content-section d-none">
            <h4 class="fw-bold text-dark mb-3">Add New Property</h4>
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-danger text-white fw-bold p-3">Property Details</div>
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="fw-bold small text-muted">Property Title</label>
                                <input type="text" name="title" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small text-muted">Monthly Rent ($)</label>
                                <input type="number" name="price" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-12">
                                <label class="fw-bold small text-muted">Location</label>
                                <input type="text" name="location" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-12">
                                <label class="fw-bold small text-muted">Description</label>
                                <textarea name="description" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="fw-bold small text-muted">Display Image</label>
                                <input type="file" name="image" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <button type="submit" name="add_property" class="btn btn-danger w-100 fw-bold py-2 mt-2">Add Property</button>
                    </form>
                </div>
            </div>
        </section>

        <section id="onboarding" class="content-section d-none">
            <h4 class="fw-bold text-dark mb-3">Assign Tenant</h4>
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-danger text-white fw-bold p-3">Create Lease Assignment</div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="fw-bold small text-muted">Select Property</label>
                            <select name="property_id" class="form-select form-select-sm" required>
                                <option value="">Choose one of your properties...</option>
                                <?php
                                $props = $conn->query("SELECT id, title FROM properties WHERE owner_id=$owner_id");
                                while($p = $props->fetch_assoc()) echo "<option value='{$p['id']}'>{$p['title']}</option>";
                                ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="fw-bold small text-muted">Select Tenant</label>
                            <select name="tenant_id" class="form-select form-select-sm" required>
                                <option value="">Choose a registered tenant...</option>
                                <?php
                                $tenants = $conn->query("SELECT id, name, email FROM users WHERE role='Tenant'");
                                while($t = $tenants->fetch_assoc()) echo "<option value='{$t['id']}'>{$t['name']} ({$t['email']})</option>";
                                ?>
                            </select>
                        </div>
                        <button type="submit" name="assign_tenant" class="btn btn-dark w-100 fw-bold py-2">Confirm & Generate Agreement</button>
                    </form>
                </div>
            </div>
        </section>

        <section id="payments" class="content-section d-none">
            <h4 class="fw-bold text-dark mb-3">Rent Payments</h4>
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white fw-bold p-3"><i class="bi bi-cash-stack me-2"></i>Review Payments</div>
                <div class="table-scroll-container">
                    <table class="table table-sm table-hover align-middle mb-0 text-center m-0">
                        <thead class="bg-light sticky-top" style="z-index: 10;">
                            <tr>
                                <th class="p-2 text-start">Tenant</th>
                                <th class="p-2">Amount</th>
                                <th class="p-2">Proof</th>
                                <th class="p-2">Status</th>
                                <th class="p-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $pays = $conn->query("SELECT p.*, u.name as t_name, pr.title FROM payments p JOIN users u ON p.tenant_id=u.id JOIN properties pr ON p.property_id=pr.id WHERE pr.owner_id=$owner_id ORDER BY p.id DESC");
                            while($pay = $pays->fetch_assoc()): ?>
                            <tr>
                                <td class="p-2 text-start">
                                    <div class="fw-bold small"><?= $pay['t_name'] ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= $pay['title'] ?></div>
                                </td>
                                <td class="p-2 fw-bold text-danger small">$<?= number_format($pay['amount'], 2) ?></td>
                                <td class="p-2"><a href="uploads/payments/<?= $pay['screenshot_path'] ?>" target="_blank" class="btn btn-sm btn-light py-0"><i class="bi bi-image"></i></a></td>
                                <td class="p-2">
                                    <span class="badge bg-<?php echo ($pay['status'] == 'Approved') ? 'success' : 'warning text-dark'; ?>" style="font-size: 0.75rem;">
                                        <?= $pay['status'] ?>
                                    </span>
                                </td>
                                <td class="p-2">
                                    <?php if($pay['status'] == 'Pending'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="payment_id" value="<?= $pay['id'] ?>">
                                        <input type="hidden" name="status" value="Approved">
                                        <button type="submit" name="update_payment" class="btn btn-success py-0 px-2 fw-bold" style="font-size: 0.8rem;"><i class="bi bi-check-lg"></i></button>
                                    </form>
                                    <?php elseif($pay['status'] == 'Approved'): ?>
                                        <a href="invoice.php?payment_id=<?= $pay['id'] ?>" target="_blank" class="btn btn-outline-danger py-0 px-2 fw-bold" style="font-size: 0.8rem;"><i class="bi bi-file-earmark-pdf"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="agreements" class="content-section d-none">
            <h4 class="fw-bold text-dark mb-3">Active Agreements</h4>
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white fw-bold p-3"><i class="bi bi-file-earmark-text me-2"></i>Lease Contracts</div>
                <div class="table-scroll-container">
                    <table class="table table-sm table-hover align-middle mb-0 m-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="p-2">Tenant Name</th>
                                <th class="p-2">Property</th>
                                <th class="p-2 text-center">Date</th>
                                <th class="p-2 text-center">Document</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $assigns = $conn->query("SELECT a.*, u.name, p.title FROM property_assignments a JOIN users u ON a.tenant_id=u.id JOIN properties p ON a.property_id=p.id WHERE a.owner_id=$owner_id ORDER BY a.id DESC");
                            while($a = $assigns->fetch_assoc()): ?>
                            <tr>
                                <td class="p-2 fw-bold small"><?= $a['name'] ?></td>
                                <td class="p-2 text-muted small"><?= $a['title'] ?></td>
                                <td class="p-2 text-center small"><?= date('d M Y', strtotime($a['created_at'])) ?></td>
                                <td class="p-2 text-center">
                                    <a href="agreement.php?assignment_id=<?= $a['id'] ?>" target="_blank" class="btn btn-danger py-0 px-2 fw-bold" style="font-size: 0.8rem;"><i class="bi bi-printer"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </div>

    <script>
        function showSection(sectionId, element) {
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(sec => sec.classList.add('d-none'));
            document.getElementById(sectionId).classList.remove('d-none');
            const navLinks = document.querySelectorAll('.nav-link-custom');
            navLinks.forEach(link => link.classList.remove('active-tab'));
            element.classList.add('active-tab');
        }

        document.addEventListener("DOMContentLoaded", function() {
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>