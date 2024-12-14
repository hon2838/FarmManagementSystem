<?php
// Include the database connection
include '../db.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch order and customer details
    $sql = "SELECT orders.order_id, orders.total_amount, orders.payment_method, orders.order_status,
                   customers.customer_name, customers.customer_phone, customers.delivery_address,
                   order_items.quantity, order_items.current_price
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
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2e8b57;
        }
        .details {
            margin-bottom: 20px;
        }
        .details p {
            margin: 5px 0;
        }
        .total {
            font-weight: bold;
            color: #2e8b57;
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
    </style>
</head>
<body>
    <div class="invoice-box">
        <h2>Invoice</h2>
        <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
        <p><strong>Customer:</strong> <?php echo $order['customer_name']; ?></p>
        <p><strong>Phone:</strong> <?php echo $order['customer_phone']; ?></p>
        <p><strong>Delivery Address:</strong> <?php echo $order['delivery_address']; ?></p>
        <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>

        <div class="details">
            <p><strong>Quantity (kg):</strong> <?php echo $order['quantity']; ?></p>
            <p><strong>Price per kg (RM):</strong> <?php echo number_format($order['current_price'], 2); ?></p>
            <p class="total"><strong>Total Amount (RM):</strong> <?php echo number_format($order['total_amount'], 2); ?></p>
        </div>

        <a href="completed_orders.php" class="back-button">Back to Completed Orders</a>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
