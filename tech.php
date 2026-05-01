<?php
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Technician') { header("Location: login.php"); exit(); }

if (isset($_POST['mark_done'])) {
    $req_id = $_POST['req_id'];
    $stmt = $conn->prepare("UPDATE maintenance_requests SET status='Completed' WHERE id=?");
    $stmt->bind_param("i", $req_id);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentVerse | Technician Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --rv-red:  #dc3545;
            --rv-gray: #f8f9fa;
            --rv-success: #198754;
            --rv-info: #0dcaf0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--rv-gray);
            margin: 0;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            height: 100vh;
            background:  #dc3545;
            border-right: 2px solid #eee;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .brand-logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            padding: 2rem 1.5rem;
            display: flex;
            align-items: center;
        }

        .nav-menu {
            list-style: none;
            padding: 0 1rem;
        }

        .nav-link-custom {
            display: block;
            padding: 1rem;
            margin-bottom: 10px;
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
            cursor: pointer;
        }

        .nav-link-custom i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .nav-link-custom:hover, .active-tab {
            background: white;
            color: var(--rv-red) !important;
            transform: translateX(4px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* Layout */
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
        }

        .header-top {
            background:  #dc3545;
            padding: 1rem 2rem;
            border-radius: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        /* Cards */
        .card-custom {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 6px 15px rgba(0,0,0,0.04);
            height: 100%;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #111;
        }

        .section-header {
            border-left: 6px solid var(--rv-red);
            padding-left: 1rem;
            margin-bottom: 2rem;
        }

        /* Tasks */
        .task-card {
            border: 1px solid #eee;
            border-left: 5px solid var(--rv-red);
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            background: white;
            transition: 0.3s;
        }

        .task-card:hover {
            box-shadow: 0 8px 15px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }

        .status-pill {
            font-size: .75rem;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .btn-rv {
            background: var(--rv-red);
            color: white;
            font-weight: 700;
            border-radius: 10px;
            border: none;
            padding: 10px 20px;
            transition: 0.3s;
        }
        .btn-rv:hover {
            background:  #dc3545;
            color: white;
        }

        .about-card-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand-logo border-bottom border-light border-opacity-25 mx-3 px-0 mb-3">
            <img src="logo_favicon.jpeg" style="height:75px; border-radius:40px;" class="me-3" onerror="this.src='https://via.placeholder.com/45?text=RV'">
            RentVerse
        </div>
        <ul class="nav-menu">
            <li>
                <a class="nav-link-custom active-tab" onclick="showSection('dashboard', this)">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li>
                <a class="nav-link-custom" onclick="showSection('tasks', this)">
                    <i class="bi bi-list-check"></i> My Tasks
                </a>
            </li>
            <li>
                <a class="nav-link-custom" onclick="showSection('completed', this)">
                    <i class="bi bi-check2-circle"></i> Completed Logs
                </a>
            </li>
            <li class="mt-5">
                <a href="logout.php" class="nav-link-custom text-white bg-dark">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">

        <div class="header-top">
            <h4 class="fw-bold text-white mb-0">Technician Portal</h4>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end">
                    <p class="mb-0 fw-bold text-white"><?php echo $_SESSION['name']; ?></p>
                    <p class="mb-0 small text-white opacity-75">Certified Tech</p>
                </div>
                <div class="rounded-circle bg-white text-danger d-flex align-items-center justify-content-center shadow-sm"
                     style="width:48px;height:48px;font-weight:bold;font-size:1.2rem;">
                    <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                </div>
            </div>
        </div>

        <?php
            // Calculate dynamic stats for the dashboard
            $active_count = $conn->query("SELECT COUNT(*) FROM maintenance_requests WHERE status='Pending'")->fetch_row()[0];
            $completed_count = $conn->query("SELECT COUNT(*) FROM maintenance_requests WHERE status='Completed'")->fetch_row()[0];
            $total_tasks = $active_count + $completed_count;
            $performance = ($total_tasks > 0) ? round(($completed_count / $total_tasks) * 100) : 100;
        ?>

        <div id="dashboard" class="content-section">
            <div class="section-header">
                <h2 class="fw-bold text-dark">Work Overview</h2>
            </div>
            
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card-custom" style="border-left:6px solid var(--rv-red);">
                        <p class="text-muted small fw-bold tracking-widest mb-1">ACTIVE TASKS</p>
                        <div class="stat-value"><?= $active_count ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-custom" style="border-left:6px solid var(--rv-success);">
                        <p class="text-muted small fw-bold tracking-widest mb-1">JOBS COMPLETED</p>
                        <div class="stat-value"><?= $completed_count ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-custom" style="border-left:6px solid var(--rv-info);">
                        <p class="text-muted small fw-bold tracking-widest mb-1">CLEARANCE RATE</p>
                        <div class="stat-value"><?= $performance ?>%</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-custom">
                        <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1000&q=80" class="about-card-img">
                        <h4 class="fw-bold text-danger">Professional Excellence</h4>
                        <p class="text-muted mb-0">
                            RentVerse technicians provide high-quality maintenance services for tenants and property owners ensuring smooth property management across all dimensions.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-custom text-center d-flex flex-column justify-content-center">
                        <i class="bi bi-shield-check text-danger mb-2" style="font-size:3.5rem"></i>
                        <h4 class="fw-bold">Verified Status</h4>
                        <p class="small text-muted mb-4">You are a Tier-1 certified technician partner for the RentVerse Network.</p>
                        <button class="btn btn-rv" data-bs-toggle="modal" data-bs-target="#techProfile">View Network Profile</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="tasks" class="content-section d-none">
            <div class="section-header">
                <h2 class="fw-bold text-dark">Assigned Tasks</h2>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <?php
                    $reqs = $conn->query("SELECT m.*, p.title, p.location FROM maintenance_requests m JOIN properties p ON m.property_id=p.id WHERE m.status='Pending'");
                    if($reqs->num_rows > 0):
                        while($r = $reqs->fetch_assoc()): 
                    ?>
                    <div class="task-card shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($r['issue']) ?></h5>
                            <span class="badge bg-danger-subtle text-danger status-pill border border-danger border-opacity-25">Action Required</span>
                        </div>
                        
                        <div class="row mb-3 mt-3">
                            <div class="col-md-6">
                                <p class="small text-muted mb-1"><i class="bi bi-building text-danger me-1"></i> <strong>Property:</strong> <?= $r['title'] ?></p>
                                <p class="small text-muted mb-0"><i class="bi bi-geo-alt text-danger me-1"></i> <strong>Location:</strong> <?= $r['location'] ?></p>
                            </div>
                            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                <p class="small text-muted mb-0"><strong>Reported On:</strong> <?= date('d M Y', strtotime($r['created_at'])) ?></p>
                            </div>
                        </div>

                        <hr class="opacity-10 my-3">
                        
                        <div class="text-end">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="req_id" value="<?= $r['id'] ?>">
                                <button type="submit" name="mark_done" class="btn btn-success fw-bold shadow-sm px-4">
                                    <i class="bi bi-check2-square me-1"></i> Mark Job Done
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                    <div class="text-center py-5">
                        <i class="bi bi-emoji-smile text-muted opacity-25" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">All caught up!</h4>
                        <p class="text-muted">There are no pending maintenance tasks assigned to you right now.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="completed" class="content-section d-none">
            <div class="section-header">
                <h2 class="fw-bold text-dark">Completion Log</h2>
            </div>
            
            <div class="card-custom table-responsive p-0 overflow-hidden">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="p-3">Resolved Issue</th>
                            <th class="p-3">Property Location</th>
                            <th class="p-3">Date Completed</th>
                            <th class="p-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $comp_reqs = $conn->query("SELECT m.*, p.title, p.location FROM maintenance_requests m JOIN properties p ON m.property_id=p.id WHERE m.status='Completed' ORDER BY m.id DESC");
                        if($comp_reqs->num_rows > 0):
                            while($c = $comp_reqs->fetch_assoc()): 
                        ?>
                        <tr>
                            <td class="p-3 fw-bold"><?= htmlspecialchars($c['issue']) ?></td>
                            <td class="p-3 text-muted small"><i class="bi bi-geo-alt text-danger me-1"></i> <?= $c['location'] ?></td>
                            <td class="p-3 small fw-bold text-secondary"><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                            <td class="p-3 text-center">
                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-shield-check me-1"></i> Verified</span>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No completed jobs found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="modal fade" id="techProfile">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-person-badge me-2"></i> Technician Network Profile
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center border-end">
                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center mx-auto mb-3 shadow"
                                 style="width:100px;height:100px;font-size:35px;font-weight:bold;">
                                <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                            </div>
                            <h4 class="fw-bold mb-1"><?php echo $_SESSION['name']; ?></h4>
                            <p class="text-muted small mb-2">Tier-1 Certified</p>
                            <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm"><i class="bi bi-patch-check-fill me-1"></i> Verified Partner</span>
                        </div>
                        <div class="col-md-8 ps-md-4 mt-4 mt-md-0">
                            <div class="row g-3">
                                <div class="col-6">
                                    <p class="small text-muted mb-0 fw-bold tracking-widest">TECHNICIAN ID</p>
                                    <p class="fw-bold text-dark">TECH-<?= str_pad($_SESSION['user_id'], 4, '0', STR_PAD_LEFT) ?></p>
                                </div>
                                <div class="col-6">
                                    <p class="small text-muted mb-0 fw-bold tracking-widest">RATING</p>
                                    <p class="fw-bold text-warning mb-0">
                                        <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-half"></i> <span class="text-dark ms-1">4.8/5</span>
                                    </p>
                                </div>
                                <div class="col-12">
                                    <p class="small text-muted mb-0 fw-bold tracking-widest">SPECIALIZATION</p>
                                    <p class="fw-bold text-dark">General Maintenance, Electrical, Plumbing</p>
                                </div>
                                <div class="col-12">
                                    <p class="small text-muted mb-0 fw-bold tracking-widest">EMAIL CONTACT</p>
                                    <p class="fw-bold text-danger">Contact via RentVerse Internal System</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionId, element) {
            // Hide all sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(sec => sec.classList.add('d-none'));

            // Show the target section
            document.getElementById(sectionId).classList.remove('d-none');

            // Manage active class on sidebar
            const navLinks = document.querySelectorAll('.nav-link-custom');
            navLinks.forEach(link => link.classList.remove('active-tab'));
            element.classList.add('active-tab');
        }

        // Keep form submissions from resetting the view to Dashboard
        document.addEventListener("DOMContentLoaded", function() {
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>