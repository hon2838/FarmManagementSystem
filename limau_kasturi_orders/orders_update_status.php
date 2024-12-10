<?php
// Include the database connection
include 'db_connection.php';

// Initialize filter variables
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// Handle order status updates
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $update_sql = "UPDATE orders SET order_status = 'Completed' WHERE order_id = '$order_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo "<p style='color: green;'>Order status updated successfully.</p>";
    } else {
        echo "<p style='color: red;'>Error updating status: " . $conn->error . "</p>";
    }
}

// Handle order deletion
if (isset($_POST['delete_order'])) {
    $order_id = $_POST['delete_order_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // First delete order items
        $delete_items = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $conn->prepare($delete_items);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        // Then delete the order
        $delete_order = "DELETE FROM orders WHERE order_id = ?";
        $stmt = $conn->prepare($delete_order);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        echo "<p style='color: green;'>Order deleted successfully.</p>";
        // Refresh the page
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo "<p style='color: red;'>Error deleting order: " . $e->getMessage() . "</p>";
    }
}

// Fetch orders based on filters
$filter_sql = "SELECT orders.order_id, orders.total_amount, orders.payment_method, 
                      orders.order_status, customers.customer_name, 
                      customers.customer_phone, orders.order_date,
                      order_items.quantity as ordered_quantity,
                      order_items.current_price as unit_price
               FROM orders
               JOIN customers ON orders.customer_id = customers.customer_id
               JOIN order_items ON orders.order_id = order_items.order_id";

if ($month && $year) {
    $filter_sql .= " WHERE MONTH(order_date) = '$month' AND YEAR(order_date) = '$year'";
} elseif ($year) {
    $filter_sql .= " WHERE YEAR(order_date) = '$year'";
}

$result = $conn->query($filter_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order Status</title>
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
        .back-button {
            padding: 10px 20px;
            background-color: #ff8c00;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #e07b00;
        }
        form.filter-form {
            text-align: center;
            margin-bottom: 20px;
        }
        select {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button.filter-button {
            padding: 10px 20px;
            background-color: #2e8b57;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button.filter-button:hover {
            background-color: #267a4c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
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
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        button {
            padding: 5px 10px;
            background-color: #2e8b57;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #267a4c;
        }
    </style>
</head>
<body>
    <h1>Update Order Status</h1>

    <!-- Back to Menu Button -->
   <div style="text-align: center; margin-bottom: 20px;">
    <a href="index.php" class="nav-button">Back to Menu</a>
    <a href="completed_orders.php" class="nav-button">View Completed Orders</a>
</div>

<style>
    .nav-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #2e8b57;
        color: white;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        margin: 5px;
    }

    .nav-button:hover {
        background-color: #267a4c;
    }
</style>

    <!-- Orders Table -->
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Phone Number</th>
            <th>Quantity (kg)</th>
            <th>Price per kg (RM)</th>
            <th>Total Amount (RM)</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>Order Date</th>
            <th colspan="2">Actions</th> <!-- Updated to span 2 columns -->
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['order_id']; ?></td>
                <td><?php echo $row['customer_name']; ?></td>
                <td><?php echo $row['customer_phone']; ?></td>
                <td><?php echo $row['ordered_quantity']; ?></td>
                <td><?php echo number_format($row['unit_price'], 2); ?></td>
                <td><?php echo number_format($row['total_amount'], 2); ?></td>
                <td><?php echo $row['payment_method']; ?></td>
                <td>
                    <?php if ($row['order_status'] === 'Completed'): ?>
                        <span style="color: #2e8b57; font-weight: bold;">Completed</span>
                    <?php else: ?>
                        <span style="color: #ffc107; font-weight: bold;">Order Pending</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $row['order_date']; ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                        <input type="checkbox" name="update_status" onchange="this.form.submit()" 
                               <?php echo $row['order_status'] === 'Completed' ? 'checked disabled' : ''; ?>>
                    </form>
                </td>
                <td>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this order?');">
                        <input type="hidden" name="delete_order_id" value="<?php echo $row['order_id']; ?>">
                        <button type="submit" name="delete_order" style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">
                            Delete Order
                        </button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
