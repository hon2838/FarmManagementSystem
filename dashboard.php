<?php
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page - Limau Kasturi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        }
        .navbar {
            background: rgba(33, 37, 41, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #fff !important;
            transform: translateY(-1px);
        }
        .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
        }
        .main-content {
            padding: 4rem 0;
        }
        .welcome-text {
            font-size: 2.5rem;
            font-weight: 600;
            color: #2e7d32;
            margin-bottom: 1rem;
        }
        .subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 3rem;
        }
        .module-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        }
        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        .module-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .dropdown-menu {
            background: rgba(33, 37, 41, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .dropdown-item {
            color: rgba(255,255,255,0.85) !important;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(255,255,255,0.1);
            color: #fff !important;
            transform: translateX(5px);
        }
    </style>
</head>
<body>
<!-- Navigation Bar -->

    <div class="main-content">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="welcome-text">Welcome to Limau Kasturi</h1>
                <p class="subtitle">Manage your farm operations efficiently</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="module-card text-center">
                        <div class="module-icon text-primary">🌱</div>
                        <h3>Farm Activities</h3>
                        <p class="text-muted mb-4">Manage and track all farming activities</p>
                        <a href="FarmActivities/index.php" class="btn btn-primary w-100">Access</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="module-card text-center">
                        <div class="module-icon text-success">🐛</div>
                        <h3>Pest Control</h3>
                        <p class="text-muted mb-4">Monitor and manage pest control measures</p>
                        <a href="pest_control_management/index.php" class="btn btn-success w-100">Access</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="module-card text-center">
                        <div class="module-icon text-warning">📦</div>
                        <h3>Inventory</h3>
                        <p class="text-muted mb-4">Track and manage your inventory levels</p>
                        <a href="InventoryManagementSystem/index.php" class="btn btn-warning w-100">Access</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="module-card text-center">
                        <div class="module-icon text-info">💰</div>
                        <h3>Sales</h3>
                        <p class="text-muted mb-4">Manage orders and track sales</p>
                        <a href="limau_kasturi_orders/index.php" class="btn btn-info w-100">Access</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="module-card text-center">
                        <div class="module-icon text-danger">📊</div>
                        <h3>Finance</h3>
                        <p class="text-muted mb-4">Monitor expenses and revenue</p>
                        <a href="expenses_revenue/dashboard.php" class="btn btn-danger w-100">Access</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>