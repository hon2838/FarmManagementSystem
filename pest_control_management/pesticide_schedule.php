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

// Add a new pesticide schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pesticide_name = $_POST['pesticide_name'];
    $method = $_POST['method'];
    $application_date = $_POST['application_date'];
    $reapplication_interval = $_POST['reapplication_interval'];
    $next_application_date = date('Y-m-d', strtotime($application_date . " + $reapplication_interval days"));
    $notes = $_POST['notes'];
    $quantity_used = $_POST['quantity_used'];

    // Deduct stock
    $stmt = $pdo->prepare("SELECT quantity FROM stock_management WHERE item_name = ?");
    $stmt->execute([$pesticide_name]);
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stock && $stock['quantity'] >= $quantity_used) {
        // Insert schedule into pesticide_schedule table
        $stmt = $pdo->prepare("INSERT INTO pesticide_schedule (pesticide_name, method, application_date, reapplication_interval, next_application_date, notes, quantity_used) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$pesticide_name, $method, $application_date, $reapplication_interval, $next_application_date, $notes, $quantity_used]);

        // Update stock
        $stmt = $pdo->prepare("UPDATE stock_management SET quantity = quantity - ? WHERE item_name = ?");
        $stmt->execute([$quantity_used, $pesticide_name]);

        $message = "Pesticide schedule added and stock updated successfully!";
    } else {
        $message = "Error: Not enough stock available for $pesticide_name.";
    }
}

// Fetch existing schedules and available stock
$schedules = $pdo->query("SELECT * FROM pesticide_schedule ORDER BY application_date DESC")->fetchAll(PDO::FETCH_ASSOC);
$available_stock = $pdo->query("SELECT item_name, quantity FROM stock_management ORDER BY item_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesticide Scheduling - Pest Control Management</title>
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
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            margin-bottom: 2rem;
        }
        form label {
            color: #2e7d32;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        form select, form input, form textarea {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
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
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .message.error {
            background-color: #ffebee;
            color: #c62828;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="welcome-text">Pesticide Scheduling</h1>
                <p class="subtitle">Manage and track pesticide applications</p>
            </div>

            <div class="form-container">
                <?php if (isset($message)): ?>
                    <div class="message <?= strpos($message, 'Error') !== false ? 'error' : '' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <label>Pesticide Name:</label>
                    <select name="pesticide_name" required>
                        <?php foreach ($available_stock as $stock): ?>
                            <option value="<?= htmlspecialchars($stock['item_name']) ?>"><?= htmlspecialchars($stock['item_name']) ?> (Available: <?= htmlspecialchars($stock['quantity']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <label>Method:</label>
                    <select name="method" required>
                        <option value="chemical">Chemical</option>
                        <option value="organic">Organic</option>
                        <option value="biological">Biological</option>
                    </select>
                    <label>Application Date:</label>
                    <input type="date" name="application_date" required>
                    <label>Reapplication Interval (days):</label>
                    <input type="number" name="reapplication_interval" required>
                    <label>Quantity Used:</label>
                    <input type="number" name="quantity_used" required>
                    <label>Notes:</label>
                    <textarea name="notes"></textarea>
                    <button type="submit">Add Schedule</button>
                </form>
            </div>

            <!-- Schedule Table -->
            <table>
                <tr>
                    <th>Pesticide</th>
                    <th>Method</th>
                    <th>Application Date</th>
                    <th>Reapplication Interval</th>
                    <th>Next Application Date</th>
                    <th>Quantity Used</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($schedules as $schedule): ?>
                <tr>
                    <td><?= htmlspecialchars($schedule['pesticide_name']) ?></td>
                    <td><?= htmlspecialchars($schedule['method']) ?></td>
                    <td><?= htmlspecialchars($schedule['application_date']) ?></td>
                    <td><?= htmlspecialchars($schedule['reapplication_interval']) ?> days</td>
                    <td><?= htmlspecialchars($schedule['next_application_date']) ?></td>
                    <td><?= htmlspecialchars($schedule['quantity_used']) ?></td>
                    <td><?= htmlspecialchars($schedule['notes']) ?></td>
                    <td>
                        <a href="pesticide_edit.php?id=<?= $schedule['id'] ?>">Edit</a> |
                        <a href="pesticide_delete.php?id=<?= $schedule['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
