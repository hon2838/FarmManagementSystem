<?php
include 'db.php';

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

    echo "Stock updated successfully!";
    header("Location: stock_management.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Stock</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f6fff2; /* Light green background */
            color: #333;
        }
        header {
            background-color: #82b74b; /* Green header */
            color: white;
            text-align: center;
            padding: 1rem 0;
        }
        main {
            margin: 2rem auto;
            max-width: 800px;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #4a7c23;
        }
        form {
            display: grid;
            gap: 1rem;
        }
        form label {
            font-weight: bold;
            color: #4a7c23;
        }
        form input, form button {
            padding: 0.5rem;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            font-size: 1rem;
        }
        form button {
            background-color: #4a7c23;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        form button:hover {
            background-color: #3b621a;
        }
    </style>
</head>
<body>
    <header>
        <h1>Edit Stock</h1>
    </header>
    <main>
        <form method="POST">
            <label for="item_name">Item Name:</label>
            <input type="text" id="item_name" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>" required>

            <label for="unit">Unit:</label>
            <input type="text" id="unit" name="unit" value="<?= htmlspecialchars($item['unit']) ?>" required>

            <button type="submit">Update Stock</button>
        </form>
    </main>
</body>
</html>
