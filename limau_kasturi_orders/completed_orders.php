<?php
// Include the database connection
include '../db.php';

// Initialize variables for the filter
$month = isset($_POST['month']) ? $_POST['month'] : null;
$year = isset($_POST['year']) ? $_POST['year'] : null;

// Construct the SQL query for completed orders
$completed_orders_sql = "SELECT orders.order_id, orders.total_amount, orders.payment_method, orders.order_status, 
                                customers.customer_name, customers.customer_phone, orders.order_date
                         FROM orders
                         JOIN customers ON orders.customer_id = customers.customer_id
                         WHERE orders.order_status = 'Completed'";

// If month and year are set, apply the filter
if ($month && $year) {
    $completed_orders_sql .= " AND MONTH(orders.order_date) = '$month' AND YEAR(orders.order_date) = '$year'";
}

$result = $conn->query($completed_orders_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4fdf4;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #2e8b57;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #2e8b57;
            color: white;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2e8b57;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .back-button:hover {
            background-color: #267a4c;
        }
        .invoice-button {
            display: inline-block;
            padding: 5px 10px;
            background-color: #ff8c00;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .invoice-button:hover {
            background-color: #e07b00;
        }
        .filter-form {
            margin: 20px;
            text-align: center;
        }
        .filter-form select, .filter-form button {
            padding: 12px 20px;
            font-size: 16px;
            margin-right: 10px;
            border: 2px solid #2e8b57;
            border-radius: 5px;
        }
        .filter-form select:focus, .filter-form button:focus {
            outline: none;
            border-color: #267a4c;
        }
        .filter-form button {
            background-color: #2e8b57;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .filter-form button:hover {
            background-color: #267a4c;
        }
    </style>
</head>
<body>
    <h1>Completed Orders</h1>

    <!-- Filter Form -->
    <div class="filter-form">
        <form method="POST" action="">
            <label for="month">Month:</label>
            <select name="month" id="month">
                <option value="">-- Select Month --</option>
                <option value="1" <?php if ($month == 1) echo "selected"; ?>>January</option>
                <option value="2" <?php if ($month == 2) echo "selected"; ?>>February</option>
                <option value="3" <?php if ($month == 3) echo "selected"; ?>>March</option>
                <option value="4" <?php if ($month == 4) echo "selected"; ?>>April</option>
                <option value="5" <?php if ($month == 5) echo "selected"; ?>>May</option>
                <option value="6" <?php if ($month == 6) echo "selected"; ?>>June</option>
                <option value="7" <?php if ($month == 7) echo "selected"; ?>>July</option>
                <option value="8" <?php if ($month == 8) echo "selected"; ?>>August</option>
                <option value="9" <?php if ($month == 9) echo "selected"; ?>>September</option>
                <option value="10" <?php if ($month == 10) echo "selected"; ?>>October</option>
                <option value="11" <?php if ($month == 11) echo "selected"; ?>>November</option>
                <option value="12" <?php if ($month == 12) echo "selected"; ?>>December</option>
            </select>

            <label for="year">Year:</label>
            <select name="year" id="year">
                <option value="">-- Select Year --</option>
                <option value="2024" <?php if ($year == 2024) echo "selected"; ?>>2024</option>
                <option value="2023" <?php if ($year == 2023) echo "selected"; ?>>2023</option>
                <!-- Add more years as necessary -->
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Phone Number</th>
                <th>Total Amount (RM)</th>
                <th>Payment Method</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['order_id']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['customer_phone']; ?></td>
                    <td><?php echo number_format($row['total_amount'], 2); ?></td>
                    <td><?php echo $row['payment_method']; ?></td>
                    <td><?php echo $row['order_date']; ?></td>
                    <td>
                        <a href="generate_invoice.php?order_id=<?php echo $row['order_id']; ?>" class="invoice-button">Generate Invoice</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align: center; color: red;">No completed orders found.</p>
    <?php endif; ?>

    <a href="index.php" class="back-button">Back to Menu</a>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
