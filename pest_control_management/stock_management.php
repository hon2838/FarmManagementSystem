<?php
include '../db.php';

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
    <title>Stock Management</title>
    <style>
        body {
            font-family: "Lato", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e8f5e9; /* Light green background */
            color: #2e7d32;
        }
        header {
            background-color: #66bb6a; /* Green header */
            color: white;
            padding: 1.5rem 0;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            font-size: 36px;
            font-weight: bold;
            margin: 0;
            text-shadow: 1px 1px 4px #a5d6a7;
        }
        main {
            margin: 2rem auto;
            max-width: 900px;
            width: 90%;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
            text-align: left;
        }
        main h2 {
            color: #2e7d32;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            background-color: #e8f5e9; /* Light green for success messages */
            color: #4caf50; /* Green text */
        }
        .error {
            background-color: #ffebee; /* Light red for error messages */
            color: #f44336; /* Red text */
        }
        form {
            margin-top: 2rem;
        }
        form label {
            display: block;
            font-size: 16px;
            margin: 0.5rem 0;
            color: #2e7d32;
        }
        form input, form select, form textarea {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f9f9f9;
        }
        form button {
            background-color: #43a047; /* Button color */
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }
        form button:hover {
            background-color: #388e3c; /* Darker green */
            transform: scale(1.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table th, table td {
            border: 1px solid #c3e6cb;
            padding: 0.8rem;
            text-align: left;
        }
        table th {
            background-color: #66bb6a; /* Header green */
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
        a.back-link {
            display: inline-block;
            margin-top: 2rem;
            margin-right: 1rem;
            text-decoration: none;
            color: white;
            background-color: #43a047; /* Button color */
            padding: 0.8rem 1.5rem;
            border-radius: 30px; /* Rounded corners */
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, transform 0.2s, box-shadow 0.2s;
            text-align: center;
        }
        a.back-link:hover {
            background-color: #388e3c; /* Darker green */
            transform: translateY(-2px); /* Lift effect */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        a.back-link:active {
            background-color: #2e7d32; /* Even darker green for active state */
            transform: translateY(0); /* Reset lift effect */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
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
                </tr>
                <?php 
                endforeach;
            } else {
                echo "<tr><td colspan='5' style='text-align: center;'>No stock records found.</td></tr>";
            }
            ?>
        </table>
        
        <a href="index.php" class="back-link">Back to Pest Control Management</a>
        <a href="view_stock.php" class="back-link">View Available Stock</a>
    </main>
</body>
</html>
