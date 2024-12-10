<?php
// Include the database connection file
include 'db_connection.php';

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
    $check_stock = "SELECT SUM(quantity) as total FROM inventory WHERE grade = '$grade'";
    $stock_result = $conn->query($check_stock);
    $available_stock = $stock_result->fetch_assoc()['total'];

    if ($available_stock >= $quantity) {
        // Begin transaction
        $conn->begin_transaction();
        try {
            // Insert order
            $sql = "INSERT INTO orders (customer_id, payment_method, total_amount) 
                    VALUES ('$customer_id', '$payment_method', '$total_amount')";
            $conn->query($sql);
            $order_id = $conn->insert_id;

            // Insert order items
            $item_sql = "INSERT INTO order_items (order_id, quantity, current_price) 
                        VALUES ('$order_id', '$quantity', '$current_price')";
            $conn->query($item_sql);

            // Update inventory - reduce stock
            $update_stock = "UPDATE inventory 
                           SET quantity = quantity - $quantity 
                           WHERE grade = '$grade' 
                           AND quantity > 0 
                           ORDER BY recorded_date ASC 
                           LIMIT 1";
            $conn->query($update_stock);

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
    <title>Create Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 10px;
            background-color: #f4fdf4; /* Light green background */
        }
        h1 {
            color: #2e8b57; /* Dark green */
            text-align: center;
        }
        form {
            max-width: 400px;
            margin: 20px auto;
            background-color: #ffffff; /* White background */
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #2e8b57;
        }
        select, input, button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #2e8b57; /* Dark green */
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        button:hover {
            background-color: #267a4c; /* Slightly darker green */
        }
        .total-amount {
            font-weight: bold;
            color: #2e8b57; /* Green for total */
        }
        p.message {
            text-align: center;
            font-weight: bold;
        }
        p.message.success {
            color: #2e8b57; /* Green */
        }
        p.message.error {
            color: red; /* Red for errors */
        }
    </style>
    <script>
        // JavaScript function to calculate total amount
        function calculateTotal() {
            var quantity = document.getElementById('quantity').value;
            var price = document.getElementById('current_price').value;
            var total = quantity * price;
            if (!isNaN(total)) {
                document.getElementById('total_amount').innerText = 'RM ' + total.toFixed(2);
            } else {
                document.getElementById('total_amount').innerText = 'RM 0.00';
            }
        }
    </script>
</head>
<body>
    <h1>Create New Order</h1>
    <?php if ($message) echo "<p class='message " . ($message[1] === 'r' ? 'error' : 'success') . "'>$message</p>"; ?>
    <form method="POST" action="">
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
    <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" step="0.01" min="0" required oninput="calculateTotal()">
    
    <label for="grade">Grade:</label>
<select name="grade" id="grade" required onchange="calculateTotal()">
    <option value="">-- Select Grade --</option>
    <option value="A">Grade A</option>
    <option value="B">Grade B</option>
    <option value="C">Grade C</option>
</select>


    <label for="current_price">Price per kg (RM):</label>
    <input type="number" id="current_price" name="current_price" placeholder="Enter price per kg" step="0.01" min="0" required oninput="calculateTotal()">

    <p>Total Amount: <span id="total_amount" class="total-amount">RM 0.00</span></p>

    <label for="payment_method">Payment Method:</label>
    <select name="payment_method" id="payment_method" required>
        <option value="">-- Select Payment Method --</option>
        <option value="cash">Cash</option>
        <option value="online_transfer">Online Transfer</option>
    </select>

    <button type="submit">Create Order</button>
    
    <button type="button" onclick="window.location.href='orders_update_status.php'" style="background-color: #2e8b57; color: white; font-weight: bold; cursor: pointer; border: none; padding: 10px; width: 100%; margin-top: 10px;">
    Update Delivery Status
</button>
    <button type="button" onclick="window.location.href='index.php'" style="background-color: #2e8b57; color: white; font-weight: bold; cursor: pointer; border: none; padding: 10px; width: 100%; margin-top: 10px;">
    Back to Menu
</button>
    </button>
</form>
