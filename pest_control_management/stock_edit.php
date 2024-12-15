<?php
session_start();
// Check if user is coming from main dashboard or has active session
if (isset($_GET['user'])) {
    $_SESSION['username'] = $_GET['user'];
} elseif (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

include '../db.php';
require_once '../header.php';

// Get the ID of the stock item to edit
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid ID.");
}

// Fetch the stock item data
$stmt = $pdo->prepare("SELECT * FROM stock_management WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Stock item not found.");
}

// Handle form submission for updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $last_updated = date('Y-m-d');

    $update_stmt = $pdo->prepare("UPDATE stock_management SET item_name = ?, quantity = ?, unit = ?, last_updated = ? WHERE id = ?");
    $update_stmt->execute([$item_name, $quantity, $unit, $last_updated, $id]);

    header("Location: stock_management.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Stock - Pest Control Management</title>
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
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        }
        form label {
            font-size: 16px;
            color: #2e7d32;
            margin-bottom: 0.5rem;
        }
        form input {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f9f9f9;
        }
        .btn-save {
            background-color: #43a047;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-save:hover {
            background-color: #388e3c;
            transform: translateY(-2px);
        }
        .btn-back {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #666;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            margin-top: 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background-color: #555;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <div class="text-center mb-4">
                <h1 class="welcome-text">Edit Stock Item</h1>
                <p class="subtitle">Update stock item details</p>
            </div>

            <div class="form-container">
                <form method="POST">
                    <div class="mb-3">
                        <label for="item_name">Item Name:</label>
                        <input type="text" id="item_name" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="unit">Unit:</label>
                        <input type="text" id="unit" name="unit" value="<?= htmlspecialchars($item['unit']) ?>" required>
                    </div>

                    <button type="submit" class="btn-save">Save Changes</button>
                    <a href="stock_management.php" class="btn-back d-block">Back to Stock Management</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
