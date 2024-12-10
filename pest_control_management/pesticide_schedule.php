<?php
include 'db.php';

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

// Fetch existing schedules
$schedules = $pdo->query("SELECT * FROM pesticide_schedule ORDER BY application_date DESC")->fetchAll(PDO::FETCH_ASSOC);
$available_stock = $pdo->query("SELECT item_name, quantity FROM stock_management ORDER BY item_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesticide Scheduling</title>
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
        }
        table th, table td {
            border: 1px solid #c3e6cb;
            padding: 0.8rem;
            text-align: left;
        }
        table th {
            background-color: #66bb6a; /* Header green */
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f6fff2;
        }
        table tr:nth-child(odd) {
            background-color: #ffffff;
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
    </style>
</head>
<body>
    <header>
        <h1>Pesticide Scheduling</h1>
    </header>
    <main>
        <h2>Schedule a Pesticide</h2>
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
        <h2>Existing Schedules</h2>
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
        <a href="index.php" class="back-link">Back to Pest Control Management</a>
        <a href="stock_management.php" class="back-link">Go to Stock Management</a>
    </main>
</body>
</html>
