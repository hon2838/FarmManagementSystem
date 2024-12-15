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

// Get the ID of the stock item to delete
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<script>
            alert('Invalid ID provided');
            window.location.href = 'stock_management.php';
          </script>";
    exit();
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Get stock details before deletion
    $get_stock = $pdo->prepare("SELECT * FROM stock_management WHERE id = ?");
    $get_stock->execute([$id]);
    $stock = $get_stock->fetch();
    
    if (!$stock) {
        throw new Exception("Stock item not found");
    }
    
    // Record in history before deletion
    $stmt = $pdo->prepare("
        INSERT INTO stock_history (item_id, action, quantity, unit, recorded_date) 
        VALUES (?, 'Deleted', ?, ?, CURRENT_DATE)
    ");
    $stmt->execute([$id, $stock['quantity'], $stock['unit']]);
    
    // Delete the stock item
    $delete_stmt = $pdo->prepare("DELETE FROM stock_management WHERE id = ?");
    $delete_stmt->execute([$id]);
    
    $pdo->commit();
    
    echo "<script>
            alert('Stock item deleted successfully');
            window.location.href = 'stock_management.php';
          </script>";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<script>
            alert('Error deleting stock: " . $e->getMessage() . "');
            window.location.href = 'stock_management.php';
          </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Stock - Pest Control Management</title>
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
    </style>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
