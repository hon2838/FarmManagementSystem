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

// Initialize a success or error message variable
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $customer_id = $conn->real_escape_string($_POST['customer_id']);
    $quantity = $conn->real_escape_string($_POST['quantity']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $current_price = $conn->real_escape_string($_POST['current_price']); // Farmer inputs price
    $grade = $conn->real_escape_string($_POST['grade']); // Get selected grade
    $total_amount = $quantity * $current_price; // Calculate total amount

    // First check if enough stock is available
    $check_stock = "SELECT SUM(quantity) as total FROM inventory WHERE grade = ?";
    $stmt = $conn->prepare($check_stock);
    $stmt->bind_param("s", $grade);
    $stmt->execute();
    $available_stock = $stmt->get_result()->fetch_assoc()['total'];

    if ($available_stock >= $quantity) {
        // Begin transaction
        $conn->begin_transaction();
        try {
            // Insert order
            $sql = "INSERT INTO orders (customer_id, payment_method, total_amount) 
                    VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isd", $customer_id, $payment_method, $total_amount);
            $stmt->execute();
            $order_id = $conn->insert_id;

            // Insert order items with grade
            $item_sql = "INSERT INTO order_items (order_id, quantity, grade, current_price) 
                        VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($item_sql);
            $stmt->bind_param("issd", $order_id, $quantity, $grade, $current_price);
            $stmt->execute();

            // Update inventory - reduce stock
            $update_stock = "UPDATE inventory 
                           SET quantity = quantity - ? 
                           WHERE grade = ? 
                           AND quantity > 0 
                           ORDER BY recorded_date ASC 
                           LIMIT 1";
            $stmt = $conn->prepare($update_stock);
            $stmt->bind_param("ds", $quantity, $grade);
            $stmt->execute();

            // Commit transaction
            $conn->commit();
            $message = "<p style='color:green;'>Order created successfully and stock updated.</p>";
        } catch (Exception $e) {
            $conn->rollback();
            $message = "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        $message = "<p style='color:red;'>Not enough stock available for grade $grade. Available: $available_stock kg</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order - Sales Management</title>
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
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        }
        form label {
            color: #2e7d32;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        form select, form input {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .total-amount {
            display: block;
            font-size: 1.5rem;
            color: #2e7d32;
            font-weight: bold;
            margin: 1rem 0;
        }
        .btn-submit {
            background-color: #43a047;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        .btn-submit:hover {
            background-color: #388e3c;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #666;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #555;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <div class="text-center mb-4">
                <h1 class="welcome-text">Create New Order</h1>
                <p class="subtitle">Enter order details below</p>
            </div>

            <?php if ($message) echo "<p class='message " . ($message[1] === 'r' ? 'error' : 'success') . "'>$message</p>"; ?>

            <div class="form-container">
                <form method="POST">
                    <label for="customer_id">Select Customer:</label>
                    <select name="customer_id" id="customer_id" required>
                        <option value="">-- Select Customer --</option>
                        <?php
                        // Fetch customers from the database
                        $result = $conn->query("SELECT * FROM customers");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['customer_id']}'>{$row['customer_name']} - {$row['customer_phone']}</option>";
                        }
                        ?>
                    </select>

                    <label for="quantity">Quantity (kg):</label>
                    <input type="number" id="quantity" name="quantity" min="0" step="0.01" required oninput="calculateTotal()">

                    <label for="grade">Grade:</label>
                    <select name="grade" id="grade" required>
                        <option value="">-- Select Grade --</option>
                        <option value="A">Grade A</option>
                        <option value="B">Grade B</option>
                        <option value="C">Grade C</option>
                    </select>

                    <label for="current_price">Price per kg (RM):</label>
                    <input type="number" id="current_price" name="current_price" step="0.01" min="0" required oninput="calculateTotal()">

                    <div class="total-amount">Total Amount: <span id="total_amount">RM 0.00</span></div>

                    <label for="payment_method">Payment Method:</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="">-- Select Payment Method --</option>
                        <option value="cash">Cash</option>
                        <option value="online_transfer">Online Transfer</option>
                    </select>

                    <button type="submit" class="btn-submit">Create Order</button>
                    <a href="orders_update_status.php" class="btn-secondary d-block text-center text-decoration-none">Update Delivery Status</a>
                    <a href="index.php" class="btn-secondary d-block text-center text-decoration-none">Back to Menu</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function calculateTotal() {
            const quantity = parseFloat(document.getElementById('quantity').value) || 0;
            const price = parseFloat(document.getElementById('current_price').value) || 0;
            const total = quantity * price;
            document.getElementById('total_amount').textContent = `RM ${total.toFixed(2)}`;
        }
    </script>
</body>
</html>
