<?php
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Agent') { header("Location: login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard | RentVerse</title>
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
        
        /* SIDEBAR STYLES */
        .sidebar {
            width: 280px;
            height: 100vh;
            background-color: #dc3545;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 30px;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.05);
        }
        
        .main-content {
            position: absolute;
            left: 280px;
            top: 0;
            width: calc(100vw - 280px); /* Exactly calculates remaining width */
            height: 100vh;
            padding: 30px 40px;
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
            display: flex; align-items: center; padding: 12px 18px;
            color: rgba(255,255,255,0.85); text-decoration: none;
            border-radius: 12px; font-weight: 600; margin-bottom: 8px;
            transition: 0.2s; cursor: pointer; font-size: 1rem;
        }
        .nav-link-custom i { margin-right: 12px; font-size: 1.2rem; }
        .nav-link-custom:hover, .nav-link-custom.active-tab {
            background: white; color: #dc3545; transform: translateX(4px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        /* DASHBOARD CARDS */
        .welcome-banner {
            background: linear-gradient(135deg, #dc3545 0%, #9c1522 100%);
            color: white; border-radius: 16px;
        }
        .stat-card {
            border: none; border-radius: 16px; transition: 0.3s; height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px); box-shadow: 0 15px 30px rgba(220, 53, 69, 0.1) !important;
        }
        .stat-icon-wrapper {
            width: 60px; height: 60px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.8rem;
        }
        
        /* Table scroll container */
        .table-scroll-container {
            max-height: calc(100vh - 200px); /* Leaves room for headers */
            overflow-y: auto;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="text-center mb-4 pb-3 border-bottom border-light border-opacity-25 mx-3">
            <img class="logo mb-3" src="logo_favicon.jpeg" width="80" height="80" alt="RentVerse Logo" onerror="this.src='https://via.placeholder.com/80?text=RV'">
            <h4 class="fw-bold text-white mb-1">RentVerse</h4>
            <div class="mt-3">
                <span class="badge bg-dark text-white px-3 py-2 rounded-pill fw-bold shadow-sm">
                    <i class="bi bi-shield-lock-fill me-1"></i> System Admin
                </span>
            </div>
        </div>

        <ul class="nav-menu mt-2">
            <li><a class="nav-link-custom active-tab" onclick="showSection('dashboard', this)"><i class="bi bi-grid-fill"></i> System Overview</a></li>
            <li><a class="nav-link-custom" onclick="showSection('users', this)"><i class="bi bi-people-fill"></i> User Directory</a></li>
            <li class="mt-4"><a href="logout.php" class="nav-link-custom text-white" style="background: rgba(0,0,0,0.3);"><i class="bi bi-box-arrow-left"></i> Secure Logout</a></li>
        </ul>
    </div>

    <div class="main-content container-fluid">
        
        <section id="dashboard" class="content-section">
            
            <div class="card border-0 shadow-sm welcome-banner mb-4">
                <div class="card-body p-4 p-lg-5 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-2">Admin Control Center</h2>
                        <p class="mb-0 opacity-75 fs-5">Monitor all metrics and user activity across the RentVerse dimension.</p>
                    </div>
                    <div class="d-none d-md-block text-end bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-hdd-network me-2"></i>Status: Online</h5>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card stat-card bg-white p-4 shadow-sm border-start border-danger border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted fw-bold text-uppercase tracking-wide mb-1">Total Users</h6>
                                <h1 class="fw-bold text-dark mb-0"><?= $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0] ?></h1>
                            </div>
                            <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-white"><i class="bi bi-people"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stat-card bg-white p-4 shadow-sm border-start border-danger border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted fw-bold text-uppercase tracking-wide mb-1">Properties</h6>
                                <h1 class="fw-bold text-dark mb-0"><?= $conn->query("SELECT COUNT(*) FROM properties")->fetch_row()[0] ?></h1>
                            </div>
                            <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-white"><i class="bi bi-building"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stat-card bg-white p-4 shadow-sm border-start border-danger border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted fw-bold text-uppercase tracking-wide mb-1">Active Leases</h6>
                                <h1 class="fw-bold text-dark mb-0"><?= $conn->query("SELECT COUNT(*) FROM property_assignments")->fetch_row()[0] ?></h1>
                            </div>
                            <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-white"><i class="bi bi-file-earmark-check"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stat-card bg-white p-4 shadow-sm border-start border-success border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted fw-bold text-uppercase tracking-wide mb-1">Total Revenue</h6>
                                <h2 class="fw-bold text-success mb-0">$<?= number_format($conn->query("SELECT SUM(amount) FROM payments WHERE status='Approved'")->fetch_row()[0] ?? 0) ?></h2>
                            </div>
                            <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success"><i class="bi bi-cash-stack"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="users" class="content-section d-none">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-dark m-0">User Directory</h3>
                <span class="badge bg-danger rounded-pill px-3 py-2">Database Access</span>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white fw-bold p-3">
                    <i class="bi bi-table me-2"></i> All Registered Accounts
                </div>
                <div class="table-scroll-container">
                    <table class="table table-hover align-middle mb-0 m-0">
                        <thead class="bg-light sticky-top" style="z-index: 10;">
                            <tr>
                                <th class="p-3 text-center" style="width: 80px;">ID</th>
                                <th class="p-3">Full Name</th>
                                <th class="p-3">Email Address</th>
                                <th class="p-3 text-center">System Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $users = $conn->query("SELECT * FROM users"); 
                            while($u = $users->fetch_assoc()): 
                                
                                // Determine Colors and Icons based on Role
                                $role_color = 'danger';
                                $role_icon = 'bi-person-fill'; // Default Tenant Icon
                                
                                if($u['role'] == 'Agent') { 
                                    $role_color = 'dark'; 
                                    $role_icon = 'bi-shield-lock-fill'; 
                                }
                                if($u['role'] == 'Technician') { 
                                    $role_color = 'warning text-dark'; 
                                    $role_icon = 'bi-tools'; 
                                }
                                if($u['role'] == 'Owner') { 
                                    $role_color = 'primary'; 
                                    $role_icon = 'bi-building-fill'; 
                                }
                            ?>
                            <tr>
                                <td class="p-3 text-center text-muted fw-bold">#<?= str_pad($u['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td class="p-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi <?= $role_icon ?> fs-4 me-3 text-<?= str_replace(' text-dark', '', $role_color) ?>"></i>
                                        <span class="fw-bold text-dark"><?= $u['name'] ?></span>
                                    </div>
                                </td>
                                <td class="p-3 text-muted"><?= $u['email'] ?></td>
                                <td class="p-3 text-center">
                                    <span class="badge bg-<?= $role_color ?> rounded-pill px-3 py-2">
                                        <?= $u['role'] ?>
                                    </span>
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>