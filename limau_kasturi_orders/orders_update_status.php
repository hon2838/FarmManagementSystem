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

// Display message if exists
if (isset($_SESSION['message'])) {
    echo "<p style='color: green;'>".$_SESSION['message']."</p>";
    unset($_SESSION['message']);
}

// Initialize filter variables
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// Handle order status updates
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update order status
        $update_sql = "UPDATE orders SET order_status = 'Completed' WHERE order_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
    
        // Get order details for profit record
        $order_details = $conn->prepare("
            SELECT o.total_amount, o.order_date, c.customer_name, c.delivery_address,
                   oi.quantity, o.order_status 
            FROM orders o 
            JOIN customers c ON o.customer_id = c.customer_id
            JOIN order_items oi ON o.order_id = oi.order_id 
            WHERE o.order_id = ?
        ");
        $order_details->bind_param("i", $order_id);
        $order_details->execute();
        $order = $order_details->get_result()->fetch_assoc();
    
        // Format the order ID to match the format in profits table
        $profit_order_id = 'ORD' . $order_id;  // Simply concatenate without padding
    
        // Insert into profits table
        $insert_profit = $conn->prepare("
            INSERT INTO profits (
                customer_name, 
                record_date, 
                order_id, 
                price, 
                delivery_address, 
                recorded_by
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $recorded_by = $_SESSION['username'] ?? 'system';
        $insert_profit->bind_param(
            "sssdss", 
            $order['customer_name'],
            $order['order_date'],
            $profit_order_id,  // Use non-padded order ID
            $order['total_amount'],
            $order['delivery_address'],
            $recorded_by
        );
        $insert_profit->execute();
    
        $conn->commit();
        echo "<p style='color: green;'>Order status updated and profit recorded successfully.</p>";
    
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color: red;'>Error updating status: " . $e->getMessage() . "</p>";
    }
}

// Handle order deletion
if (isset($_POST['delete_order'])) {
    $order_id = $_POST['delete_order_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Get order details before deletion
        $get_order = $conn->prepare("
            SELECT o.order_status, o.total_amount, o.order_date,
                   oi.quantity, oi.grade, oi.current_price,
                   c.delivery_address, c.customer_name
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN customers c ON o.customer_id = c.customer_id
            WHERE o.order_id = ?
        ");
        $get_order->bind_param("i", $order_id);
        $get_order->execute();
        $order_info = $get_order->get_result()->fetch_assoc();
        
        if ($order_info) {
            // Return quantity to inventory
            $update_inventory = "UPDATE inventory 
                               SET quantity = quantity + ? 
                               WHERE grade = ?
                               ORDER BY recorded_date DESC 
                               LIMIT 1";
            $stmt = $conn->prepare($update_inventory);
            $stmt->bind_param("ds", $order_info['quantity'], $order_info['grade']);
            $stmt->execute();
            
            // Delete from profits table if completed
            if ($order_info['order_status'] === 'Completed') {
                // Format order ID without padding
                $formatted_order_id = 'ORD' . $order_id;
                
                // Delete from profits table
                $delete_revenue = "DELETE FROM profits WHERE order_id = ?";
                $stmt = $conn->prepare($delete_revenue);
                $stmt->bind_param("s", $formatted_order_id);
                $stmt->execute();
            }
            
            // Delete order items first (foreign key constraint)
            $delete_items = "DELETE FROM order_items WHERE order_id = ?";
            $stmt = $conn->prepare($delete_items);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            
            // Delete the order
            $delete_order = "DELETE FROM orders WHERE order_id = ?";
            $stmt = $conn->prepare($delete_order);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            
            $conn->commit();
            $_SESSION['message'] = "Order deleted successfully. Stock and revenue records updated.";
            echo "<script>window.location.href = '".$_SERVER['PHP_SELF']."';</script>";
            exit();
        }
    } catch (Exception $e) {
        $conn->rollback();
        file_put_contents('delete_order_log.txt', "Error occurred: " . $e->getMessage() . "\n", FILE_APPEND);
        echo "<p style='color: red;'>Error deleting order: " . $e->getMessage() . "</p>";
    }
}

// Fetch orders based on filters
$filter_sql = "SELECT orders.order_id, orders.total_amount, orders.payment_method, 
                      orders.order_status, customers.customer_name, 
                      customers.customer_phone, orders.order_date,
                      order_items.quantity as ordered_quantity,
                      order_items.current_price as unit_price,
                      order_items.grade  -- Using SQL comment style
               FROM orders
               JOIN customers ON orders.customer_id = customers.customer_id
               JOIN order_items ON orders.order_id = order_items.order_id";

if ($month && $year) {
    $filter_sql .= " WHERE MONTH(order_date) = '$month' AND YEAR(order_date) = '$year'";
} elseif ($year) {
    $filter_sql .= " WHERE YEAR(order_date) = '$year'";
}

// Add ORDER BY clause to show pending orders first, then by date
$filter_sql .= " ORDER BY 
                 CASE 
                    WHEN orders.order_status = 'Pending' THEN 0 
                    ELSE 1 
                 END,
                 orders.order_date DESC";

$result = $conn->query($filter_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order Status - Sales Management</title>
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
        .subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
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
        .btn-update {
            background-color: #43a047;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-update:hover {
            background-color: #388e3c;
            transform: translateY(-2px);
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-delete:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        .nav-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .nav-button {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #43a047;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .nav-button:hover {
            background-color: #388e3c;
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="welcome-text">Update Order Status</h1>
                <p class="subtitle">Manage and track order deliveries</p>
            </div>

            <div class="nav-buttons">
                <a href="index.php" class="nav-button">Back to Menu</a>
                <a href="completed_orders.php" class="nav-button">View Completed Orders</a>
            </div>

            <!-- Orders Table -->
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Phone Number</th>
                    <th>Quantity (kg)</th>
                    <th>Grade</th>  <!-- Add grade column -->
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
                        <td><?php echo $row['grade']; ?></td>  <!-- Add grade column -->
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
