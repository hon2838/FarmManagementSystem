<?php
session_start();
// Check if user is coming from main dashboard or has active session
if (isset($_GET['user'])) {
    $_SESSION['username'] = $_GET['user'];
} elseif (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../header.php';
require_once '../db.php';

// Fetch current stock
$stock = $pdo->query("SELECT * FROM stock_management ORDER BY item_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Stock - Pest Control Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
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
        .main-content {
            padding: 4rem 0;
        }
        .welcome-text {
            font-size: 2.5rem;
            font-weight: 600;
            color: #2e7d32;
            margin-bottom: 1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        table th, table td {
            border: 1px solid #c3e6cb;
            padding: 1rem;
            text-align: left;
        }
        table th {
            background-color: #66bb6a;
            color: white;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background-color: #f6fff2;
        }
        table tr:nth-child(odd) {
            background-color: #ffffff;
        }
        table tr:hover {
            background-color: #e8f5e9;
        }
        .button-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        .back-link {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #43a047;
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .back-link:hover {
            background-color: #388e3c;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="welcome-text">View Available Stock</h1>
                <p class="subtitle">Monitor current stock levels</p>
            </div>

            <?php if (count($stock) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stock as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td><?= htmlspecialchars($item['unit']) ?></td>
                            <td><?= htmlspecialchars($item['last_updated']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">No stock records available.</p>
            <?php endif; ?>

            <div class="button-container">
                <a href="stock_management.php" class="back-link">Back to Stock Management</a>
                <a href="index.php" class="back-link">Back to Pest Control Management</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
