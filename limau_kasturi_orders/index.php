<?php
// Include database connection (optional, depending on usage)
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limau Kasturi Order Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4fdf4; /* Light green background */
        }
        .header {
            background-color: #2e8b57; /* Dark green */
            color: white;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 2em;
        }
        .menu {
            margin: 20px auto;
            max-width: 800px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        .menu a {
            padding: 15px 30px;
            background-color: #f4f4f4; /* Light background */
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 8px;
            color: #333;
            font-size: 1.1em;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s;
        }
        .menu a:hover {
            background-color: #ddd; /* Slight dark shade */
            transform: scale(1.05);
        }
        .menu a:active {
            background-color: #2e8b57; /* Green on click */
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Limau Kasturi Order Management System</h1>
    </div>

    <div class="menu">
        <a href="customer.php">Register Customer</a>
        <a href="order_create_order.php">Create Order</a>
        <a href="orders_update_status.php">Update Delivery Status</a>
        <a href="completed_orders.php" class="menu-button">View Completed Orders</a>
        <a href="/FarmManagementSystem/dashboard.php" class="button">Main Page</a>
    </div>
    
</body>
</html>