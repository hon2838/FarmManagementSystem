<?php
// Include the database connection
include '../db.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch order and customer details
    $sql = "SELECT orders.order_id, orders.total_amount, orders.payment_method, orders.order_status,
                   customers.customer_name, customers.customer_phone, customers.delivery_address,
                   order_items.quantity, order_items.current_price, order_items.grade, orders.order_date
            FROM orders
            JOIN customers ON orders.customer_id = customers.customer_id
            JOIN order_items ON orders.order_id = order_items.order_id
            WHERE orders.order_id = '$order_id'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
    } else {
        echo "<p style='color: red;'>Order not found.</p>";
        exit;
    }
} else {
    echo "<p style='color: red;'>No order ID provided.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Order #<?php echo $order['order_id']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2e8b57;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2e8b57;
            margin: 0;
        }
        .document-title {
            font-size: 20px;
            color: #666;
            margin: 10px 0;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .customer-details, .order-details {
            flex: 1;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th, .items-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .items-table th {
            background-color: #2e8b57;
            color: white;
        }
        .total-amount {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #2e8b57;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }
        .back-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #2e8b57;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .back-button:hover {
            background-color: #267a4c;
        }
        .print-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        .print-button:hover {
            background-color: #0056b3;
        }
        @media print {
            .print-button, .back-button {
                display: none;
            }
            .invoice-box {
                box-shadow: none;
                border: none;
            }
            body {
                background-color: white;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <h1 class="company-name">Green Farm Livestocks</h1>
            <p class="document-title">INVOICE</p>
            <p>Date: <?php echo date('d/m/Y'); ?></p>
        </div>

        <div class="invoice-details">
            <div class="customer-details">
                <h3>Bill To:</h3>
                <p><strong><?php echo $order['customer_name']; ?></strong></p>
                <p>Phone: <?php echo $order['customer_phone']; ?></p>
                <p>Address: <?php echo $order['delivery_address']; ?></p>
            </div>
            <div class="order-details">
                <h3>Invoice Details:</h3>
                <p><strong>Invoice No:</strong> INV-<?php echo $order['order_id']; ?></p>
                <p><strong>Order Date:</strong> <?php echo date('d/m/Y', strtotime($order['order_date'])); ?></p>
                <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Grade</th>  <!-- Add grade column -->
                    <th>Quantity</th>
                    <th>Unit Price (RM)</th>
                    <th>Amount (RM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Limau Kasturi</td>
                    <td><?php echo $order['grade']; ?></td>  <!-- Add grade -->
                    <td><?php echo $order['quantity']; ?> kg</td>
                    <td><?php echo number_format($order['current_price'], 2); ?></td>
                    <td><?php echo number_format($order['total_amount'], 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total-amount">
            <p>Total Amount: RM <?php echo number_format($order['total_amount'], 2); ?></p>
        </div>

        <button onclick="window.print()" class="print-button">Print Invoice</button>
        <a href="completed_orders.php" class="back-button">Back to Completed Orders</a>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
