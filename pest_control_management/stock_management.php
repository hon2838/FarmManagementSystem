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

// Get the latest stock item ID
$latest_item = $pdo->query("
    SELECT item_name 
    FROM stock_management 
    ORDER BY CAST(SUBSTRING(item_name, 5) AS UNSIGNED) DESC 
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

$next_item_id = '001';
if ($latest_item) {
    // Extract the number from the latest ID and increment it
    $latest_num = intval(substr($latest_item['item_name'], 4));
    $next_item_id = sprintf("%03d", $latest_num + 1);
}
$next_item_id = 'ITEM' . $next_item_id;

// Handle item registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_item'])) {
    $item_name = $_POST['item_name'];
    $unit = $_POST['unit'];
    
    $stmt = $pdo->prepare("INSERT INTO stock_management (item_name, quantity, unit, last_updated) VALUES (?, 0, ?, CURRENT_DATE)");
    
    if ($stmt->execute([$item_name, $unit])) {
        $success = "Item registered successfully!";
        
        // Get the next item ID after successful registration
        $latest_item = $pdo->query("
            SELECT item_name 
            FROM stock_management 
            ORDER BY CAST(SUBSTRING(item_name, 5) AS UNSIGNED) DESC 
            LIMIT 1
        ")->fetch(PDO::FETCH_ASSOC);
        
        if ($latest_item) {
            $latest_num = intval(substr($latest_item['item_name'], 4));
            $next_item_id = sprintf("%03d", $latest_num + 1);
            $next_item_id = 'ITEM' . $next_item_id;
        }
    } else {
        $error = "Error registering item";
    }
}

// Handle stock recording
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_stock'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Update stock quantity
        $stmt = $pdo->prepare("UPDATE stock_management SET quantity = quantity + ?, last_updated = CURRENT_DATE WHERE id = ?");
        $stmt->execute([$quantity, $item_id]);
        
        // Get unit from stock_management
        $unit_stmt = $pdo->prepare("SELECT unit FROM stock_management WHERE id = ?");
        $unit_stmt->execute([$item_id]);
        $unit = $unit_stmt->fetch(PDO::FETCH_ASSOC)['unit'];
        
        // Record in history
        $stmt = $pdo->prepare("INSERT INTO stock_history (item_id, action, quantity, unit) VALUES (?, 'Added', ?, ?)");
        $stmt->execute([$item_id, $quantity, $unit]);
        
        $pdo->commit();
        $success = "Stock updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error updating stock: " . $e->getMessage();
    }
}

// Fetch current stock
$stock = $pdo->query("SELECT * FROM stock_management ORDER BY item_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch stock history
$stock_history = $pdo->query("
    SELECT 
        sh.id,
        sm.item_name,
        sh.action,
        sh.quantity, 
        sh.unit,
        sh.recorded_date as last_updated
    FROM stock_history sh
    JOIN stock_management sm ON sh.item_id = sm.id
    ORDER BY sh.recorded_date DESC, sh.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management - Pest Control</title>
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
            display: flex;
            justify-content: space-between;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .form-section {
            flex: 1;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        }
        form label {
            display: block;
            font-size: 16px;
            margin: 0.5rem 0;
            color: #2e7d32;
        }
        form input, form select {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f9f9f9;
        }
        form button {
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
        form button:hover {
            background-color: #388e3c;
            transform: translateY(-2px);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            border-radius: 10px;
            overflow: hidden;
        }
        table th, table td {
            padding: 1rem;
            text-align: left;
            border: 1px solid #e0e0e0;
        }
        table th {
            background-color: #43a047;
            color: white;
            font-weight: 500;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .delete-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <header>
                <h1>Stock Management</h1>
                <p>Keep track of your essential farming supplies.</p>
            </header>
            <main>
                <?php if (isset($success)): ?>
                    <div class="message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="message error"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Forms Container -->
                <div class="form-container">
                    <!-- Register New Item Form -->
                    <div class="form-section">
                        <h2>Register New Item</h2>
                        <form method="POST">
                            <label for="item_name">Item Name:</label>
                            <input type="text" id="item_name" name="item_name" required>
                            
                            <label for="unit">Unit of Measurement:</label>
                            <input type="text" id="unit" name="unit" placeholder="e.g., kg, L, pieces" required>
                            
                            <button type="submit" name="register_item">Register Item</button>
                        </form>
                    </div>

                    <!-- Record Stock Form -->
                    <div class="form-section">
                        <h2>Record Stock</h2>
                        <form method="POST">
                            <label for="item_id">Select Item:</label>
                            <select name="item_id" id="item_id" required>
                                <option value="">Select Item</option>
                                <?php foreach ($stock as $item): ?>
                                    <option value="<?php echo $item['id']; ?>">
                                        <?php echo htmlspecialchars($item['item_name']) . ' (' . htmlspecialchars($item['unit']) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" required>
                            
                            <button type="submit" name="record_stock">Record Stock</button>
                        </form>
                    </div>
                </div>

                <!-- Current Stock Table -->
                <h2>Stock Record Log</h2>
                <table>
                    <tr>
                        <th>Item Name</th>
                        <th>Action</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Date Updated</th>
                        <th>Action</th>
                    </tr>
                    <?php 
                    if (count($stock_history) > 0) {
                        foreach ($stock_history as $record): 
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($record['item_name']) ?></td>
                            <td><?= htmlspecialchars($record['action']) ?></td>
                            <td><?= htmlspecialchars($record['quantity']) ?></td>
                            <td><?= htmlspecialchars($record['unit']) ?></td>
                            <td><?= date('Y-m-d', strtotime($record['last_updated'])) ?></td>
                            <td>
                                <button onclick="deleteRecord(<?= $record['id'] ?>)" class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <?php 
                        endforeach;
                    } else {
                        echo "<tr><td colspan='6' style='text-align: center;'>No stock records found.</td></tr>";
                    }
                    ?>
                </table>
                
                <a href="index.php" class="back-link">Back to Pest Control Management</a>
                <a href="view_stock.php" class="back-link">View Available Stock</a>
            </main>
        </div>
    </div>

    <script>
    function deleteRecord(id) {
        if (confirm('Are you sure you want to delete this record?')) {
            fetch('delete_stock_record.php?id=' + id, {
                method: 'GET'
            })
            .then(response => response.text())
            .then(message => {
                if (message.includes('successfully')) {
                    alert('Record deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting record');
            });
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
