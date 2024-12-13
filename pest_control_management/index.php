<?php
session_start();

// Check if user is coming from main dashboard or has active session
if (isset($_GET['user'])) {
    $_SESSION['username'] = $_GET['user'];
} elseif (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../header.php'; // Include header from parent directory
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pest Control Management - Limau Kasturi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
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
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="welcome-text">Pest Control Management</h1>
                <p class="subtitle">Monitor and manage pest control activities</p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="module-card text-center">
                        <div class="module-icon text-success">🗓️</div>
                        <h3>Pesticide Scheduling</h3>
                        <p class="text-muted mb-4">Schedule and manage pesticide applications</p>
                        <a href="pesticide_schedule.php" class="btn btn-success w-100">Access</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="module-card text-center">
                        <div class="module-icon text-primary">📦</div>
                        <h3>Stock Management</h3>
                        <p class="text-muted mb-4">Manage pesticide inventory levels</p>
                        <a href="stock_management.php" class="btn btn-primary w-100">Access</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="module-card text-center">
                        <div class="module-icon text-info">📊</div>
                        <h3>View Stock</h3>
                        <p class="text-muted mb-4">Monitor available pesticide stock</p>
                        <a href="view_stock.php" class="btn btn-info w-100">Access</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="module-card text-center">
                        <div class="module-icon text-secondary">🏠</div>
                        <h3>Main Dashboard</h3>
                        <p class="text-muted mb-4">Return to main system dashboard</p>
                        <a href="/FarmManagementSystem/dashboard.php" class="btn btn-secondary w-100">Back to Main</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
