<?php
include 'db.php';

// Fetch current stock
$stock = $pdo->query("SELECT * FROM stock_management ORDER BY item_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Available Stock</title>
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
        <h1>View Available Stock</h1>
    </header>
    <main>
        <h2>Available Stock</h2>
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
            <p>No stock records available.</p>
        <?php endif; ?>
        <div class="button-container">
            <a href="stock_management.php" class="back-link">Back to Stock Management</a>
            <a href="index.php" class="back-link">Back to Pest Control Management</a>
        </div>
    </main>
</body>
</html>
