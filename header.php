<?php
session_start();

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
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
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <strong>Limau Kasturi</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Dashboard</a>
                </li>
                <!-- Farm Activities Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="farmDropdown" role="button" data-bs-toggle="dropdown">
                        Farm Activities
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="FarmActivities/index.php">Dashboard</a></li>
                        <li><a class="dropdown-item" href="FarmActivities/schedule_activity.php">Schedule Activity</a></li>
                        <li><a class="dropdown-item" href="FarmActivities/activity_list.php">Activity List</a></li>
                        <li><a class="dropdown-item" href="FarmActivities/weather.php">Weather</a></li>
                        <li><a class="dropdown-item" href="FarmActivities/weather_prediction.php">Production Prediction</a></li>
                    </ul>
                </li>
                <!-- Pest Control Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="pestDropdown" role="button" data-bs-toggle="dropdown">
                        Pest Control
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="pest_control_management/index.php">Dashboard</a></li>
                        <li><a class="dropdown-item" href="pest_control_management/pesticide_schedule.php">Pesticide Schedule</a></li>
                        <li><a class="dropdown-item" href="pest_control_management/stock_management.php">Stock Management</a></li>
                        <li><a class="dropdown-item" href="pest_control_management/view_stock.php">View Stock</a></li>
                    </ul>
                </li>
                <!-- Inventory Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="inventoryDropdown" role="button" data-bs-toggle="dropdown">
                        Inventory
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="InventoryManagementSystem/index.php">Dashboard</a></li>
                        <li><a class="dropdown-item" href="InventoryManagementSystem/add_inventory.php">Add Inventory</a></li>
                        <li><a class="dropdown-item" href="InventoryManagementSystem/view_inventory.php">View Inventory</a></li>
                    </ul>
                </li>
                <!-- Sales Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="salesDropdown" role="button" data-bs-toggle="dropdown">
                        Sales
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="limau_kasturi_orders/index.php">Dashboard</a></li>
                        <li><a class="dropdown-item" href="limau_kasturi_orders/customer.php">Register Customer</a></li>
                        <li><a class="dropdown-item" href="limau_kasturi_orders/order_create_order.php">Create Order</a></li>
                        <li><a class="dropdown-item" href="limau_kasturi_orders/orders_update_status.php">Update Status</a></li>
                        <li><a class="dropdown-item" href="limau_kasturi_orders/completed_orders.php">Completed Orders</a></li>
                    </ul>
                </li>
                <!-- Finance Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="financeDropdown" role="button" data-bs-toggle="dropdown">
                        Finance
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="expenses_revenue/dashboard.php">Dashboard</a></li>
                        <li><a class="dropdown-item" href="expenses_revenue/expenses.php">Expenses</a></li>
                        <li><a class="dropdown-item" href="expenses_revenue/profit.php">Profit</a></li>
                        <li><a class="dropdown-item" href="expenses_revenue/report.php">Report</a></li>
                    </ul>
                </li>
                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="?logout">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>