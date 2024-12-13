<?php
session_start();

// Check if user is coming from main dashboard or has active session
if (isset($_GET['user'])) {
    $_SESSION['username'] = $_GET['user'];
} elseif (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

require_once 'db.php';

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // Use the authenticateUser function from db.php
        if (authenticateUser($username, $password, $pdo)) {
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Activities System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            font-family: "Lato", sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            color: #2e7d32;
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
        h1 {
            font-size: 36px;
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 30px;
            text-shadow: 1px 1px 4px #a5d6a7;
        }
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 300px;
            width: 100%;
        }
        .button {
            display: inline-block;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #ffffff;
            background-color: #66bb6a;
            text-decoration: none;
            border-radius: 8px;
            border: 2px solid #43a047;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
        .button:hover {
            background-color: #43a047;
            transform: translateY(-3px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }
        .button:active {
            transform: translateY(1px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
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
                    <li class="nav-item">
                        <a class="nav-link" href="FarmActivities/index.php">Farm Activities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pest_control_management/index.php">Pest Control</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="InventoryManagementSystem/index.php">Inventory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="limau_kasturi_orders/index.php">Sales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="expenses_revenue/dashboard.php">Finance</a>
                    </li>
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

    <h1>Farm Activities System</h1>
    <div class="button-container">
        <a href="weather.php" class="button">Weather</a>
        <a href="schedule_activity.php" class="button">Schedule Activity</a>
        <a href="activity_list.php" class="button">Activity List</a>
        <a href="weather_prediction.php" class="button">Production Prediction</a>
        <a href="/FarmManagementSystem/dashboard.php" class="button">Main Page</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
